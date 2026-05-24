<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'employee_code'  => $this->employee_code,
            'name'           => $this->user->name,
            'email'          => $this->user->email,
            'phone'          => $this->phone,
            'department'     => $this->department?->name,
            'position'       => $this->position?->title,
            'hire_date'      => $this->hire_date->format('Y-m-d'),
            'basic_salary'   => (float) $this->basic_salary,
            // 'basic_salary'  => $this->basic_salary,
            'status'         => $this->status,
            'profile_photo'  => $this->profile_photo
                ? asset('storage/' . $this->profile_photo)
                : null,
            'created_at'     => $this->created_at->toISOString(),
        ];
    }
}
