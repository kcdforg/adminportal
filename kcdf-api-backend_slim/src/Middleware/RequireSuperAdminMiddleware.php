<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Allows super_admin only.
 */
class RequireSuperAdminMiddleware extends RoleMiddleware
{
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        parent::__construct($responseFactory, ['admin_super']);
    }
}
