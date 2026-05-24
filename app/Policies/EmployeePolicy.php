<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
{
    /**
     * Admins and HR managers can bypass all policy checks.
     * This runs before every other method in this policy.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin') || $user->hasRole('hr_manager')) {
            return true; // grant everything, skip the methods below
        }

        return null; // fall through to the individual methods
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // return $user->hasRole('admin') || $user->hasRole('hr_manager');
        return true; // handled by before()
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Employee $employee): bool
    {
        // Employees can only view their own profile
        return $user->employee?->id === $employee->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // return $user->hasRole('admin') || $user->hasRole('hr_manager');
        return true; // handled by before()
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Employee $employee): bool
    {
        // return $user->hasRole('admin') || $user->hasRole('hr_manager');
        return true; // handled by before()
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        // return $user->hasRole('admin');
        return true; // handled by before()
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Employee $employee): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Employee $employee): bool
    {
        return false;
    }
}
