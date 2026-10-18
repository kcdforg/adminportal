<?php

declare(strict_types=1);

use Slim\App;

return function (App $app) {

    $app->group('/api/v1', function ($group) {

        // Auth routes
        (require __DIR__ . '/../src/Modules/Auth/routes.php')($group);

        // Families routes
        (require __DIR__ . '/../src/Modules/Families/routes.php')($group);

        // Academics routes
        (require __DIR__ . '/../src/Modules/Academics/routes.php')($group);

        // Payments routes
        (require __DIR__ . '/../src/Modules/Payments/routes.php')($group);

        // Community routes
        (require __DIR__ . '/../src/Modules/Community/routes.php')($group);

        // Notifications routes
        (require __DIR__ . '/../src/Modules/Notifications/routes.php')($group);

    });

};
