<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class VideoController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $videos = Video::ordered()->get();

        return VideoResource::collection($videos);
    }

    public function store(Request $request): VideoResource
    {
        $data = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            'title' => ['required', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $video = Video::create($data);

        return new VideoResource($video);
    }

    public function show(Video $video): VideoResource
    {
        return new VideoResource($video);
    }

    public function update(Request $request, Video $video): VideoResource
    {
        $data = $request->validate([
            'url' => ['sometimes', 'url', 'max:2048'],
            'title' => ['sometimes', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $video->update($data);

        return new VideoResource($video->fresh());
    }

    public function destroy(Video $video): JsonResponse
    {
        $video->delete();

        return response()->json(['message' => 'Video deleted successfully.']);
    }
}
