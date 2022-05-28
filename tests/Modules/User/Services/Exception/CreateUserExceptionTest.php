<?php

declare(strict_types=1);

namespace Tests\Modules\User\Services\Exception;

use App\Entities\RoleEnum;
use App\Modules\User\Services\Exception\CreateUserException;
use JetBrains\PhpStorm\NoReturn;
use PHPUnit\Framework\TestCase;

class CreateUserExceptionTest extends TestCase
{
    /**
     * @group Unit
     */
    #[NoReturn] public function test_create_get_admin_role_not_found_returns_expected_exception()
    {
        $exception = CreateUserException::createGetAdminRoleNotFound()->withMetaData(['a' => 'b']);

        $this->assertSame(
            sprintf("System Admin Role not found with ID '%s'", RoleEnum::ADMIN_ROLE->value),
            $exception->getMessage()
        );
        $this->assertSame(CreateUserException::ERROR_ADMIN_ROLE_NOT_FOUND, $exception->getApplicationCode());
        $this->assertSame(['a' => 'b'], $exception->getMetaData());
    }
}
