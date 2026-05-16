<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'total_gb' => ['required', 'integer', 'min:1', 'max:10000'],
            'expiry_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'tg_id' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
