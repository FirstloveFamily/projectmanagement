<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - Users"
    description="Frontend user management dashboard"
    active="users"
>
    @php
        $roleLabels = [
            'admin' => 'Admin',
            'it_dev' => 'IT DEV',
            'it_test' => 'IT DEV (Read Only)',
        ];

        $statusLabels = [
            'active' => 'Active',
            'pending' => 'Pending',
            'suspended' => 'Suspended',
        ];

        $roleBadge = [
            'admin' => 'bg-rose-50 text-rose-700 ring-rose-200',
            'it_dev' => 'bg-sky-50 text-sky-700 ring-sky-200',
            'it_test' => 'bg-amber-50 text-amber-700 ring-amber-200',
        ];

        $statusBadge = [
            'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'pending' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'suspended' => 'bg-rose-50 text-rose-700 ring-rose-200',
        ];

        $summaryCards = [
            ['label' => 'ผู้ใช้ทั้งหมด', 'value' => number_format($summary['users'] ?? 0), 'hint' => 'บัญชีผู้ใช้ในระบบ', 'tone' => 'from-slate-700 to-slate-500'],
            ['label' => 'มีโครงการ', 'value' => number_format($summary['with_projects'] ?? 0), 'hint' => 'ผู้ใช้ที่ผูกกับโปรเจกต์', 'tone' => 'from-sky-600 to-cyan-500'],
            ['label' => 'ไม่มีโครงการ', 'value' => number_format($summary['without_projects'] ?? 0), 'hint' => 'ผู้ใช้ที่ยังไม่มีงาน', 'tone' => 'from-emerald-600 to-teal-500'],
            ['label' => 'Admin', 'value' => number_format($summary['admin'] ?? 0), 'hint' => 'ผู้ดูแลระบบ', 'tone' => 'from-rose-600 to-red-500'],
            ['label' => 'IT DEV', 'value' => number_format($summary['it_dev'] ?? 0), 'hint' => 'ทีมพัฒนา', 'tone' => 'from-sky-600 to-cyan-500'],
            ['label' => 'IT DEV (RO)', 'value' => number_format($summary['it_test'] ?? 0), 'hint' => 'ทีมอ่านอย่างเดียว', 'tone' => 'from-amber-500 to-orange-400'],
            ['label' => 'Active', 'value' => number_format($summary['active'] ?? 0), 'hint' => 'ผู้ใช้ที่พร้อมใช้งาน', 'tone' => 'from-emerald-600 to-teal-500'],
            ['label' => 'Pending', 'value' => number_format($summary['pending'] ?? 0), 'hint' => 'รอเปิดใช้งาน', 'tone' => 'from-amber-500 to-orange-400'],
        ];
    @endphp

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                {{ session('error') }}
            </div>
        @endif

        <section class="rounded-[2rem] border border-white/60 bg-white/75 p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] backdrop-blur xl:p-7">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-4xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">User Management</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                        จัดการผู้ใช้
                    </h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 sm:text-base">
                        เพิ่ม แก้ไข และลบบัญชีผู้ใช้จากหน้า frontend นี้ พร้อมดู role และสถานะบัญชีได้ทันที
                    </p>
                </div>

                <form method="GET" action="{{ route('users.index') }}" class="w-full max-w-xl">
                    <label class="mb-2 block text-sm font-semibold text-slate-600">ค้นหาผู้ใช้</label>
                    <div class="flex gap-3">
                        <input
                            type="text"
                            name="q"
                            value="{{ $filters['q'] ?? '' }}"
                            placeholder="พิมพ์ชื่อ หรืออีเมล"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100"
                        >
                        <button type="submit" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                            ค้นหา
                        </button>
                    </div>
                    @if (!empty($filters['q']))
                        <div class="mt-2">
                            <a href="{{ route('users.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-900">ล้างการค้นหา</a>
                        </div>
                    @endif
                </form>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('permissions.index') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90">
                    ไปหน้า จัดการสิทธิ์
                </a>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($summaryCards as $card)
                    <div class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-500">{{ $card['label'] }}</p>
                            <span class="h-2.5 w-2.5 rounded-full bg-gradient-to-r {{ $card['tone'] }}"></span>
                        </div>
                        <div class="mt-3 text-3xl font-black text-slate-950">{{ $card['value'] }}</div>
                        <p class="mt-2 text-sm text-slate-500">{{ $card['hint'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[420px_minmax(0,1fr)]">
            <section id="user-form" class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">
                            {{ $editingUser ? 'แก้ไขผู้ใช้' : 'เพิ่มผู้ใช้ใหม่' }}
                        </p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">
                            {{ $editingUser ? 'อัปเดตข้อมูลผู้ใช้' : 'สร้างบัญชีผู้ใช้' }}
                        </h2>
                    </div>

                    @if ($editingUser)
                        <a href="{{ route('users.index') }}" class="rounded-full bg-slate-100 px-3 py-2 text-xs font-bold text-slate-600 ring-1 ring-inset ring-slate-200">
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

                <form class="mt-5 space-y-4" method="POST" action="{{ $editingUser ? route('users.update', $editingUser) : route('users.store') }}">
                    @csrf
                    @if ($editingUser)
                        @method('PUT')
                    @endif

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">ชื่อผู้ใช้</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $editingUser->name ?? '') }}"
                            placeholder="เช่น Somchai Prom"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                            required
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">อีเมล</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $editingUser->email ?? '') }}"
                            placeholder="name@example.com"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                            required
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Role / Permission</label>
                        <select
                            name="role"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                            required
                        >
                            @foreach ($roleLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('role', $editingUser->role ?? 'it_dev') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">สถานะผู้ใช้</label>
                        <select
                            name="status"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                            required
                        >
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $editingUser->status ?? 'active') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            {{ $editingUser ? 'รหัสผ่านใหม่' : 'รหัสผ่าน' }}
                        </label>
                        <input
                            type="password"
                            name="password"
                            placeholder="{{ $editingUser ? 'เว้นว่างถ้าไม่เปลี่ยน' : 'ตั้งรหัสผ่านเริ่มต้น' }}"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                            {{ $editingUser ? '' : 'required' }}
                        >
                        @if ($editingUser)
                            <p class="mt-2 text-xs text-slate-500">ถ้าไม่ต้องการเปลี่ยนรหัสผ่าน ให้เว้นช่องนี้ว่างไว้</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)] px-5 py-3.5 text-sm font-bold text-white shadow-[0_18px_35px_rgba(37,99,235,.24)] transition hover:brightness-105">
                            {{ $editingUser ? 'บันทึกการแก้ไข' : 'เพิ่มผู้ใช้' }}
                        </button>

                        @if ($editingUser)
                            <a href="{{ route('users.index') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                                เคลียร์
                            </a>
                        @endif
                    </div>
                </form>
            </section>

            <section class="space-y-4">
                <div class="rounded-[1.8rem] border border-slate-200 bg-white p-4 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Users list</p>
                            <h2 class="mt-2 text-2xl font-black text-slate-950">รายชื่อผู้ใช้</h2>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                            {{ number_format($users->count()) }} รายการ
                        </span>
                    </div>
                </div>

                <div class="overflow-hidden rounded-[1.8rem] border border-slate-200 bg-white shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">
                                    <th class="px-4 py-3">ชื่อ</th>
                                    <th class="px-4 py-3">อีเมล</th>
                                    <th class="px-4 py-3">Role</th>
                                    <th class="px-4 py-3">โครงการ</th>
                                    <th class="px-4 py-3">สถานะ</th>
                                    <th class="px-4 py-3 text-right">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-slate-50/60">
                                        <td class="px-4 py-4">
                                            <div class="font-black text-slate-950">{{ $user->name }}</div>
                                            <div class="mt-1 text-xs text-slate-500">ID {{ $user->id }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-slate-600">{{ $user->email }}</td>
                                        <td class="px-4 py-4">
                                            <span class="rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $roleBadge[$user->role] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                                {{ $roleLabels[$user->role] ?? $user->role ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 ring-1 ring-inset ring-slate-200">
                                                {{ number_format($user->projects_count ?? 0) }} projects
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $statusBadge[$user->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                                {{ $statusLabels[$user->status] ?? $user->status ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('users.index', ['edit' => $user->id]) }}#user-form" class="rounded-full bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                                                    แก้ไข
                                                </a>
                                                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('ต้องการลบผู้ใช้นี้ใช่ไหม? หากมีโปรเจกต์ผูกอยู่จะไม่สามารถลบได้')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-full bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 ring-1 ring-inset ring-rose-200 transition hover:bg-rose-100">
                                                        ลบ
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">
                                            ยังไม่มีผู้ใช้ในระบบ
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </section>
    </div>
</x-frontend-layout>
