<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

verifyCsrfToken();

// Clear session data and invalidate the browser's session cookie.
$_SESSION = [];
$cookie = session_get_cookie_params();

setcookie(session_name(), '', [
    'expires' => time() - 3600,
    'path' => $cookie['path'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_destroy();

header('Location: /login.php', true, 303);
exit;
