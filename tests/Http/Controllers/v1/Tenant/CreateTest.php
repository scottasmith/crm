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

class CreateTest extends TestCase
{
    private const REQUEST_DATA_REMOVE = 'remove';
    private const REQUEST_DATA_UPDATE = 'update';

    private const REQUIRED_TMPL = 'The %s.%s field is required.';
    private const MAX_SIZE_TMPL = 'The %s.%s must not be greater than 255 characters.';
    private const INVALID_STRING_TMPL = 'The %s.%s must be a string.';

    use DatabaseHelperTrait;
    use TenantTestHelpers;

    /**
     * @group Integration
     * @dataProvider provideMissingDataValidationData
     */
    #[NoReturn] public function test_returns_bad_request_when_invalid_data(
        string $operation,
        string $key,
        string $subKey,
        string $template,
        mixed $value = null
    ): void {
        $fullKey = $key . '.' . $subKey;

        $requestData = $this->createValidData();
        if (self::REQUEST_DATA_REMOVE == $operation) {
            Arr::forget($requestData, $fullKey);
        } elseif (self::REQUEST_DATA_UPDATE == $operation) {
            Arr::set($requestData, $fullKey, $value);
        }

        $response = $this->postJson('/api/v1/tenant/create', $requestData);

        $this->assertSame(SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey($fullKey, $content['errors']);

        $this->assertSame(
            [sprintf($template, $key, str_replace('_', ' ', Str::snake($subKey)))],
            $content['errors'][$fullKey]
        );
    }

    /**
     * @group Integration
     */
    #[NoReturn] public function test_returns_bad_request_when_tenant_already_exists(): void
    {
        $requestData = $this->createValidData();

        // We can't leave this without committing as it creates a lock, so create with random name
        $this->getDoctrineConnection()->commit();
        $this->createDbTenant(Uuid::uuid4()->toString(), $requestData['tenant']['name']);
        $this->getDoctrineConnection()->beginTransaction();

        $response = $this->postJson('/api/v1/tenant/create', $requestData);

        $this->assertSame(SymfonyResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertSame([
            'message' => 'Unable to create tenant. Tenant already exists',
            'tenantName' => $requestData['tenant']['name'],
        ], $content);
    }

    /**
     * @group Integration
     */
    #[NoReturn] public function test_creates_tenant_in_database(): void
    {
        $requestData = $this->createValidData();

        $response = $this->postJson('/api/v1/tenant/create', $requestData);

        $this->assertSame(SymfonyResponse::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->assertDoctrineRowInDb('tenant', ['id' => $content['tenant']['id']]);
        $this->assertDoctrineRowInDb('tenant_auth_provider', ['tenant_id' => $content['tenant']['id']]);
        $this->assertDoctrineRowInDb('user', ['id' => $content['user']['id']]);
    }

    public function provideMissingDataValidationData(): array
    {
        return [
            [self::REQUEST_DATA_REMOVE, 'tenant', 'name', self::REQUIRED_TMPL],
            [self::REQUEST_DATA_UPDATE, 'tenant', 'name', self::INVALID_STRING_TMPL, 123],
            [self::REQUEST_DATA_UPDATE, 'tenant', 'name', self::MAX_SIZE_TMPL, str_repeat('s', 256)],
            [self::REQUEST_DATA_REMOVE, 'tenant', 'description', self::REQUIRED_TMPL],
            [self::REQUEST_DATA_UPDATE, 'tenant', 'description', self::INVALID_STRING_TMPL, 123],
            [self::REQUEST_DATA_UPDATE, 'tenant', 'description', self::MAX_SIZE_TMPL, str_repeat('s', 256)],
            [self::REQUEST_DATA_REMOVE, 'user', 'email', self::REQUIRED_TMPL],
            [self::REQUEST_DATA_UPDATE, 'user', 'email', self::INVALID_STRING_TMPL, 123],
            [self::REQUEST_DATA_UPDATE, 'user', 'email', self::MAX_SIZE_TMPL, str_repeat('s', 256)],
            [self::REQUEST_DATA_REMOVE, 'user', 'title', self::REQUIRED_TMPL],
            [self::REQUEST_DATA_UPDATE, 'user', 'title', self::INVALID_STRING_TMPL, 123],
            [self::REQUEST_DATA_UPDATE, 'user', 'title', self::MAX_SIZE_TMPL, str_repeat('s', 256)],
            [self::REQUEST_DATA_REMOVE, 'user', 'givenName', self::REQUIRED_TMPL],
            [self::REQUEST_DATA_UPDATE, 'user', 'givenName', self::INVALID_STRING_TMPL, 123],
            [self::REQUEST_DATA_UPDATE, 'user', 'givenName', self::MAX_SIZE_TMPL, str_repeat('s', 256)],
            [self::REQUEST_DATA_REMOVE, 'user', 'surname', self::REQUIRED_TMPL],
            [self::REQUEST_DATA_UPDATE, 'user', 'surname', self::INVALID_STRING_TMPL, 123],
            [self::REQUEST_DATA_UPDATE, 'user', 'surname', self::MAX_SIZE_TMPL, str_repeat('s', 256)],
            [self::REQUEST_DATA_REMOVE, 'user', 'password', self::REQUIRED_TMPL],
            [self::REQUEST_DATA_UPDATE, 'user', 'password', self::INVALID_STRING_TMPL, 123],
            [self::REQUEST_DATA_UPDATE, 'user', 'password', self::MAX_SIZE_TMPL, str_repeat('s', 256)],
        ];
    }

    private function createValidData(): array
    {
        return [
            'tenant' => [
                'name' => 'Some Name ' . Str::random(),
                'description' => 'Some Description',
            ],
            'user' => [
                'email' => 'joe.bloggs@example.com',
                'title' => 'Mr',
                'givenName' => 'Joe',
                'surname' => 'Bloggs',
                'password' => 'abc123',
            ],
        ];
    }
}
