<?php
declare(strict_types=1);
// Atomic state helper shared by webhook and shell deployment process.
$config = require dirname(__DIR__) . '/config/config.php';
$dir = $config['runtime'];
$lock = fopen($dir . '/state.lock', 'c');
if (!$lock || !flock($lock, LOCK_EX)) exit(2);
function readState(string $path): array { $v = @json_decode((string) @file_get_contents($path), true); return is_array($v) ? $v : []; }
function writeState(string $path, array $data): void {
    $tmp = tempnam(dirname($path), 'state-');
    if ($tmp === false || file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false || !rename($tmp, $path)) exit(3);
}
$pending = readState($dir . '/pending.json');
$status = readState($dir . '/status.json');
$action = $argv[1] ?? '';
if ($action === 'next') {
    if (!empty($pending['delivery_id']) && $pending['delivery_id'] !== ($status['processed_delivery_id'] ?? null)) {
        echo json_encode($pending);
    }
} elseif ($action === 'start') {
    $request = json_decode($argv[2] ?? '', true);
    if (!is_array($request) || empty($request['delivery_id'])) exit(4);
    $status = array_merge($status, [
        'status' => 'running', 'commit' => $request['commit'], 'delivery_id' => $request['delivery_id'],
        'step' => 'Starting deployment', 'started_at' => gmdate('c'), 'updated_at' => gmdate('c')
    ]);
    writeState($dir . '/status.json', $status);
} elseif ($action === 'finish') {
    $request = json_decode($argv[2] ?? '', true);
    if (!is_array($request) || empty($request['delivery_id'])) exit(4);
    $outcome = $argv[3] ?? 'failed';
    if (!in_array($outcome, ['success', 'failed', 'preview', 'skipped'], true)) exit(4);
    $status = array_merge($status, [
        'status' => $outcome, 'commit' => $request['commit'], 'delivery_id' => $request['delivery_id'],
        'processed_delivery_id' => $request['delivery_id'], 'step' => $argv[4] ?? $outcome,
        'updated_at' => gmdate('c'), 'completed_at' => gmdate('c')
    ]);
    if ($outcome === 'success') $status['last_successful_commit'] = $argv[5] ?? $request['commit'];
    writeState($dir . '/status.json', $status);
} else { fwrite(STDERR, "Invalid action\n"); exit(1); }
flock($lock, LOCK_UN);
