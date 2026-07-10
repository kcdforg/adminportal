<?php

/**
 * @OA\Info(
 *     title="KCDF Parents Platform API",
 *     version="1.0.0",
 *     description="REST API for the KCDF Parents platform",
 *     contact={"name": "KCDF Support", "email": "support@kcdf.org"},
 *     license={"name": "Proprietary"}
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8080",
 *     description="Development Server"
 * )
 *
 * @OA\Server(
 *     url="https://api.example.com",
 *     description="Production Server"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     name="bearerAuth",
 *     in="header",
 *     bearerFormat="JWT",
 *     scheme="bearer",
 *     description="JWT Bearer token authentication"
 * )
 *
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     title="Success Response",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="data", type="object"),
 *     @OA\Property(property="message", type="string", example="Operation successful")
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="Error Response",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(
 *         property="error",
 *         type="object",
 *         @OA\Property(property="code", type="string", example="VALIDATION_FAILED"),
 *         @OA\Property(property="message", type="string", example="Request validation failed"),
 *         @OA\Property(property="details", type="object")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     title="Validation Error Response",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(
 *         property="error",
 *         type="object",
 *         @OA\Property(property="code", type="string", example="VALIDATION_FAILED"),
 *         @OA\Property(property="message", type="string", example="Validation failed"),
 *         @OA\Property(
 *             property="details",
 *             type="object",
 *             example={"username": {"The username field is required."}, "password": {"The password field is required."}}
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PaginatedResponse",
 *     type="object",
 *     title="Paginated Response",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="data", type="array", @OA\Items(type="object")),
 *     @OA\Property(
 *         property="meta",
 *         type="object",
 *         @OA\Property(property="total", type="integer", example=100),
 *         @OA\Property(property="per_page", type="integer", example=15),
 *         @OA\Property(property="current_page", type="integer", example=1),
 *         @OA\Property(property="last_page", type="integer", example=7)
 *     )
 * )
 */

declare(strict_types=1);

namespace App\Core;

use Psr\Http\Message\ResponseInterface as Response;

abstract class BaseController
{
    protected function success(Response $response, mixed $data = null, string $message = 'OK', int $status = 200): Response
    {
        $payload = ['success' => true, 'message' => $message];
        if ($data !== null) {
            $payload['data'] = $data;
        }
        return $this->json($response, $payload, $status);
    }

    protected function created(Response $response, mixed $data = null, string $message = 'Created'): Response
    {
        return $this->success($response, $data, $message, 201);
    }

    protected function paginate(Response $response, array $data, array $meta): Response
    {
        return $this->json($response, [
            'success' => true,
            'data'    => $data,
            'meta'    => $meta,
        ]);
    }

    protected function error(
        Response $response,
        string $code,
        string $message,
        array $details = [],
        int $status = 400
    ): Response {
        $payload = [
            'success' => false,
            'error'   => [
                'code'    => $code,
                'message' => $message,
            ],
        ];
        if (!empty($details)) {
            $payload['error']['details'] = $details;
        }
        return $this->json($response, $payload, $status);
    }

    protected function notFound(Response $response, string $message = 'Resource not found'): Response
    {
        return $this->error($response, 'NOT_FOUND', $message, [], 404);
    }

    protected function unauthorized(Response $response, string $message = 'Unauthorized'): Response
    {
        return $this->error($response, 'UNAUTHORIZED', $message, [], 403);
    }

    protected function validationError(Response $response, array $details, string $message = 'Validation failed'): Response
    {
        return $this->error($response, 'VALIDATION_FAILED', $message, $details, 422);
    }

    private function json(Response $response, array $payload, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
