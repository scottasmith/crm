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

class Update extends Controller
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
     * @param UpdateRequest $request
     * @return JsonResponse|Response
     */
    public function __invoke(string $id, UpdateRequest $request): JsonResponse|Response
    {
        $data = $request->validationData();

        $tenant = $this->provider->getById($id);
        if (!$tenant) {
            return new JsonResponse([
                'message' => sprintf('Tenant ID %s not found', $id),
            ], SymfonyResponse::HTTP_NOT_FOUND);
        }

        $updatedFields = 0;

        if (isset($data['description'])) {
            $this->updateTenant->updateDescription($tenant, $data['description']);
            $updatedFields++;
        }

        if (0 === $updatedFields) {
            return new Response('', SymfonyResponse::HTTP_I_AM_A_TEAPOT);
        }

        return new Response('', SymfonyResponse::HTTP_ACCEPTED);
    }
}
