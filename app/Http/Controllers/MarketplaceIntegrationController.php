<?php

namespace App\Http\Controllers;

use App\Http\Resources\MarketplaceIntegrationResource;

class MarketplaceIntegrationController extends Controller
{
    public function index()
    {
        return MarketplaceIntegrationResource::collection(
            $this->getShop()->marketplaces
        );
    }
}
