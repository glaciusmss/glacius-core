<?php


namespace App\Services\Easystore;


use App\Contracts\OAuth;
use App\Enums\DeviceType;
use App\Exceptions\NotSupportedException;
use App\Services\Easystore\Helpers\HasSdk;
use App\Services\Easystore\Validations\OAuth\OnInstallRule;
use App\Utils\HasMarketplace;
use App\Utils\HasShop;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            ],
        ];
    }

    public function onInstall(Request $request)
    {
        $this->cache->put('easystore:' . $this->getEasystoreShop($request) . ':shop', $this->getShop());
        if ($request->header('x-request-from') === 'mobile') {
            $this->cache->put('easystore:' . $this->getEasystoreShop($request) . ':device', DeviceType::Mobile());
        }

        $sdk = $this->getSdk([
            'shop' => $this->getEasystoreShop($request),
            'scopes' => 'read_orders,read_customers,read_products,write_products',
            'redirect_uri' => config('easystore.redirect_url')
        ]);

        return ['url' => $sdk->buildAuthUrl()];
    }

    public function onInstallCallback(Request $request)
    {
        $this->shop = throw_unless(
            $this->cache->pull('easystore:' . $this->getEasystoreShop($request) . ':shop'),
            new NotFoundHttpException('please try again')
        );

        $accessToken = $this->getSdk([
            'shop' => $this->getEasystoreShop($request)
        ])->getAccessToken();

        $this->getShop()->marketplaces()->detach($this->getMarketplace('easystore')->id);
        $this->getShop()->marketplaces()->attach($this->getMarketplace('easystore')->id, [
            'token' => $accessToken,
            'meta->easystore_shop' => $this->getEasystoreShop($request),
        ]);

        return $this->getShop();
    }

    public function onDeleteAuth(Request $request)
    {
        $this->getShop()->marketplaces()->detach($this->getMarketplace('easystore')->id);
    }

    public function onDeleteAuthCallback(Request $request)
    {
        throw new NotSupportedException('easystore dont need callback for delete');
    }

    protected function getEasystoreShop(Request $request)
    {
        $shopifyShop = $request->input('easystore_shop', $request->input('shop'));

        if (Str::endsWith($shopifyShop, '/')) {
            $shopifyShop = substr($shopifyShop, 0, -1);
        }

        if (!Str::contains($shopifyShop, '.easy.co')) {
            $shopifyShop .= '.easy.co';
        }

        return $shopifyShop;
    }
}
