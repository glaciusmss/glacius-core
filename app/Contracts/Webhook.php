<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 11:35 AM.
 */

namespace App\Contracts;

use App\MarketplaceIntegration;
use Illuminate\Http\Request;
use Illuminate\Support\Enumerable;

interface Webhook
{
    public function register(MarketplaceIntegration $marketplaceIntegration);

    public function remove(MarketplaceIntegration $marketplaceIntegration);

    public function validateHmac(Request $request);

    public function getTopicFromRequest(Request $request): string;

    public function mergeExtraDataBeforeProcess(Enumerable $rawData, Request $request): Enumerable;
}
