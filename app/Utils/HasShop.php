<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/18/2019
 * Time: 9:56 AM.
 */

namespace App\Utils;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait HasShop
{
    protected $shop;

    /**
     * @param null $withRelations
     * @return \App\Shop
     */
    public function getShop($withRelations = null)
    {
        if ($this->shop) {
            return $this->shop;
        }

        $this->validateIfShopIdPresent();
        $shopId = request()->input('shop_id');

        /** @var HasMany $query */
        $query = auth()->user()->shops();

        if ($withRelations) {
            $query->with($withRelations);
        }

        return $this->shop = throw_unless(
            $query->find($shopId),
            NotFoundHttpException::class,
            'shop not found'
        );
    }

    protected function validateIfShopIdPresent()
    {
        request()->validate(['shop_id' => 'required']);
    }
}
