<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Tenant\UpdateRequest;
use App\Modules\Tenant\Providers\TenantProvider;
use App\Modules\Tenant\Services\UpdateTenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Deactivate extends Controller
{
    /**
     * @param TenantProvider $provider
     * @param UpdateTenant $updateTenant
     */
    public function __construct(private TenantProvider $provider, private UpdateTenant $updateTenant)
    {
    }

    /**
     * @param string $id
     * @return JsonResponse|Response
     */
    public function __invoke(string $id): JsonResponse|Response
    {
        $tenant = $this->provider->getById($id);
        if (!$tenant) {
            return new JsonResponse([
                'message' => sprintf('Tenant ID %s not found', $id),
            ], SymfonyResponse::HTTP_NOT_FOUND);
        }

        $this->updateTenant->deactivate($tenant);

        return new Response('', SymfonyResponse::HTTP_NO_CONTENT);
    }
}
