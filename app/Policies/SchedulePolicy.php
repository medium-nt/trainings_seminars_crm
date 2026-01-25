<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;

class SchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function view(User $user, Schedule $schedule): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function update(User $user, Schedule $schedule): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function delete(User $user, Schedule $schedule): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}
