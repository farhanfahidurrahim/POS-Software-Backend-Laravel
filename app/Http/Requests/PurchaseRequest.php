<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            "supplier_id" => "required",
            "product_id" => "required",
            "purchase_date" => "required|date",
            "variation_id" => "required|array",
            "quantity" => "required|array",
            "unit_price" => "required|array",
            // "sub_total" => "required|array",
            "sub_total_amount" => "nullable",
            "shipping_amount" => "nullable",
            "total_amount" => "required",
            "paid_amount" => "nullable",
            // "due_amount" => "required|numeric",
            "payment_status" => "nullable|string",
            "payment_method" => "nullable|string",
            "purchase_status" => "required|string",
            'document' => 'nullable|max:10000|mimes:doc,docx,pdf,xlsx,jpg,jpeg,png,webp',
            "note" => "nullable|max:100000",
        ];
    }
}
