<?php

declare(strict_types=1);

use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;

use function DI\autowire;

return [
    UserRepositoryInterface::class => autowire(UserRepository::class),
    PostRepositoryInterface::class => autowire(PostRepository::class),
];
