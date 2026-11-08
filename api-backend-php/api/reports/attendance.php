<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/reports.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
requireReportRole($jwt, ['admin_super', 'admin_program_manager', 'admin_readonly']);
$batchId = reportPositiveId('batch_id');
[$dateFrom, $dateTo] = reportDateRange();

$batchQuery = $database->pdo->prepare('SELECT id FROM student_batches WHERE id = :id');
$batchQuery->execute([':id' => $batchId]);
if ($batchQuery->fetchColumn() === false) {
    errorResponse('NOT_FOUND', 'Batch not found.', 404);
}

$conditions = ['batch_id = :batch_id'];
$params = [':batch_id' => $batchId];
addReportDateConditions($conditions, $params, 'session_date', $dateFrom, $dateTo);
$sessionsQuery = $database->pdo->prepare(
    'SELECT id, session_date, session_title FROM batch_sessions WHERE ' . implode(' AND ', $conditions) . ' ORDER BY session_date, id'
);
$sessionsQuery->execute($params);
$sessions = $sessionsQuery->fetchAll(PDO::FETCH_ASSOC);

$membersQuery = $database->pdo->prepare(
    "SELECT mp.id AS member_id, CONCAT_WS(' ', mp.first_name, mp.middle_name, mp.last_name) AS member_name " .
    "FROM batch_members bm JOIN member_profiles mp ON mp.id = bm.member_id " .
    "WHERE bm.batch_id = :batch_id AND bm.status = 'active' ORDER BY mp.last_name, mp.first_name"
);
$membersQuery->execute([':batch_id' => $batchId]);
$members = $membersQuery->fetchAll(PDO::FETCH_ASSOC);
$rowsById = [];
foreach ($members as $member) {
    $memberId = (int) $member['member_id'];
    $rowsById[$memberId] = [
        'member_id' => $memberId,
        'member_name' => $member['member_name'],
        'sessions' => [],
    ];
}

$dateCounts = [];
foreach ($sessions as $session) {
    $date = (string) $session['session_date'];
    $dateCounts[$date] = ($dateCounts[$date] ?? 0) + 1;
}
$labels = [];
$sessionDates = [];
foreach ($sessions as $session) {
    $date = (string) $session['session_date'];
    $label = $dateCounts[$date] > 1
        ? $date . ' · ' . ((string) ($session['session_title'] ?? '') ?: 'Session ' . $session['id'])
        : $date;
    $labels[(int) $session['id']] = $label;
    $sessionDates[] = $label;
    foreach ($rowsById as &$row) {
        $row['sessions'][$label] = 'unrecorded';
    }
    unset($row);
}

if ($sessions !== [] && $rowsById !== []) {
    $sessionParams = [];
    $placeholders = [];
    foreach (array_keys($labels) as $index => $sessionId) {
        $placeholder = ':session_' . $index;
        $placeholders[] = $placeholder;
        $sessionParams[$placeholder] = $sessionId;
    }
    $attendanceQuery = $database->pdo->prepare(
        'SELECT member_id, batch_session_id, attendance_status FROM attendance WHERE batch_session_id IN (' .
        implode(', ', $placeholders) . ')'
    );
    $attendanceQuery->execute($sessionParams);
    foreach ($attendanceQuery->fetchAll(PDO::FETCH_ASSOC) as $attendance) {
        $memberId = (int) $attendance['member_id'];
        $sessionId = (int) $attendance['batch_session_id'];
        if (isset($rowsById[$memberId], $labels[$sessionId])) {
            $rowsById[$memberId]['sessions'][$labels[$sessionId]] = $attendance['attendance_status'];
        }
    }
}

successResponse([
    'members' => array_values($rowsById),
    'session_dates' => $sessionDates,
]);
