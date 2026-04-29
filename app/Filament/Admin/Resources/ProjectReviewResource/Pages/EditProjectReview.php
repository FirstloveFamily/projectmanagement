<?php

namespace App\Filament\Admin\Resources\ProjectReviewResource\Pages;

use App\Filament\Admin\Resources\ProjectReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProjectReview extends EditRecord
{
    protected static string $resource = ProjectReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
