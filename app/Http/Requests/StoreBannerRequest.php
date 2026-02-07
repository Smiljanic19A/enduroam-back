<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'string', 'max:50'],
            'image' => ['required', 'string', 'max:2048'],
            'title' => ['required', 'string', 'max:255'],
            'text' => ['nullable', 'string'],
            'text_position' => ['sometimes', 'string', 'in:left,center,right'],
            'cta_text' => ['nullable', 'string', 'max:255'],
            'cta_href' => ['nullable', 'string', 'max:2048'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
