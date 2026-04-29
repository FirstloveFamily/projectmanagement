<?php

namespace App\Filament\Admin\Resources\ProjectReviewResource\Pages;

use App\Filament\Admin\Resources\ProjectReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectReviews extends ListRecords
{
    protected static string $resource = ProjectReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
