<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - รายละเอียดคำร้อง"
    description="เอกสารสรุปคำร้องอนุมัติระบบ"
    active="requests"
>
    <style>
        @media print {
            body {
                background: #fff !important;
            }

            .print-hide {
                display: none !important;
            }

            .print-paper,
            .print-card {
                box-shadow: none !important;
                background: #fff !important;
                border-color: #111827 !important;
            }
        }
    </style>

    @php
        $requestTypeLabels = [
            'add' => 'เพิ่มระบบ',
            'improve' => 'ปรับปรุง',
            'bug' => 'แก้ไขบั๊ก',
            'other' => 'อื่น ๆ',
        ];

        $priorityLabels = [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];

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
    @endphp

    <div class="print-paper rounded-[2rem] border border-slate-300 bg-white p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] xl:p-7">
        <div class="border-b border-slate-300 pb-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-4xl">
                    <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">
                        <span>QF-ITC-0003</span>
                        <span class="h-1 w-1 rounded-full bg-slate-400"></span>
                        <span>แบบคำร้องขอเพิ่มระบบและแก้ไขปรับปรุง</span>
                    </div>

                    <div class="mt-4 flex items-center gap-4">
                        <div class="grid h-16 w-16 place-items-center rounded-full border border-slate-300 bg-slate-50 text-lg font-black text-slate-900">
                            IT
                        </div>
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Document View</p>
                            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                                รายละเอียดคำร้องเพื่ออนุมัติ
                            </h1>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                                หน้านี้ใช้เป็นเอกสารสรุปคำร้องสำหรับ IT พิจารณา ตรวจทาน และอ้างอิงก่อนแปลงเป็น Project
                            </p>
                        </div>
                    </div>
                </div>

                <div class="print-hide flex flex-wrap gap-2">
                    <a href="{{ route('requests.index') }}" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                        กลับรายการ
                    </a>
                    @can('manage-requests')
                        <a href="{{ route('requests.index', ['edit' => $requestItem->id]) }}" class="rounded-2xl bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-700 ring-1 ring-inset ring-sky-200 transition hover:bg-sky-100">
                            แก้ไข
                        </a>
                        <form
                            method="POST"
                            action="{{ route('requests.destroy', $requestItem) }}"
                            onsubmit="return confirm('ต้องการลบคำร้องนี้ใช่ไหม?')"
                            class="inline-flex"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-2xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 ring-1 ring-inset ring-rose-200 transition hover:bg-rose-100">
                                ลบ
                            </button>
                        </form>
                    @endcan
                    @if ($requestItem->project)
                        <a href="{{ route('projects.show', $requestItem->project) }}" class="rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200 transition hover:bg-emerald-100">
                            เปิด Project
                        </a>
                    @endif
                    <button onclick="window.print()" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                        พิมพ์
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-5 grid gap-4 border-b border-slate-200 pb-5 sm:grid-cols-2 xl:grid-cols-4">
            <div class="print-card rounded-[1.4rem] border border-slate-300 bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">สถานะ</p>
                <div class="mt-3">
                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $statusStyles[$requestItem->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                        {{ $statusLabels[$requestItem->status] ?? $requestItem->status }}
                    </span>
                </div>
            </div>
            <div class="print-card rounded-[1.4rem] border border-slate-300 bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ประเภท</p>
                <div class="mt-3 text-xl font-black text-slate-950">{{ $requestTypeLabels[$requestItem->request_type] ?? $requestItem->request_type }}</div>
            </div>
            <div class="print-card rounded-[1.4rem] border border-slate-300 bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ความสำคัญ</p>
                <div class="mt-3 text-xl font-black text-slate-950">{{ $priorityLabels[$requestItem->priority] ?? $requestItem->priority }}</div>
            </div>
            <div class="print-card rounded-[1.4rem] border border-slate-300 bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Project</p>
                <div class="mt-3 text-xl font-black text-slate-950">{{ $requestItem->project ? 'เชื่อมแล้ว' : 'ยังไม่เชื่อม' }}</div>
            </div>
        </div>

        <div class="mt-6 space-y-6">
            <section class="space-y-4">
                <div class="print-card rounded-none border border-slate-300 bg-white p-0">
                    <div class="border-b border-slate-300 px-5 py-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-2xl font-black text-slate-950">{{ $requestItem->title }}</h2>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                                เลขคำร้อง #{{ $requestItem->id }}
                            </span>
                        </div>
                    </div>

                    <div class="grid gap-0 sm:grid-cols-2">
                        <div class="border-b border-r border-slate-300 p-5 sm:border-b-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ผู้ร้องขอ</p>
                            <p class="mt-2 text-sm font-bold text-slate-950">{{ $requestItem->requester_name }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $requestItem->requester_email ?: 'ไม่ระบุ' }}</p>
                        </div>
                        <div class="border-b border-slate-300 p-5 sm:border-b-0 sm:border-r">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">หน่วยงาน</p>
                            <p class="mt-2 text-sm font-bold text-slate-950">{{ $requestItem->department ?: 'ไม่ระบุ' }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $requestItem->position ?: 'ไม่ระบุตำแหน่ง' }}</p>
                        </div>
                        <div class="border-b border-r border-slate-300 p-5 sm:border-b-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">บริษัท</p>
                            <p class="mt-2 text-sm font-bold text-slate-950">{{ $requestItem->company?->name ?? 'ไม่ระบุ' }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $requestItem->phone ?: 'ไม่มีเบอร์โทร' }}</p>
                        </div>
                        <div class="border-b border-slate-300 p-5 sm:border-b-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">วันที่ต้องการ</p>
                            <p class="mt-2 text-sm font-bold text-slate-950">{{ $requestItem->target_start_date?->format('d/m/Y') ?? 'ไม่ระบุ' }}</p>
                            <p class="mt-1 text-sm text-slate-500">ถึง {{ $requestItem->target_due_date?->format('d/m/Y') ?? 'ไม่ระบุ' }}</p>
                        </div>
                    </div>
                </div>

                <div class="print-card rounded-none border border-slate-300 bg-white p-5">
                    <h3 class="text-lg font-black text-slate-950">วัตถุประสงค์</h3>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">
                        {{ $requestItem->objective ?: 'ไม่มีข้อมูลวัตถุประสงค์' }}
                    </p>
                </div>

                <div class="print-card rounded-none border border-slate-300 bg-white p-5">
                    <h3 class="text-lg font-black text-slate-950">รายละเอียดความต้องการ</h3>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">
                        {{ $requestItem->details ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}
                    </p>
                </div>

                <div class="print-card rounded-none border border-slate-300 bg-white p-5">
                    <h3 class="text-lg font-black text-slate-950">ผลกระทบ / ความเสี่ยง</h3>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">
                        {{ $requestItem->impact ?: 'ไม่มีข้อมูลความเสี่ยง' }}
                    </p>
                </div>

                <div class="print-card rounded-none border border-slate-300 bg-white p-5">
                    <h3 class="text-lg font-black text-slate-950">ผลลัพธ์ที่คาดหวัง</h3>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">
                        {{ $requestItem->desired_output ?: 'ไม่มีข้อมูลผลลัพธ์ที่คาดหวัง' }}
                    </p>
                </div>

                @if ($requestItem->reference_url)
                    <div class="print-card rounded-none border border-slate-300 bg-white p-5">
                        <h3 class="text-lg font-black text-slate-950">Reference</h3>
                        <a href="{{ $requestItem->reference_url }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex break-all text-sm font-semibold text-sky-700 hover:underline">
                            {{ $requestItem->reference_url }}
                        </a>
                    </div>
                @endif

                @if ($requestItem->attachments->isNotEmpty())
                    <div class="print-card rounded-none border border-slate-300 bg-white p-5">
                        <h3 class="text-lg font-black text-slate-950">เอกสารแนบ</h3>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($requestItem->attachments as $attachment)
                                @php
                                    $attachmentUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($attachment->path);
                                @endphp

                                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:border-slate-300 hover:bg-slate-100">
                                    @can('manage-requests')
                                        <form
                                            method="POST"
                                            action="{{ route('requests.attachments.destroy', ['systemRequest' => $requestItem, 'attachment' => $attachment]) }}"
                                            class="absolute right-3 top-3 z-10"
                                            onsubmit="return confirm('ต้องการลบไฟล์แนบนี้ใช่ไหม?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="grid h-9 w-9 place-items-center rounded-full border border-rose-200 bg-white/95 text-rose-500 shadow-sm transition hover:bg-rose-50 hover:text-rose-600"
                                                aria-label="ลบไฟล์แนบ"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4">
                                                    <path d="M3 6h18" stroke-linecap="round" />
                                                    <path d="M8 6V4h8v2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M6 6l1 14h10l1-14" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M10 11v5M14 11v5" stroke-linecap="round" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan

                                    @if ($attachment->isImage())
                                        <button
                                            type="button"
                                            data-attachment-preview
                                            data-attachment-url="{{ $attachmentUrl }}"
                                            data-attachment-name="{{ $attachment->original_name }}"
                                            class="block w-full text-left"
                                        >
                                            <div class="aspect-[4/3] overflow-hidden rounded-xl border border-slate-200 bg-white">
                                                <img
                                                    src="{{ $attachmentUrl }}"
                                                    alt="{{ $attachment->original_name }}"
                                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]"
                                                >
                                            </div>

                                            <div class="mt-3 pr-10">
                                                <p class="text-sm font-bold text-slate-950">{{ $attachment->original_name }}</p>
                                                <p class="mt-1 text-xs text-slate-500">คลิกเพื่อดูภาพแบบขยาย</p>
                                                <p class="mt-2 text-xs text-slate-500">
                                                    {{ strtoupper(pathinfo($attachment->original_name, PATHINFO_EXTENSION) ?: 'FILE') }}
                                                    · {{ number_format((int) ($attachment->size_bytes / 1024)) }} KB
                                                </p>
                                            </div>
                                        </button>
                                    @else
                                        <a
                                            href="{{ $attachmentUrl }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="block"
                                        >
                                            <div class="flex aspect-[4/3] items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-12 w-12">
                                                    <path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M14 2v5h5" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </div>

                                            <div class="mt-3 pr-10">
                                                <p class="text-sm font-bold text-slate-950">{{ $attachment->original_name }}</p>
                                                <p class="mt-1 text-xs text-slate-500">
                                                    เปิดไฟล์ในแท็บใหม่
                                                </p>
                                                <p class="mt-2 text-xs text-slate-500">
                                                    {{ strtoupper(pathinfo($attachment->original_name, PATHINFO_EXTENSION) ?: 'FILE') }}
                                                    · {{ number_format((int) ($attachment->size_bytes / 1024)) }} KB
                                                </p>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

            <section class="grid gap-6 print-hide xl:grid-cols-2">
                <div class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <h3 class="text-lg font-black text-slate-950">การดำเนินการ</h3>

                    <div class="mt-4 space-y-3">
                        @if ($requestItem->project)
                            <a href="{{ route('projects.show', $requestItem->project) }}" class="block rounded-2xl bg-emerald-50 px-4 py-3 text-center text-sm font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200 transition hover:bg-emerald-100">
                                เปิด Project ที่สร้างแล้ว
                            </a>
                        @endif

                        @can('manage-requests')
                            @if ($requestItem->status === 'pending')
                                <form method="POST" action="{{ route('requests.approve', $requestItem) }}" class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    @csrf
                                    <p class="text-sm font-bold text-slate-950">อนุมัติและสร้าง Project</p>
                                    <select name="project_owner_user_id" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                        <option value="">เลือกเจ้าของ Project</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ \App\Models\User::roleLabel($user->role) }})</option>
                                        @endforeach
                                    </select>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="date" name="project_start_date" class="rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100">
                                        <input type="date" name="project_due_date" class="rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100">
                                    </div>
                                    <textarea name="decision_note" rows="3" placeholder="หมายเหตุการอนุมัติ" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100"></textarea>
                                    <button type="submit" class="w-full rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                                        อนุมัติ + สร้าง Project
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('requests.reject', $requestItem) }}" class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                @csrf
                                <p class="text-sm font-bold text-slate-950">บันทึกผลพิจารณา</p>
                                <textarea name="decision_note" rows="3" placeholder="เหตุผล / หมายเหตุ" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100">{{ $requestItem->decision_note }}</textarea>
                                <button type="submit" class="w-full rounded-2xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-700">
                                    ปฏิเสธ
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>

                <div class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <h3 class="text-lg font-black text-slate-950">ประวัติการอนุมัติ</h3>

                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">อนุมัติโดย</p>
                            <p class="mt-1 font-semibold text-slate-950">{{ $requestItem->approvedBy?->name ?? 'ยังไม่มี' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">วันที่อนุมัติ</p>
                            <p class="mt-1 font-semibold text-slate-950">{{ $requestItem->approved_at?->format('d/m/Y H:i') ?? 'ยังไม่มี' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ปฏิเสธโดย</p>
                            <p class="mt-1 font-semibold text-slate-950">{{ $requestItem->rejectedBy?->name ?? 'ยังไม่มี' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">วันที่ปฏิเสธ</p>
                            <p class="mt-1 font-semibold text-slate-950">{{ $requestItem->rejected_at?->format('d/m/Y H:i') ?? 'ยังไม่มี' }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div id="attachment-lightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/85 p-4">
        <div class="absolute inset-0" data-lightbox-close></div>
        <div class="relative z-10 w-full max-w-6xl">
            <div class="mb-3 flex items-center justify-between gap-4 text-white">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/60">Preview</p>
                    <h3 id="attachment-lightbox-name" class="mt-1 text-lg font-bold"></h3>
                </div>
                <button
                    type="button"
                    data-lightbox-close
                    class="grid h-11 w-11 place-items-center rounded-full border border-white/20 bg-white/10 text-white transition hover:bg-white/20"
                    aria-label="ปิดพรีวิว"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                        <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <div class="overflow-hidden rounded-[1.5rem] border border-white/10 bg-black/20 shadow-[0_30px_100px_rgba(0,0,0,.45)]">
                <img id="attachment-lightbox-image" src="" alt="" class="max-h-[82vh] w-full object-contain">
            </div>
        </div>
    </div>

    <script>
        (() => {
            const lightbox = document.getElementById('attachment-lightbox');
            const image = document.getElementById('attachment-lightbox-image');
            const name = document.getElementById('attachment-lightbox-name');
            const openButtons = document.querySelectorAll('[data-attachment-preview]');
            const closeButtons = document.querySelectorAll('[data-lightbox-close]');

            const close = () => {
                lightbox.classList.add('hidden');
                lightbox.classList.remove('flex');
                image.src = '';
                image.alt = '';
                name.textContent = '';
            };

            const open = (url, label) => {
                image.src = url;
                image.alt = label;
                name.textContent = label;
                lightbox.classList.remove('hidden');
                lightbox.classList.add('flex');
            };

            openButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    open(button.dataset.attachmentUrl, button.dataset.attachmentName || 'Preview');
                });
            });

            closeButtons.forEach((button) => {
                button.addEventListener('click', close);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !lightbox.classList.contains('hidden')) {
                    close();
                }
            });
        })();
    </script>
</x-frontend-layout>
