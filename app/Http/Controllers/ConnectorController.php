<?php

namespace App\Http\Controllers;

use App\Enums\TokenType;
use App\Http\Requests\ConnectorRequest;
use App\Services\Connectors\ConnectorManager;
use App\Token;
use Illuminate\Http\Request;

class ConnectorController extends Controller
{
    public function create(string $identifier, ConnectorRequest $request, ConnectorManager $connectorManager)
    {
        // store the return url to cache
        \Cache::put($identifier.':'.$this->getShop()->id.':rtn_url', $request->input('rtn_url'));

        return response()->json(
            $connectorManager->processOAuth($identifier, 'onInstall', $request)
        );
    }

    public function woocommerceCallback(string $identifier, ConnectorRequest $request, ConnectorManager $connectorManager)
    {
        $connectorManager->processOAuth($identifier, 'onInstallCallback', $request);

        return response()->json();
    }

    public function oAuthCallback(string $identifier, ConnectorRequest $request, ConnectorManager $connectorManager)
    {
        $shop = $connectorManager->processOAuth($identifier, 'onInstallCallback', $request);

        return response()->redirectTo(
            \Cache::pull($identifier.':'.$shop->id.':rtn_url')
//            config('app.frontend_url') . '/portal/account/marketplace-connections'
        );
    }

    public function destroy(string $identifier, ConnectorRequest $request, ConnectorManager $connectorManager)
    {
        $connectorManager->processOAuth($identifier, 'onDeleteAuth', $request);

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

    public function webhooks(string $identifier, Request $request, ConnectorManager $connectorManager)
    {
        $connectorManager->dispatchWebhookToProcessor($identifier, $request);

        return response()->json();
    }
}
