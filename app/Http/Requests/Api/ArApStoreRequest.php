<?php

namespace App\Http\Requests\Api;

use App\Models\ArAp;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ArApStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'uuid'],
            'date' => ['required', 'date'],
            'type' => ['required', Rule::in([ArAp::TYPE_AP, ArAp::TYPE_AR])],
            'nominal' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'max:255'],
        ];
    }
}
