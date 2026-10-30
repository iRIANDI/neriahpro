<?php

namespace App\Filament\Pages;

use App\Models\AiKnowledgeVector;
use App\Models\AiWorkflowTrace;
use App\Models\VisionBlueprint;
use App\Services\Ai\LlmGatewayService;
use App\Services\Ai\VectorEmbeddingService;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;

class AiCopilotPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cpu-chip';
    protected static string | \UnitEnum | null $navigationGroup = 'Project OS // Core';
    protected static ?string $navigationLabel = 'AI Copilot & Komando';
    protected static ?string $title = 'Autonomous AI Copilot & Knowledge Repository (RAG)';
    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'ai-copilot';
    protected string $view = 'filament.pages.ai-copilot-page';

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        return $user->hasAnyRole(['super_admin', 'investor'])
            || $user->can('view_ai_copilot')
            || in_array(strtolower($user->email ?? ''), [
                'iriandi@neriahpro.com',
                'admin@neriahpro.com',
                'yoseph.iriandi@gmail.com',
            ]);
    }

    public string $activeSubTab = 'command';
    public string $docSearch = '';
    public ?string $docCursor = null;

    public function nextDocPage(?string $cursor): void
    {
        $this->docCursor = $cursor;
    }

    public function prevDocPage(?string $cursor): void
    {
        $this->docCursor = $cursor;
    }

    public function resetDocSearch(): void
    {
        $this->docSearch = '';
        $this->docCursor = null;
    }

    public function updatedDocSearch(): void
    {
        $this->docCursor = null;
    }

    /**
     * Keyset Cursor Pagination O(1) untuk repositori pengetahuan vektor
     */
    public function getIndexedDocuments()
    {
        try {
            $query = AiKnowledgeVector::select(
                'source_type',
                'source_id',
                DB::raw('MIN(title) as display_title'),
                DB::raw('COUNT(*) as chunk_count'),
                DB::raw('MAX(updated_at) as updated_at')
            )
            ->groupBy('source_type', 'source_id')
            ->orderBy('source_id', 'asc');

            if (!empty($this->docSearch)) {
                $term = '%' . strtolower(trim($this->docSearch)) . '%';
                $query->where(function ($q) use ($term) {
                    $q->whereRaw('LOWER(source_id) LIKE ?', [$term])
                      ->orWhereRaw('LOWER(title) LIKE ?', [$term]);
                });
            }

            return $query->cursorPaginate(10, ['*'], 'doc_cursor', $this->docCursor);
        } catch (\Throwable) {
            return null;
        }
    }

    public function triggerSyncKnowledge(VectorEmbeddingService $vectorService): void
    {
        $res = $vectorService->syncSystemKnowledgeBase(true);
        $this->dispatch('show-toast', [
            'type' => 'success',
            'title' => 'Sinkronisasi Berhasil',
            'message' => $res['message'] ?? 'Repositori pengetahuan berhasil diperbarui.',
        ]);
    }

    public function getViewData(): array
    {
        $totalChunks = 0;
        $totalBlueprints = 0;
        $activeSprints = 0;
        $recentTraces = [];

        try {
            $totalChunks = DB::table('ai_knowledge_vectors')->count();
            $totalBlueprints = VisionBlueprint::count();
            $activeSprints = VisionBlueprint::where('project_status', 'Active Sprint')->count();
            $recentTraces = AiWorkflowTrace::orderBy('id', 'desc')->limit(6)->get();
        } catch (\Throwable) {}

        return [
            'totalChunks' => $totalChunks,
            'totalBlueprints' => $totalBlueprints,
            'activeSprints' => $activeSprints,
            'recentTraces' => $recentTraces,
            'indexedDocs' => $this->getIndexedDocuments(),
        ];
    }
}

