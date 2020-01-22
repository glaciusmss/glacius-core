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
            'size_per_page' => 'numeric',
            'sort_field' => 'string',
            'sort_order' => 'string'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'page' => (int)$this->page,
            'size_per_page' => (int)$this->size_per_page,
        ]);
    }
}
