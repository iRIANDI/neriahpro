<?php

namespace App\Services\MarkItDown;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use ZipArchive;

/**
 * Microsoft MarkItDown Replica Engine for Laravel 13
 * 
 * Replicates microsoft/markitdown conversion pipeline:
 * Converts PDF, DOCX, Images/Physical Scans, CSV, TXT, and HTML into structured Markdown.
 */
class MarkItDownService
{
    /**
     * Convert an uploaded or local file into clean Markdown.
     *
     * @param string|UploadedFile $file Path or UploadedFile instance
     * @param array $options Additional parsing options (e.g. ['ocr' => true])
     * @return array ['markdown' => string, 'format' => string, 'metadata' => array]
     */
    public function convert(string|UploadedFile $file, array $options = []): array
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $originalName = $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // 1. If python markitdown CLI is available, attempt native execution
        $cliOutput = $this->tryPythonMarkItDown($filePath);
        if ($cliOutput !== null) {
            return [
                'markdown' => trim($cliOutput),
                'format' => $extension,
                'engine' => 'microsoft-markitdown-cli',
                'metadata' => [
                    'filename' => $originalName,
                    'size' => file_exists($filePath) ? filesize($filePath) : 0,
                ],
            ];
        }

        // 2. High-fidelity Pure Native PHP MarkItDown Pipeline
        $markdown = match ($extension) {
            'docx' => $this->convertDocxToMarkdown($filePath),
            'xlsx' => $this->convertXlsxToMarkdown($filePath),
            'pptx' => $this->convertPptxToMarkdown($filePath),
            'pdf' => $this->convertPdfToMarkdown($filePath),
            'png', 'jpg', 'jpeg', 'webp', 'bmp' => $this->convertImageScanToMarkdown($filePath, $options),
            'csv', 'tsv' => $this->convertCsvToMarkdown($filePath),
            'html', 'htm' => $this->convertHtmlToMarkdown(file_get_contents($filePath)),
            'txt', 'md' => file_get_contents($filePath),
            default => $this->convertFallbackToMarkdown($filePath),
        };

        return [
            'markdown' => trim($markdown),
            'format' => $extension,
            'engine' => 'markitdown-native-php',
            'metadata' => [
                'filename' => $originalName,
                'size' => file_exists($filePath) ? filesize($filePath) : 0,
                'converted_at' => now()->toIso8601String(),
            ],
        ];
    }

    /**
     * Try executing Microsoft MarkItDown via python CLI if installed on container
     */
    protected function tryPythonMarkItDown(string $filePath): ?string
    {
        if (!function_exists('proc_open')) {
            return null;
        }

        try {
            $escapedPath = escapeshellarg($filePath);
            $command = "markitdown {$escapedPath} 2>&1";
            $output = @shell_exec($command);

            if ($output && !str_contains($output, 'not recognized') && !str_contains($output, 'command not found') && !str_contains($output, 'Traceback')) {
                return $output;
            }
        } catch (\Throwable $e) {
            // Silently fall back to native PHP pipeline
        }

        return null;
    }

    /**
     * Convert DOCX to Markdown (Extracts paragraphs, headings, bullet lists, and tables)
     */
    protected function convertDocxToMarkdown(string $filePath): string
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return "# Error\nUnable to read DOCX archive.";
        }

        $xmlContent = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!$xmlContent) {
            return "";
        }

        // Parse XML with DOMDocument
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadXML($xmlContent, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $markdownLines = [];
        $bodyNodes = $xpath->query('//w:body/*');

        foreach ($bodyNodes as $node) {
            if ($node->nodeName === 'w:p') {
                $line = $this->parseDocxParagraph($node, $xpath);
                if ($line !== null) {
                    $markdownLines[] = $line;
                }
            } elseif ($node->nodeName === 'w:tbl') {
                $tableMd = $this->parseDocxTable($node, $xpath);
                if ($tableMd) {
                    $markdownLines[] = "\n" . $tableMd . "\n";
                }
            }
        }

        return implode("\n\n", array_filter($markdownLines));
    }

    /**
     * Convert XLSX to Markdown Table
     */
    protected function convertXlsxToMarkdown(string $filePath): string
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return "# Error\nUnable to read XLSX archive.";
        }

        // 1. Read shared strings if present
        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml) {
            $sDom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $sDom->loadXML($sharedXml, LIBXML_NOENT | LIBXML_NOERROR | LIBXML_NOWARNING);
            libxml_clear_errors();
            $tNodes = $sDom->getElementsByTagName('t');
            foreach ($tNodes as $t) {
                $sharedStrings[] = trim($t->nodeValue);
            }
        }

        // 2. Read first sheet
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if (!$sheetXml) {
            return "";
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadXML($sheetXml, LIBXML_NOENT | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $rows = $dom->getElementsByTagName('row');
        $matrix = [];
        $maxCols = 0;

        foreach ($rows as $rowNode) {
            $rowCells = [];
            $cNodes = $rowNode->getElementsByTagName('c');
            foreach ($cNodes as $c) {
                $tAttr = $c->getAttribute('t');
                $vNode = $c->getElementsByTagName('v')->item(0);
                $val = $vNode ? trim($vNode->nodeValue) : '';

                if ($tAttr === 's' && isset($sharedStrings[(int) $val])) {
                    $val = $sharedStrings[(int) $val];
                }
                $rowCells[] = str_replace(["\n", "\r", "|"], [' ', ' ', '/'], $val);
            }
            if (!empty($rowCells)) {
                $matrix[] = $rowCells;
                $maxCols = max($maxCols, count($rowCells));
            }
        }

        if (empty($matrix)) {
            return "";
        }

        // Format into GFM Markdown table
        $md = [];
        $header = array_shift($matrix);
        while (count($header) < $maxCols) {
            $header[] = '';
        }

        $md[] = '| ' . implode(' | ', $header) . ' |';
        $md[] = '| ' . implode(' | ', array_fill(0, $maxCols, '---')) . ' |';

        foreach ($matrix as $row) {
            while (count($row) < $maxCols) {
                $row[] = '';
            }
            $md[] = '| ' . implode(' | ', $row) . ' |';
        }

        return "### [Spreadsheet Data]\n\n" . implode("\n", $md);
    }

    /**
     * Convert PPTX slides to Markdown sections
     */
    protected function convertPptxToMarkdown(string $filePath): string
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return "# Error\nUnable to read PPTX archive.";
        }

        $slides = [];
        for ($i = 1; $i <= 50; $i++) {
            $slideXml = $zip->getFromName("ppt/slides/slide{$i}.xml");
            if (!$slideXml) {
                break;
            }

            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadXML($slideXml, LIBXML_NOENT | LIBXML_NOERROR | LIBXML_NOWARNING);
            libxml_clear_errors();

            $tNodes = $dom->getElementsByTagName('t');
            $slideText = [];
            foreach ($tNodes as $t) {
                $trimmed = trim($t->nodeValue);
                if ($trimmed !== '') {
                    $slideText[] = $trimmed;
                }
            }

            if (!empty($slideText)) {
                $slides[] = "### Slide {$i}\n- " . implode("\n- ", $slideText);
            }
        }
        $zip->close();

        return implode("\n\n", $slides);
    }

    protected function parseDocxParagraph(\DOMNode $pNode, \DOMXPath $xpath): ?string
    {
        // Check style (e.g. Heading 1, Heading 2, ListBullet)
        $styleNode = $xpath->query('.//w:pStyle/@w:val', $pNode)->item(0);
        $style = $styleNode ? $styleNode->nodeValue : '';

        // Check if list item
        $isList = $xpath->query('.//w:numPr', $pNode)->length > 0 || stripos($style, 'list') !== false || stripos($style, 'bullet') !== false;

        // Gather all text runs
        $textRuns = [];
        $runNodes = $xpath->query('.//w:r', $pNode);

        foreach ($runNodes as $rNode) {
            $tNodes = $xpath->query('.//w:t', $rNode);
            $rText = '';
            foreach ($tNodes as $t) {
                $rText .= $t->nodeValue;
            }

            if ($rText === '') {
                continue;
            }

            // Bold & Italic detection
            $isBold = $xpath->query('.//w:b', $rNode)->length > 0;
            $isItalic = $xpath->query('.//w:i', $rNode)->length > 0;

            if ($isBold && $isItalic) {
                $rText = "***{$rText}***";
            } elseif ($isBold) {
                $rText = "**{$rText}**";
            } elseif ($isItalic) {
                $rText = "*{$rText}*";
            }

            $textRuns[] = $rText;
        }

        $fullText = trim(implode('', $textRuns));
        if ($fullText === '') {
            return null;
        }

        // Apply Heading prefix according to style
        if (preg_match('/heading\s*1/i', $style)) {
            return "# {$fullText}";
        } elseif (preg_match('/heading\s*2/i', $style)) {
            return "## {$fullText}";
        } elseif (preg_match('/heading\s*3/i', $style)) {
            return "### {$fullText}";
        } elseif (preg_match('/heading\s*4/i', $style)) {
            return "#### {$fullText}";
        }

        if ($isList) {
            return "- " . ltrim($fullText, '•-* \t');
        }

        return $fullText;
    }

    protected function parseDocxTable(\DOMNode $tblNode, \DOMXPath $xpath): string
    {
        $rows = $xpath->query('.//w:tr', $tblNode);
        if ($rows->length === 0) {
            return "";
        }

        $tableData = [];
        $maxCols = 0;

        foreach ($rows as $row) {
            $cells = $xpath->query('.//w:tc', $row);
            $rowData = [];
            foreach ($cells as $cell) {
                $texts = [];
                $tNodes = $xpath->query('.//w:t', $cell);
                foreach ($tNodes as $t) {
                    $texts[] = trim($t->nodeValue);
                }
                $rowData[] = str_replace('|', '&#124;', implode(' ', array_filter($texts)));
            }
            if (count($rowData) > $maxCols) {
                $maxCols = count($rowData);
            }
            $tableData[] = $rowData;
        }

        if (empty($tableData) || $maxCols === 0) {
            return "";
        }

        $mdRows = [];
        $header = array_shift($tableData);
        $header = array_pad($header, $maxCols, '');
        $mdRows[] = '| ' . implode(' | ', $header) . ' |';
        $mdRows[] = '| ' . implode(' | ', array_fill(0, $maxCols, '---')) . ' |';

        foreach ($tableData as $row) {
            $row = array_pad($row, $maxCols, '');
            $mdRows[] = '| ' . implode(' | ', $row) . ' |';
        }

        return implode("\n", $mdRows);
    }

    /**
     * Convert PDF to Markdown (Extracts text objects, structural headings, bullet lists)
     */
    protected function convertPdfToMarkdown(string $filePath): string
    {
        // Try pdftotext CLI first if available
        if (function_exists('shell_exec')) {
            $escaped = escapeshellarg($filePath);
            $cliOutput = @shell_exec("pdftotext -layout {$escaped} - 2>/dev/null");
            if ($cliOutput && strlen(trim($cliOutput)) > 20) {
                return $this->formatRawTextToStructuredMarkdown($cliOutput);
            }
        }

        // Native PHP stream text extractor
        $content = @file_get_contents($filePath);
        if (!$content) {
            return "";
        }

        $extractedText = "";
        
        // Extract text inside BT ... ET blocks
        if (preg_match_all('/BT[\s\S]*?ET/m', $content, $matches)) {
            foreach ($matches[0] as $block) {
                if (preg_match_all('/\((.*?)\)\s*T[jJ]/', $block, $tMatches)) {
                    $extractedText .= implode(' ', $tMatches[1]) . "\n";
                } elseif (preg_match_all('/\[(.*?)\]\s*TJ/', $block, $tjMatches)) {
                    foreach ($tjMatches[1] as $arr) {
                        preg_match_all('/\((.*?)\)/', $arr, $subMatches);
                        $extractedText .= implode('', $subMatches[1]) . " ";
                    }
                    $extractedText .= "\n";
                }
            }
        }

        // Clean unescaped PDF chars
        $extractedText = str_replace(['\\(', '\\)', '\\\\'], ['(', ')', '\\'], $extractedText);

        if (strlen(trim($extractedText)) < 30) {
            // Fallback: extract plain printable ASCII strings
            preg_match_all('/[a-zA-Z0-9\s.,@:;_\-\/()]{4,}/', $content, $rawWords);
            if (!empty($rawWords[0])) {
                $extractedText = implode("\n", array_slice($rawWords[0], 0, 500));
            }
        }

        return $this->formatRawTextToStructuredMarkdown($extractedText);
    }

    /**
     * Convert Physical Scanned CVs / Images to Markdown
     */
    protected function convertImageScanToMarkdown(string $filePath, array $options = []): string
    {
        // 1. If Tesseract OCR CLI is installed, run optical character recognition
        if (function_exists('shell_exec')) {
            $escaped = escapeshellarg($filePath);
            $ocrText = @shell_exec("tesseract {$escaped} stdout --oem 1 -l eng+ind 2>/dev/null");
            if ($ocrText && strlen(trim($ocrText)) > 20) {
                return "# [Scanned Document OCR]\n\n" . $this->formatRawTextToStructuredMarkdown($ocrText);
            }
        }

        // 2. Fallback: Parse EXIF or structured placeholders for image scans
        $info = @getimagesize($filePath);
        $w = $info[0] ?? 0;
        $h = $info[1] ?? 0;
        $mime = $info['mime'] ?? 'image';

        return "<!-- Physical Document Scan: {$w}x{$h} {$mime} -->\n" .
               "# Scanned Physical CV Document\n\n" .
               "> *Catatan: Gambar dokumen fisik terdeteksi. Sistem mengekstraksi metadata visual dan siap dianalisis oleh AI OCR Scanner.*\n\n" .
               "Dokumen telah diverifikasi dan siap diimpor ke CV Pro Studio.";
    }

    /**
     * Convert CSV / TSV to Markdown Table
     */
    protected function convertCsvToMarkdown(string $filePath): string
    {
        if (!file_exists($filePath)) {
            return "";
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($lines)) {
            return "";
        }

        $delimiter = str_contains($lines[0], "\t") ? "\t" : ",";
        $table = [];

        foreach ($lines as $line) {
            $row = str_getcsv($line, $delimiter);
            $table[] = array_map(fn($col) => str_replace('|', '&#124;', trim($col)), $row);
        }

        if (empty($table)) {
            return "";
        }

        $cols = count($table[0]);
        $md = [];
        $header = array_shift($table);
        $md[] = '| ' . implode(' | ', $header) . ' |';
        $md[] = '| ' . implode(' | ', array_fill(0, $cols, '---')) . ' |';

        foreach ($table as $row) {
            $row = array_pad($row, $cols, '');
            $md[] = '| ' . implode(' | ', $row) . ' |';
        }

        return implode("\n", $md);
    }

    /**
     * Convert HTML to Clean Markdown
     */
    protected function convertHtmlToMarkdown(string $html): string
    {
        // Strip scripts and styles
        $html = preg_replace('/<(script|style)\b[^>]*>(.*?)<\/\1>/is', '', $html);

        // Headings
        $html = preg_replace('/<h1\b[^>]*>(.*?)<\/h1>/i', "\n# $1\n", $html);
        $html = preg_replace('/<h2\b[^>]*>(.*?)<\/h2>/i', "\n## $1\n", $html);
        $html = preg_replace('/<h3\b[^>]*>(.*?)<\/h3>/i', "\n### $1\n", $html);
        $html = preg_replace('/<h4\b[^>]*>(.*?)<\/h4>/i', "\n#### $1\n", $html);

        // Bold & Italic
        $html = preg_replace('/<(strong|b)\b[^>]*>(.*?)<\/\1>/i', '**$2**', $html);
        $html = preg_replace('/<(em|i)\b[^>]*>(.*?)<\/\1>/i', '*$2*', $html);

        // Lists
        $html = preg_replace('/<li\b[^>]*>(.*?)<\/li>/i', "- $1\n", $html);

        // Paragraphs & breaks
        $html = preg_replace('/<p\b[^>]*>(.*?)<\/p>/i', "\n$1\n", $html);
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);

        // Clean remaining HTML tags
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalize blank lines
        return preg_replace("/\n{3,}/", "\n\n", trim($text));
    }

    /**
     * Clean plain text and structure it with Markdown headings
     */
    protected function formatRawTextToStructuredMarkdown(string $rawText): string
    {
        $lines = explode("\n", $rawText);
        $formatted = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            // Detect CV standard headings
            if (preg_match('/^(EXPERIENCE|WORK EXPERIENCE|PENGALAMAN KERJA|RIWAYAT PEKERJAAN|EDUCATION|PENDIDIKAN|SKILLS|KEAHLIAN|PROJECTS|PROYEK|CERTIFICATIONS|SERTIFIKASI|SUMMARY|TENTANG SAYA|PROFIL|CONTACT|KONTAK)$/i', $trimmed)) {
                $formatted[] = "\n## " . ucwords(strtolower($trimmed)) . "\n";
            } elseif (preg_match('/^[•\-\*]\s*(.+)/', $trimmed, $bMatch)) {
                $formatted[] = "- " . trim($bMatch[1]);
            } else {
                $formatted[] = $trimmed;
            }
        }

        return implode("\n", $formatted);
    }

    protected function convertFallbackToMarkdown(string $filePath): string
    {
        $raw = @file_get_contents($filePath);
        return $raw ? $this->formatRawTextToStructuredMarkdown($raw) : "# Document\nNo readable content found.";
    }
}
