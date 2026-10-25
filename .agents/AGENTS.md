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

# Mandatory Rule: Strict Ban on Hardcoded Magic Strings & Fuzzy String Queries (Zero Tolerance for "Dosa Hardcode")
- 🚫 **HARAM HUKUMNYA PAKAI HARDCODED STRING & DEMO PATTERNS**:
  - NEVER use hardcoded entity names, company names, dummy slugs, or demo aliases in backend queries or business logic (e.g. `orWhere('midtrans_order_id', 'LIKE', '%APEX%')`, `if ($this->slug === 'apex-logistics-global-prd')`, or matching by filenames/folder names).
  - Volatile strings like company names, person names, slugs, and filenames CAN and WILL change, rename, or collide in production. Relying on them corrupts global business state and causes false positives (such as treating unpaid projects as settled).
- 🔒 **MANDATORY CONCRETE RELATIONS & DATABASE PRIMARY KEYS**:
  - ALWAYS identify models, transactions, orders, and documents strictly via **Concrete Relational Foreign Keys** (e.g., `related_id`, `related_type`, `foreignUlid('blueprint_id')`) or **Exact Primary Key Lookups** (`find($id)`, `where('id', $ulid)`).
  - Business states (such as DP Settlement, Contract Signing, Scope Freezing) MUST strictly derive from **concrete database status enums/columns** (e.g., `$transaction->status === 'settlement'`, `$document->scope_locked === true`, `$blueprint->project_status === 'Active Sprint'`), NEVER by fuzzy string searching (`LIKE '%...'`) on customer emails, notes, order descriptions, or dummy company names.

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

# Mandatory Rule: Subtle Round Corners & Zero-Capsule Standard (Strict Ban on Capsule/Pill Shapes)
- 📐 **SUBTLE & THIN CORNERS / ZERO CORNERS**: UI/UX border radius MUST be subtle and thin (`rounded-none`, `rounded-xs` (2px), `rounded-sm` (4px), max `rounded-md` (6px)). Provide clean options without round corners (`rounded-none`) for sharp technical aesthetic.
- 🚫 **STRICT BAN ON CAPSULE/PILL BUTTONS**: NEVER use `rounded-full` or thick capsule shapes on buttons, cards, badges, or inputs. Excessive rounding looks like generic "AI slop" templates and wastes clickable bounding areas.

# Mandatory Rule: Clean Solid Brutalist Theme (Strict Ban on Gaudy Multi-Color Gradients)
- 🚫 **HINDARI GRADIENT STYLE**: Hindari gradient style dalam membuat theme UI.
- 🎨 **SOLID, MONOCHROME & SHARP BRUTALISM**: Gunakan warna solid, sleek monochrome, border presisi tajam (`border-zinc-200 dark:border-zinc-800`), dan background solid (`bg-white dark:bg-zinc-950`). Jangan gunakan background gradien warna-warni yang mencolok yang terkesan "AI slop / template murahan".

# Mandatory Rule: Comprehensive Dark & Light Mode Fidelity & Unique Hover Micro-Effects
- 🌓 **100% DARK & LIGHT MODE COMPLIANCE**: Selalu buat theme dark / light dan pastikan setiap komponen yang dibuat comply dengan theme ini secara seimbang.
- ✨ **UNIQUE HOVER MICRO-EFFECTS**: Setiap elemen interaktif (tombol, baris tabel, kartu, tab) WAJIB memiliki efek hover unik yang tetap mempertahankan kontras tema: elevasi latar belakang halus (`hover:bg-zinc-100/90 dark:hover:bg-zinc-800/80`), penegasan border (`hover:border-zinc-400 dark:hover:border-zinc-600`), atau focus ring aksen (`hover:ring-1 hover:ring-emerald-500/60`). Dilarang keras efek hover yang membuat teks kehilangan kontras atau bertabrakan warna.
- 🎨 **SEAMLESS CONTRAST & LEGIBILITY**: Gunakan pasangan class Tailwind secara disiplin (`bg-white dark:bg-zinc-950`, `text-zinc-900 dark:text-zinc-100`, `border-zinc-200 dark:border-zinc-800`, `hover:bg-zinc-100 dark:hover:bg-zinc-900`). Dilarang keras membuat komponen yang hanya terlihat bagus di salah satu mode (misal teks hitam di background gelap).

# Mandatory Rule: High-Contrast Text Legibility & Strict Ban on Low-Contrast "AI Slop" (Anti-Tenggelam)
- 👁️ **MANDATORY WCAG AAA/AA CONTRAST RATIO**: Seluruh teks, tipografi, badge, tag, dan label WAJIB memiliki rasio kontras tinggi yang tajam terhadap latar belakangnya. Dilarang keras membuat font teks yang warnanya pudar atau tenggelam ("AI slop") sehingga sulit dibaca oleh pengguna.
- 🚫 **STRICT BAN ON LOW-CONTRAST PASTEL ON LIGHT BACKGROUNDS**:
  - Pada latar terang (kartu putih, background abu-abu terang `bg-zinc-100`, atau container pastel), DILARANG KERAS menggunakan warna font pastel pudar (seperti `text-emerald-500`, `text-amber-500`, `text-cyan-500`, atau `text-zinc-400`). Warna-warna tersebut memiliki contrast ratio sangat rendah (<3:1) dan tidak terbaca di layar monitor normal.
  - WAJIB gunakan teks bernada pekat dan tegas di latar terang: `text-zinc-950`, `text-emerald-950` / `text-emerald-900` / `text-emerald-800`, `text-amber-950` / `text-amber-900`, `text-cyan-950` / `text-cyan-900`.
- 🚫 **STRICT BAN ON TRANSLUCENT DARK-ON-DARK BADGES INSIDE BUTTONS**:
  - Pada tombol atau area berwarna solid (misalnya tombol hijau aktif `bg-emerald-600 text-black`), badge/chip di dalamnya DILARANG KERAS menggunakan `bg-black/20 text-black` yang menghasilkan teks hitam di atas hijau tua transparan (sangat buram dan tidak terbaca).
  - Badge di dalam tombol aktif WAJIB menggunakan kontras solid ekstrem seperti `bg-black text-white font-black px-2 py-0.5` atau latar putih dengan teks hitam.
- 🌓 **CRISP LIGHT & DARK BADGE PROTOCOL**:
  - Light mode badge: Gunakan background tint 100 dengan teks 950 dan border 400 (e.g. `bg-emerald-100 text-emerald-950 border border-emerald-400 font-black`).
  - Dark mode badge: Gunakan background pekat dengan teks 200/300 dan border 700 (e.g. `dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-black`).
  - Pastikan seluruh badge dan teks terbaca jelas dalam sekali lirik tanpa menyipitkan mata.


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

# Mandatory Rule: Bulletproof Table & List Scalability (Millions of Records & Viewers // Ultimate Enterprise Table)
- 📊 **TABEL WAJIB SEARCH & PAGINATION O(1)**: Tabel wajib menggunakan search berindeks dan pagination yang bulletproof untuk skalabilitas jutaan data dan viewer: Keyset Cursor Pagination O(1) (`cursorPaginate()`). Dilarang keras menggunakan offset-based pagination (`paginate()`).
- 📌 **FLOATING STICKY HEADER**: Header tabel WAJIB tetap melayang saat di-scroll vertikal (`sticky top-0 z-20 bg-zinc-100/95 dark:bg-zinc-900/95 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800`).
- 🧊 **FREEZE COLUMNS (PIN KOLOM KIRI & KANAN)**: Mendukung pembekuan kolom saat scroll horizontal: Kolom seleksi checkbox dan Primary ID beku di kiri (`sticky left-0 z-30`), dan kolom aksi beku di kanan (`sticky right-0 z-30`).
- ☑️ **MULTI-SELECT & FLOATING BATCH ACTION BAR**: Checkbox di setiap baris dan 'Select All' di header. Saat 1+ baris terpilih, munculkan floating batch bar di bawah layar (`sticky bottom-4 z-40 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 px-4 py-2.5 rounded-xs shadow-2xl`) untuk ekspor massal, ubah status, atau hapus.
- 🦓 **ZEBRA EFFECT & CELL HOVER FOCUS RING**: Baris selang-seling (`odd:bg-white dark:odd:bg-zinc-950 even:bg-zinc-50/60 dark:even:bg-zinc-900/40`), elevasi hover baris (`hover:bg-zinc-100/90 dark:hover:bg-zinc-800/80`), dan cell yang disorot kursor mouse mendapatkan focus ring aksen (`hover:ring-1 hover:ring-inset hover:ring-emerald-500/60 cursor-pointer`).
- 📏 **ROW DENSITY SWITCHER**: Sediakan opsi kerapatan baris: Compact (`py-1.5 px-3 text-xs`), Normal (`py-3 px-4 text-xs`), Relaxed (`py-4 px-5 text-sm`).
- 👁️ **COLUMN VISIBILITY SELECTOR & TRI-STATE SORTING**: Sediakan menu dropdown untuk menyembunyikan/menampilkan kolom secara dinamis, dan sorting 3 status (Ascending, Descending, Unsorted) dengan ikon panah aktif.
- 📥 **QUICK EXPORT (CSV / JSON)**: Tombol ekspor cepat 1-klik untuk baris terseleksi atau seluruh hasil query cursor saat ini.
- 🔲 **DUAL VIEW (GRID & LIST VIEW)**: Setiap membuat list data, pastikan selalu sediakan search, pagination O(1), dan tampilan Grid View dan List View switcher untuk menjamin fleksibilitas dan kenyamanan pengguna dalam melihat data berskala besar.
- ✖️ **INSTANT CLEAR BUTTON ("X") PADA SEARCH FIELD**: Field search selalu wajib ada icon "X" (tombol hapus instan) untuk menghapus teks yang sudah ada di dalam textfield search secara instan dengan satu klik tanpa harus menekan tombol backspace berulang kali.

# Mandatory Rule: SEO Browser Tab Title Standard
- 🏷️ **BROWSER TAB TITLE FORMAT**: Browser page titles must strictly follow the format: `[NAMA DOMAIN - NAMA PAGE]` (e.g. `neriahpro.com - Digital Services Hub`).

# Mandatory Rule: Anti-AI Malware & Cyber Threat Defense
- 🛡️ **ANTI-AI MALWARE SUITE**: Implement multi-layered defenses against automated AI scrapers and malware (honeypot fields, rate limiting, strict CSP, bot challenge mitigation) along with user-facing educational security transparency.

# Mandatory Rule: Backend Admin AI Agentic Engine
- 🤖 **AGENTIC DECISION INTELLIGENCE**: Backend admin features must integrate AI Agentic capabilities:
  - Structured Knowledge Base
  - Retrieval-Augmented Generation (RAG) to eliminate hallucinations

# Mandatory Rule: Multi-Language 2-Tier Architecture (Database Native JSON & Whitelisted Global Plugin)
- 🌐 **BACKEND DUAL-LOCALE & DATABASE NATIVE JSON (TIER 1)**:
  - Selalu sediakan multi bahasa Tier 1 dari database di mana kolomnya adalah JSON (bukan string biasa tunggal) dan di-cast sebagai `'array'` (`{"id": "...", "en": "..."}`).
  - Memungkinkan backend admin Filament membuat dan mengedit dua versi bahasa (ID & EN) secara resmi dan terstruktur.
- 🌐 **FRONTEND GLOBAL TRANSLATE PLUGIN (TIER 2)**:
  - Ditenagai oleh plugin Google Translate di frontend.
  - Whitelist opsi bahasa disimpan di database backend admin (`CmsGlobalSetting`) dan dapat ditentukan bahasa apa saja yang dimunculkan ke publik.
- 🌐 **SINGLE UNIFIED NAVBAR DROPDOWN**:
  - Di navigasi, dilarang membuat switch bahasa redundant (misal dobel tombol ID/EN dan tombol Global berdampingan). Cukup gunakan SATU dropdown terpadu yang memuat dua list group: Tier 1 (Native Precise) dan Tier 2 (Global Translate).

# Mandatory Rule: Strict Prohibition of Page-Level Locale Switcher Dropdowns (Standar Wajib TranslatableTabs In-Place)
- 🚫 **HARAM HUKUMNYA DROPDOWN LOCALE SWITCHER DI SUDUT HALAMAN**:
  - DILARANG KERAS menggunakan dropdown pemilih bahasa di pojok kanan atas halaman (`LocaleSwitcher::make()`, `LaraZeus\SpatieTranslatable`, atau page-level locale switchers) di seluruh resource dan page Filament.
  - Menghapus 100% penggunaan `LocaleSwitcher::make()` dan trait `LaraZeus\SpatieTranslatable` dari seluruh halaman Filament (`CreateRecord`, `EditRecord`, `ListRecords`, `ManageRecords`, dan `ViewRecord`).
  - Pojok kanan atas halaman wajib bersih dan luas tanpa dropdown bahasa global yang membingungkan.
- 🇮🇩🇬🇧 **TAB BAHASA TIER 1 LANGSUNG PADA KOTAK INPUT MASING-MASING (`TranslatableTabs`)**:
  - Setiap field yang menyimpan teks dwibahasa (JSON / Spatie Translatable) wajib memiliki sub-tab independen langsung di dalam form-nya menggunakan `App\Filament\Forms\Components\TranslatableTabs`:
    - 🇮🇩 **Indonesia (ID)**
    - 🇬🇧 **English (EN)**
- ✨ **FITUR GOOGLE TRANSLATE 1-KLIK DI SETIAP INPUT (`GoogleTranslateService`)**:
  - Di samping kanan label atau di dalam tab masing-masing input, terpasang tombol translasi cerdas berbasis `App\Services\Translation\GoogleTranslateService`:
    - **Di Tab Indonesia**: Tombol `✨ Terjemahkan ke EN` → Mengklik tombol ini seketika menerjemahkan teks Indonesia dan otomatis mengisikannya ke tab Bahasa Inggris.
    - **Di Tab English**: Tombol `✨ Terjemahkan ke ID` dan `🔄 Tarik dari ID` → Untuk menyinkronkan atau memperbarui terjemahan jika narasi Indonesia diubah.
- ⚡ **AUTO-TRANSLATE SAAT SIMPAN (`HasTranslatableTabs`)**:
  - Setiap Page `CreateRecord` dan `EditRecord` untuk model translatable wajib menggunakan trait `App\Filament\Concerns\HasTranslatableTabs`.
  - Jika admin sedang buru-buru dan hanya mengisi tab Indonesia lalu menekan tombol Simpan, sistem secara otomatis menerjemahkan teks tersebut ke Bahasa Inggris di latar belakang sebelum disimpan ke database, sehingga data publik tidak pernah kosong.


# Mandatory Rule: Country Zone Dialing Code Standard
- 📞 **COUNTRY ZONE INPUTS**: Setiap membuat input nomor telepon atau WhatsApp, WAJIB menggunakan country code dropdown yang bisa di-search (`config/country_zones.php`, e.g. +62, +65, +1, +44, +81) untuk menegakkan standar E.164 internasional dan mengeliminasi nomor telepon tidak valid.

# Mandatory Rule: Thousand Separators on Numbers & Currencies
- 🔢 **THOUSAND SEPARATORS**: Setiap membuat input atau tampilan nominal angka atau currency yang mencapai ribuan (>= 1.000), WAJIB memformat pemisah ribuan (titik `.` untuk ID / koma `,` untuk EN) di seluruh komponen UI/UX.

# Mandatory Rule: Local FontAwesome Icons (Zero CDN Latency)
- 🎨 **LOCAL FONTAWESOME LIBRARY**: Representative icons for navigation, footer, global alert, and plugins MUST use local SVGs (`config/fontawesome.php` and `\App\Support\FontAwesome::svg('name')`) to prevent third-party CDN latency, blocking, or offline broken layouts.

# Mandatory Rule: Managed Sprint Capacity, Anti-Collision Batch Architecture & Local Gantt Timeline Dashboard
- ⏱️ **MANAGED CAPACITY PHILOSOPHY**: Tidak menerima proyek paralel tak terbatas demi menjaga standar rekayasa enterprise (anti-burnout, anti-AI-slop). Kapasitas sprint dibatasi maksimal 2–3 proyek paralel per batch (Batch 1, Batch 2, Batch Q1).
- 🛡️ **DUAL ANTI-COLLISION ENGINE**:
  - *Schedule Collision Prevention*: Klien mengunci slot waktu sprint terencana via frontend modal (`ArchitecturePricingIsland.jsx`) sehingga alokasi engineer 100% fokus dan terisolasi.
  - *Payment Collision Prevention*: Order generator berbasis ULID unik dan webhook Midtrans idempoten mendeteksi serta mencegah transaksi ganda (double-payment auto-refund / termin credit conversion).
- 📦 **STRICT LOCAL JS BUNDLE PROTOCOL (ZERO CDN LATENCY)**:
  - Seluruh pustaka JavaScript untuk visualisasi data, diagram Gantt, dan interaktivitas (Mermaid.js, Alpine.js, dsb.) WAJIB diunduh dan disimpan secara lokal di `public/js/vendor/` (`mermaid.min.js`, `alpine.min.js`).
  - Dilarang keras memuat script atau stylesheet dari CDN pihak ketiga (cdnjs, unpkg, jsdelivr) untuk mencegah latensi jaringan, blocking ISP, atau layout rusak saat offline.
- 📊 **FILAMENT SPRINT TIMELINE & GANTT DASHBOARD**:
  - Seluruh alokasi batch, kapasitas slot terisi/sisa, dan diagram Gantt multi-proyek wajib dapat dikelola serta dimonitor melalui Filament Admin Page `SprintTimelinePage` (`/admin/sprint-timeline`) dan widget `SprintCapacityGanttWidget`.
- 🏛️ **FULL CUSTOM ENGINEERING STUDIO STANDARDS (7 PILARS TURNKEY)**:
  - Layanan Pengembangan Penuh & Pengerjaan Kode oleh Tim Neriah Pro (`ArchitecturePricingIsland.jsx` Studio Contracts) wajib patuh pada standar Project OS:
    1. *Advisory Tier (Rp 2.5 Jt)*: Dokumen PRD 26 parameter, PostgreSQL 16 Strict ULID DDL, WBS 5 Sprint Mermaid Gantt, dan jaminan potongan 100% biaya advisory terhadap DP 50% jika berlanjut ke Full MVP.
    2. *Full Monolith MVP Tier (Rp 50 Jt, DP 50%)*: 100% turnkey dikerjakan langsung oleh Senior Architect mencakup seluruh 7 Pilar Software Factory OS (PRD, Strict ULID Keyset O(1), Docker PHP 8.5+/PG 16/Redis 7/Nginx, Synthetic Seeder 100+ baris, AI Agent Rules .cursorrules/AGENTS.md, ApiContractTest Pest, CI/CD deploy.sh), Master Gantt Timeline real-time di Customer Portal (`/customer/dashboard`), Midtrans Snap payment idempotency, Dedicated VPS hardening, dan garansi SLA 3 bulan penuh.
    3. *UMKM Digital Starter Tier (Rp 3.75 Jt)*: Engine transaksi web, kasir, database PostgreSQL Strict ULID terpusat, Filament v5 admin dashboard Bahasa Indonesia, pembayaran QRIS/VA otomatis, notifikasi WhatsApp E.164, dan domain cloud hosting bergaransi 1 bulan.

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
- 🚫 **ZERO TOLERANCE FOR RAW QUOTES IN INLINE ALPINE ATTRIBUTES**: Ketika menulis inline JavaScript di dalam atribut Alpine.js (seperti `x-data="..."`, `x-init="..."`, `x-on:...`, `@click="..."`), JANGAN PERNAH gunakan raw double quotes (`"`) atau single quotes (`'`) di dalam string literals atau output JSON (`@json()`). Raw quotes akan menutup atribut HTML secara prematur (misal `x-data="{ "`), menyebabkan parser HTML browser rusak dan salah menafsirkan karakter `>` (seperti arrow function `() =>`) sebagai penutup tag HTML, yang menumpahkan seluruh kode JavaScript ke layar pengguna sebagai teks mentah.
- 🔤 **MANDATORY HTML ENTITY ENCODING**:
  - Tanda kutip ganda (") → `&quot;`
  - Tanda kutip tunggal (') → `&apos;`
- 🛡️ **MANDATORY SCRIPT EXTRACTION FOR COMPLEX STATE**: Untuk komponen Alpine yang kompleks (>3 properti, nested objects, lifecycle hooks, atau method panjang), JANGAN jejali state besar di dalam inline HTML attributes. SELALU ekstrak komponen ke dalam blok `<script>` terpisah (misal `function componentName() { return { ... }; }`) dan hubungkan secara bersih dengan `<tag x-data="componentName()">`.

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

### 6. Nixpacks, PHP Versioning & Modern PHP Syntax Compatibility (PHP 8.5+)
- **Nixpacks PHP Provider Detection**:
  - Nixpacks (used by Coolify / Railway) automatically provisions the PHP runtime binary by directly parsing the `"require": { "php": "..." }` constraint in `composer.json`.
  - ⚠️ Environment variables like `NIXPACKS_PHP_VERSION` in `nixpacks.toml` or Coolify environment settings are **ignored** by Nixpacks's PHP provider when choosing the Nix package.
- **PHP 8.5+ Syntax in Modern Packages (Property Hooks)**:
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



