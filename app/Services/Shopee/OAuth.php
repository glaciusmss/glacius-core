<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 12:06 PM.
 */

namespace App\Services\Shopee;


use App\Contracts\OAuth as OAuthContract;
use App\Contracts\SdkFactory;
use App\Enums\DeviceType;
use App\Enums\MarketplaceEnum;
use App\Events\OAuthConnected;
use App\Events\OAuthDisconnected;
use App\Services\BaseMarketplace;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Shopee\ResponseData;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OAuth extends BaseMarketplace implements OAuthContract
{
    protected $cache;
    protected $sdkFactory;

    public function __construct(CacheContract $cache, SdkFactory $sdkFactory)
    {
        $this->cache = $cache;
        $this->sdkFactory = $sdkFactory;
    }

    public function createAuth()
    {
        return $this->buildAuthUrl();
    }

    public function oAuthCallback(Request $request)
    {
        $this->shop = throw_unless(
            $this->cache->get($this->name() . ':' . $request->input('session')),
            NotFoundHttpException::class,
            'please try again'
        );

        $isDeleteAction = $request->input('action') === 'delete';

        if (!$isDeleteAction) {
            $this->checkIfShopInAuthorizedList($request->input('shop_id'));
        }

        $this->getShop()->marketplaces()->detach($this->getMarketplace()->id);

        if (!$isDeleteAction) {
            $this->getShop()->marketplaces()->attach($this->getMarketplace()->id, [
                'meta->shopee_shop_id' => $request->input('shop_id')
            ]);
        }

        $device = $this->cache->get($this->name() . ':' . $request->input('session') . ':device', DeviceType::Web());

        $this->cache->forget($this->name() . ':' . $request->input('session'));
        $this->cache->forget($this->name() . ':' . $request->input('session') . ':device');

        if (!$isDeleteAction) {
            event(new OAuthConnected($this->getShop(), $this->name()));
        } else {
            event(new OAuthDisconnected($this->getShop(), $this->name()));
        }

        return $device;
    }

    public function deleteAuth()
    {
        return $this->buildAuthUrl(true);
    }

    public function name()
    {
        return MarketplaceEnum::Shopee();
    }

    protected function checkIfShopInAuthorizedList($shopeeShopId)
    {
        /** @var \Shopee\Client $sdk */
        $sdk = $this->sdkFactory->makeSdk();
        $response = $sdk->send(
            $sdk->newRequest('/api/v1/shop/get_partner_shop')
        );
        $response = new ResponseData($response);

        $found = false;
        foreach ($response->getData()['authed_shops'] as $shop) {
            if ((string)$shop['shopid'] === $shopeeShopId) {
                $found = true;
                break;
            }
        }

        throw_unless(
            $found,
            NotFoundHttpException::class,
            'please try again'
        );
    }

    protected function buildAuthUrl($isDelete = false)
    {
        $generatedSession = Str::orderedUuid()->toString();
        $this->cache->put($this->name() . ':' . $generatedSession, $this->getShop());
        if (request()->header('x-request-from') === 'mobile') {
            $this->cache->put($this->name() . ':' . $generatedSession . ':device', DeviceType::Mobile());
        }

        $redirectUrl = $this->getConfig('redirect_url') . ($isDelete ? '/delete' : '') . '?session=' . $generatedSession;

        return 'https://partner.shopeemobile.com/api/v1/shop/' . ($isDelete ? 'cancel_auth_partner' : 'auth_partner') .
            '?id=' . $this->getConfig('id') .
            '&token=' . hash('sha256', $this->getConfig('key') . $redirectUrl) .
            '&redirect=' . $redirectUrl;
    }
}
