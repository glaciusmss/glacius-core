<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class MarketplaceIntegrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $marketplaceData = parent::toArray($request);

        $marketplaceData['created_at'] = $this->pivot->created_at->toDateTimeString();
        $marketplaceData['updated_at'] = $this->pivot->updated_at->toDateTimeString();

        return Arr::except($marketplaceData, ['pivot']);
    }
}
