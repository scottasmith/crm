<?php

declare(strict_types=1);

namespace Tests\Modules\Tenant\Providers;

use App\Entities\Tenant;
use App\Modules\Tenant\Providers\TenantProvider;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use ScottSmith\Doctrine\Integration\Testing\DatabaseHelperTrait;
use Tests\Modules\Tenant\TenantTestHelpers;
use Tests\TestCase;

class TenantProviderTest extends TestCase
{
    use DatabaseHelperTrait;
    use TenantTestHelpers;

    private TenantProvider $tenantProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantProvider = new TenantProvider($this->getDoctrineEntityManager());
    }

    /**
     * @group Integration
     */
    #[NoReturn] public function test_get_by_slug_returns_null_when_doesnt_exist(): void
    {
        $this->assertNull($this->tenantProvider->getBySlug('test'));
    }

    /**
     * @group Integration
     */
    #[NoReturn] public function test_get_by_slug_returns_tenant_when_exists(): void
    {
        $tenantId = '5dca843c-70a2-4bde-ad0f-04bd2b072e6e';
        $tenantName = 'test-tenant';

        $this->createDbTenant($tenantId, $tenantName);

        $tenant = $this->tenantProvider->getBySlug('test-tenant');

        $this->assertInstanceOf(Tenant::class, $tenant);
        $this->assertSame($tenantId, $tenant->getId()->toString());
        $this->assertSame($tenantName, $tenant->getName());
        $this->assertSame(Str::slug($tenantName), $tenant->getSlug());
        $this->assertSame($tenantName . ' Description', $tenant->getDescription());
        $this->assertEquals(new \DateTimeImmutable('2022-05-28 01:01:01'), $tenant->getCreatedDateTime());
        $this->assertEquals(new \DateTimeImmutable('2022-05-28 02:02:02'), $tenant->getUpdatedDateTime());
    }
}
