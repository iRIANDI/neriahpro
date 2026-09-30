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
}
