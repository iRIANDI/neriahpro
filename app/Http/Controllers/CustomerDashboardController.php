<?php

namespace App\Http\Controllers;

use App\Models\CmsGlobalSetting;
use App\Models\Transaction;
use App\Models\VisionBlueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    /**
     * Display the centralized Customer & Client Project Dashboard.
     */
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        if (! Auth::check()) {
            session(['url.intended' => route('customer.dashboard')]);
            return redirect()->route('customer.login');
        }

        $user = Auth::user();
        $globalSettings = CmsGlobalSetting::getAllCached();

        // Retrieve blueprints owned by or registered to this customer
        $blueprints = VisionBlueprint::where('email', $user->email)
            ->orWhere('user_metadata->user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Retrieve payment transactions
        $transactions = Transaction::where('user_id', $user->id)
            ->orWhere('customer_details->email', $user->email)
            ->orderBy('created_at', 'desc')
            ->get();

        // Categorize into Retail Self-Service Licenses vs Studio Custom Projects
        $retailTiers = ['retail_spark', 'retail_lite', 'retail_pro', 'retail_ultimate'];

        $retailLicenses = $blueprints->filter(function ($bp) use ($retailTiers) {
            $tier = $bp->user_metadata['retail_tier'] ?? $bp->user_metadata['package_tier'] ?? null;
            return in_array($tier, $retailTiers, true)
                || in_array($bp->project_status, ['Retail License', 'Self-Service', 'Instant Blueprint'], true);
        });

        $studioProjects = $blueprints->reject(function ($bp) use ($retailTiers) {
            $tier = $bp->user_metadata['retail_tier'] ?? $bp->user_metadata['package_tier'] ?? null;
            return in_array($tier, $retailTiers, true)
                || in_array($bp->project_status, ['Retail License', 'Self-Service', 'Instant Blueprint'], true);
        });

        // Compute active metrics
        $totalBlueprints = $blueprints->count();
        $totalRetail = $retailLicenses->count();
        $totalStudio = $studioProjects->count();
        $activeStudio = $studioProjects->filter(fn ($p) => $p->isDpConfirmed() || $p->signed_agreement)->count();

        return view('customer.dashboard', [
            'user' => $user,
            'blueprints' => $blueprints,
            'retailLicenses' => $retailLicenses,
            'studioProjects' => $studioProjects,
            'transactions' => $transactions,
            'globalSettings' => $globalSettings,
            'metrics' => [
                'total_blueprints' => $totalBlueprints,
                'total_retail' => $totalRetail,
                'total_studio' => $totalStudio,
                'active_studio' => $activeStudio,
                'total_spend_idr' => $transactions->whereIn('status', ['settlement', 'capture', 'success'])->sum('total_idr'),
            ],
        ]);
    }
}
