<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class PageController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $pages = Page::visible()->ordered()->get();

        return PageResource::collection($pages);
    }
}
