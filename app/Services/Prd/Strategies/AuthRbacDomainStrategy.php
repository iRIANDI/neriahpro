<?php

namespace App\Services\Prd\Strategies;

use Illuminate\Support\Str;

class AuthRbacDomainStrategy extends AbstractDomainStrategy
{
    public function supports(string $title, string $desc, string $lower): bool
    {
        return str_contains($lower, 'autentikasi') ||
            str_contains($lower, 'login') ||
            str_contains($lower, 'rbac') ||
            str_contains($lower, 'user') ||
            str_contains($lower, 'pengguna');
    }

    public function analyze(
        string $title,
        string $desc,
        string $businessName,
        string $clientActor,
        string $primaryActor,
        int $index,
        string $prefix,
        array $erdTables = [],
        bool $isEn = false
    ): array {
        $relatedTable = $this->matchTable($title, $desc, $erdTables);

        $ac = [
            [
                'scenario' => 'Login Berhasil dengan Kredensial Valid',
                'given' => "Pengguna telah terdaftar di database dengan peran yang sah ({$primaryActor} atau {$clientActor}).",
                'when' => 'Pengguna memasukkan email dan kata sandi yang sesuai pada form login.',
                'then' => 'Sistem mengautentikasi sesi, meregenerasi Session ID untuk mencegah session fixation, dan mengarahkan pengguna ke dasbor sesuai peran.',
            ],
            [
                'scenario' => 'Blokir Akses Ilegal (RBAC Boundary)',
                'given' => "Pengguna terautentikasi dengan peran {$clientActor}.",
                'when' => 'Pengguna mencoba mengakses URL atau endpoint khusus milik Superadmin.',
                'then' => 'Sistem menolak request dengan status HTTP 403 Forbidden, menampilkan halaman error yang ramah, dan mencatat log audit percobaan akses.',
            ],
            [
                'scenario' => 'Proteksi Brute-Force Rate Limiting',
                'given' => 'Form login menerima permintaan percobaan otentikasi bertubi-tubi.',
                'when' => 'Terjadi 5 kali kegagalan login dalam kurun waktu 1 menit dari IP yang sama.',
                'then' => 'Sistem mengunci sementara proses login selama 60 detik dengan respons HTTP 429 Too Many Requests.',
            ],
        ];

        $integration = [
            'endpoint' => 'POST /api/v1/auth/login & POST /logout',
            'middleware' => ['web', 'throttle:5,1'],
            'request_schema' => '{"email": "admin@' . Str::slug($businessName) . '.com", "password": "SecurePassword123!", "remember": true}',
            'response_schema' => '{"success": true, "user": {"id": "01J...ULID", "name": "Admin", "role": "superadmin"}, "redirect_url": "/admin"}',
            'tasks' => [
                'Daftarkan route auth dengan pembatasan laju (throttling 5 request/menit).',
                'Pastikan token CSRF divalidasi pada setiap request mutasi.',
            ],
        ];

        $mission = "Implementasikan modul Otentikasi & RBAC dengan Laravel 13 (PHP 8.5+), Filament v5, dan PostgreSQL Strict ULID.";
        $targetFiles = ['app/Models/User.php', 'database/migrations/xxxx_create_users_table.php', 'app/Http/Requests/Auth/LoginRequest.php', 'resources/views/auth/login.blade.php'];
        $verifyCommand = "php artisan test --filter=AuthenticationTest";

        return [
            'category' => 'SECURITY_RBAC',
            'category_label' => 'Otentikasi & Keamanan Akses',
            'related_table' => $relatedTable['name'] ?? 'users',
            'related_table_data' => $relatedTable,
            'user_story' => "Sebagai {$primaryActor} atau {$clientActor}, saya ingin dapat mengautentikasi diri secara aman dengan sesi terisolasi dan izin akses berbasis peran (RBAC), sehingga data sensitif organisasi terlindungi dari akses ilegal.",
            'acceptance_criteria' => $ac,
            'frontend' => [
                'design_tokens' => 'Form auth high-contrast (bukan template abu-abu hambar), surface zinc-900 di dark mode / crisp white di light mode, focus-ring emerald-500 dengan transition-all duration-200.',
                'states' => 'Loading pulse bar pada tombol submit saat auth diproses, error banner inline dengan warna rose-500, auto-focus pada field email.',
                'components' => ['LoginForm.blade.php', 'PasswordInputToggle.js', 'AuthToastNotifier.js'],
                'tasks' => [
                    'Desain formulir otentikasi responsif dengan WCAG AAA contrast ratio.',
                    'Implementasikan toggle visibilitas kata sandi (eye icon SVG fluid).',
                    'Integrasikan notifikasi toast floating saat login gagal tanpa dialog modal native.',
                ],
            ],
            'backend' => [
                'model_and_migration' => 'Tabel `users` dengan `->ulid("id")->primary()`, kolom `role`, `status`, `remember_token`, timestamps.',
                'scalability_guardrail' => 'Index pada kolom `email`, password hashing Argon2id / Bcrypt 12 rounds, keyset cursor pagination untuk listing user.',
                'tasks' => [
                    'Buat migration tabel `users` dengan Primary Key ULID standar PostgreSQL.',
                    'Implementasikan Form Request `LoginRequest` dengan validasi email ketat dan sanitasi string.',
                    'Terapkan middleware `CheckRole` atau Filament Shield Policy untuk isolasi hak akses.',
                ],
            ],
            'integration' => $integration,
            'code_agent_directive' => $this->buildDirective(
                sprintf('FEAT-%s-%02d-AUTH_RBAC', $prefix, $index),
                $title,
                $businessName,
                $mission,
                $targetFiles,
                $verifyCommand,
                $relatedTable,
                $ac,
                $integration,
                $index
            ),
        ];
    }
}

