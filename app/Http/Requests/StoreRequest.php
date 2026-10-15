<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        ];

        if (auth('web')->user()?->can('edit_stores') || auth('web')->user()?->can('create_stores')) {
            $rules['manager_id'] = ['nullable', 'integer', Rule::exists('users', 'id')];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('manager_id')) {
                return;
            }

            $manager = User::find($this->input('manager_id'));

            if (! $manager?->hasRole('manager') || (int) $manager->created_by !== (int) auth()->id()) {
                $validator->errors()->add('manager_id', 'The selected manager is invalid.');

                return;
            }

            $store = $this->route('store');
            $allowsManagers = $store
                ? $store->allowsManagers()
                : auth('web')->user()->stores()->exists();

            if (! $allowsManagers) {
                $validator->errors()->add('manager_id', __('messages.manager_not_allowed'));
            }
        });
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('manager_id') === '') {
            $this->merge(['manager_id' => null]);
        }
    }
}
