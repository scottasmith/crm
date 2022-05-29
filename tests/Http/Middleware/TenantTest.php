<?php

namespace Tests\Http\Middleware;

use App\Entities\Tenant as TenantEntity;
use App\Http\Middleware\Exception\TenantException;
use App\Http\Middleware\Tenant;
use App\Modules\Tenant\Providers\TenantProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JetBrains\PhpStorm\NoReturn;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Tests\TestCase;

class TenantTest extends TestCase
{
    const HOME_URL = 'http://test.local';
    const TENANT_HOST = 'tenant.test.local';

    private TenantProvider|MockInterface $tenantProvider;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantProvider = Mockery::mock(TenantProvider::class);
    }

    /**
     * @dataProvider provideBadHosts
     * @group Unit
     */
    #[NoReturn] public function test_throws_exception_with_invalid_host_and_ajax($host): void
    {
        $request = $this->createRequest($host, true);
        $response = new Response();

        $tenantMock = $this->tenantProvider->expects('getByActiveSlug')->never()->getMock();
        $this->tenant = new Tenant(self::TENANT_HOST, $tenantMock);

        $this->expectException(TenantException::class);
        $this->expectExceptionMessage('Invalid Request');

        $this->tenant->handle($request, function () use ($response) { return $response; });
    }

    /**
     * @dataProvider provideBadHosts
     * @group Unit
     */
    #[NoReturn] public function test_throws_exception_with_invalid_host($host): void
    {
        $request = $this->createRequest($host);
        $response = new Response();

        $tenantMock = $this->tenantProvider->expects('getByActiveSlug')->never()->getMock();
        $this->tenant = new Tenant(self::TENANT_HOST, $tenantMock);

        $response = $this->tenant->handle($request, function () use ($response) { return $response; });

        $this->assertSame(BaseResponse::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame(self::HOME_URL, $response->getTargetUrl());
    }

    public function provideBadHosts(): array
    {
        return [
            ['localhost'],
            ['tenant.host'],
            ['3f4f.tenant.host'],
            ['test-tenant.tenant.host'],
        ];
    }

    /**
     * @group Unit
     */
    #[NoReturn] public function test_throws_exception_with_invalid_tenant_and_ajax(): void
    {
        $request = $this->createRequest('test.tenant.test.local', true);
        $response = new Response();

        $tenantMock = $this->setUpTenantMock('getByActiveSlug', 'test', null);
        $this->tenant = new Tenant(self::TENANT_HOST, $tenantMock);

        $this->expectException(TenantException::class);
        $this->expectExceptionMessage("Invalid Tenant 'test'");

        $this->tenant->handle($request, function () use ($response) { return $response; });
    }

    /**
     * @group Unit
     */
    #[NoReturn] public function test_throws_exception_with_invalid_tenant(): void
    {
        $request = $this->createRequest('test.tenant.test.local');
        $response = new Response();

        $tenantMock = $this->setUpTenantMock('getByActiveSlug', 'test', null);
        $this->tenant = new Tenant(self::TENANT_HOST, $tenantMock);

        $response = $this->tenant->handle($request, function () use ($response) { return $response; });

        $this->assertSame(BaseResponse::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame(self::HOME_URL, $response->getTargetUrl());
    }

    /**
     * @group Unit
     */
    #[NoReturn] public function test_chains_middleware_with_tenant(): void
    {
        $request = $this->createRequest('test.tenant.test.local');
        $response = new Response();

        $tenant = new TenantEntity('test', 'test', 'Test');
        $tenantMock = $this->setUpTenantMock('getByActiveSlug', 'test', $tenant);
        $this->tenant = new Tenant(self::TENANT_HOST, $tenantMock);

        $response = $this->tenant->handle($request, function () use ($response) {
            return $response->setContent('some-content');
        });

        $this->assertSame('some-content', $response->getContent());
        $this->assertInstanceOf(TenantEntity::class, $request->attributes->get('tenant'));
    }

    private function createRequest(string $host = 'test.local', bool $isXmlHttpRequest = false): Request
    {
        return Request::create(uri: '/', server: [
            'SERVER_NAME' => $host,
            'HTTP_HOST' => $host,
            'HTTP_X-Requested-With' => $isXmlHttpRequest ? 'XMLHttpRequest' : null,
        ]);
    }

    /**
     * @param string $method
     * @param string $tenantName
     * @param mixed $returnValue
     * @return TenantProvider
     */
    private function setUpTenantMock(string $method, string $tenantName, mixed $returnValue): TenantProvider
    {
        return $this->tenantProvider
            ->expects($method)
            ->once()
            ->with($tenantName)
            ->andReturns($returnValue)
            ->getMock();
    }
}
