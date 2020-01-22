<?php

namespace App\Scopes;


use App\Enums\PeriodEnum;

trait CustomerScope
{
    public function getCustomersByPeriod($period)
    {
        if ($period === PeriodEnum::Today) {
            return $this->customers()->createdByPeriodToday();
        }

        if ($period === PeriodEnum::Yesterday) {
            return $this->customers()->createdByPeriodYesterday();
        }

        if ($period === PeriodEnum::LastWeek) {
            return $this->customers()->createdByPeriodLastWeek();
        }

        if ($period === PeriodEnum::LastMonth) {
            return $this->customers()->createdByPeriodLastMonth();
        }

        if ($period === PeriodEnum::LastQuarter) {
            return $this->customers()->createdByPeriodLastQuarter();
        }

        if ($period === PeriodEnum::LastYear) {
            return $this->customers()->createdByPeriodLastYear();
        }

        return null;
    }

    public function getCustomersByPreviousPeriod($period)
    {
        if ($period === PeriodEnum::Today) {
            return $this->customers()->createdByPreviousPeriodToday();
        }

        if ($period === PeriodEnum::Yesterday) {
            return $this->customers()->createdByPreviousPeriodYesterday();
        }

        if ($period === PeriodEnum::LastWeek) {
            return $this->customers()->createdByPreviousPeriodLastWeek();
        }

        if ($period === PeriodEnum::LastMonth) {
            return $this->customers()->createdByPreviousPeriodLastMonth();
        }

        if ($period === PeriodEnum::LastQuarter) {
            return $this->customers()->createdByPreviousPeriodLastQuarter();
        }

        if ($period === PeriodEnum::LastYear) {
            return $this->customers()->createdByPreviousPeriodLastYear();
        }

        return null;
    }
}
