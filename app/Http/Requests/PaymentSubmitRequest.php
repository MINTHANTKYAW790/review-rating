<?php

namespace App\Http\Requests;

use App\Models\Payment;
use App\Models\Plan;
use App\Services\BillingService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PaymentSubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->user()?->can('buy_plans') ?? false;
    }

    public function rules(): array
    {
        $storeIds = auth('web')->user()?->stores()->pluck('id')->all() ?? [];
        $paidPlanIds = Plan::paid()->pluck('id')->all();

        return [
            'intent' => ['required', Rule::in([BillingService::INTENT_ADD_STORE, BillingService::INTENT_EXTEND])],
            'plan_id' => ['required', 'integer', Rule::in($paidPlanIds)],
            'store_ids' => ['nullable', 'array'],
            'store_ids.*' => ['integer', Rule::in($storeIds)],
            'method' => ['required', Rule::in([Payment::METHOD_KBZ, Payment::METHOD_WAVE])],
            'reference' => ['required', 'string', 'max:100'],
            'screenshot' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'owner_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->input('intent') !== BillingService::INTENT_EXTEND) {
                return;
            }

            if (! $this->filled('store_ids')) {
                $validator->errors()->add('store_ids', __('messages.select_stores_to_extend'));
            }
        });
    }

    public function resolvedPlan(): Plan
    {
        if ($this->input('intent') === BillingService::INTENT_ADD_STORE) {
            $plan = Plan::pro();

            if ($plan) {
                return $plan;
            }
        }

        return Plan::query()->findOrFail($this->integer('plan_id'));
    }
}
