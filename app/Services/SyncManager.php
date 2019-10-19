<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/21/2019
 * Time: 6:07 PM.
 */

namespace App\Services;


use App\Contracts\Sync as SyncContract;
use App\Enums\EventType;
use App\Enums\SyncDirection;
use App\Events\MarketplaceSynced;
use App\Events\MarketplaceSyncing;
use App\Marketplace;
use App\Shop;
use Illuminate\Database\Eloquent\Model;
use Psr\Log\LoggerInterface;

class SyncManager
{
    protected $shop;
    protected $model;
    /* @var LoggerInterface $logger */
    protected $logger;

    public function __construct(Shop $shop, Model $model)
    {
        $this->shop = $shop;
        $this->model = $model;
        $this->logger = \Log::channel('sync_' . strtolower(class_basename($model)));
    }

    public function syncWith(EventType $method)
    {
        $this->logger->info('job start on ' . now());
        $this->logger->info('connected marketplace: ' . $this->shop->marketplaces->implode('name', ','));

        foreach ($this->shop->marketplaces as $marketplace) {
            /** @var SyncContract $syncClass */
            if (!$syncClass = $this->makeSyncClassWith($marketplace)) {
                $this->logger->info('sync class not exist, skipped');
                continue;
            }

            $this->logger->info('resolved sync class [' . get_class($syncClass) . '] for ' . $marketplace->name);

            $methodName = 'when' . $method->key;
            if (!method_exists($syncClass, $methodName)) {
                $this->logger->info("method {$methodName} does not exist, skipped");
                continue;
            }

            //stop this sync if there's listener return false
            //this only works for synchronous listeners
            $shouldSkip = $this->shouldSkip(
                event(new MarketplaceSyncing($method, $this->model, SyncDirection::To(), $marketplace))
            );

            if ($shouldSkip || !$this->isSyncEnabled($marketplace)) {
                $this->logger->info('Sync' . class_basename($this->model) . ' for ' . $marketplace->name . ' is not activated, skipped');
                continue;
            }

            $this->logger->info("method {$methodName} exist, start syncing");
            $syncState = $syncClass->$methodName($this->model);

            //fire synced event after we finished each sync
            event(new MarketplaceSynced($method, $this->model, SyncDirection::To(), $marketplace, $syncState));
        }

        $this->logger->info('job end on ' . now());
    }

    protected function makeSyncClassWith(Marketplace $marketplace)
    {
        $namespace = __NAMESPACE__ . '\\' . ucfirst($marketplace->name) . '\\Syncs\\';
        $syncBaseClass = 'Sync' . class_basename($this->model);
        $syncClass = $namespace . $syncBaseClass;

        if (class_exists($syncClass)) {
            /** @var SyncContract $syncInstance */
            $syncInstance = app()->make($syncClass);
            return $syncInstance->setMarketplaceIntegration($marketplace->pivot);
        }

        return null;
    }

    protected function shouldSkip($result)
    {
        if (is_array($result)) {
            foreach ($result as $res) {
                if ($res === false) {
                    return true;
                }
            }
        }

        return $result === false;
    }

    protected function isSyncEnabled(Marketplace $marketplace)
    {
        $optionKey = 'is_' . strtolower(class_basename($this->model)) . '_sync_activated';

        return $this->shop->getSetting($optionKey, true, $marketplace->name);
    }
}
