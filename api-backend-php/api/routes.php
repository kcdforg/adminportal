<?php

declare(strict_types=1);

use FastRoute\RouteCollector;

return function (RouteCollector $routeCollector): void {
    $routeCollector->addRoute('POST', '/api/v1/auth/login', 'auth/login.php');
    $routeCollector->addRoute('POST', '/api/v1/auth/refresh', 'auth/refresh.php');
    $routeCollector->addRoute('POST', '/api/v1/auth/logout', 'auth/logout.php');
    $routeCollector->addRoute('GET', '/api/v1/auth/me', 'auth/me.php');

    $routeCollector->addRoute('GET', '/api/v1/programs', 'programs/index.php');
    $routeCollector->addRoute('POST', '/api/v1/programs', 'programs/store.php');
    $routeCollector->addRoute('GET', '/api/v1/programs/{id:[0-9]+}', 'programs/show.php');
    $routeCollector->addRoute('PUT', '/api/v1/programs/{id:[0-9]+}', 'programs/update.php');
    $routeCollector->addRoute('PATCH', '/api/v1/programs/{id:[0-9]+}/status', 'programs/status.php');

    $routeCollector->addRoute('GET', '/api/v1/batches', 'batches/index.php');
    $routeCollector->addRoute('POST', '/api/v1/batches', 'batches/store.php');
    $routeCollector->addRoute('GET', '/api/v1/batches/{id:[0-9]+}', 'batches/show.php');
    $routeCollector->addRoute('PUT', '/api/v1/batches/{id:[0-9]+}', 'batches/update.php');
    $routeCollector->addRoute('GET', '/api/v1/batches/{id:[0-9]+}/members', 'batches/members.php');
    $routeCollector->addRoute('GET', '/api/v1/batches/{id:[0-9]+}/sessions', 'batches/sessions.php');
    $routeCollector->addRoute('POST', '/api/v1/batches/{id:[0-9]+}/sessions', 'batches/session-store.php');

    $routeCollector->addRoute('GET', '/api/v1/sessions/{id:[0-9]+}', 'sessions/show.php');
    $routeCollector->addRoute('PUT', '/api/v1/sessions/{id:[0-9]+}', 'sessions/update.php');
    $routeCollector->addRoute('POST', '/api/v1/sessions/{id:[0-9]+}/lock', 'sessions/lock.php');
    $routeCollector->addRoute('GET', '/api/v1/sessions/{id:[0-9]+}/attendance', 'sessions/attendance.php');
    $routeCollector->addRoute('POST', '/api/v1/sessions/{id:[0-9]+}/attendance', 'sessions/attendance-store.php');

    $routeCollector->addRoute('PATCH', '/api/v1/attendance/{id:[0-9]+}', 'attendance/patch.php');

    $routeCollector->addRoute('GET', '/api/v1/enrollments', 'enrollments/index.php');
    $routeCollector->addRoute('POST', '/api/v1/enrollments', 'enrollments/store.php');
    $routeCollector->addRoute('GET', '/api/v1/enrollments/{id:[0-9]+}', 'enrollments/show.php');
    $routeCollector->addRoute('PATCH', '/api/v1/enrollments/{id:[0-9]+}/cancel', 'enrollments/cancel.php');

    $routeCollector->addRoute('GET', '/api/v1/payments', 'payments/index.php');
    $routeCollector->addRoute('POST', '/api/v1/payments', 'payments/store.php');
    $routeCollector->addRoute('GET', '/api/v1/payments/{id:[0-9]+}', 'payments/show.php');
    $routeCollector->addRoute('PATCH', '/api/v1/payments/{id:[0-9]+}', 'payments/update.php');
    $routeCollector->addRoute('GET', '/api/v1/families/{id:[0-9]+}/payments', 'payments/family-payments.php');

    $routeCollector->addRoute('GET', '/api/v1/groups', 'groups/index.php');
    $routeCollector->addRoute('POST', '/api/v1/groups', 'groups/store.php');
    $routeCollector->addRoute('GET', '/api/v1/groups/{id:[0-9]+}', 'groups/show.php');
    $routeCollector->addRoute('PUT', '/api/v1/groups/{id:[0-9]+}', 'groups/update.php');
    $routeCollector->addRoute('GET', '/api/v1/groups/{id:[0-9]+}/members', 'groups/members.php');
    $routeCollector->addRoute('POST', '/api/v1/groups/{id:[0-9]+}/join', 'groups/join.php');
    $routeCollector->addRoute('DELETE', '/api/v1/groups/{id:[0-9]+}/leave', 'groups/leave.php');
    $routeCollector->addRoute('DELETE', '/api/v1/groups/{id:[0-9]+}/members/{member_id:[0-9]+}', 'groups/remove-member.php');

    $routeCollector->addRoute('GET', '/api/v1/invitations', 'invitations/index.php');
    $routeCollector->addRoute('POST', '/api/v1/invitations', 'invitations/store.php');
    $routeCollector->addRoute('DELETE', '/api/v1/invitations/{id:[0-9]+}', 'invitations/cancel.php');
    $routeCollector->addRoute('GET', '/api/v1/invitations/{code}', 'invitations/show-by-code.php');
    $routeCollector->addRoute('POST', '/api/v1/invitations/{code}/accept', 'invitations/accept.php');

    $routeCollector->addRoute('GET', '/api/v1/members', 'members/index.php');
    $routeCollector->addRoute('POST', '/api/v1/members', 'members/store.php');
    $routeCollector->addRoute('GET', '/api/v1/members/{id:[0-9]+}', 'members/show.php');
    $routeCollector->addRoute('PUT', '/api/v1/members/{id:[0-9]+}', 'members/update.php');
    $routeCollector->addRoute('POST', '/api/v1/members/{id:[0-9]+}/login', 'members/login-store.php');
    $routeCollector->addRoute('GET', '/api/v1/members/{id:[0-9]+}/entity-relations', 'members/entity-relations.php');
    $routeCollector->addRoute('POST', '/api/v1/members/{id:[0-9]+}/entity-relations', 'members/entity-relation-store.php');
    $routeCollector->addRoute('DELETE', '/api/v1/members/{id:[0-9]+}/entity-relations/{relation_id:[0-9]+}', 'members/entity-relation-delete.php');

    $routeCollector->addRoute('GET', '/api/v1/families', 'families/index.php');
    $routeCollector->addRoute('POST', '/api/v1/families', 'families/store.php');
    $routeCollector->addRoute('GET', '/api/v1/families/{id:[0-9]+}', 'families/show.php');
    $routeCollector->addRoute('PUT', '/api/v1/families/{id:[0-9]+}', 'families/update.php');
    $routeCollector->addRoute('GET', '/api/v1/families/{id:[0-9]+}/members', 'families/members.php');
    $routeCollector->addRoute('POST', '/api/v1/families/{id:[0-9]+}/members', 'families/members-store.php');
    $routeCollector->addRoute('DELETE', '/api/v1/families/{id:[0-9]+}/members/{profile_id:[0-9]+}', 'families/members-destroy.php');

    $routeCollector->addRoute('GET', '/api/v1/trainers', 'trainers/index.php');
    $routeCollector->addRoute('POST', '/api/v1/trainers', 'trainers/store.php');
    $routeCollector->addRoute('GET', '/api/v1/trainers/{id:[0-9]+}', 'trainers/show.php');
    $routeCollector->addRoute('PUT', '/api/v1/trainers/{id:[0-9]+}', 'trainers/update.php');

    $routeCollector->addRoute('GET', '/api/v1/admins', 'admins/index.php');
    $routeCollector->addRoute('POST', '/api/v1/admins', 'admins/store.php');
    $routeCollector->addRoute('GET', '/api/v1/admins/{id:[0-9]+}', 'admins/show.php');
    $routeCollector->addRoute('PUT', '/api/v1/admins/{id:[0-9]+}', 'admins/update.php');

    $routeCollector->addRoute('GET', '/api/v1/entities', 'entities/index.php');
    $routeCollector->addRoute('POST', '/api/v1/entities', 'entities/store.php');
    $routeCollector->addRoute('GET', '/api/v1/entities/{id:[0-9]+}', 'entities/show.php');
    $routeCollector->addRoute('PUT', '/api/v1/entities/{id:[0-9]+}', 'entities/update.php');

    $routeCollector->addRoute('GET', '/api/v1/notifications', 'notifications/index.php');
    $routeCollector->addRoute('POST', '/api/v1/notifications/read-all', 'notifications/read-all.php');
    $routeCollector->addRoute('POST', '/api/v1/notifications/send', 'notifications/send.php');
    $routeCollector->addRoute('POST', '/api/v1/notifications/broadcast', 'notifications/broadcast.php');
    $routeCollector->addRoute('PATCH', '/api/v1/notifications/{id:[0-9]+}/read', 'notifications/mark-read.php');
    $routeCollector->addRoute('PATCH', '/api/v1/notifications/{id:[0-9]+}/archive', 'notifications/archive.php');

    $routeCollector->addRoute('GET', '/api/v1/activity-logs', 'activity-logs/index.php');
};
