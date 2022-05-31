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

class UpdateTest extends TestCase
{
    private const REQUEST_DATA_REMOVE = 'remove';
    private const REQUEST_DATA_UPDATE = 'update';

    private const MAX_SIZE_TMPL = 'The %s must not be greater than 255 characters.';
    private const INVALID_STRING_TMPL = 'The %s must be a string.';

    use DatabaseHelperTrait;
    use TenantTestHelpers;

    /**
     * @group Integration
     * @dataProvider provideMissingDataValidationData
     */
    #[NoReturn] public function test_returns_bad_request_when_invalid_data(
        string $operation,
        string $key,
        string $template,
        mixed $value = null
    ): void {
        $requestData = ['description' => 'other description'];
        if (self::REQUEST_DATA_REMOVE == $operation) {
            Arr::forget($requestData, $key);
        } elseif (self::REQUEST_DATA_UPDATE == $operation) {
            Arr::set($requestData, $key, $value);
        }

        $response = $this->patchJson('/api/v1/tenant/update/b5395410-f816-4a45-a499-7b59e42b2a0b', $requestData);

        $this->assertSame(SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey($key, $content['errors']);

        $this->assertSame(
            [sprintf($template, $key)],
            $content['errors'][$key]
        );
    }

    #[NoReturn] public function test_returns_not_found_when_tenant_doesnt_exist(): void
    {
        $tenantId = Uuid::uuid4()->toString();

        $response = $this->patchJson(
            '/api/v1/tenant/update/' . $tenantId,
            ['description' => 'other description']
        );

        $content = json_decode($response->getContent(), true);

        $this->assertSame(['message' => sprintf('Tenant ID %s not found', $tenantId)], $content);
        $this->assertSame(SymfonyResponse::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @group NonTransactional
     */
    #[NoReturn] public function test_doesnt_update_description_when_not_passed(): void
    {
        $tenantId = Uuid::uuid4()->toString();
        $tenantName = 'test-tenant-' . Str::random();

        $this->createDbTenant($tenantId, $tenantName);

        $response = $this->patchJson('/api/v1/tenant/update/' . $tenantId);
        $this->assertSame(SymfonyResponse::HTTP_I_AM_A_TEAPOT, $response->getStatusCode());
    }

    /**
     * @group NonTransactional
     */
    #[NoReturn] public function test_updates_tenant_in_database(): void
    {
        $tenantId = Uuid::uuid4()->toString();
        $tenantName = 'test-tenant-' . Str::random();

        $this->createDbTenant($tenantId, $tenantName);

        $response = $this->patchJson(
            '/api/v1/tenant/update/' . $tenantId,
            ['description' => 'other description']
        );

        $this->assertSame(SymfonyResponse::HTTP_ACCEPTED, $response->getStatusCode());
        $this->assertDoctrineRowInDb('tenant', ['id' => $tenantId, 'description' => 'other description']);
    }

    public function provideMissingDataValidationData(): array
    {
        return [
            [self::REQUEST_DATA_UPDATE, 'description', self::INVALID_STRING_TMPL, 123],
            [self::REQUEST_DATA_UPDATE, 'description', self::MAX_SIZE_TMPL, str_repeat('s', 256)],
        ];
    }
}
