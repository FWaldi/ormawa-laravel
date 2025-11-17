<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OptimizeCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add caching headers for static assets
        if ($request->is('assets/*') || $request->is('build/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000'); // 1 year
        }

        // Add caching headers for images
        if ($request->is('images/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=2592000'); // 30 days
        }

        // Add compression header
        if (extension_loaded('zlib') && !ob_get_level()) {
            ini_set('zlib.output_compression', 'On');
            ini_set('zlib.output_compression_level', '6');
        }

        return $response;
    }
}