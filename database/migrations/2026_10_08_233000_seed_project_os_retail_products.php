<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Registers the 3 Project OS Retail Packages into the product catalog.
     */
    public function up(): void
    {
        $products = [
            [
                'slug' => 'project-os-retail-lite-prd',
                'name' => [
                    'id' => 'Project OS: Tier 02 Lite PRD Blueprint',
                    'en' => 'Project OS: Tier 02 Lite PRD Blueprint',
                ],
                'description' => [
                    'id' => 'Paket Retail Self-Service Instant PRD: Spesifikasi inti sistem, identitas bisnis, dan kuesioner arsitektur 26 parameter (Pilar 1).',
                    'en' => 'Self-Service Instant PRD Specification: Core system spec, business identity, and 26-parameter architecture questionnaire (Pillar 1).',
                ],
                'features' => [
                    'Pilar 1: Spesifikasi Inti & Kuesioner 26 Parameter',
                    'Perumusan Dokumen PRD Otomatis via AI Project OS',
                    'Revisi Form 30 Hari & Unduh Dokumen Selamanya',
                    'Lisensi Penggunaan Mandiri untuk Tim Developer Internal',
                ],
                'price_idr' => 99000.00,
                'price_usd' => 6.00,
                'is_active' => true,
            ],
            [
                'slug' => 'project-os-retail-pro-blueprint',
                'name' => [
                    'id' => 'Project OS: Tier 03 Pro Production Blueprint',
                    'en' => 'Project OS: Tier 03 Pro Production Blueprint',
                ],
                'description' => [
                    'id' => 'Paket Retail Self-Service Komplit: Rangka koding scaffold container, data awal seeder, panduan agen AI, 6 diagram visual, dan lisensi white-label (Pilar 1, 3, 4, 5).',
                    'en' => 'Complete Production Blueprint: Scaffold container stack, synthetic data seeder, AI agent rules, 6 visual diagrams, and white-label license (Pillars 1, 3, 4, 5).',
                ],
                'features' => [
                    'Pilar 1, 3, 4, 5 (Spesifikasi, Scaffold, Seeder, AI Rules)',
                    'Paket Boilerplate Scaffold .zip (Docker, Laravel, Next.js)',
                    '6 Diagram Arsitektur & ERD PostgreSQL Strict ULID',
                    'SyntheticDataSeeder (100+ Data Awal Realistis)',
                    'Panduan Agen AI (.cursorrules, CLAUDE.md, AGENTS.md)',
                    'Revisi 6 Bulan & Unlimited AI Regeneration',
                ],
                'price_idr' => 399000.00,
                'price_usd' => 25.00,
                'is_active' => true,
            ],
            [
                'slug' => 'project-os-retail-ultimate-factory-os',
                'name' => [
                    'id' => 'Project OS: Tier 04 Ultimate Software Factory OS',
                    'en' => 'Project OS: Tier 04 Ultimate Software Factory OS',
                ],
                'description' => [
                    'id' => 'Paket Retail Self-Service Tertinggi 7 Pilar: Wireframe UI/UX, pengujian kontrak otomatis ApiContractTest, CI/CD Cloud Pipeline, NDA resmi berpayung hukum, dan 1 jam sesi konsultasi bersama Principal Architect.',
                    'en' => 'Ultimate 7-Pillar Factory OS: UI/UX wireframes, ApiContractTest suite, CI/CD Cloud Pipeline, binding legal NDA, and 1-hour Principal Architect consultation.',
                ],
                'features' => [
                    'Lengkap Seluruh 7 Pilar Software Factory OS',
                    'Pilar 2: Desain UI/UX & Wireframe Interaktif 4 Layar',
                    'Pilar 6: Uji Kualitas Otomatis (ApiContractTest.php)',
                    'Pilar 7: Otomasi Server & CI/CD Pipeline (deploy.sh)',
                    'Surat Perjanjian Kerahasiaan (NDA) Resmi Berpayung Hukum',
                    '1 Jam Sesi Konsultasi 1-on-1 Bersama Principal Architect',
                    'Prioritas 1 Tahun & Potongan Biaya DP Penuh jika Lanjut MVP',
                ],
                'price_idr' => 1490000.00,
                'price_usd' => 95.00,
                'is_active' => true,
            ],
        ];

        foreach ($products as $data) {
            Product::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Product::whereIn('slug', [
            'project-os-retail-lite-prd',
            'project-os-retail-pro-blueprint',
            'project-os-retail-ultimate-factory-os',
        ])->delete();
    }
};
