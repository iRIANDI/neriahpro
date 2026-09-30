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

        // Ensure PRD content is populated
        if (empty($blueprint->prd_content)) {
            $blueprint->generateAndSavePrd();
            $blueprint->refresh();
        }

        return view('blueprint.show', [
            'blueprint' => $blueprint,
            'prd' => $blueprint->prd_content,
            'globalSettings' => $globalSettings,
        ]);
    }
}
