<?php
/**
 * @author Lam Kai Loon <lkloon123@hotmail.com>
 */

namespace App\Http\Requests;

class PaginationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => 'numeric',
            'per_page' => 'numeric',
            'sort_field' => 'string',
            'sort_order' => 'string',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'page' => (int) $this->page,
            'per_page' => (int) $this->per_page,
        ]);
    }
}
