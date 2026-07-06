<?php

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
