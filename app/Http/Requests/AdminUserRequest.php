<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserRequest extends FormRequest
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
        $userId = $this->route('id');
        return [
            'name' => 'required|string|max:64',
            'phone_number' => 'required|bail|numeric|digits:11|regex:/^(?:\+?88)?01[3-9]\d{8}$/|unique:users,phone_number,' . $userId,
            'email' => 'required|string|max:32|unique:users,email,' . $userId,
            'password' => 'required|string|min:8|max:32',
            'c_password' => 'required|string|same:password|min:8|max:32',
        ];
    }
}
