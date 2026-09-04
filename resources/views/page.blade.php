<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $page->title[app()->getLocale()] ?? $page->title['en'] ?? (is_string($page->title) ? $page->title : 'Neriah Pro // Digital Services Hub') }}</title>
    <meta name="description" content="{{ $page->meta_description[app()->getLocale()] ?? $page->meta_description['en'] ?? (is_string($page->meta_description) ? $page->meta_description : 'Pusat arsitektur dan rekayasa perangkat lunak berskala tinggi.') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Vite React and CSS -->
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/islands.jsx'])

    <script>
        // Init theme before DOM paint to prevent flash
        if (localStorage.getItem('neriah_theme') === 'dark' || (!localStorage.getItem('neriah_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased overflow-x-hidden min-h-screen transition-colors duration-200">
    
    <!-- Global Navigation -->
    @react('GlobalNavigationIsland', ['settings' => $globalSettings['main_navigation']->value ?? null])

    <!-- Breadcrumb (Dynamic, hidden on home) -->
    @php
        $breadcrumbPaths = [
            ['label' => 'Home', 'url' => '/'],
        ];
        if($page->slug !== 'home') {
            $title = $page->title[app()->getLocale()] ?? $page->title['en'] ?? (is_string($page->title) ? $page->title : 'Neriah Pro');
            $breadcrumbPaths[] = ['label' => $title, 'url' => '/' . $page->slug];
        }
    @endphp
    @react('BreadcrumbIsland', ['paths' => $breadcrumbPaths])

    <main>
        @foreach($page->plugins ?? [] as $plugin)
            @php
                // Cast array to object if necessary
                $plugin = is_array($plugin) ? (object) $plugin : $plugin;
            @endphp
            @if($plugin->is_active ?? true)
                @php
                    $pluginName = '';
                    $type = $plugin->plugin_type ?? $plugin->type ?? '';
                    if($type == 'hero_section') $pluginName = 'HeroIsland';
                    if($type == 'feature_grid') $pluginName = 'ProductGridIsland';
                    if($type == 'onboarding_form') $pluginName = 'ClientOnboardingIsland';
                @endphp
                
                @if($pluginName)
                    @react($pluginName, (array) ($plugin->content_data ?? $plugin->data ?? []))
                @endif
            @endif
        @endforeach
    </main>

    <!-- Global Footer -->
    @react('FooterIsland', ['settings' => $globalSettings['footer_links']->value ?? null])

</body>
</html>
