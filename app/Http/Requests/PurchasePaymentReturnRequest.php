<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchasePaymentReturnRequest extends FormRequest
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
            'purchase_return_id' => 'required|exists:purchase_returns,id',
            'user_id' => 'exists:users,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'payment_method' => 'required|string|max:50',
            'note' => 'required|string',
        ];
    }
}
