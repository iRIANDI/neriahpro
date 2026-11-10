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

# Mandatory Rule: AlpineJS HTML Escaping & Extraction Standard (Attribute Parsing & Raw Code Leak Prevention)
- 🚫 **ZERO TOLERANCE FOR RAW QUOTES IN INLINE ALPINE ATTRIBUTES**: When writing inline JavaScript within Alpine.js attributes (such as `x-data="..."`, `x-init="..."`, `x-on:...`, `@click="..."`), NEVER use raw double quotes (`"`) or single quotes (`'`) inside string literals or JSON outputs (`@json()`). Raw quotes prematurely close the HTML attribute (e.g. `x-data="{ "`), causing the browser's HTML parser to break and misinterpret any subsequent `>` (such as in arrow functions `() =>`) as the closing bracket of the HTML tag, spilling the entire JavaScript code onto the screen as raw text.
- 🔤 **MANDATORY HTML ENTITY ENCODING**:
  - Tanda kutip ganda (") → `&quot;`
  - Tanda kutip tunggal (') → `&apos;`
- 🛡️ **MANDATORY SCRIPT EXTRACTION FOR COMPLEX STATE**: For complex Alpine components (>3 properties, nested objects, lifecycle hooks, or long methods), NEVER cram large state objects inside inline HTML attributes. ALWAYS extract the component into a separate `<script>` block (e.g. `function componentName() { return { ... }; }`) and bind cleanly using `<tag x-data="componentName()">`.

# Mandatory Rule: Pixel-Perfect Sticky Navigation, Zero-Gap Alignment & Dynamic DOM Reflow Protocol (Anti-Displaced Scroll & Ghost ID Prevention)
- 📐 **ZERO-GAP / ZERO-OVERLAP STICKY ALIGNMENT STANDARD (PENDARATAN PRESISI GARIS BATAS BAWAH TOOLBAR)**:
  - Saat pengguna mengklik link navigasi (sidebar index, TOC, header pills, tombol jumpTo), elemen target WAJIB mendarat tepat menempel (*flush*) di batas bawah sticky header/toolbar (`top: globalNavH + toolbarH`) tanpa celah kosong dan tanpa tertutup sebagian.
  - 🚫 **DILARANG KERAS MENAMBAH GAP ARSIFIAL / HARDCODED OFFSET**: Dilarang menyisipkan offset sembarangan seperti `+ 16px`, `+ 20px`, atau hardcoded magic classes (`scroll-mt-28`, `scroll-mt-32`). Menambah gap artifisial menyebabkan konten berhenti melayang di bawah toolbar dan tampak cacat/rusak.
  - 📏 **FORMULA PERSAMAAN SCROLL WAJIB**:
    - Offset batas bawah sticky: `const stickyOffset = Math.round(globalNavH + toolbarH);`
    - Target absolut scroll: `const targetY = Math.max(0, Math.round(el.getBoundingClientRect().top + window.pageYOffset - stickyOffset));`
    - CSS Class `.prd-scroll-target`: `scroll-margin-top: calc(var(--global-nav-h, 88px) + var(--toolbar-h, 46px)) !important;`
  - 🎯 **SUB-PIXEL SETTLE HANDLER**: Pasang listener event `scrollend` (dengan fallback timeout 900–1200ms) untuk memeriksa deviasi sub-pixel rendering (`Math.abs(el.getBoundingClientRect().top - stickyOffset)`). Jika selisih berada di rentang 2px–60px, lakukan auto micro-adjustment seketika via `window.scrollBy({ top: diff, behavior: 'instant' })` sehingga batas atas seksi 100% menempel tepat pada garis batas bawah toolbar.

- 🆔 **MANDATORY CONCRETE TARGET ID INTEGRITY & ANTI-GHOST ID STANDARD**:
  - Setiap item seksi/bab yang didaftarkan pada navigasi, sidebar, table of contents, drawer, atau scrollspy WAJIB memiliki elemen kontainer fisik dengan atribut `id` yang 100% identik di dalam DOM.
  - 🚫 **STRICT BAN ON GHOST IDS**: Dilarang keras mendaftarkan ID pada konfigurasi navigasi yang tidak memiliki elemen padanan di HTML/Blade. Jika ada alias modul, pasang atribut ganda (`id="section-13" data-alias-id="sub-sec-6-13-export"`) dan fallback lookup resolver pada helper JS `jumpToSection`.
  - Seluruh kontainer seksi utama maupun sub-seksi arsitektur yang dapat dituju lewat navigasi WAJIB menyertakan class `.prd-scroll-target`.

- ⚡ **ASYNCHRONOUS DOM REFLOW & EXPANSION PROTOCOL (`$nextTick` / `requestAnimationFrame`)**:
  - Jika target seksi (atau induk kontainernya) sedang dalam status tertutup/ciut (*collapsed*), sistem WAJIB membuka state collapse tersebut terlebih dahulu.
  - ⚠️ **DILARANG MENGHITUNG KOORDINAT SEBELUM REFLOW**: DILARANG KERAS memanggil `getBoundingClientRect().top` atau mengeksekusi scroll secara sinkron langsung setelah mengubah variabel state Alpine/React. Pemanggilan fungsi scroll WAJIB dibungkus ke dalam `this.$nextTick(() => { window.jumpToSection(id); })` atau `requestAnimationFrame` agar browser menyelesaikan reflow DOM terlebih dahulu dan mengukur tinggi elemen yang sudah mengembang penuh.

- 🔄 **SCROLLSPY PHYSICAL DOM ORDER STANDARD (ANTI-FALSE TRIGGERS)**:
  - Urutan array ID seksi pada ScrollSpy (`setupBlueprintScrollSpy`) WAJIB diurutkan secara persis dari atas ke bawah mengikuti urutan fisik elemen dalam DOM. Sub-seksi yang secara fisik berada di dalam atau di antara seksi lain wajib ditempatkan pada urutan fisiknya (misal: sub-seksi 13 diletakkan tepat setelah seksi 6, bukan di ujung array setelah seksi 10).
  - Deteksi ujung bawah dokumen (`isBottom`) WAJIB mengarahkan ke elemen paling bawah dokumen yang sebenarnya, bukan seksi yang tersasar di akhir array.
  - Kunci scrollspy (`isProgrammaticScroll = true`) selama animasi smooth scrolling berlangsung (1200–1600ms) untuk mencegah scrollspy merebut atau mengubah active index secara prematur di tengah animasi scroll.

- 🛡️ **ACTIVE SECTION IMMUNITY IN AUTO-ACCORDION / SCROLL OBSERVERS**:
  - Algoritma auto-accordion (`evaluateAccordionStates`) yang menciutkan seksi saat melewati viewport DILARANG KERAS menciutkan seksi yang sedang menjadi target aktif pengguna (`this.activeSectionId`).
  - Sistem tidak boleh mengeksekusi kompensasi scroll (`window.scrollBy({ top: -diff })`) saat proses programmatic smooth scrolling sedang berjalan, guna mencegah scroll jumping dan pergeseran posisi landing.

