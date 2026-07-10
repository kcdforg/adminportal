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

    /**
     * @OA\Get(path="/api/v1/activity-logs", operationId="listActivityLogs", tags={"Activity Logs"}, summary="List activity logs", security={{"bearerAuth":{}}}, @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")), @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")), @OA\Response(response=200, description="Activity logs retrieved", @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
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
