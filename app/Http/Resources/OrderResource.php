<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

/**
 * Class OrderResource
 * @mixin \App\Order
 * @package App\Http\Resources
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $orderData = parent::toArray($request);

        $orderData['marketplace'] = $this->marketplace;

        return Arr::except($orderData, ['meta', 'marketplace_id', 'shop_id', 'shop']);
    }
}
