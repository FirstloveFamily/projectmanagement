<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['active', 'on_hold', 'completed']);
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $dueDate = fake()->dateTimeBetween($startDate, '+3 months');

        return [
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'status' => $status,
            'start_date' => $startDate,
            'due_date' => $dueDate,
        ];
    }
}
