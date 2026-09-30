<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessSecureDataset implements ShouldQueue
{
    use Queueable;

    /**
     * Forbidden MIME types that should never be processed in dataset ingestion pipelines.
     */
    protected array $disallowedMimes = [
        'application/x-php',
        'application/x-httpd-php',
        'application/x-executable',
        'application/x-dosexec',
        'application/x-sh',
        'application/x-bash',
        'text/x-php',
        'text/x-python',
        'text/x-shellscript',
        'application/javascript',
    ];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $filePath,
        public ?string $originalName = null,
        public ?string $uploadedBy = null,
        public string $disk = 'local'
    ) {}

    /**
     * Execute the sandboxed ingestion job.
     */
    public function handle(): void
    {
        $storage = Storage::disk($this->disk);

        if (!$storage->exists($this->filePath)) {
            Log::channel('security')->warning('Secure Ingestion: File not found', [
                'path' => $this->filePath,
            ]);
            return;
        }

        $fullPath = $storage->path($this->filePath);

        // 1. Absolute MIME type detection via finfo (immune to extension spoofing)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMime = finfo_file($finfo, $fullPath);
        finfo_close($finfo);

        if (in_array(strtolower((string) $detectedMime), $this->disallowedMimes, true)) {
            Log::channel('security')->alert('Secure Ingestion: Disallowed executable MIME detected!', [
                'path' => $this->filePath,
                'detected_mime' => $detectedMime,
                'uploader' => $this->uploadedBy,
            ]);

            // Quarantine / delete malicious file
            $storage->delete($this->filePath);
            throw new \SecurityException("Ingestion rejected: executable or script MIME type detected ({$detectedMime}).");
        }

        // 2. Read raw data without dynamic execution
        $content = $storage->get($this->filePath);

        // 3. Scan for embedded executable tokens or PHP open tags
        if (preg_match('/<\?(?:php|=)/i', $content) || preg_match('/(?:system|exec|shell_exec|eval|passthru)\s*\(/i', $content)) {
            Log::channel('security')->alert('Secure Ingestion: Executable code payload embedded in dataset!', [
                'path' => $this->filePath,
                'uploader' => $this->uploadedBy,
            ]);

            $storage->delete($this->filePath);
            throw new \SecurityException('Ingestion rejected: Embedded executable script found within dataset content.');
        }

        // 4. Safe parsing based on file type
        $ext = strtolower(pathinfo($this->filePath, PATHINFO_EXTENSION));

        if (in_array($ext, ['xml', 'svg'], true)) {
            // XXE Protection: Disable external entity resolution
            $prevEntityLoader = libxml_disable_entity_loader(true);
            libxml_use_internal_errors(true);

            $dom = new \DOMDocument();
            $dom->loadXML($content, LIBXML_NONET | LIBXML_NOWARNING);
            libxml_clear_errors();

            if (is_callable($prevEntityLoader)) {
                libxml_disable_entity_loader($prevEntityLoader);
            }
        } elseif ($ext === 'csv') {
            // CSV Injection (Formula Injection) Sanitization
            $lines = explode("\n", $content);
            $cleanLines = [];
            foreach ($lines as $line) {
                $cells = str_getcsv($line);
                $cleanCells = array_map(function ($val) {
                    // Prepend single quote if cell starts with formula trigger (=, +, -, @, tab, newline)
                    if (is_string($val) && strlen($val) > 0 && in_array($val[0], ['=', '+', '-', '@', "\t", "\r"])) {
                        return "'" . $val;
                    }
                    return $val;
                }, $cells);
                $cleanLines[] = implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $cleanCells));
            }
            $storage->put($this->filePath, implode("\n", $cleanLines));
        }

        Log::channel('security')->info('Secure Ingestion: File verified and safely ingested', [
            'path' => $this->filePath,
            'mime' => $detectedMime,
            'size' => strlen($content),
        ]);
    }
}
