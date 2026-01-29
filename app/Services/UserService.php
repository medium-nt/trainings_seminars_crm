<?php

namespace App\Services;

class UserService
{
    public function setPasswordIfNeeded(array $data, ?string $password): array
    {
        if ($password) {
            $data['password'] = bcrypt($password);
        } else {
            unset($data['password']);
        }

        return $data;
    }
}
