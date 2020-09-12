<?php

namespace App\Http\Controllers;

use App\Enums\DeviceType;
use App\Http\Requests\ConnectorRequest;
use App\Http\Requests\Woocommerce\CreateRequest;
use App\Services\Connectors\ConnectorManager;
use Illuminate\Http\Request;

class ConnectorController extends Controller
{
    public function create(string $identifier, ConnectorRequest $request, ConnectorManager $connectorManager)
    {
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
        $connectorManager->processOAuth($identifier, 'onInstallCallback', $request);

        return response()->redirectTo(
            config('app.frontend_url') . '/portal/account/marketplace-connections'
        );
    }

    public function destroy(string $identifier, ConnectorRequest $request, ConnectorManager $connectorManager)
    {
        $connectorManager->processOAuth($identifier, 'onDeleteAuth', $request);

        return response()->noContent();
    }

    public function woocommerceRedirect(string $identifier, Request $request)
    {
        $device = cache($identifier . ':' . $request->input('user_id') . ':device', DeviceType::Web());

        cache()->forget($identifier . ':' . $request->input('user_id') . ':device');

        if ($device->is(DeviceType::Mobile())) {
            return response()->redirectTo(
                config('app.mobile_scheme') . 'callback/settings/connections/marketplaces'
            );
        }

        return response()->redirectTo(
            config('app.frontend_url') . '/portal/account/marketplace-connections'
        );
    }

    public function webhooks(string $identifier, Request $request, ConnectorManager $connectorManager)
    {
        $connectorManager->dispatchWebhookToProcessor($identifier, $request);

        return response()->json();
    }
}
