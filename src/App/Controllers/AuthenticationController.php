<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Traits\Csrf;
use Traits\Permission;
use Traits\Render;
use Traits\Session;

class AuthenticationController
{
    use Csrf;
    use Session;
    use Render;
    use Permission;

    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function viewLogin(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $error = Session::get('flash_error');

        Session::forget('flash_error');

        $body = $this->render('auth/login', 'layouts/main', [
            'csrf' => $this->token(),
            'error' => is_string($error) ? $error : null,
        ]);

        return new Response(200, ['Content-Type' => 'text/html; charset=UTF-8'], $body);
    }

    public function authenticate(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->validateCsrf($request)) {
            Session::set('flash_error', 'No CSRF Token found in request');
            return new Response(302, ['Location' => '/login']);
        }

        $formData = $request->getParsedBody();
        if (empty($formData['email']) || empty($formData['password'])) {
            Session::set('flash_error', 'Email or password is required.');
            return new Response(302, ['Location' => '/login']);
        }

        if ($this->authService->attempt((string) $formData['email'], (string) $formData['password'])) {
            return new Response(302, ['Location' => '/posts']);
        }

        Session::start();
        Session::set('flash_error', 'Invalid email or password.');

        return new Response(302, ['Location' => '/login']);
    }

    public function viewRegistration(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $error = Session::get('flash_error');

        Session::forget('flash_error');

        $body = $this->render('auth/register', 'layouts/main', [
            'csrf' => $this->token(),
            'error' => is_string($error) ? $error : null,
        ]);

        return new Response(200, ['Content-Type' => 'text/html; charset=UTF-8'], $body);
    }

    public function register(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->validateCsrf($request)) {
            Session::set('flash_error', 'No CSRF Token found in request');
            return new Response(302, ['Location' => '/register']);
        }

        $formData = $request->getParsedBody();

        if (empty(trim((string) $formData['name'])) || empty(trim((string) $formData['email'])) || empty(trim((string) $formData['password']))) {
            Session::set('flash_error', 'Name, email and password are required.');
            return new Response(302, ['Location' => '/register']);
        }

        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            Session::set('flash_error', 'Please provide a valid email address.');
            return new Response(302, ['Location' => '/register']);
        }

        if (strlen((string) $formData['password']) < 8) {
            Session::set('flash_error', 'Password must be at least 8 characters long.');
            return new Response(302, ['Location' => '/register']);
        }

        if (($formData['password'] ?? '') !== ($formData['password_confirmation'] ?? null)) {
            Session::set('flash_error', 'Password confirmation does not match.');
            return new Response(302, ['Location' => '/register']);
        }

        if (!$this->authService->register((string) $formData['name'], (string) $formData['email'], (string) $formData['password'])) {
            Session::set('flash_error', 'Email is already in use.');
            return new Response(302, ['Location' => '/register']);
        }

        return new Response(302, ['Location' => '/login']);
    }

    public function logout(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        if (self::isAuthenticated()) {
            Session::forget('auth_user_id');
        }

        return new Response(302, ['Location' => '/login']);
    }
}
