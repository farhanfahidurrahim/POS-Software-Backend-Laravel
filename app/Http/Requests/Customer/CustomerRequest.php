<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'phone_number.regex' => 'Invalid Bangladeshi Phone Number.',
        ];
    }
    public function rules()
    {
        $customerId = $this->route('id');
        $rules = [
            'name' => 'required|string',
            'email' => 'nullable|email|unique:customers,email' . $customerId,
            'phone_number' => [
                'required',
                'regex:/^(\+?88)?01[3-9]\d{8}$/',
                'unique:customers,phone_number,' . $customerId,
            ],
            // 'city_id' => 'required',
            // 'city_name' => 'nullable',
            // 'zone_id' => 'required',
            // 'zone_name' => 'nullable',
            // 'area_id' => 'nullable',
            // 'area_name' => 'nullable',
            // 'location' => 'required|string',
        ];

        if ($this->isMethod('patch') || $this->isMethod('put')) {
            unset($rules['name'], $rules['email'], $rules['phone_number']);
        }

        return $rules;
    }
}