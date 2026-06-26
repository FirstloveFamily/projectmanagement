<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'โครงการในบริษัทนี้';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['user']))
            ->defaultSort('due_date')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('โครงการ')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => Str::limit((string) ($record->description ?? ''), 60)),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('เจ้าของ')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('สถานะ')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'กำลังดำเนินการ',
                        'on_hold' => 'พักงาน',
                        'completed' => 'เสร็จสมบูรณ์',
                        default => $state,
                    })
                    ->colors([
                        'info' => 'active',
                        'warning' => 'on_hold',
                        'success' => 'completed',
                    ]),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('ครบกำหนด')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record): string => match (true) {
                        filled($record->due_date) && $record->due_date->isPast() => 'danger',
                        filled($record->due_date) && $record->due_date->diffInDays(Carbon::today()) <= 7 => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('สถานะ')
                    ->options([
                        'active' => 'กำลังดำเนินการ',
                        'on_hold' => 'พักงาน',
                        'completed' => 'เสร็จสมบูรณ์',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('ดู'),
                Tables\Actions\EditAction::make()->label('แก้ไข'),
                Tables\Actions\DeleteAction::make()->label('ลบ'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
