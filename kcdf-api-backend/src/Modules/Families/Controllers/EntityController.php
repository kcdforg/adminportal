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

    public function index(Request $request, Response $response): Response
    {
        $jwt     = $request->getAttribute('jwt_payload', []);
        $filters = $request->getQueryParams();

        $result = $this->entityService->list($filters, $jwt);

        return $this->paginate($response, $result['data'], $result['meta']);
    }

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
