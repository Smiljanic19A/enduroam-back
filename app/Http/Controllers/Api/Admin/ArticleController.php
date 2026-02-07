<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ArticleController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $articles = Article::orderByDesc('date')->get();

        return ArticleResource::collection($articles);
    }

    public function store(StoreArticleRequest $request): ArticleResource
    {
        $article = Article::create($request->validated());

        return new ArticleResource($article);
    }

    public function show(Article $article): ArticleResource
    {
        return new ArticleResource($article);
    }

    public function update(UpdateArticleRequest $request, Article $article): ArticleResource
    {
        $article->update($request->validated());

        return new ArticleResource($article->fresh());
    }

    public function destroy(Article $article): JsonResponse
    {
        $article->delete();

        return response()->json(['message' => 'Article deleted successfully.']);
    }
}
