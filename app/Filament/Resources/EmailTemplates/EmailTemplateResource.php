<?php

namespace App\Filament\Resources\EmailTemplates;

use App\Filament\Resources\EmailTemplates\Pages\CreateEmailTemplate;
use App\Filament\Resources\EmailTemplates\Pages\EditEmailTemplate;
use App\Filament\Resources\EmailTemplates\Pages\ListEmailTemplates;
use App\Filament\Resources\EmailTemplates\Schemas\EmailTemplateForm;
use App\Filament\Resources\EmailTemplates\Tables\EmailTemplatesTable;
use App\Filament\Traits\RestrictedToSuperAdmin;
use App\Models\EmailTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    use RestrictedToSuperAdmin;

    protected static ?string $model = EmailTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static string | \UnitEnum | null $navigationGroup = 'Marketing & Klien';

    protected static ?string $navigationLabel = 'Template Email Sistem';

    protected static ?string $modelLabel = 'Template Email';

    protected static ?string $pluralModelLabel = 'Template Email Sistem';

    protected static ?int $navigationSort = 25;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return EmailTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailTemplatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailTemplates::route('/'),
            'create' => CreateEmailTemplate::route('/create'),
            'edit' => EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
