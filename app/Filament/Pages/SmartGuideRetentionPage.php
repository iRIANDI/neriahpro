<?php

namespace App\Filament\Pages;

use App\Models\CmsGlobalSetting;
use App\Models\PosArchitecturePillar;
use App\Services\Ai\GeminiToolRegistry;
use App\Services\PosCommercialBridgeService;
use App\Services\Security\DocumentThreatSanitizerService;
use App\Support\PosAgentShieldAndCoDesignCatalog;
use App\Support\PosDayTwoProductionCatalog;
use App\Support\PosDualZoneFrontendAndSaasCatalog;
use App\Support\PosEnterpriseGuaranteeCatalog;
use App\Support\PosInclusiveCommercialCatalog;
use App\Support\PosLifecycleAndStudioCatalog;
use App\Support\PosRealWorldRuntimeCatalog;
use App\Support\PosSectorUiArchetypeCatalog;
use App\Support\PosSeoGeoAndZeroFailureCatalog;
use App\Support\PosStackPresetCatalog;
use App\Support\PricingRegistry;
use App\Support\ProjectOsMatrixCatalog;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class SmartGuideRetentionPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'Project OS // Core';

    protected static ?string $navigationLabel = 'Smart Guide & Unit Economics';

    protected static ?string $title = 'Executive Smart Guide // Unit Economics & Retention';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.smart-guide-retention';

    /**
     * Dynamic RBAC Gatekeeper for Executive Smart Guide:
     * - Allowed: Super Admin (Founder) and Calon Investor (for Due Diligence & Unit Economics audit).
     * - Allowed: Any actor with explicit 'view_executive_smart_guide' permission (e.g. Lead Developer).
     * - Strictly Denied: Midtrans QA Reviewer and Clients (Retail & Partner).
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        // Reviewer Midtrans dan Pelanggan dilarang keras mengakses rahasia margin & playbook unit economics
        if ($user->hasAnyRole(['midtrans_reviewer', 'client_retail', 'client_partner', 'client'])) {
            return false;
        }

        // Dinamis berbasis Role & Permission (Super Admin, Calon Investor, atau permission khusus)
        return $user->can('view_executive_smart_guide')
            || $user->hasAnyRole(['super_admin', 'investor']);
    }

    public function getHeading(): string|Htmlable
    {
        return 'Executive Smart Guide // Unit Economics, Margin & High-Retention Playbook';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return app()->getLocale() === 'en'
            ? 'Executive Business & Engineering Playbook (Project OS v11.0 Paripurna Edition): Satu Pintu SaaS Commercial Gate, 12-Sector SEO/GEO Authority (/llms.txt + Multi-Graph Schema.org), UU PDP Privacy Vault, Offline Outbox PWA, Post-BAST SLA Warranty, Holding Consolidation & 44 Native AI Tools.'
            : 'Dokumentasi Rahasia Bisnis & Operasional Neriah Pro (Project OS v11.0 Paripurna Edition): Gerbang Komersial Satu Pintu SaaS, Otoritas SEO/GEO 12 Sektor (/llms.txt + Multi-Graph Schema.org), Brankas Privasi UU PDP, Offline Outbox PWA, Garansi SLA Pasca-BAST, Konsolidasi Holding & 44 Native AI Tools.';
    }

    public function getViewData(): array
    {
        return [
            'pricingSettings' => [
                'spark_price' => CmsGlobalSetting::getVal('pricing_retail_spark_price', 'Rp 0'),
                'lite_price' => CmsGlobalSetting::getVal('pricing_retail_lite_price', 'Rp 99.000'),
                'pro_price' => CmsGlobalSetting::getVal('pricing_retail_pro_price', 'Rp 399.000'),
                'ultimate_price' => CmsGlobalSetting::getVal('pricing_retail_ultimate_price', 'Rp 1.490.000'),
                'spark_limit' => CmsGlobalSetting::getVal('pricing_retail_spark_limit', '2x Audit/Bulan (Auto-Reset tgl 1)'),
                'lite_limit' => CmsGlobalSetting::getVal('pricing_retail_lite_limit', '1 Proyek // 30 Hari Jendela Revisi Form'),
                'pro_limit' => CmsGlobalSetting::getVal('pricing_retail_pro_price_limit', CmsGlobalSetting::getVal('pricing_retail_pro_limit', '1 Proyek // 6 Bulan Unlimited AI Re-prompt')),
                'ultimate_limit' => CmsGlobalSetting::getVal('pricing_retail_ultimate_limit', '1 Proyek Enterprise // 1 Tahun Prioritas Update'),
                'login_policy' => CmsGlobalSetting::getVal('pricing_retail_login_policy', 'Spark Bebas Login // Paket Berbayar Wajib Login Akun'),
                'disclaimer' => CmsGlobalSetting::getVal('pricing_retail_disclaimer', '100% Self-Service Blueprint // Zero Neriah Pro Coding'),
            ],
            'sectorsCatalog' => ProjectOsMatrixCatalog::sectors(),
            'killerFeatures' => PosSectorUiArchetypeCatalog::killerFeaturesSummary(),
            'finalEightLayers' => PosDayTwoProductionCatalog::finalEightLayersSummary(),
            'tenPerfectionPillars' => PosDayTwoProductionCatalog::tenPerfectionPillars(),
            'fiveLifecycleFeatures' => PosLifecycleAndStudioCatalog::fiveLifecycleFeatures(),
            'slopDiffPairs' => PosLifecycleAndStudioCatalog::slopVsProjectOsDiffPairs(),
            'fiveCommercialFeatures' => PosCommercialBridgeService::fiveCommercialBridgeFeatures(),
            'fiveDeterministicGuarantees' => PosEnterpriseGuaranteeCatalog::fiveDeterministicGuarantees(),
            'fiveEnterpriseGluePrimitives' => PosEnterpriseGuaranteeCatalog::fiveEnterpriseGluePrimitives(),
            'fiveRealWorldElements' => PosRealWorldRuntimeCatalog::fiveRealWorldElementsOverview(),
            'fifteenMasterPillars' => PosArchitecturePillar::getCachedMatrix(),
            'v8RetentionAndSecurity' => PosInclusiveCommercialCatalog::retentionAndSecurityMatrix(),
            'v8InclusiveValueSkus' => PricingRegistry::getInclusiveValueMetricCatalog(),
            'v8VisualUiTotalCount' => 36,
            'v9AgentShieldContract' => DocumentThreatSanitizerService::fiveLayersBlueprintContract(
                'NeriahPro Project OS v9.0',
                'STACK_M1_LARAVEL_MONOLITH',
                'SECTOR_01_COMMERCE_OMNICHANNEL'
            ),
            'v9SectorAuditPresets' => PosAgentShieldAndCoDesignCatalog::twelveSectorAuditPresets(),
            'v10PublicUxPresets' => PosDualZoneFrontendAndSaasCatalog::sixPublicUxPresets('id'),
            'v10FiveIronLaws' => PosDualZoneFrontendAndSaasCatalog::fiveIronAnimationLaws('id'),
            'v10SixSaasModules' => PosDualZoneFrontendAndSaasCatalog::sixCriticalSaasModules('id'),
            'v11ZeroFailureModulesId' => PosSeoGeoAndZeroFailureCatalog::sixFinalZeroFailureModules('id'),
            'v11ZeroFailureModulesEn' => PosSeoGeoAndZeroFailureCatalog::sixFinalZeroFailureModules('en'),
            'v11TwentyPillars' => PosSeoGeoAndZeroFailureCatalog::twentyPillarsParipurnaMatrix(),
            'guidedJourneySteps' => PosCommercialBridgeService::guidedClientJourneySteps(),
            'satuPintuPackages' => PricingRegistry::getSatuPintuCatalog(),
            'estimatorConfig' => PricingRegistry::getCommercialEstimatorConfig(),
            'stackPresetsCount' => count(PosStackPresetCatalog::stackPresets()),
            'nativeToolsCount' => count(GeminiToolRegistry::getDeclarations()),
        ];
    }
}
