<?php

namespace App\Http\Controllers;

use App\Http\Requests\Shop\StoreRequest;
use App\Http\Requests\Shop\UpdateRequest;
use App\Http\Resources\ShopResource;
use App\Shop;

class ShopController extends Controller
{
    public function index()
    {
        return ShopResource::collection(
            $this->getUser()->shops
        );
    }

    public function store(StoreRequest $request)
    {
        $shopData = $request->validated();

        $createdShop = $this->getUser()->shops()->create($shopData);

        return new ShopResource($createdShop);
    }

    public function show(Shop $shop)
    {
        return new ShopResource($shop);
    }

    public function update(UpdateRequest $request, Shop $shop)
    {
        $shopData = $request->validated();

        $shop->update($shopData);

        return response()->noContent();
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();

        return response()->noContent();
    }
}
