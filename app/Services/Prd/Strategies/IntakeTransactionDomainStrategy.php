<?php

namespace App\Services\Prd\Strategies;

class IntakeTransactionDomainStrategy extends AbstractDomainStrategy
{
    public function supports(string $title, string $desc, string $lower): bool
    {
        return str_contains($lower, 'form') ||
            str_contains($lower, 'intake') ||
            str_contains($lower, 'reservasi') ||
            str_contains($lower, 'pesan') ||
            str_contains($lower, 'transaksi') ||
            str_contains($lower, 'doa') ||
            str_contains($lower, 'warta') ||
            str_contains($lower, 'konsultasi');
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
                'scenario' => 'Validasi Data Masukan Secara Real-Time',
                'given' => 'Pengguna sedang mengisi kolom nomor telepon, email, dan data wajib.',
                'when' => 'Pengguna mengetik format yang keliru (misal menginput angka 0 di awal nomor WhatsApp ber-country code).',
                'then' => 'Sistem otomatis memfilter atau menampilkan petunjuk inline ramah sebelum formulir dikirim.',
            ],
            [
                'scenario' => 'Pencegahan Double-Submit via Idempotency Key',
                'given' => 'Formulir siap dikirim dengan payload valid.',
                'when' => 'Pengguna menekan tombol submit berkali-kali karena koneksi internet lambat.',
                'then' => 'Sistem memproses request pertama dengan token idempotency unik, mendisabled tombol, dan mengabaikan request duplikat tanpa error.',
            ],
            [
                'scenario' => 'Pencatatan Transaksi & Penerbitan Kode Referensi',
                'given' => 'Data formulir berhasil lolos validasi server-side.',
                'when' => 'Transaksi disimpan ke database.',
                'then' => 'Sistem membungkus operasi dalam DB::transaction, menghasilkan kode referensi unik, mencatat IP & timestamp audit trail, serta mendispatch event background notifikasi.',
            ],
        ];

        $integration = [
            'endpoint' => 'POST /api/v1/intake',
            'middleware' => ['web', 'throttle:15,1'],
            'request_schema' => '{"name": "Budi Santoso", "country_code": "62", "phone": "81234567890", "notes": "Pengajuan minat unit A"}',
            'response_schema' => '{"success": true, "reference_code": "NPRO-TRX-2026-9812", "message": "Pendaftaran berhasil dicatat."}',
            'tasks' => [
                'Daftarkan endpoint API intake dengan header `X-Idempotency-Key`.',
                'Hubungkan submission dengan queue worker untuk pengiriman notifikasi instan.',
            ],
        ];

        $mission = "Bangun modul Formulir Intake & Transaksi dengan validasi server-side ketat, idempotency guard, dan UI modern.";
        $targetFiles = ['app/Models/Intake.php', 'database/migrations/xxxx_create_intakes_table.php', 'app/Http/Requests/StoreIntakeRequest.php', 'app/Actions/CreateIntakeAction.php'];
        $verifyCommand = "php artisan test --filter=IntakeSubmissionTest";

        return [
            'category' => 'DATA_INTAKE_TRANSACTION',
            'category_label' => 'Formulir Intake & Transaksi Terverifikasi',
            'related_table' => $relatedTable['name'] ?? 'intakes',
            'related_table_data' => $relatedTable,
            'user_story' => "Sebagai {$clientActor}, saya ingin mengisi formulir intake/reservasi dengan alur yang terpandu dan validasi instan, sehingga permintaan saya tercatat akurat dan langsung mendapatkan konfirmasi resmi.",
            'acceptance_criteria' => $ac,
            'frontend' => [
                'design_tokens' => 'Floating input labels, selector kode negara bendera interaktif, step progress bar indikatif, validasi error inline dengan aksen rose-500, focus ring emerald-500.',
                'states' => 'Tombol kirim memiliki state loading spinner micro, konfirmasi sukses menggunakan modal modern backdrop-blur atau redirect halaman terima kasih ber-QR code.',
                'components' => ['IntakeForm.blade.php', 'PhoneCountryCodePicker.blade.php', 'StepProgressBar.blade.php'],
                'tasks' => [
                    'Buat form intake multi-langkah yang nyaman diakses lewat smartphone.',
                    'Pasang filter anti angka 0 di awal untuk nomor telepon setelah country code.',
                    'Tampilkan feedback konfirmasi menggunakan floating Toast sistem, bukan alert native.',
                ],
            ],
            'backend' => [
                'model_and_migration' => "Tabel `transactions` / `intakes` dengan `->ulid('id')->primary()`, `reference_code` unique, `status` enum, audit metadata JSONB.",
                'scalability_guardrail' => 'Gunakan DB::transaction untuk integritas data ACID, dispatch event asinkron agar response time HTTP tetap <80ms.',
                'tasks' => [
                    'Migration tabel intake dengan Primary Key ULID dan foreign keys tervalidasi.',
                    'Form Request `StoreIntakeRequest` dengan sanitasi anti-XSS dan rules ketat.',
                    'Action class `CreateIntakeAction` yang menangani logika bisnis secara terisolasi.',
                ],
            ],
            'integration' => $integration,
            'code_agent_directive' => $this->buildDirective(
                sprintf('FEAT-%s-%02d-DATA_INTAKE', $prefix, $index),
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
