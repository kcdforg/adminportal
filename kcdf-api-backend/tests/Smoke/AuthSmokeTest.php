<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Lightweight smoke checks that do not require a running DB/HTTP stack.
 */
final class AuthSmokeTest extends TestCase
{
    public function testHelpersNowFunctionExists(): void
    {
        require_once dirname(__DIR__, 2) . '/src/helpers.php';
        $this->assertTrue(function_exists('now'));
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', now());
    }

    public function testJwtAuthMiddlewareClassExists(): void
    {
        $this->assertTrue(class_exists(\App\Middleware\JwtAuthMiddleware::class));
    }

    public function testAuthServiceExposesIssueTokenPair(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Auth\Services\AuthService::class, 'issueTokenPair'));
    }

    public function testRequireAccountsAdminMiddlewareExists(): void
    {
        $this->assertTrue(class_exists(\App\Middleware\RequireAccountsAdminMiddleware::class));
    }

    public function testActivityLogQueryServiceRenamed(): void
    {
        $this->assertTrue(class_exists(\App\Modules\Notifications\Services\ActivityLogQueryService::class));
        $this->assertFalse(class_exists(\App\Modules\Notifications\Services\ActivityLogService::class));
    }
}
