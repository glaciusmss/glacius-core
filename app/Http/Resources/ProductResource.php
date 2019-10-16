<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $productData = parent::toArray($request);

        $productData['images'] = $this->getMedia()->pluck('file_name');

        $productVariants = collect($productData['product_variants']);

        $productData['product_variants'] = $productVariants->map(function ($item) {
            return Arr::except($item, ['meta', 'product_id']);
        })->toArray();

        return Arr::except($productData, ['meta', 'shop_id']);
    }
}
