<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - ภาพรวมคำร้อง"
    description="หน้า queue สำหรับ IT ใช้รับงานหรือปฏิเสธคำร้อง"
    active="requests-queue"
>
    @php
        $statusLabels = [
            'pending' => 'รอพิจารณา',
            'approved' => 'อนุมัติแล้ว',
            'rejected' => 'ไม่อนุมัติ',
        ];

        $statusStyles = [
            'pending' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'rejected' => 'bg-rose-50 text-rose-700 ring-rose-200',
        ];

        $requestTypeLabels = [
            'add' => 'เพิ่มระบบ',
            'improve' => 'ปรับปรุง',
            'bug' => 'แก้ไขบั๊ก',
            'other' => 'อื่น ๆ',
        ];

        $priorityStyles = [
            'low' => 'bg-slate-100 text-slate-600',
            'medium' => 'bg-sky-50 text-sky-700',
            'high' => 'bg-orange-50 text-orange-700',
            'urgent' => 'bg-rose-50 text-rose-700',
        ];
    @endphp

    <div class="rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] backdrop-blur xl:p-7">
        @if (session('success'))
            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Request Queue</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                    ภาพรวมของการร้องขอ
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                    หน้านี้สำหรับ IT ตรวจสอบคำร้องเข้ามา ดูข้อมูลสั้น ๆ แล้วกดรับงานหรือปฏิเสธได้ทันที
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('requests.index') }}" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                    ไปหน้าฟอร์มคำร้อง
                </a>
                <a href="#pending-queue" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                    ดูคำร้องรอพิจารณา
                </a>
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-[1.4rem] border border-slate-200 bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ทั้งหมด</p>
                <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($summary['total']) }}</div>
            </div>
            <div class="rounded-[1.4rem] border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-500">รอพิจารณา</p>
                <div class="mt-3 text-3xl font-black text-amber-700">{{ number_format($summary['pending']) }}</div>
            </div>
            <div class="rounded-[1.4rem] border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-500">อนุมัติแล้ว</p>
                <div class="mt-3 text-3xl font-black text-emerald-700">{{ number_format($summary['approved']) }}</div>
            </div>
            <div class="rounded-[1.4rem] border border-rose-200 bg-rose-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-rose-500">ไม่อนุมัติ</p>
                <div class="mt-3 text-3xl font-black text-rose-700">{{ number_format($summary['rejected']) }}</div>
            </div>
            <div class="rounded-[1.4rem] border border-sky-200 bg-sky-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-500">แปลงเป็น Project</p>
                <div class="mt-3 text-3xl font-black text-sky-700">{{ number_format($summary['with_project']) }}</div>
            </div>
        </div>

        <div class="mt-6 rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
            <form method="GET" action="{{ route('requests.queue') }}" class="grid gap-3 lg:grid-cols-[1fr_220px_auto]">
                <input
                    type="text"
                    name="q"
                    value="{{ $filters['q'] }}"
                    placeholder="ค้นหาด้วยชื่อ, หัวข้อ, แผนก, อีเมล..."
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                >
                <select name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                    <option value="all" @selected($filters['status'] === 'all')>ทุกสถานะ</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                    ค้นหา
                </button>
            </form>
        </div>

        <div id="pending-queue" class="mt-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Queue</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-950">
                        {{ $filters['status'] === 'pending' ? 'คำร้องรอการตัดสินใจ' : 'รายการคำร้อง' }}
                    </h2>
                </div>
                <p class="text-sm text-slate-500">{{ number_format($requests->count()) }} รายการ</p>
            </div>

            @if ($requests->isEmpty())
                <div class="rounded-[1.8rem] border border-dashed border-slate-200 bg-slate-50 p-10 text-center">
                    <p class="text-lg font-bold text-slate-900">ไม่พบคำร้องตามเงื่อนไขที่เลือก</p>
                    <p class="mt-2 text-sm text-slate-500">ลองเปลี่ยนคำค้นหรือสถานะที่กรองอยู่</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($requests as $requestItem)
                        <div class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                <div class="max-w-4xl space-y-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $statusStyles[$requestItem->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                            {{ $statusLabels[$requestItem->status] ?? $requestItem->status }}
                                        </span>
                                        <span class="rounded-full {{ $priorityStyles[$requestItem->priority] ?? 'bg-slate-100 text-slate-600' }} px-3 py-1 text-xs font-bold">
                                            {{ strtoupper($requestItem->priority) }}
                                        </span>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                            {{ $requestTypeLabels[$requestItem->request_type] ?? $requestItem->request_type }}
                                        </span>
                                        @if ($requestItem->project)
                                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                มี Project แล้ว
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:gap-3">
                                        <div>
                                            <h3 class="text-2xl font-black text-slate-950">{{ $requestItem->title }}</h3>
                                            <p class="mt-1 text-sm text-slate-500">
                                                #{{ $requestItem->id }} · {{ $requestItem->requester_name }} · {{ $requestItem->company?->name ?? 'ไม่ระบุบริษัท' }}
                                            </p>
                                        </div>
                                        <p class="text-sm text-slate-500 sm:pb-1">
                                            {{ $requestItem->created_at?->format('d/m/Y H:i') }}
                                        </p>
                                    </div>

                                    <div class="grid gap-3 md:grid-cols-3">
                                        <div class="rounded-2xl bg-slate-50 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ผู้ร้องขอ</p>
                                            <p class="mt-2 text-sm font-bold text-slate-950">{{ $requestItem->requester_name }}</p>
                                            <p class="mt-1 text-sm text-slate-500">{{ $requestItem->requester_email ?: 'ไม่ระบุ' }}</p>
                                        </div>
                                        <div class="rounded-2xl bg-slate-50 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">หน่วยงาน</p>
                                            <p class="mt-2 text-sm font-bold text-slate-950">{{ $requestItem->department ?: 'ไม่ระบุ' }}</p>
                                            <p class="mt-1 text-sm text-slate-500">{{ $requestItem->position ?: 'ไม่ระบุตำแหน่ง' }}</p>
                                        </div>
                                        <div class="rounded-2xl bg-slate-50 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ไฟล์แนบ</p>
                                            <p class="mt-2 text-sm font-bold text-slate-950">{{ number_format($requestItem->attachments->count()) }} ไฟล์</p>
                                            <p class="mt-1 text-sm text-slate-500">ดูรายละเอียดได้จากหน้าเอกสาร</p>
                                        </div>
                                    </div>

                                    <div class="grid gap-3 xl:grid-cols-[1.2fr_1fr]">
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">วัตถุประสงค์</p>
                                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                                                {{ \Illuminate\Support\Str::limit($requestItem->objective ?: 'ไม่มีข้อมูลวัตถุประสงค์', 240) }}
                                            </p>
                                        </div>
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">รายละเอียด</p>
                                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                                                {{ \Illuminate\Support\Str::limit($requestItem->details ?: 'ไม่มีรายละเอียดเพิ่มเติม', 240) }}
                                            </p>
                                        </div>
                                    </div>

                                    @if ($requestItem->attachments->isNotEmpty())
                                        <div class="rounded-2xl bg-slate-50 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ไฟล์แนบล่าสุด</p>
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @foreach ($requestItem->attachments->take(3) as $attachment)
                                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                                                        {{ $attachment->original_name }}
                                                    </span>
                                                @endforeach
                                                @if ($requestItem->attachments->count() > 3)
                                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-500 ring-1 ring-inset ring-slate-200">
                                                        +{{ $requestItem->attachments->count() - 3 }} ไฟล์
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="w-full xl:max-w-[380px]">
                                        <div class="rounded-[1.4rem] border border-slate-200 bg-slate-50 p-4">
                                            <div class="flex items-center justify-between gap-3">
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">การตัดสินใจ</p>
                                                    <p class="mt-1 text-sm font-bold text-slate-900">รับงานหรือปฏิเสธ</p>
                                                </div>
                                                <a href="{{ route('requests.show', $requestItem) }}" class="text-sm font-semibold text-sky-700 hover:underline">
                                                    เปิดรายละเอียด
                                                </a>
                                            </div>

                                        @if ($requestItem->status === 'pending')
                                            @can('manage-requests')
                                                <form method="POST" action="{{ route('requests.approve', $requestItem) }}" class="mt-4 space-y-3">
                                                    @csrf
                                                    <div>
                                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ผู้รับงาน</label>
                                                        <select name="project_owner_user_id" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                                            <option value="">เลือกเจ้าของ Project</option>
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-2">
                                                        <input type="date" name="project_start_date" class="rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100">
                                                        <input type="date" name="project_due_date" class="rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100">
                                                    </div>

                                                    <textarea name="decision_note" rows="3" placeholder="หมายเหตุการรับงาน" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100"></textarea>

                                                    <button type="submit" class="w-full rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                                                        รับงาน
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('requests.reject', $requestItem) }}" class="mt-3 space-y-3">
                                                    @csrf
                                                    <textarea name="decision_note" rows="3" placeholder="เหตุผลที่ไม่รับงาน" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100"></textarea>
                                                    <button type="submit" class="w-full rounded-2xl bg-rose-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-rose-700">
                                                        ไม่รับงาน
                                                    </button>
                                                </form>

                                                <div class="mt-3 grid grid-cols-2 gap-2">
                                                    <a href="{{ route('requests.index', ['edit' => $requestItem->id]) }}" class="rounded-2xl bg-sky-50 px-4 py-3 text-center text-sm font-semibold text-sky-700 ring-1 ring-inset ring-sky-200 transition hover:bg-sky-100">
                                                        แก้ไข
                                                    </a>
                                                    <form
                                                        method="POST"
                                                        action="{{ route('requests.destroy', $requestItem) }}"
                                                        onsubmit="return confirm('ต้องการลบคำร้องนี้ใช่ไหม?')"
                                                    >
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full rounded-2xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 ring-1 ring-inset ring-rose-200 transition hover:bg-rose-100">
                                                            ลบ
                                                        </button>
                                                    </form>
                                                </div>

                                            @endcan
                                        @else
                                            <div class="mt-4 space-y-2">
                                                <div class="rounded-2xl bg-white px-4 py-3 text-sm text-slate-600 ring-1 ring-inset ring-slate-200">
                                                    <span class="font-semibold text-slate-900">สถานะ:</span> {{ $statusLabels[$requestItem->status] ?? $requestItem->status }}
                                                </div>
                                                @if ($requestItem->project)
                                                    <a href="{{ route('projects.show', $requestItem->project) }}" class="block rounded-2xl bg-emerald-50 px-4 py-3 text-center text-sm font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200 transition hover:bg-emerald-100">
                                                        เปิด Project ที่สร้างแล้ว
                                                    </a>
                                                @endif
                                                @can('manage-requests')
                                                    <div class="grid grid-cols-2 gap-2 pt-1">
                                                        <a href="{{ route('requests.index', ['edit' => $requestItem->id]) }}" class="rounded-2xl bg-sky-50 px-4 py-3 text-center text-sm font-semibold text-sky-700 ring-1 ring-inset ring-sky-200 transition hover:bg-sky-100">
                                                            แก้ไข
                                                        </a>
                                                        <form
                                                            method="POST"
                                                            action="{{ route('requests.destroy', $requestItem) }}"
                                                            onsubmit="return confirm('ต้องการลบคำร้องนี้ใช่ไหม?')"
                                                        >
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="w-full rounded-2xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 ring-1 ring-inset ring-rose-200 transition hover:bg-rose-100">
                                                                ลบ
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endcan
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-frontend-layout>
