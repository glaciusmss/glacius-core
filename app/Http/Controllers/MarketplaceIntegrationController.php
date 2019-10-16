<?php

namespace App\Http\Controllers;

use App\Http\Resources\MarketplaceIntegrationResource;
use App\MarketplaceIntegration;
use Illuminate\Http\Request;

class MarketplaceIntegrationController extends Controller
{
    public function index()
    {
        return MarketplaceIntegrationResource::collection(
            $this->getShop('marketplaces')->marketplaces
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\MarketplaceIntegration $marketplaceIntegration
     * @return \Illuminate\Http\Response
     */
    public function show(MarketplaceIntegration $marketplaceIntegration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\MarketplaceIntegration $marketplaceIntegration
     * @return \Illuminate\Http\Response
     */
    public function edit(MarketplaceIntegration $marketplaceIntegration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\MarketplaceIntegration $marketplaceIntegration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MarketplaceIntegration $marketplaceIntegration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\MarketplaceIntegration $marketplaceIntegration
     * @return \Illuminate\Http\Response
     */
    public function destroy(MarketplaceIntegration $marketplaceIntegration)
    {
        //
    }
}
