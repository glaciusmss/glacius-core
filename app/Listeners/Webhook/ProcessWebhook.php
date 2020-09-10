<?php


namespace App\Listeners\Webhook;


use App\Contracts\Processor;
use App\Contracts\Webhook;
use App\Enums\QueueGroup;
use App\Enums\WebhookEventMapper;
use App\Events\Webhook\WebhookReceived;
use App\Services\Connectors\ConnectorManager;
use App\Utils\HasMarketplace;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProcessWebhook implements ShouldQueue
{
    use HasMarketplace;

    public $queue = QueueGroup::Webhook;
    protected $connectorManager;

    public function __construct(ConnectorManager $connectorManager)
    {
        $this->connectorManager = $connectorManager;
    }

    public function handle(WebhookReceived $webhookReceived)
    {
        $connector = $this->connectorManager->resolveConnector($webhookReceived->identifier);

        [$class, $method] = Arr::get($connector->mapper(), 'webhook.' . $webhookReceived->topic);

        if (!WebhookEventMapper::hasValue($method)) {
            throw new NotFoundHttpException($method . ' not supported for this marketplace');
        }

        // dispatch processor
        $processorServices = $this->connectorManager->makeService($connector->getProcessorServices());
        $processorServices = Collection::wrap($processorServices);

        // identify which processor to be used
        $resolvedProcessor = $processorServices->map(function ($processService) {
            return $this->connectorManager->makeService($processService);
        })->first(static function (Processor $processService) use ($class) {
            return $processService->processFor() === $class;
        });

        // save the rawData
        $this->getMarketplace($webhookReceived->identifier)->rawWebhooks()->create([
            'raw_data' => $webhookReceived->rawData->toArray(),
            'topic' => $webhookReceived->topic,
        ]);

        // call the specific processor
        $result = $resolvedProcessor->{$method}($webhookReceived);

        if ($result && $result instanceof Model) {
            $this->fireEventAfterProcess($result, $method);
        }
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
