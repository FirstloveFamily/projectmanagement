<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProjectResource\Pages;
use App\Filament\Admin\Pages\ProjectGantt;
use App\Filament\Admin\Resources\ProjectReviewResource;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Project Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('company_id')
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label('บริษัท (Company)'),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique(Project::class, 'slug', ignoreRecord: true),
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Timeline & Status')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date'),
                                Forms\Components\DatePicker::make('end_date'),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'planning' => 'กำลังวางแผน',
                                        'active' => 'กำลังดำเนินการ',
                                        'on_hold' => 'ระงับชั่วคราว',
                                        'completed' => 'เสร็จสมบูรณ์',
                                        'cancelled' => 'ยกเลิก',
                                    ])
                                    ->required()
                                    ->default('planning'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('progress')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%'),
                                Forms\Components\Toggle::make('is_public')
                                    ->label('Publicly Visible')
                                    ->default(false),
                            ]),
                        Forms\Components\Select::make('created_by')
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->default(auth()->id()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->label('บริษัท')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'planning' => 'กำลังวางแผน',
                        'active' => 'กำลังดำเนินการ',
                        'on_hold' => 'ระงับชั่วคราว',
                        'completed' => 'เสร็จสมบูรณ์',
                        'cancelled' => 'ยกเลิก',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'planning' => 'gray',
                        'active' => 'info',
                        'on_hold' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('progress')
                    ->numeric()
                    ->sortable()
                    ->suffix('%'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Creator')
                    ->sortable(),
            ])
            ->defaultGroup('company.name')
            ->groups([
                Tables\Grouping\Group::make('company.name')
                    ->label('บริษัท (Company)')
                    ->collapsible(),
                Tables\Grouping\Group::make('status')
                    ->label('สถานะ (Status)'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->label('กรองตามบริษัท')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'planning' => 'Planning',
                        'active' => 'Active',
                        'on_hold' => 'On Hold',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('review')
                    ->label('ติดตามผล')
                    ->icon('heroicon-o-presentation-chart-line')
                    ->color('info')
                    ->url(fn (Project $record): string => ProjectReviewResource::getUrl('create', ['project_id' => $record->id])),
                Tables\Actions\Action::make('public_link')
                    ->label('Public')
                    ->icon('heroicon-o-globe-alt')
                    ->url(fn (Project $record): string => route('projects.public.show', ['slug' => $record->slug]))
                    ->openUrlInNewTab()
                    ->visible(fn (Project $record): bool => $record->is_public),
                Tables\Actions\Action::make('gantt')
                    ->label('Gantt')
                    ->icon('heroicon-o-chart-bar')
                    ->url(fn (Project $record): string => route('filament.admin.pages.project-gantt', ['project' => $record->id])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
