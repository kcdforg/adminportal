<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$session = getSession($database, $id);
if ($session === null) errorResponse('NOT_FOUND', 'Session not found.', 404);
requirePermission(canAccessAttendance($database, $jwt, $session, $session['batch']));

$query = $database->pdo->prepare(
    'SELECT bm.member_id, bm.status AS batch_member_status, ' .
    'a.id AS attendance_id, a.attendance_status, a.remarks, a.marked_by_member_id, ' .
    'mp.first_name, mp.middle_name, mp.last_name, mp.date_of_birth, mp.gender, mp.mobile, mp.email, ' .
    'mp.photo_url, mp.blood_group, mp.status AS profile_status, mp.created_at AS profile_created_at, ' .
    'mp.updated_at AS profile_updated_at ' .
    'FROM batch_members bm ' .
    'JOIN member_profiles mp ON mp.id = bm.member_id ' .
    'LEFT JOIN attendance a ON a.batch_session_id = :session_id AND a.member_id = bm.member_id ' .
    'WHERE bm.batch_id = :batch_id AND bm.status = :active_status ' .
    'ORDER BY mp.last_name, mp.first_name'
);
$query->execute([
    ':session_id' => $id,
    ':batch_id' => (int) $session['batch_id'],
    ':active_status' => 'active',
]);
$records = [];
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $memberId = (int) $row['member_id'];
    $attendanceId = $row['attendance_id'] === null ? null : (int) $row['attendance_id'];
    $profile = [
        'id' => $memberId,
        'first_name' => $row['first_name'],
        'middle_name' => $row['middle_name'],
        'last_name' => $row['last_name'],
        'date_of_birth' => $row['date_of_birth'],
        'gender' => $row['gender'],
        'mobile' => $row['mobile'],
        'email' => $row['email'],
        'photo_url' => $row['photo_url'],
        'blood_group' => $row['blood_group'],
        'status' => $row['profile_status'],
        'created_at' => $row['profile_created_at'],
        'updated_at' => $row['profile_updated_at'],
    ];
    $records[] = [
        'id' => $attendanceId,
        'session_id' => $id,
        'member_id' => $memberId,
        'status' => $row['attendance_status'],
        'notes' => $row['remarks'],
        'marked_by' => $row['marked_by_member_id'] === null ? null : (int) $row['marked_by_member_id'],
        'recorded' => $attendanceId !== null,
        'member' => $profile,
    ];
}
successResponse($records);
