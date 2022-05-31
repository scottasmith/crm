<?php

declare(strict_types=1);

namespace Tests\Console\Commands\Tenant;

use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use ScottSmith\Doctrine\Integration\Testing\DatabaseHelperTrait;
use Tests\Modules\Tenant\TenantTestHelpers;
use Tests\TestCase;

class SoftDeleteDeactivatedTest extends TestCase
{
    use DatabaseHelperTrait;
    use TenantTestHelpers;

    public function test_does_nothing_when_no_tenants_to_soft_delete()
    {
        $this->artisan('tenant:soft-delete-deactivated')
            ->expectsOutput('No tenants to delete')
            ->assertSuccessful();
    }

    /**
     * @group NonTransactional
     */
    public function test_updates_deleted_datetime_when_finds_inactive_tenant()
    {
        $tenantId = Uuid::uuid4()->toString();
        $tenantName = 'test-tenant-' . Str::random();

        $this->createDbTenant($tenantId, $tenantName, false);

        $this->artisan('tenant:soft-delete-deactivated')
            ->expectsOutput('Soft deleting tenant ID ' . $tenantId)
            ->assertSuccessful();
    }
}
