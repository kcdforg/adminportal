<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$batch = getBatch($database, $id, false);
if ($batch === null) errorResponse('NOT_FOUND', 'Batch not found.', 404);
requirePermission(canAccessBatch($database, $jwt, $batch));
$where = ['batch_id' => $id];
if (!empty($_GET['status'])) $where['status'] = $_GET['status'];
if (!empty($_GET['session_date_from'])) $where['session_date[>=]'] = $_GET['session_date_from'];
if (!empty($_GET['session_date_to'])) $where['session_date[<=]'] = $_GET['session_date_to'];
$total = (int) $database->count('batch_sessions', $where);
$perPage = min(max((int) ($_GET['per_page'] ?? 20), 1), 100);
$page = max((int) ($_GET['page'] ?? 1), 1);
$rows = $database->select('batch_sessions', '*', array_merge($where, [
    'ORDER' => ['session_date' => 'ASC', 'session_number' => 'ASC'],
    'LIMIT' => [($page - 1) * $perPage, $perPage],
])) ?? [];
$data = [];
foreach ($rows as $row) $data[] = getSession($database, (int) $row['id']);
jsonResponse(['success' => true, 'data' => $data, 'meta' => [
    'total' => $total, 'per_page' => $perPage, 'current_page' => $page, 'last_page' => max(1, (int) ceil($total / $perPage)),
]]);

