<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class VideoController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $videos = Video::ordered()->get();

        return VideoResource::collection($videos);
    }
}
