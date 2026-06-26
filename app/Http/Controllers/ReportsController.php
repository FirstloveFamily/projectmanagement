<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Project;
use App\Models\Training;
use App\Models\Task;
use App\Models\User;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\Common\Creator\WriterFactory;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::query()
            ->with(['company', 'user'])
            ->withCount('tasks')
            ->orderByDesc('updated_at')
            ->get();

        $selectedProject = null;
        if ($request->filled('project_id')) {
            $selectedProject = $projects->firstWhere('id', $request->integer('project_id'));
        }

        if (! $selectedProject) {
            $selectedProject = $projects->firstWhere('tasks_count', '>', 0) ?? $projects->first();
        }

        if (! $selectedProject) {
            return view('reports.index', [
                'projects' => $projects,
                'selectedProject' => null,
                'timelineDays' => collect(),
                'taskRows' => collect(),
                'summary' => [
                    'projects' => 0,
                    'tasks' => 0,
                    'done' => 0,
                    'in_progress' => 0,
                    'overdue' => 0,
                ],
                'rangeStart' => null,
                'rangeEnd' => null,
            ]);
        }

        $selectedProject->load([
            'company',
            'user',
            'tasks' => fn ($query) => $query
                ->with('user')
                ->orderBy('sort_order')
                ->orderBy('start_date')
                ->orderBy('due_date')
                ->orderBy('id'),
        ]);
        $selectedProject->loadCount([
            'tasks',
            'tasks as completed_tasks_count' => fn ($query) => $query->where('status', 'done'),
            'tasks as active_tasks_count' => fn ($query) => $query->whereIn('status', ['todo', 'in_progress']),
            'tasks as overdue_tasks_count' => fn ($query) => $query
                ->where('status', '!=', 'done')
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', Carbon::today()),
        ]);

        $tasks = $selectedProject->tasks;
        $rangeStart = $tasks->pluck('start_date')->filter()->min()
            ?? $selectedProject->start_date
            ?? Carbon::today();
        $rangeEnd = $tasks->pluck('due_date')->filter()->max()
            ?? $selectedProject->due_date
            ?? Carbon::today();

        $rangeStart = Carbon::parse($rangeStart)->startOfDay();
        $rangeEnd = Carbon::parse($rangeEnd)->endOfDay();

        if ($rangeEnd->diffInDays($rangeStart) < 13) {
            $rangeEnd = $rangeStart->copy()->addDays(13)->endOfDay();
        }

        $timelineDays = collect(CarbonPeriod::create($rangeStart, $rangeEnd))
            ->map(fn (Carbon $date) => $date->copy());

        $taskRows = $tasks->map(function ($task) use ($rangeStart, $timelineDays) {
            $taskStart = $task->start_date ? Carbon::parse($task->start_date)->startOfDay() : $rangeStart->copy();
            $taskEnd = $task->due_date ? Carbon::parse($task->due_date)->startOfDay() : $taskStart->copy();

            if ($taskEnd->lessThan($taskStart)) {
                [$taskStart, $taskEnd] = [$taskEnd->copy(), $taskStart->copy()];
            }

            $offsetDays = max(0, $rangeStart->diffInDays($taskStart));
            $durationDays = max(1, $taskStart->diffInDays($taskEnd) + 1);
            $spanDays = min($durationDays, max(1, $timelineDays->count() - $offsetDays));

            return [
                'task' => $task,
                'offset' => $offsetDays + 1,
                'span' => $spanDays,
                'taskStart' => $taskStart,
                'taskEnd' => $taskEnd,
            ];
        });

        $summary = [
            'projects' => $projects->count(),
            'tasks' => $selectedProject->tasks_count,
            'done' => $selectedProject->completed_tasks_count,
            'in_progress' => $selectedProject->active_tasks_count,
            'overdue' => $selectedProject->overdue_tasks_count,
        ];

        return view('reports.index', [
            'projects' => $projects,
            'selectedProject' => $selectedProject,
            'timelineDays' => $timelineDays,
            'taskRows' => $taskRows,
            'summary' => $summary,
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
        ]);
    }

    public function deadlines(Request $request): View
    {
        return view('reports.deadlines', $this->buildDeadlineReportData());
    }

    public function deadlinesExport(Request $request)
    {
        $data = $this->buildDeadlineReportData();
        $filename = 'deadline-alerts-' . $data['today']->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($data): void {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                return;
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['รายงานแจ้งเตือนกำหนด']);
            fputcsv($output, ['วันที่', $data['today']->translatedFormat('d M Y')]);
            fputcsv($output, ['แจ้งเตือนล่วงหน้า', $data['warningDays'] . ' วัน']);
            fputcsv($output, ['รายการต้องติดตาม', $data['summary']['critical_total'] ?? 0]);
            fputcsv($output, ['ครบกำหนดวันนี้', $data['summary']['due_today'] ?? 0]);
            fputcsv($output, []);
            fputcsv($output, [
                'ประเภท',
                'รายการ',
                'บริษัท',
                'บริบท',
                'ผู้รับผิดชอบ',
                'ครบกำหนด',
                'สถานะเตือน',
                'สถานะ',
            ]);

            foreach ($data['combinedRows'] as $row) {
                fputcsv($output, [
                    $row['type_label'],
                    $row['title'],
                    $row['company'] ?? '-',
                    $row['type'] === 'project' ? '-' : ($row['project'] ?? '-'),
                    $row['owner'] ?? '-',
                    $row['due_label'] ?? '-',
                    $row['alert_label'] ?? '-',
                    $row['status_label'] ?? '-',
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function deadlinesExportXlsx(Request $request)
    {
        $data = $this->buildDeadlineReportData();
        $tempPath = tempnam(sys_get_temp_dir(), 'deadline-alerts-');

        if ($tempPath === false) {
            abort(500, 'Unable to create temporary export file.');
        }

        $xlsxPath = $tempPath . '.xlsx';
        @rename($tempPath, $xlsxPath);

        $writer = WriterFactory::createFromFile($xlsxPath);
        $writer->openToFile($xlsxPath);

        $writer->addRow(Row::fromValues(['รายงานแจ้งเตือนกำหนด']));
        $writer->addRow(Row::fromValues(['วันที่', $data['today']->translatedFormat('d M Y')]));
        $writer->addRow(Row::fromValues(['แจ้งเตือนล่วงหน้า', $data['warningDays'] . ' วัน']));
        $writer->addRow(Row::fromValues(['รายการต้องติดตาม', $data['summary']['critical_total'] ?? 0]));
        $writer->addRow(Row::fromValues(['ครบกำหนดวันนี้', $data['summary']['due_today'] ?? 0]));
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues([
            'ประเภท',
            'รายการ',
            'บริษัท',
            'บริบท',
            'ผู้รับผิดชอบ',
            'ครบกำหนด',
            'สถานะเตือน',
            'สถานะ',
        ]));

        foreach ($data['combinedRows'] as $row) {
            $writer->addRow(Row::fromValues([
                $row['type_label'],
                $row['title'],
                $row['company'] ?? '-',
                $row['type'] === 'project' ? '-' : ($row['project'] ?? '-'),
                $row['owner'] ?? '-',
                $row['due_label'] ?? '-',
                $row['alert_label'] ?? '-',
                $row['status_label'] ?? '-',
            ]));
        }

        $writer->close();

        return response()->download($xlsxPath, 'deadline-alerts-' . $data['today']->format('Y-m-d') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function buildDeadlineReportData(): array
    {
        $today = Carbon::today()->startOfDay();
        $warningDays = 3;

        $baseProjects = Project::query()
            ->with(['company', 'user'])
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn ($query) => $query->where('status', 'done'),
                'tasks as active_tasks_count' => fn ($query) => $query->whereIn('status', ['todo', 'in_progress']),
                'tasks as overdue_tasks_count' => fn ($query) => $query
                    ->where('status', '!=', 'done')
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $today),
            ])
            ->whereNotNull('due_date')
            ->latest('due_date')
            ->latest('id')
            ->get();

        $baseTasks = Task::query()
            ->with(['project.company', 'user'])
            ->whereNotNull('due_date')
            ->latest('due_date')
            ->latest('id')
            ->get();

        $projectRows = $baseProjects
            ->filter(fn (Project $project) => $project->status !== 'completed' && filled($project->due_date))
            ->map(function (Project $project) use ($today, $warningDays): array {
                $dueDate = Carbon::parse($project->due_date)->startOfDay();
                $daysLeft = $today->diffInDays($dueDate, false);
                $alertType = $daysLeft < 0 ? 'overdue' : ($daysLeft <= $warningDays ? 'warning' : 'normal');

                return [
                    'type' => 'project',
                    'type_label' => 'Project',
                    'type_badge' => 'bg-violet-50 text-violet-700 ring-violet-200',
                    'id' => $project->id,
                    'title' => $project->name,
                    'company' => $project->company?->name ?? 'ไม่ระบุบริษัท',
                    'owner' => $project->user?->name ?? 'ไม่ระบุเจ้าของ',
                    'status_label' => match ($project->status) {
                        'active' => 'กำลังดำเนินการ',
                        'on_hold' => 'พักงาน',
                        'completed' => 'เสร็จสมบูรณ์',
                        default => $project->status,
                    },
                    'status_class' => match ($project->status) {
                        'active' => 'bg-sky-50 text-sky-700 ring-sky-200',
                        'on_hold' => 'bg-amber-50 text-amber-700 ring-amber-200',
                        'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                        default => 'bg-slate-50 text-slate-700 ring-slate-200',
                    },
                    'due_label' => $dueDate->translatedFormat('d M Y'),
                    'days_left' => $daysLeft,
                    'alert_label' => $daysLeft < 0
                        ? 'เกิน ' . number_format(abs($daysLeft)) . ' วัน'
                        : ($daysLeft === 0 ? 'ครบกำหนดวันนี้' : 'เหลือ ' . number_format($daysLeft) . ' วัน'),
                    'alert_class' => match ($alertType) {
                        'overdue' => 'bg-rose-50 text-rose-700 ring-rose-200',
                        'warning' => 'bg-amber-50 text-amber-700 ring-amber-200',
                        default => 'bg-slate-50 text-slate-700 ring-slate-200',
                    },
                    'tasks_count' => (int) ($project->tasks_count ?? 0),
                    'completed_tasks_count' => (int) ($project->completed_tasks_count ?? 0),
                    'active_tasks_count' => (int) ($project->active_tasks_count ?? 0),
                    'overdue_tasks_count' => (int) ($project->overdue_tasks_count ?? 0),
                    'progress' => (int) (($project->tasks_count ?? 0) > 0 ? round(($project->completed_tasks_count / max(1, $project->tasks_count)) * 100) : 0),
                    'link' => route('projects.show', $project),
                ];
            })
            ->filter(fn (array $row) => $row['days_left'] <= $warningDays)
            ->sortBy('days_left')
            ->values();

        $taskRows = $baseTasks
            ->filter(fn (Task $task) => $task->status !== 'done' && filled($task->due_date))
            ->map(function (Task $task) use ($today, $warningDays): array {
                $dueDate = Carbon::parse($task->due_date)->startOfDay();
                $daysLeft = $today->diffInDays($dueDate, false);
                $alertType = $daysLeft < 0 ? 'overdue' : ($daysLeft <= $warningDays ? 'warning' : 'normal');

                return [
                    'type' => 'task',
                    'type_label' => 'Task',
                    'type_badge' => 'bg-sky-50 text-sky-700 ring-sky-200',
                    'id' => $task->id,
                    'title' => $task->title,
                    'company' => $task->project?->company?->name ?? 'ไม่ระบุบริษัท',
                    'project' => $task->project?->name ?? 'ไม่ระบุโปรเจกต์',
                    'owner' => $task->user?->name ?? 'ไม่ระบุผู้รับผิดชอบ',
                    'status_label' => match ($task->status) {
                        'todo' => 'ยังไม่เริ่ม',
                        'in_progress' => 'กำลังทำ',
                        default => $task->status,
                    },
                    'status_class' => match ($task->status) {
                        'todo' => 'bg-amber-50 text-amber-700 ring-amber-200',
                        'in_progress' => 'bg-sky-50 text-sky-700 ring-sky-200',
                        default => 'bg-slate-50 text-slate-700 ring-slate-200',
                    },
                    'due_label' => $dueDate->translatedFormat('d M Y'),
                    'days_left' => $daysLeft,
                    'alert_label' => $daysLeft < 0
                        ? 'เกิน ' . number_format(abs($daysLeft)) . ' วัน'
                        : ($daysLeft === 0 ? 'ครบกำหนดวันนี้' : 'เหลือ ' . number_format($daysLeft) . ' วัน'),
                    'alert_class' => match ($alertType) {
                        'overdue' => 'bg-rose-50 text-rose-700 ring-rose-200',
                        'warning' => 'bg-amber-50 text-amber-700 ring-amber-200',
                        default => 'bg-slate-50 text-slate-700 ring-slate-200',
                    },
                    'link' => $task->project ? route('projects.tasks.show', [$task->project, $task]) : route('projects.index'),
                ];
            })
            ->filter(fn (array $row) => $row['days_left'] <= $warningDays)
            ->sortBy('days_left')
            ->values();

        $combinedRows = $projectRows
            ->merge($taskRows)
            ->sortBy('days_left')
            ->values();

        return [
            'today' => $today,
            'warningDays' => $warningDays,
            'summary' => [
                'critical_total' => $combinedRows->count(),
                'project_warning' => $projectRows->filter(fn (array $row) => $row['days_left'] >= 0)->count(),
                'project_overdue' => $projectRows->filter(fn (array $row) => $row['days_left'] < 0)->count(),
                'task_warning' => $taskRows->filter(fn (array $row) => $row['days_left'] >= 0)->count(),
                'task_overdue' => $taskRows->filter(fn (array $row) => $row['days_left'] < 0)->count(),
                'due_today' => $combinedRows->filter(fn (array $row) => $row['days_left'] === 0)->count(),
            ],
            'combinedRows' => $combinedRows,
            'projectRows' => $projectRows,
            'taskRows' => $taskRows,
        ];
    }

    public function yearly(Request $request): View
    {
        $today = Carbon::today();

        $baseProjects = Project::query()
            ->with(['company', 'user'])
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn ($query) => $query->where('status', 'done'),
                'tasks as active_tasks_count' => fn ($query) => $query->whereIn('status', ['todo', 'in_progress']),
                'tasks as overdue_tasks_count' => fn ($query) => $query
                    ->where('status', '!=', 'done')
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $today),
            ])
            ->latest('due_date')
            ->latest('id')
            ->get();

        $availableYears = $baseProjects
            ->flatMap(function (Project $project): array {
                $years = [];

                foreach ([$project->start_date, $project->due_date, $project->created_at] as $date) {
                    if ($date) {
                        $years[] = Carbon::parse($date)->year;
                    }
                }

                return $years;
            })
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        $selectedYear = $request->integer('year') ?: ($availableYears->first() ?? $today->year);
        $yearStart = Carbon::create($selectedYear, 1, 1)->startOfDay();
        $yearEnd = Carbon::create($selectedYear, 12, 31)->endOfDay();

        $yearProjects = $baseProjects
            ->filter(fn (Project $project) => $this->projectTouchesYear($project, $yearStart, $yearEnd))
            ->values();

        $monthlyStats = collect(range(1, 12))->map(function (int $month) use ($selectedYear, $yearProjects): array {
            $monthStart = Carbon::create($selectedYear, $month, 1)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();

            $projectsInMonth = $yearProjects->filter(fn (Project $project) => $this->projectTouchesMonth($project, $monthStart, $monthEnd));

            return [
                'month' => $monthStart,
                'label' => $monthStart->translatedFormat('M'),
                'total' => $projectsInMonth->count(),
                'started' => $projectsInMonth->filter(fn (Project $project) => filled($project->start_date) && Carbon::parse($project->start_date)->month === $month && Carbon::parse($project->start_date)->year === $selectedYear)->count(),
                'completed' => $projectsInMonth->filter(fn (Project $project) => $project->status === 'completed' && filled($project->due_date) && Carbon::parse($project->due_date)->month === $month && Carbon::parse($project->due_date)->year === $selectedYear)->count(),
                'active' => $projectsInMonth->where('status', 'active')->count(),
                'overdue' => $projectsInMonth->filter(fn (Project $project) => $project->status !== 'completed' && filled($project->due_date) && Carbon::parse($project->due_date)->isPast())->count(),
            ];
        });

        $summary = [
            'companies' => $yearProjects->pluck('company_id')->filter()->unique()->count(),
            'projects' => $yearProjects->count(),
            'active' => $yearProjects->where('status', 'active')->count(),
            'on_hold' => $yearProjects->where('status', 'on_hold')->count(),
            'completed' => $yearProjects->where('status', 'completed')->count(),
            'overdue' => $yearProjects->filter(fn (Project $project) => $project->status !== 'completed' && filled($project->due_date) && Carbon::parse($project->due_date)->isPast())->count(),
            'tasks' => $yearProjects->sum('tasks_count'),
            'done_tasks' => $yearProjects->sum('completed_tasks_count'),
            'task_progress' => $yearProjects->sum('tasks_count') > 0
                ? (int) round(($yearProjects->sum('completed_tasks_count') / max(1, $yearProjects->sum('tasks_count'))) * 100)
                : 0,
        ];

        $companySummaries = $yearProjects
            ->groupBy(fn (Project $project) => $project->company_id ? 'company:' . $project->company_id : 'company:none')
            ->map(function ($items, string $groupKey) use ($selectedYear): array {
                $firstProject = $items->first();
                $companyId = $firstProject?->company_id;
                $companyName = $firstProject?->company?->name ?? 'ไม่ระบุบริษัท';
                $projectsCount = $items->count();
                $completedCount = $items->where('status', 'completed')->count();
                $activeCount = $items->where('status', 'active')->count();
                $onHoldCount = $items->where('status', 'on_hold')->count();
                $overdueCount = $items->filter(fn (Project $project) => $project->status !== 'completed' && filled($project->due_date) && Carbon::parse($project->due_date)->isPast())->count();
                $tasksCount = $items->sum('tasks_count');
                $doneTasksCount = $items->sum('completed_tasks_count');
                $activeTasksCount = $items->sum('active_tasks_count');
                $overdueTasksCount = $items->sum('overdue_tasks_count');
                $startedCount = $items->filter(fn (Project $project) => filled($project->start_date) && Carbon::parse($project->start_date)->year === $selectedYear)->count();
                $progress = $tasksCount > 0 ? (int) round(($doneTasksCount / $tasksCount) * 100) : 0;
                $latestProject = $items->sortByDesc(fn (Project $project) => $project->due_date ?? $project->start_date ?? $project->created_at)->first();

                return [
                    'company_id' => $companyId,
                    'company' => $companyName,
                    'projects' => $projectsCount,
                    'started' => $startedCount,
                    'completed_projects' => $completedCount,
                    'active_projects' => $activeCount,
                    'on_hold_projects' => $onHoldCount,
                    'completed' => $completedCount,
                    'active' => $activeCount,
                    'on_hold' => $onHoldCount,
                    'overdue' => $overdueCount,
                    'tasks' => $tasksCount,
                    'done_tasks' => $doneTasksCount,
                    'active_tasks' => $activeTasksCount,
                    'overdue_tasks' => $overdueTasksCount,
                    'progress' => $progress,
                    'latest_project' => $latestProject?->name,
                    'latest_due' => $latestProject?->due_date ? Carbon::parse($latestProject->due_date)->format('d/m/Y') : null,
                ];
            })
            ->sortByDesc(fn (array $row) => $row['projects'])
            ->values();

        $topCompanies = $companySummaries->take(6);
        $statusBreakdown = collect([
            ['key' => 'active', 'label' => 'กำลังดำเนินการ', 'class' => 'from-sky-600 to-cyan-500'],
            ['key' => 'on_hold', 'label' => 'พักงาน', 'class' => 'from-amber-500 to-orange-400'],
            ['key' => 'completed', 'label' => 'เสร็จสมบูรณ์', 'class' => 'from-emerald-600 to-teal-500'],
        ])->map(function (array $row) use ($yearProjects): array {
            $count = $yearProjects->where('status', $row['key'])->count();

            return $row + ['count' => $count];
        });

        $monthlyLabels = $monthlyStats->pluck('label')->values();
        $monthlyTotals = $monthlyStats->pluck('total')->values();
        $monthlyStarted = $monthlyStats->pluck('started')->values();
        $monthlyCompleted = $monthlyStats->pluck('completed')->values();
        $monthlyOverdue = $monthlyStats->pluck('overdue')->values();
        $companyLabels = $topCompanies->pluck('company')->values();
        $companyProjects = $topCompanies->pluck('projects')->values();
        $companyProgress = $topCompanies->pluck('progress')->values();
        $companyLinks = $topCompanies->map(function (array $row) use ($selectedYear): ?string {
            return $row['company_id']
                ? route('reports.yearly.company', ['company' => $row['company_id'], 'year' => $selectedYear])
                : null;
        })->values();

        $recentProjects = $yearProjects
            ->sortByDesc(fn (Project $project) => $project->due_date ?? $project->start_date ?? $project->created_at)
            ->take(10)
            ->values();

        return view('reports.yearly', [
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'yearStart' => $yearStart,
            'yearEnd' => $yearEnd,
            'summary' => $summary,
            'monthlyStats' => $monthlyStats,
            'monthlyLabels' => $monthlyLabels,
            'monthlyTotals' => $monthlyTotals,
            'monthlyStarted' => $monthlyStarted,
            'monthlyCompleted' => $monthlyCompleted,
            'monthlyOverdue' => $monthlyOverdue,
            'companySummaries' => $companySummaries,
            'topCompanies' => $topCompanies,
            'companyLabels' => $companyLabels,
            'companyProjects' => $companyProjects,
            'companyProgress' => $companyProgress,
            'companyLinks' => $companyLinks,
            'statusBreakdown' => $statusBreakdown,
            'recentProjects' => $recentProjects,
        ]);
    }

    public function yearlyCompany(Company $company, Request $request): View
    {
        $today = Carbon::today();

        $baseProjects = Project::query()
            ->with(['company', 'user'])
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn ($query) => $query->where('status', 'done'),
                'tasks as active_tasks_count' => fn ($query) => $query->whereIn('status', ['todo', 'in_progress']),
                'tasks as overdue_tasks_count' => fn ($query) => $query
                    ->where('status', '!=', 'done')
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $today),
            ])
            ->where('company_id', $company->id)
            ->latest('due_date')
            ->latest('id')
            ->get();

        $availableYears = $baseProjects
            ->flatMap(function (Project $project): array {
                $years = [];

                foreach ([$project->start_date, $project->due_date, $project->created_at] as $date) {
                    if ($date) {
                        $years[] = Carbon::parse($date)->year;
                    }
                }

                return $years;
            })
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        $selectedYear = $request->integer('year') ?: ($availableYears->first() ?? $today->year);
        $yearStart = Carbon::create($selectedYear, 1, 1)->startOfDay();
        $yearEnd = Carbon::create($selectedYear, 12, 31)->endOfDay();

        $yearProjects = $baseProjects
            ->filter(fn (Project $project) => $this->projectTouchesYear($project, $yearStart, $yearEnd))
            ->values();

        $monthlyStats = collect(range(1, 12))->map(function (int $month) use ($selectedYear, $yearProjects): array {
            $monthStart = Carbon::create($selectedYear, $month, 1)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            $projectsInMonth = $yearProjects->filter(fn (Project $project) => $this->projectTouchesMonth($project, $monthStart, $monthEnd));

            return [
                'month' => $monthStart,
                'label' => $monthStart->translatedFormat('M'),
                'total' => $projectsInMonth->count(),
                'started' => $projectsInMonth->filter(fn (Project $project) => filled($project->start_date) && Carbon::parse($project->start_date)->month === $month && Carbon::parse($project->start_date)->year === $selectedYear)->count(),
                'completed' => $projectsInMonth->filter(fn (Project $project) => $project->status === 'completed' && filled($project->due_date) && Carbon::parse($project->due_date)->month === $month && Carbon::parse($project->due_date)->year === $selectedYear)->count(),
                'overdue' => $projectsInMonth->filter(fn (Project $project) => $project->status !== 'completed' && filled($project->due_date) && Carbon::parse($project->due_date)->isPast())->count(),
            ];
        });

        $summary = [
            'projects' => $yearProjects->count(),
            'tasks' => $yearProjects->sum('tasks_count'),
            'done_tasks' => $yearProjects->sum('completed_tasks_count'),
            'active' => $yearProjects->where('status', 'active')->count(),
            'on_hold' => $yearProjects->where('status', 'on_hold')->count(),
            'completed' => $yearProjects->where('status', 'completed')->count(),
            'overdue' => $yearProjects->filter(fn (Project $project) => $project->status !== 'completed' && filled($project->due_date) && Carbon::parse($project->due_date)->isPast())->count(),
            'task_progress' => $yearProjects->sum('tasks_count') > 0
                ? (int) round(($yearProjects->sum('completed_tasks_count') / max(1, $yearProjects->sum('tasks_count'))) * 100)
                : 0,
        ];

        $statusBreakdown = collect([
            ['key' => 'active', 'label' => 'กำลังดำเนินการ', 'class' => 'from-sky-600 to-cyan-500'],
            ['key' => 'on_hold', 'label' => 'พักงาน', 'class' => 'from-amber-500 to-orange-400'],
            ['key' => 'completed', 'label' => 'เสร็จสมบูรณ์', 'class' => 'from-emerald-600 to-teal-500'],
        ])->map(function (array $row) use ($yearProjects): array {
            $count = $yearProjects->where('status', $row['key'])->count();

            return $row + ['count' => $count];
        });

        $recentProjects = $yearProjects
            ->sortByDesc(fn (Project $project) => $project->due_date ?? $project->start_date ?? $project->created_at)
            ->take(8)
            ->values();

        $projectLinks = $yearProjects->map(fn (Project $project) => route('projects.show', $project))->values();

        return view('reports.yearly-company', [
            'company' => $company,
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'yearStart' => $yearStart,
            'yearEnd' => $yearEnd,
            'summary' => $summary,
            'monthlyStats' => $monthlyStats,
            'statusBreakdown' => $statusBreakdown,
            'recentProjects' => $recentProjects,
            'projectLinks' => $projectLinks,
        ]);
    }

    public function trainings(Request $request): View
    {
        return view('reports.trainings', $this->buildTrainingReportData($request));
    }

    public function trainingsExport(Request $request)
    {
        $data = $this->buildTrainingReportData($request);
        $filename = 'training-report-' . $data['selectedYear'] . '.csv';

        return response()->streamDownload(function () use ($data): void {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                return;
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['รายงานการอบรมรายปี']);
            fputcsv($output, ['ปี', $data['selectedYear']]);
            fputcsv($output, ['ช่วงข้อมูล', $data['yearStart']->translatedFormat('d M Y') . ' - ' . $data['yearEnd']->translatedFormat('d M Y')]);
            fputcsv($output, ['รอบอบรมทั้งหมด', $data['summary']['total'] ?? 0]);
            fputcsv($output, ['ผู้เข้าร่วมรวม', $data['summary']['attended'] ?? 0]);
            fputcsv($output, ['ความจุรวม', $data['summary']['capacity'] ?? 0]);
            fputcsv($output, []);
            fputcsv($output, [
                'วันที่',
                'หัวข้อ',
                'ผู้เข้าร่วมเป้าหมาย',
                'ผู้สอน',
                'สถานที่',
                'เวลาเริ่ม',
                'เวลาจบ',
                'ผู้เข้าร่วมจริง',
                'ความจุ',
                'อัตราเข้าร่วม',
                'สถานะ',
                'หมายเหตุ',
            ]);

            foreach ($data['trainingRows'] as $training) {
                $capacity = (int) ($training->capacity ?? 0);
                $attended = (int) ($training->attended ?? 0);
                $attendance = $capacity > 0 ? (int) round(($attended / max(1, $capacity)) * 100) : 0;

                fputcsv($output, [
                    optional($training->training_date)->translatedFormat('d/m/Y') ?? '-',
                    $training->title,
                    $training->audience ?: '-',
                    $training->trainer ?: '-',
                    $training->location ?: '-',
                    $training->start_time ?: '-',
                    $training->end_time ?: '-',
                    $attended,
                    $capacity,
                    $attendance . '%',
                    $this->trainingStatusLabel($training->status),
                    $training->notes ?: '-',
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function trainingsExportXlsx(Request $request)
    {
        $data = $this->buildTrainingReportData($request);
        $tempPath = tempnam(sys_get_temp_dir(), 'training-report-');

        if ($tempPath === false) {
            abort(500, 'Unable to create temporary export file.');
        }

        $xlsxPath = $tempPath . '.xlsx';
        @rename($tempPath, $xlsxPath);

        $writer = WriterFactory::createFromFile($xlsxPath);
        $writer->openToFile($xlsxPath);

        $writer->addRow(Row::fromValues(['รายงานการอบรมรายปี']));
        $writer->addRow(Row::fromValues(['ปี', $data['selectedYear']]));
        $writer->addRow(Row::fromValues(['ช่วงข้อมูล', $data['yearStart']->translatedFormat('d M Y') . ' - ' . $data['yearEnd']->translatedFormat('d M Y')]));
        $writer->addRow(Row::fromValues(['รอบอบรมทั้งหมด', $data['summary']['total'] ?? 0]));
        $writer->addRow(Row::fromValues(['ผู้เข้าร่วมรวม', $data['summary']['attended'] ?? 0]));
        $writer->addRow(Row::fromValues(['ความจุรวม', $data['summary']['capacity'] ?? 0]));
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues([
            'วันที่',
            'หัวข้อ',
            'ผู้เข้าร่วมเป้าหมาย',
            'ผู้สอน',
            'สถานที่',
            'เวลาเริ่ม',
            'เวลาจบ',
            'ผู้เข้าร่วมจริง',
            'ความจุ',
            'อัตราเข้าร่วม',
            'สถานะ',
            'หมายเหตุ',
        ]));

        foreach ($data['trainingRows'] as $training) {
            $capacity = (int) ($training->capacity ?? 0);
            $attended = (int) ($training->attended ?? 0);
            $attendance = $capacity > 0 ? (int) round(($attended / max(1, $capacity)) * 100) : 0;

            $writer->addRow(Row::fromValues([
                optional($training->training_date)->translatedFormat('d/m/Y') ?? '-',
                $training->title,
                $training->audience ?: '-',
                $training->trainer ?: '-',
                $training->location ?: '-',
                $training->start_time ?: '-',
                $training->end_time ?: '-',
                $attended,
                $capacity,
                $attendance . '%',
                $this->trainingStatusLabel($training->status),
                $training->notes ?: '-',
            ]));
        }

        $writer->close();

        return response()->download($xlsxPath, 'training-report-' . $data['selectedYear'] . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function teamYearly(Request $request): View
    {
        $data = $this->buildTeamYearlyReportData($request);

        return view('reports.team-yearly', $data);
    }

    public function teamYearlyExport(Request $request)
    {
        $data = $this->buildTeamYearlyReportData($request);

        $filename = 'team-yearly-report-' . $data['selectedYear'] . '.csv';

        return response()->streamDownload(function () use ($data): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'Name',
                'Role',
                'Status',
                'Tasks Total',
                'Tasks Completed',
                'Tasks In Progress',
                'Tasks Overdue',
                'Projects Total',
                'Projects Completed',
                'Projects Active',
                'Projects On Hold',
                'Completion Rate',
                'Latest Activity',
            ]);

            foreach ($data['teamRows'] as $row) {
                fputcsv($output, [
                    $row['name'],
                    $row['role'],
                    $row['status'],
                    $row['tasks_total'],
                    $row['tasks_completed'],
                    $row['tasks_in_progress'],
                    $row['tasks_overdue'],
                    $row['projects_total'],
                    $row['projects_completed'],
                    $row['projects_active'],
                    $row['projects_on_hold'],
                    $row['completion_rate'] . '%',
                    $row['latest_activity'] ?? '-',
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function teamYearlyExportXlsx(Request $request)
    {
        $data = $this->buildTeamYearlyReportData($request);
        $tempPath = tempnam(sys_get_temp_dir(), 'team-yearly-');

        if ($tempPath === false) {
            abort(500, 'Unable to create temporary export file.');
        }

        $xlsxPath = $tempPath . '.xlsx';
        @rename($tempPath, $xlsxPath);

        $writer = WriterFactory::createFromFile($xlsxPath);
        $writer->openToFile($xlsxPath);

        $writer->addRow(Row::fromValues(['รายงานผลงานทีมรายปี']));
        $writer->addRow(Row::fromValues(['ปี', $data['selectedYear']]));
        $writer->addRow(Row::fromValues(['ช่วงข้อมูล', $data['yearStart']->translatedFormat('d M Y') . ' - ' . $data['yearEnd']->translatedFormat('d M Y')]));
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues([
            'Name',
            'Role',
            'Status',
            'Tasks Total',
            'Tasks Completed',
            'Tasks In Progress',
            'Tasks Overdue',
            'Projects Total',
            'Projects Completed',
            'Projects Active',
            'Projects On Hold',
            'Completion Rate',
            'Latest Activity',
        ]));

        foreach ($data['teamRows'] as $row) {
            $writer->addRow(Row::fromValues([
                $row['name'],
                $row['role'],
                $row['status'],
                $row['tasks_total'],
                $row['tasks_completed'],
                $row['tasks_in_progress'],
                $row['tasks_overdue'],
                $row['projects_total'],
                $row['projects_completed'],
                $row['projects_active'],
                $row['projects_on_hold'],
                $row['completion_rate'] . '%',
                $row['latest_activity'] ?? '-',
            ]));
        }

        $writer->close();

        return response()->download($xlsxPath, 'team-yearly-report-' . $data['selectedYear'] . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function buildTeamYearlyReportData(Request $request): array
    {
        $today = Carbon::today();

        $baseUsers = User::query()
            ->orderBy('name')
            ->get();

        $baseTasks = Task::query()
            ->with(['user', 'project.company'])
            ->whereNotNull('user_id')
            ->latest('completed_at')
            ->latest('due_date')
            ->latest('id')
            ->get();

        $baseProjects = Project::query()
            ->with(['company', 'user'])
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn ($query) => $query->where('status', 'done'),
                'tasks as active_tasks_count' => fn ($query) => $query->whereIn('status', ['todo', 'in_progress']),
                'tasks as overdue_tasks_count' => fn ($query) => $query
                    ->where('status', '!=', 'done')
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $today),
            ])
            ->latest('due_date')
            ->latest('id')
            ->get();

        $availableYears = collect()
            ->merge($baseProjects->flatMap(function (Project $project): array {
                $years = [];

                foreach ([$project->start_date, $project->due_date, $project->created_at] as $date) {
                    if ($date) {
                        $years[] = Carbon::parse($date)->year;
                    }
                }

                return $years;
            }))
            ->merge($baseTasks->flatMap(function (Task $task): array {
                $years = [];

                foreach ([$task->start_date, $task->due_date, $task->completed_at, $task->created_at] as $date) {
                    if ($date) {
                        $years[] = Carbon::parse($date)->year;
                    }
                }

                return $years;
            }))
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        $selectedYear = $request->integer('year') ?: ($availableYears->first() ?? $today->year);
        $yearStart = Carbon::create($selectedYear, 1, 1)->startOfDay();
        $yearEnd = Carbon::create($selectedYear, 12, 31)->endOfDay();

        $yearTasks = $baseTasks
            ->filter(fn (Task $task) => $this->taskTouchesYear($task, $yearStart, $yearEnd))
            ->values();

        $yearProjects = $baseProjects
            ->filter(fn (Project $project) => $project->user_id && $this->projectTouchesYear($project, $yearStart, $yearEnd))
            ->values();

        $teamRows = $baseUsers
            ->map(function (User $user) use ($yearTasks, $yearProjects): array {
                $userTasks = $yearTasks->where('user_id', $user->id)->values();
                $userProjects = $yearProjects->where('user_id', $user->id)->values();
                $completedTasks = $userTasks->where('status', 'done')->count();
                $inProgressTasks = $userTasks->whereIn('status', ['todo', 'in_progress'])->count();
                $overdueTasks = $userTasks->filter(fn (Task $task) => $task->status !== 'done' && filled($task->due_date) && Carbon::parse($task->due_date)->isPast())->count();
                $projectCompleted = $userProjects->where('status', 'completed')->count();
                $projectActive = $userProjects->where('status', 'active')->count();
                $projectOnHold = $userProjects->where('status', 'on_hold')->count();
                $completionRate = $userTasks->count() > 0
                    ? (int) round(($completedTasks / max(1, $userTasks->count())) * 100)
                    : 0;
                $latestActivityTs = collect([
                    ...$userTasks->map(fn (Task $task) => $task->completed_at?->timestamp ?? null)->all(),
                    ...$userTasks->map(fn (Task $task) => $task->due_date?->timestamp ?? null)->all(),
                    ...$userTasks->map(fn (Task $task) => $task->start_date?->timestamp ?? null)->all(),
                    ...$userTasks->map(fn (Task $task) => $task->created_at?->timestamp ?? null)->all(),
                    ...$userProjects->map(fn (Project $project) => $project->due_date?->timestamp ?? null)->all(),
                    ...$userProjects->map(fn (Project $project) => $project->start_date?->timestamp ?? null)->all(),
                    ...$userProjects->map(fn (Project $project) => $project->created_at?->timestamp ?? null)->all(),
                ])->filter()->max();
                $latestActivity = $latestActivityTs ? Carbon::createFromTimestamp($latestActivityTs) : null;

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => User::roleLabel($user->role),
                    'status' => User::statusLabel($user->status),
                    'status_key' => $user->status,
                    'tasks_total' => $userTasks->count(),
                    'tasks_completed' => $completedTasks,
                    'tasks_in_progress' => $inProgressTasks,
                    'tasks_overdue' => $overdueTasks,
                    'projects_total' => $userProjects->count(),
                    'projects_completed' => $projectCompleted,
                    'projects_active' => $projectActive,
                    'projects_on_hold' => $projectOnHold,
                    'completion_rate' => $completionRate,
                    'latest_activity' => $latestActivity?->format('d/m/Y'),
                    'latest_activity_ts' => $latestActivity?->timestamp ?? 0,
                ];
            })
            ->filter(fn (array $row) => $row['tasks_total'] > 0 || $row['projects_total'] > 0)
            ->sortByDesc(fn (array $row) => ($row['tasks_completed'] * 10000) + ($row['projects_total'] * 100) + $row['completion_rate'])
            ->values();

        $summary = [
            'members' => $teamRows->count(),
            'tasks_total' => $yearTasks->count(),
            'tasks_completed' => $yearTasks->where('status', 'done')->count(),
            'tasks_overdue' => $yearTasks->filter(fn (Task $task) => $task->status !== 'done' && filled($task->due_date) && Carbon::parse($task->due_date)->isPast())->count(),
            'projects_total' => $yearProjects->count(),
            'projects_completed' => $yearProjects->where('status', 'completed')->count(),
            'projects_active' => $yearProjects->where('status', 'active')->count(),
            'completion_rate' => $yearTasks->count() > 0
                ? (int) round(($yearTasks->where('status', 'done')->count() / max(1, $yearTasks->count())) * 100)
                : 0,
        ];

        $topPerformers = $teamRows->take(8);
        $topPerformerLinks = $topPerformers->map(fn (array $row) => '#team-' . $row['id'])->values();

        return [
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'yearStart' => $yearStart,
            'yearEnd' => $yearEnd,
            'summary' => $summary,
            'teamRows' => $teamRows,
            'topPerformers' => $topPerformers,
            'topPerformerLinks' => $topPerformerLinks,
        ];
    }

    private function buildTrainingReportData(Request $request): array
    {
        $baseTrainings = Training::query()
            ->orderByDesc('training_date')
            ->orderByDesc('id')
            ->get();

        $availableYears = $baseTrainings
            ->flatMap(function (Training $training): array {
                $years = [];

                foreach ([$training->training_date, $training->created_at] as $date) {
                    if ($date) {
                        $years[] = Carbon::parse($date)->year;
                    }
                }

                return $years;
            })
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        $today = Carbon::today();
        $selectedYear = $request->integer('year') ?: ($availableYears->first() ?? $today->year);
        $yearStart = Carbon::create($selectedYear, 1, 1)->startOfDay();
        $yearEnd = Carbon::create($selectedYear, 12, 31)->endOfDay();

        $yearTrainings = $baseTrainings
            ->filter(fn (Training $training) => filled($training->training_date) && Carbon::parse($training->training_date)->betweenIncluded($yearStart, $yearEnd))
            ->values();

        $monthlyStats = collect(range(1, 12))->map(function (int $month) use ($selectedYear, $yearTrainings): array {
            $monthStart = Carbon::create($selectedYear, $month, 1)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();

            $trainingsInMonth = $yearTrainings->filter(fn (Training $training) => filled($training->training_date) && Carbon::parse($training->training_date)->betweenIncluded($monthStart, $monthEnd));

            return [
                'month' => $monthStart,
                'label' => $monthStart->translatedFormat('M'),
                'total' => $trainingsInMonth->count(),
                'completed' => $trainingsInMonth->where('status', 'completed')->count(),
                'scheduled' => $trainingsInMonth->where('status', 'scheduled')->count(),
                'running' => $trainingsInMonth->where('status', 'in_progress')->count(),
                'cancelled' => $trainingsInMonth->where('status', 'cancelled')->count(),
                'attendance' => $trainingsInMonth->sum('capacity') > 0
                    ? (int) round(($trainingsInMonth->sum('attended') / max(1, $trainingsInMonth->sum('capacity'))) * 100)
                    : 0,
            ];
        });

        $summary = [
            'total' => $yearTrainings->count(),
            'scheduled' => $yearTrainings->where('status', 'scheduled')->count(),
            'running' => $yearTrainings->where('status', 'in_progress')->count(),
            'completed' => $yearTrainings->where('status', 'completed')->count(),
            'cancelled' => $yearTrainings->where('status', 'cancelled')->count(),
            'capacity' => $yearTrainings->sum('capacity'),
            'attended' => $yearTrainings->sum('attended'),
            'attendance' => $yearTrainings->sum('capacity') > 0
                ? (int) round(($yearTrainings->sum('attended') / max(1, $yearTrainings->sum('capacity'))) * 100)
                : 0,
        ];

        $recentTrainings = $yearTrainings
            ->sortByDesc(fn (Training $training) => $training->training_date ?? $training->created_at)
            ->take(12)
            ->values();

        $statusBreakdown = collect([
            ['key' => 'scheduled', 'label' => 'กำหนดแล้ว', 'class' => 'from-sky-600 to-cyan-500'],
            ['key' => 'in_progress', 'label' => 'กำลังอบรม', 'class' => 'from-amber-500 to-orange-400'],
            ['key' => 'completed', 'label' => 'เสร็จสิ้น', 'class' => 'from-emerald-600 to-teal-500'],
            ['key' => 'cancelled', 'label' => 'ยกเลิก', 'class' => 'from-rose-600 to-red-500'],
        ])->map(function (array $row) use ($yearTrainings): array {
            return $row + [
                'count' => $yearTrainings->where('status', $row['key'])->count(),
            ];
        });

        $topAttendanceTrainings = $yearTrainings
            ->filter(fn (Training $training) => (int) $training->capacity > 0)
            ->sortByDesc(fn (Training $training) => (int) round((($training->attended ?? 0) / max(1, (int) $training->capacity)) * 100))
            ->take(8)
            ->values();

        $trainingRows = $yearTrainings
            ->sortByDesc(fn (Training $training) => $training->training_date ?? $training->created_at)
            ->values();

        return [
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'yearStart' => $yearStart,
            'yearEnd' => $yearEnd,
            'summary' => $summary,
            'monthlyStats' => $monthlyStats,
            'recentTrainings' => $recentTrainings,
            'statusBreakdown' => $statusBreakdown,
            'topAttendanceTrainings' => $topAttendanceTrainings,
            'trainingRows' => $trainingRows,
        ];
    }

    private function trainingStatusLabel(?string $status): string
    {
        return match ($status) {
            'scheduled' => 'กำหนดแล้ว',
            'in_progress' => 'กำลังอบรม',
            'completed' => 'เสร็จสิ้น',
            'cancelled' => 'ยกเลิก',
            default => $status ?: '-',
        };
    }

    public function projectsOverview(Request $request): View
    {
        $today = Carbon::today();

        $baseProjects = Project::query()
            ->with(['company', 'user'])
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn ($query) => $query->where('status', 'done'),
                'tasks as active_tasks_count' => fn ($query) => $query->whereIn('status', ['todo', 'in_progress']),
                'tasks as overdue_tasks_count' => fn ($query) => $query
                    ->where('status', '!=', 'done')
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $today),
            ])
            ->latest('due_date')
            ->latest('id')
            ->get();

        $availableYears = $baseProjects
            ->flatMap(function (Project $project): array {
                $years = [];

                foreach ([$project->start_date, $project->due_date, $project->created_at] as $date) {
                    if ($date) {
                        $years[] = Carbon::parse($date)->year;
                    }
                }

                return $years;
            })
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        $selectedYear = $request->integer('year') ?: ($availableYears->first() ?? $today->year);
        $selectedMonth = $request->integer('month') ?: $today->month;
        $selectedMonth = max(1, min(12, $selectedMonth));
        $yearStart = Carbon::create($selectedYear, 1, 1)->startOfDay();
        $yearEnd = Carbon::create($selectedYear, 12, 31)->endOfDay();
        $monthStart = Carbon::create($selectedYear, $selectedMonth, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $filteredProjects = $baseProjects
            ->filter(fn (Project $project) => $this->projectTouchesYear($project, $yearStart, $yearEnd))
            ->values();
        $monthProjects = $filteredProjects
            ->filter(fn (Project $project) => $this->projectTouchesMonth($project, $monthStart, $monthEnd))
            ->values();

        $statusBreakdown = collect([
            ['key' => 'active', 'label' => 'กำลังดำเนินการ', 'class' => 'from-sky-600 to-cyan-500'],
            ['key' => 'on_hold', 'label' => 'พักงาน', 'class' => 'from-amber-500 to-orange-400'],
            ['key' => 'completed', 'label' => 'เสร็จสมบูรณ์', 'class' => 'from-emerald-600 to-teal-500'],
        ])->map(function (array $row) use ($filteredProjects): array {
            $count = $filteredProjects->where('status', $row['key'])->count();

            return $row + ['count' => $count];
        });

        $summary = [
            'projects' => $filteredProjects->count(),
            'active' => $filteredProjects->where('status', 'active')->count(),
            'on_hold' => $filteredProjects->where('status', 'on_hold')->count(),
            'completed' => $filteredProjects->where('status', 'completed')->count(),
            'overdue' => $filteredProjects->filter(fn (Project $project) => $project->status !== 'completed' && filled($project->due_date) && Carbon::parse($project->due_date)->isPast())->count(),
            'tasks' => $filteredProjects->sum('tasks_count'),
            'companies' => $filteredProjects->pluck('company_id')->filter()->unique()->count(),
        ];

        $monthSummary = [
            'projects' => $monthProjects->count(),
            'started' => $monthProjects->filter(fn (Project $project) => filled($project->start_date) && Carbon::parse($project->start_date)->year === $selectedYear && Carbon::parse($project->start_date)->month === $selectedMonth)->count(),
            'completed' => $monthProjects->filter(fn (Project $project) => $project->status === 'completed' && filled($project->due_date) && Carbon::parse($project->due_date)->year === $selectedYear && Carbon::parse($project->due_date)->month === $selectedMonth)->count(),
            'active' => $monthProjects->where('status', 'active')->count(),
            'on_hold' => $monthProjects->where('status', 'on_hold')->count(),
            'overdue' => $monthProjects->filter(fn (Project $project) => $project->status !== 'completed' && filled($project->due_date) && Carbon::parse($project->due_date)->isPast())->count(),
            'tasks' => $monthProjects->sum('tasks_count'),
        ];

        $calendarDays = collect(CarbonPeriod::create($monthStart->copy()->startOfWeek(Carbon::MONDAY), $monthStart->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY)))
            ->map(fn (Carbon $date) => $date->copy());

        $calendarEvents = $monthProjects->map(function (Project $project) use ($monthStart, $monthEnd): array {
            $start = $project->start_date ? Carbon::parse($project->start_date)->startOfDay() : null;
            $end = $project->due_date ? Carbon::parse($project->due_date)->endOfDay() : null;

            $displayStart = $start && $start->betweenIncluded($monthStart, $monthEnd)
                ? $start->copy()
                : $monthStart->copy();
            $displayEnd = $end && $end->betweenIncluded($monthStart, $monthEnd)
                ? $end->copy()
                : $monthEnd->copy();

            if ($start && $start->lessThan($monthStart)) {
                $displayStart = $monthStart->copy();
            }

            if ($end && $end->greaterThan($monthEnd)) {
                $displayEnd = $monthEnd->copy();
            }

            return [
                'id' => $project->id,
                'name' => $project->name,
                'status' => $project->status,
                'status_label' => match ($project->status) {
                    'active' => 'กำลังดำเนินการ',
                    'on_hold' => 'พักงาน',
                    'completed' => 'เสร็จสมบูรณ์',
                    default => $project->status,
                },
                'status_class' => match ($project->status) {
                    'active' => 'bg-sky-500',
                    'on_hold' => 'bg-amber-500',
                    'completed' => 'bg-emerald-500',
                    default => 'bg-slate-500',
                },
                'company' => $project->company?->name ?? 'ไม่ระบุบริษัท',
                'owner' => $project->user?->name ?? 'ไม่ระบุเจ้าของ',
                'start_date' => optional($project->start_date)->format('Y-m-d'),
                'due_date' => optional($project->due_date)->format('Y-m-d'),
                'start_label' => optional($project->start_date)->format('d/m/Y') ?? 'ไม่ระบุ',
                'due_label' => optional($project->due_date)->format('d/m/Y') ?? 'ไม่ระบุ',
                'description' => $project->description ?: 'ไม่มีรายละเอียดเพิ่มเติม',
                'tasks_count' => (int) ($project->tasks_count ?? 0),
                'completed_tasks_count' => (int) ($project->completed_tasks_count ?? 0),
                'active_tasks_count' => (int) ($project->active_tasks_count ?? 0),
                'overdue_tasks_count' => (int) ($project->overdue_tasks_count ?? 0),
                'progress' => (int) (($project->tasks_count ?? 0) > 0 ? round(($project->completed_tasks_count / max(1, $project->tasks_count)) * 100) : 0),
                'display_start' => $displayStart->format('Y-m-d'),
                'display_end' => $displayEnd->format('Y-m-d'),
            ];
        })->values();

        $monthlyStats = collect(range(1, 12))->map(function (int $month) use ($selectedYear, $filteredProjects): array {
            $monthStart = Carbon::create($selectedYear, $month, 1)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();

            $projectsInMonth = $filteredProjects->filter(fn (Project $project) => $this->projectTouchesMonth($project, $monthStart, $monthEnd));

            return [
                'month' => $monthStart,
                'label' => $monthStart->translatedFormat('M'),
                'total' => $projectsInMonth->count(),
                'started' => $projectsInMonth->filter(fn (Project $project) => filled($project->start_date) && Carbon::parse($project->start_date)->year === $selectedYear && Carbon::parse($project->start_date)->month === $month)->count(),
                'completed' => $projectsInMonth->filter(fn (Project $project) => $project->status === 'completed' && filled($project->due_date) && Carbon::parse($project->due_date)->year === $selectedYear && Carbon::parse($project->due_date)->month === $month)->count(),
                'overdue' => $projectsInMonth->filter(fn (Project $project) => $project->status !== 'completed' && filled($project->due_date) && Carbon::parse($project->due_date)->isPast())->count(),
            ];
        });

        $topCompanies = $filteredProjects
            ->groupBy(fn (Project $project) => $project->company?->name ?? 'ไม่ระบุบริษัท')
            ->map(fn ($items) => $items->count())
            ->sortDesc()
            ->take(6);

        $recentProjects = $filteredProjects
            ->sortByDesc(fn (Project $project) => $project->due_date ?? $project->start_date ?? $project->created_at)
            ->take(10)
            ->values();

        return view('reports.projects', [
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'yearStart' => $yearStart,
            'yearEnd' => $yearEnd,
            'monthStart' => $monthStart,
            'monthEnd' => $monthEnd,
            'summary' => $summary,
            'monthSummary' => $monthSummary,
            'monthProjects' => $monthProjects,
            'monthlyStats' => $monthlyStats,
            'statusBreakdown' => $statusBreakdown,
            'topCompanies' => $topCompanies,
            'recentProjects' => $recentProjects,
            'calendarProjects' => $filteredProjects,
            'calendarDays' => $calendarDays,
            'calendarEvents' => $calendarEvents,
        ]);
    }

    private function projectTouchesYear(Project $project, Carbon $yearStart, Carbon $yearEnd): bool
    {
        $start = $project->start_date ? Carbon::parse($project->start_date)->startOfDay() : null;
        $end = $project->due_date ? Carbon::parse($project->due_date)->endOfDay() : null;
        $createdAt = $project->created_at ? Carbon::parse($project->created_at)->startOfDay() : null;

        if ($start && $end) {
            return $start->lessThanOrEqualTo($yearEnd) && $end->greaterThanOrEqualTo($yearStart);
        }

        if ($start) {
            return $start->betweenIncluded($yearStart, $yearEnd);
        }

        if ($end) {
            return $end->betweenIncluded($yearStart, $yearEnd);
        }

        return $createdAt ? $createdAt->betweenIncluded($yearStart, $yearEnd) : false;
    }

    private function projectTouchesMonth(Project $project, Carbon $monthStart, Carbon $monthEnd): bool
    {
        $start = $project->start_date ? Carbon::parse($project->start_date)->startOfDay() : null;
        $end = $project->due_date ? Carbon::parse($project->due_date)->endOfDay() : null;
        $createdAt = $project->created_at ? Carbon::parse($project->created_at)->startOfDay() : null;

        if ($start && $end) {
            return $start->lessThanOrEqualTo($monthEnd) && $end->greaterThanOrEqualTo($monthStart);
        }

        if ($start) {
            return $start->betweenIncluded($monthStart, $monthEnd);
        }

        if ($end) {
            return $end->betweenIncluded($monthStart, $monthEnd);
        }

        return $createdAt ? $createdAt->betweenIncluded($monthStart, $monthEnd) : false;
    }

    private function taskTouchesYear(Task $task, Carbon $yearStart, Carbon $yearEnd): bool
    {
        $start = $task->start_date ? Carbon::parse($task->start_date)->startOfDay() : null;
        $due = $task->due_date ? Carbon::parse($task->due_date)->endOfDay() : null;
        $completedAt = $task->completed_at ? Carbon::parse($task->completed_at)->startOfDay() : null;
        $createdAt = $task->created_at ? Carbon::parse($task->created_at)->startOfDay() : null;

        if ($start && $due) {
            return $start->lessThanOrEqualTo($yearEnd) && $due->greaterThanOrEqualTo($yearStart);
        }

        if ($completedAt && $completedAt->betweenIncluded($yearStart, $yearEnd)) {
            return true;
        }

        if ($start) {
            return $start->betweenIncluded($yearStart, $yearEnd);
        }

        if ($due) {
            return $due->betweenIncluded($yearStart, $yearEnd);
        }

        return $createdAt ? $createdAt->betweenIncluded($yearStart, $yearEnd) : false;
    }
}
