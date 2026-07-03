<x-frontend-layout title="{{ config('app.name', 'Laravel') }} - รายงานผลงานทีมรายปี"
    description="Yearly team performance dashboard" active="reports-team-yearly">
    @php
        $statusStyles = [
            'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'pending' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'suspended' => 'bg-rose-50 text-rose-700 ring-rose-200',
        ];

        $summaryCards = [
            [
                'label' => 'สมาชิกที่มีผลงาน',
                'value' => number_format($summary['members'] ?? 0),
                'hint' => 'คนที่มีงานหรือโครงการในปีนี้',
                'tone' => 'from-slate-700 to-slate-500',
            ],
            [
                'label' => 'งานทั้งหมด',
                'value' => number_format($summary['tasks_total'] ?? 0),
                'hint' => 'งานที่ถูกนับในปีที่เลือก',
                'tone' => 'from-sky-600 to-cyan-500',
            ],
            [
                'label' => 'งานเสร็จ',
                'value' => number_format($summary['tasks_completed'] ?? 0),
                'hint' => 'งานที่ปิดจบแล้ว',
                'tone' => 'from-emerald-600 to-teal-500',
            ],
            [
                'label' => 'โครงการที่ดูแล',
                'value' => number_format($summary['projects_total'] ?? 0),
                'hint' => 'โครงการที่ผูกกับทีมในปีนี้',
                'tone' => 'from-violet-600 to-fuchsia-500',
            ],
            [
                'label' => 'งานค้าง',
                'value' => number_format($summary['tasks_overdue'] ?? 0),
                'hint' => 'งานที่เกินกำหนด',
                'tone' => 'from-rose-600 to-red-500',
            ],
        ];

        $topPerformerNames = $topPerformers->pluck('name')->values();
        $topPerformerCounts = $topPerformers->pluck('tasks_completed')->values();
        $topPerformerMax = max(1, (int) ($topPerformers->max('tasks_completed') ?? 1));
    @endphp

    <div class="space-y-6">
        <section
            class="overflow-hidden rounded-[2rem] border border-white/60 bg-white/75 p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] backdrop-blur xl:p-7">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-4xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Yearly Team Report</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                        รายงานผลงานทีมรายปี
                    </h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 sm:text-base">
                        ดูผลงานของทีมแต่ละคนในปีที่เลือก ทั้งงานที่ทำเสร็จ งานค้าง โครงการที่รับผิดชอบ
                        และความเคลื่อนไหวล่าสุด
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2 text-sm text-slate-500">
                        <span class="rounded-full bg-slate-100 px-3 py-1 ring-1 ring-inset ring-slate-200">
                            ปี {{ $selectedYear }}
                        </span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 ring-1 ring-inset ring-slate-200">
                            ช่วงข้อมูล {{ $yearStart->translatedFormat('d M Y') }} -
                            {{ $yearEnd->translatedFormat('d M Y') }}
                        </span>
                        <span
                            class="rounded-full bg-blue-50 px-3 py-1 font-medium text-blue-700 ring-1 ring-inset ring-blue-100">
                            {{ number_format($summary['tasks_completed'] ?? 0) }} งานเสร็จ
                        </span>
                        <span
                            class="rounded-full bg-violet-50 px-3 py-1 font-medium text-violet-700 ring-1 ring-inset ring-violet-100">
                            {{ number_format($summary['projects_total'] ?? 0) }} โครงการ
                        </span>
                    </div>
                </div>

                <div class="w-full max-w-2xl rounded-[1.7rem] border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">เลือกปี</p>
                            <p class="mt-1 text-lg font-black text-slate-950">สลับดูผลงานย้อนหลัง</p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('reports.team-yearly.export-xlsx', ['year' => $selectedYear]) }}"
                                class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-4 py-2 text-sm font-semibold text-sky-700 transition hover:bg-sky-100">
                                Export Excel
                            </a>
                            <a href="{{ route('reports.team-yearly.export', ['year' => $selectedYear]) }}"
                                class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                Export CSV
                            </a>
                            <a href="{{ route('reports.projects') }}"
                                class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Project Report
                            </a>
                            <a href="{{ route('reports.deadlines') }}"
                                class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Deadline Alerts
                            </a>
                            <a href="{{ route('reports.yearly') }}"
                                class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Company Report
                            </a>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('reports.team-yearly') }}"
                        class="mt-4 flex flex-col gap-3 sm:flex-row">
                        <select name="year"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                            @forelse ($availableYears as $year)
                                <option value="{{ $year }}" @selected((int) $selectedYear === (int) $year)>{{ $year }}
                                </option>
                            @empty
                                <option value="{{ $selectedYear }}">{{ $selectedYear }}</option>
                            @endforelse
                        </select>
                        <button type="submit"
                            class="rounded-2xl bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)] px-5 py-3 text-sm font-bold text-white shadow-[0_16px_30px_rgba(37,99,235,.22)] transition hover:brightness-105">
                            ดูรายงาน
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ($summaryCards as $card)
                <div
                    class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-slate-500">{{ $card['label'] }}</p>
                        <span class="h-2.5 w-2.5 rounded-full bg-gradient-to-r {{ $card['tone'] }}"></span>
                    </div>
                    <div class="mt-3 text-3xl font-black text-slate-950">{{ $card['value'] }}</div>
                    <p class="mt-2 text-sm text-slate-500">{{ $card['hint'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.75fr)]">
            <div class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Top performers</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">ทีมงาน IT DEVLOPER CENTER</h2>
                    </div>
                    <span
                        class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                        คลิกกราฟเพื่อเลื่อนไปที่ตาราง
                    </span>
                </div>

                <div class="mt-5 h-[340px]">
                    <canvas data-yearly-chart data-chart-type="bar" data-chart-labels='@json($topPerformerNames)'
                        data-chart-values='@json($topPerformerCounts)' data-chart-links='@json($topPerformerLinks)'
                        aria-label="กราฟผลงานทีมที่ทำงานเสร็จมากที่สุด" role="img"></canvas>
                </div>
            </div>

            <div class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Performance snapshot</p>
                <h2 class="mt-2 text-2xl font-black text-slate-950">ภาพรวมเร็ว</h2>

                <div class="mt-4 space-y-3">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <div class="flex items-center justify-between gap-3 text-sm font-semibold text-slate-500">
                            <span>อัตรางานเสร็จรวม</span>
                            <span>{{ number_format($summary['completion_rate'] ?? 0) }}%</span>
                        </div>
                        <div class="mt-2 h-3 overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-600 to-teal-500"
                                style="width: {{ (int) ($summary['completion_rate'] ?? 0) }}%"></div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <p class="text-sm font-semibold text-slate-500">ทีมที่มีผลงาน</p>
                        <p class="mt-1 text-2xl font-black text-slate-950">
                            {{ number_format($summary['members'] ?? 0) }} คน</p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <p class="text-sm font-semibold text-slate-500">งานค้าง</p>
                        <p class="mt-1 text-2xl font-black text-slate-950">
                            {{ number_format($summary['tasks_overdue'] ?? 0) }} งาน</p>
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    @foreach ($topPerformers->take(5) as $row)
                        <a href="#team-{{ $row['id'] }}"
                            class="flex items-center justify-between gap-3 rounded-2xl bg-slate-50 px-4 py-3 transition hover:bg-slate-100">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-slate-950">{{ $row['name'] }}</p>
                                <p class="text-xs text-slate-500">{{ $row['role'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-slate-950">
                                    {{ number_format($row['tasks_completed']) }}</p>
                                <p class="text-xs text-slate-500">งานเสร็จ</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Team table</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950">ตารางรายงานกิจกรรมของทีม IT DEVELOPER</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        ตารางนี้นับงานและโครงการของแต่ละคนที่เกิดขึ้นในช่วงปี {{ $selectedYear }}
                    </p>
                </div>
                <span
                    class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                    {{ number_format($teamRows->count()) }} คน
                </span>
            </div>

            <div class="mt-5 overflow-hidden rounded-[1.5rem] border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50">
                            <tr class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">
                                <th class="px-4 py-3">ชื่อ</th>
                                <th class="px-4 py-3">บทบาท</th>
                                <th class="px-4 py-3">สถานะ</th>
                                <th class="px-4 py-3">งานทั้งหมด</th>
                                <th class="px-4 py-3">งานเสร็จ</th>
                                <th class="px-4 py-3">งานกำลังทำ</th>
                                <th class="px-4 py-3">งานค้าง</th>
                                <th class="px-4 py-3">โครงการ</th>
                                <th class="px-4 py-3">อัตราสำเร็จ</th>
                                <th class="px-4 py-3">ล่าสุด</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($teamRows as $row)
                                @php
                                    $statusClass =
                                        $statusStyles[$row['status_key']] ??
                                        'bg-slate-50 text-slate-700 ring-slate-200';
                                @endphp
                                <tr id="team-{{ $row['id'] }}" class="hover:bg-slate-50/80">
                                    <td class="px-4 py-4">
                                        <p class="font-bold text-slate-950">{{ $row['name'] }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">
                                        {{ $row['role'] }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <span
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $statusClass }}">
                                            {{ $row['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm font-semibold text-slate-700">
                                        {{ number_format($row['tasks_total']) }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-semibold text-emerald-700">
                                        {{ number_format($row['tasks_completed']) }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-semibold text-sky-700">
                                        {{ number_format($row['tasks_in_progress']) }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-semibold text-rose-700">
                                        {{ number_format($row['tasks_overdue']) }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700">
                                        <div class="space-y-1">
                                            <p>ทั้งหมด {{ number_format($row['projects_total']) }}</p>
                                            <p class="text-xs text-slate-500">
                                                เสร็จ {{ number_format($row['projects_completed']) }} · กำลังทำ
                                                {{ number_format($row['projects_active']) }} · พักงาน
                                                {{ number_format($row['projects_on_hold']) }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="min-w-[140px]">
                                            <div
                                                class="flex items-center justify-between text-xs font-semibold text-slate-500">
                                                <span>{{ $row['completion_rate'] }}%</span>
                                            </div>
                                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">
                                                <div class="h-full rounded-full bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)]"
                                                    style="width: {{ $row['completion_rate'] }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">
                                        {{ $row['latest_activity'] ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-12 text-center">
                                        <div class="mx-auto max-w-md">
                                            <p class="text-base font-bold text-slate-900">ยังไม่มีข้อมูลผลงานในปีนี้
                                            </p>
                                            <p class="mt-2 text-sm text-slate-500">
                                                ลองเปลี่ยนปี หรือรอให้มีงานและโครงการถูกผูกกับทีมก่อน
                                                ระบบจะแสดงผลในตารางนี้
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
</x-frontend-layout>
