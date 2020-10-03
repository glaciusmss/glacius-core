<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/18/2019
 * Time: 9:56 AM.
 */

namespace App\Models\MorphHelper;

use App\Models\Contact;

trait HasContact
{
    /**
     * @return Contact
     */
    public function contact()
    {
        return $this->morphOne(Contact::class, 'contactable');
    }

    public function addContact(array $attributes)
    {
        return $this->contact()->create($attributes);
    }

    public function updateContact(array $attributes)
    {
        return $this->contact->fill($attributes)->save();
    }

    public function deleteContact()
    {
        return $this->contact->delete();
    }
}
