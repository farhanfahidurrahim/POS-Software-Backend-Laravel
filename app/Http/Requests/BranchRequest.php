<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class BranchRequest extends FormRequest
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
          
                'name'                      => 'required|string|max:255',
                'country_id'                => 'nullable|integer',
                'division_id'               => 'required|integer',
                'district_id'               => 'required|integer',
                'upazila_id'                => 'nullable|integer',
                'city'                      => 'nullable|string|max:255',
                'state'                     => 'nullable|string|max:255',
                'post_code'                 => 'nullable|string|max:10',
                'address'                   => 'required|string',
                'phone_number'              => 'required|string|max:20',
                'alternate_number'          => 'nullable|string|max:20',
                'email'                     => 'nullable|email|max:255',
                'website'                   => 'nullable|string|max:255',
                'default_payment_accounts'  => 'nullable|string',
                'status'                    => 'nullable',
           
        ];
    }
}
