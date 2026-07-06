<?php

declare(strict_types=1);

use App\Middleware\JwtAuthMiddleware;
use App\Middleware\RequireAdminMiddleware;
use App\Modules\Payments\Controllers\PaymentController;

return function ($group) {

    // ----------------------------------------------------------------
    // Payments
    // ----------------------------------------------------------------
    $group->group('/payments', function ($payments) {

        // GET /api/v1/payments — admin (accounts, super_admin) only; policy enforces accounts/super subset
        $payments->get('', [PaymentController::class, 'index'])
            ->add(RequireAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        // POST /api/v1/payments — admin (accounts, super_admin) only
        $payments->post('', [PaymentController::class, 'store'])
            ->add(RequireAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        // GET /api/v1/payments/{id} — admin or primary family member; policy checks inside
        $payments->get('/{id:[0-9]+}', [PaymentController::class, 'show'])
            ->add(JwtAuthMiddleware::class);

        // PATCH /api/v1/payments/{id} — admin only; policy enforces accounts/super subset
        $payments->patch('/{id:[0-9]+}', [PaymentController::class, 'update'])
            ->add(RequireAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

    });

    // ----------------------------------------------------------------
    // Family payments sub-resource
    // ----------------------------------------------------------------
    $group->get('/families/{id:[0-9]+}/payments', [PaymentController::class, 'familyPayments'])
        ->add(JwtAuthMiddleware::class);

};
