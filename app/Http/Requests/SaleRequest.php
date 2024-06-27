<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
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
        $rules = [
            'phone_number' => 'required',
            'name' => 'required',
            'email' => 'nullable',
            'location' => 'required',
            'sale_from' => 'required',
            'branch_id' => 'nullable|integer',
            'variation_id' => 'required|array',
            'unit_price' => 'required|array',
            'quantity' => 'required|array|min:1',
            'discount_percentage' => 'nullable|array',
            'discount_amount' => 'nullable|array',
            'total_amount' => 'nullable|regex:/^\d{1,12}(\.\d{2,2})?$/',
            'paid_amount' => 'nullable|regex:/^\d{1,12}(\.\d{2,2})?$/',
            'due_amount' => 'nullable|regex:/^\d{1,12}(\.\d{2,2})?$/',
            'payment_status' => 'nullable|string',
            'delivery_method' => 'required|string',
            'note' => 'nullable|string',
        ];

        if ($this->input('sale_from') != "offline") {
            $rules['shipping_charge'] = 'required';
            $rules['courier_id'] = 'required';
        }

        if ($this->input('courier_id') == 1) {
            $rules['city_id'] = 'required';
            $rules['zone_id'] = 'required';
        }

        return $rules;
    }
}
