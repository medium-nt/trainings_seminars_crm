<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function view(User $user, Group $group): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function update(User $user, Group $group): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function delete(User $user, Group $group): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}
