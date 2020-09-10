<?php
/**
 * @author Lam Kai Loon <lkloon123@hotmail.com>
 */

namespace App\Contracts;


use App\Enums\DeviceType;
use Illuminate\Http\Request;

interface OAuth extends Configurable, HasShop
{
    /**
     * @param Request $request
     * @return string $url
     */
    public function onInstall(Request $request);

    /**
     * @param Request $request
     * @return DeviceType $deviceType
     */
    public function onInstallCallback(Request $request);

    public function onDeleteAuth(Request $request);

    public function onDeleteAuthCallback(Request $request);
}
