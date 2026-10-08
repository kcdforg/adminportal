<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
$where = [];
if (isAdmin($jwt)) {
    foreach (['family_id', 'batch_id', 'status', 'payment_status'] as $field) {
        if (isset($_GET[$field]) && $_GET[$field] !== '') $where[$field] = in_array($field, ['family_id', 'batch_id'], true) ? (int) $_GET[$field] : $_GET[$field];
    }
} else {
    $familyIds = array_values(array_map('intval', (array) ($jwt['family_ids'] ?? [])));
    if ($familyIds === []) {
        $result = emptyPage($_GET);
        jsonResponse(['success' => true, 'data' => [], 'meta' => $result['meta']]);
    }
    $requested = isset($_GET['family_id']) ? (int) $_GET['family_id'] : 0;
    $where['family_id'] = $requested > 0 && in_array($requested, $familyIds, true) ? $requested : $familyIds[0];
    foreach (['batch_id', 'status', 'payment_status'] as $field) {
        if (isset($_GET[$field]) && $_GET[$field] !== '') $where[$field] = in_array($field, ['batch_id'], true) ? (int) $_GET[$field] : $_GET[$field];
    }
}
$total = (int) $database->count('enrollments', $where);
$perPage = min(max((int) ($_GET['per_page'] ?? 20), 1), 100);
$page = max((int) ($_GET['page'] ?? 1), 1);
$ids = $database->select('enrollments', 'id', array_merge($where, [
    'ORDER' => ['enrolled_at' => 'DESC'],
    'LIMIT' => [($page - 1) * $perPage, $perPage],
])) ?? [];
$data = [];
foreach ($ids as $enrollmentId) $data[] = getEnrollment($database, (int) $enrollmentId);
jsonResponse(['success' => true, 'data' => $data, 'meta' => [
    'total' => $total, 'per_page' => $perPage, 'current_page' => $page, 'last_page' => max(1, (int) ceil($total / $perPage)),
]]);

