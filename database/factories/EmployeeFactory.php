<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_code' => 'EMP-' . fake()->unique()->numberBetween(1000, 9999),
            'hire_date' => fake()->dateTimeBetween('-3 years', 'now'),
            'basic_salary' => fake()->numberBetween(3000, 10000),
            'status' => 'active'
        ];
    }
}
