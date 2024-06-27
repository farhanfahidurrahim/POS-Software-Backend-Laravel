<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchageReturnRequest extends FormRequest
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
            "supplier_id"=> "required|numeric",
            "purchase_id"=> "required|numeric",
            "reference"=> "required|string",
            "total_amount"=> "required|numeric",
            "paid_amount"=> "required|numeric",
            "due_amount"=> "required|numeric",
            "status"=> "required|string",
            "payment_status"=> "required|string",
            "payment_method"=> "required|string",
            "variation_id"=> "required|array",
            "return_quantity"=> "required|array",
            "note"=> "nullable|max:10000",
        ];
    }
}
