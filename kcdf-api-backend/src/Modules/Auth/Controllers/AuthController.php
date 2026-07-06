<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controllers;

use App\Core\BaseController;
use App\Modules\Auth\Services\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController extends BaseController
{
    public function __construct(private readonly AuthService $authService) {}

    public function login(Request $request, Response $response): Response
    {
        $body = (array) $request->getParsedBody();

        $username = trim($body['username'] ?? '');
        $password = $body['password'] ?? '';

        $errors = [];
        if (empty($username)) {
            $errors['username'] = ['The username field is required.'];
        }
        if (empty($password)) {
            $errors['password'] = ['The password field is required.'];
        }
        if (!empty($errors)) {
            return $this->validationError($response, $errors);
        }

        try {
            $result = $this->authService->login($username, $password);
            return $this->success($response, $result, 'Login successful');
        } catch (\RuntimeException $e) {
            return $this->error($response, 'UNAUTHENTICATED', $e->getMessage(), [], (int) $e->getCode() ?: 401);
        }
    }

    public function refresh(Request $request, Response $response): Response
    {
        $body = (array) $request->getParsedBody();
        $refreshToken = $body['refresh_token'] ?? '';

        if (empty($refreshToken)) {
            return $this->validationError($response, ['refresh_token' => ['The refresh_token field is required.']]);
        }

        try {
            $result = $this->authService->refresh($refreshToken);
            return $this->success($response, $result, 'Token refreshed');
        } catch (\RuntimeException $e) {
            return $this->error($response, 'UNAUTHENTICATED', $e->getMessage(), [], 401);
        }
    }

    public function logout(Request $request, Response $response): Response
    {
        $body = (array) $request->getParsedBody();
        $refreshToken = $body['refresh_token'] ?? '';
        $payload = $request->getAttribute('jwt_payload', []);
        $profileId = (int) ($payload['profile_id'] ?? 0);

        if ($profileId && !empty($refreshToken)) {
            $this->authService->logout($profileId, $refreshToken);
        }

        return $this->success($response, null, 'Logged out successfully');
    }

    public function me(Request $request, Response $response): Response
    {
        $payload   = $request->getAttribute('jwt_payload', []);
        $profileId = (int) ($payload['profile_id'] ?? 0);

        try {
            $profile = $this->authService->getProfile($profileId);
            return $this->success($response, $profile);
        } catch (\Throwable) {
            return $this->notFound($response, 'Profile not found');
        }
    }
}
