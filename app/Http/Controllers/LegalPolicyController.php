<?php

namespace App\Http\Controllers;

use App\Models\CmsGlobalSetting;
use App\Models\LegalPolicy;
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
        if (in_array($reqLang, ['id', 'en'], true)) {
            $locale = $reqLang;
            session(['locale' => $locale]);
            cookie()->queue('neriah_locale', $locale, 60 * 24 * 365);
        } else {
            $locale = session('locale') ?: ($request->cookie('neriah_locale') ?: app()->getLocale());
            if (! in_array($locale, ['id', 'en'], true)) {
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
        if (! $policy) {
            $policy = new LegalPolicy([
                'type' => $resolvedType,
                'title' => [
                    'id' => match ($resolvedType) {
                        'terms_and_conditions' => 'Syarat dan Ketentuan Layanan Neriah Pro',
                        'privacy_policy' => 'Kebijakan Privasi & Perlindungan Data',
                        'refund_policy' => 'Kebijakan Pengembalian Dana & Garansi (Refund Policy)',
                    },
                    'en' => match ($resolvedType) {
                        'terms_and_conditions' => 'Neriah Pro Terms and Conditions of Service',
                        'privacy_policy' => 'Privacy and Data Protection Policy',
                        'refund_policy' => 'Refund, Cancellation & Warranty Policy',
                    },
                ],
                'content' => [
                    'id' => match ($resolvedType) {
                        'terms_and_conditions' => '<h3>1. Ruang Lingkup Layanan</h3><p>Neriah Pro menyediakan jasa rekayasa perangkat lunak berskala enterprise, lisensi Product Requirements Document (PRD), dan kontrak kerja sama digital. Seluruh pengerjaan mengacu pada spesifikasi PRD yang telah dikunci (Scope Locked).</p><h3>2. Ketentuan Pembayaran</h3><p>Pengerjaan sprint studio resmi dimulai setelah Klien menandatangani Kontrak Digital dan melunasi Uang Muka (DP) sebesar 50% melalui payment gateway Midtrans. Untuk lisensi retail, akses unduhan diberikan seketika setelah pembayaran diverifikasi oleh gateway.</p><h3>3. Perubahan Ruang Lingkup (Addendum)</h3><p>Setiap penambahan fitur di luar dokumen PRD terkunci akan dihitung sebagai Change Request (CR) / Addendum terpisah dengan tambahan biaya dan hari kerja tersendiri.</p><h3>4. Layanan Utilitas Digital & Pembuat Resume (CV Pro Studio)</h3><p>Neriah Pro menyediakan sarana teknologi pemformatan, tata letak otomatis ATS, dan generator dokumen karir digital. Seluruh data riwayat karir, kualifikasi, ijazah, sertifikat, dan klaim kompetensi yang dimasukkan ke dalam dokumen CV sepenuhnya merupakan tanggung jawab hukum Pengguna (Disclaimer of Warranty). Neriah Pro tidak bertanggung jawab atas sengketa ketenagakerjaan atau ketidakbenaran informasi yang dicantumkan oleh Pengguna.</p><h3>5. Lisensi Tipografi & Aset Visual Bebas Royalti</h3><p>Seluruh dokumen PDF dan tata letak grafis yang dihasilkan oleh Neriah Pro menggunakan tipografi bersertifikasi SIL Open Font License (Inter, Roboto, Merriweather) dan aset visual open-source (Lucide & FontAwesome) yang bebas royalti untuk kepentingan komersial maupun profesional.</p>',
                        'privacy_policy' => '<h3>1. Kerahasiaan Ide & Data</h3><p>Kami menjamin 100% kerahasiaan ide bisnis, skema database, dan dokumen PRD Anda. Neriah Pro tidak akan menjual, menyewakan, atau mendistribusikan data klien kepada pihak ketiga mana pun.</p><h3>2. Standar Keamanan Enkripsi</h3><p>Seluruh identitas data disimpan dengan aman menggunakan standar modern dan primary key ULID 26 karakter untuk menjamin integritas dan keamanan arsitektur data terdistribusi.</p><h3>3. Perlindungan Data Riwayat Karir & Anti-Broker Data (UU PDP No. 27/2022)</h3><p>Sesuai amanat Undang-Undang Pelindungan Data Pribadi (UU PDP No. 27/2022), data pribadi dan riwayat hidup yang diisi pada CV Pro Studio (nama, kontak, riwayat pekerjaan, foto) semata-mata diproses untuk memformat dokumen CV milik Pengguna. Neriah Pro menerapkan kebijakan Zero Data Brokering di mana data Pengguna tidak pernah dan tidak akan dijual, disewakan, atau dibagikan kepada pihak ketiga atau agen periklanan mana pun.</p><h3>4. Hak Penghapusan Data Pribadi (Right to Erasure)</h3><p>Pengguna memiliki hak mutlak untuk menghapus seluruh data riwayat hidup, resume, dan riwayat token AI yang tersimpan di server Neriah Pro kapan saja melalui fitur Hapus Data CV Mandiri atau permintaan resmi melalui endpoint privasi.</p>',
                        'refund_policy' => '<h3>1. Kebijakan Uang Muka (DP) Studio</h3><p>Uang Muka (DP 50%) proyek studio yang telah dibayarkan melalui Midtrans tidak dapat dikembalikan (non-refundable) apabila sprint pengerjaan arsitektur dan skema database telah resmi dimulai sesuai slot jadwal terpesan.</p><h3>2. Garansi Perbaikan Bug & SLA (30 Hari)</h3><p>Setelah serah terima sistem, Neriah Pro memberikan garansi pemeliharaan dan perbaikan bug secara gratis selama 30 hari kalender sesuai ruang lingkup kontrak yang disepakati.</p><h3>3. Kebijakan Produk Digital & Token AI (Non-Refundable)</h3><p>Pembelian lisensi digital sekali unduh, paket kuota ekspor PDF, serta token AI yang telah berhasil digunakan atau dikreditkan ke akun pengguna bersifat final dan tidak dapat dibatalkan atau di-refund (No Refund for Consumed Digital Goods). Pengembalian dana hanya dapat diproses apabila terbukti terjadi kegagalan sistemik penagihan ganda (double-charge) yang diverifikasi oleh audit log gateway pembayaran Midtrans.</p>',
                    },
                    'en' => match ($resolvedType) {
                        'terms_and_conditions' => '<h3>1. Scope of Services</h3><p>Neriah Pro provides enterprise software architecture, automated PRD synthesis, and digital contract agreements. All development follows the agreed, scope-locked PRD specifications.</p><h3>2. Payment Terms</h3><p>Development sprints officially commence once the Client executes the Digital Contract and settles a 50% Down Payment (DP) via Midtrans. For retail licenses, digital download access is provisioned immediately upon gateway verification.</p><h3>3. Change Requests (Addendum)</h3><p>Any additional feature requests beyond the locked PRD document shall be handled via an independent Addendum with separate billing and timelines.</p><h3>4. Digital Career Utilities & Resume Builder (CV Pro Studio)</h3><p>Neriah Pro provides layout formatting tools, ATS scoring automation, and digital resume generation. All career history, educational credentials, certifications, and qualification claims inputted into the resume are the sole legal responsibility of the User (Disclaimer of Warranty). Neriah Pro disclaims all liability for employment disputes or falsification of credentials submitted by Users.</p><h3>5. Royalty-Free Typography & Visual Asset Licensing</h3><p>All exported PDF documents and graphic layouts generated by Neriah Pro strictly utilize SIL Open Font Licensed typography (Inter, Roboto, Merriweather) and open-source visual assets (Lucide & FontAwesome), verified 100% royalty-free for commercial and professional use.</p>',
                        'privacy_policy' => '<h3>1. Confidentiality of Client Ideas</h3><p>We guarantee 100% confidentiality regarding your business ideas, database schemas, and PRD specifications. Neriah Pro never sells, leases, or discloses client data to third parties.</p><h3>2. Security Standards</h3><p>All records are indexed and safeguarded using 26-character ULID primary keys to maintain distributed database security and strict data isolation.</p><h3>3. Career Data Privacy & Zero Data Brokering (Indonesian PDP Law No. 27/2022)</h3><p>In strict compliance with Personal Data Protection Law (UU PDP No. 27/2022), personal identifiers and biographical data inputted into CV Pro Studio are processed exclusively for formatting User resumes. Neriah Pro enforces an uncompromising Zero Data Brokering policy: user data is never sold, leased, or disclosed to third-party data brokers or advertisers under any circumstances.</p><h3>4. Right to Erasure</h3><p>Users possess the absolute right to purge all biographical data, stored resumes, and AI interaction histories from Neriah Pro servers at any time via the self-service Purge CV Data action or official privacy deletion endpoints.</p>',
                        'refund_policy' => '<h3>1. Down Payment Refund Terms</h3><p>The 50% Down Payment processed via Midtrans is non-refundable once architecture and database sprint development has commenced for the booked schedule.</p><h3>2. 30-Day Bug Fix Warranty</h3><p>Upon production handover, Neriah Pro provides a 30-calendar-day warranty for defect fixes within the agreed scope of work at zero additional charge.</p><h3>3. Consumed Digital Goods & AI Tokens Policy</h3><p>Purchases of one-time digital licenses, PDF export allowances, and utilized AI generation credits are final and strictly non-refundable once credited or downloaded. Refund claims are only eligible in cases of verified technical double-billing confirmed by Midtrans payment gateway audit logs.</p>',
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

    /**
     * Display High-Retention Customer Smart Guide (Rights & Obligations Matrix)
     */
    public function guide(Request $request): View
    {
        $reqLang = strtolower((string) $request->query('lang', ''));
        if (in_array($reqLang, ['id', 'en'], true)) {
            $locale = $reqLang;
            session(['locale' => $locale]);
            cookie()->queue('neriah_locale', $locale, 60 * 24 * 365);
        } else {
            $locale = session('locale') ?: ($request->cookie('neriah_locale') ?: app()->getLocale());
            if (! in_array($locale, ['id', 'en'], true)) {
                $locale = 'id';
            }
        }
        app()->setLocale($locale);

        $globalSettings = CmsGlobalSetting::getAllCached();

        return view('legal.smart-guide', [
            'locale' => $locale,
            'globalSettings' => $globalSettings,
        ]);
    }
}
