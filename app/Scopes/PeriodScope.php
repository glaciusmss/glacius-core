<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

trait PeriodScope
{
    public function scopeCreatedByPeriodToday(Builder $query)
    {
        return $query->where('created_at', '>', Carbon::now());
    }

    public function scopeCreatedByPeriodYesterday(Builder $query)
    {
        return $query->whereBetween('created_at', [Carbon::now()->subDays(2), Carbon::now()->subDay()]);
    }

    public function scopeCreatedByPeriodLastWeek(Builder $query)
    {
        return $query->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()]);
    }

    public function scopeCreatedByPeriodLastMonth(Builder $query)
    {
        return $query->whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::now()]);
    }

    public function scopeCreatedByPeriodLastQuarter(Builder $query)
    {
        return $query->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()]);
    }

    public function scopeCreatedByPeriodLastYear(Builder $query)
    {
        return $query->whereBetween('created_at', [Carbon::now()->subYear(), Carbon::now()]);
    }

    public function scopeCreatedByPreviousPeriodToday(Builder $query)
    {
        return $query->whereBetween('created_at', [Carbon::now()->subDay(), Carbon::now()]);
    }

    public function scopeCreatedByPreviousPeriodYesterday(Builder $query)
    {
        return $query->whereBetween('created_at', [Carbon::now()->subDays(2), Carbon::now()->subDay()]);
    }

    public function scopeCreatedByPreviousPeriodLastWeek(Builder $query)
    {
        return $query->whereBetween('created_at', [Carbon::now()->subWeeks(2), Carbon::now()->subWeek()]);
    }

    public function scopeCreatedByPreviousPeriodLastMonth(Builder $query)
    {
        return $query->whereBetween('created_at', [Carbon::now()->subMonths(2), Carbon::now()->subMonth()]);
    }

    public function scopeCreatedByPreviousPeriodLastQuarter(Builder $query)
    {
        return $query->whereBetween('created_at', [Carbon::now()->subMonths(6), Carbon::now()->subMonths(3)]);
    }

    public function scopeCreatedByPreviousPeriodLastYear(Builder $query)
    {
        return $query->whereBetween('created_at', [Carbon::now()->subYears(2), Carbon::now()->subYear()]);
    }
}
