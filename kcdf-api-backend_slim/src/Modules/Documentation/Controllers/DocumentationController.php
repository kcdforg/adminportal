<?php

declare(strict_types=1);

namespace App\Modules\Documentation\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DocumentationController
{
    public function swagger(Request $request, Response $response): Response
    {
        $filePath = __DIR__ . '/../../../../public/swagger/swagger-ui.html';
        
        if (!file_exists($filePath)) {
            $response->getBody()->write(json_encode([
                'error' => 'Documentation not available. Run: composer docs',
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(503);
        }

        $html = file_get_contents($filePath);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }
}
