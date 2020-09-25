<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ProductVariants.
 *
 * @property int $id
 * @property string $name
 * @property string $price
 * @property int|null $stock
 * @property array $meta
 * @property int $product_id
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @property-read \App\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariants newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariants newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariants query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariants whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariants whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariants whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariants whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariants wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariants whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariants whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariants whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductVariants extends Model
{
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
