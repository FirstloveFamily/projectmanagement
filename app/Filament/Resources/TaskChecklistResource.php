<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskChecklistResource\Pages;
use App\Models\TaskChecklist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaskChecklistResource extends Resource
{
    protected static ?string $model = TaskChecklist::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'เช็กงาน';
    protected static ?string $navigationGroup = 'การจัดการงาน';
    protected static ?string $navigationParentItem = 'โครงการ';
    protected static ?string $modelLabel = 'เช็กงาน';
    protected static ?string $pluralModelLabel = 'เช็กงาน';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ข้อมูลรายการตรวจสอบ')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('task_id')
                                    ->label('งาน')
                                    ->relationship('task', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('title')
                                    ->label('หัวข้อ')
                                    ->placeholder('เช่น ตรวจสอบการแสดงผลบนมือถือ')
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
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('task.title')
                    ->label('งาน')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('หัวข้อ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_done')
                    ->label('สถานะ')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('ลำดับ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('สร้างเมื่อ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('task_id')
                    ->label('งาน')
                    ->relationship('task', 'title'),
                Tables\Filters\TernaryFilter::make('is_done')
                    ->label('สถานะ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('แก้ไข'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('ลบรายการที่เลือก'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaskChecklists::route('/'),
            'create' => Pages\CreateTaskChecklist::route('/create'),
            'edit' => Pages\EditTaskChecklist::route('/{record}/edit'),
        ];
    }
}
