<?php

namespace App\Filament\Admin\Resources\ComputerScienceResource\Pages;

use App\Filament\Admin\Resources\ComputerScienceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComputerSciences extends ListRecords
{
    protected static string $resource = ComputerScienceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
