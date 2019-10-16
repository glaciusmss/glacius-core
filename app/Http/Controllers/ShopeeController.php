<?php

namespace App\Http\Controllers;

use App\Contracts\OAuth;
use App\Http\Middleware\Webhook\Shopee as ValidateWebhookMiddleware;
use App\Http\Requests\Shopee\StoreRequest;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;

class ShopeeController extends Controller
{
    protected $oAuth;

    public function __construct(AuthManager $auth, OAuth $oAuth)
    {
        $this->middleware(ValidateWebhookMiddleware::class, ['only' => 'webhooks']);

        parent::__construct($auth);
        //contextual bounded marketplace manager
        $this->oAuth = $oAuth;
    }

    public function index()
    {
        //
    }

    public function create()
    {
        return response()->json([
            'url' => $this->oAuth->createAuth()
        ]);
    }

    public function store(StoreRequest $request)
    {
        $this->oAuth->oAuthCallback($request);

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

    public function delete(Request $request)
    {
        //this is an delete action
        $request->merge(['action' => 'delete']);

        $this->oAuth->oAuthCallback($request);

        return response()->redirectTo(
            config('app.frontend_url') . '/portal/account/marketplace-connections'
        );
    }

    public function destroy()
    {
        return response()->json([
            'url' => $this->oAuth->deleteAuth()
        ]);
    }

    public function webhooks(Request $request)
    {
        return response()->json();
    }
}
