<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

final class UserService
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * @throws \Exception
     */
    public function register(string $name, string $email, string $password): bool
    {
        if ($this->user->getUserByEmail($email) !== null) {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        if (!is_string($passwordHash) || $passwordHash === '') {
            throw new \RuntimeException('Failed to hash password.');
        }

        return $this->user->createUser($name, $email, $passwordHash);
    }
}
