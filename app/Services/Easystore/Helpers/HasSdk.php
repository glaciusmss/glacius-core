<?php


namespace App\Services\Easystore\Helpers;


use App\Exceptions\NotSupportedException;
use Illuminate\Support\Arr;
use EasyStore\Client as EasyStoreClient;

trait HasSdk
{
    public function getSdk(array $configuration = null)
    {
        if (!Arr::hasAny($configuration, ['shop'])) {
            throw new NotSupportedException('some configuration missing');
        }

        return new EasyStoreClient(array_merge([
            'client_id' => config('marketplace.easystore.key'),
            'client_secret' => config('marketplace.easystore.secret'),
        ], $configuration));
    }
}
