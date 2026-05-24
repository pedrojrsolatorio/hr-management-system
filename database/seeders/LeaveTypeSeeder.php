<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Annual Leave', 'days_allowed' => 20, 'is_paid' => true],
            ['name' => 'Sick Leave', 'days_allowed' => 14, 'is_paid' => true],
            ['name' => 'Upaid Leave', 'days_allowed' => 30, 'is_paid' => false],
            ['name' => 'Maternity Leave', 'days_allowed' => 90, 'is_paid' => true]
        ];
        foreach ($types as $type) LeaveType::create($type);
    }
}
