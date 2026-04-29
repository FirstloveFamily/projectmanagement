<x-filament-panels::page>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <!-- Legend -->
        <div class="flex flex-wrap items-center gap-4 mb-6">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full" style="background-color: #94a3b8;"></span>
                <span class="text-xs font-medium text-slate-600 uppercase tracking-wider">กำลังวางแผน</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full" style="background-color: #4f46e5;"></span>
                <span class="text-xs font-medium text-slate-600 uppercase tracking-wider">กำลังดำเนินการ</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full" style="background-color: #f59e0b;"></span>
                <span class="text-xs font-medium text-slate-600 uppercase tracking-wider">ระงับชั่วคราว</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full" style="background-color: #10b981;"></span>
                <span class="text-xs font-medium text-slate-600 uppercase tracking-wider">เสร็จสมบูรณ์</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full" style="background-color: #f43f5e;"></span>
                <span class="text-xs font-medium text-slate-600 uppercase tracking-wider">ยกเลิก</span>
            </div>
        </div>

        <div x-data="{
            events: @js($this->getProjectEvents()),
            init() {
                const calendarEl = this.$refs.calendar;
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listMonth'
                    },
                    events: this.events,
                    height: 'auto',
                    eventClick: function(info) {
                        if (info.event.url) {
                            window.location.href = info.event.url;
                            info.jsEvent.preventDefault();
                        }
                    },
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        meridiem: false
                    },
                    themeSystem: 'standard'
                });
                calendar.render();
            }
        }" wire:ignore>
            <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
            <style>
                :root {
                    --fc-border-color: #f1f5f9;
                    --fc-today-bg-color: #f8fafc;
                    --fc-button-bg-color: #4f46e5;
                    --fc-button-border-color: #4f46e5;
                    --fc-button-hover-bg-color: #4338ca;
                    --fc-button-hover-border-color: #4338ca;
                    --fc-button-active-bg-color: #3730a3;
                    --fc-button-active-border-color: #3730a3;
                }

                .fc-toolbar-title {
                    font-size: 1.25rem !important;
                    font-weight: 700 !important;
                    color: #1e293b;
                }

                .fc .fc-button-primary {
                    border-radius: 0.5rem;
                    font-weight: 600;
                    font-size: 0.875rem;
                    text-transform: capitalize;
                    padding: 0.5rem 1rem;
                }

                .fc .fc-col-header-cell-cushion {
                    padding: 0.75rem 0.5rem !important;
                    text-transform: uppercase;
                    font-size: 0.75rem;
                    letter-spacing: 0.05em;
                    color: #64748b;
                    font-weight: 700;
                }

                .fc-event {
                    cursor: pointer;
                    border-radius: 6px !important;
                    padding: 2px 4px !important;
                    border: none !important;
                    margin-bottom: 2px !important;
                }

                .fc-event-title {
                    font-weight: 600 !important;
                    font-size: 0.75rem !important;
                }
            </style>
            <div x-ref="calendar"></div>
        </div>
    </div>
</x-filament-panels::page>
