<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$session = getSession($database, $id);
if ($session === null) errorResponse('NOT_FOUND', 'Session not found.', 404);
requirePermission(canAccessAttendance($database, $jwt, $session, $session['batch']));
$rows = $database->select('attendance', ['[>]member_profiles' => ['member_id' => 'id']], [
    'attendance.id', 'attendance.batch_session_id', 'attendance.member_id', 'attendance.attendance_status',
    'attendance.remarks', 'attendance.marked_by_member_id', 'attendance.marked_at', 'attendance.created_at', 'attendance.updated_at',
    'member_profiles.id(profile_id)', 'member_profiles.first_name', 'member_profiles.middle_name', 'member_profiles.last_name',
    'member_profiles.date_of_birth', 'member_profiles.gender', 'member_profiles.mobile', 'member_profiles.email',
    'member_profiles.photo_url', 'member_profiles.blood_group', 'member_profiles.status(profile_status)',
    'member_profiles.created_at(profile_created_at)', 'member_profiles.updated_at(profile_updated_at)',
], ['attendance.batch_session_id' => $id]) ?? [];
$records = [];
foreach ($rows as $row) {
    $profileId = (int) $row['profile_id'];
    $record = castIds($row);
    unset($record['profile_id'], $record['first_name'], $record['middle_name'], $record['last_name'], $record['date_of_birth'], $record['gender'], $record['mobile'], $record['email'], $record['photo_url'], $record['blood_group'], $record['profile_status'], $record['profile_created_at'], $record['profile_updated_at']);
    $record['member'] = [
        'id' => $profileId, 'first_name' => $row['first_name'], 'middle_name' => $row['middle_name'], 'last_name' => $row['last_name'],
        'date_of_birth' => $row['date_of_birth'], 'gender' => $row['gender'], 'mobile' => $row['mobile'], 'email' => $row['email'],
        'photo_url' => $row['photo_url'], 'blood_group' => $row['blood_group'], 'status' => $row['profile_status'],
        'created_at' => $row['profile_created_at'], 'updated_at' => $row['profile_updated_at'],
    ];
    $records[] = $record;
}
successResponse($records);

