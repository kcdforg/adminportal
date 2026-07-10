<?php

declare(strict_types=1);

namespace App\Modules\Academics\Controllers;

use App\Core\BaseController;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Academics\Services\AttendanceService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AttendanceController extends BaseController
{
    public function __construct(private readonly AttendanceService $attendanceService) {}

    /**
     * @OA\Patch(path="/api/v1/attendance/{id}", operationId="updateAttendance", tags={"Attendance"}, summary="Update attendance record", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Attendance record updated", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="message", type="string"), @OA\Property(property="data", type="object"))), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized or business rule violation", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Attendance record not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
    public function patch(Request $request, Response $response, array $args): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $id   = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        try {
            $record = $this->attendanceService->patch($id, $data, $jwt);
            return $this->success($response, $record->toArray(), 'Attendance record updated.');
        } catch (BusinessRuleException $e) {
            return $this->error($response, $e->getErrorCode(), $e->getMessage(), [], 403);
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }
}
