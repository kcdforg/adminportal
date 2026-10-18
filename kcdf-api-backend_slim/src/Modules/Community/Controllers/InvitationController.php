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

    /**
     * @OA\Get(path="/api/v1/invitations", operationId="listInvitations", tags={"Invitations"}, summary="List invitations", security={{"bearerAuth":{}}}, @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")), @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")), @OA\Response(response=200, description="Invitations retrieved", @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function index(Request $request, Response $response): Response
    {
        $jwt     = $request->getAttribute('jwt_payload', []);
        $filters = $request->getQueryParams();

        $result = $this->invitationService->list($filters, $jwt);

        return $this->paginate($response, $result['data'], $result['meta']);
    }

    /**
     * @OA\Post(path="/api/v1/invitations", operationId="createInvitation", tags={"Invitations"}, summary="Send invitation", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Invitation sent", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="data", type="object"))), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=409, description="Invitation already exists", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
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

    /**
     * @OA\Get(path="/api/v1/invitations/{code}", operationId="getInvitationByCode", tags={"Invitations"}, summary="Get invitation by code", description="Retrieve invitation details using an invitation code.",  @OA\Parameter(name="code", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="Invitation retrieved", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")), @OA\Response(response=404, description="Invitation not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Invalid invitation code", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
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

    /**
     * @OA\Post(path="/api/v1/invitations/{code}/accept", operationId="acceptInvitation", tags={"Invitations"}, summary="Accept invitation", @OA\Parameter(name="code", in="path", required=true, @OA\Schema(type="string")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Invitation accepted", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")), @OA\Response(response=404, description="Invitation not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=409, description="Account already exists", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Invalid invitation code or validation failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
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

    /**
     * @OA\Delete(path="/api/v1/invitations/{id}", operationId="cancelInvitation", tags={"Invitations"}, summary="Cancel invitation", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\Response(response=200, description="Invitation cancelled", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Invitation not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Cannot cancel invitation", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
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
