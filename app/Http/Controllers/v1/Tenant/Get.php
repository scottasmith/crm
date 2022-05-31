<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Responses\v1\Tenant\GetResponse;
use App\Modules\Tenant\Providers\TenantProvider;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Get extends Controller
{
    /**
     * @param TenantProvider $provider
     */
    public function __construct(private TenantProvider $provider)
    {
    }

    /**
     * @param string $id
     * @return GetResponse|JsonResponse
     */
    public function __invoke(string $id): GetResponse|JsonResponse
    {
        $tenant = $this->provider->getById($id);

        if (!$tenant) {
            return new JsonResponse([
                'message' => sprintf('Tenant ID %s not found', $id),
            ], SymfonyResponse::HTTP_NOT_FOUND);
        }

        return new GetResponse($tenant);
    }
}
