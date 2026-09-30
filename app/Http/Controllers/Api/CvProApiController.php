<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InterviewSession;
use App\Models\OutreachLetter;
use App\Models\Resume;
use App\Services\CvPro\CvAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CvProApiController extends Controller
{
    /**
     * Save or update a resume in the database with automatic ATS scoring.
     */
    public function save(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|string',
            'title' => 'required|string|max:255',
            'target_role' => 'nullable|string|max:255',
            'template' => 'nullable|string|max:50',
            'font_family' => 'nullable|string|max:50',
            'primary_color' => 'nullable|string|max:20',
            'content' => 'required|array',
            'photo_url' => 'nullable|string',
            'is_public' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $content = $request->input('content');
            $lang = $request->input('lang', 'id');

            // Automatically run ATS Linting on save
            $atsAudit = CvAiService::lintResume($content, $lang);

            $data = [
                'title' => $request->input('title'),
                'target_role' => $request->input('target_role') ?: ($content['personal_info']['title'] ?? 'Professional'),
                'template' => $request->input('template', 'modern_minimalist'),
                'font_family' => $request->input('font_family', 'Inter'),
                'primary_color' => $request->input('primary_color', '#4f46e5'),
                'photo_url' => $request->input('photo_url'),
                'content' => $content,
                'is_public' => $request->boolean('is_public', true),
                'ats_score' => $atsAudit['overall_score'],
                'ats_feedback' => $atsAudit,
            ];

            if ($request->filled('id')) {
                $resume = Resume::findOrFail($request->input('id'));
                $resume->update($data);
            } else {
                $resume = Resume::create($data);
            }

            return response()->json([
                'success' => true,
                'message' => 'Resume berhasil disimpan ke cloud!',
                'data' => [
                    'id' => $resume->id,
                    'slug' => $resume->slug,
                    'public_url' => $resume->public_url,
                    'ats_score' => $resume->ats_score,
                    'ats_label' => $resume->ats_label,
                    'ats_audit' => $atsAudit,
                ],
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan resume: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run real-time ATS Linting and quality audit.
     */
    public function lint(Request $request): JsonResponse
    {
        $content = $request->input('content', []);
        $lang = $request->input('lang', 'id');

        $result = CvAiService::lintResume($content, $lang);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Generate 5 tailored mock interview questions for the candidate.
     */
    public function generateInterview(Request $request): JsonResponse
    {
        $jobTitle = $request->input('job_title', 'Software Engineer');
        $company = $request->input('company', 'Target Company');
        $jobDesc = $request->input('job_description', '');
        $resumeContent = $request->input('resume_content', []);
        $lang = $request->input('lang', 'id');

        $result = CvAiService::generateInterviewQuestions($jobTitle, $company, $jobDesc, $resumeContent, $lang);

        // Save session if resume_id is provided
        if ($request->filled('resume_id')) {
            try {
                InterviewSession::create([
                    'resume_id' => $request->input('resume_id'),
                    'target_company' => $company,
                    'job_title' => $jobTitle,
                    'job_description' => $jobDesc,
                    'questions' => $result['questions'],
                ]);
            } catch (\Throwable $e) {
                // Ignore DB logging error
            }
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Evaluate candidate's spoken or written answer with the STAR framework.
     */
    public function evaluateAnswer(Request $request): JsonResponse
    {
        $question = $request->input('question', '');
        $answer = $request->input('answer', '');
        $lang = $request->input('lang', 'id');

        $result = CvAiService::evaluateInterviewAnswer($question, $answer, $lang);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Generate customized Thank You, Follow-up, or Cold Outreach letters.
     */
    public function generateOutreach(Request $request): JsonResponse
    {
        $type = $request->input('type', 'thank_you');
        $company = $request->input('company', 'Perusahaan Target');
        $jobTitle = $request->input('job_title', 'Posisi');
        $recipient = $request->input('recipient', '');
        $resumeContent = $request->input('resume_content', []);
        $lang = $request->input('lang', 'id');

        $content = CvAiService::generateOutreachLetter($type, $company, $jobTitle, $recipient, $resumeContent, $lang);

        if ($request->filled('resume_id')) {
            try {
                OutreachLetter::create([
                    'resume_id' => $request->input('resume_id'),
                    'letter_type' => $type,
                    'company_name' => $company,
                    'recipient_name' => $recipient,
                    'generated_content' => $content,
                ]);
            } catch (\Throwable $e) {
                // Ignore DB logging error
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'type' => $type,
                'content' => $content,
            ],
        ]);
    }

    /**
     * Upload and parse CV file using Microsoft MarkItDown replica pipeline
     * Supports: PDF, DOCX, TXT, MD, PNG, JPG, JPEG, WEBP, CSV
     */
    public function uploadCv(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cv_file' => 'required|file|max:20480', // 20MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak valid. Maksimal 20MB.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $file = $request->file('cv_file');
            
            // 1. Convert file to Markdown via Microsoft MarkItDown replica service
            $markItDownService = new \App\Services\MarkItDown\MarkItDownService();
            $conversion = $markItDownService->convert($file);

            // 2. Parse Markdown into structured CV data
            $cvParser = new \App\Services\MarkItDown\CvMarkdownParser();
            $parsedCv = $cvParser->parse($conversion['markdown']);

            // 3. Pre-audit ATS score on the newly parsed data
            $atsAudit = CvAiService::lintResume($parsedCv, $request->input('lang', 'id'));

            return response()->json([
                'success' => true,
                'message' => 'Berkas CV berhasil dipindai dan dikonversi dengan Microsoft MarkItDown!',
                'data' => [
                    'format' => $conversion['format'],
                    'engine' => $conversion['engine'],
                    'metadata' => $conversion['metadata'],
                    'markdown' => $conversion['markdown'],
                    'parsed_content' => $parsedCv,
                    'ats_audit' => $atsAudit,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses berkas CV: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tailor CV specifically to target Job Description (Applied CV Generator).
     */
    public function tailor(Request $request): JsonResponse
    {
        $jobTitle = $request->input('job_title', 'Software Engineer');
        $company = $request->input('company', 'Perusahaan Target');
        $jobDesc = $request->input('job_description', '');
        $resumeContent = $request->input('resume_content', []);
        $lang = $request->input('lang', 'id');
        $humanize = $request->boolean('humanize', false);

        $result = CvAiService::tailorCvToJob($resumeContent, $jobTitle, $company, $jobDesc, $lang, $humanize);

        return response()->json([
            'success' => true,
            'message' => 'CV berhasil disesuaikan secara presisi dengan target lowongan!',
            'data' => $result,
        ]);
    }

    /**
     * Generate LinkedIn Personal Branding Optimization Suite.
     */
    public function generateLinkedIn(Request $request): JsonResponse
    {
        $resumeContent = $request->input('resume_content', []);
        $lang = $request->input('lang', 'id');
        $humanize = $request->boolean('humanize', false);

        $result = CvAiService::generateLinkedInContent($resumeContent, $lang, $humanize);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Quick AI Helper for Summary, Bullet Enhance/Condense, Brainstorm, and Skills.
     */
    public function aiHelper(Request $request): JsonResponse
    {
        $action = $request->input('action', 'summary');
        $lang = $request->input('lang', 'id');
        $humanize = $request->boolean('humanize', false);
        $content = $request->input('content', []);

        switch ($action) {
            case 'summary':
                $role = $request->input('role', $content['personal_info']['title'] ?? 'Profesional');
                $result = CvAiService::generateAiSummary($content, $role, $lang, $humanize);
                return response()->json(['success' => true, 'data' => ['summary' => $result]]);

            case 'enhance_bullet':
                $bullet = $request->input('bullet', '');
                $role = $request->input('role', 'Engineer');
                $result = CvAiService::enhanceBulletPoint($bullet, $role, $lang, $humanize);
                return response()->json(['success' => true, 'data' => ['bullet' => $result]]);

            case 'condense_bullet':
                $bullet = $request->input('bullet', '');
                $result = CvAiService::condenseBulletPoint($bullet, $lang);
                return response()->json(['success' => true, 'data' => ['bullet' => $result]]);

            case 'brainstorm':
                $role = $request->input('role', 'Systems Architect');
                $industry = $request->input('industry', 'Technology');
                $result = CvAiService::brainstormAchievements($role, $industry, $lang);
                return response()->json(['success' => true, 'data' => ['achievements' => $result]]);

            case 'skills':
                $role = $request->input('role', 'Full Stack Engineer');
                $result = CvAiService::suggestSkills($role, $lang);
                return response()->json(['success' => true, 'data' => ['skills' => $result]]);

            default:
                return response()->json(['success' => false, 'message' => 'Action tidak dikenali.'], 400);
        }
    }

    /**
     * Real-time STAR Voice Copilot for live interview guidance.
     */
    public function realtimeCopilot(Request $request): JsonResponse
    {
        $spokenText = $request->input('spoken_text', '');
        $resumeContent = $request->input('resume_content', []);
        $lang = $request->input('lang', 'id');

        $result = CvAiService::generateRealtimeCheatSheet($spokenText, $resumeContent, $lang);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * AI Web Portfolio Generator.
     */
    public function generatePortfolio(Request $request): JsonResponse
    {
        $resumeContent = $request->input('resume_content', []);
        $theme = $request->input('theme', 'dark');
        $lang = $request->input('lang', 'id');

        $result = CvAiService::generateWebPortfolio($resumeContent, $theme, $lang);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }


    /**
     * Get dynamic pricing tiers, a la carte top-ups, and financial margins.
     */
    public function pricing(Request $request): JsonResponse
    {
        $economicsData = \App\Services\CvPro\CvPricingService::getAllPlansWithEconomics();

        $user = $request->user();
        $userQuota = null;
        if ($user) {
            $userQuota = \App\Services\CvPro\CvQuotaService::getUserQuota($user);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'plans' => $economicsData['plans'],
                'exchange_rate' => $economicsData['exchange_rate_usd_idr'],
                'unit_costs' => $economicsData['unit_costs'],
                'current_user_quota' => $userQuota,
            ],
        ]);
    }

    /**
     * Get current user quota status.
     */
    public function quota(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => true,
                'is_guest' => true,
                'quota' => [
                    'tier_code' => 'free',
                    'tailor_cv_remaining' => 1,
                    'mock_interviews_remaining' => 1,
                    'ats_audits_remaining' => 3,
                    'linkedin_packs_remaining' => 0,
                    'outreach_letters_remaining' => 1,
                    'ai_credits_balance' => 10,
                ],
            ]);
        }

        $quota = \App\Services\CvPro\CvQuotaService::getUserQuota($user);

        return response()->json([
            'success' => true,
            'is_guest' => false,
            'quota' => [
                'tier_code' => $quota->tier_code,
                'plan_id' => $quota->plan_id,
                'tailor_cv_remaining' => $quota->remaining('tailor_cv'),
                'mock_interviews_remaining' => $quota->remaining('mock_interviews'),
                'ats_audits_remaining' => $quota->remaining('ats_audits'),
                'linkedin_packs_remaining' => $quota->remaining('linkedin_packs'),
                'outreach_letters_remaining' => $quota->remaining('outreach_letters'),
                'ai_credits_balance' => $quota->ai_credits_balance,
                'plan_expires_at' => $quota->plan_expires_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * Top-up or activate a plan (Simulated or via Midtrans settlement).
     */
    public function topup(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'plan_code' => 'required|string|exists:cv_pro_plans,code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Autentikasi diperlukan untuk aktivasi paket atau top-up kuota.',
            ], 401);
        }

        $plan = \App\Models\CvProPlan::where('code', $request->input('plan_code'))->firstOrFail();

        if ($plan->type === 'subscription') {
            $updatedQuota = \App\Services\CvPro\CvQuotaService::applyPlan($user, $plan);
        } else {
            $updatedQuota = \App\Services\CvPro\CvQuotaService::applyTopUp($user, $plan);
        }

        return response()->json([
            'success' => true,
            'message' => "Paket '{$plan->code}' berhasil diterapkan ke akun Anda.",
            'quota' => $updatedQuota,
        ]);
    }
}


