<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmartGuideRetentionAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SuperAdminSeeder::class);
    }

    /**
     * Test that Super Admin Yoseph Iriandi Tambunan CAN access Smart Guide Retention page.
     */
    public function test_superadmin_yoseph_can_access_smart_guide_retention(): void
    {
        $superadmin = User::where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($superadmin);

        $this->assertTrue(\App\Filament\Pages\SmartGuideRetentionPage::canAccess());

        $response = $this->get(\App\Filament\Pages\SmartGuideRetentionPage::getUrl());

        $response->assertStatus(200);
        $response->assertSee('Executive Smart Guide');
        $response->assertSee('Pay-Per-Project');
        $response->assertSee('GROSS MARGIN: 98.6%');
        $response->assertSee('Trojan Horse');
        $response->assertSee('Studio MVP');
    }

    /**
     * Test that Midtrans QA Reviewer is STRICTLY DENIED (403 Forbidden).
     */
    public function test_midtrans_reviewer_is_forbidden_from_smart_guide_retention(): void
    {
        $reviewer = User::where('email', 'reviewer.midtrans@neriahpro.com')->firstOrFail();

        $this->actingAs($reviewer);

        // Verification on Policy/Gate level
        $this->assertFalse(\App\Filament\Pages\SmartGuideRetentionPage::canAccess());

        // Verification on HTTP route level
        $response = $this->get(\App\Filament\Pages\SmartGuideRetentionPage::getUrl());

        $response->assertStatus(403);
    }

    /**
     * Test that an arbitrary non-superadmin user is also denied.
     */
    public function test_arbitrary_user_is_forbidden_from_smart_guide_retention(): void
    {
        $user = User::create([
            'name' => 'Regular Customer',
            'email' => 'client@customer.com',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($user);

        $this->assertFalse(\App\Filament\Pages\SmartGuideRetentionPage::canAccess());

        $response = $this->get(\App\Filament\Pages\SmartGuideRetentionPage::getUrl());

        $response->assertStatus(403);
    }

    /**
     * Test that Calon Investor CAN access Smart Guide Retention for Due Diligence.
     */
    public function test_calon_investor_can_access_smart_guide_for_due_diligence(): void
    {
        $investor = User::create([
            'name' => 'Venture Capital Partner',
            'email' => 'investor@venturefund.com',
            'password' => bcrypt('InvestorSecurePass2026#'),
        ]);
        $investor->assignRole('investor');

        $this->actingAs($investor);

        $this->assertTrue(\App\Filament\Pages\SmartGuideRetentionPage::canAccess());

        $response = $this->get(\App\Filament\Pages\SmartGuideRetentionPage::getUrl());

        $response->assertStatus(200);
        $response->assertSee('Executive Smart Guide');
        $response->assertSee('GROSS MARGIN: 98.6%');
    }

    /**
     * Test that Retail and Partner Clients are STRICTLY FORBIDDEN from internal unit economics.
     */
    public function test_clients_are_strictly_forbidden_from_smart_guide(): void
    {
        $retailClient = User::create([
            'name' => 'Retail Customer',
            'email' => 'retail@client.com',
            'password' => bcrypt('pass12345'),
        ]);
        $retailClient->assignRole('client_retail');

        $partnerClient = User::create([
            'name' => 'Enterprise Partner',
            'email' => 'partner@company.com',
            'password' => bcrypt('pass12345'),
        ]);
        $partnerClient->assignRole('client_partner');

        $this->actingAs($retailClient);
        $this->assertFalse(\App\Filament\Pages\SmartGuideRetentionPage::canAccess());

        $this->actingAs($partnerClient);
        $this->assertFalse(\App\Filament\Pages\SmartGuideRetentionPage::canAccess());
    }

    /**
     * Test that Team Developer access is dynamically governed by permission.
     */
    public function test_developer_access_is_governed_by_permission(): void
    {
        $dev = User::create([
            'name' => 'Backend Engineer',
            'email' => 'dev@neriahpro.com',
            'password' => bcrypt('DevPass2026#'),
        ]);
        $dev->assignRole('developer');

        // Without executive guide permission -> forbidden
        $this->actingAs($dev);
        $this->assertFalse(\App\Filament\Pages\SmartGuideRetentionPage::canAccess());

        // When granted permission by superadmin -> allowed
        $dev->givePermissionTo('view_executive_smart_guide');
        $this->assertTrue(\App\Filament\Pages\SmartGuideRetentionPage::canAccess());
    }

    /**
     * Test that unauthenticated guest is redirected to login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(\App\Filament\Pages\SmartGuideRetentionPage::getUrl());

        $response->assertRedirect('/admin/login');
    }
}
