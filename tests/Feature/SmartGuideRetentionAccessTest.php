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
        $response->assertSee('Gross Margin: 98.6%');
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
     * Test that unauthenticated guest is redirected to login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(\App\Filament\Pages\SmartGuideRetentionPage::getUrl());

        $response->assertRedirect('/admin/login');
    }
}
