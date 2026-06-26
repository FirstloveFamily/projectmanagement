<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today();

        $companies = Company::query()
            ->withCount([
                'projects',
                'projects as active_projects_count' => fn ($query) => $query->where('status', 'active'),
                'projects as completed_projects_count' => fn ($query) => $query->where('status', 'completed'),
                'projects as overdue_projects_count' => fn ($query) => $query
                    ->where('status', '!=', 'completed')
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $today),
            ])
            ->orderBy('name')
            ->get();

        $editingCompany = null;
        if ($request->filled('edit')) {
            $editingCompany = Company::query()->find($request->integer('edit'));
        }

        $summary = [
            'companies' => Company::query()->count(),
            'projects' => Project::query()->count(),
            'active' => Project::query()->where('status', 'active')->count(),
            'overdue' => Project::query()
                ->where('status', '!=', 'completed')
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today)
                ->count(),
        ];

        return view('companies.index', [
            'companies' => $companies,
            'editingCompany' => $editingCompany,
            'summary' => $summary,
        ]);
    }

    public function show(Company $company): View
    {
        $today = Carbon::today();

        $company->load([
            'projects' => fn ($query) => $query
                ->with('user')
                ->withCount([
                    'tasks',
                    'tasks as completed_tasks_count' => fn ($taskQuery) => $taskQuery->where('status', 'done'),
                    'tasks as active_tasks_count' => fn ($taskQuery) => $taskQuery->whereIn('status', ['todo', 'in_progress']),
                    'tasks as overdue_tasks_count' => fn ($taskQuery) => $taskQuery
                        ->where('status', '!=', 'done')
                        ->whereNotNull('due_date')
                        ->whereDate('due_date', '<', $today),
                ])
                ->latest('due_date')
                ->latest('id'),
        ]);

        $projects = $company->projects;
        $totalProjects = $projects->count();
        $activeProjects = $projects->where('status', 'active')->count();
        $completedProjects = $projects->where('status', 'completed')->count();
        $overdueProjects = $projects->filter(fn (Project $project) => $project->status !== 'completed' && filled($project->due_date) && $project->due_date->isPast())->count();
        $taskCount = $projects->sum('tasks_count');
        $completedTaskCount = $projects->sum('completed_tasks_count');
        $activeTaskCount = $projects->sum('active_tasks_count');
        $overdueTaskCount = $projects->sum('overdue_tasks_count');

        $statusBreakdown = [
            ['label' => 'กำลังดำเนินการ', 'count' => $activeProjects, 'class' => 'from-sky-600 to-cyan-500'],
            ['label' => 'พักงาน', 'count' => $projects->where('status', 'on_hold')->count(), 'class' => 'from-amber-500 to-orange-400'],
            ['label' => 'เสร็จสมบูรณ์', 'count' => $completedProjects, 'class' => 'from-emerald-600 to-teal-500'],
        ];

        return view('companies.show', [
            'company' => $company,
            'projects' => $projects,
            'summary' => [
                'totalProjects' => $totalProjects,
                'activeProjects' => $activeProjects,
                'completedProjects' => $completedProjects,
                'overdueProjects' => $overdueProjects,
                'taskCount' => $taskCount,
                'completedTaskCount' => $completedTaskCount,
                'activeTaskCount' => $activeTaskCount,
                'overdueTaskCount' => $overdueTaskCount,
            ],
            'statusBreakdown' => $statusBreakdown,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Gate::allows('manage-companies'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
            'description' => ['nullable', 'string'],
        ]);

        Company::create($validated);

        return redirect()
            ->route('companies.index')
            ->with('success', 'เพิ่มบริษัทเรียบร้อยแล้ว');
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        abort_unless(Gate::allows('manage-companies'), 403);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'name')->ignore($company->id),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $company->update($validated);

        return redirect()
            ->route('companies.index', ['edit' => $company->id])
            ->with('success', 'แก้ไขข้อมูลบริษัทเรียบร้อยแล้ว');
    }

    public function destroy(Company $company): RedirectResponse
    {
        abort_unless(Gate::allows('manage-companies'), 403);

        $company->delete();

        return redirect()
            ->route('companies.index')
            ->with('success', 'ลบบริษัทเรียบร้อยแล้ว');
    }
}
