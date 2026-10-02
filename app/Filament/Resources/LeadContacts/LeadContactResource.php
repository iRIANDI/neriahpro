<?php

namespace App\Filament\Resources\LeadContacts;

use App\Filament\Resources\LeadContacts\Pages\CreateLeadContact;
use App\Filament\Resources\LeadContacts\Pages\EditLeadContact;
use App\Filament\Resources\LeadContacts\Pages\ListLeadContacts;
use App\Filament\Resources\LeadContacts\Schemas\LeadContactForm;
use App\Filament\Resources\LeadContacts\Tables\LeadContactsTable;
use App\Models\LeadContact;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LeadContactResource extends Resource
{
    use \App\Filament\Traits\RestrictedToSuperAdmin;

    protected static ?string $model = LeadContact::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string | \UnitEnum | null $navigationGroup = 'Marketing & Klien';

    protected static ?string $navigationLabel = 'Database CRM Leads';

    protected static ?string $modelLabel = 'Kontak Lead / Calon Klien';

    protected static ?string $pluralModelLabel = 'Database CRM Leads';

    protected static ?int $navigationSort = 19;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LeadContactForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadContactsTable::configure($table);
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
            'index' => ListLeadContacts::route('/'),
            'create' => CreateLeadContact::route('/create'),
            'edit' => EditLeadContact::route('/{record}/edit'),
        ];
    }
}
