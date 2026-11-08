<?php

namespace App\Services\Prd\Strategies;

use App\Services\Prd\PrdFeatureSpecificationService;
use App\Services\PrdGeneratorService;
use Illuminate\Support\Str;

/**
 * Base Abstract Domain Strategy providing common resolution helpers.
 */
abstract class AbstractDomainStrategy implements FeatureDomainStrategyInterface
{
    /**
     * Resolve default model name from feature title.
     */
    protected function resolveModelName(string $title): string
    {
        $titleSlug = Str::slug($title);
        $modelName = Str::studly(Str::singular(explode('-', $titleSlug)[0] ?? 'Record'));
        if (in_array(strtolower($modelName), ['fitur', 'manajemen', 'sistem', 'data'])) {
            $modelName = 'BusinessRecord';
        }
        return $modelName;
    }

    /**
     * Match related ERD table.
     */
    protected function matchTable(string $title, string $desc, array $erdTables): ?array
    {
        return PrdGeneratorService::matchRelatedTable($title, $desc, $erdTables);
    }

    /**
     * Build standard agent directive prompt.
     */
    protected function buildDirective(
        string $featureId,
        string $title,
        string $businessName,
        string $mission,
        array $targetFiles,
        string $verifyCommand,
        ?array $relatedTable = null,
        array $acceptanceCriteria = [],
        ?array $integration = null,
        int $stepIndex = 1
    ): string {
        return PrdFeatureSpecificationService::buildAgentPrompt(
            $featureId,
            $title,
            $businessName,
            $mission,
            $targetFiles,
            $verifyCommand,
            $relatedTable,
            $acceptanceCriteria,
            $integration,
            $stepIndex
        );
    }
}

