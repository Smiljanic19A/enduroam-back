<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class VideoController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $videos = Video::ordered()->get();

        return VideoResource::collection($videos);
    }

    public function featured(): VideoResource|JsonResponse
    {
        $video = Video::where('is_featured', true)->first();

        if (! $video) {
            return response()->json(['message' => 'No featured video set.'], 204);
        }

        return new VideoResource($video);
    }
}
