<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CvProFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test CV Pro Studio view loads successfully.
     */
    public function test_cv_pro_studio_page_loads_successfully(): void
    {
        $response = $this->get('/cv-pro');
        $response->assertStatus(200);
        $response->assertSee('CvProStudioIsland');
    }

    /**
     * Test ATS Linting API.
     */
    public function test_cv_pro_lint_api(): void
    {
        $payload = [
            'content' => [
                'personal_info' => [
                    'name' => 'Budi Santoso, S.T.',
                    'title' => 'Senior Backend Engineer',
                    'email' => 'budi.santoso@example.com',
                    'phone' => '+62 812-9876-5432',
                    'summary' => 'Insinyur perangkat lunak dengan pengalaman 5+ tahun dalam merancang sistem terdistribusi skala tinggi. Berhasil memangkas biaya server sebesar 30% dan meningkatkan uptime menjadi 99.99%.',
                ],
                'experiences' => [
                    [
                        'role' => 'Tech Lead',
                        'company' => 'PT Solusi Teknologi',
                        'period' => '2021 - Sekarang',
                        'location' => 'Jakarta',
                        'description' => 'Memimpin tim rekayasa platform cloud.',
                        'bullets' => [
                            'Mengarsitektur sistem antrian terdistribusi dengan Redis.',
                            'Meningkatkan throughput pemrosesan transaksi sebesar 250%.',
                        ],
                    ],
                ],
                'skills' => ['Laravel', 'PostgreSQL', 'Redis', 'Docker', 'REST API', 'PHP 8.4'],
                'education' => [
                    [
                        'institution' => 'Universitas Indonesia',
                        'degree' => 'Sarjana Teknik',
                        'field' => 'Teknik Komputer',
                        'year' => '2016 - 2020',
                    ],
                ],
            ],
            'lang' => 'id',
        ];

        $response = $this->postJson('/api/cv-pro/lint', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'overall_score',
                'sub_scores' => [
                    'completeness',
                    'quantifiable_impact',
                    'action_verbs',
                    'skills_keywords',
                ],
                'grade',
                'issues',
                'strengths',
            ],
        ]);
    }

    /**
     * Test Resume Save API and Public View.
     */
    public function test_cv_pro_save_and_public_show(): void
    {
        $payload = [
            'title' => 'Resume Lead Engineer',
            'target_role' => 'Principal Architect',
            'template' => 'modern_minimalist',
            'font_family' => 'Inter',
            'primary_color' => '#4f46e5',
            'content' => [
                'personal_info' => [
                    'name' => 'Dewi Lestari',
                    'title' => 'Principal Architect',
                    'email' => 'dewi@example.com',
                    'phone' => '+62 811-2233-4455',
                    'summary' => 'Principal Architect berpengalaman 8 tahun memimpin transformasi cloud dan basis data skala raksasa.',
                ],
                'experiences' => [],
                'skills' => ['Architecture', 'Kubernetes', 'PostgreSQL'],
                'education' => [],
            ],
            'lang' => 'id',
        ];

        $saveResponse = $this->postJson('/api/cv-pro/save', $payload);
        $saveResponse->assertStatus(200);
        $saveResponse->assertJson(['success' => true]);

        $resumeId = $saveResponse->json('data.id');
        $slug = $saveResponse->json('data.slug');
        $this->assertNotEmpty($resumeId);
        $this->assertNotEmpty($slug);

        // Verify public printable view loads with HTTP 200
        $showResponse = $this->get('/cv/' . $slug);
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Dewi Lestari');
    }

    /**
     * Test Interview Question Generation API.
     */
    public function test_interview_question_generation(): void
    {
        $payload = [
            'job_title' => 'Full Stack Engineer',
            'company' => 'Unicorn Tech',
            'job_description' => 'Mencari engineer berpengalaman dengan Laravel dan React untuk membangun platform enterprise.',
            'resume_content' => [
                'personal_info' => ['name' => 'Andi Wijaya'],
                'skills' => ['Laravel', 'React', 'PostgreSQL'],
            ],
            'lang' => 'id',
        ];

        $response = $this->postJson('/api/cv-pro/interview/generate', $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonCount(5, 'data.questions');
    }

    /**
     * Test STAR Method Interview Answer Evaluation API.
     */
    public function test_interview_star_evaluation(): void
    {
        $payload = [
            'question' => 'Ceritakan sebuah proyek teknis tersulit yang pernah Anda selesaikan.',
            'answer' => 'Ketika di perusahaan sebelumnya, proyek pembayaran mengalami kendala throughput tinggi. Tugas saya adalah merancang arsitektur baru. Saya merancang ulang skema basis data PostgreSQL dan menerapkan caching Redis bertingkat. Hasilnya, kami berhasil meningkatkan throughput sebesar 300% dan memangkas waktu tunggu API.',
            'lang' => 'id',
        ];

        $response = $this->postJson('/api/cv-pro/interview/evaluate', $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'score',
                'star_breakdown' => ['situation', 'task', 'action', 'result'],
                'feedback',
                'coaching_tip',
            ],
        ]);
        $this->assertTrue($response->json('data.star_breakdown.result'));
    }

    /**
     * Test Career Outreach Letter Generator API.
     */
    public function test_outreach_letter_generation(): void
    {
        $payload = [
            'type' => 'thank_you',
            'company' => 'Neriah Pro Tech',
            'job_title' => 'Software Architect',
            'recipient' => 'Bapak Hendra',
            'resume_content' => [
                'personal_info' => [
                    'name' => 'Reza Pahlevi',
                    'email' => 'reza@example.com',
                    'phone' => '+62 813-0000-1111',
                ],
            ],
            'lang' => 'id',
        ];

        $response = $this->postJson('/api/cv-pro/outreach/generate', $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertStringContainsString('Neriah Pro Tech', $response->json('data.content'));
        $this->assertStringContainsString('Reza Pahlevi', $response->json('data.content'));
    }

    /**
     * Test Tailor CV to Target Job Description API.
     */
    public function test_tailor_cv_to_job(): void
    {
        $payload = [
            'job_title' => 'Senior Backend Engineer',
            'company' => 'Fintech Indonesia',
            'job_description' => 'Mencari engineer ahli di Laravel, PostgreSQL, Docker, dan Redis.',
            'resume_content' => [
                'personal_info' => [
                    'name' => 'Budi Santoso',
                    'title' => 'Backend Developer',
                    'summary' => 'Pengembang backend 4 tahun.',
                ],
                'experiences' => [
                    [
                        'role' => 'Software Engineer',
                        'company' => 'Startup Digital',
                        'bullets' => ['Mengembangkan modul pembayaran'],
                    ],
                ],
                'skills' => ['PHP', 'MySQL'],
            ],
            'lang' => 'id',
            'humanize' => false,
        ];

        $response = $this->postJson('/api/cv-pro/tailor', $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'job_title',
                'company',
                'match_score',
                'suggested_keywords',
                'tailored_summary',
                'applied_content',
                'diff_preview',
                'ats_audit',
            ],
        ]);
        $this->assertEquals('Senior Backend Engineer', $response->json('data.job_title'));
    }

    /**
     * Test LinkedIn Personal Branding Generator API.
     */
    public function test_linkedin_generation(): void
    {
        $payload = [
            'resume_content' => [
                'personal_info' => [
                    'name' => 'Alex Pratama',
                    'title' => 'Lead Systems Architect',
                ],
                'skills' => ['Laravel', 'PostgreSQL', 'Redis', 'Docker'],
            ],
            'lang' => 'id',
        ];

        $response = $this->postJson('/api/cv-pro/linkedin', $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'headlines',
                'about',
                'recommended_skills',
                'thought_leadership_posts',
            ],
        ]);
        $this->assertCount(3, $response->json('data.headlines'));
    }

    /**
     * Test Quick AI Helper API (Summary, Bullet, Skills).
     */
    public function test_ai_helper(): void
    {
        // 1. Summary action
        $resSummary = $this->postJson('/api/cv-pro/ai-helper', [
            'action' => 'summary',
            'role' => 'Cloud Architect',
            'content' => ['skills' => ['AWS', 'Docker']],
            'lang' => 'id',
        ]);
        $resSummary->assertStatus(200);
        $this->assertNotEmpty($resSummary->json('data.summary'));

        // 2. Enhance Bullet action
        $resBullet = $this->postJson('/api/cv-pro/ai-helper', [
            'action' => 'enhance_bullet',
            'bullet' => 'membuat sistem cache',
            'role' => 'Backend Engineer',
            'lang' => 'id',
        ]);
        $resBullet->assertStatus(200);
        $this->assertNotEmpty($resBullet->json('data.bullet'));

        // 3. Skills Suggestion
        $resSkills = $this->postJson('/api/cv-pro/ai-helper', [
            'action' => 'skills',
            'role' => 'Systems Architect',
            'lang' => 'id',
        ]);
        $resSkills->assertStatus(200);
        $this->assertNotEmpty($resSkills->json('data.skills'));
    }
}

