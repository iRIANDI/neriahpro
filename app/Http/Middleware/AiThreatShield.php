<?php

namespace App\Http\Middleware;

use App\Models\SecurityThreatLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AiThreatShield
{
    /**
     * Patterns commonly used by automated exploit scanners and autonomous AI probing engines.
     * Includes RCE, OS Command Injection, Unsafe Deserialization, Python dataset loader exploits, and SSTI.
     */
    protected array $threatPatterns = [
        'rce_system_call' => '/(?:system|exec|shell_exec|passthru|eval|proc_open|popen|pcntl_exec)\s*[\(\`]/i',
        'php_object_deserialization' => '/(?:__construct|__destruct|__wakeup|__toString)\b|O:\d+:\s*\\\\?"[a-zA-Z0-9_\\\\]+/i',
        'php_info_probe' => '/phpinfo\s*\(|<\?php\b/i',
        'python_dataset_rce' => '/(?:import\s+(?:os|subprocess|sys|shutil)|os\.system|subprocess\.(?:Popen|run|call)|__import__\s*\()/i',
        'critical_path_traversal' => '/(?:\.\.\/|\.\.\\\\){2,}(?:etc\/passwd|windows\/win\.ini|proc\/self\/environ)/i',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // 1. Check if IP is currently blocked due to previous exploit attempts
        $blockKey = "ai_shield_blocked_{$ip}";
        $isBlockedAlready = false;
        try {
            $isBlockedAlready = Cache::has($blockKey);
        } catch (\Throwable $e) {
            // Gracefully ignore cache storage unreachable issues
        }

        if ($isBlockedAlready) {
            try {
                Log::channel('security')->warning('Blocked IP attempted access', [
                    'ip' => $ip,
                    'endpoint' => $request->fullUrl(),
                ]);
            } catch (\Throwable) {}

            return response()->json([
                'success' => false,
                'error' => 'Akses ditolak: IP Anda diblokir sementara karena terdeteksi aktivitas intrusi mencurigakan.',
                'code' => 'AI_SHIELD_IP_BLOCKED',
            ], 403);
        }

        // 2. Extract and inspect full incoming payload (Query strings, Form data, JSON body)
        $inputData = json_encode($request->all());
        $rawContent = $request->getContent();
        $payloadToCheck = $inputData . ' ' . $rawContent;

        // 3. Scan payload against autonomous exploit patterns
        foreach ($this->threatPatterns as $threatType => $pattern) {
            if (preg_match($pattern, $payloadToCheck, $matches)) {
                $matchedSnippet = $matches[0] ?? '';

                // Increment IP strike count in cache (expires in 15 minutes)
                $isBlocked = false;
                $strikes = 1;
                try {
                    $strikeKey = "ai_shield_strikes_{$ip}";
                    $strikes = (int) Cache::get($strikeKey, 0) + 1;
                    Cache::put($strikeKey, $strikes, now()->addMinutes(15));

                    if ($strikes >= 3) {
                        Cache::put($blockKey, true, now()->addHours(2));
                        $isBlocked = true;
                    }
                } catch (\Throwable) {}

                // Log to security channel
                try {
                    Log::channel('security')->warning('AI / Automated Exploit Attempt Intercepted', [
                        'ip' => $ip,
                        'endpoint' => $request->fullUrl(),
                        'method' => $request->method(),
                        'threat_type' => $threatType,
                        'matched' => $matchedSnippet,
                        'strikes' => $strikes,
                        'is_blocked' => $isBlocked,
                        'user_agent' => $request->userAgent(),
                    ]);
                } catch (\Throwable) {}

                // Record in database table if available
                try {
                    SecurityThreatLog::create([
                        'ip_address' => $ip,
                        'user_agent' => substr((string) $request->userAgent(), 0, 500),
                        'endpoint' => substr($request->fullUrl(), 0, 255),
                        'http_method' => $request->method(),
                        'threat_type' => $threatType,
                        'matched_pattern' => substr($matchedSnippet, 0, 255),
                        'payload_sample' => substr($payloadToCheck, 0, 2000),
                        'is_blocked' => $isBlocked,
                    ]);
                } catch (\Throwable $e) {
                    // Fail silently to prevent crashing request handling if DB migration is pending
                }

                return response()->json([
                    'success' => false,
                    'error' => 'Pelanggaran kebijakan keamanan: Muatan berbahaya atau percobaan eksploitasi otomatis terdeteksi.',
                    'code' => 'AI_SHIELD_POLICY_VIOLATION',
                    'threat_type' => $threatType,
                ], 403);
            }
        }

        return $next($request);
    }
}
