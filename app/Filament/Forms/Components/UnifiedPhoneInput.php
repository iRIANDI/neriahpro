<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class UnifiedPhoneInput extends Field
{
    protected string $view = 'filament.forms.components.unified-phone-input';

    protected string $countryStatePath = 'company_whatsapp_country';

    protected string $numberStatePath = 'company_whatsapp_number';

    protected bool $includeLeadingPlus = false;

    public function countryStatePath(string $path): static
    {
        $this->countryStatePath = $path;

        return $this;
    }

    public function numberStatePath(string $path): static
    {
        $this->numberStatePath = $path;

        return $this;
    }

    public function includeLeadingPlus(bool $plus = true): static
    {
        $this->includeLeadingPlus = $plus;

        return $this;
    }

    public function getCountryStatePath(): string
    {
        return $this->countryStatePath;
    }

    public function getNumberStatePath(): string
    {
        return $this->numberStatePath;
    }

    public function shouldIncludeLeadingPlus(): bool
    {
        return $this->includeLeadingPlus;
    }
}
