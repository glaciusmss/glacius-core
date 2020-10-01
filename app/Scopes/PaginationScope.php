<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 12/24/2019
 * Time: 12:50 PM.
 */

namespace App\Scopes;

use App\DTO\Pagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait PaginationScope
{
    public function scopeWithPagination(Builder $query, Pagination $pagination)
    {
        return $query
            ->orderBy($this->withTableName($pagination), $pagination->getSortOrder())
            ->paginate($pagination->getPerPage());
    }

    private function withTableName(Pagination $pagination)
    {
        if (Str::contains($pagination->getSortField(), '.')) {
            return $pagination->getSortField();
        }

        return $this->getTable().'.'.$pagination->getSortField();
    }
}
