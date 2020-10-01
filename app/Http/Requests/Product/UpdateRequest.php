<?php
/**
 * @author Lam Kai Loon <lkloon123@hotmail.com>
 */

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseRequest;

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
            'name' => 'string',
            'description' => 'string',
            'product_variants.*.price' => 'numeric|gt:0',
            'product_variants.*.stock' => 'nullable|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'product_variants.*.price.numeric' => 'The price field must be a number.',
            'product_variants.*.price.gt:0' => 'The price field must be greater than 0.',
            'product_variants.*.stock.numeric' => 'The stock field must be a number.',
            'product_variants.*.stock.min:0' => 'The stock field must be at least 0.',
        ];
    }
}
