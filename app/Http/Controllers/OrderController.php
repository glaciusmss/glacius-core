<?php

namespace App\Http\Controllers;

use App\DTO\Pagination;
use App\Http\Requests\Order\RetrieveRequest;
use App\Http\Resources\OrderResource;
use App\Order;
use App\SearchEngine\SearchRules\PartialSearchRule;
use App\Utils\Helper;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(RetrieveRequest $request)
    {
        $searchData = $request->get('search');
        $pagination = Pagination::makePaginationFromRequest($request);

        if ($searchData !== null) {
            $ordersData = Order::search(Helper::escapeElasticReservedChars($searchData))
                ->rule(PartialSearchRule::class)
                ->with('marketplace')
                ->where('shop_id', $this->getShop()->id)
                ->orderBy($pagination->getSortField(), $pagination->getSortOrder())
                ->paginate($pagination->getPerPage());
        } else {
            $ordersData = $this->getShop()->orders()
                ->with('marketplace')
                ->withPagination($pagination);
        }

        return OrderResource::collection($ordersData)->additional([
            'meta' => [
                'sort_field' => $pagination->getSortField(),
                'sort_order' => $pagination->getSortOrder(),
            ],
        ]);
    }

    public function show(Order $order)
    {
        return new OrderResource($order->load('marketplace'));
    }
}
