<?php

namespace Tests\Feature;

use App\Filament\Resources\DomainHostingAssets\Pages\CreateDomainHostingAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DomainHostingAssetTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_create_domain_hosting_asset_page(): void
    {
        $this->seed(\Database\Seeders\SuperAdminSeeder::class);
        $superadmin = User::where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();

        $response = $this->actingAs($superadmin)->get('/admin/domain-hosting-assets/create');
        $response->assertStatus(200);
        $response->assertSee('Catat Aset Domain & Hosting Baru');
    }

    public function test_can_create_domain_hosting_asset_with_formatted_currency(): void
    {
        $this->seed(\Database\Seeders\SuperAdminSeeder::class);
        $superadmin = User::where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();

        \Livewire\Livewire::actingAs($superadmin)
            ->test(CreateDomainHostingAsset::class)
            ->fillForm([
                'asset_type' => 'domain',
                'name' => 'Domain Utama NeriahPro',
                'domain_name' => 'neriahpro.com',
                'provider' => 'Niagahoster',
                'purchase_date' => '2026-10-30',
                'expires_at' => '2027-10-30',
                'billing_cycle' => 'yearly',
                'cost_price' => '150,000',
                'client_price' => '250,000',
                'currency' => 'IDR',
                'status' => 'active',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('domain_hosting_assets', [
            'domain_name' => 'neriahpro.com',
            'cost_price' => 150000,
            'client_price' => 250000,
        ]);

        \Livewire\Livewire::actingAs($superadmin)
            ->test(CreateDomainHostingAsset::class)
            ->fillForm([
                'asset_type' => 'hosting',
                'name' => 'Cloud VPS Hetzner',
                'domain_name' => 'hetzner.neriahpro.com',
                'provider' => 'Hetzner',
                'purchase_date' => '2026-10-30',
                'expires_at' => '2027-10-30',
                'billing_cycle' => 'yearly',
                'cost_price' => '1.500.000',
                'client_price' => '2.500.000',
                'currency' => 'IDR',
                'status' => 'active',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('domain_hosting_assets', [
            'domain_name' => 'hetzner.neriahpro.com',
            'cost_price' => 1500000,
            'client_price' => 2500000,
        ]);
    }
}
