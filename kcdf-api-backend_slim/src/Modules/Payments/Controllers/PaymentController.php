<?php

declare(strict_types=1);

namespace App\Modules\Payments\Controllers;

use App\Core\BaseController;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Payments\Services\PaymentService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PaymentController extends BaseController
{
    public function __construct(private readonly PaymentService $paymentService) {}

    /**
     * @OA\Get(path="/api/v1/payments", operationId="listPayments", tags={"Payments"}, summary="List payments", security={{"bearerAuth":{}}}, @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")), @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")), @OA\Response(response=200, description="Payments retrieved", @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function index(Request $request, Response $response): Response
    {
        $jwt     = $request->getAttribute('jwt_payload', []);
        $filters = $request->getQueryParams();

        try {
            $result = $this->paymentService->list($filters, $jwt);
            return $this->paginate($response, $result['data'], $result['meta']);
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    /**
     * @OA\Get(path="/api/v1/families/{id}/payments", operationId="listFamilyPayments", tags={"Payments"}, summary="List family payments", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", description="Family ID", required=true, @OA\Schema(type="integer")), @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")), @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")), @OA\Response(response=200, description="Family payments retrieved", @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Family not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function familyPayments(Request $request, Response $response, array $args): Response
    {
        $jwt      = $request->getAttribute('jwt_payload', []);
        $familyId = (int) $args['id'];
        $filters  = $request->getQueryParams();

        try {
            $result = $this->paymentService->listForFamily($familyId, $filters, $jwt);
            return $this->paginate($response, $result['data'], $result['meta']);
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    /**
     * @OA\Post(path="/api/v1/payments", operationId="createPayment", tags={"Payments"}, summary="Record payment", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Payment recorded", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="data", type="object"))), @OA\Response(response=404, description="Family not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Validation or business rule failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
    public function store(Request $request, Response $response): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $data = (array) $request->getParsedBody();

        try {
            $payment = $this->paymentService->create($data, $jwt);
            return $this->created($response, $payment->toArray(), 'Payment recorded successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $jwt = $request->getAttribute('jwt_payload', []);
        $id  = (int) $args['id'];

        try {
            $payment = $this->paymentService->show($id, $jwt);
            return $this->success($response, $payment->toArray());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $id   = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        try {
            $payment = $this->paymentService->update($id, $data, $jwt);
            return $this->success($response, $payment->toArray(), 'Payment updated successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (BusinessRuleException $e) {
            return $this->error($response, $e->getErrorCode(), $e->getMessage(), [], 422);
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }
}
