<?php

declare(strict_types=1);

namespace App\Modules\Families\Controllers;

use App\Core\BaseController;
use App\Core\Exceptions\DuplicateException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Families\Services\MemberService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MemberController extends BaseController
{
    public function __construct(private readonly MemberService $memberService) {}

    public function index(Request $request, Response $response): Response
    {
        $jwt     = $request->getAttribute('jwt_payload', []);
        $filters = $request->getQueryParams();

        $result = $this->memberService->list($filters, $jwt);

        return $this->paginate($response, $result['data'], $result['meta']);
    }

    public function store(Request $request, Response $response): Response
    {
        $jwt  = $request->getAttribute('jwt_payload', []);
        $data = (array) $request->getParsedBody();

        try {
            $profile = $this->memberService->create($data, $jwt);
            return $this->created($response, $profile->toArray(), 'Member profile created successfully.');
        } catch (ValidationException $e) {
            return $this->validationError($response, $e->getErrors());
        } catch (DuplicateException $e) {
            return $this->error($response, 'DUPLICATE_ENTRY', $e->getMessage(), [], 409);
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $jwt = $request->getAttribute('jwt_payload', []);
        $id  = (int) $args['id'];

        try {
            $profile = $this->memberService->show($id, $jwt);
            return $this->success($response, $profile->toArray());
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
            $profile = $this->memberService->update($id, $data, $jwt);
            return $this->success($response, $profile->toArray(), 'Member profile updated successfully.');
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
}
