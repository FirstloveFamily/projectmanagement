<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskChecklist;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ProjectOverviewStats extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();

        $projectCount = Project::query()->count();
        $taskCount = Task::query()->count();
        $completedTaskCount = Task::query()->where('status', 'done')->count();
        $activeTaskCount = Task::query()->whereIn('status', ['todo', 'in_progress'])->count();
        $overdueTaskCount = Task::query()
            ->where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->count();

        $checklistCount = TaskChecklist::query()->count();
        $doneChecklistCount = TaskChecklist::query()->where('is_done', true)->count();
        $checklistProgress = $checklistCount > 0
            ? round(($doneChecklistCount / $checklistCount) * 100)
            : 0;

        return [
            Stat::make('โครงการทั้งหมด', number_format($projectCount))
                ->description('จำนวนโครงการในระบบ')
                ->icon('heroicon-o-briefcase')
                ->color('primary'),
            Stat::make('งานทั้งหมด', number_format($taskCount))
                ->description("อยู่ใน {$projectCount} โครงการ")
                ->icon('heroicon-o-clipboard-document-check')
                ->color('info'),
            Stat::make('งานกำลังดำเนินการ', number_format($activeTaskCount))
                ->description('งานที่ยังไม่ปิดและกำลังเดินหน้า')
                ->icon('heroicon-o-arrow-path')
                ->color('warning'),
            Stat::make('งานเสร็จแล้ว', number_format($completedTaskCount))
                ->description('งานที่ปิดจบเรียบร้อย')
                ->icon('heroicon-o-check-badge')
                ->color('success'),
            Stat::make('งานค้างเกินกำหนด', number_format($overdueTaskCount))
                ->description('งานที่ยังไม่ปิดและพ้นกำหนดแล้ว')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
            Stat::make('เช็กงานเสร็จแล้ว', number_format($doneChecklistCount))
                ->description("ความคืบหน้า {$checklistProgress}% จาก {$checklistCount} รายการ")
                ->icon('heroicon-o-list-bullet')
                ->color('warning'),
        ];
    }
}
