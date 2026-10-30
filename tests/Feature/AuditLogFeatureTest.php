<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\DomainHostingAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_records_model_creation_and_updates(): void
    {
        $user = User::factory()->create(['name' => 'Architect Lead']);
        $this->actingAs($user);

        // Create domain asset (which uses Auditable trait)
        $asset = DomainHostingAsset::create([
            'name' => 'Audit Test Domain',
            'domain_name' => 'audittest.com',
            'asset_type' => 'domain',
            'provider' => 'Niagahoster',
            'purchase_date' => now(),
            'expires_at' => now()->addYear(),
            'cost_price' => 150000.50,
            'client_price' => 300000.75,
            'status' => 'active',
            'auto_renew' => true,
        ]);

        // Verify creation was recorded in audit_logs
        $createLog = AuditLog::where('model_type', DomainHostingAsset::class)
            ->where('model_id', $asset->id)
            ->where('action', 'created')
            ->first();

        $this->assertNotNull($createLog);
        $this->assertEquals($user->id, $createLog->user_id);
        $this->assertArrayHasKey('domain_name', $createLog->new_values);

        // Update the asset
        $asset->update([
            'name' => 'Updated Domain Name',
            'client_price' => 350000.00,
        ]);

        // Verify update was recorded with diff
        $updateLog = AuditLog::where('model_type', DomainHostingAsset::class)
            ->where('model_id', $asset->id)
            ->where('action', 'updated')
            ->first();

        $this->assertNotNull($updateLog);
        $this->assertEquals('Audit Test Domain', $updateLog->old_values['name']);
        $this->assertEquals('Updated Domain Name', $updateLog->new_values['name']);
    }

    public function test_manual_audit_log_recording(): void
    {
        $user = User::factory()->create(['name' => 'Security Admin']);
        $this->actingAs($user);

        $log = AuditLog::record(
            action: 'security',
            model: null,
            newValues: ['event' => '2fa_enabled'],
            actorName: 'Security Admin'
        );

        $this->assertNotNull($log);
        $this->assertEquals('security', $log->action);
        $this->assertEquals('Security Admin', $log->actor_name);
        $this->assertEquals('2fa_enabled', $log->new_values['event']);
    }
}
