<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperContact
 */
class Contact extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'phone', 'email'
    ];

    protected $touches = [
        'contactable'
    ];

    public function contactable()
    {
        return $this->morphTo();
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
