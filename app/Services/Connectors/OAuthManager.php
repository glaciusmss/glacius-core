<?php


namespace App\Services\Connectors;

use App\Contracts\OAuth;
use App\Enums\ServiceMethod;
use App\Events\OAuthConnected;
use App\Events\OAuthDisconnected;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class OAuthManager
{
    protected $oAuthService;
    protected $identifier;

    public function __construct(string $identifier, OAuth $service)
    {
        $this->oAuthService = $service;
        $this->identifier = $identifier;
    }

    public function getConfiguration($key, $default = null)
    {
        return Arr::get($this->oAuthService->configurations(), $key, $default);
    }

    public function onInstall(Request $request)
    {
        $result = $this->oAuthService->onInstall($request);

        if (!$this->hasMethodEnabled('onInstallCallback')) {
            $this->registerWebhook();

            event(new OAuthConnected($this->oAuthService->getShop(), $this->identifier));
        }

        return value($result);
    }

    public function onInstallCallback(Request $request)
    {
        $result = $this->oAuthService->onInstallCallback($request);

        $this->registerWebhook();

        event(new OAuthConnected($this->oAuthService->getShop(), $this->identifier));

        return value($result);
    }

    public function onDeleteAuth(Request $request)
    {
        // remove webhook
        if (!$this->hasMethodEnabled('onDeleteAuthCallback')) {
            $this->removeWebhook();
        }

        $result = $this->oAuthService->onDeleteAuth($request);

        if (!$this->hasMethodEnabled('onDeleteAuthCallback')) {
            event(new OAuthDisconnected($this->oAuthService->getShop(), $this->identifier));
        }

        return value($result);
    }

    public function onDeleteAuthCallback(Request $request)
    {
        $this->removeWebhook();

        $result = $this->oAuthService->onDeleteAuth($request);

        event(new OAuthDisconnected($this->oAuthService->getShop(), $this->identifier));

        return value($result);
    }

    protected function registerWebhook()
    {
        $marketplace = $this->oAuthService->getShop()->marketplaces()->whereName($this->identifier)->firstOrFail();

        $this->getWebhookManager()->register($marketplace->pivot);
    }

    protected function removeWebhook()
    {
        $marketplace = $this->oAuthService->getShop()->marketplaces()->whereName($this->identifier)->firstOrFail();

        $this->getWebhookManager()->remove($marketplace->pivot);
    }

    protected function hasMethodEnabled($method): bool
    {
        if (!Arr::get($this->oAuthService->configurations(), "config.$method", true)) {
            return false;
        }

        return true;
    }

    protected function getWebhookManager(): WebhookManager
    {
        return app(ManagerBuilder::class)
            ->setIdentifier($this->identifier)
            ->setManagerClass(WebhookManager::class)
            ->setServiceMethod(ServiceMethod::WebhookService)
            ->build();
    }
}
