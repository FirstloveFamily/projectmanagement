<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Project;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'active')->count();
        $overdueTasks = Task::where('status', '!=', 'completed')
            ->where('due_date', '<', now())
            ->count();
            
        $completedTasks = Task::where('status', 'completed')->count();
        $totalTasks = Task::count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

         return [
        //     Stat::make('Total Projects', $totalProjects)
        //         ->description('All managed projects')
        //         ->chart([7, 2, 10, 3, 15, 4, 17])
        //         ->color('info'),
        //     Stat::make('Active Projects', $activeProjects)
        //         ->description('Projects currently in progress')
        //         ->color('success'),
        //     Stat::make('Overdue Tasks', $overdueTasks)
        //         ->description('Tasks past their due date')
        //         ->color('danger'),
        //     Stat::make('Completion Rate', $completionRate . '%')
        //         ->description('Total task completion percentage')
        //         ->chart([$completionRate, 100 - $completionRate])
        //         ->color('warning'),
        ];
    }
}
