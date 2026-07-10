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

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     operationId="authLogin",
     *     tags={"Auth"},
     *     summary="User login",
     *     description="Authenticate user with username and password. Returns access and refresh tokens.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Login credentials",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string", example="john.doe"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="access_token", type="string"),
     *                 @OA\Property(property="refresh_token", type="string"),
     *                 @OA\Property(property="profile", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/auth/refresh",
     *     operationId="authRefresh",
     *     tags={"Auth"},
     *     summary="Refresh access token",
     *     description="Issue new access and refresh tokens using a valid refresh token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"refresh_token"},
     *             @OA\Property(property="refresh_token", type="string", example="eyJhbGc...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Token refreshed"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="access_token", type="string"),
     *                 @OA\Property(property="refresh_token", type="string"),
     *                 @OA\Property(property="profile", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid refresh token",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     operationId="authLogout",
     *     tags={"Auth"},
     *     summary="User logout",
     *     description="Invalidate the user's refresh token and logout.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="refresh_token", type="string", example="eyJhbGc...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/auth/me",
     *     operationId="getCurrentProfile",
     *     tags={"Auth"},
     *     summary="Get current user profile",
     *     description="Retrieve the authenticated user's profile information.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OK"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Profile not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
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
