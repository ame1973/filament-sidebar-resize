# Filament Sidebar Resize

Drag-to-resize sidebar for Filament v5 admin panels. Admin users can adjust the navigation sidebar width by dragging a handle on its edge. The chosen width is persisted in the browser via `localStorage` and restored on every visit and Livewire navigation.

No Vite, NPM, or Tailwind build step is required — the package injects a single Blade view with minimal inline CSS and vanilla JavaScript through Filament's render hook system.

---

## Table of contents

1. [Features](#features)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Panel registration](#panel-registration)
6. [Fluent API reference](#fluent-api-reference)
7. [Compatibility](#compatibility)
8. [How it works](#how-it-works)
9. [Publishing and customization](#publishing-and-customization)
10. [Troubleshooting](#troubleshooting)
11. [License](#license)

---

## Features

- **Drag-to-resize** — a visible grip handle on the sidebar edge lets users resize with the mouse (`col-resize` cursor).
- **Filament-native layout** — updates the `--sidebar-width` CSS variable and inline sidebar dimensions so `.fi-main` and the rest of the layout respond correctly.
- **localStorage persistence** — each Filament panel stores its own width key (`martin6363-sidebar-resize:{panelId}`).
- **Collapsible sidebar support** — works alongside `sidebarCollapsibleOnDesktop()`; inline width styles are cleared when the sidebar is collapsed and restored when expanded.
- **RTL-aware** — handle position and drag direction respect `dir="rtl"` layouts.
- **SPA-safe** — re-initializes cleanly on `livewire:navigated` without duplicate handles or event listeners.
- **Zero frontend build** — no asset compilation, no external JavaScript libraries.

---

## Requirements

- PHP `^8.2`, `^8.3`, or `^8.4`
- Laravel `^12`, or `^13`
- Filament `^5.0`

---

## Installation

```bash
composer require martin6363/filament-sidebar-resize
```

The service provider is auto-discovered. Optionally publish the config file:

```bash
php artisan vendor:publish --tag=sidebar-resize-config
```

Register the plugin on your Filament panel (see [Panel registration](#panel-registration)).

---

## Configuration

Published file: `config/sidebar-resize.php`

```php
return [
    'min_width' => 200,
    'max_width' => 450,
];
```

| Key | Purpose |
|-----|---------|
| `min_width` | Narrowest sidebar width in pixels when dragging. Default: `200`. |
| `max_width` | Widest sidebar width in pixels when dragging. Default: `450`. |

These defaults apply globally. Override per panel with the fluent `minWidth()` and `maxWidth()` methods on the plugin instance.

---

## Panel registration

Add the plugin to your panel provider:

```php
use Filament\Panel;
use Filament\PanelProvider;
use Martin6363\SidebarResize\SidebarResizePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->plugins([
                SidebarResizePlugin::make(),
            ]);
    }
}
```

### With custom width limits

```php
SidebarResizePlugin::make()
    ->minWidth(220)
    ->maxWidth(480),
```

### With a collapsible desktop sidebar

The plugin is designed to coexist with Filament's collapsible sidebar. No extra setup is required:

```php
return $panel
    ->sidebarCollapsibleOnDesktop()
    ->plugins([
        SidebarResizePlugin::make(),
    ]);
```

When the sidebar is collapsed, resize styles are removed so Filament can render the narrow icon-only layout. When expanded again, the user's saved width is reapplied.

---

## Fluent API reference

| Method | Description |
|--------|-------------|
| `SidebarResizePlugin::make()` | Resolve the plugin singleton from the container. |
| `minWidth(int $pixels)` | Set the minimum draggable sidebar width. Overrides `config('sidebar-resize.min_width')`. |
| `maxWidth(int $pixels)` | Set the maximum draggable sidebar width. Overrides `config('sidebar-resize.max_width')`. |

---

## Compatibility

| Scenario | Behavior |
|----------|----------|
| Desktop (`≥ 1024px`) | Resize handle visible when the sidebar is open. |
| Mobile (`< 1024px`) | Handle hidden; Filament uses the overlay sidebar. |
| `sidebarCollapsibleOnDesktop()` | Resize applies only when expanded; collapse/expand works normally. |
| `sidebarFullyCollapsibleOnDesktop()` | Same inline-style clearing when the sidebar is closed. |
| Top navigation layout | Plugin does not activate (`fi-body-has-top-navigation`). |
| RTL | Drag direction and handle edge use logical (`inline-end`) positioning. |
| Livewire SPA (`wire:navigate`) | Script re-inits on `livewire:navigated` with idempotent teardown. |
| Multiple panels | Each panel gets a separate `localStorage` key based on `panel->getId()`. |

---

## Publishing and customization

| Tag | Contents |
|-----|----------|
| `sidebar-resize-config` | `config/sidebar-resize.php` |

```bash
php artisan vendor:publish --tag=sidebar-resize-config
```

Adjust `min_width` and `max_width` in the published config, or override limits per panel with `->minWidth()` and `->maxWidth()` on the plugin instance.

---

## Troubleshooting

**The resize handle does not appear**

- Confirm the plugin is registered in `->plugins([...])` on the active panel.
- The handle is desktop-only (`≥ 1024px` viewport width).
- The sidebar must be expanded (`fi-sidebar-open`). It is hidden when the sidebar is collapsed.
- Panels with top navigation (`->topNavigation()`) are intentionally excluded.

**Sidebar collapse stops working after installing the plugin**

- Update to the latest package version. Collapsible desktop sidebars require inline width styles to be cleared on collapse.
- Ensure you are not setting sidebar width elsewhere with inline styles or custom CSS that uses `!important`.

**Width resets after refresh**

- Check that `localStorage` is available and not blocked (private browsing restrictions, browser extensions).
- Each panel uses its own key: `martin6363-sidebar-resize:{panelId}`. Switching panels will load that panel's saved width.

**Width does not apply on first paint**

- The script runs at `BODY_END`. A brief flash of the default Filament width may occur before the saved width is applied. This is expected without a head-level blocking script.

**Drag feels laggy**

- Width updates during drag are already throttled through `requestAnimationFrame`. If custom CSS adds `transition` to the sidebar, the plugin disables transitions on the sidebar while dragging via `body.fi-sidebar-resizing`.

---

## License

The MIT License (MIT). Copyright (c) Martin.
