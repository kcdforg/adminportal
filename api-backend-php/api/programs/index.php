<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$database = $bootstrap['database'];
$jwt = checkAuth();

    $result = paginateAcademics($database, 'programs', $_GET, ['status' => 'status', 'program_type' => 'program_type'], ['id', 'program_name', 'program_type', 'created_at'], 'created_at');
    jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);

