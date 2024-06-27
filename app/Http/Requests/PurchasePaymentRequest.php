<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchasePaymentRequest extends FormRequest
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
            'purchase_id' => 'required',
            'user_id' => 'exists:users,id',
            'paid_amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'payment_method' => 'required',
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ];
    }
}
