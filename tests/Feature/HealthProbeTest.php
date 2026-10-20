<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthProbeTest extends TestCase
{
    public function test_healthz_endpoint_returns_json_and_healthy_status(): void
    {
        $response = $this->getJson('/healthz');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'timestamp',
            'total_duration_ms',
            'checks' => [
                'database',
                'cache',
                'storage',
            ],
            'environment',
            'php_version',
        ]);
        $response->assertJsonPath('status', 'healthy');
    }

    public function test_livez_endpoint_returns_200_and_alive_status(): void
    {
        $response = $this->getJson('/livez');

        $response->assertStatus(200);
        $response->assertExactJson([
            'status' => 'alive',
            'timestamp' => $response->json('timestamp'),
        ]);
    }

    public function test_readyz_endpoint_returns_200_and_ready_status(): void
    {
        $response = $this->getJson('/readyz');

        $response->assertStatus(200);
        $response->assertExactJson([
            'status' => 'ready',
            'timestamp' => $response->json('timestamp'),
        ]);
    }
}
