<?php

namespace App\Http\Middleware;

use App\Models\CmsGlobalSetting;
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
        'rce_system_call' => '/(?:system|exec|shell_exec|passthru|eval|proc_open|popen|pcntl_exec|assert)\s*[\(\`]/i',
        'python_dataset_loader_exploit' => '/(?:datasets\.load_dataset|trust_remote_code\s*=\s*(?:True|1)|pickle\.(?:loads|load)|torch\.load|joblib\.load|__reduce__\s*\()/i',
        'python_system_execution' => '/(?:import\s+(?:os|subprocess|sys|shutil|pty)|os\.(?:system|popen)|subprocess\.(?:Popen|run|call|check_output)|__import__\s*\(|pty\.spawn)/i',
        'php_object_deserialization' => '/(?:__construct|__destruct|__wakeup|__toString)\b|O:\d+:\s*\\\\?"[a-zA-Z0-9_\\\\]+|unserialize\s*\(/i',
        'php_info_probe' => '/phpinfo\s*\(|<\?php\b|<\?=|\bpreg_replace\s*\([^,]+,[^,]*\/e/i',
        'critical_path_traversal' => '/(?:\.\.\/|\.\.\\\\){2,}(?:etc\/passwd|windows\/win\.ini|proc\/self\/environ|\.env|\.git)/i',
        'ssti_template_injection' => '/\{\{\s*(?:config|app|request|self|system|exec|phpinfo|__globals__|__builtins__)\b/i',
    ];

    /**
     * Known sensitive reconnaissance paths targeted by autonomous bots and vulnerability scanners.
     */
    protected array $reconPaths = [
        '#\.env$#i',
        '#\.git(?:[/?]|$)#i',
        '#\.aws(?:[/?]|$)#i',
        '#\.dockerenv$#i',
        '#\.vscode(?:[/?]|$)#i',
        '#/actuator(?:[/?]|$)#i',
        '#/wp-(?:admin|login|config)(?:[/?]|$)#i',
        '#/phpmyadmin(?:[/?]|$)#i',
        '#/server-status$#i',
        '#/__debug__(?:[/?]|$)#i',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 0. Check Master Feature Toggle from CMS Global Settings
        try {
            if (! CmsGlobalSetting::getVal('feature_enable_ai_threat_shield', true)) {
                return $next($request);
            }
        } catch (\Throwable) {
            // Proceed with defense if database check fails
        }

        // Exempt payment gateway and external webhooks from threat shielding
        if ($request->is('api/webhook/*') || $request->is('webhook/*') || $request->is('api/midtrans/*') || $request->is('midtrans/*')) {
            return $next($request);
        }

        $ip = $request->ip() ?: '127.0.0.1';

        // 1. Check if IP is currently blocked due to previous exploit attempts
        $blockKey = "ai_shield_blocked_{$ip}";
        $isBlockedAlready = false;
        try {
            $isBlockedAlready = Cache::has($blockKey);
        } catch (\Throwable) {
            // Gracefully ignore cache storage unreachable issues
        }

        if ($isBlockedAlready) {
            try {
                Log::channel('security')->warning('Blocked IP attempted access', [
                    'ip' => $ip,
                    'endpoint' => $request->fullUrl(),
                ]);
            } catch (\Throwable) {}

            return $this->buildBlockedResponse($request, 'IP Anda telah diblokir sementara oleh sistem pertahanan siber AI-Shield karena terdeteksi aktivitas intrusi berulang.');
        }

        // 2. High-Frequency Autonomous Probing (Burst Rate Anomaly)
        $burstThreatDetected = false;
        try {
            $burstKey = "ai_shield_rate_{$ip}";
            $burstCount = (int) Cache::get($burstKey, 0) + 1;
            Cache::put($burstKey, $burstCount, now()->addSeconds(5));

            // Exceeding 35 requests within 5 seconds indicates automated machine probing
            if ($burstCount > 35) {
                $burstThreatDetected = true;
            }
        } catch (\Throwable) {}

        if ($burstThreatDetected) {
            return $this->interceptThreat(
                $request,
                $ip,
                'autonomous_velocity_burst',
                'Rapid automated probe burst rate exceeded threshold (>35 req / 5s)',
                'Burst Rate: ' . ($burstCount ?? 36) . ' req/5s'
            );
        }

        // 3. Sensitive Path Reconnaissance Detection
        $requestPath = '/' . ltrim($request->path(), '/');
        foreach ($this->reconPaths as $reconPattern) {
            if (preg_match($reconPattern, $requestPath)) {
                return $this->interceptThreat(
                    $request,
                    $ip,
                    'autonomous_recon_probing',
                    $requestPath,
                    "Reconnaissance attempt on sensitive system file [{$requestPath}]"
                );
            }
        }

        // 4. Extract and inspect full incoming payload (Query strings, Form data, JSON body)
        $inputData = json_encode($request->all());
        $rawContent = (string) $request->getContent();
        $payloadToCheck = $inputData . ' ' . $rawContent;

        // 5. Scan payload against autonomous exploit patterns
        foreach ($this->threatPatterns as $threatType => $pattern) {
            if (preg_match($pattern, $payloadToCheck, $matches)) {
                $matchedSnippet = $matches[0] ?? '';
                return $this->interceptThreat($request, $ip, $threatType, $matchedSnippet, $payloadToCheck);
            }
        }

        return $next($request);
    }

    /**
     * Intercept a detected threat, record strike, log audit, and return defense response.
     */
    protected function interceptThreat(
        Request $request,
        string $ip,
        string $threatType,
        string $matchedSnippet,
        string $payloadSample
    ): Response {
        $blockKey = "ai_shield_blocked_{$ip}";
        $strikeKey = "ai_shield_strikes_{$ip}";

        $isBlocked = false;
        $strikes = 1;

        try {
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

        // Record in database table
        try {
            SecurityThreatLog::create([
                'ip_address' => $ip,
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'endpoint' => substr($request->fullUrl(), 0, 255),
                'http_method' => $request->method(),
                'threat_type' => $threatType,
                'matched_pattern' => substr($matchedSnippet, 0, 255),
                'payload_sample' => substr($payloadSample, 0, 2000),
                'is_blocked' => $isBlocked,
            ]);
        } catch (\Throwable) {
            // Fail silently to prevent cascading exceptions
        }

        $message = 'Pelanggaran kebijakan keamanan siber: Muatan berbahaya atau percobaan eksploitasi otomatis terdeteksi (AI-Shield Active Defense).';

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'error' => $message,
                'code' => 'AI_SHIELD_POLICY_VIOLATION',
                'threat_type' => $threatType,
                'strikes' => $strikes,
                'is_blocked' => $isBlocked,
                'defense_mode' => 'ACTIVE_SANDBOX_INTERCEPTION',
            ], 403);
        }

        return response()->view('errors.cyber-defense', [
            'ip' => $ip,
            'threatType' => $threatType,
            'strikes' => $strikes,
            'isBlocked' => $isBlocked,
            'incidentId' => strtoupper(substr(md5($ip . microtime()), 0, 10)),
        ], 403);
    }

    /**
     * Build blocked IP response.
     */
    protected function buildBlockedResponse(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'error' => $message,
                'code' => 'AI_SHIELD_IP_BLOCKED',
                'defense_mode' => 'ISOLATION_WALL',
            ], 403);
        }

        return response()->view('errors.cyber-defense', [
            'ip' => $request->ip() ?: '127.0.0.1',
            'threatType' => 'ip_banned_active',
            'strikes' => 3,
            'isBlocked' => true,
            'incidentId' => strtoupper(substr(md5(($request->ip() ?: '127.0.0.1') . 'blocked'), 0, 10)),
        ], 403);
    }
}
