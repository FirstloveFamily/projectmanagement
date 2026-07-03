<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use App\Filament\Resources\CompanyResource\Widgets\CompanyOverviewStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanies extends ListRecords
{
    protected static string $resource = CompanyResource::class;

    protected static ?string $title = 'ภาพรวมบริษัท';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('เพิ่มบริษัท')
                ->icon('heroicon-m-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CompanyOverviewStats::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | string | array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'xl' => 4,
        ];
    }
}
