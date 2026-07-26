<?php

namespace App\Filament\Clusters\Tarefas\Resources\MetaResource\Pages;

use App\Filament\Clusters\Tarefas\Resources\MetaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMeta extends EditRecord
{
    protected static string $resource = MetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
