<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            'Software Engineer',
            'HR Officer',
            'Accountant',
            'Marketing Specialist',
            'Operations Manager',
        ];

        foreach ($positions as $name) {
            Position::create(['title' => $name]);
        }
    }
}
