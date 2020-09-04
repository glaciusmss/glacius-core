<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/25/2019
 * Time: 10:01 AM.
 */

namespace App\Services;


use App\Address;
use App\Contracts\Processor;
use App\Enums\AddressType;
use App\Enums\EventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class BaseProcessor extends BaseMarketplace implements Processor
{
    protected $event;

    public function process($event)
    {
        $this->log('job start on ' . now());
        $this->log('webhook ' . $event->topic . ' received from ' . $this->getMarketplace()->name);
        $this->event = $event;

        if (!$type = $this->mapWebhookTopicToEventType()) {
            return;
        }

        /** @var Collection $rawData */
        $rawData = $this->event->rawData;

        $this->getMarketplace()->rawWebhooks()->create([
            'raw_data' => $rawData->toArray(),
            'topic' => $this->event->topic,
        ]);

        $this->log('webhook type: ' . $type->key);
        $this->log('payload', $rawData->toArray());

        $result = $this->{'processWhen' . $type->key}($rawData);

        if ($result) {
            $this->fireEventAfterProcess($result);
        }

        $this->log('job end on ' . now());
    }

    protected function log($message, $context = [])
    {
        \Log::channel('process_' . $this->transformProcessForToModel())->info($message, $context ?? []);
    }

    protected function error($message, $context = [])
    {
        \Log::channel('process_' . $this->transformProcessForToModel())->error($message, $context ?? []);
    }

    protected function createBillingAddress($record, $billingAddress)
    {
        $this->createAddress($record, $billingAddress, AddressType::Billing());
    }

    protected function createShippingAddress($record, $shippingAddress)
    {
        $this->createAddress($record, $shippingAddress, AddressType::Shipping());
    }

    protected function createAddress($record, $address, $type = null)
    {
        $address = Arr::add($address, 'type', $type ?? AddressType::Default());

        /** @var Address $createdAddress */
        $createdAddress = $record->addAddress(
            Arr::only($address, ['type', 'address1', 'address2', 'city', 'state', 'zip', 'country'])
        );

        $createdAddress->addContact(
            Arr::only($address, ['first_name', 'last_name', 'phone', 'email'])
        );
    }

    private function fireEventAfterProcess($model)
    {
        $extractedClassFromObject = ucfirst(class_basename($model));
        $namespace = 'App\\Events\\' . $extractedClassFromObject . '\\';
        $eventName = $extractedClassFromObject . ucfirst($this->mapWebhookTopicToEventType()->key);
        $eventClass = $namespace . $eventName;

        if (class_exists($eventClass)) {
            event(new $eventClass($model));
        }
    }

    private function transformProcessForToModel()
    {
        return strtolower(class_basename($this->processFor()));
    }

    /**
     * @return EventType $eventType the event type base on webhook
     */
    abstract protected function mapWebhookTopicToEventType();

    /**
     * @param Collection $rawData
     * @return Model $model the created model
     */
    abstract protected function processWhenCreated(Collection $rawData);

    /**
     * @param Collection $rawData
     * @return Model $model the updated model
     */
    abstract protected function processWhenUpdated(Collection $rawData);

    /**
     * @param Collection $rawData
     * @return Model $model the deleted model
     */
    abstract protected function processWhenDeleted(Collection $rawData);

    /**
     * @return String $modelClass the model class name
     */
    abstract protected function processFor();
}
