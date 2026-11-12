<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\SecurityException;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessSecureDataset;
use App\Models\AuditLog;
use App\Models\CvProPlan;
use App\Models\InterviewSession;
use App\Models\OutreachLetter;
use App\Models\Resume;
use App\Services\CvPro\CvAiService;
use App\Services\CvPro\CvPricingService;
use App\Services\CvPro\CvQuotaService;
use App\Services\MarkItDown\CvMarkdownParser;
use App\Services\MarkItDown\MarkItDownService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

            AuditLog::record(
                action: 'CV_PRO_RESUME_SAVED',
                model: $resume,
                newValues: [
                    'title' => $resume->title,
                    'target_role' => $resume->target_role,
                    'template' => $resume->template,
                    'ats_score' => $resume->ats_score,
                ],
                description: "Resume '{$resume->title}' ({$resume->target_role}) disimpan dengan skor ATS {$resume->ats_score}/100.",
                eventCategory: 'cv_pro_studio',
                sectorCode: 'SECTOR_07_HRTECH_EDUTECH',
                primitiveCode: 'PRIMITIVE_CV_CLOUD_SAVE'
            );

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
                'message' => 'Gagal menyimpan resume: '.$e->getMessage(),
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

            // 0. Sandboxed dataset & file security inspection
            ProcessSecureDataset::inspectAndSanitizeUploadedFile($file);

            // 1. Convert file to Markdown via Microsoft MarkItDown replica service
            $markItDownService = new MarkItDownService;
            $conversion = $markItDownService->convert($file);

            // 2. Parse Markdown into structured CV data
            $cvParser = new CvMarkdownParser;
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
        } catch (SecurityException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Pelanggaran keamanan berkas: Berkas ditolak oleh AI-Shield Ingestion Pipeline.',
                'code' => 'SECURE_INGESTION_REJECTED',
            ], 403);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses berkas CV: '.$e->getMessage(),
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
        $economicsData = CvPricingService::getAllPlansWithEconomics();

        $user = $request->user();
        $userQuota = null;
        if ($user) {
            $userQuota = CvQuotaService::getUserQuota($user);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'plans' => $economicsData['plans'],
                'exchange_rate' => $economicsData['exchange_rate_usd_idr'],
                'unit_costs' => $economicsData['unit_costs'],
                'current_user_quota' => $userQuota,
                'is_superadmin_bypass' => (bool) ($user && $user->hasUniversalSuperadminBypass('CV_PRO')),
                'universal_bypass' => $user ? $user->getUniversalBypassEntitlementPayload('CV_PRO') : null,
            ],
        ]);
    }

    /**
     * Get current user quota status.
     */
    public function quota(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => true,
                'is_guest' => true,
                'is_superadmin_bypass' => false,
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

        $quota = CvQuotaService::getUserQuota($user);
        $isBypass = $user->hasUniversalSuperadminBypass('CV_PRO');

        return response()->json([
            'success' => true,
            'is_guest' => false,
            'is_superadmin_bypass' => $isBypass,
            'universal_bypass' => $user->getUniversalBypassEntitlementPayload('CV_PRO'),
            'quota' => [
                'tier_code' => $quota->tier_code,
                'plan_id' => $quota->plan_id,
                'tailor_cv_remaining' => $isBypass ? 999999 : $quota->remaining('tailor_cv'),
                'mock_interviews_remaining' => $isBypass ? 999999 : $quota->remaining('mock_interviews'),
                'ats_audits_remaining' => $isBypass ? 999999 : $quota->remaining('ats_audits'),
                'linkedin_packs_remaining' => $isBypass ? 999999 : $quota->remaining('linkedin_packs'),
                'outreach_letters_remaining' => $isBypass ? 999999 : $quota->remaining('outreach_letters'),
                'ai_credits_balance' => $isBypass ? max(999999, (int) $quota->ai_credits_balance) : $quota->ai_credits_balance,
                'plan_expires_at' => $quota->plan_expires_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * Top-up or activate a plan (Simulated, via Midtrans settlement, or Free Superadmin Bypass).
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
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Autentikasi diperlukan untuk aktivasi paket atau top-up kuota.',
            ], 401);
        }

        $plan = CvProPlan::where('code', $request->input('plan_code'))->firstOrFail();

        if ($user->hasUniversalSuperadminBypass('CV_PRO')) {
            $updatedQuota = CvQuotaService::activateSuperadminVipBypass($user, $request->ip());

            return response()->json([
                'success' => true,
                'is_superadmin_bypass' => true,
                'billing_amount_idr' => 0,
                'message' => "VIP Superadmin Bypass Aktif (Rp 0): Paket '{$plan->code}' & Unlimited 8-Pillar Studio dibuka tanpa biaya.",
                'quota' => $updatedQuota,
            ]);
        }

        if ($plan->type === 'subscription') {
            $updatedQuota = CvQuotaService::applyPlan($user, $plan);
        } else {
            $updatedQuota = CvQuotaService::applyTopUp($user, $plan);
        }

        AuditLog::record(
            action: 'CV_PRO_PLAN_ACTIVATED',
            model: $updatedQuota,
            newValues: [
                'plan_code' => $plan->code,
                'plan_type' => $plan->type,
                'tier_code' => $updatedQuota->tier_code,
            ],
            description: "Pengguna ({$user->email}) mengaktifkan paket CV Pro '{$plan->code}'.",
            eventCategory: 'cv_pro_studio',
            sectorCode: 'SECTOR_07_HRTECH_EDUTECH',
            primitiveCode: 'PRIMITIVE_CV_QUOTA_BILLING'
        );

        return response()->json([
            'success' => true,
            'is_superadmin_bypass' => false,
            'message' => "Paket '{$plan->code}' berhasil diterapkan ke akun Anda.",
            'quota' => $updatedQuota,
        ]);
    }

    /**
     * 1-Click VIP Superadmin Free Bypass for CV Pro Ultimate Studio (Rp 0 Unlimited).
     */
    public function vipSuperadminBypass(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user || ! $user->hasUniversalSuperadminBypass('CV_PRO')) {
            return response()->json([
                'success' => false,
                'message' => 'Otorisasi ditolak: Hanya Superadmin atau pemegang izin bypass_all_payment_gates yang dapat mengeksekusi VIP Zero-Cost Bypass.',
            ], 403);
        }

        $quota = CvQuotaService::activateSuperadminVipBypass($user, $request->ip());

        return response()->json([
            'success' => true,
            'is_superadmin_bypass' => true,
            'billing_amount_idr' => 0,
            'message' => 'VIP Superadmin Bypass (Rp 0 Unlimited) berhasil diaktifkan untuk seluruh 8 Pilar CV Pro Ultimate Studio!',
            'universal_bypass' => $user->getUniversalBypassEntitlementPayload('CV_PRO'),
            'quota' => [
                'tier_code' => $quota->tier_code,
                'tailor_cv_remaining' => 999999,
                'mock_interviews_remaining' => 999999,
                'ats_audits_remaining' => 999999,
                'linkedin_packs_remaining' => 999999,
                'outreach_letters_remaining' => 999999,
                'ai_credits_balance' => $quota->ai_credits_balance,
            ],
        ]);
    }

    /**
     * Generate branded Social Media Promo Card content and announcement captions.
     */
    public function generateSosmed(Request $request): JsonResponse
    {
        $content = $request->input('content', []);
        $topic = $request->input('topic', 'OpenToWork');
        $customPrompt = $request->input('custom_prompt', '');
        $lang = $request->input('lang', 'id');

        $result = CvAiService::generateSosmedPromo($content, $topic, $customPrompt, $lang);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Analyze an interview or recruiter call transcript and provide structured STAR coaching.
     */
    public function analyzeTranscript(Request $request): JsonResponse
    {
        $transcript = $request->input('transcript', '');
        $content = $request->input('content', []);
        $lang = $request->input('lang', 'id');

        if (empty(trim($transcript))) {
            return response()->json([
                'success' => false,
                'message' => $lang === 'id' ? 'Teks transkrip tidak boleh kosong.' : 'Transcript text cannot be empty.',
            ], 422);
        }

        $result = CvAiService::analyzeTranscript($transcript, $content, $lang);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * UU PDP No. 27/2022 Compliance: Right to Erasure / Purge User CV & PII Data.
     */
    public function purgeData(Request $request): JsonResponse
    {
        $lang = $request->input('lang', 'id');
        $resumeId = $request->input('resume_id');
        $user = auth()->user();

        $purgedCount = 0;
        if (! empty($resumeId)) {
            $resume = Resume::find($resumeId);
            if ($resume) {
                $resume->delete();
                $purgedCount++;
            }
        } elseif ($user) {
            $purgedCount = Resume::where('user_id', $user->id)->delete();
        }

        try {
            AuditLog::create([
                'user_id' => $user?->id,
                'actor_name' => $user?->name ?? 'Guest User',
                'actor_type' => $user ? 'user' : 'guest',
                'execution_channel' => 'web_api',
                'action' => 'CV_USER_DATA_PURGED_UU_PDP',
                'event_category' => 'privacy_compliance',
                'sector_code' => 'SECTOR_07_HRTECH_EDUTECH',
                'primitive_code' => 'PRIMITIVE_RIGHT_TO_ERASURE',
                'description' => "Pengguna mengeksekusi Hak Penghapusan Data Pribadi (Right to Erasure UU PDP No. 27/2022). Total resume dihapus: {$purgedCount}.",
                'model_type' => Resume::class,
                'model_id' => $resumeId ?: ($user?->id ? (string) $user->id : null),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'context_url' => $request->fullUrl(),
            ]);
        } catch (\Throwable $e) {
            // Log silently
        }

        return response()->json([
            'success' => true,
            'purged_count' => $purgedCount,
            'message' => $lang === 'id'
                ? 'Seluruh data riwayat karir dan informasi pribadi CV berhasil dimusnahkan secara permanen (Kepatuhan UU PDP No. 27/2022).'
                : 'All CV biographical data and personal identifiers have been permanently purged (UU PDP No. 27/2022 compliance).',
        ]);
    }

    /**
     * Check CV Pro 4-Pillar Compliance Governance status.
     */
    public function complianceStatus(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'governance_certified' => true,
            'pillars' => [
                'uu_pdp_compliance' => [
                    'code' => 'UU_PDP_27_2022',
                    'status' => 'ACTIVE',
                    'title' => [
                        'id' => 'Pelindungan Data Pribadi & Anti-Broker Data (UU PDP No. 27/2022)',
                        'en' => 'Personal Data Protection & Zero Data Brokering (Indonesian PDP Law 27/2022)',
                    ],
                    'description' => [
                        'id' => 'Data riwayat hidup diproses semata-mata untuk memformat dokumen Pengguna. Hak Penghapusan Data (Right to Erasure) aktif 100%.',
                        'en' => 'Biographical data is processed solely for formatting user resumes. Right to Erasure is 100% operational.',
                    ],
                    'right_to_erasure_endpoint' => '/api/cv-pro/purge-data',
                ],
                'content_disclaimer' => [
                    'code' => 'DISCLAIMER_OF_WARRANTY',
                    'status' => 'ACTIVE',
                    'title' => [
                        'id' => 'Bebas Tanggung Jawab atas Kebenaran Isi CV (Disclaimer of Warranty)',
                        'en' => 'User Credential Responsibility (Disclaimer of Warranty)',
                    ],
                    'description' => [
                        'id' => 'Neriah Pro menyediakan sarana teknologi tata letak. Kebenaran ijazah, riwayat karir, dan sertifikat 100% tanggung jawab pengguna.',
                        'en' => 'Neriah Pro provides layout technology. All claims, degrees, and career records are 100% user responsibility.',
                    ],
                ],
                'royalty_free_licensing' => [
                    'code' => 'SIL_OFL_FONTS',
                    'status' => 'ACTIVE',
                    'title' => [
                        'id' => 'Lisensi Tipografi & Aset Visual Bebas Royalti (SIL Open Font License)',
                        'en' => 'Royalty-Free Typography & Visual Asset Licensing (SIL OFL)',
                    ],
                    'verified_fonts' => ['Inter', 'Roboto', 'Merriweather'],
                    'verified_icons' => ['Lucide', 'FontAwesome Open Source'],
                ],
                'digital_goods_policy' => [
                    'code' => 'CONSUMED_DIGITAL_GOODS_NO_REFUND',
                    'status' => 'ACTIVE',
                    'title' => [
                        'id' => 'Kebijakan Produk Digital & Token AI Terpakai (Non-Refundable)',
                        'en' => 'Consumed Digital Goods & AI Tokens Policy (Non-Refundable)',
                    ],
                    'description' => [
                        'id' => 'Pembelian kuota unduh dan token AI yang telah terpakai bersifat final, kecuali terbukti terjadi penagihan ganda (double-charge).',
                        'en' => 'Consumed download credits and AI tokens are final, except in cases of verified double-billing errors.',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Deterministic Hybrid ATS Scorer (Zero-LLM Algorithm).
     */
    public function deterministicScore(Request $request): JsonResponse
    {
        $content = $request->input('content', []);
        $jobDesc = $request->input('job_description', '');
        $lang = $request->input('lang', 'id');

        $result = CvAiService::calculateDeterministicAtsScore($content, $jobDesc, $lang);

        AuditLog::record(
            action: 'CV_PRO_DETERMINISTIC_ATS_SCORED',
            newValues: [
                'overall_score' => $result['overall_score'] ?? 0,
                'Breakdown' => $result['breakdown'] ?? [],
            ],
            description: 'Eksekusi Deterministic Hybrid ATS Scorer (Formula 0.30*K + 0.25*I + 0.25*S + 0.20*R). Skor: '.($result['overall_score'] ?? 0).'/100.',
            eventCategory: 'cv_pro_studio',
            sectorCode: 'SECTOR_07_HRTECH_EDUTECH',
            primitiveCode: 'PRIMITIVE_CV_DETERMINISTIC_ATS'
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * ATS Parser Emulator (Workday & Greenhouse linear extraction simulator).
     */
    public function simulateAts(Request $request): JsonResponse
    {
        $content = $request->input('content', []);
        $lang = $request->input('lang', 'id');

        $result = CvAiService::simulateAtsParser($content, $lang);

        AuditLog::record(
            action: 'CV_PRO_ATS_PARSER_EMULATED',
            newValues: [
                'parsing_confidence' => $result['parsing_confidence'] ?? 0,
                'engine_compatibility' => $result['engine_compatibility'] ?? [],
            ],
            description: 'Simulasi ekstraksi linear ATS Parser Emulator (Workday, Greenhouse, Lever, Taleo).',
            eventCategory: 'cv_pro_studio',
            sectorCode: 'SECTOR_07_HRTECH_EDUTECH',
            primitiveCode: 'PRIMITIVE_CV_ATS_EMULATOR'
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Google "XYZ" Formula Rewriter.
     */
    public function googleXyz(Request $request): JsonResponse
    {
        $bullet = $request->input('bullet', '');
        $role = $request->input('role', '');
        $lang = $request->input('lang', 'id');

        $result = CvAiService::rewriteGoogleXyz($bullet, $role, $lang);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Company OSINT Tailoring & Bespoke Micro-Pitch.
     */
    public function companyOsint(Request $request): JsonResponse
    {
        $company = $request->input('company', '');
        $role = $request->input('role', '');
        $jobDesc = $request->input('job_description', '');
        $content = $request->input('content', []);
        $lang = $request->input('lang', 'id');

        $result = CvAiService::runCompanyOsint($company, $role, $jobDesc, $content, $lang);

        AuditLog::record(
            action: 'CV_PRO_COMPANY_OSINT_EXECUTED',
            newValues: [
                'company' => $company,
                'role' => $role,
                'detected_tech_stack' => $result['detected_tech_stack'] ?? [],
            ],
            description: "Eksekusi Company OSINT Tailoring & Micro-Pitch untuk target perusahaan '{$company}' ({$role}).",
            eventCategory: 'cv_pro_studio',
            sectorCode: 'SECTOR_07_HRTECH_EDUTECH',
            primitiveCode: 'PRIMITIVE_CV_OSINT_TAILOR'
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Typst Source Code Generator (Anti-Orphan & Deterministic Page-Break).
     */
    public function exportTypst(Request $request): JsonResponse
    {
        $content = $request->input('content', []);
        $config = $request->input('config', []);

        $result = CvAiService::generateTypstSource($content, $config);

        AuditLog::record(
            action: 'CV_PRO_TYPST_SOURCE_EXPORTED',
            newValues: [
                'engine' => $result['engine'] ?? 'Typst v0.12 Deterministic Vector Compiler',
                'blind_hiring_mode' => (bool) ($config['blind_hiring'] ?? false),
            ],
            description: 'Ekspor kode sumber vektor deterministik Typst (.typ) dengan aturan Anti-Orphan #block(breakable: false).',
            eventCategory: 'cv_pro_studio',
            sectorCode: 'SECTOR_07_HRTECH_EDUTECH',
            primitiveCode: 'PRIMITIVE_CV_TYPST_COMPILER'
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * AI Recruiter Chatbot with Interactive Co-Browsing (RAG).
     */
    public function recruiterChat(Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        $content = $request->input('content', []);
        $lang = $request->input('lang', 'id');

        if (empty(trim($query))) {
            return response()->json([
                'success' => false,
                'message' => 'Query cannot be empty.',
            ], 422);
        }

        $result = CvAiService::askRecruiterChat($query, $content, $lang);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * CV-to-Case-Study Architect Agent (Zero-Hallucination Guardrail & Widget Mapper).
     */
    public function caseStudyConvert(Request $request): JsonResponse
    {
        $experience = $request->input('experience', []);
        $lang = $request->input('lang', 'id');

        $result = CvAiService::convertExperienceToCaseStudy($experience, $lang);

        AuditLog::record(
            action: 'CV_PRO_CASE_STUDY_CONVERTED',
            newValues: [
                'role' => $experience['role'] ?? 'Professional',
                'company' => $experience['company'] ?? '-',
                'recommended_widget' => $result['recommended_widget']['type'] ?? 'metric_counter_grid',
                'unverified_placeholders_count' => count($result['unverified_placeholders'] ?? []),
            ],
            description: 'Konversi Bullet CV menjadi Studi Kasus Portofolio dengan Zero-Hallucination Guardrail.',
            eventCategory: 'cv_pro_studio',
            sectorCode: 'SECTOR_07_HRTECH_EDUTECH',
            primitiveCode: 'PRIMITIVE_CV_CASE_STUDY_ARCHITECT'
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Get the complete catalog of 7 Functional Types, 10 Visual Styles, and 4 Launch MVPs.
     */
    public function portfolioCatalog(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => CvAiService::getPortfolioStyleCatalog(),
        ]);
    }

    /**
     * Global Format Localization (US/Global ATS, German Lebenslauf, Japanese Rirekisho).
     */
    public function localizeFormat(Request $request): JsonResponse
    {
        $content = $request->input('content', []);
        $countryFormat = $request->input('country_format', 'us_ats');
        $lang = $request->input('lang', 'id');

        $result = CvAiService::localizeGlobalFormat($content, $countryFormat, $lang);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
