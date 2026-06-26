<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - Project Detail"
    description="Project detail overview"
    active="projects"
>
    @php
        $statusLabel = match ($project->status) {
            'active' => 'กำลังดำเนินการ',
            'on_hold' => 'พักงาน',
            'completed' => 'เสร็จสมบูรณ์',
            default => $project->status,
        };

        $statusBadge = match ($project->status) {
            'active' => 'bg-sky-50 text-sky-700 ring-sky-200',
            'on_hold' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            default => 'bg-slate-50 text-slate-700 ring-slate-200',
        };

        $taskCount = (int) ($project->tasks_count ?? 0);
        $completedTaskCount = (int) ($project->completed_tasks_count ?? 0);
        $activeTaskCount = (int) ($project->active_tasks_count ?? 0);
        $overdueTaskCount = (int) ($project->overdue_tasks_count ?? 0);
        $progress = $taskCount > 0 ? (int) round(($completedTaskCount / $taskCount) * 100) : 0;
        $daysToDue = $project->due_date ? now()->startOfDay()->diffInDays($project->due_date->startOfDay(), false) : null;
        $daysFromStart = $project->start_date ? now()->startOfDay()->diffInDays($project->start_date->startOfDay(), false) : null;
    @endphp

    <div class="rounded-[2rem] border border-slate-200 bg-[radial-gradient(circle_at_top_left,rgba(14,165,233,.12),transparent_30%),radial-gradient(circle_at_top_right,rgba(34,197,94,.08),transparent_28%),linear-gradient(180deg,#f8fbff_0%,#eef3f9_100%)] shadow-[0_24px_80px_rgba(15,23,42,.08)]">
        @if (session('success'))
            <div class="px-4 pt-4 sm:px-6 lg:px-8 lg:pt-8">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="px-4 pb-4 pt-4 sm:px-6 lg:px-8 lg:pb-8 lg:pt-8">
            <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-[linear-gradient(135deg,rgba(2,6,23,.97),rgba(15,23,42,.94)_42%,rgba(8,47,73,.96)_100%)] text-white shadow-[0_30px_90px_rgba(2,8,23,.35)]">
                <div class="grid gap-0 xl:grid-cols-[1.15fr_.85fr]">
                    <div class="p-5 sm:p-6 lg:p-8">
                        <p class="text-xs font-bold uppercase tracking-[0.35em] text-cyan-200/80">Project detail</p>
                        <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">{{ $project->name }}</h1>
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300 sm:text-base">
                            {{ $project->description ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}
                        </p>

                        <div class="mt-5 flex flex-wrap gap-2 text-sm font-semibold">
                            <span class="rounded-full {{ $statusBadge }} px-3 py-1 ring-1 ring-inset">{{ $statusLabel }}</span>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-white ring-1 ring-inset ring-white/10">
                                {{ $project->company?->name ?? 'ไม่ระบุบริษัท' }}
                            </span>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-white ring-1 ring-inset ring-white/10">
                                เจ้าของ {{ $project->user?->name ?? 'ไม่ระบุเจ้าของ' }}
                            </span>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="#summary" class="rounded-full bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">
                                ดูสรุป
                            </a>
                            <a href="#tasks" class="rounded-full bg-white/10 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-inset ring-white/10 transition hover:bg-white/15">
                                ดูงานในโครงการ
                            </a>
                        </div>
                    </div>

                    <div class="border-t border-white/10 bg-white/5 p-5 sm:p-6 lg:border-l lg:border-t-0 lg:p-8">
                        <div class="grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                            <div class="rounded-[1.4rem] border border-white/10 bg-white/5 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Company</p>
                                <p class="mt-2 text-lg font-black text-white">{{ $project->company?->name ?? 'ไม่ระบุบริษัท' }}</p>
                            </div>
                            <div class="rounded-[1.4rem] border border-white/10 bg-white/5 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Owner</p>
                                <p class="mt-2 text-lg font-black text-white">{{ $project->user?->name ?? 'ไม่ระบุเจ้าของ' }}</p>
                            </div>
                            <div class="rounded-[1.4rem] border border-white/10 bg-white/5 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Deadline</p>
                                <p class="mt-2 text-lg font-black {{ $project->due_date?->isPast() ? 'text-rose-300' : 'text-white' }}">
                                    {{ $project->due_date?->format('d/m/Y') ?? 'ไม่ระบุ' }}
                                </p>
                                <p class="mt-1 text-sm {{ $project->due_date?->isPast() ? 'text-rose-200' : 'text-slate-300' }}">
                                    {{ $project->due_date?->isPast() ? 'เกินกำหนดแล้ว' : 'ยังอยู่ในกำหนด' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 pb-4 sm:px-6 lg:px-8 lg:pb-8">
            <section id="summary" class="rounded-[1.9rem] border border-slate-200 bg-white shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Overview</p>
                    <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950">สรุปโครงการ</h2>
                </div>

                <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-[1.4rem] border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">งานทั้งหมด</p>
                        <div class="mt-3 flex items-end gap-3">
                            <div class="text-3xl font-black text-slate-950">{{ number_format($taskCount) }}</div>
                            <div class="pb-1 text-xs font-semibold text-slate-500">รายการ</div>
                        </div>
                    </div>
                    <div class="rounded-[1.4rem] border border-sky-200 bg-gradient-to-br from-sky-50 to-white p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-700">กำลังทำ</p>
                        <div class="mt-3 flex items-end gap-3">
                            <div class="text-3xl font-black text-slate-950">{{ number_format($activeTaskCount) }}</div>
                            <div class="pb-1 text-xs font-semibold text-slate-500">งาน</div>
                        </div>
                    </div>
                    <div class="rounded-[1.4rem] border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">เสร็จแล้ว</p>
                        <div class="mt-3 flex items-end gap-3">
                            <div class="text-3xl font-black text-slate-950">{{ number_format($completedTaskCount) }}</div>
                            <div class="pb-1 text-xs font-semibold text-slate-500">งาน</div>
                        </div>
                    </div>
                    <div class="rounded-[1.4rem] border border-rose-200 bg-gradient-to-br from-rose-50 to-white p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-rose-700">ค้างเกินกำหนด</p>
                        <div class="mt-3 flex items-end gap-3">
                            <div class="text-3xl font-black text-slate-950">{{ number_format($overdueTaskCount) }}</div>
                            <div class="pb-1 text-xs font-semibold text-slate-500">งาน</div>
                        </div>
                    </div>
                </div>

                <div class="px-5 pb-5 sm:px-6">
                    <div class="h-3 overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-gradient-to-r from-sky-600 via-cyan-500 to-emerald-500" style="width: {{ $progress }}%"></div>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-sm text-slate-500">
                        <span>ความคืบหน้า</span>
                        <span>{{ $progress }}%</span>
                    </div>
                </div>
            </section>

            <div class="mt-6 grid gap-6 xl:grid-cols-[1.05fr_.95fr]">
                <section class="rounded-[1.9rem] border border-slate-200 bg-white p-5 shadow-[0_16px_50px_rgba(15,23,42,.08)] sm:p-6">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Project info</p>
                    <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950">ข้อมูลสำคัญ</h2>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">บริษัท</p>
                            <p class="mt-2 text-sm font-bold text-slate-900">{{ $project->company?->name ?? 'ไม่ระบุบริษัท' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">เจ้าของ</p>
                            <p class="mt-2 text-sm font-bold text-slate-900">{{ $project->user?->name ?? 'ไม่ระบุเจ้าของ' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">เริ่มโครงการ</p>
                            <p class="mt-2 text-sm font-bold text-slate-900">{{ $project->start_date?->format('d/m/Y') ?? 'ไม่ระบุ' }}</p>
                            @if (!is_null($daysFromStart))
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $daysFromStart >= 0 ? 'เริ่มมาแล้ว ' . number_format($daysFromStart) . ' วัน' : 'เริ่มอีก ' . number_format(abs($daysFromStart)) . ' วัน' }}
                                </p>
                            @endif
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">ครบกำหนด</p>
                            <p class="mt-2 text-sm font-bold {{ $project->due_date?->isPast() ? 'text-rose-700' : 'text-slate-900' }}">
                                {{ $project->due_date?->format('d/m/Y') ?? 'ไม่ระบุ' }}
                            </p>
                            @if (!is_null($daysToDue))
                                <p class="mt-1 text-xs {{ $daysToDue < 0 ? 'text-rose-600' : 'text-slate-500' }}">
                                    {{ $daysToDue < 0 ? 'เลยกำหนดมาแล้ว ' . number_format(abs($daysToDue)) . ' วัน' : 'เหลืออีก ' . number_format($daysToDue) . ' วัน' }}
                                </p>
                            @endif
                        </div>
                    </div>
                </section>

                <section class="rounded-[1.9rem] border border-slate-200 bg-white p-5 shadow-[0_16px_50px_rgba(15,23,42,.08)] sm:p-6">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Objective / Risk / Notes</p>
                    <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950">สาระสำคัญของโครงการ</h2>

                    <div class="mt-5 space-y-4">
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">วัตถุประสงค์</p>
                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $project->objective ?: 'ไม่มีวัตถุประสงค์ระบุไว้' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">ความเสี่ยง</p>
                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $project->risk ?: 'ไม่มีความเสี่ยงระบุไว้' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">หมายเหตุ</p>
                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $project->notes ?: 'ไม่มีหมายเหตุเพิ่มเติม' }}</p>
                        </div>
                    </div>
                </section>
            </div>

            <section class="mt-6 rounded-[1.9rem] border border-slate-200 bg-white shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Description</p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950">รายละเอียดโครงการ</h2>
                    </div>
                    <a href="#summary" class="rounded-full bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                        กลับสรุปด้านบน
                    </a>
                </div>

                <div class="px-5 py-5 sm:px-6">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-sm leading-7 text-slate-700">
                            {{ $project->description ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="mt-6 rounded-[1.9rem] border border-slate-200 bg-white shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Tasks</p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950">งานในโครงการ</h2>
                        <p class="mt-2 text-sm text-slate-500">ย้ายรายการงานทั้งหมดไปหน้าแยกแล้ว เหลือแค่ลิงก์เปิดดูรายละเอียด</p>
                    </div>
                    <a href="{{ route('projects.tasks.index', $project) }}" class="rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90">
                        เปิดหน้ารายการงาน
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-frontend-layout>
