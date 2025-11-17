<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageCache
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
        // Only cache GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Don't cache authenticated pages
        if (auth()->check()) {
            return $next($request);
        }

        // Generate cache key
        $cacheKey = 'page_' . md5($request->fullUrl());

        // Try to get cached response
        if (Cache::has($cacheKey)) {
            $cachedContent = Cache::get($cacheKey);
            return response($cachedContent)
                ->header('X-Cache', 'HIT');
        }

        // Get response and cache it
        $response = $next($request);

        // Only cache successful responses
        if ($response->getStatusCode() === 200) {
            $content = $response->getContent();
            Cache::put($cacheKey, $content, now()->addHours(6)); // Cache for 6 hours
            $response->header('X-Cache', 'MISS');
        }

        return $response;
    }
}