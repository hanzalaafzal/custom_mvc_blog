<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface PostRepositoryInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function paginate(int $limit, int $offset): array;

    public function countAll(): int;

    /**
     * @return array<string, mixed>|null
     */
    public function getById(int $id): ?array;

    public function create(int $userId, string $title, string $body): bool;

    public function update(int $id, string $title, string $body): bool;

    public function updateByOwner(int $id, int $ownerId, string $title, string $body): bool;

    public function delete(int $id): bool;

    public function deleteByOwner(int $id, int $ownerId): bool;
}
