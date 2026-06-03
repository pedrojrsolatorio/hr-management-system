<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     $admin = User::create([
    //         'name' => 'System Admin',
    //         'email' => 'admin@hrms.com',
    //         'password' => Hash::make('password'),
    //     ]);
    //     $admin->roles()->attach(Role::where('slug', 'admin')->first());
    // }

    // recommended version to avoid duplicates when seeding multiple times
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@hrms.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
            ]
        );

        $adminRole = Role::where('slug', 'admin')->first();

        $admin->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}
