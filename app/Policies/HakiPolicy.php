<?php

namespace App\Policies;

use App\Models\Haki;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class HakiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Haki $haki): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role == '0';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Haki $haki): bool
    {
        return $user->role == '0';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Haki $haki): bool
    {
        return $user->role == '0';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Haki $haki): bool
    {
        return $user->role == '0';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Haki $haki): bool
    {
        return $user->role == '0';
    }
}
