<?php

namespace App\Filament\Resources\CyberNewsResource\Pages;

use App\Filament\Resources\CyberNewsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCyberNews extends EditRecord
{
    protected static string $resource = CyberNewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
