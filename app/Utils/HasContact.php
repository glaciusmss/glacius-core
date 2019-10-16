<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/18/2019
 * Time: 9:56 AM.
 */

namespace App\Utils;


use App\Contact;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function updateContact(Contact $contact, array $attributes)
    {
        return $contact->fill($attributes)->save();
    }

    public function deleteContact(Contact $contact)
    {
        if ($this !== $contact->contactable()->first()) {
            return false;
        }

        return $contact->delete();
    }
}
