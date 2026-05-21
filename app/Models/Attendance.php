<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'date', 'check_in', 'check_out', 'status', 'notes'];

    protected $casts = ['date' => 'date'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getWorkedHoursAttribute(): ?float
    {
        if (!$this->check_in || !$this->check_out) return null;
        return Carbon::parse($this->check_out)->diffInMinutes(Carbon::parse($this->check_in)) / 60;
    }
}
