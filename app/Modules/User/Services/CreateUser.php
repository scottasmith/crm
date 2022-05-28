<?php

declare(strict_types=1);

namespace App\Modules\User\Services;

use App\Entities\Authentication;
use App\Entities\Role;
use App\Entities\RoleEnum;
use App\Entities\Tenant;
use App\Entities\User;
use App\Modules\User\Services\Exception\CreateUserException;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Hashing\HashManager;

class CreateUser
{
    /**
     * @param EntityManagerInterface $em
     * @param HashManager $hashManager
     */
    public function __construct(private EntityManagerInterface $em, private HashManager $hashManager)
    {
    }

    /**
     * @param Tenant $tenant
     * @param string $email
     * @param string $password
     * @param RoleEnum $roleId
     * @return User
     * @throws CreateUserException
     */
    public function createUser(Tenant $tenant, string $email, string $password, RoleEnum $roleId): User
    {
        $securePassword = $this->hashManager->make($password);

        $user = new User($tenant, $email);
        $user->getRoles()->add($this->getRole($roleId));

        $auth = new Authentication($user, $securePassword);

        $this->em->persist($user);
        $this->em->persist($auth);
        $this->em->flush();

        return $user;
    }

    /**
     * @param RoleEnum $roleId
     * @return Role
     * @throws CreateUserException
     */
    private function getRole(RoleEnum $roleId): Role
    {
        $role = $this->em->getRepository(Role::class)->find($roleId->value);
        if (!$role) {
            throw CreateUserException::createGetAdminRoleNotFound();
        }

        return $role;
    }
}
