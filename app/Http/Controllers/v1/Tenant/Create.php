<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Tenant;

use App\Entities\RoleEnum;
use App\Entities\TenantAuthProviderTypeIdEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Tenant\CreateRequest;
use App\Http\Responses\v1\Tenant\CreateResponse;
use App\Modules\Tenant\Providers\TenantProvider;
use App\Modules\Tenant\Services\CreateTenant;
use App\Modules\Tenant\Services\Exception\CreateTenantException;
use App\Modules\User\Services\CreateUser;
use App\Modules\User\Services\Exception\CreateUserException;
use Illuminate\Http\JsonResponse;

class Create extends Controller
{
    /**
     * @param CreateRequest $request
     * @param TenantProvider $tenantProvider
     * @param CreateTenant $createTenant
     * @param CreateUser $createUser
     * @return CreateResponse|JsonResponse
     * @throws CreateTenantException
     * @throws CreateUserException
     */
    public function __invoke(
        CreateRequest $request,
        TenantProvider $tenantProvider,
        CreateTenant $createTenant,
        CreateUser $createUser
    ): CreateResponse|JsonResponse {
        $data = $request->validationData();

        if ($tenantProvider->getByName($data['tenant']['name'])) {
            return new JsonResponse([
                'message' => 'Unable to create tenant. Tenant already exists',
                'tenantName' => $data['tenant']['name'],
            ], 400);
        }

        $tenant = $createTenant->create($data['tenant']['name'], $data['tenant']['description']);

        $createTenant->setAuthProvider($tenant, TenantAuthProviderTypeIdEnum::PROVIDER_TYPE_BASIC_ID);

        $user = $createUser->createUser(
            $tenant,
            $data['user']['email'],
            $data['user']['password'],
            RoleEnum::ADMIN_ROLE,
            $data['user']
        );

        return new CreateResponse($tenant, $user);
    }
}
