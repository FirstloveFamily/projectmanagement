<x-frontend-layout
    title="{{ config('app.name', 'Laravel') }} - รายงานคำร้อง"
    description="รายงานสถานะคำร้องทั้งหมด"
    active="requests-report"
>
    @php
        $statusLabels = [
            'pending' => 'รอพิจารณา',
            'approved' => 'อนุมัติแล้ว',
            'rejected' => 'ไม่อนุมัติ',
        ];

        $statusStyles = [
            'pending' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'rejected' => 'bg-rose-50 text-rose-700 ring-rose-200',
        ];
    @endphp

    <div class="rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-[0_20px_70px_rgba(15,23,42,.08)] backdrop-blur xl:p-7">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Request Report</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                    รายงานสถานะคำร้อง
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                    หน้านี้ช่วยให้เห็นว่าคำร้องใบไหนรอพิจารณา อนุมัติแล้ว หรือไม่อนุมัติ พร้อมเปิดดูรายละเอียดรายใบได้ทันที
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('requests.queue') }}" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200">
                    ไปหน้า Queue
                </a>
                <a href="{{ route('requests.index') }}" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                    ไปหน้าส่งคำร้อง
                </a>
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[1.4rem] border border-slate-200 bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">ทั้งหมด</p>
                <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($summary['total']) }}</div>
            </div>
            <div class="rounded-[1.4rem] border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-500">รอพิจารณา</p>
                <div class="mt-3 text-3xl font-black text-amber-700">{{ number_format($summary['pending']) }}</div>
            </div>
            <div class="rounded-[1.4rem] border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-500">อนุมัติแล้ว</p>
                <div class="mt-3 text-3xl font-black text-emerald-700">{{ number_format($summary['approved']) }}</div>
            </div>
            <div class="rounded-[1.4rem] border border-rose-200 bg-rose-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-rose-500">ไม่อนุมัติ</p>
                <div class="mt-3 text-3xl font-black text-rose-700">{{ number_format($summary['rejected']) }}</div>
            </div>
        </div>

        <div class="mt-6 rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)]">
            <form method="GET" action="{{ route('requests.report') }}" class="grid gap-3 lg:grid-cols-[1fr_220px_auto]">
                <input
                    type="text"
                    name="q"
                    value="{{ $filters['q'] }}"
                    placeholder="ค้นหาเลขคำร้อง, ชื่อ, หัวข้อ, บริษัท..."
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                >
                <select name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                    <option value="all" @selected($filters['status'] === 'all')>ทุกสถานะ</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                    ค้นหา
                </button>
            </form>
        </div>

        <div class="mt-6 overflow-hidden rounded-[1.8rem] border border-slate-200 bg-white shadow-[0_14px_40px_rgba(15,23,42,.07)]">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-black text-slate-950">รายการคำร้อง</h2>
                <p class="mt-1 text-sm text-slate-500">{{ number_format($requests->count()) }} รายการ</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-[0.2em] text-slate-500">เลขคำร้อง</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-[0.2em] text-slate-500">หัวข้อ</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-[0.2em] text-slate-500">ผู้ร้องขอ</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-[0.2em] text-slate-500">บริษัท</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-[0.2em] text-slate-500">สถานะ</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-[0.2em] text-slate-500">อัปเดตล่าสุด</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-[0.2em] text-slate-500"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($requests as $requestItem)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-4 text-sm font-bold text-slate-950">#{{ $requestItem->id }}</td>
                                <td class="px-5 py-4">
                                    <div class="max-w-md">
                                        <p class="font-bold text-slate-950">{{ $requestItem->title }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $requestItem->request_type }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">
                                    <p class="font-semibold text-slate-900">{{ $requestItem->requester_name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $requestItem->requester_email ?: 'ไม่ระบุ' }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $requestItem->company?->name ?? 'ไม่ระบุบริษัท' }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $statusStyles[$requestItem->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                        {{ $statusLabels[$requestItem->status] ?? $requestItem->status }}
                                    </span>
                                    <div class="mt-2 text-xs text-slate-500">
                                        @if ($requestItem->status === 'approved')
                                            อนุมัติโดย {{ $requestItem->approvedBy?->name ?? 'ไม่ระบุ' }}
                                        @elseif ($requestItem->status === 'rejected')
                                            ไม่อนุมัติโดย {{ $requestItem->rejectedBy?->name ?? 'ไม่ระบุ' }}
                                        @else
                                            ยังรอการพิจารณา
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-500">
                                    {{ $requestItem->updated_at?->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('requests.show', $requestItem) }}" class="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90">
                                        ดูรายละเอียด
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-frontend-layout>
