<?php

namespace App\Console\Commands;

use App\Entities\Authentication;
use App\Entities\Role;
use App\Entities\Tenant;
use App\Entities\User;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Faker\Generator;
use Faker\Guesser\Name;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class TestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test User';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws ORMException
     */
    public function handle(EntityManagerInterface $em)
    {
        $conn = $em->getConnection();

        /** @var Generator $faker */
        $faker = Container::getInstance()->make(Generator::class);
        $tenantId = $this->setup($conn, $faker);

        $tenant = $em->getRepository(Tenant::class)->findOneBy(['id' => $tenantId]);
        if (!$tenant) {
            throw new \RuntimeException('Failed to find Auth');
        }

        $role = $em->getRepository(Role::class)->findOneBy(['name' => 'user', 'tenant' => null]);
        if (!$role) {
            throw new \RuntimeException('Failed to find Role by the name of some_role');
        }

        $user = new User($tenant, $faker->email());
        $auth = new Authentication($user, 'abc123');

        $user->getRoles()->add($role);

        $em->persist($user);
        $em->persist($auth);
        $em->flush();

        $userId = $user->getId()->toString();

        $users = $em->getRepository(User::class)->findAll();

        dd($user);

        return 0;
    }

    private function setup(Connection $conn, Generator $faker): string
    {
        $authProviderTypeId = '8c8f3f38-5274-4327-8504-a4cb1a048852';
        $authProviderId = Uuid::uuid4();
        $tenantId = Uuid::uuid4();

        $stmt = $conn->prepare('
            INSERT IGNORE INTO tenant (id, name, slug, description, is_active, created_datetime, updated_datetime)
            VALUES (:id, :name, :slug, :description, 1, now(), now())
        ');

        $company = $faker->unique()->company();

        $stmt->executeQuery([
            'id' => $tenantId,
            'name' => $company,
            'slug' => Str::slug($company),
            'description' => $company . ' description'
        ]);

        $stmt = $conn->prepare('
            INSERT IGNORE INTO tenant_auth_provider (id, tenant_id, provider_type_id, options, created_datetime, updated_datetime)
            VALUES (:id, :tenantId, :providerTypeId, :options, now(), now())
        ');

        $stmt->executeQuery([
            'id' => $authProviderId,
            'tenantId' => $tenantId,
            'providerTypeId' => $authProviderTypeId,
            'options' => json_encode(['some' => 'options']),
        ]);

        return $tenantId;
    }
}
