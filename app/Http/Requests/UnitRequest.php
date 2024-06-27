<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitRequest extends FormRequest
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
            'name'                  => 'required|string|max:255',
            'short_name'            => 'nullable|string|max:255',
            'base_unit_id'          => 'nullable|exists:units,id',
            // 'allow_decimal'         => 'nullable|integer',
            'base_unit_multiplier'  => 'nullable|integer|',
            'created_by'            => 'nullable|integer',
        ];
    }
}
