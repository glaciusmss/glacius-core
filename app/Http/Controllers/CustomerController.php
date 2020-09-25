<?php

namespace App\Http\Controllers;

use App\Customer;
use App\DTO\Pagination;
use App\Http\Requests\Customer\RetrieveRequest;
use App\Http\Resources\CustomerResource;
use App\SearchEngine\SearchRules\PartialSearchRule;
use App\Utils\Helper;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(RetrieveRequest $request)
    {
        $searchData = $request->get('search');
        $pagination = Pagination::makePaginationFromRequest($request);

        if ($searchData !== null) {
            $customersData = Customer::search(Helper::escapeElasticReservedChars($searchData))
                ->rule(PartialSearchRule::class)
                ->with(['marketplace', 'contact', 'addresses'])
                ->where('shop_id', $this->getShop()->id)
                ->orderBy($pagination->getSortField(), $pagination->getSortOrder())
                ->paginate($pagination->getPerPage());
        } else {
            $customersData = $this->getShop()->customers()
                ->with(['marketplace', 'contact', 'addresses'])
                ->withPagination($pagination);
        }

        return CustomerResource::collection($customersData)->additional([
            'meta' => [
                'sort_field' => $pagination->getSortField(),
                'sort_order' => $pagination->getSortOrder(),
            ],
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

    /**
     * Display the specified resource.
     *
     * @param \App\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
