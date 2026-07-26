<?php

namespace App\Filament\Clusters\Tarefas\Resources\MetaResource\Pages;

use App\Filament\Clusters\Tarefas\Resources\MetaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMetas extends ListRecords
{
    protected static string $resource = MetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
