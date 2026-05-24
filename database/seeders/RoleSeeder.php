<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Admin' => 'admin', 'HR Manager' => 'hr_manager', 'Employee' => 'employee'] as $name => $slug) {
            Role::create(['name' => $name, 'slug' => $slug]);
        }
    }
}
