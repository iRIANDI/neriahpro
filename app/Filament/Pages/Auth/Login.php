<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    protected Width | string | null $maxContentWidth = Width::Medium;

    public function getTitle(): string | Htmlable
    {
        return 'Neriah Pro // Enterprise Control Center';
    }

    public function getHeading(): string | Htmlable | null
    {
        return new HtmlString('
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.5rem;">
                <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 11px; font-family: monospace; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; background: rgba(245, 158, 11, 0.1); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.25);">
                    <span style="width: 8px; height: 8px; border-radius: 9999px; background: #10b981; display: inline-block;"></span>
                    <span>Scope Lock OS &bull; Vision Blueprint &bull; CV Pro</span>
                </div>
                <h1 style="font-size: 1.5rem; font-weight: 900; letter-spacing: -0.025em; text-transform: uppercase; margin: 0; line-height: 1.2;" class="text-zinc-950 dark:text-white font-sans">
                    Neriah<span style="color: #f59e0b;">Pro</span> Control Hub
                </h1>
            </div>
        ');
    }

    public function getSubheading(): string | Htmlable | null
    {
        return new HtmlString('
            <p class="text-xs text-zinc-600 dark:text-zinc-400 max-w-md mx-auto text-center leading-relaxed">
                Centralized architectural portal for enterprise software blueprints, zero-scope-creep digital contracts, and automated AI career infrastructure.
            </p>
        ');
    }
}
