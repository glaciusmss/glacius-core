<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/21/2019
 * Time: 5:43 PM.
 */

namespace App\Contracts;


use App\DTO\SyncState;
use App\MarketplaceIntegration;
use Illuminate\Database\Eloquent\Model;

interface Sync
{
    /**
     * @param Model $model
     * @return SyncState
     */
    public function whenCreated(Model $model);

    /**
     * @param Model $model
     * @return SyncState
     */
    public function whenUpdated(Model $model);

    /**
     * @param Model $model
     * @return SyncState
     */
    public function whenDeleted(Model $model);

    public function withExisting();

    public function setMarketplaceIntegration(MarketplaceIntegration $marketplaceIntegration);
}
