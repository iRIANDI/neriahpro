<?php

namespace App\Services\Ai;

use App\Models\AiWorkflowTrace;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AiWorkflowTracer
{
    /**
     * Catat satu trace CCTV secara instan.
     */
    public static function record(array $data): ?AiWorkflowTrace
    {
        try {
            if (!Schema::hasTable('ai_workflow_traces')) {
                return null;
            }

            return AiWorkflowTrace::create([
                'session_id' => $data['session_id'] ?? null,
                'user_id' => $data['user_id'] ?? (auth()->id() ?? null),
                'stage' => $data['stage'] ?? 'analysis',
                'title' => $data['title'] ?? 'AI Agent Operation',
                'action_type' => $data['action_type'] ?? null,
                'status' => $data['status'] ?? 'success',
                'input_payload' => $data['input_payload'] ?? null,
                'output_payload' => $data['output_payload'] ?? null,
                'affected_model' => $data['affected_model'] ?? null,
                'affected_id' => $data['affected_id'] ?? null,
                'url' => $data['url'] ?? null,
                'execution_time_ms' => $data['execution_time_ms'] ?? null,
                'summary' => $data['summary'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::warning("AiWorkflowTracer record error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Mulai trace baru (status: running).
     */
    public static function startTrace(
        string $stage,
        string $title,
        ?string $sessionId = null,
        ?User $user = null,
        ?array $input = null,
        ?string $actionType = null,
        string $status = 'running'
    ): ?AiWorkflowTrace {
        return self::record([
            'session_id' => $sessionId,
            'user_id' => $user?->id ?? auth()->id(),
            'stage' => $stage,
            'title' => $title,
            'action_type' => $actionType,
            'status' => $status,
            'input_payload' => $input,
        ]);
    }

    /**
     * Selesaikan trace yang sudah berjalan.
     */
    public static function completeTrace(
        string|AiWorkflowTrace $trace,
        string $status = 'success',
        ?array $output = null,
        ?string $summary = null,
        ?string $model = null,
        ?string $affectedId = null,
        ?string $url = null,
        ?int $executionTimeMs = null
    ): ?AiWorkflowTrace {
        try {
            $record = $trace instanceof AiWorkflowTrace ? $trace : AiWorkflowTrace::find($trace);
            if (!$record) return null;

            $record->update([
                'status' => $status,
                'output_payload' => $output ?? $record->output_payload,
                'summary' => $summary ?? $record->summary,
                'affected_model' => $model ?? $record->affected_model,
                'affected_id' => $affectedId ?? $record->affected_id,
                'url' => $url ?? $record->url,
                'execution_time_ms' => $executionTimeMs ?? $record->execution_time_ms,
            ]);

            return $record;
        } catch (\Throwable $e) {
            Log::warning("AiWorkflowTracer completeTrace error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Catat trace yang gagal.
     */
    public static function failTrace(
        string|AiWorkflowTrace $trace,
        string $errorMessage,
        ?int $executionTimeMs = null
    ): ?AiWorkflowTrace {
        return self::completeTrace(
            $trace,
            'failed',
            ['error' => $errorMessage],
            "Operasi gagal: {$errorMessage}",
            executionTimeMs: $executionTimeMs
        );
    }
}

