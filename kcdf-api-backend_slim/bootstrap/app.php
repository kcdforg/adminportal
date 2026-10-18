<?php

declare(strict_types=1);

use App\Middleware\CorsMiddleware;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;

return function (App $app) {

    // Slim middleware is LIFO: last added runs first (outermost).
    // Effective order (outer → inner): Error → CORS → Routing → BodyParsing → route.
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $app->add(CorsMiddleware::class);

    $displayErrors = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $app->addErrorMiddleware($displayErrors, true, true);

    // Bootstrap Eloquent by resolving Capsule from container
    $app->getContainer()->get(\Illuminate\Database\Capsule\Manager::class);

};
