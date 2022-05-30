<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Responses\v1\Tenant\GetResponse;
use App\Modules\Tenant\Providers\TenantProvider;
use Illuminate\Http\JsonResponse;

class Get extends Controller
{
    /**
     * @param string $id
     * @param TenantProvider $tenantProvider
     * @return GetResponse|JsonResponse
     */
    public function __invoke(string $id, TenantProvider $tenantProvider): GetResponse|JsonResponse
    {
        $tenant = $tenantProvider->getById($id);

        if (!$tenant) {
            return new JsonResponse([
                'message' => sprintf('Tenant ID %s not found', $id),
            ], 404);
        }

        return new GetResponse($tenant);
    }
}
