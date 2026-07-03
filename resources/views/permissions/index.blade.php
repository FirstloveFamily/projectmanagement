<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - Permissions"
    description="Frontend permissions dashboard"
    active="permissions"
>
    @php
        $roleLabels = \App\Models\User::roleLabels();
        $statusLabels = \App\Models\User::statusLabels();

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
    @endphp

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <section class="rounded-[2rem] border border-white/60 bg-white/75 p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] backdrop-blur xl:p-7">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-4xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Permission Center</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                        จัดการสิทธิ์ผู้ใช้
                    </h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 sm:text-base">
                        เปลี่ยน role และสถานะของผู้ใช้ได้จากหน้า frontend เดียว เห็นภาพรวมสิทธิ์ของระบบแบบชัดเจน
                    </p>
                </div>

                <form method="GET" action="{{ route('permissions.index') }}" class="w-full max-w-xl">
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
                </form>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('users.index') }}" class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                    กลับหน้า Users
                </a>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <p class="text-sm font-semibold text-slate-500">ผู้ใช้ทั้งหมด</p>
                    <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($summary['users'] ?? 0) }}</div>
                </div>
                <div class="rounded-[1.6rem] border border-rose-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <p class="text-sm font-semibold text-rose-700">Admin</p>
                    <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($summary['admin'] ?? 0) }}</div>
                </div>
                <div class="rounded-[1.6rem] border border-sky-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <p class="text-sm font-semibold text-sky-700">IT DEV</p>
                    <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($summary['it_dev'] ?? 0) }}</div>
                </div>
                <div class="rounded-[1.6rem] border border-amber-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <p class="text-sm font-semibold text-amber-700">IT DEV (Read Only)</p>
                    <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($summary['it_test'] ?? 0) }}</div>
                </div>
                <div class="rounded-[1.6rem] border border-emerald-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <p class="text-sm font-semibold text-emerald-700">Active</p>
                    <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($summary['active'] ?? 0) }}</div>
                </div>
                <div class="rounded-[1.6rem] border border-amber-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
                    <p class="text-sm font-semibold text-amber-700">Pending / Suspended</p>
                    <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format(($summary['pending'] ?? 0) + ($summary['suspended'] ?? 0)) }}</div>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-[1.8rem] border border-slate-200 bg-white shadow-[0_14px_40px_rgba(15,23,42,.07)]">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-500">
                            <th class="px-4 py-3">ชื่อ</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">สถานะ</th>
                            <th class="px-4 py-3">โครงการ</th>
                            <th class="px-4 py-3 text-right">อัปเดต</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-4">
                                    <div class="font-black text-slate-950">{{ $user->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $user->email }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $roleBadge[$user->role] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                        {{ $roleLabels[$user->role] ?? $user->role ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $statusBadge[$user->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                        {{ $statusLabels[$user->status] ?? $user->status ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ number_format($user->projects_count ?? 0) }} projects</td>
                                <td class="px-4 py-4">
                                    <form method="POST" action="{{ route('permissions.update', $user) }}" class="flex flex-wrap items-center justify-end gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-900">
                                            @foreach ($roleLabels as $value => $label)
                                                <option value="{{ $value }}" @selected($user->role === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <select name="status" class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-900">
                                            @foreach ($statusLabels as $value => $label)
                                                <option value="{{ $value }}" @selected($user->status === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90">
                                            บันทึก
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-sm text-slate-500">
                                    ยังไม่มีผู้ใช้ในระบบ
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-frontend-layout>
