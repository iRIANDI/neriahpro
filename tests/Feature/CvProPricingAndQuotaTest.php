<?php

namespace Tests\Feature;

use App\Models\CvProPlan;
use App\Models\CvQuotaTransaction;
use App\Models\User;
use App\Services\CvPro\CvPricingService;
use App\Services\CvPro\CvQuotaService;
use Database\Seeders\CvProPlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CvProPricingAndQuotaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CvProPlanSeeder::class);
    }

    /**
     * Test GET /api/cv-pro/pricing returns dynamic plans and margin economics.
     */
    public function test_pricing_endpoint_returns_plans_and_margin_economics(): void
    {
        $response = $this->getJson('/api/cv-pro/pricing');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'plans',
                'exchange_rate',
                'unit_costs',
            ],
        ]);

        $plans = $response->json('data.plans');
        $this->assertNotEmpty($plans);

        // Verify that paid plans (Pro Career, Ultimate) have healthy gross margins (>80%)
        $proCareer = collect($plans)->firstWhere('model.code', 'pro_career');
        $this->assertNotNull($proCareer);
        $this->assertGreaterThan(80, $proCareer['economics']['margins']['expected_margin_percent']);
        $this->assertTrue($proCareer['economics']['safety_assessment']['is_profitable']);
    }

    /**
     * Test GET /api/cv-pro/quota returns quota for guest and authenticated user.
     */
    public function test_quota_endpoint_for_guest_and_authenticated_user(): void
    {
        // 1. Guest request
        $guestResponse = $this->getJson('/api/cv-pro/quota');
        $guestResponse->assertStatus(200);
        $this->assertTrue($guestResponse->json('is_guest'));

        // 2. Authenticated user request
        $user = User::create([
            'name' => 'Budi Tester',
            'email' => 'budi.tester.' . Str::random(5) . '@example.com',
            'password' => bcrypt('password123'),
        ]);

        $authResponse = $this->actingAs($user)->getJson('/api/cv-pro/quota');
        $authResponse->assertStatus(200);
        $this->assertFalse($authResponse->json('is_guest'));
        $this->assertEquals(1, $authResponse->json('quota.tailor_cv_remaining'));
    }

    /**
     * Test applying plan and consuming quota.
     */
    public function test_applying_plan_and_consuming_quota(): void
    {
        $user = User::create([
            'name' => 'Siti Pro',
            'email' => 'siti.pro.' . Str::random(5) . '@example.com',
            'password' => bcrypt('password123'),
        ]);

        $proPlan = CvProPlan::where('code', 'pro_career')->firstOrFail();
        CvQuotaService::applyPlan($user, $proPlan);

        $quota = $user->cvQuota()->first();
        $this->assertEquals('pro_career', $quota->tier_code);
        $this->assertEquals(15, $quota->tailor_cv_quota);
        $this->assertEquals(0, $quota->tailor_cv_used);
        $this->assertNotNull($quota->plan_expires_at);

        // Consume 1 tailor quota
        $consumed = CvQuotaService::consume($user, 'tailor_cv', 1, 'Tailor CV test');
        $this->assertTrue($consumed);

        $quota->refresh();
        $this->assertEquals(1, $quota->tailor_cv_used);
        $this->assertEquals(14, $quota->remaining('tailor_cv'));

        // Check transaction audit record
        $this->assertDatabaseHas('cv_quota_transactions', [
            'user_id' => $user->id,
            'type' => 'usage_deduction',
            'feature' => 'tailor_cv',
            'amount' => -1,
        ]);
    }

    /**
     * Test a la carte top-up endpoint.
     */
    public function test_a_la_carte_topup_endpoint(): void
    {
        $user = User::create([
            'name' => 'Ahmad Topup',
            'email' => 'ahmad.topup.' . Str::random(5) . '@example.com',
            'password' => bcrypt('password123'),
        ]);

        $initialQuota = CvQuotaService::getUserQuota($user);
        $initialTailorQuota = $initialQuota->tailor_cv_quota;

        $response = $this->actingAs($user)->postJson('/api/cv-pro/topup', [
            'plan_code' => 'topup_tailor_5',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $initialQuota->refresh();
        $this->assertEquals($initialTailorQuota + 5, $initialQuota->tailor_cv_quota);
    }

    /**
     * Test admin pricing economics projection ensures admin never loses money (no nombok).
     */
    public function test_admin_pricing_economics_zero_loss_guarantee(): void
    {
        $plans = CvProPlan::where('is_active', true)->where('price_idr', '>', 0)->get();

        foreach ($plans as $plan) {
            $econ = CvPricingService::calculatePlanEconomics($plan);
            $this->assertTrue(
                $econ['safety_assessment']['is_profitable'],
                "Plan {$plan->code} must be profitable to avoid admin loss."
            );
            $this->assertGreaterThan(
                0,
                $econ['margins']['worst_case_profit_idr'],
                "Plan {$plan->code} worst-case profit must be greater than 0."
            );
        }
    }
}
