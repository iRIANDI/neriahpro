<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Facades\DB;

class AiKnowledgeVector extends Model
{
    use HasUlids;

    protected $table = 'ai_knowledge_vectors';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function setEmbeddingAttribute($value): void
    {
        if (is_array($value)) {
            if (DB::getDriverName() === 'pgsql') {
                $this->attributes['embedding'] = '[' . implode(',', $value) . ']';
            }
            $this->attributes['embedding_json'] = json_encode($value);
        } elseif (is_string($value)) {
            if (DB::getDriverName() === 'pgsql') {
                $this->attributes['embedding'] = $value;
            }
            $this->attributes['embedding_json'] = $value;
        } else {
            if (DB::getDriverName() === 'pgsql') {
                $this->attributes['embedding'] = null;
            }
            $this->attributes['embedding_json'] = null;
        }
    }

    public function getEmbeddingAttribute($value): ?array
    {
        if (empty($value)) {
            return !empty($this->embedding_json) ? json_decode($this->embedding_json, true) : null;
        }
        if (is_array($value)) return $value;
        $trimmed = trim($value, '[]');
        if ($trimmed === '') return [];
        return array_map('floatval', explode(',', $trimmed));
    }

    /**
     * Scope untuk pencarian semantik kemiripan kosinus via PostgreSQL pgvector.
     */
    public function scopeSearchSimilar($query, array $vector, int $limit = 5)
    {
        if (empty($vector)) {
            return $query->whereRaw('1 = 0');
        }

        if (DB::getDriverName() === 'pgsql') {
            $vectorStr = '[' . implode(',', $vector) . ']';

            return $query->select('*')
                ->selectRaw('1 - (embedding <=> ?) as similarity_score', [$vectorStr])
                ->whereNotNull('embedding')
                ->orderByRaw('embedding <=> ? asc', [$vectorStr])
                ->limit($limit);
        }

        return $query->whereRaw('1 = 0');
    }
}

