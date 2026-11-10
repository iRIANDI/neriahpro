<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

        // Explicit Global, Project OS v2.5 & Executive Permissions
        $executiveGuidePerm = Permission::firstOrCreate(['name' => 'view_executive_smart_guide', 'guard_name' => 'web']);
        $manageSettingsPerm = Permission::firstOrCreate(['name' => 'manage_settings', 'guard_name' => 'web']);
        $manageVouchersPerm = Permission::firstOrCreate(['name' => 'manage_vouchers', 'guard_name' => 'web']);
        $auditLogPerm = Permission::firstOrCreate(['name' => 'view_audit_logs', 'guard_name' => 'web']);
        $exportAuditLogPerm = Permission::firstOrCreate(['name' => 'export_audit_logs', 'guard_name' => 'web']);
        $aiCopilotPerm = Permission::firstOrCreate(['name' => 'view_ai_copilot', 'guard_name' => 'web']);
        $executeAiToolsPerm = Permission::firstOrCreate(['name' => 'execute_ai_native_tools', 'guard_name' => 'web']);
        $twoFactorPerm = Permission::firstOrCreate(['name' => 'manage_two_factor', 'guard_name' => 'web']);
        $viewMatrixPerm = Permission::firstOrCreate(['name' => 'view_project_os_matrix', 'guard_name' => 'web']);
        $manageMatrixPerm = Permission::firstOrCreate(['name' => 'manage_project_os_matrix', 'guard_name' => 'web']);
        $brownfieldImportPerm = Permission::firstOrCreate(['name' => 'execute_brownfield_importer', 'guard_name' => 'web']);
        $slopScannerPerm = Permission::firstOrCreate(['name' => 'run_slop_drift_scanner', 'guard_name' => 'web']);
        $cliBootstrapPerm = Permission::firstOrCreate(['name' => 'export_cli_bootstrapper', 'guard_name' => 'web']);
        $blueprintDoctorPerm = Permission::firstOrCreate(['name' => 'run_blueprint_doctor', 'guard_name' => 'web']);
        $manageDay2OpsPerm = Permission::firstOrCreate(['name' => 'manage_day2_ops_contracts', 'guard_name' => 'web']);
        $exportPrintOpsPerm = Permission::firstOrCreate(['name' => 'export_print_and_ops_specs', 'guard_name' => 'web']);
        $manageChangeRequestsPerm = Permission::firstOrCreate(['name' => 'manage_blueprint_change_requests', 'guard_name' => 'web']);
        $connectMcpBridgePerm = Permission::firstOrCreate(['name' => 'connect_mcp_bridge', 'guard_name' => 'web']);
        $runRedteamPerm = Permission::firstOrCreate(['name' => 'run_redteam_security_suite', 'guard_name' => 'web']);
        $validateEnvPerm = Permission::firstOrCreate(['name' => 'validate_env_vault', 'guard_name' => 'web']);
        $manageMitraQuotationsPerm = Permission::firstOrCreate(['name' => 'manage_mitra_quotations', 'guard_name' => 'web']);
        $freezeScopeSpkPerm = Permission::firstOrCreate(['name' => 'freeze_blueprint_scope_spk', 'guard_name' => 'web']);
        $approvePaidAddendumPerm = Permission::firstOrCreate(['name' => 'approve_paid_change_orders', 'guard_name' => 'web']);
        $signOffUatBastPerm = Permission::firstOrCreate(['name' => 'signoff_uat_and_issue_bast', 'guard_name' => 'web']);
        $manageEnterpriseMenuPerm = Permission::firstOrCreate(['name' => 'manage_enterprise_menu_matrix', 'guard_name' => 'web']);
        $compileSymbolDagPerm = Permission::firstOrCreate(['name' => 'compile_symbol_manifest_and_dag_chunks', 'guard_name' => 'web']);
        $overrideFiscalMakerCheckerPerm = Permission::firstOrCreate(['name' => 'override_fiscal_and_maker_checker_locks', 'guard_name' => 'web']);
        $manage15PillarsPerm = Permission::firstOrCreate(['name' => 'manage_15_architecture_pillars', 'guard_name' => 'web']);
        $configureHardwareWsPerm = Permission::firstOrCreate(['name' => 'configure_hardware_and_websocket_contracts', 'guard_name' => 'web']);
        $generateClientSopPerm = Permission::firstOrCreate(['name' => 'generate_client_sop_and_onboarding_pack', 'guard_name' => 'web']);
        $manageInclusiveGrantsPerm = Permission::firstOrCreate(['name' => 'manage_inclusive_grants_and_whitelabel', 'guard_name' => 'web']);
        $verifyCanaryWatermarksPerm = Permission::firstOrCreate(['name' => 'verify_canary_ip_watermarks', 'guard_name' => 'web']);
        $broadcastLivingCvePerm = Permission::firstOrCreate(['name' => 'broadcast_living_cve_security_alerts', 'guard_name' => 'web']);
        $manageCommunityMarketplacePerm = Permission::firstOrCreate(['name' => 'manage_community_primitives_marketplace', 'guard_name' => 'web']);
        $manageAgentShieldPerm = Permission::firstOrCreate(['name' => 'manage_agentshield_security', 'guard_name' => 'web']);
        $participateCoDesignPerm = Permission::firstOrCreate(['name' => 'participate_codesign_alignment', 'guard_name' => 'web']);
        $executeDualKeyLockPerm = Permission::firstOrCreate(['name' => 'execute_dual_key_scope_lock', 'guard_name' => 'web']);
        $verifyCryptoAuditPerm = Permission::firstOrCreate(['name' => 'verify_crypto_audit_chain', 'guard_name' => 'web']);
        $export3SheetAuditPerm = Permission::firstOrCreate(['name' => 'export_3sheet_audit_xlsx', 'guard_name' => 'web']);
        $manageVerifiedTestimonialsPerm = Permission::firstOrCreate(['name' => 'manage_verified_testimonials', 'guard_name' => 'web']);
        $configureDualZoneUxPerm = Permission::firstOrCreate(['name' => 'configure_dual_zone_public_ux', 'guard_name' => 'web']);
        $manageAffiliatePerm = Permission::firstOrCreate(['name' => 'manage_affiliate_commissions', 'guard_name' => 'web']);
        $approveImpactGrantsPerm = Permission::firstOrCreate(['name' => 'approve_nonprofit_impact_grants', 'guard_name' => 'web']);
        $manageOrgWorkspacesPerm = Permission::firstOrCreate(['name' => 'manage_organization_workspaces', 'guard_name' => 'web']);
        $viewSaasHealthPulsePerm = Permission::firstOrCreate(['name' => 'view_saas_system_health_pulse', 'guard_name' => 'web']);
        $manageSeoGeoAuthorityPerm = Permission::firstOrCreate(['name' => 'manage_seo_geo_authority', 'guard_name' => 'web']);
        $manageUuPdpPrivacyPerm = Permission::firstOrCreate(['name' => 'manage_uu_pdp_privacy_vault', 'guard_name' => 'web']);
        $manageSlaWarrantyPerm = Permission::firstOrCreate(['name' => 'manage_sla_warranty_tickets', 'guard_name' => 'web']);
        $runSandboxWebhookPerm = Permission::firstOrCreate(['name' => 'run_sandbox_webhook_simulator', 'guard_name' => 'web']);
        $manage20PillarsPerm = Permission::firstOrCreate(['name' => 'manage_20_architecture_pillars', 'guard_name' => 'web']);
        $manageAiKnowledgePerm = Permission::firstOrCreate(['name' => 'manage_ai_knowledge_cag_rag_kag', 'guard_name' => 'web']);
        $manageScaffoldWorkspacesPerm = Permission::firstOrCreate(['name' => 'manage_scaffold_workspaces', 'guard_name' => 'web']);
        $managePricingTiersPerm = Permission::firstOrCreate(['name' => 'manage_pricing_tiers', 'guard_name' => 'web']);

        $superAdminRole->syncPermissions([
            $executiveGuidePerm,
            $manageSettingsPerm,
            $manageVouchersPerm,
            $auditLogPerm,
            $exportAuditLogPerm,
            $aiCopilotPerm,
            $executeAiToolsPerm,
            $twoFactorPerm,
            $viewMatrixPerm,
            $manageMatrixPerm,
            $brownfieldImportPerm,
            $slopScannerPerm,
            $cliBootstrapPerm,
            $blueprintDoctorPerm,
            $manageDay2OpsPerm,
            $exportPrintOpsPerm,
            $manageChangeRequestsPerm,
            $connectMcpBridgePerm,
            $runRedteamPerm,
            $validateEnvPerm,
            $manageMitraQuotationsPerm,
            $freezeScopeSpkPerm,
            $approvePaidAddendumPerm,
            $signOffUatBastPerm,
            $manageEnterpriseMenuPerm,
            $compileSymbolDagPerm,
            $overrideFiscalMakerCheckerPerm,
            $manage15PillarsPerm,
            $configureHardwareWsPerm,
            $generateClientSopPerm,
            $manageInclusiveGrantsPerm,
            $verifyCanaryWatermarksPerm,
            $broadcastLivingCvePerm,
            $manageCommunityMarketplacePerm,
            $manageAgentShieldPerm,
            $participateCoDesignPerm,
            $executeDualKeyLockPerm,
            $verifyCryptoAuditPerm,
            $export3SheetAuditPerm,
            $manageVerifiedTestimonialsPerm,
            $configureDualZoneUxPerm,
            $manageAffiliatePerm,
            $approveImpactGrantsPerm,
            $manageOrgWorkspacesPerm,
            $viewSaasHealthPulsePerm,
            $manageSeoGeoAuthorityPerm,
            $manageUuPdpPrivacyPerm,
            $manageSlaWarrantyPerm,
            $runSandboxWebhookPerm,
            $manage20PillarsPerm,
            $manageAiKnowledgePerm,
            $manageScaffoldWorkspacesPerm,
            $managePricingTiersPerm,
        ]);

        $developerRole->givePermissionTo([
            $aiCopilotPerm,
            $executeAiToolsPerm,
            $twoFactorPerm,
            $viewMatrixPerm,
            $brownfieldImportPerm,
            $slopScannerPerm,
            $cliBootstrapPerm,
            $blueprintDoctorPerm,
            $manageDay2OpsPerm,
            $exportPrintOpsPerm,
            $manageChangeRequestsPerm,
            $connectMcpBridgePerm,
            $runRedteamPerm,
            $validateEnvPerm,
            $manageMitraQuotationsPerm,
            $freezeScopeSpkPerm,
            $approvePaidAddendumPerm,
            $signOffUatBastPerm,
            $manageEnterpriseMenuPerm,
            $compileSymbolDagPerm,
            $configureHardwareWsPerm,
            $generateClientSopPerm,
            $verifyCanaryWatermarksPerm,
            $broadcastLivingCvePerm,
            $manageAgentShieldPerm,
            $participateCoDesignPerm,
            $executeDualKeyLockPerm,
            $verifyCryptoAuditPerm,
            $export3SheetAuditPerm,
            $manageVerifiedTestimonialsPerm,
            $configureDualZoneUxPerm,
            $viewSaasHealthPulsePerm,
            $manageSeoGeoAuthorityPerm,
            $manageUuPdpPrivacyPerm,
            $manageSlaWarrantyPerm,
            $runSandboxWebhookPerm,
            $manage20PillarsPerm,
            $manageAiKnowledgePerm,
            $manageScaffoldWorkspacesPerm,
            $managePricingTiersPerm,
        ]);

        // Grant Due Diligence Read-Only permissions to Investor & Superadmin
        $investorRole->givePermissionTo([
            $executiveGuidePerm,
            $viewMatrixPerm,
            $aiCopilotPerm,
            $auditLogPerm,
            $blueprintDoctorPerm,
            $runRedteamPerm,
            $validateEnvPerm,
            $compileSymbolDagPerm,
            $generateClientSopPerm,
            $verifyCryptoAuditPerm,
            $export3SheetAuditPerm,
            $participateCoDesignPerm,
            $viewSaasHealthPulsePerm,
        ]);

        // Retail & Partner Clients can export CLI bootstrapper, print/ops specs, connect MCP bridge, & manage iterative Delta CRs for their own licensed blueprints
        $clientRetailRole->givePermissionTo([
            $cliBootstrapPerm,
            $blueprintDoctorPerm,
            $exportPrintOpsPerm,
            $manageChangeRequestsPerm,
            $connectMcpBridgePerm,
            $validateEnvPerm,
            $manageEnterpriseMenuPerm,
            $compileSymbolDagPerm,
            $generateClientSopPerm,
            $participateCoDesignPerm,
            $configureDualZoneUxPerm,
            $manageOrgWorkspacesPerm,
        ]);
        $clientPartnerRole->givePermissionTo([
            $cliBootstrapPerm,
            $brownfieldImportPerm,
            $blueprintDoctorPerm,
            $exportPrintOpsPerm,
            $manageChangeRequestsPerm,
            $connectMcpBridgePerm,
            $runRedteamPerm,
            $validateEnvPerm,
            $freezeScopeSpkPerm,
            $approvePaidAddendumPerm,
            $signOffUatBastPerm,
            $manageEnterpriseMenuPerm,
            $compileSymbolDagPerm,
            $configureHardwareWsPerm,
            $generateClientSopPerm,
            $participateCoDesignPerm,
            $executeDualKeyLockPerm,
            $configureDualZoneUxPerm,
            $manageOrgWorkspacesPerm,
        ]);

        // 1. Primary Superadmin (Founder & Chief Architect - Yoseph Iriandi Tambunan)
        $admin = User::updateOrCreate(
            ['email' => 'yoseph.iriandi.tambunan@gmail.com'],
            [
                'name' => 'Yoseph Iriandi Tambunan',
                'password' => Hash::make('#T4mbun4n#'),
                'email_verified_at' => now(),
            ]
        );

        if (! $admin->hasRole('super_admin')) {
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
        if (! $reviewer->hasRole('midtrans_reviewer')) {
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
            if (! $reviewerRole->hasPermissionTo($perm)) {
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
            if (! $developerRole->hasPermissionTo($perm)) {
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
            if (! $investorRole->hasPermissionTo($perm)) {
                $investorRole->givePermissionTo($perm);
            }
        }
    }
}
