<?php

declare(strict_types=1);

namespace Tests\Modules\Tenant\Providers;

use App\Entities\Tenant;
use App\Modules\Tenant\Providers\TenantProvider;
use DateTimeImmutable;
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
        $this->assertNull($this->tenantProvider->getByActiveSlug('test'));
    }

    /**
     * @group Integration
     */
    #[NoReturn] public function test_get_by_slug_returns_null_when_tenant_deleted(): void
    {
        $tenantId = '5dca843c-70a2-4bde-ad0f-04bd2b072e6e';
        $tenantName = 'test-tenant';

        $this->createDbTenant($tenantId, $tenantName, deletedDatetime: new DateTimeImmutable());

        $tenant = $this->tenantProvider->getByActiveSlug($tenantName);
        $this->assertNull($tenant);
    }

    /**
     * @group Integration
     */
    #[NoReturn] public function test_get_by_slug_returns_null_when_tenant_not_active(): void
    {
        $tenantId = '5dca843c-70a2-4bde-ad0f-04bd2b072e6e';
        $tenantName = 'test-tenant';

        $this->createDbTenant($tenantId, $tenantName, isActive: false);

        $tenant = $this->tenantProvider->getByActiveSlug($tenantName);
        $this->assertNull($tenant);
    }

    /**
     * @group Integration
     */
    #[NoReturn] public function test_get_by_slug_returns_tenant_when_exists_and_active(): void
    {
        $tenantId = '5dca843c-70a2-4bde-ad0f-04bd2b072e6e';
        $tenantName = 'test-tenant';

        $this->createDbTenant($tenantId, $tenantName, isActive: true);

        $tenant = $this->tenantProvider->getByActiveSlug(Str::slug($tenantName));

        $this->assertInstanceOf(Tenant::class, $tenant);
        $this->assertSame($tenantId, $tenant->getId()->toString());
        $this->assertSame($tenantName, $tenant->getName());
        $this->assertSame(Str::slug($tenantName), $tenant->getSlug());
        $this->assertSame($tenantName . ' Description', $tenant->getDescription());
        $this->assertTrue($tenant->isActive());
        $this->assertNull($tenant->getDeletedDateTime());
        $this->assertEquals(new DateTimeImmutable('2022-05-28 01:01:01'), $tenant->getCreatedDateTime());
        $this->assertEquals(new DateTimeImmutable('2022-05-28 02:02:02'), $tenant->getUpdatedDateTime());
    }

    /**
     * @group Integration
     */
    #[NoReturn] public function test_get_by_name_returns_null_when_doesnt_exist(): void
    {
        $this->assertNull($this->tenantProvider->getByName('test'));
    }

    /**
     * @group Integration
     */
    #[NoReturn] public function test_get_by_name_returns_null_when_tenant_deleted(): void
    {
        $tenantId = '5dca843c-70a2-4bde-ad0f-04bd2b072e6e';
        $tenantName = 'test-tenant';

        $this->createDbTenant($tenantId, $tenantName, deletedDatetime: new DateTimeImmutable());

        $tenant = $this->tenantProvider->getByName('test-tenant');
        $this->assertNull($tenant);
    }

    /**
     * @group Integration
     * @dataProvider provideActiveFlag
     */
    #[NoReturn] public function test_get_by_name_returns_tenant_when_not_deleted(bool $isActive): void
    {
        $tenantId = '5dca843c-70a2-4bde-ad0f-04bd2b072e6e';
        $tenantName = 'test-tenant';

        $this->createDbTenant($tenantId, $tenantName, isActive: $isActive);

        $tenant = $this->tenantProvider->getByName($tenantName);

        $this->assertInstanceOf(Tenant::class, $tenant);
        $this->assertSame($tenantId, $tenant->getId()->toString());
        $this->assertSame($tenantName, $tenant->getName());
        $this->assertSame(Str::slug($tenantName), $tenant->getSlug());
        $this->assertSame($tenantName . ' Description', $tenant->getDescription());
        $this->assertSame($isActive, $tenant->isActive());
        $this->assertNull($tenant->getDeletedDateTime());
        $this->assertEquals(new DateTimeImmutable('2022-05-28 01:01:01'), $tenant->getCreatedDateTime());
        $this->assertEquals(new DateTimeImmutable('2022-05-28 02:02:02'), $tenant->getUpdatedDateTime());
    }

    /**
     * @group Integration
     */
    #[NoReturn] public function test_get_by_id_returns_null_when_doesnt_exist(): void
    {
        $tenantId = '5dca843c-70a2-4bde-ad0f-04bd2b072e6e';
        $this->assertNull($this->tenantProvider->getById($tenantId));
    }

    /**
     * @group Integration
     */
    #[NoReturn] public function test_get_by_id_returns_null_when_tenant_deleted(): void
    {
        $tenantId = '5dca843c-70a2-4bde-ad0f-04bd2b072e6e';
        $tenantName = 'test-tenant';

        $this->createDbTenant($tenantId, $tenantName, deletedDatetime: new DateTimeImmutable());

        $tenant = $this->tenantProvider->getById($tenantId);
        $this->assertNull($tenant);
    }

    /**
     * @group Integration
     * @dataProvider provideActiveFlag
     */
    #[NoReturn] public function test_get_by_id_returns_tenant_when_not_deleted(bool $isActive): void
    {
        $tenantId = '5dca843c-70a2-4bde-ad0f-04bd2b072e6e';
        $tenantName = 'test-tenant';

        $this->createDbTenant($tenantId, $tenantName, isActive: $isActive);

        $tenant = $this->tenantProvider->getById($tenantId);

        $this->assertInstanceOf(Tenant::class, $tenant);
        $this->assertSame($tenantId, $tenant->getId()->toString());
        $this->assertSame($tenantName, $tenant->getName());
        $this->assertSame(Str::slug($tenantName), $tenant->getSlug());
        $this->assertSame($tenantName . ' Description', $tenant->getDescription());
        $this->assertSame($isActive, $tenant->isActive());
        $this->assertNull($tenant->getDeletedDateTime());
        $this->assertEquals(new DateTimeImmutable('2022-05-28 01:01:01'), $tenant->getCreatedDateTime());
        $this->assertEquals(new DateTimeImmutable('2022-05-28 02:02:02'), $tenant->getUpdatedDateTime());
    }

    public function provideActiveFlag(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
