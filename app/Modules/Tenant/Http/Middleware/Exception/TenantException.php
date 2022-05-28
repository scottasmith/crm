<?php

declare(strict_types=1);

namespace App\Modules\Tenant\Http\Middleware\Exception;

use ScottSmith\ErrorHandler\Exception\ApplicationException;

class TenantException extends ApplicationException
{
    const ERROR_INVALID_REQUEST = 1;
    const ERROR_INVALID_TENANT = 2;

    public static function createInvalidUrl(): static
    {
        return new static('Invalid Request', self::ERROR_INVALID_REQUEST);
    }

    public static function createInvalidTenant(string $tenantName): static
    {
        return new static(
            "Invalid Tenant '%s'",
            self::ERROR_INVALID_TENANT,
            null,
            $tenantName
        );
    }
}
