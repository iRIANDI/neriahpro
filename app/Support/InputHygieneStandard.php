<?php

declare(strict_types=1);

namespace App\Support;

/**
 * InputHygieneStandard — Mandatory Enterprise Input Hygiene & Regional Formatting Engine.
 *
 * Enforces across neriahpro.com AND all generated 12-Sector Client Blueprints:
 * 1. Strict Email Character Filter: only RFC-valid email characters [a-zA-Z0-9._%+\-@], lowercase, zero spaces.
 * 2. Regional Thousand Separator: dot (.) for ID/DE/ES (1.500.000) and comma (,) for EN/US/SG/JP/CN (1,500,000).
 * 3. Country Zone Dialing Code (sourced directly from config/country_zones.php) + Instant Leading-Zero ('0') Stripper.
 */
class InputHygieneStandard
{
    /**
     * Load canonical country zones from config/country_zones.php (with fallback if empty).
     *
     * @return array<int, array{code: string, dial_code: string, name: string, flag: string}>
     */
    public static function countryZones(): array
    {
        $zones = config('country_zones.zones', []);
        if (is_array($zones) && ! empty($zones)) {
            return array_values($zones);
        }

        return [
            ['code' => 'ID', 'dial_code' => '+62', 'name' => 'Indonesia', 'flag' => '🇮🇩'],
            ['code' => 'SG', 'dial_code' => '+65', 'name' => 'Singapore', 'flag' => '🇸🇬'],
            ['code' => 'MY', 'dial_code' => '+60', 'name' => 'Malaysia', 'flag' => '🇲🇾'],
            ['code' => 'US', 'dial_code' => '+1', 'name' => 'United States', 'flag' => '🇺🇸'],
            ['code' => 'AU', 'dial_code' => '+61', 'name' => 'Australia', 'flag' => '🇦🇺'],
            ['code' => 'GB', 'dial_code' => '+44', 'name' => 'United Kingdom', 'flag' => '🇬🇧'],
            ['code' => 'JP', 'dial_code' => '+81', 'name' => 'Japan', 'flag' => '🇯🇵'],
        ];
    }

    /**
     * Associative options keyed by dial_code for searchable Filament / Blade selects:
     * ['+62' => '🇮🇩 Indonesia (+62)', ...]
     *
     * @return array<string, string>
     */
    public static function dialCodeSelectOptions(): array
    {
        $options = [];
        foreach (self::countryZones() as $zone) {
            $dial = (string) ($zone['dial_code'] ?? '+62');
            if (! isset($options[$dial])) {
                $options[$dial] = sprintf(
                    '%s %s (%s)',
                    $zone['flag'] ?? '🌐',
                    $zone['name'] ?? $zone['code'],
                    $dial
                );
            }
        }

        return $options;
    }

    /**
     * Format country_zones.php into the structure expected by React Islands:
     * [['name' => 'Indonesia', 'code' => '+62', 'dial_code' => '+62', 'emoji' => '🇮🇩', 'flag' => '🇮🇩', 'iso' => 'ID'], ...]
     *
     * @return array<int, array<string, string>>
     */
    public static function countryZonesForFrontend(): array
    {
        return array_map(static fn (array $z): array => [
            'iso' => (string) ($z['code'] ?? 'ID'),
            'code' => (string) ($z['dial_code'] ?? '+62'),
            'dial_code' => (string) ($z['dial_code'] ?? '+62'),
            'name' => (string) ($z['name'] ?? 'Indonesia'),
            'emoji' => (string) ($z['flag'] ?? '🌐'),
            'flag' => (string) ($z['flag'] ?? '🌐'),
        ], self::countryZones());
    }

    /**
     * Sanitize email string strictly to valid email characters only ([a-zA-Z0-9._%+\-@]).
     * Strips embedded HTML/script tags first so tag names do not leak into the local-part.
     */
    public static function sanitizeStrictEmail(?string $email): string
    {
        if ($email === null || $email === '') {
            return '';
        }

        $noTags = preg_replace('/<[^>]*>/', '', trim($email)) ?? '';
        $cleaned = preg_replace('/[^a-zA-Z0-9._%+\-@]/', '', $noTags) ?? '';

        return strtolower($cleaned);
    }

    /**
     * Validate strict RFC-like email format after character sanitization.
     */
    public static function isValidStrictEmail(?string $email): bool
    {
        $sanitized = self::sanitizeStrictEmail($email);
        if ($sanitized === '' || $sanitized !== strtolower(trim((string) $email))) {
            // Rejects strings that contained forbidden characters or whitespace
            if (preg_match('/[^a-zA-Z0-9._%+\-@]/', trim((string) $email))) {
                return false;
            }
        }

        return (bool) preg_match('/^[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$/', $sanitized);
    }

    /**
     * Normalize country dial code to E.164 prefix format (e.g. '62' -> '+62', '+1' -> '+1').
     */
    public static function normalizeDialCode(?string $dialCode): string
    {
        $digits = preg_replace('/\D/', '', (string) $dialCode) ?? '';

        return '+'.($digits !== '' ? $digits : '62');
    }

    /**
     * Sanitize local phone digits: strips all non-digits AND strips any leading '0' immediately
     * because the Country Zone dial code (e.g. +62) is already selected.
     */
    public static function sanitizePhoneLocalDigits(?string $rawPhone, ?string $dialCode = '+62'): string
    {
        if ($rawPhone === null || $rawPhone === '') {
            return '';
        }

        $digits = preg_replace('/\D/', '', $rawPhone) ?? '';
        $cleanDialDigits = preg_replace('/\D/', '', (string) $dialCode) ?? '';

        // If user pasted full E.164 with country dial prefix (e.g. 62812...), strip dial prefix first
        if ($cleanDialDigits !== '' && str_starts_with($digits, $cleanDialDigits) && strlen($digits) > strlen($cleanDialDigits) + 5) {
            $digits = substr($digits, strlen($cleanDialDigits));
        }

        // Strip leading zeros immediately (0812... -> 812...)
        return ltrim($digits, '0');
    }

    /**
     * Alias of sanitizePhoneLocalDigits for semantic clarity across controllers.
     */
    public static function sanitizePhoneLocalNumber(?string $rawPhone, ?string $dialCode = '+62'): string
    {
        return self::sanitizePhoneLocalDigits($rawPhone, $dialCode);
    }

    /**
     * Build clean E.164 phone number (+6281234567890) from country zone dial code and local input.
     */
    public static function toE164(string $dialCode, ?string $rawLocalPhone): string
    {
        $normalizedDial = '+'.ltrim(preg_replace('/\D/', '', $dialCode) ?: '62', '+');
        $local = self::sanitizePhoneLocalDigits($rawLocalPhone, $normalizedDial);

        return $local === '' ? '' : ($normalizedDial.$local);
    }

    /**
     * Format integer/float with regional thousand separators:
     * - 'id', 'de', 'es' -> dot '.' thousand separator (e.g. 1.500.000)
     * - 'en', 'zh-CN', 'ja', 'ar' -> comma ',' thousand separator (e.g. 1,500,000)
     */
    public static function formatRegionalNumber(int|float|string $amount, ?string $locale = null, int $decimals = 0): string
    {
        $loc = strtolower((string) ($locale ?: app()->getLocale()));
        $numeric = is_numeric($amount) ? (float) $amount : (float) self::parseRegionalInteger((string) $amount);

        $useDotThousands = in_array($loc, ['id', 'de', 'es'], true);
        $thousandsSep = $useDotThousands ? '.' : ',';
        $decimalSep = $useDotThousands ? ',' : '.';

        return number_format($numeric, $decimals, $decimalSep, $thousandsSep);
    }

    /**
     * Parse a formatted regional number string ("1.500.000" or "1,500,000") back into clean integer.
     */
    public static function parseRegionalInteger(int|float|string|null $formatted): int
    {
        if ($formatted === null || $formatted === '') {
            return 0;
        }
        if (is_int($formatted)) {
            return $formatted;
        }
        if (is_float($formatted)) {
            return (int) round($formatted);
        }

        $digits = preg_replace('/[^\d\-]/', '', $formatted) ?? '0';

        return (int) $digits;
    }
}
