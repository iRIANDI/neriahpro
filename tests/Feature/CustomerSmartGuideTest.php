<?php

namespace Tests\Feature;

use Tests\TestCase;

class CustomerSmartGuideTest extends TestCase
{
    public function test_smart_guide_page_renders_indonesian_successfully()
    {
        $response = $this->get('/smart-guide?lang=id');
        $response->assertStatus(200);
        $response->assertSee('Matriks Komparasi');
        $response->assertSee('Jalur Retail Digital');
        $response->assertSee('Jalur Studio MVP');
        $response->assertSee('Keamanan SLA & Midtrans');
    }

    public function test_smart_guide_renders_english_when_requested()
    {
        $response = $this->get('/smart-guide?lang=en');
        $response->assertStatus(200);
        $response->assertSee('Comparison Matrix');
        $response->assertSee('Retail Digital Track');
        $response->assertSee('Studio MVP Track');
        $response->assertSee('SLA & Midtrans Safety');
    }

    public function test_smart_guide_respects_session_locale()
    {
        $response = $this->withSession(['locale' => 'id'])->get('/smart-guide');
        $response->assertStatus(200);
        $response->assertSee('Matriks Komparasi');
        $response->assertSee('Jalur Retail Digital');

        $responseEn = $this->withSession(['locale' => 'en'])->get('/smart-guide');
        $responseEn->assertStatus(200);
        $responseEn->assertSee('Comparison Matrix');
        $responseEn->assertSee('Retail Digital Track');
    }
}
