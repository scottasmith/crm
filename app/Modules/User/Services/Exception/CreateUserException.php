<?php

declare(strict_types=1);

namespace App\Modules\User\Services\Exception;

use App\Entities\RoleEnum;
use ScottSmith\ErrorHandler\Exception\ApplicationException;

class CreateUserException extends ApplicationException
{
    const ERROR_ADMIN_ROLE_NOT_FOUND = 1;

    /**
     * @return static
     */
    public static function createGetAdminRoleNotFound(): static
    {
        return new static(
            "System Admin Role not found with ID '%s'",
            static::ERROR_ADMIN_ROLE_NOT_FOUND,
            null,
            RoleEnum::ADMIN_ROLE->value
        );
    }

}
