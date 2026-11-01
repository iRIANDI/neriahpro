<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * HasEncryptedPiiWithBlindIndex — UU PDP (UU No. 27/2022) & GDPR Data Privacy Vault Trait
 *
 * Encrypts sensitive PII fields (NIK, Passport, Phone, Email, Medical/Tax IDs) at rest via Laravel
 * Crypt while maintaining a deterministic 64-character HMAC-SHA256 blind index column so exact
 * equality lookups remain O(1) indexed without full-table decryption in PHP.
 */
trait HasEncryptedPiiWithBlindIndex
{
    public static function bootHasEncryptedPiiWithBlindIndex(): void
    {
        static::saving(static function ($model): void {
            if (! method_exists($model, 'blindIndexMap')) {
                return;
            }

            foreach ($model->blindIndexMap() as $sourceColumn => $blindIndexColumn) {
                $rawValue = $model->getAttribute($sourceColumn);
                if (is_string($rawValue) && trim($rawValue) !== '') {
                    $model->setAttribute($blindIndexColumn, static::computeBlindIndex($rawValue));
                } elseif ($rawValue === null) {
                    $model->setAttribute($blindIndexColumn, null);
                }
            }
        });
    }

    /**
     * Compute deterministic 64-char HMAC-SHA256 blind index normalized for case/whitespace.
     */
    public static function computeBlindIndex(string $plainValue): string
    {
        $normalized = strtolower(trim($plainValue));
        $pepper = (string) config('app.key', 'neriahpro-uu-pdp-blind-index-pepper');

        return hash_hmac('sha256', 'uupdp:v11:'.$normalized, $pepper);
    }

    /**
     * O(1) Indexed Blind Index query scope (`Model::query()->whereBlindIndex('requester_email', $email)`).
     */
    public function scopeWhereBlindIndex(Builder $query, string $sourceOrIndexColumn, string $plainValue): Builder
    {
        $map = method_exists($this, 'blindIndexMap') ? $this->blindIndexMap() : [];
        $indexColumn = $map[$sourceOrIndexColumn] ?? $sourceOrIndexColumn;

        return $query->where($indexColumn, static::computeBlindIndex($plainValue));
    }
}
