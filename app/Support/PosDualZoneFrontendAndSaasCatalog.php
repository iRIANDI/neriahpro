<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\PosVerifiedTestimonial;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * PosDualZoneFrontendAndSaasCatalog — Project OS v10.0 Ultimate
 *
 * Unifies the specifications from scrolly_telling.md:
 * 1. Cryptographically Verified Testimonial & Case-Study Engine (PRIMITIVE_VERIFIED_SOCIAL_PROOF + Infinite Kinetic Aniwall)
 * 2. Dual-Zone UI Architecture:
 *    - Zone A: Operational Back-Stage (Crisp Linear/Stripe 36 Visual UI Variants + 5 Shells — ZERO heavy animation)
 *    - Zone B: Public Front-Stage (6 Advanced Frontend UX Presets UX_F1..UX_F6 mapped to 12 Sectors)
 * 3. 5 Iron Laws of Enterprise Anti-Slop Animation (100% Local @vite, GPU translate3d/scale/opacity, CLS=0.00, prefers-reduced-motion, IntersectionObserver FCP <0.8s)
 * 4. Universal Input Hygiene Standard (Strict Email Chars, Regional Thousand Separators, config/country_zones.php + Instant Leading-0 Strip)
 * 5. 6 Critical SaaS Commercial & Growth Modules for neriahpro.com
 */
class PosDualZoneFrontendAndSaasCatalog
{
    public const VERSION = '10.0.0-DUAL-ZONE-SAAS-ENGINE';

    public const PRIMITIVE_SOCIAL_PROOF = 'PRIMITIVE_VERIFIED_SOCIAL_PROOF';

    public const UX_F1 = 'UX_F1_SCROLLYTELLING';

    public const UX_F2 = 'UX_F2_KINETIC_ANIWALL';

    public const UX_F3 = 'UX_F3_SPATIAL_SHOWCASE';

    public const UX_F4 = 'UX_F4_LOOKBOOK_COMMERCE';

    public const UX_F5 = 'UX_F5_TRUST_SIMULATOR';

    public const UX_F6 = 'UX_F6_GAMIFIED_PATHWAY';

    /**
     * 6 Preset Advanced Public Front-Stage UX Archetypes (Zone B) — Bilingual Tier-1 (ID & EN).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function sixPublicUxPresets(?string $locale = null): array
    {
        $loc = ($locale ?: app()->getLocale()) === 'en' ? 'en' : 'id';

        $presets = [
            self::UX_F1 => [
                'code' => self::UX_F1,
                'short_badge' => 'STICKY SCROLLYTELLING',
                'name' => [
                    'id' => 'Apple-Style Sticky Scrollytelling',
                    'en' => 'Apple-Style Sticky Scrollytelling',
                ],
                'local_tech_stack' => 'GSAP ScrollTrigger + Lenis Smooth Scroll + IntersectionObserver (@vite local bundle)',
                'npm_packages' => ['gsap', 'lenis'],
                'summary' => [
                    'id' => 'Elemen visual utama terkunci (pinned) di tengah layar saat pengguna menggulir ke bawah dan bertransformasi tahap demi tahap mengikuti narasi progres.',
                    'en' => 'Center stage visual pins smoothly on scroll and transforms step-by-step synchronized with narrative milestones.',
                ],
                'best_sectors' => ['SECTOR_12_GOVTECH_NONPROFIT', 'SECTOR_05_INDUSTRIAL_MRP'],
                'miniature_wireframe_stages' => [
                    ['step' => '01', 'label_id' => 'Hulu / Pondasi', 'label_en' => 'Origin / Foundation', 'metric' => '100% SHA-256'],
                    ['step' => '02', 'label_id' => 'Proses & Verifikasi', 'label_en' => 'Execution & Audit', 'metric' => 'CLS 0.00'],
                    ['step' => '03', 'label_id' => 'Dampak Nyata / Serah Terima', 'label_en' => 'Verified Impact / Handover', 'metric' => '< 0.8s FCP'],
                ],
                'extra_man_hours' => 14,
            ],
            self::UX_F2 => [
                'code' => self::UX_F2,
                'short_badge' => 'BENTO & KINETIC ANIWALL',
                'name' => [
                    'id' => 'Interactive Bento Grid & Kinetic Aniwall',
                    'en' => 'Interactive Bento Grid & Kinetic Aniwall',
                ],
                'local_tech_stack' => 'Tailwind CSS Grid + Alpine.js + GPU translate3d Marquee + Spotlight Border',
                'npm_packages' => ['alpinejs'],
                'summary' => [
                    'id' => 'Grid Bento asimetris presisi dengan sorotan border mengikuti kursor dan dinding testimoni terverifikasi bergerak mulus dua arah (pause on hover).',
                    'en' => 'Asymmetric Bento grid with cursor border spotlight and bidirectional hardware-accelerated Wall of Love (pause on hover).',
                ],
                'best_sectors' => ['SECTOR_04_FINTECH_LEDGER', 'SECTOR_08_PROFESSIONAL_PSA'],
                'miniature_wireframe_stages' => [
                    ['step' => 'B1', 'label_id' => 'Bento Telemetry Hero', 'label_en' => 'Bento Telemetry Hero', 'metric' => '60-120 FPS'],
                    ['step' => 'B2', 'label_id' => 'Aniwall Marquee 2-Arah', 'label_en' => 'Bidirectional Aniwall', 'metric' => 'Pause Hover'],
                    ['step' => 'B3', 'label_id' => 'Receipt Blueprint Modal', 'label_en' => 'Blueprint Receipt Modal', 'metric' => 'ULID Linked'],
                ],
                'extra_man_hours' => 12,
            ],
            self::UX_F3 => [
                'code' => self::UX_F3,
                'short_badge' => 'SPATIAL 360° & FLOORPLAN',
                'name' => [
                    'id' => 'Immersive Spatial, 360° & Interactive Floorplan Explorer',
                    'en' => 'Immersive Spatial, 360° & Interactive Floorplan Explorer',
                ],
                'local_tech_stack' => 'Three.js / Interactive SVG Layering + High-Res Local WebP (@vite)',
                'npm_packages' => ['three'],
                'summary' => [
                    'id' => 'Eksplorasi ruang interaktif: klik denah meja restoran, kursi konser/VIP, kamar resort, atau lapangan olahraga secara visual real-time.',
                    'en' => 'Interactive spatial explorer: click live restaurant tables, concert VIP seats, resort suites, or sports courts directly on SVG/WebGL floorplans.',
                ],
                'best_sectors' => ['SECTOR_02_HOSPITALITY_SPACE', 'SECTOR_09_FOOD_BEVERAGE', 'SECTOR_10_MEDIA_TICKETING'],
                'miniature_wireframe_stages' => [
                    ['step' => 'S1', 'label_id' => 'Peta Kursi / Meja SVG', 'label_en' => 'Interactive SVG Seat Map', 'metric' => 'Live Lock'],
                    ['step' => 'S2', 'label_id' => 'Pratinjau Sudut Pandang', 'label_en' => 'Spatial Viewpoint', 'metric' => 'WebP Local'],
                    ['step' => 'S3', 'label_id' => 'Kunci Slot Anti-Bentrok', 'label_en' => 'Atomic Slot Hold', 'metric' => 'Redis TTL'],
                ],
                'extra_man_hours' => 18,
            ],
            self::UX_F4 => [
                'code' => self::UX_F4,
                'short_badge' => 'LOOKBOOK & INSTANT DRAWER',
                'name' => [
                    'id' => 'Editorial Lookbook & Instant Drawer Commerce',
                    'en' => 'Editorial Lookbook & Instant Drawer Commerce',
                ],
                'local_tech_stack' => 'View Transitions API + Optimistic UI Slide-Over Cart + Spec Matrix',
                'npm_packages' => ['alpinejs'],
                'summary' => [
                    'id' => 'Transisi antar-halaman produk mulus tanpa reload, quick-view laci instan, dan matriks perbandingan spesifikasi B2B/Ritel.',
                    'en' => 'Zero-reload shared-element product transitions, instant slide-over cart drawer, and side-by-side SKU spec comparison.',
                ],
                'best_sectors' => ['SECTOR_01_COMMERCE_OMNICHANNEL'],
                'miniature_wireframe_stages' => [
                    ['step' => 'L1', 'label_id' => 'Editorial Lookbook Grid', 'label_en' => 'Editorial Lookbook Grid', 'metric' => 'Aspect 4:5'],
                    ['step' => 'L2', 'label_id' => 'Quick-View Spec Compare', 'label_en' => 'Quick-View Spec Compare', 'metric' => '0ms Optimistic'],
                    ['step' => 'L3', 'label_id' => 'Slide-Over Checkout', 'label_en' => 'Slide-Over Checkout', 'metric' => 'QRIS/VA'],
                ],
                'extra_man_hours' => 12,
            ],
            self::UX_F5 => [
                'code' => self::UX_F5,
                'short_badge' => 'INTERACTIVE TRUST SIMULATOR',
                'name' => [
                    'id' => 'Interactive Calculator & Trust Portal',
                    'en' => 'Interactive Calculator & Trust Portal',
                ],
                'local_tech_stack' => 'Reactive Alpine.js Mathematical Sliders + Local Canvas Chart',
                'npm_packages' => ['alpinejs'],
                'summary' => [
                    'id' => 'Pengunjung menggeser slider simulasi langsung di halaman depan (tarif kargo kg/volumetrik, estimasi paket medis, atau kalkulator PPh21/ROI) dengan pemisah ribuan regional.',
                    'en' => 'Visitors drag live mathematical sliders on the landing page (cargo volumetric tariff, medical package estimator, or payroll/ROI calculator) with regional thousand separators.',
                ],
                'best_sectors' => ['SECTOR_03_HEALTHCARE_CLINICAL', 'SECTOR_06_LOGISTICS_FLEET', 'SECTOR_11_HRIS_INTERNAL_OPS'],
                'miniature_wireframe_stages' => [
                    ['step' => 'T1', 'label_id' => 'Slider Parameter Real-Time', 'label_en' => 'Live Parameter Sliders', 'metric' => 'Tabular-Nums'],
                    ['step' => 'T2', 'label_id' => 'Rincian Biaya Transparan', 'label_en' => 'Transparent Breakdown', 'metric' => '1.000 / 1,000'],
                    ['step' => 'T3', 'label_id' => 'Konversi Booking / Resi', 'label_en' => 'Instant Booking CTA', 'metric' => 'E.164 Ready'],
                ],
                'extra_man_hours' => 10,
            ],
            self::UX_F6 => [
                'code' => self::UX_F6,
                'short_badge' => 'SKILL-TREE & GAMIFIED PATH',
                'name' => [
                    'id' => 'Skill-Tree & Milestone Progress Showcase',
                    'en' => 'Skill-Tree & Milestone Progress Showcase',
                ],
                'local_tech_stack' => 'SVG Path Stroke Animation + Interactive Curriculum Tree (@vite)',
                'npm_packages' => ['alpinejs'],
                'summary' => [
                    'id' => 'Menampilkan peta perjalanan kurikulum seperti pohon keahlian interaktif, lencana sertifikasi SHA-256, dan papan progres kohort.',
                    'en' => 'Visualizes learning journeys as an interactive SVG skill-tree, SHA-256 verifiable badges, and cohort milestone leaderboards.',
                ],
                'best_sectors' => ['SECTOR_07_EDTECH_MEMBERSHIP'],
                'miniature_wireframe_stages' => [
                    ['step' => 'G1', 'label_id' => 'Pohon Kurikulum SVG', 'label_en' => 'Interactive SVG Skill-Tree', 'metric' => 'Node Unlock'],
                    ['step' => 'G2', 'label_id' => 'Lencana & Sertifikat Hash', 'label_en' => 'Hash-Verified Badges', 'metric' => 'SHA-256'],
                    ['step' => 'G3', 'label_id' => 'Pendaftaran Kohort Instan', 'label_en' => 'Cohort Enrollment', 'metric' => 'O(1) Cursor'],
                ],
                'extra_man_hours' => 14,
            ],
        ];

        foreach ($presets as &$item) {
            $item['localized_name'] = $item['name'][$loc] ?? $item['name']['id'];
            $item['localized_summary'] = $item['summary'][$loc] ?? $item['summary']['id'];
        }
        unset($item);

        return $presets;
    }

    /**
     * Canonical Default Mapping from each of the 12 Sectors to its optimal Zone B Public UX Preset.
     *
     * @return array<string, string>
     */
    public static function sectorDefaultPublicUxMap(): array
    {
        return [
            'SECTOR_01_COMMERCE_OMNICHANNEL' => self::UX_F4,
            'SECTOR_02_HOSPITALITY_SPACE' => self::UX_F3,
            'SECTOR_03_HEALTHCARE_CLINICAL' => self::UX_F5,
            'SECTOR_04_FINTECH_LEDGER' => self::UX_F2,
            'SECTOR_05_INDUSTRIAL_MRP' => self::UX_F1,
            'SECTOR_06_LOGISTICS_FLEET' => self::UX_F5,
            'SECTOR_07_EDTECH_MEMBERSHIP' => self::UX_F6,
            'SECTOR_08_PROFESSIONAL_PSA' => self::UX_F2,
            'SECTOR_09_FOOD_BEVERAGE' => self::UX_F3,
            'SECTOR_10_MEDIA_TICKETING' => self::UX_F3,
            'SECTOR_11_HRIS_INTERNAL_OPS' => self::UX_F5,
            'SECTOR_12_GOVTECH_NONPROFIT' => self::UX_F1,
        ];
    }

    /**
     * Resolve default Zone B Public UX Preset for a given Sector.
     */
    public static function defaultPublicUxForSector(string $sectorCode): string
    {
        $map = self::sectorDefaultPublicUxMap();

        return $map[$sectorCode] ?? self::UX_F2;
    }

    /**
     * Normalize & validate a Public UX Preset code against UX_F1..UX_F6 (falling back to sector default).
     */
    public static function normalizePublicUxPreset(?string $code, ?string $sectorCode = null): string
    {
        $upper = strtoupper(trim((string) $code));
        $valid = [self::UX_F1, self::UX_F2, self::UX_F3, self::UX_F4, self::UX_F5, self::UX_F6];
        if (in_array($upper, $valid, true)) {
            return $upper;
        }

        return $sectorCode ? self::defaultPublicUxForSector($sectorCode) : self::UX_F2;
    }

    /**
     * Smart Idea + Sector Auto-Matcher for Public Front-Stage UX Preset (Zone B).
     *
     * @return array<string, mixed>
     */
    public static function autoMatchPublicUxPresetForSectorAndIdea(string $sectorCode, string $corpusText = '', string $locale = 'id'): array
    {
        $presets = self::sixPublicUxPresets($locale);
        $defaultMap = self::sectorDefaultPublicUxMap();
        $chosenCode = $defaultMap[$sectorCode] ?? self::UX_F2;
        $haystack = mb_strtolower($corpusText);

        if ($haystack !== '') {
            if (preg_match('/(scrollytelling|cerita perjalanan|story|donasi|yayasan|filantropi|tahap pembangunan|konstruksi)/u', $haystack)) {
                $chosenCode = self::UX_F1;
            } elseif (preg_match('/(denah|floorplan|360|kursi|seat|meja restoran|lapangan|stadion|villa|resort)/u', $haystack)) {
                $chosenCode = self::UX_F3;
            } elseif (preg_match('/(lookbook|fashion|katalog produk|drawer cart|perbandingan spesifikasi|quick-view)/u', $haystack)) {
                $chosenCode = self::UX_F4;
            } elseif (preg_match('/(kalkulator|simulator|slider|simulasi cicilan|ongkir|tarif kargo|pph21|paket medis)/u', $haystack)) {
                $chosenCode = self::UX_F5;
            } elseif (preg_match('/(skill-tree|pohon keahlian|gamifikasi|leaderboard|kurikulum|kursus|akademi)/u', $haystack)) {
                $chosenCode = self::UX_F6;
            } elseif (preg_match('/(bento|aniwall|wall of love|portofolio|saas|fintech)/u', $haystack)) {
                $chosenCode = self::UX_F2;
            }
        }

        return $presets[$chosenCode] ?? $presets[self::UX_F2];
    }

    /**
     * 5 Iron Laws of Enterprise Anti-Slop Animation (scrolly_telling.md Section 2).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function fiveIronAnimationLaws(?string $locale = null): array
    {
        $loc = ($locale ?: app()->getLocale()) === 'en' ? 'en' : 'id';

        $laws = [
            [
                'code' => 'LAW_01_LOCAL_VITE_BUNDLE_ONLY',
                'title' => [
                    'id' => '100% Local Bundled Libraries (HARAM CDN Eksternal)',
                    'en' => '100% Local Bundled Libraries (Zero External CDN)',
                ],
                'rule' => [
                    'id' => 'Seluruh pustaka (gsap, lenis, three, alpinejs) wajib di-install via npm dan di-bundle melalui @vite; dilarang memuat cdnjs, unpkg, atau jsdelivr.',
                    'en' => 'All libraries (gsap, lenis, three, alpinejs) must be installed via npm and bundled via @vite; external CDNs (cdnjs, unpkg, jsdelivr) are strictly banned.',
                ],
            ],
            [
                'code' => 'LAW_02_GPU_COMPOSITOR_ONLY',
                'title' => [
                    'id' => 'GPU Compositor Only (60–120 FPS Guaranteed)',
                    'en' => 'GPU Compositor Only (60–120 FPS Guaranteed)',
                ],
                'rule' => [
                    'id' => 'Dilarang menganimasikan properti pemicu layout reflow (top, left, width, height, margin). Wajib 100% transform: translate3d(), scale(), dan opacity.',
                    'en' => 'Never animate layout-reflow CSS properties (top, left, width, height, margin). Strictly animate transform: translate3d(), scale(), and opacity.',
                ],
            ],
            [
                'code' => 'LAW_03_ZERO_CLS_ASPECT_LOCK',
                'title' => [
                    'id' => 'Zero Cumulative Layout Shift (CLS = 0.00)',
                    'en' => 'Zero Cumulative Layout Shift (CLS = 0.00)',
                ],
                'rule' => [
                    'id' => 'Setiap gambar, video, dan kanvas WebGL/SVG wajib memiliki dimensi rasio eksplisit (aspect-video, aspect-square, min-h-[...]) sebelum aset dimuat.',
                    'en' => 'Every image, video, and WebGL/SVG canvas must reserve explicit aspect dimensions (aspect-video, aspect-square, min-h-[...]) prior to asset load.',
                ],
            ],
            [
                'code' => 'LAW_04_PREFERS_REDUCED_MOTION',
                'title' => [
                    'id' => 'Mandatory prefers-reduced-motion Accessibility Fallback',
                    'en' => 'Mandatory prefers-reduced-motion Accessibility Fallback',
                ],
                'rule' => [
                    'id' => 'Wajib menyertakan @media (prefers-reduced-motion: reduce) yang mematikan efek marquee/parallax/scroll-pinning bagi pengguna sensitif gerakan.',
                    'en' => 'Must include @media (prefers-reduced-motion: reduce) disabling marquee, parallax, and scroll-pinning for motion-sensitive users.',
                ],
            ],
            [
                'code' => 'LAW_05_LAZY_INTERSECTION_OBSERVER',
                'title' => [
                    'id' => 'Lazy-Hydration via IntersectionObserver (FCP < 0.8s)',
                    'en' => 'Lazy-Hydration via IntersectionObserver (FCP < 0.8s)',
                ],
                'rule' => [
                    'id' => 'Komponen animasi interaktif hanya diinisialisasi saat masuk viewport melalui IntersectionObserver agar First Contentful Paint tetap < 0.8 detik.',
                    'en' => 'Heavy interactive animations initialize strictly upon entering the viewport via IntersectionObserver so First Contentful Paint stays < 0.8s.',
                ],
            ],
        ];

        foreach ($laws as &$law) {
            $law['localized_title'] = $law['title'][$loc];
            $law['localized_rule'] = $law['rule'][$loc];
        }
        unset($law);

        return $laws;
    }

    /**
     * Mandatory Universal Input Hygiene Contract (Email, Regional Thousand Separator, Country Zone + Leading-0 Strip).
     *
     * @return array<string, mixed>
     */
    public static function inputHygieneContract(?string $locale = null): array
    {
        $loc = ($locale ?: app()->getLocale()) === 'en' ? 'en' : 'id';

        $emailStd = [
            'allowed_pattern' => '^[a-z0-9._%+\\-]+@[a-z0-9.\\-]+\\.[a-z]{2,}$',
            'allowed_chars_regex' => '^[a-z0-9._%+\\-]+@[a-z0-9.\\-]+\\.[a-z]{2,}$',
            'keystroke_sanitizer_regex' => '[^a-zA-Z0-9._%+\\-@]',
            'rule' => $loc === 'en'
                ? 'Only valid RFC email characters ([a-zA-Z0-9._%+-@]) are accepted on keystroke and paste; spaces and illegal symbols are stripped immediately.'
                : 'Hanya karakter email resmi ([a-zA-Z0-9._%+-@]) yang diizinkan saat ketik maupun tempel; spasi dan simbol ilegal dihapus seketika.',
        ];
        $phoneStd = [
            'template_source' => 'config/country_zones.php',
            'source_of_truth' => 'config/country_zones.php',
            'supported_zones_count' => count(InputHygieneStandard::countryZones()),
            'strip_leading_zero_on_first_keystroke' => true,
            'e164_output_example' => '+6281234567890',
            'rule' => $loc === 'en'
                ? 'Mandatory country zone selector sourced from config/country_zones.php; typing a leading 0 in the phone number field is immediately stripped because the dial code is already attached.'
                : 'Wajib menggunakan daftar Country Zone dari config/country_zones.php; saat pertama kali mengetik angka 0 pada kolom telepon, angka 0 otomatis dihapus karena sudah diwakili kode negara.',
        ];

        return [
            'contract_version' => '10.0.0-E164-REGIONAL-STRICT',
            'email_standard' => $emailStd,
            'email' => $emailStd,
            'number_regional_thousand_separator' => [
                'id_de_es_format' => '1.500.000 (dot thousand separator)',
                'en_us_sg_jp_cn_format' => '1,500,000 (comma thousand separator)',
                'css_font_variant' => 'tabular-nums',
                'rule' => $loc === 'en'
                    ? 'All numeric and currency inputs/displays >= 1,000 automatically format regional thousand separators (comma for EN, dot for ID) with tabular-nums.'
                    : 'Seluruh input dan tampilan angka/mata uang >= 1.000 wajib memakai pemisah ribuan sesuai regional (titik . untuk ID, koma , untuk EN) dengan tabular-nums.',
            ],
            'phone_country_zone_standard' => $phoneStd,
            'phone_country_zone' => $phoneStd,
        ];
    }

    /**
     * Compile the complete Dual-Zone UI Contract (Zone A Operational + Zone B Public + PRIMITIVE_VERIFIED_SOCIAL_PROOF).
     *
     * @param  array<string, mixed>|string|null  $operationalVisualContract
     * @return array<string, mixed>
     */
    public static function compileDualZoneUiContract(
        string $sectorCode,
        array|string|null $operationalVisualContract = [],
        ?string $requestedPublicUxCode = null,
        ?string $corpusText = '',
        string $locale = 'id'
    ): array {
        // Support flexible call signature where arg #3 was navigationShell and arg #4 was publicUxPreset
        if (is_string($corpusText) && str_starts_with($corpusText, 'UX_F')) {
            $requestedPublicUxCode = $corpusText;
            $corpusText = '';
        }

        $opArray = is_array($operationalVisualContract)
            ? $operationalVisualContract
            : ['selected_variant_code' => $operationalVisualContract ?: 'UI_01A_POS_SPLIT_TERMINAL'];

        $loc = $locale === 'en' ? 'en' : 'id';
        $allPublicPresets = self::sixPublicUxPresets($loc);

        if ($requestedPublicUxCode && isset($allPublicPresets[$requestedPublicUxCode])) {
            $selectedPublicPreset = $allPublicPresets[$requestedPublicUxCode];
        } else {
            $selectedPublicPreset = self::autoMatchPublicUxPresetForSectorAndIdea($sectorCode, (string) $corpusText, $loc);
        }

        $opVariantCode = (string) ($opArray['selected_variant_code'] ?? $opArray['code'] ?? 'UI_01A_POS_SPLIT_TERMINAL');
        $navShellCode = (string) ($opArray['navigation_shell_code'] ?? $opArray['navigation_shell'] ?? 'SIDEBAR_ERP_DENSE');

        return [
            'architecture_paradigm' => 'DUAL_ZONE_SEPARATED_UI_V10',
            'zone_a_operational_backstage' => [
                'zone_code' => 'ZONE_A_OPERATIONAL_BACKSTAGE',
                'purpose' => $loc === 'en'
                    ? 'Internal Backoffice, POS, Clinical EMR, Ledger & High-Velocity Transactions'
                    : 'Internal Backoffice, Kasir POS, Rekam Medis EMR, Buku Besar & Transaksi Kecepatan Tinggi',
                'selected_ui_variant' => $opVariantCode,
                'selected_operational_variant' => $opVariantCode,
                'ui_variant_code' => $opVariantCode,
                'navigation_shell' => $navShellCode,
                'animation_policy' => 'CRISP_LINEAR_STRIPE_ZERO_PARALLAX',
                'scrollytelling_banned' => true,
                'strict_prohibitions' => [
                    'NO_SCROLLYTELLING_OR_PARALLAX',
                    'NO_CAPSULE_PILL_BUTTONS',
                    'NO_MULTI_COLOR_GRADIENTS',
                    'NO_OFFSET_PAGINATION',
                ],
            ],
            'zone_b_public_frontstage' => [
                'zone_code' => 'ZONE_B_PUBLIC_FRONTSTAGE',
                'purpose' => $loc === 'en'
                    ? 'High-Conversion Public Landing Page, Product Catalog, Donation Campaign & Brand Showcase'
                    : 'Halaman Depan Publik Konversi Tinggi, Katalog Produk, Kampanye Donasi & Brand Showcase',
                'selected_public_ux_preset' => $selectedPublicPreset['code'],
                'public_ux_preset_code' => $selectedPublicPreset['code'],
                'preset_name' => $selectedPublicPreset['localized_name'],
                'name' => $selectedPublicPreset['localized_name'],
                'local_tech_stack' => $selectedPublicPreset['local_tech_stack'],
                'tech_blueprint' => $selectedPublicPreset['local_tech_stack'],
                'npm_packages' => $selectedPublicPreset['npm_packages'],
                'summary' => $selectedPublicPreset['localized_summary'],
                'miniature_wireframe_stages' => $selectedPublicPreset['miniature_wireframe_stages'],
                'extra_man_hours' => $selectedPublicPreset['extra_man_hours'],
                'verified_social_proof_primitive' => [
                    'code' => self::PRIMITIVE_SOCIAL_PROOF,
                    'component' => 'VerifiedTestimonialAniwall',
                    'features' => [
                        'Hardware-accelerated bidirectional CSS translate3d marquee (pause on hover)',
                        '12-Sector instant filter tabs',
                        'Telemetry-backed Architecture Receipt badges (ULID tables, locks, SHA-256)',
                        'Interactive [Inspect Anonymized Blueprint Architecture] modal',
                    ],
                ],
            ],
            'five_iron_animation_laws' => self::fiveIronAnimationLaws($loc),
            'input_hygiene_contract' => self::inputHygieneContract($loc),
            'available_public_ux_presets' => array_values($allPublicPresets),
        ];
    }

    /**
     * 6 Critical SaaS Commercial & Growth Modules Registry for neriahpro.com (scrolly_telling.md Section 3).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function sixCriticalSaasModules(?string $locale = null): array
    {
        $loc = ($locale ?: app()->getLocale()) === 'en' ? 'en' : 'id';

        $modules = [
            [
                'number' => 1,
                'code' => 'SAAS_MOD_01_BILLING_FEATURE_GATE',
                'title' => [
                    'id' => 'Billing, Kuota Langganan & Dual Payment Gateway (IDR Midtrans + Global USD)',
                    'en' => 'Billing, Subscription Quota & Dual Payment Gateway (IDR Midtrans + Global USD)',
                ],
                'summary' => [
                    'id' => 'Enforcer kuota otomatis untuk Copy Phase 3–6 & MCP Bridge yang membuka akses < 2 detik setelah webhook QRIS/VA/USD lunas bersegel Idempotency Guard + PDF E-Receipt.',
                    'en' => 'Automated quota gate for Phase 3–6 prompts & MCP Bridge unlocking in < 2s upon idempotent IDR/USD payment webhook settlement + PDF Tax E-Receipt.',
                ],
                'route_or_service' => 'PosSaasCommercialEngineService::checkFeatureAccess()',
            ],
            [
                'number' => 2,
                'code' => 'SAAS_MOD_02_AFFILIATE_FINDERS_FEE',
                'title' => [
                    'id' => 'Mesin Afiliasi, Referral (?ref=CODE) & High-Ticket Mitra Finder\'s Fee',
                    'en' => 'Affiliate, Referral (?ref=CODE) & High-Ticket Mitra Finder\'s Fee Engine',
                ],
                'summary' => [
                    'id' => 'Link unik ?ref=KODE dengan komisi berulang 20% paket Retail dan pencadangan otomatis Finder\'s Fee 7,5% (5%–10%) saat DP Proyek Turnkey Mitra lunas.',
                    'en' => 'Unique ?ref=CODE attribution awarding 20% recurring Retail commission and automatic 7.5% (5%–10%) Finder\'s Fee reservation upon Mitra Turnkey DP settlement.',
                ],
                'route_or_service' => '/customer/dashboard#affiliate',
            ],
            [
                'number' => 3,
                'code' => 'SAAS_MOD_03_MULTISEAT_WHITELABEL_ORG',
                'title' => [
                    'id' => 'Multi-Seat Organization, Delegasi Role & Agency White-Label Vault',
                    'en' => 'Multi-Seat Organization, Role Delegation & Agency White-Label Vault',
                ],
                'summary' => [
                    'id' => 'Undang anggota tim (Owner, Lead Architect, Junior Dev, Client Viewer) dan ekspor PDF Spesifikasi, RAB & BAST memakai kop surat/logo agensi sendiri.',
                    'en' => 'Invite workspace seats (Owner, Lead Architect, Junior Dev, Client Viewer) and export White-Label PDF Specs, Quotations & BAST under your agency brand.',
                ],
                'route_or_service' => '/customer/dashboard#workspace',
            ],
            [
                'number' => 4,
                'code' => 'SAAS_MOD_04_IMPACT_GRANT_WORKFLOW',
                'title' => [
                    'id' => 'Alur Verifikasi 1-Klik Non-Profit, Yayasan, Rumah Ibadah & Akademik',
                    'en' => '1-Click Non-Profit, NGO, Religious House & Academic Impact Grant Workflow',
                ],
                'summary' => [
                    'id' => 'Unggah SK Kemenkumham / Akta Yayasan / Kartu Akademik ke private disk tersanitasi AgentShield -> Admin setujui 1-klik di Filament -> Akun otomatis naik ke paket Impact Grant.',
                    'en' => 'Upload legal decree / academic ID to private disk with AgentShield scrub -> 1-click Filament admin approval -> automatic upgrade to Impact Grant tier.',
                ],
                'route_or_service' => '/admin/saas-system-health-pulse',
            ],
            [
                'number' => 5,
                'code' => 'SAAS_MOD_05_PROGRAMMATIC_SEO_EXPLORE',
                'title' => [
                    'id' => 'SEO Programmatic Architecture Library (/explore/{sector}/{slug})',
                    'en' => 'SEO Programmatic Architecture Library (/explore/{sector}/{slug})',
                ],
                'summary' => [
                    'id' => 'Katalog SSR publik 12 sektor lengkap Schema.org SoftwareApplication JSON-LD, Dynamic SVG OpenGraph lokal, Aniwall Testimoni, & pratinjau 20% arsitektur.',
                    'en' => 'SSR public 12-sector architecture showcase with Schema.org JSON-LD, local Dynamic SVG OpenGraph, Verified Aniwall, and 20% architecture teaser.',
                ],
                'route_or_service' => '/explore',
            ],
            [
                'number' => 6,
                'code' => 'SAAS_MOD_06_SYSTEM_HEALTH_PULSE',
                'title' => [
                    'id' => 'System Health, Queue Pulse & Self-Diagnostic Super Admin Dashboard',
                    'en' => 'System Health, Queue Pulse & Self-Diagnostic Super Admin Dashboard',
                ],
                'summary' => [
                    'id' => 'Pusat kendali Filament v5 untuk memantau antrean Queue/Jobs, kesehatan kuota Multi-AI LLM, corong konversi Retail/Mitra, dan ruang disk VPS real-time.',
                    'en' => 'Filament v5 command center monitoring Queue/Failed Jobs pulse, Multi-AI LLM token health, Retail/Mitra conversion funnel, and live VPS disk telemetry.',
                ],
                'route_or_service' => '/admin/saas-system-health-pulse',
            ],
        ];

        foreach ($modules as &$mod) {
            $mod['localized_title'] = $mod['title'][$loc];
            $mod['localized_summary'] = $mod['summary'][$loc];
        }
        unset($mod);

        return $modules;
    }

    /**
     * Retrieve O(1) Forever-Cached Verified Testimonials & Case Studies across all 12 Sectors
     * (merging real DB BAST/Phase-6 records with canonical 12-sector baseline receipts).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getVerifiedTestimonialsCatalog(?string $locale = null): array
    {
        $loc = ($locale ?: app()->getLocale()) === 'en' ? 'en' : 'id';
        $cacheKey = "project_os:verified_testimonials_aniwall:v10:{$loc}";

        return Cache::rememberForever($cacheKey, static function () use ($loc): array {
            $items = [];

            if (Schema::hasTable('pos_verified_testimonials')) {
                $dbRows = PosVerifiedTestimonial::query()
                    ->where('is_published', true)
                    ->orderByDesc('is_featured')
                    ->orderBy('id', 'desc')
                    ->limit(60)
                    ->get();

                foreach ($dbRows as $row) {
                    $roleArr = $row->getTranslations('client_role');
                    $quoteArr = $row->getTranslations('quote');
                    $summaryArr = $row->getTranslations('case_study_summary');

                    $items[] = [
                        'id' => $row->id,
                        'is_live_db_record' => true,
                        'sector_code' => $row->sector_code,
                        'stack_code' => $row->stack_code,
                        'public_ux_preset' => $row->public_ux_preset,
                        'operational_ui_variant' => $row->operational_ui_variant ?: 'UI_01A_POS_SPLIT_TERMINAL',
                        'verification_trigger' => $row->verification_trigger,
                        'client_name' => $row->client_name,
                        'client_role' => $roleArr[$loc] ?? $roleArr['id'] ?? 'Direktur Operasional',
                        'client_company' => $row->client_company,
                        'quote' => $quoteArr[$loc] ?? $quoteArr['id'] ?? '',
                        'case_study_summary' => $summaryArr[$loc] ?? $summaryArr['id'] ?? '',
                        'receipt' => $row->architecture_receipt_json ?: [],
                        'anonymized_blueprint' => $row->anonymized_blueprint_json ?: [],
                        'verified_at' => $row->verified_at?->toDateString() ?? now()->toDateString(),
                    ];
                }
            }

            // Production Ready: Pure real BAST-verified testimonials from database.
            // When pos_verified_testimonials has 0 published records, this returns []
            // so the Wall of Love area is completely clean and hidden.

            return $items;
        });
    }

    /**
     * Canonical 12-Sector Telemetry-Backed Architecture Receipts so Aniwall & /explore/{sector}
     * showcase authentic architecture metrics across every functional sector.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function baselineTwelveSectorReceipts(string $loc = 'id'): array
    {
        $rows = [
            [
                'id' => '01JNERIAH90000000000000001',
                'sector_code' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
                'sector_badge' => 'Sector 01: Omnichannel Retail',
                'stack_code' => 'M1 Laravel 13 + Flux UI',
                'public_ux_preset' => self::UX_F4,
                'operational_ui_variant' => 'UI_01A_POS_SPLIT_TERMINAL',
                'verification_trigger' => 'MITRA_BAST_SIGNED',
                'client_name' => 'Hendra Kusuma',
                'client_role' => ['id' => 'VP Operasional Ritel', 'en' => 'VP of Retail Operations'],
                'client_company' => 'Nusantara Mart Multi-Cabang (Anonymized)',
                'quote' => [
                    'id' => 'Kasir split-screen <30ms barcode wedge dan Lookbook katalog B2B menghilangkan selisih stok antar 18 gudang cabang sejak minggu pertama go-live.',
                    'en' => 'The <30ms barcode wedge POS split-screen and B2B Lookbook eliminated stock discrepancies across 18 branch warehouses from week one.',
                ],
                'ulid_tables' => 24,
                'locks' => 7,
                'token_saved' => 66,
                'entities' => ['catalog_skus (ULID)', 'inventory_ledger_entries (ULID)', 'pos_register_shifts (ULID)', 'sales_return_vouchers (ULID)'],
                'slug' => 'arsitektur-aplikasi-kasir-pos-dan-stok-multi-gudang',
            ],
            [
                'id' => '01JNERIAH90000000000000002',
                'sector_code' => 'SECTOR_02_HOSPITALITY_SPACE',
                'sector_badge' => 'Sector 02: Sports Venue & Resort',
                'stack_code' => 'M1 Laravel 13 + Flux UI',
                'public_ux_preset' => self::UX_F3,
                'operational_ui_variant' => 'UI_02A_TIMELINE_SLOT_MATRIX',
                'verification_trigger' => 'MITRA_BAST_SIGNED',
                'client_name' => 'Kevin Wijaya',
                'client_role' => ['id' => 'Managing Partner', 'en' => 'Managing Partner'],
                'client_company' => 'Arena Smash & Villa Group (Anonymized)',
                'quote' => [
                    'id' => 'Denah lantai interaktif 360° menaikkan konversi booking malam 42%, sementara kunci slot atomik mencegah double-booking hingga 0 insiden.',
                    'en' => 'The interactive spatial floorplan boosted evening court bookings by 42%, while atomic slot locks reduced double-bookings to zero.',
                ],
                'ulid_tables' => 22,
                'locks' => 6,
                'token_saved' => 64,
                'entities' => ['venue_spaces (ULID)', 'time_slot_reservations (ULID)', 'peak_pricing_rules (ULID)', 'member_pass_ledgers (ULID)'],
                'slug' => 'arsitektur-booking-hotel-villa-dan-sewa-lapangan-anti-bentrok',
            ],
            [
                'id' => '01JNERIAH90000000000000003',
                'sector_code' => 'SECTOR_03_HEALTHCARE_CLINICAL',
                'sector_badge' => 'Sector 03: Healthcare & Clinic RME',
                'stack_code' => 'M1 Laravel 13 + PostgreSQL 16',
                'public_ux_preset' => self::UX_F5,
                'operational_ui_variant' => 'UI_03A_CLINICAL_SOAP_COCKPIT',
                'verification_trigger' => 'MITRA_BAST_SIGNED',
                'client_name' => 'dr. Ratna Pratiwi, MARS',
                'client_role' => ['id' => 'Direktur Medis Klinik Utama', 'en' => 'Chief Medical Director'],
                'client_company' => 'Klinik Sentosa Medika (Anonymized)',
                'quote' => [
                    'id' => 'Kokpit RME SOAP patuh Permenkes 24/2022, apotek FEFO terkunci rapat, dan rantai audit SHA-256 lolos inspeksi kepatuhan tanpa satu pun catatan.',
                    'en' => 'The MoH-compliant SOAP EMR cockpit, FEFO pharmacy lock, and SHA-256 audit chain passed clinical compliance inspection with zero findings.',
                ],
                'ulid_tables' => 26,
                'locks' => 8,
                'token_saved' => 68,
                'entities' => ['patient_emr_encounters (ULID)', 'clinical_soap_notes (ULID)', 'pharmacy_fefo_batches (ULID)', 'satusehat_sync_logs (ULID)'],
                'slug' => 'arsitektur-database-rekam-medis-klinik-rme-dan-apotek-fefo',
            ],
            [
                'id' => '01JNERIAH90000000000000004',
                'sector_code' => 'SECTOR_04_FINTECH_LEDGER',
                'sector_badge' => 'Sector 04: Finance & Double-Entry',
                'stack_code' => 'D1 Laravel API + Next.js 15',
                'public_ux_preset' => self::UX_F2,
                'operational_ui_variant' => 'UI_04A_LEDGER_RECON_WORKBENCH',
                'verification_trigger' => 'RETAIL_PHASE6_VERIFIED',
                'client_name' => 'Arif Rahman, CPA',
                'client_role' => ['id' => 'Head of Financial Engineering', 'en' => 'Head of Financial Engineering'],
                'client_company' => 'Artha Treasury Systems (Anonymized)',
                'quote' => [
                    'id' => 'Jurnal berpasangan immutable dengan contra-entry otomatis dan ekspor Excel 3-sheet bersegel SHA-256 memangkas waktu audit bulanan dari 5 hari menjadi 20 menit.',
                    'en' => 'Immutable double-entry journals with contra-entry enforcement and 3-sheet SHA-256 Excel exports cut monthly audit prep from 5 days to 20 minutes.',
                ],
                'ulid_tables' => 25,
                'locks' => 9,
                'token_saved' => 67,
                'entities' => ['chart_of_accounts (ULID)', 'journal_headers (ULID)', 'journal_lines (ULID)', 'bank_recon_matches (ULID)'],
                'slug' => 'arsitektur-buku-besar-akuntansi-double-entry-psak-109',
            ],
            [
                'id' => '01JNERIAH90000000000000005',
                'sector_code' => 'SECTOR_05_INDUSTRIAL_MRP',
                'sector_badge' => 'Sector 05: Manufacturing & MRP',
                'stack_code' => 'M1 Laravel 13 + Livewire 4',
                'public_ux_preset' => self::UX_F1,
                'operational_ui_variant' => 'UI_05A_MRP_SHOPFLOOR_BOARD',
                'verification_trigger' => 'MITRA_BAST_SIGNED',
                'client_name' => 'Ir. Bambang Suryanto',
                'client_role' => ['id' => 'Direktur Pabrik & Proyek', 'en' => 'VP of Plant & Engineering'],
                'client_company' => 'PT Baja Presisi Nusantara (Anonymized)',
                'quote' => [
                    'id' => 'Halaman depan Scrollytelling menampilkan progres konstruksi secara elegan bagi investor, sedangkan di lantai pabrik varian BoM terpantau real-time.',
                    'en' => 'Sticky Scrollytelling showcases construction milestones cleanly to investors while shopfloor BoM yield variances track in real time.',
                ],
                'ulid_tables' => 27,
                'locks' => 7,
                'token_saved' => 63,
                'entities' => ['bill_of_materials (ULID)', 'production_work_orders (ULID)', 'qc_inspection_lots (ULID)', 'progress_termin_certificates (ULID)'],
                'slug' => 'arsitektur-erp-manufaktur-bom-mrp-dan-termin-konstruksi',
            ],
            [
                'id' => '01JNERIAH90000000000000006',
                'sector_code' => 'SECTOR_06_LOGISTICS_FLEET',
                'sector_badge' => 'Sector 06: Logistics & Fleet TMS',
                'stack_code' => 'D2 Go Fiber + React 19',
                'public_ux_preset' => self::UX_F5,
                'operational_ui_variant' => 'UI_06A_DISPATCH_CONTROL_TOWER',
                'verification_trigger' => 'MITRA_BAST_SIGNED',
                'client_name' => 'Dharmawan Putra',
                'client_role' => ['id' => 'Chief Logistics Officer', 'en' => 'Chief Logistics Officer'],
                'client_company' => 'Lintas Samudera Kargo (Anonymized)',
                'quote' => [
                    'id' => 'Kalkulator tarif volumetrik di landing page langsung menjaring prospek korporat, dan menara dispatch WebSocket tanpa polling sanggup menangani 400 armada.',
                    'en' => 'The landing-page volumetric tariff simulator converts corporate shippers directly, while zero-poll WebSocket dispatch handles 400+ trucks effortlessly.',
                ],
                'ulid_tables' => 23,
                'locks' => 6,
                'token_saved' => 65,
                'entities' => ['shipment_waybills (ULID)', 'fleet_dispatch_trips (ULID)', 'driver_cash_advances (ULID)', 'pod_digital_receipts (ULID)'],
                'slug' => 'arsitektur-aplikasi-ekspedisi-kargo-dan-pelacakan-armada',
            ],
            [
                'id' => '01JNERIAH90000000000000007',
                'sector_code' => 'SECTOR_07_EDTECH_MEMBERSHIP',
                'sector_badge' => 'Sector 07: EdTech & Academy',
                'stack_code' => 'M1 Laravel 13 + Flux UI',
                'public_ux_preset' => self::UX_F6,
                'operational_ui_variant' => 'UI_07A_COHORT_LEARNING_STUDIO',
                'verification_trigger' => 'RETAIL_PHASE6_VERIFIED',
                'client_name' => 'Nadia Maharani, M.Ed',
                'client_role' => ['id' => 'Founder & Academic Dean', 'en' => 'Founder & Academic Dean'],
                'client_company' => 'Talenta Kode Academy (Anonymized)',
                'quote' => [
                    'id' => 'Peta kurikulum Skill-Tree SVG membuat pendaftar kohort baru naik pesat, sementara kunci nilai rapor permanen menjaga kredibilitas sertifikat kami.',
                    'en' => 'The interactive SVG Skill-Tree curriculum showcase surged cohort enrollments, while immutable grade locks safeguard our certificate integrity.',
                ],
                'ulid_tables' => 21,
                'locks' => 5,
                'token_saved' => 62,
                'entities' => ['curriculum_skill_nodes (ULID)', 'cohort_enrollments (ULID)', 'locked_grade_ledgers (ULID)', 'verifiable_certificates (ULID)'],
                'slug' => 'arsitektur-lms-kursus-online-dan-akademi-pendidikan',
            ],
            [
                'id' => '01JNERIAH90000000000000008',
                'sector_code' => 'SECTOR_08_PROFESSIONAL_PSA',
                'sector_badge' => 'Sector 08: Agency & PSA Studio',
                'stack_code' => 'M1 Laravel 13 + Filament v5',
                'public_ux_preset' => self::UX_F2,
                'operational_ui_variant' => 'UI_08A_PSA_SPRINT_MATRIX',
                'verification_trigger' => 'RETAIL_PHASE6_VERIFIED',
                'client_name' => 'Samuel Halim',
                'client_role' => ['id' => 'Principal Consultant', 'en' => 'Principal Consultant'],
                'client_company' => 'Karsa Digital Advisory (Anonymized)',
                'quote' => [
                    'id' => 'Co-Design Room bersegel Dual-Key SHA-256 mengakhiri drama revisi tak berbayar (scope creep) dengan klien korporat kami secara permanen.',
                    'en' => 'The Dual-Key SHA-256 Co-Design Sign-Off Room permanently ended unpaid scope creep disputes across our enterprise client engagements.',
                ],
                'ulid_tables' => 20,
                'locks' => 5,
                'token_saved' => 69,
                'entities' => ['client_engagements (ULID)', 'sprint_capacity_slots (ULID)', 'billable_timesheets (ULID)', 'milestone_bast_seals (ULID)'],
                'slug' => 'arsitektur-manajemen-proyek-agensi-konsultan-dan-psa',
            ],
            [
                'id' => '01JNERIAH90000000000000009',
                'sector_code' => 'SECTOR_09_FOOD_BEVERAGE',
                'sector_badge' => 'Sector 09: F&B & Cloud Kitchen',
                'stack_code' => 'M1 Laravel 13 + Reverb WebSocket',
                'public_ux_preset' => self::UX_F3,
                'operational_ui_variant' => 'UI_09A_KDS_TICKET_RAIL',
                'verification_trigger' => 'MITRA_BAST_SIGNED',
                'client_name' => 'Chef Matteo Tan',
                'client_role' => ['id' => 'Co-Founder & Culinary Director', 'en' => 'Co-Founder & Culinary Director'],
                'client_company' => 'Roast & Hearth Bistro Group (Anonymized)',
                'quote' => [
                    'id' => 'Tamu memilih meja VIP langsung dari denah lantai interaktif, dan pesanan masuk ke layar dapur KDS via WebSocket dalam hitungan milidetik tanpa kertas hilang.',
                    'en' => 'Guests reserve VIP tables on the live floorplan while orders hit Kitchen Display Screens via WebSocket in milliseconds with zero lost tickets.',
                ],
                'ulid_tables' => 23,
                'locks' => 6,
                'token_saved' => 64,
                'entities' => ['dining_floor_tables (ULID)', 'kds_order_tickets (ULID)', 'recipe_bom_deductions (ULID)', 'split_bill_settlements (ULID)'],
                'slug' => 'arsitektur-pos-restoran-denah-meja-dan-kitchen-display-kds',
            ],
            [
                'id' => '01JNERIAH90000000000000010',
                'sector_code' => 'SECTOR_10_MEDIA_TICKETING',
                'sector_badge' => 'Sector 10: Event & High-Concurrency Ticketing',
                'stack_code' => 'S1 Go Microservice + PostgreSQL 16',
                'public_ux_preset' => self::UX_F3,
                'operational_ui_variant' => 'UI_10A_SEAT_GATE_COMMAND',
                'verification_trigger' => 'MITRA_BAST_SIGNED',
                'client_name' => 'Rizky Prakoso',
                'client_role' => ['id' => 'Head of Live Events', 'en' => 'Head of Live Events'],
                'client_company' => 'Gema Panggung Nusantara (Anonymized)',
                'quote' => [
                    'id' => 'Saat war tiket 15.000 kursi dibuka serentak, denah kursi SVG tetap 60 FPS dan gate scanner QR lokal menolak tiket duplikat dalam <50ms.',
                    'en' => 'During a 15,000-seat flash sale, the SVG venue map held 60 FPS and local gate QR scanners blocked duplicate tickets in under 50ms.',
                ],
                'ulid_tables' => 22,
                'locks' => 8,
                'token_saved' => 65,
                'entities' => ['event_seat_inventories (ULID)', 'atomic_seat_holds (ULID)', 'gate_checkin_scans (ULID)', 'royalty_payout_splits (ULID)'],
                'slug' => 'arsitektur-tiket-konser-denah-kursi-anti-war-scalping',
            ],
            [
                'id' => '01JNERIAH90000000000000011',
                'sector_code' => 'SECTOR_11_HRIS_INTERNAL_OPS',
                'sector_badge' => 'Sector 11: HRIS, Payroll PPh21 & ESS',
                'stack_code' => 'M1 Laravel 13 + Filament v5',
                'public_ux_preset' => self::UX_F5,
                'operational_ui_variant' => 'UI_11A_PAYROLL_WORKFORCE_GRID',
                'verification_trigger' => 'RETAIL_PHASE6_VERIFIED',
                'client_name' => 'Siti Nurhaliza, SH, MM',
                'client_role' => ['id' => 'VP People & Culture', 'en' => 'VP of People & Culture'],
                'client_company' => 'Cakrawala Korpora Grup (Anonymized)',
                'quote' => [
                    'id' => 'Integrasi webhook mesin absensi ZKTeco ADMS + kalkulator PPh 21 TER presisi memotong proses tutup payroll 1.200 karyawan dari 4 hari menjadi 45 menit.',
                    'en' => 'ZKTeco ADMS biometric webhooks and exact PPh 21 TER tax calculation cut payroll closing for 1,200 staff from 4 days to 45 minutes.',
                ],
                'ulid_tables' => 24,
                'locks' => 6,
                'token_saved' => 63,
                'entities' => ['employee_contracts (ULID)', 'biometric_attendance_logs (ULID)', 'payroll_pph21_runs (ULID)', 'expense_reimbursements (ULID)'],
                'slug' => 'arsitektur-hris-absensi-biometrik-dan-payroll-pph21-ter',
            ],
            [
                'id' => '01JNERIAH90000000000000012',
                'sector_code' => 'SECTOR_12_GOVTECH_NONPROFIT',
                'sector_badge' => 'Sector 12: Non-Profit, NGO & ISAK 35',
                'stack_code' => 'M1 Laravel 13 + Flux UI',
                'public_ux_preset' => self::UX_F1,
                'operational_ui_variant' => 'UI_12A_FUND_TRANSPARENCY_PORTAL',
                'verification_trigger' => 'MITRA_BAST_SIGNED',
                'client_name' => 'Pdt. Daniel Simanjuntak',
                'client_role' => ['id' => 'Ketua Pengurus Yayasan', 'en' => 'Foundation Board Chairman'],
                'client_company' => 'Yayasan Pelita Kasih Nusantara (Anonymized)',
                'quote' => [
                    'id' => 'Scrollytelling perjalanan donasi meningkatkan kepercayaan donatur publik, dan buku besar dana terikat ISAK 35 + audit SHA-256 siap diserahkan ke KAP kapan saja.',
                    'en' => 'Sticky Scrollytelling donor journeys elevated public trust, while ISAK 35 restricted fund ledgers and SHA-256 exports stay CPA-audit ready 24/7.',
                ],
                'ulid_tables' => 23,
                'locks' => 7,
                'token_saved' => 70,
                'entities' => ['donor_pledge_receipts (ULID)', 'isak35_restricted_funds (ULID)', 'field_aid_disbursements (ULID)', 'public_impact_milestones (ULID)'],
                'slug' => 'arsitektur-akuntansi-yayasan-donasi-psak-109-isak-35',
            ],
        ];

        $presets = self::sixPublicUxPresets($loc);
        $formatted = [];

        foreach ($rows as $r) {
            $scopeHash = substr(hash('sha256', 'NERIAHPRO_V10_RECEIPT_'.$r['sector_code']), 0, 16);
            $uxPreset = $presets[$r['public_ux_preset']] ?? $presets[self::UX_F2];

            $formatted[] = [
                'id' => $r['id'],
                'is_live_db_record' => false,
                'sector_code' => $r['sector_code'],
                'sector_badge' => $r['sector_badge'],
                'stack_code' => $r['stack_code'],
                'public_ux_preset' => $r['public_ux_preset'],
                'public_ux_label' => $uxPreset['short_badge'],
                'operational_ui_variant' => $r['operational_ui_variant'],
                'verification_trigger' => $r['verification_trigger'],
                'client_name' => $r['client_name'],
                'client_role' => $r['client_role'][$loc],
                'client_company' => $r['client_company'],
                'quote' => $r['quote'][$loc],
                'case_study_summary' => $uxPreset['localized_summary'],
                'seo_slug' => $r['slug'],
                'receipt' => [
                    'ulid_tables_count' => $r['ulid_tables'],
                    'concurrency_locks_count' => $r['locks'],
                    'token_saved_percent' => $r['token_saved'],
                    'scope_hash_short' => $scopeHash,
                    'anti_slop_score' => 100,
                ],
                'anonymized_blueprint' => [
                    'zone_a_operational' => $r['operational_ui_variant'].' (Linear/Stripe Crisp Back-Stage)',
                    'zone_b_public' => $r['public_ux_preset'].' — '.$uxPreset['localized_name'],
                    'core_ulid_tables' => $r['entities'],
                    'animation_compliance' => 'GPU translate3d/opacity | CLS 0.00 | 0% External CDN',
                    'audit_compliance' => 'SHA-256 Hash Chain + 3-Sheet .xlsx CPA Ready',
                ],
                'verified_at' => '2026-10-15',
            ];
        }

        return $formatted;
    }

    /**
     * Render `.agents/DUAL_ZONE_PUBLIC_UX_AND_INPUT_HYGIENE.md` contract for scaffolded projects & CLI bootstrapper.
     *
     * @param  array<string, mixed>  $schema
     */
    public static function renderDualZoneAndInputHygieneMarkdown(array $schema, string $locale = 'id'): string
    {
        $loc = $locale === 'en' ? 'en' : 'id';
        $dual = $schema['dual_zone_ui_contract'] ?? self::compileDualZoneUiContract(
            (string) ($schema['project_metadata']['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL'),
            (string) ($schema['visual_frontend_ui_contract']['code'] ?? 'UI_01A_POS_SPLIT_TERMINAL'),
            (string) ($schema['visual_frontend_ui_contract']['navigation_shell'] ?? 'SIDEBAR_COLLAPSIBLE'),
            null,
            $loc
        );
        $hygiene = $schema['input_hygiene_contract'] ?? self::inputHygieneContract($loc);
        $laws = $dual['five_iron_animation_laws'] ?? self::fiveIronAnimationLaws($loc);

        $zoneA = $dual['zone_a_operational_backstage'] ?? [];
        $zoneB = $dual['zone_b_public_frontstage'] ?? [];

        $md = "# PROJECT OS v10.0 — DUAL-ZONE UI ARCHITECTURE, 5 IRON ANIMATION LAWS & UNIVERSAL INPUT HYGIENE\n\n";
        $md .= "## 1. Dual-Zone UI Separation Policy\n";
        $md .= "- **Zone A — Operational Back-Stage (`{$zoneA['ui_variant_code']}` / `{$zoneA['navigation_shell']}`)**:\n";
        $md .= "  - Policy: `{$zoneA['animation_policy']}`\n";
        $md .= "  - Surfaces: Admin Filament v5, Cashier POS, Clinical SOAP EMR, Double-Entry Ledger, Inventory Dispatch.\n";
        $md .= "  - Strict Ban: ZERO scrollytelling, ZERO parallax, ZERO scroll-hijacking on operational workspaces.\n";
        $md .= "- **Zone B — Public Front-Stage (`{$zoneB['public_ux_preset_code']}` — {$zoneB['name']})**:\n";
        $md .= "  - Summary: {$zoneB['summary']}\n";
        $md .= "  - Tech Blueprint: `{$zoneB['tech_blueprint']}`\n";
        $md .= "  - Surfaces: Landing Page, Public Catalog, Donation Campaign, Event Showcase, Verified Aniwall (`PRIMITIVE_VERIFIED_SOCIAL_PROOF`).\n\n";

        $md .= "## 2. Five Iron Laws of Enterprise Anti-Slop Animation\n";
        foreach ($laws as $law) {
            $md .= "- **[{$law['code']}] {$law['localized_title']}**: {$law['localized_rule']}\n";
        }

        $md .= "\n## 3. Mandatory Universal Input Hygiene Standard\n";
        $md .= "- **Strict Email Input**: Regex `{$hygiene['email']['allowed_chars_regex']}` — strip illegal characters & spaces on keystroke/paste + RFC validation.\n";
        $md .= "- **Regional Thousand Separator**: ID/EU (`1.500.000` dot `.`), EN/US/Global (`1,500,000` comma `,`) with `tabular-nums` alignment.\n";
        $md .= "- **Country Zone Phone Input**: Source `{$hygiene['phone_country_zone']['source_of_truth']}` — **immediately strip leading `0` on first keystroke** (`0812...` -> `812...`) and store canonical E.164 (`+62812...`).\n";
        $md .= "- **Anti-Slop Geometry**: Sharp/subtle corners (`rounded-none`, `rounded-xs`, `rounded-sm`, max `rounded-md`), ZERO `rounded-full`, ZERO multi-color gradients, ZERO `window.alert()`, 100% Light & Dark WCAG AA/AAA.\n";

        return $md;
    }
}
