<?php

namespace App\Services;

use App\Models\{Employee, User, Role, AuditLog};
use Illuminate\Support\Facades\{DB, Hash, Storage};
use Illuminate\Support\Str;

class EmployeeService
{
    // public function create(array $data): Employee
    // {
    //     $employee = null;

    //     DB::transaction(function () use ($data, &$employee) {
    //         $user = User::create([
    //             'name'     => $data['name'],
    //             'email'    => $data['email'],
    //             'password' => Hash::make($data['password'] ?? Str::random(10)),
    //         ]);

    //         $empRole = Role::where('slug', 'employee')->first();
    //         if ($empRole) {
    //             $user->roles()->attach($empRole->id);
    //         }

    //         $photoPath = null;
    //         if (!empty($data['profile_photo']) && is_object($data['profile_photo'])) {
    //             $photoPath = $data['profile_photo']->store('employees', 'public');
    //         }

    //         $employee = Employee::create([
    //             'user_id'       => $user->id,
    //             'department_id' => $data['department_id'] ?? null,
    //             'position_id'   => $data['position_id'] ?? null,
    //             'phone'         => $data['phone'] ?? null,
    //             'address'       => $data['address'] ?? null,
    //             'date_of_birth' => $data['date_of_birth'] ?? null,
    //             'hire_date'     => $data['hire_date'],
    //             'basic_salary'  => $data['basic_salary'],
    //             'status'        => $data['status'] ?? 'active',
    //             'gender'        => $data['gender'] ?? null,
    //             'profile_photo' => $photoPath,
    //             'employee_code' => $this->generateCode(),
    //         ]);

    //         AuditLog::create([
    //             'user_id'    => auth()->id(),
    //             'action'     => 'employee.created',
    //             'model_type' => Employee::class,
    //             'model_id'   => $employee->id,
    //             'new_values' => $employee->toArray(),
    //             'ip_address' => request()->ip(),
    //         ]);
    //     });

    //     // This tells the IDE $employee is guaranteed Employee after the transaction
    //     assert($employee instanceof Employee);

    //     // $employee is set inside the transaction via reference
    //     return $employee;
    // }

    // // Better version without reference variable and assert
    public function create(array $data): Employee
    {
        return DB::transaction(function () use ($data) {

            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                // 'password' => Hash::make($data['password'] ?? Str::random(10)),
                'password' => Hash::make('password'), // use this instead of random password since there's no password input in view
            ]);

            $empRole = Role::where('slug', 'employee')->first();
            if ($empRole) {
                $user->roles()->attach($empRole->id);
            }

            $employee = Employee::create([
                'user_id'       => $user->id,
                'department_id' => $data['department_id'] ?? null,
                'position_id'   => $data['position_id'] ?? null,
                'phone'         => $data['phone'] ?? null,
                'address'       => $data['address'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'hire_date'     => $data['hire_date'],
                'basic_salary'  => $data['basic_salary'],
                'status'        => $data['status'] ?? 'active',
                'gender'        => $data['gender'] ?? null,
                'profile_photo' => $data['profile_photo'] ?? null,
                // 'employee_code' => $this->generateCode(),
                'employee_code' => Str::uuid(),
            ]);

            // replaced the generateCode()
            $employee->update([
                'employee_code' => sprintf('EMP-%04d', $employee->id),
            ]);

            AuditLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'employee.created',
                'model_type' => Employee::class,
                'model_id'   => $employee->id,
                'new_values' => $employee->toArray(),
                'ip_address' => request()->ip(),
            ]);

            return $employee;
        });
    }

    public function update(Employee $employee, array $data): Employee
    {
        $old = $employee->toArray();

        if (!empty($data['profile_photo']) && is_object($data['profile_photo'])) {
            if ($employee->profile_photo) {
                Storage::disk('public')->delete($employee->profile_photo);
            }
            $data['profile_photo'] = $data['profile_photo']->store('employees', 'public');
        } else {
            unset($data['profile_photo']);
        }

        $employee->update([
            'department_id' => $data['department_id'] ?? $employee->department_id,
            'position_id'   => $data['position_id'] ?? $employee->position_id,
            'phone'         => $data['phone'] ?? $employee->phone,
            'address'       => $data['address'] ?? $employee->address,
            'date_of_birth' => $data['date_of_birth'] ?? $employee->date_of_birth,
            'hire_date'     => $data['hire_date'] ?? $employee->hire_date,
            'basic_salary'  => $data['basic_salary'] ?? $employee->basic_salary,
            'status'        => $data['status'] ?? $employee->status,
            'gender'        => $data['gender'] ?? $employee->gender,
            'profile_photo' => $data['profile_photo'] ?? $employee->profile_photo,
        ]);

        if (!empty($data['name'])) {
            $employee->user->update(['name' => $data['name']]);
        }

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'employee.updated',
            'model_type' => Employee::class,
            'model_id'   => $employee->id,
            'old_values' => $old,
            'new_values' => $employee->fresh()->toArray(),
            'ip_address' => request()->ip(),
        ]);

        return $employee->fresh();
    }

    /**
     * Soft delete — standard termination flow.
     * Employee and user become inactive but all records are preserved and queryable.
     */
    public function delete(Employee $employee): void
    {
        // Mark as terminated before soft deleting
        $employee->update(['status' => 'terminated']);

        // if ($employee->profile_photo) {
        //     Storage::disk('public')->delete($employee->profile_photo);
        // }

        $employee->delete();        // soft employees.deleted_at
        $employee->user->delete();  // soft users.deleted_at
    }

    /**
     * Permanent deletion — GDPR erasure or test data cleanup only.
     *
     * Historical records (payroll, attendance, leave, reviews) are intentionally
     * kept with employee_id set to NULL so financial and audit data is preserved.
     * The employee name is anonymised before deletion.
     */
    public function forceDelete(Employee $employee): void
    {
        // Anonymise related records before wiping the identity
        // so reports still show correct totals without personal data
        $employee->payrolls()->withTrashed()->update([
            'employee_id' => null,
        ]);

        $employee->attendance()->update([
            'employee_id' => null,
        ]);

        $employee->leaveRequests()->update([
            'employee_id' => null,
        ]);

        $employee->reviews()->update([
            'employee_id' => null,
        ]);

        // Remove profile photo
        if ($employee->profile_photo) {
            Storage::disk('public')->delete($employee->profile_photo);
        }

        // Force delete employee first (nullifies user_id on employee row via DB)
        $user = $employee->user;
        $employee->forceDelete();

        // Force delete user (login credentials permanently erased)
        $user?->forceDelete();
    }

    /**
     * Restore a soft-deleted employee and their user account.
     */
    public function restore(int $employeeId): Employee
    {
        $employee = Employee::withTrashed()->findOrFail($employeeId);
        $employee->restore();
        $employee->update(['status' => 'active']);
        $employee->user()->withTrashed()->first()?->restore();

        return $employee;
    }

    // // Could create duplicate employee_code if two employee are created at the same time
    // private function generateCode(): string
    // {
    //     $last = Employee::withTrashed()->latest('id')->value('id') ?? 0;
    //     return 'EMP-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    // }
}
