<?php
/**
 * @author Lam Kai Loon <lkloon123@hotmail.com>
 */

namespace App\Http\Requests\Statistic;


use App\Enums\PeriodEnum;
use App\Http\Requests\BaseRequest;
use BenSampo\Enum\Rules\EnumValue;

class MarketplaceSalesRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'period' => [new EnumValue(PeriodEnum::class)]
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'period' => (int)$this->period,
        ]);
    }
}
