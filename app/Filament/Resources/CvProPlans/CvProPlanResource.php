<?php

namespace App\Filament\Resources\CvProPlans;

use App\Filament\Resources\CvProPlans\Pages\CreateCvProPlan;
use App\Filament\Resources\CvProPlans\Pages\EditCvProPlan;
use App\Filament\Resources\CvProPlans\Pages\ListCvProPlans;
use App\Filament\Resources\CvProPlans\Schemas\CvProPlanForm;
use App\Filament\Resources\CvProPlans\Tables\CvProPlansTable;
use App\Models\CvProPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CvProPlanResource extends Resource
{
    use \App\Filament\Traits\RestrictedToSuperAdmin;

    protected static ?string $model = CvProPlan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string | \UnitEnum | null $navigationGroup = 'Career & CV Pro';

    protected static ?string $navigationLabel = 'Paket Harga & Kuota';

    protected static ?string $modelLabel = 'Paket & Kuota CV Pro';

    protected static ?string $pluralModelLabel = 'Katalog Paket & Kuota CV Pro';

    protected static ?int $navigationSort = 15;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return CvProPlanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CvProPlansTable::configure($table);
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
            'index' => ListCvProPlans::route('/'),
            'create' => CreateCvProPlan::route('/create'),
            'edit' => EditCvProPlan::route('/{record}/edit'),
        ];
    }
}
