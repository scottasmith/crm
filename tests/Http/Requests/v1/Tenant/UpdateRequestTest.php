<?php

declare(strict_types=1);

namespace Tests\Http\Requests\v1\Tenant;

use App\Http\Requests\v1\Tenant\UpdateRequest;
use PHPUnit\Framework\TestCase;

class UpdateRequestTest extends TestCase
{
    public function test_rules_returns_expected_array()
    {
        $request = new UpdateRequest();
        $this->assertSame([
            'description' => 'nullable|string|max:255',
        ], $request->rules());
    }
}
