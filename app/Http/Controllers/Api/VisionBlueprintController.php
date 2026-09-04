<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisionBlueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class VisionBlueprintController extends Controller
{
    /**
     * Store a newly created Vision Blueprint from the public form.
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Honeypot Anti-Spam Check
        // Bots usually fill all fields, including hidden ones.
        if ($request->filled('_website')) {
            // Silently accept but do nothing (to fool the bot)
            return response()->json([
                'success' => true,
                'message' => 'Vision blueprint submitted successfully.',
            ], 200);
        }

        // 2. Validation
        $validator = Validator::make($request->all(), [
            'client_name' => 'nullable|string|max:255',
            'nama_bisnis' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'masalah_utama' => 'required|string',
            'tujuan_utama' => 'required|string',
            'target_audiens' => 'required|string',
            'aktor_sistem' => 'required|string',
            'fitur_wajib' => 'required|string',
            'fitur_tambahan' => 'nullable|string',
            'alur_kerja' => 'required|string',
            'kebutuhan_integrasi' => 'nullable|string',
            'referensi_desain' => 'nullable|string',
            'kesiapan_aset' => 'nullable|string',
            'target_waktu' => 'nullable|string',
            'service_options' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 3. Collect Metadata
        $userMetadata = [
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'accept_language' => $request->header('accept-language'),
            'submitted_at' => now()->toIso8601String(),
        ];

        // 4. Save to Database & Generate PRD
        try {
            $clientName = $request->filled('client_name') ? $request->client_name : $request->nama_bisnis;
            $email = $request->filled('email') ? $request->email : 'lead@' . \Illuminate\Support\Str::slug($request->nama_bisnis) . '.com';

            $vision = VisionBlueprint::create([
                'client_name' => $clientName,
                'nama_bisnis' => $request->nama_bisnis,
                'email' => $email,
                'phone' => $request->phone,
                'masalah_utama' => $request->masalah_utama,
                'tujuan_utama' => $request->tujuan_utama,
                'target_audiens' => $request->target_audiens,
                'aktor_sistem' => $request->aktor_sistem,
                'fitur_wajib' => $request->fitur_wajib,
                'fitur_tambahan' => $request->fitur_tambahan,
                'alur_kerja' => $request->alur_kerja,
                'kebutuhan_integrasi' => $request->kebutuhan_integrasi,
                'referensi_desain' => $request->referensi_desain,
                'kesiapan_aset' => $request->kesiapan_aset ?? 'Belum Siap Sama Sekali',
                'target_waktu' => $request->target_waktu,
                'service_options' => $request->service_options ?? ['Web Development', 'Custom Software Architecture'],
                'project_status' => 'Prospecting',
                'is_published' => true, // Auto-publish for immediate preview by creator
                'ip_address' => $request->ip(),
                'user_metadata' => $userMetadata,
            ]);

            // Synthesize and attach Ultimate PRD
            $vision->generateAndSavePrd();

            return response()->json([
                'success' => true,
                'message' => 'Vision blueprint & Ultimate PRD berhasil diproses!',
                'data' => [
                    'id' => $vision->id,
                    'slug' => $vision->slug,
                    'redirect_url' => url('/blueprint/' . $vision->slug),
                ]
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Vision Blueprint Submission Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menyimpan blueprint: ' . $e->getMessage()
            ], 500);
        }
    }
}
