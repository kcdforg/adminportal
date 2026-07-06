<?php

declare(strict_types=1);

use App\Middleware\JwtAuthMiddleware;
use App\Middleware\RequireAdminMiddleware;
use App\Modules\Community\Controllers\GroupController;
use App\Modules\Community\Controllers\InvitationController;

return function ($group) {

    // ----------------------------------------------------------------
    // Groups
    // ----------------------------------------------------------------
    $group->group('/groups', function ($groups) {

        // GET /api/v1/groups — authenticated (admin sees all, parent sees public + own)
        $groups->get('', [GroupController::class, 'index'])
            ->add(JwtAuthMiddleware::class);

        // POST /api/v1/groups — admin only
        $groups->post('', [GroupController::class, 'store'])
            ->add(RequireAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        // GET /api/v1/groups/{id}
        $groups->get('/{id:[0-9]+}', [GroupController::class, 'show'])
            ->add(JwtAuthMiddleware::class);

        // PUT /api/v1/groups/{id} — admin only
        $groups->put('/{id:[0-9]+}', [GroupController::class, 'update'])
            ->add(RequireAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        // GET /api/v1/groups/{id}/members — admin or group member
        $groups->get('/{id:[0-9]+}/members', [GroupController::class, 'members'])
            ->add(JwtAuthMiddleware::class);

        // POST /api/v1/groups/{id}/join — authenticated parent (public groups only)
        $groups->post('/{id:[0-9]+}/join', [GroupController::class, 'join'])
            ->add(JwtAuthMiddleware::class);

        // DELETE /api/v1/groups/{id}/leave — authenticated member
        $groups->delete('/{id:[0-9]+}/leave', [GroupController::class, 'leave'])
            ->add(JwtAuthMiddleware::class);

        // DELETE /api/v1/groups/{id}/members/{member_id} — admin only (ban or remove)
        $groups->delete('/{id:[0-9]+}/members/{member_id:[0-9]+}', [GroupController::class, 'removeMember'])
            ->add(RequireAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

    });

    // ----------------------------------------------------------------
    // Invitations
    // ----------------------------------------------------------------
    $group->group('/invitations', function ($invitations) {

        // GET /api/v1/invitations — authenticated (own or all for admin)
        $invitations->get('', [InvitationController::class, 'index'])
            ->add(JwtAuthMiddleware::class);

        // POST /api/v1/invitations — any authenticated parent
        $invitations->post('', [InvitationController::class, 'store'])
            ->add(JwtAuthMiddleware::class);

        // DELETE /api/v1/invitations/{id} — sender or admin (cancel pending)
        $invitations->delete('/{id:[0-9]+}', [InvitationController::class, 'cancel'])
            ->add(JwtAuthMiddleware::class);

        // GET /api/v1/invitations/{code} — public (for accept flow UI)
        $invitations->get('/{code}', [InvitationController::class, 'showByCode']);

        // POST /api/v1/invitations/{code}/accept — public (registration via invite)
        $invitations->post('/{code}/accept', [InvitationController::class, 'accept']);

    });

};
