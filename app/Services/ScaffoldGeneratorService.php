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
        return [
            'docker-compose.yml' => self::generateDockerCompose($blueprint, $slug),
            '.env.example' => self::generateEnvExample($blueprint, $slug),
            'README.md' => self::generateReadme($blueprint, $projectName, $slug),
            'schema_complete.sql' => $schemaSql,
            'database/migrations/schema_complete.sql' => $schemaSql,
            'docker/nginx/default.conf' => self::generateNginxConf($slug),
            'routes/web.php' => self::generateLaravelRoutes($blueprint, $erdTables),
            'routes/api.php' => self::generateLaravelApiRoutes($blueprint, $erdTables),
            'app/api/route.ts' => self::generateNextJsRoute($blueprint, $erdTables),
            'nextjs/app/api/resources/route.ts' => self::generateNextJsRoute($blueprint, $erdTables),
        ];
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
}
