<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->user()?->can('manage_plans') ?? false;
    }

    public function rules(): array
    {
        $plan = $this->route('plan');

        return [
            'slug' => [
                $plan?->isProtected() ? 'nullable' : 'required',
                'string',
                'max:64',
                'alpha_dash',
                Rule::unique('plans', 'slug')->ignore($plan?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'price_mmk' => ['required', 'integer', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:0'],
            'max_staff' => ['required', 'integer', 'min:0'],
            'max_managers' => ['required', 'integer', 'min:0'],
            'max_reviews' => ['nullable', 'integer', 'min:1'],
            'is_account_free' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_account_free' => $this->boolean('is_account_free'),
            'is_active' => $this->boolean('is_active'),
            'max_reviews' => $this->filled('max_reviews') ? $this->input('max_reviews') : null,
        ]);
    }
}
