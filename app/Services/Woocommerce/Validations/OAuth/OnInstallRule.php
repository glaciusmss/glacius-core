<?php


namespace App\Services\Woocommerce\Validations\OAuth;


use App\Contracts\RequestValidation;

class OnInstallRule implements RequestValidation
{
    public function rules(): array
    {
        return [
            'woocommerce_store_url' => 'required'
        ];
    }
}
