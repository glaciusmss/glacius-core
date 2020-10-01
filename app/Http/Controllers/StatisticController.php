<?php

namespace App\Http\Controllers;

use App\Enums\PeriodEnum;
use App\Http\Requests\Statistic\MarketplaceSalesRequest;
use App\Http\Requests\Statistic\NewCustomerRequest;
use App\Http\Resources\MarketplaceResource;
use App\Utils\Helper;

class StatisticController extends Controller
{
    public function marketplaceSales(MarketplaceSalesRequest $request)
    {
        $period = $request->input('period') ?: PeriodEnum::LastMonth;

        $finalResult = [
            'total_current_count' => 0,
            'total_previous_count' => 0,
            'total_current_sales' => 0,
            'total_previous_sales' => 0,
            'total_count_percentage' => 0,
            'total_sales_percentage' => 0,
        ];
        $marketplaces = $this->getShop()->marketplaces;

        foreach ($marketplaces as $marketplace) {
            $currentCount = $marketplace->getOrdersByPeriod($period)->count();
            $previousCount = $marketplace->getOrdersByPreviousPeriod($period)->count();
            $currentSales = $marketplace->getOrdersByPeriod($period)->sum('total_price');
            $previousSales = $marketplace->getOrdersByPreviousPeriod($period)->sum('total_price');

            $finalResult['marketplaces'][] = [
                'current_count' => $currentCount,
                'previous_count' => $previousCount,
                'current_sales' => $currentSales,
                'previous_sales' => $previousSales,
                'count_percentage' => Helper::calculatePercentage($previousCount, $currentCount),
                'sales_percentage' => Helper::calculatePercentage($previousSales, $currentSales),
                'marketplace' => new MarketplaceResource($marketplace),
            ];
            $finalResult['total_current_count'] += $currentCount;
            $finalResult['total_previous_count'] += $previousCount;
            $finalResult['total_current_sales'] += $currentSales;
            $finalResult['total_previous_sales'] += $previousSales;
        }

        $finalResult['total_count_percentage'] = Helper::calculatePercentage($finalResult['total_previous_count'], $finalResult['total_current_count']);
        $finalResult['total_sales_percentage'] = Helper::calculatePercentage($finalResult['total_previous_sales'], $finalResult['total_current_sales']);

        return response()->json($finalResult);
    }

    public function newCustomer(NewCustomerRequest $request)
    {
        $period = $request->input('period') ?: PeriodEnum::LastMonth;

        $finalResult = [
            'total_current_count' => 0,
            'total_previous_count' => 0,
            'total_count_percentage' => 0,
        ];
        $marketplaces = $this->getShop()->marketplaces;

        foreach ($marketplaces as $marketplace) {
            $currentCount = $marketplace->getCustomersByPeriod($period)->count();
            $previousCount = $marketplace->getCustomersByPreviousPeriod($period)->count();

            $finalResult['marketplaces'][] = [
                'current_count' => $currentCount,
                'previous_count' => $previousCount,
                'count_percentage' => Helper::calculatePercentage($previousCount, $currentCount),
                'marketplace' => new MarketplaceResource($marketplace),
            ];
            $finalResult['total_current_count'] += $currentCount;
            $finalResult['total_previous_count'] += $previousCount;
        }

        $finalResult['total_count_percentage'] = Helper::calculatePercentage($finalResult['total_previous_count'], $finalResult['total_current_count']);

        return response()->json($finalResult);
    }
}
