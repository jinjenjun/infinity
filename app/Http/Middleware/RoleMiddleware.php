<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!$request->user() || !$request->user()->hasAnyRole($roles)) {
            abort(403, '權限不足');
        }

        return $next($request);
    }
}
