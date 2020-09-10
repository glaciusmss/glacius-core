<?php

namespace App;

use App\Events\Product\ProductDeleted;
use App\Scopes\OrderScope;
use App\Scopes\PaginationScope;
use App\Utils\HasSyncTrasactions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @mixin IdeHelperProduct
 */
class Product extends Model implements HasMedia
{
    use InteractsWithMedia, HasSyncTrasactions, OrderScope, PaginationScope;

    protected $fillable = [
        'name', 'description', 'meta'
    ];

    protected $casts = [
        'meta' => 'array'
    ];

    protected $dispatchesEvents = [
        'deleted' => ProductDeleted::class,
    ];

    public function attachNewMedia($newMedias)
    {
        $currentMedia = $this->getMedia();

        foreach (Arr::wrap($newMedias) as $newMedia) {
            if (!$currentMedia->pluck('file_name')->contains($newMedia)) {
                //search in temp media
                $tempMedia = TempMedia::whereFileName($newMedia)->first();
                $this->attachTempMedia($tempMedia);
                $tempMedia->delete();
            }
        }

        $this->detachDeletedMedia(
            $currentMedia,
            $newMedias
        );

        //refresh instance to avoid media being cached
        $this->unsetRelation('media');
    }

    public function attachTempMedia(TempMedia $tempMedia, $collectionName = 'default', string $diskName = '')
    {
        $this->addMedia(\Storage::path($tempMedia->path))
            ->usingName($tempMedia->original_file_name)
            ->usingFileName($tempMedia->file_name)
            ->toMediaCollection($collectionName, $diskName);
    }

    protected function detachDeletedMedia(Collection $previousMedia, $newMedias)
    {
        $mediaToDetach = array_diff($previousMedia->pluck('file_name')->toArray(), $newMedias);

        $previousMedia->whereIn('file_name', $mediaToDetach)
            ->each(function ($item) {
                $item->delete();
            });
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariants::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_details')
            ->withTimestamps()
            ->withPivot(['quantity'])
            ->using(OrderDetails::class);
    }
}
