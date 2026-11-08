<?php

namespace App\Services\Prd\Strategies;

use Illuminate\Support\Str;

class GeneralCoreDomainStrategy extends AbstractDomainStrategy
{
    public function supports(string $title, string $desc, string $lower): bool
    {
        return true; // Fallback strategy for general core business features
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
        $modelName = $this->resolveModelName($title);
        $titleSlug = Str::slug($title);
        $cleanSlug = strtoupper(substr(str_replace('-', '_', $titleSlug), 0, 14));
        $relatedTable = $this->matchTable($title, $desc, $erdTables);

        $ac = [
            [
                'scenario' => 'Eksekusi Fungsional Utama Berjalan Normal',
                'given' => "Pengguna memiliki hak akses terhadap modul {$title}.",
                'when' => 'Pengguna menjalankan alur utama fitur sesuai petunjuk sistem.',
                'then' => 'Sistem memproses data secara akurat, menyimpan hasil dengan integritas ACID, dan memperbarui status tampilan secara reaktif.',
            ],
            [
                'scenario' => 'Penanganan Error dan Validasi Input',
                'given' => 'Data yang diberikan tidak memenuhi syarat spesifikasi.',
                'when' => 'Data dikirimkan ke server.',
                'then' => 'Sistem mengembalikan pesan peringatan yang jelas dan mencegah penyimpanan data yang rusak.',
            ],
        ];

        $integration = [
            'endpoint' => '/api/v1/' . Str::kebab(Str::plural($modelName)),
            'middleware' => ['web'],
            'request_schema' => '{"title": "Sample Record", "status": "active"}',
            'response_schema' => '{"success": true, "data": {"id": "01J...", "title": "Sample Record"}}',
            'tasks' => [
                'Daftarkan endpoint RESTful atau Livewire Handler dengan kontrak data ketat.',
            ],
        ];

        $mission = "Implementasikan modul {$title} sesuai spesifikasi arsitektur Laravel 13 (PHP 8.5+), Filament v5, dan PostgreSQL Strict ULID.";
        $targetFiles = ['app/Models/' . $modelName . '.php', 'app/Actions/' . $modelName . 'Action.php'];
        $verifyCommand = "php artisan test --filter=" . $modelName . "Test";

        return [
            'category' => 'CORE_FEATURE',
            'category_label' => 'Modul Inti Bisnis',
            'related_table' => $relatedTable['name'] ?? Str::snake(Str::plural($modelName)),
            'related_table_data' => $relatedTable,
            'user_story' => "Sebagai {$clientActor}, saya ingin menggunakan fitur {$title} pada sistem {$businessName}, sehingga {$desc}.",
            'acceptance_criteria' => $ac,
            'frontend' => [
                'design_tokens' => 'Desain komponen berkarakter dengan tipografi tajam, margin konsisten, transisi interaksi halus, dan kompatibilitas dark/light mode.',
                'states' => 'Loading skeleton saat proses komputasi berlangsung, feedback aksi menggunakan sistem Toast, bukan dialog alert native.',
                'components' => [$modelName . 'Component.blade.php', $modelName . 'Card.blade.php'],
                'tasks' => [
                    "Bangun antarmuka pengguna untuk fitur {$title} dengan prinsip Anti-AI-Slop.",
                    'Pastikan pengalaman navigasi mulus di perangkat seluler maupun desktop.',
                ],
            ],
            'backend' => [
                'model_and_migration' => "Tabel `" . Str::snake(Str::plural($modelName)) . "` dengan `->ulid('id')->primary()`, proper indexation, soft deletes.",
                'scalability_guardrail' => 'Terapkan Keyset Cursor Pagination O(1) untuk seluruh endpoint listing data.',
                'tasks' => [
                    "Migration tabel untuk fitur {$title} dengan Primary Key ULID standar PostgreSQL.",
                    "Service atau Action class untuk merangkum logika bisnis secara independen.",
                ],
            ],
            'integration' => $integration,
            'code_agent_directive' => $this->buildDirective(
                sprintf('FEAT-%s-%02d-%s', $prefix, $index, $cleanSlug),
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

