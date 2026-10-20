<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;

class HealthProbeController extends Controller
{
    /**
     * Comprehensive System Health Probe (/healthz).
     * Used by Kubernetes, Docker Swarm, and Uptime monitors.
     */
    public function healthz(): JsonResponse
    {
        $startTime = microtime(true);
        $checks = [];
        $isHealthy = true;

        // 1. Database Check
        try {
            $dbStart = microtime(true);
            DB::connection()->getPdo();
            $dbLatencyMs = round((microtime(true) - $dbStart) * 1000, 2);
            $checks['database'] = [
                'status' => 'operational',
                'latency_ms' => $dbLatencyMs,
                'connection' => config('database.default'),
            ];
        } catch (Throwable $e) {
            $isHealthy = false;
            $checks['database'] = [
                'status' => 'down',
                'error' => $e->getMessage(),
            ];
        }

        // 2. Redis / Cache Check
        try {
            $cacheStart = microtime(true);
            Cache::put('healthz_ping', 1, 10);
            $cacheCheck = Cache::get('healthz_ping');
            $cacheLatencyMs = round((microtime(true) - $cacheStart) * 1000, 2);

            $checks['cache'] = [
                'status' => ($cacheCheck == 1) ? 'operational' : 'degraded',
                'latency_ms' => $cacheLatencyMs,
                'driver' => config('cache.default'),
            ];
        } catch (Throwable $e) {
            $checks['cache'] = [
                'status' => 'unreachable',
                'error' => $e->getMessage(),
                'driver' => config('cache.default'),
            ];
        }

        // 3. Filesystem Storage Writable Check
        $storageWritable = is_writable(storage_path('framework/cache'));
        $checks['storage'] = [
            'status' => $storageWritable ? 'operational' : 'read_only',
            'writable' => $storageWritable,
        ];
        if (!$storageWritable) {
            $isHealthy = false;
        }

        $totalDurationMs = round((microtime(true) - $startTime) * 1000, 2);

        $payload = [
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'total_duration_ms' => $totalDurationMs,
            'checks' => $checks,
            'environment' => config('app.env'),
            'app_version' => config('app.version', '1.0.0'),
            'php_version' => PHP_VERSION,
        ];

        return response()->json($payload, $isHealthy ? 200 : 503);
    }

    /**
     * Ultra-lightweight Liveness Probe (/livez).
     * Indicates whether the web server process is responsive.
     */
    public function livez(): JsonResponse
    {
        return response()->json([
            'status' => 'alive',
            'timestamp' => now()->toIso8601String(),
        ], 200);
    }

    /**
     * Readiness Probe (/readyz).
     * Indicates whether the application is ready to accept user traffic (DB reachable).
     */
    public function readyz(): JsonResponse
    {
        try {
            DB::connection()->getPdo();
            return response()->json([
                'status' => 'ready',
                'timestamp' => now()->toIso8601String(),
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'not_ready',
                'error' => 'Database connection unavailable',
                'timestamp' => now()->toIso8601String(),
            ], 503);
        }
    }
}
