<?php

declare(strict_types=1);

use App\Controllers\AuthenticationController;

return [
    ['method' => 'GET', 'path' => '/login', 'handler' => [AuthenticationController::class, 'viewLogin']],
    ['method' => 'POST', 'path' => '/authenticate', 'handler' => [AuthenticationController::class, 'authenticate']],

    ['method' => 'GET', 'path' => '/register', 'handler' => [AuthenticationController::class, 'viewRegister']],
    ['method' => 'POST', 'path' => '/register', 'handler' => [AuthenticationController::class, 'register']],
];
