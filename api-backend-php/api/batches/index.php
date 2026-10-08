<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$database = $bootstrap['database'];
$jwt = checkAuth();

    $where = [];
    if (isAdmin($jwt)) {
        // Administrators can filter across all batches.
    } elseif (isTrainer($jwt)) {
        $trainerId = getTrainerId($database, $jwt);
        if ($trainerId === null) {
            $result = emptyPage($_GET);
            jsonResponse(['success' => true, 'data' => [], 'meta' => $result['meta']]);
        }
        $where['trainer_id'] = $trainerId;
    } else {
        $batchIds = $database->select('enrollments', 'batch_id', [
            'member_id' => getUserId($jwt),
            'status' => ['active', 'pending'],
        ]) ?? [];
        if ($batchIds === []) {
            $result = emptyPage($_GET);
            jsonResponse(['success' => true, 'data' => [], 'meta' => $result['meta']]);
        }
        $where['id'] = array_values(array_unique(array_map('intval', $batchIds)));
    }

    $result = paginateAcademics($database, 'student_batches', $_GET, ['status' => 'status', 'program_id' => 'program_id', 'trainer_id' => 'trainer_id'], ['id', 'batch_name', 'start_date', 'created_at'], 'created_at', $where);
    foreach ($result['data'] as &$batch) {
        $batch = getBatch($database, (int) $batch['id']);
    }
    unset($batch);

    jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);

