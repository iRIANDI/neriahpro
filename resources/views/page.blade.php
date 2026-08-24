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
        @foreach($page->plugins as $plugin)
            @if($plugin->is_active)
                @php
                    $pluginName = '';
                    if($plugin->plugin_type == 'hero_section') $pluginName = 'HeroIsland';
                    if($plugin->plugin_type == 'onboarding_form') $pluginName = 'ClientOnboardingIsland';
                    if($plugin->plugin_type == 'feature_grid') $pluginName = 'ProductGridIsland';
                    // ... other mappings
                @endphp
                
                @if($pluginName)
                    @react($pluginName, $plugin->content_data)
                @endif
            @endif
        @endforeach
    </main>

    <!-- Global Footer -->
    @react('FooterIsland', ['settings' => $globalSettings['footer_links']->value ?? null])

</body>
</html>
