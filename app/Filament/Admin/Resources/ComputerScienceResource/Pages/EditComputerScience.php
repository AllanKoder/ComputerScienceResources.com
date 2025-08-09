<?php

namespace App\Filament\Admin\Resources\ComputerScienceResource\Pages;

use App\Filament\Admin\Resources\ComputerScienceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComputerScience extends EditRecord
{
    protected static string $resource = ComputerScienceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
