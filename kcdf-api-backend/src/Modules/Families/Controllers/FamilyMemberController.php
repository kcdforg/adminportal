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
