<?php

namespace App;

use App\Scopes\OrderScope;
use App\Scopes\PaginationScope;
use App\SearchEngine\IndexConfigurators\ProductIndexConfigurator;
use App\Utils\HasSyncTrasactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use ScoutElastic\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Product
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $shop_id
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @property array $meta
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductVariant[] $productVariants
 * @property-read int|null $product_variants_count
 * @property-read \App\Shop $shop
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SyncTransaction[] $syncTransactions
 * @property-read int|null $sync_transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product withPagination(\App\DTO\Pagination $pagination)
 * @mixin \Eloquent
 */
class Product extends Model implements HasMedia
{
    use InteractsWithMedia, HasSyncTrasactions, OrderScope, PaginationScope, Searchable, HasFactory;

    protected $indexConfigurator = ProductIndexConfigurator::class;

    protected $mapping = [
        'properties' => [
            'id' => ['type' => 'keyword'],
            'name' => ['type' => 'keyword'],
            'price' => ['type' => 'keyword'],
            'shop_id' => ['type' => 'keyword'],
            'updated_at' => ['type' => 'keyword'],
        ]
    ];

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array'
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->productVariants[0]->price,
            'shop_id' => $this->shop_id,
            'updated_at' => $this->updated_at,
        ];
    }

    public function attachNewMedia($newMedias)
    {
        $currentMedia = $this->getMedia();

        foreach (Arr::wrap($newMedias) as $newMedia) {
            if (!$currentMedia->pluck('file_name')->contains($newMedia)) {
                //search in temp media
                $tempMedia = TempMedia::whereFileName($newMedia)->firstOrFail();
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
        return $this->hasMany(ProductVariant::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_details')
            ->withTimestamps()
            ->withPivot(['quantity'])
            ->using(OrderDetail::class);
    }
}
