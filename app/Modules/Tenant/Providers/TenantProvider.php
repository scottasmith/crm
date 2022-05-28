<?php

declare(strict_types=1);

namespace App\Modules\Tenant\Providers;

use App\Entities\Tenant;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TenantProvider
{
    private ?EntityRepository $tenantRepository = null;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @param string $slug
     * @return Tenant|null
     */
    public function getBySlug(string $slug): ?Tenant
    {
        return $this->getTenantRepository()->findOneBy(['slug' => $slug, 'isActive' => 1]);
    }

    /**
     * @return EntityRepository
     */
    private function getTenantRepository(): EntityRepository
    {
        if (!$this->tenantRepository) {
            $this->tenantRepository = $this->em->getRepository(Tenant::class);
        }

        return $this->tenantRepository;
    }
}