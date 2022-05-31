<?php

declare(strict_types=1);

namespace App\Console\Commands\Tenant;

use App\Entities\Tenant;
use App\Modules\Tenant\Services\UpdateTenant;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Console\Command;

class SoftDeleteDeactivated extends Command
{
    protected $signature = 'tenant:soft-delete-deactivated';

    protected $description = 'Soft Delete Deactivated Tenants';

    /**
     * @param EntityManagerInterface $em
     * @param UpdateTenant $updateTenant
     */
    public function __construct(private EntityManagerInterface $em, private UpdateTenant $updateTenant)
    {
        parent::__construct();
    }

    public function handle()
    {
        $tenants = $this->getDeactivatedTenants();

        if (!count($tenants)) {
            $this->line('<comment>No tenants to delete</comment>');
        }

        foreach ($tenants as $tenant) {
            $this->line(sprintf('<comment>Soft deleting tenant ID</comment> <info>%s</info>', $tenant->getId()->toString()));
            $this->updateTenant->softDelete($tenant, new DateTimeImmutable());
        }
    }

    /**
     * @return Tenant[]
     */
    private function getDeactivatedTenants(): array
    {
        $query = $this->em->createQuery(
            'SELECT t FROM App\Entities\Tenant t WHERE t.isActive = 0 AND t.deletedDateTime IS NULL'
        );

        return $query->getResult();
    }
}
