<?php

namespace App\Filament\Resources;

use App\Models\Tarefa;
use Filament\Resources\Resource;

class TarefaResource extends Resource
{
    protected static ?string $model = Tarefa::class;

    protected static ?string $modelLabel = 'Tarefa';
    protected static ?string $pluralModelLabel = 'Tarefas';

    protected static bool $shouldRegisterNavigation = false;
}
