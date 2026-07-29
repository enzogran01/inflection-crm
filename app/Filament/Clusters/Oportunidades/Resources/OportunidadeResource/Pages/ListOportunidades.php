<?php

namespace App\Filament\Clusters\Oportunidades\Resources\OportunidadeResource\Pages;

use App\Filament\Clusters\Oportunidades\Resources\OportunidadeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOportunidades extends ListRecords
{
    protected static string $resource = OportunidadeResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
