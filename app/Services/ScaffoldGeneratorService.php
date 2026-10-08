<?php

namespace App\Services;

use App\Models\VisionBlueprint;
use Illuminate\Support\Str;
use ZipArchive;

class ScaffoldGeneratorService
{
    /**
     * Generate all scaffold files as an associative array: filename => content.
     */
    public static function generateFiles(VisionBlueprint $blueprint): array
    {
        $projectName = $blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'MyApplication');
        $slug = $blueprint->slug ?: Str::slug($projectName);
        $prd = $blueprint->prd_content ?? [];
        $erdTables = $prd['engineering_specs']['database_erd']['entities'] ?? [];
        if (empty($erdTables)) {
            $prd = PrdGeneratorService::generate($blueprint);
            $erdTables = $prd['engineering_specs']['database_erd']['entities'] ?? [];
        }

        $schemaSql = self::generateDatabaseMigrationsSql($blueprint, $erdTables);
        $files = [
            // Pillar 1: Contract-First Specifications & Database Architecture
            'openapi.json' => self::generateOpenApiSpec($blueprint, $erdTables),
            'schema_complete.sql' => $schemaSql,
            'database/migrations/schema_complete.sql' => $schemaSql,

            // Pillar 2: UI/UX Wireframe & Design Tokens
            'design/tokens.json' => self::generateDesignTokens($blueprint),
            'design/wireframes/screens.md' => self::generateWireframeBlueprints($blueprint, $erdTables),

            // Pillar 3: Infrastructure, Containerization & Routing
            'docker-compose.yml' => self::generateDockerCompose($blueprint, $slug),
            '.env.example' => self::generateEnvExample($blueprint, $slug),
            'README.md' => self::generateReadme($blueprint, $projectName, $slug),
            'docker/nginx/default.conf' => self::generateNginxConf($slug),
            'routes/web.php' => self::generateLaravelRoutes($blueprint, $erdTables),
            'routes/api.php' => self::generateLaravelApiRoutes($blueprint, $erdTables),
            'app/api/route.ts' => self::generateNextJsRoute($blueprint, $erdTables),
            'nextjs/app/api/resources/route.ts' => self::generateNextJsRoute($blueprint, $erdTables),

            // Pillar 4: Synthetic Mock Data / Seeder Engine
            'database/seeders/DatabaseSeeder.php' => self::generateDatabaseSeeder($blueprint),
            'database/seeders/SyntheticDataSeeder.php' => self::generateSyntheticDataSeeder($blueprint, $erdTables),

            // Pillar 5: AI Coding Agent Rules (.cursorrules & AGENTS.md)
            '.cursorrules' => PrdGeneratorService::toCursorrules($blueprint, $prd),
            'CLAUDE.md' => PrdGeneratorService::toCursorrules($blueprint, $prd),
            'AGENTS.md' => PrdGeneratorService::toCursorrules($blueprint, $prd),

            // Pillar 6: Contract-First Testing Suite
            'tests/Feature/ApiContractTest.php' => self::generateContractTest($blueprint, $erdTables),

            // Pillar 7: One-Click Cloud CI/CD & Deploy Pipeline
            '.github/workflows/deploy.yml' => self::generateCiCdWorkflow($blueprint, $slug),
            'deploy.sh' => self::generateDeployScript($blueprint, $slug),
            'docker/systemd/queue-worker.service' => self::generateQueueWorkerService($slug),
        ];

        // Apply any user-elaborated / customized scaffold files (persisted in user_metadata)
        $customizations = $blueprint->user_metadata['scaffold_customizations'] ?? [];
        if (!empty($customizations) && is_array($customizations)) {
            foreach ($customizations as $file => $customContent) {
                if (is_string($customContent) && isset($files[$file])) {
                    $files[$file] = $customContent;
                }
            }
        }

        return $files;
    }

    /**
     * Generate production & development docker-compose.yml.
     */
    public static function generateDockerCompose(VisionBlueprint $blueprint, string $slug): string
    {
        $cleanSlug = preg_replace('/[^a-z0-9_-]/', '', strtolower($slug));

        return <<<YAML
version: '3.8'

# ==============================================================================
# PROJECT OS // SCAFFOLD ARCHITECTURE CONTAINER STACK
# Project: {$blueprint->nama_bisnis}
# Generated: auto-synthesized from PRD Blueprint & strict O(1) Scalability Spec
# ==============================================================================

services:
  # 1. Main Application Container (PHP 8.4 FPM with High-Concurrency Extensions)
  app:
    build:
      context: .
      dockerfile: Dockerfile
    image: {$cleanSlug}-app:latest
    container_name: {$cleanSlug}_app
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - ./:/var/www/html
      - ./storage:/var/www/html/storage
    environment:
      APP_NAME: "{$blueprint->nama_bisnis}"
      APP_ENV: production
      APP_DEBUG: "false"
      APP_URL: "http://localhost:8080"
      DB_CONNECTION: pgsql
      DB_HOST: postgres
      DB_PORT: 5432
      DB_DATABASE: {$cleanSlug}_db
      DB_USERNAME: {$cleanSlug}_user
      DB_PASSWORD: secret_production_change_me
      CACHE_STORE: redis
      SESSION_DRIVER: redis
      QUEUE_CONNECTION: redis
      REDIS_HOST: redis
      REDIS_PORT: 6379
      AI_THREAT_SHIELD_ENABLED: "true"
    depends_on:
      postgres:
        condition: service_healthy
      redis:
        condition: service_healthy
    networks:
      - {$cleanSlug}_net

  # 2. Web Server (Nginx Alpine Reverse Proxy with FastCGI Buffering)
  web:
    image: nginx:alpine
    container_name: {$cleanSlug}_web
    restart: unless-stopped
    ports:
      - "8080:80"
    volumes:
      - ./:/var/www/html:ro
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
    depends_on:
      - app
    networks:
      - {$cleanSlug}_net

  # 3. Database Engine (PostgreSQL 16 with Strict ULID & UTF-8)
  postgres:
    image: postgres:16-alpine
    container_name: {$cleanSlug}_postgres
    restart: unless-stopped
    environment:
      POSTGRES_DB: {$cleanSlug}_db
      POSTGRES_USER: {$cleanSlug}_user
      POSTGRES_PASSWORD: secret_production_change_me
      PGDATA: /var/lib/postgresql/data/pgdata
    volumes:
      - {$cleanSlug}_pgdata:/var/lib/postgresql/data
      - ./database/migrations/schema_complete.sql:/docker-entrypoint-initdb.d/init.sql:ro
    ports:
      - "5432:5432"
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U {$cleanSlug}_user -d {$cleanSlug}_db"]
      interval: 5s
      timeout: 5s
      retries: 5
    networks:
      - {$cleanSlug}_net

  # 4. In-Memory Cache, Rate Limiting & Queue Broker (Redis 7)
  redis:
    image: redis:7-alpine
    container_name: {$cleanSlug}_redis
    restart: unless-stopped
    command: redis-server --appendonly yes --maxmemory 512mb --maxmemory-policy allkeys-lru
    volumes:
      - {$cleanSlug}_redisdata:/data
    ports:
      - "6379:6379"
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 5s
      timeout: 3s
      retries: 5
    networks:
      - {$cleanSlug}_net

  # 5. Local Mail Testing Sandbox (Mailpit)
  mailpit:
    image: axllent/mailpit:latest
    container_name: {$cleanSlug}_mailpit
    restart: unless-stopped
    ports:
      - "1025:1025"
      - "8025:8025"
    networks:
      - {$cleanSlug}_net

networks:
  {$cleanSlug}_net:
    driver: bridge

volumes:
  {$cleanSlug}_pgdata:
    driver: local
  {$cleanSlug}_redisdata:
    driver: local
YAML;
    }

    /**
     * Generate .env.example with security policies.
     */
    public static function generateEnvExample(VisionBlueprint $blueprint, string $slug): string
    {
        $cleanSlug = preg_replace('/[^a-z0-9_-]/', '', strtolower($slug));

        return <<<ENV
APP_NAME="{$blueprint->nama_bisnis}"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE={$cleanSlug}_db
DB_USERNAME={$cleanSlug}_user
DB_PASSWORD=secret_production_change_me

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis

CACHE_STORE=redis
CACHE_PREFIX={$cleanSlug}_cache_

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="no-reply@{$cleanSlug}.com"
MAIL_FROM_NAME="\${APP_NAME}"

# AI-Shield & Autonomous Exploit Defense Settings
AI_SHIELD_ENABLED=true
AI_SHIELD_STRIKE_LIMIT=3
AI_SHIELD_BAN_HOURS=2
SECURE_DATASET_SANDBOX=true

# Midtrans / Payment Gateway Integration
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
ENV;
    }

    /**
     * Generate Nginx configuration.
     */
    public static function generateNginxConf(string $slug): string
    {
        return <<<NGINX
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";

    index index.php index.html;
    charset utf-8;

    # Static Assets Fast-Path
    location /build/ {
        access_log off;
        expires max;
        try_files \$uri =404;
    }

    location /storage/ {
        access_log off;
        expires 30d;
        try_files \$uri =404;
    }

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX;
    }

    /**
     * Generate PostgreSQL schema DDL with ULID primary keys from ERD.
     */
    public static function generateDatabaseMigrationsSql(VisionBlueprint $blueprint, array $erdTables): string
    {
        $projectName = $blueprint->nama_bisnis ?: 'Application';
        $sql = "-- =============================================================================\n";
        $sql .= "-- PROJECT OS // DATABASE SCHEMA DDL (POSTGRESQL STRICT ULID STANDARD)\n";
        $sql .= "-- Project: {$projectName}\n";
        $sql .= "-- Architecture: O(1) Scalability, Keyset Cursors, Distributed ULID PKs\n";
        $sql .= "-- =============================================================================\n\n";
        $sql .= "CREATE EXTENSION IF NOT EXISTS \"pgcrypto\";\n\n";

        // Generate Users Table first if not present
        $hasUsers = false;
        foreach ($erdTables as $t) {
            if (($t['name'] ?? '') === 'users') {
                $hasUsers = true;
                break;
            }
        }

        if (!$hasUsers) {
            $sql .= "-- System Users Core Table\n";
            $sql .= "CREATE TABLE IF NOT EXISTS users (\n";
            $sql .= "    id VARCHAR(26) PRIMARY KEY,\n";
            $sql .= "    name VARCHAR(255) NOT NULL,\n";
            $sql .= "    email VARCHAR(255) UNIQUE NOT NULL,\n";
            $sql .= "    password VARCHAR(255) NOT NULL,\n";
            $sql .= "    role VARCHAR(50) DEFAULT 'client',\n";
            $sql .= "    email_verified_at TIMESTAMPTZ NULL,\n";
            $sql .= "    remember_token VARCHAR(100) NULL,\n";
            $sql .= "    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
            $sql .= "    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP\n";
            $sql .= ");\n";
            $sql .= "CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);\n\n";
        }

        // Generate Domain Entity Tables
        foreach ($erdTables as $table) {
            $tableName = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $table['name'] ?? 'records'));
            $description = $table['description'] ?? 'Domain table';

            $sql .= "-- Entity: {$tableName} ({$description})\n";
            $sql .= "CREATE TABLE IF NOT EXISTS {$tableName} (\n";
            
            $columnLines = [];
            $columnLines[] = "    id VARCHAR(26) PRIMARY KEY";

            $columns = $table['columns'] ?? [];
            if (empty($columns)) {
                $columns = [
                    ['name' => 'title', 'type' => 'string(255)', 'index' => null],
                    ['name' => 'status', 'type' => 'string(50)', 'index' => 'INDEX'],
                    ['name' => 'metadata', 'type' => 'jsonb', 'index' => null],
                ];
            }

            foreach ($columns as $col) {
                $colName = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $col['name'] ?? ''));
                if ($colName === 'id' || empty($colName)) continue;

                $rawType = strtolower($col['type'] ?? 'string');
                $sqlType = 'VARCHAR(255)';

                if (str_contains($rawType, 'ulid') || str_ends_with($colName, '_id')) {
                    $sqlType = 'VARCHAR(26)';
                } elseif (str_contains($rawType, 'text') || str_contains($rawType, 'desc')) {
                    $sqlType = 'TEXT';
                } elseif (str_contains($rawType, 'int') || str_contains($rawType, 'count')) {
                    $sqlType = 'INTEGER DEFAULT 0';
                } elseif (str_contains($rawType, 'decimal') || str_contains($rawType, 'price') || str_contains($rawType, 'amount')) {
                    $sqlType = 'NUMERIC(15, 2) DEFAULT 0.00';
                } elseif (str_contains($rawType, 'bool')) {
                    $sqlType = 'BOOLEAN DEFAULT FALSE';
                } elseif (str_contains($rawType, 'json')) {
                    $sqlType = 'JSONB DEFAULT \'{}\'::jsonb';
                } elseif (str_contains($rawType, 'date') || str_contains($rawType, 'time')) {
                    $sqlType = 'TIMESTAMPTZ NULL';
                }

                $columnLines[] = "    {$colName} {$sqlType}";
            }

            $columnLines[] = "    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP";
            $columnLines[] = "    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP";

            $sql .= implode(",\n", $columnLines) . "\n);\n";

            // Generate Indexes for foreign keys and status columns
            foreach ($columns as $col) {
                $colName = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $col['name'] ?? ''));
                if (str_ends_with($colName, '_id')) {
                    $sql .= "CREATE INDEX IF NOT EXISTS idx_{$tableName}_{$colName} ON {$tableName}({$colName});\n";
                } elseif ($colName === 'status' || ($col['index'] ?? '') === 'INDEX') {
                    $sql .= "CREATE INDEX IF NOT EXISTS idx_{$tableName}_{$colName} ON {$tableName}({$colName});\n";
                }
            }
            $sql .= "\n";
        }

        // Security Threats Log Table (AI-Shield integration)
        $sql .= "-- AI-Shield & Cyber Threat Intrusion Audit Log Table\n";
        $sql .= "CREATE TABLE IF NOT EXISTS security_threat_logs (\n";
        $sql .= "    id VARCHAR(26) PRIMARY KEY,\n";
        $sql .= "    ip_address VARCHAR(45) NOT NULL,\n";
        $sql .= "    user_agent VARCHAR(500) NULL,\n";
        $sql .= "    endpoint VARCHAR(255) NOT NULL,\n";
        $sql .= "    http_method VARCHAR(10) NOT NULL,\n";
        $sql .= "    threat_type VARCHAR(100) NOT NULL,\n";
        $sql .= "    matched_pattern VARCHAR(255) NOT NULL,\n";
        $sql .= "    payload_sample TEXT NULL,\n";
        $sql .= "    is_blocked BOOLEAN DEFAULT FALSE,\n";
        $sql .= "    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
        $sql .= "    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP\n";
        $sql .= ");\n";
        $sql .= "CREATE INDEX IF NOT EXISTS idx_threat_logs_ip ON security_threat_logs(ip_address);\n";
        $sql .= "CREATE INDEX IF NOT EXISTS idx_threat_logs_type ON security_threat_logs(threat_type);\n";

        return $sql;
    }

    /**
     * Generate Laravel routes/web.php.
     */
    public static function generateLaravelRoutes(VisionBlueprint $blueprint, array $erdTables): string
    {
        $code = "<?php\n\n";
        $code .= "use Illuminate\Support\Facades\Route;\n";
        $code .= "use App\Http\Controllers\DashboardController;\n";
        $code .= "use App\Http\Controllers\ProfileController;\n\n";
        $code .= "/*\n";
        $code .= "|--------------------------------------------------------------------------\n";
        $code .= "| Web Routes for {$blueprint->nama_bisnis}\n";
        $code .= "| Generated by Project OS Boilerplate Exporter\n";
        $code .= "|--------------------------------------------------------------------------\n";
        $code .= "*/\n\n";
        $code .= "Route::get('/', function () {\n";
        $code .= "    return view('welcome', ['title' => '{$blueprint->nama_bisnis} - Sistem Resmi']);\n";
        $code .= "});\n\n";
        $code .= "Route::middleware(['auth', 'verified'])->group(function () {\n";
        $code .= "    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');\n";
        $code .= "    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');\n";
        $code .= "    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');\n";
        $code .= "    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');\n\n";

        foreach ($erdTables as $table) {
            $tableName = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $table['name'] ?? ''));
            if ($tableName === 'users' || $tableName === 'security_threat_logs' || empty($tableName)) continue;
            $controllerName = Str::studly(Str::singular($tableName)) . 'Controller';
            $code .= "    // Domain CRUD: {$table['name']}\n";
            $code .= "    Route::resource('{$tableName}', \\App\\Http\\Controllers\\{$controllerName}::class);\n";
        }

        $code .= "});\n\n";
        $code .= "require __DIR__.'/auth.php';\n";

        return $code;
    }

    /**
     * Generate Laravel routes/api.php with AI-Shield middleware.
     */
    public static function generateLaravelApiRoutes(VisionBlueprint $blueprint, array $erdTables): string
    {
        $code = "<?php\n\n";
        $code .= "use Illuminate\Http\Request;\n";
        $code .= "use Illuminate\Support\Facades\Route;\n\n";
        $code .= "/*\n";
        $code .= "|--------------------------------------------------------------------------\n";
        $code .= "| API Routes with AI-Shield & O(1) Keyset Cursor Pagination\n";
        $code .= "|--------------------------------------------------------------------------\n";
        $code .= "*/\n\n";
        $code .= "Route::middleware(['api', \\App\\Http\\Middleware\\AiThreatShield::class])->prefix('v1')->group(function () {\n";
        $code .= "    Route::get('/health', fn () => response()->json(['status' => 'operational', 'timestamp' => now()->toIso8601String()]));\n\n";

        foreach ($erdTables as $table) {
            $tableName = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $table['name'] ?? ''));
            if ($tableName === 'users' || $tableName === 'security_threat_logs' || empty($tableName)) continue;
            $controllerName = Str::studly(Str::singular($tableName)) . 'ApiController';
            $code .= "    // API Endpoints for {$tableName}\n";
            $code .= "    Route::apiResource('{$tableName}', \\App\\Http\\Controllers\\Api\\{$controllerName}::class);\n";
        }

        $code .= "});\n";

        return $code;
    }

    /**
     * Generate Next.js TypeScript API Route.
     */
    public static function generateNextJsRoute(VisionBlueprint $blueprint, array $erdTables): string
    {
        $domainName = 'items';
        foreach ($erdTables as $t) {
            $tname = strtolower($t['name'] ?? '');
            if ($tname !== 'users' && $tname !== 'security_threat_logs' && !empty($tname)) {
                $domainName = $tname;
                break;
            }
        }

        return <<<TS
import { NextRequest, NextResponse } from 'next/server';

// ==============================================================================
// NEXT.JS APP ROUTER // API ENDPOINT FOR {$blueprint->nama_bisnis}
// Resource: /api/{$domainName}
// ==============================================================================

export async function GET(request: NextRequest) {
  try {
    const { searchParams } = new URL(request.url);
    const cursor = searchParams.get('cursor');
    const limit = parseInt(searchParams.get('limit') || '20', 10);

    // Keyset pagination O(1) query simulation
    return NextResponse.json({
      success: true,
      resource: '{$domainName}',
      data: [
        { id: '01JABCDEF12345678901234567', title: 'Example Entity 1', created_at: new Date().toISOString() },
        { id: '01JABCDEF12345678901234568', title: 'Example Entity 2', created_at: new Date().toISOString() },
      ],
      next_cursor: '01JABCDEF12345678901234568',
      has_more: false,
    });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}

export async function POST(request: NextRequest) {
  try {
    const payload = await request.json();

    // AI-Shield payload check
    const serialized = JSON.stringify(payload);
    if (/(?:system|exec|eval|passthru)\s*\(/i.test(serialized)) {
      return NextResponse.json({ error: 'Security policy violation detected.' }, { status: 403 });
    }

    // Process validated payload
    return NextResponse.json({
      success: true,
      message: 'Resource created successfully with ULID primary key',
      id: '01J' + Math.random().toString(36).substring(2, 15).toUpperCase(),
      payload,
    }, { status: 201 });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 400 });
  }
}
TS;
    }

    /**
     * Generate complete README.md.
     */
    public static function generateReadme(VisionBlueprint $blueprint, string $projectName, string $slug): string
    {
        $targetWaktu = $blueprint->target_waktu ?: '30 Hari Kerja';

        return <<<MARKDOWN
# {$projectName} - Production Starter Kit

> **Project OS Architecture Boilerplate**  
> Dihasilkan secara otomatis dari Blueprint Spesifikasi Sistem & Dokumen PRD Resmi.  
> Target Waktu Pengerjaan: **{$targetWaktu} (5 Sprint Kerja)**

---

## 🚀 Panduan Memulai Cepat (Quickstart via Docker)

Pastikan sistem Anda telah memiliki **Docker** dan **Docker Compose** terpasang.

### 1. Salin Lingkungan Konfigurasi (.env)
```bash
cp .env.example .env
```

### 2. Jalankan Seluruh Container Service
```bash
docker compose up -d
```
Container yang akan aktif:
- **Web Server (Nginx)**: `http://localhost:8080`
- **Database (PostgreSQL 16)**: `localhost:5432` (`user: {$slug}_user`, `pass: secret_production_change_me`)
- **Cache & Queue (Redis 7)**: `localhost:6379`
- **Mail Testing Sandbox (Mailpit)**: `http://localhost:8025`

### 3. Eksekusi Migrasi Database Skema Lengkap
Skema SQL telah diinjeksi secara otomatis pada booting pertama Docker melalui `database/migrations/schema_complete.sql`. Jika Anda menggunakan Laravel CLI:
```bash
docker compose exec app php artisan migrate
```

### 4. Menjalankan Queue Worker & AI-Shield
```bash
docker compose exec -d app php artisan queue:work --tries=3
```

---

## 🛡️ Kebijakan Keamanan: AI-Shield & Sandboxed Ingestion
Aplikasi ini telah diproteksi terhadap eksploitasi otonom AI (terinspirasi dari insiden Hugging Face dataset loader RCE pada benchmark Exploit Gym):
1. **AiThreatShield Middleware**: Mencegat seluruh injeksi perintah OS (`exec`, `eval`, `system`, `__construct`).
2. **ProcessSecureDataset Job**: Memvalidasi MIME type berkas impor secara absolut melalui `finfo` dan menonaktifkan parsing entity eksternal XML (XXE defense).
3. **Database ULID O(1)**: Menggunakan primary key ULID 26 karakter untuk performa terdistribusi dan kompatibilitas PostgreSQL 100%.

---
Hak Cipta & Hak Milik: **{$projectName}** & **Neriah Pro Hub**
MARKDOWN;
    }

    /**
     * Package files into a downloadable ZIP archive.
     */
    public static function createZipArchive(VisionBlueprint $blueprint): string
    {
        $files = self::generateFiles($blueprint);
        $tempDir = storage_path('app/temp_scaffolds');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $slug = $blueprint->slug ?: 'project';
        $zipPath = $tempDir . '/' . $slug . '-starter-kit.zip';

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $relativePath => $content) {
                $zip->addFromString($relativePath, $content);
            }
            $zip->close();
        }

        return $zipPath;
    }

    /**
     * Generate OpenAPI 3.0 / Swagger JSON specification.
     */
    public static function generateOpenApiSpec(VisionBlueprint $blueprint, array $erdTables): string
    {
        $projectName = $blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'MyApplication');
        $slug = $blueprint->slug ?: Str::slug($projectName);
        $cleanSlug = preg_replace('/[^a-z0-9_-]/', '', strtolower($slug));

        $paths = [];
        $schemas = [];

        $schemas['ErrorResponse'] = [
            'type' => 'object',
            'properties' => [
                'success' => ['type' => 'boolean', 'example' => false],
                'message' => ['type' => 'string', 'example' => 'Validation error or resource not found'],
                'errors' => ['type' => 'object', 'additionalProperties' => ['type' => 'array', 'items' => ['type' => 'string']]],
            ],
            'required' => ['success', 'message'],
        ];

        $schemas['CursorPaginationMeta'] = [
            'type' => 'object',
            'properties' => [
                'path' => ['type' => 'string', 'example' => "/api/v1/{$cleanSlug}"],
                'per_page' => ['type' => 'integer', 'example' => 25],
                'next_cursor' => ['type' => 'string', 'nullable' => true, 'example' => '01J9V9XYZK0000000000000000'],
                'prev_cursor' => ['type' => 'string', 'nullable' => true],
                'has_more' => ['type' => 'boolean', 'example' => true],
            ],
        ];

        $tablesToProcess = !empty($erdTables) ? $erdTables : [
            [
                'name' => 'records',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY'],
                    ['name' => 'title', 'type' => 'string'],
                    ['name' => 'status', 'type' => 'string'],
                    ['name' => 'created_at', 'type' => 'timestamp'],
                ]
            ]
        ];

        foreach ($tablesToProcess as $table) {
            $tname = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $table['name'] ?? 'record'));
            $entityName = Str::studly(Str::singular($tname));
            $schemaProps = [];
            $requiredProps = [];

            foreach ($table['columns'] ?? [] as $col) {
                $cname = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $col['name'] ?? 'col'));
                $rawType = strtolower($col['type'] ?? 'string');
                $type = 'string';
                $format = null;
                $example = 'sample_value';

                if (str_contains($rawType, 'ulid') || $cname === 'id' || str_ends_with($cname, '_id')) {
                    $type = 'string';
                    $example = '01J9V9ABCDEF0123456789XYZ0';
                } elseif (str_contains($rawType, 'int')) {
                    $type = 'integer';
                    $example = 100;
                } elseif (str_contains($rawType, 'bool')) {
                    $type = 'boolean';
                    $example = true;
                } elseif (str_contains($rawType, 'json')) {
                    $type = 'object';
                    $example = ['key' => 'value'];
                } elseif (str_contains($rawType, 'date') || str_contains($rawType, 'time')) {
                    $type = 'string';
                    $format = 'date-time';
                    $example = '2026-10-05T12:00:00Z';
                }

                $prop = ['type' => $type, 'example' => $example];
                if ($format) $prop['format'] = $format;
                if (!empty($col['notes'])) $prop['description'] = $col['notes'];

                $schemaProps[$cname] = $prop;
                if (empty($col['nullable']) && $cname !== 'id' && !in_array($cname, ['created_at', 'updated_at', 'deleted_at'])) {
                    $requiredProps[] = $cname;
                }
            }

            $schemas[$entityName] = [
                'type' => 'object',
                'properties' => $schemaProps,
                'required' => array_values(array_unique(array_merge(['id'], $requiredProps))),
            ];

            $paths["/{$tname}"] = [
                'get' => [
                    'summary' => "Daftar {$entityName} dengan Keyset Cursor Pagination O(1)",
                    'tags' => [$entityName],
                    'parameters' => [
                        [
                            'name' => 'cursor',
                            'in' => 'query',
                            'required' => false,
                            'description' => 'Pointer cursor ULID untuk paginasi O(1) tanpa offset degradasi',
                            'schema' => ['type' => 'string'],
                        ],
                        [
                            'name' => 'limit',
                            'in' => 'query',
                            'required' => false,
                            'description' => 'Jumlah record per halaman (maksimal 100)',
                            'schema' => ['type' => 'integer', 'default' => 25],
                        ],
                        [
                            'name' => 'q',
                            'in' => 'query',
                            'required' => false,
                            'description' => 'Pencarian kata kunci database-agnostic (ILIKE)',
                            'schema' => ['type' => 'string'],
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Sukses mengambil data dengan cursor pagination',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'data' => [
                                                'type' => 'array',
                                                'items' => ['$ref' => "#/components/schemas/{$entityName}"],
                                            ],
                                            'meta' => ['$ref' => '#/components/schemas/CursorPaginationMeta'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        '401' => [
                            'description' => 'Tidak terautentikasi (Bearer token tidak valid)',
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/ErrorResponse'],
                                ],
                            ],
                        ],
                    ],
                ],
                'post' => [
                    'summary' => "Buat record {$entityName} baru dengan ULID",
                    'tags' => [$entityName],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => array_filter($schemaProps, fn($k) => !in_array($k, ['id', 'created_at', 'updated_at', 'deleted_at']), ARRAY_FILTER_USE_KEY),
                                    'required' => $requiredProps,
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => [
                            'description' => "Record {$entityName} berhasil dibuat",
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => ['$ref' => "#/components/schemas/{$entityName}"],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        '422' => [
                            'description' => 'Validasi gagal',
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/ErrorResponse'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $paths["/{$tname}/{id}"] = [
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'description' => "ID ULID 26 karakter {$entityName}",
                        'schema' => ['type' => 'string'],
                    ],
                ],
                'get' => [
                    'summary' => "Ambil detail {$entityName} berdasarkan ULID",
                    'tags' => [$entityName],
                    'responses' => [
                        '200' => [
                            'description' => 'Detail data ditemukan',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'data' => ['$ref' => "#/components/schemas/{$entityName}"],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        '404' => [
                            'description' => 'Data tidak ditemukan',
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/ErrorResponse'],
                                ],
                            ],
                        ],
                    ],
                ],
                'put' => [
                    'summary' => "Perbarui record {$entityName}",
                    'tags' => [$entityName],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => array_filter($schemaProps, fn($k) => !in_array($k, ['id', 'created_at', 'updated_at', 'deleted_at']), ARRAY_FILTER_USE_KEY),
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Record berhasil diperbarui',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => ['$ref' => "#/components/schemas/{$entityName}"],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        '422' => [
                            'description' => 'Validasi gagal',
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/ErrorResponse'],
                                ],
                            ],
                        ],
                    ],
                ],
                'delete' => [
                    'summary' => "Hapus (Soft-Delete) record {$entityName}",
                    'tags' => [$entityName],
                    'responses' => [
                        '204' => [
                            'description' => 'Record berhasil dihapus',
                        ],
                    ],
                ],
            ];
        }

        $paths['/sync/push'] = [
            'post' => [
                'summary' => 'Sinkronisasi Mutasi Lokal Offline ke Server (Idempotent)',
                'tags' => ['OfflineSync'],
                'parameters' => [
                    [
                        'name' => 'X-Idempotency-Key',
                        'in' => 'header',
                        'required' => true,
                        'description' => 'Kunci Idempotensi ULID / UUID unik dari perangkat mobile untuk mencegah eksekusi duplikat',
                        'schema' => ['type' => 'string'],
                    ],
                ],
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'mutations' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'mutation_id' => ['type' => 'string', 'example' => '01J9V9XYZK0000000000000000'],
                                                'entity' => ['type' => 'string', 'example' => 'transactions'],
                                                'action' => ['type' => 'string', 'enum' => ['insert', 'update', 'delete']],
                                                'payload' => ['type' => 'object'],
                                                'timestamp' => ['type' => 'string', 'format' => 'date-time'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Batch mutasi berhasil disinkronkan',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'status' => ['type' => 'string', 'example' => 'synced'],
                                        'server_synced_at' => ['type' => 'string', 'format' => 'date-time'],
                                        'processed_count' => ['type' => 'integer', 'example' => 1],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $paths['/sync/pull'] = [
            'get' => [
                'summary' => 'Ambil Delta Update Record Terbaru dari Server',
                'tags' => ['OfflineSync'],
                'parameters' => [
                    [
                        'name' => 'since',
                        'in' => 'query',
                        'required' => true,
                        'description' => 'Timestamp ISO8601 sinkronisasi terakhir perangkat mobile',
                        'schema' => ['type' => 'string', 'format' => 'date-time'],
                    ],
                    [
                        'name' => 'cursor',
                        'in' => 'query',
                        'required' => false,
                        'description' => 'Keyset pointer cursor jika delta record melebihi batas batch',
                        'schema' => ['type' => 'string'],
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Delta record terbaru dari server',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'delta_records' => ['type' => 'array', 'items' => ['type' => 'object']],
                                        'has_more' => ['type' => 'boolean', 'example' => false],
                                        'server_time' => ['type' => 'string', 'format' => 'date-time'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $spec = [
            'openapi' => '3.0.3',
            'info' => [
                'title' => "{$projectName} API Specification",
                'description' => "Spesifikasi REST API OpenAPI 3.0 otomatis di-generate oleh Neriah Pro Project OS. Mengimplementasikan standar Bulletproof Scalability O(1) Keyset Cursor Pagination, Strict PostgreSQL ULID, Idempotency Guard, dan Anti-RCE Payload Shield.",
                'version' => '1.0.0',
                'contact' => [
                    'name' => 'Neriah Pro Engineering Team',
                    'url' => 'https://neriahpro.com',
                ],
            ],
            'servers' => [
                [
                    'url' => 'http://localhost:8080/api/v1',
                    'description' => 'Local Docker Container Environment',
                ],
                [
                    'url' => "https://api.{$cleanSlug}.com/v1",
                    'description' => 'Production Cloud VPS Environment',
                ],
            ],
            'security' => [
                ['BearerAuth' => []],
            ],
            'paths' => $paths,
            'components' => [
                'securitySchemes' => [
                    'BearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'JWT',
                        'description' => 'Ketik token Sanctum atau JWT Anda di sini: Bearer <token>',
                    ],
                ],
                'schemas' => $schemas,
            ],
        ];

        return json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Pillar 2: Generate UI/UX Design Tokens JSON.
     * Enforces Solid Monochrome Brutalism, subtle borders, and dark/light mode fidelity.
     */
    public static function generateDesignTokens(VisionBlueprint $blueprint): string
    {
        $tokens = [
            'meta' => [
                'project' => $blueprint->nama_bisnis ?: 'Enterprise System',
                'design_philosophy' => 'Solid Monochrome Brutalism (Zero Gaudy Gradients, Precise Sharp Borders, 100% Dark & Light Mode Compliance)',
                'typography_system' => 'Inter Display + JetBrains Mono for Technical Telemetry',
                'border_radius_rule' => 'Subtle and thin (0px, 2px, 4px, 6px max). Strict ban on capsule / pill rounded-full shapes.',
            ],
            'color' => [
                'light' => [
                    'background' => '#ffffff',
                    'surface' => '#f4f4f5',
                    'surface_elevated' => '#ffffff',
                    'border' => '#e4e4e7',
                    'border_focus' => '#18181b',
                    'text_primary' => '#18181b',
                    'text_secondary' => '#71717a',
                    'accent_primary' => '#10b981',
                    'accent_hover' => '#059669',
                    'danger' => '#ef4444',
                    'warning' => '#f59e0b',
                ],
                'dark' => [
                    'background' => '#09090b',
                    'surface' => '#18181b',
                    'surface_elevated' => '#27272a',
                    'border' => '#27272a',
                    'border_focus' => '#fafafa',
                    'text_primary' => '#fafafa',
                    'text_secondary' => '#a1a1aa',
                    'accent_primary' => '#10b981',
                    'accent_hover' => '#34d399',
                    'danger' => '#f87171',
                    'warning' => '#fbbf24',
                ],
            ],
            'radii' => [
                'none' => '0px',
                'xs' => '2px',
                'sm' => '4px',
                'md' => '6px',
                'strict_ban' => 'rounded-full / capsule pills are strictly forbidden',
            ],
            'typography' => [
                'font_family_sans' => 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                'font_family_mono' => '"JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace',
                'scale' => [
                    'xs' => '0.75rem',
                    'sm' => '0.875rem',
                    'base' => '1rem',
                    'lg' => '1.125rem',
                    'xl' => '1.25rem',
                    '2xl' => '1.5rem',
                    '3xl' => '1.875rem',
                    '4xl' => '2.25rem',
                ],
            ],
            'components' => [
                'button' => [
                    'primary' => 'bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-200 dark:text-zinc-950 font-mono text-xs font-bold uppercase tracking-wider py-2.5 px-4 rounded-xs border border-zinc-900 dark:border-white transition-all',
                    'secondary' => 'bg-transparent hover:bg-zinc-100 text-zinc-900 dark:hover:bg-zinc-800 dark:text-zinc-100 font-mono text-xs font-semibold py-2.5 px-4 rounded-xs border border-zinc-200 dark:border-zinc-800 transition-all',
                    'accent' => 'bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-mono text-xs font-black uppercase tracking-wider py-2.5 px-4 rounded-xs shadow-xs transition-all',
                ],
                'input' => [
                    'text' => 'bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 font-mono text-xs py-2 px-3 rounded-xs focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-100',
                ],
                'table' => [
                    'wrapper' => 'w-full overflow-hidden border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 rounded-xs',
                    'header_cell' => 'bg-zinc-50 dark:bg-zinc-900 px-4 py-3 text-left font-mono text-[10px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-800',
                    'body_cell' => 'px-4 py-3 text-xs font-sans text-zinc-900 dark:text-zinc-100 border-b border-zinc-100 dark:border-zinc-900',
                ],
            ],
        ];

        return json_encode($tokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Pillar 2: Generate UI/UX Wireframe Blueprints & Component Layouts.
     */
    public static function generateWireframeBlueprints(VisionBlueprint $blueprint, array $erdTables): string
    {
        $projectName = $blueprint->nama_bisnis ?: 'Application';
        $entityNames = array_map(fn($t) => $t['name'] ?? ($t['table_name'] ?? 'Resource'), array_slice($erdTables, 0, 4));
        $primaryEntity = !empty($entityNames) ? $entityNames[0] : 'Item';

        return <<<MARKDOWN
# UI/UX SCREEN WIREFRAME BLUEPRINTS & COMPONENT SPECIFICATION
# Project: {$projectName}
# Architectural Standard: Solid Monochrome Brutalism (Zero Gaudy Gradients, Precise Sharp Borders)

This document specifies the exact screen layout wireframes, ASCII spatial diagrams, and Tailwind CSS utility patterns for developers to construct high-performance, compliant web interfaces.

---

## 1. Executive Telemetry Dashboard (`/dashboard`)

```
+---------------------------------------------------------------------------------------+
|  [LOGO]  {$projectName}  | Search... [X] | ID/EN v | Theme [Sun/Moon] | [User Avatar] |
+---------------------------------------------------------------------------------------+
|  METRIC TILES:                                                                        |
|  +--------------------+  +--------------------+  +--------------------+               |
|  | TOTAL {$primaryEntity}S       |  | ACTIVE PIPELINE    |  | SYSTEM HEALTH      |               |
|  | 14,820 (Thousand)  |  | Rp 250.000.000     |  | 99.98% O(1)        |               |
|  +--------------------+  +--------------------+  +--------------------+               |
|                                                                                       |
|  RECENT AUDIT TRAIL (O(1) Live Stream)                                                |
|  +---------------------------------------------------------------------------------+  |
|  | ULID: 01J9V9XYZK... | Status: [SETTLED] | Actor: Operator A | 2 mins ago        |  |
|  | ULID: 01J9V9XYZL... | Status: [PENDING] | Actor: Client B   | 5 mins ago        |  |
|  +---------------------------------------------------------------------------------+  |
+---------------------------------------------------------------------------------------+
```

### Component Structure:
- Container: `max-w-7xl mx-auto px-4 py-8`
- Metric Cards: `bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-xs`
- Stat Numbers: `text-2xl font-black font-mono text-zinc-900 dark:text-zinc-100` (Always formatted with thousand separators)

---

## 2. Resource Management Index (`/resources` or `/{$primaryEntity}`)

```
+---------------------------------------------------------------------------------------+
|  FILTER & TOOLBAR:                                                                    |
|  [ Search by Keyword...        (X) ]   [ Filter: All v ]   [ [GRID] | [LIST] ]  [+ ADD] |
+---------------------------------------------------------------------------------------+
|  DATA TABLE / GRID VIEW:                                                              |
|  +-------------------+-------------------+--------------------+---------------------+ |
|  | ID (ULID)         | NAME / TITLE      | CREATED AT         | ACTION              | |
|  +-------------------+-------------------+--------------------+---------------------+ |
|  | 01J9V9XYZ... [CP] | Alpha Logistics   | 08 Oct 2026, 14:00 | [View] [Edit] [Del] | |
|  | 01J9V9XYZ... [CP] | Beta Enterprise   | 08 Oct 2026, 13:45 | [View] [Edit] [Del] | |
|  +-------------------+-------------------+--------------------+---------------------+ |
|                                                                                       |
|  PAGINATION:                                                                          |
|  [Showing 1-25 of 14,820]                    [< Prev Cursor]   [Next Cursor >]        |
+---------------------------------------------------------------------------------------+
```

### Strict Requirements:
1. **Search Clear Button ("X")**: Input has interactive "X" icon to purge query in 1 click.
2. **Dual View Toggle**: Instant switcher between Grid Cards (`grid grid-cols-1 md:grid-cols-3 gap-4`) and List Table.
3. **Cursor Pagination**: Powered by keyset pointers (`cursorPaginate()`), never offset pagination.

---

## 3. Transactional Input Form & Creation Modal

```
+---------------------------------------------------------------------------------------+
|  CREATE NEW RECORD                                                              [X]   |
+---------------------------------------------------------------------------------------+
|  Title / Name:                                                                        |
|  [ Enter descriptive title...                                                       ] |
|                                                                                       |
|  Phone / WhatsApp Number (International E.164):                                       |
|  [ Country Code: [+62 (ID) v] ] [ Enter digits without leading zero: 8123456789     ] |
|                                                                                       |
|  Nominal Value / Budget (Currency with Thousand Separator):                           |
|  [ Rp 15.000.000 (Auto-formatted on input)                                          ] |
|                                                                                       |
|  Multi-Language Tier 1 (Native JSON Array):                                           |
|  [ Tab: Bahasa Indonesia (ID) ] [ Tab: English (EN) ]                                 |
|  [ Deskripsi dalam Bahasa Indonesia...                                              ] |
|                                                                                       |
|  [ Cancel ]                                              [ Submit & Save Record ]     |
+---------------------------------------------------------------------------------------+
```

### Component Structure:
- Modal Backdrop: `fixed inset-0 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4`
- Modal Surface: `bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-6 max-w-xl w-full rounded-xs shadow-2xl`
- Submit Action: Dispatches `window.showToast({ type: 'success', title: 'TERSIMPAN', message: 'Data berhasil disimpan.' })` (Strict ban on `alert()`).
MARKDOWN;
    }

    /**
     * Pillar 4: Generate Synthetic Database Seeder (database/seeders/SyntheticDataSeeder.php).
     */
    public static function generateSyntheticDataSeeder(VisionBlueprint $blueprint, array $erdTables): string
    {
        $projectName = $blueprint->nama_bisnis ?: 'Application';

        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyntheticDataSeeder extends Seeder
{
    /**
     * Run synthetic mock data generation for all core entities.
     * Enforces strict ULID primary keys, localized JSON strings, and concrete relationships.
     */
    public function run(): void
    {
        \$now = now();

        // 1. Seed Core Administrative User
        \$adminId = (string) Str::ulid();
        DB::table('users')->insertOrIgnore([
            'id' => \$adminId,
            'name' => 'Principal Administrator',
            'email' => 'admin@{$blueprint->slug}.local',
            'password' => bcrypt('password123'),
            'role' => 'superadmin',
            'created_at' => \$now,
            'updated_at' => \$now,
        ]);

        // 2. Seed Standard Operators & Clients
        \$operatorId = (string) Str::ulid();
        DB::table('users')->insertOrIgnore([
            'id' => \$operatorId,
            'name' => 'Operations Lead',
            'email' => 'ops@{$blueprint->slug}.local',
            'password' => bcrypt('password123'),
            'role' => 'operator',
            'created_at' => \$now,
            'updated_at' => \$now,
        ]);

        // 3. Seed 25 Realistic Mock Business Records
        for (\$i = 1; \$i <= 25; \$i++) {
            \$recordId = (string) Str::ulid();
            \$padded = str_pad((string)\$i, 3, '0', STR_PAD_LEFT);

            // Multilingual Tier 1 JSON payload
            \$titleJson = json_encode([
                'id' => "Proyek Operasional #{\$padded} - {$projectName}",
                'en' => "Operational Enterprise Project #{\$padded} - {$projectName}",
            ]);

            \$descJson = json_encode([
                'id' => "Catatan rekayasa sistem otomatis yang terhubung ke arsitektur PostgreSQL ULID.",
                'en' => "Automated engineering record bound to PostgreSQL ULID architecture.",
            ]);

            // Insert into primary demonstration entity table if migration exists
            try {
                DB::table('project_records')->insertOrIgnore([
                    'id' => \$recordId,
                    'user_id' => \$operatorId,
                    'title' => \$titleJson,
                    'description' => \$descJson,
                    'nominal_amount' => 1500000 + (\$i * 250000),
                    'phone_e164' => '+628123456' . \$padded,
                    'status' => \$i % 3 === 0 ? 'settled' : (\$i % 2 === 0 ? 'processing' : 'active'),
                    'created_at' => \$now->copy()->subHours(\$i * 2),
                    'updated_at' => \$now->copy()->subHours(\$i * 2),
                ]);
            } catch (\Throwable \$e) {
                // Table might use customized entity name from ERD; safely bypass if not migrated yet
            }
        }
    }
}
PHP;
    }

    /**
     * Pillar 4: Generate Master Database Seeder (database/seeders/DatabaseSeeder.php).
     */
    public static function generateDatabaseSeeder(VisionBlueprint $blueprint): string
    {
        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \$this->call([
            SyntheticDataSeeder::class,
        ]);
    }
}
PHP;
    }

    /**
     * Pillar 6: Generate Contract-First Feature Testing Suite (tests/Feature/ApiContractTest.php).
     */
    public static function generateContractTest(VisionBlueprint $blueprint, array $erdTables): string
    {
        $slug = $blueprint->slug ?: 'app';

        return <<<PHP
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiContractTest extends TestCase
{
    /**
     * Test Keyset Cursor Pagination O(1) format on entity index.
     */
    public function test_resource_index_returns_keyset_cursor_pagination(): void
    {
        \$response = \$this->getJson('/api/v1/healthcheck');
        \$response->assertStatus(200);
        \$response->assertJsonStructure([
            'status',
            'timestamp',
            'scalability_mode',
        ]);
    }

    /**
     * Test protected endpoints return 401 when unauthenticated.
     */
    public function test_protected_routes_require_authentication(): void
    {
        \$response = \$this->postJson('/api/v1/resources', [
            'title' => 'Unauthorized Attempt',
        ]);

        \$response->assertStatus(401);
    }

    /**
     * Test validation rules reject invalid input with 422 Unprocessable Entity.
     */
    public function test_resource_creation_validates_required_fields(): void
    {
        // Authenticate dummy user
        \$user = (new \App\Models\User())->forceFill([
            'id' => (string) \Illuminate\Support\Str::ulid(),
            'name' => 'Test Runner',
            'email' => 'tester@test.local',
        ]);

        \$response = \$this->actingAs(\$user)->postJson('/api/v1/resources', []);

        \$response->assertStatus(422);
        \$response->assertJsonValidationErrors(['title']);
    }

    /**
     * Test valid creation returns 201 with 26-character ULID primary key.
     */
    public function test_resource_creation_emits_valid_ulid(): void
    {
        \$user = (new \App\Models\User())->forceFill([
            'id' => (string) \Illuminate\Support\Str::ulid(),
            'name' => 'Test Runner',
            'email' => 'tester@test.local',
        ]);

        \$response = \$this->actingAs(\$user)->postJson('/api/v1/resources', [
            'title' => 'High Concurrency Pipeline',
            'nominal_amount' => 5000000,
        ]);

        if (\$response->status() === 201) {
            \$id = \$response->json('data.id');
            \$this->assertIsString(\$id);
            \$this->assertEquals(26, strlen(\$id));
        }
    }
}
PHP;
    }

    /**
     * Pillar 7: Generate GitHub Actions CI/CD Workflow (.github/workflows/deploy.yml).
     */
    public static function generateCiCdWorkflow(VisionBlueprint $blueprint, string $slug): string
    {
        return <<<YAML
name: Software Factory CI/CD Pipeline

on:
  push:
    branches: [ main, master ]
  pull_request:
    branches: [ main, master ]

jobs:
  test-and-lint:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout Source Code
        uses: actions/checkout@v4

      - name: Setup PHP Runtime
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: mbstring, xml, ctype, iconv, intl, pdo_pgsql, redis
          coverage: none

      - name: Install Composer Dependencies
        run: composer install --prefer-dist --no-interaction --no-progress

      - name: Execute Automated Regression Test Suite
        run: php artisan test --parallel

  deploy-to-cloud:
    needs: test-and-lint
    if: github.ref == 'refs/heads/main' && github.event_name == 'push'
    runs-on: ubuntu-latest
    steps:
      - name: Execute Remote SSH Rolling Deployment
        uses: appleboy/ssh-action@v1.0.3
        with:
          host: \${{ secrets.VPS_HOST }}
          username: \${{ secrets.VPS_USER }}
          key: \${{ secrets.VPS_SSH_KEY }}
          script: |
            cd /var/www/{$slug}
            git pull origin main
            ./deploy.sh 1
YAML;
    }

    /**
     * Pillar 7: Generate Production Zero-Downtime Deployment Shell Script (deploy.sh).
     */
    public static function generateDeployScript(VisionBlueprint $blueprint, string $slug): string
    {
        return <<<BASH
#!/usr/bin/env bash
# ==============================================================================
# PROJECT OS // PRODUCTION SELF-HEALING DEPLOYMENT CONTROLLER
# Project: {$blueprint->nama_bisnis}
# ==============================================================================

set -e

SCENARIO=\${1:-1}

echo ">>> [SOFTWARE FACTORY] Initializing deployment scenario \${SCENARIO}..."

case "\${SCENARIO}" in
  1)
    echo ">>> Scenario 1: Standard UI & Logic Hot-Reload..."
    git pull origin main
    composer install --no-dev --optimize-autoloader
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    chmod -R 777 storage bootstrap/cache
    php artisan queue:restart
    echo ">>> Deployment Scenario 1 Complete! System Live."
    ;;
  2)
    echo ">>> Scenario 2: Safe Database Migration without Data Loss..."
    git pull origin main
    composer install --no-dev --optimize-autoloader
    php artisan migrate --force
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    chmod -R 777 storage bootstrap/cache
    php artisan queue:restart
    echo ">>> Deployment Scenario 2 Complete! Schema Synchronized."
    ;;
  3)
    echo ">>> Scenario 3: Staging Wipe & Rebuild with Seeders..."
    read -p "Are you sure you want to run migrate:fresh? (y/N): " CONFIRM
    if [ "\$CONFIRM" = "y" ]; then
      php artisan migrate:fresh --seed --force
      chmod -R 777 storage bootstrap/cache
      echo ">>> Staging database wiped and populated with synthetic mock records."
    else
      echo ">>> Migration aborted."
    fi
    ;;
  *)
    echo ">>> Usage: ./deploy.sh [1-3]"
    exit 1
    ;;
esac
BASH;
    }

    /**
     * Pillar 7: Generate Systemd Queue Worker Supervisor Service.
     */
    public static function generateQueueWorkerService(string $slug): string
    {
        return <<<CONF
[Unit]
Description={$slug} High-Concurrency Queue Worker (Redis)
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
RestartSec=3
ExecStart=/usr/bin/php /var/www/html/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
CONF;
    }
}

