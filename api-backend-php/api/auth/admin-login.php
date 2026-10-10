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
validateOrFail($errors);

$tokens = authenticatePortalLogin($database, $bootstrap, $username, $password, 'admin');
successResponse($tokens, 'Admin login successful');
