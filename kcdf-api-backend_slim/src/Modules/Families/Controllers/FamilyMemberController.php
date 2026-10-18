<?php

declare(strict_types=1);

namespace App\Modules\Families\Controllers;

use App\Core\BaseController;
use App\Core\Exceptions\DuplicateException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Families\Services\FamilyService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FamilyMemberController extends BaseController
{
    public function __construct(private readonly FamilyService $familyService) {}

    /**
     * @OA\Get(path="/api/v1/families/{id}/members", operationId="listFamilyMembers", tags={"Family Members"}, summary="List family members", description="Get all members of a specific family.", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", description="Family ID", required=true, @OA\Schema(type="integer")), @OA\Response(response=200, description="Family members retrieved", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Family not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function index(Request $request, Response $response, array $args): Response
    {
        $jwt      = $request->getAttribute('jwt_payload', []);
        $familyId = (int) $args['id'];

        try {
            $members = $this->familyService->listMembers($familyId, $jwt);
            return $this->success($response, $members);
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }

    /**
     * @OA\Post(path="/api/v1/families/{id}/members", operationId="addFamilyMember", tags={"Family Members"}, summary="Add member to family", description="Add a member to a specific family.", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", description="Family ID", required=true, @OA\Schema(type="integer")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Member added to family", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="message", type="string"), @OA\Property(property="data", type="object"))), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Family or member not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=409, description="Member already in family", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")))
     */
    public function store(Request $request, Response $response, array $args): Response
    {
        $jwt      = $request->getAttribute('jwt_payload', []);
        $familyId = (int) $args['id'];
        $data     = (array) $request->getParsedBody();

        try {
            $membership = $this->familyService->addMember($familyId, $data, $jwt);
            return $this->created($response, $membership->toArray(), 'Member added to family successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        } catch (DuplicateException $e) {
            return $this->error($response, 'DUPLICATE_ENTRY', $e->getMessage(), [], 409);
        }
    }

    /**
     * @OA\Delete(path="/api/v1/families/{id}/members/{profile_id}", operationId="removeFamilyMember", tags={"Family Members"}, summary="Remove member from family", description="Remove a member from a specific family.", security={{"bearerAuth":{}}}, @OA\Parameter(name="id", in="path", description="Family ID", required=true, @OA\Schema(type="integer")), @OA\Parameter(name="profile_id", in="path", description="Member profile ID", required=true, @OA\Schema(type="integer")), @OA\Response(response=200, description="Member removed from family", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="message", type="string"))), @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=403, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")), @OA\Response(response=404, description="Family or member not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")))
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $jwt       = $request->getAttribute('jwt_payload', []);
        $familyId  = (int) $args['id'];
        $profileId = (int) $args['profile_id'];

        try {
            $this->familyService->removeMember($familyId, $profileId, $jwt);
            return $this->success($response, null, 'Member removed from family successfully.');
        } catch (NotFoundException $e) {
            return $this->notFound($response, $e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->unauthorized($response, $e->getMessage());
        }
    }
}
