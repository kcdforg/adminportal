<?php

declare(strict_types=1);

use App\Middleware\JwtAuthMiddleware;
use App\Middleware\RequireAdminMiddleware;
use App\Middleware\RequireSuperAdminMiddleware;
use App\Modules\Notifications\Controllers\ActivityLogController;
use App\Modules\Notifications\Controllers\NotificationController;

return function ($group) {

    // ----------------------------------------------------------------
    // Notifications
    // ----------------------------------------------------------------
    $group->group('/notifications', function ($notifications) {

        // GET /api/v1/notifications — own notifications, authenticated user
        $notifications->get('', [NotificationController::class, 'index'])
            ->add(JwtAuthMiddleware::class);

        // POST /api/v1/notifications/read-all — mark all unread as read
        $notifications->post('/read-all', [NotificationController::class, 'markAllRead'])
            ->add(JwtAuthMiddleware::class);

        // POST /api/v1/notifications/send — admin only
        $notifications->post('/send', [NotificationController::class, 'send'])
            ->add(RequireAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        // POST /api/v1/notifications/broadcast — admin only
        $notifications->post('/broadcast', [NotificationController::class, 'broadcast'])
            ->add(RequireAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        // PATCH /api/v1/notifications/{id}/read — own notifications only
        $notifications->patch('/{id:[0-9]+}/read', [NotificationController::class, 'markRead'])
            ->add(JwtAuthMiddleware::class);

        // PATCH /api/v1/notifications/{id}/archive — own notifications only
        $notifications->patch('/{id:[0-9]+}/archive', [NotificationController::class, 'archive'])
            ->add(JwtAuthMiddleware::class);

    });

    // ----------------------------------------------------------------
    // Activity Logs
    // ----------------------------------------------------------------

    // GET /api/v1/activity-logs — super_admin only
    $group->get('/activity-logs', [ActivityLogController::class, 'index'])
        ->add(RequireSuperAdminMiddleware::class)
        ->add(JwtAuthMiddleware::class);

};
