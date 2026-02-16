<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Contracts\UserRepositoryInterface;
use PDO;

final class UserRepository implements UserRepositoryInterface
{
    private string $table = 'users';

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getByEmail(?string $email): ?array
    {
        if ($email === null || trim($email) === '') {
            throw new \InvalidArgumentException('Email is required.');
        }

        $queryToPrepare = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";

        $statement = $this->pdo->prepare($queryToPrepare);
        $statement->bindValue(':email', $email);
        $statement->execute();

        $userData = $statement->fetch();

        return is_array($userData) ? $userData : null;
    }

    public function createUser(string $name, string $email, string $hashedPassword): bool
    {
        $queryToPrepare = "INSERT INTO {$this->table} (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)";

        $statement = $this->pdo->prepare($queryToPrepare);
        $statement->bindValue(':name', $name);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':password_hash', $hashedPassword);
        $statement->bindValue(':role', 'normal');

        return $statement->execute();
    }
}
