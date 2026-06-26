<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TaskChecklistsRelationManager extends RelationManager
{
    protected static string $relationship = 'checklists';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('หัวข้อ')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_done')
                    ->label('ทำเสร็จแล้ว')
                    ->required(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('ลำดับ')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('หัวข้อ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_done')
                    ->label('สถานะ')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('ลำดับ')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_done')
                    ->label('สถานะ'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('เพิ่มรายการ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('แก้ไข'),
                Tables\Actions\DeleteAction::make()->label('ลบ'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('ลบรายการที่เลือก'),
                ]),
            ]);
    }
}
