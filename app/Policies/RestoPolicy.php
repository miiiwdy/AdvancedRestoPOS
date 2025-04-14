<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Resto;
use Illuminate\Auth\Access\HandlesAuthorization;

class RestoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_resto');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Resto $resto): bool
    {
        return $user->can('view_resto');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_resto');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Resto $resto): bool
    {
        return $user->can('update_resto');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Resto $resto): bool
    {
        return $user->can('delete_resto');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_resto');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Resto $resto): bool
    {
        return $user->can('force_delete_resto');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_resto');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Resto $resto): bool
    {
        return $user->can('restore_resto');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_resto');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Resto $resto): bool
    {
        return $user->can('replicate_resto');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_resto');
    }
}
