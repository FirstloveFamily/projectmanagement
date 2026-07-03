<form method="POST" action="{{ route('it.login.store') }}" class="space-y-4">
    @csrf

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">อีเมล</label>
        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="it@example.com"
            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
            required
        >
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">รหัสผ่าน</label>
        <input
            type="password"
            name="password"
            placeholder="••••••••"
            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
            required
        >
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
        จำฉันไว้ในระบบ
    </label>

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
            <p class="font-semibold">เข้าสู่ระบบไม่สำเร็จ</p>
            <ul class="mt-2 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <button type="submit" class="w-full rounded-2xl bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)] px-5 py-3.5 text-sm font-bold text-white shadow-[0_18px_35px_rgba(37,99,235,.24)] transition hover:brightness-105">
        เข้าสู่ระบบ IT
    </button>
</form>
