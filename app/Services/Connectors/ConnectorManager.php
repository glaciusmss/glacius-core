<?php


namespace App\Services\Connectors;


use App\Contracts\Configurable;
use App\Contracts\Connector;
use App\Contracts\OAuth;
use App\Contracts\ResolvesConnector;
use App\Contracts\Webhook;
use App\Events\OAuthConnected;
use App\Events\OAuthDisconnected;
use App\Events\Webhook\WebhookReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ConnectorManager
{
    protected $resolvedConnector;

    public function processOAuth(string $identifier, string $methodToBeCalled, ...$parameters)
    {
        $authService = $this->makeService(
            $this->resolveConnector($identifier)->getAuthService()
        );

        $this->removeWebhook($identifier, $methodToBeCalled, $authService);

        /** @var OAuth $authService */
        $result = $authService->{$methodToBeCalled}(...$parameters);

        // register webhooks before fire event
        $this->registerWebhook($identifier, $methodToBeCalled, $authService);

        $this->fireOAuthEvents($identifier, $methodToBeCalled, $authService);

        return value($result);
    }

    public function resolveConnector(string $identifier): Connector
    {
        if ($this->resolvedConnector) {
            return $this->resolvedConnector;
        }

        return $this->resolvedConnector = app(ResolvesConnector::class)->findConnector($identifier);
    }

    public function dispatchWebhookToProcessor(string $identifier, Request $request)
    {
        $connector = $this->resolveConnector($identifier);
        /** @var Webhook $webhookService */
        $webhookService = $this->makeService($connector->getWebhookService());

        $topic = $webhookService->getTopic($request);
        $rawData = $webhookService->mergeExtraDataBeforeProcess(Collection::wrap($request->all()), $request);

        WebhookReceived::dispatch($topic, $rawData, $identifier);
    }

    protected function registerWebhook(string $identifier, string $methodToBeCalled, OAuth $authService)
    {
        if ($methodToBeCalled === 'onInstall' && !$this->hasMethodEnabled($authService, 'onInstallCallback')) {
            $marketplace = $authService->getShop()->marketplaces()->whereName($identifier)->firstOrFail();

            $webhookService = $this->makeService(
                $this->resolveConnector($identifier)->getWebhookService()
            );

            $webhookService->register($marketplace->pivot);
            return;
        }

        if ($methodToBeCalled === 'onInstallCallback') {
            $marketplace = $authService->getShop()->marketplaces()->whereName($identifier)->firstOrFail();

            $webhookService = $this->makeService(
                $this->resolveConnector($identifier)->getWebhookService()
            );

            $webhookService->register($marketplace->pivot);
        }
    }

    protected function removeWebhook(string $identifier, string $methodToBeCalled, OAuth $authService)
    {
        if ($methodToBeCalled === 'onDeleteAuth' && !$this->hasMethodEnabled($authService, 'onDeleteAuthCallback')) {
            $marketplace = $authService->getShop()->marketplaces()->whereName($identifier)->firstOrFail();

            $webhookService = $this->makeService(
                $this->resolveConnector($identifier)->getWebhookService()
            );

            $webhookService->remove($marketplace->pivot);
            return;
        }

        if ($methodToBeCalled === 'onDeleteAuthCallback') {
            $marketplace = $authService->getShop()->marketplaces()->whereName($identifier)->firstOrFail();

            $webhookService = $this->makeService(
                $this->resolveConnector($identifier)->getWebhookService()
            );

            $webhookService->remove($marketplace->pivot);
        }
    }

    protected function fireOAuthEvents(string $identifier, string $methodToBeCalled, OAuth $authService): void
    {
        if ($methodToBeCalled === 'onInstall' && !$this->hasMethodEnabled($authService, 'onInstallCallback')) {
            // this will be fire if onInstallCallback is not enabled
            event(new OAuthConnected($authService->getShop(), $identifier));
            return;
        }

        if ($methodToBeCalled === 'onInstallCallback') {
            event(new OAuthConnected($authService->getShop(), $identifier));
            return;
        }

        if ($methodToBeCalled === 'onDeleteAuth' && !$this->hasMethodEnabled($authService, 'onDeleteAuthCallback')) {
            // this will be fire if onDeleteAuthCallback is not enabled
            event(new OAuthDisconnected($authService->getShop(), $identifier));
            return;
        }

        if ($methodToBeCalled === 'onDeleteAuthCallback') {
            event(new OAuthDisconnected($authService->getShop(), $identifier));
        }
    }

    protected function hasMethodEnabled(Configurable $configurable, $method): bool
    {
        if (!Arr::get($configurable->configurations(), "config.$method", true)) {
            return false;
        }

        return true;
    }

    public function makeService($service)
    {
        if (is_string($service)) {
            return app($service);
        }

        return $service;
    }
}
