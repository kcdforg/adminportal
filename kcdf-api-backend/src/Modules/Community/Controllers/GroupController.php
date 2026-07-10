<?php

declare(strict_types=1);

namespace App\Modules\Community\Controllers;

use App\Core\BaseController;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Exceptions\DuplicateException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Community\Services\GroupService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GroupController extends BaseController
{
    public function __construct(private readonly GroupService $groupService) {}

    /**
     * @OA\Get(path="/api/v1/groups", operationId="listGroups", tags={"Groups"}, summary="List groups", security={{"bearerAuth":{}}}, @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")), @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")), @OA\Response(response=200, description="Groups retrieved", @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function index(Request $request, Response $response): Response
    {
        $jwt     = $request->getAttribute('jwt_payload', []);
        $filters = $request->getQueryParams();

        $result = $this->groupService->list($filters, $jwt);

        return $this->paginate($response, $result['data'], $result['meta']);
    }

    /**
     * @OA\Post(path="/api/v1/groups", operationId="createGroup", tags={"Groups"}, summary="Create group", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Group created", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="data", type="object"))), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
    public function store(Request $request, Response $response): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $data = (array) $request->getParsedBody();

        try {
            $group = $this->groupService->create($data, $jwt);
            return $this->created($response, $group->toArray(), 'Group created successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    /**
     * @OA\Get(path="/api/v1/groups/{id}", operationId="getGroup", tags={"Groups"}, summary="Get group", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\Response(response=200, description="Group retrieved", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Group not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $jwt = $request->getAttribute('jwt_payload', []);
        $id  = (int) $args['id'];

        try {
            $group = $this->groupService->show($id, $jwt);
            return $this->success($response, $group->toArray());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    /**
     * @OA\Put(path="/api/v1/groups/{id}", operationId="updateGroup", tags={"Groups"}, summary="Update group", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Group updated", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Group not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $id   = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        try {
            $group = $this->groupService->update($id, $data, $jwt);
            return $this->success($response, $group->toArray(), 'Group updated successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    public function members(Request $request, Response $response, array $args): Response
    {
        $jwt = $request->getAttribute('jwt_payload', []);
        $id  = (int) $args['id'];

        try {
            $members = $this->groupService->listMembers($id, $jwt);
            return $this->success($response, $members);
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    public function join(Request $request, Response $response, array $args): Response
    {
        $jwt = $request->getAttribute('jwt_payload', []);
        $id  = (int) $args['id'];

        try {
            $member = $this->groupService->join($id, $jwt);
            return $this->created($response, $member->toArray(), 'Joined group successfully.');
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        } catch (BusinessRuleException $e) {
            return $this->error($response, $e->getErrorCode(), $e->getMessage(), [], 422);
        } catch (DuplicateException $e) {
            return $this->error($response, 'ALREADY_MEMBER', $e->getMessage(), [], 409);
        }
    }

    public function leave(Request $request, Response $response, array $args): Response
    {
        $jwt = $request->getAttribute('jwt_payload', []);
        $id  = (int) $args['id'];

        try {
            $this->groupService->leave($id, $jwt);
            return $this->success($response, null, 'Left group successfully.');
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        }
    }

    public function removeMember(Request $request, Response $response, array $args): Response
    {
        $jwt      = $request->getAttribute('jwt_payload', []);
        $id       = (int) $args['id'];
        $memberId = (int) $args['member_id'];
        $data     = (array) $request->getParsedBody();

        try {
            $this->groupService->removeMember($id, $memberId, $data, $jwt);
            return $this->success($response, null, 'Member action applied successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }
}
