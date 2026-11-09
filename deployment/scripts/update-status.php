<?php

declare(strict_types=1);

/** CLI-only status writer called by deploy.sh. */
if (PHP_SAPI !== 'cli' || $argc !== 4) {
    exit(2);
}

[$script, $status, $step, $commit] = $argv;
$allowedStatuses = ['running', 'success', 'failed'];

if (!in_array($status, $allowedStatuses, true)) {
    exit(2);
}

$statusPath = dirname(__DIR__) . '/runtime/status.json';
$statusData = [
    'status' => $status,
    'step' => $step,
    'commit' => $commit,
    'updated_at' => gmdate('c'),
];

if ($status === 'failed') {
    $statusData['error'] = $step;
}

$temporaryPath = tempnam(dirname($statusPath), '.status-');

if ($temporaryPath === false) {
    exit(3);
}

try {
    $json = json_encode(
        $statusData,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
    );

    if (file_put_contents($temporaryPath, $json . "\n") === false) {
        exit(3);
    }

    chmod($temporaryPath, 0600);

    if (!rename($temporaryPath, $statusPath)) {
        exit(3);
    }
} finally {
    if (is_file($temporaryPath)) {
        unlink($temporaryPath);
    }
}
