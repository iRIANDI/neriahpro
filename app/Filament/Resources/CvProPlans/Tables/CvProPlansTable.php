<?php

namespace App\Filament\Resources\CvProPlans\Tables;

use App\Services\CvPro\CvPricingService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CvProPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nama Paket & Promosi')
                    ->state(fn ($record) => is_array($record->name) ? ($record->name['id'] ?? $record->name['en'] ?? '-') : $record->name)
                    ->description(fn ($record) => $record->badge ? "Badge: {$record->badge}" : null)
                    ->weight('bold')
                    ->searchable(query: fn ($query, $search) => $query->whereRaw("LOWER(name::text) LIKE ?", ['%' . strtolower($search) . '%'])),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'subscription' => 'info',
                        'topup' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'subscription' => 'Langganan',
                        'topup' => 'A La Carte / Top-Up',
                        default => ucfirst($state),
                    }),

                TextColumn::make('price_idr')
                    ->label('Harga (IDR)')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('billing_cycle')
                    ->label('Siklus')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'monthly' => 'Bulanan',
                        'quarterly' => '3 Bulan',
                        'yearly' => 'Tahunan',
                        'one_time' => '1x Beli',
                        default => $state,
                    }),

                TextColumn::make('economics')
                    ->label('Margin AI & Profit')
                    ->badge()
                    ->state(function ($record) {
                        $econ = CvPricingService::calculatePlanEconomics($record);
                        if ($record->price_idr == 0) {
                            return 'Free (Akuisisi)';
                        }
                        return "Margin: {$econ['margins']['expected_margin_percent']}%";
                    })
                    ->color(function ($record) {
                        if ($record->price_idr == 0) {
                            return 'gray';
                        }
                        $econ = CvPricingService::calculatePlanEconomics($record);
                        return $econ['safety_assessment']['is_profitable'] ? 'success' : 'danger';
                    })
                    ->tooltip(function ($record) {
                        $econ = CvPricingService::calculatePlanEconomics($record);
                        return "Biaya API Maks: Rp " . number_format($econ['cost_breakdown']['max_ai_cost_idr'], 0, ',', '.') . "\n"
                            . "Biaya Rata-Rata: Rp " . number_format($econ['cost_breakdown']['expected_ai_cost_idr'], 0, ',', '.') . "\n"
                            . "Profit Bersih: Rp " . number_format($econ['margins']['expected_profit_idr'], 0, ',', '.') . "\n"
                            . "Status: " . $econ['safety_assessment']['verdict_id'];
                    }),

                TextColumn::make('quotas')
                    ->label('Ringkasan Kuota')
                    ->state(function ($record) {
                        $q = $record->quotas ?? [];
                        $tailor = ($q['tailor_cv_limit'] ?? 0) === -1 ? '∞' : ($q['tailor_cv_limit'] ?? 0);
                        $interview = ($q['mock_interviews_limit'] ?? 0) === -1 ? '∞' : ($q['mock_interviews_limit'] ?? 0);
                        return "Tailor: {$tailor} | Interview: {$interview}";
                    })
                    ->fontFamily('mono')
                    ->size('xs'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Filter Tipe')
                    ->options([
                        'subscription' => 'Langganan',
                        'topup' => 'A La Carte / Top-Up',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
