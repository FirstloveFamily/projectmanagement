<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCompany extends ViewRecord
{
    protected static string $resource = CompanyResource::class;

    protected static ?string $title = 'รายละเอียดบริษัท';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('createProject')
                ->label('เพิ่มโครงการ')
                ->icon('heroicon-m-plus')
                ->url(ProjectResource::getUrl('create', ['company_id' => $this->record->id]))
                ->color('primary'),
            Actions\EditAction::make()
                ->label('แก้ไข')
                ->icon('heroicon-m-pencil-square'),
            Actions\DeleteAction::make()
                ->label('ลบ')
                ->icon('heroicon-m-trash'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\CompanyResource\Widgets\CompanyOverviewStats::class,
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
