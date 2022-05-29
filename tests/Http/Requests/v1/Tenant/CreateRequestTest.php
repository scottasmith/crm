<?php

declare(strict_types=1);

namespace Tests\Http\Requests\v1\Tenant;

use App\Http\Requests\v1\Tenant\CreateRequest;
use PHPUnit\Framework\TestCase;

class CreateRequestTest extends TestCase
{
    public function test_rules_returns_expected_array()
    {
        $request = new CreateRequest();
        $this->assertSame([
            'tenant.name' => 'required|string|max:255',
            'tenant.description' => 'required|string|max:255',
            'user.email' => 'required|string|max:255',
            'user.title' => 'required|string|max:255',
            'user.givenName' => 'required|string|max:255',
            'user.surname' => 'required|string|max:255',
            'user.password' => 'required|string|max:255',
            'user.phone' => 'nullable',
            'user.phone.home' => 'nullable|string|max:255',
            'user.phone.mobile' => 'nullable|string|max:255',
            'user.phone.work' => 'nullable|string|max:255',
            'user.phone.other' => 'nullable|string|max:255',
        ], $request->rules());
    }
}
