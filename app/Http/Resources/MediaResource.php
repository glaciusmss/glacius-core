<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductVariantResource.
 * @mixin \Spatie\MediaLibrary\MediaCollections\Models\Media
 */
class MediaResource extends JsonResource
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
            'url' => $this->getFullUrl(),
            'name' => $this->name,
            'file_name' => $this->file_name,
            'collection' => $this->collection_name,
        ];
    }
}
