<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Employee::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'department_id' => 'nullable|exists:departments,id',
            'position_id'   => 'nullable|exists:positions,id',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'hire_date'     => 'required|date',
            'basic_salary'  => 'required|numeric|min:0|max:9999999.99',
            'status'        => 'required|in:active,inactive,terminated',
            'gender'        => 'nullable|in:male,female,other',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}

// File upload security — only allow images, store outside public, generate unique names
// This is enforced in StoreEmployeeRequest + Service:
//          $data['profile_photo']->store('employees', 'public');
// Storage::disk('public') — files go to storage/app/public, served via symlink
// Never store user files in public/ directly