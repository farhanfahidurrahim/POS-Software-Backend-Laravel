<?php

namespace App\Http\Requests\user;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;



class UserRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        $userId = $this->route('id'); // Get the user ID from the route

        $rules = [
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'user_name' => 'nullable|string|max:255|unique:users,user_name,' . $userId,
            'phone_number' => 'required|string|max:255|unique:users,phone_number,' . $userId,
            'alt_number' => 'nullable|string|max:255',
            'family_number' => 'nullable|string|max:255',
            'nid' => 'nullable|string|max:255|unique:users,nid,' . $userId,
            'passport' => 'nullable|string|max:255|unique:users,passport,' . $userId,
            'dob' => 'nullable|date',
            'gender' => 'nullable|string|max:255',
            'guardian_name' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|max:255',
            'blood_group' => 'nullable|string|max:255',
            'permanent_address' => 'nullable|string|max:255',
            'current_address' => 'nullable|string|max:255',
            'bank_details' => 'nullable|string|max:255',
            'status' => 'nullable|integer',
            'password' => ['sometimes', 'required', 'confirmed', Rules\Password::defaults()],
        ];


        if ($this->isMethod('patch') || $this->isMethod('put')) {
            unset($rules['name'], $rules['email'], $rules['phone_number']);
        }

        return $rules;
    }
}
