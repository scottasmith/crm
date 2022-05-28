<?php

declare(strict_types=1);

namespace App\Modules\Auth;

use App\Entities\Tenant;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TenantProvider
{
    private ?EntityRepository $tenantRepository = null;

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function getBySlug(string $slug): ?Tenant
    {
        return $this->getTenantRepository()->findOneBy(['slug' => $slug, 'isActive' => 1]);
    }

    private function getTenantRepository(): EntityRepository
    {
        if (!$this->tenantRepository) {
            $this->tenantRepository = $this->em->getRepository(Tenant::class);
        }

        return $this->tenantRepository;
    }
}
