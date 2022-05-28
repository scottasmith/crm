<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Middleware\Exception;

use ScottSmith\ErrorHandler\Exception\ApplicationException;

class TenantException extends ApplicationException
{
    const ERROR_INVALID_REQUEST = 1;

    public static function createInvalidUrl(): TenantException
    {
        return new static('Invalid request', self::ERROR_INVALID_REQUEST);
    }

    public static function createInvalidTenant(string $tenantName): TenantException
    {
        return new static(
            "Invalid Tenant '%s'",
            self::ERROR_INVALID_REQUEST,
            null,
            $tenantName
        );
    }
}
