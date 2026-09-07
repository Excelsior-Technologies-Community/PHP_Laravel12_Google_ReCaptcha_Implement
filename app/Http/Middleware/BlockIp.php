<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $blocked = BlockedIp::where('ip_address', $request->ip())
            ->where('banned_until', '>', now())
            ->exists();

        if ($blocked) {
            abort(403, 'Your IP has been temporarily blocked due to suspicious activity.');
        }

        return $next($request);
    }
}
