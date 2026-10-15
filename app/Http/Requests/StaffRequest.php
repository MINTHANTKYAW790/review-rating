<?php

namespace App\Http\Requests;

use App\Models\Store;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        $storeIds = auth('web')->user()->accessibleStores()->pluck('id')->all();

        return [
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'image_url' => 'nullable|image|max:2048',
            'store_id' => ['required', Rule::in($storeIds)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        if ($this->route('staff')) {
            return;
        }

        $validator->after(function (Validator $validator) {
            $store = Store::find($this->input('store_id'));

            if ($store && ! $store->hasStaffCapacity()) {
                $validator->errors()->add('store_id', __('messages.staff_limit_reached'));
            }
        });
    }
}
