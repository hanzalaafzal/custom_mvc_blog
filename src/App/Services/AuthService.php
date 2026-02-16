<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Traits\Session;

final class AuthService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function attempt(string $email, string $password): bool
    {
        $userData = $this->userRepository->getByEmail($email);

        if (!is_array($userData)) {
            return false;
        }

        if (!password_verify($password, (string) $userData['password_hash'])) {
            return false;
        }

        Session::start();
        Session::regenerateId();

        Session::set('auth_user_id', (int) $userData['id']);
        Session::set('auth_role', (string) $userData['role']);

        return true;
    }

    public function register(string $name, string $email, string $password): bool
    {
        if ($this->userRepository->getByEmail($email) !== null) {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        if (!is_string($passwordHash) || $passwordHash === '') {
            throw new \RuntimeException('Failed to hash password.');
        }

        return $this->userRepository->createUser($name, $email, $passwordHash);
    }
}
