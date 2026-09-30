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

## 2. Katalog Domain & Entity Model (15 Models)

Seluruh model domain bisnis menggunakan ULID (`HasUlids`) string 26-karakter untuk menjamin skalabilitas enterprise dan integritas PostgreSQL:

| Model | Lokasi File | Primary Key | Traits / Fitur Utama | Relasi Utama |
| :--- | :--- | :---: | :--- | :--- |
| `Resume` | [Resume.php](file:///c:/xampp/htdocs/neriahpro/app/Models/Resume.php) | ULID | `HasUlids`, Multi-template ATS CV, Score audit, Experience/Edu JSON | `user` (belongsTo), `interviewSessions` (hasMany), `outreachLetters` (hasMany) |
| `InterviewSession` | [InterviewSession.php](file:///c:/xampp/htdocs/neriahpro/app/Models/InterviewSession.php) | ULID | `HasUlids`, Mock interview Q&A, Voice audio transcription, STAR score evaluation | `resume` (belongsTo), `user` (belongsTo) |
| `OutreachLetter` | [OutreachLetter.php](file:///c:/xampp/htdocs/neriahpro/app/Models/OutreachLetter.php) | ULID | `HasUlids`, Thank you / follow-up / cold pitch letter generator | `resume` (belongsTo), `user` (belongsTo) |
| `VisionBlueprint` | [VisionBlueprint.php](file:///c:/xampp/htdocs/neriahpro/app/Models/VisionBlueprint.php) | ULID | `HasUlids`, Project OS discovery questionnaire, PRD synthesis, Contract converter | `documents` (morphMany), `domainHostingAssets` (hasMany) |
| `Document` | [Document.php](file:///c:/xampp/htdocs/neriahpro/app/Models/Document.php) | ULID | `HasUlids`, Scope Lock, Digital signature, SHA-256 hash, Midtrans DP 50% | `related` (morphTo) |
| `DomainHostingAsset` | [DomainHostingAsset.php](file:///c:/xampp/htdocs/neriahpro/app/Models/DomainHostingAsset.php) | ULID | `HasUlids`, Domain & hosting subscription tracking, Expiration alerts, Quick renewal | `visionBlueprint` (belongsTo) |
| `CmsPage` | [CmsPage.php](file:///c:/xampp/htdocs/neriahpro/app/Models/CmsPage.php) | ULID | `HasUlids`, Dynamic landing pages, Multilingual title/meta (`id`/`en`), React Islands | - |
| `CmsGlobalSetting` | [CmsGlobalSetting.php](file:///c:/xampp/htdocs/neriahpro/app/Models/CmsGlobalSetting.php) | ULID | `HasUlids`, Key-value global configuration, Forever cached | - |
| `Product` | [Product.php](file:///c:/xampp/htdocs/neriahpro/app/Models/Product.php) | ULID | `HasUlids`, Multilingual catalog (`id`/`en`), Dual-currency (`price_idr`, `price_usd`) | - |
| `Transaction` | [Transaction.php](file:///c:/xampp/htdocs/neriahpro/app/Models/Transaction.php) | ULID | `HasUlids`, Midtrans Snap gateway integration, Settlement audit | `user` (belongsTo), `product` (belongsTo) |
| `LegalPolicy` | [LegalPolicy.php](file:///c:/xampp/htdocs/neriahpro/app/Models/LegalPolicy.php) | ULID | `HasUlids`, Multilingual legal policies (`title`, `content` array) | - |
| `ClientOnboarding` | [ClientOnboarding.php](file:///c:/xampp/htdocs/neriahpro/app/Models/ClientOnboarding.php) | ULID | `HasUlids`, Rapid lead intake & onboarding payload | - |
| `SecurityThreatLog` | [SecurityThreatLog.php](file:///c:/xampp/htdocs/neriahpro/app/Models/SecurityThreatLog.php) | ULID | `HasUlids`, RCE/Deserialization interception, Forensic threat log, IP auto-quarantine | - |
| `User` | [User.php](file:///c:/xampp/htdocs/neriahpro/app/Models/User.php) | ULID | `HasUlids`, `HasRoles`, Spatie Shield RBAC, FilamentUser access control | `transactions` (hasMany), `resumes` (hasMany) |
| `Role` | [Role.php](file:///c:/xampp/htdocs/neriahpro/app/Models/Role.php) | Default | Spatie Permission Role entity | `permissions`, `users` |
| `Permission` | [Permission.php](file:///c:/xampp/htdocs/neriahpro/app/Models/Permission.php) | Default | Spatie Permission Permission entity | `roles`, `users` |

---

## 3. Katalog Filament v5 Resources (10 Resources)

| Resource | Navigation Group | Fitur Utama | Schema / Tables |
| :--- | :--- | :--- | :--- |
| `ResumeResource` | Career & CV Pro | Resume management, ATS score breakdown, Skills tags, Live view link | `ResumeForm`, `ResumesTable` |
| `InterviewSessionResource` | Career & CV Pro | Mock interview recordings, STAR analysis, Confidence score, Transcript audit | `InterviewSessionsTable`, Infolist |
| `SecurityThreatResource` | System & Security | AI-Shield threat interception dashboard, RCE monitoring, IP quarantine & unblock | `SecurityThreatsTable`, Infolist, `SecurityThreatStatsWidget` |
| `VisionBlueprintResource` | Project Management | Discovery questionnaire, Sintesis PRD, Publikasi URL publik, Ikat Kontrak Digital | `VisionBlueprintForm`, `VisionBlueprintsTable` |
| `DomainHostingAssetResource` | Project Management | Pencatatan domain/hosting, Expiration badge, Auto-renew, Widget analitik, Pengingat harian | `DomainHostingAssetForm`, `DomainHostingAssetsTable`, `DomainHostingStatsWidget` |
| `DocumentResource` | Contracts & Legal | Digital contract viewer, Scope lock status, Midtrans order ID, Signature pad | `DocumentForm`, `DocumentsTable` |
| `CmsPageResource` | Content Management | Builder React Islands (Hero, Grid, Onboarding, HTML), Copy settings, Multilingual KeyValue | Inline Schema & Table |
| `ProductResource` | Commerce & Billing | Layanan digital, Dual-currency input, Fitur list, Infolist preview | `ProductForm`, `ProductsTable`, `ProductInfolist` |
| `TransactionResource` | Commerce & Billing | Midtrans status settlement, Total IDR, Payment timestamp | `TransactionsTable`, `TransactionInfolist` |
| `LegalPolicyResource` | Contracts & Legal | Syarat ketentuan, Kebijakan privasi multibahasa | `LegalPolicyForm`, `LegalPoliciesTable` |

---

## 4. Routing & Endpoints Map

- `/cv-pro`: Full-stack interactive Studio CV Pro SaaS (`CvProStudioIsland` with Editor, Job Hub Kanban, Keuangan Pro, ATS Audit, Mock Interview, LinkedIn Suite).
- `/cv/{slug}`: Public ATS printable resume preview & print view (`CvProController::show`).
- `/api/cv-pro/save`: Auto-save & sync resume state (POST).
- `/api/cv-pro/lint`: ATS quality auditor & metric detector (POST).
- `/api/cv-pro/tailor`: Tailor CV specifically to target Job Description (POST).
- `/api/cv-pro/linkedin`: LinkedIn Personal Branding Suite (Headlines, About, Skills, Post ideas) (POST).
- `/api/cv-pro/ai-helper`: Quick AI Helper for summary, bullet enhancer/condenser, and skill suggest (POST).
- `/api/cv-pro/interview/generate`: AI mock interview question generator (POST).
- `/api/cv-pro/interview/evaluate`: STAR method answer evaluation & scoring (POST).
- `/api/cv-pro/outreach/generate`: Job application letter generator (Thank You, Follow-up, Cold Pitch) (POST).
- `/api/cv-pro/upload-cv`: Microsoft MarkItDown multi-format CV scanner & parser (POST).
- `/blueprint`: Halaman public kuesioner Project OS (`BlueprintController::create`).
- `/blueprint/{slug}`: Halaman preview dokumen PRD, ERD, dan Tech Stack (`BlueprintController::show`).
- `/document/{document}/preview`: Preview draft kontrak kerja sama digital.
- `/document/{document}/sign`: Livewire signing page (`DocumentSignature`).
- `/lang/{locale}`: Switcher bahasa (`id` / `en`) dengan persistensi session dan cookie.
- `/admin`: Panel admin Filament v5 dengan database notifications.
- `/{slug?}`: Fallback dinamis CMS page (`PageController::show`).
- `api/vision-blueprint`: Endpoint POST penyimpanan form Project OS dengan Honeypot anti-spam (`throttle:30,1`).

---

## 5. Layanan Inti & Background Scheduler

- **`App\Services\CvPro\CvAiService`**:
  Mesin kecerdasan karir CV Pro SaaS: linter ATS CV dengan deteksi kata kerja lemah dan metrik kuantitatif, generator penyesuaian CV presisi terhadap lowongan (Applied CV Generator & Diff), generator LinkedIn personal branding pack, AI bullet enhancer/condenser, generator pertanyaan mock interview strategis berdasarkan posisi/target, evaluator jawaban kandidat berbasis formula STAR (Situation, Task, Action, Result), serta generator surat korespondensi pasca-wawancara.
- **`App\Services\PrdGeneratorService`**:
  Mesin pengolah ide kuesioner klien menjadi Ultimate PRD: menyusun ringkasan eksekutif, aktor sistem (RBAC), fitur MVP Fase 1, roadmap Fase 2, alur kerja (workflow), dan skema basis data ERD PostgreSQL Strict ULID.
- **`App\Console\Commands\CheckExpiringAssetsCommand`** (`assets:check-expirations`):
  Memindai aset domain dan hosting yang mendekati batas tenggat (<= 30 hari) dan mengirimkan notifikasi peringatan database ke seluruh superadmin. Terjadwal di `routes/console.php` pukul 08:00 WIB harian.
- **`App\Http\Middleware\AiThreatShield`**:
  Internal firewall pendeteksi anomali serangan otonom AI & RCE: mencegat system calls (`exec`, `shell_exec`, `eval`), unsafe object deserialization (`__construct`, `__wakeup`), eksekusi Python dataset loaders, dan otomatis mengisolasi IP penyerang ke database & cache blacklist.
- **`App\Jobs\ProcessSecureDataset`**:
  Sandboxed job pipeline untuk ingesti data (CSV, JSON, XML): validasi MIME type absolut bebas spoofing, pencegahan XXE via penonaktifan external entity loader, serta netralisasi CSV formula injection.
- **`App\Http\Middleware\SetAppLocale`**:
  Mendeteksi dan menetapkan bahasa aktif (`id` / `en`) secara transparan dari query string, sesi, atau cookie `neriah_locale`.
