<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VisionBlueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_customer_login(): void
    {
        $response = $this->get('/customer/dashboard');
        $response->assertRedirect(route('customer.login'));
    }

    public function test_portal_route_redirects_to_customer_dashboard(): void
    {
        $response = $this->get('/portal');
        $response->assertRedirect(route('customer.dashboard'));
    }

    public function test_authenticated_customer_can_view_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'client@example.com',
            'name' => 'Acme Corp Client',
        ]);

        $blueprint = VisionBlueprint::create([
            'nama_bisnis' => 'Acme Logistics SaaS',
            'slug' => 'acme-logistics-saas',
            'email' => $user->email,
            'client_name' => $user->name,
            'project_status' => 'Active Sprint',
            'prd_content' => [
                'tier' => 'lite',
                'executive_summary' => 'Acme test summary',
            ],
            'user_metadata' => [
                'tier' => 'lite',
                'dev_progress_percent' => 50,
                'dev_checkpoints' => [
                    'phase_1' => true,
                    'phase_2' => true,
                    'phase_3' => false,
                ],
            ],
        ]);

        $response = $this->actingAs($user)->get('/customer/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Acme Logistics SaaS');
        $response->assertSee('customerDashboardApp()');
        $response->assertSee('cdn.jsdelivr.net/npm/alpinejs');
        $response->assertSee('x-cloak');
        $response->assertSee('Overview');
        $response->assertSee('Studio Projects');
    }
}
