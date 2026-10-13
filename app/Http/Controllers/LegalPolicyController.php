<?php

namespace App\Http\Controllers;

use App\Models\LegalPolicy;
use App\Models\CmsGlobalSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalPolicyController extends Controller
{
    public function show(Request $request, string $type)
    {
        $typeMap = [
            'terms' => 'terms_and_conditions',
            'terms-and-conditions' => 'terms_and_conditions',
            'syarat-ketentuan' => 'terms_and_conditions',
            'privacy-policy' => 'privacy_policy',
            'privacy' => 'privacy_policy',
            'kebijakan-privasi' => 'privacy_policy',
            'refund-policy' => 'refund_policy',
            'refund' => 'refund_policy',
            'kebijakan-pengembalian' => 'refund_policy',
        ];

        $resolvedType = $typeMap[$type] ?? 'terms_and_conditions';
        
        // Resolve Locale
        $reqLang = strtolower((string) $request->query('lang', ''));
        if (in_array($reqLang, ['id', 'en'])) {
            $locale = $reqLang;
            cookie()->queue('neriah_locale', $locale, 60 * 24 * 365);
        } else {
            $locale = $request->cookie('neriah_locale', app()->getLocale());
            if (!in_array($locale, ['id', 'en'])) {
                $locale = 'id';
            }
        }
        app()->setLocale($locale);

        $policy = null;
        try {
            $policy = LegalPolicy::where('type', $resolvedType)->where('is_active', true)->first();
        } catch (\Throwable $e) {
            // DB table might be empty or migrating, fallback gracefully
        }

        // Fallbacks if not in DB yet
        if (!$policy) {
            $policy = new LegalPolicy([
                'type' => $resolvedType,
                'title' => [
                    'id' => match($resolvedType) {
                        'terms_and_conditions' => 'Syarat dan Ketentuan Layanan Neriah Pro',
                        'privacy_policy' => 'Kebijakan Privasi & Perlindungan Data',
                        'refund_policy' => 'Kebijakan Pengembalian Dana & Garansi (Refund Policy)',
                    },
                    'en' => match($resolvedType) {
                        'terms_and_conditions' => 'Neriah Pro Terms and Conditions of Service',
                        'privacy_policy' => 'Privacy and Data Protection Policy',
                        'refund_policy' => 'Refund, Cancellation & Warranty Policy',
                    },
                ],
                'content' => [
                    'id' => match($resolvedType) {
                        'terms_and_conditions' => '<h3>1. Ruang Lingkup Layanan</h3><p>Neriah Pro menyediakan jasa rekayasa perangkat lunak berskala enterprise, lisensi Product Requirements Document (PRD), dan kontrak kerja sama digital. Seluruh pengerjaan mengacu pada spesifikasi PRD yang telah dikunci (Scope Locked).</p><h3>2. Ketentuan Pembayaran</h3><p>Pengerjaan sprint studio resmi dimulai setelah Klien menandatangani Kontrak Digital dan melunasi Uang Muka (DP) sebesar 50% melalui payment gateway Midtrans. Untuk lisensi retail, akses unduhan diberikan seketika setelah pembayaran diverifikasi oleh gateway.</p><h3>3. Perubahan Ruang Lingkup (Addendum)</h3><p>Setiap penambahan fitur di luar dokumen PRD terkunci akan dihitung sebagai Change Request (CR) / Addendum terpisah dengan tambahan biaya dan hari kerja tersendiri.</p>',
                        'privacy_policy' => '<h3>1. Kerahasiaan Ide & Data</h3><p>Kami menjamin 100% kerahasiaan ide bisnis, skema database, dan dokumen PRD Anda. Neriah Pro tidak akan menjual, menyewakan, atau mendistribusikan data klien kepada pihak ketiga mana pun.</p><h3>2. Standar Keamanan Enkripsi</h3><p>Seluruh identitas data disimpan dengan aman menggunakan standar modern dan primary key ULID 26 karakter untuk menjamin integritas dan keamanan arsitektur data terdistribusi.</p>',
                        'refund_policy' => '<h3>1. Kebijakan Uang Muka (DP) Studio</h3><p>Uang Muka (DP 50%) proyek studio yang telah dibayarkan melalui Midtrans tidak dapat dikembalikan (non-refundable) apabila sprint pengerjaan arsitektur dan skema database telah resmi dimulai sesuai slot jadwal terpesan.</p><h3>2. Garansi Perbaikan Bug & SLA (30 Hari)</h3><p>Setelah serah terima sistem, Neriah Pro memberikan garansi pemeliharaan dan perbaikan bug secara gratis selama 30 hari kalender sesuai ruang lingkup kontrak yang disepakati.</p><h3>3. Kebijakan Lisensi Retail Digital</h3><p>Untuk produk digital unduhan instan (PRD dan Source Code), pengembalian dana dapat diajukan dalam waktu 24 jam apabila file yang diterima terbukti rusak atau tidak sesuai dengan spesifikasi teknis yang dijanjikan.</p>',
                    },
                    'en' => match($resolvedType) {
                        'terms_and_conditions' => '<h3>1. Scope of Services</h3><p>Neriah Pro provides enterprise software architecture, automated PRD synthesis, and digital contract agreements. All development follows the agreed, scope-locked PRD specifications.</p><h3>2. Payment Terms</h3><p>Development sprints officially commence once the Client executes the Digital Contract and settles a 50% Down Payment (DP) via Midtrans. For retail licenses, digital download access is provisioned immediately upon gateway verification.</p><h3>3. Change Requests (Addendum)</h3><p>Any additional feature requests beyond the locked PRD document shall be handled via an independent Addendum with separate billing and timelines.</p>',
                        'privacy_policy' => '<h3>1. Confidentiality of Client Ideas</h3><p>We guarantee 100% confidentiality regarding your business ideas, database schemas, and PRD specifications. Neriah Pro never sells, leases, or discloses client data to third parties.</p><h3>2. Security Standards</h3><p>All records are indexed and safeguarded using 26-character ULID primary keys to maintain distributed database security and strict data isolation.</p>',
                        'refund_policy' => '<h3>1. Down Payment Refund Terms</h3><p>The 50% Down Payment processed via Midtrans is non-refundable once architecture and database sprint development has commenced for the booked schedule.</p><h3>2. 30-Day Bug Fix Warranty</h3><p>Upon production handover, Neriah Pro provides a 30-calendar-day warranty for defect fixes within the agreed scope of work at zero additional charge.</p><h3>3. Digital Retail License Policy</h3><p>For instant downloadable digital assets (PRD and Source Code), refund requests may be submitted within 24 hours if the delivered files are corrupted or fail to match promised technical specifications.</p>',
                    },
                ],
                'is_active' => true,
            ]);
        }

        $globalSettings = CmsGlobalSetting::getAllCached();

        return view('legal.policy', [
            'policy' => $policy,
            'resolvedType' => $resolvedType,
            'locale' => $locale,
            'globalSettings' => $globalSettings,
        ]);
    }
}
