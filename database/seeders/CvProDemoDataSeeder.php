<?php

namespace Database\Seeders;

use App\Models\CvProPlan;
use App\Models\CvQuotaTransaction;
use App\Models\InterviewSession;
use App\Models\OutreachLetter;
use App\Models\Resume;
use App\Models\User;
use App\Models\UserCvQuota;
use App\Services\CvPro\CvAiService;
use App\Services\CvPro\CvQuotaService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CvProDemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get or create Super Admin User
        $superAdmin = User::where('email', 'yoseph.iriandi.tambunan@gmail.com')->first();
        if (!$superAdmin) {
            $superAdmin = User::create([
                'name' => 'Yoseph Iriandi Tambunan',
                'email' => 'yoseph.iriandi.tambunan@gmail.com',
                'password' => bcrypt('#T4mbun4n#'),
            ]);
        }

        // 2. Get Pro & Ultimate Plans
        $proPlan = CvProPlan::where('code', 'pro_career')->first();
        $ultimatePlan = CvProPlan::where('code', 'ultimate_exec')->first();

        // 3. Assign Ultimate Executive Quota to Super Admin
        if ($superAdmin && $ultimatePlan) {
            CvQuotaService::applyPlan($superAdmin, $ultimatePlan);
        }

        // 4. Seed Demo Resumes
        $resumeContent = [
            'personal_info' => [
                'name' => 'Alex Pratama, S.Kom',
                'title' => 'Lead Cloud Systems Architect',
                'email' => 'alex.pratama@neriahpro.com',
                'phone' => '+62 812-3456-7890',
                'location' => 'Jakarta, Indonesia',
                'linkedin' => 'linkedin.com/in/alexpratama',
                'website' => 'https://neriahpro.com',
                'summary' => 'Insinyur perangkat lunak dengan pengalaman 6+ tahun dalam merancang arsitektur sistem berskala tinggi menggunakan Laravel 13, React 19, PostgreSQL, dan arsitektur terdistribusi. Berhasil memangkas biaya infrastruktur cloud hingga 45% dan meningkatkan throughput query database hingga 300% pada platform enterprise.',
            ],
            'experiences' => [
                [
                    'id' => 1,
                    'company' => 'Neriah Pro Enterprise',
                    'role' => 'Lead Systems Architect',
                    'period' => '2023 - Sekarang',
                    'location' => 'Jakarta / Remote',
                    'description' => 'Memimpin rekayasa arsitektur cloud untuk 15+ aplikasi enterprise klien.',
                    'bullets' => [
                        'Mengarsitektur sistem micro-monolith ber-throughput tinggi dengan kompleksitas query O(1) keystone pagination.',
                        'Memangkas latensi respon API sebesar 65% melalui integrasi multi-tier Redis caching dan Postgres index tuning.',
                        'Memimpin tim 8 engineer dan menerapkan standar CI/CD otomatis zero-downtime deployment.',
                    ],
                ],
                [
                    'id' => 2,
                    'company' => 'Fintech Nusantara Ltd',
                    'role' => 'Senior Backend Engineer',
                    'period' => '2020 - 2023',
                    'location' => 'Jakarta, Indonesia',
                    'description' => 'Mengembangkan core payment gateway dan ledger akuntansi keuangan terdesentralisasi.',
                    'bullets' => [
                        'Memproses lebih dari Rp 50 Miliar volume transaksi bulanan dengan reliabilitas 99.98% uptime.',
                        'Merekayasa sistem idempotency webhook Midtrans dan deteksi fraud otomatis.',
                    ],
                ],
            ],
            'education' => [
                [
                    'id' => 1,
                    'institution' => 'Institut Teknologi Bandung (ITB)',
                    'degree' => 'Sarjana Komputer (S.Kom)',
                    'field' => 'Teknik Informatika & Rekayasa Perangkat Lunak',
                    'year' => '2016 - 2020',
                    'gpa' => '3.85 / 4.00 (Cum Laude)',
                ],
            ],
            'skills' => [
                'Laravel 13 & PHP 8.4',
                'Distributed Database Systems',
                'React 19 & Next.js',
                'Docker & Nixpacks CI/CD',
                'Livewire 4 & Flux UI',
                'Redis Cache & Queues',
                'System Architecture O(1)',
                'RESTful & Webhook APIs',
                'Midtrans Payment Gateway',
                'Automated Testing Pest',
            ],
            'certifications' => [
                [
                    'id' => 1,
                    'name' => 'AWS Certified Solutions Architect - Associate',
                    'issuer' => 'Amazon Web Services',
                    'year' => '2023',
                ],
            ],
            'projects' => [
                [
                    'id' => 1,
                    'name' => 'Project OS & PRD Generator Platform',
                    'role' => 'Principal Architect',
                    'description' => 'Platform sintesis otomatis Product Requirements Document (PRD) dan skema ERD PostgreSQL berbasis LLM AI.',
                    'link' => 'https://neriahpro.com/blueprint',
                ],
            ],
        ];

        $atsAudit = CvAiService::lintResume($resumeContent, 'id');

        $demoResume = Resume::updateOrCreate(
            ['slug' => 'alex-pratama-lead-cloud-systems-architect'],
            [
                'user_id' => $superAdmin->id,
                'title' => 'Resume Alex Pratama - Lead Systems Architect',
                'target_role' => 'Lead Cloud Systems Architect',
                'template' => 'tech_minimal',
                'font_family' => 'Inter',
                'primary_color' => '#f59e0b',
                'content' => $resumeContent,
                'is_public' => true,
                'ats_score' => $atsAudit['overall_score'] ?? 92,
                'ats_feedback' => $atsAudit,
            ]
        );

        // 5. Seed Demo Mock Interview Session
        InterviewSession::updateOrCreate(
            ['resume_id' => $demoResume->id],
            [
                'user_id' => $superAdmin->id,
                'target_company' => 'Neriah Global Tech',
                'job_title' => 'Principal Cloud Architect',
                'job_description' => 'Memimpin rekayasa arsitektur cloud enterprise skala terdistribusi.',
                'questions' => [
                    'Ceritakan bagaimana Anda merancang arsitektur sistem yang mampu menangani jutaan transaksi tanpa lonjakan biaya server?',
                    'Bagaimana pendekatan Anda dalam menerapkan Scope Lock OS untuk memitigasi scope creep?',
                ],
                'answers' => [
                    'Saya mengimplementasikan partisi basis data PostgreSQL dan pagination berbasis keyset pointer O(1) alih-alih OFFSET. Saya juga menempatkan Redis buffer queue untuk menangani lonjakan webhook simultan.',
                    'Kami memformalkan seluruh spesifikasi deliverable di PRD awal dan mengikatnya dalam kontrak digital Midtrans ber-token SHA-256.',
                ],
                'evaluation' => [
                    'situation' => 'Menghadapi lonjakan transaksi pembayaran digital skala tinggi.',
                    'task' => 'Merancang arsitektur yang efisien dan memangkas beban database.',
                    'action' => 'Mengganti query OFFSET dengan keyset cursor O(1) dan Redis queue.',
                    'result' => '12.000 RPS dengan CPU load stabil di bawah 40%.',
                    'score' => 90,
                    'feedback' => 'Jawaban terstruktur dengan formula STAR yang sangat konkret dan didukung metrik angka terukur.',
                ],
                'overall_score' => 88,
            ]
        );

        // 6. Seed Demo Outreach Letter
        OutreachLetter::updateOrCreate(
            [
                'resume_id' => $demoResume->id,
                'letter_type' => 'thank_you',
            ],
            [
                'user_id' => $superAdmin->id,
                'recipient_name' => 'VP of Engineering',
                'company_name' => 'Neriah Global Tech',
                'generated_content' => CvAiService::generateOutreachLetter(
                    'thank_you',
                    'Neriah Global Tech',
                    'Principal Cloud Architect',
                    'VP of Engineering',
                    $resumeContent,
                    'id'
                ),
            ]
        );
    }
}
