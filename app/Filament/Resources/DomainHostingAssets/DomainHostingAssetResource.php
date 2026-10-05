<?php

namespace App\Filament\Resources\DomainHostingAssets;

use App\Filament\Resources\DomainHostingAssets\Pages\CreateDomainHostingAsset;
use App\Filament\Resources\DomainHostingAssets\Pages\EditDomainHostingAsset;
use App\Filament\Resources\DomainHostingAssets\Pages\ListDomainHostingAssets;
use App\Filament\Resources\DomainHostingAssets\Pages\ViewDomainHostingAsset;
use App\Filament\Resources\DomainHostingAssets\Schemas\DomainHostingAssetForm;
use App\Filament\Resources\DomainHostingAssets\Tables\DomainHostingAssetsTable;
use App\Models\DomainHostingAsset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DomainHostingAssetResource extends Resource
{
    use \App\Filament\Traits\AuditableByMidtransReviewer;

    protected static ?string $model = DomainHostingAsset::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-server-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Project Management';

    protected static ?string $navigationLabel = 'Domain & Hosting';

    protected static ?string $modelLabel = 'Aset Domain & Hosting';

    protected static ?string $pluralModelLabel = 'Kelola Domain & Hosting';

    protected static ?int $navigationSort = 12;

    protected static ?string $recordTitleAttribute = 'domain_name';

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = DomainHostingAsset::expiringSoon(30)->count();
            return $count > 0 ? (string) $count : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return DomainHostingAssetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DomainHostingAssetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDomainHostingAssets::route('/'),
            'create' => CreateDomainHostingAsset::route('/create'),
            'view' => ViewDomainHostingAsset::route('/{record}'),
            'edit' => EditDomainHostingAsset::route('/{record}/edit'),
        ];
    }
}
