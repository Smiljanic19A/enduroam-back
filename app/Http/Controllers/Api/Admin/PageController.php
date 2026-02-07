<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class PageController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $pages = Page::ordered()->get();

        return PageResource::collection($pages);
    }

    public function update(Request $request, Page $page): PageResource
    {
        $data = $request->validate([
            'is_visible' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $page->update($data);

        return new PageResource($page->fresh());
    }
}
