<?php


namespace App\Services\Connectors;


use App\Contracts\Processor;
use App\Contracts\ServiceConnector;
use App\Enums\WebhookEventMapper;
use App\Events\Webhook\WebhookReceived;
use App\Models\Marketplace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Str;

class ProcessorManager
{
    protected $identifier;
    protected $processorServices;
    protected $connector;
    protected $webhookReceivedEvent;

    public function __construct(string $identifier, Enumerable $service, ServiceConnector $connector)
    {
        $this->identifier = $identifier;
        $this->processorServices = $service;
        $this->connector = $connector;
    }

    public function process()
    {
        $this->saveRawWebhook();

        [$class, $method] = $this->getClassAndMethodFromMapper() + [null, null];

        if (!$class || !$method || !WebhookEventMapper::hasValue($method)) {
            // not supported
            return;
        }

        $resolvedProcessor = $this->resolvedProcessor($class);

        // call the specific processor
        $result = $resolvedProcessor->{$method}($this->webhookReceivedEvent);

        if ($result && $result instanceof Model) {
            $this->fireEventAfterProcess($result, $method);
        }
    }

    public function setWebhookReceivedEvent(WebhookReceived $webhookReceivedEvent)
    {
        $this->webhookReceivedEvent = $webhookReceivedEvent;
        return $this;
    }

    protected function saveRawWebhook()
    {
        Marketplace::whereName($this->webhookReceivedEvent->identifier)
            ->firstOrFail()
            ->rawWebhooks()
            ->create([
                'raw_data' => $this->webhookReceivedEvent->rawData->toArray(),
                'topic' => $this->webhookReceivedEvent->topic,
            ]);
    }

    protected function resolvedProcessor($class)
    {
        return $this->processorServices
            ->first(function (Processor $processorService) use ($class) {
                return $processorService->processFor() === $class;
            });
    }

    protected function getClassAndMethodFromMapper()
    {
        return Arr::get($this->connector->mapper(), 'webhook.' . $this->webhookReceivedEvent->topic);
    }

    protected function fireEventAfterProcess($model, $method)
    {
        // transform method onCreate -> Created
        $method = Str::of($method)->replaceFirst('on', '')->append('d');

        $extractedClassFromObject = ucfirst(class_basename($model));
        $namespace = 'App\\Events\\' . $extractedClassFromObject . '\\';
        $eventName = $extractedClassFromObject . $method;
        $eventClass = $namespace . $eventName;

        if (class_exists($eventClass)) {
            event(new $eventClass($model));
        }
    }
}
