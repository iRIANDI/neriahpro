<?php

namespace App\Services\Prd\Strategies;

/**
 * Interface FeatureDomainStrategyInterface
 *
 * Defines the contract for modular PRD Feature Domain Analysis strategies.
 */
interface FeatureDomainStrategyInterface
{
    /**
     * Determine if this strategy supports the given feature title & description.
     */
    public function supports(string $title, string $desc, string $lower): bool;

    /**
     * Analyze and produce the detailed feature specification array.
     */
    public function analyze(
        string $title,
        string $desc,
        string $businessName,
        string $clientActor,
        string $primaryActor,
        int $index,
        string $prefix,
        array $erdTables = [],
        bool $isEn = false
    ): array;
}

