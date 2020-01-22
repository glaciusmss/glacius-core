<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static PeriodEnum Today()
 * @method static PeriodEnum Yesterday()
 * @method static PeriodEnum LastWeek()
 * @method static PeriodEnum LastMonth()
 * @method static PeriodEnum LastQuarter()
 * @method static PeriodEnum LastYear()
 */
final class PeriodEnum extends Enum
{
    const Today = 0;
    const Yesterday = 1;
    const LastWeek = 2;
    const LastMonth = 3;
    const LastQuarter = 4;
    const LastYear = 5;
}
