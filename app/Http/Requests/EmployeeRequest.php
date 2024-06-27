<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
        $employeeId = $this->route('employee');

        $isCreating = $this->getMethod() == 'POST';
        // $imageRule = $isCreating ? 'required|image|mimes:jpeg,png,jpg,webp|max:2048' : 'sometimes|nullable|image|mimes:jpeg,png,jpg,webp|max:2048';

        $rules = [
            'name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'password'  => 'required|min:6|max:15',
            // 'image'      =>  $imageRule,
            'email'      => 'required|email|unique:users,email,' . $employeeId,
            // 'designation_id' => 'required',
            'phone_number' => [
                'required',
                
            ],
            // 'family_number' => [
            //     'nullable',
            //     'regex:/^(\+?88)?01[3-9]\d{8}$/',
            //     'unique:employees,family_number,' . $employeeId,
            // ],
            // 'nid' => 'required|numeric|unique:employees,nid,' . $employeeId,
            // 'passport' => 'nullable|unique:employees,passport,' . $employeeId,
            // 'dob' => 'required|date',
            // 'gender' => 'required',
            // 'marital_status' => 'required',
            // 'blood_group' => 'required',
            // 'permanent_address' => 'required|max:255',
            // 'current_address' => 'required|max:255',
            // 'bank_details' => 'nullable|max:255',
            // 'status' => 'required',
        ];

        if ($this->isMethod('patch') || $this->isMethod('put')) {
            unset($rules['first_name'], $rules['last_name'], $rules['email']);
        }
        return $rules;
    }
}
