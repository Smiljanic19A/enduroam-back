<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SponsorResource;
use App\Models\Sponsor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class SponsorController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $sponsors = Sponsor::ordered()->get();

        return SponsorResource::collection($sponsors);
    }

    public function store(Request $request): SponsorResource
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['required', 'string', 'max:2048'],
            'url' => ['nullable', 'url', 'max:2048'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $sponsor = Sponsor::create($data);

        return new SponsorResource($sponsor);
    }

    public function show(Sponsor $sponsor): SponsorResource
    {
        return new SponsorResource($sponsor);
    }

    public function update(Request $request, Sponsor $sponsor): SponsorResource
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'logo' => ['sometimes', 'string', 'max:2048'],
            'url' => ['nullable', 'url', 'max:2048'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $sponsor->update($data);

        return new SponsorResource($sponsor->fresh());
    }

    public function destroy(Sponsor $sponsor): JsonResponse
    {
        $sponsor->delete();

        return response()->json(['message' => 'Sponsor deleted successfully.']);
    }
}
