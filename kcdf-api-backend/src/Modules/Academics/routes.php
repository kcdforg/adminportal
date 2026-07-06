<?php

declare(strict_types=1);

use App\Middleware\JwtAuthMiddleware;
use App\Middleware\RequireElevatedAdminMiddleware;
use App\Modules\Academics\Controllers\AttendanceController;
use App\Modules\Academics\Controllers\BatchController;
use App\Modules\Academics\Controllers\EnrollmentController;
use App\Modules\Academics\Controllers\ProgramController;
use App\Modules\Academics\Controllers\SessionController;

return function ($group) {

    // ----------------------------------------------------------------
    // Programs
    // ----------------------------------------------------------------
    $group->group('/programs', function ($programs) {

        $programs->get('', [ProgramController::class, 'index'])
            ->add(JwtAuthMiddleware::class);

        $programs->post('', [ProgramController::class, 'store'])
            ->add(RequireElevatedAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $programs->get('/{id:[0-9]+}', [ProgramController::class, 'show'])
            ->add(JwtAuthMiddleware::class);

        $programs->put('/{id:[0-9]+}', [ProgramController::class, 'update'])
            ->add(RequireElevatedAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $programs->patch('/{id:[0-9]+}/status', [ProgramController::class, 'updateStatus'])
            ->add(RequireElevatedAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

    });

    // ----------------------------------------------------------------
    // Batches
    // ----------------------------------------------------------------
    $group->group('/batches', function ($batches) {

        $batches->get('', [BatchController::class, 'index'])
            ->add(JwtAuthMiddleware::class);

        $batches->post('', [BatchController::class, 'store'])
            ->add(RequireElevatedAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $batches->get('/{id:[0-9]+}', [BatchController::class, 'show'])
            ->add(JwtAuthMiddleware::class);

        $batches->put('/{id:[0-9]+}', [BatchController::class, 'update'])
            ->add(RequireElevatedAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $batches->get('/{id:[0-9]+}/members', [BatchController::class, 'members'])
            ->add(JwtAuthMiddleware::class);

        $batches->get('/{id:[0-9]+}/sessions', [BatchController::class, 'sessions'])
            ->add(JwtAuthMiddleware::class);

        $batches->post('/{id:[0-9]+}/sessions', [BatchController::class, 'storeSession'])
            ->add(RequireElevatedAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

    });

    // ----------------------------------------------------------------
    // Sessions
    // ----------------------------------------------------------------
    $group->group('/sessions', function ($sessions) {

        $sessions->get('/{id:[0-9]+}', [SessionController::class, 'show'])
            ->add(JwtAuthMiddleware::class);

        $sessions->put('/{id:[0-9]+}', [SessionController::class, 'update'])
            ->add(JwtAuthMiddleware::class);

        $sessions->post('/{id:[0-9]+}/lock', [SessionController::class, 'lock'])
            ->add(RequireElevatedAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $sessions->get('/{id:[0-9]+}/attendance', [SessionController::class, 'attendance'])
            ->add(JwtAuthMiddleware::class);

        $sessions->post('/{id:[0-9]+}/attendance', [SessionController::class, 'storeAttendance'])
            ->add(JwtAuthMiddleware::class);

    });

    // ----------------------------------------------------------------
    // Attendance (individual record patch)
    // ----------------------------------------------------------------
    $group->group('/attendance', function ($attendance) {

        $attendance->patch('/{id:[0-9]+}', [AttendanceController::class, 'patch'])
            ->add(JwtAuthMiddleware::class);

    });

    // ----------------------------------------------------------------
    // Enrollments
    // ----------------------------------------------------------------
    $group->group('/enrollments', function ($enrollments) {

        $enrollments->get('', [EnrollmentController::class, 'index'])
            ->add(JwtAuthMiddleware::class);

        $enrollments->post('', [EnrollmentController::class, 'store'])
            ->add(JwtAuthMiddleware::class);

        $enrollments->get('/{id:[0-9]+}', [EnrollmentController::class, 'show'])
            ->add(JwtAuthMiddleware::class);

        $enrollments->patch('/{id:[0-9]+}/cancel', [EnrollmentController::class, 'cancel'])
            ->add(JwtAuthMiddleware::class);

    });

};
