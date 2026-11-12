<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

#[Fillable([
    'name',
    'email',
    'password',
    'phone_country_code',
    'phone',
    'company_name',
    'npwp',
    'billing_address',
    'billing_city',
    'billing_province',
    'billing_postal_code',
    'notification_preferences',
    'profile_metadata',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'two_factor_confirmed_at',
    'two_factor_required',
    'referral_code',
    'referred_by_user_id',
    'saas_subscription_tier',
    'saas_subscription_expires_at',
    'saas_project_quota',
])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUlids, HasRoles, \App\Traits\Auditable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_preferences' => 'array',
            'profile_metadata' => 'array',
            'saas_subscription_expires_at' => 'datetime',
            'saas_project_quota' => 'integer',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->isSuperAdmin()
                || $this->hasAnyRole([
                    'developer',
                    'investor',
                    'midtrans_reviewer',
                ])
                || ($this->email && $this->email === 'reviewer.midtrans@neriahpro.com');
        }

        return false;
    }

    public function resumes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Resume::class);
    }

    public function interviewSessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InterviewSession::class);
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function cvQuota(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserCvQuota::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin') || ($this->email && $this->email === 'yoseph.iriandi.tambunan@gmail.com');
    }

    /**
     * Universal Superadmin Payment & Quota Bypass Gate across Project OS, CV Pro, and all future Neriah Pro modules.
     */
    public function hasUniversalSuperadminBypass(?string $moduleCode = null): bool
    {
        try {
            if ($this->isSuperAdmin()) {
                return true;
            }

            if (method_exists($this, 'hasPermissionTo') && $this->hasPermissionTo('bypass_all_payment_gates')) {
                return true;
            }
        } catch (\Throwable) {
            return (bool) ($this->email && $this->email === 'yoseph.iriandi.tambunan@gmail.com');
        }

        return false;
    }

    /**
     * Standardized Zero-Cost Entitlement Payload for Universal Superadmin Bypass across current & future modules.
     *
     * @return array<string, mixed>
     */
    public function getUniversalBypassEntitlementPayload(string $moduleCode = 'ALL_MODULES'): array
    {
        $active = $this->hasUniversalSuperadminBypass($moduleCode);

        return [
            'is_bypass_active' => $active,
            'module_code' => strtoupper($moduleCode),
            'tier_code' => $active ? 'vip_superadmin_bypass' : 'standard',
            'billing_amount_idr' => 0,
            'unlimited_quota' => $active,
            'supported_ecosystems' => [
                'PROJECT_OS',
                'CV_PRO',
                'FUTURE_ECOSYSTEM_MODULES',
            ],
            'verified_actor' => $this->email ?? 'guest',
        ];
    }

    public function isDeveloper(): bool
    {
        return $this->hasRole('developer');
    }

    public function isInvestor(): bool
    {
        return $this->hasRole('investor');
    }

    public function isMidtransReviewer(): bool
    {
        return $this->hasRole('midtrans_reviewer');
    }

    public function isClient(): bool
    {
        return $this->hasAnyRole(['client_retail', 'client_partner', 'client']);
    }

    /**
     * Check if user is required by policy to use Two-Factor Authentication.
     */
    public function requiresTwoFactor(): bool
    {
        return (bool) ($this->two_factor_required ?? false);
    }

    /**
     * Check if user has completed Two-Factor Authentication setup.
     */
    public function hasEnabledTwoFactor(): bool
    {
        return !empty($this->two_factor_secret) && !is_null($this->two_factor_confirmed_at);
    }

    /**
     * Enable Two-Factor Authentication.
     */
    public function enableTwoFactor(string $secret, array $recoveryCodes): void
    {
        $this->update([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'two_factor_confirmed_at' => now(),
        ]);
    }

    /**
     * Disable Two-Factor Authentication.
     */
    public function disableTwoFactor(): void
    {
        $this->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
    }

    /**
     * Verify a TOTP code or recovery code.
     */
    public function verifyTwoFactorCode(string $code): bool
    {
        if (!$this->hasEnabledTwoFactor()) {
            return true;
        }

        try {
            $secret = decrypt($this->two_factor_secret);
            if (app(\App\Services\TwoFactorService::class)->verifyCode($secret, $code)) {
                return true;
            }
        } catch (\Throwable) {}

        // Check recovery codes
        try {
            $codes = json_decode(decrypt($this->two_factor_recovery_codes), true) ?: [];
            $normalized = strtoupper(trim($code));
            if (in_array($normalized, $codes, true)) {
                // Invalidate used recovery code
                $codes = array_values(array_filter($codes, fn ($c) => $c !== $normalized));
                $this->update([
                    'two_factor_recovery_codes' => encrypt(json_encode($codes)),
                ]);
                return true;
            }
        } catch (\Throwable) {}

        return false;
    }
}
