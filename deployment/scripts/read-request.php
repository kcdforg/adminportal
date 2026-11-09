<?php

declare(strict_types=1);

/**
 * CLI-only reader for the deployment request.
 *
 * Prints one validated application identifier per line. Bash must never
 * evaluate or execute data read from the request JSON.
 */
if (PHP_SAPI !== 'cli' || $argc !== 2) {
    exit(2);
}

$request = json_decode((string) file_get_contents($argv[1]), true);
$allowedApplications = ['api', 'admin', 'member'];

if (
    !is_array($request)
    || !isset($request['apps'])
    || !is_array($request['apps'])
    || count($request['apps']) === 0
) {
    fwrite(STDERR, "Invalid deployment request.\n");
    exit(2);
}

foreach ($request['apps'] as $application) {
    if (!is_string($application) || !in_array($application, $allowedApplications, true)) {
        fwrite(STDERR, "Unknown application in deployment request.\n");
        exit(2);
    }
}

foreach (array_unique($request['apps']) as $application) {
    echo $application, "\n";
}
