<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Services\CvPro\CvAiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CvProUltimateUpgradeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $this->user = User::factory()->create([
            'email' => 'architect@neriahpro.com',
        ]);
    }

    public function test_deterministic_hybrid_ats_scorer_calculates_weighted_formula(): void
    {
        $content = [
            'personal_info' => [
                'name' => 'Alex Wijaya',
                'title' => 'Senior Systems Architect',
                'email' => 'alex@example.com',
                'phone' => '+6281234567890',
                'summary' => 'Insinyur perangkat lunak dengan pengalaman 8+ tahun dalam merancang arsitektur sistem berskala tinggi.',
            ],
            'experiences' => [
                [
                    'role' => 'Lead Engineer',
                    'company' => 'Fintech Global',
                    'period' => '2023 - Present',
                    'bullets' => [
                        'Merancang ulang arsitektur pembayaran mikro yang meningkatkan throughput transaksi sebesar 340%.',
                        'Memangkas biaya infrastruktur cloud tahunan sebesar $45.000 melalui optimasi query dan caching Redis.',
                    ],
                ],
            ],
            'education' => [
                [
                    'degree' => 'S.Kom',
                    'field' => 'Informatika',
                    'institution' => 'ITB',
                    'year' => '2019',
                ],
            ],
            'skills' => ['Laravel 13', 'React 19', 'PostgreSQL', 'Docker', 'Redis', 'API Optimization'],
        ];

        $response = $this->postJson('/api/cv-pro/ats/deterministic-score', [
            'content' => $content,
            'job_description' => 'Looking for a Senior Architect skilled in PostgreSQL, Docker, Redis, and high scalability APIs.',
        ]);

        $response->assertSuccessful()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'final_score',
                    'grade',
                    'breakdown' => [
                        'exact_keyword_score',
                        'semantic_score',
                        'quantifiable_impact_score',
                        'parser_health_score',
                    ],
                    'weights',
                    'quantifiable_stats',
                    'matched_keywords',
                    'parser_health_status',
                ],
            ]);

        $score = $response->json('data.final_score');
        $this->assertGreaterThanOrEqual(60, $score);
    }

    public function test_ats_parser_emulator_produces_linear_stream_and_workday_json(): void
    {
        $content = [
            'personal_info' => [
                'name' => 'Alex Wijaya',
                'title' => 'Lead Architect',
                'email' => 'alex@example.com',
                'phone' => '+628123456789',
                'summary' => 'System architect specializing in high-throughput.',
            ],
            'experiences' => [
                [
                    'role' => 'Lead Engineer',
                    'company' => 'FinTech Global',
                    'period' => '2023 - 2026',
                    'bullets' => ['Architected O(1) query pagination.'],
                ],
            ],
            'education' => [
                [
                    'degree' => 'B.S.',
                    'field' => 'Computer Science',
                    'institution' => 'ITB',
                    'year' => '2020',
                ],
            ],
            'skills' => ['Go', 'PostgreSQL', 'Kubernetes'],
        ];

        $response = $this->postJson('/api/cv-pro/ats/emulator', [
            'content' => $content,
            'lang' => 'id',
        ]);

        $response->assertSuccessful()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'emulator_name',
                    'health_rating',
                    'raw_text_stream',
                    'workday_json',
                    'greenhouse_json',
                    'corruption_audit',
                ],
            ]);

        $this->assertStringContainsString('ALEX WIJAYA', $response->json('data.raw_text_stream'));
        $this->assertEquals('Alex Wijaya', $response->json('data.workday_json.applicant.name.formatted_name'));
    }

    public function test_google_xyz_rewriter_returns_structured_variations(): void
    {
        $response = $this->postJson('/api/cv-pro/ai/google-xyz', [
            'bullet' => 'Mengembangkan sistem pembayaran online dan database',
            'role' => 'Backend Engineer',
            'lang' => 'id',
        ]);

        $response->assertSuccessful()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'original',
                    'formula_breakdown' => [
                        'x_outcome',
                        'y_metric',
                        'z_action',
                    ],
                    'full_xyz_bullet',
                    'variations',
                    'rule_guarantee',
                ],
            ]);

        $this->assertNotEmpty($response->json('data.variations'));
    }

    public function test_company_osint_tailoring_generates_bespoke_pitch_banner(): void
    {
        $content = [
            'personal_info' => ['name' => 'Alex Wijaya', 'title' => 'Systems Architect'],
            'projects' => [
                ['name' => 'Legacy Blog App', 'description' => 'Simple CRUD'],
                ['name' => 'Payment Gateway Idempotency', 'description' => 'Kafka Go PostgreSQL high scale'],
            ],
        ];

        $response = $this->postJson('/api/cv-pro/osint/tailor', [
            'company' => 'Stripe',
            'role' => 'Staff Infrastructure Engineer',
            'content' => $content,
            'lang' => 'id',
        ]);

        $response->assertSuccessful()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.target_company', 'Stripe')
            ->assertJsonPath('data.company_slug', 'stripe')
            ->assertJsonPath('data.bespoke_micro_pitch.route_slug', '/for-stripe');

        $this->assertStringContainsString('Stripe', $response->json('data.tailored_ats_summary'));
    }

    public function test_typst_export_generates_valid_source_with_orphan_protection(): void
    {
        $content = [
            'personal_info' => ['name' => 'Alex Wijaya', 'title' => 'Architect'],
            'experiences' => [
                [
                    'role' => 'Lead Engineer',
                    'company' => 'Tech Corp',
                    'period' => '2023 - 2026',
                    'bullets' => ['Increased throughput by 300%.'],
                ],
            ],
            'education' => [
                ['degree' => 'S.Kom', 'field' => 'CS', 'institution' => 'ITB', 'year' => '2020'],
            ],
            'skills' => ['Go', 'Postgres'],
        ];

        $response = $this->postJson('/api/cv-pro/export/typst', [
            'content' => $content,
            'config' => [
                'density' => 'compact',
                'anonymized' => true,
            ],
        ]);

        $response->assertSuccessful()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.density', 'compact')
            ->assertJsonPath('data.is_anonymized', true);

        $source = $response->json('data.typst_source');
        $this->assertStringContainsString('#block(breakable: false', $source);
        $this->assertStringContainsString('BLIND HIRING', $source);
    }

    public function test_recruiter_chat_returns_co_browsing_ui_actions(): void
    {
        $content = [
            'personal_info' => ['name' => 'Alex Wijaya'],
            'experiences' => [
                [
                    'role' => 'Lead Engineer',
                    'company' => 'FinTech Global',
                    'bullets' => ['Memangkas biaya infrastruktur cloud tahunan sebesar $45.000.'],
                ],
            ],
        ];

        $response = $this->postJson('/api/cv-pro/recruiter-chat', [
            'query' => 'Apakah Alex pernah menangani optimasi biaya server cloud?',
            'content' => $content,
            'lang' => 'id',
        ]);

        $response->assertSuccessful()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'recruiter_query',
                    'ai_response_stream',
                    'ui_actions' => [
                        'scroll_to_element',
                        'highlight_widgets',
                        'suggested_follow_ups',
                    ],
                ],
            ]);

        $this->assertEquals('#exp-1', $response->json('data.ui_actions.scroll_to_element'));
    }

    public function test_case_study_converter_enforces_zero_hallucination_and_maps_widgets(): void
    {
        $response = $this->postJson('/api/cv-pro/ai/case-study-convert', [
            'experience' => [
                'id' => 'exp_01',
                'company' => 'FinTech Global',
                'role' => 'Lead Engineer',
                'description' => 'Sistem pembayaran monolitik mengalami kendala kapasitas.',
                'bullets' => [
                    'Meningkatkan throughput transaksi sebesar +340%.',
                    'Memangkas biaya cloud sebesar $45K/thn.',
                ],
            ],
            'lang' => 'id',
        ]);

        $response->assertSuccessful()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.zero_hallucination_verified', true)
            ->assertJsonStructure([
                'data' => [
                    'experience_id',
                    'case_study_title',
                    'confidence_score',
                    'structured_narrative' => [
                        'context_and_problem',
                        'technical_execution',
                        'measurable_outcomes',
                    ],
                    'missing_context_prompts',
                ],
            ]);

        $outcomes = $response->json('data.structured_narrative.measurable_outcomes');
        $this->assertNotEmpty($outcomes);
        $this->assertEquals('+340%', $outcomes[0]['metric_value']);
        $this->assertEquals('stat_card', $outcomes[0]['recommended_widget']);
    }

    public function test_portfolio_style_catalog_returns_7_types_10_styles_and_4_mvps(): void
    {
        $response = $this->getJson('/api/cv-pro/portfolio/catalog');

        $response->assertSuccessful()
            ->assertJsonPath('success', true);

        $this->assertCount(7, $response->json('data.functional_types'));
        $this->assertCount(10, $response->json('data.visual_styles'));
        $this->assertCount(4, $response->json('data.mvp_combinations'));
    }

    public function test_global_format_localization_adapts_norms_between_us_de_and_jp(): void
    {
        $response = $this->postJson('/api/cv-pro/localize-format', [
            'content' => ['personal_info' => ['name' => 'Alex Wijaya', 'location' => 'Munich']],
            'country_format' => 'de_lebenslauf',
            'lang' => 'id',
        ]);

        $response->assertSuccessful()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.selected_format.code', 'de_lebenslauf')
            ->assertJsonPath('data.selected_format.include_signature_block', true);
    }
}


