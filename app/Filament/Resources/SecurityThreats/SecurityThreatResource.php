<?php

namespace App\Filament\Resources\SecurityThreats;

use App\Filament\Resources\SecurityThreats\Pages\ListSecurityThreats;
use App\Filament\Resources\SecurityThreats\Pages\ViewSecurityThreat;
use App\Filament\Resources\SecurityThreats\Tables\SecurityThreatsTable;
use App\Filament\Resources\SecurityThreats\Widgets\SecurityThreatStatsWidget;
use App\Models\SecurityThreatLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class SecurityThreatResource extends Resource
{
    protected static ?string $model = SecurityThreatLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string | \UnitEnum | null $navigationGroup = 'System & Security';

    protected static ?string $navigationLabel = 'AI Threat Shield';

    protected static ?string $modelLabel = 'Log Percobaan Intrusi';

    protected static ?string $pluralModelLabel = 'AI Threat Shield Logs';

    protected static ?int $navigationSort = 99;

    public static function table(Table $table): Table
    {
        return SecurityThreatsTable::configure($table);
    }

    public static function infolist(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return \App\Filament\Resources\SecurityThreats\Schemas\SecurityThreatInfolist::configure($schema);
    }

    public static function getWidgets(): array
    {
        return [
            SecurityThreatStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSecurityThreats::route('/'),
            'view' => ViewSecurityThreat::route('/{record}'),
        ];
    }
}
