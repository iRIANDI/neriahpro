<?php

namespace App\Support;

class BlueprintPdfHelper
{
    /**
     * Parse and format raw text/array into cleanly indented, readable bullet/numbered points.
     * Supports both DomPDF table layout and responsive Web/Tailwind layout.
     * Optionally translates each item to English if $isEn is true.
     */
    public static function formatPoints(string|array|null $content, bool $ordered = true, bool $isPdf = true, bool $isEn = false): string
    {
        if (empty($content)) {
            return $isPdf 
                ? '<span style="color: #94a3b8; font-style: italic;">-</span>'
                : '<span class="text-zinc-400 italic text-xs">-</span>';
        }

        $items = self::extractItems($content);

        if (empty($items)) {
            return $isPdf 
                ? '<span style="color: #94a3b8; font-style: italic;">-</span>'
                : '<span class="text-zinc-400 italic text-xs">-</span>';
        }

        if ($isEn) {
            $items = array_map(fn($item) => self::translateText((string)$item, true), $items);
        }

        if (count($items) === 1) {
            $escaped = htmlspecialchars((string)$items[0]);
            if (str_contains($escaped, ':')) {
                $parts = explode(':', $escaped, 2);
                return $isPdf
                    ? '<div style="line-height: 1.45;"><strong>' . $parts[0] . ':</strong>' . $parts[1] . '</div>'
                    : '<div class="leading-relaxed"><strong class="font-bold text-zinc-900 dark:text-zinc-100">' . $parts[0] . ':</strong>' . $parts[1] . '</div>';
            }
            return $isPdf
                ? '<div style="line-height: 1.45;">' . $escaped . '</div>'
                : '<div class="leading-relaxed">' . $escaped . '</div>';
        }

        if ($isPdf) {
            $html = '<table style="width: 100%; border-collapse: collapse; border: none; margin: 1px 0;">';
            foreach ($items as $idx => $item) {
                $num = $idx + 1;
                $escaped = htmlspecialchars((string)$item);

                // Auto-bold label before colon if present (e.g. "Fragmentasi: Penjelasan...")
                if (str_contains($escaped, ':')) {
                    $parts = explode(':', $escaped, 2);
                    $formattedText = '<strong>' . $parts[0] . ':</strong>' . $parts[1];
                } else {
                    $formattedText = $escaped;
                }

                $bullet = $ordered
                    ? '<span style="font-weight: bold; color: #047857; font-family: monospace; font-size: 8px;">' . $num . '.</span>'
                    : '<span style="font-weight: bold; color: #047857; font-size: 9px; line-height: 1;">&bull;</span>';

                $html .= '<tr>';
                $html .= '<td style="width: 16px; border: none; padding: 2px 0 3px 0; vertical-align: top; text-align: left;">' . $bullet . '</td>';
                $html .= '<td style="border: none; padding: 2px 0 3px 2px; vertical-align: top; font-size: 8.5px; line-height: 1.4; color: #1e293b;">' . $formattedText . '</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';

            return $html;
        }

        // Web / Tailwind responsive mode
        $html = '<ul class="space-y-2 my-1 text-xs sm:text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">';
        foreach ($items as $idx => $item) {
            $num = $idx + 1;
            $escaped = htmlspecialchars((string)$item);

            if (str_contains($escaped, ':')) {
                $parts = explode(':', $escaped, 2);
                $formattedText = '<strong class="font-bold text-zinc-900 dark:text-zinc-100">' . $parts[0] . ':</strong>' . $parts[1];
            } else {
                $formattedText = $escaped;
            }

            $bullet = $ordered
                ? '<span class="font-mono font-bold text-xs text-emerald-600 dark:text-emerald-400 mt-0.5 shrink-0 select-none w-5">' . $num . '.</span>'
                : '<span class="text-emerald-500 mt-0.5 shrink-0 select-none">&bull;</span>';

            $html .= '<li class="flex items-start gap-2">';
            $html .= $bullet;
            $html .= '<div class="flex-1">' . $formattedText . '</div>';
            $html .= '</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * Web-friendly alias for formatPoints($content, $ordered, false).
     */
    public static function formatWebPoints(string|array|null $content, bool $ordered = true): string
    {
        return self::formatPoints($content, $ordered, false);
    }

    /**
     * Extract individual items from raw string or array.
     */
    public static function extractItems(string|array $content): array
    {
        if (is_array($content)) {
            $flat = [];
            foreach ($content as $c) {
                if (is_array($c)) {
                    $flat[] = $c['name'] ?? $c['title'] ?? $c['task'] ?? json_encode($c);
                } elseif (!empty(trim((string)$c))) {
                    $flat[] = trim((string)$c);
                }
            }
            return $flat;
        }

        $text = trim((string)$content);
        if (empty($text)) {
            return [];
        }

        // 1. Check arrow workflows (e.g. "Step A -> Step B -> Step C")
        if (preg_match('/(?:\s*->\s*|\s*→\s*)/u', $text) && !preg_match('/^\d+[\.\)]/u', $text)) {
            $steps = preg_split('/(?:\s*->\s*|\s*→\s*)/u', $text, -1, PREG_SPLIT_NO_EMPTY);
            if (count($steps) > 1) {
                return array_map('trim', $steps);
            }
        }

        // 2. Check newlines
        if (str_contains($text, "\n")) {
            $lines = preg_split('/\r\n|\r|\n/', $text, -1, PREG_SPLIT_NO_EMPTY);
            $clean = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $clean[] = preg_replace('/^(?:[-*•]\s+|\d+[\.\)]\s*)/u', '', $line);
                }
            }
            if (count($clean) > 1) {
                return $clean;
            }
        }

        // 3. Check numbered list embedded in string (e.g. "1. Foo... 2. Bar..." or "1. Foo, 2. Bar")
        if (preg_match('/(?:^|\s+|,\s*|\.\s*)\d+[\.\)]\s+/u', $text)) {
            $parts = preg_split('/(?:^|\s+|,\s*|\.\s*)(?=\d+[\.\)]\s+)/u', $text, -1, PREG_SPLIT_NO_EMPTY);
            $clean = [];
            foreach ($parts as $p) {
                $p = trim($p, " \t\n\r\0\x0B,;");
                $p = preg_replace('/^\d+[\.\)]\s*/u', '', $p);
                if (!empty($p)) {
                    $clean[] = $p;
                }
            }
            if (count($clean) > 1) {
                return $clean;
            }
        }

        // 4. Check bullet points (e.g. "- Foo - Bar" or "• Foo • Bar")
        // Note: For hyphens (-), only treat as bullet delimiter if string starts with a bullet or uses true bullet glyphs (•, *)
        // to avoid splitting "Title - Description" into two separate items.
        if (preg_match('/^\s*[-•*]\s+/u', $text) || preg_match('/(?:^|\s+)[•*]\s+/u', $text)) {
            $parts = preg_split('/(?:^|\s+)(?=[-•*]\s+)/u', $text, -1, PREG_SPLIT_NO_EMPTY);
            $clean = [];
            foreach ($parts as $p) {
                $p = trim($p, " \t\n\r\0\x0B,;");
                $p = preg_replace('/^[-•*]\s*/u', '', $p);
                if (!empty($p)) {
                    $clean[] = $p;
                }
            }
            if (count($clean) > 1) {
                return $clean;
            }
        }

        return [$text];
    }

    /**
     * Translate or localize text from Indonesian to English if $isEn is true.
     * Uses dictionary mapping first, then falls back to GoogleTranslateService.
     */
    public static function translateText(?string $text, bool $isEn = false): string
    {
        if (!$isEn || empty($text)) {
            return (string) $text;
        }

        $trimmed = trim((string) $text);
        if ($trimmed === '') {
            return '';
        }

        // Direct dictionary lookup for common domain phrases
        static $dict = [
            'Otomatisasi proses bisnis dan administrasi operasional' => 'Automation of business processes and operational administration',
            'eliminasi duplikasi input data manual' => 'eliminating duplicate manual data entry',
            'sentralisasi basis data transaksi organisasi' => 'centralizing organizational transaction databases',
            'Meningkatkan efisiensi kerja tim operasional' => 'Increase operational efficiency for the team',
            'meminimalisir kesalahan pelaporan' => 'minimize reporting errors',
            'menyediakan platform digital terpadu yang andal dan scalable' => 'provide a reliable and scalable integrated digital platform',
            'Pengguna internal organisasi, staf operasional, tim manajemen, dan pelanggan publik.' => 'Internal organizational staff, operational coordinators, management, and public end-customers.',
            'Pengguna internal organisasi dan klien eksternal.' => 'Internal organizational staff, operators, and external clients.',
            'Superadmin (Full Control), Operator/Staff (Input & Validasi), Klien/Pelanggan (Self-Service).' => 'Superadmin (Full Control), Operator/Staff (Input & Validation), Client/Customer (Self-Service).',
            'Superadmin (Full Control), Staff/Operator (Input & Review), Pengunjung/Klien (Pengisian Form).' => 'Superadmin (Full Control), Staff/Operator (Input & Review), Visitors/Clients (Public Interaction).',
            'Autentikasi Aman & RBAC, Formulir Intake Tervalidasi, Dasbor Administrasi Filament v5, Rekapitulasi Data & Ekspor PDF/Excel.' => 'Secure Authentication & RBAC, Validated Intake Forms, Filament PHP v5 Admin Dashboard, Dynamic Data Ledger & PDF/Excel Export.',
            'Manajemen Pengguna & RBAC, Input Formulir Data, Rekap Database Dinamis, Ekspor PDF/Excel.' => 'User Management & RBAC, Structured Intake Forms, Real-Time Database Ledger, PDF/Excel Export.',
            'Notifikasi Otomatis WhatsApp/Email E.164, Audit Trail Activity Log, Dual Theme Dark/Light Mode.' => 'Automated WhatsApp & Email Notifications (E.164), Cryptographic Activity Audit Trail, Dual Dark/Light Mode Theme.',
            'Notifikasi WhatsApp/Email otomatis, Audit Trail Log, Dark Mode.' => 'Automated WhatsApp & Email Notifications, Cryptographic Audit Trail, Dark/Light Mode Theme.',
            'Midtrans Snap Payment Gateway, Cloud Storage S3, WhatsApp API Gateway.' => 'Midtrans Snap Payment Gateway, Amazon S3 Cloud Storage, WhatsApp Business API Gateway.',
            'Payment Gateway, WhatsApp Gateway, Cloud Storage S3.' => 'Payment Rails, WhatsApp Gateway API, S3 Cloud Storage.',
            'Database Baru Bersih (Clean Start)' => 'Clean Start New Database',
            'Database Baru Bersih' => 'Clean Start New Database',
            'Managed Cloud VPS Neriah Pro (Hardened Linux)' => 'Managed Cloud VPS Neriah Pro (Hardened Linux)',
            'Sedang Disiapkan' => 'Currently In Preparation',
            'Sedang Disiapkan Tim Internal' => 'Currently In Preparation by Internal Team',
            '30 Hari Kalender Pascameluncur' => '30 Calendar Days Post-Launch',
            '30 Hari Garansi Bug Pascameluncur' => '30 Days Post-Launch Bug Warranty',
            'Termin Standar 50/50' => 'Standard Milestone 50/50',
            '0 - 100.000 Pengguna / Bulan' => '0 - 100,000 Users / Month',
            'Domestik Indonesia' => 'Domestic Indonesia',
        ];

        if (isset($dict[$trimmed])) {
            return $dict[$trimmed];
        }

        // Try GoogleTranslateService
        try {
            $translated = \App\Services\Translation\GoogleTranslateService::directTranslate($trimmed, 'en', 'id');
            if (!empty($translated) && $translated !== $trimmed) {
                return $translated;
            }
        } catch (\Throwable) {}

        // Fallback: Perform term-by-term replacements
        static $termReplacements = [
            'Hari Kerja' => 'Business Days',
            'Pascameluncur' => 'Post-Launch',
            'Wisatawan' => 'Travelers',
            'Tamu hotel' => 'Hotel guests',
            'Pengemudi' => 'Drivers',
            'Armada' => 'Fleet',
            'Penjemputan' => 'Pickup',
            'Bandara' => 'Airport',
            'Skenario' => 'Scenario',
            'Izin:' => 'Permissions:',
            'Fitur Inti' => 'Core Feature',
            'Modul' => 'Module',
            'Tabel akun otentikasi' => 'Authentication user account table',
            'Entitas utama' => 'Primary entity',
            'Jejak audit' => 'Audit trail',
        ];

        $result = $trimmed;
        foreach ($termReplacements as $idTerm => $enTerm) {
            $result = str_ireplace($idTerm, $enTerm, $result);
        }

        return $result;
    }
}
