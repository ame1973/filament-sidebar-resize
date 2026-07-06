<?php

declare(strict_types=1);

namespace Martin6363\SidebarResize;

use Illuminate\Support\ServiceProvider;

class SidebarResizeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/sidebar-resize.php',
            'sidebar-resize',
        );

        $this->app->singleton(SidebarResizePlugin::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sidebar-resize');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/sidebar-resize.php' => config_path('sidebar-resize.php'),
            ], 'sidebar-resize-config');
        }
    }
}
