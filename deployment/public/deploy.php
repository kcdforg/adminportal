
<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

requireLogin();

// Only POST requests can initiate deployments.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

verifyCsrfToken();

// Validate the applications selected on the dashboard.
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

$selectedApplications = array_values(
    array_unique($selectedApplications)
);

// Prevent simultaneous requests from the web interface.
$lockFile = fopen(RUNTIME_DIR . '/request.lock', 'c');

if ($lockFile === false || !flock($lockFile, LOCK_EX)) {
    http_response_code(503);
    exit('Unable to lock deployment requests.');
}

try {
    $requestFile = RUNTIME_DIR . '/request.json';
    $processingFile = RUNTIME_DIR . '/processing.json';

    /*
     * Only one deployment may be pending or running.
     *
     * The cron worker moves request.json to processing.json
     * when it starts processing the request.
     */
    if (
        is_file($requestFile)
        || is_file($processingFile)
    ) {
        header('Location: /', true, 303);
        exit;
    }

    // Generate a unique identifier for this request.
    $requestId = bin2hex(random_bytes(12));

    $request = [
        'id' => $requestId,
        'apps' => $selectedApplications,
        'requested_at' => gmdate('c'),
    ];

    /*
     * Save the request for the cron worker.
     *
     * No shell execution is required from PHP.
     */
    writeJsonAtomically($requestFile, $request);

    // Update the dashboard status.
    writeJsonAtomically(RUNTIME_DIR . '/status.json', [
        'status' => 'queued',
        'step' => 'Waiting for cron deployment worker',
        'request_id' => $requestId,
        'updated_at' => gmdate('c'),
    ]);

} finally {
    flock($lockFile, LOCK_UN);
    fclose($lockFile);
}

// Return to the dashboard.
header('Location: /', true, 303);
exit;
