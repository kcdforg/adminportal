<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';

$database = $bootstrap['database'];
$jwt = checkAuth();
$groupId = (int) ($routeParams['group_id'] ?? 0);
$contentId = (int) ($routeParams['content_id'] ?? 0);
$normalizedPath = normalizeApiRequestPath(
    (string) ($_SERVER['REQUEST_URI'] ?? ''),
    (string) ($_SERVER['SCRIPT_NAME'] ?? '/api/index.php')
);
$pathSegments = explode('/', trim($normalizedPath, '/'));
$resource = (string) ($pathSegments[4] ?? '');
$action = (string) ($pathSegments[6] ?? '');
$group = findOne($database, 'parent_groups', ['id' => $groupId]);

if ($group === null) {
    errorResponse('NOT_FOUND', 'Group not found.', 404);
}
if (!canAccessGroup($database, $jwt, $group, 'view')) {
    errorResponse('UNAUTHORIZED', 'You do not have permission to view this group.', 403);
}

$types = ['posts' => 'announcement', 'polls' => 'poll', 'events' => 'event'];
if (!isset($types[$resource])) {
    errorResponse('NOT_FOUND', 'Group content resource not found.', 404);
}
$contentType = $types[$resource];
$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$memberId = ($jwt['user_type'] ?? null) === 'member' ? getUserId($jwt) : 0;
$actorId = $memberId > 0 ? $memberId : (int) ($jwt['login_id'] ?? 0);
$isManager = isCommunityAdmin($jwt)
    || in_array(getGroupRole($database, $jwt, $groupId), ['owner', 'admin', 'moderator'], true);
$isSettingsManager = canManageGroupSettings($database, $jwt, $group);

$fetch = static function (int $id) use ($database, $groupId, $contentType, $memberId): ?array {
    $statement = $database->pdo->prepare(
        'SELECT * FROM group_contents WHERE id = :id AND group_id = :group_id AND content_type = :content_type'
    );
    $statement->execute([':id' => $id, ':group_id' => $groupId, ':content_type' => $contentType]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    if (!is_array($row)) {
        return null;
    }
    $row = castIds($row);
    $row['content_data'] = $row['content_data'] === null ? [] : json_decode((string) $row['content_data'], true, 512, JSON_THROW_ON_ERROR);
    if ($contentType === 'event') {
        $row['registered_count'] = (int) $database->count('group_event_registrations', [
            'event_id' => $id,
            'status' => 'registered',
        ]);
        $row['my_registration'] = $memberId > 0
            ? findOne($database, 'group_event_registrations', [
                'event_id' => $id,
                'member_id' => $memberId,
                'status' => 'registered',
            ])
            : null;
    }
    if ($contentType === 'poll') {
        $row['vote_count'] = (int) $database->count('group_poll_votes', ['poll_id' => $id]);
        $row['has_voted'] = $memberId > 0
            && $database->has('group_poll_votes', ['poll_id' => $id, 'member_id' => $memberId]);
    }
    return $row;
};

$publicContent = static function (?array $content): ?array {
    if ($content !== null) {
        unset($content['created_by_user_type'], $content['created_by_user_id']);
    }
    return $content;
};

$requireContent = static function () use ($fetch, $contentId): array {
    $content = $fetch($contentId);
    if ($content === null) {
        errorResponse('NOT_FOUND', 'Group content not found.', 404);
    }
    return $content;
};

$requireActiveMember = static function () use ($database, $groupId, $memberId): void {
    if ($memberId < 1 || !isActiveGroupMember($database, $groupId, $memberId)) {
        errorResponse('UNAUTHORIZED', 'An active group membership is required.', 403);
    }
};

$decodeData = static function (array $row): array {
    return $row['content_data'] === null ? [] : (array) json_decode((string) $row['content_data'], true, 512, JSON_THROW_ON_ERROR);
};

$status = static function (mixed $value, string $default = 'published'): string {
    $status = $value ?? $default;
    if (!is_string($status) || !in_array($status, ['draft', 'published', 'archived'], true)) {
        errorResponse('VALIDATION_FAILED', 'The status must be draft, published, or archived.', 422, [
            'status' => ['Choose draft, published, or archived.'],
        ]);
    }
    return $status;
};

$validateTitle = static function (mixed $value): string {
    if (!is_string($value) || trim($value) === '' || mb_strlen(trim($value)) > 255) {
        errorResponse('VALIDATION_FAILED', 'A valid title is required.', 422, [
            'title' => ['The title must contain between 1 and 255 characters.'],
        ]);
    }
    return trim($value);
};

if ($method === 'GET' && $contentId === 0) {
    $perPage = min(max((int) ($_GET['per_page'] ?? 20), 1), 100);
    $page = max((int) ($_GET['page'] ?? 1), 1);
    $where = 'group_id = :group_id AND content_type = :content_type';
    $params = [':group_id' => $groupId, ':content_type' => $contentType];
    if (!$isManager) {
        $where .= " AND status = 'published'";
    } elseif (isset($_GET['status'])) {
        $where .= ' AND status = :status';
        $params[':status'] = $status($_GET['status']);
    }
    $count = $database->pdo->prepare("SELECT COUNT(*) FROM group_contents WHERE $where");
    $count->execute($params);
    $total = (int) $count->fetchColumn();
    $statement = $database->pdo->prepare(
        "SELECT * FROM group_contents WHERE $where ORDER BY created_at DESC, id DESC LIMIT :limit OFFSET :offset"
    );
    foreach ($params as $name => $value) {
        $statement->bindValue($name, $value);
    }
    $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $statement->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
    $statement->execute();
    $items = [];
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $item = $fetch((int) $row['id']);
        if ($item !== null) {
            $items[] = $publicContent($item);
        }
    }
    jsonResponse([
        'success' => true,
        'data' => $items,
        'meta' => ['total' => $total, 'per_page' => $perPage, 'current_page' => $page, 'last_page' => max(1, (int) ceil($total / $perPage))],
    ]);
}

if ($method === 'POST' && $contentId === 0) {
    if (!$isManager) {
        errorResponse('UNAUTHORIZED', 'You do not have permission to create group content.', 403);
    }
    if ($group['status'] !== 'active') {
        errorResponse('GROUP_INACTIVE', 'Content cannot be created in an inactive or archived group.', 409);
    }
    $body = getJsonBody();
    $title = $validateTitle($body['title'] ?? null);
    $content = $body['content'] ?? null;
    if ($content !== null && (!is_string($content) || mb_strlen($content) > 20000)) {
        errorResponse('VALIDATION_FAILED', 'Content must be a string of at most 20,000 characters.', 422, [
            'content' => ['Enter no more than 20,000 characters.'],
        ]);
    }
    $data = [];
    if ($contentType === 'poll') {
        $options = $body['options'] ?? null;
        if (!is_array($options) || count($options) < 2 || count($options) > 10) {
            errorResponse('VALIDATION_FAILED', 'Polls require between 2 and 10 options.', 422, [
                'options' => ['Provide between 2 and 10 options.'],
            ]);
        }
        foreach ($options as $option) {
            if (!is_string($option) || trim($option) === '' || mb_strlen(trim($option)) > 255) {
                errorResponse('VALIDATION_FAILED', 'Each poll option must contain 1 to 255 characters.', 422, [
                    'options' => ['Each option must contain 1 to 255 characters.'],
                ]);
            }
        }
        $data['options'] = array_values(array_map('trim', $options));
        foreach (['allow_multiple', 'allow_vote_change', 'show_results_before_voting'] as $flag) {
            if (isset($body[$flag]) && !is_bool($body[$flag])) {
                errorResponse('VALIDATION_FAILED', 'Poll rules must be boolean values.', 422, [
                    $flag => ['Choose true or false.'],
                ]);
            }
        }
        $data['allow_multiple'] = $body['allow_multiple'] ?? false;
        $data['allow_vote_change'] = $body['allow_vote_change'] ?? false;
        $data['show_results_before_voting'] = $body['show_results_before_voting'] ?? false;
        $data['closes_at'] = $body['closes_at'] ?? null;
        if ($data['closes_at'] !== null && (!is_string($data['closes_at']) || strtotime($data['closes_at']) === false)) {
            errorResponse('VALIDATION_FAILED', 'The poll closing date is invalid.', 422, ['closes_at' => ['Provide a valid date and time.']]);
        }
    } elseif ($contentType === 'event') {
        foreach (['start_at', 'end_at'] as $field) {
            if (!isset($body[$field]) || !is_string($body[$field]) || strtotime($body[$field]) === false) {
                errorResponse('VALIDATION_FAILED', 'Valid event start and end dates are required.', 422, [
                    $field => ['Provide a valid date and time.'],
                ]);
            }
        }
        if (strtotime($body['end_at']) <= strtotime($body['start_at'])) {
            errorResponse('VALIDATION_FAILED', 'The event end must be after its start.', 422, [
                'end_at' => ['Choose a time after the event start.'],
            ]);
        }
        $capacity = $body['capacity'] ?? null;
        if ($capacity !== null
            && (!(is_int($capacity) || (is_string($capacity) && ctype_digit($capacity))) || (int) $capacity < 1)) {
            errorResponse('VALIDATION_FAILED', 'Event capacity must be a positive integer.', 422, [
                'capacity' => ['Enter a positive number or leave capacity empty.'],
            ]);
        }
        $location = $body['location'] ?? null;
        if ($location !== null && (!is_string($location) || mb_strlen($location) > 255)) {
            errorResponse('VALIDATION_FAILED', 'The event location must be at most 255 characters.', 422, [
                'location' => ['Enter at most 255 characters.'],
            ]);
        }
        $data = [
            'start_at' => $body['start_at'],
            'end_at' => $body['end_at'],
            'location' => $location === null ? null : trim($location),
            'capacity' => $capacity === null ? null : (int) $capacity,
        ];
    }
    $createdStatus = $status($body['status'] ?? null);
    $creatorType = (string) ($jwt['user_type'] ?? 'admin');
    $insert = $database->pdo->prepare(
        'INSERT INTO group_contents (group_id, content_type, title, content, status, content_data, created_by_user_type, created_by_user_id) '
        . 'VALUES (:group_id, :content_type, :title, :content, :status, :content_data, :creator_type, :creator_id)'
    );
    $insert->execute([
        ':group_id' => $groupId,
        ':content_type' => $contentType,
        ':title' => $title,
        ':content' => $content,
        ':status' => $createdStatus,
        ':content_data' => json_encode($data, JSON_THROW_ON_ERROR),
        ':creator_type' => $creatorType,
        ':creator_id' => $actorId,
    ]);
    successResponse($publicContent($fetch((int) $database->pdo->lastInsertId())), 'Group content created successfully.', 201);
}

$content = $requireContent();

if ($method === 'GET' && $action === '') {
    if (!$isManager && $content['status'] !== 'published') {
        errorResponse('NOT_FOUND', 'Group content not found.', 404);
    }
    successResponse($publicContent($content));
}

if ($contentType === 'event' && $action === 'registrations' && $method === 'GET') {
    $requireActiveMember();
    $registration = findOne($database, 'group_event_registrations', [
        'event_id' => $contentId,
        'member_id' => $memberId,
        'status' => 'registered',
    ]);
    successResponse($registration === null ? null : castIds($registration));
}

if ($contentType === 'event' && $action === 'registrations' && $method === 'POST') {
    $requireActiveMember();
    if ($group['status'] !== 'active') {
        errorResponse('GROUP_INACTIVE', 'Registration is unavailable for an inactive or archived group.', 409);
    }
    if ($content['status'] !== 'published' || strtotime((string) ($content['content_data']['start_at'] ?? '')) <= time()) {
        errorResponse('EVENT_CLOSED', 'Registration is not available for this event.', 409);
    }
    $data = $content['content_data'];
    $existing = findOne($database, 'group_event_registrations', ['event_id' => $contentId, 'member_id' => $memberId]);
    if ($existing !== null && $existing['status'] === 'registered') {
        errorResponse('ALREADY_REGISTERED', 'You are already registered for this event.', 409);
    }
    communityTransaction($database, static function () use ($database, $groupId, $contentId, $memberId, $data, $existing): void {
        $lockSql = 'SELECT id, status, content_data FROM group_contents WHERE id = :id AND group_id = :group_id'
            . ($database->type === 'sqlite' ? '' : ' FOR UPDATE');
        $lock = $database->pdo->prepare($lockSql);
        $lock->execute([':id' => $contentId, ':group_id' => $groupId]);
        $lockedEvent = $lock->fetch(PDO::FETCH_ASSOC);
        if (!is_array($lockedEvent)) {
            failCommunity('NOT_FOUND', 'Event not found.', 404);
        }
        $lockedData = (array) json_decode((string) $lockedEvent['content_data'], true, 512, JSON_THROW_ON_ERROR);
        if ($lockedEvent['status'] !== 'published'
            || strtotime((string) ($lockedData['start_at'] ?? '')) <= time()) {
            failCommunity('EVENT_CLOSED', 'Registration is not available for this event.', 409);
        }
        if (isset($lockedData['capacity']) && $lockedData['capacity'] !== null) {
            $statement = $database->pdo->prepare(
                "SELECT COUNT(*) FROM group_event_registrations WHERE event_id = :event_id AND status = 'registered'"
            );
            $statement->execute([':event_id' => $contentId]);
            if ((int) $statement->fetchColumn() >= (int) $lockedData['capacity']) {
                failCommunity('EVENT_FULL', 'This event has reached its registration limit.', 409);
            }
        }
        if ($existing !== null) {
            $database->update('group_event_registrations', [
                'status' => 'registered',
                'registered_at' => date('Y-m-d H:i:s'),
            ], ['id' => $existing['id']]);
        } else {
            $database->insert('group_event_registrations', [
                'event_id' => $contentId,
                'member_id' => $memberId,
                'status' => 'registered',
            ]);
        }
    });
    successResponse($fetch($contentId), 'Registered for event successfully.', 201);
}

if ($contentType === 'event' && $action === 'registrations' && $method === 'DELETE') {
    $requireActiveMember();
    $database->update('group_event_registrations', ['status' => 'cancelled'], [
        'event_id' => $contentId,
        'member_id' => $memberId,
        'status' => 'registered',
    ]);
    successResponse(null, 'Event registration cancelled.');
}

if ($contentType === 'poll' && $action === 'vote' && $method === 'POST') {
    $requireActiveMember();
    if ($group['status'] !== 'active') {
        errorResponse('GROUP_INACTIVE', 'Voting is unavailable for an inactive or archived group.', 409);
    }
    $data = $content['content_data'];
    if ($content['status'] !== 'published'
        || ($data['closes_at'] !== null && strtotime((string) $data['closes_at']) <= time())) {
        errorResponse('POLL_CLOSED', 'Voting is closed for this poll.', 409);
    }
    $body = getJsonBody();
    $choices = $body['option_indexes'] ?? (isset($body['option_index']) ? [$body['option_index']] : null);
    if (!is_array($choices) || $choices === [] || (!$data['allow_multiple'] && count($choices) !== 1)) {
        errorResponse('VALIDATION_FAILED', 'Select a valid option for this poll.', 422, [
            'option_indexes' => ['Select one option, or multiple options when enabled.'],
        ]);
    }
    foreach ($choices as $choice) {
        if (!(is_int($choice) || (is_string($choice) && ctype_digit($choice)))) {
            errorResponse('VALIDATION_FAILED', 'A selected poll option does not exist.', 422, [
                'option_indexes' => ['Choose only options provided by this poll.'],
            ]);
        }
    }
    $choices = array_values(array_unique(array_map('intval', $choices)));
    foreach ($choices as $choice) {
        if ($choice < 0 || $choice >= count($data['options'])) {
            errorResponse('VALIDATION_FAILED', 'A selected poll option does not exist.', 422, [
                'option_indexes' => ['Choose only options provided by this poll.'],
            ]);
        }
    }
    communityTransaction($database, static function () use ($database, $groupId, $contentId, $memberId, $choices): void {
        $lockSql = 'SELECT id, content_data, status FROM group_contents WHERE id = :id AND group_id = :group_id'
            . ($database->type === 'sqlite' ? '' : ' FOR UPDATE');
        $lock = $database->pdo->prepare($lockSql);
        $lock->execute([':id' => $contentId, ':group_id' => $groupId]);
        $lockedPoll = $lock->fetch(PDO::FETCH_ASSOC);
        if (!is_array($lockedPoll)) {
            failCommunity('NOT_FOUND', 'Poll not found.', 404);
        }
        $lockedData = (array) json_decode((string) $lockedPoll['content_data'], true, 512, JSON_THROW_ON_ERROR);
        if ($lockedPoll['status'] !== 'published'
            || ($lockedData['closes_at'] !== null && strtotime((string) $lockedData['closes_at']) <= time())) {
            failCommunity('POLL_CLOSED', 'Voting is closed for this poll.', 409);
        }
        if ($choices === [] || (!$lockedData['allow_multiple'] && count($choices) !== 1)) {
            failCommunity('INVALID_VOTE', 'Select a valid option for this poll.', 422, [
                'option_indexes' => ['Select one option, or multiple options when enabled.'],
            ]);
        }
        foreach ($choices as $choice) {
            if ($choice < 0 || $choice >= count($lockedData['options'])) {
                failCommunity('INVALID_VOTE', 'A selected poll option does not exist.', 422, [
                    'option_indexes' => ['Choose only options provided by this poll.'],
                ]);
            }
        }
        $existingVote = findOne($database, 'group_poll_votes', ['poll_id' => $contentId, 'member_id' => $memberId]);
        if ($existingVote !== null && !$lockedData['allow_vote_change']) {
            failCommunity('VOTE_ALREADY_CAST', 'This poll does not allow changing a submitted vote.', 409);
        }
        if ($existingVote !== null) {
            $database->update('group_poll_votes', ['option_indexes' => json_encode($choices, JSON_THROW_ON_ERROR)], ['id' => $existingVote['id']]);
        } else {
            $database->insert('group_poll_votes', [
                'poll_id' => $contentId,
                'member_id' => $memberId,
                'option_indexes' => json_encode($choices, JSON_THROW_ON_ERROR),
            ]);
        }
    });
    successResponse($publicContent($fetch($contentId)), 'Vote submitted successfully.');
}

if ($contentType === 'poll' && $action === 'results' && $method === 'GET') {
    if (!$isSettingsManager) {
        $requireActiveMember();
    }
    $data = $content['content_data'];
    if (!$isSettingsManager && !$data['show_results_before_voting'] && !$content['has_voted']
        && ($data['closes_at'] === null || strtotime((string) $data['closes_at']) > time())) {
        errorResponse('RESULTS_HIDDEN', 'Poll results are not available yet.', 403);
    }
    $statement = $database->pdo->prepare('SELECT option_indexes FROM group_poll_votes WHERE poll_id = :poll_id');
    $statement->execute([':poll_id' => $contentId]);
    $counts = array_fill(0, count($data['options']), 0);
    foreach ($statement->fetchAll(PDO::FETCH_COLUMN) as $value) {
        foreach ((array) json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR) as $choice) {
            if (isset($counts[$choice])) {
                $counts[$choice]++;
            }
        }
    }
    successResponse([
        'options' => array_map(static fn (string $label, int $index): array => [
            'option_index' => $index,
            'label' => $label,
            'votes' => $counts[$index],
        ], $data['options'], array_keys($data['options'])),
        'total_votes' => (int) $content['vote_count'],
    ]);
}

if ($method === 'PUT') {
    if ($group['status'] !== 'active') {
        errorResponse('GROUP_INACTIVE', 'Content cannot be changed in an inactive or archived group.', 409);
    }
    $body = getJsonBody();
    $isCreator = ($content['created_by_user_type'] ?? null) === ($jwt['user_type'] ?? null)
        && (int) ($content['created_by_user_id'] ?? 0) === ($memberId > 0 ? $memberId : $actorId)
        && ($memberId === 0 || isActiveGroupMember($database, $groupId, $memberId));
    if (!$isManager && !$isCreator) {
        errorResponse('UNAUTHORIZED', 'You do not have permission to update this content.', 403);
    }
    if ($contentType === 'poll' && (int) $database->count('group_poll_votes', ['poll_id' => $contentId]) > 0) {
        errorResponse('POLL_ALREADY_VOTED', 'A poll cannot be edited after votes have been submitted.', 409);
    }
    $updates = [];
    if (array_key_exists('title', $body)) {
        $updates['title'] = $validateTitle($body['title']);
    }
    if (array_key_exists('content', $body)) {
        if ($body['content'] !== null && (!is_string($body['content']) || mb_strlen($body['content']) > 20000)) {
            errorResponse('VALIDATION_FAILED', 'Content must contain at most 20,000 characters.', 422);
        }
        $updates['content'] = $body['content'];
    }
    if (array_key_exists('status', $body)) {
        if (!$isSettingsManager) {
            errorResponse('UNAUTHORIZED', 'Only group managers can change content status.', 403);
        }
        $updates['status'] = $status($body['status']);
    }
    if (in_array($contentType, ['poll', 'event'], true) && array_intersect(array_keys($body), ['options', 'allow_multiple', 'allow_vote_change', 'show_results_before_voting', 'closes_at', 'start_at', 'end_at', 'location', 'capacity']) !== []) {
        $data = $decodeData($content);
        if ($contentType === 'poll') {
            if (array_key_exists('options', $body)) {
                if (!is_array($body['options']) || count($body['options']) < 2 || count($body['options']) > 10) {
                    errorResponse('VALIDATION_FAILED', 'Polls require between 2 and 10 options.', 422);
                }
                foreach ($body['options'] as $option) {
                    if (!is_string($option) || trim($option) === '' || mb_strlen(trim($option)) > 255) {
                        errorResponse('VALIDATION_FAILED', 'Each poll option must contain 1 to 255 characters.', 422);
                    }
                }
                $data['options'] = array_values(array_map('trim', $body['options']));
            }
            foreach (['allow_multiple', 'allow_vote_change', 'show_results_before_voting'] as $flag) {
                if (array_key_exists($flag, $body)) {
                    if (!is_bool($body[$flag])) {
                        errorResponse('VALIDATION_FAILED', 'Poll rules must be boolean values.', 422, [
                            $flag => ['Choose true or false.'],
                        ]);
                    }
                    $data[$flag] = $body[$flag];
                }
            }
            if (array_key_exists('closes_at', $body)) {
                if ($body['closes_at'] !== null && (!is_string($body['closes_at']) || strtotime($body['closes_at']) === false)) {
                    errorResponse('VALIDATION_FAILED', 'The poll closing date is invalid.', 422);
                }
                $data['closes_at'] = $body['closes_at'];
            }
        } else {
            foreach (['start_at', 'end_at', 'location', 'capacity'] as $field) {
                if (array_key_exists($field, $body)) {
                    $data[$field] = $body[$field];
                }
            }
            foreach (['start_at', 'end_at'] as $field) {
                if (!isset($data[$field]) || !is_string($data[$field]) || strtotime($data[$field]) === false) {
                    errorResponse('VALIDATION_FAILED', 'Valid event start and end dates are required.', 422, [
                        $field => ['Provide a valid date and time.'],
                    ]);
                }
            }
            if (strtotime($data['end_at']) <= strtotime($data['start_at'])) {
                errorResponse('VALIDATION_FAILED', 'The event end must be after its start.', 422, [
                    'end_at' => ['Choose a time after the event start.'],
                ]);
            }
            if (isset($data['capacity']) && $data['capacity'] !== null
                && (!(is_int($data['capacity']) || (is_string($data['capacity']) && ctype_digit($data['capacity'])))
                    || (int) $data['capacity'] < 1)) {
                errorResponse('VALIDATION_FAILED', 'Event capacity must be a positive integer.', 422);
            }
            if (isset($data['location']) && (!is_string($data['location']) || mb_strlen($data['location']) > 255)) {
                errorResponse('VALIDATION_FAILED', 'The event location must be at most 255 characters.', 422);
            }
        }
        $updates['content_data'] = json_encode($data, JSON_THROW_ON_ERROR);
    }
    if ($updates !== []) {
        if ($contentType === 'poll') {
            communityTransaction($database, static function () use ($database, $groupId, $contentId, $updates): void {
                $lockSql = 'SELECT id FROM group_contents WHERE id = :id AND group_id = :group_id'
                    . ($database->type === 'sqlite' ? '' : ' FOR UPDATE');
                $lock = $database->pdo->prepare($lockSql);
                $lock->execute([':id' => $contentId, ':group_id' => $groupId]);
                if ($lock->fetchColumn() === false) {
                    failCommunity('NOT_FOUND', 'Poll not found.', 404);
                }
                if ((int) $database->count('group_poll_votes', ['poll_id' => $contentId]) > 0) {
                    failCommunity('POLL_ALREADY_VOTED', 'A poll cannot be edited after votes have been submitted.', 409);
                }
                $database->update('group_contents', $updates, [
                    'id' => $contentId,
                    'group_id' => $groupId,
                    'content_type' => 'poll',
                ]);
            });
        } else {
            $database->update('group_contents', $updates, [
                'id' => $contentId,
                'group_id' => $groupId,
                'content_type' => $contentType,
            ]);
        }
    }
    successResponse($publicContent($fetch($contentId)), 'Group content updated successfully.');
}

if ($method === 'DELETE') {
    $isCreator = ($content['created_by_user_type'] ?? null) === ($jwt['user_type'] ?? null)
        && (int) ($content['created_by_user_id'] ?? 0) === ($memberId > 0 ? $memberId : $actorId)
        && ($memberId === 0 || isActiveGroupMember($database, $groupId, $memberId));
    if (!$isManager && !$isCreator) {
        errorResponse('UNAUTHORIZED', 'You do not have permission to archive this content.', 403);
    }
    $database->update('group_contents', ['status' => 'archived'], [
        'id' => $contentId,
        'group_id' => $groupId,
        'content_type' => $contentType,
    ]);
    successResponse(null, 'Group content archived successfully.');
}

errorResponse('METHOD_NOT_ALLOWED', 'The requested operation is not supported.', 405);
