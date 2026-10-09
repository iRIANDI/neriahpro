<?php

namespace App\Jobs;

use App\Exceptions\SecurityException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessSecureDataset implements ShouldQueue
{
    use Queueable;

    /**
     * Forbidden MIME types that should never be processed in dataset ingestion pipelines.
     */
    public const DISALLOWED_MIMES = [
        'application/x-php',
        'application/x-httpd-php',
        'application/x-executable',
        'application/x-dosexec',
        'application/x-sharedlib',
        'application/x-sh',
        'application/x-bash',
        'text/x-php',
        'text/x-python',
        'application/x-python-code',
        'text/x-shellscript',
        'application/javascript',
        'application/x-javascript',
        'application/x-msdownload',
        'application/x-perl',
        'application/x-ruby',
    ];

    /**
     * Forbidden executable file extensions.
     */
    public const DISALLOWED_EXTENSIONS = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps', 'phar',
        'py', 'pyc', 'pyw', 'sh', 'bash', 'zsh', 'exe', 'dll', 'bat', 'cmd', 'ps1', 'vbs', 'pl', 'rb'
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

        try {
            static::inspectAndSanitizeFile($fullPath, $this->filePath, $this->originalName, $this->disk, $this->uploadedBy);
        } catch (SecurityException $e) {
            // Quarantine / delete malicious file upon detection
            if ($storage->exists($this->filePath)) {
                $storage->delete($this->filePath);
            }
            throw $e;
        }
    }

    /**
     * Synchronously inspect and sanitize an uploaded file or stored path before further ingestion.
     *
     * @throws SecurityException
     */
    public static function inspectAndSanitizeUploadedFile(UploadedFile|string $fileOrPath, ?string $disk = 'local'): array
    {
        $uploadedFileInstance = null;
        if ($fileOrPath instanceof UploadedFile) {
            $uploadedFileInstance = $fileOrPath;
            $fullPath = $fileOrPath->getRealPath() ?: $fileOrPath->path();
            $originalName = $fileOrPath->getClientOriginalName();
            $relativeStoragePath = null;
        } else {
            $relativeStoragePath = $fileOrPath;
            $fullPath = Storage::disk($disk)->path($fileOrPath);
            $originalName = basename($fileOrPath);
        }

        return static::inspectAndSanitizeFile($fullPath, $relativeStoragePath, $originalName, $disk, null, $uploadedFileInstance);
    }

    /**
     * Internal inspector for absolute MIME, malicious executable scripts, XXE, and CSV formulas.
     *
     * @throws SecurityException
     */
    protected static function inspectAndSanitizeFile(
        string $fullPath,
        ?string $relativeStoragePath = null,
        ?string $originalName = null,
        string $disk = 'local',
        ?string $uploader = null,
        ?UploadedFile $uploadedFileInstance = null
    ): array {
        if (!$uploadedFileInstance && !file_exists($fullPath)) {
            throw new SecurityException("Ingestion failed: File does not exist at [{$fullPath}].", 404);
        }

        $filename = $originalName ?: basename($fullPath);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // 1. Strict Extension Whitelist check against executable extensions
        if (in_array($ext, static::DISALLOWED_EXTENSIONS, true)) {
            Log::channel('security')->alert('Secure Ingestion: Disallowed executable extension blocked!', [
                'filename' => $filename,
                'extension' => $ext,
                'uploader' => $uploader,
            ]);
            throw new SecurityException("Ingestion rejected: Executable file extension [{$ext}] is prohibited.", 403);
        }

        // 2. Read sample of raw data (up to 4MB) to scan for embedded executable payloads
        $content = '';
        if ($uploadedFileInstance instanceof \Illuminate\Http\Testing\File && isset($uploadedFileInstance->tempFile) && is_resource($uploadedFileInstance->tempFile)) {
            rewind($uploadedFileInstance->tempFile);
            $content = stream_get_contents($uploadedFileInstance->tempFile);
            rewind($uploadedFileInstance->tempFile);
        }

        if (empty($content) && $uploadedFileInstance) {
            try {
                $content = $uploadedFileInstance->get();
            } catch (\Throwable) {}
        }

        if (empty($content) && file_exists($fullPath)) {
            try {
                $content = @file_get_contents($fullPath, false, null, 0, 4 * 1024 * 1024) ?: '';
            } catch (\Throwable) {}
        }

        // 3. Absolute MIME type detection via finfo (immune to extension spoofing)
        $detectedMime = 'unknown';
        if ($uploadedFileInstance) {
            $detectedMime = $uploadedFileInstance->getMimeType() ?: 'unknown';
        }

        if (function_exists('finfo_open') && !empty($content)) {
            try {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $detectedMime = @finfo_buffer($finfo, substr($content, 0, 2048)) ?: $detectedMime;
                    finfo_close($finfo);
                }
            } catch (\Throwable) {}
        }

        if ($detectedMime === 'unknown' && function_exists('mime_content_type') && file_exists($fullPath)) {
            try {
                $detectedMime = @mime_content_type($fullPath) ?: 'unknown';
            } catch (\Throwable) {}
        }

        if (in_array(strtolower((string) $detectedMime), static::DISALLOWED_MIMES, true)) {
            Log::channel('security')->alert('Secure Ingestion: Disallowed executable MIME detected!', [
                'filename' => $filename,
                'detected_mime' => $detectedMime,
                'uploader' => $uploader,
            ]);
            throw new SecurityException("Ingestion rejected: executable or script MIME type detected ({$detectedMime}).", 403);
        }

        // Scan for PHP open tags
        if (preg_match('/<\?(?:php|=)/i', $content)) {
            Log::channel('security')->alert('Secure Ingestion: Embedded PHP tag detected in dataset!', [
                'filename' => $filename,
                'uploader' => $uploader,
            ]);
            throw new SecurityException('Ingestion rejected: Embedded PHP code found within dataset content.', 403);
        }

        // Scan for native command execution (avoid false positives on documentation words like "Management System (LMS)")
        if (!in_array($ext, ['md', 'markdown', 'txt', 'csv', 'tsv', 'json'], true)) {
            if (preg_match('/(?:\bexec|\bshell_exec|\beval|\bpassthru|\bproc_open|\bpopen)\s*\(/i', $content)) {
                Log::channel('security')->alert('Secure Ingestion: Embedded OS execution payload detected in dataset!', [
                    'filename' => $filename,
                    'uploader' => $uploader,
                ]);
                throw new SecurityException('Ingestion rejected: Embedded executable script found within dataset content.', 403);
            }
        }

        // Scan for Python Dataset Loader Exploit signatures (Exploit Gym / Hugging Face RCE vector)
        $datasetExploitPattern = '/(?:datasets\.load_dataset|trust_remote_code\s*=\s*(?:True|1)|pickle\.(?:loads|load)|torch\.load|joblib\.load|__reduce__\s*\(|import\s+(?:os|subprocess|sys|shutil)|subprocess\.(?:Popen|run|call)|os\.system)/i';
        if (preg_match($datasetExploitPattern, $content, $matches)) {
            Log::channel('security')->alert('Secure Ingestion: Autonomous Dataset Loader RCE attempt intercepted!', [
                'filename' => $filename,
                'matched' => $matches[0] ?? 'pattern',
                'uploader' => $uploader,
            ]);
            throw new SecurityException('Ingestion rejected: Unsafe dataset loader or deserialization exploit signature detected.', 403);
        }

        // 4. Safe sanitization based on file type
        $sanitized = false;

        if (in_array($ext, ['xml', 'svg'], true)) {
            // XXE Protection: Disable external entity resolution
            $prevEntityLoader = function_exists('libxml_disable_entity_loader') ? libxml_disable_entity_loader(true) : null;
            libxml_use_internal_errors(true);

            $dom = new \DOMDocument();
            $loaded = $dom->loadXML($content, LIBXML_NONET | LIBXML_NOWARNING);
            libxml_clear_errors();

            if (is_callable($prevEntityLoader)) {
                libxml_disable_entity_loader($prevEntityLoader);
            }

            if (!$loaded) {
                throw new SecurityException('Ingestion rejected: Malformed XML/SVG or external entity resolution detected.', 403);
            }
            $sanitized = true;
        } elseif (in_array($ext, ['csv', 'tsv'], true)) {
            // CSV Injection (Formula Injection) Sanitization: Prepend single quote if cell starts with formula trigger (=, +, -, @, tab, newline)
            $lines = explode("\n", $content);
            $cleanLines = [];
            $modified = false;

            foreach ($lines as $line) {
                if (trim($line) === '') {
                    continue;
                }
                $delimiter = ($ext === 'tsv') ? "\t" : ",";
                $cells = str_getcsv($line, $delimiter);
                $cleanCells = array_map(function ($val) use (&$modified) {
                    if (is_string($val) && strlen($val) > 0 && in_array($val[0], ['=', '+', '-', '@', "\t", "\r"])) {
                        $modified = true;
                        return "'" . $val;
                    }
                    return $val;
                }, $cells);
                $cleanLines[] = implode($delimiter, array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $cleanCells));
            }

            if ($modified) {
                file_put_contents($fullPath, implode("\n", $cleanLines));
                $sanitized = true;
            }
        }

        Log::channel('security')->info('Secure Ingestion: File verified and safely ingested', [
            'filename' => $filename,
            'mime' => $detectedMime,
            'size' => filesize($fullPath),
            'sanitized' => $sanitized,
        ]);

        return [
            'is_safe' => true,
            'mime' => $detectedMime,
            'original_name' => $filename,
            'sanitized' => $sanitized,
        ];
    }
}
