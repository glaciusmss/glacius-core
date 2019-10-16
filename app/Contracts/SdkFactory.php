<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 12:12 PM.
 */

namespace App\Contracts;


interface SdkFactory
{
    public function setupSdk(array $config = null, array $extras = []);

    public function getSdk();

    public function makeSdk();

    public function getConfig();

    public function clearCache();
}
