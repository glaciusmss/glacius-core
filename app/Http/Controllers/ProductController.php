<?php

namespace App\Http\Controllers;

use App\Events\Product\ProductCreated;
use App\Events\Product\ProductUpdated;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    public function index()
    {
        return ProductResource::collection(
            $this->getShop()->products()->with('productVariants')->get()
        );
    }

    public function store(StoreRequest $request)
    {
        $productData = $request->only(['name', 'description']);
        $productVariantData = $request->input('product_variants')[0];

        /** @var Product $createdProduct */
        $createdProduct = $this->getShop()->products()->create($productData);
        $createdProduct->productVariants()->create(
            Arr::only($productVariantData, ['price', 'stock'])
        );
        $createdProduct->load('productVariants');

        $createdProduct->attachNewMedia(
            $request->input('images')
        );

        //fire product created event
        event(new ProductCreated($createdProduct));

        return new ProductResource($createdProduct);
    }

    public function show(Product $product)
    {
        return new ProductResource($product->load('productVariants'));
    }

    public function update(UpdateRequest $request, Product $product)
    {
        $productData = $request->only(['name', 'description']);
        $productVariantData = $request->input('product_variants')[0];

        $product->load('productVariants')->update($productData);
        $product->productVariants->find($productVariantData['id'])->update(
            Arr::only($productVariantData, ['price', 'stock'])
        );

        $product->attachNewMedia(
            $request->input('images')
        );

        //fire product updated event
        event(new ProductUpdated($product));

        return response()->noContent();
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->noContent();
    }
}
