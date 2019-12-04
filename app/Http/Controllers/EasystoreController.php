<?php

namespace App\Http\Controllers;

use App\Contracts\OAuth;
use App\Contracts\Webhook;
use App\Enums\DeviceType;
use App\Http\Middleware\Webhook\Easystore as ValidateWebhookMiddleware;
use App\Http\Requests\Easystore\CreateRequest;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;

class EasystoreController extends Controller
{
    protected $oAuth;
    protected $webhook;

    public function __construct(AuthManager $auth, OAuth $oAuth, Webhook $webhook)
    {
        $this->middleware(ValidateWebhookMiddleware::class, ['only' => 'webhooks']);

        parent::__construct($auth);
        //contextual bounded marketplace manager
        $this->oAuth = $oAuth;
        $this->webhook = $webhook;
    }

    public function index()
    {
        //
    }

    public function create(CreateRequest $request)
    {
        return response()->json([
            'url' => $this->oAuth->createAuth()
        ]);
    }

    public function store(Request $request)
    {
        $device = $this->oAuth->oAuthCallback($request);

        if ($device->is(DeviceType::Mobile())) {
            return response()->redirectTo(
                config('app.mobile_scheme') . 'callback/settings/connections/marketplaces'
            );
        }

        return response()->redirectTo(
            config('app.frontend_url') . '/portal/account/marketplace-connections'
        );
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy()
    {
        $this->oAuth->deleteAuth();

        return response()->noContent();
    }

    public function webhooks(Request $request)
    {
        $this->webhook->dispatcher($request);

        return response()->json();
    }
}
