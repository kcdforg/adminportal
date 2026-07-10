<?php

declare(strict_types=1);

namespace App\Modules\Academics\Controllers;

use App\Core\BaseController;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Academics\Services\ProgramService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProgramController extends BaseController
{
    public function __construct(private readonly ProgramService $programService) {}

    /**
     * @OA\Get(path="/api/v1/programs", operationId="listPrograms", tags={"Programs"}, summary="List programs", security={{"bearerAuth":{}}}, @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")), @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")), @OA\Response(response=200, description="Programs retrieved", @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function index(Request $request, Response $response): Response
    {
        $jwt     = $request->getAttribute('jwt_payload', []);
        $filters = $request->getQueryParams();

        $result = $this->programService->list($filters, $jwt);

        return $this->paginate($response, $result['data'], $result['meta']);
    }

    /**
     * @OA\Post(path="/api/v1/programs", operationId="createProgram", tags={"Programs"}, summary="Create program", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Program created", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="data", type="object"))), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
    public function store(Request $request, Response $response): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $data = (array) $request->getParsedBody();

        try {
            $program = $this->programService->create($data, $jwt);
            return $this->created($response, $program->toArray(), 'Program created successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    /**
     * @OA\Get(path="/api/v1/programs/{id}", operationId="getProgram", tags={"Programs"}, summary="Get program", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\Response(response=200, description="Program retrieved", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Program not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $jwt = $request->getAttribute('jwt_payload', []);
        $id  = (int) $args['id'];

        try {
            $program = $this->programService->show($id, $jwt);
            return $this->success($response, $program->toArray());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        }
    }

    /**
     * @OA\Put(path="/api/v1/programs/{id}", operationId="updateProgram", tags={"Programs"}, summary="Update program", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Program updated", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Program not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $id   = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        try {
            $program = $this->programService->update($id, $data, $jwt);
            return $this->success($response, $program->toArray(), 'Program updated successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Response $response, array $args): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $id   = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        try {
            $program = $this->programService->updateStatus($id, $data, $jwt);
            return $this->success($response, $program->toArray(), 'Program status updated successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }
}
