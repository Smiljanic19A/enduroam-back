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
            'mobile_image' => ['nullable', 'string', 'max:2048'],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'text' => ['sometimes', 'nullable', 'string'],
            'text_position' => ['sometimes', 'nullable', 'string', 'in:top-left,top-center,top-right,center-left,center,center-right,bottom-left,bottom-center,bottom-right'],
            'text_color' => ['sometimes', 'nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'title_color' => ['sometimes', 'nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'title_size' => ['sometimes', 'string', 'in:small,medium,large'],
            'focal_point' => ['sometimes', 'nullable', 'string', 'in:top-left,top-center,top-right,center-left,center,center-right,bottom-left,bottom-center,bottom-right'],
            'overlay_opacity' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'image_fit' => ['sometimes', 'string', 'in:cover,contain'],
            'animation' => ['sometimes', 'nullable', 'array'],
            'animation.type' => ['required_with:animation', 'string', 'in:zoom,pan,parallax,static'],
            'animation.direction' => ['required_with:animation', 'string', 'in:in,out,left,right,up,down'],
            'animation.speed' => ['required_with:animation', 'numeric', 'min:2', 'max:20'],
            'animation.delay' => ['sometimes', 'numeric', 'min:0', 'max:3'],
            'cta_text' => ['sometimes', 'nullable', 'string', 'max:255'],
            'cta_href' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
