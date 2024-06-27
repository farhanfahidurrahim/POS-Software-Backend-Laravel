<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
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
            "expense_category_id" => "required|integer",
            "date" => "required|date",
            "expense_reason" => "required|string",
            // "total_amount" => "required|float",
            'total_amount' => 'required|regex:/^\d{1,12}(\.\d{2,2})?$/',
            'document' => 'nullable|max:10000|mimes:doc,docx,pdf,xlsx,jpg,jpeg,png,webp',
        ];
    }
}
