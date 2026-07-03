<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - Companies"
    description="Frontend company management dashboard"
    active="companies"
>
    @php
        $summaryCards = [
            ['label' => 'บริษัททั้งหมด', 'value' => number_format($summary['companies'] ?? 0), 'hint' => 'รายการบริษัทในระบบ', 'accent' => 'from-slate-700 to-slate-500'],
            ['label' => 'โครงการทั้งหมด', 'value' => number_format($summary['projects'] ?? 0), 'hint' => 'โครงการที่ผูกบริษัท', 'accent' => 'from-sky-600 to-cyan-500'],
            ['label' => 'กำลังดำเนินการ', 'value' => number_format($summary['active'] ?? 0), 'hint' => 'โครงการที่ยังเดินหน้า', 'accent' => 'from-emerald-600 to-teal-500'],
            ['label' => 'ค้างเกินกำหนด', 'value' => number_format($summary['overdue'] ?? 0), 'hint' => 'โครงการที่ต้องเร่งติดตาม', 'accent' => 'from-rose-600 to-red-500'],
        ];
    @endphp
    <div class="rounded-[2rem] border border-white/60 bg-white/70 p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] backdrop-blur xl:p-7">
                    @if (session('success'))
                        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                        <div class="max-w-3xl">
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">หน้า Company</p>
                            <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                                จัดการบริษัทแบบเร็วและชัด
                            </h2>
                            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                                เพิ่ม แก้ไข และลบบริษัทจากหน้า frontend นี้ได้เลย พร้อมดูสรุปโครงการของแต่ละบริษัทในมุมมองแบบ dashboard
                            </p>
                        </div>

                        <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">สถานะ</p>
                            <p class="mt-1 text-lg font-black text-slate-950">{{ $editingCompany ? 'กำลังแก้ไขบริษัท' : 'โหมดเพิ่มบริษัท' }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $editingCompany ? $editingCompany->name : 'พร้อมสร้างบริษัทใหม่' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach ($summaryCards as $card)
                            <div class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-semibold text-slate-500">{{ $card['label'] }}</p>
                                    <span class="inline-flex h-3 w-3 rounded-full bg-gradient-to-r {{ $card['accent'] }}"></span>
                                </div>
                                <div class="mt-3 text-3xl font-black text-slate-950">{{ $card['value'] }}</div>
                                <p class="mt-2 text-sm text-slate-500">{{ $card['hint'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 grid gap-6 xl:grid-cols-[420px_minmax(0,1fr)]">
                        @can('manage-companies')
                            <section id="company-form" class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">
                                            {{ $editingCompany ? 'แก้ไขบริษัท' : 'เพิ่มบริษัทใหม่' }}
                                        </p>
                                        <h3 class="mt-2 text-2xl font-black text-slate-950">
                                            {{ $editingCompany ? 'อัปเดตข้อมูลบริษัท' : 'สร้างบริษัทใหม่' }}
                                        </h3>
                                    </div>

                                    @if ($editingCompany)
                                        <a href="{{ route('companies.index') }}" class="rounded-full bg-slate-100 px-3 py-2 text-xs font-bold text-slate-600 ring-1 ring-inset ring-slate-200">
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

                                <form
                                    class="mt-5 space-y-4"
                                    method="POST"
                                    action="{{ $editingCompany ? route('companies.update', $editingCompany) : route('companies.store') }}"
                                >
                                    @csrf
                                    @if ($editingCompany)
                                        @method('PUT')
                                    @endif

                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">ชื่อบริษัท</label>
                                        <input
                                            type="text"
                                            name="name"
                                            value="{{ old('name', $editingCompany->name ?? '') }}"
                                            placeholder="เช่น โรงเรียนดิจิทัลเพื่อครูไทย"
                                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                            required
                                        >
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">รายละเอียด</label>
                                        <textarea
                                            name="description"
                                            rows="5"
                                            placeholder="อธิบายบทบาทหรือหน้าที่ของบริษัท"
                                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                                        >{{ old('description', $editingCompany->description ?? '') }}</textarea>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <button
                                            type="submit"
                                            class="inline-flex flex-1 items-center justify-center rounded-2xl bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)] px-5 py-3.5 text-sm font-bold text-white shadow-[0_18px_35px_rgba(37,99,235,.24)] transition hover:brightness-105"
                                        >
                                            {{ $editingCompany ? 'บันทึกการแก้ไข' : 'เพิ่มบริษัท' }}
                                        </button>

                                        @if ($editingCompany)
                                            <a href="{{ route('companies.index') }}"
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
                                <h3 class="mt-2 text-2xl font-black text-slate-950">คุณไม่มีสิทธิ์แก้ไขข้อมูลบริษัท</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600">ผู้ใช้กลุ่ม IT DEV (Read Only) สามารถดูข้อมูลบริษัทและโครงการได้ แต่ไม่สามารถเพิ่ม แก้ไข หรือลบข้อมูลได้</p>
                            </section>
                        @endcan

                        <section class="space-y-4">
                            @forelse ($companies as $company)
                                <article class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-3">
                                                <a href="{{ route('companies.show', $company) }}" class="text-xl font-black text-slate-950 hover:text-sky-700">{{ $company->name }}</a>
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-inset ring-slate-200">
                                                    โครงการ {{ number_format($company->projects_count) }}
                                                </span>
                                                <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700 ring-1 ring-inset ring-sky-200">
                                                    กำลังดำเนินการ {{ number_format($company->active_projects_count) }}
                                                </span>
                                                <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 ring-1 ring-inset ring-rose-200">
                                                    ค้าง {{ number_format($company->overdue_projects_count) }}
                                                </span>
                                            </div>
                                            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
                                                {{ $company->description ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}
                                            </p>
                                        </div>

                                        <div class="flex shrink-0 items-center gap-2">
                                            <a href="{{ route('companies.show', $company) }}"
                                                class="rounded-full bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 ring-1 ring-inset ring-sky-200 transition hover:bg-sky-100">
                                                ดู
                                            </a>
                                            @can('manage-companies')
                                                <a href="{{ route('companies.index', ['edit' => $company->id]) }}#company-form"
                                                    class="rounded-full bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                                                    แก้ไข
                                                </a>
                                                <form method="POST" action="{{ route('companies.destroy', $company) }}"
                                                    onsubmit="return confirm('ต้องการลบบริษัทนี้ใช่ไหม?')">
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
                                </article>
                            @empty
                                <div class="rounded-[1.6rem] border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-slate-500 shadow-[0_14px_40px_rgba(15,23,42,.05)]">
                                    ยังไม่มีข้อมูลบริษัทในระบบ
                                </div>
                            @endforelse
                        </section>
                    </div>
    </div>
</x-frontend-layout>
