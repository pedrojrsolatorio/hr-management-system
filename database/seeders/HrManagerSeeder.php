<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class HrManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hrRole = Role::where('slug', 'hr_manager')->first();

        if (!$hrRole) {
            $this->command->warn('HR Manager role not found. Run RoleSeeder first.');
            return;
        }

        // Create the HR Manager user
        $hrManager = User::firstOrCreate(
            ['email' => 'hr@hrms.com'],
            [
                'name' => 'HR Manager',
                'password' => Hash::make('password'),
            ]
        );

        // Assign the role only if not already assigned
        if (!$hrManager->roles()->where('slug', 'hr_manager')->exists()) {
            $hrManager->roles()->attach($hrRole->id);
        }

        $this->command->info('HR Manager created: hr@hrms.com / password');
    }
}
