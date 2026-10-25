@php
    $currentLang = $locale ?? (request()->cookie('neriah_locale', app()->getLocale()) === 'en' ? 'en' : 'id');
    $isEn = $currentLang === 'en';
    $title = is_array($policy->title) ? ($policy->title[$currentLang] ?? $policy->title['id'] ?? 'Kebijakan Layanan') : $policy->title;
    $content = is_array($policy->content) ? ($policy->content[$currentLang] ?? $policy->content['id'] ?? '') : $policy->content;
    $companyName = \App\Models\CmsGlobalSetting::getVal('company_name', 'Neriah Pro Digital Services');
    $supportEmail = \App\Models\CmsGlobalSetting::getVal('developer_support_email', 'support@neriahpro.com');
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLang }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Neriah Pro</title>
    <meta name="description" content="{{ strip_tags(substr($content, 0, 160)) }}">

    <!-- Schema.org JSON-LD Structured Data -->
    {!! \App\Services\Seo\SchemaOrgService::render([
        \App\Services\Seo\SchemaOrgService::organization(),
        \App\Services\Seo\SchemaOrgService::breadcrumbs([
            ($isEn ? 'Home' : 'Beranda') => url('/'),
            $title => request()->url(),
        ]),
    ]) !!}

    <link rel="stylesheet" href="{{ asset('fonts/instrument-sans/instrument-sans.css') }}">
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.getItem('neriah_theme') === 'dark' || (!localStorage.getItem('neriah_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-emerald-500 selection:text-black">

    <!-- Global Navigation -->
    <x-global-navigation :global-settings="$globalSettings" />

    <!-- Main Content Container -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 sm:py-12 flex-1 w-full">
        
        <!-- Breadcrumb & Tag -->
        <div class="mb-6 flex items-center gap-2 font-mono text-xs text-zinc-500">
            <a href="/" class="hover:text-emerald-500">{{ $isEn ? 'Home' : 'Beranda' }}</a>
            <span>/</span>
            <span class="text-zinc-900 dark:text-zinc-100 font-bold">{{ $title }}</span>
        </div>

        <!-- Policy Navigation Tabs -->
        <div class="flex flex-wrap gap-2 mb-8 border-b border-zinc-200 dark:border-zinc-800 pb-4">
            <a 
                href="/terms?lang={{ $currentLang }}" 
                class="px-3 py-1.5 font-mono text-xs font-bold border transition {{ $resolvedType === 'terms_and_conditions' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500' }}"
            >
                {{ $isEn ? 'Terms of Service' : 'Syarat & Ketentuan' }}
            </a>
            <a 
                href="/privacy-policy?lang={{ $currentLang }}" 
                class="px-3 py-1.5 font-mono text-xs font-bold border transition {{ $resolvedType === 'privacy_policy' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500' }}"
            >
                {{ $isEn ? 'Privacy Policy' : 'Kebijakan Privasi' }}
            </a>
            <a 
                href="/refund-policy?lang={{ $currentLang }}" 
                class="px-3 py-1.5 font-mono text-xs font-bold border transition {{ $resolvedType === 'refund_policy' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500' }}"
            >
                {{ $isEn ? 'Refund & Warranty Policy' : 'Garansi & Pengembalian Dana' }}
            </a>
            <a 
                href="/smart-guide?lang={{ $currentLang }}" 
                class="px-3 py-1.5 font-mono text-xs font-bold border border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500 hover:text-black transition"
            >
                {{ $isEn ? 'Smart Client Guide' : 'Panduan Pintar & Hak Klien' }}
            </a>
        </div>

        <!-- Article Card -->
        <article class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-10 shadow-xs rounded-none">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-2.5 h-2.5 bg-emerald-500"></span>
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                    NERIAH PRO // OFFICIAL COMPLIANCE &bull; {{ strtoupper($currentLang) }}
                </span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2">
                {{ $title }}
            </h1>
            <div class="text-xs font-mono text-zinc-500 mb-8 pb-4 border-b border-zinc-200 dark:border-zinc-800">
                {{ $isEn ? 'Last Updated:' : 'Terakhir Diperbarui:' }} <strong>{{ optional($policy->updated_at)->format('d F Y') ?: date('d F Y') }}</strong> &bull; {{ $isEn ? 'Published by' : 'Diterbitkan oleh' }} {{ $companyName }}
            </div>

            <!-- Rich Body Content -->
            <div class="prose prose-zinc dark:prose-invert max-w-none text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed space-y-4">
                {!! $content !!}
            </div>

            <!-- Compliance Footer Notice -->
            <div class="mt-10 pt-6 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 p-4 font-mono text-xs text-zinc-500 space-y-1.5">
                <div class="font-bold text-zinc-800 dark:text-zinc-200 uppercase">
                    {{ $isEn ? 'OFFICIAL CONTACT & COMPLIANCE SUPPORT:' : 'INFORMASI KONTAK RESMI & DUKUNGAN:' }}
                </div>
                <p>
                    {{ $isEn 
                        ? 'If you have any questions regarding our terms, privacy practices, or warranty claims, please contact our legal team via WhatsApp or email to: ' 
                        : 'Apabila Anda memiliki pertanyaan mengenai syarat layanan, kebijakan privasi, atau klaim garansi, hubungi tim legal kami melalui WhatsApp resmi atau email ke: ' 
                    }}
                    <a href="mailto:{{ $supportEmail }}" class="text-emerald-600 dark:text-emerald-400 font-bold underline">{{ $supportEmail }}</a>.
                </p>
            </div>
        </article>

    </main>

    @php
        $getSettingVal = function($key, $default = null) use (&$globalSettings) {
            try {
                if (!isset($globalSettings[$key])) return $default;
                $item = $globalSettings[$key];
                if ($item instanceof \__PHP_Incomplete_Class) return $default;
                if (is_object($item)) return $item->value ?? $default;
                if (is_array($item)) return $item['value'] ?? $default;
                return $default;
            } catch (\Throwable) {
                return $default;
            }
        };

        $featureFlags = [
            'enable_cv_pro' => (bool) $getSettingVal('feature_enable_cv_pro', true),
            'pricing_show_cv_tab' => (bool) $getSettingVal('pricing_show_cv_tab', false),
            'enable_pricing' => (bool) $getSettingVal('feature_enable_cv_pricing', true),
            'enable_job_hub' => (bool) $getSettingVal('feature_enable_cv_job_hub', true),
            'enable_keuangan' => (bool) $getSettingVal('feature_enable_cv_keuangan', true),
            'enable_mock_interview' => (bool) $getSettingVal('feature_enable_cv_mock_interview', true),
            'enable_linkedin_suite' => (bool) $getSettingVal('feature_enable_cv_linkedin_suite', true),
            'enable_vision_blueprint' => (bool) $getSettingVal('feature_enable_vision_blueprint', true),
            'enable_client_onboarding' => (bool) $getSettingVal('feature_enable_client_onboarding', true),
            'enable_digital_contract' => (bool) $getSettingVal('feature_enable_digital_contract', true),
            'midtrans_mode' => (bool) $getSettingVal('midtrans_compliance_strict_mode', true),
        ];
    @endphp

    <!-- Global Footer Island -->
    @react('FooterIsland', [
        'settings' => $getSettingVal('footer_links', null),
        'featureFlags' => $featureFlags,
        'whatsappNumber' => $getSettingVal('company_whatsapp', '6281380448253'),
        'supportEmail' => $supportEmail,
        'companyAddress' => $getSettingVal('company_address', 'Gianyar, Bali, Indonesia'),
        'ownerName' => $getSettingVal('company_owner_name', 'Yoseph Iriandi'),
        'ownerTitle' => $getSettingVal('company_owner_title', 'Independent Full-Stack Developer & Software Architect'),
        'currentLocale' => $currentLang,
    ])

</body>
</html>
