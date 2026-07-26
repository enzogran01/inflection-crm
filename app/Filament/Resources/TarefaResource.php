<?php

namespace App\Filament\Resources;

use App\Models\Tarefa;
use Filament\Resources\Resource;

class TarefaResource extends Resource implements \BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions
{
    protected static ?string $model = Tarefa::class;

    protected static ?string $modelLabel = 'Tarefa';
    protected static ?string $pluralModelLabel = 'Tarefas';

    protected static bool $shouldRegisterNavigation = false;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'view_others',
            'view_unassigned',
            'view_own',
        ];
    }
}
