<?php

namespace App\Support;

use App\Models\VisionBlueprint;

/**
 * WorldDesignSystemCatalog
 *
 * Canonical catalog & CSS/Blade synthesizer of 10 Worldwide Frontend Design Systems.
 * Eliminates cloning Neriah Pro's internal zinc/emerald theme into generated client apps
 * by calibrating typography, surface hierarchy, contrast, border geometry, and interactive
 * tokens strictly to each project's domain background, industry ergonomics, and operator needs.
 *
 * All generated assets compile 100% locally via Vite + Tailwind CSS v4 (zero external CDNs).
 */
class WorldDesignSystemCatalog
{
    /**
     * Global taxonomy explaining worldwide frontend design system schools.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function worldwideTaxonomySummary(): array
    {
        return [
            [
                'school' => 'International Typographic & Swiss Structural Grid',
                'benchmarks' => 'Swiss Style, Linear, Atlassian ADG',
                'system_code' => 'SWISS_TECHNICAL_GRID',
                'best_for' => ['id' => 'SaaS B2B, Agensi PSA, Workflow Kolaboratif', 'en' => 'B2B SaaS, PSA Consulting, Collaborative Workflows'],
            ],
            [
                'school' => 'Editorial Broadsheet & Archival Print Typography',
                'benchmarks' => 'Financial Times, NYT Ink, Academic Archives',
                'system_code' => 'ARCHITECTURAL_EDITORIAL_SERIF',
                'best_for' => ['id' => 'EdTech, Hukum, Publikasi, Knowledge Portal', 'en' => 'EdTech, Legal, Publishing, Knowledge Portals'],
            ],
            [
                'school' => 'High-Density Fiduciary & Quant Terminal',
                'benchmarks' => 'Bloomberg Terminal, IBM Carbon Quant, Stripe Ledger',
                'system_code' => 'FIDUCIARY_QUANT_TERMINAL',
                'best_for' => ['id' => 'Fintech, Akuntansi Double-Entry, Treasury', 'en' => 'Fintech, Double-Entry Accounting, Treasury'],
            ],
            [
                'school' => 'Clinical WCAG AAA & Zero-Modal Cockpit',
                'benchmarks' => 'NHS Digital, Epic EMR Clarity, HL7 FHIR Consoles',
                'system_code' => 'CLINICAL_STERILE_PRECISION',
                'best_for' => ['id' => 'Klinik, Rumah Sakit, Farmasi FEFO, Lab EMR', 'en' => 'Clinics, Hospitals, FEFO Pharmacy, Lab EMR'],
            ],
            [
                'school' => 'Civic Sanctuary & GOV.UK Universal Accessibility',
                'benchmarks' => 'GOV.UK Design System, USWDS, WCAG 2.2 AAA',
                'system_code' => 'CIVIC_SANCTUARY_ACCESSIBLE',
                'best_for' => ['id' => 'Gereja, Yayasan, GovTech, Komunitas, Donor Portal', 'en' => 'Churches, Nonprofits, GovTech, Community & Donor Portals'],
            ],
            [
                'school' => 'Kinetic Commerce & High-Velocity Merchandising',
                'benchmarks' => 'Shopify Polaris, Vercel Geist, Omnichannel POS',
                'system_code' => 'SPATIAL_KINETIC_COMMERCE',
                'best_for' => ['id' => 'E-Commerce, POS Ritel, Gudang Multi-SKU', 'en' => 'E-Commerce, Retail POS, Multi-SKU Warehousing'],
            ],
            [
                'school' => 'ISA-101 High-Performance Industrial SCADA / HMI',
                'benchmarks' => 'Rockwell FactoryTalk, Siemens WinCC, Dispatch Grids',
                'system_code' => 'INDUSTRIAL_SCADA_TELEMETRY',
                'best_for' => ['id' => 'Manufaktur MRP, Lantai Pabrik, Logistik & Armada', 'en' => 'Manufacturing MRP, Shop Floor, Fleet & Logistics'],
            ],
            [
                'school' => 'Tactile Hospitality Concierge & Culinary Contrast',
                'benchmarks' => 'Hospitality Booking Suites, Forced-Dark KDS',
                'system_code' => 'HOSPITALITY_CONCIERGE_WARMTH',
                'best_for' => ['id' => 'Hotel, Villa, Venue Olahraga, F&B & Dapur KDS', 'en' => 'Hotels, Venues, Court Booking, F&B & KDS'],
            ],
            [
                'school' => 'Material 3 Tonal Elevation & Human-Centric Systems',
                'benchmarks' => 'Google Material 3, Rippling, Modern HRIS',
                'system_code' => 'HR_HUMAN_CENTRIC_TONAL',
                'best_for' => ['id' => 'HRIS, Payroll PPh 21, Jadwal Shift & Rekrutmen', 'en' => 'HRIS, Payroll, Shift Roster & Talent Ops'],
            ],
            [
                'school' => 'Broadcast Control Booth & Real-Time Signal Cockpit',
                'benchmarks' => 'OBS Studio, Broadcast Consoles, Live Audio Stage',
                'system_code' => 'REALTIME_MEDIA_COCKPIT',
                'best_for' => ['id' => 'Live Audio Interpreter, Streaming, WebRTC, Ticketing', 'en' => 'Live Audio Interpreter, Streaming, WebRTC, Ticketing'],
            ],
        ];
    }

    /**
     * All 10 Worldwide Domain-Calibrated Design Systems.
     * Note: Zero overlap with Neriah Pro's internal emerald/zinc palette.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            'SWISS_TECHNICAL_GRID' => [
                'code' => 'SWISS_TECHNICAL_GRID',
                'name' => [
                    'id' => 'Swiss Technical Grid (Minimalis Presisi B2B)',
                    'en' => 'Swiss Technical Grid (B2B Structural Precision)',
                ],
                'school' => 'International Typographic Style / Atlassian ADG',
                'sectors' => ['SECTOR_08_PROFESSIONAL_PSA'],
                'keywords' => ['saas', 'b2b', 'agency', 'konsultan', 'consulting', 'kanban', 'timesheet', 'crm', 'portal', 'proyek', 'project'],
                'default_mode' => 'light',
                'radius_token' => '2px',
                'border_weight' => '1px',
                'font_heading' => "'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif",
                'font_body' => "'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif",
                'font_mono' => "'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, monospace",
                'tokens' => [
                    'light' => [
                        'bg' => '#F8FAFC',
                        'surface' => '#FFFFFF',
                        'surface_muted' => '#F1F5F9',
                        'border' => '#CBD5E1',
                        'border_strong' => '#64748B',
                        'text' => '#0F172A',
                        'text_muted' => '#334155',
                        'primary' => '#1D4ED8',
                        'primary_hover' => '#1E40AF',
                        'primary_ink' => '#FFFFFF',
                        'accent' => '#0369A1',
                        'accent_tint' => '#E0F2FE',
                        'accent_tint_ink' => '#0C4A6E',
                        'danger' => '#B91C1C',
                        'warning' => '#B45309',
                        'success' => '#047857',
                    ],
                    'dark' => [
                        'bg' => '#0B1120',
                        'surface' => '#111827',
                        'surface_muted' => '#1E293B',
                        'border' => '#334155',
                        'border_strong' => '#475569',
                        'text' => '#F8FAFC',
                        'text_muted' => '#CBD5E1',
                        'primary' => '#3B82F6',
                        'primary_hover' => '#60A5FA',
                        'primary_ink' => '#0B1120',
                        'accent' => '#38BDF8',
                        'accent_tint' => '#0C4A6E',
                        'accent_tint_ink' => '#E0F2FE',
                        'danger' => '#F87171',
                        'warning' => '#FBBF24',
                        'success' => '#34D399',
                    ],
                ],
                'rationale' => [
                    'id' => 'Grid tipografi Swiss berakurasi tinggi dengan aksen Ultramarine Cobalt untuk kejelasan operasional B2B & manajemen deliverable.',
                    'en' => 'High-clarity Swiss typographic grid with Ultramarine Cobalt accents engineered for B2B workflows and milestone tracking.',
                ],
            ],

            'ARCHITECTURAL_EDITORIAL_SERIF' => [
                'code' => 'ARCHITECTURAL_EDITORIAL_SERIF',
                'name' => [
                    'id' => 'Editorial Broadsheet & Archival Serif',
                    'en' => 'Architectural Editorial Serif (Broadsheet Clarity)',
                ],
                'school' => 'Financial Times Ink / Academic Archive Architecture',
                'sectors' => ['SECTOR_07_EDTECH_MEMBERSHIP'],
                'keywords' => ['edukasi', 'education', 'kampus', 'sekolah', 'lms', 'kursus', 'artikel', 'hukum', 'legal', 'arsip', 'library', 'perpustakaan', 'academy'],
                'default_mode' => 'light',
                'radius_token' => '0px',
                'border_weight' => '1px',
                'font_heading' => "Georgia, 'Times New Roman', ui-serif, serif",
                'font_body' => "system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif",
                'font_mono' => "ui-monospace, SFMono-Regular, Menlo, monospace",
                'tokens' => [
                    'light' => [
                        'bg' => '#FAF8F5',
                        'surface' => '#FFFFFF',
                        'surface_muted' => '#F3EFEA',
                        'border' => '#D6CFC4',
                        'border_strong' => '#57534E',
                        'text' => '#1C1917',
                        'text_muted' => '#44403C',
                        'primary' => '#991B1B',
                        'primary_hover' => '#7F1D1D',
                        'primary_ink' => '#FFFFFF',
                        'accent' => '#92400E',
                        'accent_tint' => '#FEF3C7',
                        'accent_tint_ink' => '#78350F',
                        'danger' => '#991B1B',
                        'warning' => '#92400E',
                        'success' => '#166534',
                    ],
                    'dark' => [
                        'bg' => '#141210',
                        'surface' => '#1E1B18',
                        'surface_muted' => '#292524',
                        'border' => '#44403C',
                        'border_strong' => '#78716C',
                        'text' => '#FAF8F5',
                        'text_muted' => '#D6D3D1',
                        'primary' => '#F87171',
                        'primary_hover' => '#FCA5A5',
                        'primary_ink' => '#141210',
                        'accent' => '#FBBF24',
                        'accent_tint' => '#451A03',
                        'accent_tint_ink' => '#FEF3C7',
                        'danger' => '#FCA5A5',
                        'warning' => '#FCD34D',
                        'success' => '#4ADE80',
                    ],
                ],
                'rationale' => [
                    'id' => 'Latar kertas hangat Broadsheet dengan heading Serif berwibawa untuk fokus baca panjang, kurikulum, dan dokumentasi hukum.',
                    'en' => 'Warm archival paper surface with authoritative serif display headings optimized for deep reading, curricula, and legal records.',
                ],
            ],

            'FIDUCIARY_QUANT_TERMINAL' => [
                'code' => 'FIDUCIARY_QUANT_TERMINAL',
                'name' => [
                    'id' => 'Fiduciary Quant Terminal (Monokrom Akuntansi)',
                    'en' => 'Fiduciary Quant Terminal (Ledger Precision)',
                ],
                'school' => 'Bloomberg / Stripe / Carbon Financial Systems',
                'sectors' => ['SECTOR_04_FINTECH_LEDGER'],
                'keywords' => ['fintech', 'accounting', 'akuntansi', 'jurnal', 'ledger', 'kas', 'keuangan', 'koperasi', 'pinjaman', 'bank', 'rekonsiliasi', 'treasury'],
                'default_mode' => 'light',
                'radius_token' => '2px',
                'border_weight' => '1px',
                'font_heading' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_body' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_mono' => "'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, monospace",
                'tokens' => [
                    'light' => [
                        'bg' => '#F4F4F5',
                        'surface' => '#FFFFFF',
                        'surface_muted' => '#E4E4E7',
                        'border' => '#A1A1AA',
                        'border_strong' => '#3F3F46',
                        'text' => '#09090B',
                        'text_muted' => '#27272A',
                        'primary' => '#1E3A8A',
                        'primary_hover' => '#1E40AF',
                        'primary_ink' => '#FFFFFF',
                        'accent' => '#4338CA',
                        'accent_tint' => '#E0E7FF',
                        'accent_tint_ink' => '#1E1B4B',
                        'danger' => '#B91C1C',
                        'warning' => '#A16207',
                        'success' => '#15803D',
                    ],
                    'dark' => [
                        'bg' => '#09090B',
                        'surface' => '#121215',
                        'surface_muted' => '#18181B',
                        'border' => '#27272A',
                        'border_strong' => '#52525B',
                        'text' => '#FAFAFA',
                        'text_muted' => '#D4D4D8',
                        'primary' => '#60A5FA',
                        'primary_hover' => '#93C5FD',
                        'primary_ink' => '#09090B',
                        'accent' => '#818CF8',
                        'accent_tint' => '#1E1B4B',
                        'accent_tint_ink' => '#E0E7FF',
                        'danger' => '#F87171',
                        'warning' => '#FACC15',
                        'success' => '#4ADE80',
                    ],
                ],
                'rationale' => [
                    'id' => 'Terminal keuangan monokrom tajam dengan angka tabular sejajar (tabular-nums) dan kontras Royal Indigo untuk audit buku besar.',
                    'en' => 'Razor-sharp monochrome financial terminal with mandatory tabular figures and Royal Indigo hierarchy for audit reconciliation.',
                ],
            ],

            'CLINICAL_STERILE_PRECISION' => [
                'code' => 'CLINICAL_STERILE_PRECISION',
                'name' => [
                    'id' => 'Clinical Sterile Precision (WCAG AAA Medis)',
                    'en' => 'Clinical Sterile Precision (WCAG AAA Medical)',
                ],
                'school' => 'NHS Digital / HL7 FHIR Zero-Modal Cockpit',
                'sectors' => ['SECTOR_03_HEALTHCARE_CLINICAL'],
                'keywords' => ['klinik', 'clinic', 'hospital', 'rumah sakit', 'rekam medis', 'emr', 'pasien', 'dokter', 'apotek', 'farmasi', 'resep', 'laboratorium', 'kesehatan'],
                'default_mode' => 'light',
                'radius_token' => '2px',
                'border_weight' => '1px',
                'font_heading' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_body' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_mono' => "ui-monospace, SFMono-Regular, Menlo, monospace",
                'tokens' => [
                    'light' => [
                        'bg' => '#F0F9FF',
                        'surface' => '#FFFFFF',
                        'surface_muted' => '#E0F2FE',
                        'border' => '#BAE6FD',
                        'border_strong' => '#0284C7',
                        'text' => '#0F172A',
                        'text_muted' => '#1E293B',
                        'primary' => '#0369A1',
                        'primary_hover' => '#075985',
                        'primary_ink' => '#FFFFFF',
                        'accent' => '#BE123C',
                        'accent_tint' => '#FFE4E6',
                        'accent_tint_ink' => '#881337',
                        'danger' => '#BE123C',
                        'warning' => '#B45309',
                        'success' => '#0F766E',
                    ],
                    'dark' => [
                        'bg' => '#081424',
                        'surface' => '#0F2138',
                        'surface_muted' => '#162F4D',
                        'border' => '#1E3A5F',
                        'border_strong' => '#38BDF8',
                        'text' => '#F8FAFC',
                        'text_muted' => '#CBD5E1',
                        'primary' => '#38BDF8',
                        'primary_hover' => '#7DD3FC',
                        'primary_ink' => '#081424',
                        'accent' => '#FB7185',
                        'accent_tint' => '#4C0519',
                        'accent_tint_ink' => '#FFE4E6',
                        'danger' => '#FB7185',
                        'warning' => '#FBBF24',
                        'success' => '#2DD4BF',
                    ],
                ],
                'rationale' => [
                    'id' => 'Palet medis steril rendah silau dengan warna Clinical Cerulean & Crimson peringatan alergi untuk akurasi resep tanpa kelelahan mata.',
                    'en' => 'Low-glare clinical daylight surface pairing Medical Cerulean with high-visibility Crimson allergy warnings.',
                ],
            ],

            'CIVIC_SANCTUARY_ACCESSIBLE' => [
                'code' => 'CIVIC_SANCTUARY_ACCESSIBLE',
                'name' => [
                    'id' => 'Civic Sanctuary & Universal AAA (Gereja / NGO / GovTech)',
                    'en' => 'Civic Sanctuary & Universal AAA (Church / NGO / Civic)',
                ],
                'school' => 'GOV.UK Design System / Civic Sanctuary Heritage',
                'sectors' => ['SECTOR_12_GOVTECH_NONPROFIT'],
                'keywords' => ['gereja', 'church', 'ibadah', 'jemaat', 'ministry', 'donasi', 'zakat', 'wakaf', 'yayasan', 'pemerintah', 'gov', 'desa', 'warga', 'komunitas', 'relawan'],
                'default_mode' => 'light',
                'radius_token' => '4px',
                'border_weight' => '1.5px',
                'font_heading' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_body' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_mono' => "ui-monospace, SFMono-Regular, Menlo, monospace",
                'tokens' => [
                    'light' => [
                        'bg' => '#FDFBF7',
                        'surface' => '#FFFFFF',
                        'surface_muted' => '#F3EFE6',
                        'border' => '#D4CBB8',
                        'border_strong' => '#475569',
                        'text' => '#0F172A',
                        'text_muted' => '#334155',
                        'primary' => '#1E40AF',
                        'primary_hover' => '#1E3A8A',
                        'primary_ink' => '#FFFFFF',
                        'accent' => '#B45309',
                        'accent_tint' => '#FEF3C7',
                        'accent_tint_ink' => '#78350F',
                        'danger' => '#B91C1C',
                        'warning' => '#B45309',
                        'success' => '#166534',
                    ],
                    'dark' => [
                        'bg' => '#0F172A',
                        'surface' => '#1E293B',
                        'surface_muted' => '#263349',
                        'border' => '#334155',
                        'border_strong' => '#94A3B8',
                        'text' => '#F8FAFC',
                        'text_muted' => '#E2E8F0',
                        'primary' => '#60A5FA',
                        'primary_hover' => '#93C5FD',
                        'primary_ink' => '#0F172A',
                        'accent' => '#F59E0B',
                        'accent_tint' => '#451A03',
                        'accent_tint_ink' => '#FEF3C7',
                        'danger' => '#FCA5A5',
                        'warning' => '#FCD34D',
                        'success' => '#4ADE80',
                    ],
                ],
                'rationale' => [
                    'id' => 'Linen hangat menenangkan dengan fokus WCAG AAA 7:1+, aksen Sapphire & Emas hangat, serta target sentuh ramah lansia/komunitas.',
                    'en' => 'Welcoming Sanctuary Linen paired with Royal Sapphire & Sovereign Amber accents enforcing 7:1+ WCAG AAA readability across ages.',
                ],
            ],

            'SPATIAL_KINETIC_COMMERCE' => [
                'code' => 'SPATIAL_KINETIC_COMMERCE',
                'name' => [
                    'id' => 'Spatial Kinetic Commerce (Polaris & Omnichannel POS)',
                    'en' => 'Spatial Kinetic Commerce (Polaris & Retail POS)',
                ],
                'school' => 'Shopify Polaris / Vercel Geist Retail Architecture',
                'sectors' => ['SECTOR_01_COMMERCE_OMNICHANNEL'],
                'keywords' => ['toko', 'ecommerce', 'pos', 'kasir', 'retail', 'ritel', 'gudang', 'stok', 'sku', 'barcode', 'produk', 'katalog', 'grosir'],
                'default_mode' => 'light',
                'radius_token' => '4px',
                'border_weight' => '1px',
                'font_heading' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_body' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_mono' => "ui-monospace, SFMono-Regular, Menlo, monospace",
                'tokens' => [
                    'light' => [
                        'bg' => '#F9FAFB',
                        'surface' => '#FFFFFF',
                        'surface_muted' => '#F3F4F6',
                        'border' => '#D1D5DB',
                        'border_strong' => '#374151',
                        'text' => '#111827',
                        'text_muted' => '#374151',
                        'primary' => '#4F46E5',
                        'primary_hover' => '#4338CA',
                        'primary_ink' => '#FFFFFF',
                        'accent' => '#0F766E',
                        'accent_tint' => '#EEF2FF',
                        'accent_tint_ink' => '#312E81',
                        'danger' => '#DC2626',
                        'warning' => '#D97706',
                        'success' => '#059669',
                    ],
                    'dark' => [
                        'bg' => '#0A0A0A',
                        'surface' => '#141417',
                        'surface_muted' => '#1F2026',
                        'border' => '#2E3039',
                        'border_strong' => '#52525B',
                        'text' => '#F9FAFB',
                        'text_muted' => '#D1D5DB',
                        'primary' => '#818CF8',
                        'primary_hover' => '#A5B4FC',
                        'primary_ink' => '#0A0A0A',
                        'accent' => '#2DD4BF',
                        'accent_tint' => '#312E81',
                        'accent_tint_ink' => '#E0E7FF',
                        'danger' => '#F87171',
                        'warning' => '#FBBF24',
                        'success' => '#34D399',
                    ],
                ],
                'rationale' => [
                    'id' => 'Kanvas ritel bersih beraksen Electric Indigo agar foto produk, harga promo, dan tombol Checkout Kasir menonjol seketika.',
                    'en' => 'High-contrast retail canvas with Electric Indigo CTAs calibrated for rapid barcode scan, variant grids, and checkout clarity.',
                ],
            ],

            'INDUSTRIAL_SCADA_TELEMETRY' => [
                'code' => 'INDUSTRIAL_SCADA_TELEMETRY',
                'name' => [
                    'id' => 'Industrial SCADA & Logistics Telemetry (ISA-101 HMI)',
                    'en' => 'Industrial SCADA & Logistics Telemetry (ISA-101 HMI)',
                ],
                'school' => 'ISA-101 High-Performance HMI / Dispatch Systems',
                'sectors' => ['SECTOR_05_INDUSTRIAL_MRP', 'SECTOR_06_LOGISTICS_FLEET'],
                'keywords' => ['pabrik', 'manufaktur', 'mrp', 'bom', 'produksi', 'logistik', 'armada', 'kurir', 'kargo', 'ekspedisi', 'pengiriman', 'fleet', 'gudang'],
                'default_mode' => 'light',
                'radius_token' => '2px',
                'border_weight' => '2px',
                'font_heading' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_body' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_mono' => "'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, monospace",
                'tokens' => [
                    'light' => [
                        'bg' => '#ECEFF4',
                        'surface' => '#FFFFFF',
                        'surface_muted' => '#E5E9F0',
                        'border' => '#94A3B8',
                        'border_strong' => '#1E293B',
                        'text' => '#0F172A',
                        'text_muted' => '#334155',
                        'primary' => '#B45309',
                        'primary_hover' => '#92400E',
                        'primary_ink' => '#FFFFFF',
                        'accent' => '#1E3A8A',
                        'accent_tint' => '#FEF3C7',
                        'accent_tint_ink' => '#78350F',
                        'danger' => '#B91C1C',
                        'warning' => '#D97706',
                        'success' => '#15803D',
                    ],
                    'dark' => [
                        'bg' => '#111317',
                        'surface' => '#1A1D24',
                        'surface_muted' => '#252932',
                        'border' => '#3E4451',
                        'border_strong' => '#9CA3AF',
                        'text' => '#F8FAFC',
                        'text_muted' => '#D1D5DB',
                        'primary' => '#F59E0B',
                        'primary_hover' => '#FBBF24',
                        'primary_ink' => '#111317',
                        'accent' => '#60A5FA',
                        'accent_tint' => '#451A03',
                        'accent_tint_ink' => '#FEF3C7',
                        'danger' => '#F87171',
                        'warning' => '#FBBF24',
                        'success' => '#4ADE80',
                    ],
                ],
                'rationale' => [
                    'id' => 'Border struktural 2px tegas dengan warna Safety Amber & Steel Navy yang tetap jelas terbaca di tablet gudang maupun bawah terik matahari.',
                    'en' => 'Heavy 2px structural framing with Safety Amber & Steel Navy engineered for glove-friendly shop floor tablets and dispatch boards.',
                ],
            ],

            'HOSPITALITY_CONCIERGE_WARMTH' => [
                'code' => 'HOSPITALITY_CONCIERGE_WARMTH',
                'name' => [
                    'id' => 'Hospitality Concierge Warmth (Hotel & Kuliner)',
                    'en' => 'Hospitality Concierge Warmth (Booking & Culinary)',
                ],
                'school' => 'Boutique Hospitality & High-Contrast KDS System',
                'sectors' => ['SECTOR_02_HOSPITALITY_SPACE', 'SECTOR_09_FOOD_BEVERAGE'],
                'keywords' => ['hotel', 'villa', 'resort', 'booking', 'reservasi', 'kamar', 'lapangan', 'restoran', 'kafe', 'kuliner', 'fnb', 'dapur', 'kds'],
                'default_mode' => 'light',
                'radius_token' => '4px',
                'border_weight' => '1px',
                'font_heading' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_body' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_mono' => "ui-monospace, SFMono-Regular, Menlo, monospace",
                'tokens' => [
                    'light' => [
                        'bg' => '#FAF7F2',
                        'surface' => '#FFFFFF',
                        'surface_muted' => '#F3ECE2',
                        'border' => '#D6C7B2',
                        'border_strong' => '#574C3F',
                        'text' => '#1C1612',
                        'text_muted' => '#4A3F35',
                        'primary' => '#C2410C',
                        'primary_hover' => '#9A3412',
                        'primary_ink' => '#FFFFFF',
                        'accent' => '#3F6212',
                        'accent_tint' => '#FFEDD5',
                        'accent_tint_ink' => '#7C2D12',
                        'danger' => '#B91C1C',
                        'warning' => '#B45309',
                        'success' => '#3F6212',
                    ],
                    'dark' => [
                        'bg' => '#171311',
                        'surface' => '#211B18',
                        'surface_muted' => '#2C2420',
                        'border' => '#443831',
                        'border_strong' => '#A8968A',
                        'text' => '#FAF7F2',
                        'text_muted' => '#DFD3C8',
                        'primary' => '#FB923C',
                        'primary_hover' => '#FDBA74',
                        'primary_ink' => '#171311',
                        'accent' => '#A3E635',
                        'accent_tint' => '#431407',
                        'accent_tint_ink' => '#FFEDD5',
                        'danger' => '#FCA5A5',
                        'warning' => '#FBBF24',
                        'success' => '#A3E635',
                    ],
                ],
                'rationale' => [
                    'id' => 'Nuansa Limestone hangat dengan aksen Burnished Copper untuk pengalaman tamu ramah sekaligus mendukung mode gelap dapur KDS.',
                    'en' => 'Warm Limestone canvas accented with Burnished Copper for welcoming guest reservation flows and low-glare kitchen mode.',
                ],
            ],

            'HR_HUMAN_CENTRIC_TONAL' => [
                'code' => 'HR_HUMAN_CENTRIC_TONAL',
                'name' => [
                    'id' => 'Human-Centric Tonal (Material 3 / People Ops)',
                    'en' => 'Human-Centric Tonal (Material 3 / HRIS)',
                ],
                'school' => 'Google Material 3 Tonal Elevation / Workday',
                'sectors' => ['SECTOR_11_HRIS_INTERNAL_OPS'],
                'keywords' => ['hris', 'payroll', 'gaji', 'absensi', 'cuti', 'karyawan', 'pegawai', 'rekrutmen', 'shift', 'roster', 'pph', 'bpjs'],
                'default_mode' => 'light',
                'radius_token' => '4px',
                'border_weight' => '1px',
                'font_heading' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_body' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_mono' => "ui-monospace, SFMono-Regular, Menlo, monospace",
                'tokens' => [
                    'light' => [
                        'bg' => '#F8FAFC',
                        'surface' => '#FFFFFF',
                        'surface_muted' => '#F3E8FF',
                        'border' => '#CBD5E1',
                        'border_strong' => '#4C1D95',
                        'text' => '#0F172A',
                        'text_muted' => '#334155',
                        'primary' => '#6D28D9',
                        'primary_hover' => '#5B21B6',
                        'primary_ink' => '#FFFFFF',
                        'accent' => '#4338CA',
                        'accent_tint' => '#EDE9FE',
                        'accent_tint_ink' => '#4C1D95',
                        'danger' => '#B91C1C',
                        'warning' => '#B45309',
                        'success' => '#047857',
                    ],
                    'dark' => [
                        'bg' => '#0F101A',
                        'surface' => '#171928',
                        'surface_muted' => '#22253A',
                        'border' => '#323654',
                        'border_strong' => '#A78BFA',
                        'text' => '#F8FAFC',
                        'text_muted' => '#CBD5E1',
                        'primary' => '#A78BFA',
                        'primary_hover' => '#C4B5FD',
                        'primary_ink' => '#0F101A',
                        'accent' => '#818CF8',
                        'accent_tint' => '#2E1065',
                        'accent_tint_ink' => '#EDE9FE',
                        'danger' => '#F87171',
                        'warning' => '#FBBF24',
                        'success' => '#34D399',
                    ],
                ],
                'rationale' => [
                    'id' => 'Elevasi tonal Royal Violet modern yang nyaman dilihat berjam-jam oleh tim HR & Finance saat menyusun shift dan slip gaji 3-kolom.',
                    'en' => 'Clean Royal Violet tonal hierarchy built for prolonged People Ops sessions, shift scheduling, and 3-column payroll inspection.',
                ],
            ],

            'REALTIME_MEDIA_COCKPIT' => [
                'code' => 'REALTIME_MEDIA_COCKPIT',
                'name' => [
                    'id' => 'Broadcast & Real-Time Signal Cockpit (Audio/Video/Event)',
                    'en' => 'Broadcast & Real-Time Signal Cockpit (Audio/WebRTC/Event)',
                ],
                'school' => 'OBS Broadcast Control Room / Live Audio Booth',
                'sectors' => ['SECTOR_10_MEDIA_TICKETING'],
                'keywords' => ['audio', 'interpreter', 'translator', 'penerjemah', 'live', 'webrtc', 'streaming', 'broadcast', 'realtime', 'real-time', 'websocket', 'reverb', 'livekit', 'tiket', 'ticketing', 'event', 'kursi', 'konser', 'stage', 'siaran'],
                'default_mode' => 'dark',
                'radius_token' => '3px',
                'border_weight' => '1px',
                'font_heading' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_body' => "system-ui, -apple-system, 'Segoe UI', sans-serif",
                'font_mono' => "'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, monospace",
                'tokens' => [
                    'light' => [
                        'bg' => '#F1F5F9',
                        'surface' => '#FFFFFF',
                        'surface_muted' => '#E2E8F0',
                        'border' => '#94A3B8',
                        'border_strong' => '#0F172A',
                        'text' => '#0F172A',
                        'text_muted' => '#334155',
                        'primary' => '#0369A1',
                        'primary_hover' => '#075985',
                        'primary_ink' => '#FFFFFF',
                        'accent' => '#4F46E5',
                        'accent_tint' => '#E0F2FE',
                        'accent_tint_ink' => '#0C4A6E',
                        'danger' => '#E11D48',
                        'warning' => '#B45309',
                        'success' => '#0F766E',
                    ],
                    'dark' => [
                        'bg' => '#07090E',
                        'surface' => '#0F141F',
                        'surface_muted' => '#182032',
                        'border' => '#263147',
                        'border_strong' => '#38BDF8',
                        'text' => '#F8FAFC',
                        'text_muted' => '#CBD5E1',
                        'primary' => '#38BDF8',
                        'primary_hover' => '#7DD3FC',
                        'primary_ink' => '#07090E',
                        'accent' => '#818CF8',
                        'accent_tint' => '#0C4A6E',
                        'accent_tint_ink' => '#E0F2FE',
                        'danger' => '#FB7185',
                        'warning' => '#FBBF24',
                        'success' => '#2DD4BF',
                    ],
                ],
                'rationale' => [
                    'id' => 'Kokpit ruang kendali siaran beraksen Broadcast Azure & Signal Violet dengan mode studio gelap rendah silau untuk operator audio live, WebRTC, & tiket event.',
                    'en' => 'Broadcast control-room cockpit with Azure & Signal Violet telemetry tailored for live audio booths, WebRTC stages, and event seating.',
                ],
            ],
        ];
    }

    /**
     * Get single design system by code with safe Swiss fallback.
     *
     * @return array<string, mixed>
     */
    public static function get(?string $code): array
    {
        $all = self::all();
        $normalized = strtoupper(trim((string) $code));

        return $all[$normalized] ?? $all['SWISS_TECHNICAL_GRID'];
    }

    /**
     * Resolve design system from Blueprint user override or project domain background analysis.
     *
     * @return array{code: string, source: string, system: array<string, mixed>, reason: array{id: string, en: string}}
     */
    public static function resolveForBlueprint(VisionBlueprint $blueprint, array $universalSchema = []): array
    {
        $meta = is_array($blueprint->user_metadata) ? $blueprint->user_metadata : [];
        $explicitCode = strtoupper(trim((string) ($meta['design_system'] ?? ($meta['infra_config']['design_system'] ?? ''))));
        $all = self::all();

        if ($explicitCode !== '' && isset($all[$explicitCode])) {
            $sys = $all[$explicitCode];

            return [
                'code' => $explicitCode,
                'source' => 'user_selected',
                'system' => $sys,
                'reason' => $sys['rationale'],
            ];
        }

        $sectorCode = strtoupper(trim((string) ($universalSchema['sector']['code'] ?? ($meta['selected_sector'] ?? ''))));
        $corpus = strtolower(implode(' ', array_filter([
            (string) $blueprint->nama_bisnis,
            (string) $blueprint->masalah_utama,
            (string) $blueprint->tujuan_utama,
            (string) $blueprint->target_audiens,
            is_array($blueprint->fitur_wajib) ? implode(' ', $blueprint->fitur_wajib) : (string) $blueprint->fitur_wajib,
            (string) $blueprint->alur_kerja,
            (string) $blueprint->kebutuhan_integrasi,
            (string) $blueprint->referensi_desain,
        ])));

        $scores = [];
        foreach ($all as $code => $system) {
            $score = 0;
            if ($sectorCode !== '' && in_array($sectorCode, $system['sectors'] ?? [], true)) {
                $score += 6;
            }
            foreach ($system['keywords'] ?? [] as $kw) {
                $kwLower = strtolower((string) $kw);
                if ($kwLower !== '' && preg_match('/(?<![a-z0-9])'.preg_quote($kwLower, '/').'(?![a-z0-9])/i', $corpus) === 1) {
                    $score += 3;
                }
            }
            $scores[$code] = $score;
        }

        arsort($scores);
        $topCode = (string) array_key_first($scores);
        $topScore = (int) ($scores[$topCode] ?? 0);

        if ($topScore <= 0) {
            $topCode = 'SWISS_TECHNICAL_GRID';
        }

        $chosen = $all[$topCode];

        return [
            'code' => $topCode,
            'source' => 'domain_recommended',
            'system' => $chosen,
            'reason' => $chosen['rationale'],
        ];
    }

    /**
     * Compact JSON-friendly list of all 10 design systems for UI config cards.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function catalogForUi(): array
    {
        $items = [];
        foreach (self::all() as $code => $sys) {
            $items[] = [
                'code' => $code,
                'name' => $sys['name'],
                'school' => $sys['school'],
                'default_mode' => $sys['default_mode'],
                'radius_token' => $sys['radius_token'],
                'rationale' => $sys['rationale'],
                'swatches' => [
                    'light_bg' => $sys['tokens']['light']['bg'],
                    'light_surface' => $sys['tokens']['light']['surface'],
                    'light_primary' => $sys['tokens']['light']['primary'],
                    'light_accent' => $sys['tokens']['light']['accent'],
                    'dark_bg' => $sys['tokens']['dark']['bg'],
                    'dark_primary' => $sys['tokens']['dark']['primary'],
                ],
            ];
        }

        return $items;
    }

    /**
     * Generate pure local Vite + Tailwind CSS v4 stylesheet calibrated strictly to the resolved Design System.
     * Zero references to Neriah Pro internal CSS.
     */
    public static function generateAppCss(array $resolvedDesign, string $appName): string
    {
        $sys = $resolvedDesign['system'] ?? self::get(null);
        $code = $resolvedDesign['code'] ?? 'SWISS_TECHNICAL_GRID';
        $l = $sys['tokens']['light'];
        $d = $sys['tokens']['dark'];
        $radius = $sys['radius_token'] ?? '2px';
        $borderWeight = $sys['border_weight'] ?? '1px';

        return <<<CSS
@import 'tailwindcss';

@custom-variant dark (&:where(.dark, .dark *));

@source '../views/**/*.blade.php';
@source '../../app/**/*.php';
@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';

/*
 * ============================================================================
 * {$appName} — Domain Design System: {$code}
 * School: {$sys['school']}
 * Generated cleanly by Project OS Engine (Zero External CDN / Zero Template Clone)
 * ============================================================================
 */
@theme {
    --font-sans: {$sys['font_body']};
    --font-display: {$sys['font_heading']};
    --font-mono: {$sys['font_mono']};
}

:root {
    --ds-code: '{$code}';
    --ds-bg: {$l['bg']};
    --ds-surface: {$l['surface']};
    --ds-surface-muted: {$l['surface_muted']};
    --ds-border: {$l['border']};
    --ds-border-strong: {$l['border_strong']};
    --ds-text: {$l['text']};
    --ds-text-muted: {$l['text_muted']};
    --ds-primary: {$l['primary']};
    --ds-primary-hover: {$l['primary_hover']};
    --ds-primary-ink: {$l['primary_ink']};
    --ds-accent: {$l['accent']};
    --ds-accent-tint: {$l['accent_tint']};
    --ds-accent-tint-ink: {$l['accent_tint_ink']};
    --ds-danger: {$l['danger']};
    --ds-warning: {$l['warning']};
    --ds-success: {$l['success']};
    --ds-radius: {$radius};
    --ds-border-width: {$borderWeight};
}

.dark {
    --ds-bg: {$d['bg']};
    --ds-surface: {$d['surface']};
    --ds-surface-muted: {$d['surface_muted']};
    --ds-border: {$d['border']};
    --ds-border-strong: {$d['border_strong']};
    --ds-text: {$d['text']};
    --ds-text-muted: {$d['text_muted']};
    --ds-primary: {$d['primary']};
    --ds-primary-hover: {$d['primary_hover']};
    --ds-primary-ink: {$d['primary_ink']};
    --ds-accent: {$d['accent']};
    --ds-accent-tint: {$d['accent_tint']};
    --ds-accent-tint-ink: {$d['accent_tint_ink']};
    --ds-danger: {$d['danger']};
    --ds-warning: {$d['warning']};
    --ds-success: {$d['success']};
}

@layer base {
    html {
        font-family: var(--font-sans);
        background-color: var(--ds-bg);
        color: var(--ds-text);
        color-scheme: light dark;
    }

    h1, h2, h3, .font-display {
        font-family: var(--font-display);
    }

    code, pre, .font-mono, .tabular-nums {
        font-family: var(--font-mono);
        font-variant-numeric: tabular-nums;
    }
}

.ds-card {
    background-color: var(--ds-surface);
    border: var(--ds-border-width) solid var(--ds-border);
    border-radius: var(--ds-radius);
    color: var(--ds-text);
    transition: border-color 150ms ease, background-color 150ms ease;
}

.ds-card:hover {
    border-color: var(--ds-border-strong);
}

.ds-btn-primary {
    background-color: var(--ds-primary);
    color: var(--ds-primary-ink);
    border-radius: var(--ds-radius);
    font-weight: 700;
    transition: background-color 150ms ease, transform 120ms ease;
}

.ds-btn-primary:hover {
    background-color: var(--ds-primary-hover);
}

.ds-badge-accent {
    background-color: var(--ds-accent-tint);
    color: var(--ds-accent-tint-ink);
    border: 1px solid var(--ds-border);
    border-radius: var(--ds-radius);
}
CSS;
    }

    /**
     * Generate pure local resources/js/app.js with safe theme toggling,
     * accessible toast dispatcher (no native alert/confirm/prompt), and duplicate Alpine guard.
     */
    public static function generateAppJs(array $resolvedDesign): string
    {
        $defaultMode = $resolvedDesign['system']['default_mode'] ?? 'light';

        return <<<JS
/**
 * Domain Application Entry Point (Vite-bundled, Zero External CDN)
 * Default Theme Preference: {$defaultMode}
 */
(() => {
    const STORAGE_KEY = 'app_theme_mode';
    const root = document.documentElement;

    const applyTheme = (mode) => {
        if (mode === 'dark') {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
        try {
            localStorage.setItem(STORAGE_KEY, mode);
        } catch (_) {}
    };

    const saved = (() => {
        try {
            return localStorage.getItem(STORAGE_KEY);
        } catch (_) {
            return null;
        }
    })();

    applyTheme(saved || '{$defaultMode}');

    window.toggleAppTheme = () => {
        const next = root.classList.contains('dark') ? 'light' : 'dark';
        applyTheme(next);
        window.dispatchEvent(new CustomEvent('app:theme-changed', { detail: { mode: next } }));
    };

    /**
     * Accessible non-blocking toast helper (never use native window.alert/confirm/prompt).
     */
    window.showAppToast = ({ type = 'info', title = '', message = '' } = {}) => {
        let stack = document.getElementById('app-toast-stack');
        if (!stack) {
            stack = document.createElement('div');
            stack.id = 'app-toast-stack';
            stack.className = 'fixed bottom-4 right-4 z-50 flex flex-col gap-2 max-w-sm pointer-events-none';
            document.body.appendChild(stack);
        }

        const item = document.createElement('div');
        item.className = 'ds-card pointer-events-auto px-4 py-3 shadow-xl text-xs border-l-4';
        item.style.borderLeftColor = type === 'error'
            ? 'var(--ds-danger)'
            : (type === 'success' ? 'var(--ds-success)' : 'var(--ds-primary)');

        const heading = document.createElement('div');
        heading.className = 'font-bold uppercase tracking-wider';
        heading.textContent = title || type.toUpperCase();

        const body = document.createElement('div');
        body.className = 'mt-0.5 opacity-90';
        body.textContent = message;

        item.appendChild(heading);
        if (message) {
            item.appendChild(body);
        }
        stack.appendChild(item);

        setTimeout(() => {
            item.remove();
        }, 4200);
    };

    // Guard against duplicate Alpine.start() when Livewire / Flux injects Alpine at runtime
    if (!window.Alpine && !window.Livewire && typeof window.__APP_ALPINE_BOOTSTRAP__ === 'function') {
        window.__APP_ALPINE_BOOTSTRAP__();
    }
})();
JS;
    }

    /**
     * Generate zero-CDN, Vite-backed welcome.blade.php customized to the chosen Design System.
     */
    public static function generateWelcomeBlade(
        string $appName,
        string $sectorTitle,
        string $summary,
        array $resolvedDesign,
        string $navLinksHtml,
        bool $hasRealtimeMedia = false
    ): string {
        $sys = $resolvedDesign['system'] ?? self::get(null);
        $code = $resolvedDesign['code'] ?? 'SWISS_TECHNICAL_GRID';
        $school = $sys['school'] ?? 'Modular Domain System';
        $rationale = $sys['rationale']['en'] ?? $sys['rationale']['id'] ?? '';
        $safeAppName = e($appName);
        $safeSector = e($sectorTitle);
        $safeSummary = e($summary);

        $realtimeBadge = $hasRealtimeMedia
            ? "\n                    <span class=\"ds-badge-accent px-2.5 py-1 text-[11px] font-mono uppercase tracking-wider\">LIVE REAL-TIME STREAM READY</span>"
            : '';

        return <<<BLADE
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{$safeAppName} — Enterprise Operations Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col selection:opacity-90">
    <header class="border-b" style="background-color: var(--ds-surface); border-color: var(--ds-border);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <span class="inline-flex h-8 w-8 items-center justify-center font-mono text-xs font-black"
                      style="background-color: var(--ds-primary); color: var(--ds-primary-ink); border-radius: var(--ds-radius);">
                    OS
                </span>
                <div class="min-w-0">
                    <div class="font-display font-bold text-sm sm:text-base truncate">{$safeAppName}</div>
                    <div class="text-[11px] font-mono uppercase tracking-wider opacity-75 truncate">{$safeSector}</div>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="button"
                        onclick="window.toggleAppTheme()"
                        class="ds-card px-3 py-1.5 text-xs font-mono uppercase tracking-wider cursor-pointer"
                        title="Toggle Light / Dark Mode">
                    Theme
                </button>
                <a href="{{ url('/docs/api') }}" class="ds-card px-3 py-1.5 text-xs font-mono uppercase tracking-wider">
                    API Docs
                </a>
                <a href="{{ url('/up') }}" class="ds-card px-3 py-1.5 text-xs font-mono uppercase tracking-wider">
                    Health
                </a>
                @auth
                    <span class="ds-badge-accent px-3 py-1.5 text-xs font-mono">{{ auth()->user()->name }}</span>
                @else
                    <a href="{{ route('login') }}" class="ds-btn-primary px-3.5 py-1.5 text-xs uppercase tracking-wider">
                        Sign In
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        <section class="ds-card p-6 sm:p-8 space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="ds-badge-accent px-2.5 py-1 text-[11px] font-mono uppercase tracking-wider">DESIGN SYSTEM: {$code}</span>
                <span class="ds-card px-2.5 py-1 text-[11px] font-mono uppercase tracking-wider">{$school}</span>{$realtimeBadge}
            </div>

            <h1 class="text-2xl sm:text-4xl font-display font-bold tracking-tight">
                {$safeAppName}
            </h1>

            <p class="text-sm sm:text-base max-w-3xl leading-relaxed" style="color: var(--ds-text-muted);">
                {$safeSummary}
            </p>

            <div class="pt-2 border-t text-xs font-mono" style="border-color: var(--ds-border); color: var(--ds-text-muted);">
                Aesthetic Calibration: {$rationale}
            </div>
        </section>

        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-mono font-bold uppercase tracking-widest">
                    Operational Domain Modules
                </h2>
                <span class="text-xs font-mono opacity-75">ULID + Keyset Cursor O(1)</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
{$navLinksHtml}
            </div>
        </section>
    </main>

    <footer class="border-t py-6 text-xs font-mono" style="background-color: var(--ds-surface); border-color: var(--ds-border); color: var(--ds-text-muted);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>{$safeAppName} &middot; Laravel 13 &middot; PostgreSQL ULID Engine</span>
            <span>Local Vite Assets &middot; Zero External CDN</span>
        </div>
    </footer>
</body>
</html>
BLADE;
    }

    /**
     * Generate 100% local offline-safe OpenAPI 3.1 explorer blade reading /openapi.json.
     * Replaces external @scalar/api-reference CDN.
     */
    public static function generateApiDocsBlade(string $appName, array $resolvedDesign): string
    {
        $safeName = e($appName);
        $code = e($resolvedDesign['code'] ?? 'SWISS_TECHNICAL_GRID');

        return <<<BLADE
<!DOCTYPE html>
<html lang="en" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$safeName} — OpenAPI 3.1 Explorer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen p-4 sm:p-8">
    <div class="max-w-6xl mx-auto space-y-6">
        <header class="ds-card p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="text-xs font-mono uppercase tracking-widest opacity-75">OpenAPI 3.1 Local Specification &middot; {$code}</div>
                <h1 class="text-xl sm:text-2xl font-display font-bold mt-1">{$safeName} API Reference</h1>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.toggleAppTheme()" class="ds-card px-3 py-1.5 text-xs font-mono uppercase cursor-pointer">
                    Theme
                </button>
                <a href="{{ url('/openapi.json') }}" class="ds-card px-3 py-1.5 text-xs font-mono uppercase">
                    Raw JSON
                </a>
                <a href="{{ url('/') }}" class="ds-btn-primary px-3.5 py-1.5 text-xs uppercase">
                    Dashboard
                </a>
            </div>
        </header>

        <div id="openapi-status" class="ds-card p-4 text-xs font-mono">
            Loading local /openapi.json schema...
        </div>

        <div id="openapi-endpoints" class="space-y-3"></div>
    </div>

    <script>
        (async () => {
            const statusEl = document.getElementById('openapi-status');
            const listEl = document.getElementById('openapi-endpoints');
            try {
                const res = await fetch('/openapi.json', { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const spec = await res.json();
                const paths = spec.paths || {};
                const pathKeys = Object.keys(paths);
                statusEl.textContent = (spec.info?.title || '{$safeName}') + ' v' + (spec.info?.version || '1.0.0') + ' — ' + pathKeys.length + ' endpoint groups loaded cleanly from local /openapi.json.';

                pathKeys.forEach((path) => {
                    const methods = paths[path] || {};
                    Object.keys(methods).forEach((method) => {
                        const op = methods[method] || {};
                        const card = document.createElement('div');
                        card.className = 'ds-card p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3';

                        const left = document.createElement('div');
                        left.className = 'space-y-1 min-w-0';

                        const row = document.createElement('div');
                        row.className = 'flex items-center gap-2 flex-wrap';

                        const badge = document.createElement('span');
                        badge.className = 'ds-badge-accent px-2 py-0.5 text-[11px] font-mono font-bold uppercase';
                        badge.textContent = method.toUpperCase();

                        const uri = document.createElement('code');
                        uri.className = 'text-xs font-mono font-bold';
                        uri.textContent = path;

                        const summary = document.createElement('p');
                        summary.className = 'text-xs opacity-80';
                        summary.textContent = op.summary || op.description || '';

                        row.appendChild(badge);
                        row.appendChild(uri);
                        left.appendChild(row);
                        left.appendChild(summary);
                        card.appendChild(left);

                        const tag = document.createElement('span');
                        tag.className = 'text-[11px] font-mono uppercase opacity-75 shrink-0';
                        tag.textContent = (op.tags || []).join(', ');
                        card.appendChild(tag);

                        listEl.appendChild(card);
                    });
                });
            } catch (err) {
                statusEl.textContent = 'Failed to parse local /openapi.json: ' + (err?.message || err);
                statusEl.style.borderColor = 'var(--ds-danger)';
            }
        })();
    </script>
</body>
</html>
BLADE;
    }
}

