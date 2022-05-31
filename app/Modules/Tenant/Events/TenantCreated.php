<?php

namespace App\Modules\Tenant\Events;

use App\Entities\Tenant;
use App\Entities\User;

class TenantCreated
{
    /**
     * @param Tenant $tenant
     * @param User $user
     */
    public function __construct(private Tenant $tenant, private User $user)
    {
    }

    /**
     * @return Tenant
     */
    public function getTenant(): Tenant
    {
        return $this->tenant;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
