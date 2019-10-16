<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 11:35 AM.
 */

namespace App\Contracts;


use Illuminate\Http\Request;

interface Webhook
{
    public function register();

    public function validateHmac(Request $request);

    public function dispatcher(Request $request);
}
