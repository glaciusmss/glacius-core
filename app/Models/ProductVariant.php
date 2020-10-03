<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProductVariant
 *
 * @property int $id
 * @property string $name
 * @property string $price
 * @property int|null $stock
 * @property array $meta
 * @property int $product_id
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductVariant extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
