<?php


namespace App\Services\Shopify;


use App\Contracts\OAuth;
use App\Enums\DeviceType;
use App\Exceptions\NotSupportedException;
use App\Services\Shopify\Helpers\HasSdk;
use App\Services\Shopify\Validations\OAuth\OnInstallRule;
use App\Utils\HasMarketplace;
use App\Utils\HasShop;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPShopify\AuthHelper;
use PHPShopify\ShopifySDK;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OAuthService implements OAuth
{
    use HasShop, HasSdk, HasMarketplace;

    protected $cache;

    public function __construct(CacheContract $cache)
    {
        $this->cache = $cache;
    }

    public function configurations(): array
    {
        return [
            'config' => [
                'onDeleteAuthCallback' => false
            ],
            'validation' => [
                'onInstall' => OnInstallRule::class
            ]
        ];
    }

    public function onInstall(Request $request)
    {
        $this->cache->put('shopify:' . $this->getShopifyShop($request) . ':shop', $this->getShop());
        if ($request->header('x-request-from') === 'mobile') {
            $this->cache->put('shopify:' . $this->getShopifyShop($request) . ':device', DeviceType::Mobile());
        }

        $this->configureSdk([
            'ShopUrl' => $this->getShopifyShop($request)
        ]);

        $url = AuthHelper::createAuthRequest(
            'read_orders,read_customers,read_products,write_products',
            config('marketplace.shopify.redirect_url'),
            null,
            null,
            true
        );

        return compact('url');
    }

    public function onInstallCallback(Request $request)
    {
        $this->setShop(
            throw_unless(
                $this->cache->get('shopify:' . $this->getShopifyShop($request) . ':shop'),
                new NotFoundHttpException('please try again')
            )
        );

        $this->configureSdk([
            'ShopUrl' => $this->getShopifyShop($request)
        ]);

        $accessToken = AuthHelper::getAccessToken();

        $this->getShop()->marketplaces()->detach($this->getMarketplace('shopify')->id);
        $this->getShop()->marketplaces()->attach($this->getMarketplace('shopify')->id, [
            'token' => $accessToken,
            'meta->shopify_shop' => $this->getShopifyShop($request),
        ]);

        $device = $this->cache->get('shopify:' . $this->getShopifyShop($request) . ':device', DeviceType::Web());

        $this->cache->forget('shopify:' . $this->getShopifyShop($request) . ':device');
        $this->cache->forget('shopify:' . $this->getShopifyShop($request) . ':shop');

        return $device;
    }

    public function onDeleteAuth(Request $request)
    {
        /** @var \App\Marketplace $marketplace */
        $marketplace = $this->getShop()
            ->marketplaces()
            ->wherePivot('marketplace_id', '=', $this->getMarketplace('shopify')->id)
            ->first();

        $this->configureSdk([
            'ShopUrl' => $marketplace->pivot->meta['shopify_shop']
        ]);

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $marketplace->pivot->token
        ])->delete(ShopifySDK::getAdminUrl() . 'api_permissions/current.json');

        try {
            $response->throw();
        } catch (RequestException $exception) {
            throw new BadRequestHttpException('unable to disconnect, please try again');
        }

        $this->getShop()->marketplaces()->detach($marketplace->id);
    }

    public function onDeleteAuthCallback(Request $request)
    {
        throw new NotSupportedException('shopify dont need callback for delete');
    }

    protected function getShopifyShop(Request $request)
    {
        $shopifyShop = $request->input('shopify_shop', $request->input('shop'));

        if (Str::endsWith($shopifyShop, '/')) {
            $shopifyShop = substr($shopifyShop, 0, -1);
        }

        if (!Str::contains($shopifyShop, '.myshopify.com')) {
            $shopifyShop .= '.myshopify.com';
        }

        return $shopifyShop;
    }
}
