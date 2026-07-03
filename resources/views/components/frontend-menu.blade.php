@props([
    'active' => 'home',
])

@php
    $currentUser = auth()->user();
    $canUseFullMenu = $currentUser && in_array($currentUser->role, [
        \App\Models\User::ROLE_ADMIN,
        \App\Models\User::ROLE_IT_DEV,
    ], true);
    $loginViaModal = request()->routeIs('home');
    $activeKey = $active === 'home' ? 'dashboard' : $active;

    $items = [
        [
            'key' => 'dashboard',
            'label' => 'Dashboard',
            'href' => url('/'),
            'icon' => '<path d="M3 11.5 12 4l9 7.5" stroke-linecap="round" stroke-linejoin="round" /><path d="M5 10.75V20h4.5v-5.5h5V20H19v-9.25" stroke-linecap="round" stroke-linejoin="round" />',
        ],
        [
            'key' => 'projects',
            'label' => 'Projects',
            'href' => route('projects.index'),
            'icon' => '<path d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v9A2.5 2.5 0 0 1 17.5 19h-11A2.5 2.5 0 0 1 4 16.5z" /><path d="M8 9h8M8 12h8M8 15h5" stroke-linecap="round" />',
        ],
        [
            'key' => 'companies',
            'label' => 'Companies',
            'href' => route('companies.index'),
            'icon' => '<path d="M4 19V5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v14m-9 0h18M8 4v15m8-10h4a1 1 0 0 1 1 1v9" stroke-linecap="round" stroke-linejoin="round" />',
        ],
        [
            'key' => 'reports',
            'label' => 'Task Report',
            'href' => route('reports.index'),
            'icon' => '<path d="M5 19V5m0 14h14M8 15v-3m4 3V8m4 7v-5" stroke-linecap="round" stroke-linejoin="round" />',
        ],
        [
            'key' => 'reports-projects',
            'label' => 'Project Report',
            'href' => route('reports.projects'),
            'icon' => '<path d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v9A2.5 2.5 0 0 1 17.5 19h-11A2.5 2.5 0 0 1 4 16.5z" /><path d="M8 9h8M8 12h8M8 15h5" stroke-linecap="round" />',
        ],
        [
            'key' => 'reports-deadlines',
            'label' => 'Deadline Alerts',
            'href' => route('reports.deadlines'),
            'icon' => '<path d="M12 8v5l3 2" stroke-linecap="round" stroke-linejoin="round" /><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" /><path d="M12 6v1" stroke-linecap="round" /><path d="M12 17v1" stroke-linecap="round" />',
        ],
        [
            'key' => 'reports-yearly',
            'label' => 'รายงานรายปี',
            'href' => route('reports.yearly'),
            'icon' => '<path d="M5 18V6m4 12V9m4 9v-5m4 5V7" stroke-linecap="round" stroke-linejoin="round" />',
        ],
        [
            'key' => 'reports-trainings',
            'label' => 'รายงานอบรม',
            'href' => route('reports.trainings'),
            'icon' => '<path d="M4 7.5 12 4l8 3.5-8 3.5-8-3.5z" /><path d="M6 9.5v4.25c0 1.38 2.7 3.25 6 3.25s6-1.87 6-3.25V9.5" stroke-linecap="round" stroke-linejoin="round" />',
        ],
        [
            'key' => 'reports-team-yearly',
            'label' => 'รายงานทีม',
            'href' => route('reports.team-yearly'),
            'icon' => '<path d="M7.5 11a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" /><path d="M16.5 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" /><path d="M4 20a5.5 5.5 0 0 1 11 0" stroke-linecap="round" /><path d="M13 20a4 4 0 0 1 8 0" stroke-linecap="round" />',
        ],
        [
            'key' => 'requests',
            'label' => 'คำร้องระบบ',
            'href' => route('requests.index'),
            'icon' => '<path d="M7 4h7l5 5v11a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z" /><path d="M14 4v5h5" stroke-linecap="round" stroke-linejoin="round" />',
        ],
        [
            'key' => 'requests-queue',
            'label' => 'ภาพรวมคำร้อง',
            'href' => route('requests.queue'),
            'icon' => '<path d="M4 6h16M4 12h10M4 18h13" stroke-linecap="round" stroke-linejoin="round" /><path d="M18 12v6m0 0-2-2m2 2 2-2" stroke-linecap="round" stroke-linejoin="round" />',
        ],
        [
            'key' => 'requests-report',
            'label' => 'รายงานคำร้อง',
            'href' => route('requests.report'),
            'icon' => '<path d="M5 19V5m0 14h14M8 15v-3m4 3V8m4 7v-5" stroke-linecap="round" stroke-linejoin="round" />',
        ],
        [
            'key' => 'trainings',
            'label' => 'อบรมพนักงาน',
            'href' => route('trainings.index'),
            'icon' => '<path d="M12 3 3 8l9 5 9-5-9-5Z" /><path d="M5 10v4c0 1.7 3.1 4 7 4s7-2.3 7-4v-4" stroke-linecap="round" stroke-linejoin="round" />',
        ],
    ];
@endphp

<aside
    {{ $attributes->merge(['class' => 'relative w-full shrink-0 overflow-hidden border-r border-slate-200 bg-[linear-gradient(180deg,#07111f_0%,#0a1a2d_45%,#0b2138_100%)] text-white shadow-[0_24px_80px_rgba(2,8,23,.22)] lg:flex lg:w-[310px] lg:flex-col']) }}>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(59,130,246,.18),transparent_34%),radial-gradient(circle_at_bottom,rgba(34,211,238,.12),transparent_28%)]"></div>

    <div class="relative flex h-full flex-col px-6 py-6">
        <div class="flex items-center gap-3">
            <div class="grid h-11 w-11 place-items-center rounded-2xl bg-[linear-gradient(135deg,#2563eb_0%,#22d3ee_100%)] font-black text-white shadow-lg">
                B
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-200/80">Frontend Admin</p>
                <h1 class="mt-1 text-xl font-black leading-tight text-white">ITC Center</h1>
            </div>
        </div>

        <p class="mt-5 text-sm leading-6 text-slate-300">
            มุมมองรวมของ Dashboard, Projects, Companies และ Reports
        </p>

        <div class="my-6 h-px bg-white/10"></div>

        @if ($canUseFullMenu)
            <nav class="space-y-2">
                <p class="px-2 text-[11px] font-bold uppercase tracking-[0.32em] text-slate-400">Menu</p>
                @foreach ($items as $item)
                    @php
                        $isActive = $activeKey === $item['key'];
                    @endphp
                    <a href="{{ $item['href'] }}"
                        class="{{ $isActive ? 'bg-white/12 text-white shadow-[inset_0_0_0_1px_rgba(255,255,255,.08)]' : 'text-slate-200 hover:bg-white/6 hover:text-white' }} flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
                        <span class="grid h-9 w-9 place-items-center rounded-xl {{ $isActive ? 'bg-white/15' : 'bg-white/10' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                                {!! $item['icon'] !!}
                            </svg>
                        </span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach

                @can('manage-users')
                    <a href="{{ route('users.index') }}"
                        class="{{ $activeKey === 'users' ? 'bg-white/12 text-white shadow-[inset_0_0_0_1px_rgba(255,255,255,.08)]' : 'text-slate-200 hover:bg-white/6 hover:text-white' }} flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
                        <span class="grid h-9 w-9 place-items-center rounded-xl {{ $activeKey === 'users' ? 'bg-white/15' : 'bg-white/10' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                                <path d="M20 21a8 8 0 1 0-16 0" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span>Users</span>
                    </a>

                    <a href="{{ route('permissions.index') }}"
                        class="{{ $activeKey === 'permissions' ? 'bg-white/12 text-white shadow-[inset_0_0_0_1px_rgba(255,255,255,.08)]' : 'text-slate-200 hover:bg-white/6 hover:text-white' }} flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
                        <span class="grid h-9 w-9 place-items-center rounded-xl {{ $activeKey === 'permissions' ? 'bg-white/15' : 'bg-white/10' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                                <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M19.4 15a7.9 7.9 0 0 0 .1-1 7.9 7.9 0 0 0-.1-1l2-1.5-1.8-3.1-2.4.7a7.8 7.8 0 0 0-1.7-1l-.4-2.5H9l-.4 2.5a7.8 7.8 0 0 0-1.7 1l-2.4-.7L2.7 11.5 4.7 13a7.9 7.9 0 0 0-.1 1 7.9 7.9 0 0 0 .1 1l-2 1.5 1.8 3.1 2.4-.7a7.8 7.8 0 0 0 1.7 1l.4 2.5h6l.4-2.5a7.8 7.8 0 0 0 1.7-1l2.4.7 1.8-3.1z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span>Permissions</span>
                    </a>
                @endcan

                @can('manage-settings')
                    <a href="{{ route('settings.index') }}"
                        class="{{ $activeKey === 'settings' ? 'bg-white/12 text-white shadow-[inset_0_0_0_1px_rgba(255,255,255,.08)]' : 'text-slate-200 hover:bg-white/6 hover:text-white' }} flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
                        <span class="grid h-9 w-9 place-items-center rounded-xl {{ $activeKey === 'settings' ? 'bg-white/15' : 'bg-white/10' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                                <path d="M12 8.5A3.5 3.5 0 1 0 12 15.5A3.5 3.5 0 0 0 12 8.5z" />
                                <path d="M19.4 15a7.9 7.9 0 0 0 .1-1 7.9 7.9 0 0 0-.1-1l2-1.5-1.8-3.1-2.4.7a7.8 7.8 0 0 0-1.7-1l-.4-2.5H9l-.4 2.5a7.8 7.8 0 0 0-1.7 1l-2.4-.7L2.7 11.5 4.7 13a7.9 7.9 0 0 0-.1 1 7.9 7.9 0 0 0 .1 1l-2 1.5 1.8 3.1 2.4-.7a7.8 7.8 0 0 0 1.7 1l.4 2.5h6l.4-2.5a7.8 7.8 0 0 0 1.7-1l2.4.7 1.8-3.1z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span>Settings</span>
                    </a>
                @endcan
            </nav>
        @else
            <nav class="space-y-2">
                <p class="px-2 text-[11px] font-bold uppercase tracking-[0.32em] text-slate-400">Quick Access</p>
                <a href="{{ url('/') }}"
                    class="{{ $activeKey === 'dashboard' ? 'bg-white/12 text-white shadow-[inset_0_0_0_1px_rgba(255,255,255,.08)]' : 'text-slate-200 hover:bg-white/6 hover:text-white' }} flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
                    <span class="grid h-9 w-9 place-items-center rounded-xl {{ $activeKey === 'dashboard' ? 'bg-white/15' : 'bg-white/10' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                            <path d="M3 11.5 12 4l9 7.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M5 10.75V20h4.5v-5.5h5V20H19v-9.25" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span>หน้าแรก</span>
                </a>

                @if ($loginViaModal)
                    <button type="button" data-login-open class="flex items-center gap-3 rounded-2xl bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)] px-4 py-3 text-sm font-semibold text-white shadow-[0_18px_35px_rgba(37,99,235,.24)] transition hover:brightness-105">
                        <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/15">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                                <path d="M10 7V6a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-5a2 2 0 0 1-2-2v-1" />
                                <path d="M14 12H3m0 0 3-3m-3 3 3 3" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span>IT Login</span>
                    </button>
                @else
                    <a href="{{ route('it.login') }}" class="flex items-center gap-3 rounded-2xl bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)] px-4 py-3 text-sm font-semibold text-white shadow-[0_18px_35px_rgba(37,99,235,.24)] transition hover:brightness-105">
                        <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/15">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                                <path d="M10 7V6a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-5a2 2 0 0 1-2-2v-1" />
                                <path d="M14 12H3m0 0 3-3m-3 3 3 3" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span>IT Login</span>
                    </a>
                @endif
            </nav>
        @endif

        <div class="mt-auto pt-6">
            @if ($canUseFullMenu)
                <form method="POST" action="{{ route('it.logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center justify-between rounded-[1.35rem] border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm font-semibold text-rose-100 transition hover:bg-rose-500/15">
                        <span class="flex items-center gap-3">
                            <span class="grid h-8 w-8 place-items-center rounded-xl bg-rose-500/15">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4.5 w-4.5">
                                    <path d="M10 7V6a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-5a2 2 0 0 1-2-2v-1" />
                                    <path d="M14 12H3m0 0 3-3m-3 3 3 3" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            ออกจากระบบ
                        </span>
                    </button>
                </form>
            @else
                <div class="rounded-[1.35rem] border border-white/10 bg-white/5 px-4 py-4 text-sm text-slate-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">IT Access</p>
                    <p class="mt-2 leading-6">ล็อกอินเพื่อเข้าดูเมนูเพิ่มเติมของทีม IT</p>
                </div>
            @endif
        </div>
    </div>
</aside>
