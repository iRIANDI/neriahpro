<?php

namespace App\Support;

use App\Models\PosPrimitive;
use App\Models\PosSector;
use App\Models\PosStackPreset;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class PosStackPresetCatalog
{
    public const DEFAULT_PRESET_CODE = 'STACK_M1_LARAVEL_MONOLITH';

    /**
     * Backward-compatible alias mapping for legacy stack_preset values.
     */
    public static function normalizePresetCode(?string $code): string
    {
        $clean = strtoupper(trim((string) $code));
        if ($clean === '' || in_array($clean, [
            'LARAVEL13_FILAMENT5_LIVEWIRE4_FLUX_PGSQL16_RAILPACK',
            'ANTI_SLOP_ENTERPRISE_V1',
            'M1',
            'DEFAULT',
        ], true)) {
            return self::DEFAULT_PRESET_CODE;
        }

        $shortMap = [
            'M1' => 'STACK_M1_LARAVEL_MONOLITH',
            'M2' => 'STACK_M2_LARAVEL_INERTIA_REACT',
            'M3' => 'STACK_M3_NEXTJS_TRPC_DRIZZLE',
            'M4' => 'STACK_M4_DJANGO_NINJA_HTMX',
            'D1' => 'STACK_D1_LARAVEL_HEADLESS_OPENAPI',
            'D2' => 'STACK_D2_GO_SQLC_NEXTJS',
            'D3' => 'STACK_D3_FASTAPI_SQLALCHEMY_REACT',
            'S1' => 'STACK_S1_POLYGLOT_GO_LARAVEL_TEMPORAL',
            'S2' => 'STACK_S2_TS_TURBOREPO_NATS',
        ];

        return $shortMap[$clean] ?? $clean;
    }

    /**
     * Idempotently seed all 9 Stack Presets and enrich all 12 Core Primitives with the 4 Ultimate JSONB columns.
     */
    public static function seedDatabase(): void
    {
        if (Schema::hasTable('pos_stack_presets')) {
            foreach (self::stackPresets() as $presetData) {
                PosStackPreset::query()->updateOrCreate(
                    ['code' => $presetData['code']],
                    $presetData
                );
            }
        }

        if (Schema::hasTable('pos_primitives') && Schema::hasColumn('pos_primitives', 'deterministic_formulas')) {
            $ultimateMap = self::primitiveUltimateContracts();
            foreach ($ultimateMap as $primitiveCode => $contracts) {
                PosPrimitive::query()
                    ->where('code', $primitiveCode)
                    ->update([
                        'deterministic_formulas' => json_encode($contracts['deterministic_formulas'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        'saga_and_webhook_contracts' => json_encode($contracts['saga_and_webhook_contracts'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        'ui_screen_archetypes' => json_encode($contracts['ui_screen_archetypes'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        'acceptance_test_scenarios' => json_encode($contracts['acceptance_test_scenarios'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    ]);
            }
        }

        Cache::forget(PosSector::CACHE_KEY);
    }

    /**
     * Quick Architecture Comparison Matrix for Frontend Studio & Admin UI.
     */
    public static function comparisonMatrix(): array
    {
        return [
            [
                'short_code' => 'M1',
                'code' => 'STACK_M1_LARAVEL_MONOLITH',
                'topology' => 'MONOLITH',
                'architecture_label' => 'Modular Monolith',
                'primary_language' => 'PHP 8.4/8.5',
                'inter_layer_contract' => 'Native Livewire 4 + Invokable Domain Action Classes',
                'best_use_case' => ['id' => 'SaaS B2B Cepat, ERP Internal, Backoffice Multi-Tenant', 'en' => 'Rapid B2B SaaS, Internal ERP, Multi-Tenant Backoffice'],
            ],
            [
                'short_code' => 'M2',
                'code' => 'STACK_M2_LARAVEL_INERTIA_REACT',
                'topology' => 'MONOLITH',
                'architecture_label' => 'Reactive Monolith',
                'primary_language' => 'PHP + TypeScript',
                'inter_layer_contract' => 'Spatie Laravel Data -> Inertia v2 TypeScript (.d.ts)',
                'best_use_case' => ['id' => 'SaaS dengan UI Interaktif Kompleks (POS, Kanban, Canvas)', 'en' => 'SaaS with Complex Interactive UI (POS, Kanban, Canvas)'],
            ],
            [
                'short_code' => 'M3',
                'code' => 'STACK_M3_NEXTJS_TRPC_DRIZZLE',
                'topology' => 'MONOLITH',
                'architecture_label' => 'Unified TS Monolith',
                'primary_language' => 'TypeScript',
                'inter_layer_contract' => 'Drizzle ORM + Shared Zod Schema + tRPC / Server Actions',
                'best_use_case' => ['id' => 'Startup AI/Web Modern 1 Bahasa (Zero-Prisma Overhead)', 'en' => 'Single-Language Modern Web/AI Startup (Zero-Prisma Overhead)'],
            ],
            [
                'short_code' => 'M4',
                'code' => 'STACK_M4_DJANGO_NINJA_HTMX',
                'topology' => 'MONOLITH',
                'architecture_label' => 'Python AI Monolith',
                'primary_language' => 'Python 3.12+',
                'inter_layer_contract' => 'Django Ninja Pydantic v2 + HTMX Partial Contracts',
                'best_use_case' => ['id' => 'Aplikasi Machine Learning, Computer Vision & Data Science Berat', 'en' => 'Heavy Machine Learning, Computer Vision & Data Science Apps'],
            ],
            [
                'short_code' => 'D1',
                'code' => 'STACK_D1_LARAVEL_HEADLESS_OPENAPI',
                'topology' => 'DECOUPLED_HEADLESS',
                'architecture_label' => 'Decoupled Enterprise API',
                'primary_language' => 'PHP + TS / Dart',
                'inter_layer_contract' => 'OpenAPI 3.1 (Scramble -> Orval / HeyAPI Generated SDK)',
                'best_use_case' => ['id' => 'Web Global + Native Mobile App (iOS/Android Expo/Flutter)', 'en' => 'Global Web + Native Mobile App (iOS/Android Expo/Flutter)'],
            ],
            [
                'short_code' => 'D2',
                'code' => 'STACK_D2_GO_SQLC_NEXTJS',
                'topology' => 'DECOUPLED_HEADLESS',
                'architecture_label' => 'High-Perf Go Decoupled',
                'primary_language' => 'Golang 1.24 + TS',
                'inter_layer_contract' => 'sqlc Type-Safe SQL + OpenAPI 3.1 / ConnectRPC',
                'best_use_case' => ['id' => 'Fintech Ledger, Fleet Telemetry, High-Concurrency Ticketing War (~15MB RAM)', 'en' => 'Fintech Ledger, Fleet Telemetry, High-Concurrency Ticketing (~15MB RAM)'],
            ],
            [
                'short_code' => 'D3',
                'code' => 'STACK_D3_FASTAPI_SQLALCHEMY_REACT',
                'topology' => 'DECOUPLED_HEADLESS',
                'architecture_label' => 'Python AI-Native Headless',
                'primary_language' => 'Python + TypeScript',
                'inter_layer_contract' => 'FastAPI Pydantic v2 OpenAPI -> HeyAPI React 19 SDK',
                'best_use_case' => ['id' => 'AI Agentic Platform, RAG Pipeline & Clinical Inference API', 'en' => 'AI Agentic Platform, RAG Pipeline & Clinical Inference API'],
            ],
            [
                'short_code' => 'S1',
                'code' => 'STACK_S1_POLYGLOT_GO_LARAVEL_TEMPORAL',
                'topology' => 'MICROSERVICES',
                'architecture_label' => 'Polyglot Event-Driven Mesh',
                'primary_language' => 'Go + PHP + Python',
                'inter_layer_contract' => 'Protobuf (ConnectRPC/gRPC) + NATS JetStream + Temporal.io Saga',
                'best_use_case' => ['id' => 'Enterprise Perbankan/Kesehatan Multi-DB & Isolasi Regulasi Ketat', 'en' => 'Banking/Healthcare Enterprise Multi-DB & Strict Regulatory Isolation'],
            ],
            [
                'short_code' => 'S2',
                'code' => 'STACK_S2_TS_TURBOREPO_NATS',
                'topology' => 'MICROSERVICES',
                'architecture_label' => 'TS Turborepo Microservices',
                'primary_language' => 'TypeScript (Monorepo)',
                'inter_layer_contract' => '@repo/contracts (Protobuf + Zod) + Hono/NestJS + NATS JetStream',
                'best_use_case' => ['id' => 'Tim Full-TypeScript Multi-Squad dengan Isolasi Database per Domain', 'en' => 'Multi-Squad Full-TypeScript Teams with Isolated Domain Databases'],
            ],
        ];
    }

    /**
     * 9 Curated Tech Stack & Topology Presets (3 Topologies: MONOLITH, DECOUPLED_HEADLESS, MICROSERVICES).
     */
    public static function stackPresets(): array
    {
        return [
            // =====================================================================
            // KATEGORI A: FULL-STACK MODULAR MONOLITH
            // =====================================================================
            [
                'code' => 'STACK_M1_LARAVEL_MONOLITH',
                'short_code' => 'M1',
                'topology' => 'MONOLITH',
                'sort_order' => 1,
                'name' => [
                    'id' => 'M1 — The Artisanal Enterprise (Laravel 13 + Filament v5 + Livewire 4 + Flux UI)',
                    'en' => 'M1 — The Artisanal Enterprise (Laravel 13 + Filament v5 + Livewire 4 + Flux UI)',
                ],
                'primary_language' => 'PHP 8.4/8.5',
                'best_use_cases' => [
                    'id' => '80% SaaS B2B, ERP internal, sistem klinis/ritel multi-cabang, dan peluncuran cepat zero-latency.',
                    'en' => '80% of B2B SaaS, internal ERPs, multi-branch clinical/retail systems, and rapid zero-latency delivery.',
                    'recommended_sectors' => ['SECTOR_01_COMMERCE_OMNICHANNEL', 'SECTOR_02_HOSPITALITY_SPACE', 'SECTOR_03_HEALTHCARE_CLINICAL', 'SECTOR_05_INDUSTRIAL_MRP', 'SECTOR_08_PROFESSIONAL_PSA', 'SECTOR_11_HRIS_INTERNAL_OPS', 'SECTOR_12_GOVTECH_NONPROFIT'],
                ],
                'layer_technologies' => [
                    'backend' => 'Laravel 13 (PHP 8.4/8.5+)',
                    'frontend_public' => 'Livewire 4 + Flux UI + Alpine.js + Tailwind CSS v4 (100% Local Bundle)',
                    'frontend_admin' => 'FilamentPHP v5 (Strict Schema Signature & Width::Full)',
                    'orm_data_access' => 'Eloquent ORM (Strict HasUlids + Keyset cursorPaginate O(1))',
                    'database' => 'PostgreSQL 16 (Strict Mode, pgvector 768-dim HNSW)',
                    'cache_queue' => 'Redis 7 + Laravel Horizon / Database Queue',
                    'api_protocol' => 'Direct In-Process Invokable Domain Actions (External Webhooks via RFC 9457 REST)',
                    'runtime_server' => 'FrankenPHP (Caddyfile) + Railpack (railpack.json)',
                ],
                'directory_structure_contract' => [
                    'root_pattern' => 'Single-Repo Domain-Modular Laravel 13',
                    'required_paths' => [
                        'app/Domains/{Domain}/Actions/*Action.php',
                        'app/Domains/{Domain}/DTOs/*Data.php',
                        'app/Enums/*Status.php',
                        'app/Models/*.php',
                        'app/Filament/Resources/*Resource.php',
                        'app/Livewire/{Domain}/*.php',
                        'database/migrations/*.php',
                        'tests/Feature/*AcceptanceTest.php',
                    ],
                ],
                'inter_layer_type_contract' => [
                    'mechanism' => 'PHP 8.4+ Readonly DTOs + Backed Enums + Invokable Domain Actions',
                    'validation_sharing' => 'FormRequest / Filament Schema -> Typed Action Input DTO',
                    'zero_drift_guarantee' => 'No internal HTTP serialization overhead; Livewire 4 & Filament v5 invoke identical Domain Action classes inside DB::transaction().',
                ],
                'deployment_artifacts' => [
                    'files' => ['railpack.json', 'Caddyfile', 'docker-compose.yml', 'deploy.sh'],
                    'process_manager' => 'FrankenPHP Worker Mode + Queue Worker + Scheduler',
                ],
                'stack_specific_anti_slop_rules' => "1. HARAM membuat internal REST API yang dipanggil sendiri oleh Livewire via Http::get(); panggil Invokable Action di app/Domains/{Domain}/Actions secara langsung.\n2. HARAM memakai Filament\\Forms\\Form di Filament v5; WAJIB gunakan Filament\\Schemas\\Schema.\n3. HARAM memakai \$table->id() atau \$table->uuid('id'); WAJIB \$table->ulid('id')->primary() + HasUlids.\n4. HARAM memakai ->paginate() berbasis OFFSET; WAJIB ->cursorPaginate() dengan fallback ->orderBy('id', 'asc').",
                'detection_keywords' => ['laravel', 'filament', 'livewire', 'flux', 'monolith php'],
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'code' => 'STACK_M2_LARAVEL_INERTIA_REACT',
                'short_code' => 'M2',
                'topology' => 'MONOLITH',
                'sort_order' => 2,
                'name' => [
                    'id' => 'M2 — The Type-Safe Reactive Monolith (Laravel 13 + Inertia v2 + React 19 + Shadcn/UI)',
                    'en' => 'M2 — The Type-Safe Reactive Monolith (Laravel 13 + Inertia v2 + React 19 + Shadcn/UI)',
                ],
                'primary_language' => 'PHP + TypeScript',
                'best_use_cases' => [
                    'id' => 'Aplikasi dengan state UI klien sangat interaktif (Kasir POS Split-Screen, Kanban Drag-Drop, Seat Map) + Backoffice Admin Filament v5.',
                    'en' => 'Highly interactive client-state UIs (Split-Screen POS, Drag-Drop Kanban, Seat Maps) + Filament v5 Backoffice.',
                    'recommended_sectors' => ['SECTOR_01_COMMERCE_OMNICHANNEL', 'SECTOR_07_EDTECH_MEMBERSHIP', 'SECTOR_08_PROFESSIONAL_PSA', 'SECTOR_09_FOOD_BEVERAGE'],
                ],
                'layer_technologies' => [
                    'backend' => 'Laravel 13 (PHP 8.4/8.5+) + Spatie Laravel Data v4',
                    'frontend_public' => 'Inertia.js v2 + React 19 + TypeScript 5.8 + Shadcn/UI (Radix Primitives) + Tailwind CSS v4',
                    'frontend_admin' => 'FilamentPHP v5 (Dedicated /admin Backoffice)',
                    'orm_data_access' => 'Eloquent ORM (Strict ULID + Keyset Cursor)',
                    'database' => 'PostgreSQL 16 + Redis 7',
                    'api_protocol' => 'Inertia v2 Protocol (Deferred Props, Polling, Prefetching) — Zero separate REST boilerplate',
                    'runtime_server' => 'FrankenPHP + Vite SSR Bundle (Railpack)',
                ],
                'directory_structure_contract' => [
                    'root_pattern' => 'Single-Repo Laravel 13 + Inertia React TypeScript',
                    'required_paths' => [
                        'app/Domains/{Domain}/Actions/*Action.php',
                        'app/Data/{Domain}/*Data.php',
                        'app/Http/Controllers/Web/*Controller.php',
                        'resources/js/Pages/{Domain}/*.tsx',
                        'resources/js/Components/ui/*.tsx',
                        'resources/js/types/generated.d.ts',
                    ],
                ],
                'inter_layer_type_contract' => [
                    'mechanism' => 'Spatie Laravel Data #[TypeScript] -> spatie/laravel-typescript-transformer',
                    'codegen_command' => 'php artisan typescript:transform',
                    'zero_drift_guarantee' => 'Every Inertia page prop MUST extend Spatie\\LaravelData\\Data and compile to resources/js/types/generated.d.ts. 0% manual any props.',
                ],
                'deployment_artifacts' => [
                    'files' => ['railpack.json', 'Caddyfile', 'config/typescript-transformer.php', 'tsconfig.json'],
                    'process_manager' => 'FrankenPHP + Node SSR Worker (optional)',
                ],
                'stack_specific_anti_slop_rules' => "1. HARAM mengirim array mentah tak bertipe ke Inertia::render(); WAJIB dibungkus Spatie Laravel Data DTO dengan atribut #[TypeScript].\n2. HARAM menulis tipe `any` atau menebak struktur props di komponen React `.tsx`; WAJIB mengimpor tipe dari `resources/js/types/generated.d.ts`.\n3. HARAM membuat endpoint REST/Axios duplikat untuk navigasi form internal; gunakan Inertia v2 `useForm()` dan router visits.",
                'detection_keywords' => ['inertia', 'shadcn', 'react monolith', 'spatie data'],
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'code' => 'STACK_M3_NEXTJS_TRPC_DRIZZLE',
                'short_code' => 'M3',
                'topology' => 'MONOLITH',
                'sort_order' => 3,
                'name' => [
                    'id' => 'M3 — The Unified TypeScript Monolith (Next.js 16 App Router + tRPC + Drizzle ORM)',
                    'en' => 'M3 — The Unified TypeScript Monolith (Next.js 16 App Router + tRPC + Drizzle ORM)',
                ],
                'primary_language' => 'TypeScript',
                'best_use_cases' => [
                    'id' => 'Startup Web/AI modern yang menginginkan 100% satu bahasa (TypeScript end-to-end) dengan performa SQL murni tanpa overhead Prisma.',
                    'en' => 'Modern Web/AI startups wanting 100% single-language TypeScript end-to-end with raw SQL speed and zero Prisma overhead.',
                    'recommended_sectors' => ['SECTOR_07_EDTECH_MEMBERSHIP', 'SECTOR_08_PROFESSIONAL_PSA', 'SECTOR_10_MEDIA_TICKETING'],
                ],
                'layer_technologies' => [
                    'backend' => 'Next.js 15/16 Route Handlers + tRPC v11 + Server Actions (Bun / Node 22 LTS)',
                    'frontend_public' => 'React 19 Server Components + Shadcn/UI + Tailwind CSS v4',
                    'auth' => 'Better-Auth (Organization / Multi-Tenant Plugin + ULID Sessions)',
                    'orm_data_access' => 'Drizzle ORM (SQL-like Zero-Overhead + PostgreSQL pg-core numeric(19,4))',
                    'database' => 'PostgreSQL 16 (pgvector) + Redis (Upstash / ioredis)',
                    'api_protocol' => 'tRPC End-to-End Type Inference + RFC 9457 External Webhooks',
                    'runtime_server' => 'Standalone Next.js Output on Bun / Node 22 Docker / Railpack',
                ],
                'directory_structure_contract' => [
                    'root_pattern' => 'Single-Repo Next.js App Router + Drizzle Domain Modules',
                    'required_paths' => [
                        'src/app/(dashboard)/[orgSlug]/**/page.tsx',
                        'src/server/domains/{domain}/actions.ts',
                        'src/server/trpc/routers/{domain}.ts',
                        'src/db/schema/{domain}.ts',
                        'src/contracts/zod/{domain}.schema.ts',
                        'tests/acceptance/*.spec.ts',
                    ],
                ],
                'inter_layer_type_contract' => [
                    'mechanism' => 'drizzle-zod -> Shared Zod Schemas -> tRPC Procedure Input/Output Inference',
                    'codegen_command' => 'bunx drizzle-kit generate && bunx tsc --noEmit',
                    'zero_drift_guarantee' => 'Database DDL, Server Action validation, and React Hook Form share the exact same Zod schema object.',
                ],
                'deployment_artifacts' => [
                    'files' => ['railpack.json', 'drizzle.config.ts', 'Dockerfile', 'package.json'],
                    'process_manager' => 'Next.js Standalone Server + BullMQ Worker',
                ],
                'stack_specific_anti_slop_rules' => "1. HARAM menggunakan Prisma ORM untuk query join kompleks/ledger finansial; WAJIB gunakan Drizzle ORM (`pgTable`, `varchar('id', { length: 26 })`, `numeric({ precision: 19, scale: 4 })`).\n2. HARAM menggunakan tipe `number` JavaScript untuk nominal uang (rawan IEEE-754 float drift); WAJIB gunakan `decimal.js` atau `BigInt` minor units.\n3. HARAM memvalidasi form hanya di sisi client; setiap tRPC procedure / Server Action WAJIB memvalidasi ulang dengan Zod Schema yang sama dan mengeksekusi mutasi di dalam `db.transaction(async (tx) => ...)` dengan `.for('update')`.",
                'detection_keywords' => ['drizzle', 'trpc', 'nextjs monolith', 'better-auth', 'typescript monolith', 'bun'],
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'code' => 'STACK_M4_DJANGO_NINJA_HTMX',
                'short_code' => 'M4',
                'topology' => 'MONOLITH',
                'sort_order' => 4,
                'name' => [
                    'id' => 'M4 — The High-Throughput Python AI Monolith (Django 5.2 + Django Ninja + HTMX + Unfold)',
                    'en' => 'M4 — The High-Throughput Python AI Monolith (Django 5.2 + Django Ninja + HTMX + Unfold)',
                ],
                'primary_language' => 'Python 3.12+',
                'best_use_cases' => [
                    'id' => 'Sistem enterprise berbasis Machine Learning, Computer Vision, Bioinformatika/Klinis, atau Analitik Data berat dalam satu repositori Python.',
                    'en' => 'Enterprise systems centered on Machine Learning, Computer Vision, Clinical/Bioinformatics, or heavy Data Science in one Python repo.',
                    'recommended_sectors' => ['SECTOR_03_HEALTHCARE_CLINICAL', 'SECTOR_05_INDUSTRIAL_MRP', 'SECTOR_12_GOVTECH_NONPROFIT'],
                ],
                'layer_technologies' => [
                    'backend' => 'Django 5.2 LTS + Django Ninja (Pydantic v2 Typed Endpoints)',
                    'frontend_public' => 'Django Templates + HTMX 2.0 + Alpine.js + Tailwind CSS v4',
                    'frontend_admin' => 'Django Unfold Admin (Enterprise Brutalist Backoffice)',
                    'orm_data_access' => 'Django ORM (`select_for_update()`, `DecimalField(max_digits=19, decimal_places=4)`, `python-ulid`)',
                    'database' => 'PostgreSQL 16 (pgvector) + Redis 7',
                    'async_workers' => 'Celery / Taskiq + Redis Broker',
                    'runtime_server' => 'Granian / Uvicorn ASGI + WhiteNoise / Caddy',
                ],
                'directory_structure_contract' => [
                    'root_pattern' => 'Modular Django Apps per Bounded Domain',
                    'required_paths' => [
                        'apps/{domain}/models.py',
                        'apps/{domain}/schemas.py',
                        'apps/{domain}/services/actions.py',
                        'apps/{domain}/api.py',
                        'templates/{domain}/partials/*.html',
                        'tests/acceptance/test_{domain}.py',
                    ],
                ],
                'inter_layer_type_contract' => [
                    'mechanism' => 'Pydantic v2 Schema (`ninja.Schema`) + Python `StrEnum` + `mypy --strict`',
                    'codegen_command' => 'uv run mypy apps && uv run pytest',
                    'zero_drift_guarantee' => 'All inputs/outputs pass strict Pydantic v2 validation with Decimal preservation.',
                ],
                'deployment_artifacts' => [
                    'files' => ['pyproject.toml', 'railpack.json', 'Dockerfile', 'docker-compose.yml'],
                    'process_manager' => 'Granian ASGI + Celery Worker + Celery Beat',
                ],
                'stack_specific_anti_slop_rules' => "1. HARAM meletakkan logika bisnis/mutasi saldo di dalam `views.py` atau `Model.save()`; WAJIB di `apps/{domain}/services/actions.py` dengan `@transaction.atomic` dan `.select_for_update()`.\n2. HARAM menggunakan `float` Python untuk uang/stok; WAJIB gunakan `decimal.Decimal` dengan `ROUND_HALF_UP`.\n3. HARAM memakai `on_delete=models.CASCADE` pada tabel transaksi/medis/jurnal; WAJIB `on_delete=models.PROTECT`.",
                'detection_keywords' => ['django', 'django ninja', 'htmx', 'unfold', 'python monolith'],
                'is_default' => false,
                'is_active' => true,
            ],

            // =====================================================================
            // KATEGORI B: DECOUPLED HEADLESS ARCHITECTURE
            // =====================================================================
            [
                'code' => 'STACK_D1_LARAVEL_HEADLESS_OPENAPI',
                'short_code' => 'D1',
                'topology' => 'DECOUPLED_HEADLESS',
                'sort_order' => 5,
                'name' => [
                    'id' => 'D1 — Enterprise PHP Headless + Web/Mobile (Laravel 13 Octane + Scramble OpenAPI 3.1 + Next.js/Expo)',
                    'en' => 'D1 — Enterprise PHP Headless + Web/Mobile (Laravel 13 Octane + Scramble OpenAPI 3.1 + Next.js/Expo)',
                ],
                'primary_language' => 'PHP + TypeScript / Dart',
                'best_use_cases' => [
                    'id' => 'Perusahaan yang membutuhkan Web App (Next.js/Nuxt) + Native Mobile App (iOS & Android via Expo/Flutter) sekaligus dari 1 Core API.',
                    'en' => 'Companies needing Web App (Next.js/Nuxt) + Native Mobile App (iOS & Android via Expo/Flutter) simultaneously from 1 Core API.',
                    'recommended_sectors' => ['SECTOR_01_COMMERCE_OMNICHANNEL', 'SECTOR_02_HOSPITALITY_SPACE', 'SECTOR_06_LOGISTICS_FLEET', 'SECTOR_09_FOOD_BEVERAGE'],
                ],
                'layer_technologies' => [
                    'backend_api' => 'Laravel 13 API-Only + Laravel Octane (FrankenPHP) + Dedoc Scramble (Auto OpenAPI 3.1) + Sanctum',
                    'headless_admin' => 'FilamentPHP v5 (Backoffice Control Plane)',
                    'frontend_web' => 'Next.js 15 / Nuxt 4 + TanStack Query v5 + Orval / HeyAPI Generated Hooks + Shadcn/UI',
                    'mobile_client' => 'Expo (React Native) or Flutter consuming generated OpenAPI 3.1 Client SDK',
                    'database' => 'PostgreSQL 16 (Strict ULID) + Redis 7',
                    'api_protocol' => 'REST JSON (RFC 9457 Problem Details + Keyset Cursor Envelope + X-Idempotency-Key)',
                ],
                'directory_structure_contract' => [
                    'root_pattern' => 'Workspace Monorepo: /backend-api, /web-app, /mobile-app, /packages/api-sdk',
                    'required_paths' => [
                        'backend-api/app/Domains/{Domain}/Actions/*Action.php',
                        'backend-api/app/Http/Controllers/Api/V1/*Controller.php',
                        'backend-api/app/Http/Resources/V1/*Resource.php',
                        'packages/api-sdk/openapi.json',
                        'packages/api-sdk/src/generated/*.ts',
                        'web-app/src/app/**/page.tsx',
                    ],
                ],
                'inter_layer_type_contract' => [
                    'mechanism' => 'Dedoc Scramble OpenAPI 3.1 Export -> Orval / HeyAPI TypeScript & TanStack Query SDK Generator',
                    'codegen_command' => 'php artisan scramble:export --path=../packages/api-sdk/openapi.json && bunx orval --config orval.config.ts',
                    'zero_drift_guarantee' => 'Frontend and Mobile ONLY call generated SDK hooks (`useReserveInventorySlot`, etc.). Manual `fetch("/api/...")` with `any` is strictly banned.',
                ],
                'deployment_artifacts' => [
                    'files' => ['backend-api/railpack.json', 'backend-api/Caddyfile', 'orval.config.ts', 'docker-compose.yml'],
                    'process_manager' => 'Octane FrankenPHP API + Edge/Container Next.js Web',
                ],
                'stack_specific_anti_slop_rules' => "1. HARAM menulis `fetch('/api/...')` atau `axios.post(...)` manual dengan tipe `any` di Next.js/React Native; WAJIB mengekspor OpenAPI 3.1 via Scramble dan mengonsumsi hooks hasil generate Orval/HeyAPI.\n2. Setiap error HTTP dari Laravel API WAJIB mematuhi RFC 9457 (`application/problem+json`) dan setiap list endpoint WAJIB memakai Keyset Cursor (`meta.cursor.next`).\n3. Setiap endpoint mutasi (`POST`/`PUT`/`PATCH`) WAJIB memvalidasi header `X-Idempotency-Key` dan `X-Tenant-ID`.",
                'detection_keywords' => ['headless', 'mobile app', 'ios', 'android', 'flutter', 'react native', 'expo', 'orval', 'scramble', 'decoupled php'],
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'code' => 'STACK_D2_GO_SQLC_NEXTJS',
                'short_code' => 'D2',
                'topology' => 'DECOUPLED_HEADLESS',
                'sort_order' => 6,
                'name' => [
                    'id' => 'D2 — Ultra-Fast Go Headless Engine + Next.js (Golang 1.24 + sqlc + Chi/Fiber + Next.js 15)',
                    'en' => 'D2 — Ultra-Fast Go Headless Engine + Next.js (Golang 1.24 + sqlc + Chi/Fiber + Next.js 15)',
                ],
                'primary_language' => 'Golang 1.24+ & TypeScript',
                'best_use_cases' => [
                    'id' => 'Sistem berkonkurensi ekstrem (~15MB idle RAM, puluhan ribu req/detik): Fintech Ledger, Fleet GPS Tracking, atau Flash-Sale Ticketing War.',
                    'en' => 'Extreme-concurrency systems (~15MB idle RAM, tens of thousands req/sec): Fintech Ledgers, Fleet GPS Tracking, or Ticketing Wars.',
                    'recommended_sectors' => ['SECTOR_04_FINTECH_LEDGER', 'SECTOR_06_LOGISTICS_FLEET', 'SECTOR_10_MEDIA_TICKETING'],
                ],
                'layer_technologies' => [
                    'backend_api' => 'Golang 1.24+ (Chi v5 / Fiber v3) + pgx/v5 Connection Pool + shopspring/decimal',
                    'sql_compiler' => 'sqlc (Compiles raw PostgreSQL 16 SQL queries into 100% type-safe Go structs — ZERO reflection ORM)',
                    'migrations' => 'golang-migrate (Strict ULID oklog/ulid/v2)',
                    'frontend_web' => 'Next.js 15 + Shadcn/UI + TanStack Query v5 + Generated OpenAPI/ConnectRPC Client',
                    'database' => 'PostgreSQL 16 + Redis 7',
                    'api_protocol' => 'OpenAPI 3.1 REST (RFC 9457) or ConnectRPC',
                ],
                'directory_structure_contract' => [
                    'root_pattern' => 'Clean Go Standard Layout + Next.js Web Client',
                    'required_paths' => [
                        'backend-go/cmd/api/main.go',
                        'backend-go/sqlc.yaml',
                        'backend-go/db/migrations/*.sql',
                        'backend-go/db/queries/*.sql',
                        'backend-go/internal/db/*.sql.go',
                        'backend-go/internal/domain/{domain}/service.go',
                        'backend-go/internal/domain/{domain}/service_test.go',
                        'web-nextjs/src/app/**/page.tsx',
                    ],
                ],
                'inter_layer_type_contract' => [
                    'mechanism' => 'PostgreSQL DDL + SQL Queries -> `sqlc generate` -> Go Structs -> OpenAPI 3.1 / ConnectRPC -> TypeScript Client',
                    'codegen_command' => 'sqlc generate && go test ./... -race',
                    'zero_drift_guarantee' => 'Compile-time verification from SQL column types (`NUMERIC(19,4)`, `VARCHAR(26)`) all the way to React hooks.',
                ],
                'deployment_artifacts' => [
                    'files' => ['backend-go/sqlc.yaml', 'backend-go/Dockerfile', 'docker-compose.yml', 'railpack.json'],
                    'process_manager' => 'Static Distroless Go Binary (~15MB RAM) + Next.js Frontend',
                ],
                'stack_specific_anti_slop_rules' => "1. HARAM menggunakan GORM atau ORM refleksi lambat di Go; WAJIB menulis SQL eksplisit di `db/queries/*.sql` (termasuk `SELECT ... FOR UPDATE`) dan men-generate kode Go via `sqlc`.\n2. HARAM menggunakan `float64` untuk nilai uang/saldo; WAJIB gunakan `github.com/shopspring/decimal` dengan `NUMERIC(19,4)`.\n3. Setiap test konkurensi Go WAJIB lulus dengan flag `-race` (`go test ./... -race`).",
                'detection_keywords' => ['golang', 'go ', 'sqlc', 'high concurrency', 'ticketing war', 'ribuan request'],
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'code' => 'STACK_D3_FASTAPI_SQLALCHEMY_REACT',
                'short_code' => 'D3',
                'topology' => 'DECOUPLED_HEADLESS',
                'sort_order' => 7,
                'name' => [
                    'id' => 'D3 — Python AI-Native Headless + React (FastAPI + Pydantic v2 + SQLAlchemy 2.0 Async + React 19)',
                    'en' => 'D3 — Python AI-Native Headless + React (FastAPI + Pydantic v2 + SQLAlchemy 2.0 Async + React 19)',
                ],
                'primary_language' => 'Python 3.12+ & TypeScript',
                'best_use_cases' => [
                    'id' => 'Platform AI Agentic, RAG Multi-Dokumen, Telemedicine AI, dan Pipeline Inferensi Asinkron dengan UI SPA React 19 modern.',
                    'en' => 'Agentic AI Platforms, Multi-Document RAG, AI Telemedicine, and Async Inference Pipelines with modern React 19 SPA.',
                    'recommended_sectors' => ['SECTOR_03_HEALTHCARE_CLINICAL', 'SECTOR_07_EDTECH_MEMBERSHIP', 'SECTOR_08_PROFESSIONAL_PSA'],
                ],
                'layer_technologies' => [
                    'backend_api' => 'FastAPI + Pydantic v2 + SQLAlchemy 2.0 (Asyncpg) + Alembic',
                    'async_workers' => 'Taskiq / Celery + Redis 7',
                    'frontend_web' => 'React 19 (Vite / TanStack Start) + TanStack Router + TanStack Query v5 + Shadcn/UI',
                    'database' => 'PostgreSQL 16 (pgvector HNSW) + Redis 7',
                    'api_protocol' => 'Auto-Generated OpenAPI 3.1 (FastAPI -> HeyAPI TypeScript Client) + SSE Streaming',
                ],
                'directory_structure_contract' => [
                    'root_pattern' => 'Decoupled /backend-fastapi and /frontend-react Workspace',
                    'required_paths' => [
                        'backend-fastapi/app/domains/{domain}/models.py',
                        'backend-fastapi/app/domains/{domain}/schemas.py',
                        'backend-fastapi/app/domains/{domain}/actions.py',
                        'backend-fastapi/app/api/v1/{domain}.py',
                        'backend-fastapi/alembic/versions/*.py',
                        'frontend-react/src/client/generated/*.ts',
                    ],
                ],
                'inter_layer_type_contract' => [
                    'mechanism' => 'FastAPI Pydantic v2 `openapi.json` -> `@hey-api/openapi-ts` TypeScript SDK',
                    'codegen_command' => 'uv run python -m app.export_openapi && bunx openapi-ts',
                    'zero_drift_guarantee' => '100% typed Pydantic v2 response models map directly to TypeScript interfaces.',
                ],
                'deployment_artifacts' => [
                    'files' => ['backend-fastapi/pyproject.toml', 'backend-fastapi/Dockerfile', 'frontend-react/vite.config.ts', 'docker-compose.yml'],
                    'process_manager' => 'Uvicorn/Granian Async Workers + Taskiq Worker + Static/Edge SPA',
                ],
                'stack_specific_anti_slop_rules' => "1. HARAM memanggil driver DB sinkron (`psycopg2` blocking) di dalam route `async def` FastAPI; WAJIB gunakan SQLAlchemy 2.0 `AsyncSession` (`asyncpg`).\n2. HARAM mengembalikan `dict` mentah tanpa `response_model=` Pydantic v2 di FastAPI.\n3. Semua error wajib memakai exception handler RFC 9457 (`application/problem+json`).",
                'detection_keywords' => ['fastapi', 'sqlalchemy', 'pydantic', 'python api', 'rag api'],
                'is_default' => false,
                'is_active' => true,
            ],

            // =====================================================================
            // KATEGORI C: DISTRIBUTED MICROSERVICES & EVENT-DRIVEN
            // =====================================================================
            [
                'code' => 'STACK_S1_POLYGLOT_GO_LARAVEL_TEMPORAL',
                'short_code' => 'S1',
                'topology' => 'MICROSERVICES',
                'sort_order' => 8,
                'name' => [
                    'id' => 'S1 — The Polyglot Event-Driven Mesh (Go Ledger/Booking + Laravel Filament Backoffice + FastAPI AI + Temporal.io)',
                    'en' => 'S1 — The Polyglot Event-Driven Mesh (Go Ledger/Booking + Laravel Filament Backoffice + FastAPI AI + Temporal.io)',
                ],
                'primary_language' => 'Go + PHP + Python',
                'best_use_cases' => [
                    'id' => 'Enterprise skala besar, perbankan/fintech/rumah sakit dengan regulasi isolasi database per domain & orkestrasi Saga lintas service.',
                    'en' => 'Large enterprises, banking/fintech/hospitals requiring strict database-per-domain isolation & distributed Saga orchestration.',
                    'recommended_sectors' => ['SECTOR_03_HEALTHCARE_CLINICAL', 'SECTOR_04_FINTECH_LEDGER', 'SECTOR_06_LOGISTICS_FLEET', 'SECTOR_10_MEDIA_TICKETING'],
                ],
                'layer_technologies' => [
                    'api_gateway_edge' => 'Traefik v3 / Kong Gateway + Rate Limiting + JWT/mTLS Verification',
                    'identity_and_backoffice_service' => 'Laravel 13 + Filament v5 (Isolated `identity_db` PostgreSQL 16)',
                    'core_transactional_engine' => 'Golang 1.24 + sqlc + ConnectRPC/gRPC (Isolated `ledger_booking_db` PostgreSQL 16)',
                    'ai_rag_worker_service' => 'Python FastAPI + pgvector (Isolated `knowledge_vector_db` PostgreSQL 16)',
                    'sync_rpc' => 'Buf Protobuf v2 + ConnectRPC (gRPC)',
                    'async_event_bus' => 'NATS JetStream (CloudEvents v1.0 Envelope + Transactional Outbox Relay)',
                    'saga_orchestrator' => 'Temporal.io (Deterministic Distributed Saga & Compensating Workflows)',
                ],
                'directory_structure_contract' => [
                    'root_pattern' => 'Polyglot Multi-Service Mesh with Central Buf Protobuf Registry',
                    'required_paths' => [
                        'proto/domain/v1/*.proto',
                        'buf.gen.yaml',
                        'services/backoffice-laravel/',
                        'services/core-engine-go/',
                        'services/ai-worker-python/',
                        'services/temporal-sagas-go/',
                        'deploy/docker-compose.polyglot-mesh.yml',
                    ],
                ],
                'inter_layer_type_contract' => [
                    'mechanism' => 'Central `proto/` Registry (`buf generate`) -> Go, PHP, Python, & TypeScript Stubs + Temporal Workflow Contracts',
                    'codegen_command' => 'buf lint && buf generate',
                    'zero_drift_guarantee' => 'Cross-service communication ONLY via compiled Protobuf schemas and NATS JetStream subjects; cross-service DB JOINs are physically impossible.',
                ],
                'deployment_artifacts' => [
                    'files' => ['buf.gen.yaml', 'proto/domain/v1/ledger_saga.proto', 'docker-compose.polyglot-mesh.yml', 'internal/temporal/saga_workflow.go'],
                    'process_manager' => 'Kubernetes / Docker Swarm / Coolify Multi-Service Mesh + Temporal Server + NATS JetStream',
                ],
                'stack_specific_anti_slop_rules' => "1. HARAM mengakses database milik service lain secara langsung (Zero Shared Database); setiap service memiliki instance/database PostgreSQL 16 terisolasi.\n2. HARAM menulis `queue retry` manual untuk transaksi lintas service; WAJIB gunakan Temporal.io Saga Workflow + Transactional Outbox (`outbox_messages`) dengan `Compensating Action` eksplisit.\n3. Semua komunikasi sinkron antar-service WAJIB menggunakan Protobuf/ConnectRPC (bukan JSON tak bertipe).",
                'detection_keywords' => ['microservices', 'polyglot', 'temporal', 'grpc', 'protobuf', 'nats', 'kafka', 'multi-service'],
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'code' => 'STACK_S2_TS_TURBOREPO_NATS',
                'short_code' => 'S2',
                'topology' => 'MICROSERVICES',
                'sort_order' => 9,
                'name' => [
                    'id' => 'S2 — TypeScript Turborepo Microservices (Hono.js / NestJS Services + @repo/contracts + NATS JetStream)',
                    'en' => 'S2 — TypeScript Turborepo Microservices (Hono.js / NestJS Services + @repo/contracts + NATS JetStream)',
                ],
                'primary_language' => 'TypeScript (Turborepo)',
                'best_use_cases' => [
                    'id' => 'Organisasi engineering multi-squad berbasis TypeScript penuh yang membutuhkan isolasi deployment & database independen per domain.',
                    'en' => 'Multi-squad full-TypeScript engineering orgs needing independent deployment & isolated PostgreSQL databases per domain.',
                    'recommended_sectors' => ['SECTOR_01_COMMERCE_OMNICHANNEL', 'SECTOR_04_FINTECH_LEDGER', 'SECTOR_10_MEDIA_TICKETING'],
                ],
                'layer_technologies' => [
                    'monorepo_orchestrator' => 'Turborepo + Bun / pnpm Workspaces',
                    'domain_services' => 'Independent Hono.js / NestJS Microservices (Order, Ledger, Inventory, Notification)',
                    'shared_contracts' => '@repo/contracts (Protobuf + Shared Zod Schemas + RFC 9457 Error Catalogue)',
                    'orm_data_access' => 'Drizzle ORM per Service (Isolated PostgreSQL 16 DB per Service + Transactional Outbox)',
                    'async_event_bus' => 'NATS JetStream (At-Least-Once Delivery + Consumer Idempotency Table)',
                    'frontend_apps' => 'apps/web (Next.js 15) + apps/admin (Shadcn Brutalist Control Plane)',
                ],
                'directory_structure_contract' => [
                    'root_pattern' => 'Turborepo Strict Domain Microservices',
                    'required_paths' => [
                        'turbo.json',
                        'packages/contracts/src/*.ts',
                        'apps/web/',
                        'apps/admin/',
                        'services/identity-service/',
                        'services/core-domain-service/',
                        'services/ledger-service/',
                    ],
                ],
                'inter_layer_type_contract' => [
                    'mechanism' => 'Workspace Package `@repo/contracts` exporting versioned Zod + Protobuf Event Envelopes',
                    'codegen_command' => 'bunx turbo run typecheck test build',
                    'zero_drift_guarantee' => 'Turborepo fails CI immediately if any microservice or frontend breaks `@repo/contracts`.',
                ],
                'deployment_artifacts' => [
                    'files' => ['turbo.json', 'packages/contracts/src/events.ts', 'docker-compose.yml'],
                    'process_manager' => 'Independent Containerized Services + NATS JetStream Cluster',
                ],
                'stack_specific_anti_slop_rules' => "1. HARAM mengimpor kode internal/skema Drizzle dari `services/service-a` ke `services/service-b`; komunikasi hanya boleh mengimpor kontrak dari `@repo/contracts`.\n2. Setiap event NATS JetStream WAJIB diterbitkan melalui pola `outbox_messages` dalam transaksi lokal yang sama dan dikonsumsi secara idempoten (`processed_event_ids`).\n3. HARAM menggunakan `float`/`number` JS untuk kalkulasi moneter; wajib `decimal.js`.",
                'detection_keywords' => ['turborepo', 'hono', 'nestjs', 'monorepo microservices'],
                'is_default' => false,
                'is_active' => true,
            ],
        ];
    }

    /**
     * Dimension 1: RFC 9457 Problem Details & Standard Payload Envelope Contract.
     */
    public static function apiEnvelopeContract(string $topology, string $presetCode): array
    {
        return [
            'standard' => 'RFC 9457 (Problem Details for HTTP APIs) + Deterministic O(1) Keyset Envelope',
            'active_topology' => $topology,
            'active_stack_preset' => $presetCode,
            'mandatory_request_headers' => [
                'X-Request-ID' => '26-char ULID correlation ID generated at edge/client and echoed in every response & log.',
                'X-Tenant-ID' => '26-char ULID of active organization_id (enforced against authenticated membership).',
                'X-Idempotency-Key' => 'REQUIRED on all state-mutating POST/PUT/PATCH requests (TTL 24h in Redis/DB idempotency store; duplicate replay returns cached original response without re-execution).',
                'Accept-Language' => 'BCP-47 locale tag (`id` or `en`).',
            ],
            'success_single_envelope' => [
                'content_type' => 'application/json',
                'example' => [
                    'success' => true,
                    'request_id' => '01J8Z9K4M2N6P8Q0R2S4T6V8W0',
                    'data' => ['id' => '01J8Z9K4M2N6P8Q0R2S4T6V8W1', 'status' => 'CONFIRMED'],
                ],
            ],
            'success_collection_cursor_envelope' => [
                'content_type' => 'application/json',
                'pagination_invariant' => 'STRICT O(1) KEYSET CURSOR ONLY. Offset fields (page, last_page, total) are FORBIDDEN.',
                'example' => [
                    'success' => true,
                    'request_id' => '01J8Z9K4M2N6P8Q0R2S4T6V8W0',
                    'data' => [],
                    'meta' => [
                        'cursor' => [
                            'next' => 'eyJpZCI6IjAxSjhaOUs0TTJONlA4UTBSMlM0VDZWOFcxIn0',
                            'prev' => null,
                            'per_page' => 25,
                            'has_more' => true,
                        ],
                    ],
                ],
            ],
            'error_rfc9457_problem_envelope' => [
                'content_type' => 'application/problem+json',
                'example' => [
                    'type' => 'https://neriahpro.com/problems/domain-invariant-violation',
                    'title' => 'Domain State Transition Rejected',
                    'status' => 422,
                    'detail' => 'Insufficient available stock in warehouse 01J8... after atomic pessimistic lock.',
                    'instance' => '/api/v1/inventory/reservations',
                    'request_id' => '01J8Z9K4M2N6P8Q0R2S4T6V8W0',
                    'error_code' => 'INVENTORY_INSUFFICIENT_STOCK',
                    'errors' => ['quantity' => ['Requested 10.0000 exceeds available 4.0000.']],
                ],
            ],
            'inter_service_event_envelope' => $topology === 'MICROSERVICES'
                ? [
                    'protocol' => 'CloudEvents v1.0 over NATS JetStream + Protobuf ConnectRPC',
                    'required_attributes' => ['specversion' => '1.0', 'id' => 'ULID', 'source' => 'service-name', 'type' => 'com.domain.entity.action.v1', 'tenantid' => 'ULID', 'time' => 'RFC3339_UTC', 'datacontenttype' => 'application/protobuf'],
                ]
                : [
                    'protocol' => 'Transactional Outbox Table (`outbox_messages`) -> Queued Domain Event Dispatcher',
                ],
        ];
    }

    /**
     * Dimensions 2 & 4: Mandatory Reliability Infrastructure Tables (Transactional Outbox & Inbound Webhook Vault).
     */
    public static function transactionalOutboxAndWebhookTables(): array
    {
        return [
            [
                'table' => 'outbox_messages',
                'description' => 'Transactional Outbox Pattern table written inside the exact same DB transaction as domain state changes before publishing to Queue/NATS/Temporal.',
                'tenant_scoped' => true,
                'soft_deletes' => false,
                'deletion_policy' => 'SOFT_ARCHIVE',
                'retention_period' => '90_DAYS_HOT_THEN_PARTITION_ARCHIVE',
                'pii_columns' => [],
                'foreign_key_on_delete' => 'RESTRICT',
                'columns' => [
                    ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                    ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                    ['name' => 'aggregate_type', 'type' => 'VARCHAR(64)', 'nullable' => false],
                    ['name' => 'aggregate_id', 'type' => 'ULID', 'nullable' => false, 'index' => true],
                    ['name' => 'event_type', 'type' => 'VARCHAR(128)', 'nullable' => false, 'index' => true],
                    ['name' => 'idempotency_key', 'type' => 'VARCHAR(128)', 'nullable' => false],
                    ['name' => 'payload', 'type' => 'JSONB', 'nullable' => false],
                    ['name' => 'status', 'type' => 'VARCHAR(24)', 'nullable' => false, 'index' => true],
                    ['name' => 'attempts', 'type' => 'SMALLINT', 'nullable' => false],
                    ['name' => 'next_retry_at', 'type' => 'TIMESTAMPTZ', 'nullable' => true, 'index' => true],
                    ['name' => 'dispatched_at', 'type' => 'TIMESTAMPTZ', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'TIMESTAMPTZ', 'nullable' => false],
                ],
                'unique_constraints' => [
                    ['name' => 'outbox_messages_idempotency_unique', 'rule' => 'UNIQUE (idempotency_key)'],
                ],
                'indexes' => [['status', 'next_retry_at'], ['aggregate_type', 'aggregate_id']],
            ],
            [
                'table' => 'webhook_inbound_logs',
                'description' => 'Tamper-evident inbound webhook vault verifying HMAC-SHA256 signatures and deduplicating replays before async job processing.',
                'tenant_scoped' => false,
                'soft_deletes' => false,
                'deletion_policy' => 'IMMUTABLE_LEDGER',
                'retention_period' => '5_YEARS_AUDIT_COMPLIANCE',
                'pii_columns' => ['raw_headers'],
                'foreign_key_on_delete' => 'RESTRICT',
                'columns' => [
                    ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                    ['name' => 'provider', 'type' => 'VARCHAR(48)', 'nullable' => false, 'index' => true],
                    ['name' => 'provider_event_id', 'type' => 'VARCHAR(128)', 'nullable' => false],
                    ['name' => 'signature_header', 'type' => 'VARCHAR(255)', 'nullable' => false],
                    ['name' => 'signature_verified', 'type' => 'BOOLEAN', 'nullable' => false],
                    ['name' => 'raw_payload', 'type' => 'JSONB', 'nullable' => false],
                    ['name' => 'processing_status', 'type' => 'VARCHAR(24)', 'nullable' => false, 'index' => true],
                    ['name' => 'processed_at', 'type' => 'TIMESTAMPTZ', 'nullable' => true],
                    ['name' => 'error_detail', 'type' => 'TEXT', 'nullable' => true],
                    ['name' => 'received_at', 'type' => 'TIMESTAMPTZ', 'nullable' => false, 'index' => true],
                ],
                'unique_constraints' => [
                    ['name' => 'webhook_inbound_logs_provider_event_unique', 'rule' => 'UNIQUE (provider, provider_event_id)'],
                ],
                'indexes' => [['processing_status', 'received_at']],
            ],
        ];
    }

    /**
     * Dimension 5: Deterministic Data Lifecycle, PII Masking & Deletion Governance per Table.
     */
    public static function resolveTableDeletionPolicy(string $tableName): array
    {
        $t = strtolower(trim($tableName));

        // 1. IMMUTABLE_LEDGER: Strictly forbidden to DELETE or overwrite; corrections ONLY via reversal/addendum
        foreach ([
            'journal', 'ledger', 'movement', 'invoice', 'payment', 'settlement', 'disbursement',
            'clinical', 'encounter', 'emr', 'soap', 'prescription', 'payroll', 'payslip',
            'approval_log', 'audit', 'webhook_inbound_logs', 'ticket_order', 'pod', 'manifest',
        ] as $kw) {
            if (str_contains($t, $kw)) {
                return [
                    'deletion_policy' => 'IMMUTABLE_LEDGER',
                    'foreign_key_on_delete' => 'RESTRICT',
                    'retention_period' => str_contains($t, 'clinical') || str_contains($t, 'emr') || str_contains($t, 'encounter')
                        ? '25_YEARS_PERMENKES_RME'
                        : '10_YEARS_STATUTORY_PSAK_TAX',
                    'pii_columns' => [],
                    'enforcement_rule' => 'HARAM `cascadeOnDelete()` atau `DELETE FROM`. Foreign key wajib `RESTRICT`. Koreksi kesalahan hanya boleh melalui entri jurnal balik (contra-entry) atau addendum bertanda tangan waktu.',
                ];
            }
        }

        // 2. ANONYMIZE_PII: User/Customer/Patient/Employee master records subject to UU PDP No. 27/2022 right-to-erasure
        foreach (['user', 'customer', 'patient', 'employee', 'member', 'guest', 'donor', 'student', 'contact'] as $kw) {
            if (str_contains($t, $kw)) {
                return [
                    'deletion_policy' => 'ANONYMIZE_PII',
                    'foreign_key_on_delete' => 'RESTRICT',
                    'retention_period' => 'ACTIVE_ACCOUNT_PLUS_STATUTORY_REDACTION',
                    'pii_columns' => ['name', 'full_name', 'email', 'phone', 'whatsapp', 'nik', 'address', 'birth_date'],
                    'enforcement_rule' => 'Saat permintaan hapus akun (UU PDP / GDPR), DILARANG menghapus baris agar relasi FK jurnal/faktur tidak rusak. Hash & timpa kolom PII menjadi `[REDACTED_USER_<ULID>]` dan catat `anonymized_at`.',
                ];
            }
        }

        // 3. SOFT_ARCHIVE: Operational / catalog / schedule / work-order tables
        return [
            'deletion_policy' => 'SOFT_ARCHIVE',
            'foreign_key_on_delete' => 'RESTRICT',
            'retention_period' => '5_YEARS_ACTIVE_THEN_PARTITION_ARCHIVE',
            'pii_columns' => [],
            'enforcement_rule' => 'Gunakan penanda `archived_at` / `is_active = false` dan larang `cascadeOnDelete()` bila baris sudah direferensikan oleh tabel transaksi historis (`RESTRICT`).',
        ];
    }

    /**
     * Dimensions 2, 3, 4, 6, 7: Complete Ultimate Contracts for all 12 Core Primitives.
     */
    public static function primitiveUltimateContracts(): array
    {
        return [
            'PRIMITIVE_CATALOG_SKU' => [
                'deterministic_formulas' => [
                    [
                        'formula_code' => 'COMMERCIAL_LINE_AND_TAX_PRECEDENCE',
                        'expression' => 'gross = round_half_up(qty * unit_price); net_before_tax = max(0, gross - line_discount - allocated_voucher_discount); service_fee = round_half_up(net_before_tax * service_rate); tax_amount = round_half_up((net_before_tax + (tax_on_service ? service_fee : 0)) * ppn_or_pb1_rate); grand_total = net_before_tax + service_fee + tax_amount',
                        'order_of_precedence' => [
                            '1. Konversi UoM ke base unit (`base_qty = input_qty * uom_conversion_factor`) dengan `DECIMAL(19,4)`.',
                            '2. Ambil harga tier grosir (`min_qty <= input_qty`) lalu hitung `gross = qty * unit_price`.',
                            '3. Potong diskon item & diskon voucher proporsional SEBELUM perhitungan PPN 11% / PB1 10%.',
                            '4. Pembulatan menggunakan `HALF_UP` ke satuan mata uang terkecil (`IDR` = 0 desimal pada hasil akhir tagihan, internal tetap `DECIMAL(19,4)`).',
                        ],
                        'penny_allocation_rule' => 'Jika diskon voucher nominal (misal Rp 20.000) dialokasikan proporsional ke N baris item dan menyisakan selisih Rp 1 akibat pembulatan, selisih Rp 1 WAJIB dialokasikan ke baris item dengan nilai subtotal terbesar (`Largest Remainder Penny Rule`).',
                    ],
                ],
                'saga_and_webhook_contracts' => [
                    'transactional_outbox_events' => ['catalog.sku.price_updated.v1', 'catalog.sku.archived.v1'],
                    'compensating_actions' => [
                        ['trigger' => 'MarketplacePriceSyncRejected', 'compensation' => 'RevertStagedPriceBookVersionAction', 'mechanism' => 'Transactional Outbox (`outbox_messages`)'],
                    ],
                    'external_integrations' => [
                        ['provider' => 'Omnichannel Marketplace / POS Sync', 'inbound_security' => 'HMAC-SHA256 + `webhook_inbound_logs` unique `(provider, provider_event_id)`', 'outbound_resilience' => 'Async Job + Circuit Breaker (5 failures -> 60s open) + Exponential Backoff [10s, 60s, 300s, 1800s]'],
                    ],
                ],
                'ui_screen_archetypes' => [
                    [
                        'screen' => 'POS Cashier & B2B Quick Order Terminal',
                        'archetype' => 'ARCHETYPE_POS_SPLIT_SCREEN',
                        'layout_contract' => '12-Col Full Width: Left 7-Col High-Density SKU Grid + Instant Barcode/Text Search with `✕` Clear Button; Right 5-Col Sticky Cart with `tabular-nums` Breakdown & Tiered Discount Preview.',
                        'keyboard_shortcuts' => ['/' => 'Focus Barcode/SKU Search', 'F2' => 'Proceed to Payment Modal', 'F4' => 'Hold / Park Cart', 'ESC' => 'Close Modal / Clear Search'],
                        'empty_state' => 'Monochrome package SVG + "Belum ada SKU aktif di gudang/cabang ini" + Primary CTA "Tambah SKU / Impor CSV".',
                        'loading_state' => 'Deterministic 12-card grid pulse skeleton + 4-row cart skeleton (zero layout shift).',
                        'error_state' => 'Inline solid high-contrast banner showing RFC 9457 `error_code` & `request_id` copy button (ZERO `window.alert()`).',
                    ],
                ],
                'acceptance_test_scenarios' => [
                    [
                        'id' => 'AT-CAT-01',
                        'given' => 'Keranjang berisi 3 SKU dengan subtotal Rp 30.000, Rp 30.000, dan Rp 40.000 serta voucher diskon Rp 10.000 dan PPN 11%',
                        'when' => 'Mesin kalkulasi menghitung alokasi diskon per baris dan total pajak',
                        'then' => 'Total diskon teralokasi tepat Rp 10.000,0000 tanpa selisih Rp 1, DPP menjadi Rp 90.000, PPN 11% = Rp 9.900, dan Grand Total = Rp 99.900.',
                    ],
                    [
                        'id' => 'AT-CAT-02',
                        'given' => 'Sebuah SKU sudah pernah dipakai pada baris transaksi penjualan historis',
                        'when' => 'Admin mencoba melakukan hard DELETE pada SKU tersebut',
                        'then' => 'Database menolak penghapusan (`RESTRICT`) dan sistem mengalihkan ke kebijakan `SOFT_ARCHIVE` (`is_active = false`).',
                    ],
                ],
            ],

            'PRIMITIVE_INVENTORY_LEDGER' => [
                'deterministic_formulas' => [
                    [
                        'formula_code' => 'IMMUTABLE_STOCK_BALANCE_AND_MOVING_AVERAGE_COGS',
                        'expression' => 'available_qty = SUM(signed_movement_qty) - SUM(active_reserved_qty); new_wac_cost = round_half_up(((current_qty * current_wac) + (inbound_qty * inbound_unit_cost)) / nullif(current_qty + inbound_qty, 0))',
                        'order_of_precedence' => [
                            '1. Kunci baris saldo gudang `(warehouse_id, variant_id)` dengan `SELECT ... FOR UPDATE`.',
                            '2. Validasi `available_qty >= requested_qty` (untuk reservasi/outbound) atau urutan `expiry_date ASC` (FEFO untuk obat/makanan).',
                            '3. Sisipkan baris append-only ke `inventory_movements` dan perbarui proyeksi `inventory_balances` dalam transaksi yang sama.',
                        ],
                        'penny_allocation_rule' => 'Biaya ongkos angkut masuk (landed cost) dialokasikan ke setiap batch berdasarkan bobot nilai; sisa pembulatan sen diserap oleh baris SKU dengan nilai pembelian tertinggi.',
                    ],
                ],
                'saga_and_webhook_contracts' => [
                    'transactional_outbox_events' => ['inventory.stock.reserved.v1', 'inventory.stock.committed.v1', 'inventory.stock.reservation_released.v1'],
                    'compensating_actions' => [
                        ['trigger' => 'PaymentCaptureFailedOrExpired', 'compensation' => 'ReleaseStockReservationCompensatingAction', 'mechanism' => 'Appends `+qty` reversal reservation release & marks reservation `EXPIRED` via Outbox/Temporal Saga.'],
                        ['trigger' => 'InventoryLockInsufficientAfterWalletDebit', 'compensation' => 'RefundWalletBalanceCommand', 'mechanism' => 'Publishes `order.inventory_failed.v1` outbox event to trigger automatic refund/reversal.'],
                    ],
                    'external_integrations' => [
                        ['provider' => '3PL WMS / ERP Stock Webhook', 'inbound_security' => 'HMAC-SHA256 + `webhook_inbound_logs` deduplication', 'outbound_resilience' => 'Circuit Breaker + Exponential Backoff [10s, 60s, 300s, 1800s]'],
                    ],
                ],
                'ui_screen_archetypes' => [
                    [
                        'screen' => 'Multi-Warehouse Stock Card & Mutation Ledger',
                        'archetype' => 'ARCHETYPE_ENTERPRISE_CURSOR_LEDGER_TABLE',
                        'layout_contract' => 'Sticky Top Header + Frozen Left SKU Column + Frozen Right Action Column + Density Switcher (Compact/Normal/Relaxed) + FEFO Batch Expiry Warning Badges.',
                        'keyboard_shortcuts' => ['/' => 'Focus SKU/Batch Search', 'ALT+N' => 'Open Stock Transfer Drawer'],
                        'empty_state' => 'Warehouse icon + "Belum ada mutasi stok tercatat" + CTA "Catat Saldo Awal (Opening Stock)".',
                        'loading_state' => '10-row tabular skeleton matching active row density.',
                        'error_state' => 'RFC 9457 conflict toast displaying exact locked available quantity.',
                    ],
                ],
                'acceptance_test_scenarios' => [
                    [
                        'id' => 'AT-INV-01',
                        'given' => 'Stok tersedia untuk SKU Raket Pro di Gudang Utama tersisa tepat 1.0000 unit',
                        'when' => 'Dua request checkout konkuren mengirim kuantitas 1.0000 pada milidetik yang sama dengan `X-Idempotency-Key` berbeda',
                        'then' => 'Pessimistic lock (`SELECT ... FOR UPDATE`) memastikan tepat 1 request berhasil (`201 Created`) dan request kedua ditolak deterministik dengan HTTP `422` RFC 9457 (`INVENTORY_INSUFFICIENT_STOCK`), saldo akhir tidak pernah negatif.',
                    ],
                ],
            ],

            'PRIMITIVE_TIME_SLOT_ENGINE' => [
                'deterministic_formulas' => [
                    [
                        'formula_code' => 'INTERVAL_OVERLAP_AND_DYNAMIC_PEAK_TARIFF',
                        'expression' => 'is_overlapping = (req_start_utc < existing_end_utc) AND (req_end_utc > existing_start_utc); slot_total = SUM_OVER_SLICES(round_half_up(slice_hours * base_hourly_rate * peak_or_weekend_multiplier))',
                        'order_of_precedence' => [
                            '1. Normalisasi seluruh input waktu dari timezone tenant ke `UTC` sebelum evaluasi irisan.',
                            '2. Kunci parent `bookable_resources` row dengan `SELECT ... FOR UPDATE`.',
                            '3. Pecah durasi lintas jam reguler vs jam puncak (peak hour / weekend) per segmen waktu, kalikan tarif masing-masing segmen dengan `DECIMAL(19,4)`, lalu jumlahkan.',
                        ],
                        'penny_allocation_rule' => 'Pembayaran DP persentase (misal DP 30% atau 50%) dibulatkan `HALF_UP` ke Rupiah terdekat, dan tagihan pelunasan wajib dihitung sebagai `pelunasan = grand_total - dp_settled` (0 selisih).',
                    ],
                ],
                'saga_and_webhook_contracts' => [
                    'transactional_outbox_events' => ['booking.slot.held.v1', 'booking.slot.confirmed.v1', 'booking.slot.expired.v1'],
                    'compensating_actions' => [
                        ['trigger' => 'HoldTtlExpiredBeforeSettlement', 'compensation' => 'ExpireHeldReservationAction', 'mechanism' => 'Releases time slot atomically; if late Midtrans settlement arrives afterward, triggers `ConvertLatePaymentToDepositCreditOrAutoRefundSaga`.'],
                    ],
                    'external_integrations' => [
                        ['provider' => 'Payment Gateway & Calendar/OTA Channel Sync', 'inbound_security' => 'HMAC-SHA256 / SHA-512 Signature + `webhook_inbound_logs`', 'outbound_resilience' => 'Async Outbox Relay + Backoff [10s, 60s, 300s, 1800s]'],
                    ],
                ],
                'ui_screen_archetypes' => [
                    [
                        'screen' => 'Resource × Time-Slot Availability & Gantt Matrix',
                        'archetype' => 'ARCHETYPE_TIMELINE_GANTT',
                        'layout_contract' => 'Frozen Left Resource Column (Rooms / Courts / Doctors / Fleet) + Horizontal 30-Min Time Grid + Live Hold Countdown Badge + Unit Housekeeping Status Chip (`CLEAN`/`DIRTY`/`MAINTENANCE`).',
                        'keyboard_shortcuts' => ['T' => 'Jump to Today', '[' => 'Previous Day', ']' => 'Next Day', 'ESC' => 'Cancel Slot Selection'],
                        'empty_state' => 'Calendar grid icon + "Belum ada unit/lapangan/ruangan terdaftar" + CTA "Tambah Unit Reservasi".',
                        'loading_state' => 'Time-grid matrix skeleton with sticky time header.',
                        'error_state' => 'Inline slot-collision alert highlighting conflicting time range in high-contrast amber/red.',
                    ],
                ],
                'acceptance_test_scenarios' => [
                    [
                        'id' => 'AT-SLOT-01',
                        'given' => 'Lapangan Badminton #1 sudah dibooking berstatus `CONFIRMED` pada pukul 19:00–21:00 WIB',
                        'when' => 'User lain mencoba memesan Lapangan #1 pada pukul 20:00–22:00 WIB (irisan 1 jam)',
                        'then' => 'Sistem menolak transaksi dengan HTTP `409 Conflict` RFC 9457 (`TIME_SLOT_OVERLAP`) tanpa membuat baris duplikat.',
                    ],
                ],
            ],

            'PRIMITIVE_DOUBLE_ENTRY_LEDGER' => [
                'deterministic_formulas' => [
                    [
                        'formula_code' => 'ZERO_SUM_DOUBLE_ENTRY_AND_INSTALLMENT_PENNY_RULE',
                        'expression' => 'SUM(journal_lines.debit_amount) - SUM(journal_lines.credit_amount) === 0.0000; installment_k = floor(total / n) for k in 1..(n-1), installment_n = total - SUM(installment_1..n-1)',
                        'order_of_precedence' => [
                            '1. Urutkan seluruh `account_id` yang disentuh secara leksikografis (`ORDER BY id ASC`) lalu kunci dengan `SELECT ... FOR UPDATE` (mencegah AB-BA deadlock).',
                            '2. Validasi minimal 2 baris jurnal, tidak ada baris dengan debit & kredit positif bersamaan, dan `SUM(debit) === SUM(credit)` tepat hingga 4 desimal.',
                            '3. Untuk cicilan (misal Rp 100.000 / 3 bulan), terapkan aturan penyerapan cicilan terakhir: `[Rp 33.333, Rp 33.333, Rp 33.334]`.',
                        ],
                        'penny_allocation_rule' => 'Sisa pembagian cicilan atau split-payment multi-pihak (`total - sum(rounded_shares)`) WAJIB diserap pada jadwal cicilan terakhir atau akun fee platform sehingga total debit dan kredit 100% seimbang tanpa selisih Rp 1.',
                    ],
                ],
                'saga_and_webhook_contracts' => [
                    'transactional_outbox_events' => ['ledger.journal.posted.v1', 'ledger.journal.reversed.v1', 'payment.settlement.reconciled.v1'],
                    'compensating_actions' => [
                        ['trigger' => 'DownstreamPayoutOrFulfillmentFailed', 'compensation' => 'PostContraReversalJournalAction', 'mechanism' => 'Posts an immutable contra-journal swapping debit/credit referencing `reverses_journal_id` (NEVER deletes/updates posted rows).'],
                    ],
                    'external_integrations' => [
                        ['provider' => 'Payment Gateway (Midtrans/Xendit) & Bank Statement Mutation Webhook', 'inbound_security' => 'Cryptographic Signature Verification + `webhook_inbound_logs` unique `(provider, provider_event_id)`', 'outbound_resilience' => 'Idempotency-Key header + Circuit Breaker + Backoff [10s, 60s, 300s, 1800s]'],
                    ],
                ],
                'ui_screen_archetypes' => [
                    [
                        'screen' => 'General Ledger, Trial Balance & Bank Reconciliation Workspace',
                        'archetype' => 'ARCHETYPE_SPLIT_LEDGER_AUDITOR',
                        'layout_contract' => 'Sticky Period Filter + Balanced Debit/Credit Summary Strip (`tabular-nums`) + Expandable Journal Voucher Rows + 1-Click Contra-Reversal Drawer.',
                        'keyboard_shortcuts' => ['ALT+J' => 'New Manual Journal', 'ALT+R' => 'Filter Unreconciled Mutations'],
                        'empty_state' => 'Ledger book icon + "Belum ada jurnal terposting pada periode fiskal ini" + CTA "Inisialisasi Chart of Accounts (CoA)".',
                        'loading_state' => 'Tabular numeric skeleton with right-aligned debit/credit placeholders.',
                        'error_state' => 'RFC 9457 `LEDGER_UNBALANCED_ENTRY` banner displaying exact Debit vs Credit delta.',
                    ],
                ],
                'acceptance_test_scenarios' => [
                    [
                        'id' => 'AT-LED-01',
                        'given' => 'Payload posting jurnal dengan Debit Rp 150.000,0000 dan Kredit Rp 149.999,0000',
                        'when' => 'Action `PostJournalEntryAction` dieksekusi',
                        'then' => 'Transaksi ditolak dengan HTTP `422` RFC 9457 (`LEDGER_UNBALANCED_ENTRY`) dan 0 baris tersimpan di `journal_entries` maupun `journal_lines`.',
                    ],
                    [
                        'id' => 'AT-LED-02',
                        'given' => 'Tagihan Rp 100.000 dipecah menjadi 3 termin cicilan bulanan',
                        'when' => 'Engine menjadwalkan 3 baris piutang cicilan',
                        'then' => 'Jadwal cicilan menghasilkan tepat `[33333.0000, 33333.0000, 33334.0000]` dengan total eksak `100000.0000`.',
                    ],
                ],
            ],

            'PRIMITIVE_BOM_WORK_ORDER' => [
                'deterministic_formulas' => [
                    [
                        'formula_code' => 'BOM_MATERIAL_YIELD_AND_UNIT_HPP_COGS',
                        'expression' => 'required_raw_qty = round_half_up(target_output_qty * bom_component_qty * (1 + scrap_allowance_rate)); unit_finished_hpp = round_half_up((total_raw_material_cost + direct_labor_cost + allocated_overhead_cost) / nullif(actual_good_yield_qty, 0))',
                        'order_of_precedence' => [
                            '1. Hitung kebutuhan bahan baku berdasarkan resep BoM aktif + toleransi susut (`scrap_allowance_rate`).',
                            '2. Kunci dan potong stok bahan baku ke akun `Work-In-Progress (WIP)` saat Work Order bertransisi ke `IN_PROGRESS`.',
                            '3. Saat `COMPLETED`, hitung HPP per unit barang jadi berdasarkan `actual_good_yield_qty` (bukan rencana awal) agar biaya scrap terserap akurat.',
                        ],
                        'penny_allocation_rule' => 'Selisih pembulatan pembagian total biaya WIP terhadap `actual_good_yield_qty` dicatat ke akun `Manufacturing Rounding Variance` sehingga nilai persediaan barang jadi cocok 100% dengan kredit WIP.',
                    ],
                ],
                'saga_and_webhook_contracts' => [
                    'transactional_outbox_events' => ['manufacturing.work_order.started.v1', 'manufacturing.work_order.completed.v1'],
                    'compensating_actions' => [
                        ['trigger' => 'WorkOrderAbortedMidProduction', 'compensation' => 'ReturnUnconsumedRawMaterialAndWriteOffScrapAction', 'mechanism' => 'Restores unconsumed raw stock and posts scrap variance journal atomically.'],
                    ],
                    'external_integrations' => [
                        ['provider' => 'IoT Shop-Floor PLC / Kitchen Printer Gateway', 'inbound_security' => 'Device Token + HMAC-SHA256', 'outbound_resilience' => 'Local Queue Buffer + Retry Backoff [10s, 60s, 300s, 1800s]'],
                    ],
                ],
                'ui_screen_archetypes' => [
                    [
                        'screen' => 'Production Work Order / Kitchen Display System (KDS) Board',
                        'archetype' => 'ARCHETYPE_KANBAN_STATE_BOARD',
                        'layout_contract' => '4 Deterministic State Columns (`PLANNED`, `MATERIAL_ISSUED`, `IN_PROGRESS`, `QC_INSPECTION`) + Elapsed SLA Timer (`tabular-nums`) + BoM Checklist Drawer.',
                        'keyboard_shortcuts' => ['SPACE' => 'Advance Selected Card State', '/' => 'Filter Work Order Number'],
                        'empty_state' => 'Factory/recipe icon + "Belum ada Work Order produksi aktif" + CTA "Buat Resep BoM / Work Order".',
                        'loading_state' => '4-column Kanban skeleton with 3 card placeholders per column.',
                        'error_state' => 'Missing raw-material shortage table inside modal before state transition.',
                    ],
                ],
                'acceptance_test_scenarios' => [
                    [
                        'id' => 'AT-BOM-01',
                        'given' => 'Work Order memproduksi 100 unit dengan salah satu bahan baku kurang dari kebutuhan BoM',
                        'when' => 'Operator menekan transisi `IssueMaterialsAction`',
                        'then' => 'Transisi diblokir deterministik dengan daftar defisit bahan baku dan tidak ada bahan baku lain yang terpotong parsial.',
                    ],
                ],
            ],

            'PRIMITIVE_APPROVAL_WORKFLOW' => [
                'deterministic_formulas' => [
                    [
                        'formula_code' => 'THRESHOLD_ROUTING_AND_QUORUM_EVALUATION',
                        'expression' => 'required_tier = MAX(tier WHERE amount >= tier.min_threshold); is_step_approved = (approvals_count >= step.quorum_required) AND (rejections_count === 0) AND (requester_id NOT IN approver_ids)',
                        'order_of_precedence' => [
                            '1. Evaluasi matriks batas nominal (`amount_threshold`) untuk menentukan jumlah level persetujuan.',
                            '2. Tegakkan `Segregation of Duties (SoD)`: pembuat permohonan (`requester_id`) DILARANG menyetujui dokumennya sendiri.',
                            '3. Bekukan snapshot payload (`SHA-256 payload_hash`) saat status berubah dari `DRAFT` ke `SUBMITTED`.',
                        ],
                        'penny_allocation_rule' => 'Perbandingan ambang batas otorisasi (`amount >= min_threshold`) wajib dievaluasi menggunakan nilai `DECIMAL(19,4)` penuh sebelum pembulatan tampilan.',
                    ],
                ],
                'saga_and_webhook_contracts' => [
                    'transactional_outbox_events' => ['workflow.approval.requested.v1', 'workflow.approval.granted.v1', 'workflow.approval.rejected.v1'],
                    'compensating_actions' => [
                        ['trigger' => 'FinalStepExecutionFailedAfterApproval', 'compensation' => 'ReopenApprovalStepWithFailureAuditAction', 'mechanism' => 'Marks execution failed without losing cryptographic approval signatures and alerts approver.'],
                    ],
                    'external_integrations' => [
                        ['provider' => 'WhatsApp / Email Actionable Approval Notification & e-Sign', 'inbound_security' => 'Signed Callback HMAC-SHA256 + `webhook_inbound_logs`', 'outbound_resilience' => 'Async Outbox + Exponential Backoff [10s, 60s, 300s, 1800s]'],
                    ],
                ],
                'ui_screen_archetypes' => [
                    [
                        'screen' => 'Multi-Stage Approval Inbox & SHA-256 Diff Inspector',
                        'archetype' => 'ARCHETYPE_MASTER_DETAIL_APPROVAL_DESK',
                        'layout_contract' => 'Left 4-Col Pending Queue sorted by SLA urgency; Right 8-Col Payload Snapshot, SHA-256 Seal Badge, Audit Timeline, and Approve/Reject Reason Form.',
                        'keyboard_shortcuts' => ['J' => 'Next Request', 'K' => 'Previous Request', 'A' => 'Open Approve Confirmation', 'R' => 'Open Mandatory Reason Reject Form'],
                        'empty_state' => 'Check-shield icon + "Tidak ada dokumen yang menunggu persetujuan Anda".',
                        'loading_state' => 'Split master-detail skeleton.',
                        'error_state' => 'RFC 9457 `SOD_SELF_APPROVAL_FORBIDDEN` banner if requester attempts self-approval.',
                    ],
                ],
                'acceptance_test_scenarios' => [
                    [
                        'id' => 'AT-APP-01',
                        'given' => 'User A mengajukan permohonan pencairan dana Rp 25.000.000 dan juga memiliki role Manager',
                        'when' => 'User A mencoba mengeksekusi `ApproveStepAction` atas permohonannya sendiri',
                        'then' => 'Sistem menolak dengan HTTP `403 Forbidden` RFC 9457 (`SOD_SELF_APPROVAL_FORBIDDEN`).',
                    ],
                ],
            ],

            'PRIMITIVE_SUBSCRIPTION_COHORT' => [
                'deterministic_formulas' => [
                    [
                        'formula_code' => 'PRORATED_PLAN_UPGRADE_AND_DUNNING_GRACE',
                        'expression' => 'remaining_ratio = remaining_seconds_in_cycle / total_seconds_in_cycle; unused_credit = round_half_up(current_plan_price * remaining_ratio); upgrade_charge = max(0, round_half_up(new_plan_price * remaining_ratio) - unused_credit)',
                        'order_of_precedence' => [
                            '1. Hitung rasio sisa detik siklus penagihan menggunakan timestamp `UTC`.',
                            '2. Hitung kredit sisa paket lama (`unused_credit`) lalu kurangkan dari biaya proporsional paket baru.',
                            '3. Jika `upgrade_charge == 0` atau negatif (downgrade), simpan selisih sebagai saldo kredit faktur periode berikutnya.',
                        ],
                        'penny_allocation_rule' => 'Pendapatan berlangganan tahunan yang diakui per bulan (PSAK 72 deferred revenue 12 bulan) menyerap sisa pembulatan Rupiah pada bulan ke-12.',
                    ],
                ],
                'saga_and_webhook_contracts' => [
                    'transactional_outbox_events' => ['subscription.renewed.v1', 'subscription.past_due.v1', 'subscription.suspended.v1'],
                    'compensating_actions' => [
                        ['trigger' => 'RecurringChargeDeclined', 'compensation' => 'TransitionToPastDueAndScheduleDunningSaga', 'mechanism' => 'Enters `PAST_DUE` grace period (e.g. 7 days) with scheduled retries at Day 1, 3, 7 before `SUSPENDED`.'],
                    ],
                    'external_integrations' => [
                        ['provider' => 'Recurring Billing Gateway (Midtrans/Xendit/Stripe)', 'inbound_security' => 'HMAC-SHA256 + `webhook_inbound_logs` idempotent `invoice_id`', 'outbound_resilience' => 'Circuit Breaker + Backoff [10s, 60s, 300s, 1800s]'],
                    ],
                ],
                'ui_screen_archetypes' => [
                    [
                        'screen' => 'Cohort / Membership Roster & Recurring Billing Console',
                        'archetype' => 'ARCHETYPE_COHORT_LIFECYCLE_MATRIX',
                        'layout_contract' => 'KPI Retention Strip (MRR, Active, Grace Period, Churned `tabular-nums`) + Keyset Member Table + Proration Preview Modal.',
                        'keyboard_shortcuts' => ['/' => 'Search Member / Student', 'ALT+P' => 'Simulate Proration'],
                        'empty_state' => 'Users badge icon + "Belum ada peserta/member aktif di paket ini" + CTA "Buka Pendaftaran Cohort".',
                        'loading_state' => '4 KPI card skeletons + 8-row member table skeleton.',
                        'error_state' => 'Prerequisite / quota-exceeded RFC 9457 notice.',
                    ],
                ],
                'acceptance_test_scenarios' => [
                    [
                        'id' => 'AT-SUB-01',
                        'given' => 'Member berada tepat di pertengahan siklus 30 hari paket Rp 100.000 dan melakukan upgrade ke paket Rp 300.000',
                        'when' => 'Kalkulator prorata menghitung tagihan upgrade hari itu',
                        'then' => 'Kredit sisa terhitung Rp 50.000, biaya paket baru setengah siklus Rp 150.000, dan tagihan bersih tepat Rp 100.000.',
                    ],
                ],
            ],

            'PRIMITIVE_CLINICAL_EMR_AUDIT' => [
                'deterministic_formulas' => [
                    [
                        'formula_code' => 'DOSAGE_DISPENSING_AND_FEFO_BATCH_ALLOCATION',
                        'expression' => 'total_dispense_units = ceil(dose_per_intake * frequency_per_day * duration_days); fefo_batches = ORDER BY expiry_date ASC WHERE expiry_date > CURRENT_DATE AND available_qty > 0',
                        'order_of_precedence' => [
                            '1. Abaikan seluruh batch obat yang sudah melewati `expiry_date <= CURRENT_DATE` (diblokir keras dari dispensing).',
                            '2. Alokasikan kuantitas resep secara berurutan dari batch dengan `expiry_date` paling dekat (`FEFO`) di bawah `SELECT ... FOR UPDATE`.',
                            '3. Segel rekam medis SOAP dengan `SHA-256 content_hash` saat dokter menandatangani (`SIGNED`); perubahan setelahnya WAJIB berupa baris `clinical_addendums` baru.',
                        ],
                        'penny_allocation_rule' => 'Pembagian tanggungan klaim penjamin/asuransi (co-pay pasien vs klaim asuransi) menyerap selisih pembulatan Rp 1 pada porsi klaim institusi agar tagihan tunai pasien bulat.',
                    ],
                ],
                'saga_and_webhook_contracts' => [
                    'transactional_outbox_events' => ['clinical.encounter.signed.v1', 'pharmacy.prescription.dispensed.v1'],
                    'compensating_actions' => [
                        ['trigger' => 'SatuSehatFhirSyncTemporaryOutage', 'compensation' => 'QueueFhirBundleOutboxRetryAction', 'mechanism' => 'Never blocks doctor UI; stores encrypted FHIR payload in `outbox_messages` and retries asynchronously until HTTP 200/201.'],
                    ],
                    'external_integrations' => [
                        ['provider' => 'Kemenkes SATUSEHAT FHIR HL7 / BPJS VClaim', 'inbound_security' => 'OAuth2 Client Credentials + HMAC Signature + `webhook_inbound_logs`', 'outbound_resilience' => 'Strict Async Outbox + Circuit Breaker + Backoff [10s, 60s, 300s, 1800s]'],
                    ],
                ],
                'ui_screen_archetypes' => [
                    [
                        'screen' => 'Doctor Clinical SOAP & FEFO e-Prescription Workspace',
                        'archetype' => 'ARCHETYPE_CLINICAL_SOAP_WORKSPACE',
                        'layout_contract' => 'Sticky Patient Allergy & Vital Banner (High-Contrast Red Allergy Alert) + Left Historical Encounter Timeline + Right Structured SOAP & FEFO Drug Picker.',
                        'keyboard_shortcuts' => ['ALT+S' => 'Focus Subjective/Objective', 'ALT+R' => 'Add e-Prescription Row', 'CTRL+ENTER' => 'Sign & Seal SOAP (Requires Confirmation Modal)'],
                        'empty_state' => 'Stethoscope icon + "Belum ada riwayat kunjungan klinis untuk pasien ini" + CTA "Mulai Pemeriksaan Baru".',
                        'loading_state' => 'Patient header skeleton + 4 SOAP section skeletons.',
                        'error_state' => 'High-contrast expired-batch or drug-allergy interaction warning banner.',
                    ],
                ],
                'acceptance_test_scenarios' => [
                    [
                        'id' => 'AT-EMR-01',
                        'given' => 'Catatan rekam medis SOAP sudah berstatus `SIGNED` dengan hash SHA-256 tercatat',
                        'when' => 'Permintaan `PUT/PATCH` atau `DELETE` mencoba mengubah teks diagnosis asli atau menghapus baris kunjungan',
                        'then' => 'Sistem menolak mutasi langsung (`409 Conflict` / `IMMUTABLE_LEDGER`) dan hanya mengizinkan penambahan `clinical_addendums` berjejak waktu & aktor.',
                    ],
                ],
            ],

            'PRIMITIVE_LOGISTICS_MANIFEST_POD' => [
                'deterministic_formulas' => [
                    [
                        'formula_code' => 'CHARGEABLE_WEIGHT_AND_FREIGHT_TARIFF',
                        'expression' => 'volumetric_kg = round_half_up((length_cm * width_cm * height_cm) / volumetric_divisor); chargeable_kg = max(actual_gross_kg, volumetric_kg, min_chargeable_kg); freight_cost = round_half_up(chargeable_kg * lane_rate_per_kg) + insurance_surcharge',
                        'order_of_precedence' => [
                            '1. Hitung berat volumetrik (`divisor = 6000` darat/laut atau `5000` ekspres udara).',
                            '2. Ambil nilai maksimum antara berat aktual timbangan vs berat volumetrik vs berat minimum rute.',
                            '3. Validasi total muatan manifest kendaraan (`SUM(actual_gross_kg) <= vehicle.max_payload_kg`) sebelum keberangkatan.',
                        ],
                        'penny_allocation_rule' => 'Biaya perjalanan gabungan (BBM/tol satu truk) yang dibebankan ke N resi di dalam satu manifest dialokasikan proporsional berdasarkan `chargeable_kg`, dengan selisih Rp 1 diserap oleh resi terberat.',
                    ],
                ],
                'saga_and_webhook_contracts' => [
                    'transactional_outbox_events' => ['logistics.waybill.dispatched.v1', 'logistics.waybill.pod_verified.v1', 'logistics.cod.collected.v1'],
                    'compensating_actions' => [
                        ['trigger' => 'DeliveryFailedMaxAttemptsReached', 'compensation' => 'InitiateReturnToSenderManifestSaga', 'mechanism' => 'Transitions waybill to `RETURN_IN_TRANSIT` and holds COD escrow settlement automatically.'],
                    ],
                    'external_integrations' => [
                        ['provider' => '3PL Courier Webhook / GPS Fleet Telemetry', 'inbound_security' => 'HMAC-SHA256 + `webhook_inbound_logs` timestamp & event deduplication', 'outbound_resilience' => 'Async Outbox + Backoff [10s, 60s, 300s, 1800s]'],
                    ],
                ],
                'ui_screen_archetypes' => [
                    [
                        'screen' => 'Dispatch Control Tower, Manifest Builder & e-POD Verifier',
                        'archetype' => 'ARCHETYPE_DISPATCH_CONTROL_SPLIT_VIEW',
                        'layout_contract' => 'Left 7-Col Active Manifest & Waybill Cursor Table with Vehicle Payload Meter; Right 5-Col Checkpoint Timeline & Geotagged Photo/OTP e-POD Inspector.',
                        'keyboard_shortcuts' => ['/' => 'Scan AWB / Waybill Barcode', 'ALT+M' => 'Seal & Dispatch Manifest'],
                        'empty_state' => 'Truck icon + "Belum ada resi/manifest aktif untuk rute ini" + CTA "Terbitkan Resi / Waybill Baru".',
                        'loading_state' => 'Manifest table + timeline skeleton.',
                        'error_state' => 'Over-capacity payload warning (`VEHICLE_MAX_PAYLOAD_EXCEEDED`).',
                    ],
                ],
                'acceptance_test_scenarios' => [
                    [
                        'id' => 'AT-LOG-01',
                        'given' => 'Paket memiliki berat timbangan 2,0 kg namun dimensi kotak 60x40x30 cm (volumetrik darat = 12,0 kg)',
                        'when' => 'Tarif pengiriman rute Rp 10.000/kg dihitung',
                        'then' => '`chargeable_kg` ditetapkan 12.0000 kg dan biaya kirim terhitung eksak Rp 120.000.',
                    ],
                ],
            ],

            'PRIMITIVE_HIGH_CONCURRENCY_TICKETING' => [
                'deterministic_formulas' => [
                    [
                        'formula_code' => 'ATOMIC_SEAT_QUOTA_AND_PLATFORM_FEE',
                        'expression' => 'remaining_quota = section_capacity - (confirmed_seats + active_unexpired_held_seats); buyer_total = ticket_face_value + round_half_up(ticket_face_value * platform_fee_rate) + entertainment_tax',
                        'order_of_precedence' => [
                            '1. Validasi batas pembelian per identitas (`existing_user_tickets + requested_qty <= max_tickets_per_identity`).',
                            '2. Kunci kursi spesifik (`seat_id`) atau kuota festival dengan `SELECT ... FOR UPDATE SKIP LOCKED` (untuk auto-assign kursi terbaik tanpa antrean saling blokir).',
                            '3. Set `hold_expires_at = NOW_UTC + 600 seconds` dan terbitkan QR dinamis ber-HMAC hanya setelah status `PAID`.',
                        ],
                        'penny_allocation_rule' => 'Pembagian revenue share antara Promotor dan Platform Ticketing menyerap selisih pembulatan Rp 1 pada porsi fee platform agar laporan pencairan promotor cocok dengan nilai lembar tiket.',
                    ],
                ],
                'saga_and_webhook_contracts' => [
                    'transactional_outbox_events' => ['ticketing.seat.locked.v1', 'ticketing.ticket.issued.v1', 'ticketing.gate.checked_in.v1'],
                    'compensating_actions' => [
                        ['trigger' => 'CheckoutHold600sExpired', 'compensation' => 'ReclaimSeatLockToAvailablePoolAction', 'mechanism' => 'Releases seat atomically; duplicate/late payment triggers automatic refund via Outbox Saga.'],
                    ],
                    'external_integrations' => [
                        ['provider' => 'High-Throughput QRIS / Virtual Account Payment Webhook', 'inbound_security' => 'HMAC-SHA256 + `webhook_inbound_logs`', 'outbound_resilience' => 'Non-blocking 202 Accepted (<30ms) + Queue Worker + Backoff [10s, 60s, 300s, 1800s]'],
                    ],
                ],
                'ui_screen_archetypes' => [
                    [
                        'screen' => 'High-Concurrency Seat Map, Hold Timer & Gate Scanner Console',
                        'archetype' => 'ARCHETYPE_HIGH_CONCURRENCY_QUEUE_GATE',
                        'layout_contract' => 'Top Sticky 10:00 Hold Countdown Bar (`tabular-nums`) + Interactive Section/Seat Grid (Solid High-Contrast States: Available, Held, Sold) + Sticky Checkout Summary.',
                        'keyboard_shortcuts' => ['ESC' => 'Release Selected Unpaid Seat', 'ENTER' => 'Lock Seat & Pay'],
                        'empty_state' => 'Ticket icon + "Seluruh kuota sesi ini belum dibuka atau telah habis" + CTA "Daftar Waiting List Resmi".',
                        'loading_state' => 'Seat-block matrix skeleton.',
                        'error_state' => 'Instant non-blocking toast `SEAT_ALREADY_TAKEN` auto-refreshing available seats.',
                    ],
                ],
                'acceptance_test_scenarios' => [
                    [
                        'id' => 'AT-TIX-01',
                        'given' => 'Sebuah tiket berstatus `PAID` dengan kode QR valid sudah di-scan masuk (`CHECKED_IN` pukul 18:42:10) di Gate A',
                        'when' => 'Kode QR yang sama di-scan ulang 3 detik kemudian di Gate B',
                        'then' => 'Gate B menolak dengan HTTP `409 Conflict` RFC 9457 (`TICKET_ALREADY_REDEEMED`) sambil menampilkan timestamp & lokasi scan pertama.',
                    ],
                ],
            ],

            'PRIMITIVE_HRIS_PAYROLL_ROSTER' => [
                'deterministic_formulas' => [
                    [
                        'formula_code' => 'OVERTIME_BPJS_AND_TER_PPH21_NET_PAY',
                        'expression' => 'hourly_base = round_half_up(monthly_base_salary / 173); overtime_pay = round_half_up((first_ot_hours * 1.5 + subsequent_ot_hours * 2.0) * hourly_base); gross_income = monthly_base_salary + fixed_allowances + overtime_pay; net_take_home = gross_income - employee_bpjs_deduction - round_half_up(gross_income * ter_pph21_rate) - unpaid_leave_deduction',
                        'order_of_precedence' => [
                            '1. Hitung upah sejam lembur sesuai regulasi ketenagakerjaan (`1/173 * gaji pokok + tunjangan tetap`).',
                            '2. Hitung iuran BPJS Kesehatan & Ketenagakerjaan porsi perusahaan vs porsi potongan karyawan dengan batas plafon upah resmi.',
                            '3. Terapkan tarif efektif rata-rata (`TER PPh 21` Kategori A/B/C sesuai status PTKP) dan kunci slip gaji (`LOCKED`) sebelum pencairan bank.',
                        ],
                        'penny_allocation_rule' => 'Rekonsiliasi PPh 21 masa pajak terakhir (Desember) menghitung kewajiban pajak setahun penuh dikurangi akumulasi potongan Januari–November sehingga 0 selisih pembulatan tahunan.',
                    ],
                ],
                'saga_and_webhook_contracts' => [
                    'transactional_outbox_events' => ['hris.payroll.locked.v1', 'hris.payroll.disbursed.v1'],
                    'compensating_actions' => [
                        ['trigger' => 'BankBatchPayrollTransferPartialReject', 'compensation' => 'MarkIndividualPayslipDisbursementRetryAndHoldLedgerAction', 'mechanism' => 'Isolates rejected employee bank account for account-fix retry without re-sending already-paid employees.'],
                    ],
                    'external_integrations' => [
                        ['provider' => 'Corporate Bank Host-to-Host Payroll & Biometric Attendance Webhook', 'inbound_security' => 'HMAC-SHA256 + `webhook_inbound_logs`', 'outbound_resilience' => 'Idempotent Batch File/API + Backoff [10s, 60s, 300s, 1800s]'],
                    ],
                ],
                'ui_screen_archetypes' => [
                    [
                        'screen' => 'Shift Roster Matrix & Payroll Run Verification Console',
                        'archetype' => 'ARCHETYPE_ROSTER_AND_PAYROLL_LEDGER',
                        'layout_contract' => 'Sticky Payroll Totals Header (Gross, BPJS, PPh 21, Net Take-Home `tabular-nums`) + Frozen Employee Name Column + Variance Highlight vs Previous Month.',
                        'keyboard_shortcuts' => ['/' => 'Search Employee NIK / Name', 'ALT+L' => 'Lock Verified Payroll Period'],
                        'empty_state' => 'Briefcase icon + "Belum ada periode penggajian dibuat untuk bulan ini" + CTA "Generate Draft Payroll".',
                        'loading_state' => 'Summary KPI skeleton + 12-row payroll table skeleton.',
                        'error_state' => 'Unverified attendance / missing bank account blocker list before payroll lock.',
                    ],
                ],
                'acceptance_test_scenarios' => [
                    [
                        'id' => 'AT-HR-01',
                        'given' => 'Karyawan memiliki gaji pokok + tunjangan tetap Rp 17.300.000 (upah lembur sejam = Rp 100.000) dan lembur hari kerja 3 jam (1 jam x 1,5 + 2 jam x 2,0 = 5,5x)',
                        'when' => 'Engine payroll menghitung komponen lembur periode tersebut',
                        'then' => 'Upah lembur terhitung eksak `Rp 550.000,0000` dan setelah periode `LOCKED`, perubahan presensi mundur ditolak deterministik.',
                    ],
                ],
            ],

            'PRIMITIVE_FUND_DISBURSEMENT_IMPACT' => [
                'deterministic_formulas' => [
                    [
                        'formula_code' => 'RESTRICTED_FUND_CEILING_AND_DISBURSEMENT_VARIANCE',
                        'expression' => 'remaining_budget_ceiling = approved_program_budget - (disbursed_settled_total + pending_committed_requests); operational_fee_cap_valid = (admin_allocation_rate <= statutory_or_donor_max_cap_rate)',
                        'order_of_precedence' => [
                            '1. Kunci baris mata anggaran program (`program_budget_line_id`) dengan `SELECT ... FOR UPDATE`.',
                            '2. Validasi `requested_amount <= remaining_budget_ceiling` dan pastikan dana terikat (`RESTRICTED_FUND`) tidak dipakai lintas peruntukan program.',
                            '3. Posting otomatis jurnal akuntansi dana (ISAK 35 / PSAK Nirlaba) ke `PRIMITIVE_DOUBLE_ENTRY_LEDGER` saat pencairan diselesaikan.',
                        ],
                        'penny_allocation_rule' => 'Bantuan tunai atau subsidi yang dibagi merata ke N penerima manfaat menyerap sisa pembulatan pada pos cadangan program (tidak pernah melebihi pagu anggaran).',
                    ],
                ],
                'saga_and_webhook_contracts' => [
                    'transactional_outbox_events' => ['govtech.fund.committed.v1', 'govtech.fund.disbursed.v1', 'govtech.impact.verified.v1'],
                    'compensating_actions' => [
                        ['trigger' => 'BeneficiaryDisbursementAccountInvalid', 'compensation' => 'RestoreCommittedBudgetCeilingAndReverseJournalAction', 'mechanism' => 'Restores committed ceiling via contra-journal so program funds remain 100% auditable.'],
                    ],
                    'external_integrations' => [
                        ['provider' => 'Payment Gateway / Bank Disbursement & Public Transparency Verification', 'inbound_security' => 'HMAC-SHA256 + `webhook_inbound_logs`', 'outbound_resilience' => 'Idempotency-Key + Backoff [10s, 60s, 300s, 1800s]'],
                    ],
                ],
                'ui_screen_archetypes' => [
                    [
                        'screen' => 'Program Budget Ceiling Tracker & Public Impact Audit Ledger',
                        'archetype' => 'ARCHETYPE_FUND_TRANSPARENCY_LEDGER',
                        'layout_contract' => 'Restricted vs Unrestricted Fund KPI Cards (`tabular-nums`) + Budget Absorption Progress Bar (Solid Sharp Fill) + Receipt Evidence & Impact Milestone Table.',
                        'keyboard_shortcuts' => ['/' => 'Search Program / Beneficiary ID', 'ALT+D' => 'Submit Disbursement Request'],
                        'empty_state' => 'Shield-ledger icon + "Belum ada mata anggaran program aktif" + CTA "Buat Pagu Program & Dana".',
                        'loading_state' => '3 fund cards + 8-row disbursement ledger skeleton.',
                        'error_state' => 'RFC 9457 `BUDGET_CEILING_EXCEEDED` or `RESTRICTED_FUND_MISMATCH` alert.',
                    ],
                ],
                'acceptance_test_scenarios' => [
                    [
                        'id' => 'AT-GOV-01',
                        'given' => 'Pagu anggaran Dana Pendidikan Terikat tersisa Rp 5.000.000',
                        'when' => 'Pengajuan pencairan sebesar Rp 5.000.001 atau pengajuan untuk kategori non-pendidikan dikirim',
                        'then' => 'Sistem menolak dengan HTTP `422` RFC 9457 (`BUDGET_CEILING_EXCEEDED` / `RESTRICTED_FUND_VIOLATION`) dan saldo pagu tetap utuh.',
                    ],
                ],
            ],
        ];
    }
}
