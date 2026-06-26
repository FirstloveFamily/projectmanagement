<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskCommentResource\Pages;
use App\Models\TaskComment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaskCommentResource extends Resource
{
    protected static ?string $model = TaskComment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?string $navigationLabel = 'คอมเมนต์';
    protected static ?string $navigationGroup = 'การจัดการงาน';
    protected static ?string $navigationParentItem = 'โครงการ';
    protected static ?string $modelLabel = 'คอมเมนต์';
    protected static ?string $pluralModelLabel = 'คอมเมนต์';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ข้อมูลความคิดเห็น')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('task_id')
                                    ->label('งาน')
                                    ->relationship('task', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('user_id')
                                    ->label('ผู้เขียน')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\Textarea::make('body')
                                    ->label('ข้อความ')
                                    ->placeholder('พิมพ์ความคิดเห็นหรือข้อเสนอแนะที่เกี่ยวกับงานนี้')
                                    ->required()
                                    ->rows(5)
                                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('ผู้เขียน')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('body')
                    ->label('ข้อความ')
                    ->limit(80)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('สร้างเมื่อ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('task_id')
                    ->label('งาน')
                    ->relationship('task', 'title'),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('ผู้เขียน')
                    ->relationship('user', 'name'),
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
            'index' => Pages\ListTaskComments::route('/'),
            'create' => Pages\CreateTaskComment::route('/create'),
            'edit' => Pages\EditTaskComment::route('/{record}/edit'),
        ];
    }
}
