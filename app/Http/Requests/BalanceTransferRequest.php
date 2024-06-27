<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BalanceTransferRequest extends FormRequest
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
            'branch_id' => 'required|integer',
            'transfer_amount' => 'required|regex:/^\d{1,10}(\.\d{2,2})?$/',
            'document' => 'max:10000|mimes:doc,docx,pdf,xlsx',
            'note' => 'required',
        ];
    }
}
