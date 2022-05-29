<?php

declare(strict_types=1);

namespace Tests\Modules\User\Services;

use App\Entities\RoleEnum;
use App\Entities\Tenant;
use App\Modules\User\Services\CreateUser;
use Exception;
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
    public function test_create_user_creates_user2()
    {
        $tenantId = '5dca843c-70a2-4bde-ad0f-04bd2b072e6e';
        $tenantName = 'test-tenant';
        $userEmail = 'test@example.org';

        $this->createDbTenant($tenantId, $tenantName);

        $tenant = $this->getDoctrineEntityManager()->getRepository(Tenant::class)->find($tenantId);

        $details = ['title' => 'Mr', 'givenName' => 'Joe', 'surname' => 'Bloggs'];
        $user = $this->createUser->createUser($tenant, $userEmail, 'abc123', RoleEnum::ADMIN_ROLE, $details);

        $this->assertTenantCreatedInDb($tenantId, $tenantName, $tenantName . ' Description');
        $this->assertUserCreatedInDb($user->getId()->toString(), $tenantId, $userEmail);

        $this->assertDoctrineRowInDb('user_roles', [
            'user_id' => $user->getId()->toString(),
            'role_id' => RoleEnum::ADMIN_ROLE->value,
        ]);

        $authRow = $this->fetchFromDoctrine('authentication', ['user_id' => $user->getId()->toString()]);
        $this->assertTrue($this->hashManager->check('abc123', $authRow['password']));
    }

    /**
     * @group Integration
     */
    public function test_create_user_throws_exception_with_missing_details()
    {
        $tenantId = '5dca843c-70a2-4bde-ad0f-04bd2b072e6e';
        $tenantName = 'test-tenant';
        $userEmail = 'test@example.org';

        $this->createDbTenant($tenantId, $tenantName);

        $tenant = $this->getDoctrineEntityManager()->getRepository(Tenant::class)->find($tenantId);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Required properties missing: title,givenName,surname');
        $user = $this->createUser->createUser($tenant, $userEmail, 'abc123', RoleEnum::ADMIN_ROLE, []);
    }

    /**
     * @group Integration
     */
    public function test_create_user_creates_user_with_details()
    {
        $tenantId = '5dca843c-70a2-4bde-ad0f-04bd2b072e6e';
        $tenantName = 'test-tenant';
        $userEmail = 'test@example.org';

        $this->createDbTenant($tenantId, $tenantName);

        $tenant = $this->getDoctrineEntityManager()->getRepository(Tenant::class)->find($tenantId);

        $details = [
            'title' => 'Mr',
            'givenName' => 'Joe',
            'surname' => 'Bloggs',
            'position' => 'Manager',
            'homeTel' => '123',
            'mobileTel' => '456',
            'workTel' => '789',
            'otherTel' => ['136'],
        ];

        $user = $this->createUser->createUser(
            $tenant,
            $userEmail,
            'abc123',
            RoleEnum::ADMIN_ROLE,
            $details + ['notAField' => 'some-value']
        );

        $dbDetails = $this->fetchFromDoctrine('user_detail', ['user_id' => $user->getId()->toString()]);

        $this->assertSame($details['title'], $dbDetails['title']);
        $this->assertSame($details['givenName'], $dbDetails['given_name']);
        $this->assertSame($details['surname'], $dbDetails['surname']);
        $this->assertSame($details['position'], $dbDetails['position']);
        $this->assertSame($details['homeTel'], $dbDetails['home_tel']);
        $this->assertSame($details['mobileTel'], $dbDetails['mobile_tel']);
        $this->assertSame($details['workTel'], $dbDetails['work_tel']);
        $this->assertSame($details['otherTel'], [$dbDetails['other_tel']]);
        $this->assertArrayNotHasKey('notAField', $dbDetails);
    }
}
