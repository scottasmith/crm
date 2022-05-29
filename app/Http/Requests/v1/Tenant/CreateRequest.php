<?php

namespace App\Http\Requests\v1\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
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
        ];
    }
}
