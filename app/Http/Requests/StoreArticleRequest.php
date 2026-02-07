<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['required', 'string'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'string', 'max:2048'],
            'date' => ['required', 'date'],
            'author' => ['nullable', 'string', 'max:255'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
