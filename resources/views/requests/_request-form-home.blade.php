<section class="rounded-[1.8rem] border border-slate-200 bg-white p-5 shadow-[0_14px_40px_rgba(15,23,42,.07)] xl:p-7">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">System Request</p>
            <h2 class="mt-2 text-2xl font-black text-slate-950">ใบร้องขอเพิ่มระบบและแก้ไขปรับปรุง</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                กรอกข้อมูลให้ครบเพื่อส่งให้ทีม IT ตรวจสอบและพิจารณาต่อ
            </p>
        </div>

        @auth
            @can('manage-requests')
                <a href="{{ route('requests.queue') }}" class="inline-flex rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                    ไปหน้า Queue
                </a>
            @endcan
        @endauth
    </div>

    @if (session('success'))
        <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
            <p class="font-semibold">ตรวจพบข้อมูลไม่ครบ</p>
            <ul class="mt-2 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="mt-6 space-y-4" method="POST" enctype="multipart/form-data" action="{{ route('requests.store') }}">
        @csrf

        <div class="grid gap-4 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">ชื่อ-สกุล</label>
                <input type="text" name="requester_name" value="{{ old('requester_name', auth()->user()?->name ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">อีเมล</label>
                <input type="email" name="requester_email" value="{{ old('requester_email', auth()->user()?->email ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">ตำแหน่ง</label>
                <input type="text" name="position" value="{{ old('position') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">แผนก</label>
                <input type="text" name="department" value="{{ old('department') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">เบอร์โทร</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">บริษัท/หน่วยงาน</label>
                <select name="company_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                    <option value="">ไม่ระบุ</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" @selected((string) old('company_id') === (string) $company->id)>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">ประเภทคำร้อง</label>
                <select name="request_type" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                    <option value="add" @selected(old('request_type', 'improve') === 'add')>เพิ่มระบบ</option>
                    <option value="improve" @selected(old('request_type', 'improve') === 'improve')>ปรับปรุง</option>
                    <option value="bug" @selected(old('request_type') === 'bug')>แก้ไขบั๊ก</option>
                    <option value="other" @selected(old('request_type') === 'other')>อื่น ๆ</option>
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">ความสำคัญ</label>
                <select name="priority" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
                    <option value="low" @selected(old('priority') === 'low')>Low</option>
                    <option value="medium" @selected(old('priority', 'medium') === 'medium')>Medium</option>
                    <option value="high" @selected(old('priority') === 'high')>High</option>
                    <option value="urgent" @selected(old('priority') === 'urgent')>Urgent</option>
                </select>
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">หัวข้อคำร้อง</label>
            <input type="text" name="title" value="{{ old('title') }}" placeholder="เช่น ขอเพิ่มระบบรายงาน..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100" required>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">วัตถุประสงค์</label>
                <textarea name="objective" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">{{ old('objective') }}</textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">ผลลัพธ์ที่คาดหวัง</label>
                <textarea name="desired_output" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">{{ old('desired_output') }}</textarea>
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">รายละเอียดความต้องการ</label>
            <textarea name="details" rows="6" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">{{ old('details') }}</textarea>
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">ผลกระทบ / ความเสี่ยง</label>
            <textarea name="impact" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">{{ old('impact') }}</textarea>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">วันที่เริ่มที่ต้องการ</label>
                <input type="date" name="target_start_date" value="{{ old('target_start_date') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">วันที่ต้องการเสร็จ</label>
                <input type="date" name="target_due_date" value="{{ old('target_due_date') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Reference URL</label>
                <input type="url" name="reference_url" value="{{ old('reference_url') }}" placeholder="https://..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">แนบเอกสารหรือรูป</label>
                <input
                    type="file"
                    name="attachments[]"
                    data-attachment-input
                    multiple
                    accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100"
                >
                <p class="mt-2 text-xs text-slate-500">รองรับไฟล์รูป, PDF, Word, Excel และ TXT</p>
            </div>
        </div>

        <div class="rounded-[1.5rem] border border-dashed border-slate-200 bg-slate-50 p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">พรีวิวก่อนส่ง</p>
                    <h3 class="mt-1 text-lg font-black text-slate-950">ไฟล์ที่เลือกจากเครื่อง</h3>
                </div>
                <p class="text-xs text-slate-500">แสดงเฉพาะไฟล์ที่เพิ่งเลือกยังไม่ถูกอัปโหลด</p>
            </div>
            <div data-attachment-preview-list class="mt-4 hidden grid gap-3 sm:grid-cols-2 xl:grid-cols-3"></div>
            <p data-attachment-preview-empty class="mt-4 text-sm text-slate-500">ยังไม่ได้เลือกไฟล์</p>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-[linear-gradient(90deg,#2563eb_0%,#06b6d4_100%)] px-5 py-3.5 text-sm font-bold text-white shadow-[0_18px_35px_rgba(37,99,235,.24)] transition hover:brightness-105">
                ส่งคำร้อง
            </button>
        </div>
    </form>
</section>

<script>
    (() => {
        const input = document.querySelector('[data-attachment-input]');
        const previewList = document.querySelector('[data-attachment-preview-list]');
        const previewEmpty = document.querySelector('[data-attachment-preview-empty]');
        let selectedFiles = [];

        if (!input || !previewList || !previewEmpty) {
            return;
        }

        const iconMarkup = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-10 w-10">
                <path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M14 2v5h5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        `;

        const syncInputFiles = () => {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach((file) => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
        };

        const isSameFile = (left, right) => (
            left.name === right.name
            && left.size === right.size
            && left.type === right.type
            && left.lastModified === right.lastModified
        );

        const render = () => {
            previewList.innerHTML = '';

            if (selectedFiles.length === 0) {
                previewList.classList.add('hidden');
                previewEmpty.classList.remove('hidden');
                return;
            }

            previewList.classList.remove('hidden');
            previewEmpty.classList.add('hidden');

            selectedFiles.forEach((file, index) => {
                const card = document.createElement('div');
                card.className = 'relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-3';

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'absolute right-3 top-3 grid h-8 w-8 place-items-center rounded-full border border-rose-200 bg-white text-rose-500 shadow-sm transition hover:bg-rose-50 hover:text-rose-600';
                removeButton.setAttribute('aria-label', 'ลบไฟล์ที่เลือก');
                removeButton.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4">
                        <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                `;
                removeButton.addEventListener('click', () => {
                    selectedFiles.splice(index, 1);
                    syncInputFiles();
                    render();
                });
                card.appendChild(removeButton);

                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.className = 'h-40 w-full rounded-xl border border-slate-200 object-cover pr-8';
                    img.alt = file.name;
                    img.src = URL.createObjectURL(file);
                    img.onload = () => URL.revokeObjectURL(img.src);
                    card.appendChild(img);
                } else {
                    const box = document.createElement('div');
                    box.className = 'flex h-40 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-400';
                    box.innerHTML = iconMarkup;
                    card.appendChild(box);
                }

                const meta = document.createElement('div');
                meta.className = 'mt-3';
                meta.innerHTML = `
                    <p class="line-clamp-2 text-sm font-bold text-slate-950">${file.name}</p>
                    <p class="mt-1 text-xs text-slate-500">${Math.max(1, Math.round(file.size / 1024))} KB</p>
                `;
                card.appendChild(meta);
                previewList.appendChild(card);
            });
        };

        input.addEventListener('change', () => {
            const newlyPickedFiles = Array.from(input.files || []);

            newlyPickedFiles.forEach((file) => {
                if (!selectedFiles.some((existing) => isSameFile(existing, file))) {
                    selectedFiles.push(file);
                }
            });

            syncInputFiles();
            render();
        });

        render();
    })();
</script>
