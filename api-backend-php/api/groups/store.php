<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$database = $bootstrap['database'];
$jwt = checkAuth();

requireGroupAdmin($jwt, 'Only admins can create groups.');
$body = getJsonBody();
validateOrFail(validateGroup($body, 'create'));
$actorId = getActorId($jwt);

$id = communityTransaction($database, static function () use ($database, $body, $actorId): int {
        $database->insert('parent_groups', [
            'group_name' => $body['group_name'],
            'description' => !empty($body['description']) ? $body['description'] : null,
            'visibility' => $body['visibility'],
            'status' => 'active',
        ]);
        $groupId = (int) $database->id();
        logActivity($database, ['profile_id' => $actorId], 'created', 'parent_groups', $groupId, null, [
            'group_name' => $body['group_name'],
            'visibility' => $body['visibility'],
        ]);
        return $groupId;
    });

successResponse(findOne($database, 'parent_groups', ['id' => $id]), 'Group created successfully.', 201);

