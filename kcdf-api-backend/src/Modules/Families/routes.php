<?php

declare(strict_types=1);

use App\Middleware\JwtAuthMiddleware;
use App\Middleware\RequireAdminMiddleware;
use App\Middleware\RequireElevatedAdminMiddleware;
use App\Middleware\RequireSuperAdminMiddleware;
use App\Modules\Families\Controllers\AdminController;
use App\Modules\Families\Controllers\EntityController;
use App\Modules\Families\Controllers\FamilyController;
use App\Modules\Families\Controllers\FamilyMemberController;
use App\Modules\Families\Controllers\MemberController;
use App\Modules\Families\Controllers\TrainerController;

return function ($group) {

    // ----------------------------------------------------------------
    // Members (Profiles)
    // Admin-only list and create; show/update use fine-grained policy
    // ----------------------------------------------------------------
    $group->group('/members', function ($members) {

        $members->get('', [MemberController::class, 'index'])
            ->add(RequireAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $members->post('', [MemberController::class, 'store'])
            ->add(RequireAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $members->get('/{id:[0-9]+}', [MemberController::class, 'show'])
            ->add(JwtAuthMiddleware::class);

        $members->put('/{id:[0-9]+}', [MemberController::class, 'update'])
            ->add(JwtAuthMiddleware::class);

        // Entity relations nested under members
        $members->get('/{id:[0-9]+}/entity-relations', [EntityController::class, 'listRelations'])
            ->add(JwtAuthMiddleware::class);

        $members->post('/{id:[0-9]+}/entity-relations', [EntityController::class, 'storeRelation'])
            ->add(JwtAuthMiddleware::class);

        $members->delete('/{id:[0-9]+}/entity-relations/{relation_id:[0-9]+}', [EntityController::class, 'destroyRelation'])
            ->add(JwtAuthMiddleware::class);

    });

    // ----------------------------------------------------------------
    // Families
    // ----------------------------------------------------------------
    $group->group('/families', function ($families) {

        $families->get('', [FamilyController::class, 'index'])
            ->add(RequireAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $families->post('', [FamilyController::class, 'store'])
            ->add(RequireAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $families->get('/{id:[0-9]+}', [FamilyController::class, 'show'])
            ->add(JwtAuthMiddleware::class);

        $families->put('/{id:[0-9]+}', [FamilyController::class, 'update'])
            ->add(JwtAuthMiddleware::class);

        $families->get('/{id:[0-9]+}/members', [FamilyMemberController::class, 'index'])
            ->add(JwtAuthMiddleware::class);

        $families->post('/{id:[0-9]+}/members', [FamilyMemberController::class, 'store'])
            ->add(JwtAuthMiddleware::class);

        $families->delete('/{id:[0-9]+}/members/{profile_id:[0-9]+}', [FamilyMemberController::class, 'destroy'])
            ->add(JwtAuthMiddleware::class);

    });

    // ----------------------------------------------------------------
    // Trainers
    // ----------------------------------------------------------------
    $group->group('/trainers', function ($trainers) {

        $trainers->get('', [TrainerController::class, 'index'])
            ->add(RequireAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $trainers->post('', [TrainerController::class, 'store'])
            ->add(RequireElevatedAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $trainers->get('/{id:[0-9]+}', [TrainerController::class, 'show'])
            ->add(JwtAuthMiddleware::class);

        $trainers->put('/{id:[0-9]+}', [TrainerController::class, 'update'])
            ->add(JwtAuthMiddleware::class);

    });

    // ----------------------------------------------------------------
    // Admins — super_admin only
    // ----------------------------------------------------------------
    $group->group('/admins', function ($admins) {

        $admins->get('', [AdminController::class, 'index'])
            ->add(RequireSuperAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $admins->post('', [AdminController::class, 'store'])
            ->add(RequireSuperAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $admins->get('/{id:[0-9]+}', [AdminController::class, 'show'])
            ->add(RequireSuperAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $admins->put('/{id:[0-9]+}', [AdminController::class, 'update'])
            ->add(RequireSuperAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

    });

    // ----------------------------------------------------------------
    // Entities
    // ----------------------------------------------------------------
    $group->group('/entities', function ($entities) {

        $entities->get('', [EntityController::class, 'index'])
            ->add(JwtAuthMiddleware::class);

        $entities->post('', [EntityController::class, 'store'])
            ->add(RequireElevatedAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

        $entities->get('/{id:[0-9]+}', [EntityController::class, 'show'])
            ->add(JwtAuthMiddleware::class);

        $entities->put('/{id:[0-9]+}', [EntityController::class, 'update'])
            ->add(RequireAdminMiddleware::class)
            ->add(JwtAuthMiddleware::class);

    });

};
