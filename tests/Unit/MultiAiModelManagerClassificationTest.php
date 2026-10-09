<?php

namespace Tests\Unit;

use App\Services\Ai\MultiAiModelManager;
use Tests\TestCase;

class MultiAiModelManagerClassificationTest extends TestCase
{
    public function test_non_chat_models_are_correctly_identified()
    {
        $this->assertTrue(MultiAiModelManager::isNonChatModel('text-embedding-3-small'));
        $this->assertTrue(MultiAiModelManager::isNonChatModel('text-embedding-3-large'));
        $this->assertTrue(MultiAiModelManager::isNonChatModel('bge-m3'));
        $this->assertTrue(MultiAiModelManager::isNonChatModel('whisper-1'));
        $this->assertTrue(MultiAiModelManager::isNonChatModel('tts-1'));
        $this->assertTrue(MultiAiModelManager::isNonChatModel('dall-e-3'));
        $this->assertTrue(MultiAiModelManager::isNonChatModel('text-moderation-latest'));
        $this->assertTrue(MultiAiModelManager::isNonChatModel('rerank-english-v3.0'));

        // Legitimate chat models must NOT be flagged as non-chat
        $this->assertFalse(MultiAiModelManager::isNonChatModel('gpt-4o-mini'));
        $this->assertFalse(MultiAiModelManager::isNonChatModel('claude-3-7-sonnet-20250219'));
        $this->assertFalse(MultiAiModelManager::isNonChatModel('deepseek-chat'));
        $this->assertFalse(MultiAiModelManager::isNonChatModel('deepseek-reasoner'));
        $this->assertFalse(MultiAiModelManager::isNonChatModel('gemini-2.0-flash'));
        $this->assertFalse(MultiAiModelManager::isNonChatModel('qwen-2.5-72b-instruct'));
    }

    public function test_model_tier_classification_logic()
    {
        // 1. Discovery / Fast / Economical tier
        $this->assertEquals('discovery', MultiAiModelManager::classifyModelTier('gpt-4o-mini'));
        $this->assertEquals('discovery', MultiAiModelManager::classifyModelTier('gemini-2.0-flash-lite'));
        $this->assertEquals('discovery', MultiAiModelManager::classifyModelTier('gemini-2.5-flash'));
        $this->assertEquals('discovery', MultiAiModelManager::classifyModelTier('claude-3-5-haiku-20241022'));
        $this->assertEquals('discovery', MultiAiModelManager::classifyModelTier('deepseek-chat'));
        $this->assertEquals('discovery', MultiAiModelManager::classifyModelTier('deepseek-v3'));
        $this->assertEquals('discovery', MultiAiModelManager::classifyModelTier('meta-llama/llama-3.1-8b-instruct'));
        $this->assertEquals('discovery', MultiAiModelManager::classifyModelTier('qwen-2.5-coder-7b'));
        $this->assertEquals('discovery', MultiAiModelManager::classifyModelTier('mistral-small-latest'));

        // 2. PRD & Complex Architecture tier
        $this->assertEquals('prd', MultiAiModelManager::classifyModelTier('claude-3-7-sonnet-20250219'));
        $this->assertEquals('prd', MultiAiModelManager::classifyModelTier('claude-sonnet-4-5-20250929'));
        $this->assertEquals('prd', MultiAiModelManager::classifyModelTier('claude-3-5-sonnet-20241022'));
        $this->assertEquals('prd', MultiAiModelManager::classifyModelTier('deepseek-reasoner'));
        $this->assertEquals('prd', MultiAiModelManager::classifyModelTier('deepseek-r1'));
        $this->assertEquals('prd', MultiAiModelManager::classifyModelTier('gpt-4o'));
        $this->assertEquals('prd', MultiAiModelManager::classifyModelTier('o1'));
        $this->assertEquals('prd', MultiAiModelManager::classifyModelTier('o3-mini'));
        $this->assertEquals('prd', MultiAiModelManager::classifyModelTier('gemini-2.5-pro'));
        $this->assertEquals('prd', MultiAiModelManager::classifyModelTier('gemini-2.0-pro-exp'));
        $this->assertEquals('prd', MultiAiModelManager::classifyModelTier('qwen-2.5-72b-instruct'));
        $this->assertEquals('prd', MultiAiModelManager::classifyModelTier('meta-llama/llama-3.3-70b-instruct'));
        $this->assertEquals('prd', MultiAiModelManager::classifyModelTier('grok-2-1212'));
    }

    public function test_gemini_pro_does_not_falsely_match_mini()
    {
        // Critical boundary test: 'gemini' has 'mini' as substring, must NOT be categorized as discovery unless it's flash/lite
        $this->assertEquals('prd', MultiAiModelManager::classifyModelTier('gemini-2.5-pro'));
        $this->assertEquals('prd', MultiAiModelManager::classifyModelTier('gemini-1.5-pro'));
    }

    public function test_categorize_and_sort_models_filters_junk_and_groups_properly()
    {
        $raw = [
            'text-embedding-3-small',
            'gpt-4o-mini',
            'whisper-1',
            'claude-3-7-sonnet-20250219',
            'dall-e-3',
            'deepseek-reasoner',
            'deepseek-chat',
            'gemini-2.0-flash',
            'bge-m3',
            'qwen-2.5-72b-instruct',
        ];

        $categorized = MultiAiModelManager::categorizeAndSortModels($raw);

        $this->assertArrayHasKey('discovery', $categorized);
        $this->assertArrayHasKey('prd', $categorized);
        $this->assertArrayHasKey('all_valid', $categorized);

        // Junk non-chat models must be excluded from all_valid
        $this->assertNotContains('text-embedding-3-small', $categorized['all_valid']);
        $this->assertNotContains('whisper-1', $categorized['all_valid']);
        $this->assertNotContains('dall-e-3', $categorized['all_valid']);
        $this->assertNotContains('bge-m3', $categorized['all_valid']);

        // Discovery group must have fast models
        $this->assertContains('gpt-4o-mini', $categorized['discovery']);
        $this->assertContains('deepseek-chat', $categorized['discovery']);
        $this->assertContains('gemini-2.0-flash', $categorized['discovery']);

        // PRD group must have complex/reasoning models
        $this->assertContains('claude-3-7-sonnet-20250219', $categorized['prd']);
        $this->assertContains('deepseek-reasoner', $categorized['prd']);
        $this->assertContains('qwen-2.5-72b-instruct', $categorized['prd']);
    }

    public function test_get_grouped_model_options_structure()
    {
        $optionsDiscovery = MultiAiModelManager::getGroupedModelOptions('relayrouter', 'discovery');
        $this->assertIsArray($optionsDiscovery);
        $this->assertArrayHasKey('⚡ Rekomendasi Utama (Discovery & Audit Cepat)', $optionsDiscovery);

        $optionsPrd = MultiAiModelManager::getGroupedModelOptions('relayrouter', 'prd');
        $this->assertIsArray($optionsPrd);
        $this->assertArrayHasKey('🏆 Rekomendasi Utama (PRD & Arsitektur Kompleks)', $optionsPrd);

        // Test custom value preservation
        $optionsWithCustom = MultiAiModelManager::getGroupedModelOptions('relayrouter', 'prd', 'my-custom-model-2026');
        $this->assertArrayHasKey('📌 Model Aktif Saat Ini', $optionsWithCustom);
        $this->assertArrayHasKey('my-custom-model-2026', $optionsWithCustom['📌 Model Aktif Saat Ini']);
    }
}
