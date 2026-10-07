<?php

namespace App\Filament\Traits;

use Illuminate\Database\Eloquent\Model;

trait AuditableByMidtransReviewer
{
    /**
     * Check if the authenticated user is a superadmin.
     */
    public static function isSuperAdmin(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('super_admin');
    }

    /**
     * Check if the authenticated user is a developer.
     */
    public static function isDeveloper(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('developer');
    }

    /**
     * Check if the authenticated user is an investor.
     */
    public static function isInvestor(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('investor');
    }

    /**
     * Check if the authenticated user is the designated Midtrans reviewer.
     */
    public static function isMidtransReviewer(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('midtrans_reviewer');
    }

    /**
     * Superadmin, developer, investor, and midtrans reviewer have view access.
     */
    public static function canViewAny(): bool
    {
        return static::isSuperAdmin() 
            || static::isDeveloper() 
            || static::isInvestor() 
            || static::isMidtransReviewer();
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
        return static::canViewAny();
    }

    /**
     * Only superadmin and team developers can create records. Reviewers and investors are strictly read-only.
     */
    public static function canCreate(): bool
    {
        return static::isSuperAdmin() || static::isDeveloper();
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
