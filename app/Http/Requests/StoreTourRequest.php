<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'full_description' => ['nullable', 'string'],
            'duration' => ['required', 'integer', 'min:1'],
            'difficulty' => ['required', 'in:easy,intermediate,advanced,expert'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'max:10'],
            'location' => ['required', 'string', 'max:255'],
            'max_participants' => ['required', 'integer', 'min:1'],
            'featured_image' => ['nullable', 'string', 'max:2048'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'includes' => ['sometimes', 'array'],
            'includes.*.icon' => ['sometimes', 'string', 'max:100'],
            'includes.*.text' => ['required_with:includes', 'string', 'max:255'],
            'includes.*.sort_order' => ['sometimes', 'integer'],
            'images' => ['sometimes', 'array'],
            'images.*.path' => ['required_with:images', 'string', 'max:2048'],
            'images.*.alt' => ['nullable', 'string', 'max:255'],
            'images.*.sort_order' => ['sometimes', 'integer'],
            'unavailable_dates' => ['sometimes', 'array'],
            'unavailable_dates.*' => ['date'],
        ];
    }
}
