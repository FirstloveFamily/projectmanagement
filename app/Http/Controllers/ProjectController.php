<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today();
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $companyId = $request->query('company_id');

        $projectsQuery = Project::query()
            ->with(['company', 'user'])
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn ($query) => $query->where('status', 'done'),
                'tasks as active_tasks_count' => fn ($query) => $query->whereIn('status', ['todo', 'in_progress']),
                'tasks as overdue_tasks_count' => fn ($query) => $query
                    ->where('status', '!=', 'done')
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $today),
            ]);

        if ($search !== '') {
            $projectsQuery->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('objective', 'like', "%{$search}%")
                    ->orWhere('risk', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('company', fn ($companyQuery) => $companyQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if (filled($status)) {
            $projectsQuery->where('status', $status);
        }

        if (filled($companyId)) {
            $projectsQuery->where('company_id', $companyId);
        }

        $projects = (clone $projectsQuery)
            ->latest('due_date')
            ->latest('id')
            ->get();

        $summary = [
            'total' => Project::query()->count(),
            'active' => Project::query()->where('status', 'active')->count(),
            'completed' => Project::query()->where('status', 'completed')->count(),
            'overdue' => Project::query()
                ->where('status', '!=', 'completed')
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today)
                ->count(),
        ];

        $statusCounts = [
            'active' => Project::query()->where('status', 'active')->count(),
            'on_hold' => Project::query()->where('status', 'on_hold')->count(),
            'completed' => Project::query()->where('status', 'completed')->count(),
        ];

        return view('projects.index', [
            'projects' => $projects,
            'summary' => $summary,
            'statusCounts' => $statusCounts,
            'companies' => Company::query()->orderBy('name')->get(),
            'filters' => [
                'q' => $search,
                'status' => $status,
                'company_id' => $companyId,
            ],
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless(Gate::allows('manage-projects'), 403);

        $editingProject = null;
        if ($request->filled('edit')) {
            $editingProject = Project::query()->find($request->integer('edit'));
        }

        return view('projects.create', [
            'companies' => Company::query()->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
            'editingProject' => $editingProject,
        ]);
    }

    public function show(Project $project): View|RedirectResponse
    {
        $today = Carbon::today();
        $project->load([
            'company',
            'user',
        ]);

        if ($requestId = request()->integer('edit_task')) {
            $task = $project->tasks->firstWhere('id', $requestId);

            if ($task) {
                return redirect()->route('projects.tasks.edit', [$project, $task]);
            }
        }

        $project->loadCount([
            'tasks',
            'tasks as completed_tasks_count' => fn ($query) => $query->where('status', 'done'),
            'tasks as active_tasks_count' => fn ($query) => $query->whereIn('status', ['todo', 'in_progress']),
            'tasks as overdue_tasks_count' => fn ($query) => $query
                ->where('status', '!=', 'done')
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today),
        ]);

        $users = User::query()->orderBy('name')->get();

        return view('projects.show', [
            'project' => $project,
            'editingTask' => null,
            'users' => $users,
            'taskCategories' => Task::taskCategoryLabels(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Gate::allows('manage-projects'), 403);

        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'objective' => ['nullable', 'string'],
            'risk' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,on_hold,completed'],
            'start_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $project = Project::create($validated);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'เพิ่มโครงการเรียบร้อยแล้ว');
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        abort_unless(Gate::allows('manage-projects'), 403);

        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'objective' => ['nullable', 'string'],
            'risk' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,on_hold,completed'],
            'start_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $project->update($validated);

        return redirect()
            ->route('projects.create', ['edit' => $project->id])
            ->with('success', 'แก้ไขโครงการเรียบร้อยแล้ว');
    }

    public function destroy(Project $project): RedirectResponse
    {
        abort_unless(Gate::allows('manage-projects'), 403);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'ลบโครงการเรียบร้อยแล้ว');
    }

    public function storeTask(Request $request, Project $project): RedirectResponse
    {
        abort_unless(Gate::allows('manage-tasks'), 403);

        $validated = $this->validateTask($request);

        $validated['project_id'] = $project->id;
        $validated['completed_at'] = $validated['status'] === 'done'
            ? ($validated['completed_at'] ?? now())
            : null;

        Task::create($validated);

        return redirect()
            ->route('projects.tasks.index', $project)
            ->with('success', 'เพิ่มงานเรียบร้อยแล้ว');
    }

    public function updateTask(Request $request, Project $project, Task $task): RedirectResponse
    {
        abort_unless(Gate::allows('manage-tasks'), 403);

        abort_if($task->project_id !== $project->id, 404);

        $validated = $this->validateTask($request);
        $validated['completed_at'] = $validated['status'] === 'done'
            ? ($validated['completed_at'] ?? now())
            : null;

        $task->update($validated);

        return redirect()
            ->route('projects.tasks.index', $project)
            ->with('success', 'แก้ไขงานเรียบร้อยแล้ว');
    }

    public function destroyTask(Project $project, Task $task): RedirectResponse
    {
        abort_unless(Gate::allows('manage-tasks'), 403);

        abort_if($task->project_id !== $project->id, 404);

        $task->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'ลบงานเรียบร้อยแล้ว');
    }

    /**
     * Validate task payload for frontend CRUD.
     *
     * @return array<string, mixed>
     */
    private function validateTask(Request $request): array
    {
        return $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'task_category' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['todo', 'in_progress', 'done'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'start_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:start_date'],
            'completed_at' => ['nullable', 'date'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);
    }
}
