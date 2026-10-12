<?php

namespace App\Filament\Resources\ProjectIssues;

use App\Filament\Resources\ProjectIssues\Pages\CreateProjectIssue;
use App\Filament\Resources\ProjectIssues\Pages\EditProjectIssue;
use App\Filament\Resources\ProjectIssues\Pages\ListProjectIssues;
use App\Filament\Resources\ProjectIssues\Schemas\ProjectIssueForm;
use App\Filament\Resources\ProjectIssues\Tables\ProjectIssuesTable;
use App\Models\ProjectIssue;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProjectIssueResource extends Resource
{
    protected static ?string $model = ProjectIssue::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ProjectIssueForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectIssuesTable::configure($table);
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
            'index' => ListProjectIssues::route('/'),
            'create' => CreateProjectIssue::route('/create'),
            'edit' => EditProjectIssue::route('/{record}/edit'),
        ];
    }
}
