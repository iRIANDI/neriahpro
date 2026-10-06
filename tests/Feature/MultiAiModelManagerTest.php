<?php

namespace Tests\Feature;

use App\Services\Ai\MultiAiModelManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MultiAiModelManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_returns_all_models_with_health_status_and_free_badges()
    {
        $catalog = MultiAiModelManager::getCatalog();

        $this->assertIsArray($catalog);
        $this->assertArrayHasKey('deepseek', $catalog);
        $this->assertArrayHasKey('gemini', $catalog);
        $this->assertArrayHasKey('anthropic', $catalog);
        $this->assertArrayHasKey('openai', $catalog);
        $this->assertArrayHasKey('xai', $catalog);
        $this->assertArrayHasKey('groq', $catalog);
        $this->assertArrayHasKey('openrouter', $catalog);

        // Verify free tier flag
        $this->assertTrue($catalog['groq']['is_free']);
        $this->assertTrue($catalog['openrouter']['is_free']);
        $this->assertFalse($catalog['anthropic']['is_free']);

        // Verify status fields
        foreach ($catalog as $provider) {
            $this->assertArrayHasKey('status', $provider);
            $this->assertArrayHasKey('status_label', $provider);
            $this->assertArrayHasKey('status_color', $provider);
            $this->assertArrayHasKey('recommended_for', $provider);
        }
    }

    public function test_circuit_breaker_sets_exhausted_status_and_triggers_automatic_failover()
    {
        // 1. Initially clear any cache
        Cache::forget('ai_model_status_deepseek');
        Cache::forget('ai_cooldown_until_deepseek');

        // 2. Simulate 429 Rate Limit / Quota Exhaustion
        MultiAiModelManager::recordFailure('deepseek', '429 Too Many Requests - Token Limit Exceeded', 429);

        // 3. Verify status is 'exhausted' in Cache and Catalog
        $this->assertEquals('exhausted', Cache::get('ai_model_status_deepseek'));
        $catalog = MultiAiModelManager::getCatalog();
        $this->assertEquals('exhausted', $catalog['deepseek']['status']);
        $this->assertEquals('rose', $catalog['deepseek']['status_color']);
        $this->assertGreaterThan(0, $catalog['deepseek']['cooldown_sec']);

        // 4. Verify executeWithFailover skips the exhausted provider
        $result = MultiAiModelManager::executeWithFailover('Test prompt', '', 'discovery', 'deepseek');
        $this->assertIsArray($result);
        
        // Deepseek must be listed in failed_attempts due to cooldown
        $exhaustedAttempt = collect($result['failed_attempts'] ?? [])->firstWhere('provider', 'deepseek');
        $this->assertNotNull($exhaustedAttempt);
        $this->assertStringContainsString('cooldown', strtolower($exhaustedAttempt['reason']));

        // 5. Test recovery
        MultiAiModelManager::recordSuccess('deepseek');
        $this->assertEquals('healthy', Cache::get('ai_model_status_deepseek'));
        $catalogRecovered = MultiAiModelManager::getCatalog();
        $this->assertEquals('healthy', $catalogRecovered['deepseek']['status']);
    }

    public function test_api_models_endpoint_returns_json_catalog()
    {
        $response = $this->getJson(route('api.ai.models'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'models' => [
                'deepseek' => ['key', 'name', 'is_free', 'status', 'status_label', 'status_color'],
                'groq' => ['key', 'name', 'is_free', 'status', 'status_label', 'status_color'],
            ]
        ]);
    }
}
