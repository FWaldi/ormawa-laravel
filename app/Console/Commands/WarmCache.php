<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WarmCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:warm {--url=http://localhost}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm up application cache by visiting all public routes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseUrl = $this->option('url');
        $this->info('Starting cache warm-up...');

        // Get all public GET routes
        $routes = collect(Route::getRoutes()->getRoutes())
            ->filter(function ($route) {
                return in_array('GET', $route->methods()) && 
                       !$this->isAuthRoute($route->uri()) &&
                       !$this->isApiRoute($route->uri());
            })
            ->map(function ($route) {
                return $route->uri();
            })
            ->unique();

        $this->info("Found {$routes->count()} public routes to cache");

        $progressBar = $this->output->createProgressBar($routes->count());
        $progressBar->start();

        foreach ($routes as $route) {
            try {
                $url = rtrim($baseUrl, '/') . '/' . ltrim($route, '/');
                $response = Http::timeout(30)->get($url);
                
                if ($response->successful()) {
                    $this->line(" ✓ Cached: {$route}");
                } else {
                    $this->line(" ✗ Failed: {$route} (Status: {$response->status()})");
                }
            } catch (\Exception $e) {
                $this->line(" ✗ Error: {$route} ({$e->getMessage()})");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info('Cache warm-up completed!');
    }

    private function isAuthRoute($uri)
    {
        return str_contains($uri, 'login') || 
               str_contains($uri, 'register') || 
               str_contains($uri, 'logout') ||
               str_contains($uri, 'dashboard');
    }

    private function isApiRoute($uri)
    {
        return str_starts_with($uri, 'api/');
    }
}