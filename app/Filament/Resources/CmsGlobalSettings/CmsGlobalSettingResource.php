<?php

namespace App\Filament\Resources\CmsGlobalSettings;

use App\Filament\Resources\CmsGlobalSettings\Pages\CreateCmsGlobalSetting;
use App\Filament\Resources\CmsGlobalSettings\Pages\EditCmsGlobalSetting;
use App\Filament\Resources\CmsGlobalSettings\Pages\ListCmsGlobalSettings;
use App\Models\CmsGlobalSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Builder;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class CmsGlobalSettingResource extends Resource
{
    protected static ?string $model = CmsGlobalSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'key';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true),
                KeyValue::make('value')
                    ->label('Settings Data')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->searchable()->sortable(),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => ListCmsGlobalSettings::route('/'),
            'create' => CreateCmsGlobalSetting::route('/create'),
            'edit' => EditCmsGlobalSetting::route('/{record}/edit'),
        ];
    }
}
