<?php

namespace Tests\Feature;

use App\Models\AiKnowledgeVector;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\Ai\AgenticActionExecutionEngine;
use App\Services\Ai\GeminiToolRegistry;
use App\Services\Ai\VectorEmbeddingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiAgentModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_vector_embedding_service_generates_768_dim_vector_and_chunks_text(): void
    {
        $service = app(VectorEmbeddingService::class);

        // Fallback deterministic embedding produces exactly 768 dimensions
        $embedding = $service->generateDeterministicEmbedding('Neriah Pro Software Factory OS');
        $this->assertCount(768, $embedding);
        $this->assertIsFloat($embedding[0]);

        // Chunking with overlap
        $longText = str_repeat("Ini adalah paragraf arsitektur sistem. ", 80);
        $chunks = $service->chunkText($longText, 300, 50);
        $this->assertGreaterThan(1, count($chunks));
    }

    public function test_gemini_tool_registry_returns_valid_declarations(): void
    {
        $declarations = GeminiToolRegistry::getDeclarations();
        $this->assertNotEmpty($declarations);

        $toolNames = collect($declarations)->pluck('name')->toArray();
        $this->assertContains('search_blueprints', $toolNames);
        $this->assertContains('create_blueprint', $toolNames);
        $this->assertContains('convert_to_contract', $toolNames);
        $this->assertContains('query_knowledge_base', $toolNames);
        $this->assertContains('manage_domain_hosting', $toolNames);
        $this->assertContains('get_system_stats', $toolNames);
    }

    public function test_agentic_action_execution_engine_executes_blueprint_and_system_tools(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $engine = app(AgenticActionExecutionEngine::class);

        // Test create_blueprint tool
        $result = $engine->execute('create_blueprint', [
            'title' => 'E-Commerce Enterprise ERP',
            'client_name' => 'PT Megah Logistik',
            'service_tier' => 'studio_mvp',
            'idea_summary' => 'Sistem ERP Multi-Gudang dengan Keyset Cursor O(1)',
        ]);

        $this->assertTrue($result['success'] ?? false);
        $this->assertDatabaseHas('vision_blueprints', [
            'nama_bisnis' => 'E-Commerce Enterprise ERP',
            'client_name' => 'PT Megah Logistik',
        ]);

        // Test search_blueprints tool
        $searchResult = $engine->execute('search_blueprints', [
            'query' => 'Megah Logistik',
        ]);

        $this->assertEquals(1, $searchResult['total_found']);
        $this->assertEquals('E-Commerce Enterprise ERP', $searchResult['blueprints'][0]['title']);

        // Test get_system_stats tool
        $stats = $engine->execute('get_system_stats', []);
        $this->assertArrayHasKey('total_blueprints', $stats);
        $this->assertGreaterThanOrEqual(1, $stats['total_blueprints']);
    }

    public function test_vector_service_syncs_10_pillars_into_knowledge_base(): void
    {
        $service = app(VectorEmbeddingService::class);
        $res = $service->syncSystemKnowledgeBase(true);

        $this->assertEquals('success', $res['status']);
        $this->assertGreaterThanOrEqual(10, $res['synced_sections']);

        // Verify retrieval finds relevant content
        $retrieved = $service->retrieveRelevantContext('PostgreSQL Strict ULID', 3);
        $this->assertNotEmpty($retrieved);
        $titles = collect($retrieved)->pluck('title')->implode(' ');
        $this->assertStringContainsString('Pilar', $titles);
    }
}
