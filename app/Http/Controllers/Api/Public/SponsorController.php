<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\SponsorResource;
use App\Models\Sponsor;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class SponsorController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $sponsors = Sponsor::ordered()->get();

        return SponsorResource::collection($sponsors);
    }
}
