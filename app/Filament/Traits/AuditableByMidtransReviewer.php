<?php

namespace App\Filament\Traits;

use Illuminate\Database\Eloquent\Model;

trait AuditableByMidtransReviewer
{
    /**
     * Check if the authenticated user is the primary superadmin.
     */
    public static function isSuperAdmin(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->hasRole('super_admin') || $user->email === 'yoseph.iriandi.tambunan@gmail.com';
    }

    /**
     * Check if the authenticated user is the designated Midtrans reviewer.
     */
    public static function isMidtransReviewer(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->hasRole('midtrans_reviewer') || $user->email === 'reviewer.midtrans@neriahpro.com';
    }

    /**
     * Both superadmin and midtrans reviewer have view access.
     */
    public static function canViewAny(): bool
    {
        return static::isSuperAdmin() || static::isMidtransReviewer();
    }

    public static function canAccess(): bool
    {
        return static::canViewAny();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function canView(Model $record): bool
    {
        return static::isSuperAdmin() || static::isMidtransReviewer();
    }

    /**
     * Reviewers have strictly read-only audit access to protect production data.
     */
    public static function canCreate(): bool
    {
        return static::isSuperAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        return static::isSuperAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        return static::isSuperAdmin();
    }

    public static function canDeleteAny(): bool
    {
        return static::isSuperAdmin();
    }
}
