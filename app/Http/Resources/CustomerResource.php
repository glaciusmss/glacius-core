<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CustomerResource
 * @mixin \App\Customer
 * @package App\Http\Resources
 */
class CustomerResource extends JsonResource
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
            'contact' => new ContactResource($this->contact),
            'addresses' => AddressResource::collection($this->addresses),
            'marketplace' => $this->marketplace,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
