<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $productId = $this->route('product');

        $isCreating = $this->getMethod() == 'POST';
        $imageRule = $isCreating ? 'required|image|mimes:jpeg,png,jpg,webp|max:2048' : 'sometimes|nullable|image|mimes:jpeg,png,jpg,webp|max:2048';

        return [
            'name'                   => 'required|string|max:255',
            'detail'                 => 'nullable|string',
            // 'sku'                    => 'nullable|string|max:255|unique:products,sku,' . $productId,
            'unit_id'                => 'required|integer|exists:units,id',
            'sub_unit_ids'           => 'nullable|json',
            'brand_id'               => 'nullable|integer|exists:brands,id',
            'category_id'            => 'required|integer|exists:categories,id',
            'sub_category_id'        => 'nullable|integer|exists:categories,id',
            'type'                   => 'required|in:single,variable,modifier,combo',
            'images.*'               => $imageRule,
            'image'                  => $imageRule,
            'default_purchase_price' => 'required|array',
            'profit_percent'         => 'required|array',
            'default_sell_price'     => 'required|array',
            'stock_amount'           => 'required',
            'alert_quantity'         => 'required',
            'status'                 => 'nullable|in:active,inactive',
            'created_by'             => 'nullable|integer|exists:users,id',
        ];
    }
}