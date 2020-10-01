<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class OrderResource.
 * @mixin \App\Order
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
        return [
            'id' => $this->id,
            'total_price' => $this->total_price,
            'subtotal_price' => $this->subtotal_price,
            'customer' => $this->when($this->customer, function () {
                return new CustomerResource($this->customer);
            }),
            'marketplace' => $this->marketplace,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
