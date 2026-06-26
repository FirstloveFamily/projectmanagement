<?php

namespace App\Filament\Resources\TaskChecklistResource\Pages;

use App\Filament\Resources\TaskChecklistResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTaskChecklist extends CreateRecord
{
    protected static string $resource = TaskChecklistResource::class;
}
