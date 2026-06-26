<?php

namespace App\Filament\Resources\TaskChecklistResource\Pages;

use App\Filament\Resources\TaskChecklistResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTaskChecklists extends ListRecords
{
    protected static string $resource = TaskChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
