<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - อบรมพนักงาน"
    description="จัดการตารางอบรมพนักงานเกี่ยวกับการใช้โปรแกรม"
    active="trainings"
>
    @php
        $statusOptions = [
            'scheduled' => 'กำหนดแล้ว',
            'in_progress' => 'กำลังอบรม',
            'completed' => 'เสร็จสิ้น',
            'cancelled' => 'ยกเลิก',
        ];

        $statusTone = [
            'scheduled' => 'bg-sky-50 text-sky-700 ring-sky-200',
            'in_progress' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'cancelled' => 'bg-rose-50 text-rose-700 ring-rose-200',
        ];

        $summaryCards = [
            ['label' => 'รอบอบรมทั้งหมด', 'value' => number_format($summary['total'] ?? 0), 'hint' => 'รายการที่เก็บไว้ในระบบ', 'tone' => 'from-slate-700 to-slate-500'],
            ['label' => 'รออบรม', 'value' => number_format($summary['upcoming'] ?? 0), 'hint' => 'รอบที่กำหนดไว้แล้ว', 'tone' => 'from-sky-600 to-cyan-500'],
            ['label' => 'อบรมเสร็จแล้ว', 'value' => number_format($summary['completed'] ?? 0), 'hint' => 'รอบที่ปิดงานแล้ว', 'tone' => 'from-emerald-600 to-teal-500'],
            ['label' => 'อัตราเข้าร่วม', 'value' => number_format($summary['attendance'] ?? 0) . '%', 'hint' => 'เฉพาะรอบที่เสร็จสิ้น', 'tone' => 'from-violet-600 to-fuchsia-500'],
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
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Training Management</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                    ตารางอบรมพนักงานเกี่ยวกับการใช้โปรแกรม
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                    จัดการประวัติการอบรมได้แบบครบในหน้าเดียว ทั้งเพิ่ม แก้ไข ลบ และค้นหาข้อมูลย้อนหลังได้ทันที
                </p>
            </div>

            <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">สถานะหน้าจอ</p>
                <p class="mt-1 text-lg font-black text-slate-950">{{ $editingTraining ? 'กำลังแก้ไขรอบอบรม' : 'โหมดเพิ่มรอบอบรม' }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $editingTraining?->title ?? 'พร้อมสร้างข้อมูลใหม่' }}</p>
                <a href="{{ route('reports.trainings') }}" class="mt-3 inline-flex rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90">
                    ไปหน้ารายงานอบรม
                </a>
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($summaryCards as $card)
                <div class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-slate-500">{{ $card['label'] }}</p>
                        <span class="inline-flex h-3 w-3 rounded-full bg-gradient-to-r {{ $card['tone'] }}"></span>
                    </div>
                    <div class="mt-3 text-3xl font-black text-slate-950">{{ $card['value'] }}</div>
                    <p class="mt-2 text-sm text-slate-500">{{ $card['hint'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-[420px_minmax(0,1fr)]">
            @can('manage-trainings')
                <section id="training-form" class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">
                                {{ $editingTraining ? 'แก้ไขข้อมูลอบรม' : 'เพิ่มรอบอบรมใหม่' }}
                            </p>
                            <h2 class="mt-2 text-2xl font-black text-slate-950">
                                {{ $editingTraining ? 'อัปเดตรายการอบรม' : 'สร้างรายการอบรม' }}
                            </h2>
                        </div>

                        @if ($editingTraining)
                            <a href="{{ route('trainings.index') }}"
                                class="rounded-full bg-slate-100 px-3 py-2 text-xs font-bold text-slate-600 ring-1 ring-inset ring-slate-200">
                                ยกเลิก
                            </a>
                        @endif
                    </div>

                    @if ($errors->any())
                        <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                            <p class="font-semibold">ตรวจพบข้อมูลไม่ครบ</p>
                            <ul class="mt-2 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="mt-5 space-y-4"
                        method="POST"
                        action="{{ $editingTraining ? route('trainings.update', $editingTraining) : route('trainings.store') }}"
                    >
                        @csrf
                        @if ($editingTraining)
                            @method('PUT')
                        @endif

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">หัวข้ออบรม</label>
                            <input
                                type="text"
                                name="title"
                                value="{{ old('title', optional($editingTraining)->title ?? '') }}"
                                placeholder="เช่น การใช้งานระบบ Project"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                required
                            >
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">วันที่อบรม</label>
                                <input
                                    type="date"
                                    name="training_date"
                                    value="{{ old('training_date', $editingTraining?->training_date?->format('Y-m-d')) }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                    required
                                >
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">สถานะ</label>
                                <select
                                    name="status"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                    required
                                >
                                    @foreach ($statusOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', optional($editingTraining)->status ?? 'scheduled') === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">เวลาเริ่ม</label>
                                <input
                                    type="time"
                                    name="start_time"
                                    value="{{ old('start_time', optional($editingTraining)->start_time ?? '') }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                >
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">เวลาจบ</label>
                                <input
                                    type="time"
                                    name="end_time"
                                    value="{{ old('end_time', optional($editingTraining)->end_time ?? '') }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                >
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">กลุ่มเป้าหมาย</label>
                                <input
                                    type="text"
                                    name="audience"
                                    value="{{ old('audience', optional($editingTraining)->audience ?? '') }}"
                                    placeholder="เช่น Admin, IT DEV"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                >
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">ผู้สอน</label>
                                <input
                                    type="text"
                                    name="trainer"
                                    value="{{ old('trainer', optional($editingTraining)->trainer ?? '') }}"
                                    placeholder="เช่น IT Admin"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                >
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">สถานที่</label>
                                <input
                                    type="text"
                                    name="location"
                                    value="{{ old('location', optional($editingTraining)->location ?? '') }}"
                                    placeholder="เช่น ห้องอบรม 1"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                >
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Google Sheet URL</label>
                                <input
                                    type="url"
                                    name="google_sheet_url"
                                    value="{{ old('google_sheet_url', optional($editingTraining)->google_sheet_url ?? '') }}"
                                    placeholder="https://docs.google.com/spreadsheets/..."
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                >
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">จำนวนที่รองรับ</label>
                                <input
                                    type="number"
                                    name="capacity"
                                    min="0"
                                    value="{{ old('capacity', optional($editingTraining)->capacity ?? 0) }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">จำนวนผู้เข้าอบรมจริง</label>
                            <input
                                type="number"
                                name="attended"
                                min="0"
                                value="{{ old('attended', optional($editingTraining)->attended ?? 0) }}"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                            >
                        </div>

                        <details class="group rounded-[1.35rem] border border-slate-200 bg-slate-50 px-4 py-3">
                            <summary class="cursor-pointer list-none text-sm font-semibold text-slate-700">
                                Advanced
                                <span class="ml-2 text-xs font-medium text-slate-400">รายชื่อผู้เข้าอบรม และหมายเหตุ</span>
                            </summary>

                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">รายชื่อผู้เข้าอบรม</label>
                                    <textarea
                                        name="participants"
                                        rows="4"
                                        placeholder="เช่น นาย A, นาง B, นาย C"
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100"
                                    >{{ old('participants', optional($editingTraining)->participants ?? '') }}</textarea>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">หมายเหตุ</label>
                                    <textarea
                                        name="notes"
                                        rows="4"
                                        placeholder="บันทึกประเด็นที่สอนหรือข้อสังเกตหลังอบรม"
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100"
                                    >{{ old('notes', optional($editingTraining)->notes ?? '') }}</textarea>
                                </div>
                            </div>
                        </details>

                        <div class="flex items-center gap-3">
                            <button
                                type="submit"
                                class="inline-flex flex-1 items-center justify-center rounded-2xl bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)] px-5 py-3.5 text-sm font-bold text-white shadow-[0_18px_35px_rgba(37,99,235,.24)] transition hover:brightness-105"
                            >
                                {{ $editingTraining ? 'บันทึกการแก้ไข' : 'เพิ่มรอบอบรม' }}
                            </button>

                            @if ($editingTraining)
                                <a href="{{ route('trainings.index') }}"
                                    class="rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                                    เคลียร์
                                </a>
                            @endif
                        </div>
                    </form>
                </section>
            @else
                <section class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">โหมดอ่านอย่างเดียว</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950">คุณไม่มีสิทธิ์แก้ไขข้อมูลอบรม</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        ผู้ใช้กลุ่ม IT DEV (Read Only) สามารถดูข้อมูลการอบรมได้ แต่ไม่สามารถเพิ่ม แก้ไข หรือลบรายการได้
                    </p>
                </section>
            @endcan

            <section class="space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Training Records</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">ตารางประวัติการอบรม</h2>
                    </div>

                    <form method="GET" action="{{ route('trainings.index') }}" class="flex flex-wrap gap-2">
                        <input
                            type="text"
                            name="q"
                            value="{{ $filters['q'] ?? '' }}"
                            placeholder="ค้นหา..."
                            class="w-48 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100"
                        >
                        <select
                            name="status"
                            class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-sky-400 focus:outline-none focus:ring-4 focus:ring-sky-100"
                        >
                            <option value="">ทุกสถานะ</option>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                            กรอง
                        </button>
                    </form>
                </div>

                @forelse ($trainings as $training)
                    @php
                        $searchText = strtolower(
                            $training->title.' '.$training->audience.' '.$training->trainer.' '.$training->location.' '.$training->google_sheet_url.' '.$training->participants.' '.$training->notes.' '.$training->status
                        );
                        $participants = collect(preg_split('/\r\n|\r|\n|,/', (string) $training->participants))
                            ->map(fn ($value) => trim((string) $value))
                            ->filter()
                            ->values();
                        $attendanceRate = max(0, min(100, $training->capacity > 0 ? (int) round(($training->attended / max(1, $training->capacity)) * 100) : 0));
                    @endphp
                    <article
                        data-training-row
                        data-search="{{ $searchText }}"
                        class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]"
                    >
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-3">
                                    <a href="#training-form" class="text-xl font-black text-slate-950 hover:text-sky-700">
                                        {{ $training->title }}
                                    </a>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-inset ring-slate-200">
                                        {{ optional($training->training_date)->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $statusTone[$training->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                        {{ $statusOptions[$training->status] ?? $training->status }}
                                    </span>
                                </div>

                                <div class="mt-3 flex flex-wrap gap-2 text-sm text-slate-500">
                                    <span class="rounded-full bg-slate-50 px-3 py-1 ring-1 ring-inset ring-slate-200">
                                        กลุ่มเป้าหมาย: {{ $training->audience ?: 'ไม่ระบุ' }}
                                    </span>
                                    <span class="rounded-full bg-slate-50 px-3 py-1 ring-1 ring-inset ring-slate-200">
                                        ผู้สอน: {{ $training->trainer ?: 'ไม่ระบุ' }}
                                    </span>
                                    <span class="rounded-full bg-slate-50 px-3 py-1 ring-1 ring-inset ring-slate-200">
                                        สถานที่: {{ $training->location ?: 'ไม่ระบุ' }}
                                    </span>
                                </div>

                                @if ($training->google_sheet_url)
                                    <div class="mt-3">
                                        <a href="{{ $training->google_sheet_url }}" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200 transition hover:bg-emerald-100">
                                            เปิด Google Sheet
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-3.5 w-3.5">
                                                <path d="M14 5h5v5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M10 14 19 5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M19 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h4" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                    </div>
                                @endif

                                <p class="mt-3 max-w-4xl text-sm leading-6 text-slate-600">
                                    {{ $training->notes ?: 'ไม่มีหมายเหตุเพิ่มเติม' }}
                                </p>

                                @if ($participants->isNotEmpty())
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @foreach ($participants->take(6) as $participant)
                                            <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                                {{ $participant }}
                                            </span>
                                        @endforeach
                                        @if ($participants->count() > 6)
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                                                +{{ $participants->count() - 6 }} คน
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="flex shrink-0 flex-col gap-3 lg:items-end">
                                <div class="min-w-[220px] rounded-[1.35rem] bg-slate-50 p-4">
                                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                                        <span>จำนวนผู้เข้าอบรม</span>
                                        <span>{{ $attendanceRate }}%</span>
                                    </div>
                                    <div class="mt-2 flex items-baseline justify-between">
                                        <p class="text-2xl font-black text-slate-950">{{ number_format($training->attended) }}</p>
                                        <p class="text-sm text-slate-500">/ {{ number_format($training->capacity) }}</p>
                                    </div>
                                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200">
                                        <div class="h-full rounded-full bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)]"
                                            style="width: {{ $attendanceRate }}%"></div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    @can('manage-trainings')
                                        <a href="{{ route('trainings.index', ['edit' => $training->id]) }}#training-form"
                                            class="rounded-full bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 ring-1 ring-inset ring-sky-200 transition hover:bg-sky-100">
                                            แก้ไข
                                        </a>
                                        <form method="POST" action="{{ route('trainings.destroy', $training) }}"
                                            onsubmit="return confirm('ต้องการลบข้อมูลอบรมนี้ใช่ไหม?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-full bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 ring-1 ring-inset ring-rose-200 transition hover:bg-rose-100">
                                                ลบ
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[1.6rem] border border-dashed border-slate-300 bg-white px-6 py-12 text-center text-slate-500 shadow-[0_14px_40px_rgba(15,23,42,.05)]">
                        ยังไม่มีข้อมูลการอบรมในระบบ
                    </div>
                @endforelse
            </section>
        </div>
    </div>

    <script>
        const searchInput = document.querySelector('input[name="q"]');
        const trainingCards = Array.from(document.querySelectorAll('[data-training-row]'));

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const term = searchInput.value.trim().toLowerCase();

                for (const card of trainingCards) {
                    const haystack = card.dataset.search || '';
                    card.hidden = term !== '' && !haystack.includes(term);
                }
            });
        }
    </script>
</x-frontend-layout>
