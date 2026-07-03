<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - Project Calendar"
    description="Project calendar report dashboard"
    active="reports-projects"
>
    @php
        $monthNames = [
            1 => 'มกราคม',
            2 => 'กุมภาพันธ์',
            3 => 'มีนาคม',
            4 => 'เมษายน',
            5 => 'พฤษภาคม',
            6 => 'มิถุนายน',
            7 => 'กรกฎาคม',
            8 => 'สิงหาคม',
            9 => 'กันยายน',
            10 => 'ตุลาคม',
            11 => 'พฤศจิกายน',
            12 => 'ธันวาคม',
        ];

        $summaryCards = [
            ['label' => 'โครงการทั้งหมด', 'value' => number_format($monthSummary['projects'] ?? 0), 'hint' => 'เฉพาะเดือนที่เลือก', 'tone' => 'from-slate-700 to-slate-500'],
            ['label' => 'กำลังดำเนินการ', 'value' => number_format($monthSummary['active'] ?? 0), 'hint' => 'ยังเดินหน้าอยู่', 'tone' => 'from-sky-600 to-cyan-500'],
            ['label' => 'พักงาน', 'value' => number_format($monthSummary['on_hold'] ?? 0), 'hint' => 'รอการตัดสินใจต่อ', 'tone' => 'from-amber-500 to-orange-400'],
            ['label' => 'เสร็จสมบูรณ์', 'value' => number_format($monthSummary['completed'] ?? 0), 'hint' => 'ปิดงานแล้ว', 'tone' => 'from-emerald-600 to-teal-500'],
            ['label' => 'ค้างเกินกำหนด', 'value' => number_format($monthSummary['overdue'] ?? 0), 'hint' => 'ต้องเร่งติดตาม', 'tone' => 'from-rose-600 to-red-500'],
        ];

        $statusBadge = fn (string $status) => match ($status) {
            'active' => 'bg-sky-50 text-sky-700 ring-sky-200',
            'on_hold' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            default => 'bg-slate-50 text-slate-700 ring-slate-200',
        };

        $statusLabel = fn (string $status) => match ($status) {
            'active' => 'กำลังดำเนินการ',
            'on_hold' => 'พักงาน',
            'completed' => 'เสร็จสมบูรณ์',
            default => $status,
        };
    @endphp

    <div class="space-y-6">
        <section class="rounded-[2rem] border border-white/60 bg-white/75 p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] backdrop-blur xl:p-7">
            <div class="flex flex-col gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Project Calendar</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                        ปฏิทินโครงการรายเดือน
                    </h1>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                    <span class="rounded-full bg-slate-100 px-3 py-1 ring-1 ring-inset ring-slate-200">
                        {{ $monthNames[$selectedMonth] }} {{ $selectedYear }}
                    </span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 ring-1 ring-inset ring-slate-200">
                        {{ $monthStart->translatedFormat('d M Y') }} - {{ $monthEnd->translatedFormat('d M Y') }}
                    </span>
                    <span class="rounded-full bg-blue-50 px-3 py-1 font-medium text-blue-700 ring-1 ring-inset ring-blue-100">
                        {{ number_format($monthSummary['projects'] ?? 0) }} projects
                    </span>
                    <span class="rounded-full bg-violet-50 px-3 py-1 font-medium text-violet-700 ring-1 ring-inset ring-violet-100">
                        {{ number_format($monthSummary['tasks'] ?? 0) }} tasks
                    </span>
                </div>

                <form method="GET" action="{{ route('reports.projects') }}" class="grid gap-3 sm:grid-cols-[1fr_1fr_auto] max-w-3xl">
                    <select name="year" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                        @forelse ($availableYears as $year)
                            <option value="{{ $year }}" @selected((int) $selectedYear === (int) $year)>{{ $year }}</option>
                        @empty
                            <option value="{{ $selectedYear }}">{{ $selectedYear }}</option>
                        @endforelse
                    </select>

                    <select name="month" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                        @foreach ($monthNames as $monthValue => $monthLabel)
                            <option value="{{ $monthValue }}" @selected((int) $selectedMonth === (int) $monthValue)>{{ $monthLabel }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="rounded-2xl bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)] px-5 py-3 text-sm font-bold text-white shadow-[0_16px_30px_rgba(37,99,235,.22)] transition hover:brightness-105">
                        ดูเดือนนี้
                    </button>
                </form>
            </div>
        </section>

        <section class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Calendar</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950">ปฏิทินเดือน {{ $monthNames[$selectedMonth] }}</h2>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-600">
                    <span class="rounded-full bg-sky-50 px-3 py-1 text-sky-700 ring-1 ring-inset ring-sky-200">กำลังดำเนินการ</span>
                    <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-700 ring-1 ring-inset ring-amber-200">พักงาน</span>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700 ring-1 ring-inset ring-emerald-200">เสร็จสมบูรณ์</span>
                </div>
            </div>

            <div class="mt-5 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                <div class="grid grid-cols-7 gap-1 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">
                    <div class="text-center">Mo</div>
                    <div class="text-center">Tu</div>
                    <div class="text-center">We</div>
                    <div class="text-center">Th</div>
                    <div class="text-center">Fr</div>
                    <div class="text-center">Sa</div>
                    <div class="text-center">Su</div>
                </div>

                <div class="mt-2 grid grid-cols-7 gap-1">
                    @foreach ($calendarDays as $date)
                        @php
                            $inMonth = $date->month === $selectedMonth && $date->year === $selectedYear;
                            $dayEvents = $calendarEvents->filter(function ($project) use ($date) {
                                $start = \Illuminate\Support\Carbon::parse($project['display_start']);
                                $end = \Illuminate\Support\Carbon::parse($project['display_end']);

                                return $date->betweenIncluded($start, $end);
                            })->values();
                        @endphp

                        <div class="{{ $inMonth ? 'bg-white' : 'bg-slate-100/70 text-slate-400' }} min-h-[132px] rounded-2xl p-2 ring-1 ring-inset ring-slate-200">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-black">{{ $date->day }}</span>
                                @if ($dayEvents->isNotEmpty() && $inMonth)
                                    <span class="rounded-full bg-slate-900 px-2 py-0.5 text-[10px] font-bold text-white">
                                        {{ $dayEvents->count() }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-2 space-y-1.5">
                                @foreach ($dayEvents->take(3) as $event)
                                    <button
                                        type="button"
                                        class="w-full rounded-xl bg-slate-50 px-2 py-1.5 text-left text-[11px] font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-100"
                                        data-project-modal-open
                                        data-project-title="{{ $event['name'] }}"
                                        data-project-status="{{ $event['status_label'] }}"
                                        data-project-status-class="{{ $event['status_class'] }}"
                                        data-project-company="{{ $event['company'] }}"
                                        data-project-owner="{{ $event['owner'] }}"
                                        data-project-start="{{ $event['start_label'] }}"
                                        data-project-due="{{ $event['due_label'] }}"
                                        data-project-description="{{ $event['description'] }}"
                                        data-project-tasks="{{ $event['tasks_count'] }}"
                                        data-project-completed="{{ $event['completed_tasks_count'] }}"
                                        data-project-active="{{ $event['active_tasks_count'] }}"
                                        data-project-overdue="{{ $event['overdue_tasks_count'] }}"
                                        data-project-progress="{{ $event['progress'] }}"
                                        data-project-link="{{ route('projects.show', $event['id']) }}"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span class="h-2.5 w-2.5 rounded-full {{ $event['status_class'] }}"></span>
                                            <span class="min-w-0 truncate">{{ $event['name'] }}</span>
                                        </div>
                                    </button>
                                @endforeach

                                @if ($dayEvents->count() > 3)
                                    <div class="rounded-xl border border-dashed border-slate-300 px-2 py-1 text-[10px] font-semibold text-slate-400">
                                        +{{ $dayEvents->count() - 3 }} more
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Project table</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950">โครงการของเดือนที่เลือก</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        แสดงรายการโครงการที่ทับซ้อนกับช่วง {{ $monthStart->translatedFormat('d M Y') }} - {{ $monthEnd->translatedFormat('d M Y') }}
                    </p>
                </div>
                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                    {{ number_format($monthProjects->count()) }} projects
                </span>
            </div>

            <div class="mt-5 overflow-hidden rounded-[1.5rem] border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50">
                            <tr class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">
                                <th class="px-4 py-3">โครงการ</th>
                                <th class="px-4 py-3">บริษัท</th>
                                <th class="px-4 py-3">เจ้าของ</th>
                                <th class="px-4 py-3">ช่วงเวลา</th>
                                <th class="px-4 py-3">ความคืบหน้า</th>
                                <th class="px-4 py-3">สถานะ</th>
                                <th class="px-4 py-3 text-right">เปิดดู</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($monthProjects as $project)
                                @php
                                    $statusClass = $statusBadge($project->status);
                                    $statusText = $statusLabel($project->status);
                                    $tasksCount = (int) ($project->tasks_count ?? 0);
                                    $completedTasks = (int) ($project->completed_tasks_count ?? 0);
                                    $progress = $tasksCount > 0 ? (int) round(($completedTasks / max(1, $tasksCount)) * 100) : 0;
                                @endphp
                                <tr class="align-top hover:bg-slate-50/80">
                                    <td class="px-4 py-4">
                                        <button
                                            type="button"
                                            class="text-left transition hover:opacity-80"
                                            data-project-modal-open
                                            data-project-title="{{ $project->name }}"
                                            data-project-status="{{ $statusText }}"
                                            data-project-status-class="{{ $statusClass }}"
                                            data-project-company="{{ $project->company?->name ?? 'ไม่ระบุบริษัท' }}"
                                            data-project-owner="{{ $project->user?->name ?? 'ไม่ระบุเจ้าของ' }}"
                                            data-project-start="{{ optional($project->start_date)->format('d/m/Y') ?? 'ไม่ระบุ' }}"
                                            data-project-due="{{ optional($project->due_date)->format('d/m/Y') ?? 'ไม่ระบุ' }}"
                                            data-project-description="{{ $project->description ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}"
                                            data-project-tasks="{{ $tasksCount }}"
                                            data-project-completed="{{ $completedTasks }}"
                                            data-project-active="{{ (int) ($project->active_tasks_count ?? 0) }}"
                                            data-project-overdue="{{ (int) ($project->overdue_tasks_count ?? 0) }}"
                                            data-project-progress="{{ $progress }}"
                                            data-project-link="{{ route('projects.show', $project) }}"
                                        >
                                            <div class="max-w-sm">
                                                <p class="font-bold text-slate-950">{{ $project->name }}</p>
                                                <p class="mt-1 text-xs text-slate-500">
                                                    {{ $project->description ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}
                                                </p>
                                            </div>
                                        </button>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">
                                        {{ $project->company?->name ?? 'ไม่ระบุบริษัท' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">
                                        {{ $project->user?->name ?? 'ไม่ระบุเจ้าของ' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">
                                        <div class="space-y-1">
                                            <p>เริ่ม {{ optional($project->start_date)->format('d/m/Y') ?? 'ไม่ระบุ' }}</p>
                                            <p>ครบ {{ optional($project->due_date)->format('d/m/Y') ?? 'ไม่ระบุ' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="min-w-[140px]">
                                            <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                                                <span>{{ number_format($completedTasks) }}/{{ number_format($tasksCount) }}</span>
                                                <span>{{ $progress }}%</span>
                                            </div>
                                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">
                                                <div class="h-full rounded-full bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)]" style="width: {{ $progress }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <a
                                            href="{{ route('projects.show', $project) }}"
                                            class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"
                                        >
                                            เปิด
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <div class="mx-auto max-w-md">
                                            <p class="text-base font-bold text-slate-900">ไม่มีโครงการในเดือนนี้</p>
                                            <p class="mt-2 text-sm text-slate-500">
                                                ลองเปลี่ยนเดือนหรือปี แล้วระบบจะแสดงรายการโครงการที่ตรงกับช่วงเวลาที่เลือก
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <div id="project-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm">
        <div class="w-full max-w-2xl overflow-hidden rounded-[1.8rem] bg-white shadow-[0_30px_90px_rgba(15,23,42,.35)]">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Project detail</p>
                    <h3 id="project-modal-title" class="mt-1 truncate text-2xl font-black text-slate-950"></h3>
                </div>
                <button type="button" data-project-modal-close class="rounded-full bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-200">ปิด</button>
            </div>

            <div class="px-5 py-5">
                <div class="flex flex-wrap items-center gap-2">
                    <span id="project-modal-status" class="rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset"></span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">เดือน {{ $monthNames[$selectedMonth] }}</span>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-inset ring-slate-200">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">บริษัท</p>
                        <p id="project-modal-company" class="mt-1 text-sm font-bold text-slate-900"></p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-inset ring-slate-200">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">เจ้าของ</p>
                        <p id="project-modal-owner" class="mt-1 text-sm font-bold text-slate-900"></p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-inset ring-slate-200">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">เริ่ม</p>
                        <p id="project-modal-start" class="mt-1 text-sm font-bold text-slate-900"></p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-inset ring-slate-200">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ครบกำหนด</p>
                        <p id="project-modal-due" class="mt-1 text-sm font-bold text-slate-900"></p>
                    </div>
                </div>

                <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">รายละเอียด</p>
                    <p id="project-modal-description" class="mt-2 text-sm leading-6 text-slate-700"></p>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-4">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-inset ring-slate-200">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Tasks</p>
                        <p id="project-modal-tasks" class="mt-1 text-lg font-black text-slate-950"></p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-inset ring-slate-200">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Done</p>
                        <p id="project-modal-completed" class="mt-1 text-lg font-black text-slate-950"></p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-inset ring-slate-200">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Active</p>
                        <p id="project-modal-active" class="mt-1 text-lg font-black text-slate-950"></p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-inset ring-slate-200">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Overdue</p>
                        <p id="project-modal-overdue" class="mt-1 text-lg font-black text-slate-950"></p>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="flex items-center justify-between text-sm font-semibold text-slate-600">
                        <span>ความคืบหน้า</span>
                        <span id="project-modal-progress"></span>
                    </div>
                    <div class="mt-2 h-3 overflow-hidden rounded-full bg-slate-200">
                        <div id="project-modal-progress-bar" class="h-full rounded-full bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)]"></div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between gap-3 border-t border-slate-200 px-5 py-4">
                <a id="project-modal-link" href="#" class="rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90">
                    เปิดหน้าโปรเจกต์
                </a>
                <button type="button" data-project-modal-close class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    ปิด
                </button>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('project-modal');
            if (!modal) return;

            const openButtons = Array.from(document.querySelectorAll('[data-project-modal-open]'));
            const closeButtons = Array.from(document.querySelectorAll('[data-project-modal-close]'));

            const setText = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.textContent = value || '-';
            };

            const setStatus = (label, className) => {
                const el = document.getElementById('project-modal-status');
                if (!el) return;

                el.className = `rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset ${className || 'bg-slate-50 text-slate-700 ring-slate-200'}`;
                el.textContent = label || '-';
            };

            const openModal = (button) => {
                setText('project-modal-title', button.dataset.projectTitle);
                setStatus(button.dataset.projectStatus, button.dataset.projectStatusClass);
                setText('project-modal-company', button.dataset.projectCompany);
                setText('project-modal-owner', button.dataset.projectOwner);
                setText('project-modal-start', button.dataset.projectStart);
                setText('project-modal-due', button.dataset.projectDue);
                setText('project-modal-description', button.dataset.projectDescription);
                setText('project-modal-tasks', button.dataset.projectTasks);
                setText('project-modal-completed', button.dataset.projectCompleted);
                setText('project-modal-active', button.dataset.projectActive);
                setText('project-modal-overdue', button.dataset.projectOverdue);
                setText('project-modal-progress', `${button.dataset.projectProgress || 0}%`);

                const progressBar = document.getElementById('project-modal-progress-bar');
                if (progressBar) {
                    progressBar.style.width = `${button.dataset.projectProgress || 0}%`;
                }

                const link = document.getElementById('project-modal-link');
                if (link) {
                    link.href = button.dataset.projectLink || '#';
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };

            openButtons.forEach((button) => {
                button.addEventListener('click', () => openModal(button));
            });

            closeButtons.forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        })();
    </script>
</x-frontend-layout>
