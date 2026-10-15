<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillingSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'kbz_number' => ['nullable', 'string', 'max:50'],
            'wave_number' => ['nullable', 'string', 'max:50'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'instructions' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
