<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/18/2019
 * Time: 9:56 AM.
 */

namespace App\Utils;


use App\Shop;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait HasShop
{
    protected $shop;

    /**
     * @param null $withRelations
     * @return \App\Shop
     */
    public function getShop(): Shop
    {
        if ($this->shop) {
            return $this->shop;
        }

        $this->validateIfShopIdPresent();
        $shopId = request()->input('shop_id');

        return $this->shop = throw_unless(
            auth()->user()->shops()->find($shopId),
            new NotFoundHttpException('shop not found')
        );
    }

    public function setShop(Shop $shop)
    {
        $this->shop = $shop;

        return $this;
    }

    protected function validateIfShopIdPresent()
    {
        request()->validate(['shop_id' => 'required']);
    }
}
