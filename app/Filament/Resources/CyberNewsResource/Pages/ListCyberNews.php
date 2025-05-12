<?php

namespace App\Filament\Resources\CyberNewsResource\Pages;

use App\Filament\Resources\CyberNewsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCyberNews extends ListRecords
{
    protected static string $resource = CyberNewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
