<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'งาน';
    protected static ?string $navigationGroup = 'การจัดการงาน';
    protected static ?string $navigationParentItem = 'โครงการ';
    protected static ?string $modelLabel = 'งาน';
    protected static ?string $pluralModelLabel = 'งาน';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ข้อมูลงาน')
                    ->description('กำหนดผู้รับผิดชอบ โครงการ และรายละเอียดหลัก')
                    ->schema([
                Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('เจ้าของงาน')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('project_id')
                                    ->label('โครงการ')
                                    ->relationship('project', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('task_category')
                                    ->label('ประเภทงาน')
                                    ->default('งานทั่วไป')
                                    ->placeholder('เช่น ติดตามเอกสาร, MIFC, ประสานงาน')
                                    ->maxLength(255)
                                    ->required()
                                    ->helperText('พิมพ์ชื่อหมวดเองได้ เช่น งานทั่วไป, ติดตามเอกสาร, MIFC'),
                                Forms\Components\TextInput::make('title')
                                    ->label('ชื่องาน')
                                    ->placeholder('เช่น ปรับปรุงหน้าแอดมิน')
                                    ->required()
                                    ->maxLength(255),
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
                                Forms\Components\Textarea::make('description')
                                    ->label('รายละเอียด')
                                    ->placeholder('อธิบายงานอย่างสั้นและชัดเจน')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),
                    ]),
                Forms\Components\Section::make('กำหนดเวลา')
                    ->description('ตั้งช่วงเริ่มต้น วันครบกำหนด และเวลาที่เสร็จจริง')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('วันที่เริ่ม')
                                    ->displayFormat('d/m/Y')
                                    ->required(),
                                Forms\Components\DatePicker::make('due_date')
                                    ->label('กำหนดเสร็จ')
                                    ->displayFormat('d/m/Y')
                                    ->required()
                                    ->rule('after_or_equal:start_date'),
                                Forms\Components\DateTimePicker::make('completed_at')
                                    ->label('เสร็จจริงเมื่อ'),
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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('เจ้าของ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('โครงการ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('task_category')
                    ->label('ประเภทงาน')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Task::taskCategoryLabel($state))
                    ->color(fn (?string $state): string => Task::taskCategoryColor($state))
                    ->visibleFrom('md'),
                Tables\Columns\TextColumn::make('title')
                    ->label('ชื่องาน')
                    ->searchable()
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('เสร็จจริงเมื่อ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('ลำดับ')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('เจ้าของ')
                    ->relationship('user', 'name'),
                Tables\Filters\SelectFilter::make('project_id')
                    ->label('โครงการ')
                    ->relationship('project', 'name'),
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
        return [
            RelationManagers\TaskChecklistsRelationManager::class,
            RelationManagers\TaskCommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
