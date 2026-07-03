<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - Task Detail"
    description="Task tracking detail page"
    active="projects"
>
    @php
        $statusLabel = match ($task->status) {
            'todo' => 'ยังไม่เริ่ม',
            'in_progress' => 'กำลังทำ',
            'done' => 'เสร็จแล้ว',
            default => $task->status,
        };

        $priorityLabel = match ($task->priority) {
            'low' => 'ต่ำ',
            'medium' => 'กลาง',
            'high' => 'สูง',
            default => $task->priority,
        };

        $statusBadge = match ($task->status) {
            'todo' => 'bg-white/10 text-white ring-white/15',
            'in_progress' => 'bg-cyan-400/15 text-cyan-100 ring-cyan-200/20',
            'done' => 'bg-emerald-400/15 text-emerald-100 ring-emerald-200/20',
            default => 'bg-white/10 text-white ring-white/15',
        };

        $priorityBadge = match ($task->priority) {
            'low' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'medium' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'high' => 'bg-rose-50 text-rose-700 ring-rose-200',
            default => 'bg-slate-50 text-slate-700 ring-slate-200',
        };

        $projectStatusLabel = match ($project->status) {
            'active' => 'กำลังดำเนินการ',
            'on_hold' => 'พักงาน',
            'completed' => 'เสร็จสมบูรณ์',
            default => $project->status,
        };

        $taskCategoryLabel = \App\Models\Task::taskCategoryLabel($task->task_category);
        $checklistCount = (int) ($task->checklists_count ?? $task->checklists->count());
        $doneChecklistCount = (int) ($task->done_checklists_count ?? $task->checklists->where('is_done', true)->count());
        $commentCount = (int) ($task->comments_count ?? $task->comments->count());
        $checklistProgress = $checklistCount > 0 ? (int) round(($doneChecklistCount / $checklistCount) * 100) : 0;
        $isOverdue = $task->due_date?->isPast() && $task->status !== 'done';
    @endphp

    <div class="rounded-[2rem] border border-slate-200 bg-[radial-gradient(circle_at_top_left,rgba(14,165,233,.16),transparent_30%),radial-gradient(circle_at_top_right,rgba(34,197,94,.11),transparent_28%),linear-gradient(180deg,#f8fbff_0%,#eef3f9_100%)] shadow-[0_24px_80px_rgba(15,23,42,.08)]">
        <div class="px-4 pb-4 pt-4 sm:px-6 lg:px-8 lg:pb-8 lg:pt-8">
            <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-[linear-gradient(135deg,rgba(2,6,23,.97),rgba(15,23,42,.94)_42%,rgba(8,47,73,.96)_100%)] text-white shadow-[0_30px_90px_rgba(2,8,23,.35)]">
                <div class="grid gap-0 xl:grid-cols-[1.15fr_.85fr]">
                    <div class="p-5 sm:p-6 lg:p-8">
                        <p class="text-xs font-bold uppercase tracking-[0.35em] text-cyan-200/80">Task tracking</p>
                        <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">
                            {{ $task->title }}
                        </h1>
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300 sm:text-base">
                            หน้าติดตามงานรายชิ้น แยกจาก Project detail เพื่อดูสถานะ ความคืบหน้า checklist และคอมเมนต์ได้ชัดกว่าเดิม
                        </p>

                        <div class="mt-5 flex flex-wrap gap-2 text-sm font-semibold">
                            <span class="rounded-full {{ $statusBadge }} px-3 py-1 ring-1 ring-inset">{{ $statusLabel }}</span>
                            <span class="rounded-full {{ $priorityBadge }} px-3 py-1 ring-1 ring-inset">{{ $priorityLabel }}</span>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-white ring-1 ring-inset ring-white/10">
                                {{ $taskCategoryLabel }}
                            </span>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('projects.show', $project) }}#tasks" class="rounded-full bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">
                                กลับไปหน้าโครงการ
                            </a>
                            @can('manage-tasks')
                                <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="rounded-full bg-white/10 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-inset ring-white/10 transition hover:bg-white/15">
                                    แก้ไขงาน
                                </a>
                            @endcan
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
                                <p class="mt-1 text-sm text-slate-300">สถานะโครงการ: {{ $projectStatusLabel }}</p>
                            </div>
                            <div class="rounded-[1.4rem] border border-white/10 bg-white/5 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Deadline</p>
                                <p class="mt-2 text-lg font-black {{ $isOverdue ? 'text-rose-300' : 'text-white' }}">
                                    {{ $task->due_date?->format('d/m/Y') ?? 'ไม่ระบุ' }}
                                </p>
                                <p class="mt-1 text-sm {{ $isOverdue ? 'text-rose-200' : 'text-slate-300' }}">
                                    {{ $isOverdue ? 'เกินกำหนดแล้ว' : 'ยังอยู่ในกำหนด' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 pb-4 sm:px-6 lg:px-8 lg:pb-8">
            <div class="grid gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">
                <aside class="space-y-4 xl:sticky xl:top-6 xl:self-start">
                    <section class="overflow-hidden rounded-[1.7rem] border border-slate-200 bg-white shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                        <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-cyan-950 px-5 py-5 text-white">
                            <p class="text-xs font-bold uppercase tracking-[0.3em] text-cyan-200/80">Quick status</p>
                            <h2 class="mt-2 text-2xl font-black tracking-tight">{{ $task->title }}</h2>
                            <p class="mt-2 text-sm text-slate-300">สรุปข้อมูลสำคัญของงานนี้</p>
                        </div>

                        <div class="space-y-3 p-5">
                            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                                <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">สถานะ</p>
                                    <p class="mt-2 text-sm font-bold text-slate-900">{{ $statusLabel }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">ความสำคัญ</p>
                                    <p class="mt-2 text-sm font-bold text-slate-900">{{ $priorityLabel }}</p>
                                </div>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                                <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">เริ่ม</p>
                                    <p class="mt-2 text-sm font-bold text-slate-900">{{ $task->start_date?->format('d/m/Y') ?? 'ไม่ระบุ' }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">ครบกำหนด</p>
                                    <p class="mt-2 text-sm font-bold {{ $isOverdue ? 'text-rose-700' : 'text-slate-900' }}">
                                        {{ $task->due_date?->format('d/m/Y') ?? 'ไม่ระบุ' }}
                                    </p>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white px-4 py-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">ลำดับงาน</p>
                                <p class="mt-2 text-sm font-bold text-slate-900">{{ number_format($task->sort_order ?? 0) }}</p>
                                <p class="mt-1 text-sm text-slate-600">ในรายการงานของโปรเจกต์</p>
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
                </aside>

                <section class="space-y-6">
                    <section class="rounded-[1.9rem] border border-slate-200 bg-white shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                        <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Overview</p>
                            <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950">ภาพรวมการติดตามงาน</h2>
                        </div>

                        <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-4">
                            <div class="rounded-[1.4rem] border border-slate-200 bg-slate-50 p-4">
                                <p class="text-sm font-semibold text-slate-500">Checklist</p>
                                <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($checklistCount) }}</div>
                                <p class="mt-2 text-sm text-slate-500">รายการทั้งหมด</p>
                            </div>
                            <div class="rounded-[1.4rem] border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-4">
                                <p class="text-sm font-semibold text-emerald-700">เสร็จแล้ว</p>
                                <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($doneChecklistCount) }}</div>
                                <p class="mt-2 text-sm text-slate-500">รายการที่ปิดแล้ว</p>
                            </div>
                            <div class="rounded-[1.4rem] border border-sky-200 bg-gradient-to-br from-sky-50 to-white p-4">
                                <p class="text-sm font-semibold text-sky-700">ความคืบหน้า</p>
                                <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($checklistProgress) }}%</div>
                                <p class="mt-2 text-sm text-slate-500">จาก checklist ทั้งหมด</p>
                            </div>
                            <div class="rounded-[1.4rem] border border-violet-200 bg-gradient-to-br from-violet-50 to-white p-4">
                                <p class="text-sm font-semibold text-violet-700">คอมเมนต์</p>
                                <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($commentCount) }}</div>
                                <p class="mt-2 text-sm text-slate-500">บันทึกการติดตามงาน</p>
                            </div>
                        </div>

                        <div class="px-5 pb-5 sm:px-6">
                            <div class="h-3 overflow-hidden rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-gradient-to-r from-sky-600 via-cyan-500 to-emerald-500" style="width: {{ $checklistProgress }}%"></div>
                            </div>
                            <div class="mt-2 flex items-center justify-between text-sm text-slate-500">
                                <span>Checklist progress</span>
                                <span>{{ $checklistProgress }}%</span>
                            </div>
                        </div>
                    </section>

                    <section class="grid gap-6 xl:grid-cols-[1.05fr_.95fr]">
                        <div class="rounded-[1.9rem] border border-slate-200 bg-white shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Description</p>
                                <h3 class="mt-2 text-2xl font-black text-slate-950">รายละเอียดงาน</h3>
                            </div>
                            <div class="px-5 py-5 sm:px-6">
                                <p class="text-sm leading-7 text-slate-700">
                                    {{ $task->description ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}
                                </p>
                            </div>
                        </div>

                        <div class="rounded-[1.9rem] border border-slate-200 bg-white shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Timeline</p>
                                <h3 class="mt-2 text-2xl font-black text-slate-950">เส้นเวลางาน</h3>
                            </div>

                            <div class="space-y-4 px-5 py-5 sm:px-6">
                                <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">สร้างเมื่อ</p>
                                    <p class="mt-2 text-sm font-bold text-slate-900">{{ $task->created_at?->format('d/m/Y H:i') ?? 'ไม่ระบุ' }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">อัปเดตล่าสุด</p>
                                    <p class="mt-2 text-sm font-bold text-slate-900">{{ $task->updated_at?->format('d/m/Y H:i') ?? 'ไม่ระบุ' }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">เสร็จจริงเมื่อ</p>
                                    <p class="mt-2 text-sm font-bold text-slate-900">{{ $task->completed_at?->format('d/m/Y H:i') ?? 'ยังไม่มี' }}</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="grid gap-6 xl:grid-cols-[1fr_.95fr]">
                        <div class="rounded-[1.9rem] border border-slate-200 bg-white shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                            <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-5 sm:px-6">
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Checklist</p>
                                    <h3 class="mt-2 text-2xl font-black text-slate-950">รายการย่อยของงาน</h3>
                                </div>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-inset ring-slate-200">
                                    {{ number_format($checklistProgress) }}%
                                </span>
                            </div>

                            <div class="space-y-3 px-5 py-5 sm:px-6">
                                @forelse ($task->checklists as $item)
                                    <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                        <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ $item->is_done ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500' }}">
                                            @if ($item->is_done)
                                                <span class="text-xs font-black">✓</span>
                                            @else
                                                <span class="text-xs font-black">•</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold {{ $item->is_done ? 'text-slate-500 line-through' : 'text-slate-900' }}">
                                                {{ $item->title }}
                                            </p>
                                            <p class="mt-1 text-xs text-slate-500">
                                                ลำดับ {{ number_format($item->sort_order ?? 0) }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-slate-500">
                                        ยังไม่มี checklist สำหรับงานนี้
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="rounded-[1.9rem] border border-slate-200 bg-white shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Comments</p>
                                <h3 class="mt-2 text-2xl font-black text-slate-950">บันทึกการติดตาม</h3>
                            </div>

                            <div class="space-y-4 px-5 py-5 sm:px-6">
                                @forelse ($task->comments as $comment)
                                    <article class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                        <div class="flex items-start gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-black text-white">
                                                {{ mb_substr($comment->user?->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="text-sm font-bold text-slate-900">{{ $comment->user?->name ?? 'ไม่ระบุผู้เขียน' }}</p>
                                                    <span class="text-xs text-slate-500">{{ $comment->created_at?->format('d/m/Y H:i') ?? '' }}</span>
                                                </div>
                                                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $comment->body }}</p>
                                            </div>
                                        </div>
                                    </article>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-slate-500">
                                        ยังไม่มี comment สำหรับงานนี้
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[1.9rem] border border-slate-200 bg-white shadow-[0_16px_50px_rgba(15,23,42,.08)]">
                        <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Quick actions</p>
                                <h3 class="mt-2 text-2xl font-black text-slate-950">ลิงก์ใช้งานเร็ว</h3>
                            </div>
                            @can('manage-tasks')
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90">
                                        แก้ไขงาน
                                    </a>
                                    <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $task]) }}" onsubmit="return confirm('ต้องการลบงานนี้ใช่ไหม?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-full bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 ring-1 ring-inset ring-rose-200 transition hover:bg-rose-100">
                                            ลบงาน
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </div>

                        <div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-4 sm:p-6">
                            <a href="{{ route('projects.show', $project) }}#overview" class="rounded-2xl bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                ภาพรวมโครงการ
                            </a>
                            <a href="{{ route('projects.show', $project) }}#tasks" class="rounded-2xl bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                รายการงานทั้งหมด
                            </a>
                            <a href="{{ route('projects.show', $project) }}#notes" class="rounded-2xl bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                หมายเหตุโปรเจกต์
                            </a>
                            <a href="{{ route('projects.index') }}" class="rounded-2xl bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                กลับหน้า Project list
                            </a>
                        </div>
                    </section>
                </section>
            </div>
        </div>
    </div>
</x-frontend-layout>
