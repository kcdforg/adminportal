<?php

declare(strict_types=1);

namespace App\Modules\Families\Controllers;

use App\Core\BaseController;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Families\Services\EntityService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EntityController extends BaseController
{
    public function __construct(private readonly EntityService $entityService) {}

    /**
     * @OA\Get(path="/api/v1/entities", operationId="listEntities", tags={"Entities"}, summary="List entities", security={{"bearerAuth":{}}}, @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")), @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")), @OA\Response(response=200, description="Entities retrieved", @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function index(Request $request, Response $response): Response
    {
        $jwt     = $request->getAttribute('jwt_payload', []);
        $filters = $request->getQueryParams();

        $result = $this->entityService->list($filters, $jwt);

        return $this->paginate($response, $result['data'], $result['meta']);
    }

    /**
     * @OA\Post(path="/api/v1/entities", operationId="createEntity", tags={"Entities"}, summary="Create entity", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Entity created", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="data", type="object"))), @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
    public function store(Request $request, Response $response): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $data = (array) $request->getParsedBody();

        try {
            $entity = $this->entityService->create($data, $jwt);
            return $this->created($response, $entity->toArray(), 'Entity created successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        }
    }

    /**
     * @OA\Get(path="/api/v1/entities/{id}", operationId="getEntity", tags={"Entities"}, summary="Get entity", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\Response(response=200, description="Entity retrieved", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Entity not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $jwt = $request->getAttribute('jwt_payload', []);
        $id  = (int) $args['id'];

        try {
            $entity = $this->entityService->show($id, $jwt);
            return $this->success($response, $entity->toArray());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        }
    }

    /**
     * @OA\Put(path="/api/v1/entities/{id}", operationId="updateEntity", tags={"Entities"}, summary="Update entity", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Entity updated", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Entity not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $id   = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        try {
            $entity = $this->entityService->update($id, $data, $jwt);
            return $this->success($response, $entity->toArray(), 'Entity updated successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        }
    }

    public function listRelations(Request $request, Response $response, array $args): Response
    {
        $jwt      = $request->getAttribute('jwt_payload', []);
        $memberId = (int) $args['id'];

        try {
            $relations = $this->entityService->listRelations($memberId, $jwt);
            return $this->success($response, $relations);
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    public function storeRelation(Request $request, Response $response, array $args): Response
    {
        $jwt      = $request->getAttribute('jwt_payload', []);
        $memberId = (int) $args['id'];
        $data     = (array) $request->getParsedBody();

        try {
            $relation = $this->entityService->addRelation($memberId, $data, $jwt);
            return $this->created($response, $relation->toArray(), 'Entity relation added successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    public function destroyRelation(Request $request, Response $response, array $args): Response
    {
        $jwt        = $request->getAttribute('jwt_payload', []);
        $memberId   = (int) $args['id'];
        $relationId = (int) $args['relation_id'];

        try {
            $this->entityService->removeRelation($memberId, $relationId, $jwt);
            return $this->success($response, null, 'Entity relation removed successfully.');
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }
}
