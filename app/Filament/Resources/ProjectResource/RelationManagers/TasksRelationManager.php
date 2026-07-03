<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('เจ้าของงาน')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->default(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->user_id)
                    ->disabled()
                    ->dehydrated()
                    ->helperText('ระบบดึงค่านี้จากเจ้าของโครงการโดยอัตโนมัติ')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label('ชื่องาน')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('task_category')
                    ->label('ประเภทงาน')
                    ->default('งานทั่วไป')
                    ->placeholder('เช่น ติดตามเอกสาร, MIFC, ประสานงาน')
                    ->maxLength(255)
                    ->helperText('พิมพ์ชื่อหมวดเองได้ตามที่ทีมใช้งานจริง')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('รายละเอียด')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('สถานะ')
                    ->options([
                        'todo' => 'ยังไม่เริ่ม',
                        'in_progress' => 'กำลังทำ',
                        'done' => 'เสร็จแล้ว',
                    ])
                    ->required()
                    ->default('todo'),
                Forms\Components\Select::make('priority')
                    ->label('ความสำคัญ')
                    ->options([
                        'low' => 'ต่ำ',
                        'medium' => 'กลาง',
                        'high' => 'สูง',
                    ])
                    ->required()
                    ->default('medium'),
                Forms\Components\DatePicker::make('start_date')
                    ->label('วันที่เริ่ม')
                    ->default(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->start_date)
                    ->disabled()
                    ->displayFormat('d/m/Y')
                    ->helperText('ระบบดึงค่านี้จากวันที่เริ่มของโครงการโดยอัตโนมัติ')
                    ->required(),
                Forms\Components\DatePicker::make('due_date')
                    ->label('กำหนดเสร็จ')
                    ->default(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->due_date)
                    ->disabled()
                    ->displayFormat('d/m/Y')
                    ->helperText('ระบบดึงค่านี้จากวันครบกำหนดของโครงการโดยอัตโนมัติ')
                    ->required()
                    ->rule('after_or_equal:start_date'),
                Forms\Components\DateTimePicker::make('completed_at')
                    ->label('เสร็จจริงเมื่อ'),
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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('เจ้าของ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('ชื่องาน')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('task_category')
                    ->label('ประเภทงาน')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Task::taskCategoryLabel($state))
                    ->color(fn (?string $state): string => Task::taskCategoryColor($state))
                    ->visibleFrom('md'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'todo' => 'ยังไม่เริ่ม',
                        'in_progress' => 'กำลังทำ',
                        'done' => 'เสร็จแล้ว',
                        default => $state,
                    })
                    ->colors([
                        'gray' => 'todo',
                        'warning' => 'in_progress',
                        'success' => 'done',
                    ]),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'ต่ำ',
                        'medium' => 'กลาง',
                        'high' => 'สูง',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                    ]),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('วันที่เริ่ม')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('กำหนดเสร็จ')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('ลำดับ')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('สถานะ')
                    ->options([
                        'todo' => 'ยังไม่เริ่ม',
                        'in_progress' => 'กำลังทำ',
                        'done' => 'เสร็จแล้ว',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->label('ความสำคัญ')
                    ->options([
                        'low' => 'ต่ำ',
                        'medium' => 'กลาง',
                        'high' => 'สูง',
                    ]),
                Tables\Filters\Filter::make('task_category')
                    ->form([
                        Forms\Components\TextInput::make('task_category')
                            ->label('ประเภทงาน')
                            ->placeholder('ค้นหาชื่อหมวด เช่น MIFC, เอกสาร, ประสานงาน'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        $search = trim((string) ($data['task_category'] ?? ''));

                        return $query->when(
                            $search !== '',
                            fn (\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder => $query
                                ->where('task_category', 'like', '%' . $search . '%'),
                        );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('เพิ่มงาน'),
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
