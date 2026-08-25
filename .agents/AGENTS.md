# Framework version

This project uses **Filament v5**.

All Filament UI components, namespaces, and property type signatures (e.g. `navigationGroup`, `DeleteAction` vs `Tables\Actions\DeleteAction`) must adhere strictly to the **Filament v5** structures.

## Known Type Constraints
- `Page::$title` must be exactly `?string`.
- `Page::$navigationLabel` must be exactly `?string`.
- `Page::$navigationIcon` must be exactly `string | \BackedEnum | null`.
- `Page::$navigationGroup` must be exactly `string | \UnitEnum | null`.
- Action namespaces use `\Filament\Actions\...` instead of `\Filament\Tables\Actions\...` for specific actions.
