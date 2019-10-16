<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 2:50 PM.
 */

namespace App\Services\Shopee;


use App\Contracts\SdkFactory;
use App\Contracts\Webhook as WebhookContract;
use App\Enums\MarketplaceEnum;
use App\Services\BaseMarketplace;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Http\Request;

class Webhook extends BaseMarketplace implements WebhookContract
{
    protected $sdkFactory;

    public function __construct($config, CacheContract $cache, SdkFactory $sdkFactory)
    {
        parent::__construct($config, $cache);

        $this->sdkFactory = $sdkFactory;
    }

    public function register()
    {
        //not supported
    }

    public function validateHmac(Request $request)
    {
        $hmacHeader = $request->headers->get('authorization', '');
        $data = $this->config['webhook_url'] . '|' . file_get_contents('php://input');

        $calculatedHmac = hash_hmac('sha256', $data, $this->config['key']);

        return hash_equals($hmacHeader, $calculatedHmac);
    }

    public function dispatcher(Request $request)
    {
        // TODO: Implement dispatcher() method.
    }

    public function name()
    {
        return MarketplaceEnum::Shopee();
    }
}
