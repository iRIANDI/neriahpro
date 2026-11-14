<?php

use App\Models\CmsGlobalSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Remove standalone Project OS (PRD) and duplicate flat Services Hub entries
     * from cached global navigation settings so Services Hub and Pricing submenus
     * organize Project OS and CV Pro cleanly without clutter.
     */
    public function up(): void
    {
        $keys = ['navigation_format', 'main_navigation', 'navigation_links'];

        foreach ($keys as $key) {
            $setting = CmsGlobalSetting::where('key', $key)->first();
            if (! $setting || ! is_array($setting->value)) {
                continue;
            }

            $value = $setting->value;
            $changed = false;

            if (isset($value[0]['data']['links']) && is_array($value[0]['data']['links'])) {
                $filtered = $this->filterLegacyNavLinks($value[0]['data']['links']);
                if ($filtered !== $value[0]['data']['links']) {
                    $value[0]['data']['links'] = $filtered;
                    $changed = true;
                }
            } elseif (isset($value[0]['links']) && is_array($value[0]['links'])) {
                $filtered = $this->filterLegacyNavLinks($value[0]['links']);
                if ($filtered !== $value[0]['links']) {
                    $value[0]['links'] = $filtered;
                    $changed = true;
                }
            }

            if ($changed) {
                $setting->value = $value;
                $setting->save();
            }
        }

        // Ensure pricing_show_cv_tab defaults to enabled alongside feature_enable_cv_pro
        if (! CmsGlobalSetting::where('key', 'pricing_show_cv_tab')->exists()) {
            CmsGlobalSetting::create([
                'key' => 'pricing_show_cv_tab',
                'value' => true,
            ]);
        }

        if (method_exists(CmsGlobalSetting::class, 'purgeRuntimeCache')) {
            CmsGlobalSetting::purgeRuntimeCache();
        }
    }

    /**
     * Filter redundant top-level Project OS & flat Services Hub links.
     *
     * @param  array<int, array<string, mixed>>  $links
     * @return array<int, array<string, mixed>>
     */
    protected function filterLegacyNavLinks(array $links): array
    {
        $filtered = array_values(array_filter($links, function ($item): bool {
            if (! is_array($item)) {
                return true;
            }

            $url = strtolower((string) ($item['url'] ?? $item['href'] ?? ''));
            $label = strtolower((string) ($item['label'] ?? $item['title'] ?? ''));

            if (str_contains($url, 'pricing#retail') || $url === '/blueprint' || str_contains($label, 'project os')) {
                return false;
            }

            if (str_contains($url, '#services') || str_contains($label, 'services hub') || str_contains($label, 'service hub') || str_contains($label, 'layanan hub')) {
                return false;
            }

            return true;
        }));

        return $filtered;
    }

    public function down(): void
    {
        // Intentionally left non-destructive to preserve customized navigation items.
    }
};

