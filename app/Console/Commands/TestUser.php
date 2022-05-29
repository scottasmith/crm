<?php

namespace App\Console\Commands;

use App\Entities\Authentication;
use App\Entities\Role;
use App\Entities\RoleEnum;
use App\Entities\Tenant;
use App\Entities\TenantAuthProviderTypeIdEnum;
use App\Entities\User;
use App\Modules\Tenant\Services\CreateTenant;
use App\Modules\Tenant\Services\Exception\CreateTenantException;
use App\Modules\User\Services\CreateUser;
use App\Modules\User\Services\Exception\CreateUserException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Faker\Generator;
use Faker\Guesser\Name;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
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
     * @param CreateTenant $createTenant
     * @param CreateUser $createUser
     * @return int
     * @throws CreateTenantException
     * @throws CreateUserException
     * @throws BindingResolutionException
     */
    public function handle(CreateTenant $createTenant, CreateUser $createUser)
    {
        /** @var Generator $faker */
        $faker = Container::getInstance()->make(Generator::class);

        $company = $faker->unique()->company();
        $tenant = $createTenant->create($company, $company . ' Description');
        $createTenant->setAuthProvider($tenant, TenantAuthProviderTypeIdEnum::PROVIDER_TYPE_BASIC_ID, ['some' => 'options']);

        $user = $createUser->createUser($tenant, $faker->email(), 'abc123', RoleEnum::ADMIN_ROLE);

        dd($user);

        return 0;
    }
}
