<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSellerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'alpha_dash', 'max:255', Rule::unique('sellers', 'username')],
            'password' => ['required', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
            'price_per_gb' => ['nullable', 'integer', 'min:0'],
            'xui_inbound_id' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
