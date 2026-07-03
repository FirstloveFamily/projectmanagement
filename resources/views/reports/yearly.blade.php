<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - รายงานบริษัทประจำปี"
    description="Yearly company summary dashboard"
    active="reports-yearly"
>
    @php
        $monthLabels = $monthlyLabels ?? $monthlyStats->pluck('label')->values();
        $monthStarted = $monthlyStarted ?? $monthlyStats->pluck('started')->values();
        $monthCompleted = $monthlyCompleted ?? $monthlyStats->pluck('completed')->values();
        $monthOverdue = $monthlyOverdue ?? $monthlyStats->pluck('overdue')->values();

        $companyLabels = $companyLabels ?? $topCompanies->pluck('company')->values();
        $companyProjects = $companyProjects ?? $topCompanies->pluck('projects')->values();
        $companyProgress = $companyProgress ?? $topCompanies->pluck('progress')->values();
        $companyLinks = $companyLinks ?? collect();

        $statusLabels = [
            'active' => 'กำลังดำเนินการ',
            'on_hold' => 'พักงาน',
            'completed' => 'เสร็จสมบูรณ์',
        ];

        $statusStyles = [
            'active' => 'bg-sky-50 text-sky-700 ring-sky-200',
            'on_hold' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        ];

        $summaryCards = [
            ['label' => 'บริษัท', 'value' => number_format($summary['companies'] ?? 0), 'hint' => 'บริษัทที่มีโครงการในปีนี้', 'tone' => 'from-slate-700 to-slate-500'],
            ['label' => 'โครงการ', 'value' => number_format($summary['projects'] ?? 0), 'hint' => 'โครงการทั้งหมดในปีที่เลือก', 'tone' => 'from-sky-600 to-cyan-500'],
            ['label' => 'งานทั้งหมด', 'value' => number_format($summary['tasks'] ?? 0), 'hint' => 'งานรวมในทุกบริษัท', 'tone' => 'from-violet-600 to-fuchsia-500'],
            ['label' => 'งานเสร็จ', 'value' => number_format($summary['done_tasks'] ?? 0), 'hint' => 'งานที่ปิดจบแล้ว', 'tone' => 'from-emerald-600 to-teal-500'],
            ['label' => 'ค้างเกินกำหนด', 'value' => number_format($summary['overdue'] ?? 0), 'hint' => 'โครงการที่ต้องเร่งติดตาม', 'tone' => 'from-rose-600 to-red-500'],
        ];

        $companyMax = max(1, (int) ($topCompanies->max('projects') ?? 1));
        $overallProgress = (int) ($summary['task_progress'] ?? 0);
    @endphp

    <div class="space-y-6">
        <section class="overflow-hidden rounded-[2rem] border border-white/60 bg-white/75 p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] backdrop-blur xl:p-7">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-4xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Yearly Company Report</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                        สรุปภาพรวมทั้งปีของแต่ละบริษัท
                    </h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 sm:text-base">
                        ใช้ดูว่าบริษัทไหนมีโครงการกี่รายการ งานค้างมากน้อยแค่ไหน และภาพรวมการปิดงานตลอดปีที่เลือก
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2 text-sm text-slate-500">
                        <span class="rounded-full bg-slate-100 px-3 py-1 ring-1 ring-inset ring-slate-200">
                            ปี {{ $selectedYear }}
                        </span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 ring-1 ring-inset ring-slate-200">
                            ช่วงข้อมูล {{ $yearStart->translatedFormat('d M Y') }} - {{ $yearEnd->translatedFormat('d M Y') }}
                        </span>
                        <span class="rounded-full bg-blue-50 px-3 py-1 font-medium text-blue-700 ring-1 ring-inset ring-blue-100">
                            {{ number_format($summary['projects'] ?? 0) }} โครงการ
                        </span>
                        <span class="rounded-full bg-violet-50 px-3 py-1 font-medium text-violet-700 ring-1 ring-inset ring-violet-100">
                            {{ number_format($summary['tasks'] ?? 0) }} งาน
                        </span>
                    </div>
                </div>

                <div class="w-full max-w-2xl rounded-[1.7rem] border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">เลือกปี</p>
                            <p class="mt-1 text-lg font-black text-slate-950">สลับดูข้อมูลย้อนหลัง</p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('reports.index') }}" class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Gantt
                            </a>
                            <a href="{{ route('reports.projects') }}" class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Calendar
                            </a>
                            <a href="{{ route('reports.team-yearly') }}" class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Team
                            </a>
                            <a href="{{ route('reports.deadlines') }}" class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Deadline Alerts
                            </a>
                            <a href="{{ route('reports.trainings') }}" class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Trainings
                            </a>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('reports.yearly') }}" class="mt-4 flex flex-col gap-3 sm:flex-row">
                        <select name="year" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                            @forelse ($availableYears as $year)
                                <option value="{{ $year }}" @selected((int) $selectedYear === (int) $year)>{{ $year }}</option>
                            @empty
                                <option value="{{ $selectedYear }}">{{ $selectedYear }}</option>
                            @endforelse
                        </select>
                        <button type="submit" class="rounded-2xl bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)] px-5 py-3 text-sm font-bold text-white shadow-[0_16px_30px_rgba(37,99,235,.22)] transition hover:brightness-105">
                            ดูภาพรวม
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ($summaryCards as $card)
                <div class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-slate-500">{{ $card['label'] }}</p>
                        <span class="h-2.5 w-2.5 rounded-full bg-gradient-to-r {{ $card['tone'] }}"></span>
                    </div>
                    <div class="mt-3 text-3xl font-black text-slate-950">{{ $card['value'] }}</div>
                    <p class="mt-2 text-sm text-slate-500">{{ $card['hint'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(340px,0.9fr)]">
            <div class="space-y-6">
                <div class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Yearly trend</p>
                            <h2 class="mt-2 text-2xl font-black text-slate-950">แนวโน้มโครงการรายเดือน</h2>
                        </div>
                        <div class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                            รวมทั้งปี
                        </div>
                    </div>

                    <div class="mt-5 h-[340px]">
                        <canvas
                            data-yearly-chart
                            data-chart-type="line"
                            data-chart-labels='@json($monthLabels)'
                            data-chart-started='@json($monthStarted)'
                            data-chart-completed='@json($monthCompleted)'
                            data-chart-overdue='@json($monthOverdue)'
                            aria-label="กราฟเส้นแนวโน้มโครงการรายเดือน"
                            role="img"
                        ></canvas>
                    </div>
                </div>

                <div class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Company share</p>
                            <h2 class="mt-2 text-2xl font-black text-slate-950">บริษัทที่มีโครงการมากสุด</h2>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                            Top {{ $topCompanies->count() }}
                        </span>
                    </div>

                    <div class="mt-5 h-[320px]">
                        <canvas
                            data-yearly-chart
                            data-chart-type="bar"
                            data-chart-labels='@json($companyLabels)'
                            data-chart-values='@json($companyProjects)'
                            data-chart-links='@json($companyLinks)'
                            aria-label="กราฟแท่งจำนวนโครงการของบริษัทที่มากสุด"
                            role="img"
                        ></canvas>
                    </div>
                    <p class="mt-3 text-sm text-slate-500">
                        คลิกแท่งกราฟหรือชื่อบริษัทในตารางด้านล่างเพื่อเปิดหน้ารายละเอียดรายปีของบริษัทนั้น
                    </p>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Status mix</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950">สถานะโครงการในปีนี้</h2>

                    <div class="mt-5 h-[240px]">
                        <canvas
                            data-yearly-chart
                            data-chart-type="doughnut"
                            data-chart-labels='@json($statusBreakdown->pluck("label")->values())'
                            data-chart-values='@json($statusBreakdown->pluck("count")->values())'
                            aria-label="กราฟโดนัทสถานะโครงการ"
                            role="img"
                        ></canvas>
                    </div>

                    <div class="mt-4 space-y-3">
                        @foreach ($statusBreakdown as $row)
                            <div class="flex items-center justify-between gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="h-2.5 w-2.5 rounded-full bg-gradient-to-r {{ $row['class'] }}"></span>
                                    <span class="text-sm font-semibold text-slate-700">{{ $row['label'] }}</span>
                                </div>
                                <span class="text-sm font-black text-slate-950">{{ number_format($row['count']) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Progress</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950">ความคืบหน้างานรวม</h2>
                    <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-4">
                        <div class="flex items-center justify-between text-sm font-semibold text-slate-500">
                            <span>งานปิดแล้ว</span>
                            <span>{{ $overallProgress }}%</span>
                        </div>
                        <div class="mt-2 h-3 overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-600 to-teal-500" style="width: {{ $overallProgress }}%"></div>
                        </div>
                        <p class="mt-3 text-sm text-slate-500">
                            งานเสร็จแล้ว {{ number_format($summary['done_tasks'] ?? 0) }} จาก {{ number_format($summary['tasks'] ?? 0) }} งาน
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Company table</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">สรุปตามบริษัท</h2>
                    </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                    {{ number_format($companySummaries->count()) }} บริษัท
                </span>
            </div>

            <div class="mt-5 overflow-hidden rounded-[1.5rem] border border-slate-200">
                <div class="divide-y divide-slate-100">
                    @forelse ($companySummaries as $row)
                        <div class="grid gap-4 px-4 py-4 xl:grid-cols-[minmax(0,2fr)_repeat(6,minmax(0,1fr))] xl:items-center">
                            <div class="min-w-0">
                                @if (!empty($row['company_id']))
                                    <a
                                        href="{{ route('reports.yearly.company', ['company' => $row['company_id'], 'year' => $selectedYear]) }}"
                                        class="truncate text-base font-black text-slate-950 transition hover:text-sky-700"
                                    >
                                        {{ $row['company'] }}
                                    </a>
                                @else
                                    <h3 class="truncate text-base font-black text-slate-950">{{ $row['company'] }}</h3>
                                @endif
                                <p class="mt-1 text-sm text-slate-500">
                                    โครงการล่าสุด: {{ $row['latest_project'] ?? 'ไม่ระบุ' }}
                                    @if ($row['latest_due'])
                                        · ครบกำหนด {{ $row['latest_due'] }}
                                    @endif
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 px-3 py-3 text-center">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Projects</p>
                                <p class="mt-1 text-lg font-black text-slate-950">{{ number_format($row['projects']) }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-3 py-3 text-center">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Completed</p>
                                <p class="mt-1 text-lg font-black text-emerald-700">{{ number_format($row['completed_projects']) }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-3 py-3 text-center">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Active</p>
                                <p class="mt-1 text-lg font-black text-sky-700">{{ number_format($row['active_projects']) }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-3 py-3 text-center">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">On Hold</p>
                                <p class="mt-1 text-lg font-black text-amber-700">{{ number_format($row['on_hold_projects']) }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-3 py-3 text-center">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Overdue</p>
                                <p class="mt-1 text-lg font-black text-rose-700">{{ number_format($row['overdue']) }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-3 py-3 text-center">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Progress</p>
                                <p class="mt-1 text-lg font-black text-slate-950">{{ $row['progress'] }}%</p>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-sm text-slate-500">
                            ยังไม่มีบริษัทที่มีโครงการในปีนี้
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Recent projects</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950">โครงการสำคัญในปีนี้</h2>
                </div>
                <a href="{{ route('projects.index') }}" class="rounded-full bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                    ไปหน้า Projects
                </a>
            </div>

            <div class="mt-5 overflow-hidden rounded-[1.6rem] border border-slate-200">
                <div class="divide-y divide-slate-100">
                    @forelse ($recentProjects as $project)
                        @php
                            $statusLabel = $statusLabels[$project->status] ?? $project->status;
                            $statusClass = $statusStyles[$project->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200';
                        @endphp
                        <div class="flex flex-col gap-4 px-4 py-4 md:flex-row md:items-center md:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-base font-black text-slate-950">{{ $project->name }}</h3>
                                    <span class="rounded-full {{ $statusClass }} px-3 py-1 text-xs font-bold ring-1 ring-inset">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                                <p class="mt-2 text-sm text-slate-500">
                                    {{ $project->company?->name ?? 'ไม่ระบุบริษัท' }} · {{ $project->user?->name ?? 'ไม่ระบุเจ้าของ' }}
                                </p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    {{ $project->description ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}
                                </p>
                            </div>

                            <div class="flex shrink-0 items-center gap-2">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-right ring-1 ring-inset ring-slate-200">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ช่วงเวลา</p>
                                    <p class="mt-1 text-sm font-bold text-slate-900">
                                        {{ optional($project->start_date)->format('d M') ?? 'ยังไม่ระบุ' }} - {{ optional($project->due_date)->format('d M Y') ?? 'ไม่ระบุ' }}
                                    </p>
                                </div>
                                <a href="{{ route('projects.show', $project) }}" class="rounded-2xl bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)] px-4 py-3 text-sm font-bold text-white shadow-[0_16px_30px_rgba(37,99,235,.22)] transition hover:brightness-105">
                                    เปิดโปรเจกต์
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-sm text-slate-500">
                            ยังไม่มีโครงการให้แสดงในปีนี้
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-frontend-layout>
