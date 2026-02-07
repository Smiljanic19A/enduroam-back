<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class FaqController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $faqs = Faq::ordered()->get();

        return FaqResource::collection($faqs);
    }

    public function store(Request $request): FaqResource
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $faq = Faq::create($data);

        return new FaqResource($faq);
    }

    public function show(Faq $faq): FaqResource
    {
        return new FaqResource($faq);
    }

    public function update(Request $request, Faq $faq): FaqResource
    {
        $data = $request->validate([
            'question' => ['sometimes', 'string', 'max:500'],
            'answer' => ['sometimes', 'string'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $faq->update($data);

        return new FaqResource($faq->fresh());
    }

    public function destroy(Faq $faq): JsonResponse
    {
        $faq->delete();

        return response()->json(['message' => 'FAQ deleted successfully.']);
    }
}
