<?php

namespace App\Filament\Admin\Resources\NewsPostResource\Pages;

use App\Filament\Admin\Resources\NewsPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNewsPosts extends ListRecords
{
    protected static string $resource = NewsPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
