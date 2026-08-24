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
            'client_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
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

        // 4. Save to Database
        try {
            $vision = VisionBlueprint::create([
                'client_name' => $request->client_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'service_options' => $request->service_options ?? [],
                'project_status' => 'Prospecting',
                'ip_address' => $request->ip(),
                'user_metadata' => $userMetadata,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vision blueprint submitted successfully.',
                'data' => [
                    'id' => $vision->id,
                    'slug' => $vision->slug
                ]
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Vision Blueprint Submission Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving your submission. Please try again later.'
            ], 500);
        }
    }
}
