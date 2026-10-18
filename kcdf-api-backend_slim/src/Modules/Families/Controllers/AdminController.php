<?php

declare(strict_types=1);

namespace App\Modules\Families\Controllers;

use App\Core\BaseController;
use App\Core\Exceptions\DuplicateException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Families\Services\AdminService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminController extends BaseController
{
    public function __construct(private readonly AdminService $adminService) {}

    /**
     * @OA\Get(path="/api/v1/admins", operationId="listAdmins", tags={"Admins"}, summary="List admins", security={{"bearerAuth":{}}}, @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")), @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")), @OA\Response(response=200, description="Admins retrieved", @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function index(Request $request, Response $response): Response
    {
        $jwt     = $request->getAttribute('jwt_payload', []);
        $filters = $request->getQueryParams();

        $result = $this->adminService->list($filters, $jwt);

        return $this->paginate($response, $result['data'], $result['meta']);
    }

    /**
     * @OA\Post(path="/api/v1/admins", operationId="createAdmin", tags={"Admins"}, summary="Create admin", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Admin created", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="data", type="object"))), @OA\Response(response=409, description="Duplicate entry", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
    public function store(Request $request, Response $response): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $data = (array) $request->getParsedBody();

        try {
            $admin = $this->adminService->create($data, $jwt);
            return $this->created($response, $admin->toArray(), 'Admin created successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (DuplicateException $e) {
            return $this->error($response, 'DUPLICATE_ENTRY', $e->getMessage(), [], 409);
        }
    }

    /**
     * @OA\Get(path="/api/v1/admins/{id}", operationId="getAdmin", tags={"Admins"}, summary="Get admin", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\Response(response=200, description="Admin retrieved", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")), @OA\Response(response=404, description="Admin not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $jwt = $request->getAttribute('jwt_payload', []);
        $id  = (int) $args['id'];

        try {
            $admin = $this->adminService->show($id, $jwt);
            return $this->success($response, $admin->toArray());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        }
    }

    /**
     * @OA\Put(path="/api/v1/admins/{id}", operationId="updateAdmin", tags={"Admins"}, summary="Update admin", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Admin updated", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")), @OA\Response(response=404, description="Admin not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $id   = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        try {
            $admin = $this->adminService->update($id, $data, $jwt);
            return $this->success($response, $admin->toArray(), 'Admin updated successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        }
    }
}
