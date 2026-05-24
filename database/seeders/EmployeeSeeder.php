<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = Department::pluck('id')->toArray();
        $positions = Position::pluck('id')->toArray();
        $empRole = Role::where('slug', 'employee')->first();

        // // Manual Loop
        // for ($i = 1; $i <= 10; $i++) {
        //     $user = User::create([
        //         'name' => "Employee $i",
        //         'email' => "emp$i@hrms.com",
        //         'password' => Hash::make('password')
        //     ]);
        //     $user->roles()->attach($empRole);

        //     Employee::create([
        //         'user_id' => $user->id,
        //         'department_id' => $departments[array_rand($departments)],
        //         'position_id' => $positions[array_rand($positions)],
        //         'employee_code' => 'EMP-' . str_pad($i, 4, '0', STR_PAD_LEFT),
        //         'hire_date' => now()->subMonths(rand(1, 36)),
        //         'basic_salary' => rand(3000, 10000),
        //         'status' => 'active'
        //     ]);
        // }

        // using Factory
        User::factory(10)->create()->each(function ($user) use ($empRole, $departments, $positions) {

            $user->roles()->attach($empRole);

            Employee::factory()->create([
                'user_id' => $user->id,

                // pass random values from seeder not from factory (recommended)
                'department_id' => $departments[array_rand($departments)],
                'position_id' => $positions[array_rand($positions)],
            ]);
        });
    }
}
