<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayrollRequest extends FormRequest
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
            'employee_id'     => 'required|integer',
            'month'           => 'required',
            'total_amount'    => 'required|numeric',
            'partial_payment' => 'required|numeric',
            'payment_status'  => 'required',
        ];
    }
}
