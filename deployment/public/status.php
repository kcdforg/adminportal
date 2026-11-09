<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

// Deployment details must never be available to anonymous visitors.
if (!isLoggedIn()) {
    http_response_code(401);
    exit('Authentication required.');
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(readDeploymentStatus(), JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
