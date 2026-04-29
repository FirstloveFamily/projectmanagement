<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ProjectsChart extends ChartWidget
{
    protected static ?string $heading = 'โปรเจกต์ใหม่ในแต่ละเดือน (ปีนี้)';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = [];
        $months = [];

        for ($month = 1; $month <= 12; $month++) {
            $months[] = Carbon::create()->month($month)->translatedFormat('M');
            $data[] = Project::whereYear('start_date', date('Y'))
                ->whereMonth('start_date', $month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'จำนวนโปรเจกต์ใหม่',
                    'data' => $data,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
