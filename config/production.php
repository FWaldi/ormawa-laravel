<?php

/**
 * Production optimizations for cPanel deployment
 */

// Increase memory limits for shared hosting
ini_set('memory_limit', '256M');

// Optimize execution time
ini_set('max_execution_time', 300);

// Optimize file uploads
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');

// Enable OPcache if available
if (function_exists('opcache_enable')) {
    opcache_enable();
}

// Limit concurrent processes for shared hosting
if (!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}