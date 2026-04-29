<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'active')->count();
        $completedProjects = Project::where('status', 'completed')->count();

        return [
            Stat::make('โปรเจกต์ทั้งหมด (Total)', $totalProjects)
                ->description('จำนวนโปรเจกต์ทั้งหมดในระบบ')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary'),
            Stat::make('กำลังดำเนินการ (Active)', $activeProjects)
                ->description('โปรเจกต์ที่กำลังทำงานอยู่')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),
            Stat::make('เสร็จสมบูรณ์ (Completed)', $completedProjects)
                ->description('โปรเจกต์ที่เสร็จสิ้นแล้ว')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
