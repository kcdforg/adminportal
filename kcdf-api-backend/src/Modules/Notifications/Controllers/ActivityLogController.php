<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Controllers;

use App\Core\BaseController;
use App\Core\Exceptions\UnauthorizedException;
use App\Modules\Notifications\Services\ActivityLogService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ActivityLogController extends BaseController
{
    public function __construct(private readonly ActivityLogService $activityLogService) {}

    public function index(Request $request, Response $response): Response
    {
        $jwt     = $request->getAttribute('jwt_payload', []);
        $filters = $request->getQueryParams();

        try {
            $result = $this->activityLogService->list($filters, $jwt);
            return $this->paginate($response, $result['data'], $result['meta']);
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }
}
