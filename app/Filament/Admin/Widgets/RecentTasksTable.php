<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentTasksTable extends BaseWidget
{
    protected static ?string $heading = 'ตารางงานที่กำลังดำเนินการ (ช่วงนี้)';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->whereIn('status', ['todo', 'in_progress'])
                    ->orderBy('due_date', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('project.name')
                    ->label('โปรเจกต์ (Project)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('ชื่องาน (Task)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('ผู้รับผิดชอบ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('สถานะ')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'todo' => 'รอดำเนินการ',
                        'in_progress' => 'กำลังดำเนินการ',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'todo' => 'gray',
                        'in_progress' => 'warning',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('กำหนดส่ง')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->due_date && $record->due_date->isPast() ? 'danger' : null),
            ])
            ->paginated([5, 10]);
    }
}
