<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\CmsGlobalSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CvProController extends Controller
{
    /**
     * Display the CV Pro Studio SaaS Interface.
     */
    public function index(): View
    {
        $globalSettings = CmsGlobalSetting::getAllCached();

        $isCvProEnabled = (bool) ($globalSettings['feature_enable_cv_pro']->value ?? true);
        $isMidtransMode = (bool) ($globalSettings['midtrans_compliance_strict_mode']->value ?? false);

        if (! $isCvProEnabled || $isMidtransMode) {
            $user = auth()->user();
            $isSuperAdmin = $user && ($user->hasRole('super_admin') || $user->email === 'yoseph.iriandi.tambunan@gmail.com');
            if (! $isSuperAdmin) {
                abort(404);
            }
        }

        // Initial default resume data for instant interactive editing
        $initialData = [
            'personal_info' => [
                'name' => 'Alex Pratama, S.Kom',
                'title' => 'Senior Full Stack & Systems Architect',
                'email' => 'alex.pratama@neriahpro.com',
                'phone' => '+62 812-3456-7890',
                'location' => 'Jakarta, Indonesia',
                'linkedin' => 'linkedin.com/in/alexpratama',
                'website' => 'https://neriahpro.com',
                'summary' => 'Insinyur perangkat lunak dengan pengalaman 6+ tahun dalam merancang arsitektur sistem berskala tinggi menggunakan Laravel 13, React 19, PostgreSQL, dan arsitektur terdistribusi. Berhasil memangkas biaya infrastruktur hingga 45% dan meningkatkan throughput query database hingga 300% pada platform enterprise.',
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
                [
                    'id' => 2,
                    'name' => 'High-Performance Database Tuning & Optimization',
                    'issuer' => 'EnterpriseDB',
                    'year' => '2022',
                ],
            ],
            'projects' => [
                [
                    'id' => 1,
                    'name' => 'Project OS & PRD Generator Platform',
                    'role' => 'Principal Architect',
                    'description' => 'Platform sintesis otomatis Product Requirements Document (PRD) dan skema ERD arsitektur modern berbasis AI.',
                    'link' => 'https://neriahpro.com/blueprint',
                ],
            ],
            'references' => [
                [
                    'id' => 1,
                    'name' => 'Dr. Hendra Gunawan, M.T.',
                    'title' => 'Chief Technology Officer (CTO)',
                    'company' => 'Neriah Pro Tech Hub',
                    'email' => 'hendra.gunawan@neriahpro.com',
                    'phone' => '+62 811-9876-5432',
                    'note' => 'Atasan langsung selama 3 tahun dalam proyek pengembangan sistem enterprise skala nasional.',
                ],
            ],
            'section_order' => [
                'experiences',
                'education',
                'skills',
                'projects',
                'certifications',
                'references',
            ],
        ];

        $featureFlags = [
            'enable_cv_pro' => (bool) ($globalSettings['feature_enable_cv_pro']->value ?? true),
            'enable_pricing' => (bool) ($globalSettings['feature_enable_cv_pricing']->value ?? true),
            'enable_job_hub' => (bool) ($globalSettings['feature_enable_cv_job_hub']->value ?? true),
            'enable_keuangan' => (bool) ($globalSettings['feature_enable_cv_keuangan']->value ?? true),
            'enable_mock_interview' => (bool) ($globalSettings['feature_enable_cv_mock_interview']->value ?? true),
            'enable_linkedin_suite' => (bool) ($globalSettings['feature_enable_cv_linkedin_suite']->value ?? true),
        ];

        return view('cv-pro.index', [
            'globalSettings' => $globalSettings,
            'initialData' => $initialData,
            'featureFlags' => $featureFlags,
        ]);
    }

    /**
     * Display a clean, public, ATS-parseable, and printable version of the resume.
     */
    public function show(string $slug): View
    {
        $globalSettings = CmsGlobalSetting::getAllCached();
        $isCvProEnabled = (bool) ($globalSettings['feature_enable_cv_pro']->value ?? true);
        $isMidtransStrict = (bool) ($globalSettings['midtrans_compliance_strict_mode']->value ?? false);

        if ((! $isCvProEnabled || $isMidtransStrict) && ! auth()->user()?->isSuperAdmin()) {
            abort(404);
        }

        $resume = Resume::where('slug', $slug)->firstOrFail();

        return view('cv-pro.show', [
            'resume' => $resume,
            'content' => $resume->content ?? [],
            'globalSettings' => $globalSettings,
        ]);
    }
}
