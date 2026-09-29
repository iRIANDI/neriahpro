# Ponytail Protocol: Senior Developer Decision Ladder & Token Conservation

> **Filosofi Inti**: *"Kode terbaik adalah kode yang tidak perlu ditulis. Solusi paling elegan adalah yang memanfaatkan kapabilitas bawaan arsitektur secara maksimal dengan biaya token dan komputasi minimal."*

Dokumen ini adalah standar operasional wajib bagi AI coding assistant dan pengembang dalam proyek **Neriah Pro** (Laravel 13, Filament v5, Livewire 4, Flux UI, React 19 Islands, PostgreSQL Strict ULID). Protokol ini dirancang untuk memotong pemborosan token hingga >50% sekaligus mencegah kode berlebihan (*over-engineering/bloat*).

---

## 1. The 4-Rung Decision Ladder (Tangga Keputusan 4 Langkah)

Sebelum menulis baris kode baru atau mengusulkan komponen baru, evaluasi masalah secara ketat melalui 4 anak tangga keputusan berikut dari atas ke bawah:

```
   ┌────────────────────────────────────────────────────────┐
   │ 1. FRAMEWORK NATIVE FIRST                              │
   │    Gunakan fitur bawaan Laravel 13 / Filament v5 /     │
   │    Livewire 4 / Flux UI / React Islands                │
   └───────────────────────────┬────────────────────────────┘
                               │ (Jika tidak mencukupi)
   ┌───────────────────────────▼────────────────────────────┐
   │ 2. REUSE EXISTING CODEBASE (Cek Graphify Dulu!)        │
   │    Manfaatkan Service, Model, Trait, atau Komponen     │
   │    yang SUDAH ADA di neriahpro                         │
   └───────────────────────────┬────────────────────────────┘
                               │ (Jika benar-benar baru)
   ┌───────────────────────────▼────────────────────────────┐
   │ 3. CONFIGURATION OVER CODE                             │
   │    Gunakan atribut deklaratif, skema form, props,      │
   │    atau konfigurasi daripada logika prosedural kustom  │
   └───────────────────────────┬────────────────────────────┘
                               │ (Jika harus menulis kode)
   ┌───────────────────────────▼────────────────────────────┐
   │ 4. SURGICAL & MINIMALIST DIFF                          │
   │    Tulis kode seminimal mungkin, terisolasi, O(1),     │
   │    tanpa mengulang file utuh (Gunakan replace tool)    │
   └────────────────────────────────────────────────────────┘
```

### Rung 1: Framework Native First
- **Filament v5**:
  - Butuh form input terstruktur? Gunakan `Section::make()`, `Tabs::make()`, `KeyValue::make()`, jangan membuat custom Blade form manual di dalam Filament.
  - Butuh dialog konfirmasi? Gunakan `->requiresConfirmation()`, jangan membuat modal JS kustom.
  - Butuh notifikasi? Gunakan `Filament\Notifications\Notification::make()`, jangan membuat flash session handler sendiri.
- **Livewire 4 & Flux UI**:
  - Butuh interaktivitas UI ringan? Gunakan directive Alpine.js atau atribut Livewire 4 bawaan (`wire:model`, `wire:click`).
  - Tanda kutip di AlpineJS wajib di-encode: `&quot;` dan `&apos;`.
- **Laravel 13**:
  - Validasi data? Gunakan `Validator::make()` atau Form Request bawaan.
  - Pembatasan laju / throttle? Gunakan middleware `throttle:30,1` atau `RateLimiter`.
  - Penjadwalan? Gunakan `Schedule::command(...)` di `routes/console.php`.

### Rung 2: Reuse Existing Codebase (Cek Graphify Dulu!)
- **PRD & ERD Generator**: Gunakan `App\Services\PrdGeneratorService`.
- **Kontrak Digital & Scope Freeze**: Gunakan `App\Models\Document` dan method `$blueprint->convertToDigitalContract()`.
- **Manajemen Domain & Hosting**: Gunakan `App\Models\DomainHostingAsset` dan `CheckExpiringAssetsCommand`.
- **Pengaturan Global**: Gunakan `App\Models\CmsGlobalSetting` dengan caching `Cache::rememberForever`.
- **Multi-Bahasa**: Gunakan middleware `App\Http\Middleware\SetAppLocale` dan field JSON `title['id']` / `title['en']`.

### Rung 3: Configuration Over Code
- Gunakan casts Eloquent (`'title' => 'array'`, `'is_published' => 'boolean'`).
- Gunakan schema deklaratif Filament v5 di folder `Schemas/` dan `Tables/`.

### Rung 4: Surgical & Minimalist Diff
- Jangan pernah menulis ulang file 500 baris hanya untuk mengubah 3 baris. Gunakan `replace_file_content` untuk perubahan terisolasi.
- Hindari pembuatan controller atau service baru jika fungsionalitas dapat diselesaikan dengan 1 method ringkas pada model yang ada.

---

## 2. Aturan Hemat Token untuk AI Coding Assistant

1. **Gunakan Graphify Sebelum Melakukan Deep Exploration**:
   - Baca `.agents/graphify/GRAPH_REPORT.md` untuk memahami relasi model, controller, dan resource tanpa perlu `list_dir` atau membaca puluhan file satu per satu.
2. **Hindari Membaca File Utuh Jika Cukup Melihat Snippet**:
   - Gunakan `StartLine` dan `EndLine` pada `view_file` jika hanya membutuhkan fungsi tertentu.
3. **Patuhi Standar Skalabilitas O(1)**:
   - Primary Key: Selalu ULID 26 karakter (`->ulid('id')->primary()`, `HasUlids`). Dilarang keras memakai `$table->id()` atau AUTO_INCREMENT.
   - Paginasi: Selalu gunakan `cursorPaginate()` untuk query data massal.
