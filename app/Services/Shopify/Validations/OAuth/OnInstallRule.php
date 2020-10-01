<?php

namespace App\Services\Shopify\Validations\OAuth;

use App\Contracts\RequestValidation;

class OnInstallRule implements RequestValidation
{
    public function rules(): array
    {
        return [
            'shopify_shop' => 'required',
        ];
    }
}
