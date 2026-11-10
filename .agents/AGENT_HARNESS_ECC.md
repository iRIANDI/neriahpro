# PROJECT OS AGENT HARNESS & EXECUTION PROTOCOL (PONYTAIL + ECC ENGINE)

## 1. The Ponytail + ECC 4-Rung Decision Ladder (Token & Context Optimization)
Untuk menghemat >50% konsumsi token dan mencegah *context pollution*:
- **Rung 1 (Framework Native First)**: Maksimalkan fitur bawaan Laravel 13, Filament v5, Livewire 4, Flux UI, dan Alpine.js sebelum membuat abstraksi baru.
- **Rung 2 (Graphify O(1) Lookup First)**: DILARANG melakukan *blind directory scanning*. Baca `.agents/graphify/graph.json` dan `.agents/BLUEPRINT_SCHEMA.json` terlebih dahulu, lalu baca baris spesifik yang relevan.
- **Rung 3 (Declarative Over Procedural)**: Utamakan Backed Enum, Schema Contract, Model Casts, dan Database Constraints daripada percabangan `if-else` prosedural.
- **Rung 4 (Surgical Diff Only)**: Terapkan perubahan kode secara presisi pada baris terdampak tanpa menulis ulang seluruh file.

## 2. The 6-Stage ECC Execution Loop (Mandatory Lifecycle per Task)
Setiap pengembangan fitur WAJIB melewati 6 tahapan deterministik ini:
1. **PLAN**: Periksa entitas di `.agents/graphify/graph.json` dan kontrak di `.agents/BLUEPRINT_SCHEMA.json`. Jika ada kebutuhan bisnis di luar spesifikasi, BERHENTI dan TANYA terlebih dahulu.
2. **TEST (TDD First)**: Tulis pengujian Pest PHP untuk memverifikasi *State Machine Guards*, *Pessimistic Concurrency Locks (`lockForUpdate()`)*, *Edge-Case Playbooks*, dan *Tenant Isolation* sebelum menyentuh UI.
3. **IMPLEMENT**: Bangun Migration (ULID + `DECIMAL(19,4)`), Model (`HasUlids`, `BelongsToTenant`), Enum, dan Invokable Action Class (`DB::transaction()`), lalu hubungkan ke Filament v5 / Flux UI.
4. **REVIEW (Self-Audit Against Anti-Slop Checklist)**:
   - Pastikan tidak ada `$table->id()`, `$table->uuid('id')` untuk ULID, `float`, atau `->paginate()`.
   - Pastikan tidak ada `rounded-full`, gradien warna-warni, font kontras pudar, `alert()`, atau CDN eksternal.
5. **VERIFY (4-Pillar Synchronization)**:
   - Sinkronkan: **(1) AI RAG & Native Tool Registry**, **(2) Smart Guide Admin**, **(3) RBAC Policies**, dan **(4) SHA-256 Chained Audit Log**.
6. **REMEMBER & DEPLOY**:
   - Perbarui `.agents/graphify/graph.json` setiap menambah Model/Action/Endpoint baru.
   - Commit & push ke repositori Git, lalu jalankan skenario `./deploy.sh [1-6]` yang sesuai.

## 3. Built-in Agent "Instincts" (Auto-Triggered by File Context)
- **Saat menyentuh `database/migrations/*.php`**:
  - *Instinct*: Wajib `ulid('id')->primary()`, `foreignUlid()`, `DECIMAL(19,4)`, larangan sintaks MySQL di PostgreSQL, dan pemisahan `Schema::table` untuk *self-referencing FK*.
- **Saat menyentuh `app/Actions/**/*.php`**:
  - *Instinct*: Wajib `DB::transaction()`, akuisisi `lockForUpdate()` pada parent resource dengan urutan ULID konsisten (anti-deadlock), validasi Guard Enum sebelum mutasi, dan pencatatan jejak audit.
- **Saat menyentuh `app/Filament/**/*.php`**:
  - *Instinct*: Wajib signature `\Filament\Schemas\Schema $form`, `Width::Full`, `Grid::make(['default' => 1, 'xl' => 12])`, `TranslatableTabs` in-place (tanpa `LocaleSwitcher` global), dan proteksi FilePond dari `->live(onBlur: true)`.
- **Saat menyentuh `resources/views/**/*.blade.php` atau `resources/js/**/*.jsx`**:
  - *Instinct*: Wajib `tabular-nums` & pemisah ribuan pada angka, sudut tipis maksimal `rounded-md` (haram `rounded-full`), hairline border 1px kontras tinggi dark/light, tanpa `alert()`, dan 100% aset lokal tanpa CDN.

## 4. Open Agent Skills Ecosystem & Vercel Skills Hub (`npx skills` & `find-skills`)
Proyek ini mengadopsi standar resmi Open Agent Skills (`skills.sh`) dari Vercel Labs:
- **Skill Discovery Engine**: Gunakan modul `find-skills` untuk menemukan modul keahlian terverifikasi saat menghadapi kebutuhan kapabilitas baru:
  ```bash
  npx skills add https://github.com/vercel-labs/skills --skill find-skills
  ```
- **Manajemen Skill (CLI)**:
  - `npx skills find [query]` — Cari skill interaktif atau berdasarkan kata kunci di ekosistem global.
  - `npx skills add <package> --skill <nama-skill>` — Pasang skill spesifik secara terisolasi ke `.agents/skills/`.
  - `npx skills update` — Perbarui seluruh modul skill ke versi mutakhir.
- Seluruh modul skill disimpan secara deterministik di `.agents/skills/` dan diverifikasi integritasnya via `skills-lock.json`.

## 5. ECC Universal v2.2.3 CLI Engine (`.agents` Execution Target)
Folder `.agents/` pada proyek ini dioperasikan di bawah manajemen ECC Universal v2.2.3 (`antigravity` target):
- **Diagnosa Kesehatan**:
  ```bash
  npx ecc-universal@2.2.3 doctor --target antigravity
  ```
- **Daftar Target Terpasang**:
  ```bash
  npx ecc-universal@2.2.3 list-installed
  ```
- **Sinkronisasi / Re-install Modul Harness**:
  ```bash
  npx ecc-universal@2.2.3 install --target antigravity php --no-hooks
  ```
- Seluruh state tercatat dan diverifikasi di `.agents/ecc-install-state.json`.
