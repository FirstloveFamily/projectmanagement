<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProjectReviewResource\Pages;
use App\Models\ProjectReview;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectReviewResource extends Resource
{
    protected static ?string $model = ProjectReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Post-Project Review';
    protected static ?string $modelLabel = 'ติดตามผลงาน';
    protected static ?string $pluralModelLabel = 'ติดตามผลงาน';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Evaluation')
                    ->schema([
                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} (" . ($record->company?->name ?? 'ไม่มีบริษัท') . ")")
                            ->required()
                            ->searchable()
                            ->label('โปรเจกต์ (และบริษัท)'),
                        Forms\Components\DatePicker::make('review_date')
                            ->required()
                            ->default(now())
                            ->label('วันที่บันทึก'),
                        Forms\Components\Select::make('usage_status')
                            ->options([
                                'stable' => 'เสถียร (Stable)',
                                'bugs_found' => 'พบจุดบกพร่อง (Bugs found)',
                                'improvement_needed' => 'ต้องปรับปรุง (Improvement needed)',
                                'outdated' => 'ตกรุ่น (Outdated/Decommissioned)',
                            ])
                            ->required()
                            ->default('stable')
                            ->label('สถานะการใช้งานปัจจุบัน'),
                        Forms\Components\Select::make('satisfaction_rating')
                            ->options([
                                5 => '⭐⭐⭐⭐⭐ (ดีมาก)',
                                4 => '⭐⭐⭐⭐ (ดี)',
                                3 => '⭐⭐⭐ (ปานกลาง)',
                                2 => '⭐⭐ (ควรปรับปรุง)',
                                1 => '⭐ (ไม่พอใจ)',
                            ])
                            ->required()
                            ->default(5)
                            ->label('ระดับความพึงพอใจของลูกค้า'),
                    ]),
                Forms\Components\Section::make('Detailed Feedback')
                    ->schema([
                        Forms\Components\Textarea::make('user_feedback')
                            ->rows(3)
                            ->label('ความคิดเห็นจากผู้ใช้ (Feedback)'),
                        Forms\Components\Textarea::make('technical_notes')
                            ->rows(3)
                            ->label('บันทึกด้านเทคนิค (Technical Notes)'),
                        Forms\Components\Select::make('reviewer_id')
                            ->relationship('reviewer', 'name')
                            ->default(auth()->id())
                            ->label('ผู้บันทึกข้อมูล'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.company.name')
                    ->label('บริษัท')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->sortable()
                    ->searchable()
                    ->label('ชื่อโปรเจกต์'),
                Tables\Columns\TextColumn::make('usage_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'stable' => 'success',
                        'bugs_found' => 'danger',
                        'improvement_needed' => 'warning',
                        'outdated' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'stable' => 'เสถียร (Stable)',
                        'bugs_found' => 'พบจุดบกพร่อง',
                        'improvement_needed' => 'ต้องปรับปรุง',
                        'outdated' => 'ตกรุ่น',
                        default => $state,
                    })
                    ->label('สถานะการใช้งาน'),
                Tables\Columns\TextColumn::make('satisfaction_rating')
                    ->label('คะแนน')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', (int)$state)),
                Tables\Columns\TextColumn::make('review_date')
                    ->date()
                    ->sortable()
                    ->label('วันที่บันทึก'),
                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label('ผู้บันทึก'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('usage_status')
                    ->options([
                        'stable' => 'เสถียร (Stable)',
                        'bugs_found' => 'พบจุดบกพร่อง',
                        'improvement_needed' => 'ต้องปรับปรุง',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectReviews::route('/'),
            'create' => Pages\CreateProjectReview::route('/create'),
            'edit' => Pages\EditProjectReview::route('/{record}/edit'),
        ];
    }
}
