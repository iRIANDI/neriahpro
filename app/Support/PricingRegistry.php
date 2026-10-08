<?php

namespace App\Support;

/**
 * Centralized, Concrete Pricing & Package Registry
 * Strictly avoids magic strings and fuzzy prefix matching.
 */
class PricingRegistry
{
    // Concrete Package IDs
    public const TIER_RETAIL_SPARK = 'retail_spark';
    public const TIER_RETAIL_LITE = 'retail_lite';
    public const TIER_RETAIL_PRO = 'retail_pro';
    public const TIER_RETAIL_ULTIMATE = 'retail_ultimate';

    public const TIER_FULL_MVP = 'full_mvp';
    public const TIER_UMKM_STARTER = 'umkm_starter';
    public const TIER_BLUEPRINT_ADVISORY = 'blueprint_advisory';

    /**
     * Complete immutable specification of all application packages.
     */
    public const PACKAGES = [
        self::TIER_RETAIL_SPARK => [
            'id' => self::TIER_RETAIL_SPARK,
            'name' => 'Spark Free Idea Audit',
            'category' => 'blueprint_self_service',
            'execution_model' => 'self_service',
            'is_paid' => false,
            'requires_login' => false,
            'requires_sprint_batch' => false,
            'requires_kickoff_slot' => false,
            'default_price' => 0,
            'setting_key' => 'pricing_retail_spark_price',
        ],
        self::TIER_RETAIL_LITE => [
            'id' => self::TIER_RETAIL_LITE,
            'name' => 'Lite PRD Generator',
            'category' => 'blueprint_self_service',
            'execution_model' => 'self_service',
            'is_paid' => true,
            'requires_login' => true,
            'requires_sprint_batch' => false,
            'requires_kickoff_slot' => false,
            'default_price' => 99000,
            'setting_key' => 'pricing_retail_lite_price',
        ],
        self::TIER_RETAIL_PRO => [
            'id' => self::TIER_RETAIL_PRO,
            'name' => 'Pro Production PRD',
            'category' => 'blueprint_self_service',
            'execution_model' => 'self_service',
            'is_paid' => true,
            'requires_login' => true,
            'requires_sprint_batch' => false,
            'requires_kickoff_slot' => false,
            'default_price' => 399000,
            'setting_key' => 'pricing_retail_pro_price',
        ],
        self::TIER_RETAIL_ULTIMATE => [
            'id' => self::TIER_RETAIL_ULTIMATE,
            'name' => 'Ultimate Advisory',
            'category' => 'blueprint_self_service',
            'execution_model' => 'self_service',
            'is_paid' => true,
            'requires_login' => true,
            'requires_sprint_batch' => false,
            'requires_kickoff_slot' => false,
            'default_price' => 1490000,
            'setting_key' => 'pricing_retail_ultimate_price',
        ],
        self::TIER_FULL_MVP => [
            'id' => self::TIER_FULL_MVP,
            'name' => 'Enterprise Rapid Monolith MVP',
            'category' => 'studio_engineering',
            'execution_model' => 'studio_contract',
            'is_paid' => true,
            'requires_login' => false,
            'requires_sprint_batch' => true,
            'requires_kickoff_slot' => true,
            'default_price' => 25000000,
            'setting_key' => null,
        ],
        self::TIER_UMKM_STARTER => [
            'id' => self::TIER_UMKM_STARTER,
            'name' => 'UMKM Digital Starter',
            'category' => 'studio_engineering',
            'execution_model' => 'studio_contract',
            'is_paid' => true,
            'requires_login' => false,
            'requires_sprint_batch' => false,
            'requires_kickoff_slot' => false,
            'default_price' => 3750000,
            'setting_key' => null,
        ],
        self::TIER_BLUEPRINT_ADVISORY => [
            'id' => self::TIER_BLUEPRINT_ADVISORY,
            'name' => 'Blueprint & PRD Architecture Advisory',
            'category' => 'studio_engineering',
            'execution_model' => 'studio_contract',
            'is_paid' => true,
            'requires_login' => false,
            'requires_sprint_batch' => false,
            'requires_kickoff_slot' => false,
            'default_price' => 2500000,
            'setting_key' => null,
        ],
    ];

    /**
     * Retrieve package details by exact ID.
     */
    public static function get(string $id): ?array
    {
        return self::PACKAGES[$id] ?? null;
    }

    /**
     * Check if package is self-service (100% digital, automated generation).
     */
    public static function isSelfService(string $id): bool
    {
        return (self::PACKAGES[$id]['execution_model'] ?? null) === 'self_service';
    }

    /**
     * Check if package is studio contract (Neriah Pro team custom engineering).
     */
    public static function isStudioContract(string $id): bool
    {
        return (self::PACKAGES[$id]['execution_model'] ?? null) === 'studio_contract';
    }

    /**
     * Get all concrete self-service package IDs.
     */
    public static function getSelfServiceIds(): array
    {
        return array_keys(array_filter(self::PACKAGES, fn($p) => $p['execution_model'] === 'self_service'));
    }

    /**
     * Get all concrete studio contract package IDs.
     */
    public static function getStudioContractIds(): array
    {
        return array_keys(array_filter(self::PACKAGES, fn($p) => $p['execution_model'] === 'studio_contract'));
    }
}
