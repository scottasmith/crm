<?php

declare(strict_types=1);

namespace App\Modules\Tenant\Services;

use App\Entities\Tenant;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class UpdateTenant
{
    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @param Tenant $tenant
     * @param string $description
     * @return void
     */
    public function updateDescription(Tenant $tenant, string $description): void
    {
        $tenant->setDescription($description);
        $this->em->persist($tenant);
        $this->em->flush();
    }

    public function deactivate(Tenant $tenant)
    {
        $tenant->setIsActive(false);
        $this->em->persist($tenant);
        $this->em->flush();
    }

    public function softDelete(Tenant $tenant, DateTimeImmutable $dateTime)
    {
        $tenant->setDeletedDateTime($dateTime);
        $this->em->persist($tenant);
        $this->em->flush();
    }
}
