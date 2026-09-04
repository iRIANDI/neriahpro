<?php

namespace App\Filament\Resources\VisionBlueprints;

use App\Filament\Resources\VisionBlueprints\Pages\CreateVisionBlueprint;
use App\Filament\Resources\VisionBlueprints\Pages\EditVisionBlueprint;
use App\Filament\Resources\VisionBlueprints\Pages\ListVisionBlueprints;
use App\Filament\Resources\VisionBlueprints\Pages\ViewVisionBlueprint;
use App\Filament\Resources\VisionBlueprints\Schemas\VisionBlueprintForm;
use App\Filament\Resources\VisionBlueprints\Schemas\VisionBlueprintInfolist;
use App\Filament\Resources\VisionBlueprints\Tables\VisionBlueprintsTable;
use App\Models\VisionBlueprint;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VisionBlueprintResource extends Resource
{
    protected static ?string $model = VisionBlueprint::class;

    protected static ?string $navigationLabel = 'Project OS / PRD';

    protected static ?string $modelLabel = 'Project Blueprint & PRD';

    protected static ?string $pluralModelLabel = 'Project Blueprints & PRDs';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string | \UnitEnum | null $navigationGroup = 'Project Management';

    protected static ?string $recordTitleAttribute = 'nama_bisnis';

    public static function form(Schema $schema): Schema
    {
        return VisionBlueprintForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VisionBlueprintInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VisionBlueprintsTable::configure($table);
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
            'index' => ListVisionBlueprints::route('/'),
            'create' => CreateVisionBlueprint::route('/create'),
            'view' => ViewVisionBlueprint::route('/{record}'),
            'edit' => EditVisionBlueprint::route('/{record}/edit'),
        ];
    }
}
