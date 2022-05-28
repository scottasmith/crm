<?php

declare(strict_types=1);

namespace Tests\Modules\User\Services;

use App\Entities\RoleEnum;
use App\Entities\Tenant;
use App\Modules\User\Services\CreateUser;
use Illuminate\Hashing\HashManager;
use ScottSmith\Doctrine\Integration\Testing\DatabaseHelperTrait;
use Tests\Modules\Tenant\TenantTestHelpers;
use Tests\Modules\User\UserTestHelpers;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use DatabaseHelperTrait;
    use TenantTestHelpers;
    use UserTestHelpers;

    private HashManager $hashManager;
    private CreateUser $createUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hashManager = $this->app->get(HashManager::class);
        $this->createUser = new CreateUser($this->getDoctrineEntityManager(), $this->hashManager);
    }

    /**
     * @group Integration
     */
    public function test_create_admin_user_creates_user()
    {
        $tenantId = '5dca843c-70a2-4bde-ad0f-04bd2b072e6e';
        $tenantName = 'test-tenant';
        $userEmail = 'test@example.org';

        $this->createDbTenant($tenantId, $tenantName);

        $tenant = $this->getDoctrineEntityManager()->getRepository(Tenant::class)->find($tenantId);

        $user = $this->createUser->createUser($tenant, $userEmail, 'abc123', RoleEnum::ADMIN_ROLE);

        $this->assertTenantCreatedInDb($tenantId, $tenantName, $tenantName . ' Description');
        $this->assertUserCreatedInDb($user->getId()->toString(), $tenantId, $userEmail);

        $this->assertDoctrineRowInDb('user_roles', [
            'user_id' => $user->getId()->toString(),
            'role_id' => RoleEnum::ADMIN_ROLE->value,
        ]);

        $authRow = $this->fetchFromDoctrine('authentication', ['user_id' => $user->getId()->toString()]);
        $this->assertTrue($this->hashManager->check('abc123', $authRow['password']));
    }
}
