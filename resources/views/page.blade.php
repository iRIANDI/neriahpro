<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-black text-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>{{ json_decode($page->title)->en ?? 'Neriah Pro' }}</title>
    <meta name="description" content="{{ json_decode($page->meta_description)->en ?? '' }}">
    
    <!-- Vite React and CSS -->
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/islands.jsx'])
</head>
<body class="font-sans antialiased overflow-x-hidden">
    
    <!-- Global Navigation -->
    @react('GlobalNavigationIsland', ['settings' => $globalSettings['main_navigation']->value ?? null])

    <!-- Breadcrumb (Dynamic) -->
    @php
        $breadcrumbPaths = [
            ['label' => 'Home', 'url' => '/'],
        ];
        if($page->slug !== 'home') {
            $title = json_decode($page->title)->en ?? $page->title;
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
                    if($type == 'onboarding_form') $pluginName = 'ClientOnboardingIsland';
                    if($type == 'feature_grid') $pluginName = 'ProductGridIsland';
                    // ... other mappings
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
