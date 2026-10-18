<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Allows accounts admins and super admins (payment mutations / listings).
 */
class RequireAccountsAdminMiddleware extends RoleMiddleware
{
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        parent::__construct($responseFactory, [
            'admin_super',
            'admin_accounts',
        ]);
    }
}
