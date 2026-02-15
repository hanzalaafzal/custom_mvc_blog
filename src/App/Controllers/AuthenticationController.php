<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\UserService;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Traits\Csrf;
use Traits\Render;
use Traits\Session;

class AuthenticationController
{
    use Csrf;
    use Session;
    use Render;

    /**
     * @throws \Exception
     */
    public function viewLogin(): ResponseInterface
    {
        Session::start();

        $error = Session::get('flash_error');
        $success = Session::get('flash_success');

        Session::forget('flash_error');
        Session::forget('flash_success');

        $body = $this->render('auth/login', 'layouts/main', [
            'csrf' => $this->token(),
            'error' => is_string($error) ? $error : null,
            'success' => is_string($success) ? $success : null,
        ]);

        return new Response(200, ['Content-Type' => 'text/html; charset=UTF-8'], $body);
    }

    /**
     * @throws \Exception
     */
    public function viewRegister(): ResponseInterface
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

    /**
     * @throws \Exception
     */
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

        if ((new AuthService())->attempt((string) $formData['email'], (string) $formData['password'])) {
            return new Response(302, ['Location' => '/posts']);
        }

        Session::start();
        Session::set('flash_error', 'Invalid email or password.');

        return new Response(302, ['Location' => '/login']);
    }

    /**
     * @throws \Exception
     */
    public function register(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->validateCsrf($request)) {
            Session::set('flash_error', 'No CSRF Token found in request');
            return new Response(302, ['Location' => '/register']);
        }

        $formData = $request->getParsedBody();

        $name = trim((string) ($formData['name'] ?? ''));
        $email = trim((string) ($formData['email'] ?? ''));
        $password = (string) ($formData['password'] ?? '');
        $passwordConfirmation = (string) ($formData['password_confirmation'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            Session::set('flash_error', 'Name, email and password are required.');
            return new Response(302, ['Location' => '/register']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::set('flash_error', 'Please provide a valid email address.');
            return new Response(302, ['Location' => '/register']);
        }

        if (strlen($password) < 8) {
            Session::set('flash_error', 'Password must be at least 8 characters long.');
            return new Response(302, ['Location' => '/register']);
        }

        if ($password !== $passwordConfirmation) {
            Session::set('flash_error', 'Password confirmation does not match.');
            return new Response(302, ['Location' => '/register']);
        }

        if (!(new UserService())->register($name, $email, $password)) {
            Session::set('flash_error', 'Email is already in use.');
            return new Response(302, ['Location' => '/register']);
        }

        Session::set('flash_success', 'Registration successful. Please log in.');

        return new Response(302, ['Location' => '/login']);
    }
}
