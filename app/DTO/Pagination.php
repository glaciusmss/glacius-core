<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 12/24/2019
 * Time: 12:45 PM.
 */

namespace App\DTO;

use App\Http\Requests\PaginationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Pagination
{
    protected $perPage;
    protected $sortField;
    protected $sortOrder;

    /**
     * Pagination constructor.
     * @param $perPage
     * @param $sortField
     * @param $sortOrder
     */
    public function __construct($perPage, $sortField, $sortOrder)
    {
        $this->perPage = $perPage;
        $this->sortField = $sortField;
        $this->sortOrder = $sortOrder;
    }

    public static function makePaginationFromRequest(Request $request)
    {
        if ($request instanceof PaginationRequest) {
            return new self(
                $request->input('per_page'),
                $request->input('sort_field', 'id'),
                $request->input('sort_order', 'desc')
            );
        }

        throw new HttpException(500, 'request is not an instance of PaginationRequest');
    }

    /**
     * @return int
     */
    public function getPerpage()
    {
        return $this->perPage;
    }

    /**
     * @return string
     */
    public function getSortField()
    {
        if (Str::contains($this->sortField, '.')) {
            return $this->sortField;
        }

        return $this->sortField;
    }

    /**
     * @return string
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}
