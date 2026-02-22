<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->is_admin) {
            return response()->json([
                'message' => 'Forbidden. Admin access required.',
            ], 403);
        }

        if ($request->user()->role === 'read' && ! $request->isMethod('get')) {
            return response()->json([
                'message' => 'Write access required.',
            ], 403);
        }

        return $next($request);
    }
}
