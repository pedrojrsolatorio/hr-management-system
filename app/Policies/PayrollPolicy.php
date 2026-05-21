<?php

namespace App\Policies;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PayrollPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('hr_manager');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Payroll $payroll): bool
    {
        // Employees can only see their own payslips
        if ($user->hasRole('employee')) {
            return $user->employee?->id === $payroll->employee_id;
        }
        return true;
    }

    public function generate(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Payroll $payroll): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Payroll $payroll): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Payroll $payroll): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Payroll $payroll): bool
    {
        return false;
    }
}
