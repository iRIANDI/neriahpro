<?php

namespace Database\Seeders;

use App\Models\CvProPlan;
use Illuminate\Database\Seeder;

class CvProPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            // 1. Free Starter
            [
                'code' => 'starter_free',
                'type' => 'subscription',
                'name' => [
                    'id' => 'Free Starter',
                    'en' => 'Free Starter',
                ],
                'badge' => 'GRATIS',
                'description' => [
                    'id' => 'Akses dasar untuk eksplorasi dan pembuatan 1 resume ATS profesional.',
                    'en' => 'Basic access for exploration and building 1 professional ATS resume.',
                ],
                'price_idr' => 0,
                'price_usd' => 0,
                'billing_cycle' => 'monthly',
                'quotas' => [
                    'resumes_limit' => 1,
                    'ats_audits_limit' => 3,
                    'tailor_cv_limit' => 1,
                    'mock_interviews_limit' => 1,
                    'outreach_letters_limit' => 1,
                    'linkedin_packs_limit' => 0,
                    'ai_credits' => 10,
                ],
                'features' => [
                    '1 ATS-Friendly Resume Slot',
                    '3x Real-Time ATS Quality Audits',
                    '1x AI Job Description Tailoring',
                    '1x Mock Interview Voice Simulation',
                    'High-DPI PDF & Web Link Export',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],

            // 2. Pro Career (Tier A)
            [
                'code' => 'pro_career',
                'type' => 'subscription',
                'name' => [
                    'id' => 'Pro Career (Paket A)',
                    'en' => 'Pro Career (Tier A)',
                ],
                'badge' => 'POPULER',
                'description' => [
                    'id' => 'Paket akselerasi karir lengkap untuk pelamar aktif dengan fitur AI Tailor dan Mock Interview intensif.',
                    'en' => 'Complete career accelerator for active job seekers with AI Tailoring and intensive Mock Interviews.',
                ],
                'price_idr' => 49000,
                'price_usd' => 3.50,
                'billing_cycle' => 'monthly',
                'quotas' => [
                    'resumes_limit' => 5,
                    'ats_audits_limit' => -1, // Unlimited
                    'tailor_cv_limit' => 15,
                    'mock_interviews_limit' => 5,
                    'outreach_letters_limit' => 10,
                    'linkedin_packs_limit' => 5,
                    'ai_credits' => 100,
                ],
                'features' => [
                    '5 ATS Multi-Version Resumes',
                    'Unlimited ATS Scoring & Weak Verb Linter',
                    '15x Precision AI Job Tailoring & Diff',
                    '5x Full Mock Interviews (STAR Evaluation & Audio)',
                    '5x LinkedIn Personal Branding Generator',
                    '10x Outreach & Thank You Letters',
                    'Priority AI Generation Speed',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],

            // 3. Ultimate Executive (Tier B)
            [
                'code' => 'ultimate_exec',
                'type' => 'subscription',
                'name' => [
                    'id' => 'Ultimate Executive (Paket B)',
                    'en' => 'Ultimate Executive (Tier B)',
                ],
                'badge' => 'BEST VALUE',
                'description' => [
                    'id' => 'Akses tak terbatas untuk profesional senior, lead engineer, dan pencari kerja agresif.',
                    'en' => 'Unlimited access for senior professionals, lead engineers, and aggressive job hunters.',
                ],
                'price_idr' => 99000,
                'price_usd' => 6.90,
                'billing_cycle' => 'monthly',
                'quotas' => [
                    'resumes_limit' => -1, // Unlimited
                    'ats_audits_limit' => -1,
                    'tailor_cv_limit' => 50,
                    'mock_interviews_limit' => 20,
                    'outreach_letters_limit' => -1,
                    'linkedin_packs_limit' => -1,
                    'ai_credits' => 300,
                ],
                'features' => [
                    'Unlimited Resumes & Custom Templates',
                    '50x Precision AI Job Tailoring & ATS Audits',
                    '20x Mock Interview Voice Sessions with STAR Scoring',
                    'Unlimited LinkedIn Personal Branding Suite',
                    'Unlimited Post-Interview Outreach Letters',
                    'Job Hub Kanban & Salary Negotiation Co-Pilot',
                    'VIP Priority API Queue & Humanized Tone Toggle',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],

            // 4. All-Access VIP Quarterly Pass (Tier C)
            [
                'code' => 'enterprise_vip',
                'type' => 'subscription',
                'name' => [
                    'id' => 'VIP Career Sprint 3-Bulan (Paket C)',
                    'en' => 'VIP Career Sprint 3-Months (Tier C)',
                ],
                'badge' => 'HEMAT 40%',
                'description' => [
                    'id' => 'Pass lengkap 90 hari untuk persiapan transisi karir global dan remote work internasional.',
                    'en' => 'Complete 90-day pass for global career transitions and international remote work.',
                ],
                'price_idr' => 199000,
                'price_usd' => 13.90,
                'billing_cycle' => 'quarterly',
                'quotas' => [
                    'resumes_limit' => -1,
                    'ats_audits_limit' => -1,
                    'tailor_cv_limit' => 150,
                    'mock_interviews_limit' => 60,
                    'outreach_letters_limit' => -1,
                    'linkedin_packs_limit' => -1,
                    'ai_credits' => 1000,
                ],
                'features' => [
                    'Akses Penuh 90 Hari (3 Bulan)',
                    '150x AI Job Tailoring & Keuangan Pro',
                    '60x Audio Mock Interview Sessions',
                    'LinkedIn Authority Engine & Content Hooks',
                    'Semua Template ATS Klasik, Modern & Tech Minimal',
                    'Dukungan Teknis Langsung Neriah Pro',
                ],
                'is_active' => true,
                'sort_order' => 4,
            ],

            // 5. A La Carte: 5x AI Tailor CV Booster
            [
                'code' => 'topup_tailor_5',
                'type' => 'topup',
                'name' => [
                    'id' => 'Top-Up: +5x AI Job Tailor CV',
                    'en' => 'Top-Up: +5x AI Job Tailor CV',
                ],
                'badge' => 'INSTANT BOOSTER',
                'description' => [
                    'id' => 'Tambah kuota 5x penyesuaian CV terhadap lowongan kerja target tanpa harus berlangganan bulanan.',
                    'en' => 'Add 5x CV tailoring quota to target job descriptions without a monthly subscription.',
                ],
                'price_idr' => 19000,
                'price_usd' => 1.30,
                'billing_cycle' => 'one_time',
                'quotas' => [
                    'tailor_cv_limit' => 5,
                    'ai_credits' => 25,
                ],
                'features' => [
                    '+5x Kuota AI Job Tailor CV',
                    'Diff Highlight Keyword & Skill',
                    'Berlaku Selamanya (No Expiry)',
                ],
                'is_active' => true,
                'sort_order' => 5,
            ],

            // 6. A La Carte: 3x Mock Interview STAR Simulator
            [
                'code' => 'topup_interview_3',
                'type' => 'topup',
                'name' => [
                    'id' => 'Top-Up: +3x Mock Interview STAR',
                    'en' => 'Top-Up: +3x Mock Interview STAR',
                ],
                'badge' => 'SIMULASI SUARA',
                'description' => [
                    'id' => 'Tambah kuota 3 sesi wawancara latihan lengkap dengan rekaman audio, transkrip, dan evaluasi STAR.',
                    'en' => 'Add 3 full interview simulation sessions with audio recording, transcript, and STAR scoring.',
                ],
                'price_idr' => 29000,
                'price_usd' => 1.95,
                'billing_cycle' => 'one_time',
                'quotas' => [
                    'mock_interviews_limit' => 3,
                    'ai_credits' => 30,
                ],
                'features' => [
                    '+3x Sesi Penuh Mock Interview Audio',
                    'Evaluasi STAR & Confidence Scoring',
                    'Berlaku Selamanya (No Expiry)',
                ],
                'is_active' => true,
                'sort_order' => 6,
            ],

            // 7. A La Carte: 100 Universal AI Credits
            [
                'code' => 'topup_credits_100',
                'type' => 'topup',
                'name' => [
                    'id' => 'Top-Up: 100 Universal AI Credits',
                    'en' => 'Top-Up: 100 Universal AI Credits',
                ],
                'badge' => 'FLEKSIBEL',
                'description' => [
                    'id' => 'Kredit token universal yang dapat digunakan untuk semua aksi AI (Tailor, Interview, LinkedIn, Summary).',
                    'en' => 'Universal token credits usable across all AI actions (Tailor, Interview, LinkedIn, Summary).',
                ],
                'price_idr' => 35000,
                'price_usd' => 2.40,
                'billing_cycle' => 'one_time',
                'quotas' => [
                    'ai_credits' => 100,
                ],
                'features' => [
                    '100 Poin Kredit AI Fleksibel',
                    'Bebas Dipakai di Fitur Mana Saja',
                    'Tidak Pernah Hangus (No Expiry)',
                ],
                'is_active' => true,
                'sort_order' => 7,
            ],
        ];

        foreach ($plans as $planData) {
            CvProPlan::updateOrCreate(
                ['code' => $planData['code']],
                $planData
            );
        }
    }
}
