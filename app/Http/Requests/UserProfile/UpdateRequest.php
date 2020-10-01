<?php
/**
 * @author Lam Kai Loon <lkloon123@hotmail.com>
 */

namespace App\Http\Requests\UserProfile;

use App\Enums\GenderEnum;
use App\Http\Requests\BaseRequest;
use BenSampo\Enum\Rules\EnumValue;

class UpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone_number' => 'string',
            'gender' => [new EnumValue(GenderEnum::class)],
            'date_of_birth' => 'date',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'gender' => (int) $this->input('gender'),
        ]);
    }
}
