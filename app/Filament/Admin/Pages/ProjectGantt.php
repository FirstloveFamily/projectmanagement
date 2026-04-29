<?php

namespace App\Filament\Admin\Pages;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskDependency;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Response;

class ProjectGantt extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.admin.pages.project-gantt';
    protected static bool $shouldRegisterNavigation = false;

    public Project $project;

    public static function getRoutePath(): string
    {
        return '/projects/{project}/gantt';
    }

    public function mount(Project $project): void
    {
        $this->project = $project;
    }

    public function getGanttData()
    {
        $tasks = Task::where('project_id', $this->project->id)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'text' => $task->title,
                    'start_date' => $task->start_date ? $task->start_date->format('d-m-Y') : now()->format('d-m-Y'),
                    'duration' => $task->start_date && $task->due_date ? $task->start_date->diffInDays($task->due_date) : 1,
                    'progress' => $task->progress / 100,
                    'open' => true,
                ];
            });

        $links = TaskDependency::whereHas('task', function($query) {
                $query->where('project_id', $this->project->id);
            })
            ->get()
            ->map(function ($link) {
                return [
                    'id' => $link->id,
                    'source' => $link->depends_on_task_id,
                    'target' => $link->task_id,
                    'type' => '0', // Finish-to-Start
                ];
            });

        return [
            'data' => $tasks,
            'links' => $links,
        ];
    }
    
    public function getTitle(): string 
    {
        return "Gantt Chart: " . $this->project->name;
    }
}
