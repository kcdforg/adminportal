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

    public function index(Request $request, Response $response): Response
    {
        $jwt     = $request->getAttribute('jwt_payload', []);
        $filters = $request->getQueryParams();

        $result = $this->notificationService->listForMember($filters, $jwt);

        return $this->paginate($response, $result['data'], $result['meta']);
    }

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

    public function markAllRead(Request $request, Response $response): Response
    {
        $jwt   = $request->getAttribute('jwt_payload', []);
        $count = $this->notificationService->markAllRead($jwt);

        return $this->success($response, ['updated' => $count], 'All notifications marked as read.');
    }

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
