<?php

namespace App\Http\Controllers;

use App\DTO\Pagination;
use App\Events\Product\ProductCreated;
use App\Events\Product\ProductDeleted;
use App\Events\Product\ProductUpdated;
use App\Http\Requests\Order\RetrieveRequest;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    public function index(RetrieveRequest $request)
    {
        $pagination = Pagination::makePaginationFromRequest($request);

        $productsData = $this->getShop()->products()
            ->with('productVariants')
            ->withPagination($pagination);

        return ProductResource::collection($productsData)->additional([
            'meta' => [
                'sort_field' => $pagination->getSortField(),
                'sort_order' => $pagination->getSortOrder(),
            ]
        ]);
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

        $createdProduct->attachNewMedia(
            $request->input('images')
        );

        //fire product created event
        event(new ProductCreated($createdProduct, $this->getShop()));

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
        event(new ProductUpdated($product, $this->getShop()));

        return response()->noContent();
    }

    public function destroy(Product $product)
    {
        $product->delete();

        //fire product deleted event
        event(new ProductDeleted($product, $this->getShop()));

        return response()->noContent();
    }
}
