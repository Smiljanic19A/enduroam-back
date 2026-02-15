<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return NotificationResource::collection(
            $this->notificationService->all()
        );
    }

    public function destroy(Notification $notification): JsonResponse
    {
        $notification->delete();

        return response()->json(['message' => 'Notification dismissed.']);
    }

    public function destroyAll(): JsonResponse
    {
        Notification::truncate();

        return response()->json(['message' => 'All notifications dismissed.']);
    }
}
