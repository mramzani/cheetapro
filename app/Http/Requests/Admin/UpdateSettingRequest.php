<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'xui_base_url' => ['required', 'url', 'max:255'],
            'subscription_base_url' => ['required', 'url', 'max:255'],
            'xui_username' => ['required', 'string', 'max:255'],
            'xui_password' => ['nullable', 'string', 'max:255'],
            'xui_inbound_id' => ['required', 'integer', 'min:1'],
            'price_per_gb' => ['required', 'integer', 'min:0'],
            'default_expiry_days' => ['required', 'integer', 'min:1'],
        ];
    }
}
