<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - Project Report"
    description="Project report dashboard"
    active="reports"
>
    @php
        $summaryCards = [
            ['label' => 'Projects', 'value' => number_format($summary['projects'] ?? 0), 'hint' => 'โครงการทั้งหมดในระบบ', 'tone' => 'from-slate-700 to-slate-500'],
            ['label' => 'Tasks', 'value' => number_format($summary['tasks'] ?? 0), 'hint' => 'งานในโปรเจกต์ที่เลือก', 'tone' => 'from-sky-600 to-cyan-500'],
            ['label' => 'Done', 'value' => number_format($summary['done'] ?? 0), 'hint' => 'งานที่เสร็จแล้ว', 'tone' => 'from-emerald-600 to-teal-500'],
            ['label' => 'Overdue', 'value' => number_format($summary['overdue'] ?? 0), 'hint' => 'งานค้างเกินกำหนด', 'tone' => 'from-rose-600 to-red-500'],
        ];

        $monthGroups = $timelineDays->groupBy(fn ($date) => $date->format('Y-m'));

        $taskStatusLabel = fn (string $status) => match ($status) {
            'todo' => 'ยังไม่เริ่ม',
            'in_progress' => 'กำลังทำ',
            'done' => 'เสร็จแล้ว',
            default => $status,
        };

        $taskProgress = fn (string $status) => match ($status) {
            'todo' => 15,
            'in_progress' => 60,
            'done' => 100,
            default => 30,
        };

        $taskTone = fn (string $status) => match ($status) {
            'todo' => ['bg' => 'bg-amber-500', 'soft' => 'bg-amber-100', 'text' => 'text-amber-700'],
            'in_progress' => ['bg' => 'bg-sky-500', 'soft' => 'bg-sky-100', 'text' => 'text-sky-700'],
            'done' => ['bg' => 'bg-emerald-500', 'soft' => 'bg-emerald-100', 'text' => 'text-emerald-700'],
            default => ['bg' => 'bg-slate-500', 'soft' => 'bg-slate-100', 'text' => 'text-slate-700'],
        };

        $totalDays = $timelineDays->count();
    @endphp
    <div class="rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] backdrop-blur xl:p-7">
                        <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                            <div class="max-w-4xl">
                                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Project Report</p>
                                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                                    {{ $selectedProject?->name ?? 'ไม่มีข้อมูลโปรเจกต์' }}
                                </h1>
                                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 sm:text-base">
                                    {{ $selectedProject ? ($selectedProject->description ?: 'ไม่มีรายละเอียดเพิ่มเติม') : 'ยังไม่มีโปรเจกต์สำหรับแสดงรายงาน' }}
                                </p>

                                @if ($selectedProject)
                                    <div class="mt-4 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 ring-1 ring-inset ring-slate-200">
                                            {{ $selectedProject->company?->name ?? 'ไม่ระบุบริษัท' }}
                                        </span>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 ring-1 ring-inset ring-slate-200">
                                            {{ $selectedProject->user?->name ?? 'ไม่ระบุเจ้าของ' }}
                                        </span>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 ring-1 ring-inset ring-slate-200">
                                            {{ optional($rangeStart)->format('d M Y') }} - {{ optional($rangeEnd)->format('d M Y') }}
                                        </span>
                                        <span class="rounded-full bg-blue-50 px-3 py-1 font-medium text-blue-700 ring-1 ring-inset ring-blue-100">
                                            {{ $selectedProject->tasks_count }} tasks
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <form method="GET" action="{{ route('reports.index') }}" class="w-full max-w-xl">
                                <label class="mb-2 block text-sm font-semibold text-slate-600">เลือกโปรเจกต์</label>
                                <div class="flex gap-3">
                                    <select name="project_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                                        @if ($projects->isEmpty())
                                            <option value="">ยังไม่มีโปรเจกต์</option>
                                        @endif
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}" @selected($selectedProject?->id === $project->id)>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                                        ดู
                                    </button>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <a href="{{ route('reports.projects') }}" class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                                        Project Report
                                    </a>
                                    <a href="{{ route('reports.yearly') }}" class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                                        ภาพรวมรายปี
                                    </a>
                                    <a href="{{ route('reports.deadlines') }}" class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                                        แจ้งเตือนกำหนด
                                    </a>
                                    <a href="{{ route('reports.trainings') }}" class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                                        รายงานอบรม
                                    </a>
                                    <a href="{{ route('reports.team-yearly') }}" class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                                        รายงานทีม
                                    </a>
                                </div>
                            </form>
                        </div>

                        <div class="mt-6 flex items-center gap-2 border-b border-slate-200">
                            <a href="{{ $selectedProject ? route('projects.show', $selectedProject) : route('projects.index') }}"
                                class="px-3 py-2 text-sm font-semibold text-slate-500 hover:text-slate-900">
                                Kanban
                            </a>
                            <span class="border-b-2 border-blue-600 px-3 py-2 text-sm font-semibold text-slate-900">
                                Gantt
                            </span>
                        </div>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach ($summaryCards as $card)
                                <div class="rounded-[1.4rem] border border-slate-200 bg-white p-4 shadow-sm">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-sm font-semibold text-slate-500">{{ $card['label'] }}</p>
                                        <span class="h-2.5 w-2.5 rounded-full bg-gradient-to-r {{ $card['tone'] }}"></span>
                                    </div>
                                    <div class="mt-3 text-3xl font-black text-slate-950">{{ $card['value'] }}</div>
                                    <p class="mt-2 text-sm text-slate-500">{{ $card['hint'] }}</p>
                                </div>
                            @endforeach
                        </div>

                        @if ($selectedProject)
                            <div class="mt-6 overflow-hidden rounded-[1.6rem] border border-slate-200 bg-white shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                                <div class="overflow-x-auto">
                                    <div class="min-w-[1200px]">
                                        <div class="grid border-b border-slate-200 bg-slate-950 text-white"
                                            style="grid-template-columns: 280px repeat({{ $totalDays }}, minmax(34px, 1fr));">
                                            <div class="border-r border-white/10 px-4 py-3 text-xs font-bold uppercase tracking-[0.24em] text-slate-300">
                                                Task
                                            </div>
                                            @foreach ($monthGroups as $monthKey => $days)
                                                <div class="border-r border-white/10 px-3 py-3 text-center text-xs font-bold uppercase tracking-[0.28em] text-sky-100"
                                                    style="grid-column: span {{ $days->count() }};">
                                                    {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $monthKey)->translatedFormat('M Y') }}
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="grid border-b border-slate-200 bg-slate-50"
                                            style="grid-template-columns: 280px repeat({{ $totalDays }}, minmax(34px, 1fr));">
                                            <div class="border-r border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">
                                                รายการ
                                            </div>
                                            @foreach ($timelineDays as $day)
                                                <div class="border-r border-slate-200 px-1 py-2 text-center text-[11px] font-semibold {{ $day->isWeekend() ? 'bg-slate-100 text-slate-500' : 'text-slate-500' }}">
                                                    {{ $day->format('j') }}
                                                </div>
                                            @endforeach
                                        </div>

                                        @forelse ($taskRows as $row)
                                            @php
                                                $task = $row['task'];
                                                $tone = $taskTone($task->status);
                                                $progress = $taskProgress($task->status);
                                                $duration = $row['span'];
                                                $offset = $row['offset'];
                                                $barClass = match ($task->status) {
                                                    'todo' => 'bg-amber-300/30 text-amber-900',
                                                    'in_progress' => 'bg-sky-300/30 text-sky-900',
                                                    'done' => 'bg-emerald-300/30 text-emerald-900',
                                                    default => 'bg-slate-300/30 text-slate-900',
                                                };
                                            @endphp
                                            <div class="grid border-b border-slate-100"
                                                style="grid-template-columns: 280px repeat({{ $totalDays }}, minmax(34px, 1fr));">
                                                <div class="border-r border-slate-100 px-4 py-2.5">
                                                    <div class="flex items-start gap-3">
                                                        <div class="mt-1 h-3 w-3 rounded-full {{ $tone['bg'] }}"></div>
                                                        <div class="min-w-0">
                                                            <p class="truncate text-sm font-bold text-slate-950">{{ $task->title }}</p>
                                                            <p class="mt-1 text-xs text-slate-500">
                                                                {{ \App\Models\Task::taskCategoryLabel($task->task_category) }} · {{ optional($task->user)->name ?? 'ไม่ระบุ' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="relative px-2 py-2" style="grid-column: span {{ $totalDays }};">
                                                    <div class="relative grid h-9 overflow-hidden rounded-xl"
                                                        style="grid-template-columns: repeat({{ $totalDays }}, minmax(34px, 1fr));">
                                                        @foreach ($timelineDays as $day)
                                                            <div class="border-r border-slate-100 {{ $day->isWeekend() ? 'bg-slate-50' : 'bg-white' }}"></div>
                                                        @endforeach

                                                        <div class="pointer-events-none absolute inset-0 grid"
                                                            style="grid-template-columns: repeat({{ $totalDays }}, minmax(34px, 1fr));">
                                                            <div class="flex h-6 items-center gap-2 rounded-full px-2 {{ $barClass }}"
                                                                style="grid-column: {{ $offset }} / span {{ $duration }};">
                                                                <div class="flex h-4.5 flex-1 items-center rounded-full bg-white/70">
                                                                    <div class="h-4.5 rounded-full {{ $tone['bg'] }}" style="width: {{ $progress }}%"></div>
                                                                </div>
                                                                <span class="shrink-0 text-[11px] font-bold">{{ $progress }}%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="px-6 py-12 text-center text-slate-500">
                                                ยังไม่มีงานในโปรเจกต์นี้
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-6 rounded-[1.6rem] border border-dashed border-slate-300 bg-white px-6 py-12 text-center text-slate-500">
                                ยังไม่มีข้อมูลโปรเจกต์สำหรับสร้างรายงาน
                            </div>
                        @endif
    </div>
</x-frontend-layout>
