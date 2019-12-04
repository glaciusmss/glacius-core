<?php
/**
 * @author Lam Kai Loon <lkloon123@hotmail.com>
 */

namespace App\Contracts;


use App\Enums\DeviceType;
use Illuminate\Http\Request;

interface OAuth
{
    /**
     * @return string $url
     */
    public function createAuth();

    /**
     * @param Request $request
     * @return DeviceType $deviceType
     */
    public function oAuthCallback(Request $request);

    public function deleteAuth();
}
