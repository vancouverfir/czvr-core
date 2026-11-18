<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\URL;

class ForceMainDomain
{
    public function handle($request, Closure $next)
    {
        if ($request->route()?->getDomain() === null) {
            abort_unless($request->getHost() === config('app.domain'), 404);
        }

        URL::forceRootUrl(config('app.url'));

        return $next($request);
    }
}
