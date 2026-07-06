<?php

declare(strict_types=1);

use App\Middleware\JwtAuthMiddleware;
use App\Modules\Auth\Controllers\AuthController;

return function ($group) {

    $group->group('/auth', function ($auth) {

        $auth->post('/login', [AuthController::class, 'login']);
        $auth->post('/refresh', [AuthController::class, 'refresh']);

        // Protected routes
        $auth->post('/logout', [AuthController::class, 'logout'])
            ->add(JwtAuthMiddleware::class);

        $auth->get('/me', [AuthController::class, 'me'])
            ->add(JwtAuthMiddleware::class);

    });

};
