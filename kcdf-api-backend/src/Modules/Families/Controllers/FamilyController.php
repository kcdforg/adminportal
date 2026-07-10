<?php

declare(strict_types=1);

namespace App\Modules\Families\Controllers;

use App\Core\BaseController;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Families\Services\FamilyService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FamilyController extends BaseController
{
    public function __construct(private readonly FamilyService $familyService) {}

    /**
     * @OA\Get(
     *     path="/api/v1/families",
     *     operationId="listFamilies",
     *     tags={"Families"},
     *     summary="List families",
     *     description="Retrieve a paginated list of families with optional filtering.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", example=15)),
     *     @OA\Response(response=200, description="Families retrieved", @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function index(Request $request, Response $response): Response
    {
        $jwt     = $request->getAttribute('jwt_payload', []);
        $filters = $request->getQueryParams();

        $result = $this->familyService->list($filters, $jwt);

        return $this->paginate($response, $result['data'], $result['meta']);
    }

    /**
     * @OA\Post(path="/api/v1/families", operationId="createFamily", tags={"Families"}, summary="Create family", description="Create a new family with provided details.", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Family created", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="message", type="string", example="Family created successfully."), @OA\Property(property="data", type="object"))), @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
    public function store(Request $request, Response $response): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $data = (array) $request->getParsedBody();

        try {
            $family = $this->familyService->create($data, $jwt);
            return $this->created($response, $family->toArray(), 'Family created successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        }
    }

    /**
     * @OA\Get(path="/api/v1/families/{id}", operationId="getFamily", tags={"Families"}, summary="Get family", description="Retrieve a specific family by ID.", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", description="Family ID", required=true, @OA\Schema(type="integer")), @OA\Response(response=200, description="Family retrieved", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Family not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $jwt = $request->getAttribute('jwt_payload', []);
        $id  = (int) $args['id'];

        try {
            $family = $this->familyService->show($id, $jwt);
            return $this->success($response, $family->toArray());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    /**
     * @OA\Put(path="/api/v1/families/{id}", operationId="updateFamily", tags={"Families"}, summary="Update family", description="Update an existing family with new data.", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", description="Family ID", required=true, @OA\Schema(type="integer")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Family updated", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="message", type="string", example="Family updated successfully."), @OA\Property(property="data", type="object"))), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Family not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $id   = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        try {
            $family = $this->familyService->update($id, $data, $jwt);
            return $this->success($response, $family->toArray(), 'Family updated successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }
}
