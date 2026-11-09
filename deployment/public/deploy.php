<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

verifyCsrfToken();

// Only these three fixed application identifiers may reach the worker.
$allowedApplications = ['api', 'admin', 'member'];
$selectedApplications = $_POST['apps'] ?? null;

if (
    !is_array($selectedApplications)
    || count($selectedApplications) < 1
    || count($selectedApplications) > 3
    || array_diff($selectedApplications, $allowedApplications)
) {
    http_response_code(400);
    exit('Select one or more valid applications.');
}

$selectedApplications = array_values(array_unique($selectedApplications));

// Serialize requests while checking status and starting the worker.
$requestLock = fopen(RUNTIME_DIR . '/request.lock', 'c');

if ($requestLock === false || !flock($requestLock, LOCK_EX)) {
    http_response_code(503);
    exit('Unable to lock deployment requests.');
}

try {
    $status = readDeploymentStatus();

    if (in_array($status['status'] ?? '', ['queued', 'running'], true)) {
        header('Location: /', true, 303);
        exit;
    }

    $requestId = bin2hex(random_bytes(12));

    writeJsonAtomically(RUNTIME_DIR . '/request.json', [
        'id' => $requestId,
        'apps' => $selectedApplications,
        'requested_at' => gmdate('c'),
    ]);

    writeJsonAtomically(RUNTIME_DIR . '/status.json', [
        'status' => 'queued',
        'step' => 'Starting background deployment',
        'request_id' => $requestId,
        'updated_at' => gmdate('c'),
    ]);

    // Use a fixed script path; never construct shell commands from form values.
    $worker = realpath(__DIR__ . '/../scripts/deploy.sh');

    if ($worker === false || !function_exists('exec')) {
        writeJsonAtomically(RUNTIME_DIR . '/status.json', [
            'status' => 'failed',
            'step' => 'Deployment worker is unavailable',
            'updated_at' => gmdate('c'),
        ]);
    } else {
        $logPath = RUNTIME_DIR . '/deploy.log';
        $command = '/usr/bin/nohup /bin/bash '
            . escapeshellarg($worker)
            . ' > ' . escapeshellarg($logPath)
            . ' 2>&1 < /dev/null &';

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            writeJsonAtomically(RUNTIME_DIR . '/status.json', [
                'status' => 'failed',
                'step' => 'Unable to start deployment worker',
                'updated_at' => gmdate('c'),
            ]);
        }
    }
} finally {
    flock($requestLock, LOCK_UN);
    fclose($requestLock);
}

header('Location: /', true, 303);
exit;
