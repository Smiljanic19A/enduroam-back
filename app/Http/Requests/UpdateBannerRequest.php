<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'string', 'max:50'],
            'image' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'text' => ['sometimes', 'nullable', 'string'],
            'text_position' => ['sometimes', 'nullable', 'string', 'in:left,center,right'],
            'cta_text' => ['sometimes', 'nullable', 'string', 'max:255'],
            'cta_href' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
