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

class GetTest extends TestCase
{
    use DatabaseHelperTrait;
    use TenantTestHelpers;

    /**
     * @group NonTransactional
     */
    #[NoReturn] public function test_throws_exception_hen_tenant_doesnt_exist(): void
    {
        $tenantId = '91c5d584-00ef-422e-b9af-7d17a44922f9';

        $response = $this->getJson('/api/v1/tenant/get/' . $tenantId);

        $content = json_decode($response->getContent(), true);

        $this->assertSame(SymfonyResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertSame(['message'=> sprintf('Tenant ID %s not found', $tenantId)], $content);
    }

    /**
     * @group NonTransactional
     */
    #[NoReturn] public function test_returns_correct_details_from_tenant(): void
    {
        $tenantId = '91c5d584-00ef-422e-b9af-7d17a44922f9';
        $tenantName = 'test-tenant-' . Str::random();

        $this->createDbTenant($tenantId, $tenantName);

        $response = $this->getJson('/api/v1/tenant/get/' . $tenantId);

        $content = json_decode($response->getContent(), true);

        $this->assertSame([
            'id' => $tenantId,
            'name' => $tenantName,
            'description' => $tenantName . ' Description',
        ], $content);
    }
}
