<?php

declare(strict_types=1);

namespace Martin6363\SidebarResize;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;

class SidebarResizePlugin implements Plugin
{
    protected int $minWidth;

    protected int $maxWidth;

    protected bool $centralGripOnly;

    public function __construct()
    {
        $this->minWidth = (int) config('sidebar-resize.min_width', 200);
        $this->maxWidth = (int) config('sidebar-resize.max_width', 450);
        $this->centralGripOnly = (bool) config('sidebar-resize.central_grip_only', true);
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'sidebar-resize';
    }

    public function minWidth(int $width): static
    {
        $this->minWidth = $width;

        return $this;
    }

    public function maxWidth(int $width): static
    {
        $this->maxWidth = $width;

        return $this;
    }

    public function centralGripOnly(bool $enabled = true): static
    {
        $this->centralGripOnly = $enabled;

        return $this;
    }

    public function getMinWidth(): int
    {
        return $this->minWidth;
    }

    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    public function isCentralGripOnly(): bool
    {
        return $this->centralGripOnly;
    }

    public function register(Panel $panel): void
    {
        $panel->renderHook(
            PanelsRenderHook::BODY_END,
            fn (): View => view('sidebar-resize::resize-script', [
                'minWidth' => $this->getMinWidth(),
                'maxWidth' => $this->getMaxWidth(),
                'centralGripOnly' => $this->isCentralGripOnly(),
                'storageKey' => $this->getStorageKey($panel),
            ]),
        );
    }

    public function boot(Panel $panel): void
    {
        //
    }

    protected function getStorageKey(Panel $panel): string
    {
        return 'martin6363-sidebar-resize:'.$panel->getId();
    }
}
