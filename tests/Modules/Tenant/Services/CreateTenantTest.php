<?php

declare(strict_types=1);

namespace Tests\Modules\Tenant\Services;

use App\Entities\TenantAuthProviderTypeIdEnum;
use App\Modules\Tenant\Services\CreateTenant;
use ScottSmith\Doctrine\Integration\Testing\DatabaseHelperTrait;
use Tests\Modules\Tenant\TenantTestHelpers;
use Tests\TestCase;

class CreateTenantTest extends TestCase
{
    use DatabaseHelperTrait;
    use TenantTestHelpers;

    private CreateTenant $createTenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createTenant = new CreateTenant($this->getDoctrineEntityManager());
    }

    /**
     * @group Integration
     */
    public function test_create_creates_tenant(): void
    {
        $tenantName = 'test tenant';
        $tenantDesc = 'test-tenant Description';

        $tenant = $this->createTenant->create($tenantName, $tenantDesc);

        $this->assertTenantCreatedInDb($tenant->getId()->toString(), $tenantName, $tenantDesc);
    }

    /**
     * @group Integration
     */
    public function test_set_auth_provider_sets_expected_provider(): void
    {
        $tenantName = 'test-tenant';
        $tenantDesc = 'test-tenant Description';

        $tenant = $this->createTenant->create($tenantName, $tenantDesc);

        $this->createTenant->setAuthProvider(
            $tenant,
            TenantAuthProviderTypeIdEnum::PROVIDER_TYPE_BASIC_ID,
            ['a' => 'b']
        );

        $this->assertTenantCreatedInDb($tenant->getId()->toString(), $tenantName, $tenantDesc);
        $this->assertTenantAuthProviderInDb(
            $tenant->getId()->toString(),
            TenantAuthProviderTypeIdEnum::PROVIDER_TYPE_BASIC_ID->value,
            ['a' => 'b']
        );

    }
}
