<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ProjectResource\Widgets\ProjectOverviewStats;
use App\Models\Project;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected static ?string $title = 'ภาพรวมโครงการ';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('เพิ่มโครงการ')
                ->icon('heroicon-m-plus'),
        ];
    }

    public function getTabs(): array
    {
        $today = Carbon::today();

        return [
            'all' => Tab::make('ทั้งหมด')
                ->badge(Project::query()->count())
                ->badgeColor('gray')
                ->icon('heroicon-m-squares-2x2'),
            'active' => Tab::make('กำลังดำเนินการ')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'active'))
                ->badge(Project::query()->where('status', 'active')->count())
                ->badgeColor('info')
                ->icon('heroicon-m-arrow-path'),
            'on_hold' => Tab::make('พักงาน')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'on_hold'))
                ->badge(Project::query()->where('status', 'on_hold')->count())
                ->badgeColor('warning')
                ->icon('heroicon-m-pause'),
            'completed' => Tab::make('เสร็จสมบูรณ์')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'completed'))
                ->badge(Project::query()->where('status', 'completed')->count())
                ->badgeColor('success')
                ->icon('heroicon-m-check-badge'),
            'overdue' => Tab::make('ค้างเกินกำหนด')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('status', '!=', 'completed')
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $today))
                ->badge(Project::query()
                    ->where('status', '!=', 'completed')
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $today)
                    ->count())
                ->badgeColor('danger')
                ->icon('heroicon-m-exclamation-triangle'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'active';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProjectOverviewStats::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | string | array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'xl' => 5,
        ];
    }
}
