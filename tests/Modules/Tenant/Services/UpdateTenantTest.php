<?php

declare(strict_types=1);

namespace Tests\Modules\Tenant\Services;

use App\Entities\Tenant;
use App\Modules\Tenant\Services\UpdateTenant;
use ScottSmith\Doctrine\Integration\Testing\DatabaseHelperTrait;
use Tests\Modules\Tenant\TenantTestHelpers;
use Tests\TestCase;

class UpdateTenantTest extends TestCase
{
    use DatabaseHelperTrait;
    use TenantTestHelpers;

    private UpdateTenant $updateTenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->updateTenant = new UpdateTenant($this->getDoctrineEntityManager());
    }

    /**
     * @group Integration
     */
    public function test_updates_description(): void
    {
        $tenantId = '1d1cc8ea-d655-4348-996a-89ccafaf12db';
        $tenantName = 'test tenant';

        $this->createDbTenant($tenantId, $tenantName);

        $tenant = $this->getDoctrineEntityManager()->getRepository(Tenant::class)->find($tenantId);

        $this->updateTenant->updateDescription($tenant, 'another description');

        $this->assertTenantCreatedInDb($tenant->getId()->toString(), $tenantName, 'another description');
    }
}
