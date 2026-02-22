<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $users = User::where('is_admin', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request): UserResource
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'is_admin' => true,
            'role' => $request->validated('role'),
        ]);

        return new UserResource($user);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $data = $request->validated();

        // Prevent changing own role
        if ($request->user()->id === $user->id) {
            unset($data['role']);
        }

        // Only update password if provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        unset($data['password_confirmation']);

        $user->update($data);

        return new UserResource($user->fresh());
    }

    public function destroy(User $user): JsonResponse
    {
        // Prevent self-deletion
        if (request()->user()->id === $user->id) {
            return response()->json([
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        // Revoke all Sanctum tokens
        $user->tokens()->delete();

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
