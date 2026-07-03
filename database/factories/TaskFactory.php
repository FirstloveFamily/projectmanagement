<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['todo', 'in_progress', 'done']);
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $dueDate = fake()->dateTimeBetween($startDate, '+2 months');
        $taskCategories = [
            'งานทั่วไป',
            'ติดตามเอกสารสัญญา',
            'ติดตาม MIFC',
            'ประสานงานลูกค้า',
            'ติดตามเอกสารอนุมัติ',
        ];

        return [
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'task_category' => fake()->randomElement($taskCategories),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => $status,
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'start_date' => $startDate,
            'due_date' => $dueDate,
            'completed_at' => $status === 'done' ? fake()->dateTimeBetween('-2 weeks', 'now') : null,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
