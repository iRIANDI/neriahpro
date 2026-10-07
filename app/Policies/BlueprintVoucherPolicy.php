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
     * Dynamic RBAC Gatekeeper: Super Admin and authorized staff can manage project vouchers.
     * Reviewers and clients are strictly forbidden.
     */
    public function before(AuthUser $authUser, string $ability): ?bool
    {
        if ($authUser->hasRole('midtrans_reviewer')) {
            return false;
        }

        if ($this->checkSuperAdmin($authUser)) {
            return true;
        }

        return null;
    }

    private function checkSuperAdmin(AuthUser $authUser): bool
    {
        return method_exists($authUser, 'isSuperAdmin') 
            ? $authUser->isSuperAdmin() 
            : ($authUser->hasRole('super_admin') || $authUser->email === 'yoseph.iriandi.tambunan@gmail.com');
    }

    public function viewAny(AuthUser $authUser): bool
    {
        if ($authUser->hasRole('midtrans_reviewer')) return false;
        if ($this->checkSuperAdmin($authUser)) return true;
        return $authUser->can('ViewAny:BlueprintVoucher') || $authUser->can('manage_vouchers');
    }

    public function view(AuthUser $authUser, BlueprintVoucher $voucher): bool
    {
        if ($authUser->hasRole('midtrans_reviewer')) return false;
        if ($this->checkSuperAdmin($authUser)) return true;
        return $authUser->can('View:BlueprintVoucher') || $authUser->can('manage_vouchers');
    }

    public function create(AuthUser $authUser): bool
    {
        if ($authUser->hasRole('midtrans_reviewer')) return false;
        if ($this->checkSuperAdmin($authUser)) return true;
        return $authUser->can('Create:BlueprintVoucher') || $authUser->can('manage_vouchers');
    }

    public function update(AuthUser $authUser, BlueprintVoucher $voucher): bool
    {
        if ($authUser->hasRole('midtrans_reviewer')) return false;
        if ($this->checkSuperAdmin($authUser)) return true;
        return $authUser->can('Update:BlueprintVoucher') || $authUser->can('manage_vouchers');
    }

    public function delete(AuthUser $authUser, BlueprintVoucher $voucher): bool
    {
        if ($authUser->hasRole('midtrans_reviewer')) return false;
        if ($this->checkSuperAdmin($authUser)) return true;
        return $authUser->can('Delete:BlueprintVoucher') || $authUser->can('manage_vouchers');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        if ($authUser->hasRole('midtrans_reviewer')) return false;
        if ($this->checkSuperAdmin($authUser)) return true;
        return $authUser->can('DeleteAny:BlueprintVoucher') || $authUser->can('manage_vouchers');
    }
}
