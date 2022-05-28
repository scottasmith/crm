<?php

declare(strict_types=1);

namespace Tests\Modules\Tenant\Services\Exception;

use App\Entities\TenantAuthProviderTypeIdEnum;
use App\Modules\Tenant\Services\Exception\CreateTenantException;
use JetBrains\PhpStorm\NoReturn;
use PHPUnit\Framework\TestCase;

class CreateTenantExceptionTest extends TestCase
{
    /**
     * @group Unit
     */
    #[NoReturn] public function test_create_invalid_tenant_returns_expected_exception()
    {
        $exception = CreateTenantException::createAuthTypeNotFound(
            TenantAuthProviderTypeIdEnum::PROVIDER_TYPE_BASIC_ID
        )->withMetaData(['a' => 'b']);

        $providerTypeName = TenantAuthProviderTypeIdEnum::PROVIDER_TYPE_BASIC_ID->name;
        $this->assertSame(
            sprintf("Authenticate type '%s' not found", $providerTypeName),
            $exception->getMessage()
        );
        $this->assertSame(CreateTenantException::ERROR_AUTH_TYPE_NOT_FOUND, $exception->getApplicationCode());
        $this->assertSame(['a' => 'b'], $exception->getMetaData());
    }
}
