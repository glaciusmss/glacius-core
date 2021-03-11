<?php

namespace App\Http\Controllers;

use App\Contracts\ServiceConnector;
use App\Enums\ServiceMethod;
use App\Enums\TokenType;
use App\Http\Requests\ConnectorRequest;
use App\Models\Token;
use App\Services\Connectors\ManagerBuilder;
use App\Services\Connectors\OAuthManager;
use App\Services\Connectors\WebhookManager;
use Illuminate\Http\Request;

class ConnectorController extends Controller
{
    public function create(string $identifier, ConnectorRequest $request, ManagerBuilder $managerBuilder)
    {
        // store the return url to cache
        \Cache::put($identifier . ':' . $this->getShop()->id . ':rtn_url', $request->input('rtn_url'));

        /** @var OAuthManager $oAuthManager */
        $oAuthManager = $managerBuilder
            ->setIdentifier($identifier)
            ->setManagerClass(OAuthManager::class)
            ->setServiceMethod(ServiceMethod::AuthService)
            ->build();

        return response()->json(
            $oAuthManager->onInstall($request)
        );
    }

    public function woocommerceCallback(string $identifier, ConnectorRequest $request, ManagerBuilder $managerBuilder)
    {
        /** @var OAuthManager $oAuthManager */
        $oAuthManager = $managerBuilder
            ->setIdentifier($identifier)
            ->setManagerClass(OAuthManager::class)
            ->setServiceMethod(ServiceMethod::AuthService)
            ->build();

        $oAuthManager->onInstallCallback($request);

        return response()->json();
    }

    public function oAuthCallback(string $identifier, ConnectorRequest $request, ManagerBuilder $managerBuilder)
    {
        /** @var OAuthManager $oAuthManager */
        $oAuthManager = $managerBuilder
            ->setIdentifier($identifier)
            ->setManagerClass(OAuthManager::class)
            ->setServiceMethod(ServiceMethod::AuthService)
            ->build();

        $shop = $oAuthManager->onInstallCallback($request);

        return response()->redirectTo(
            \Cache::pull($identifier.':'.$shop->id.':rtn_url')
//            config('app.frontend_url') . '/portal/account/marketplace-connections'
        );
    }

    public function destroy(string $identifier, ConnectorRequest $request, ManagerBuilder $managerBuilder)
    {
        /** @var OAuthManager $oAuthManager */
        $oAuthManager = $managerBuilder
            ->setIdentifier($identifier)
            ->setManagerClass(OAuthManager::class)
            ->setServiceMethod(ServiceMethod::AuthService)
            ->build();

        $oAuthManager->onDeleteAuth($request);

        return response()->noContent();
    }

    public function woocommerceRedirect(string $identifier, Request $request)
    {
        $token = Token::validateAndDelete(trim($request->input('user_id')), TokenType::WoocommerceConnect());

        return response()->redirectTo(
            \Cache::pull($identifier.':'.$token->meta['id'].':rtn_url')
        );

//        $device = \Cache::pull($identifier . ':' . $request->input('user_id') . ':device', DeviceType::Web());

//        cache()->forget($identifier . ':' . $request->input('user_id') . ':device');

//        if ($device->is(DeviceType::Mobile())) {
//            return response()->redirectTo(
//                config('app.mobile_scheme') . 'callback/settings/connections/marketplaces'
//            );
//        }
//
//        return response()->redirectTo(
//            config('app.frontend_url') . '/portal/account/marketplace-connections'
//        );
    }

    public function webhooks(string $identifier, Request $request, ManagerBuilder $managerBuilder)
    {
        /** @var WebhookManager $webhookManager */
        $webhookManager = $managerBuilder
            ->setIdentifier($identifier)
            ->setManagerClass(WebhookManager::class)
            ->setServiceMethod(ServiceMethod::WebhookService)
            ->build();

        $webhookManager->dispatchWebhookToProcessor($request);

        return response()->json();
    }
}
