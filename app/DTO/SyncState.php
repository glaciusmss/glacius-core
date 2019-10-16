<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 10/9/2019
 * Time: 9:43 AM.
 */

namespace App\DTO;


class SyncState
{
    protected $state;
    protected $errorMsg;

    public function __construct($state, $errorMsg = null)
    {
        $this->state = $state;
        $this->errorMsg = $errorMsg;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return null
     */
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }


}
