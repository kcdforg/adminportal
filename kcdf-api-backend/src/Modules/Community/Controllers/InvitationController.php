<?php

declare(strict_types=1);

namespace App\Modules\Community\Controllers;

use App\Core\BaseController;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Exceptions\DuplicateException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Community\Services\InvitationService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class InvitationController extends BaseController
{
    public function __construct(private readonly InvitationService $invitationService) {}

    public function index(Request $request, Response $response): Response
    {
        $jwt     = $request->getAttribute('jwt_payload', []);
        $filters = $request->getQueryParams();

        $result = $this->invitationService->list($filters, $jwt);

        return $this->paginate($response, $result['data'], $result['meta']);
    }

    public function store(Request $request, Response $response): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $data = (array) $request->getParsedBody();

        try {
            $invitation = $this->invitationService->create($data, $jwt);
            return $this->created($response, $invitation->toArray(), 'Invitation sent successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        } catch (DuplicateException $e) {
            return $this->error($response, 'DUPLICATE_INVITATION', $e->getMessage(), [], 409);
        }
    }

    public function showByCode(Request $request, Response $response, array $args): Response
    {
        $code = (string) $args['code'];

        try {
            $data = $this->invitationService->showByCode($code);
            return $this->success($response, $data);
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (BusinessRuleException $e) {
            return $this->error($response, $e->getErrorCode(), $e->getMessage(), [], 422);
        }
    }

    public function accept(Request $request, Response $response, array $args): Response
    {
        $code = (string) $args['code'];
        $data = (array) $request->getParsedBody();

        try {
            $tokens = $this->invitationService->accept($code, $data);
            return $this->success($response, $tokens, 'Invitation accepted. Account created successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (BusinessRuleException $e) {
            return $this->error($response, $e->getErrorCode(), $e->getMessage(), [], 422);
        } catch (DuplicateException $e) {
            return $this->error($response, 'ACCOUNT_EXISTS', $e->getMessage(), [], 409);
        }
    }

    public function cancel(Request $request, Response $response, array $args): Response
    {
        $jwt = $request->getAttribute('jwt_payload', []);
        $id  = (int) $args['id'];

        try {
            $this->invitationService->cancel($id, $jwt);
            return $this->success($response, null, 'Invitation cancelled successfully.');
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        } catch (BusinessRuleException $e) {
            return $this->error($response, $e->getErrorCode(), $e->getMessage(), [], 422);
        }
    }
}
