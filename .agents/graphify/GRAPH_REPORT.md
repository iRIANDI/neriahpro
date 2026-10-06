# Graphify Architectural Knowledge Graph: Neriah Pro

> **Status Indeks**: Synchronized & Verified  
> **Framework Stack**: Laravel 13 | Filament v5 | Livewire 4 | Flux UI | React 19 Islands  
> **Database**: PostgreSQL 16 (Strict Mode) / MySQL (Local Development)  
> **Primary Key Standard**: ULID (`->ulid('id')->primary()`, `HasUlids`)  
> **Pagination Strategy**: Cursor Pagination ($O(1)$ Keyset Stability)  
> **Multi-Language**: Dual-Locale Native (`id` / `en`) via `SetAppLocale` & JSON attributes  

---

## 1. Topologi Sistem & Arsitektur Global

Aplikasi **Neriah Pro** beroperasi sebagai Digital Architecture Hub & Productized Software Agency OS:

```mermaid
graph TD
    ClientPublic[Klien Publik & Pengunjung Web] -->|HTTP / React 19 Islands| WebRoutes[Web Routes / Frontend]
    WebRoutes --> BlueprintCtrl[BlueprintController /blueprint]
    WebRoutes --> PageCtrl[PageController Dynamic CMS]
    WebRoutes --> DocCtrl[DocumentController Digital Contract]
    
    BlueprintCtrl --> VisionModel[VisionBlueprint (ULID)]
    VisionModel --> PrdService[PrdGeneratorService (PRD & ERD Engine)]
    VisionModel --> DocModel[Document (Digital Contract & Scope Lock)]
    VisionModel --> AssetModel[DomainHostingAsset (Infra & Renewal)]
    
    AdminUser[Superadmin Neriah Pro] -->|Filament v5 Panel /admin| FilamentAdmin[Filament Admin Panel]
    FilamentAdmin --> VisionResource[VisionBlueprintResource]
    FilamentAdmin --> DocResource[DocumentResource]
    FilamentAdmin --> AssetResource[DomainHostingAssetResource]
    FilamentAdmin --> CmsResource[CmsPageResource]
    FilamentAdmin --> ProdResource[ProductResource]
    FilamentAdmin --> TransResource[TransactionResource]
    FilamentAdmin --> LegalResource[LegalPolicyResource]
    
    Scheduler[Cron 08:00 WIB] --> AssetCommand[CheckExpiringAssetsCommand]
    AssetCommand --> AssetModel
    AssetCommand --> Notifications[(Database Notifications)]
    
    MidtransWebhook[Midtrans Payment Webhook] --> TransModel[Transaction]
    TransModel --> DocModel
    TransModel --> AssetModel
```

---

## 2. Katalog Domain & Entity Model (19 Models)

Seluruh model domain bisnis menggunakan ULID (`HasUlids`) string 26-karakter untuk menjamin skalabilitas enterprise dan integritas PostgreSQL:

| Model | Lokasi File | Primary Key | Traits / Fitur Utama | Relasi Utama |
| :--- | :--- | :---: | :--- | :--- |
| `LeadContact` | [LeadContact.php](file:///c:/xampp/htdocs/neriahpro/app/Models/LeadContact.php) | ULID | `HasUlids`, CRM lead contacts database, company metadata JSON, direct mail dispatcher | - |
| `EmailCampaign` | [EmailCampaign.php](file:///c:/xampp/htdocs/neriahpro/app/Models/EmailCampaign.php) | ULID | `HasUlids`, Custom dynamic sender name/email, Reply-to Gmail routing, Audience segmentation | `creator` (belongsTo), `logs` (hasMany) |
| `CvProPlan` | [CvProPlan.php](file:///c:/xampp/htdocs/neriahpro/app/Models/CvProPlan.php) | ULID | `HasUlids`, Dynamic pricing tiers (A, B, C) & a la carte top-up packages | `userQuotas` (hasMany), `transactions` (hasMany) |
| `UserCvQuota` | [UserCvQuota.php](file:///c:/xampp/htdocs/neriahpro/app/Models/UserCvQuota.php) | ULID | `HasUlids`, User quota tracking (tailor, interview, audit, credits) & expiration | `user` (belongsTo), `plan` (belongsTo) |
| `CvQuotaTransaction` | [CvQuotaTransaction.php](file:///c:/xampp/htdocs/neriahpro/app/Models/CvQuotaTransaction.php) | ULID | `HasUlids`, Quota top-up and feature consumption audit trail | `user` (belongsTo), `plan` (belongsTo), `paymentTransaction` (belongsTo) |
| `Resume` | [Resume.php](file:///c:/xampp/htdocs/neriahpro/app/Models/Resume.php) | ULID | `HasUlids`, Multi-template ATS CV, Score audit, Experience/Edu JSON | `user` (belongsTo), `interviewSessions` (hasMany), `outreachLetters` (hasMany) |
| `InterviewSession` | [InterviewSession.php](file:///c:/xampp/htdocs/neriahpro/app/Models/InterviewSession.php) | ULID | `HasUlids`, Mock interview Q&A, Voice audio transcription, STAR score evaluation | `resume` (belongsTo), `user` (belongsTo) |
| `OutreachLetter` | [OutreachLetter.php](file:///c:/xampp/htdocs/neriahpro/app/Models/OutreachLetter.php) | ULID | `HasUlids`, Thank you / follow-up / cold pitch letter generator | `resume` (belongsTo), `user` (belongsTo) |
| `VisionBlueprint` | [VisionBlueprint.php](file:///c:/xampp/htdocs/neriahpro/app/Models/VisionBlueprint.php) | ULID | `HasUlids`, Project OS discovery questionnaire, PRD synthesis, Contract converter, SHA-256 seal, Staging provisioning | `documents` (morphMany), `domainHostingAssets` (hasMany) |
| `BlueprintVoucher` | [BlueprintVoucher.php](file:///c:/xampp/htdocs/neriahpro/app/Models/BlueprintVoucher.php) | ULID | `HasUlids`, Promo & free-bypass vouchers for Ministry/Charity (Rp 0), Usage quota, Expiration | - |
| `PaymentWebhookLog` | [PaymentWebhookLog.php](file:///c:/xampp/htdocs/neriahpro/app/Models/PaymentWebhookLog.php) | ULID | `HasUlids`, Midtrans dead-letter queue (DLQ) & raw payload audit logging, Retry resilience | - |
| `Document` | [Document.php](file:///c:/xampp/htdocs/neriahpro/app/Models/Document.php) | ULID | `HasUlids`, Scope Lock, Digital signature, SHA-256 hash, Midtrans DP 50% | `related` (morphTo) |
| `DomainHostingAsset` | [DomainHostingAsset.php](file:///c:/xampp/htdocs/neriahpro/app/Models/DomainHostingAsset.php) | ULID | `HasUlids`, Domain & hosting subscription tracking, Expiration alerts, Quick renewal | `visionBlueprint` (belongsTo) |
| `CmsPage` | [CmsPage.php](file:///c:/xampp/htdocs/neriahpro/app/Models/CmsPage.php) | ULID | `HasUlids`, Dynamic landing pages, Multilingual title/meta (`id`/`en`), React Islands, Cache forever | - |
| `CmsGlobalSetting` | [CmsGlobalSetting.php](file:///c:/xampp/htdocs/neriahpro/app/Models/CmsGlobalSetting.php) | ULID | `HasUlids`, Key-value global configuration, Forever cached, Auto-reset on save | - |
| `Product` | [Product.php](file:///c:/xampp/htdocs/neriahpro/app/Models/Product.php) | ULID | `HasUlids`, Multilingual catalog (`id`/`en`), Dual-currency (`price_idr`, `price_usd`) | - |
| `Transaction` | [Transaction.php](file:///c:/xampp/htdocs/neriahpro/app/Models/Transaction.php) | ULID | `HasUlids`, Midtrans Snap gateway integration, Settlement audit | `user` (belongsTo), `product` (belongsTo) |
| `LegalPolicy` | [LegalPolicy.php](file:///c:/xampp/htdocs/neriahpro/app/Models/LegalPolicy.php) | ULID | `HasUlids`, Multilingual legal policies (`title`, `content` array) | - |
| `ClientOnboarding` | [ClientOnboarding.php](file:///c:/xampp/htdocs/neriahpro/app/Models/ClientOnboarding.php) | ULID | `HasUlids`, Rapid lead intake & onboarding payload | - |
| `SecurityThreatLog` | [SecurityThreatLog.php](file:///c:/xampp/htdocs/neriahpro/app/Models/SecurityThreatLog.php) | ULID | `HasUlids`, RCE/Deserialization interception, Forensic threat log, IP auto-quarantine | - |
| `User` | [User.php](file:///c:/xampp/htdocs/neriahpro/app/Models/User.php) | ULID | `HasUlids`, `HasRoles`, Spatie Shield RBAC, FilamentUser access control | `transactions` (hasMany), `resumes` (hasMany) |
| `Role` | [Role.php](file:///c:/xampp/htdocs/neriahpro/app/Models/Role.php) | Default | Spatie Permission Role entity | `permissions`, `users` |
| `Permission` | [Permission.php](file:///c:/xampp/htdocs/neriahpro/app/Models/Permission.php) | Default | Spatie Permission Permission entity | `roles`, `users` |

---

## 3. Katalog Filament v5 Resources (14 Resources)

| Resource | Navigation Group | Fitur Utama | Schema / Tables |
| :--- | :--- | :--- | :--- |
| `LeadContactResource` | Marketing & Klien | Database CRM Leads, Perusahaan, Kontak, Custom Metadata, Kirim Email Langsung | `LeadContactForm`, `LeadContactsTable` |
| `EmailCampaignResource` | Marketing & Klien | Promosi email dan blast penawaran, Sender dinamis, Reply-to Gmail routing | `EmailCampaignForm`, `EmailCampaignsTable` |
| `CvProPlanResource` | Career & CV Pro | Katalog paket langganan & top-up kuota ala carte, penetapan harga dinamis, kalkulator margin keuntungan AI | `CvProPlanForm`, `CvProPlansTable` |
| `ResumeResource` | Career & CV Pro | Resume management, ATS score breakdown, Skills tags, Live view link | `ResumeForm`, `ResumesTable` |
| `InterviewSessionResource` | Career & CV Pro | Mock interview recordings, STAR analysis, Confidence score, Transcript audit | `InterviewSessionsTable`, Infolist |
| `SecurityThreatResource` | System & Security | AI-Shield threat interception dashboard, RCE monitoring, IP quarantine & unblock | `SecurityThreatsTable`, Infolist, `SecurityThreatStatsWidget` |
| `VisionBlueprintResource` | Project Management | Discovery questionnaire, Sintesis PRD, Publikasi URL publik, Ikat Kontrak Digital | `VisionBlueprintForm`, `VisionBlueprintsTable` |
| `BlueprintVoucherResource` | Project Management | Voucher kode promo & pelayanan gratis bypass 100% (Rp 0), strict RBAC khusus Yoseph | `BlueprintVoucherForm`, `BlueprintVouchersTable` |
| `DomainHostingAssetResource` | Project Management | Pencatatan domain/hosting, Expiration badge, Auto-renew, Widget analitik, Pengingat harian | `DomainHostingAssetForm`, `DomainHostingAssetsTable`, `DomainHostingStatsWidget` |
| `DocumentResource` | Contracts & Legal | Digital contract viewer, Scope lock status, Midtrans order ID, Signature pad | `DocumentForm`, `DocumentsTable` |
| `CmsPageResource` | Content Management | Builder React Islands (Hero, Grid, Onboarding, HTML), Copy settings, Multilingual KeyValue | Inline Schema & Table |
| `ProductResource` | Commerce & Billing | Layanan digital, Dual-currency input, Fitur list, Infolist preview | `ProductForm`, `ProductsTable`, `ProductInfolist` |
| `TransactionResource` | Commerce & Billing | Midtrans status settlement, Total IDR, Payment timestamp | `TransactionsTable`, `TransactionInfolist` |
| `LegalPolicyResource` | Contracts & Legal | Syarat ketentuan, Kebijakan privasi multibahasa | `LegalPolicyForm`, `LegalPoliciesTable` |

> 🛡️ **Role & Scope Isolation (Midtrans Merchant Compliance & Web Developer Contracting OS)**:
> - **Super Admin (`yoseph.iriandi.tambunan@gmail.com`)**: Akses $100\%$ tanpa batas ke seluruh Resource, Spatie Shield RBAC, dan Global Settings.
> - **Midtrans Reviewer (`reviewer.midtrans@neriahpro.com` / `midtrans_reviewer`)**: Diisolasi secara ketat dan aman melalui trait `AuditableByMidtransReviewer` (Read-Only) untuk mengaudit seluruh siklus transaksi web developer dengan klien:
>   - **Project OS**: `VisionBlueprintResource` (Spesifikasi PRD/ERD), `DocumentResource` (Surat Kontrak Kerja Sama Digital SPK & Scope Lock), `DomainHostingAssetResource` (Aset Domain & Server VPS klien).
>   - **Commerce & Billing**: `TransactionResource` (Mutasi pembayaran Down Payment 50% via Midtrans Snap, status settlement, order ID), `ProductResource` (Katalog paket layanan web development resmi & pricing IDR).
>   - **Contracts & Legal**: `LegalPolicyResource` (Syarat & Ketentuan kontrak kerja sama, Kebijakan Privasi, dan Kebijakan Refund).
>   - **Marketing & Klien**: `LeadContactResource` (Database CRM prospek klien yang masuk dari form onboarding).
>   - *Proteksi Read-Only*: Reviewer tidak dapat membuat record baru atau menghapus data live produksi (`canCreate`, `canEdit`, `canDelete`, `canDeleteAny` = false).
>   - *Modul Terisolasi*: Modul internal/sekunder (`CvProPlanResource`, `ResumeResource`, `InterviewSessionResource`, `EmailCampaignResource`, `SecurityThreatResource`, `CmsPageResource`, `ManageSettings`, Shield RBAC) disembunyikan sepenuhnya dari reviewer.
> - **Dashboard Widgets**: `ProjectOsComplianceWidget` (6 pilar arsitektur Project OS & verifikasi Midtrans) serta `FrontpageQuickLaunchWidget` (portal cepat frontpage ke `/`, `/blueprint`, `/cv-pro`, `/pricing`, `/cart`, onboarding lead, dan session info) menggantikan default Account & Info widgets Filament.

---

## 4. Routing & Endpoints Map

- `/cv-pro`: Full-stack interactive Studio CV Pro SaaS (`CvProStudioIsland` with Editor, Job Hub Kanban, Keuangan Pro, ATS Audit, Mock Interview, LinkedIn Suite, Real-time Voice Copilot, Web Portfolio Generator).
- `/cv/{slug}`: Public ATS printable resume preview & print view (`CvProController::show`).
- `/pricing`: Halaman publik kelas harga CV Pro, paket langganan (Starter, Pro Career, Ultimate Executive, VIP Sprint), top-up a la carte, rincian biaya API, dan FAQ interaktif (`CvPricingIsland`).
- `/api/cv-pro/save`: Auto-save & sync resume state (POST).
- `/api/cv-pro/lint`: ATS quality auditor & metric detector (POST).
- `/api/cv-pro/tailor`: Tailor CV specifically to target Job Description (POST).
- `/api/cv-pro/linkedin`: LinkedIn Personal Branding Suite (Headlines, About, Skills, Post ideas) (POST).
- `/api/cv-pro/realtime-copilot`: Asisten Wawancara Real-Time (Live Voice Copilot) contekkan STAR & kata kunci emas (POST).
- `/api/cv-pro/portfolio/generate`: AI Web Portfolio Generator instan (HTML responsive website preview & download) (POST).
- `/api/cv-pro/ai-helper`: Quick AI Helper for summary, bullet enhancer/condenser, and skill suggest (POST).
- `/api/cv-pro/pricing`: Dynamic pricing plans, a la carte top-ups, and financial margin economics (GET).
- `/api/cv-pro/quota`: Real-time user quota balance & entitlements (GET).
- `/api/cv-pro/topup`: Top-up quota a la carte atau aktivasi paket (POST).
- `/api/cv-pro/interview/generate`: AI mock interview question generator (POST).
- `/api/cv-pro/interview/evaluate`: STAR method answer evaluation & scoring (POST).
- `/api/cv-pro/outreach/generate`: Job application letter generator (Thank You, Follow-up, Cold Pitch) (POST).
- `/api/cv-pro/upload-cv`: Microsoft MarkItDown multi-format CV scanner & parser (POST).
- `/blueprint`: Halaman public kuesioner Project OS (`BlueprintController::create`).
- `/api/blueprint/analyze-idea`: Endpoint POST sintesis ide awal & ekstraksi MarkItDown (`BlueprintController::analyzeIdea`).
- `/api/blueprint/supplement-idea`: Endpoint POST asisten AI proaktif untuk membedah dan menempatkan ide tambahan klien (`BlueprintController::supplementIdea`).
- `/blueprint/{slug}`: Halaman preview dokumen PRD, ERD, dan Tech Stack (`BlueprintController::show`).
- `/blueprint/{slug}/raw-md`: Endpoint raw Markdown PRD Ultimate untuk 1-click clipboard prompt AI Code Agent (`BlueprintController::rawMd`).
- `/blueprint/{slug}/download/md`: Endpoint unduh dokumen spesifikasi PRD Ultimate format Markdown (`BlueprintController::downloadMd`).
- `/blueprint/{slug}/export/scaffold`: Endpoint ekspor 1-click arsip zip berisikan docker-compose.yml, schema_complete.sql (strict ULID), dan struktur routing Laravel 13 / Next.js (`BlueprintController::exportScaffold`).
- `/blueprint/{slug}/scaffold/preview`: Endpoint AJAX JSON preview source code berkas scaffold (`BlueprintController::previewScaffold`).
- `/api/blueprint/{slug}/presence`: Endpoint POST & GET sinkronisasi kehadiran kolaborator real-time (Lead Architect & Klien) dan koordinat kursor langsung (`BlueprintController::updatePresence`, `BlueprintController::getPresence`).
- `/blueprint/{slug}/download/pdf`: Endpoint unduh dokumen PRD format PDF (`BlueprintController::downloadPdf`).
- `/blueprint/{slug}/snap-token`: Endpoint AJAX pembuatan Midtrans Snap Token untuk Blueprint DP dengan kalkulasi diskon voucher otomatis (`BlueprintController::getSnapToken`).
- `/blueprint/{slug}/voucher/validate`: Validasi kode voucher promo / subsidi (`BlueprintController::validateVoucher`).
- `/blueprint/{slug}/voucher/claim`: Klaim voucher 100% Free Grant bypass (Rp 0) dengan proteksi penolakan partial vouchers (`BlueprintController::claimVoucher`).
- `/cart`: Halaman Cart pembayaran & penguncian kontrak proyek dengan Anti-Ghost Hold 24 jam countdown & diskon voucher terintegrasi (`CartController::index`).
- `/cart/voucher/apply`: Terapkan kode voucher promo / subsidi ke keranjang belanja (`CartController::applyVoucher`).
- `/cart/voucher/remove`: Hapus kode voucher aktif dari keranjang belanja (`CartController::removeVoucher`).
- `/cart/snap-token`: Endpoint AJAX pembuatan Midtrans Snap Token untuk Cart DP checkout multi-item dengan kalkulasi subsidi voucher (`CartController::getSnapToken`).
- `/document/{document}/preview`: Preview draft kontrak kerja sama digital.
- `/document/{document}/sign`: Livewire signing page (`DocumentSignature`, Dual-Locale `id`/`en`, Dark/Light Mode, Printer-ready formatting, Dynamic Project OS clauses via `ContractLegalHelper`).
- `/lang/{locale}`: Switcher bahasa (`id` / `en`) dengan persistensi session dan cookie.
- `/admin`: Panel admin Filament v5 dengan database notifications (PostgreSQL `jsonb` schema).
- `/admin/login`: Customized Enterprise Login Portal (`App\Filament\Pages\Auth\Login`) dengan Vision & Mission Pillars, 1-Click Demo Credential Assistant, dan System Telemetry (Split-Screen Desktop & Responsive Portrait).
- `/admin/settings`: Pengaturan Global (`ManageSettings.php`) dengan tab General, Multi-Language (2-Tier Locale: Native ID/EN & Google Translate Whitelist), Frontend Feature Flags (toggle saklar on/off untuk CV Pro, Job Hub, Keuangan Pro, Mock Interview, LinkedIn Suite, Vision Blueprint, dsb), Navigation & Footer, dan SEO Schema Markup.
- `/{slug?}`: Fallback dinamis CMS page (`PageController::show`).
- `api/vision-blueprint`: Endpoint POST penyimpanan form Project OS dengan Honeypot anti-spam (`throttle:30,1`), normalisasi nomor WhatsApp internasional dengan selector kode negara dari `config/countries.php`, dan filter anti-awalan 0.

---

## 5. Layanan Inti & Background Scheduler

- **`App\Services\ScaffoldGeneratorService`**:
  Mesin sintesis boilerplate dan scaffold kode dari dokumen PRD/ERD: menghasilkan file `docker-compose.yml` (PHP 8.4, PostgreSQL 16, Redis 7, Nginx, Mailpit), `.env.example`, migrasi SQL `schema_complete.sql` lengkap dengan skema strict ULID (`VARCHAR(26)`), spesifikasi REST API OpenAPI 3.0 (`openapi.json` siap impor ke Postman/Swagger), routing web & API Laravel 13, serta Next.js App Router API route (`route.ts`). Mengemasnya ke dalam file `.zip` sekali klik via `ZipArchive`.
- **`App\Services\PrdGeneratorService`**:
  Mesin sintesis PRD Ultimate & Technical Architecture: mendekomposisi kebutuhan bisnis menjadi vertical slices terstruktur (Frontend Anti-AI-Slop, Backend Keyset O(1) & ULID, API Contracts, dan AI Code Agent Prompt Directives), visualisasi diagram alur kerja Mermaid Flowchart, skema relasional Mermaid ERD PostgreSQL, evaluasi infrastruktur (Hosting Ladder & Scale Matrix), Architecture & Security Compliance Health Auditor (Score 100/100), Simulator Interaktif Biaya Server VPS & SLA Token AI, serta generator dokumen Markdown (.md) komprehensif.
- **`App\Services\MidtransSnapService`**:
  Layanan integrasi Midtrans Snap API: memproses pembuatan Snap Token transaksi secara aman via HTTP Basic Auth ke endpoint Midtrans Sandbox/Production, mendukung modal prompt interaktif in-page (Snap popup `window.snap.pay`) tanpa redirect 404, serta menangani audit log kegagalan gateway.
- **`App\Services\CvPro\CvAiService`**:
  Mesin kecerdasan karir CV Pro SaaS: linter ATS CV dengan deteksi kata kerja lemah dan metrik kuantitatif, generator penyesuaian CV presisi terhadap lowongan (Applied CV Generator & Diff), generator LinkedIn personal branding pack, AI bullet enhancer/condenser, generator pertanyaan mock interview strategis berdasarkan posisi/target, evaluator jawaban kandidat berbasis formula STAR (Situation, Task, Action, Result), serta generator surat korespondensi pasca-wawancara.
- **`App\Services\CvPro\CvPricingService`**:
  Mesin kalkulator biaya API AI dan margin keuntungan dinamis: memproyeksikan biaya unit LLM (OpenAI GPT-4o-mini & Google Gemini 2.0 Flash) dan audio Whisper per aksi pengguna, menghitung COGS (Cost of Goods Sold) maksimal dan realistis per paket langganan dan top-up, serta memvalidasi margin keuntungan admin (>90%) untuk menjamin admin tidak pernah merugi/nombok.
- **`App\Services\CvPro\CvQuotaService`**:
  Mesin manajemen kuota pengguna dan hak akses fitur (entitlements): inisialisasi kuota gratis awal, pengecekan ketersediaan kuota fitur sebelum eksekusi AI, konsumsi saldo kuota/kredit dengan fallback transaksional, aktivasi paket langganan berjangka waktu, penerapan paket top-up kuota a la carte permanen, serta pencatatan audit log di `cv_quota_transactions`.
- **`App\Services\PrdGeneratorService`**:
  Mesin pengolah ide kuesioner klien menjadi Ultimate PRD: menyusun ringkasan eksekutif, aktor sistem (RBAC), fitur MVP Fase 1, roadmap Fase 2, alur kerja (workflow), dan skema basis data ERD PostgreSQL Strict ULID.
- **`App\Console\Commands\CheckExpiringAssetsCommand`** (`assets:check-expirations`):
  Memindai aset domain dan hosting yang mendekati batas tenggat (<= 30 hari) dan mengirimkan notifikasi peringatan database ke seluruh superadmin. Terjadwal di `routes/console.php` pukul 08:00 WIB harian.
- **`App\Http\Middleware\AiThreatShield`**:
  Internal firewall pendeteksi anomali serangan otonom AI & RCE (terinspirasi mitigasi insiden Exploit Gym & Hugging Face dataset loader): mencegat system calls (`system`, `exec`, `shell_exec`, `eval`, `proc_open`), Python dataset loader remote code execution (`datasets.load_dataset`, `trust_remote_code=True`, `pickle.loads`, `torch.load`), unsafe object deserialization (`__construct`, `__wakeup`, `O:\d+:`), sensitive reconnaissance probing (`.env`, `.git`, `/actuator`, `/phpmyadmin`), SSTI injection, serta burst velocity probing (>35 req/5s). Otomatis mengisolasi IP penyerang ke database `security_threat_logs` dan cache blacklist 24 jam dengan tampilan HTML Cyber Defense responsif.
- **`App\Jobs\ProcessSecureDataset`**:
  Sandboxed job pipeline dan synchronous inspector untuk ingesti data (CSV, JSON, XML, SVG, TXT, PDF, Office): inspeksi in-memory stream-safe bebas file lock Windows, validasi MIME type absolut via `finfo_buffer` (kebal spoofing ekstensi), pemblokiran skrip berbahaya sebelum pemrosesan MarkItDown, pencegahan XXE via penonaktifan external entity loader (`LIBXML_NONET`), serta netralisasi CSV formula injection.
- **`App\Support\FilamentRichEditor`**:
  Standar terpusat Rich Text Editor untuk Filament v5: memunculkan seluruh fitur toolbar lengkap (H1-H6, formatting, lists, tables, links, code, attachments) dengan penegakan struktur folder penyimpanan dangkal/shallow (maksimal 1-2 tingkat kedalaman) guna mengoptimalkan inode Linux dan mencegah lonjakan konsumsi RAM server saat scanning direktori.
- **`App\Support\FilamentCuratorHelper`**:
  Standar komponen input media gambar di Filament v5: mengintegrasikan Curator Media Picker (`\Awcodes\Curator\Components\Forms\CuratorPicker`) dan FileUpload fallback dengan penegakan path folder dangkal untuk menjamin integritas server OS.
- **`App\Services\BlueprintDiscoveryService` & `ProjectBlueprintIsland` (Textarea Studio Mode)**:
  Antarmuka Studio Project OS berbasis input textarea interaktif yang mendukung konversi instan multi-dokumen (PDF, DOCX, TXT, MD, CSV, XLSX, PPTX, PNG, JPG) menjadi format Markdown (.md) via Microsoft MarkItDown (`MarkItDownService`) guna menghemat >80% konsumsi token AI LLM. Dilengkapi suite anti-spam multi-lapis (decoy honeypots `_hp_check` & `_website`, deteksi repetisi karakter, dan IP throttling).
- **`App\Models\CmsGlobalSetting` & `App\Http\Controllers\PageController` (Cache Incomplete Class Resiliency)**:
  Sistem penanganan cache CMS dan global configuration yang 100% kebal terhadap error `__PHP_Incomplete_Class`: `CmsGlobalSetting::getAllCached()` dan `PageController` hanya menyimpan raw array atribut primitif (`cms_global_settings_data` & `cms_page_data_{slug}`) alih-alih serialisasi objek/koleksi Eloquent mentah. Dilengkapi self-healing otomatis di level model, controller, dan Blade view (`page.blade.php`, `cv-pro/index.blade.php`) yang mendeteksi serta me-reset cache secara transparan jika terdeteksi data korup/legacy.
- **`App\Http\Middleware\SetAppLocale`**:
  Mendeteksi dan menetapkan bahasa aktif (`id` / `en`) secara transparan dari query string, sesi, atau cookie `neriah_locale`.
