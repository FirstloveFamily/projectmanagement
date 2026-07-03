<?php

namespace App\Filament\Resources\CompanyResource\Widgets;

use App\Models\Company;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class CompanyOverviewStats extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $record = request()->route('record');

        $company = $record instanceof Company
            ? $record
            : (is_numeric($record) ? Company::find($record) : null);

        if ($company) {
            $projects = $company->projects();

            $totalProjects = $projects->count();
            $activeProjects = (clone $projects)->where('status', 'active')->count();
            $completedProjects = (clone $projects)->where('status', 'completed')->count();
            $overdueProjects = (clone $projects)
                ->where('status', '!=', 'completed')
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today)
                ->count();

            return [
                Stat::make('โครงการทั้งหมด', number_format($totalProjects))
                    ->description($company->name)
                    ->icon('heroicon-o-briefcase')
                    ->color('primary'),
                Stat::make('กำลังดำเนินการ', number_format($activeProjects))
                    ->description('โครงการที่กำลังเดินหน้า')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info'),
                Stat::make('เสร็จสมบูรณ์', number_format($completedProjects))
                    ->description('โครงการที่ปิดจบแล้ว')
                    ->icon('heroicon-o-check-badge')
                    ->color('success'),
                Stat::make('ค้างเกินกำหนด', number_format($overdueProjects))
                    ->description('ต้องเร่งติดตาม')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger'),
            ];
        }

        $companyCount = Company::query()->count();
        $projectCount = Project::query()->count();
        $activeProjects = Project::query()->where('status', 'active')->count();
        $overdueProjects = Project::query()
            ->where('status', '!=', 'completed')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->count();

        return [
            Stat::make('บริษัททั้งหมด', number_format($companyCount))
                ->description('รายการบริษัทในระบบ')
                ->icon('heroicon-o-building-office-2')
                ->color('primary'),
            Stat::make('โครงการทั้งหมด', number_format($projectCount))
                ->description('โครงการที่ผูกกับบริษัท')
                ->icon('heroicon-o-briefcase')
                ->color('info'),
            Stat::make('บริษัทที่มีงานเดินหน้า', number_format($activeProjects))
                ->description('มีอย่างน้อย 1 โครงการกำลังดำเนินการ')
                ->icon('heroicon-o-arrow-path')
                ->color('warning'),
            Stat::make('มีงานค้าง', number_format($overdueProjects))
                ->description('มีอย่างน้อย 1 โครงการค้างเกินกำหนด')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
