<?php

namespace App\Filament\Resources\LegalPolicies;

use App\Filament\Resources\LegalPolicies\Pages\CreateLegalPolicy;
use App\Filament\Resources\LegalPolicies\Pages\EditLegalPolicy;
use App\Filament\Resources\LegalPolicies\Pages\ListLegalPolicies;
use App\Filament\Resources\LegalPolicies\Schemas\LegalPolicyForm;
use App\Filament\Resources\LegalPolicies\Tables\LegalPoliciesTable;
use App\Models\LegalPolicy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LegalPolicyResource extends Resource
{
    use \App\Filament\Traits\AuditableByMidtransReviewer;

    protected static ?string $model = LegalPolicy::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string | \UnitEnum | null $navigationGroup = 'Contracts & Legal';

    protected static ?string $navigationLabel = 'Legal & Kebijakan';

    protected static ?string $modelLabel = 'Dokumen Legal';

    protected static ?string $pluralModelLabel = 'Kebijakan Legal';

    protected static ?int $navigationSort = 16;

    protected static ?string $recordTitleAttribute = 'type';

    public static function form(Schema $schema): Schema
    {
        return LegalPolicyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LegalPoliciesTable::configure($table);
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
            'index' => ListLegalPolicies::route('/'),
            'create' => CreateLegalPolicy::route('/create'),
            'edit' => EditLegalPolicy::route('/{record}/edit'),
        ];
    }
}
