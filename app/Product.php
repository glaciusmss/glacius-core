<?php

namespace App;

use App\Events\Product\ProductDeleted;
use App\Utils\HasSyncTrasactions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * App\Product
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $shop_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array|null $meta
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductVariants[] $productVariants
 * @property-read int|null $product_variants_count
 * @property-read \App\Shop $shop
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SyncTransaction[] $syncTransactions
 * @property-read int|null $sync_transactions_count
 */
class Product extends Model implements HasMedia
{
    use HasMediaTrait, HasSyncTrasactions;

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
