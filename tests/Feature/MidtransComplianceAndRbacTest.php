<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\CmsGlobalSetting;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransComplianceAndRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SuperAdminSeeder::class);
    }

    public function test_superadmin_yoseph_has_full_access_to_all_features(): void
    {
        $superadmin = User::where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($superadmin);

        // Project OS Resources
        $this->assertTrue(\App\Filament\Resources\VisionBlueprints\VisionBlueprintResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\Documents\DocumentResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\DomainHostingAssets\DomainHostingAssetResource::canViewAny());

        // Secondary / Non-Project OS Resources
        $this->assertTrue(\App\Filament\Resources\CvProPlans\CvProPlanResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\Resumes\ResumeResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\InterviewSessions\InterviewSessionResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\Products\ProductResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\Transactions\TransactionResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\CmsPages\CmsPageResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\SecurityThreats\SecurityThreatResource::canViewAny());

        // Settings Page
        $this->assertTrue(\App\Filament\Pages\ManageSettings::canAccess());

        // RBAC Access
        $rolePolicy = new \App\Policies\RolePolicy();
        $this->assertTrue($rolePolicy->viewAny($superadmin));
    }

    public function test_midtrans_reviewer_can_only_access_project_os_and_client_transaction_contracts(): void
    {
        $reviewer = User::where('email', 'reviewer.midtrans@neriahpro.com')->firstOrFail();
        $this->actingAs($reviewer);

        // 1. Project OS & Client Architecture MUST be viewable
        $this->assertTrue(\App\Filament\Resources\VisionBlueprints\VisionBlueprintResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\Documents\DocumentResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\DomainHostingAssets\DomainHostingAssetResource::canViewAny());

        // 2. Client Transactions, Services Catalog & Legal Compliance MUST be viewable by reviewer
        $this->assertTrue(\App\Filament\Resources\Transactions\TransactionResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\Products\ProductResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\LegalPolicies\LegalPolicyResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\LeadContacts\LeadContactResource::canViewAny());

        // 3. Reviewer MUST have strictly Read-Only access (No create or delete permissions on live records)
        $this->assertFalse(\App\Filament\Resources\Transactions\TransactionResource::canCreate());
        $this->assertFalse(\App\Filament\Resources\Products\ProductResource::canCreate());
        $this->assertFalse(\App\Filament\Resources\Documents\DocumentResource::canCreate());
        $this->assertFalse(\App\Filament\Resources\LegalPolicies\LegalPolicyResource::canCreate());
        $this->assertFalse(\App\Filament\Resources\LeadContacts\LeadContactResource::canCreate());
        $this->assertFalse(\App\Filament\Resources\DomainHostingAssets\DomainHostingAssetResource::canCreate());

        $this->assertFalse(\App\Filament\Resources\Transactions\TransactionResource::canDeleteAny());
        $this->assertFalse(\App\Filament\Resources\Products\ProductResource::canDeleteAny());
        $this->assertFalse(\App\Filament\Resources\Documents\DocumentResource::canDeleteAny());
        $this->assertFalse(\App\Filament\Resources\LegalPolicies\LegalPolicyResource::canDeleteAny());
        $this->assertFalse(\App\Filament\Resources\LeadContacts\LeadContactResource::canDeleteAny());
        $this->assertFalse(\App\Filament\Resources\DomainHostingAssets\DomainHostingAssetResource::canDeleteAny());

        // 4. Non-Developer & Internal features MUST remain strictly forbidden / hidden
        $this->assertFalse(\App\Filament\Resources\CvProPlans\CvProPlanResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\Resumes\ResumeResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\InterviewSessions\InterviewSessionResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\CmsPages\CmsPageResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\SecurityThreats\SecurityThreatResource::canViewAny());

        // Settings Page MUST be hidden
        $this->assertFalse(\App\Filament\Pages\ManageSettings::canAccess());

        // RBAC Shield MUST be blocked
        $rolePolicy = new \App\Policies\RolePolicy();
        $this->assertFalse($rolePolicy->viewAny($reviewer));
    }

    public function test_admin_dashboard_renders_compliance_statement_for_reviewer(): void
    {
        $reviewer = User::where('email', 'reviewer.midtrans@neriahpro.com')->firstOrFail();
        
        $response = $this->actingAs($reviewer)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('PROJECT OS');
        $response->assertSee('MIDTRANS COMPLIANCE READY');
        $response->assertSee('Yoseph Iriandi Tambunan');
        $response->assertSee('Midtrans Snap');
    }

    /**
     * Test that Midtrans reviewer NEVER sees CV Pro features anywhere on dashboard or navigation.
     */
    public function test_midtrans_reviewer_strictly_sees_no_cv_features_in_dashboard_or_navigation(): void
    {
        $reviewer = User::where('email', 'reviewer.midtrans@neriahpro.com')->firstOrFail();

        $response = $this->actingAs($reviewer)->get('/admin');

        $response->assertStatus(200);

        // Project OS & Client Transaction features MUST be present
        $response->assertSee('PORTAL REVIEWER');
        $response->assertSee('Kuesioner Blueprint');
        $response->assertSee('Daftar Blueprint Proyek');
        $response->assertSee('Kontrak Perjanjian Digital');
        $response->assertSee('Riwayat Transaksi Midtrans');
        $response->assertSee('Katalog Produk');
        $response->assertSee('Kebijakan Refund');
        $response->assertSee('Aset Domain');
        $response->assertSee('Database CRM Leads Klien');
        $response->assertSee('MIDTRANS REVIEWER');

        // All CV features MUST be completely absent
        $response->assertDontSee('Career & CV Pro');
        $response->assertDontSee('Kelola CV & Resume');
        $response->assertDontSee('Interview Sessions');
        $response->assertDontSee('Paket & Kuota CV Pro');
        $response->assertDontSee('Studio CV Pro Enterprise');
        $response->assertDontSee('/cv-pro');
        $response->assertDontSee('/pricing');
        $response->assertDontSee('ATS');
        $response->assertDontSee('Katalog Harga & Kuota');
    }

    /**
     * Test that Midtrans reviewer is forbidden (403) from directly accessing CV resource routes.
     */
    public function test_midtrans_reviewer_direct_access_to_cv_resources_is_forbidden(): void
    {
        $reviewer = User::where('email', 'reviewer.midtrans@neriahpro.com')->firstOrFail();

        // Direct access to CV Resumes
        $response = $this->actingAs($reviewer)->get('/admin/resumes');
        $response->assertStatus(403);

        // Direct access to CV Pro Plans
        $response = $this->actingAs($reviewer)->get('/admin/cv-pro-plans');
        $response->assertStatus(403);

        // Direct access to Interview Sessions
        $response = $this->actingAs($reviewer)->get('/admin/interview-sessions');
        $response->assertStatus(403);
    }
}
