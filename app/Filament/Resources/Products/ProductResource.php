<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Pages\ViewProduct;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Schemas\ProductInfolist;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string | \UnitEnum | null $navigationGroup = 'Commerce & Billing';

    protected static ?string $navigationLabel = 'Katalog Produk & Layanan';

    protected static ?string $modelLabel = 'Produk / Layanan';

    protected static ?string $pluralModelLabel = 'Katalog Produk & Layanan';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'slug';

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (! $user || $user->hasRole('midtrans_reviewer')) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'developer', 'investor']) || $user->can('ViewAny:Product');
    }

    public static function canAccess(): bool
    {
        return static::canViewAny();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        if (! $user || $user->hasRole('midtrans_reviewer')) {
            return false;
        }

        return $user->hasRole('super_admin') || $user->can('Create:Product');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        if (! $user || $user->hasRole('midtrans_reviewer')) {
            return false;
        }

        return $user->hasRole('super_admin') || $user->can('Update:Product');
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        if (! $user || $user->hasRole('midtrans_reviewer')) {
            return false;
        }

        return $user->hasRole('super_admin') || $user->can('Delete:Product');
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();
        if (! $user || $user->hasRole('midtrans_reviewer')) {
            return false;
        }

        return $user->hasRole('super_admin') || $user->can('Delete:Product');
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
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
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view' => ViewProduct::route('/{record}'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
