<?php

namespace App\Filament\Resources\BlueprintVouchers;

use App\Filament\Resources\BlueprintVouchers\Pages\CreateBlueprintVoucher;
use App\Filament\Resources\BlueprintVouchers\Pages\EditBlueprintVoucher;
use App\Filament\Resources\BlueprintVouchers\Pages\ListBlueprintVouchers;
use App\Filament\Resources\BlueprintVouchers\Schemas\BlueprintVoucherForm;
use App\Filament\Resources\BlueprintVouchers\Tables\BlueprintVouchersTable;
use App\Models\BlueprintVoucher;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class BlueprintVoucherResource extends Resource
{
    protected static ?string $model = BlueprintVoucher::class;

    protected static ?string $navigationLabel = 'Project Vouchers';

    protected static ?string $modelLabel = 'Project Voucher';

    protected static ?string $pluralModelLabel = 'Project Promo Vouchers';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static string | \UnitEnum | null $navigationGroup = 'Project Management';

    protected static ?string $recordTitleAttribute = 'code';

    /**
     * Strict RBAC Gatekeeper: ONLY yoseph.iriandi.tambunan@gmail.com can access, view, or manage vouchers.
     */
    public static function canViewAny(): bool
    {
        return auth()->user()?->email === 'yoseph.iriandi.tambunan@gmail.com';
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->email === 'yoseph.iriandi.tambunan@gmail.com';
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->email === 'yoseph.iriandi.tambunan@gmail.com';
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->email === 'yoseph.iriandi.tambunan@gmail.com';
    }

    public static function form(Schema $schema): Schema
    {
        return BlueprintVoucherForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlueprintVouchersTable::configure($table);
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
            'index' => ListBlueprintVouchers::route('/'),
            'create' => CreateBlueprintVoucher::route('/create'),
            'edit' => EditBlueprintVoucher::route('/{record}/edit'),
        ];
    }
}
