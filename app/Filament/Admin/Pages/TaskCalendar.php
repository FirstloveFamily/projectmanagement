<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\TaskResource;
use App\Models\Task;
use Filament\Pages\Page;

class TaskCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static string $view = 'filament.admin.pages.task-calendar';
    protected static ?string $navigationLabel = 'Task Calendar';
    protected static ?string $title = 'Task Overview Calendar';
    protected static ?int $navigationSort = 2;

    public function getTaskEvents(): array
    {
        return Task::query()
            ->with('project')
            ->whereNotNull('start_date')
            ->whereNotNull('due_date')
            ->get()
            ->map(function (Task $task) {
                $color = match ($task->status) {
                    'todo' => '#94a3b8',        // slate-400 (ยังไม่เริ่ม)
                    'in_progress' => '#4f46e5', // indigo-600 (กำลังทำ)
                    'review' => '#f59e0b',      // amber-500 (รอตรวจสอบ)
                    'completed' => '#10b981',   // emerald-500 (เสร็จสิ้น)
                    default => '#64748b',
                };

                return [
                    'id' => $task->id,
                    'title' => '[' . ($task->project->name ?? 'N/A') . '] ' . $task->title,
                    'start' => $task->start_date->toDateString(),
                    'end' => $task->due_date->addDay()->toDateString(), // Calendar ends are exclusive
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'url' => TaskResource::getUrl('edit', ['record' => $task->id]),
                ];
            })
            ->toArray();
    }
}
