<?php


namespace App\Services\Woocommerce\Helpers;


use App\Exceptions\NotSupportedException;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Arr;

trait HasSdk
{
    public function getSdk(array $configuration = null)
    {
        if (!Arr::hasAny($configuration, ['woocommerceStoreUrl', 'consumerKey', 'consumerSecret'])) {
            throw new NotSupportedException('some configuration missing');
        }

        return new Client(
            $configuration['woocommerceStoreUrl'],
            $configuration['consumerKey'],
            $configuration['consumerSecret'],
            array_merge(
                Arr::except($configuration, ['woocommerceStoreUrl', 'consumerKey', 'consumerSecret']),
                ['timeout' => config('woocommerce.timeout')]
            )
        );
    }
}
