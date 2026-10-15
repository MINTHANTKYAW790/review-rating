<?php

namespace App\Http\Requests;

use App\Models\Review;
use Illuminate\Foundation\Http\FormRequest;

class ReviewTriageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $review = $this->route('review');

        return $review instanceof Review && $this->user()?->can('update', $review);
    }

    public function rules(): array
    {
        return [
            'admin_remark' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
