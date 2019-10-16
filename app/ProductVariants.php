<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ProductVariants
 *
 * @property int $id
 * @property string $name
 * @property float $price
 * @property int|null $stock
 * @property array|null $meta
 * @property int $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductVariants newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductVariants newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductVariants query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductVariants whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductVariants whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductVariants whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductVariants whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductVariants wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductVariants whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductVariants whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProductVariants whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductVariants extends Model
{
    protected $fillable = [
        'name', 'price', 'stock', 'meta'
    ];

    protected $casts = [
        'meta' => 'array'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
