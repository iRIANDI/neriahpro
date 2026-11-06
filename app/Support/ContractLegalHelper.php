<?php

namespace App\Support;

use App\Models\CmsGlobalSetting;
use Illuminate\Support\Facades\Cache;

class ContractLegalHelper
{
    /**
     * Retrieve official Party Two (Developer / Technology Provider) information
     * dynamically configured from Backend Admin Global Settings.
     */
    public static function getDeveloperInfo(): array
    {
        return Cache::rememberForever('cms_contract_developer_info', function () {
            try {
                $settings = CmsGlobalSetting::whereIn('key', [
                    'developer_entity_name',
                    'developer_support_email',
                    'developer_phone',
                    'developer_location',
                    'developer_pic_name',
                    'developer_pic_title',
                    'developer_seal_text',
                    'developer_signature_image',
                ])->pluck('value', 'key')->toArray();
            } catch (\Throwable $e) {
                $settings = [];
            }

            $sigImage = $settings['developer_signature_image'] ?? null;
            $sigUrl = null;
            if (!empty($sigImage)) {
                if (str_starts_with($sigImage, 'http://') || str_starts_with($sigImage, 'https://') || str_starts_with($sigImage, 'data:image')) {
                    $sigUrl = $sigImage;
                } else {
                    $sigUrl = asset('storage/' . ltrim($sigImage, '/'));
                }
            }

            return [
                'entity_name' => $settings['developer_entity_name'] ?? 'Neriah Pro',
                'email' => $settings['developer_support_email'] ?? 'support@neriahpro.com',
                'phone' => $settings['developer_phone'] ?? '+628123456789',
                'location' => $settings['developer_location'] ?? 'Jakarta, Indonesia',
                'pic_name' => $settings['developer_pic_name'] ?? 'Yoseph Iriandi Tambunan',
                'pic_title' => $settings['developer_pic_title'] ?? 'Lead Software Architect & Tech Lead',
                'seal_text' => $settings['developer_seal_text'] ?? 'NERIAH PRO VERIFIED ARCHITECT',
                'signature_image' => $sigUrl,
            ];
        });
    }

    /**
     * Parse structured or unstructured input into clean array of individual list items.
     */
    public static function parseListItems(mixed $input): array
    {
        if (empty($input)) {
            return [];
        }

        if (is_array($input)) {
            $flat = [];
            foreach ($input as $val) {
                if (is_array($val)) {
                    $flat = array_merge($flat, self::parseListItems($val));
                } elseif (is_string($val) && trim($val) !== '') {
                    $flat[] = trim($val);
                }
            }
            return $flat;
        }

        if (!is_string($input)) {
            return [(string) $input];
        }

        $trimmed = trim($input);
        if ($trimmed === '') {
            return [];
        }

        // Check if JSON string
        if ((str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']')) ||
            (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}'))) {
            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return self::parseListItems($decoded);
            }
        }

        // Try splitting by numbered patterns like "1. ", "2. ", or "(1) ", or newlines with numbers
        $numberedMatches = preg_split('/(?:^|\r?\n|\s+)(?:\d+[\.\)]|\([0-9]+\))\s+/u', $trimmed, -1, PREG_SPLIT_NO_EMPTY);
        if (count($numberedMatches) > 1) {
            $results = [];
            foreach ($numberedMatches as $m) {
                $m = trim($m, " \t\n\r\0\x0B;,");
                // Avoid leading intro phrase like "Ruang lingkup inti MVP mencakup:" if caught as match[0]
                if (str_ends_with($m, ':') && count($results) === 0 && strlen($m) < 80) {
                    continue;
                }
                if ($m !== '') {
                    $results[] = $m;
                }
            }
            if (!empty($results)) {
                return $results;
            }
        }

        // If there are explicit newlines, split by line
        if (str_contains($trimmed, "\n")) {
            $lines = preg_split('/\r?\n/', $trimmed);
            $results = [];
            foreach ($lines as $line) {
                $cleaned = trim($line, " \t\n\r\0\x0B-–*•");
                if ($cleaned !== '') {
                    $results[] = $cleaned;
                }
            }
            if (!empty($results)) {
                return $results;
            }
        }

        // Try splitting by commas that are NOT inside parentheses
        $commaParts = preg_split('/,(?![^\(\[]*[\)\]])\s*/u', $trimmed);
        if (count($commaParts) > 1) {
            $results = [];
            foreach ($commaParts as $p) {
                $p = trim($p, " \t\n\r\0\x0B.");
                if ($p !== '') {
                    $results[] = $p;
                }
            }
            if (!empty($results)) {
                return $results;
            }
        }

        return [$trimmed];
    }

    /**
     * Generate dynamic, dispute-proof contract clauses in Dual-Locale (ID & EN)
     * derived strictly from the Project OS Vision Blueprint specification.
     */
    public static function getDynamicClauses(\App\Models\Document $document, string $locale = 'id'): array
    {
        $blueprint = $document->related instanceof \App\Models\VisionBlueprint ? $document->related : null;

        $projectName = $blueprint?->nama_bisnis ?: ($blueprint?->client_name ?: ($document->signer_name ?: 'Proyek Rekayasa Perangkat Lunak'));
        $clientPic = $document->signer_name ?: ($blueprint?->client_name ?: 'Klien / Mitra');
        $docId = strtoupper(substr($document->id, 0, 10));
        $targetWaktu = $blueprint?->target_waktu ?: '30 Hari Kerja';
        $targetTimelineEn = str_ireplace(['hari kerja', 'pekan', 'bulan'], ['Working Days', 'Weeks', 'Months'], $targetWaktu);

        $contractAmount = $document->contract_amount ?: 50000000;
        $dpAmount = $document->dp_amount ?: ($contractAmount * 0.50);
        $pelunasanAmount = $contractAmount - $dpAmount;

        $devInfo = self::getDeveloperInfo();
        $devEntity = $devInfo['entity_name'];

        // Extract real features and sprint deliverables from blueprint
        $fiturMvp = $blueprint?->fitur_wajib ?: 'Autentikasi & RBAC, Intake Form, Master Database, Export Laporan';
        $integrasi = $blueprint?->kebutuhan_integrasi ?: 'Midtrans Payment Gateway, WhatsApp Gateway, Cloud Storage';
        $masalah = $blueprint?->masalah_utama ?: 'Digitalisasi dan modernisasi alur operasional bisnis.';
        $tujuan = $blueprint?->tujuan_utama ?: 'Meningkatkan efisiensi kerja dan skalabilitas sistem.';

        $mvpItems = self::parseListItems($fiturMvp);
        $integrasiItems = self::parseListItems($integrasi);
        $masalahItems = self::parseListItems($masalah);
        $tujuanItems = self::parseListItems($tujuan);

        if ($locale === 'en') {
            $leadEn = "Party Two ({$devEntity}) agrees to architect, develop, and deliver a production-grade software system for Party One ({$projectName}) strictly in accordance with the features, engineering architecture, and milestones defined in Ultimate PRD Document (SPEC_ID: {$docId}), with the detailed scope outlined below:";

            $sectionsEn = [];
            if (!empty($mvpItems)) {
                $sectionsEn[] = [
                    'heading' => 'A. Core MVP Functional Modules (Phase 1 Deliverables)',
                    'items' => $mvpItems,
                ];
            }
            if (!empty($integrasiItems)) {
                $sectionsEn[] = [
                    'heading' => 'B. Third-Party Integrations & Cloud Infrastructure',
                    'items' => $integrasiItems,
                ];
            }
            if (!empty($masalahItems)) {
                $sectionsEn[] = [
                    'heading' => 'C. Operational Problems & Bottlenecks Addressed',
                    'items' => $masalahItems,
                ];
            }
            if (!empty($tujuanItems)) {
                $sectionsEn[] = [
                    'heading' => 'D. Key Success Metrics & Business KPIs',
                    'items' => $tujuanItems,
                ];
            }

            $descLinesEn = [$leadEn];
            foreach ($sectionsEn as $sec) {
                if (!empty($sec['items'])) {
                    $descLinesEn[] = "";
                    $descLinesEn[] = $sec['heading'] . ":";
                    $isBullet = str_contains(strtolower($sec['heading']), 'integration');
                    foreach ($sec['items'] as $idx => $it) {
                        $prefix = $isBullet ? "  • " : ("  " . ($idx + 1) . ". ");
                        $descLinesEn[] = $prefix . $it;
                    }
                }
            }

            return [
                'pasal_1_ruang_lingkup' => [
                    'title' => 'Article 1: Scope of Work & PRD Technical Specifications',
                    'lead' => $leadEn,
                    'sections' => $sectionsEn,
                    'description' => implode("\n", $descLinesEn),
                ],
                'pasal_2_timeline_sprint' => [
                    'title' => 'Article 2: Project Timeline & 5 Engineering Sprints',
                    'description' => "The development shall be executed over a total duration of {$targetTimelineEn}, structured systematically into 5 sequential Engineering Sprints: Sprint 1 (System Architecture & Database Schema), Sprint 2 (Core Business MVP Logic), Sprint 3 (Frontend User Flow & High-Fidelity UI), Sprint 4 (Security Audit, Rate-Limiting & QA), and Sprint 5 (Cloud VPS Deployment, Staging Verification & Handover).",
                ],
                'pasal_3_biaya_dan_dp' => [
                    'title' => 'Article 3: Contract Value & Payment Milestones',
                    'description' => "The total investment value for this development agreement is IDR " . number_format($contractAmount, 0, ',', '.') . " with two payment milestones: Milestone 1: 50% Down Payment (DP) of IDR " . number_format($dpAmount, 0, ',', '.') . " payable prior to development kickoff; Milestone 2: Remaining 50% balance of IDR " . number_format($pelunasanAmount, 0, ',', '.') . " payable upon User Acceptance Testing (UAT) sign-off and repository/key handover.",
                ],
                'pasal_4_penguncian_scope' => [
                    'title' => 'Article 4: Scope Freeze & Change Request (CR) Protocol',
                    'description' => "Upon digital signing and Down Payment verification, all technical specifications in this PRD are cryptographically locked (Scope Freeze). Any new feature requests or architectural modifications outside this agreed scope shall be managed through a formal Addendum / Change Request (CR) with separate cost and timeline adjustments, without delaying the original contract.",
                ],
                'pasal_5_kerahasiaan_hki' => [
                    'title' => 'Article 5: Intellectual Property & Confidentiality (NDA)',
                    'description' => "Upon full settlement of the contract value, the client owns complete rights to the custom source code, private GitHub repository, and business database. Party Two ({$devEntity}) guarantees complete confidentiality of client data, business logic, and operational assets.",
                ],
                'pasal_6_keabsahan_hukum' => [
                    'title' => 'Article 6: Electronic Signature Validity & SHA-256 Audit Trail',
                    'description' => "This agreement is legally binding and recognized under the Electronic Information and Transactions (ITE) laws of the Republic of Indonesia. Validity is sealed through a digital signature accompanied by recorded timestamp, signer IP address, and SHA-256 cryptographic anti-tamper hash verification.",
                ],
            ];
        }

        // Default Indonesian Locale
        $leadId = "Pihak Kedua ({$devEntity}) sepakat untuk merancang, membangun, dan menyerahkan arsitektur sistem perangkat lunak untuk Pihak Pertama ({$projectName}) secara presisi sesuai dengan rincian fitur dan spesifikasi teknis pada Dokumen Ultimate PRD ID: {$docId}, dengan rincian ruang lingkup sebagai berikut:";

        $sectionsId = [];
        if (!empty($mvpItems)) {
            $sectionsId[] = [
                'heading' => 'A. Ruang Lingkup Inti Modul MVP (Fase 1)',
                'items' => $mvpItems,
            ];
        }
        if (!empty($integrasiItems)) {
            $sectionsId[] = [
                'heading' => 'B. Integrasi Layanan Pihak Ketiga & Cloud Infrastructure',
                'items' => $integrasiItems,
            ];
        }
        if (!empty($masalahItems)) {
            $sectionsId[] = [
                'heading' => 'C. Masalah Operasional yang Diselesaikan',
                'items' => $masalahItems,
            ];
        }
        if (!empty($tujuanItems)) {
            $sectionsId[] = [
                'heading' => 'D. Tolak Ukur Keberhasilan & Target Kinerja (KPI)',
                'items' => $tujuanItems,
            ];
        }

        $descLinesId = [$leadId];
        foreach ($sectionsId as $sec) {
            if (!empty($sec['items'])) {
                $descLinesId[] = "";
                $descLinesId[] = $sec['heading'] . ":";
                $isBullet = str_contains(strtolower($sec['heading']), 'integrasi');
                foreach ($sec['items'] as $idx => $it) {
                    $prefix = $isBullet ? "  • " : ("  " . ($idx + 1) . ". ");
                    $descLinesId[] = $prefix . $it;
                }
            }
        }

        return [
            'pasal_1_ruang_lingkup' => [
                'title' => 'Pasal 1: Ruang Lingkup Sistem & Spesifikasi PRD',
                'lead' => $leadId,
                'sections' => $sectionsId,
                'description' => implode("\n", $descLinesId),
            ],
            'pasal_2_timeline_sprint' => [
                'title' => 'Pasal 2: Alokasi Waktu & Timeline 5 Sprint Pengerjaan',
                'description' => "Pekerjaan dilaksanakan dengan total durasi {$targetWaktu} yang dibagi ke dalam 5 Sprint kerja berurutan: Sprint 1 (Arsitektur & Skema Database ULID), Sprint 2 (Logika Inti Core MVP), Sprint 3 (Alur Interaksi Frontend & UI High-Fidelity), Sprint 4 (Audit Keamanan, Rate-Limiting & QA), dan Sprint 5 (Deployment Cloud VPS Staging & Serah Terima Kunci).",
            ],
            'pasal_3_biaya_dan_dp' => [
                'title' => 'Pasal 3: Nilai Kontrak & Ketentuan Pembayaran Uang Muka (DP)',
                'description' => "Total nilai investasi proyek adalah Rp " . number_format($contractAmount, 0, ',', '.') . " dengan 2 termin pembayaran: Termin 1: Uang Muka (DP 50%) sebesar Rp " . number_format($dpAmount, 0, ',', '.') . " dibayarkan sebelum pekerjaan dimulai; Termin 2: Pelunasan sisa 50% sebesar Rp " . number_format($pelunasanAmount, 0, ',', '.') . " dibayarkan setelah lolos UAT (User Acceptance Testing) dan serah terima akses penuh sistem.",
            ],
            'pasal_4_penguncian_scope' => [
                'title' => 'Pasal 4: Penguncian Ruang Lingkup (Scope Freeze & Protokol CR)',
                'description' => "Setelah tanda tangan digital dan konfirmasi pembayaran DP, spesifikasi PRD terkunci secara kriptografis (Scope Freeze). Segala permintaan fitur tambahan atau perubahan alur di luar MVP disepakati akan dituangkan ke dalam Addendum / Change Request (CR) terpisah dengan penyesuaian biaya dan estimasi waktu tambahan tanpa menunda kontrak utama.",
            ],
            'pasal_5_kerahasiaan_hki' => [
                'title' => 'Pasal 5: Kerahasiaan Data & Hak Kekayaan Intelektual (NDA & HKI)',
                'description' => "Setelah pelunasan biaya proyek diselesaikan, seluruh hak cipta kode sumber aplikasi, repositori privat GitHub, dan database diserahkan penuh kepada Pihak Pertama. Pihak Kedua ({$devEntity}) menjamin kerahasiaan penuh data bisnis, credential server, dan informasi rahasia Pihak Pertama.",
            ],
            'pasal_6_keabsahan_hukum' => [
                'title' => 'Pasal 6: Keabsahan Hukum Tanda Tangan Elektronik & Audit Hash SHA-256',
                'description' => "Surat perjanjian kerja sama ini sah dan mengikat secara hukum berdasarkan Undang-Undang ITE (Informasi dan Transaksi Elektronik) Republik Indonesia, dibubuhkan tanda tangan elektronik dengan pencatatan audit trail waktu presisi, alamat IP penandatangan, serta segel kriptografi hash SHA-256 yang anti-manipulasi.",
            ],
        ];
    }
}
