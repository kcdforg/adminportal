<?php

declare(strict_types=1);

use App\Middleware\JwtAuthMiddleware;
use App\Middleware\RequireAccountsAdminMiddleware;
use App\Modules\Payments\Controllers\PaymentController;

return function ($group) {

    // ----------------------------------------------------------------
    // Payments
    // ----------------------------------------------------------------
    $group->group('/payments', function ($payments) {

        $payments->get('', [PaymentController::class, 'index'])
            ->add(RequireAccountsAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $payments->post('', [PaymentController::class, 'store'])
            ->add(RequireAccountsAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $payments->get('/{id:[0-9]+}', [PaymentController::class, 'show'])
            ->add(JwtAuthMiddleware::class);

        $payments->patch('/{id:[0-9]+}', [PaymentController::class, 'update'])
            ->add(RequireAccountsAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

    });

    // ----------------------------------------------------------------
    // Family payments sub-resource
    // ----------------------------------------------------------------
    $group->get('/families/{id:[0-9]+}/payments', [PaymentController::class, 'familyPayments'])
        ->add(JwtAuthMiddleware::class);

};
