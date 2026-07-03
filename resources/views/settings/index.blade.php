<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - Settings"
    description="Frontend settings page"
    active="settings"
>
    <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">หน้า Settings</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                ตั้งค่าระบบ
            </h2>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                พื้นที่สำหรับการตั้งค่า theme, เมนู, และค่าเริ่มต้นของระบบ
            </p>
        </div>
    </div>

    <div class="mt-6 rounded-[1.6rem] border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-slate-500">
        หน้านี้เตรียมไว้สำหรับการตั้งค่าระบบในอนาคต
    </div>
</x-frontend-layout>
