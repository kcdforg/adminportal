<?php

declare(strict_types=1);

use App\Middleware\CorsMiddleware;
use App\Middleware\JwtAuthMiddleware;
use App\Middleware\RequireAccountsAdminMiddleware;
use App\Middleware\RequireAdminMiddleware;
use App\Middleware\RequireElevatedAdminMiddleware;
use App\Middleware\RequireSuperAdminMiddleware;
use App\Modules\Auth\Repositories\ProfileRepository;
use App\Modules\Auth\Repositories\UserLoginRepository;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Community\Policies\InvitationPolicy;
use App\Modules\Community\Repositories\InvitationRepository;
use App\Modules\Community\Services\InvitationService;
use App\Modules\Community\Validators\InvitationValidator;
use App\Core\ActivityLogService;
use Illuminate\Database\Capsule\Manager as Capsule;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;

return [

    // PSR Response Factory
    ResponseFactoryInterface::class => function () {
        return new ResponseFactory();
    },

    // CORS Middleware
    CorsMiddleware::class => function ($container) {
        return new CorsMiddleware(
            $container->get(ResponseFactoryInterface::class),
            $container->get('config')
        );
    },

    // JWT Auth Middleware
    JwtAuthMiddleware::class => function ($container) {
        return new JwtAuthMiddleware(
            $container->get(ResponseFactoryInterface::class),
            $container->get('config'),
            $container->get(LoggerInterface::class)
        );
    },

    // Prefer typed admin middleware subclasses on routes — do not resolve RoleMiddleware::class bare.
    RequireAdminMiddleware::class => function ($container) {
        return new RequireAdminMiddleware(
            $container->get(ResponseFactoryInterface::class)
        );
    },

    RequireElevatedAdminMiddleware::class => function ($container) {
        return new RequireElevatedAdminMiddleware(
            $container->get(ResponseFactoryInterface::class)
        );
    },

    RequireSuperAdminMiddleware::class => function ($container) {
        return new RequireSuperAdminMiddleware(
            $container->get(ResponseFactoryInterface::class)
        );
    },

    RequireAccountsAdminMiddleware::class => function ($container) {
        return new RequireAccountsAdminMiddleware(
            $container->get(ResponseFactoryInterface::class)
        );
    },

    // Database — Eloquent via Capsule
    Capsule::class => function () {
        $capsule = new Capsule();
        $capsule->addConnection(require __DIR__ . '/database.php');
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        return $capsule;
    },

    // Logger
    LoggerInterface::class => function () {
        $logger = new Logger('kcdf');
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../storage/logs/app.log', Logger::DEBUG));
        return $logger;
    },

    AuthService::class => function ($container) {
        return new AuthService(
            $container->get(UserLoginRepository::class),
            $container->get(ProfileRepository::class),
            $container->get('config')
        );
    },

    InvitationService::class => function ($container) {
        return new InvitationService(
            $container->get(InvitationRepository::class),
            $container->get(ProfileRepository::class),
            $container->get(InvitationPolicy::class),
            $container->get(InvitationValidator::class),
            $container->get(ActivityLogService::class),
            $container->get(AuthService::class)
        );
    },

    // App config
    'config' => require __DIR__ . '/app.php',

];
