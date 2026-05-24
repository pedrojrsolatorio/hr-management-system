<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePerformanceReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr_manager');
    }

    public function rules(): array
    {
        return [
            'employee_id'  => 'required|exists:employees,id',
            'score'        => 'required|integer|min:1|max:100',
            'period'       => 'required|string|max:20',
            'strengths'    => 'nullable|string|max:1000',
            'improvements' => 'nullable|string|max:1000',
            'comments'     => 'nullable|string|max:1000',
            'reviewed_at'  => 'nullable|date',
        ];
    }
}
