<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BlueprintVoucher;
use Illuminate\Auth\Access\HandlesAuthorization;

class BlueprintVoucherPolicy
{
    use HandlesAuthorization;

    /**
     * Strict RBAC Gatekeeper: ONLY yoseph.iriandi.tambunan@gmail.com can manage project vouchers.
     */
    public function before(AuthUser $authUser, string $ability): ?bool
    {
        if ($authUser->email === 'yoseph.iriandi.tambunan@gmail.com') {
            return true;
        }

        return false;
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->email === 'yoseph.iriandi.tambunan@gmail.com';
    }

    public function view(AuthUser $authUser, BlueprintVoucher $voucher): bool
    {
        return $authUser->email === 'yoseph.iriandi.tambunan@gmail.com';
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->email === 'yoseph.iriandi.tambunan@gmail.com';
    }

    public function update(AuthUser $authUser, BlueprintVoucher $voucher): bool
    {
        return $authUser->email === 'yoseph.iriandi.tambunan@gmail.com';
    }

    public function delete(AuthUser $authUser, BlueprintVoucher $voucher): bool
    {
        return $authUser->email === 'yoseph.iriandi.tambunan@gmail.com';
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->email === 'yoseph.iriandi.tambunan@gmail.com';
    }
}
