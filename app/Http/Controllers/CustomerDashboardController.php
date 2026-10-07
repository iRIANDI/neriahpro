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
        if (! $user) {
            session(['url.intended' => route('customer.dashboard')]);
            return redirect()->route('customer.login');
        }

        $email = strtolower(trim($user->email ?? ''));
        $userId = $user->id;
        $globalSettings = CmsGlobalSetting::getAllCached();

        // Retrieve blueprints owned by or registered to this customer
        $blueprints = VisionBlueprint::where(function ($q) use ($email, $userId) {
            if ($email) {
                $q->whereRaw('LOWER(email) = ?', [$email]);
            }
            if ($userId) {
                $q->orWhere('user_metadata->user_id', $userId);
            }
        })->orderBy('created_at', 'desc')->get();

        // Retrieve payment transactions (resilient across PostgreSQL & MySQL)
        $transactions = Transaction::where(function ($q) use ($email, $userId) {
            if ($userId) {
                $q->where('user_id', $userId);
            }
            if ($email) {
                $q->orWhere(function ($sub) use ($email) {
                    $sub->whereNotNull('customer_details')
                        ->where('customer_details', 'LIKE', '%"' . $email . '"%');
                });
            }
        })->orderBy('created_at', 'desc')->get();

        // Retrieve linked domain & hosting assets if any
        $blueprintIds = $blueprints->pluck('id')->filter()->toArray();
        $hostingAssets = !empty($blueprintIds) 
            ? \App\Models\DomainHostingAsset::whereIn('vision_blueprint_id', $blueprintIds)->orderBy('expiration_date', 'asc')->get()
            : collect();

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
            'hostingAssets' => $hostingAssets,
            'globalSettings' => $globalSettings,
            'metrics' => [
                'total_blueprints' => $totalBlueprints,
                'total_retail' => $totalRetail,
                'total_studio' => $totalStudio,
                'active_studio' => $activeStudio,
                'total_spend_idr' => (float) $transactions->whereIn('status', ['settlement', 'capture', 'success'])->sum('total_idr'),
            ],
        ]);
    }
}
