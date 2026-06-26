<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - Company Detail"
    description="Frontend company detail dashboard"
    active="companies"
>
    @php
        $summaryCards = [
            ['label' => 'โครงการทั้งหมด', 'value' => number_format($summary['totalProjects'] ?? 0), 'hint' => 'รายการในบริษัทนี้'],
            ['label' => 'กำลังดำเนินการ', 'value' => number_format($summary['activeProjects'] ?? 0), 'hint' => 'โครงการที่กำลังเดินหน้า'],
            ['label' => 'เสร็จสมบูรณ์', 'value' => number_format($summary['completedProjects'] ?? 0), 'hint' => 'โครงการที่ปิดแล้ว'],
            ['label' => 'ค้างเกินกำหนด', 'value' => number_format($summary['overdueProjects'] ?? 0), 'hint' => 'ต้องเร่งติดตาม'],
        ];
    @endphp
    <div class="rounded-[2rem] border border-white/60 bg-white/70 p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] backdrop-blur xl:p-7">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-4xl">
                        <div class="flex flex-wrap items-center gap-3">
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">รายละเอียดบริษัท</p>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-inset ring-slate-200">
                                โครงการ {{ number_format($summary['totalProjects'] ?? 0) }} รายการ
                            </span>
                        </div>
                        <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">{{ $company->name }}</h1>
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 sm:text-base">
                            {{ $company->description ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @can('manage-companies')
                            <a href="{{ route('companies.index', ['edit' => $company->id]) }}#company-form" class="rounded-full bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                                แก้ไขบริษัท
                            </a>
                        @endcan
                        <a href="{{ route('projects.index', ['company_id' => $company->id]) }}" class="rounded-full bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                            ดูโครงการ
                        </a>
                        <a href="{{ route('companies.index') }}" class="rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90">
                            กลับไปหน้า Company
                        </a>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($summaryCards as $card)
                        <div class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                            <p class="text-sm font-semibold text-slate-500">{{ $card['label'] }}</p>
                            <div class="mt-3 text-3xl font-black text-slate-950">{{ $card['value'] }}</div>
                            <p class="mt-2 text-sm text-slate-500">{{ $card['hint'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
                    <section class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">กราฟสรุป</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">สถานะโครงการในบริษัทนี้</h2>

                        <div class="mt-5 space-y-4">
                            @foreach ($statusBreakdown as $row)
                                <div>
                                    <div class="flex items-center justify-between text-sm font-semibold text-slate-600">
                                        <span>{{ $row['label'] }}</span>
                                        <span>{{ number_format($row['count']) }}</span>
                                    </div>
                                    <div class="mt-2 h-3 overflow-hidden rounded-full bg-slate-200">
                                        <div
                                            class="h-full rounded-full bg-gradient-to-r {{ $row['class'] }}"
                                            style="width: {{ ($summary['totalProjects'] ?? 0) > 0 ? max(6, round(($row['count'] / $summary['totalProjects']) * 100)) : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">งานในระบบ</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">สรุปงานรวม</h2>

                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">งานทั้งหมด</p>
                                <p class="mt-2 text-sm font-bold text-slate-900">{{ number_format($summary['taskCount'] ?? 0) }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">กำลังทำ</p>
                                <p class="mt-2 text-sm font-bold text-slate-900">{{ number_format($summary['activeTaskCount'] ?? 0) }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">เสร็จแล้ว</p>
                                <p class="mt-2 text-sm font-bold text-slate-900">{{ number_format($summary['completedTaskCount'] ?? 0) }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">ค้าง</p>
                                <p class="mt-2 text-sm font-bold text-slate-900">{{ number_format($summary['overdueTaskCount'] ?? 0) }}</p>
                            </div>
                        </div>
                    </section>
                </div>

                <section class="mt-6 rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">โครงการล่าสุด</p>
                            <h2 class="mt-2 text-2xl font-black text-slate-950">รายการโครงการของบริษัทนี้</h2>
                        </div>
                        <a href="{{ route('projects.index', ['company_id' => $company->id]) }}" class="rounded-full bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                            ดูทั้งหมด
                        </a>
                    </div>

                    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @forelse ($projects->take(6) as $project)
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
                            @endphp
                            <article class="rounded-[1.4rem] border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="text-base font-black text-slate-950">{{ $project->name }}</h3>
                                        <p class="mt-1 text-sm text-slate-500">{{ optional($project->user)->name ?? 'ไม่ระบุเจ้าของ' }}</p>
                                    </div>
                                    <span class="rounded-full {{ $statusBadge }} px-3 py-1 text-xs font-bold ring-1 ring-inset">{{ $statusLabel }}</span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $project->description ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}</p>
                                <div class="mt-4 flex items-center gap-2">
                                    <a href="{{ route('projects.show', $project) }}" class="rounded-full bg-slate-900 px-3 py-2 text-xs font-bold text-white">ดู</a>
                                    @can('manage-projects')
                                        <a href="{{ route('projects.create', ['edit' => $project->id]) }}#project-form" class="rounded-full bg-white px-3 py-2 text-xs font-bold text-slate-700 ring-1 ring-inset ring-slate-200">แก้ไข</a>
                                    @endcan
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full rounded-[1.4rem] border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-slate-500">
                                ยังไม่มีโครงการในบริษัทนี้
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
    </div>
</x-frontend-layout>
