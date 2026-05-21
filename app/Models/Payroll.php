<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'month', 'basic_salary', 'gross_salary', 'total_deductions', 'net_salary', 'status', 'paid_at'];

    protected $casts = ['basic_salary' => 'decimal:2', 'gross_salary' => 'decimal:2', 'total_deductions' => 'decimal:2', 'net_salary' => 'decimal:2', 'paid_at' => 'datetime'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }
    public function allowances(): HasMany
    {
        return $this->hasMany(PayrollItem::class)->where('type', 'allowance');
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(PayrollItem::class)->where('type', 'deduction');
    }
}
