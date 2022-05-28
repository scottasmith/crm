<?php

declare(strict_types=1);

namespace App\Modules\Tenant\Services\Exception;

use App\Entities\TenantAuthProviderTypeIdEnum;
use ScottSmith\ErrorHandler\Exception\ApplicationException;

class CreateTenantException extends ApplicationException
{
    public const ERROR_AUTH_TYPE_NOT_FOUND = 1;

    /**
     * @param TenantAuthProviderTypeIdEnum $type
     * @return static
     */
    public static function createAuthTypeNotFound(TenantAuthProviderTypeIdEnum $type): static
    {
        return new static(
            "Authenticate type '%s' not found",
            static::ERROR_AUTH_TYPE_NOT_FOUND,
            null,
            $type->name
        );
    }
}
