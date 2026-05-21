<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'department_id',
        'position_id',
        'employee_code',
        'phone',
        'address',
        'date_of_birth',
        'hire_date',
        'basic_salary',
        'status',
        'profile_photo',
        'gender',
    ];

    protected $casts = ['hire_date' => 'date', 'date_of_birth' => 'date', 'basic_salary' => 'decimal:2'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->user->name;
    }
}
