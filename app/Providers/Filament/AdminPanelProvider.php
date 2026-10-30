<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\MenuItem;
use Filament\View\PanelsRenderHook;
use App\Filament\Widgets\ProjectOsComplianceWidget;
use App\Filament\Widgets\FrontpageQuickLaunchWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $adminPath = config('app.admin_path', env('FILAMENT_ADMIN_PATH', 'admin'));

        return $panel
            ->default()
            ->id('admin')
            ->path($adminPath)
            ->brandName('Neriah Pro')
            ->brandLogo(fn () => view('filament.components.brand-logo'))
            ->brandLogoHeight('2.25rem')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->renderHook(
                PanelsRenderHook::HEAD_START,
                fn () => new HtmlString(
                    '<meta name="google" content="notranslate">' . PHP_EOL .
                    '<script>' . PHP_EOL .
                    'window.addEventListener("unhandledrejection", function(event) {' . PHP_EOL .
                    '    var msg = (event && event.reason && event.reason.message) ? event.reason.message : "";' . PHP_EOL .
                    '    if (msg.includes("Could not establish connection") || msg.includes("Receiving end does not exist") || msg.includes("message port closed")) {' . PHP_EOL .
                    '        event.preventDefault();' . PHP_EOL .
                    '        event.stopImmediatePropagation();' . PHP_EOL .
                    '    }' . PHP_EOL .
                    '});' . PHP_EOL .
                    '</script>'
                )
            )
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn (): string => Blade::render('
                    <div class="flex items-center gap-1.5 sm:gap-2 my-auto mx-1 sm:mx-2 shrink-0">
                        @include("filament.components.topbar-ai-widget")
                    </div>
                ')
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn () => view('filament.auth.login-after')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => view('filament.components.floating-copilot-hook')
            )
            ->userMenuItems([
                MenuItem::make()
                    ->label(fn () => __('Neriah Pro AI Copilot'))
                    ->url('javascript:if(window.Livewire){window.Livewire.dispatch(\'toggle-ai-copilot\');}else{window.dispatchEvent(new CustomEvent(\'toggle-ai-copilot\'));}')
                    ->icon('heroicon-o-cpu-chip'),
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->databaseNotifications()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                ProjectOsComplianceWidget::class,
                FrontpageQuickLaunchWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->registerNavigation(fn () => auth()->user()?->hasRole('super_admin')),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
