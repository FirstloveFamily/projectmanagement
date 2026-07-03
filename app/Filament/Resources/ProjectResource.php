<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'โครงการ';
    protected static ?string $navigationGroup = 'การจัดการงาน';
    protected static ?string $modelLabel = 'โครงการ';
    protected static ?string $pluralModelLabel = 'โครงการ';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ข้อมูลโครงการ')
                    ->description('รายละเอียดหลักของโครงการและผู้รับผิดชอบ')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('company_id')
                                    ->label('บริษัท')
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(fn () => request()->filled('company_id') ? (int) request()->query('company_id') : null)
                                    ->required(),
                                Forms\Components\Select::make('user_id')
                                    ->label('เจ้าของโครงการ')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->label('ชื่อโครงการ')
                                    ->placeholder('เช่น ปรับปรุงระบบงานหน้าแอดมิน')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('status')
                                    ->label('สถานะ')
                                    ->options([
                                        'active' => 'กำลังดำเนินการ',
                                        'on_hold' => 'พักงาน',
                                        'completed' => 'เสร็จสมบูรณ์',
                                    ])
                                    ->required()
                                    ->default('active'),
                                Forms\Components\Textarea::make('description')
                                    ->label('รายละเอียด')
                                    ->placeholder('อธิบายขอบเขตหรือเป้าหมายของโครงการ')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),
                    ]),
                Forms\Components\Section::make('กำหนดเวลา')
                    ->description('กำหนดช่วงเริ่มต้นและวันครบกำหนดของโครงการ')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('วันที่เริ่ม')
                                    ->displayFormat('d/m/Y')
                                    ->required(),
                                Forms\Components\DatePicker::make('due_date')
                                    ->label('วันที่คาดว่าจะเสร็จ')
                                    ->displayFormat('d/m/Y')
                                    ->required()
                                    ->rule('after_or_equal:start_date'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                return $query
                    ->with(['company', 'user']);
            })
            ->defaultSort('due_date')
            ->striped()
            ->description('ภาพรวมโครงการพร้อมความคืบหน้า สถานะ และกำหนดส่งล่าสุด')
            ->groups([
                Group::make('status')
                    ->label('สถานะ')
                    ->titlePrefixedWithLabel(false)
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn (Project $record): string => match ($record->status) {
                        'active' => 'กำลังดำเนินการ',
                        'on_hold' => 'พักงาน',
                        'completed' => 'เสร็จสมบูรณ์',
                        default => $record->status,
                    }),
            ])
            ->defaultGroup('status')
            ->groupingSettingsHidden()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('โครงการ')
                    ->searchable()
                    ->sortable()
                    ->size(TextColumnSize::Medium)
                    ->description(function (Project $record): string {
                        if (blank($record->description)) {
                            return 'ไม่มีรายละเอียดเพิ่มเติม';
                        }

                        return Str::of($record->description)
                            ->squish()
                            ->limit(60)
                            ->toString();
                    }),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('บริษัท')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->size(TextColumnSize::Small)
                    ->visibleFrom('lg'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('วันที่คาดว่าจะเสร็จ')
                    ->date('d/m/Y')
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->color(fn (Project $record): string => match (true) {
                        filled($record->due_date) && $record->due_date->isPast() => 'danger',
                        filled($record->due_date) && $record->due_date->diffInDays(Carbon::today()) <= 7 => 'warning',
                        default => 'primary',
                    })
                    ->size(TextColumnSize::Medium)
                    ->alignCenter()
                    ->visibleFrom('lg'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        'active' => 'heroicon-m-arrow-path',
                        'on_hold' => 'heroicon-m-pause',
                        'completed' => 'heroicon-m-check-badge',
                        default => 'heroicon-m-question-mark-circle',
                    })
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
                    ])
                    ->weight(FontWeight::SemiBold)
                    ->size(TextColumnSize::Small)
                    ->visibleFrom('md'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('เจ้าของ')
                    ->searchable()
                    ->sortable()
                    ->size(TextColumnSize::Small)
                    ->visibleFrom('xl'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('วันที่เริ่ม')
                    ->date('d/m/Y')
                    ->sortable()
                    ->size(TextColumnSize::Small)
                    ->alignCenter()
                    ->visibleFrom('xl'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company_id')
                    ->label('บริษัท')
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('เจ้าของ')
                    ->relationship('user', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('สถานะ')
                    ->options([
                        'active' => 'กำลังดำเนินการ',
                        'on_hold' => 'พักงาน',
                        'completed' => 'เสร็จสมบูรณ์',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('ดู')
                    ->color('gray')
                    ->iconButton()
                    ->hiddenLabel()
                    ->tooltip('ดู')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('ชื่อโครงการ')
                            ->disabled(),
                        Forms\Components\Select::make('company_id')
                            ->label('บริษัท')
                            ->relationship('company', 'name')
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->label('เจ้าของโครงการ')
                            ->relationship('user', 'name')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('สถานะ')
                            ->options([
                                'active' => 'กำลังดำเนินการ',
                                'on_hold' => 'พักงาน',
                                'completed' => 'เสร็จสมบูรณ์',
                            ])
                            ->disabled(),
                        Forms\Components\Textarea::make('description')
                            ->label('รายละเอียด')
                            ->rows(4)
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('วันที่เริ่ม')
                            ->disabled(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('วันที่คาดว่าจะเสร็จ')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('สร้างเมื่อ')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
                Tables\Actions\EditAction::make()
                    ->label('แก้ไข')
                    ->color('gray')
                    ->iconButton()
                    ->hiddenLabel()
                    ->tooltip('แก้ไข'),
                Tables\Actions\DeleteAction::make()
                    ->label('ลบ')
                    ->color('danger')
                    ->iconButton()
                    ->hiddenLabel()
                    ->tooltip('ลบ'),
            ])
            ->actionsAlignment('end')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('ลบรายการที่เลือก'),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('ข้อมูลโครงการ')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('ชื่อโครงการ'),
                        Infolists\Components\TextEntry::make('company.name')
                            ->label('บริษัท'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('เจ้าของโครงการ'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('สถานะ')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => 'กำลังดำเนินการ',
                                'on_hold' => 'พักงาน',
                                'completed' => 'เสร็จสมบูรณ์',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'info',
                                'on_hold' => 'warning',
                                'completed' => 'success',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('description')
                            ->label('รายละเอียด')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('กำหนดเวลา')
                    ->schema([
                        Infolists\Components\TextEntry::make('start_date')
                            ->label('วันที่เริ่ม')
                            ->date('d/m/Y'),
                        Infolists\Components\TextEntry::make('due_date')
                            ->label('วันที่คาดว่าจะเสร็จ')
                            ->date('d/m/Y'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('สร้างเมื่อ')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('อัปเดตล่าสุด')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
