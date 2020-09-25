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
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function show(Order $order)
    {
        return new OrderResource($order->load('marketplace'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
