<?php

namespace App\Http\Controllers\Blueprint\Concerns;

use App\Models\VisionBlueprint;
use Illuminate\Support\Facades\Auth;

trait AuthorizesBlueprintAccess
{
    /**
     * Enforce strict security access to blueprint documents.
     * Prevents IDOR leaks on paid tiers (Lite, Pro, Ultimate, MVP).
     */
    protected function authorizeBlueprintAccess(VisionBlueprint $blueprint): void
    {
        $user = Auth::user();

        // Lead Architects / Super Admins bypass
        if ($user && ($user->isSuperAdmin() || $user->hasRole('super_admin') || str_contains($user->email, 'neriahpro'))) {
            return;
        }

        // Guest access allowed for Free Grant / Spark Tier or public blueprints without paid retail tier
        $tier = $blueprint->user_metadata['retail_tier'] ?? $blueprint->user_metadata['package_tier'] ?? null;
        if ($tier === 'retail_spark' || $blueprint->is_free_grant || empty($blueprint->user_metadata) || ! $tier) {
            return;
        }

        // Paid tiers mandate login
        if (! $user) {
            abort(403, 'Akses Ditolak: Sesuai kebijakan, wajib login/daftar akun untuk mengakses & mengunduh dokumen lisensi berbayar Anda.');
        }

        // Check exact ownership
        $blueprintUserId = $blueprint->user_metadata['user_id'] ?? null;
        $blueprintEmail = $blueprint->email ?? '';

        if ($blueprintUserId !== $user->id && strtolower(trim($blueprintEmail)) !== strtolower(trim($user->email))) {
            abort(403, 'Akses Ditolak: Dokumen ini terproteksi hak cipta Neriah Pro dan hanya dapat diakses oleh pemilik lisensi terdaftar.');
        }
    }
}

