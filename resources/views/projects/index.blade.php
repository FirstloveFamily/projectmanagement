<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - Projects"
    description="Frontend project management dashboard"
    active="projects"
>
    @php
        $statusOptions = [
            '' => 'ทั้งหมด',
            'active' => 'กำลังดำเนินการ',
            'on_hold' => 'พักงาน',
            'completed' => 'เสร็จสมบูรณ์',
        ];

        $statusBadge = fn (string $status) => match ($status) {
            'active' => 'bg-sky-50 text-sky-700 ring-sky-200',
            'on_hold' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            default => 'bg-slate-50 text-slate-700 ring-slate-200',
        };
    @endphp

    <div class="rounded-[2rem] border border-white/60 bg-white/70 p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] backdrop-blur xl:p-7">
        @if (session('success'))
            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">หน้า Project</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                    รายการโครงการทั้งหมด
                </h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                    หน้านี้แสดงเฉพาะภาพรวมและรายการโครงการทั้งหมด ส่วนการสร้างโครงการใหม่แยกไปอีกหน้าหนึ่งเพื่อให้ใช้งานชัดเจนขึ้น
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                @can('manage-projects')
                    <a href="{{ route('projects.create') }}" class="group inline-flex items-center gap-3 rounded-[1.15rem] bg-[linear-gradient(135deg,#2563eb_0%,#0ea5e9_45%,#06b6d4_100%)] px-5 py-3 text-sm font-bold text-white shadow-[0_18px_35px_rgba(37,99,235,.26)] ring-1 ring-inset ring-white/10 transition hover:-translate-y-0.5 hover:shadow-[0_22px_40px_rgba(37,99,235,.32)]">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-white/15 transition group-hover:bg-white/20">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4.5 w-4.5">
                                <path d="M12 5v14M5 12h14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </span>
                        <span class="flex flex-col items-start leading-tight">
                            <span>เพิ่มโครงการใหม่</span>
                            <span class="text-[11px] font-semibold text-white/80">สร้างโปรเจกต์จากฟอร์มแยก</span>
                        </span>
                    </a>
                @endcan
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">ค้นหา / กรอง</p>
                    <p class="mt-1 text-lg font-black text-slate-950">{{ $filters['q'] ?: 'แสดงรายการทั้งหมด' }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $filters['status'] ? ($statusOptions[$filters['status']] ?? $filters['status']) : 'ไม่กรองสถานะ' }}</p>
                </div>
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                <p class="text-sm font-semibold text-slate-500">โครงการทั้งหมด</p>
                <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($summary['total'] ?? 0) }}</div>
                <p class="mt-2 text-sm text-slate-500">รายการในระบบ</p>
            </div>
            <div class="rounded-[1.6rem] border border-sky-200 bg-gradient-to-br from-sky-50 to-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                <p class="text-sm font-semibold text-sky-700">กำลังดำเนินการ</p>
                <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($summary['active'] ?? 0) }}</div>
                <p class="mt-2 text-sm text-slate-500">งานที่ยังเดินหน้า</p>
            </div>
            <div class="rounded-[1.6rem] border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                <p class="text-sm font-semibold text-emerald-700">เสร็จสมบูรณ์</p>
                <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($summary['completed'] ?? 0) }}</div>
                <p class="mt-2 text-sm text-slate-500">ปิดงานแล้ว</p>
            </div>
            <div class="rounded-[1.6rem] border border-rose-200 bg-gradient-to-br from-rose-50 to-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                <p class="text-sm font-semibold text-rose-700">ค้างเกินกำหนด</p>
                <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($summary['overdue'] ?? 0) }}</div>
                <p class="mt-2 text-sm text-slate-500">ต้องเร่งติดตาม</p>
            </div>
        </div>

        <div class="mt-6 rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
            <form method="GET" action="{{ route('projects.index') }}" class="grid gap-4 lg:grid-cols-[1.1fr_.8fr_.8fr_auto]">
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">ค้นหา</label>
                    <input
                        type="text"
                        name="q"
                        value="{{ $filters['q'] ?? '' }}"
                        placeholder="พิมพ์ชื่อโครงการ หรือคำสำคัญ"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">บริษัท</label>
                    <select
                        name="company_id"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                    >
                        <option value="">ทุกบริษัท</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected((string) ($filters['company_id'] ?? '') === (string) $company->id)>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">สถานะ</label>
                    <select
                        name="status"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                    >
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected((string) ($filters['status'] ?? '') === (string) $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                        กรองข้อมูล
                    </button>
                    <a href="{{ route('projects.index') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        ล้าง
                    </a>
                </div>
            </form>
        </div>

        <section class="mt-6 space-y-4">
            @forelse ($projects as $project)
                @php
                    $taskCount = (int) ($project->tasks_count ?? 0);
                    $completedTaskCount = (int) ($project->completed_tasks_count ?? 0);
                    $activeTaskCount = (int) ($project->active_tasks_count ?? 0);
                    $overdueTaskCount = (int) ($project->overdue_tasks_count ?? 0);
                    $progress = $taskCount > 0 ? (int) round(($completedTaskCount / $taskCount) * 100) : 0;
                    $companyName = optional($project->company)->name ?? 'ไม่ระบุบริษัท';
                    $ownerName = optional($project->user)->name ?? 'ไม่ระบุเจ้าของ';
                    $statusLabel = match ($project->status) {
                        'active' => 'กำลังดำเนินการ',
                        'on_hold' => 'พักงาน',
                        'completed' => 'เสร็จสมบูรณ์',
                        default => $project->status,
                    };
                @endphp
                <article class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="text-xl font-black text-slate-950">{{ $project->name }}</h3>
                                <span class="rounded-full {{ $statusBadge($project->status) }} px-3 py-1 text-xs font-bold ring-1 ring-inset">
                                    {{ $statusLabel }}
                                </span>
                                @if ($overdueTaskCount > 0)
                                    <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 ring-1 ring-inset ring-rose-200">
                                        ค้าง {{ $overdueTaskCount }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-2 text-sm font-semibold text-slate-500">{{ $companyName }} · {{ $ownerName }}</p>
                            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
                                {{ $project->description ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}
                            </p>

                            <div class="mt-4 grid gap-3 lg:grid-cols-3">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Objective</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-600">
                                        {{ \Illuminate\Support\Str::limit($project->objective ?: 'ยังไม่มีวัตถุประสงค์', 90) }}
                                    </p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Risk</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-600">
                                        {{ \Illuminate\Support\Str::limit($project->risk ?: 'ยังไม่มีความเสี่ยงที่ระบุ', 90) }}
                                    </p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Notes</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-600">
                                        {{ \Illuminate\Support\Str::limit($project->notes ?: 'ยังไม่มีหมายเหตุ', 90) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <a href="{{ route('projects.show', $project) }}" class="rounded-full bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                                ดู
                            </a>
                            @can('manage-projects')
                                <a href="{{ route('projects.create', ['edit' => $project->id]) }}#project-form" class="rounded-full bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                                    แก้ไข
                                </a>
                                <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('ต้องการลบโครงการนี้ใช่ไหม?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 ring-1 ring-inset ring-rose-200 transition hover:bg-rose-100">
                                        ลบ
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>

                    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">เริ่ม</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ $project->start_date?->format('d/m/Y') ?? 'ไม่ระบุ' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">ครบกำหนด</p>
                            <p class="mt-1 text-sm font-bold {{ $project->due_date?->isPast() ? 'text-rose-700' : 'text-slate-900' }}">{{ $project->due_date?->format('d/m/Y') ?? 'ไม่ระบุ' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">งานทั้งหมด</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ number_format($taskCount) }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">ความคืบหน้า</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ $taskCount > 0 ? "{$progress}%" : 'ไม่มีงาน' }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                            <span>Progress</span>
                            <span>{{ $progress }}%</span>
                        </div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full bg-gradient-to-r from-sky-600 to-cyan-500" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[1.6rem] border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-slate-500 shadow-[0_14px_40px_rgba(15,23,42,.05)]">
                    ยังไม่มีข้อมูลโครงการในระบบ
                </div>
            @endforelse
        </section>
    </div>
</x-frontend-layout>
