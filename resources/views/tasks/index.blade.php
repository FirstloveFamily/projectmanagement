<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - Task Overview"
    description="Dashboard view for tasks in one project"
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

        $statusFilterLabel = match ($statusFilter) {
            'todo' => 'ยังไม่เริ่ม',
            'in_progress' => 'กำลังทำ',
            'done' => 'เสร็จแล้ว',
            default => 'ทั้งหมด',
        };

        $statusMeta = [
            'todo' => [
                'label' => 'ยังไม่เริ่ม',
                'hint' => 'งานที่ยังรอเริ่ม',
                'badge' => 'bg-slate-50 text-slate-700 ring-slate-200',
                'tone' => 'from-slate-50 via-white to-slate-50',
                'bar' => 'from-slate-500 to-slate-400',
            ],
            'in_progress' => [
                'label' => 'กำลังทำ',
                'hint' => 'งานที่กำลังเดินหน้า',
                'badge' => 'bg-sky-50 text-sky-700 ring-sky-200',
                'tone' => 'from-sky-50 via-white to-cyan-50',
                'bar' => 'from-sky-600 to-cyan-500',
            ],
            'done' => [
                'label' => 'เสร็จแล้ว',
                'hint' => 'งานที่ปิดจบแล้ว',
                'badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                'tone' => 'from-emerald-50 via-white to-teal-50',
                'bar' => 'from-emerald-600 to-teal-500',
            ],
        ];
    @endphp

    <div class="rounded-[2rem] border border-slate-200 bg-[radial-gradient(circle_at_top_left,rgba(14,165,233,.12),transparent_30%),radial-gradient(circle_at_top_right,rgba(34,197,94,.08),transparent_28%),linear-gradient(180deg,#f8fbff_0%,#eef3f9_100%)] shadow-[0_16px_50px_rgba(15,23,42,.08)]">
        <div class="px-4 pb-4 pt-4 sm:px-6 lg:px-8 lg:pb-8 lg:pt-8">
            <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-[linear-gradient(135deg,rgba(2,6,23,.97),rgba(15,23,42,.94)_42%,rgba(8,47,73,.96)_100%)] text-white shadow-[0_30px_90px_rgba(2,8,23,.35)]">
                <div class="grid gap-0 xl:grid-cols-[1.1fr_.9fr]">
                    <div class="p-5 sm:p-6 lg:p-8">
                        <p class="text-xs font-bold uppercase tracking-[0.35em] text-cyan-200/80">Task overview</p>
                        <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">{{ $project->name }}</h1>
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300 sm:text-base">
                            Dashboard งานของโครงการนี้ แสดงสถานะ ความคืบหน้า และการกรองงานแบบเร็วในหน้าเดียว
                        </p>

                        <div class="mt-5 flex flex-wrap gap-2 text-sm font-semibold">
                            <span class="rounded-full {{ $statusBadge }} px-3 py-1 ring-1 ring-inset">{{ $statusLabel }}</span>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-white ring-1 ring-inset ring-white/10">
                                {{ $project->company?->name ?? 'ไม่ระบุบริษัท' }}
                            </span>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-white ring-1 ring-inset ring-white/10">
                                {{ $project->user?->name ?? 'ไม่ระบุเจ้าของ' }}
                            </span>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('projects.show', $project) }}" class="rounded-full bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">
                                กลับหน้า Project detail
                            </a>
                            <a href="#task-form" class="rounded-full bg-white/10 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-inset ring-white/10 transition hover:bg-white/15">
                                เพิ่มงานใหม่
                            </a>
                        </div>
                    </div>

                    <div class="border-t border-white/10 bg-white/5 p-5 sm:p-6 lg:border-l lg:border-t-0 lg:p-8">
                        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                            <div class="rounded-[1.4rem] border border-white/10 bg-white/5 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Tasks</p>
                                <p class="mt-2 text-3xl font-black text-white">{{ number_format($taskCount) }}</p>
                                <p class="mt-1 text-sm text-slate-300">งานทั้งหมด</p>
                            </div>
                            <div class="rounded-[1.4rem] border border-white/10 bg-white/5 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Progress</p>
                                <p class="mt-2 text-3xl font-black text-white">{{ $progress }}%</p>
                                <p class="mt-1 text-sm text-slate-300">ปิดงานแล้ว {{ number_format($doneTaskCount) }}</p>
                            </div>
                            <div class="rounded-[1.4rem] border border-white/10 bg-white/5 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Overdue</p>
                                <p class="mt-2 text-3xl font-black text-rose-300">{{ number_format($overdueTaskCount) }}</p>
                                <p class="mt-1 text-sm text-slate-300">งานค้างเกินกำหนด</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 pb-4 sm:px-6 lg:px-8 lg:pb-8">
            <section class="rounded-[1.9rem] border border-slate-200 bg-white shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Quick filters</p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950">กรองงานแบบเร็ว</h2>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ([
                            'all' => 'ทั้งหมด',
                            'todo' => 'ยังไม่เริ่ม',
                            'in_progress' => 'กำลังทำ',
                            'done' => 'เสร็จแล้ว',
                        ] as $value => $label)
                            <a
                                href="{{ route('projects.tasks.index', array_filter(['project' => $project, 'status' => $value])) }}"
                                class="rounded-full px-4 py-2 text-sm font-semibold ring-1 ring-inset transition {{ $statusFilter === $value ? 'bg-slate-900 text-white ring-slate-900' : 'bg-slate-50 text-slate-700 ring-slate-200 hover:bg-slate-100' }}"
                            >
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-5 sm:p-6">
                    <div class="rounded-[1.4rem] border border-slate-200 bg-slate-50 p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">ทั้งหมด</p>
                        <div class="mt-3 flex items-end gap-2">
                            <div class="text-3xl font-black text-slate-950">{{ number_format($taskCount) }}</div>
                            <div class="pb-1 text-xs font-semibold text-slate-500">งาน</div>
                        </div>
                    </div>
                    <div class="rounded-[1.4rem] border border-sky-200 bg-gradient-to-br from-sky-50 to-white p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-700">กำลังทำ</p>
                        <div class="mt-3 flex items-end gap-2">
                            <div class="text-3xl font-black text-slate-950">{{ number_format($doingTaskCount) }}</div>
                            <div class="pb-1 text-xs font-semibold text-slate-500">งาน</div>
                        </div>
                    </div>
                    <div class="rounded-[1.4rem] border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">เสร็จแล้ว</p>
                        <div class="mt-3 flex items-end gap-2">
                            <div class="text-3xl font-black text-slate-950">{{ number_format($doneTaskCount) }}</div>
                            <div class="pb-1 text-xs font-semibold text-slate-500">งาน</div>
                        </div>
                    </div>
                    <div class="rounded-[1.4rem] border border-rose-200 bg-gradient-to-br from-rose-50 to-white p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-rose-700">ค้าง</p>
                        <div class="mt-3 flex items-end gap-2">
                            <div class="text-3xl font-black text-slate-950">{{ number_format($overdueTaskCount) }}</div>
                            <div class="pb-1 text-xs font-semibold text-slate-500">งาน</div>
                        </div>
                    </div>
                    <div class="rounded-[1.4rem] border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">ใกล้ครบกำหนด</p>
                        <div class="mt-3 flex items-end gap-2">
                            <div class="text-3xl font-black text-slate-950">{{ number_format($dueSoonTaskCount) }}</div>
                            <div class="pb-1 text-xs font-semibold text-slate-500">งาน</div>
                        </div>
                    </div>
                </div>

                <div class="px-5 pb-5 sm:px-6">
                    <div class="h-3 overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-gradient-to-r from-sky-600 via-cyan-500 to-emerald-500" style="width: {{ $progress }}%"></div>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-sm text-slate-500">
                        <span>ความคืบหน้ารวม</span>
                        <span>{{ $progress }}%</span>
                    </div>
                </div>
            </section>

            <section class="mt-6 rounded-[1.9rem] border border-slate-200 bg-white shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Dashboard board</p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950">งานตามสถานะ</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-inset ring-slate-200">
                        มุมมอง: {{ $statusFilterLabel }}
                    </span>
                </div>

                <div class="grid gap-5 p-5 xl:grid-cols-3 sm:p-6">
                    @forelse ($groupedTasks as $status => $tasks)
                        @php
                            $meta = $statusMeta[$status] ?? $statusMeta['todo'];
                            $statusCount = $tasks->count();
                            $statusOverdueCount = $tasks->filter(fn ($task) => $task->status !== 'done' && $task->due_date?->isPast())->count();
                            $statusDueSoonCount = $tasks->filter(fn ($task) => $task->status !== 'done' && $task->due_date && $task->due_date->betweenIncluded(now()->startOfDay(), now()->addDays(7)->endOfDay()))->count();
                        @endphp
                        <section class="rounded-[1.5rem] border border-slate-200 bg-gradient-to-b {{ $meta['tone'] }} p-4">
                            <div class="rounded-[1.2rem] bg-white/80 p-4 ring-1 ring-inset ring-slate-200">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">{{ $meta['label'] }}</p>
                                        <h3 class="mt-2 text-xl font-black text-slate-950">{{ $statusCount }} งาน</h3>
                                        <p class="mt-1 text-sm text-slate-500">{{ $meta['hint'] }}</p>
                                    </div>
                                    <span class="rounded-full {{ $meta['badge'] }} px-3 py-1 text-xs font-bold ring-1 ring-inset">{{ $statusCount }}</span>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-2">
                                    <div class="rounded-2xl bg-slate-50 px-3 py-3">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">ค้าง</p>
                                        <p class="mt-1 text-lg font-black text-rose-700">{{ number_format($statusOverdueCount) }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 px-3 py-3">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">ใกล้ครบ</p>
                                        <p class="mt-1 text-lg font-black text-amber-700">{{ number_format($statusDueSoonCount) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 space-y-3">
                                @forelse ($tasks as $task)
                                    @php
                                        $taskOverdue = $task->status !== 'done' && $task->due_date?->isPast();
                                        $taskDueSoon = $task->status !== 'done' && $task->due_date && $task->due_date->betweenIncluded(now()->startOfDay(), now()->addDays(7)->endOfDay());
                                    @endphp
                                    <article class="rounded-[1.3rem] border border-slate-200 bg-white p-4 shadow-sm">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <h4 class="truncate text-base font-black text-slate-950">{{ $task->title }}</h4>
                                                <p class="mt-1 text-sm text-slate-500">{{ \App\Models\Task::taskCategoryLabel($task->task_category) }}</p>
                                            </div>
                                            @if ($taskOverdue)
                                                <span class="rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-700 ring-1 ring-inset ring-rose-200">Overdue</span>
                                            @elseif ($taskDueSoon)
                                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700 ring-1 ring-inset ring-amber-200">Soon</span>
                                            @endif
                                        </div>

                                        <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold text-slate-500">
                                            <span class="rounded-full bg-slate-50 px-3 py-1 ring-1 ring-inset ring-slate-200">
                                                {{ $task->user?->name ?? 'ไม่ระบุเจ้าของ' }}
                                            </span>
                                            <span class="rounded-full bg-slate-50 px-3 py-1 ring-1 ring-inset ring-slate-200">
                                                {{ $task->due_date?->format('d/m/Y') ?? 'ไม่ระบุครบกำหนด' }}
                                            </span>
                                            <span class="rounded-full bg-slate-50 px-3 py-1 ring-1 ring-inset ring-slate-200">
                                                checklist {{ number_format($task->checklists_count ?? 0) }}
                                            </span>
                                        </div>

                                        <div class="mt-4 flex flex-wrap gap-2">
                                            <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="rounded-full bg-slate-900 px-3 py-2 text-xs font-bold text-white transition hover:opacity-90">
                                                เปิดรายละเอียด
                                            </a>
                                            @can('manage-tasks')
                                                <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="rounded-full bg-white px-3 py-2 text-xs font-bold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-100">
                                                    แก้ไข
                                                </a>
                                            @endcan
                                        </div>
                                    </article>
                                @empty
                                    <div class="rounded-[1.3rem] border border-dashed border-slate-300 bg-white px-4 py-8 text-center text-sm text-slate-500">
                                        ไม่มีงานในสถานะนี้
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    @empty
                        <div class="col-span-full rounded-[1.4rem] border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-slate-500">
                            ยังไม่มีงานในโครงการนี้
                        </div>
                    @endforelse
                </div>
            </section>

            @can('manage-tasks')
                <section id="task-form" class="mt-6 rounded-[1.9rem] border border-slate-200 bg-white shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                    <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Create task</p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950">เพิ่มงานใหม่ในโปรเจกต์นี้</h2>
                    </div>

                    <div class="px-5 py-5 sm:px-6">
                        @if ($errors->any())
                            <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                                <p class="font-semibold">ตรวจพบข้อมูลไม่ครบ</p>
                                <ul class="mt-2 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>• {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('projects.tasks.store', $project) }}" class="space-y-6">
                            @csrf

                            <div class="grid gap-5 lg:grid-cols-2">
                                <div class="space-y-5">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">เจ้าของงาน</label>
                                        <select name="user_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                            <option value="">เลือกเจ้าของ</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" @selected(old('user_id', $project->user_id) == $user->id)>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">ประเภทงาน</label>
                                        <select name="task_category" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                            @foreach ($taskCategories as $value => $label)
                                                <option value="{{ $value }}" @selected(old('task_category', 'general') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">ชื่องาน</label>
                                        <input
                                            type="text"
                                            name="title"
                                            value="{{ old('title') }}"
                                            placeholder="เช่น ติดตามเอกสารอนุมัติ"
                                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                            required
                                        >
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">รายละเอียด</label>
                                        <textarea
                                            name="description"
                                            rows="7"
                                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-900 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                        >{{ old('description') }}</textarea>
                                    </div>
                                </div>

                                <div class="space-y-5">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">สถานะ</label>
                                            <select name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                                <option value="todo" @selected(old('status', 'todo') === 'todo')>ยังไม่เริ่ม</option>
                                                <option value="in_progress" @selected(old('status') === 'in_progress')>กำลังทำ</option>
                                                <option value="done" @selected(old('status') === 'done')>เสร็จแล้ว</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">ความสำคัญ</label>
                                            <select name="priority" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                                <option value="low" @selected(old('priority', 'medium') === 'low')>ต่ำ</option>
                                                <option value="medium" @selected(old('priority', 'medium') === 'medium')>กลาง</option>
                                                <option value="high" @selected(old('priority') === 'high')>สูง</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">วันที่เริ่ม</label>
                                            <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">ครบกำหนด</label>
                                            <input type="date" name="due_date" value="{{ old('due_date') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">เสร็จจริงเมื่อ</label>
                                        <input type="datetime-local" name="completed_at" value="{{ old('completed_at') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">ลำดับ</label>
                                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                    </div>

                                    <div class="rounded-[1.6rem] border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-5">
                                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Quick note</p>
                                        <p class="mt-2 text-sm leading-6 text-slate-600">
                                            ใช้ฟอร์มนี้เพื่อเพิ่มงานใหม่ได้ทันทีโดยไม่ต้องออกจาก dashboard
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-5">
                                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-bold text-white shadow-[0_18px_35px_rgba(15,23,42,.2)] transition hover:bg-slate-800">
                                    บันทึกงานใหม่
                                </button>
                                <a href="#summary" class="rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                                    กลับขึ้นสรุป
                                </a>
                            </div>
                        </form>
                    </div>
                </section>
            @endcan
        </div>
    </div>
</x-frontend-layout>
