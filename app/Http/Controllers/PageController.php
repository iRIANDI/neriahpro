<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CmsPage;
use App\Models\CmsGlobalSetting;

class PageController extends Controller
{
    public function show($slug = 'home')
    {
        $page = CmsPage::where('slug', $slug)->where('is_published', true)->firstOrFail();
        
        $globalSettings = CmsGlobalSetting::all()->keyBy('key');

        return view('page', compact('page', 'globalSettings'));
    }
}
