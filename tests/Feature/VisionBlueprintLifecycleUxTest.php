<?php

namespace Tests\Feature;

use App\Filament\Resources\VisionBlueprints\Pages\ListVisionBlueprints;
use App\Models\Document;
use App\Models\User;
use App\Models\VisionBlueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VisionBlueprintLifecycleUxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'yoseph.iriandi.tambunan@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($admin);
    }

    public function test_prospecting_row_actions_visibility(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'John Doe',
            'nama_bisnis' => 'Acme Corp',
            'email' => 'john@acme.com',
            'project_status' => 'Prospecting',
            'is_published' => true,
        ]);

        Livewire::test(ListVisionBlueprints::class)
            ->assertTableActionVisible('regenerate_prd', $blueprint)
            ->assertTableActionVisible('convert_to_contract', $blueprint)
            ->assertTableActionHidden('view_contract', $blueprint)
            ->assertTableActionHidden('sign_contract', $blueprint)
            ->assertTableActionHidden('confirm_dp', $blueprint)
            ->assertTableActionHidden('view_staging', $blueprint);
    }

    public function test_contract_created_row_actions_visibility(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Jane Smith',
            'nama_bisnis' => 'Nova Logistics',
            'email' => 'jane@novalogistics.com',
            'project_status' => 'Contract Created',
            'is_published' => true,
        ]);

        Document::create([
            'title' => 'Contract - Nova Logistics',
            'document_type' => 'contract',
            'related_type' => VisionBlueprint::class,
            'related_id' => $blueprint->id,
            'status' => 'pending_signature',
            'scope_locked' => true,
            'contract_amount' => 50000000,
            'dp_amount' => 25000000,
        ]);

        Livewire::test(ListVisionBlueprints::class)
            ->assertTableActionHidden('convert_to_contract', $blueprint)
            ->assertTableActionHidden('regenerate_prd', $blueprint)
            ->assertTableActionVisible('view_contract', $blueprint)
            ->assertTableActionVisible('sign_contract', $blueprint)
            ->assertTableActionHidden('confirm_dp', $blueprint);
    }

    public function test_awaiting_dp_payment_row_actions_visibility(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'dr. Hendra Pratama',
            'nama_bisnis' => 'Medika Prima Telehealth',
            'email' => 'dr.hendra@medikaprima.id',
            'project_status' => 'Awaiting DP Payment',
            'is_published' => true,
        ]);

        Document::create([
            'title' => 'Contract - Medika Prima',
            'document_type' => 'contract',
            'related_type' => VisionBlueprint::class,
            'related_id' => $blueprint->id,
            'status' => 'signed',
            'scope_locked' => true,
            'contract_amount' => 40000000,
            'dp_amount' => 20000000,
            'midtrans_payment_url' => 'https://app.midtrans.com/snap/v2/vtweb/mock-payment',
        ]);

        Livewire::test(ListVisionBlueprints::class)
            ->assertTableActionHidden('convert_to_contract', $blueprint)
            ->assertTableActionHidden('regenerate_prd', $blueprint)
            ->assertTableActionHidden('sign_contract', $blueprint)
            ->assertTableActionVisible('view_contract', $blueprint)
            ->assertTableActionVisible('confirm_dp', $blueprint)
            ->assertTableActionVisible('view_dp_invoice', $blueprint);
    }

    public function test_active_sprint_row_actions_visibility(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Alexander Wijaya',
            'nama_bisnis' => 'Apex Logistics Global',
            'email' => 'alexander@apexlogistics.co.id',
            'project_status' => 'Active Sprint',
            'is_published' => true,
            'staging_url' => 'https://apex.staging.neriahpro.com',
        ]);

        Document::create([
            'title' => 'Contract - Apex Logistics',
            'document_type' => 'contract',
            'related_type' => VisionBlueprint::class,
            'related_id' => $blueprint->id,
            'status' => 'signed',
            'scope_locked' => true,
            'contract_amount' => 50000000,
            'dp_amount' => 25000000,
        ]);

        Livewire::test(ListVisionBlueprints::class)
            ->assertTableActionHidden('convert_to_contract', $blueprint)
            ->assertTableActionHidden('regenerate_prd', $blueprint)
            ->assertTableActionHidden('confirm_dp', $blueprint)
            ->assertTableActionVisible('view_contract', $blueprint)
            ->assertTableActionVisible('view_staging', $blueprint)
            ->assertTableActionVisible('mark_completed', $blueprint);
    }

    public function test_confirm_dp_action_execution(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'dr. Hendra Pratama',
            'nama_bisnis' => 'Medika Prima Telehealth',
            'email' => 'dr.hendra@medikaprima.id',
            'project_status' => 'Awaiting DP Payment',
            'is_published' => true,
        ]);

        $contract = Document::create([
            'title' => 'Contract - Medika Prima',
            'document_type' => 'contract',
            'related_type' => VisionBlueprint::class,
            'related_id' => $blueprint->id,
            'status' => 'pending_signature',
            'scope_locked' => false,
            'contract_amount' => 40000000,
            'dp_amount' => 20000000,
        ]);

        Livewire::test(ListVisionBlueprints::class)
            ->callTableAction('confirm_dp', $blueprint);

        $blueprint->refresh();
        $contract->refresh();

        $this->assertEquals('Active Sprint', $blueprint->project_status);
        $this->assertNotEmpty($blueprint->staging_url);
        $this->assertEquals('signed', $contract->status);
        $this->assertTrue($contract->scope_locked);
    }

    public function test_mark_completed_action_execution(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Alexander Wijaya',
            'nama_bisnis' => 'Apex Logistics Global',
            'email' => 'alexander@apexlogistics.co.id',
            'project_status' => 'Active Sprint',
            'is_published' => true,
        ]);

        Livewire::test(ListVisionBlueprints::class)
            ->callTableAction('mark_completed', $blueprint);

        $blueprint->refresh();
        $this->assertEquals('Completed', $blueprint->project_status);
    }
}
