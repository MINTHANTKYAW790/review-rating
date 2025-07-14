<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return auth()->check();
    // }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            
            'phone' => 'nullable|string|max:20',
            
            'email' => 'nullable|email|max:255',
            
            'address' => 'nullable|string',
            
            'store_description' => 'nullable|string',
            
            'logo_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', 
        ];
    }
}