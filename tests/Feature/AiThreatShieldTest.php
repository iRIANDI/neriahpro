<?php

namespace Tests\Feature;

use App\Exceptions\SecurityException;
use App\Jobs\ProcessSecureDataset;
use App\Models\SecurityThreatLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AiThreatShieldTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_ai_threat_shield_intercepts_rce_system_call_payload(): void
    {
        $response = $this->postJson('/api/blueprint/analyze-idea', [
            'idea_text' => 'Testing normal text with system("id"); command attempt.',
        ], [
            'REMOTE_ADDR' => '192.168.1.100',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'code' => 'AI_SHIELD_POLICY_VIOLATION',
            'threat_type' => 'rce_system_call',
        ]);

        $this->assertDatabaseHas('security_threat_logs', [
            'ip_address' => '192.168.1.100',
            'threat_type' => 'rce_system_call',
        ]);
    }

    public function test_ai_threat_shield_intercepts_huggingface_dataset_loader_exploit(): void
    {
        // Vector inspired by the Exploit Gym & Hugging Face dataset loader incident
        $exploitPayload = 'from datasets import load_dataset; dataset = datasets.load_dataset("victim/dataset", trust_remote_code=True)';

        $response = $this->postJson('/api/blueprint/analyze-idea', [
            'idea_text' => $exploitPayload,
        ], [
            'REMOTE_ADDR' => '192.168.1.105',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'code' => 'AI_SHIELD_POLICY_VIOLATION',
            'threat_type' => 'python_dataset_loader_exploit',
        ]);

        $this->assertDatabaseHas('security_threat_logs', [
            'ip_address' => '192.168.1.105',
            'threat_type' => 'python_dataset_loader_exploit',
        ]);
    }

    public function test_ai_threat_shield_intercepts_sensitive_path_probing(): void
    {
        $response = $this->getJson('/.env', [
            'REMOTE_ADDR' => '192.168.1.110',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'code' => 'AI_SHIELD_POLICY_VIOLATION',
            'threat_type' => 'autonomous_recon_probing',
        ]);

        $this->assertDatabaseHas('security_threat_logs', [
            'ip_address' => '192.168.1.110',
            'threat_type' => 'autonomous_recon_probing',
        ]);
    }

    public function test_ai_threat_shield_strikes_and_auto_blocks_ip_after_threshold(): void
    {
        $ip = '192.168.1.120';

        // 1st exploit attempt
        $res1 = $this->postJson('/api/blueprint/analyze-idea', [
            'idea_text' => 'eval("phpinfo()");',
        ], ['REMOTE_ADDR' => $ip]);
        $res1->assertStatus(403);
        $res1->assertJson(['is_blocked' => false, 'strikes' => 1]);

        // 2nd exploit attempt
        $res2 = $this->postJson('/api/blueprint/analyze-idea', [
            'idea_text' => 'exec("whoami");',
        ], ['REMOTE_ADDR' => $ip]);
        $res2->assertStatus(403);
        $res2->assertJson(['is_blocked' => false, 'strikes' => 2]);

        // 3rd exploit attempt triggers auto-block
        $res3 = $this->postJson('/api/blueprint/analyze-idea', [
            'idea_text' => 'passthru("cat /etc/passwd");',
        ], ['REMOTE_ADDR' => $ip]);
        $res3->assertStatus(403);
        $res3->assertJson(['is_blocked' => true, 'strikes' => 3]);

        $this->assertTrue(Cache::has("ai_shield_blocked_{$ip}"));

        // Subsequent benign request from blocked IP must be rejected immediately
        $resBlocked = $this->postJson('/api/blueprint/analyze-idea', [
            'idea_text' => 'Just a normal innocent idea text.',
        ], ['REMOTE_ADDR' => $ip]);

        $resBlocked->assertStatus(403);
        $resBlocked->assertJson([
            'code' => 'AI_SHIELD_IP_BLOCKED',
        ]);
    }

    public function test_ai_threat_shield_returns_cyber_defense_html_view_for_browser_requests(): void
    {
        $response = $this->get('/.env', [
            'REMOTE_ADDR' => '192.168.1.130',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml',
        ]);

        $response->assertStatus(403);
        $response->assertSee('NERIAH PRO // CYBER SHIELD');
        $response->assertSee('Permintaan Akses Diblokir');
        $response->assertSee('autonomous_recon_probing');
    }

    public function test_process_secure_dataset_blocks_executable_extensions(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'backdoor.php',
            '<?php echo "malicious payload"; ?>'
        );

        $this->expectException(SecurityException::class);
        $this->expectExceptionMessage('Executable file extension [php] is prohibited');

        ProcessSecureDataset::inspectAndSanitizeUploadedFile($file);
    }

    public function test_process_secure_dataset_blocks_dataset_loader_exploit_payload(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'dataset_loader_exploit.txt',
            'datasets.load_dataset("malicious/repo", trust_remote_code=True)'
        );

        $this->expectException(SecurityException::class);
        $this->expectExceptionMessage('Unsafe dataset loader or deserialization exploit signature detected');

        ProcessSecureDataset::inspectAndSanitizeUploadedFile($file);
    }

    public function test_process_secure_dataset_sanitizes_csv_formula_injection(): void
    {
        Storage::fake('local');
        $csvContent = "Name,Formula,Notes\nAlice,=1+1,Normal\nBob,@SUM(2+2),Injected\nCharlie,-CMD|'/C calc'!A0,Dangerous";
        
        Storage::disk('local')->put('datasets/test.csv', $csvContent);

        $result = ProcessSecureDataset::inspectAndSanitizeUploadedFile('datasets/test.csv', 'local');

        $this->assertTrue($result['is_safe']);
        $this->assertTrue($result['sanitized']);

        $sanitizedContent = Storage::disk('local')->get('datasets/test.csv');
        $this->assertStringContainsString("'=1+1", $sanitizedContent);
        $this->assertStringContainsString("'@SUM", $sanitizedContent);
        $this->assertStringContainsString("'-CMD", $sanitizedContent);
    }

    public function test_cv_upload_endpoint_rejects_malicious_payload_file(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'cv_exploit.txt',
            '<?php system($_GET["cmd"]); ?>'
        );

        $response = $this->postJson('/api/cv-pro/upload-cv', [
            'cv_file' => $file,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'code' => 'SECURE_INGESTION_REJECTED',
        ]);
    }
}
