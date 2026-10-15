<?php

namespace App\Http\Requests;

use App\Models\Review;
use App\Models\Staff;
use App\Models\StoreNote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    protected function prepareForValidation(): void
    {
        foreach (['category', 'staff_id', 'review_id'] as $field) {
            if ($this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }
    }

    public function rules(): array
    {
        $storeIds = auth('web')->user()?->accessibleStores()->pluck('id')->all() ?? [];

        return [
            'store_id' => ['required', 'integer', Rule::in($storeIds)],
            'staff_id' => ['nullable', 'integer', 'exists:staff,id'],
            'review_id' => ['nullable', 'integer', 'exists:reviews,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'category' => ['nullable', 'string', Rule::in(StoreNote::CATEGORIES)],
            'status' => ['required', 'string', Rule::in(StoreNote::STATUSES)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $storeId = (int) $this->input('store_id');

            if ($this->filled('staff_id')) {
                $staff = Staff::find($this->input('staff_id'));
                if (! $staff || (int) $staff->store_id !== $storeId) {
                    $validator->errors()->add('staff_id', __('messages.note_staff_store_mismatch'));
                }
            }

            if ($this->filled('review_id')) {
                $review = Review::find($this->input('review_id'));
                if (! $review || (int) $review->store_id !== $storeId) {
                    $validator->errors()->add('review_id', __('messages.note_review_store_mismatch'));
                }
            }
        });
    }
}
