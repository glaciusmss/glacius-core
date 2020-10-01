<?php

namespace App\Services\Woocommerce;

use App\Contracts\OAuth;
use App\Enums\DeviceType;
use App\Enums\TokenType;
use App\Exceptions\NotSupportedException;
use App\Models\Token;
use App\Services\Woocommerce\Helpers\HasSdk;
use App\Services\Woocommerce\Validations\OAuth\OnInstallRule;
use App\Utils\HasMarketplace;
use App\Utils\HasShop;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OAuthService implements OAuth
{
    use HasShop, HasMarketplace, HasSdk;

    protected $cache;

    public function __construct(CacheContract $cache)
    {
        $this->cache = $cache;
    }

    public function configurations(): array
    {
        return [
            'config' => [
                'onDeleteAuthCallback' => false,
            ],
            'validation' => [
                'onInstall' => OnInstallRule::class,
            ],
        ];
    }

    public function onInstall(Request $request)
    {
        $generatedSession = Token::generateAndSave(TokenType::WoocommerceConnect(), $this->getShop()->attributesToArray())->token;
        $this->cache->put('woocommerce:'.$generatedSession.':shop', $this->getShop());
        $this->cache->put('woocommerce:'.$generatedSession.':woocommerce_store_url', $this->getWoocommerceStoreUrl($request));
        if (request()->header('x-request-from') === 'mobile') {
            $this->cache->put('woocommerce:'.$generatedSession.':device', DeviceType::Mobile());
        }

        $url = $this->getWoocommerceStoreUrl($request).'/wc-auth/v1/authorize'
            .'?app_name=GlaciusMSS.'.$generatedSession
            .'&scope=read_write'
            .'&user_id='.$generatedSession
            .'&return_url='.config('woocommerce.return_url')
            .'&callback_url='.config('woocommerce.callback_url');

        return compact('url');
    }

    public function onInstallCallback(Request $request)
    {
        $this->setShop(
            throw_unless(
                $this->cache->pull('woocommerce:'.$request->input('user_id').':shop'),
                new NotFoundHttpException('please try again')
            )
        );

        $woocommerceStoreUrl = throw_unless(
            $this->cache->pull('woocommerce:'.$request->input('user_id').':woocommerce_store_url'),
            new NotFoundHttpException('please try again')
        );

        $this->getShop()->marketplaces()->detach($this->getMarketplace('woocommerce')->id);
        $this->getShop()->marketplaces()->attach($this->getMarketplace('woocommerce')->id, [
            'meta->key' => $request->input('consumer_key'),
            'meta->secret' => $request->input('consumer_secret'),
            'meta->woocommerce_store_url' => $woocommerceStoreUrl,
            'meta->session_id' => $request->input('user_id'),
        ]);
    }

    public function onDeleteAuth(Request $request)
    {
        $this->getShop()->marketplaces()->detach($this->getMarketplace('woocommerce')->id);
    }

    public function onDeleteAuthCallback(Request $request)
    {
        throw new NotSupportedException('woocommerce dont need callback for delete');
    }

    protected function getWoocommerceStoreUrl(Request $request)
    {
        $woocommerceStoreUrl = $request->input('woocommerce_store_url');

        if (! Str::endsWith($woocommerceStoreUrl, '/')) {
            $woocommerceStoreUrl .= '/';
        }

        return $woocommerceStoreUrl;
    }
}
