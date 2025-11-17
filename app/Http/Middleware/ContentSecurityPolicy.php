<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only add CSP to HTML responses
        if ($response->headers->get('Content-Type') && 
            str_contains($response->headers->get('Content-Type'), 'text/html')) {
            
            $cspHeader = $this->buildCspHeader();
            $response->headers->set('Content-Security-Policy', $cspHeader);
        }

        return $response;
    }

    /**
     * Build Content Security Policy header
     *
     * @return string
     */
    private function buildCspHeader(): string
    {
        $directives = [
            // Default policy: restrict to same origin by default
            "default-src 'self'",
            
            // Script sources: allow same origin, trusted CDN for CKEditor
            "script-src 'self' 'unsafe-inline' https://cdn.ckeditor.com",
            
            // Style sources: allow same origin, inline styles (required for rich text editor), trusted CDN
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.ckeditor.com",
            
            // Image sources: allow same origin, data URIs (for inline images), trusted image hosts
            "img-src 'self' data: https: blob:",
            
            // Font sources: allow same origin and Google Fonts
            "font-src 'self' https://fonts.gstatic.com",
            
            // Connect sources: allow same origin and required APIs
            "connect-src 'self'",
            
            // Frame sources: deny all frames (prevent clickjacking)
            "frame-src 'none'",
            "frame-ancestors 'none'",
            
            // Object sources: deny plugins and embedded content
            "object-src 'none'",
            
            // Base URI: restrict to same origin
            "base-uri 'self'",
            
            // Form actions: restrict to same origin
            "form-action 'self'",
            
            // Manifest: allow same origin
            "manifest-src 'self'",
            
            // Media sources: allow same origin and data URIs
            "media-src 'self' data:",
            
            // Worker sources: restrict to same origin
            "worker-src 'self'",
            
            // Upgrade insecure requests in production
            app()->environment('production') ? "upgrade-insecure-requests" : null,
        ];

        // Filter out null values and join with semicolons
        $cspDirectives = array_filter($directives);
        
        return implode('; ', $cspDirectives);
    }
}