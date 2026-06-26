<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - รายงานการอบรมรายปี"
    description="Yearly training report dashboard"
    active="reports-trainings"
>
    @php
        $summaryCards = [
            ['label' => 'รอบอบรมทั้งหมด', 'value' => number_format($summary['total'] ?? 0), 'hint' => 'ในปีที่เลือก', 'tone' => 'from-slate-700 to-slate-500'],
            ['label' => 'กำหนดแล้ว', 'value' => number_format($summary['scheduled'] ?? 0), 'hint' => 'รอถึงวันอบรม', 'tone' => 'from-sky-600 to-cyan-500'],
            ['label' => 'กำลังอบรม', 'value' => number_format($summary['running'] ?? 0), 'hint' => 'รอบที่กำลังดำเนินการ', 'tone' => 'from-amber-500 to-orange-400'],
            ['label' => 'เสร็จสิ้น', 'value' => number_format($summary['completed'] ?? 0), 'hint' => 'รอบที่ปิดงานแล้ว', 'tone' => 'from-emerald-600 to-teal-500'],
            ['label' => 'ยกเลิก', 'value' => number_format($summary['cancelled'] ?? 0), 'hint' => 'รอบที่ยกเลิก', 'tone' => 'from-rose-600 to-red-500'],
        ];

        $monthLabels = $monthlyStats->pluck('label')->values();
        $monthTotals = $monthlyStats->pluck('total')->values();
        $monthAttendance = $monthlyStats->pluck('attendance')->values();
        $topMonths = $monthlyStats->sortByDesc('total')->take(6);

        $statusLabels = [
            'scheduled' => 'กำหนดแล้ว',
            'in_progress' => 'กำลังอบรม',
            'completed' => 'เสร็จสิ้น',
            'cancelled' => 'ยกเลิก',
        ];

        $statusStyles = [
            'scheduled' => 'bg-sky-50 text-sky-700 ring-sky-200',
            'in_progress' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'cancelled' => 'bg-rose-50 text-rose-700 ring-rose-200',
        ];
    @endphp

    <div class="space-y-6">
        <section class="overflow-hidden rounded-[2rem] border border-white/60 bg-white/75 p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] backdrop-blur xl:p-7">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-4xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Training Report</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                        รายงานการอบรมรายปี
                    </h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 sm:text-base">
                        สรุปรอบอบรมของปีที่เลือก ทั้งจำนวนรอบ สถานะการอบรม อัตราเข้าร่วม และรายการอบรมล่าสุด
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2 text-sm text-slate-500">
                        <span class="rounded-full bg-slate-100 px-3 py-1 ring-1 ring-inset ring-slate-200">
                            ปี {{ $selectedYear }}
                        </span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 ring-1 ring-inset ring-slate-200">
                            ช่วงข้อมูล {{ $yearStart->translatedFormat('d M Y') }} - {{ $yearEnd->translatedFormat('d M Y') }}
                        </span>
                        <span class="rounded-full bg-blue-50 px-3 py-1 font-medium text-blue-700 ring-1 ring-inset ring-blue-100">
                            {{ number_format($summary['attended'] ?? 0) }} คนเข้าร่วม
                        </span>
                        <span class="rounded-full bg-violet-50 px-3 py-1 font-medium text-violet-700 ring-1 ring-inset ring-violet-100">
                            {{ number_format($summary['capacity'] ?? 0) }} ที่นั่ง
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
                            <a href="{{ route('trainings.index') }}" class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Manage Trainings
                            </a>
                            <a href="{{ route('reports.yearly') }}" class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Company Report
                            </a>
                            <a href="{{ route('reports.deadlines') }}" class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Deadline Alerts
                            </a>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('reports.trainings') }}" class="mt-4 flex flex-col gap-3 sm:flex-row">
                        <select name="year" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100">
                            @forelse ($availableYears as $year)
                                <option value="{{ $year }}" @selected((int) $selectedYear === (int) $year)>{{ $year }}</option>
                            @empty
                                <option value="{{ $selectedYear }}">{{ $selectedYear }}</option>
                            @endforelse
                        </select>
                        <button type="submit" class="rounded-2xl bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)] px-5 py-3 text-sm font-bold text-white shadow-[0_16px_30px_rgba(37,99,235,.22)] transition hover:brightness-105">
                            ดูรายงาน
                        </button>
                        <button type="submit" formaction="{{ route('reports.trainings.export-xlsx') }}" formmethod="GET" class="inline-flex items-center justify-center rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100">
                            Export Excel
                        </button>
                        <button type="submit" formaction="{{ route('reports.trainings.export') }}" formmethod="GET" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-sky-50 px-5 py-3 text-sm font-bold text-sky-700 transition hover:bg-sky-100">
                            Export CSV
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

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.75fr)]">
            <div class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Monthly volume</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">จำนวนรอบอบรมรายเดือน</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                        ทั้งปี {{ $selectedYear }}
                    </span>
                </div>

                <div class="mt-5 h-[340px]">
                    <canvas
                        data-yearly-chart
                        data-chart-type="bar"
                        data-chart-labels='@json($monthLabels)'
                        data-chart-values='@json($monthTotals)'
                        aria-label="กราฟจำนวนรอบอบรมรายเดือน"
                        role="img"
                    ></canvas>
                </div>
            </div>

            <div class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Attendance</p>
                <h2 class="mt-2 text-2xl font-black text-slate-950">อัตราเข้าร่วมรวม</h2>

                <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-4">
                    <div class="flex items-center justify-between text-sm font-semibold text-slate-500">
                        <span>เข้าร่วมจริง</span>
                        <span>{{ number_format($summary['attendance'] ?? 0) }}%</span>
                    </div>
                    <div class="mt-2 h-3 overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-gradient-to-r from-emerald-600 to-teal-500" style="width: {{ (int) ($summary['attendance'] ?? 0) }}%"></div>
                    </div>
                    <p class="mt-3 text-sm text-slate-500">
                        เข้าร่วม {{ number_format($summary['attended'] ?? 0) }} จากความจุรวม {{ number_format($summary['capacity'] ?? 0) }}
                    </p>
                </div>

                <div class="mt-5 space-y-3">
                    @foreach ($statusBreakdown as $row)
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-gradient-to-r {{ $row['class'] }}"></span>
                                <span class="text-sm font-semibold text-slate-700">{{ $row['label'] }}</span>
                            </div>
                            <span class="text-sm font-black text-slate-950">{{ number_format($row['count']) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
            <div class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Monthly spotlight</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">เดือนที่มีการอบรมมากที่สุด</h2>
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($topMonths as $row)
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">{{ $row['label'] }} {{ $selectedYear }}</p>
                                    <p class="text-xs text-slate-500">
                                        เสร็จ {{ number_format($row['completed']) }} · กำลังอบรม {{ number_format($row['running']) }} · กำหนดแล้ว {{ number_format($row['scheduled']) }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-black text-slate-950">{{ number_format($row['total']) }}</p>
                                    <p class="text-xs text-slate-500">รอบ</p>
                                </div>
                            </div>
                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-gradient-to-r from-sky-600 to-cyan-500" style="width: {{ $topMonths->max('total') > 0 ? (int) round(($row['total'] / max(1, $topMonths->max('total'))) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
                            ยังไม่มีข้อมูลอบรมในปีนี้
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Quick links</p>
                <h2 class="mt-2 text-2xl font-black text-slate-950">ลิงก์ใช้งานเร็ว</h2>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('trainings.index') }}" class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        <span>จัดการรายการอบรม</span>
                        <span>→</span>
                    </a>
                    <a href="{{ route('reports.team-yearly') }}" class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        <span>ดูรายงานทีม</span>
                        <span>→</span>
                    </a>
                    <a href="{{ route('reports.projects') }}" class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        <span>ดูรายงานโครงการ</span>
                        <span>→</span>
                    </a>
                </div>
            </div>
        </section>

        <section class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Recent trainings</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950">รอบอบรมล่าสุดในปีที่เลือก</h2>
                </div>
                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                    {{ number_format($recentTrainings->count()) }} รอบ
                </span>
            </div>

            <div class="mt-5 overflow-hidden rounded-[1.5rem] border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50">
                            <tr class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">
                                <th class="px-4 py-3">หัวข้อ</th>
                                <th class="px-4 py-3">วันที่</th>
                                <th class="px-4 py-3">ผู้สอน</th>
                                <th class="px-4 py-3">สถานที่</th>
                                <th class="px-4 py-3">ผู้เข้าร่วม</th>
                                <th class="px-4 py-3">ความจุ</th>
                                <th class="px-4 py-3">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($recentTrainings as $training)
                                @php
                                    $statusClass = $statusStyles[$training->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200';
                                @endphp
                                <tr class="hover:bg-slate-50/80">
                                    <td class="px-4 py-4">
                                        <p class="font-bold text-slate-950">{{ $training->title }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $training->audience ?: 'ไม่ระบุผู้เข้าร่วมเป้าหมาย' }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">
                                        {{ optional($training->training_date)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">
                                        {{ $training->trainer ?: 'ไม่ระบุ' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">
                                        {{ $training->location ?: 'ไม่ระบุ' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-semibold text-slate-700">
                                        {{ number_format($training->attended) }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-semibold text-slate-700">
                                        {{ number_format($training->capacity) }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $statusClass }}">
                                            {{ $statusLabels[$training->status] ?? $training->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <div class="mx-auto max-w-md">
                                            <p class="text-base font-bold text-slate-900">ยังไม่มีข้อมูลอบรมในปีนี้</p>
                                            <p class="mt-2 text-sm text-slate-500">
                                                ลองเปลี่ยนปี หรือเพิ่มรายการอบรมก่อน ระบบจะเริ่มแสดงข้อมูลในหน้ารายงานนี้
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
