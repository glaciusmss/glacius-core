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
}
