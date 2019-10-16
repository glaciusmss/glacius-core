<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static MarketplaceEnum Shopify()
 * @method static MarketplaceEnum Shopee()
 * @method static MarketplaceEnum WooCommerce()
 * @method static MarketplaceEnum EasyStore()
 */
final class MarketplaceEnum extends Enum
{
    const Shopify = 'shopify';
    const Shopee = 'shopee';
    const WooCommerce = 'woocommerce';
    const EasyStore = 'easystore';
}
