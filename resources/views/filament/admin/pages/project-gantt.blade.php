<x-filament-panels::page>
    <div 
        x-data="{
            data: @js($this->getGanttData()),
            init() {
                gantt.config.date_format = '%d-%m-%Y';
                gantt.init(this.$refs.gantt_container);
                gantt.parse(this.data);
            }
        }"
        class="bg-white p-4 rounded-xl shadow-sm border border-gray-100"
    >
        <link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css">
        <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>

        <div x-ref="gantt_container" style="width: 100%; height: 600px;"></div>
    </div>
</x-filament-panels::page>
