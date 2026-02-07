<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ArticleController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $articles = Article::published()
            ->orderByDesc('date')
            ->get();

        return ArticleResource::collection($articles);
    }

    public function show(Article $article): ArticleResource
    {
        return new ArticleResource($article);
    }

    public function related(Article $article): AnonymousResourceCollection
    {
        $related = Article::published()
            ->where('id', '!=', $article->id)
            ->orderByDesc('date')
            ->limit(3)
            ->get();

        return ArticleResource::collection($related);
    }
}
