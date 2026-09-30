<?php

namespace App\Filament\Traits;

use App\Models\CmsGlobalSetting;

trait RestrictedToSuperAdmin
{
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        // Reviewer midtrans is strictly prohibited from non-Project OS features
        if ($user->hasRole('midtrans_reviewer') || $user->email === 'reviewer.midtrans@neriahpro.com') {
            return false;
        }

        // If compliance strict mode is active, only super admin can see
        try {
            $strictMode = CmsGlobalSetting::where('key', 'midtrans_compliance_strict_mode')->value('value');
            if ($strictMode && ! ($user->hasRole('super_admin') || $user->email === 'yoseph.iriandi.tambunan@gmail.com')) {
                return false;
            }
        } catch (\Throwable $e) {
            // Table might not exist during early migration
        }

        // Superadmin has full access
        return $user->hasRole('super_admin') || $user->email === 'yoseph.iriandi.tambunan@gmail.com';
    }
}
