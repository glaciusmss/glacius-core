<?php

namespace App\Utils;

use App\Product;
use App\ProductVariant;
use Illuminate\Support\Arr;

trait FilterInternalField
{
    protected function filterInternalProductField(Product $product): array
    {
        return Arr::except($product->toArray(), ['id', 'meta', 'created_at', 'updated_at', 'deleted_at', 'product_variants', 'media', 'shop_id']);
    }

    protected function filterInternalProductVariantField(ProductVariant $productVariant): array
    {
        return Arr::except($productVariant->toArray(), ['id', 'meta', 'created_at', 'updated_at', 'deleted_at', 'product_id']);
    }
}
