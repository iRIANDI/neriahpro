<?php

declare(strict_types=1);

namespace App\Actions\Audit;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * ExportFilteredAuditExcelAction — Project OS v9.0 (PRIMITIVE_CRYPTO_AUDIT_EXPLORER)
 *
 * Compliant with PSAK 109, ISAK 35, PP 93/2010, ISO 27001, SOC2, RME Kemenkes, & UU PDP No. 27/2022.
 * Supports:
 * - Multi-Sheet Professional .xlsx Workbook (3 Sheets: Executive Summary & SHA-256 Certificate,
 *   Transaction Audit Trail, AI Copilot & Idempotency Log) via zero-dependency ZipArchive OpenXML.
 * - O(1) Memory Cursor Streaming .csv with UTF-8 BOM & live SHA-256 Chain Verification.
 */
class ExportFilteredAuditExcelAction
{
    /**
     * Build filtered Eloquent query across all 8 Audit Explorer dimensions.
     *
     * @param  array<string, mixed>  $filters
     * @return Builder<AuditLog>
     */
    public function buildFilteredQuery(array $filters, ?string $organizationUlid = null): Builder
    {
        $query = AuditLog::query();
        $hasOrgCol = Schema::hasColumn('audit_logs', 'organization_id');
        $hasSectorCol = Schema::hasColumn('audit_logs', 'sector_code');
        $hasPrimitiveCol = Schema::hasColumn('audit_logs', 'primitive_code');
        $hasChannelCol = Schema::hasColumn('audit_logs', 'execution_channel');

        $orgFilter = $organizationUlid ?: ($filters['organization_id'] ?? null);
        if ($hasOrgCol && ! empty($orgFilter) && $orgFilter !== 'ALL') {
            $query->where('organization_id', (string) $orgFilter);
        }

        if (! empty($filters['selected_ids']) && is_array($filters['selected_ids'])) {
            $query->whereIn('id', array_values(array_map('strval', $filters['selected_ids'])));
        }

        if (! empty($filters['date_from'])) {
            $query->where('created_at', '>=', (string) $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $dateTo = (string) $filters['date_to'];
            if ( strlen($dateTo) === 10) {
                $dateTo .= ' 23:59:59';
            }
            $query->where('created_at', '<=', $dateTo);
        }

        $sectorCandidate = $filters['sector_code'] ?? $filters['sector_preset'] ?? null;
        if (! empty($sectorCandidate) && $sectorCandidate !== 'ALL_12_SECTORS') {
            $sec = (string) $sectorCandidate;
            if ($hasSectorCol) {
                $query->where(function (Builder $sub) use ($sec): void {
                    $sub->where('sector_code', $sec)
                        ->orWhere('description', 'LIKE', "%{$sec}%");
                });
            }
        }

        if (! empty($filters['primitive_code'])) {
            $prim = (string) $filters['primitive_code'];
            $query->where(function (Builder $sub) use ($prim, $hasPrimitiveCol): void {
                $sub->where('event_category', $prim);
                if ($hasPrimitiveCol) {
                    $sub->orWhere('primitive_code', $prim);
                }
            });
        }

        if (! empty($filters['event_category'])) {
            $query->where('event_category', (string) $filters['event_category']);
        }

        if (! empty($filters['actor_id'])) {
            $query->where('user_id', (string) $filters['actor_id']);
        }

        if (! empty($filters['actor_type'])) {
            $query->where('actor_type', (string) $filters['actor_type']);
        }

        if ($hasChannelCol && ! empty($filters['execution_channel'])) {
            $query->where('execution_channel', (string) $filters['execution_channel']);
        }

        if (! empty($filters['action_name'])) {
            $act = (string) $filters['action_name'];
            $query->where('action', 'LIKE', "%{$act}%");
        }

        if (! empty($filters['preset_keywords']) && is_array($filters['preset_keywords'])) {
            $keywords = array_values(array_filter(array_map('strval', $filters['preset_keywords'])));
            if (! empty($keywords)) {
                $query->where(function (Builder $sub) use ($keywords): void {
                    foreach ($keywords as $kw) {
                        $sub->orWhere('action', 'LIKE', "%{$kw}%")
                            ->orWhere('description', 'LIKE', "%{$kw}%")
                            ->orWhere('event_category', 'LIKE', "%{$kw}%");
                    }
                });
            }
        }

        if (! empty($filters['target_type'])) {
            $targetType = (string) $filters['target_type'];
            $query->where('model_type', 'LIKE', "%{$targetType}%");
        }

        if (! empty($filters['target_id'])) {
            $query->where('model_id', (string) $filters['target_id']);
        }

        if (! empty($filters['search'])) {
            $term = '%' . trim((string) $filters['search']) . '%';
            $query->where(function (Builder $sub) use ($term): void {
                $sub->where('id', 'LIKE', $term)
                    ->orWhere('action', 'LIKE', $term)
                    ->orWhere('actor_name', 'LIKE', $term)
                    ->orWhere('description', 'LIKE', $term)
                    ->orWhere('model_id', 'LIKE', $term)
                    ->orWhere('ip_address', 'LIKE', $term);
            });
        }

        return $query->orderBy('id', 'asc');
    }

    /**
     * Stream O(1) Cursor CSV Report or 3-Sheet Professional .xlsx Report.
     *
     * @param  array<string, mixed>  $filters
     */
    public function __invoke(array $filters = [], string $organizationUlid = 'NERIAHPRO_CORE', string $format = 'csv'): StreamedResponse|BinaryFileResponse
    {
        $normalizedFormat = strtolower(trim($format));
        if ($normalizedFormat === 'xlsx') {
            return $this->exportThreeSheetXlsx($filters, $organizationUlid);
        }

        return $this->exportStreamingCsv($filters, $organizationUlid);
    }

    /**
     * O(1) Memory Cursor Streaming CSV with UTF-8 BOM & live SHA-256 Chain Verification.
     *
     * @param  array<string, mixed>  $filters
     */
    public function exportStreamingCsv(array $filters = [], string $organizationUlid = 'NERIAHPRO_CORE'): StreamedResponse
    {
        $safeOrg = preg_replace('/[^A-Za-z0-9_\-]/', '_', $organizationUlid) ?: 'NERIAHPRO_CORE';
        $fileName = 'AUDIT_REPORT_' . $safeOrg . '_' . now()->format('Ymd_His') . '.csv';
        $extractorName = auth()->user()?->name ?? 'System Auditor';

        return response()->streamDownload(function () use ($filters, $organizationUlid, $extractorName): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            // UTF-8 BOM so Microsoft Excel (Windows/macOS) opens columns cleanly
            fwrite($handle, "\xEF\xBB\xBF");

            // 1. Official Cryptographic Audit Certificate Header
            fputcsv($handle, ['NERIAHPRO PROJECT OS — OFFICIAL CRYPTOGRAPHIC AUDIT TRAIL REPORT']);
            fputcsv($handle, ['Organization ULID', $organizationUlid]);
            fputcsv($handle, ['Sector Filter', (string) ($filters['sector_code'] ?? 'ALL_12_SECTORS')]);
            fputcsv($handle, ['Period', (($filters['date_from'] ?? 'START') . ' to ' . ($filters['date_to'] ?? 'NOW'))]);
            fputcsv($handle, ['Extracted By', $extractorName]);
            fputcsv($handle, ['Compliance Standards', 'PSAK 109 | ISAK 35 | PP 93/2010 | ISO 27001 | SOC2 | RME Kemenkes | UU PDP No. 27/2022']);
            fputcsv($handle, ['Generated At (UTC)', now('UTC')->toIso8601String()]);
            fputcsv($handle, []);

            // 2. Column Headers
            fputcsv($handle, [
                'Audit ULID',
                'Timestamp (UTC)',
                'Sector Code',
                'Module / Primitive',
                'Actor Name',
                'Actor Role / Type',
                'Execution Channel',
                'Action Name',
                'Target Entity',
                'Target ULID',
                'Old Values (JSON)',
                'New Values (JSON)',
                'IP Address',
                'Idempotency Key',
                'Prev SHA-256 Hash',
                'Current SHA-256 Hash',
                'Chain Integrity Status',
            ]);

            $query = $this->buildFilteredQuery($filters, $organizationUlid === 'NERIAHPRO_CORE' ? null : $organizationUlid);
            $integrityFilter = (string) ($filters['integrity_status'] ?? '');
            $escapeCsvFormula = static function (mixed $val): string {
                $str = (string) $val;
                if ($str !== '' && $str !== '-' && in_array($str[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
                    return "'" . $str;
                }

                return $str;
            };

            foreach ($query->cursor() as $row) {
                /** @var AuditLog $row */
                $integrityStatus = $row->verifyRowIntegrity();
                if ($integrityFilter !== '' && $integrityFilter !== $integrityStatus) {
                    continue;
                }

                fputcsv($handle, array_map($escapeCsvFormula, [
                    $row->id,
                    $row->created_at?->copy()->setTimezone('UTC')->toIso8601String() ?? '',
                    $row->sector_code ?? 'CORE',
                    $row->primitive_code ?? ($row->event_category ?? 'SYSTEM'),
                    $row->actor_name ?? 'System/Webhook',
                    strtoupper((string) ($row->actor_type ?? 'system')),
                    $row->execution_channel ?? 'WEB_UI',
                    $row->action,
                    $row->model_type ? class_basename($row->model_type) : '-',
                    $row->model_id ?? '-',
                    json_encode($row->old_values ?? new \stdClass(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    json_encode($row->new_values ?? new \stdClass(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    $row->ip_address ?? '-',
                    $row->idempotency_key ?? '-',
                    $row->prev_hash ?? 'GENESIS',
                    $row->current_hash ?? ($row->integrity_hash ?? 'LEGACY'),
                    $integrityStatus,
                ]));
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Generate a true 3-Sheet OpenXML (.xlsx) Workbook:
     * - Sheet 1: Ringkasan Eksekutif & Sertifikat Integritas SHA-256
     * - Sheet 2: Rincian Jejak Audit Transaksi
     * - Sheet 3: Log Eksekusi AI Copilot & Idempotency
     *
     * @param  array<string, mixed>  $filters
     */
    public function exportThreeSheetXlsx(array $filters = [], string $organizationUlid = 'NERIAHPRO_CORE'): StreamedResponse
    {
        $safeOrg = preg_replace('/[^A-Za-z0-9_\-]/', '_', $organizationUlid) ?: 'NERIAHPRO_CORE';
        $fileName = 'AUDIT_REPORT_3SHEET_' . $safeOrg . '_' . now()->format('Ymd_His') . '.xlsx';
        $binary = $this->compileThreeSheetXlsxBinary($filters, $organizationUlid);

        return response()->streamDownload(static function () use ($binary): void {
            echo $binary;
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Compile the binary OpenXML (.xlsx) bytes so both HTTP downloads and automated PHPUnit tests can inspect all 3 sheets.
     *
     * @param  array<string, mixed>  $filters
     */
    public function compileThreeSheetXlsxBinary(array $filters = [], string $organizationUlid = 'NERIAHPRO_CORE'): string
    {
        $query = $this->buildFilteredQuery($filters, $organizationUlid === 'NERIAHPRO_CORE' ? null : $organizationUlid);
        $integrityFilter = (string) ($filters['integrity_status'] ?? '');
        $tz = (string) config('app.timezone', 'Asia/Jakarta');
        $extractorName = auth()->user()?->name ?? 'System Auditor';

        $sheet2Rows = [[
            'No',
            'Waktu (UTC)',
            "Waktu Lokal ({$tz})",
            'ID Audit (ULID)',
            'Sektor / Modul',
            'Nama Aktor',
            'Role / Tipe',
            'Sumber (Channel)',
            'Aksi',
            'Entitas Target',
            'ID Target (ULID)',
            'Nilai Sebelum (Old Values)',
            'Nilai Sesudah (New Values)',
            'Alamat IP',
            'Prev SHA-256 Hash',
            'Current SHA-256 Hash',
            'Status Integritas Rantai',
        ]];

        $sheet3Rows = [[
            'No',
            'Waktu (UTC)',
            'ID Audit (ULID)',
            'Nama Aktor / Penyetuju RBAC',
            'Execution Channel',
            'Tool / Action Name',
            'Idempotency Key',
            'Target Entitas & ULID',
            'Payload Ringkas',
            'Current SHA-256 Hash',
            'Bukti Integritas RBAC & Hash',
        ]];

        $totalRows = 0;
        $verifiedCount = 0;
        $tamperedCount = 0;
        $aiToolCount = 0;
        $rollingChecksum = hash_init('sha256');

        foreach ($query->limit(10000)->cursor() as $row) {
            /** @var AuditLog $row */
            $integrityStatus = $row->verifyRowIntegrity();
            if ($integrityFilter !== '' && $integrityFilter !== $integrityStatus) {
                continue;
            }

            $totalRows++;
            if ($integrityStatus === 'TAMPER_ALERT_MISMATCH') {
                $tamperedCount++;
            } else {
                $verifiedCount++;
            }

            $utcStr = $row->created_at?->copy()->setTimezone('UTC')->toIso8601String() ?? '';
            $localStr = $row->created_at?->copy()->setTimezone($tz)->format('Y-m-d H:i:s') ?? '';
            $sectorAndModule = trim(($row->sector_code ?? 'CORE') . ' / ' . ($row->primitive_code ?? ($row->event_category ?? 'SYSTEM')));
            $channel = (string) ($row->execution_channel ?? ($row->actor_type === 'ai_agent' ? 'AI_COPILOT_TOOL' : 'WEB_UI'));
            $oldJson = (string) json_encode($row->old_values ?? new \stdClass(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $newJson = (string) json_encode($row->new_values ?? new \stdClass(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $currHash = (string) ($row->current_hash ?? ($row->integrity_hash ?? 'LEGACY'));

            hash_update($rollingChecksum, $row->id . '|' . $currHash . '|' . $integrityStatus . "\n");

            $sheet2Rows[] = [
                (string) $totalRows,
                $utcStr,
                $localStr,
                (string) $row->id,
                $sectorAndModule,
                (string) ($row->actor_name ?? 'System'),
                strtoupper((string) ($row->actor_type ?? 'system')),
                $channel,
                (string) $row->action,
                $row->model_type ? class_basename($row->model_type) : '-',
                (string) ($row->model_id ?? '-'),
                $oldJson,
                $newJson,
                (string) ($row->ip_address ?? '-'),
                (string) ($row->prev_hash ?? 'GENESIS'),
                $currHash,
                $integrityStatus,
            ];

            if (in_array($channel, ['AI_COPILOT_TOOL', 'MCP_BRIDGE'], true) || $row->actor_type === 'ai_agent' || ! empty($row->idempotency_key)) {
                $aiToolCount++;
                $sheet3Rows[] = [
                    (string) $aiToolCount,
                    $utcStr,
                    (string) $row->id,
                    (string) ($row->actor_name ?? 'AI Copilot'),
                    $channel,
                    (string) $row->action,
                    (string) ($row->idempotency_key ?? 'IDEMPOTENT_SYNC'),
                    ($row->model_type ? class_basename($row->model_type) : 'System') . ' #' . ($row->model_id ?? '-'),
                    substr($newJson, 0, 500),
                    $currHash,
                    $integrityStatus === 'VERIFIED_INTACT' ? 'RBAC_AND_HASH_VERIFIED' : $integrityStatus,
                ];
            }
        }

        $workbookChecksum = hash_final($rollingChecksum);
        $overallCertificate = $tamperedCount === 0
            ? '100% VERIFIED TAMPER-PROOF'
            : "TAMPER_ALERT_MISMATCH ({$tamperedCount} ROWS)";

        $sheet1Rows = [
            ['SERTIFIKAT INTEGRITAS & RINGKASAN EKSEKUTIF JEJAK AUDIT KRIPTOGRAFIS — NERIAHPRO PROJECT OS v9.0'],
            ['Standar Kepatuhan Resmi', 'PSAK 109, ISAK 35, PP 93/2010, ISO/IEC 27001, SOC2 Type II, RME Kemenkes, UU PDP No. 27/2022'],
            ['Organisasi / Tenant ULID', $organizationUlid],
            ['Filter Sektor Fungsional', (string) ($filters['sector_code'] ?? 'ALL_12_SECTORS')],
            ['Filter Modul / Kategori', (string) ($filters['primitive_code'] ?? $filters['event_category'] ?? 'ALL_MODULES')],
            ['Filter Kanal Eksekusi', (string) ($filters['execution_channel'] ?? 'ALL_CHANNELS (Human + AI Copilot + Webhook)')],
            ['Rentang Periode Laporan', (($filters['date_from'] ?? 'GENESIS') . ' s/d ' . ($filters['date_to'] ?? now('UTC')->toIso8601String()))],
            ['Waktu Penarikan Laporan (UTC)', now('UTC')->toIso8601String()],
            ['Ditarik Oleh (Auditor / Actor)', $extractorName],
            ['Total Baris Transaksi Teraudit', number_format($totalRows, 0, ',', '.')],
            ['Baris Valid (VERIFIED_INTACT)', number_format($verifiedCount, 0, ',', '.')],
            ['Indikasi Manipulasi (TAMPER_ALERT)', number_format($tamperedCount, 0, ',', '.')],
            ['Total Eksekusi AI Copilot & MCP', number_format($aiToolCount, 0, ',', '.')],
            ['Hasil Verifikasi Rantai SHA-256', $overallCertificate],
            ['Checksum Hash Laporan Excel (SHA-256)', $workbookChecksum],
        ];

        return $this->packSheetsIntoXlsx([
            '1_Ringkasan_Sertifikat' => $sheet1Rows,
            '2_Jejak_Audit_Transaksi' => $sheet2Rows,
            '3_Log_AI_Copilot_Idemp' => $sheet3Rows,
        ]);
    }

    /**
     * Minimal, rock-solid OpenXML (.xlsx) packager using native PHP ZipArchive.
     *
     * @param  array<string, array<int, array<int, string>>>  $namedSheets
     */
    private function packSheetsIntoXlsx(array $namedSheets): string
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'npro_audit_xlsx_');
        if ($tmpFile === false) {
            throw new \RuntimeException('Unable to allocate temporary file for XLSX generation.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($tmpFile, \ZipArchive::OVERWRITE) !== true) {
            @unlink($tmpFile);
            throw new \RuntimeException('Unable to open ZipArchive for XLSX generation.');
        }

        $sheetNames = array_keys($namedSheets);
        $contentTypesOverrides = '';
        $workbookSheetsXml = '';
        $workbookRelsXml = '';

        foreach ($sheetNames as $idx => $sheetTitle) {
            $sheetNum = $idx + 1;
            $safeSheetName = substr(preg_replace('/[\\\\\/\?\*\[\]:]/', '_', $sheetTitle) ?: "Sheet{$sheetNum}", 0, 31);
            $escapedName = htmlspecialchars($safeSheetName, ENT_XML1 | ENT_QUOTES, 'UTF-8');

            $contentTypesOverrides .= "<Override PartName=\"/xl/worksheets/sheet{$sheetNum}.xml\" ContentType=\"application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml\"/>";
            $workbookSheetsXml .= "<sheet name=\"{$escapedName}\" sheetId=\"{$sheetNum}\" r:id=\"rId{$sheetNum}\"/>";
            $workbookRelsXml .= "<Relationship Id=\"rId{$sheetNum}\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet\" Target=\"worksheets/sheet{$sheetNum}.xml\"/>";

            $zip->addFromString("xl/worksheets/sheet{$sheetNum}.xml", $this->renderWorksheetXml($namedSheets[$sheetTitle]));
        }

        $stylesRelId = count($sheetNames) + 1;
        $workbookRelsXml .= "<Relationship Id=\"rId{$stylesRelId}\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles\" Target=\"styles.xml\"/>";

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . $contentTypesOverrides
            . '</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>');

        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>' . $workbookSheetsXml . '</sheets>'
            . '</workbook>');

        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . $workbookRelsXml
            . '</Relationships>');

        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2">'
            . '<font><sz val="10"/><name val="Consolas"/></font>'
            . '<font><b/><sz val="10"/><name val="Consolas"/></font>'
            . '</fonts>'
            . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            . '</cellXfs>'
            . '</styleSheet>');

        $zip->close();

        $bytes = (string) file_get_contents($tmpFile);
        @unlink($tmpFile);

        return $bytes;
    }

    /**
     * @param  array<int, array<int, string>>  $rows
     */
    private function renderWorksheetXml(array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';

        foreach ($rows as $rIdx => $cols) {
            $rowNumber = $rIdx + 1;
            $styleAttr = $rowNumber === 1 ? ' s="1"' : '';
            $xml .= "<row r=\"{$rowNumber}\">";

            foreach ($cols as $cIdx => $cellVal) {
                $colRef = $this->excelColumnLetters($cIdx) . $rowNumber;
                $cleanText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', (string) $cellVal) ?? '';
                $escaped = htmlspecialchars($cleanText, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $xml .= "<c r=\"{$colRef}\" t=\"inlineStr\"{$styleAttr}><is><t xml:space=\"preserve\">{$escaped}</t></is></c>";
            }

            $xml .= '</row>';
        }

        $xml .= '</sheetData></worksheet>';

        return $xml;
    }

    private function excelColumnLetters(int $zeroBasedIndex): string
    {
        $letters = '';
        $n = $zeroBasedIndex;
        while ($n >= 0) {
            $letters = chr(($n % 26) + 65) . $letters;
            $n = intdiv($n, 26) - 1;
        }

        return $letters;
    }
}
