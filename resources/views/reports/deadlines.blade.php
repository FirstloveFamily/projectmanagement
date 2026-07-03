<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - รายงานแจ้งเตือนกำหนด"
    description="Deadline alert report dashboard"
    active="reports-deadlines"
>
    @php
        $summaryCards = [
            ['label' => 'รายการต้องติดตาม', 'value' => number_format($summary['critical_total'] ?? 0), 'hint' => 'ทั้งหมดในรัศมี 3 วัน', 'tone' => 'from-slate-700 to-slate-500'],
            ['label' => 'ครบกำหนดวันนี้', 'value' => number_format($summary['due_today'] ?? 0), 'hint' => 'ควรรีบเช็กก่อนปิดวัน', 'tone' => 'from-amber-500 to-orange-400'],
            ['label' => 'โปรเจกต์ใกล้ครบ', 'value' => number_format($summary['project_warning'] ?? 0), 'hint' => 'เหลือไม่เกิน 3 วัน', 'tone' => 'from-sky-600 to-cyan-500'],
            ['label' => 'งานใกล้ครบ', 'value' => number_format($summary['task_warning'] ?? 0), 'hint' => 'เหลือไม่เกิน 3 วัน', 'tone' => 'from-violet-600 to-fuchsia-500'],
            ['label' => 'เกินกำหนด', 'value' => number_format(($summary['project_overdue'] ?? 0) + ($summary['task_overdue'] ?? 0)), 'hint' => 'รายการที่ควรเร่งแก้ทันที', 'tone' => 'from-rose-600 to-red-500'],
        ];

        $kindLabel = fn (string $type) => match ($type) {
            'project' => 'Project',
            'task' => 'Task',
            default => $type,
        };
    @endphp

    <div class="space-y-6">
        <section class="overflow-hidden rounded-[2rem] border border-white/60 bg-white/75 p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] backdrop-blur xl:p-7">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-4xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Deadline Watch</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                        รายงานแจ้งเตือนงานและโครงการที่ใกล้ครบกำหนด
                    </h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 sm:text-base">
                        หน้านี้รวมทั้ง Project และ Task ที่เหลือเวลาไม่เกิน {{ $warningDays }} วัน
                        รวมถึงรายการที่เกินกำหนดแล้ว เพื่อให้ทีมเห็นจุดเสี่ยงก่อนชัดเจน
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2 text-sm text-slate-500">
                        <span class="rounded-full bg-slate-100 px-3 py-1 ring-1 ring-inset ring-slate-200">
                            วันนี้ {{ $today->translatedFormat('d M Y') }}
                        </span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 ring-1 ring-inset ring-slate-200">
                            แจ้งเตือนล่วงหน้า {{ $warningDays }} วัน
                        </span>
                        <span class="rounded-full bg-amber-50 px-3 py-1 font-medium text-amber-700 ring-1 ring-inset ring-amber-100">
                            {{ number_format($summary['due_today'] ?? 0) }} รายการครบวันนี้
                        </span>
                        <span class="rounded-full bg-rose-50 px-3 py-1 font-medium text-rose-700 ring-1 ring-inset ring-rose-100">
                            {{ number_format(($summary['project_overdue'] ?? 0) + ($summary['task_overdue'] ?? 0)) }} รายการเกินกำหนด
                        </span>
                    </div>
                </div>

                <div class="w-full max-w-2xl rounded-[1.7rem] border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">ลัดไปหน้าอื่น</p>
                            <p class="mt-1 text-lg font-black text-slate-950">เชื่อมทุกมุมมองรายงาน</p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('reports.projects') }}" class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Project Report
                            </a>
                            <a href="{{ route('reports.index') }}" class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Gantt
                            </a>
                            <a href="{{ route('reports.yearly') }}" class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                รายงานรายปี
                            </a>
                            <a href="{{ route('reports.team-yearly') }}" class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                รายงานทีม
                            </a>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('reports.deadlines.export-xlsx') }}" class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">
                            Export Excel
                        </a>
                        <a href="{{ route('reports.deadlines.export') }}" class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-4 py-2 text-sm font-semibold text-sky-700 transition hover:bg-sky-100">
                            Export CSV
                        </a>
                    </div>
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

        <section class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Critical list</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950">รายการเฝ้าระวังทั้งหมด</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        แสดงรายการที่เหลือเวลาไม่เกิน {{ $warningDays }} วัน และรายการที่เกินกำหนดแล้ว เรียงตามความเร่งด่วน
                    </p>
                </div>
                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                    {{ number_format($combinedRows->count()) }} รายการ
                </span>
            </div>

            <div class="mt-5 overflow-hidden rounded-[1.5rem] border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50">
                            <tr class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">
                                <th class="px-4 py-3">ประเภท</th>
                                <th class="px-4 py-3">รายการ</th>
                                <th class="px-4 py-3">บริบท</th>
                                <th class="px-4 py-3">ครบกำหนด</th>
                                <th class="px-4 py-3">สถานะเตือน</th>
                                <th class="px-4 py-3">สถานะ</th>
                                <th class="px-4 py-3 text-right">เปิดดู</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($combinedRows as $row)
                                <tr class="align-top hover:bg-slate-50/70">
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $row['type_badge'] }}">
                                            {{ $kindLabel($row['type']) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <p class="font-bold text-slate-950">{{ $row['title'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $row['type'] === 'project' ? 'โปรเจกต์หลัก' : 'งานย่อย' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">
                                        @if ($row['type'] === 'project')
                                            <p>{{ $row['company'] }}</p>
                                            <p class="mt-1 text-xs text-slate-500">เจ้าของ: {{ $row['owner'] }}</p>
                                        @else
                                            <p>{{ $row['company'] }}</p>
                                            <p class="mt-1 text-xs text-slate-500">โปรเจกต์: {{ $row['project'] }}</p>
                                            <p class="mt-1 text-xs text-slate-500">ผู้รับผิดชอบ: {{ $row['owner'] }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm font-semibold text-slate-700">
                                        {{ $row['due_label'] }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $row['alert_class'] }}">
                                            {{ $row['alert_label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $row['status_class'] }}">
                                            {{ $row['status_label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <a href="{{ $row['link'] }}" class="inline-flex rounded-full bg-slate-900 px-3 py-2 text-xs font-bold text-white transition hover:opacity-90">
                                            เปิดดู
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <div class="mx-auto max-w-md">
                                            <p class="text-base font-bold text-slate-900">ยังไม่มีรายการที่ต้องแจ้งเตือน</p>
                                            <p class="mt-2 text-sm text-slate-500">
                                                ตอนนี้ยังไม่มีโปรเจกต์หรือ task ที่เหลือเวลาไม่เกิน {{ $warningDays }} วัน
                                                และไม่มีรายการเกินกำหนด
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

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Projects</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">โครงการที่ต้องติดตาม</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                        {{ number_format($projectRows->count()) }} โครงการ
                    </span>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($projectRows->take(8) as $row)
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-slate-950">{{ $row['title'] }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $row['company'] }} · เจ้าของ {{ $row['owner'] }}</p>
                                </div>
                                <div class="text-left md:text-right">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $row['alert_class'] }}">
                                        {{ $row['alert_label'] }}
                                    </span>
                                    <p class="mt-2 text-xs text-slate-500">ครบกำหนด {{ $row['due_label'] }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
                            ยังไม่มีโครงการที่เข้าเงื่อนไขเตือน
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Tasks</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">งานที่ต้องเร่งดู</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                        {{ number_format($taskRows->count()) }} งาน
                    </span>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($taskRows->take(8) as $row)
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-slate-950">{{ $row['title'] }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $row['company'] }} · {{ $row['project'] }}</p>
                                    <p class="mt-1 text-xs text-slate-500">ผู้รับผิดชอบ: {{ $row['owner'] }}</p>
                                </div>
                                <div class="text-left md:text-right">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $row['alert_class'] }}">
                                        {{ $row['alert_label'] }}
                                    </span>
                                    <p class="mt-2 text-xs text-slate-500">ครบกำหนด {{ $row['due_label'] }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
                            ยังไม่มีงานที่เข้าเงื่อนไขเตือน
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-frontend-layout>
