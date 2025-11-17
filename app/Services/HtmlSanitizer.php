<?php

namespace App\Services;

class HtmlSanitizer
{
    /**
     * Allowed HTML tags for announcements
     */
    private static $allowedTags = [
        'p', 'br', 'strong', 'em', 'u', 'i', 'b',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'ul', 'ol', 'li',
        'a', 'img',
        'blockquote', 'code', 'pre',
        'div', 'span'
    ];

    /**
     * Allowed attributes for specific tags
     */
    private static $allowedAttributes = [
        'a' => ['href', 'title', 'target'],
        'img' => ['src', 'alt', 'width', 'height', 'style'],
        'div' => ['style', 'class'],
        'span' => ['style', 'class'],
        'p' => ['style', 'class'],
        'h1' => ['style', 'class'],
        'h2' => ['style', 'class'],
        'h3' => ['style', 'class'],
        'h4' => ['style', 'class'],
        'h5' => ['style', 'class'],
        'h6' => ['style', 'class'],
        'ul' => ['style', 'class'],
        'ol' => ['style', 'class'],
        'li' => ['style', 'class'],
        'blockquote' => ['style', 'class'],
        'code' => ['style', 'class'],
        'pre' => ['style', 'class'],
    ];

    /**
     * Sanitize HTML content to prevent XSS attacks
     *
     * @param string $html
     * @return string
     */
    public static function sanitize($html)
    {
        return self::clean($html);
    }

    /**
     * Clean HTML content to prevent XSS attacks (alias for sanitize)
     *
     * @param string $html
     * @return string
     */
    public static function clean($html)
    {
        if (empty($html)) {
            return '';
        }

        // Normalize HTML to prevent encoding bypasses
        $html = self::normalizeHtml($html);

        // Remove potentially dangerous JavaScript events (more comprehensive)
        $html = preg_replace('/\bon\w+\s*=\s*["\']?[^"\']*["\']?/i', '', $html);
        
        // Remove data attributes that could be used for XSS
        $html = preg_replace('/\bdata-[a-z-]+\s*=\s*["\']?[^"\']*["\']?/i', '', $html);

        // Remove dangerous protocols
        $dangerousProtocols = ['javascript', 'vbscript', 'data', 'mocha', 'livescript', 'feed'];
        foreach ($dangerousProtocols as $protocol) {
            if ($protocol === 'data') {
                // Allow data: protocol only for images
                $html = preg_replace('/data\s*:(?!image\/)/i', '', $html);
            } else {
                $html = preg_replace('/' . preg_quote($protocol) . '\s*:/i', '', $html);
            }
        }

        // Remove dangerous tags completely
        $dangerousTags = ['script', 'iframe', 'object', 'embed', 'form', 'input', 'textarea', 'button', 'select', 'option', 'meta', 'link', 'style'];
        foreach ($dangerousTags as $tag) {
            $html = preg_replace('/<' . $tag . '[^>]*>.*?<\/' . $tag . '>/is', '', $html);
            $html = preg_replace('/<' . $tag . '[^>]*\/>/i', '', $html);
        }

        // Remove HTML comments (can hide malicious code)
        $html = preg_replace('/<!--.*?-->/s', '', $html);

        // Allow only safe tags
        $html = strip_tags($html, '<' . implode('><', self::$allowedTags) . '>');

        // Sanitize attributes more thoroughly
        $html = self::sanitizeAttributes($html);

        // Final cleanup of any remaining dangerous patterns
        $html = self::finalCleanup($html);

        return $html;
    }

    /**
     * Normalize HTML to prevent encoding bypasses
     *
     * @param string $html
     * @return string
     */
    private static function normalizeHtml($html)
    {
        // Convert various encodings to UTF-8
        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
        
        // Normalize whitespace
        $html = preg_replace('/\s+/', ' ', $html);
        
        return trim($html);
    }

    /**
     * Final cleanup of dangerous patterns
     *
     * @param string $html
     * @return string
     */
    private static function finalCleanup($html)
    {
        // Remove any remaining JavaScript-like patterns
        $patterns = [
            '/\bexpression\s*\(/i',
            '/\burl\s*\(\s*["\']?\s*javascript:/i',
            '/\b@import\s*["\']?\s*javascript:/i',
            '/\bbinding\s*:/i',
        ];

        foreach ($patterns as $pattern) {
            $html = preg_replace($pattern, '', $html);
        }

        return $html;
    }

    /**
     * Sanitize HTML attributes
     *
     * @param string $html
     * @return string
     */
    private static function sanitizeAttributes($html)
    {
        // This is a simplified attribute sanitizer
        // In production, consider using a more robust solution
        
        // Remove all style attributes except for safe CSS properties
        $html = preg_replace('/style\s*=\s*["\'][^"\']*["\']/', function($matches) {
            $style = $matches[0];
            // Allow only safe CSS properties
            $safeProperties = ['color', 'background-color', 'font-size', 'font-weight', 'text-align', 'margin', 'padding', 'width', 'height'];
            
            if (preg_match('/style\s*=\s*["\']([^"\']*)["\']/', $style, $styleMatch)) {
                $cssContent = $styleMatch[1];
                $safeCss = [];
                
                // Simple CSS parsing (this is basic, consider using a proper CSS parser)
                $properties = explode(';', $cssContent);
                foreach ($properties as $property) {
                    if (strpos($property, ':') !== false) {
                        list($propName, $propValue) = explode(':', $property, 2);
                        $propName = trim($propName);
                        if (in_array($propName, $safeProperties)) {
                            $safeCss[] = trim($propName) . ':' . trim($propValue);
                        }
                    }
                }
                
                if (!empty($safeCss)) {
                    return 'style="' . implode(';', $safeCss) . '"';
                }
            }
            
            return '';
        }, $html);

        return $html;
    }

    /**
     * Sanitize URLs to prevent malicious links
     *
     * @param string $url
     * @return string
     */
    public static function sanitizeUrl($url)
    {
        if (empty($url)) {
            return '';
        }

        // Remove dangerous protocols
        $url = preg_replace('/^(javascript|vbscript|data):/i', '', $url);

        // Ensure URL starts with http, https, or / for relative links
        if (!preg_match('/^(https?:|\/)/', $url)) {
            return '';
        }

        return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    }
}