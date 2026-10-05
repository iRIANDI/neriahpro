<?php

namespace App\Filament\Resources\SecurityThreats\Tables;

use App\Models\SecurityThreatLog;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class SecurityThreatsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu Kejadian')
                    ->dateTime('d M Y H:i:s')
                    ->sortable()
                    ->description(fn (SecurityThreatLog $record) => $record->created_at->diffForHumans()),

                TextColumn::make('ip_address')
                    ->label('IP Penyerang')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('IP berhasil disalin')
                    ->description(fn (SecurityThreatLog $record) => substr($record->user_agent ?? 'Unknown User-Agent', 0, 40) . '...'),

                TextColumn::make('threat_type')
                    ->label('Kategori Ancaman')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'rce_system_call' => 'RCE (System Call)',
                        'python_dataset_loader_exploit' => 'Dataset Loader Exploit (HF/Gym)',
                        'python_system_execution' => 'Python Subprocess/OS',
                        'php_object_deserialization' => 'Unsafe Deserialization',
                        'php_info_probe' => 'PHP Recon / Info Leak',
                        'python_dataset_rce' => 'Dataset Loader Exploit',
                        'critical_path_traversal' => 'Path Traversal',
                        'ssti_template_injection' => 'Template Injection (SSTI)',
                        'autonomous_recon_probing' => 'Sensitive Recon Probe',
                        'autonomous_velocity_burst' => 'Velocity Burst Anomaly',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'rce_system_call', 'python_dataset_loader_exploit', 'python_system_execution', 'php_object_deserialization', 'ssti_template_injection' => 'danger',
                        'critical_path_traversal', 'php_info_probe', 'autonomous_recon_probing', 'autonomous_velocity_burst' => 'warning',
                        default => 'secondary',
                    })
                    ->sortable(),

                TextColumn::make('http_method')
                    ->label('Method')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'POST' => 'primary',
                        'PUT', 'PATCH' => 'warning',
                        'DELETE' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('endpoint')
                    ->label('Target Endpoint')
                    ->searchable()
                    ->limit(35)
                    ->copyable(),

                IconColumn::make('is_blocked')
                    ->label('Status Blokir')
                    ->boolean()
                    ->trueIcon('heroicon-o-no-symbol')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('threat_type')
                    ->label('Tipe Serangan')
                    ->options([
                        'rce_system_call' => 'RCE (System Call)',
                        'python_dataset_loader_exploit' => 'Dataset Loader Exploit (HF/Gym)',
                        'python_system_execution' => 'Python Subprocess/OS',
                        'php_object_deserialization' => 'Unsafe Deserialization',
                        'php_info_probe' => 'PHP Recon / Probe',
                        'critical_path_traversal' => 'Path Traversal',
                        'ssti_template_injection' => 'Template Injection (SSTI)',
                        'autonomous_recon_probing' => 'Sensitive Recon Probe',
                        'autonomous_velocity_burst' => 'Velocity Burst Anomaly',
                    ]),

                TernaryFilter::make('is_blocked')
                    ->label('Status Blokir IP'),
            ])
            ->recordActions([
                Action::make('block_ip')
                    ->label('Blokir IP')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn (SecurityThreatLog $record) => !Cache::has("ai_shield_blocked_{$record->ip_address}"))
                    ->requiresConfirmation()
                    ->modalHeading('Blokir IP Penyerang')
                    ->modalDescription(fn (SecurityThreatLog $record) => "Apakah Anda yakin ingin memblokir IP {$record->ip_address} selama 24 jam dari seluruh request aplikasi?")
                    ->action(function (SecurityThreatLog $record) {
                        Cache::put("ai_shield_blocked_{$record->ip_address}", true, now()->addHours(24));
                        $record->update(['is_blocked' => true]);

                        Notification::make()
                            ->title('IP Berhasil Diblokir!')
                            ->body("IP {$record->ip_address} telah diisolasi dari sistem.")
                            ->danger()
                            ->send();
                    }),

                Action::make('unblock_ip')
                    ->label('Buka Blokir')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (SecurityThreatLog $record) => Cache::has("ai_shield_blocked_{$record->ip_address}"))
                    ->action(function (SecurityThreatLog $record) {
                        Cache::forget("ai_shield_blocked_{$record->ip_address}");
                        Cache::forget("ai_shield_strikes_{$record->ip_address}");
                        $record->update(['is_blocked' => false]);

                        Notification::make()
                            ->title('Blokir IP Dicabut')
                            ->body("IP {$record->ip_address} kembali diizinkan mengakses sistem.")
                            ->success()
                            ->send();
                    }),

                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
