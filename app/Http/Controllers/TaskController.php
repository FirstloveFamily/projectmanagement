<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Project $project): View
    {
        $project->load([
            'company',
            'user',
            'tasks' => fn ($query) => $query
                ->with('user')
                ->withCount(['checklists', 'comments'])
                ->orderBy('sort_order')
                ->orderBy('due_date')
                ->orderBy('id'),
        ]);

        $statusFilter = request()->string('status')->toString();
        $allowedStatuses = ['all', 'todo', 'in_progress', 'done'];
        if (! in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = 'all';
        }

        $tasks = $project->tasks;
        $tasksByStatus = $tasks->groupBy('status');
        $taskCount = $tasks->count();
        $doneTaskCount = $tasksByStatus->get('done', collect())->count();
        $doingTaskCount = $tasksByStatus->get('in_progress', collect())->count();
        $todoTaskCount = $tasksByStatus->get('todo', collect())->count();
        $overdueTaskCount = $tasks->filter(fn (Task $task) => $task->status !== 'done' && $task->due_date?->isPast())->count();
        $dueSoonTaskCount = $tasks->filter(fn (Task $task) => $task->status !== 'done' && $task->due_date && $task->due_date->betweenIncluded(now()->startOfDay(), now()->addDays(7)->endOfDay()))->count();
        $progress = $taskCount > 0 ? (int) round(($doneTaskCount / $taskCount) * 100) : 0;

        $groupedTasks = match ($statusFilter) {
            'todo' => collect(['todo' => $tasksByStatus->get('todo', collect())]),
            'in_progress' => collect(['in_progress' => $tasksByStatus->get('in_progress', collect())]),
            'done' => collect(['done' => $tasksByStatus->get('done', collect())]),
            default => collect([
                'todo' => $tasksByStatus->get('todo', collect()),
                'in_progress' => $tasksByStatus->get('in_progress', collect()),
                'done' => $tasksByStatus->get('done', collect()),
            ]),
        };

        return view('tasks.index', [
            'project' => $project,
            'statusFilter' => $statusFilter,
            'taskCount' => $taskCount,
            'doneTaskCount' => $doneTaskCount,
            'doingTaskCount' => $doingTaskCount,
            'todoTaskCount' => $todoTaskCount,
            'overdueTaskCount' => $overdueTaskCount,
            'dueSoonTaskCount' => $dueSoonTaskCount,
            'progress' => $progress,
            'groupedTasks' => $groupedTasks,
            'users' => User::query()->orderBy('name')->get(),
            'taskCategories' => Task::taskCategoryLabels(),
        ]);
    }

    public function show(Project $project, Task $task): View
    {
        abort_if($task->project_id !== $project->id, 404);

        $project->load([
            'company',
            'user',
        ]);

        $task->load([
            'project.company',
            'user',
            'checklists' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
            'comments' => fn ($query) => $query->with('user')->latest('id'),
        ]);

        $task->loadCount([
            'checklists',
            'checklists as done_checklists_count' => fn ($query) => $query->where('is_done', true),
            'comments',
        ]);

        return view('tasks.show', [
            'project' => $project,
            'task' => $task,
        ]);
    }

    public function edit(Project $project, Task $task): View
    {
        abort_unless(Gate::allows('manage-tasks'), 403);

        abort_if($task->project_id !== $project->id, 404);

        $project->load([
            'company',
            'user',
        ]);

        $task->load('user');

        return view('tasks.edit', [
            'project' => $project,
            'task' => $task,
            'users' => User::query()->orderBy('name')->get(),
            'taskCategories' => Task::taskCategoryLabels(),
        ]);
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        return app(ProjectController::class)->storeTask($request, $project);
    }

    public function update(Request $request, Project $project, Task $task): RedirectResponse
    {
        return app(ProjectController::class)->updateTask($request, $project, $task);
    }

    public function destroy(Project $project, Task $task): RedirectResponse
    {
        return app(ProjectController::class)->destroyTask($project, $task);
    }
}
