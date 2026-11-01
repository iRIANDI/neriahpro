<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\PosOrganizationMember;
use App\Models\PosOrganizationWorkspace;
use App\Models\PosProject;
use App\Models\PosVerifiedTestimonial;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\Saas\PosSaasCommercialEngineService;
use App\Services\Security\DocumentThreatSanitizerService;
use App\Support\PosDualZoneFrontendAndSaasCatalog;
use App\Support\ProjectOsMatrixCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * PublicExploreArchitectureController — Project OS v10.0
 *
 * Implements:
 * - Programmatic SEO Architecture Library (/explore, /explore/{sector}, /explore/{sector}/{slug}, /explore/og/{slug}.svg)
 * - Cryptographically Verified Testimonial Aniwall & Sticky Scrollytelling Showcase
 * - Dual-Gateway SaaS Billing Webhook, Feature Gate, Multi-Seat White-Label Workspace, & Impact Grant APIs
 */
class PublicExploreArchitectureController extends Controller
{
    /**
     * SSR Programmatic SEO Index & Sector Filter (/explore and /explore/{sector}).
     */
    public function index(Request $request, ?string $sector = null): View
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'id';
        $allSectors = ProjectOsMatrixCatalog::sectors();
        $sectorMap = [];
        foreach ($allSectors as $sec) {
            $sectorMap[$sec['code']] = $sec;
        }

        $activeSector = 'ALL';
        if ($sector !== null && $sector !== '') {
            $upper = strtoupper(trim($sector));
            if (isset($sectorMap[$upper])) {
                $activeSector = $upper;
            } else {
                foreach ($sectorMap as $code => $meta) {
                    if (Str::slug($code) === strtolower($sector) || str_contains(strtolower($code), strtolower(str_replace('-', '_', $sector)))) {
                        $activeSector = $code;
                        break;
                    }
                }
                if ($activeSector === 'ALL') {
                    abort(404);
                }
            }
        }

        $allTestimonials = PosDualZoneFrontendAndSaasCatalog::getVerifiedTestimonialsCatalog($locale);
        $filteredTestimonials = $activeSector === 'ALL'
            ? $allTestimonials
            : array_values(array_filter($allTestimonials, static fn (array $item): bool => ($item['sector_code'] ?? '') === $activeSector));

        $publicUxPresets = array_values(PosDualZoneFrontendAndSaasCatalog::sixPublicUxPresets($locale));
        $fiveIronLaws = PosDualZoneFrontendAndSaasCatalog::fiveIronAnimationLaws($locale);
        $sixSaasModules = PosDualZoneFrontendAndSaasCatalog::sixCriticalSaasModules($locale);

        $domainHost = parse_url((string) config('app.url', 'https://neriahpro.com'), PHP_URL_HOST) ?: 'neriahpro.com';
        $sectorTitle = $activeSector === 'ALL'
            ? ($locale === 'en' ? 'Enterprise Architecture & Verified Aniwall' : 'Pustaka Arsitektur Enterprise & Aniwall Terverifikasi')
            : ($sectorMap[$activeSector]['name'][$locale] ?? $activeSector);
        $pageTitle = "{$domainHost} - {$sectorTitle}";

        $schemaOrgJsonLd = Cache::rememberForever("project_os:seo_explore_jsonld:v10:{$locale}:{$activeSector}", static function () use ($filteredTestimonials, $sectorTitle, $locale, $activeSector): string {
            $itemList = [];
            foreach (array_slice($filteredTestimonials, 0, 12) as $idx => $item) {
                $slug = $item['seo_slug'] ?? Str::slug($item['client_company'] ?? ('architecture-'.($idx + 1)));
                $itemList[] = [
                    '@type' => 'ListItem',
                    'position' => $idx + 1,
                    'url' => route('explore.show', ['sector' => strtolower($item['sector_code']), 'slug' => $slug]),
                    'name' => ($item['sector_badge'] ?? $item['sector_code']).' — '.($item['public_ux_label'] ?? $item['public_ux_preset']),
                ];
            }

            return (string) json_encode([
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'CollectionPage',
                        'name' => $sectorTitle,
                        'description' => $locale === 'en'
                            ? 'Cryptographically verified enterprise software blueprints across 12 functional sectors with PostgreSQL 16 Strict ULID, Dual-Zone UI, and Zero AI Slop.'
                            : 'Katalog arsitektur perangkat lunak enterprise terverifikasi kriptografi lintas 12 sektor bisnis dengan PostgreSQL 16 Strict ULID, Dual-Zone UI, dan Zero AI Slop.',
                        'url' => $activeSector === 'ALL' ? route('explore.index') : route('explore.sector', ['sector' => strtolower($activeSector)]),
                        'mainEntity' => [
                            '@type' => 'ItemList',
                            'itemListElement' => $itemList,
                        ],
                    ],
                    [
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Explore Architecture', 'item' => route('explore.index')],
                        ],
                    ],
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        });

        return view('explore.index', [
            'pageTitle' => $pageTitle,
            'locale' => $locale,
            'sectors' => $allSectors,
            'sectorMap' => $sectorMap,
            'activeSector' => $activeSector,
            'testimonials' => $filteredTestimonials,
            'allTestimonials' => $allTestimonials,
            'publicUxPresets' => $publicUxPresets,
            'fiveIronLaws' => $fiveIronLaws,
            'sixSaasModules' => $sixSaasModules,
            'schemaOrgJsonLd' => $schemaOrgJsonLd,
        ]);
    }

    /**
     * SSR Programmatic SEO Architecture Detail Page (/explore/{sector}/{slug}) — 20% Public Teaser + Studio CTA.
     */
    public function show(Request $request, string $sector, string $slug): View
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'id';
        $catalog = PosDualZoneFrontendAndSaasCatalog::getVerifiedTestimonialsCatalog($locale);

        $matched = null;
        foreach ($catalog as $item) {
            $itemSlug = (string) ($item['seo_slug'] ?? Str::slug($item['client_company'] ?? $item['id']));
            if ($itemSlug === $slug || (string) $item['id'] === $slug) {
                $matched = $item;
                break;
            }
        }

        if (! $matched) {
            abort(404);
        }

        $presets = PosDualZoneFrontendAndSaasCatalog::sixPublicUxPresets($locale);
        $uxDetail = $presets[$matched['public_ux_preset']] ?? reset($presets);
        $fiveIronLaws = PosDualZoneFrontendAndSaasCatalog::fiveIronAnimationLaws($locale);

        $domainHost = parse_url((string) config('app.url', 'https://neriahpro.com'), PHP_URL_HOST) ?: 'neriahpro.com';
        $headline = ($matched['sector_badge'] ?? $matched['sector_code']).' Blueprint';
        $pageTitle = "{$domainHost} - {$headline}";
        $ogSvgUrl = route('explore.og', ['slug' => $slug]);

        $schemaOrgJsonLd = Cache::rememberForever("project_os:seo_explore_detail:v10:{$locale}:{$slug}", static function () use ($matched, $headline, $ogSvgUrl, $sector, $slug): string {
            return (string) json_encode([
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'SoftwareApplication',
                        'name' => 'NeriahPro Project OS — '.$headline,
                        'applicationCategory' => 'BusinessApplication',
                        'operatingSystem' => 'Linux, Docker, PostgreSQL 16, Web',
                        'image' => $ogSvgUrl,
                        'offers' => [
                            '@type' => 'Offer',
                            'price' => '0',
                            'priceCurrency' => 'IDR',
                        ],
                        'review' => [
                            '@type' => 'Review',
                            'author' => ['@type' => 'Person', 'name' => $matched['client_name']],
                            'reviewBody' => $matched['quote'],
                            'reviewRating' => ['@type' => 'Rating', 'ratingValue' => '5', 'bestRating' => '5'],
                        ],
                    ],
                    [
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Explore', 'item' => route('explore.index')],
                            ['@type' => 'ListItem', 'position' => 3, 'name' => $headline, 'item' => route('explore.show', ['sector' => $sector, 'slug' => $slug])],
                        ],
                    ],
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        });

        return view('explore.show', [
            'pageTitle' => $pageTitle,
            'locale' => $locale,
            'item' => $matched,
            'uxDetail' => $uxDetail,
            'fiveIronLaws' => $fiveIronLaws,
            'ogSvgUrl' => $ogSvgUrl,
            'schemaOrgJsonLd' => $schemaOrgJsonLd,
        ]);
    }

    /**
     * 100% Local Dynamic SVG OpenGraph Card Generator (/explore/og/{slug}.svg — Zero External API/CDN).
     */
    public function openGraphSvg(string $slug): Response
    {
        $catalog = PosDualZoneFrontendAndSaasCatalog::getVerifiedTestimonialsCatalog('en');
        $matched = $catalog[0] ?? [];
        foreach ($catalog as $item) {
            $itemSlug = (string) ($item['seo_slug'] ?? Str::slug($item['client_company'] ?? $item['id']));
            if ($itemSlug === $slug || (string) $item['id'] === $slug) {
                $matched = $item;
                break;
            }
        }

        $sectorBadge = htmlspecialchars((string) ($matched['sector_badge'] ?? 'Enterprise Sector Blueprint'), ENT_QUOTES, 'UTF-8');
        $uxLabel = htmlspecialchars((string) ($matched['public_ux_preset'] ?? 'UX_F1_SCROLLYTELLING'), ENT_QUOTES, 'UTF-8');
        $opVariant = htmlspecialchars((string) ($matched['operational_ui_variant'] ?? 'UI_01A_POS_SPLIT_TERMINAL'), ENT_QUOTES, 'UTF-8');
        $tables = (int) ($matched['receipt']['ulid_tables_count'] ?? 24);
        $locks = (int) ($matched['receipt']['concurrency_locks_count'] ?? 6);
        $tokens = (int) ($matched['receipt']['token_saved_percent'] ?? 65);
        $hash = htmlspecialchars((string) ($matched['receipt']['scope_hash_short'] ?? 'a94f82e1c03d9912'), ENT_QUOTES, 'UTF-8');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630">
  <rect width="1200" height="630" fill="#09090b"/>
  <rect x="36" y="36" width="1128" height="558" fill="#09090b" stroke="#27272a" stroke-width="2"/>
  <rect x="36" y="36" width="1128" height="6" fill="#10b981"/>
  <text x="80" y="110" fill="#10b981" font-family="monospace" font-size="20" font-weight="bold">NERIAHPRO PROJECT OS v10.0 // VERIFIED ARCHITECTURE RECEIPT</text>
  <text x="80" y="195" fill="#fafafa" font-family="sans-serif" font-size="44" font-weight="bold">{$sectorBadge}</text>
  <text x="80" y="255" fill="#a1a1aa" font-family="monospace" font-size="22">Zone A Back-Stage: {$opVariant}</text>
  <text x="80" y="295" fill="#d4d4d8" font-family="monospace" font-size="22">Zone B Public UX : {$uxLabel}</text>
  <rect x="80" y="350" width="1040" height="110" fill="#18181b" stroke="#3f3f46" stroke-width="1.5"/>
  <text x="110" y="395" fill="#34d399" font-family="monospace" font-size="22" font-weight="bold">{$tables} ULID Tables  |  {$locks} Concurrency Locks  |  Token Saved: {$tokens}%</text>
  <text x="110" y="435" fill="#a1a1aa" font-family="monospace" font-size="18">SHA-256 Scope Seal: {$hash}  |  PostgreSQL 16 Strict  |  Zero AI Slop</text>
  <text x="80" y="540" fill="#fafafa" font-family="monospace" font-size="20" font-weight="bold">neriahpro.com/explore  —  12 Functional Sectors x Dual-Zone Enterprise UI</text>
</svg>
SVG;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * SaaS Module #1: Check Feature Gate Entitlement (Phase 3–6 Copy & MCP Bridge).
     */
    public function featureGate(Request $request, ?string $slug = null): JsonResponse
    {
        $feature = (string) $request->query('feature', 'copy_phase_3_6');
        $projectId = (string) ($slug ?: $request->query('project_id', ''));
        $project = null;
        if ($projectId !== '' && Schema::hasTable('pos_projects')) {
            $project = PosProject::query()
                ->where('id', $projectId)
                ->orWhereHas('visionBlueprint', static fn ($q) => $q->where('slug', $projectId)->orWhere('id', $projectId))
                ->first();
        }

        $gate = PosSaasCommercialEngineService::checkFeatureAccess($request->user(), $project, $feature);

        return response()->json([
            'success' => true,
            'can_copy_phase_3_to_6' => (bool) ($gate['allowed'] ?? false),
            'can_connect_mcp_bridge' => (bool) ($gate['allowed'] ?? false),
            'gate' => $gate,
        ]);
    }

    /**
     * SaaS Module #1: Instant (<2s) Idempotent Webhook Settlement (Midtrans IDR / Global USD Stripe).
     * Strictly verifies HMAC-SHA256 signature (`X-NeriahPro-Signature`) or SuperAdmin session.
     */
    public function billingWebhook(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|string|max:64',
            'user_id' => 'required|string|size:26',
            'target_tier' => 'required|string|max:40',
            'amount_idr' => 'required|integer|min:0',
            'gateway' => 'nullable|string|in:MIDTRANS_IDR,STRIPE_USD,LEMONSQUEEZY_USD',
            'original_currency' => 'nullable|string|max:8',
            'original_amount' => 'nullable|numeric|min:0',
            'blueprint_id' => 'nullable|string|size:26',
            'transaction_id' => 'nullable|string|max:80',
        ]);

        $secret = (string) (config('services.saas_webhook.secret') ?: config('app.key'));
        $canonicalMessage = implode('|', [
            $validated['order_id'],
            $validated['user_id'],
            $validated['target_tier'],
            (string) $validated['amount_idr'],
        ]);
        $expectedHmac = hash_hmac('sha256', $canonicalMessage, $secret);
        $providedSig = (string) $request->header('X-NeriahPro-Signature', '');

        $isSuperAdmin = $request->user() && $request->user()->isSuperAdmin();
        if (! $isSuperAdmin && ($providedSig === '' || ! hash_equals($expectedHmac, $providedSig))) {
            AuditLog::record(
                action: 'SAAS_WEBHOOK_INVALID_SIGNATURE_BLOCKED',
                newValues: ['order_id' => $validated['order_id'], 'ip' => $request->ip()],
                description: 'Blocked unauthenticated SaaS billing webhook with invalid HMAC signature.',
                eventCategory: 'security',
                executionChannel: 'WEBHOOK'
            );

            return response()->json([
                'success' => false,
                'message' => 'Invalid webhook HMAC signature.',
            ], 401);
        }

        $user = User::query()->where('id', $validated['user_id'])->firstOrFail();
        $blueprint = ! empty($validated['blueprint_id'])
            ? VisionBlueprint::query()->where('id', $validated['blueprint_id'])->first()
            : null;

        $result = PosSaasCommercialEngineService::settleSubscriptionOrProjectWebhook(
            user: $user,
            orderId: $validated['order_id'],
            targetTier: $validated['target_tier'],
            amountIdr: (int) $validated['amount_idr'],
            gateway: (string) (($validated['gateway'] ?? null) ?: 'MIDTRANS_IDR'),
            originalCurrency: (string) (($validated['original_currency'] ?? null) ?: 'IDR'),
            originalAmount: (float) (($validated['original_amount'] ?? null) ?: $validated['amount_idr']),
            blueprint: $blueprint,
            payload: $validated
        );

        return response()->json($result);
    }

    /**
     * SaaS Module #3: Configure Multi-Seat Organization Workspace & Agency White-Label Branding Vault.
     */
    public function upsertWorkspace(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        if (! $request->filled('primary_hex') && $request->filled('brand_primary_hex')) {
            $request->merge(['primary_hex' => $request->input('brand_primary_hex')]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:160',
            'agency_name' => 'nullable|string|max:160',
            'legal_entity' => 'nullable|string|max:180',
            'primary_hex' => 'nullable|string|max:16',
            'letterhead_footer' => 'nullable|string|max:300',
            'contact_email' => 'nullable|string|max:180',
            'phone_country_code' => 'nullable|string|max:10',
            'phone_number' => 'nullable|string|max:40',
            'seat_limit' => 'nullable|integer|min:2|max:100',
        ]);

        $workspace = PosSaasCommercialEngineService::createOrUpdateWorkspace($user, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Workspace organisasi & White-Label Branding berhasil disimpan.',
            'workspace' => $workspace,
        ]);
    }

    /**
     * SaaS Module #3: Invite Member Seat (OWNER, LEAD_ARCHITECT, JUNIOR_DEV, CLIENT_VIEWER).
     */
    public function inviteWorkspaceMember(Request $request, ?string $workspaceId = null): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        if ($workspaceId !== null && ! $request->filled('workspace_id')) {
            $request->merge(['workspace_id' => $workspaceId]);
        }

        $validated = $request->validate([
            'workspace_id' => 'nullable|string|size:26',
            'email' => 'required|string|max:180',
            'workspace_role' => 'required|string|in:OWNER,LEAD_ARCHITECT,JUNIOR_DEV,CLIENT_VIEWER',
            'phone_country_code' => 'nullable|string|max:10',
            'phone_number' => 'nullable|string|max:40',
        ]);

        $workspace = ! empty($validated['workspace_id'])
            ? PosOrganizationWorkspace::query()->where('id', $validated['workspace_id'])->firstOrFail()
            : PosOrganizationWorkspace::query()->where('owner_user_id', $user->id)->first();

        if (! $workspace) {
            $workspace = PosSaasCommercialEngineService::createOrUpdateWorkspace($user, [
                'name' => ($user->name ?: 'Partner').' Organization',
            ]);
        }

        $isAuthorized = $workspace->owner_user_id === $user->id
            || $user->isSuperAdmin()
            || PosOrganizationMember::query()
                ->where('workspace_id', $workspace->id)
                ->where('user_id', $user->id)
                ->whereIn('workspace_role', ['OWNER', 'LEAD_ARCHITECT'])
                ->where('status', 'ACTIVE')
                ->exists();

        if (! $isAuthorized) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Owner atau Lead Architect yang diizinkan mengundang kursi anggota organisasi.',
            ], 403);
        }

        try {
            $member = PosSaasCommercialEngineService::inviteWorkspaceMember($workspace, $validated);

            return response()->json([
                'success' => true,
                'message' => "Undangan kursi [{$member->workspace_role}] berhasil dikirim ke {$member->invited_email}.",
                'member' => $member,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * SaaS Module #4: Submit Non-Profit / NGO / Religious / Academic Impact Grant Application.
     * Stores legal decree strictly to `private` disk + AgentShield scrub.
     */
    public function submitImpactGrant(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        // Normalize alternate form field aliases from Customer Dashboard
        $typeAliasMap = [
            'NGO' => 'NGO_SOCIAL',
            'RELIGIOUS' => 'RELIGIOUS_HOUSE',
            'ACADEMIC' => 'ACADEMIC_CAMPUS',
            'SOCIAL_FOUNDATION' => 'NONPROFIT_YAYASAN',
        ];
        $rawType = strtoupper((string) $request->input('organization_type', 'NONPROFIT_YAYASAN'));
        if (isset($typeAliasMap[$rawType])) {
            $request->merge(['organization_type' => $typeAliasMap[$rawType]]);
        }
        if (! $request->filled('official_email') && $request->filled('pic_email')) {
            $request->merge(['official_email' => $request->input('pic_email')]);
        }
        if (! $request->filled('phone_country_code') && $request->filled('country_code')) {
            $request->merge(['phone_country_code' => $request->input('country_code')]);
        }
        if (! $request->filled('phone_number') && $request->filled('pic_phone')) {
            $request->merge(['phone_number' => $request->input('pic_phone')]);
        }
        if (! $request->filled('mission_statement') && $request->filled('social_mission_summary')) {
            $request->merge(['mission_statement' => $request->input('social_mission_summary')]);
        }
        if (! $request->filled('requested_project_budget_idr') && $request->filled('annual_beneficiaries_display')) {
            $request->merge(['requested_project_budget_idr' => $request->input('annual_beneficiaries_display')]);
        }
        if (! $request->filled('legal_decree_number')) {
            $request->merge(['legal_decree_number' => 'SK-'.strtoupper(substr((string) Str::ulid(), -8))]);
        }

        $validated = $request->validate([
            'applicant_name' => 'nullable|string|max:160',
            'organization_name' => 'required|string|max:200',
            'organization_type' => 'required|string|in:NONPROFIT_YAYASAN,NGO_SOCIAL,RELIGIOUS_HOUSE,ACADEMIC_CAMPUS',
            'official_email' => 'required|string|max:180',
            'phone_country_code' => 'required|string|max:10',
            'phone_number' => 'required|string|max:40',
            'legal_decree_number' => 'required|string|max:160',
            'requested_project_budget_idr' => 'nullable|string|max:40',
            'mission_statement' => 'required|string|min:10|max:4000',
            'decree_document' => 'nullable|file|max:8192|mimes:pdf,png,jpg,jpeg',
            'legality_document' => 'nullable|file|max:8192|mimes:pdf,png,jpg,jpeg',
        ]);

        $privatePath = '';
        $uploadedFile = $request->file('decree_document') ?: $request->file('legality_document');
        if ($uploadedFile) {
            $privatePath = (string) Storage::disk('private')->putFile('impact-grants/'.date('Y/m'), $uploadedFile);
        } else {
            $privatePath = 'private://impact-grants/declaration-'.strtolower((string) Str::ulid()).'.txt';
        }

        try {
            $application = PosSaasCommercialEngineService::submitImpactGrantApplication(
                array_merge($validated, ['private_document_path' => $privatePath]),
                $user
            );

            return response()->json([
                'success' => true,
                'message' => 'Permohonan Impact Grant berhasil dienkripsi & dikirim ke tim kurasi NeriahPro.',
                'application_id' => $application->id,
                'private_document_path' => $application->private_document_path,
                'status' => $application->status,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bagian 1.1: Allow Verified Project Owner to customize quote on their Telemetry-Backed Architecture Receipt.
     */
    public function updateTestimonialQuote(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        $validated = $request->validate([
            'testimonial_id' => 'required|string|size:26',
            'quote_id' => 'required|string|min:12|max:600',
            'quote_en' => 'nullable|string|max:600',
            'is_published' => 'nullable|boolean',
        ]);

        $testimonial = PosVerifiedTestimonial::query()
            ->where('id', $validated['testimonial_id'])
            ->when(! $user->isSuperAdmin(), static fn ($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $sanitizer = app(DocumentThreatSanitizerService::class);
        $cleanId = $sanitizer->sanitizeDocumentText($validated['quote_id'], 'testimonial_quote_id')['sanitized_text'];
        $cleanEn = ! empty($validated['quote_en'])
            ? $sanitizer->sanitizeDocumentText($validated['quote_en'], 'testimonial_quote_en')['sanitized_text']
            : $cleanId;

        $testimonial->quote = [
            'id' => $cleanId,
            'en' => $cleanEn,
        ];
        if (array_key_exists('is_published', $validated)) {
            $testimonial->is_published = (bool) $validated['is_published'];
        }
        $testimonial->save();

        AuditLog::record(
            action: 'VERIFIED_TESTIMONIAL_QUOTE_UPDATED',
            model: $testimonial,
            newValues: ['testimonial_id' => $testimonial->id, 'is_published' => $testimonial->is_published],
            description: "Client updated quote on verified testimonial [{$testimonial->id}].",
            eventCategory: 'blueprint',
            sectorCode: $testimonial->sector_code,
            primitiveCode: 'PRIMITIVE_VERIFIED_SOCIAL_PROOF',
            executionChannel: 'WEB_UI'
        );

        return response()->json([
            'success' => true,
            'message' => 'Testimoni terverifikasi arsitektur Anda berhasil diperbarui.',
            'testimonial' => $testimonial,
        ]);
    }
}
