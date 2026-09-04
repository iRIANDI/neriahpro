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

        return view('blueprint.create', [
            'globalSettings' => $globalSettings,
        ]);
    }

    /**
     * Show the generated Ultimate PRD & Blueprint for a specific project slug.
     */
    public function show(string $slug): View
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        // Ensure PRD content is populated
        if (empty($blueprint->prd_content)) {
            $blueprint->generateAndSavePrd();
            $blueprint->refresh();
        }

        $globalSettings = CmsGlobalSetting::all()->keyBy('key');

        return view('blueprint.show', [
            'blueprint' => $blueprint,
            'prd' => $blueprint->prd_content,
            'globalSettings' => $globalSettings,
        ]);
    }
}
