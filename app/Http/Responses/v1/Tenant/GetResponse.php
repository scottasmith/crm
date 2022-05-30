<?php

declare(strict_types=1);

namespace App\Http\Responses\v1\Tenant;

use App\Entities\Tenant;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

class GetResponse implements Responsable
{
    public function __construct(private Tenant $tenant)
    {
    }

    public function toResponse($request): JsonResponse
    {
        return new JsonResponse([
            'id' => $this->tenant->getId(),
            'name' => $this->tenant->getName(),
            'description' => $this->tenant->getDescription(),
        ]);
    }
}
