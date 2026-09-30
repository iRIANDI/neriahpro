<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    protected Width | string | null $maxContentWidth = Width::Large;

    public function getTitle(): string | Htmlable
    {
        return 'Neriah Pro // Enterprise Control Center';
    }

    public function getHeading(): string | Htmlable | null
    {
        return new HtmlString('
            <div class="flex flex-col items-center text-center space-y-2.5">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono font-bold tracking-wider uppercase bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>Scope Lock OS &bull; Vision Blueprint &bull; CV Pro</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white uppercase font-sans">
                    Neriah<span class="text-amber-500">Pro</span> Control Hub
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
