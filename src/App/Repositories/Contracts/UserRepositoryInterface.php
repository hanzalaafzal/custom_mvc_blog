<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function getByEmail(?string $email): ?array;

    public function createUser(string $name, string $email, string $hashedPassword): bool;
}
