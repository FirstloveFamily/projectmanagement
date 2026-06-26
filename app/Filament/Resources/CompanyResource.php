<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Filament\Resources\CompanyResource\Widgets\CompanyOverviewStats;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'บริษัท';
    protected static ?string $navigationGroup = 'การจัดการงาน';
    protected static ?string $modelLabel = 'บริษัท';
    protected static ?string $pluralModelLabel = 'บริษัท';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ข้อมูลบริษัท')
                    ->description('ชื่อบริษัทและคำอธิบายสั้น ๆ สำหรับใช้ผูกกับโครงการ')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('ชื่อบริษัท')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Placeholder::make('project_hint')
                                    ->label('โครงการ')
                                    ->content('บริษัทนี้จะใช้เป็นตัวเลือกในโครงการ เพื่อบอกว่าโครงการนั้นเป็นของบริษัทไหน')
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')
                                    ->label('รายละเอียด')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->striped()
            ->emptyStateHeading('ยังไม่มีบริษัท')
            ->emptyStateDescription('เพิ่มบริษัทเพื่อให้โครงการแต่ละรายการผูกกับหน่วยงานได้ชัดเจนขึ้น')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('ชื่อบริษัท')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),
                Tables\Columns\TextColumn::make('description')
                    ->label('รายละเอียด')
                    ->toggleable()
                    ->wrap()
                    ->limit(60),
                Tables\Columns\TextColumn::make('projects_count')
                    ->label('โครงการ')
                    ->counts('projects')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('สร้างเมื่อ')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()->label('ดู'),
                Tables\Actions\EditAction::make()->label('แก้ไข'),
                Tables\Actions\DeleteAction::make()->label('ลบ'),
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
            RelationManagers\ProjectsRelationManager::class,
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('ข้อมูลบริษัท')
                    ->description('รายละเอียดหลักของบริษัท และทางลัดสำหรับเริ่มงานต่อ')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('ชื่อบริษัท')
                            ->weight(FontWeight::Medium),
                        Infolists\Components\TextEntry::make('description')
                            ->label('รายละเอียด')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('สร้างเมื่อ')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('อัปเดตล่าสุด')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('คำแนะนำ')
                    ->schema([
                        Infolists\Components\TextEntry::make('note')
                            ->state('ใช้ปุ่มด้านบนเพื่อเพิ่มโครงการ แก้ไขข้อมูล หรือดูรายการโครงการของบริษัทนี้')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'view' => Pages\ViewCompany::route('/{record}'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
