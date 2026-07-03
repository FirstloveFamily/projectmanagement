<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - Task Editor"
    description="Dedicated task edit workspace"
    active="projects"
>
    @php
        $taskStatusLabel = match ($task->status) {
            'todo' => 'ยังไม่เริ่ม',
            'in_progress' => 'กำลังทำ',
            'done' => 'เสร็จแล้ว',
            default => $task->status,
        };

        $taskPriorityLabel = match ($task->priority) {
            'low' => 'ต่ำ',
            'medium' => 'กลาง',
            'high' => 'สูง',
            default => $task->priority,
        };

        $taskStatusBadge = match ($task->status) {
            'todo' => 'bg-white/10 text-white ring-white/15',
            'in_progress' => 'bg-cyan-400/15 text-cyan-100 ring-cyan-200/20',
            'done' => 'bg-emerald-400/15 text-emerald-100 ring-emerald-200/20',
            default => 'bg-white/10 text-white ring-white/15',
        };

        $taskPriorityBadge = match ($task->priority) {
            'low' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'medium' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'high' => 'bg-rose-50 text-rose-700 ring-rose-200',
            default => 'bg-slate-50 text-slate-700 ring-slate-200',
        };

        $statusTone = match ($task->status) {
            'todo' => 'from-slate-950 via-slate-900 to-slate-800',
            'in_progress' => 'from-sky-950 via-slate-900 to-cyan-950',
            'done' => 'from-emerald-950 via-slate-900 to-slate-800',
            default => 'from-slate-950 via-slate-900 to-slate-800',
        };

        $projectStatusLabel = match ($project->status) {
            'active' => 'กำลังดำเนินการ',
            'on_hold' => 'พักงาน',
            'completed' => 'เสร็จสมบูรณ์',
            default => $project->status,
        };

        $isOverdue = $task->due_date?->isPast() && $task->status !== 'done';
    @endphp

    <div class="rounded-[2rem] border border-slate-200 bg-[radial-gradient(circle_at_top_left,rgba(14,165,233,.14),transparent_28%),radial-gradient(circle_at_top_right,rgba(99,102,241,.12),transparent_30%),linear-gradient(180deg,#f8fbff_0%,#eef3f9_100%)] shadow-[0_24px_80px_rgba(15,23,42,.08)]">
        <div class="px-4 pb-4 pt-4 sm:px-6 lg:px-8 lg:pb-8 lg:pt-8">
            <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-[linear-gradient(135deg,rgba(2,6,23,.97),rgba(15,23,42,.92)_40%,rgba(8,47,73,.95)_100%)] text-white shadow-[0_30px_90px_rgba(2,8,23,.35)]">
                <div class="grid gap-0 xl:grid-cols-[1.1fr_.9fr]">
                    <div class="p-5 sm:p-6 lg:p-8">
                        <p class="text-xs font-bold uppercase tracking-[0.38em] text-cyan-200/80">Task editor</p>
                        <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">แก้ไขงาน</h1>
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300 sm:text-base">
                            ปรับข้อมูลของงานนี้แบบแยกหน้าชัดเจน โดยเน้นฟอร์มที่อ่านง่ายและเร็วต่อการแก้ไข
                        </p>

                        <div class="mt-5 flex flex-wrap gap-2 text-sm font-semibold">
                            <span class="rounded-full {{ $taskStatusBadge }} px-3 py-1 ring-1 ring-inset">{{ $taskStatusLabel }}</span>
                            <span class="rounded-full {{ $taskPriorityBadge }} px-3 py-1 ring-1 ring-inset">{{ $taskPriorityLabel }}</span>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-white ring-1 ring-inset ring-white/10">
                                {{ $project->name }}
                            </span>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('projects.tasks.index', $project) }}" class="rounded-full bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">
                                กลับไปงานทั้งหมด
                            </a>
                            <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="rounded-full bg-white/10 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-inset ring-white/10 transition hover:bg-white/15">
                                ดูรายละเอียดงาน
                            </a>
                        </div>
                    </div>

                    <div class="border-t border-white/10 bg-white/5 p-5 sm:p-6 lg:border-l lg:border-t-0 lg:p-8">
                        <div class="grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                            <div class="rounded-[1.4rem] border border-white/10 bg-white/5 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Project</p>
                                <p class="mt-2 text-lg font-black text-white">{{ $project->name }}</p>
                                <p class="mt-1 text-sm text-slate-300">{{ $project->company?->name ?? 'ไม่ระบุบริษัท' }}</p>
                            </div>
                            <div class="rounded-[1.4rem] border border-white/10 bg-white/5 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Owner</p>
                                <p class="mt-2 text-lg font-black text-white">{{ $task->user?->name ?? 'ไม่ระบุเจ้าของ' }}</p>
                                <p class="mt-1 text-sm text-slate-300">{{ \App\Models\Task::taskCategoryLabel($task->task_category) }}</p>
                            </div>
                            <div class="rounded-[1.4rem] border border-white/10 bg-white/5 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Deadline</p>
                                <p class="mt-2 text-lg font-black {{ $isOverdue ? 'text-rose-300' : 'text-white' }}">
                                    {{ $task->due_date?->format('d/m/Y') ?? 'ไม่ระบุ' }}
                                </p>
                                <p class="mt-1 text-sm {{ $isOverdue ? 'text-rose-200' : 'text-slate-300' }}">
                                    {{ $isOverdue ? 'เลยกำหนดแล้ว' : 'ยังไม่เกินกำหนด' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 pb-4 sm:px-6 lg:px-8 lg:pb-8">
            <div class="grid gap-6 xl:grid-cols-[320px_minmax(0,1fr)]">
                <aside class="space-y-4 xl:sticky xl:top-6 xl:self-start">
                    <section class="rounded-[1.7rem] border border-slate-200 bg-white p-5 shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                        <div class="rounded-[1.4rem] bg-gradient-to-r {{ $statusTone }} px-5 py-5 text-white">
                            <p class="text-xs font-bold uppercase tracking-[0.3em] text-cyan-200/80">Task snapshot</p>
                            <h2 class="mt-2 text-2xl font-black tracking-tight">{{ $task->title }}</h2>
                            <p class="mt-2 text-sm text-slate-300">สรุปก่อนบันทึก</p>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">สถานะ</p>
                                <p class="mt-2 text-sm font-bold text-slate-900">{{ $taskStatusLabel }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">ความสำคัญ</p>
                                <p class="mt-2 text-sm font-bold text-slate-900">{{ $taskPriorityLabel }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">เริ่มต้น</p>
                                <p class="mt-2 text-sm font-bold text-slate-900">{{ $task->start_date?->format('d/m/Y') ?? 'ไม่ระบุ' }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">ครบกำหนด</p>
                                <p class="mt-2 text-sm font-bold {{ $isOverdue ? 'text-rose-700' : 'text-slate-900' }}">
                                    {{ $task->due_date?->format('d/m/Y') ?? 'ไม่ระบุ' }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[1.7rem] border border-slate-200 bg-white p-5 shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Project context</p>
                        <div class="mt-4 space-y-3 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-900">โครงการ:</span> {{ $project->name }}</p>
                            <p><span class="font-semibold text-slate-900">บริษัท:</span> {{ $project->company?->name ?? 'ไม่ระบุบริษัท' }}</p>
                            <p><span class="font-semibold text-slate-900">เจ้าของ:</span> {{ $project->user?->name ?? 'ไม่ระบุเจ้าของ' }}</p>
                            <p><span class="font-semibold text-slate-900">สถานะ:</span> {{ $projectStatusLabel }}</p>
                        </div>
                    </section>

                    @if ($isOverdue)
                        <section class="rounded-[1.7rem] border border-rose-200 bg-rose-50 p-5 text-rose-800 shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                            <p class="text-sm font-bold">งานค้างเกินกำหนด</p>
                            <p class="mt-1 text-sm">ควรเช็กสถานะและวันส่งก่อนบันทึก</p>
                        </section>
                    @endif
                </aside>

                <section class="rounded-[1.9rem] border border-slate-200 bg-white shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                    <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Task form</p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950">ฟอร์มแก้ไขข้อมูล</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            แก้ไขเฉพาะข้อมูลที่จำเป็นของงานนี้
                        </p>
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

                        <form method="POST" action="{{ route('projects.tasks.update', [$project, $task]) }}" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid gap-5 lg:grid-cols-2">
                                <div class="space-y-5">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">เจ้าของงาน</label>
                                        <select name="user_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                            <option value="">เลือกเจ้าของ</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" @selected(old('user_id', $task->user_id) == $user->id)>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">ประเภทงาน</label>
                                        <select name="task_category" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                            @foreach ($taskCategories as $value => $label)
                                                <option value="{{ $value }}" @selected(old('task_category', $task->task_category) === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">ชื่องาน</label>
                                        <input
                                            type="text"
                                            name="title"
                                            value="{{ old('title', $task->title) }}"
                                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                            required
                                        >
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">รายละเอียด</label>
                                        <textarea
                                            name="description"
                                            rows="9"
                                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-900 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                        >{{ old('description', $task->description) }}</textarea>
                                    </div>
                                </div>

                                <div class="space-y-5">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">สถานะ</label>
                                            <select name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                                <option value="todo" @selected(old('status', $task->status) === 'todo')>ยังไม่เริ่ม</option>
                                                <option value="in_progress" @selected(old('status', $task->status) === 'in_progress')>กำลังทำ</option>
                                                <option value="done" @selected(old('status', $task->status) === 'done')>เสร็จแล้ว</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">ความสำคัญ</label>
                                            <select name="priority" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                                <option value="low" @selected(old('priority', $task->priority) === 'low')>ต่ำ</option>
                                                <option value="medium" @selected(old('priority', $task->priority) === 'medium')>กลาง</option>
                                                <option value="high" @selected(old('priority', $task->priority) === 'high')>สูง</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">วันที่เริ่ม</label>
                                            <input type="date" name="start_date" value="{{ old('start_date', optional($task->start_date)->format('Y-m-d')) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">ครบกำหนด</label>
                                            <input type="date" name="due_date" value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                        </div>
                                    </div>

                                    <details class="rounded-[1.6rem] border border-slate-200 bg-slate-50 px-4 py-4">
                                        <summary class="cursor-pointer list-none text-sm font-semibold text-slate-700">
                                            Advanced
                                            <span class="ml-2 text-xs font-medium text-slate-500">ซ่อนฟิลด์ที่ใช้ไม่บ่อย</span>
                                        </summary>

                                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                            <div>
                                                <label class="mb-2 block text-sm font-semibold text-slate-700">เสร็จจริงเมื่อ</label>
                                                <input type="datetime-local" name="completed_at" value="{{ old('completed_at', optional($task->completed_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                                            </div>
                                            <div>
                                                <label class="mb-2 block text-sm font-semibold text-slate-700">ลำดับ</label>
                                                <input type="number" name="sort_order" value="{{ old('sort_order', $task->sort_order ?? 0) }}" min="0" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                            </div>
                                        </div>
                                    </details>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-5">
                                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-bold text-white shadow-[0_18px_35px_rgba(15,23,42,.2)] transition hover:bg-slate-800">
                                    บันทึกการแก้ไข
                                </button>
                                <a href="{{ route('projects.tasks.index', $project) }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                                    ยกเลิก
                                </a>
                                <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-100">
                                    เปิดหน้ารายละเอียด
                                </a>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-frontend-layout>
