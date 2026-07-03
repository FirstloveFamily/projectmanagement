<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - IT Login</title>
    <meta name="description" content="เข้าสู่ระบบสำหรับทีม IT เพื่อจัดการคำร้องและรายงาน">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen text-slate-900 antialiased">
    <div class="relative min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(37,99,235,.16),_transparent_28%),radial-gradient(circle_at_top_right,_rgba(14,165,233,.12),_transparent_26%),linear-gradient(180deg,#f7f9fd_0%,#eef2f8_100%)]">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(15,23,42,.03)_1px,transparent_1px),linear-gradient(to_bottom,rgba(15,23,42,.03)_1px,transparent_1px)] bg-[size:28px_28px] opacity-40"></div>

        <div class="relative mx-auto flex min-h-screen max-w-7xl items-center px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid w-full gap-6 lg:grid-cols-[1.05fr_.95fr]">
                <section class="relative overflow-hidden rounded-[2rem] border border-slate-200/70 bg-[linear-gradient(180deg,#081120_0%,#0b1f35_55%,#10284b_100%)] p-6 text-white shadow-[0_30px_100px_rgba(2,8,23,.25)] xl:p-8">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(34,211,238,.16),transparent_28%),radial-gradient(circle_at_bottom_left,rgba(59,130,246,.12),transparent_30%)]"></div>

                    <div class="relative flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="grid h-12 w-12 place-items-center rounded-2xl bg-[linear-gradient(135deg,#2563eb_0%,#22d3ee_100%)] font-black text-white shadow-lg">
                                IT
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-200/80">IT Portal</p>
                                <h1 class="mt-1 text-xl font-black leading-tight text-white">Request Management</h1>
                            </div>
                        </div>

                        <a href="{{ route('home') }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                            กลับหน้า Home
                        </a>
                    </div>

                    <div class="relative mt-10 max-w-xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-200/80">Team Access</p>
                        <h2 class="mt-3 text-4xl font-black tracking-tight text-white sm:text-5xl">
                            เข้าสู่ระบบทีม IT
                        </h2>
                        <p class="mt-4 text-base leading-7 text-slate-300 sm:text-lg">
                            สำหรับ Admin และ IT DEV เพื่อเข้าดูคำร้อง Queue, รายงานสถานะ, และจัดการงานของทีมได้จากศูนย์กลางเดียว
                        </p>
                    </div>

                    <div class="relative mt-8 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-[1.4rem] border border-white/10 bg-white/5 p-4 backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Queue</p>
                            <p class="mt-2 text-sm font-bold text-white">รับงานเร็ว</p>
                            <p class="mt-1 text-xs text-slate-300">ดูคำร้องที่รอพิจารณา</p>
                        </div>
                        <div class="rounded-[1.4rem] border border-white/10 bg-white/5 p-4 backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Report</p>
                            <p class="mt-2 text-sm font-bold text-white">ติดตามสถานะ</p>
                            <p class="mt-1 text-xs text-slate-300">อนุมัติ / ไม่อนุมัติชัดเจน</p>
                        </div>
                        <div class="rounded-[1.4rem] border border-white/10 bg-white/5 p-4 backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Access</p>
                            <p class="mt-2 text-sm font-bold text-white">ควบคุมสิทธิ์</p>
                            <p class="mt-1 text-xs text-slate-300">เฉพาะ role ที่อนุญาต</p>
                        </div>
                    </div>

                    <div class="relative mt-8 rounded-[1.6rem] border border-white/10 bg-white/5 p-5 backdrop-blur">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-300">สถานะผู้ใช้</p>
                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl bg-white/10 px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Admin</p>
                                <p class="mt-1 text-sm font-bold text-white">Full Access</p>
                            </div>
                            <div class="rounded-2xl bg-white/10 px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">IT DEV (Read Only)</p>
                                <p class="mt-1 text-sm font-bold text-white">Manage Requests</p>
                            </div>
                            <div class="rounded-2xl bg-white/10 px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">IT DEV</p>
                                <p class="mt-1 text-sm font-bold text-white">Read Only</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_30px_100px_rgba(15,23,42,.14)] xl:p-8">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Sign In</p>
                            <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">เข้าสู่ระบบ</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                ใส่อีเมลและรหัสผ่านเพื่อเข้าใช้งานระบบ IT
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-right">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Secure Portal</p>
                            <p class="mt-1 text-sm font-bold text-slate-950">IT Access Only</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        @include('auth._it-login-form')
                    </div>

                    <div class="mt-6 rounded-[1.4rem] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-900">ก่อนเข้าสู่ระบบ</p>
                        <ul class="mt-2 space-y-2 text-sm text-slate-600">
                            <li>• ใช้บัญชีที่มี role เป็น Admin หรือ IT DEV</li>
                            <li>• บัญชีต้องมีสถานะ Active เท่านั้น</li>
                            <li>• หากยังไม่มีสิทธิ์ ให้ติดต่อผู้ดูแลระบบ</li>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
</html>
