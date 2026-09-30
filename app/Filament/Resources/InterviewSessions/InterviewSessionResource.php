<?php

namespace App\Filament\Resources\InterviewSessions;

use App\Filament\Resources\InterviewSessions\Pages\ListInterviewSessions;
use App\Filament\Resources\InterviewSessions\Pages\ViewInterviewSession;
use App\Filament\Resources\InterviewSessions\Schemas\InterviewSessionForm;
use App\Filament\Resources\InterviewSessions\Tables\InterviewSessionsTable;
use App\Models\InterviewSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class InterviewSessionResource extends Resource
{
    use \App\Filament\Traits\RestrictedToSuperAdmin;

    protected static ?string $model = InterviewSession::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-microphone';

    protected static string | \UnitEnum | null $navigationGroup = 'Career & CV Pro';

    protected static ?string $navigationLabel = 'Simulasi Wawancara AI';

    protected static ?string $modelLabel = 'Sesi Wawancara';

    protected static ?string $pluralModelLabel = 'Riwayat Simulasi Wawancara';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return InterviewSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InterviewSessionsTable::configure($table);
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
            'index' => ListInterviewSessions::route('/'),
            'view' => ViewInterviewSession::route('/{record}'),
        ];
    }
}
