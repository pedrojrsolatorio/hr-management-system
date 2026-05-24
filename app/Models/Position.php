<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'level'];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class)->withTrashed();
    }
}
