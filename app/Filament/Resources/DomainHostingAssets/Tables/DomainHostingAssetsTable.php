<?php

namespace App\Filament\Resources\DomainHostingAssets\Tables;

use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DomainHostingAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'domain' => 'Domain',
                        'hosting' => 'Hosting',
                        'vps' => 'VPS Server',
                        'ssl' => 'SSL',
                        'email' => 'Email',
                        'bundle' => 'Bundle',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'domain' => 'info',
                        'hosting' => 'success',
                        'vps' => 'warning',
                        'ssl' => 'danger',
                        'email' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('domain_name')
                    ->label('Domain / Hostname')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Domain berhasil disalin')
                    ->description(fn ($record) => $record->name . ' • ' . $record->provider),

                TextColumn::make('visionBlueprint.nama_bisnis')
                    ->label('Proyek Klien')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Internal / Mandiri')
                    ->toggleable(),

                TextColumn::make('purchase_date')
                    ->label('Tgl Beli')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('expires_at')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable()
                    ->description(fn ($record) => $record->billing_cycle ? 'Siklus: ' . ucfirst($record->billing_cycle) : null),

                TextColumn::make('days_remaining')
                    ->label('Status Tenggat')
                    ->badge()
                    ->state(fn ($record) => $record->expiration_label)
                    ->color(fn ($record) => $record->expiration_color)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('expires_at', $direction);
                    }),

                TextColumn::make('cost_price')
                    ->label('Biaya Modal')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('client_price')
                    ->label('Tagihan Klien')
                    ->money('IDR', locale: 'id')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('auto_renew')
                    ->label('Auto Renew')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'expiring_soon' => 'warning',
                        'expired' => 'danger',
                        'cancelled' => 'gray',
                        'transferred' => 'info',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'expiring_soon' => 'Mendekati Jatuh Tempo',
                        'expired' => 'Kadaluarsa',
                        'cancelled' => 'Dibatalkan',
                        'transferred' => 'Ditransfer',
                        default => ucfirst($state),
                    }),
            ])
            ->defaultSort('expires_at', 'asc')
            ->filters([
                SelectFilter::make('asset_type')
                    ->label('Tipe Aset')
                    ->options([
                        'domain' => 'Domain Name',
                        'hosting' => 'Web Hosting',
                        'vps' => 'VPS Server',
                        'ssl' => 'SSL Certificate',
                        'email' => 'Business Email',
                        'bundle' => 'Bundle',
                    ]),

                SelectFilter::make('provider')
                    ->label('Provider')
                    ->options(fn () => \App\Models\DomainHostingAsset::distinct()->pluck('provider', 'provider')->filter()->toArray()),

                Filter::make('expiring_soon')
                    ->label('Jatuh Tempo Segera (<= 30 Hari)')
                    ->query(fn (Builder $query): Builder => $query->expiringSoon(30)),

                Filter::make('critical_expiring')
                    ->label('Kritis (<= 7 Hari)')
                    ->query(fn (Builder $query): Builder => $query->expiringSoon(7)),

                Filter::make('expired')
                    ->label('Sudah Kadaluarsa')
                    ->query(fn (Builder $query): Builder => $query->expired()),
            ])
            ->recordActions([
                Action::make('renew')
                    ->label('Perpanjang (Renew)')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'developer']))
                    ->form([
                        Select::make('months')
                            ->label('Durasi Perpanjangan')
                            ->options([
                                1 => '1 Bulan',
                                3 => '3 Bulan',
                                6 => '6 Bulan',
                                12 => '1 Tahun (12 Bulan)',
                                24 => '2 Tahun (24 Bulan)',
                                36 => '3 Tahun (36 Bulan)',
                            ])
                            ->default(12)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $months = (int) $data['months'];
                        $record->renew($months);

                        Notification::make()
                            ->title('Aset Berhasil Diperpanjang!')
                            ->body("Jatuh tempo baru: {$record->expires_at->format('d M Y')}")
                            ->success()
                            ->send();
                    }),

                Action::make('trigger_reminder')
                    ->label('Picu Pengingat')
                    ->icon('heroicon-o-bell-alert')
                    ->color('warning')
                    ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'developer']))
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Pengingat Jatuh Tempo Sekarang')
                    ->modalDescription(fn ($record) => "Kirimkan notifikasi peringatan jatuh tempo untuk aset '{$record->domain_name}' ({$record->expiration_label}) ke seluruh Superadmin?")
                    ->action(function ($record) {
                        $admins = User::all();
                        foreach ($admins as $admin) {
                            Notification::make()
                                ->title('⚠️ Peringatan Jatuh Tempo: ' . ($record->domain_name ?: $record->name))
                                ->body("Layanan {$record->asset_type} ({$record->provider}) akan jatuh tempo pada {$record->expires_at->format('d M Y')} ({$record->expiration_label}).")
                                ->warning()
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('view')
                                        ->label('Lihat Detail')
                                        ->url(\App\Filament\Resources\DomainHostingAssets\DomainHostingAssetResource::getUrl('view', ['record' => $record])),
                                ])
                                ->sendToDatabase($admin);
                        }

                        $record->update(['last_reminder_sent_at' => Carbon::now()]);

                        Notification::make()
                            ->title('Pengingat Berhasil Dikirimkan!')
                            ->body('Notifikasi telah disiarkan ke panel admin.')
                            ->success()
                            ->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
