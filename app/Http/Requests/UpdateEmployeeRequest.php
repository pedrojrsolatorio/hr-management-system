<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('employee'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          => 'sometimes|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'position_id'   => 'nullable|exists:positions,id',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'hire_date'     => 'sometimes|date',
            'basic_salary'  => 'sometimes|numeric|min:0|max:9999999.99',
            'status'        => 'sometimes|in:active,inactive,terminated',
            'gender'        => 'nullable|in:male,female,other',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}
