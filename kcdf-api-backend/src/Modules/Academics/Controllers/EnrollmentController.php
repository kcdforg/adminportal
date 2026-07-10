<?php

declare(strict_types=1);

namespace App\Modules\Academics\Controllers;

use App\Core\BaseController;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Academics\Services\EnrollmentService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EnrollmentController extends BaseController
{
    public function __construct(private readonly EnrollmentService $enrollmentService) {}

    /**
     * @OA\Get(path="/api/v1/enrollments", operationId="listEnrollments", tags={"Enrollments"}, summary="List enrollments", security={{"bearerAuth":{}}}, @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")), @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")), @OA\Response(response=200, description="Enrollments retrieved", @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function index(Request $request, Response $response): Response
    {
        $jwt     = $request->getAttribute('jwt_payload', []);
        $filters = $request->getQueryParams();

        $result = $this->enrollmentService->list($filters, $jwt);

        return $this->paginate($response, $result['data'], $result['meta']);
    }

    /**
     * @OA\Post(path="/api/v1/enrollments", operationId="createEnrollment", tags={"Enrollments"}, summary="Create enrollment", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Enrollment created", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="data", type="object"))), @OA\Response(response=404, description="Batch or member not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=409, description="Enrollment already exists or batch full", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Validation or business rule failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
    public function store(Request $request, Response $response): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $data = (array) $request->getParsedBody();

        try {
            $enrollment = $this->enrollmentService->create($data, $jwt);
            return $this->created($response, $enrollment->toArray(), 'Enrollment created successfully.');
        } catch (BusinessRuleException $e) {
            return $this->error($response, $e->getErrorCode(), $e->getMessage(), [], 422);
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    /**
     * @OA\Get(path="/api/v1/enrollments/{id}", operationId="getEnrollment", tags={"Enrollments"}, summary="Get enrollment", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\Response(response=200, description="Enrollment retrieved", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Enrollment not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $jwt = $request->getAttribute('jwt_payload', []);
        $id  = (int) $args['id'];

        try {
            $enrollment = $this->enrollmentService->show($id, $jwt);
            return $this->success($response, $enrollment->toArray());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    public function cancel(Request $request, Response $response, array $args): Response
    {
        $jwt = $request->getAttribute('jwt_payload', []);
        $id  = (int) $args['id'];

        try {
            $enrollment = $this->enrollmentService->cancel($id, $jwt);
            return $this->success($response, $enrollment->toArray(), 'Enrollment cancelled successfully.');
        } catch (BusinessRuleException $e) {
            return $this->error($response, $e->getErrorCode(), $e->getMessage(), [], 422);
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }
}
