<?php

declare(strict_types=1);

namespace App\Modules\Tenant\Services;

use App\Entities\Tenant;
use App\Entities\TenantAuthProvider;
use App\Entities\TenantAuthProviderType;
use App\Entities\TenantAuthProviderTypeIdEnum;
use App\Modules\Tenant\Services\Exception\CreateTenantException;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Str;

class CreateTenant
{
    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @param string $name
     * @param string $description
     * @return Tenant|null
     */
    public function create(string $name, string $description): ?Tenant
    {
        $tenant = new Tenant($name, Str::slug($name), $description);
        $this->em->persist($tenant);
        $this->em->flush();

        return $tenant;
    }

    /**
     * @param Tenant $tenant
     * @param TenantAuthProviderTypeIdEnum $authProviderTypeId
     * @param array|null $options
     * @return void
     * @throws CreateTenantException
     */
    public function setAuthProvider(
        Tenant $tenant,
        TenantAuthProviderTypeIdEnum $authProviderTypeId,
        ?array $options = null
    ): void {
        $authProviderType = $this->getAuthProviderType($authProviderTypeId);
        $authProvider = new TenantAuthProvider($tenant, $authProviderType, $options ?? []);

        $this->em->persist($authProvider);
        $this->em->flush();
    }

    /**
     * @param TenantAuthProviderTypeIdEnum $authProviderTypeId
     * @return TenantAuthProviderType
     * @throws CreateTenantException
     */
    private function getAuthProviderType(TenantAuthProviderTypeIdEnum $authProviderTypeId): TenantAuthProviderType
    {
        $authProviderType = $this->em->getRepository(TenantAuthProviderType::class)->find($authProviderTypeId->value);
        if (!$authProviderType) {
            throw CreateTenantException::createAuthTypeNotFound($authProviderTypeId);
        }

        return $authProviderType;
    }
}
