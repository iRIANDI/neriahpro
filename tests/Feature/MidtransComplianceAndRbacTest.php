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

    public function test_midtrans_reviewer_can_only_access_project_os_and_blocked_from_rbac(): void
    {
        $reviewer = User::where('email', 'reviewer.midtrans@neriahpro.com')->firstOrFail();
        $this->actingAs($reviewer);

        // Project OS MUST be allowed
        $this->assertTrue(\App\Filament\Resources\VisionBlueprints\VisionBlueprintResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\Documents\DocumentResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\DomainHostingAssets\DomainHostingAssetResource::canViewAny());

        // Non-Project OS MUST be strictly forbidden / hidden
        $this->assertFalse(\App\Filament\Resources\CvProPlans\CvProPlanResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\Resumes\ResumeResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\InterviewSessions\InterviewSessionResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\Products\ProductResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\Transactions\TransactionResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\CmsPages\CmsPageResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\SecurityThreats\SecurityThreatResource::canViewAny());

        // Settings Page MUST be hidden
        $this->assertFalse(\App\Filament\Pages\ManageSettings::canAccess());

        // RBAC MUST be blocked
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
        $response->assertSee('tidak berasumsi 100%');
    }
}
