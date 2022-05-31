<?php

declare(strict_types=1);

namespace Tests\Http\Controllers\v1\Tenant;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use Ramsey\Uuid\Uuid;
use ScottSmith\Doctrine\Integration\Testing\DatabaseHelperTrait;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Tests\Modules\Tenant\TenantTestHelpers;
use Tests\TestCase;

class DeactivateTest extends TestCase
{
    use DatabaseHelperTrait;
    use TenantTestHelpers;

    #[NoReturn] public function test_returns_not_found_when_tenant_doesnt_exist(): void
    {
        $tenantId = Uuid::uuid4()->toString();

        $response = $this->deleteJson('/api/v1/tenant/deactivate/' . $tenantId);

        $content = json_decode($response->getContent(), true);

        $this->assertSame(['message' => sprintf('Tenant ID %s not found', $tenantId)], $content);
        $this->assertSame(SymfonyResponse::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @group NonTransactional
     */
    #[NoReturn] public function test_updates_tenant_in_database(): void
    {
        $tenantId = Uuid::uuid4()->toString();
        $tenantName = 'test-tenant-' . Str::random();

        $this->createDbTenant($tenantId, $tenantName);

        $response = $this->deleteJson('/api/v1/tenant/deactivate/' . $tenantId);

        $this->assertSame(SymfonyResponse::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->assertDoctrineRowInDb('tenant', ['id' => $tenantId, 'is_active' => 0]);
    }

    public function provideMissingDataValidationData(): array
    {
        return [
            [self::REQUEST_DATA_UPDATE, 'description', self::INVALID_STRING_TMPL, 123],
            [self::REQUEST_DATA_UPDATE, 'description', self::MAX_SIZE_TMPL, str_repeat('s', 256)],
        ];
    }
}
