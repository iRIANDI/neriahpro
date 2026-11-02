<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Resume;
use App\Models\User;
use App\Services\Ai\AgenticActionExecutionEngine;
use App\Services\Ai\GeminiToolRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CvProLegalComplianceAndGovernanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_legal_policies_render_four_cv_compliance_pillars_in_dual_locale(): void
    {
        // 1. Terms of Service ID
        $resTermsId = $this->get('/terms?lang=id');
        $resTermsId->assertStatus(200);
        $resTermsId->assertSee('CV Pro Studio');
        $resTermsId->assertSee('Disclaimer of Warranty');
        $resTermsId->assertSee('SIL Open Font License');

        // 2. Terms of Service EN
        $resTermsEn = $this->get('/terms?lang=en');
        $resTermsEn->assertStatus(200);
        $resTermsEn->assertSee('CV Pro Studio');
        $resTermsEn->assertSee('Disclaimer of Warranty');
        $resTermsEn->assertSee('Royalty-Free Typography');

        // 3. Privacy Policy ID (UU PDP No. 27/2022)
        $resPrivacyId = $this->get('/privacy-policy?lang=id');
        $resPrivacyId->assertStatus(200);
        $resPrivacyId->assertSee('UU PDP No. 27/2022');
        $resPrivacyId->assertSee('Zero Data Brokering');
        $resPrivacyId->assertSee('Hak Penghapusan Data Pribadi (Right to Erasure)');

        // 4. Refund Policy ID
        $resRefundId = $this->get('/refund-policy?lang=id');
        $resRefundId->assertStatus(200);
        $resRefundId->assertSee('Token AI');
        $resRefundId->assertSee('No Refund for Consumed Digital Goods');
    }

    public function test_cv_pro_purge_data_executes_right_to_erasure_and_records_audit_log(): void
    {
        $user = User::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi@example.com']);

        $resume = Resume::create([
            'user_id' => $user->id,
            'title' => 'Software Engineer Resume',
            'target_role' => 'Senior Developer',
            'content' => ['personal_info' => ['name' => 'Budi Santoso', 'email' => 'budi@example.com']],
        ]);

        $this->assertDatabaseHas('resumes', ['id' => $resume->id]);

        $res = $this->actingAs($user)->postJson('/api/cv-pro/purge-data', [
            'resume_id' => $resume->id,
            'lang' => 'id',
        ]);

        $res->assertStatus(200);
        $res->assertJsonFragment([
            'success' => true,
            'purged_count' => 1,
        ]);

        // Verifikasi resume terhapus
        $this->assertDatabaseMissing('resumes', ['id' => $resume->id]);

        // Verifikasi AuditLog tercatat
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'CV_USER_DATA_PURGED_UU_PDP',
            'event_category' => 'privacy_compliance',
        ]);
    }

    public function test_cv_pro_compliance_status_endpoint_returns_all_four_certified_pillars(): void
    {
        $res = $this->getJson('/api/cv-pro/compliance-status');
        $res->assertStatus(200);
        $res->assertJsonPath('success', true);
        $res->assertJsonPath('governance_certified', true);
        $res->assertJsonPath('pillars.uu_pdp_compliance.status', 'ACTIVE');
        $res->assertJsonPath('pillars.content_disclaimer.status', 'ACTIVE');
        $res->assertJsonPath('pillars.royalty_free_licensing.status', 'ACTIVE');
        $res->assertJsonPath('pillars.digital_goods_policy.status', 'ACTIVE');
    }

    public function test_native_ai_tool_audit_cv_pro_compliance_and_governance_executes_cleanly(): void
    {
        $declarations = GeminiToolRegistry::getDeclarations();
        $toolNames = array_column($declarations, 'name');
        $this->assertContains('audit_cv_pro_compliance_and_governance', $toolNames);

        $engine = app(AgenticActionExecutionEngine::class);

        $resAudit = $engine->execute('audit_cv_pro_compliance_and_governance', [
            'action' => 'audit_compliance',
        ]);

        $this->assertTrue($resAudit['success']);
        $this->assertTrue($resAudit['governance_certified']);
        $this->assertSame(100, $resAudit['compliance_score_percent']);
        $this->assertArrayHasKey('uu_pdp_anti_brokering', $resAudit['four_pillars']);
        $this->assertArrayHasKey('content_disclaimer', $resAudit['four_pillars']);
        $this->assertArrayHasKey('royalty_free_licensing', $resAudit['four_pillars']);
        $this->assertArrayHasKey('digital_goods_refund_policy', $resAudit['four_pillars']);
    }
}
