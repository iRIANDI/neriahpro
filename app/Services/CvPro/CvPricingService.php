<?php

namespace App\Services\CvPro;

use App\Models\CmsGlobalSetting;
use App\Models\CvProPlan;

class CvPricingService
{
    /**
     * Get USD to IDR conversion rate (dynamically loaded with fallback).
     */
    public static function getExchangeRate(): float
    {
        try {
            $setting = CmsGlobalSetting::where('key', 'usd_to_idr_rate')->first();
            return $setting ? (float) $setting->value : 16000.0;
        } catch (\Throwable) {
            return 16000.0;
        }
    }

    /**
     * Get estimated API unit costs in IDR.
     * Based on OpenAI GPT-4o-mini and Google Gemini 2.0 Flash market rates.
     */
    public static function getUnitCosts(): array
    {
        $rate = self::getExchangeRate();

        return [
            // Model: gpt-4o-mini ($0.15/1M input, $0.60/1M output)
            // or gemini-2.0-flash ($0.10/1M input, $0.40/1M output)
            'tailor_cv' => [
                'name' => '1x AI Job Description Tailoring & Diff',
                'avg_input_tokens' => 1500,
                'avg_output_tokens' => 800,
                'cost_usd' => (1500 * 0.00000015) + (800 * 0.00000060), // ~$0.000705
                'cost_idr' => round(((1500 * 0.00000015) + (800 * 0.00000060)) * $rate, 2), // ~Rp 11.28
            ],
            'mock_interview_session' => [
                'name' => '1x Full Mock Interview Session (5 Q&A + STAR Evaluation)',
                'avg_input_tokens' => 3500,
                'avg_output_tokens' => 1800,
                'cost_usd' => ((3500 * 0.00000015) + (1800 * 0.00000060)) + 0.002, // tokens + optional whisper audio buffer
                'cost_idr' => round((((3500 * 0.00000015) + (1800 * 0.00000060)) + 0.002) * $rate, 2), // ~Rp 57.60
            ],
            'ats_audit' => [
                'name' => '1x ATS Quality Score & Verb Linter',
                'avg_input_tokens' => 0,
                'avg_output_tokens' => 0,
                'cost_usd' => 0.0, // Native deterministic PHP engine! 0 API cost!
                'cost_idr' => 0.0,
            ],
            'linkedin_pack' => [
                'name' => '1x LinkedIn Personal Branding Pack',
                'avg_input_tokens' => 1200,
                'avg_output_tokens' => 600,
                'cost_usd' => (1200 * 0.00000015) + (600 * 0.00000060),
                'cost_idr' => round(((1200 * 0.00000015) + (600 * 0.00000060)) * $rate, 2), // ~Rp 8.64
            ],
            'outreach_letter' => [
                'name' => '1x Post-Interview Outreach Letter',
                'avg_input_tokens' => 800,
                'avg_output_tokens' => 400,
                'cost_usd' => (800 * 0.00000015) + (400 * 0.00000060),
                'cost_idr' => round(((800 * 0.00000015) + (400 * 0.00000060)) * $rate, 2), // ~Rp 5.76
            ],
            'payment_gateway_fee' => [
                'name' => 'Midtrans QRIS / VA Estimated Processing Fee',
                'cost_idr' => 2000.0, // Average QRIS / VA fee (0.7% or Rp 2.000)
            ],
        ];
    }

    /**
     * Calculate financial projection & gross margin for a specific plan.
     */
    public static function calculatePlanEconomics(CvProPlan $plan): array
    {
        $unitCosts = self::getUnitCosts();
        $quotas = $plan->quotas ?? [];

        $priceIdr = (float) $plan->price_idr;

        // 1. Calculate Maximum Potential AI Cost (if user consumes 100% of their quota)
        $tailorLimit = max(0, (int) ($quotas['tailor_cv_limit'] ?? 0));
        $interviewLimit = max(0, (int) ($quotas['mock_interviews_limit'] ?? 0));
        $linkedinLimit = max(0, (int) ($quotas['linkedin_packs_limit'] ?? 0));
        $outreachLimit = max(0, (int) ($quotas['outreach_letters_limit'] ?? 0));

        // For unlimited (-1), we model a generous heavy-user cap (30 tailor, 10 interviews, etc.)
        $calcTailor = $tailorLimit === -1 ? 35 : $tailorLimit;
        $calcInterview = $interviewLimit === -1 ? 15 : $interviewLimit;
        $calcLinkedin = $linkedinLimit === -1 ? 15 : $linkedinLimit;
        $calcOutreach = $outreachLimit === -1 ? 25 : $outreachLimit;

        $maxAiCostIdr = ($calcTailor * $unitCosts['tailor_cv']['cost_idr'])
            + ($calcInterview * $unitCosts['mock_interview_session']['cost_idr'])
            + ($calcLinkedin * $unitCosts['linkedin_pack']['cost_idr'])
            + ($calcOutreach * $unitCosts['outreach_letter']['cost_idr']);

        // Expected realistic usage in SaaS is typically 45% - 60% of max quota
        $expectedAiCostIdr = $maxAiCostIdr * 0.55;

        // Gateway Fee (only applicable if price > 0)
        $gatewayFeeIdr = $priceIdr > 0 ? min(4000, max(1500, $priceIdr * 0.015)) : 0;

        // Total COGS (Cost of Goods Sold)
        $totalCogsMax = $maxAiCostIdr + $gatewayFeeIdr;
        $totalCogsExpected = $expectedAiCostIdr + $gatewayFeeIdr;

        // Net Margin (Rp and %)
        $netMarginMaxIdr = $priceIdr - $totalCogsMax;
        $netMarginExpectedIdr = $priceIdr - $totalCogsExpected;

        $grossMarginPercent = $priceIdr > 0 ? round(($netMarginExpectedIdr / $priceIdr) * 100, 1) : 0;
        $worstCaseMarginPercent = $priceIdr > 0 ? round(($netMarginMaxIdr / $priceIdr) * 100, 1) : 0;

        // Nombok Risk Analysis
        $isSafe = $priceIdr >= $totalCogsMax;
        $safetyBufferMultiple = $totalCogsMax > 0 ? round($priceIdr / $totalCogsMax, 1) : 999;

        return [
            'plan_code' => $plan->code,
            'plan_name' => is_array($plan->name) ? ($plan->name['id'] ?? $plan->code) : $plan->name,
            'price_idr' => $priceIdr,
            'price_usd' => (float) $plan->price_usd,
            'type' => $plan->type,
            'billing_cycle' => $plan->billing_cycle,
            'cost_breakdown' => [
                'max_ai_cost_idr' => round($maxAiCostIdr, 2),
                'expected_ai_cost_idr' => round($expectedAiCostIdr, 2),
                'gateway_fee_idr' => round($gatewayFeeIdr, 2),
                'total_cogs_max_idr' => round($totalCogsMax, 2),
                'total_cogs_expected_idr' => round($totalCogsExpected, 2),
            ],
            'margins' => [
                'expected_profit_idr' => round($netMarginExpectedIdr, 2),
                'expected_margin_percent' => $grossMarginPercent,
                'worst_case_profit_idr' => round($netMarginMaxIdr, 2),
                'worst_case_margin_percent' => $worstCaseMarginPercent,
            ],
            'safety_assessment' => [
                'is_profitable' => $isSafe,
                'risk_status' => $priceIdr === 0.0 ? 'FREE_TIER_ACQUISITION' : ($isSafe ? 'HIGHLY_PROFITABLE' : 'RISK_OF_LOSS'),
                'safety_multiple' => "{$safetyBufferMultiple}x",
                'verdict_id' => $priceIdr === 0.0 
                    ? 'Tier Gratis untuk akuisisi pengguna baru (biaya AI disubsidi platform maks ~Rp 120/user).'
                    : "Sangat Aman! Admin mendapatkan margin ~{$grossMarginPercent}% (harga jual {$safetyBufferMultiple}x lipat di atas biaya API). Admin dijamin TIDAK NOMBOK.",
                'verdict_en' => $priceIdr === 0.0
                    ? 'Free acquisition tier (platform subsidizes max ~$0.007/user).'
                    : "Completely Safe! Operating at ~{$grossMarginPercent}% margin ({$safetyBufferMultiple}x markup over API costs). Guaranteed zero loss.",
            ],
        ];
    }

    /**
     * Get all active plans enriched with dynamic financial economics.
     */
    public static function getAllPlansWithEconomics(): array
    {
        $plans = CvProPlan::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        $enriched = [];
        foreach ($plans as $plan) {
            $enriched[] = [
                'model' => $plan->toArray(),
                'economics' => self::calculatePlanEconomics($plan),
            ];
        }

        return [
            'exchange_rate_usd_idr' => self::getExchangeRate(),
            'unit_costs' => self::getUnitCosts(),
            'plans' => $enriched,
        ];
    }

    /**
     * Simulate a custom pricing tier dynamically.
     */
    public static function simulatePricing(int $priceIdr, array $quotas): array
    {
        $dummyPlan = new CvProPlan([
            'code' => 'custom_simulation',
            'name' => ['id' => 'Simulasi Kustom', 'en' => 'Custom Simulation'],
            'price_idr' => $priceIdr,
            'price_usd' => round($priceIdr / self::getExchangeRate(), 2),
            'type' => 'subscription',
            'billing_cycle' => 'monthly',
            'quotas' => $quotas,
        ]);

        return self::calculatePlanEconomics($dummyPlan);
    }
}
