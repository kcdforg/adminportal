<?php

declare(strict_types=1);

function jsonEncode($payload): string
{
    return json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
}

function errorResponse(string $code, string $message, int $status = 400, array $details = []): void
{
    http_response_code($status);
    $payload = [
        'success' => false,
        'error' => [
            'code' => $code,
            'message' => $message,
        ],
    ];

    if ($details !== []) {
        $payload['error']['details'] = $details;
    }

    echo jsonEncode($payload);
    exit;
}

function jsonResponse($payload, int $status = 200): void
{
    http_response_code($status);
    echo jsonEncode(['success' => true, 'data' => $payload]);
    exit;
}

function successResponse($data = null, string $message = 'OK', int $status = 200): void
{
    $payload = ['success' => true, 'message' => $message];
    if ($data !== null) {
        $payload['data'] = $data;
    }

    http_response_code($status);
    echo jsonEncode($payload);
    exit;
}

function getJsonBody(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        errorResponse('MALFORMED_JSON', 'Request body must be valid JSON.', 400, [
            'raw' => 'The request body could not be decoded as JSON.',
        ]);
    }

    return $decoded;
}

function getRouteParam(string $name, $default = null)
{
    $params = $GLOBALS['routeParams'] ?? [];
    if (!is_array($params)) {
        return $default;
    }

    return array_key_exists($name, $params) ? $params[$name] : $default;
}

function normalizeApiRequestPath(string $requestUri, string $scriptName): string
{
    $path = parse_url($requestUri, PHP_URL_PATH);
    $path = is_string($path) && $path !== '' ? $path : '/';
    $scriptPath = parse_url($scriptName, PHP_URL_PATH);
    $scriptPath = is_string($scriptPath) && $scriptPath !== '' ? $scriptPath : '/api/index.php';
    $mountPath = dirname(dirname($scriptPath));

    if ($mountPath !== '/' && ($path === $mountPath || str_starts_with($path, $mountPath . '/'))) {
        $path = substr($path, strlen($mountPath));
    }

    return $path !== '' ? $path : '/';
}

function handleException(Throwable $exception): never
{
    if ($exception instanceof KcdfAcademicsException
        || $exception instanceof KcdfCommunityException
        || $exception instanceof KcdfPaymentException) {
        errorResponse(
            $exception->errorCode,
            $exception->getMessage(),
            $exception->status,
            $exception->details
        );
    }

    if ($exception instanceof PDOException) {
        error_log('API database failure: ' . $exception->getMessage());
        if ((string) $exception->getCode() === '23000') {
            errorResponse('DUPLICATE_ENTRY', 'The requested record conflicts with an existing record.', 409);
        }
        errorResponse('INTERNAL_ERROR', 'The request could not be completed.', 500);
    }

    if ($exception instanceof RuntimeException) {
        if ((int) $exception->getCode() === 401) {
            errorResponse('UNAUTHENTICATED', $exception->getMessage(), 401);
        }
        if ((int) $exception->getCode() === 404) {
            errorResponse('NOT_FOUND', $exception->getMessage(), 404);
        }
        if ((int) $exception->getCode() === 409) {
            errorResponse('DUPLICATE_ENTRY', $exception->getMessage(), 409);
        }
    }

    error_log('API endpoint failure: ' . $exception->getMessage());
    errorResponse('INTERNAL_ERROR', 'The request could not be completed.', 500);
}
