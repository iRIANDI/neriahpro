<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 5 Dynamic Core RBAC Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $developerRole = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $investorRole = Role::firstOrCreate(['name' => 'investor', 'guard_name' => 'web']);
        $reviewerRole = Role::firstOrCreate(['name' => 'midtrans_reviewer', 'guard_name' => 'web']);
        $clientRetailRole = Role::firstOrCreate(['name' => 'client_retail', 'guard_name' => 'web']);
        $clientPartnerRole = Role::firstOrCreate(['name' => 'client_partner', 'guard_name' => 'web']);

        // Explicit Global & Executive Permissions
        $executiveGuidePerm = Permission::firstOrCreate(['name' => 'view_executive_smart_guide', 'guard_name' => 'web']);
        $manageSettingsPerm = Permission::firstOrCreate(['name' => 'manage_settings', 'guard_name' => 'web']);
        $manageVouchersPerm = Permission::firstOrCreate(['name' => 'manage_vouchers', 'guard_name' => 'web']);

        // Grant Due Diligence permissions to Investor & Superadmin
        if (!$investorRole->hasPermissionTo($executiveGuidePerm)) {
            $investorRole->givePermissionTo($executiveGuidePerm);
        }

        // 1. Primary Superadmin (Founder & Chief Architect - Yoseph Iriandi Tambunan)
        $admin = User::updateOrCreate(
            ['email' => 'yoseph.iriandi.tambunan@gmail.com'],
            [
                'name' => 'Yoseph Iriandi Tambunan',
                'password' => Hash::make('#T4mbun4n#'),
                'email_verified_at' => now(),
            ]
        );

        if (!$admin->hasRole('super_admin')) {
            $admin->assignRole($superAdminRole);
        }

        // 2. Dedicated Midtrans QA / Reviewer Dummy Account (Restricted Strictly to Project OS)
        $reviewer = User::updateOrCreate(
            ['email' => 'reviewer.midtrans@neriahpro.com'],
            [
                'name' => 'Midtrans QA Reviewer',
                'password' => Hash::make('MidtransDemo2026#'),
                'email_verified_at' => now(),
            ]
        );

        // Ensure reviewer does NOT have super_admin and ONLY has midtrans_reviewer
        if ($reviewer->hasRole('super_admin')) {
            $reviewer->removeRole('super_admin');
        }
        if (!$reviewer->hasRole('midtrans_reviewer')) {
            $reviewer->assignRole($reviewerRole);
        }

        // Assign Project OS & Compliance Audit permissions to midtrans_reviewer
        $reviewerPermissions = [
            'ViewAny:VisionBlueprint',
            'View:VisionBlueprint',
            'Create:VisionBlueprint',
            'Update:VisionBlueprint',
            'ViewAny:Document',
            'View:Document',
            'ViewAny:DomainHostingAsset',
            'View:DomainHostingAsset',
            'ViewAny:Transaction',
            'View:Transaction',
            'ViewAny:Product',
            'View:Product',
            'ViewAny:LegalPolicy',
            'View:LegalPolicy',
            'ViewAny:LeadContact',
            'View:LeadContact',
        ];

        foreach ($reviewerPermissions as $permName) {
            $perm = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            if (!$reviewerRole->hasPermissionTo($perm)) {
                $reviewerRole->givePermissionTo($perm);
            }
        }

        // 3. Team Developer Permissions
        $developerPermissions = [
            'ViewAny:VisionBlueprint',
            'View:VisionBlueprint',
            'Create:VisionBlueprint',
            'Update:VisionBlueprint',
            'ViewAny:Document',
            'View:Document',
            'Create:Document',
            'Update:Document',
            'ViewAny:DomainHostingAsset',
            'View:DomainHostingAsset',
            'Create:DomainHostingAsset',
            'Update:DomainHostingAsset',
            'ViewAny:Product',
            'View:Product',
            'ViewAny:Transaction',
            'View:Transaction',
            'ViewAny:LeadContact',
            'View:LeadContact',
        ];

        foreach ($developerPermissions as $permName) {
            $perm = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            if (!$developerRole->hasPermissionTo($perm)) {
                $developerRole->givePermissionTo($perm);
            }
        }

        // 4. Calon Investor Permissions (Due Diligence Read-Only)
        $investorPermissions = [
            'ViewAny:Product',
            'View:Product',
            'ViewAny:DomainHostingAsset',
            'View:DomainHostingAsset',
            'ViewAny:LegalPolicy',
            'View:LegalPolicy',
            'ViewAny:VisionBlueprint',
            'View:VisionBlueprint',
        ];

        foreach ($investorPermissions as $permName) {
            $perm = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            if (!$investorRole->hasPermissionTo($perm)) {
                $investorRole->givePermissionTo($perm);
            }
        }
    }
}
