<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - หน้าใบร้องขอ"
    description="หน้าใบร้องขอพร้อม IT login modal"
    active="dashboard"
>
    <div class="rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] backdrop-blur xl:p-7">
        @if (session('success'))
            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <section class="rounded-[1.8rem] border border-slate-200 bg-white p-6 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Request Portal</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                หน้าใบร้องขอ
            </h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                ใช้กรอกคำร้องเพื่อส่งให้ทีม IT พิจารณา พร้อมแนบเอกสารประกอบได้จากหน้านี้โดยตรง
            </p>

            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-[1.4rem] border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">คำร้องทั้งหมด</p>
                    <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($requestSummary['total'] ?? 0) }}</div>
                </div>
                <div class="rounded-[1.4rem] border border-amber-200 bg-amber-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-500">รอพิจารณา</p>
                    <div class="mt-3 text-3xl font-black text-amber-700">{{ number_format($requestSummary['pending'] ?? 0) }}</div>
                </div>
                <div class="rounded-[1.4rem] border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-500">อนุมัติแล้ว</p>
                    <div class="mt-3 text-3xl font-black text-emerald-700">{{ number_format($requestSummary['approved'] ?? 0) }}</div>
                </div>
            </div>
        </section>

        <div class="mt-6">
            @include('requests._request-form-home', ['companies' => $companies])
        </div>
    </div>

    <div id="it-login-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/75 p-4">
        <div class="absolute inset-0" data-login-close></div>
        <div class="relative z-10 w-full max-w-lg rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_30px_100px_rgba(0,0,0,.35)]">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">IT Login</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950">เข้าสู่ระบบทีม IT</h2>
                </div>
                <button type="button" data-login-close class="grid h-10 w-10 place-items-center rounded-full border border-slate-200 bg-slate-50 text-slate-500 transition hover:bg-slate-100">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                        <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <div class="mt-5">
                @include('auth._it-login-form')
            </div>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('it-login-modal');
            const openButtons = document.querySelectorAll('[data-login-open]');
            const closeButtons = document.querySelectorAll('[data-login-close]');

            if (!modal) {
                return;
            }

            const open = () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };

            const close = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };

            openButtons.forEach((button) => button.addEventListener('click', open));
            closeButtons.forEach((button) => button.addEventListener('click', close));

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    close();
                }
            });
        })();
    </script>
</x-frontend-layout>
