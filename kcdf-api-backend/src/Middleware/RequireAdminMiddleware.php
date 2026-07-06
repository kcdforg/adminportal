<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Allows any admin role: super_admin, program_manager, accounts, readonly.
 */
class RequireAdminMiddleware extends RoleMiddleware
{
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        parent::__construct($responseFactory, [
            'admin_super',
            'admin_program_manager',
            'admin_accounts',
            'admin_readonly',
        ]);
    }
}
