<?php

namespace App\Policies;

use App\Models\ComputerScienceResource;
use App\Models\User;

class ComputerScienceResourcePolicy
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
    public function view(User $user, ComputerScienceResource $computerScienceResource): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ComputerScienceResource $computerScienceResource): bool
    {
        if ($user->isAdmin()) return true;

        return $user->id == $computerScienceResource->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ComputerScienceResource $computerScienceResource): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ComputerScienceResource $computerScienceResource): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ComputerScienceResource $computerScienceResource): bool
    {
        return false;
    }
}
