<?php

declare(strict_types=1);

namespace App\Models;

use Database\Connection;
use PDO;

final class User
{
    private string $table = 'users';

    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new Connection())->pdo();
    }

    /**
     * @throws \Exception
     */
    public function getUserByEmail(?string $email): ?array
    {
        if ($email === null || trim($email) === '') {
            throw new \Exception('Trying to find email on null');
        }

        $queryToPrepare = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";

        $statement = $this->pdo->prepare($queryToPrepare);
        $statement->bindValue(':email', $email);
        $statement->execute();

        $userData = $statement->fetch();

        return is_array($userData) ? $userData : null;
    }

    public function createUser(string $name, string $email, string $passwordHash, string $role = 'normal'): bool
    {
        $queryToPrepare = "INSERT INTO {$this->table} (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)";

        $statement = $this->pdo->prepare($queryToPrepare);
        $statement->bindValue(':name', $name);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':password_hash', $passwordHash);
        $statement->bindValue(':role', $role);

        return $statement->execute();
    }
}
