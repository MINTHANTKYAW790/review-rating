<?php

namespace App\Http\Requests;

use App\Models\Store;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        $ownedStoreIds = auth('web')->user()?->stores()->pluck('id')->all() ?? [];
        $isManager = collect($this->input('roles', []))->contains('manager');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', Rule::in($this->allowedRoleNames())],
            'store_id' => [
                Rule::requiredIf($isManager),
                'nullable',
                'integer',
                Rule::in($ownedStoreIds),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! collect($this->input('roles', []))->contains('manager') || ! $this->filled('store_id')) {
                return;
            }

            $store = Store::find($this->input('store_id'));

            if ($store && ! $store->allowsManagers()) {
                $validator->errors()->add('store_id', __('messages.manager_not_allowed'));
            }
        });
    }

    private function allowedRoleNames(): array
    {
        if (auth('web')->user()?->isSuperAdmin()) {
            return ['admin', 'manager'];
        }

        return ['manager'];
    }
}
