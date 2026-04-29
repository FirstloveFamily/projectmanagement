<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CompanyResource\Pages;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Companies';
    protected static ?string $modelLabel = 'บริษัท';
    protected static ?string $pluralModelLabel = 'บริษัท';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('ชื่อบริษัท'),
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->label('โลโก้'),
                        Forms\Components\Textarea::make('address')
                            ->label('ที่อยู่'),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->label('เบอร์โทรศัพท์'),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->label('อีเมล'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('ชื่อบริษัท'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('อีเมล'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('เบอร์โทรศัพท์'),
                Tables\Columns\TextColumn::make('projects_count')
                    ->counts('projects')
                    ->label('จำนวนโปรเจกต์'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
