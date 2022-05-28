<?php

declare(strict_types=1);

namespace App\Entities;

/**
 * System roles
 */
enum RoleEnum: string
{
    case ADMIN_ROLE = 'c516fcd2-8b2f-45a9-9f8f-a62307dd80c7';
    case EDITOR_ROLE = '5c46a10a-ca58-40ac-b25c-d03e2fec26c6';
    case USER_ROLE = 'caf531ee-5bd2-400b-b0ba-42c92e1bf689';
}
