<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Controllers;

use App\Core\BaseController;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Notifications\Services\NotificationService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NotificationController extends BaseController
{
    public function __construct(private readonly NotificationService $notificationService) {}

    /**
     * @OA\Get(path="/api/v1/notifications", operationId="listNotifications", tags={"Notifications"}, summary="List notifications", security={{"bearerAuth":{}}}, @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")), @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")), @OA\Response(response=200, description="Notifications retrieved", @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function index(Request $request, Response $response): Response
    {
        $jwt     = $request->getAttribute('jwt_payload', []);
        $filters = $request->getQueryParams();

        $result = $this->notificationService->listForMember($filters, $jwt);

        return $this->paginate($response, $result['data'], $result['meta']);
    }

    /**
     * @OA\Patch(path="/api/v1/notifications/{id}", operationId="markNotificationRead", tags={"Notifications"}, summary="Mark notification as read", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\Response(response=200, description="Notification marked as read", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="message", type="string"), @OA\Property(property="data", type="object"))), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Notification not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function markRead(Request $request, Response $response, array $args): Response
    {
        $jwt = $request->getAttribute('jwt_payload', []);
        $id  = (int) $args['id'];

        try {
            $notification = $this->notificationService->markAsRead($id, $jwt);
            return $this->success($response, $notification->toArray(), 'Notification marked as read.');
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        }
    }

    /**
     * @OA\Post(path="/api/v1/notifications/mark-all-read", operationId="markAllNotificationsRead", tags={"Notifications"}, summary="Mark all notifications as read", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="All notifications marked as read", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="message", type="string"), @OA\Property(property="data", type="object", @OA\Property(property="updated", type="integer")))), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function markAllRead(Request $request, Response $response): Response
    {
        $jwt   = $request->getAttribute('jwt_payload', []);
        $count = $this->notificationService->markAllRead($jwt);

        return $this->success($response, ['updated' => $count], 'All notifications marked as read.');
    }

    /**
     * @OA\Post(path="/api/v1/notifications/{id}/archive", operationId="archiveNotification", tags={"Notifications"}, summary="Archive notification", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\Response(response=200, description="Notification archived", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Notification not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function archive(Request $request, Response $response, array $args): Response
    {
        $jwt = $request->getAttribute('jwt_payload', []);
        $id  = (int) $args['id'];

        try {
            $notification = $this->notificationService->archive($id, $jwt);
            return $this->success($response, $notification->toArray(), 'Notification archived.');
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        }
    }

    public function send(Request $request, Response $response): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $data = (array) $request->getParsedBody();

        try {
            $count = $this->notificationService->send($data, $jwt);
            return $this->success($response, ['sent_to' => $count], 'Notifications sent successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    public function broadcast(Request $request, Response $response): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $data = (array) $request->getParsedBody();

        try {
            $count = $this->notificationService->broadcast($data, $jwt);
            return $this->success($response, ['sent_to' => $count], 'Notification broadcast successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }
}
