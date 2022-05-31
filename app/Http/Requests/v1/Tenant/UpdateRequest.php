<?php

declare(strict_types=1);

namespace App\Http\Requests\v1\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'description' => 'nullable|string|max:255',
        ];
    }
}
