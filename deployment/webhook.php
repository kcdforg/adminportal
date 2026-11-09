<?php
declare(strict_types=1);
// Route POST /deploy to this file from your API router. Keep deployment/ outside web root.
$config = require __DIR__ . '/config/config.php';
header('Content-Type: application/json');
function reply(int $code, array $data): never { http_response_code($code); echo json_encode($data); exit; }
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') reply(405, ['error' => 'POST required']);
$body = file_get_contents('php://input', false, null, 0, 1048577);
if ($body === false || strlen($body) > 1048576) reply(413, ['error' => 'Payload too large']);
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$expected = 'sha256=' . hash_hmac('sha256', $body, $config['secret']);
if (!hash_equals($expected, $signature)) reply(401, ['error' => 'Invalid signature']);
$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
if ($event === 'ping') reply(200, ['ok' => true]);
if ($event !== 'push') reply(202, ['ignored' => 'Not a push']);
$data = json_decode($body, true);
if (!is_array($data)) reply(400, ['error' => 'Invalid JSON']);
if (($data['repository']['full_name'] ?? '') !== $config['repository'] || ($data['ref'] ?? '') !== 'refs/heads/' . $config['branch']) reply(202, ['ignored' => 'Repository or branch mismatch']);
if (($data['deleted'] ?? false) === true) reply(202, ['ignored' => 'Deleted branch']);
$sha = $data['after'] ?? '';
if (!is_string($sha) || !preg_match('/^[0-9a-f]{40}$/', $sha)) reply(400, ['error' => 'Invalid SHA']);
$dir = $config['runtime'];
if (!is_dir($dir) || !is_writable($dir)) reply(503, ['error' => 'Runtime unavailable']);
$lock = fopen($dir . '/state.lock', 'c');
if (!$lock || !flock($lock, LOCK_EX)) reply(503, ['error' => 'State lock unavailable']);
$pending = ['commit' => $sha, 'delivery_id' => $_SERVER['HTTP_X_GITHUB_DELIVERY'] ?? bin2hex(random_bytes(12)), 'received_at' => gmdate('c')];
$tmp = tempnam($dir, 'pending-');
if ($tmp === false || file_put_contents($tmp, json_encode($pending, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false || !rename($tmp, $dir . '/pending.json')) {
    flock($lock, LOCK_UN); reply(503, ['error' => 'Unable to queue']);
}
flock($lock, LOCK_UN); fclose($lock);
// Every accepted webhook launches a detached worker. Workers serialize on deploy.lock.
// Do not inject webhook fields into this command.
$command = '/usr/bin/nohup /bin/bash ' . escapeshellarg($config['worker']) . ' >> ' . escapeshellarg($dir . '/launcher.log') . ' 2>&1 < /dev/null &';
exec($command, $ignored, $exit);
if ($exit !== 0) reply(503, ['queued' => true, 'warning' => 'Worker launch failed']);
reply(202, ['queued' => true, 'commit' => $sha]);
