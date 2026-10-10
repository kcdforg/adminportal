<?php

declare(strict_types=1);

$database = $bootstrap['database'];
$body = getJsonBody();
$username = trim((string) ($body['username'] ?? ''));
$password = (string) ($body['password'] ?? '');

$errors = [];
if ($username === '') {
    $errors['username'] = ['The username field is required.'];
}
if ($password === '') {
    $errors['password'] = ['The password field is required.'];
}
if ($errors !== []) {
    errorResponse('VALIDATION_FAILED', 'The given data was invalid.', 422, $errors);
}

$tokens = authenticatePortalLogin($database, $bootstrap, $username, $password, 'member');
successResponse($tokens, 'Login successful');
