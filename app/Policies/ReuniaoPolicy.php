<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reuniao;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReuniaoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Todos podem ver a listagem/calendário para poderem ver suas próprias reuniões.
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Reuniao $reuniao): bool
    {
        if ($user->can('view_reuniao')) {
            return true;
        }

        // Verifica se é participante direto
        if ($reuniao->participantes()->where('users.id', $user->id)->exists()) {
            return true;
        }

        // Verifica se o usuário tem algum dos cargos vinculados à reunião
        // Assumindo que User tem a relação 'roles()' do Spatie
        return $reuniao->cargos()->whereIn('roles.id', $user->roles()->pluck('id'))->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_reuniao');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Reuniao $reuniao): bool
    {
        return $user->can('update_reuniao');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Reuniao $reuniao): bool
    {
        return $user->can('delete_reuniao');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_reuniao');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Reuniao $reuniao): bool
    {
        return $user->can('force_delete_reuniao');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_reuniao');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Reuniao $reuniao): bool
    {
        return $user->can('restore_reuniao');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_reuniao');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Reuniao $reuniao): bool
    {
        return $user->can('replicate_reuniao');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_reuniao');
    }
}
