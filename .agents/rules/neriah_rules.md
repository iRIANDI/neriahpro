# Git Sync Rule
Always automatically commit and push any changes to GitHub after completing a task.
Git Remote: https://github.com/iRIANDI/neriahpro.git

# Framework Versions
This project uses Laravel 13, Filament v4, Flux UI, and Livewire 4. Ensure all syntax, features, and troubleshooting align with these specific major versions.

# Mandatory Rule: Database Primary Keys (Enterprise Scalability)
For enterprise-grade scalability capable of handling millions of records without performance degradation, ALWAYS prioritize O(1) time complexity algorithms.
1. Primary Keys: ALWAYS use ULID (`$table->ulid('id')->primary()`) for new database tables to ensure distributed scalability. Do NOT use AUTO_INCREMENT or `$table->id()`.
2. Pagination: NEVER use standard paginate() (OFFSET-based). ALWAYS use Cursor Pagination (`cursorPaginate()`) with proper keyset/pointer fallbacks (e.g., `->orderBy('id', 'asc')`) to guarantee cursor stability.

# Deployment Workflow Rule
After completing any task, you MUST always suggest which deployment script number the user should run on their SSH terminal using `./deploy.sh [number]`. Choose the number based on the following 6 deployment scenarios:
1. Skenario Ringan: Hanya merubah tampilan (UI/Blade) atau logika PHP biasa. (`./deploy.sh 1`)
2. Migrasi Aman: Menambah kolom/tabel baru di database tanpa menghapus data lama. (`./deploy.sh 2`)
3. Hancur & Bangun DB (Staging Only): Jika ada perombakan schema besar-besaran yang membutuhkan migrate fresh dan seeder. (`./deploy.sh 3`)
4. Install Plugin/Package Baru: Jika ada penambahan package via composer atau upgrade filament. (`./deploy.sh 4`)
5. Dump Autoload: Jika ada perubahan nama class, folder, atau restrukturisasi namespace/file PHP. (`./deploy.sh 5`)
6. Build Assets Frontend: Jika ada perubahan pada custom CSS, konfigurasi Tailwind, Vite, atau instalasi NPM package baru. (`./deploy.sh 6`)

# AlpineJS HTML Escaping Rule
When writing inline javascript within AlpineJS attributes (such as `x-data="..."`), NEVER use raw double quotes (`"`) or single quotes (`'`) inside string literals as it can break the HTML attribute parsing. ALWAYS encode them into HTML entities:
- Tanda kutip ganda (") → &quot;
- Tanda kutip tunggal (') → &apos;
