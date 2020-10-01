<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/18/2019
 * Time: 9:56 AM.
 */

namespace App\Utils;

use App\SyncTransaction;

trait HasSyncTrasactions
{
    /**
     * @return SyncTransaction
     */
    public function syncTransactions()
    {
        return $this->morphMany(SyncTransaction::class, 'sync_transactional');
    }

    public function addSyncTransaction(array $attributes)
    {
        return $this->syncTransactions()->create($attributes);
    }

    public function updateSyncTransaction(SyncTransaction $syncTransaction, array $attributes)
    {
        return $syncTransaction->fill($attributes)->save();
    }

    public function deleteSyncTransaction(SyncTransaction $syncTransaction)
    {
        if ($this !== $syncTransaction->syncTransactional()->first()) {
            return false;
        }

        return $syncTransaction->delete();
    }
}
