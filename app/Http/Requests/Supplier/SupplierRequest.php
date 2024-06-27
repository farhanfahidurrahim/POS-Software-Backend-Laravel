<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'phone_number.regex' => 'Invalid Bangladeshi Phone Number.',
            'company_number.regex' => 'Invalid Bangladeshi Phone Number.',
        ];
    }
    public function rules()
    {
        $supplierId = $this->route('id');
        $rules = [
            'name' => 'required|string',
            'phone_number' => [
                'required',
                'regex:/^(\+?88)?01[3-9]\d{8}$/',
                'unique:suppliers,phone_number,' . $supplierId,
            ],
            'email' => 'nullable|email|unique:suppliers,email' . $supplierId,
            'company_name' => 'nullable|string',
            'company_number' => [
                'nullable',
                'regex:/^(\+?88)?01[3-9]\d{8}$/',
                'unique:suppliers,company_number,' . $supplierId,
            ],
            'location' => 'nullable|string',
        ];

        if ($this->isMethod('patch') || $this->isMethod('put')) {
            unset($rules['name'], $rules['email'], $rules['phone_number']);
        }

        return $rules;
    }
}
