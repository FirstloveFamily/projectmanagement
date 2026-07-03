<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->get();
        $companies = Company::query()->get();

        if ($users->isEmpty()) {
            $users = User::factory()->count(4)->create();
        } else {
            $users = $users->merge(User::factory()->count(4)->create());
        }

        if ($companies->isEmpty()) {
            $companies = Company::factory()
                ->count(4)
                ->create();
        }

        foreach ($users as $user) {
            Project::factory()
                ->count(fake()->numberBetween(1, 3))
                ->for($user)
                ->state(fn (): array => [
                    'company_id' => $companies->random()->id,
                ])
                ->create()
                ->each(function (Project $project) use ($users): void {
                    Task::factory()
                        ->count(fake()->numberBetween(3, 6))
                        ->for($project)
                        ->state([
                            'user_id' => $project->user_id,
                        ])
                        ->create()
                        ->each(function (Task $task) use ($users): void {
                            TaskChecklist::factory()
                                ->count(fake()->numberBetween(2, 4))
                                ->for($task)
                                ->create();

                            TaskComment::factory()
                                ->count(fake()->numberBetween(1, 3))
                                ->for($task)
                                ->state(fn (): array => [
                                    'user_id' => $users->random()->id,
                                ])
                                ->create();
                        });
                });
        }
    }
}
