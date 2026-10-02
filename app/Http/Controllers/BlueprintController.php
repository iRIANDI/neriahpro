<?php

namespace App\Http\Controllers;

use App\Models\VisionBlueprint;
use App\Models\CmsGlobalSetting;
use App\Services\MidtransSnapService;
use App\Services\BlueprintDiscoveryService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BlueprintController extends Controller
{
    /**
     * Show the public Project OS & Vision Blueprint Questionnaire form.
     */
    public function create(Request $request): View
    {
        $globalSettings = CmsGlobalSetting::getAllCached();
        $isBlueprintEnabled = (bool) ($globalSettings['feature_enable_vision_blueprint']->value ?? true);

        if (! $isBlueprintEnabled && ! auth()->user()?->isSuperAdmin()) {
            abort(404);
        }

        $draftId = $request->query('draft_id');
        $initialData = [];

        if ($draftId && Cache::has('blueprint_draft_' . $draftId)) {
            $initialData = Cache::get('blueprint_draft_' . $draftId, []);
        } elseif (session()->has('blueprint_draft')) {
            $initialData = session('blueprint_draft', []);
        }

        return view('blueprint.create', [
            'globalSettings' => $globalSettings,
            'initialData' => $initialData,
        ]);
    }

    /**
     * Analyze user's raw idea text and uploaded documents/images via MarkItDown & AI Engine.
     */
    public function analyzeIdea(Request $request, BlueprintDiscoveryService $discoveryService): JsonResponse
    {
        // 1. Honeypot Anti-Spam Check
        if ($request->filled('_hp_check') || $request->filled('_website')) {
            return response()->json([
                'success' => true,
                'message' => 'Idea processed successfully.',
                'redirect_url' => route('blueprint.create'),
                'data' => []
            ], 200);
        }

        // 2. Validate inputs
        $request->validate([
            'idea_text' => 'nullable|string|max:30000',
            'files.*' => 'nullable|file|max:15360|mimes:pdf,doc,docx,txt,md,rtf,csv,tsv,xlsx,pptx,png,jpg,jpeg,webp',
            'locale' => 'nullable|string|in:id,en'
        ]);

        $rawIdeaText = $request->input('idea_text', '') ?: '';
        $files = $request->file('files', []) ?: [];
        if (!is_array($files)) {
            $files = [$files];
        }
        $locale = $request->input('locale', app()->getLocale() ?: 'id');

        try {
            $synthesized = $discoveryService->synthesize($rawIdeaText, $files, $locale);

            $draftId = (string) Str::ulid();
            Cache::put('blueprint_draft_' . $draftId, $synthesized, now()->addHours(24));
            session(['blueprint_draft' => $synthesized]);

            return response()->json([
                'success' => true,
                'message' => $locale === 'en' 
                    ? 'Project blueprint synthesized successfully via MarkItDown & AI Engine.'
                    : 'Ide proyek berhasil dianalisis via MarkItDown & AI Engine.',
                'draft_id' => $draftId,
                'redirect_url' => route('blueprint.create', ['draft_id' => $draftId]),
                'data' => $synthesized
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses ide: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the generated Ultimate PRD & Blueprint for a specific project slug.
     */
    public function show(string $slug): View
    {
        $globalSettings = CmsGlobalSetting::all()->keyBy('key');
        $isBlueprintEnabled = (bool) ($globalSettings['feature_enable_vision_blueprint']->value ?? true);

        if (! $isBlueprintEnabled && ! auth()->user()?->isSuperAdmin()) {
            abort(404);
        }

        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        // Ensure PRD content is populated or regenerate if requested or missing new evaluation schema or engineering specs
        if (empty($blueprint->prd_content) || !isset($blueprint->prd_content['engineering_specs']) || request()->has('regenerate')) {
            $blueprint->generateAndSavePrd();
            $blueprint->refresh();
        }

        return view('blueprint.show', [
            'blueprint' => $blueprint,
            'prd' => $blueprint->prd_content,
            'globalSettings' => $globalSettings,
        ]);
    }

    /**
     * Generate contract and redirect to sign page
     */
    public function generateContract(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        
        $tier = request('tier', 'standard');
        $overrides = [];
        if ($tier === 'fast_track') {
            $overrides['contract_amount'] = 75000000.00;
            $overrides['dp_amount'] = 37500000.00;
        } elseif ($tier === 'hyper_sprint') {
            $overrides['contract_amount'] = 100000000.00;
            $overrides['dp_amount'] = 50000000.00;
        } else {
            $overrides['contract_amount'] = 50000000.00;
            $overrides['dp_amount'] = 25000000.00;
        }

        $document = $blueprint->documents()->where('document_type', 'contract')->first();
        if (!$document) {
            $document = $blueprint->convertToDigitalContract($overrides);
        } else {
            $document->update([
                'contract_amount' => $overrides['contract_amount'],
                'dp_amount' => $overrides['dp_amount'],
            ]);
        }

        return redirect()->route('document.sign', ['document' => $document->id]);
    }

    /**
     * Get Midtrans Snap Token for this blueprint proposal (Termin DP 50%).
     */
    public function getSnapToken(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        
        $tier = $request->input('tier', 'standard');
        
        // Calculate contract and DP amount based on tier
        $contractAmount = match ($tier) {
            'fast_track' => 75000000.00,
            'hyper_sprint' => 100000000.00,
            default => 50000000.00,
        };

        // DP is 50%
        $dpAmount = (int) ($contractAmount * 0.50);
        
        $orderId = 'NP-BP-' . strtoupper(substr($blueprint->id, 0, 8)) . '-' . time();
        $tierLabel = match ($tier) {
            'fast_track' => 'Fast-Track Velocity',
            'hyper_sprint' => 'Hyper-Sprint Delivery',
            default => 'Standard Velocity',
        };

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $dpAmount,
            ],
            'customer_details' => [
                'first_name' => $blueprint->client_name ?: ($blueprint->nama_bisnis ?: 'Client'),
                'email' => $blueprint->email ?: 'client@neriahpro.com',
                'phone' => $blueprint->phone ?: '08123456789',
            ],
            'item_details' => [
                [
                    'id' => 'DP-' . strtoupper($tier),
                    'price' => $dpAmount,
                    'quantity' => 1,
                    'name' => substr('DP (50%) - ' . ($blueprint->nama_bisnis ?: 'Proyek') . ' (' . $tierLabel . ')', 0, 50),
                ]
            ],
        ];

        $snapResponse = MidtransSnapService::createSnapToken($params);

        if (!$snapResponse['success']) {
            return response()->json([
                'success' => false,
                'message' => $snapResponse['error'] ?? 'Gagal membuat sesi transaksi Midtrans.',
            ], $snapResponse['status_code'] ?? 500);
        }

        return response()->json([
            'success' => true,
            'token' => $snapResponse['token'],
            'redirect_url' => $snapResponse['redirect_url'],
            'order_id' => $orderId,
            'gross_amount' => $dpAmount,
            'client_key' => $snapResponse['client_key'] ?? config('midtrans.client_key'),
        ]);
    }

    /**
     * Download PRD as PDF
     */
    public function downloadPdf(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $prd = $blueprint->prd_content;
        
        // Ensure DomPDF can render this (might need a simpler view, but we can reuse show for now or create a dedicated one later)
        // Here we just render a simple string if no specific PDF view exists, or use a basic template.
        // For production, create a resources/views/pdf/blueprint.blade.php
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('blueprint.show', [
            'blueprint' => $blueprint,
            'prd' => $prd,
        ]);
        
        return $pdf->download('PRD_' . $blueprint->slug . '.pdf');
    }

    /**
     * Download PRD as Markdown
     */
    public function downloadMd(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $prd = $blueprint->prd_content ?? [];
        
        $md = \App\Services\PrdGeneratorService::toMarkdown($blueprint, $prd);
        
        return response($md, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="PRD_' . $blueprint->slug . '.md"'
        ]);
    }

    /**
     * Get Raw PRD Markdown for AI Code Agent clipboard copy
     */
    public function rawMd(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $prd = $blueprint->prd_content ?? [];
        
        $md = \App\Services\PrdGeneratorService::toMarkdown($blueprint, $prd);
        
        return response($md, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
