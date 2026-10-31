<?php

namespace App\Filament\Resources\AuditLogs\Pages;

use App\Filament\Resources\AuditLogs\AuditLogResource;
use App\Models\AuditLog;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected static ?string $title = 'Jejak Audit & Akuntabilitas Sistem (SHA-256 Tamper-Evident)';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_csv')
                ->label('Ekspor CSV Forensik (O(1) Stream)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(function (): bool {
                    $user = auth()->user();
                    if (! $user || $user->hasRole('midtrans_reviewer')) {
                        return false;
                    }
                    return $user->hasRole('super_admin') || $user->can('export_audit_logs');
                })
                ->action(function (): StreamedResponse {
                    AuditLog::record(
                        action: 'audit_logs_exported',
                        description: 'Ekspor CSV forensik jejak audit dijalankan oleh admin.',
                        eventCategory: 'security'
                    );

                    $filename = 'neriahpro_audit_trail_' . now()->format('Ymd_His') . '.csv';

                    return response()->streamDownload(function (): void {
                        $out = fopen('php://output', 'w');
                        fputcsv($out, [
                            'ULID',
                            'Timestamp',
                            'Category',
                            'Action',
                            'Actor',
                            'Actor Type',
                            'Entity',
                            'Entity ID',
                            'Description',
                            'IP Address',
                            'SHA-256 Integrity Hash',
                        ]);

                        foreach (AuditLog::query()->orderBy('id', 'desc')->limit(5000)->cursor() as $row) {
                            fputcsv($out, [
                                $row->id,
                                $row->created_at?->toIso8601String(),
                                $row->event_category ?? 'system',
                                $row->action,
                                $row->actor_name,
                                $row->actor_type,
                                $row->model_type ? class_basename($row->model_type) : '-',
                                $row->model_id ?? '-',
                                $row->description ?? '',
                                $row->ip_address ?? '',
                                $row->integrity_hash ?? 'LEGACY',
                            ]);
                        }

                        fclose($out);
                    }, $filename, [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                    ]);
                }),
        ];
    }
}


