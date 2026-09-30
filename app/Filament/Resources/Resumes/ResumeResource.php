<?php

namespace App\Filament\Resources\Resumes;

use App\Filament\Resources\Resumes\Pages\CreateResume;
use App\Filament\Resources\Resumes\Pages\EditResume;
use App\Filament\Resources\Resumes\Pages\ListResumes;
use App\Filament\Resources\Resumes\Pages\ViewResume;
use App\Filament\Resources\Resumes\Schemas\ResumeForm;
use App\Filament\Resources\Resumes\Tables\ResumesTable;
use App\Models\Resume;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ResumeResource extends Resource
{
    use \App\Filament\Traits\RestrictedToSuperAdmin;

    protected static ?string $model = Resume::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string | \UnitEnum | null $navigationGroup = 'Career & CV Pro';

    protected static ?string $navigationLabel = 'Kelola CV & Resume';

    protected static ?string $modelLabel = 'Dokumen Resume';

    protected static ?string $pluralModelLabel = 'Koleksi CV & Resume';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ResumeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResumesTable::configure($table);
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
            'index' => ListResumes::route('/'),
            'create' => CreateResume::route('/create'),
            'view' => ViewResume::route('/{record}'),
            'edit' => EditResume::route('/{record}/edit'),
        ];
    }
}
