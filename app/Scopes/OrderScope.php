<?php

namespace App\Scopes;


use App\Enums\PeriodEnum;

trait OrderScope
{
    public function getOrdersByPeriod($period)
    {
        if ($period === PeriodEnum::Today) {
            return $this->orders()->createdByPeriodToday();
        }

        if ($period === PeriodEnum::Yesterday) {
            return $this->orders()->createdByPeriodYesterday();
        }

        if ($period === PeriodEnum::LastWeek) {
            return $this->orders()->createdByPeriodLastWeek();
        }

        if ($period === PeriodEnum::LastMonth) {
            return $this->orders()->createdByPeriodLastMonth();
        }

        if ($period === PeriodEnum::LastQuarter) {
            return $this->orders()->createdByPeriodLastQuarter();
        }

        if ($period === PeriodEnum::LastYear) {
            return $this->orders()->createdByPeriodLastYear();
        }

        return null;
    }

    public function getOrdersByPreviousPeriod($period)
    {
        if ($period === PeriodEnum::Today) {
            return $this->orders()->createdByPreviousPeriodToday();
        }

        if ($period === PeriodEnum::Yesterday) {
            return $this->orders()->createdByPreviousPeriodYesterday();
        }

        if ($period === PeriodEnum::LastWeek) {
            return $this->orders()->createdByPreviousPeriodLastWeek();
        }

        if ($period === PeriodEnum::LastMonth) {
            return $this->orders()->createdByPreviousPeriodLastMonth();
        }

        if ($period === PeriodEnum::LastQuarter) {
            return $this->orders()->createdByPreviousPeriodLastQuarter();
        }

        if ($period === PeriodEnum::LastYear) {
            return $this->orders()->createdByPreviousPeriodLastYear();
        }

        return null;
    }
}
