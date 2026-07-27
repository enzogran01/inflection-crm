<?php

namespace App\Filament\Resources\AreaAtuacaoResource\Pages;

use App\Filament\Resources\AreaAtuacaoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAreaAtuacaos extends ListRecords
{
    protected static string $resource = AreaAtuacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
