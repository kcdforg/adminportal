<?php

declare(strict_types=1);

use Slim\App;

return function (App $app) {
    
    // Swagger UI HTML page
    $app->get('/swagger', 'App\Modules\Documentation\Controllers\DocumentationController:swagger')
        ->setName('swagger.ui');
    
    // Swagger OpenAPI JSON specification
    $app->get('/swagger/swagger.json', function ($request, $response) {
        $filePath = __DIR__ . '/../public/swagger/swagger.json';
        
        if (!file_exists($filePath)) {
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode(['error' => 'Swagger documentation not generated. Run: composer docs']));
        }
        
        $json = file_get_contents($filePath);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write($json);
    });
};
