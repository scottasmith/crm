<?php

declare(strict_types=1);

namespace Tests\Modules\Tenant;

use DateTimeImmutable;
use Illuminate\Support\Str;

/**
 * @uses void insertDoctrineRow(string $table, array $data)
 * @uses void assertDoctrineRowInDb(string $table, array $queryParams)
 */
trait TenantTestHelpers
{
    protected function createDbTenant(
        string $id,
        string $name,
        bool $isActive = true,
        DateTimeImmutable $deletedDatetime = null
    ): void {
        $this->insertDoctrineRow('tenant', [
            'id' => $id,
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $name . ' Description',
            'is_active' => $isActive ? 1 : 0,
            'deleted_datetime' => $deletedDatetime?->format('Y-m-d H:i:s'),
            'created_datetime' => '2022-05-28 01:01:01',
            'updated_datetime' => '2022-05-28 02:02:02',
        ]);
    }

    protected function assertTenantCreatedInDb(string $id, string $name, string $description): void
    {
        $this->assertDoctrineRowInDb('tenant', [
            'id' => $id,
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $description,
            'is_active' => 1,
            'created_datetime' => true,
            'updated_datetime' => true,
            'deleted_datetime' => null,
        ]);
    }

    protected function assertTenantAuthProviderInDb(string $tenantId, string $providerTypeId, array $options): void
    {
        $this->assertDoctrineRowInDb('tenant_auth_provider', [
            'tenant_id' => $tenantId,
            'provider_type_id' => $providerTypeId,
            'options' => $options,
            'created_datetime' => true,
            'updated_datetime' => true,
            'deleted_datetime' => null,
        ]);
    }
}
