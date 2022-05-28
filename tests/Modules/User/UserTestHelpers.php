<?php

declare(strict_types=1);

namespace Tests\Modules\User;

use Illuminate\Support\Str;

/**
 * @uses void insertDoctrineRow(string $table, array $data)
 * @uses void assertDoctrineRowInDb(string $table, array $queryParams)
 */
trait UserTestHelpers
{
    protected function assertUserCreatedInDb(string $id, string $tenantId, string $email): void
    {
        $this->assertDoctrineRowInDb('user', [
            'id' => $id,
            'tenant_id' => $tenantId,
            'email' => $email,
        ]);
    }
}
