<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
$filters = $_GET;
if (!isAdmin($jwt)) {
    $filters['status'] = 'active';
}
$result = paginate(
    $database,
    'entities',
    $filters,
    ['entity_type' => 'entity_type', 'status' => 'status'],
    ['id', 'name', 'entity_type', 'created_at'],
    ['name']
);
foreach ($result['data'] as &$entity) {
    if ($entity['meta'] !== null) {
        $entity['meta'] = json_decode((string) $entity['meta'], true);
    }
}
unset($entity);
jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);

