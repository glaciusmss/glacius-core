<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperProductVariants
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
