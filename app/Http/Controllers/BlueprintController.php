<?php

namespace App\Http\Controllers;

use App\Models\VisionBlueprint;
use App\Models\CmsGlobalSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlueprintController extends Controller
{
    /**
     * Show the public Project OS & Vision Blueprint Questionnaire form.
     */
    public function create(): View
    {
        $globalSettings = CmsGlobalSetting::all()->keyBy('key');
        $isBlueprintEnabled = (bool) ($globalSettings['feature_enable_vision_blueprint']->value ?? true);

        if (! $isBlueprintEnabled && ! auth()->user()?->isSuperAdmin()) {
            abort(404);
        }

        return view('blueprint.create', [
            'globalSettings' => $globalSettings,
        ]);
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

        // Ensure PRD content is populated or regenerate if requested or missing new evaluation schema
        if (empty($blueprint->prd_content) || !isset($blueprint->prd_content['architecture_evaluation']) || request()->has('regenerate')) {
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
        $prd = $blueprint->prd_content;
        
        $md = "# Ultimate PRD: " . ($blueprint->nama_bisnis ?: $blueprint->client_name) . "\n\n";
        
        $md .= "## 1. Executive Technical Discovery\n";
        $md .= "**Masalah Utama:** " . ($blueprint->masalah_utama ?? ($prd['executive_summary']['problem_statement'] ?? '-')) . "\n\n";
        $md .= "**Tujuan Utama:** " . ($blueprint->tujuan_utama ?? ($prd['executive_summary']['success_metrics'] ?? '-')) . "\n\n";
        
        $md .= "## 2. Fitur MVP (Fase 1)\n";
        foreach ($prd['features']['mvp_phase1'] ?? [] as $fitur) {
            $md .= "- **" . ($fitur['title'] ?? '') . "**: " . ($fitur['desc'] ?? '') . "\n";
        }
        $md .= "\n";
        
        return response($md, 200, [
            'Content-Type' => 'text/markdown',
            'Content-Disposition' => 'attachment; filename="PRD_' . $blueprint->slug . '.md"'
        ]);
    }
}
