<?php

declare(strict_types=1);

namespace App\Http\Responses\v1\Tenant;

use App\Entities\Tenant;
use App\Entities\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

class CreateResponse implements Responsable
{
    public function __construct(private Tenant $tenant, private User $user)
    {
    }

    public function toResponse($request): JsonResponse
    {
        return new JsonResponse([
            'tenant' => [
                'id' => $this->tenant->getId()->toString(),
                'name' => $this->tenant->getName(),
            ],
            'user' => [
                'id' => $this->user->getId()->toString(),
            ],
        ]);
    }
}
