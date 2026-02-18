<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'full_description' => ['nullable', 'string'],
            'date' => ['sometimes', 'date'],
            'duration' => ['sometimes', 'integer', 'min:1'],
            'difficulty' => ['sometimes', 'in:easy,intermediate,advanced,expert'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'max:10'],
            'location' => ['sometimes', 'string', 'max:255'],
            'max_participants' => ['sometimes', 'integer', 'min:1'],
            'spots_left' => ['nullable', 'integer', 'min:0'],
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
            'availability_type' => ['sometimes', 'in:all,specific_dates,weekdays'],
            'available_weekdays' => ['nullable', 'array'],
            'available_weekdays.*' => ['integer', 'min:0', 'max:6'],
            'available_dates' => ['sometimes', 'array'],
            'available_dates.*' => ['date'],
            'translations' => ['sometimes', 'array'],
            'translations.*.name' => ['required', 'string', 'max:255'],
            'translations.*.description' => ['required', 'string'],
            'translations.*.full_description' => ['nullable', 'string'],
            'includes.*.translations' => ['sometimes', 'array'],
            'includes.*.translations.*.text' => ['required', 'string', 'max:255'],
        ];
    }
}
