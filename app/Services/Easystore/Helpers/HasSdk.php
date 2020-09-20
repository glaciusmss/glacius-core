<?php


namespace App\Services\Easystore\Helpers;


use App\Exceptions\NotSupportedException;
use EasyStore\Client as EasyStoreClient;
use Illuminate\Support\Arr;

trait HasSdk
{
    public function getSdk(array $configuration = null)
    {
        if (!Arr::hasAny($configuration, ['shop'])) {
            throw new NotSupportedException('some configuration missing');
        }

        return new EasyStoreClient(array_merge([
            'client_id' => config('easystore.key'),
            'client_secret' => config('easystore.secret'),
        ], $configuration));
    }
}
