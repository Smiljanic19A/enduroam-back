<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class FaqController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $faqs = Faq::ordered()->get();

        return FaqResource::collection($faqs);
    }
}
