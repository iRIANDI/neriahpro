# Mandatory Protocol: Strict @Ponytail & @graphify Usage
- ⚠️ **MANDATORY ON EVERY TASK & TURN**: You MUST always use and adhere strictly to `@Ponytail` and `@graphify`. Never perform blind codebase exploration or duplicate abstractions.
- **Ponytail Decision Ladder ([.agents/PONYTAIL.md](file:///c:/xampp/htdocs/neriahpro/.agents/PONYTAIL.md))**:
  1. *Rung 1 (Framework Native First)*: Leverage built-in Laravel 13, Filament v5, and Livewire 4 tools before adding any code.
  2. *Rung 2 (Reuse Existing Codebase)*: Check existing services, traits, and components before creating new ones.
  3. *Rung 3 (Configuration Over Code)*: Use Laravel configs, environment variables, and database settings.
  4. *Rung 4 (Surgical & Minimalist Diff)*: Apply precise, targeted diffs to conserve tokens and preserve stability.
- **Graphify Architectural Knowledge Graph ([.agents/graphify/GRAPH_REPORT.md](file:///c:/xampp/htdocs/neriahpro/.agents/graphify/GRAPH_REPORT.md))**:
  - Always consult `GRAPH_REPORT.md` before querying files. Locate models, resources, tables, controllers, and services instantly without multi-file scanning.
  - Keep `GRAPH_REPORT.md` synchronized whenever new models, resources, or endpoints are introduced.

# Git Sync Rule
Always automatically commit and push any changes to GitHub after completing a task.
Git Remote: https://github.com/iRIANDI/neriahpro.git

# Framework Versions
This project uses Laravel 13, Filament v5, Flux UI, and Livewire 4. Ensure all syntax, features, and troubleshooting align with these specific major versions.

## Known Type Constraints
- `Page::$title` must be exactly `?string`.
- `Page::$navigationLabel` must be exactly `?string`.
- `Page::$navigationIcon` must be exactly `string | \BackedEnum | null`.
- `Page::$navigationGroup` must be exactly `string | \UnitEnum | null`.
- Action namespaces use `\Filament\Actions\...` instead of `\Filament\Tables\Actions\...` for specific actions.
- **Filament v5 Form & Schema Rule**: In Livewire components using `InteractsWithForms` (or Filament Pages implementing `HasForms`), the method signature for `form()` MUST use `\Filament\Schemas\Schema` (i.e. `public function form(\Filament\Schemas\Schema $form): \Filament\Schemas\Schema`), NEVER `\Filament\Forms\Form`. Using `Form` will throw a fatal `TypeError: Argument #1 ($form) must be of type Filament\Forms\Form, Filament\Schemas\Schema given`.

# Mandatory Rule: Database Primary Keys (Enterprise Scalability & PostgreSQL Compatibility)
For enterprise-grade scalability capable of handling millions of records without performance degradation, ALWAYS prioritize O(1) time complexity algorithms.
1. **Primary Keys**: ALWAYS use ULID (`->ulid('id')->primary()`) for business domain database tables (`users`, `articles`, `projects`, `donations`, `pages`, `volunteers`, etc.) to ensure distributed scalability.
   - ⚠️ **CRITICAL POSTGRESQL RULE**: NEVER use `$table->uuid('id')` for models using `HasUlids` trait! In PostgreSQL, `uuid` strictly rejects 26-character ULID strings with `SQLSTATE[22P02] (Invalid text representation)`. ALWAYS use `->ulid('id')` (which translates to `VARCHAR(26)`/`CHAR(26)`).
   - NEVER use AUTO_INCREMENT or `->id()` for business domain models.
   - 🛡️ **Third-Party Plugin Tables Exception**: Tables generated and managed by third-party packages / Filament plugins (`jobs`, `cache`, `sessions`, `activity_log`, `failed_jobs`, etc.) MUST retain their vendor default primary keys to ensure 100% upgrade-compatibility. For `Media`, this app overrides the model using `HasUlids`.
2. **Foreign Keys**: Always use `->foreignUlid('parent_id')` to match ULID primary keys. For relations pointing to vendor plugin tables, match the underlying foreign key type.
3. **Pagination**: NEVER use standard paginate() (OFFSET-based). ALWAYS use Cursor Pagination (`cursorPaginate()`) with proper keyset/pointer fallbacks (e.g., `->orderBy('id', 'asc')`) to guarantee cursor stability.

# Deployment Workflow Rule
After completing any task, you MUST always suggest which deployment script number the user should run on their SSH terminal using `./deploy.sh [number]`. Choose the number based on the following 6 deployment scenarios:
1. Skenario Ringan: Hanya merubah tampilan (UI/Blade) atau logika PHP biasa. (`./deploy.sh 1`)
2. Migrasi Aman: Menambah kolom/tabel baru di database tanpa menghapus data lama. (`./deploy.sh 2`)
3. Hancur & Bangun DB (Staging Only): Jika ada perombakan schema besar-besaran yang membutuhkan migrate fresh dan seeder. (`./deploy.sh 3`)
4. Install Plugin/Package Baru: Jika ada penambahan package via composer atau upgrade filament. (`./deploy.sh 4`)
5. Dump Autoload: Jika ada perubahan nama class, folder, atau restrukturisasi namespace/file PHP. (`./deploy.sh 5`)
6. Build Assets Frontend: Jika ada perubahan pada custom CSS, konfigurasi Tailwind, Vite, atau instalasi NPM package baru. (`./deploy.sh 6`)

# Mandatory Rule: Strict Ban on Native JavaScript Dialogs (No "Modal Kampungan")
- 🚫 **ZERO TOLERANCE FOR NATIVE DIALOGS**: NEVER use native browser popups (`window.alert()`, `alert()`, `window.confirm()`, `confirm()`, `window.prompt()`, `prompt()`) anywhere across the frontend (Blade, React, Vue, Alpine, or raw JavaScript). Native browser dialogs look cheap/unprofessional ("modal kampungan"), freeze the UI thread, and degrade client credibility.
- ✨ **MANDATORY TOAST & MODERN MODAL PROTOCOL**:
  - Always use the centralized floating Toast notification system: `window.showToast({ type: 'success'|'error'|'warning'|'info', title: '...', message: '...' })`.
  - For interactive confirmations or data entry, use curated Tailwind + Alpine.js dialogs with backdrop-blur, smooth transition animations, and dark/light mode fidelity.

# Mandatory Rule: Subtle Round Corners (Strict Ban on Capsule/Pill Shapes)
- 📐 **SUBTLE & THIN CORNERS**: UI/UX border radius MUST be subtle and thin (`rounded-none`, `rounded-xs`, `rounded-sm`, max `rounded-md`).
- 🚫 **STRICT BAN ON CAPSULE/PILL BUTTONS**: NEVER use `rounded-full` or thick capsule shapes on buttons, cards, or inputs. Excessive rounding looks like generic "AI slop" templates and wastes clickable bounding areas.

# Mandatory Rule: Schema.org Structured Data & Dynamic Admin Control
- 🌐 **MANDATORY SCHEMA.ORG**: All public-facing views must emit valid Schema.org JSON-LD (Organization, WebSite, SoftwareApplication, Product, Breadcrumbs).
- ⚙️ **ADMIN BACKEND ADJUSTMENTS**: Schema.org data must be editable via admin settings (`ManageSettings`).
- ⚡ **CACHE FOREVER & EVENT-DRIVEN FLUSH**: All Schema.org structures must be cached via `Cache::rememberForever()` and automatically invalidated on Eloquent model `saved` and `deleted` hooks.

# Mandatory Rule: Modular CMS Page Architecture & Decoupled Global Layout
- 🧩 **MODULAR PAGE PLUGINS**: Frontpage, landing pages, and sub-pages must be composed of independent, configurable plugins (`HeroIsland`, `ProductGridIsland`, `CvPricingIsland`, `ClientOnboardingIsland`, etc.).
- 🌐 **ISOLATED GLOBAL NAVIGATION & FOOTER**: Global navigation (topbar, sidebar), footer, and global alerts MUST NEVER be embedded inside page plugins. They must remain isolated global components driven by centralized settings so they can be modified independently.
- 🎓 **EDUCATIONAL & INTUITIVE PLUGIN UX**: Every plugin must be informative, guiding, and user-friendly with explanatory tooltips, interactive micro-states, and contextual help rather than barren forms.
- 🎨 **INTERACTIVE & 3D WEB DESIGN (ANTI-AI-SLOP)**: Frontpages must feature engaging, intuitive web design with 60fps micro-animations, tailored color palettes, and optional 3D/Canvas elements (Three.js/WebGL) where appropriate.

# Mandatory Rule: Bulletproof Scalability (Cache Forever & Event-Driven Reset)
- 🚀 **MILLIONS OF RECORDS & VIEWERS**: CMS pages and global configurations must leverage Redis or `Cache::rememberForever()` to guarantee O(1) response times sub-10ms.
- 🔄 **EVENT-DRIVEN CACHE INVALIDATION**: Model observers (`booted()` on `CmsPage` and `CmsGlobalSetting`) must automatically forget page and setting caches immediately upon creation, update, or deletion.

# Mandatory Rule: SEO Browser Tab Title Standard
- 🏷️ **BROWSER TAB TITLE FORMAT**: Browser page titles must strictly follow the format: `[NAMA DOMAIN - NAMA PAGE]` (e.g. `neriahpro.com - Digital Services Hub`).

# Mandatory Rule: Anti-AI Malware & Cyber Threat Defense
- 🛡️ **ANTI-AI MALWARE SUITE**: Implement multi-layered defenses against automated AI scrapers and malware (honeypot fields, rate limiting, strict CSP, bot challenge mitigation) along with user-facing educational security transparency.

# Mandatory Rule: Backend Admin AI Agentic Engine
- 🤖 **AGENTIC DECISION INTELLIGENCE**: Backend admin features must integrate AI Agentic capabilities:
  - Structured Knowledge Base
  - Retrieval-Augmented Generation (RAG) to eliminate hallucinations
# Mandatory Rule: Multi-Language 2-Tier Architecture (Frontend & Backend Native JSON)
- 🌐 **BACKEND DUAL-LOCALE**: Filament v5 must support English and Indonesia natively. Multilingual database columns MUST be formatted as JSON (`{"id": "...", "en": "..."}`) and cast as `'array'`.
- 🌐 **FRONTEND 2-TIER LOCALE**:
  - **Tier 1 (Native Precise)**: UI and UX support English & Indonesian native language toggle.
  - **Tier 2 (Global Plugin)**: Google Translate plugin in frontend with selectable language whitelist managed centrally from backend admin settings.

# Mandatory Rule: Country Zone Dialing Code Standard
- 📞 **COUNTRY ZONE INPUTS**: Every phone and WhatsApp input must use Country Zone selector (`config/country_zones.php`, e.g. +62, +65, +1, +44, +81) to enforce international E.164 compliance and eliminate invalid phone numbers.

# Mandatory Rule: Thousand Separators on Numbers & Currencies
- 🔢 **THOUSAND SEPARATORS**: Any numeric input, currency, or metric display reaching thousands (>= 1,000) MUST format thousands with proper separators (dot `.` for ID / comma `,` for EN) across UI/UX components.

# Mandatory Rule: Local FontAwesome Icons (Zero CDN Latency)
- 🎨 **LOCAL FONTAWESOME LIBRARY**: Representative icons for navigation, footer, global alert, and plugins MUST use local SVGs (`config/fontawesome.php` and `\App\Support\FontAwesome::svg('name')`) to prevent third-party CDN latency, blocking, or offline broken layouts.

# Mandatory Rule: Curator Picker & Shallow Storage Hierarchy (Server Inodes & RAM Optimization)
- 🖼️ **CURATOR PICKER MANDATORY**: Every image input in Filament v5 backend admin MUST use Curator Picker (`\Awcodes\Curator\Components\Forms\CuratorPicker` or `\App\Support\FilamentCuratorHelper::picker('name')`).
- 📁 **SHALLOW DIRECTORY MAP (MAX 1-2 LEVELS)**: Every image upload MUST specify a shallow directory mapping (e.g. `->directory('products')`, `->directory('branding')`, `->directory('media')`). NEVER use deep recursive folder paths (such as `uploads/YYYY/MM/DD/user/id/...`) because deep directory nesting exhausts Linux filesystem inodes, degrades OS filesystem traversal, and spikes RAM consumption when scanning directories.

# Mandatory Rule: Full-Featured Rich Text Editor with Curator Media Insertion
- 📝 **FULL-FEATURED TOOLBAR MANDATORY**: Any textarea accepting paragraphs or long descriptive copy MUST use a standardized full-featured Rich Text Editor (`\App\Support\FilamentRichEditor::make('content', 'shallow_dir')`).
- 🛠️ **ALL TOOLBAR BUTTONS ENABLED**: Must explicitly enable `['attachFiles', 'blockquote', 'bold', 'bulletList', 'codeBlock', 'h2', 'h3', 'italic', 'link', 'orderedList', 'redo', 'strike', 'underline', 'undo']`.
- 🖼️ **INTEGRATED MEDIA INSERTION**: Must support direct media insertion using shallow public directory attachments (`fileAttachmentsDisk('public')`, `fileAttachmentsDirectory('shallow_dir')`).

# Mandatory Rule: Eloquent Cache Serialization Guard (Incomplete Class Prevention)
- 🚫 **NEVER CACHE RAW ELOQUENT INSTANCES**: In `Cache::rememberForever()`, NEVER serialize raw Eloquent model instances across worker processes or sessions. Serializing Eloquent models in cache causes fatal `ErrorException: The script tried to access a property on an incomplete object (__PHP_Incomplete_Class)` if loaded before autoloader or across separate execution lifecycles.
- ⚡ **CACHE PRIMITIVE ATTRIBUTES ARRAY**: ALWAYS store raw attribute arrays (`$record ? $record->getAttributes() : null`) or JSON, and reconstitute the model via `(new Model)->newFromBuilder($cachedAttributes)`.
- 🛡️ **DEFENSIVE VIEW GUARD**: Views accessing cached models must always guard against `__PHP_Incomplete_Class` and self-heal from database if invalid.

# Mandatory Rule: AlpineJS HTML Escaping & Extraction Standard (Attribute Parsing & Raw Code Leak Prevention)
- 🚫 **ZERO TOLERANCE FOR RAW QUOTES IN INLINE ALPINE ATTRIBUTES**: When writing inline JavaScript within Alpine.js attributes (such as `x-data="..."`, `x-init="..."`, `x-on:...`, `@click="..."`), NEVER use raw double quotes (`"`) or single quotes (`'`) inside string literals or JSON outputs (`@json()`). Raw quotes prematurely close the HTML attribute (e.g. `x-data="{ "`), causing the browser's HTML parser to break and misinterpret any subsequent `>` (such as in arrow functions `() =>`) as the closing bracket of the HTML tag, spilling the entire JavaScript code onto the screen as raw text.
- 🔤 **MANDATORY HTML ENTITY ENCODING**:
  - Tanda kutip ganda (") → `&quot;`
  - Tanda kutip tunggal (') → `&apos;`
- 🛡️ **MANDATORY SCRIPT EXTRACTION FOR COMPLEX STATE**: For complex Alpine components (>3 properties, nested objects, lifecycle hooks, or long methods), NEVER cram large state objects inside inline HTML attributes. ALWAYS extract the component into a separate `<script>` block (e.g. `function componentName() { return { ... }; }`) and bind cleanly using `<tag x-data="componentName()">`.

---

# Error Prevention & Best Practice Guidelines

### 1. Curator & Media Management Prevention Rules
- **Broken / Missing File Guards**: When manipulating or rendering media, never assume physical files exist on the disk.
  - In `MediaObserver` or any cleanup logic, ALWAYS wrap file deletions and directory checks with `if ($disk->exists($path))` or `try { ... } catch (\Throwable)`.
  - In custom attributes (such as `placeholder` or Glide conversions), ALWAYS catch exceptions gracefully (`catch (\Throwable) { return ''; }`) so missing files do not break Livewire pickers or crash Filament tables.
- **Disk & Visibility Isolation**:
  - Sensitive files (KYC, identity cards, payment receipts) MUST be routed to `disk = 'private'` and `visibility = 'private'`.
  - Public assets (products, articles, banners) MUST have `disk = 'public'` and `visibility = 'public'`.
- **Media Deletion Lifecycle**: When a Media record is deleted in Curator Picker, deleting the database record must never fail if the physical file was already missing. Let the deletion proceed cleanly and purge empty parent directories safely.

### 2. Frontend Assets, Vite & Nginx Routing Preventions
- **Nginx Static Asset Routing**:
  - Keep explicit location blocks in `nginx.conf` for `/build/`, `/storage/`, `/fonts/`, and common static files with `access_log off; expires max; try_files $uri =404;`.
  - Map `/storage/` alias directly to `/app/storage/app/public/;` so public media is immediately accessible without relying on dynamic PHP overhead.
- **Vite & Production Build Verification**:
  - Whenever frontend CSS, Tailwind theme, or JS files are modified, always execute `npm run build` so `public/build/manifest.json` stays synchronized with production assets.
- **Livewire 4 Request & Asset Integrity**:
  - Livewire scripts (`/livewire/livewire.js` or custom routes) and update endpoints (`/livewire/update`) must never be intercepted or blocked by aggressive Nginx regexes or caching headers.
  - **FilePond & DOM Re-renders (Filament/Curator)**: In Livewire 4, NEVER use `->live(onBlur: true)` on form fields (like `title`) if their state updates cause dynamic closures (e.g., `directory()`) on `CuratorPicker` or `FileUpload` to re-evaluate. This will trigger a background DOM diffing during active file uploads, destroying the FilePond instance and throwing `TypeError: Cannot read properties of null (reading 'getFiles')`. Decouple dynamic dependencies or handle them in backend hooks (`mutateFormDataBeforeCreate`).
  - **Form Slug Auto-Generation (Livewire 4)**: To auto-generate slugs from `title` without triggering Livewire server-side DOM re-renders, ALWAYS use client-side Alpine.js (`extraAlpineAttributes` with `x-on:input` setting `$wire.set('data.slug', slug, false)`) or backend mutation hooks (`mutateFormDataBeforeCreate`). NEVER attach `live(onBlur: true)` and `afterStateUpdated` to `title` in forms containing media pickers / file uploaders.

### 3. Storage, Log Rotation & Permission Resiliency (Docker/Nixpacks)
- **Midnight Log Rotation Issue**: Laravel's `daily` log channel creates a new file at midnight (`laravel-YYYY-MM-DD.log`). If the PHP-FPM process lacks permissions on `/app/storage/logs/`, it triggers cascading `Permission denied` stream errors.
  - **Prevention**: In `config/logging.php`, specify `'permission' => 0777` for the `daily` and `single` channels.
  - **Build Setup**: Keep `mkdir -p storage/... && chmod -R 777 storage bootstrap/cache` inside `[phases.build]` in `nixpacks.toml`.
  - **Live Container Hotfix**:
    ```bash
    docker exec $(docker ps -q --filter name=neriahpro) sh -c "mkdir -p /app/storage/framework/views /app/storage/framework/cache/data /app/storage/framework/sessions /app/storage/logs /app/bootstrap/cache && chmod -R 777 /app/storage /app/bootstrap/cache"
    ```

### 4. MySQL to PostgreSQL Transition Rules (Strict SQL Standards)
- **PostgreSQL JSON / JSONB Empty String Prohibition**: In PostgreSQL, `json` and `jsonb` columns strictly reject empty strings `""` with `SQLSTATE[22P02]: invalid input syntax for type json (The input string ended unexpectedly)`. In raw SQL queries, batch inserts (`->insert()`), or manual model array mapping, ALWAYS ensure JSON fields are formatted as valid JSON strings (e.g. `json_encode($data)`), valid JSON defaults (`'{}'` / `'[]'`), or explicitly `NULL`. NEVER pass `""` into JSON columns.
- **Self-Referencing Foreign Keys**: In PostgreSQL, self-referencing foreign keys (e.g. `parent_id` referencing the same table) MUST be added via a separate `Schema::table('table_name', ...)` AFTER the `Schema::create` block. Defining them inline within `Schema::create` triggers `SQLSTATE[42830]: Invalid foreign key (no unique constraint matching given keys)`.
- **Database-Agnostic Generated Columns & Queries**: NEVER use MySQL-specific functions like `JSON_UNQUOTE()`, `JSON_EXTRACT()`, or `storedAs(...)` with MySQL JSON expressions in migrations. In search queries, NEVER use `MATCH(...) AGAINST(...) IN BOOLEAN MODE`. Use database-agnostic queries or PostgreSQL `ILIKE` / `LOWER(col::text) LIKE ?`.
- **Date and Time Functions**: In PostgreSQL, functions like `YEAR(col)` or `MONTH(col)` do NOT exist. NEVER use `YEAR(created_at)`. ALWAYS use `EXTRACT(YEAR FROM created_at)::integer` or database driver checks (`DB::getDriverName() === 'pgsql' ? 'EXTRACT(YEAR FROM created_at)::integer as year' : 'YEAR(created_at) as year'`).
- **JSON Column Ordering (`orderBy`)**: In PostgreSQL, JSON columns cannot be sorted directly with `orderBy('col')` (which throws `SQLSTATE[42883]: could not identify an ordering operator for type json`). ALWAYS sort JSON columns by specific language keys (e.g. `orderByRaw("col->>'{$locale}' asc, col->>'id' asc")` or `orderByRaw("col->>'id' asc")`).
- **Foreign Key Disabling in Seeders**: NEVER use raw SQL `DB::statement('SET FOREIGN_KEY_CHECKS=0;')` (which crashes PostgreSQL with `SQLSTATE[42704]`). ALWAYS use Laravel's standard `Schema::disableForeignKeyConstraints()` and `Schema::enableForeignKeyConstraints()`.

### 5. Layout Architecture & Global Navigation Visibility (Declarative Component-Driven Props)
- **Brittle URL Route Matching Prohibition**: NEVER use URL string pattern matching (e.g., `request()->is('donor*')` or hardcoded route checks) inside base layouts (`app.blade.php`) to hide/show global elements (like `navigation` or `footer`). Route changes and slug updates will silently break this logic.
- **Declarative Layout Props**: ALWAYS use explicit component props in `resources/views/components/layouts/app.blade.php`:
  ```blade
  @props([
      'title' => null,
      'metaDescription' => null,
      'metaImage' => null,
      'hideNav' => false,
      'hideFooter' => false,
  ])
  ```

### 6. Nixpacks, PHP Versioning & Modern PHP Syntax Compatibility (PHP 8.4 & 8.5)
- **Nixpacks PHP Provider Detection**:
  - Nixpacks (used by Coolify / Railway) automatically provisions the PHP runtime binary by directly parsing the `"require": { "php": "..." }` constraint in `composer.json`.
  - ⚠️ Environment variables like `NIXPACKS_PHP_VERSION` in `nixpacks.toml` or Coolify environment settings are **ignored** by Nixpacks's PHP provider when choosing the Nix package.
- **PHP 8.4+ Syntax in Modern Packages (Property Hooks)**:
  - Framework dependencies in Laravel 13 (notably `symfony/http-foundation`) use PHP 8.4 property hooks syntax:
    ```php
    public ParameterBag $attributes {
        set { ... }
    }
    ```
  - If `composer.json` targets PHP 8.3 (`^8.3`), Nixpacks provisions PHP 8.3. During `composer install --ignore-platform-reqs`, post-autoload-dump runs `@php artisan package:discover`, which crashes on PHP 8.3 with:
    `Parse error: syntax error, unexpected token "{", expecting "," or ";" in /app/vendor/symfony/http-foundation/Request.php on line 117`.
- **PHP 8.4 vs PHP 8.5 Strategy (Local Development vs Production Container)**:
  - Local workstation runs **PHP 8.5** (`PHP 8.5.x` CLI).
  - Nixpacks / NixOS (`nixpkgs`) currently only provides up to **PHP 8.4** (`php84`). Specifying only `"php": "^8.5"` will cause Nixpacks to fail with `No version available for ^8.5`.
- **MANDATORY RULE**: In `composer.json`, ALWAYS maintain the dual-version requirement:
    ```json
    "require": {
        "php": "^8.4|^8.5",
        ...
    },
    "config": {
        "platform": {
            "php": "8.4.4"
        }
    }
    ```
    This satisfies local PHP 8.5 execution while guaranteeing Nixpacks resolves to PHP 8.4 on Coolify.
- **Lock File Synchronization**:
  - Whenever modifying PHP constraints or dependencies in `composer.json`, ALWAYS run `composer update --lock` to update `content-hash` in `composer.lock` without unintentionally upgrading other dependencies.

### 7. Redis Client Resiliency & Container Execution
- **Redis Driver Fallback (`Class "Redis" not found` Prevention)**:
  - If `CACHE_STORE=redis`, `SESSION_DRIVER=redis`, or `QUEUE_CONNECTION=redis` is used in production, never assume the PHP C-extension (`ext-redis` / `phpredis`) is installed in the container.
  - In `config/database.php`, always configure Redis client to gracefully fall back:
    ```php
    'client' => (env('REDIS_CLIENT') === 'predis' || ! extension_loaded('redis')) ? 'predis' : 'phpredis',
    ```
  - In `config/cache.php`, ensure the default cache store gracefully falls back to `database` if Redis client is missing:
    ```php
    'default' => (env('CACHE_STORE') === 'redis' && ! extension_loaded('redis') && ! class_exists(\Predis\Client::class))
        ? 'database'
        : env('CACHE_STORE', 'database'),
    ```
  - Always maintain `predis/predis` in `composer.json` (`composer require predis/predis`) as a pure PHP Redis client fallback.
  - In `deploy.sh`, avoid naked `php artisan optimize:clear` which crashes if the cache store has driver issues; clear `config:clear`, `route:clear`, `view:clear` and guard `cache:clear 2>/dev/null || true`.
- **Container Execution in `deploy.sh` & Git Sync**:
  - Inside a Docker/Nixpacks container, the `.git` folder does not exist by default. `deploy.sh` guards Git synchronization with `if [ -d .git ]` or `elif [ -n "$GIT_REMOTE_URL" ]` to allow container-level sync via `GIT_REMOTE_URL` without hardcoding credentials in repository files.

### 8. Global Middleware & Initial Database Bootstrapping Resiliency
- **Missing Table Guard in Global Middleware**:
  - Any global middleware executing on every HTTP request (such as `SetGlobalTimezone`) that queries database settings (e.g., `CmsGlobalSetting::where(...)`) MUST be wrapped in `try { ... } catch (\Throwable)`:
    ```php
    $timezone = Cache::rememberForever('app_timezone', function () {
        try {
            $setting = CmsGlobalSetting::where('key', 'app_timezone')->first();
            return $setting ? $setting->value : config('app.timezone', 'UTC');
        } catch (\Throwable) {
            return config('app.timezone', 'UTC');
        }
    });
    ```
    This prevents `SQLSTATE[42P01]: Undefined table` (or `relation does not exist`) 500 errors if the database has not yet been migrated or during fresh deployment setups.
- **Initial Database Seeding Dependency**:
  - In `deploy.sh` Skenario 2 (Migrate Safe), always chain essential configuration seeders (`php artisan db:seed --class=CmsSeeder --force`) so global settings tables (`cms_global_settings`) are populated immediately after migration without manual intervention.



