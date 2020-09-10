<?php


namespace App\Services\Easystore\Validations\OAuth;


use App\Contracts\RequestValidation;

class OnInstallRule implements RequestValidation
{
    public function rules(): array
    {
        return [
            'easystore_shop' => 'required'
        ];
    }
}
