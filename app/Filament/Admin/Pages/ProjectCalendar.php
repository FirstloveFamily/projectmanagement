<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\ProjectResource;
use App\Models\Project;
use Filament\Pages\Page;

class ProjectCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static string $view = 'filament.admin.pages.project-calendar';
    protected static ?string $navigationLabel = 'Project Calendar';
    protected static ?string $title = 'Project Overview Calendar';
    protected static ?int $navigationSort = 1;

    public function getProjectEvents(): array
    {
        return Project::query()
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->get()
            ->map(function (Project $project) {
                $color = match ($project->status) {
                    'planning' => '#94a3b8', // slate-400
                    'active' => '#4f46e5',   // indigo-600
                    'on_hold' => '#f59e0b',  // amber-500
                    'completed' => '#10b981', // emerald-500
                    'cancelled' => '#f43f5e', // rose-500
                    default => '#64748b',
                };

                return [
                    'id' => $project->id,
                    'title' => $project->name . ' (' . $project->progress . '%)',
                    'start' => $project->start_date->toDateString(),
                    'end' => $project->end_date->addDay()->toDateString(), // Calendar ends are exclusive
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'url' => ProjectResource::getUrl('edit', ['record' => $project->id]),
                ];
            })
            ->toArray();
    }
}
