<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\PosAffiliateReferral;
use App\Models\PosImpactGrantApplication;
use App\Models\PosPrivacyErasureRequest;
use App\Models\PosSlaMaintenanceTicket;
use App\Services\Saas\PosSaasCommercialEngineService;
use App\Services\SeoGeo\GeoAuthorityGeneratorService;
use App\Services\Sla\PosSlaAndSandboxEngineService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Schema;

class SaasSystemHealthPulsePage extends Page
{
    protected static ?string $slug = 'saas-system-health-pulse';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static string|\UnitEnum|null $navigationGroup = 'System & Security';

    protected static ?string $navigationLabel = 'SaaS Health Pulse & Impact Grants';

    protected static ?string $title = 'Super Admin SaaS System Health Pulse, Affiliates & Impact Grants';

    protected static ?int $navigationSort = 18;

    protected string $view = 'filament.pages.saas-system-health-pulse-page';

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        if ($user->hasAnyRole(['midtrans_reviewer', 'client_retail', 'client_partner', 'client'])) {
            return false;
        }

        return $user->can('view_saas_system_health_pulse')
            || $user->can('approve_nonprofit_impact_grants')
            || $user->hasAnyRole(['super_admin', 'developer']);
    }

    public function getHeading(): string|Htmlable
    {
        return app()->getLocale() === 'en'
            ? 'SaaS System Health Pulse, SEO/GEO IndexNow, SLA Warranty & Impact Grants (v11.0)'
            : 'Kesehatan Sistem SaaS, Ping SEO/GEO IndexNow, Garansi SLA Pasca-BAST & Hibah Non-Profit 1-Klik (v11.0)';
    }

    public function triggerIndexNowPing(): void
    {
        $user = auth()->user();
        if (! $user || (! $user->can('manage_seo_geo_authority') && ! $user->hasAnyRole(['super_admin', 'developer']))) {
            Notification::make()->title('Akses ditolak (RBAC manage_seo_geo_authority).')->danger()->send();

            return;
        }

        GeoAuthorityGeneratorService::flushAllSeoGeoCaches();
        $ping = GeoAuthorityGeneratorService::pingIndexNow([
            url('/explore'),
            url('/llms.txt'),
            url('/llms-full.txt'),
        ]);

        Notification::make()
            ->title('SEO/GEO Cache Direset & IndexNow Ping Terkirim (HTTP '.($ping['http_status'] ?? 200).')')
            ->success()
            ->send();
    }

    public function runAdminSandboxWebhook(string $scenario): void
    {
        if (app()->isProduction()) {
            Notification::make()->title('Sandbox Webhook Simulator dikunci di server production (HTTP 403).')->danger()->send();

            return;
        }

        $user = auth()->user();
        if (! $user || (! $user->can('run_sandbox_webhook_simulator') && ! $user->hasAnyRole(['super_admin', 'developer']))) {
            Notification::make()->title('Akses ditolak (RBAC run_sandbox_webhook_simulator).')->danger()->send();

            return;
        }

        $res = app(PosSlaAndSandboxEngineService::class)->simulateSandboxWebhookEvent(
            $scenario,
            ['plan_tier' => 'RETAIL_PRO', 'amount_idr' => 399000],
            $user
        );

        Notification::make()
            ->title('Sandbox Webhook ['.$scenario.'] Selesai — Order: '.($res['order_id'] ?? '-'))
            ->success()
            ->send();
    }

    public function approveImpactGrant(string $applicationId, PosSaasCommercialEngineService $engine): void
    {
        $user = auth()->user();
        if (! $user || (! $user->can('approve_nonprofit_impact_grants') && ! $user->hasRole('super_admin'))) {
            Notification::make()->title('Akses ditolak (RBAC approve_nonprofit_impact_grants).')->danger()->send();

            return;
        }

        $appRow = PosImpactGrantApplication::query()->find($applicationId);
        if (! $appRow) {
            Notification::make()->title('Permohonan hibah tidak ditemukan.')->warning()->send();

            return;
        }

        $result = $engine->approveImpactGrantOneClick($appRow, $user, 'Approved 1-Click via SaasSystemHealthPulsePage');
        Notification::make()
            ->title('Hibah Impact Grant Disetujui! Voucher: '.($result['voucher_code'] ?? '-'))
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        $engine = app(PosSaasCommercialEngineService::class);
        $pulse = $engine->buildSystemHealthPulse();

        $pendingGrants = Schema::hasTable('pos_impact_grant_applications')
            ? PosImpactGrantApplication::query()->orderByDesc('id')->limit(20)->get()
            : collect();

        $recentReferrals = Schema::hasTable('pos_affiliate_referrals')
            ? PosAffiliateReferral::query()->orderByDesc('id')->limit(20)->get()
            : collect();

        $recentSlaTickets = Schema::hasTable('pos_sla_maintenance_tickets')
            ? PosSlaMaintenanceTicket::query()->orderByDesc('id')->limit(15)->get()
            : collect();

        $recentPrivacyRequests = Schema::hasTable('pos_privacy_erasure_requests')
            ? PosPrivacyErasureRequest::query()->orderByDesc('id')->limit(10)->get()
            : collect();

        return [
            'pulse' => $pulse,
            'pendingGrants' => $pendingGrants,
            'recentReferrals' => $recentReferrals,
            'recentSlaTickets' => $recentSlaTickets,
            'recentPrivacyRequests' => $recentPrivacyRequests,
            'locale' => app()->getLocale() === 'en' ? 'en' : 'id',
        ];
    }
}
