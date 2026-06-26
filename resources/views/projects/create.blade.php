<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - Create Project"
    description="Create or edit project"
    active="projects"
>
    <div class="rounded-[2rem] border border-white/60 bg-white/70 p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] backdrop-blur xl:p-7">
        @if (session('success'))
            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Project form</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                    {{ $editingProject ? 'แก้ไขโครงการ' : 'สร้างโครงการใหม่' }}
                </h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                    ฟอร์มนี้แยกออกจากหน้ารายการ เพื่อให้กรอกข้อมูลได้เต็มที่และไม่รกสายตา
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('projects.index') }}" class="rounded-full bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                    กลับรายการโครงการ
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                <p class="font-semibold">ตรวจพบข้อมูลไม่ครบ</p>
                <ul class="mt-2 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-6">
            @can('manage-projects')
                <section id="project-form" class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">
                                {{ $editingProject ? 'แก้ไขโครงการ' : 'เพิ่มโครงการใหม่' }}
                            </p>
                            <h3 class="mt-2 text-2xl font-black text-slate-950">
                                {{ $editingProject ? 'อัปเดตข้อมูลโครงการ' : 'สร้างโครงการใหม่' }}
                            </h3>
                        </div>

                        @if ($editingProject)
                            <a href="{{ route('projects.create') }}" class="rounded-full bg-slate-100 px-3 py-2 text-xs font-bold text-slate-600 ring-1 ring-inset ring-slate-200">
                                ยกเลิก
                            </a>
                        @endif
                    </div>

                    <form
                        class="mt-5 space-y-4"
                        method="POST"
                        action="{{ $editingProject ? route('projects.update', $editingProject) : route('projects.store') }}"
                    >
                        @csrf
                        @if ($editingProject)
                            @method('PUT')
                        @endif

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">ชื่อโครงการ</label>
                            <input type="text" name="name" value="{{ old('name', $editingProject->name ?? '') }}" placeholder="เช่น พัฒนาระบบรายงานผล" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">บริษัท</label>
                                <select name="company_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                    <option value="">เลือกบริษัท</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}" @selected((string) old('company_id', $editingProject->company_id ?? request()->query('company_id')) === (string) $company->id)>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">เจ้าของโครงการ</label>
                                <select name="user_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                    <option value="">เลือกเจ้าของ</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @selected((string) old('user_id', $editingProject->user_id ?? '') === (string) $user->id)>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">สถานะ</label>
                                <select name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                                    <option value="active" @selected(old('status', $editingProject->status ?? 'active') === 'active')>กำลังดำเนินการ</option>
                                    <option value="on_hold" @selected(old('status', $editingProject->status ?? '') === 'on_hold')>พักงาน</option>
                                    <option value="completed" @selected(old('status', $editingProject->status ?? '') === 'completed')>เสร็จสมบูรณ์</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">วันที่เริ่ม</label>
                                <input type="date" name="start_date" value="{{ old('start_date', optional($editingProject?->start_date)->format('Y-m-d')) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">วันที่ครบกำหนด</label>
                            <input type="date" name="due_date" value="{{ old('due_date', optional($editingProject?->due_date)->format('Y-m-d')) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">รายละเอียด</label>
                            <textarea name="description" rows="5" placeholder="อธิบายขอบเขต เป้าหมาย หรือหมายเหตุ" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">{{ old('description', $editingProject->description ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">วัตถุประสงค์</label>
                            <textarea name="objective" rows="4" placeholder="เป้าหมายหลักของโครงการนี้คืออะไร" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">{{ old('objective', $editingProject->objective ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">ความเสี่ยง</label>
                            <textarea name="risk" rows="4" placeholder="ประเด็นที่อาจกระทบโครงการ และวิธีเฝ้าระวัง" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">{{ old('risk', $editingProject->risk ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">หมายเหตุ</label>
                            <textarea name="notes" rows="4" placeholder="ข้อมูลเสริม, ข้อสังเกต, หรือสิ่งที่ต้องติดตาม" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">{{ old('notes', $editingProject->notes ?? '') }}</textarea>
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)] px-5 py-3.5 text-sm font-bold text-white shadow-[0_18px_35px_rgba(37,99,235,.24)] transition hover:brightness-105">
                                {{ $editingProject ? 'บันทึกการแก้ไข' : 'เพิ่มโครงการ' }}
                            </button>
                            @if ($editingProject)
                                <a href="{{ route('projects.create') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                                    เคลียร์
                                </a>
                            @endif
                        </div>
                    </form>
                </section>
            @else
                <section class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">โหมดอ่านอย่างเดียว</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-950">คุณไม่มีสิทธิ์เพิ่มหรือแก้ไขโครงการ</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">ผู้ใช้กลุ่ม IT DEV (Read Only) จะเห็นรายการและรายละเอียดโครงการได้ แต่ไม่สามารถจัดการข้อมูลโครงการได้</p>
                </section>
            @endcan
        </div>
    </div>
</x-frontend-layout>
