<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuotationRequest extends FormRequest
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
        return [
            'customer_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'variation_id' => 'required|array',
            'unit_price' => 'required|array',
            'quantity' => 'required|array|min:1',
            'discount_percentage' => 'nullable|array',
            'discount_amount' => 'nullable|array',
            'discount_type_subtotal' => 'nullable|string',
            'discount_on_subtotal' => 'nullable|string',
            'discount_on_subtotal_amount' => 'nullable|string',
            'shipping_amount' => 'nullable|regex:/^\d{1,12}(\.\d{2,2})?$/',
            'status' => 'nullable|string',
            'document' => 'nullable|max:10000|mimes:doc,docx,pdf,xlsx',
            'note' => 'nullable|string',
        ];
    }
}
