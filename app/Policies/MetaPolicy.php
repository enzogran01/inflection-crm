<?php

namespace App\Policies;

use App\Models\Meta;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MetaPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_meta');
    }

    public function view(User $user, Meta $meta): bool
    {
        return $user->can('view_meta');
    }

    public function create(User $user): bool
    {
        return $user->can('create_meta');
    }

    public function update(User $user, Meta $meta): bool
    {
        return $user->can('update_meta');
    }

    public function delete(User $user, Meta $meta): bool
    {
        return $user->can('delete_meta');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_meta');
    }

    public function forceDelete(User $user, Meta $meta): bool
    {
        return $user->can('force_delete_meta');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_meta');
    }

    public function restore(User $user, Meta $meta): bool
    {
        return $user->can('restore_meta');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_meta');
    }

    public function replicate(User $user, Meta $meta): bool
    {
        return $user->can('replicate_meta');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_meta');
    }
}
