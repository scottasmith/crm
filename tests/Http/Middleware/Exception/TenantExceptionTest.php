<?php

declare(strict_types=1);

namespace Tests\Http\Middleware\Exception;

use App\Http\Middleware\Exception\TenantException;
use JetBrains\PhpStorm\NoReturn;
use PHPUnit\Framework\TestCase;

class TenantExceptionTest extends TestCase
{
    /**
     * @group Unit
     */
    #[NoReturn] public function test_create_invalid_url_returns_expected_exception(): void
    {
        $exception = TenantException::createInvalidUrl()->withMetaData(['a' => 'b']);

        $this->assertSame('Invalid Request', $exception->getMessage());
        $this->assertSame(TenantException::ERROR_INVALID_REQUEST, $exception->getApplicationCode());
        $this->assertSame(['a' => 'b'], $exception->getMetaData());
    }

    /**
     * @group Unit
     */
    #[NoReturn] public function test_create_invalid_tenant_returns_expected_exception(): void
    {
        $exception = TenantException::createInvalidTenant('some-tenant-name')->withMetaData(['a' => 'b']);

        $this->assertSame("Invalid Tenant 'some-tenant-name'", $exception->getMessage());
        $this->assertSame(TenantException::ERROR_INVALID_TENANT, $exception->getApplicationCode());
        $this->assertSame(['a' => 'b'], $exception->getMetaData());
    }
}
