<?php

namespace App\Support;

class CountryZoneHelper
{
    /**
     * Cache of countries list from config.
     */
    protected static ?array $countries = null;

    /**
     * Get all country records from config/countries.php.
     *
     * @return array<int, array{name: string, code: string, emoji: string, iso: string}>
     */
    public static function getAll(): array
    {
        if (static::$countries === null) {
            static::$countries = config('countries', []);
        }

        return static::$countries;
    }

    /**
     * Get associative array for Filament Select: [ 'ID' => '🇮🇩 Indonesia (+62)', ... ]
     *
     * @return array<string, string>
     */
    public static function getSelectOptions(): array
    {
        $options = [];
        foreach (static::getAll() as $item) {
            $iso = strtoupper($item['iso'] ?? '');
            if (! $iso) {
                continue;
            }
            $name = $item['name'] ?? $iso;
            $code = $item['code'] ?? '';
            $emoji = $item['emoji'] ?? '🌐';

            $options[$iso] = "{$emoji} {$name} ({$code})";
        }

        return $options;
    }

    /**
     * Find country record by ISO code.
     *
     * @return array{name: string, code: string, emoji: string, iso: string}|null
     */
    public static function findByIso(?string $iso): ?array
    {
        if (! $iso) {
            return null;
        }

        $needle = strtoupper(trim($iso));
        foreach (static::getAll() as $item) {
            if (strtoupper($item['iso'] ?? '') === $needle) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Get dial code for an ISO (e.g. '+62' for 'ID').
     */
    public static function getDialCode(?string $iso, string $default = '+62'): string
    {
        $country = static::findByIso($iso);
        return $country['code'] ?? $default;
    }

    /**
     * Clean a local number by stripping non-digits and leading zeros.
     */
    public static function cleanLocalNumber(?string $number): string
    {
        if (! $number) {
            return '';
        }

        $digits = preg_replace('/\D/', '', $number);
        return ltrim($digits, '0');
    }

    /**
     * Split a full phone number into ISO, dial code, local number, and clean E.164.
     *
     * @return array{iso: string, dial_code: string, local_number: string, full_e164: string, formatted: string}
     */
    public static function splitPhone(?string $phone, string $defaultIso = 'ID'): array
    {
        $raw = trim($phone ?? '');
        if ($raw === '') {
            $dial = static::getDialCode($defaultIso, '+62');
            return [
                'iso' => $defaultIso,
                'dial_code' => $dial,
                'local_number' => '',
                'full_e164' => '',
                'formatted' => '',
            ];
        }

        // Clean digits
        $digitsOnly = preg_replace('/\D/', '', $raw);

        // Sort countries by dial code length descending so longer prefixes match first (e.g. +1-684 before +1)
        $all = static::getAll();
        usort($all, function ($a, $b) {
            $cleanA = preg_replace('/\D/', '', $a['code'] ?? '');
            $cleanB = preg_replace('/\D/', '', $b['code'] ?? '');
            return strlen($cleanB) <=> strlen($cleanA);
        });

        // Try to match dial code prefix from digits
        foreach ($all as $c) {
            $cleanDial = preg_replace('/\D/', '', $c['code'] ?? '');
            if ($cleanDial !== '' && str_starts_with($digitsOnly, $cleanDial)) {
                $local = substr($digitsOnly, strlen($cleanDial));
                $local = ltrim($local, '0');
                $iso = strtoupper($c['iso']);
                $code = $c['code'];

                return [
                    'iso' => $iso,
                    'dial_code' => $code,
                    'local_number' => $local,
                    'full_e164' => $cleanDial . $local,
                    'formatted' => $code . ' ' . $local,
                ];
            }
        }

        // Fallback: If starts with 0, assume defaultIso (usually Indonesia)
        if (str_starts_with($digitsOnly, '0')) {
            $local = ltrim($digitsOnly, '0');
            $dial = static::getDialCode($defaultIso, '+62');
            $cleanDial = preg_replace('/\D/', '', $dial);
            return [
                'iso' => $defaultIso,
                'dial_code' => $dial,
                'local_number' => $local,
                'full_e164' => $cleanDial . $local,
                'formatted' => $dial . ' ' . $local,
            ];
        }

        // Default fallback to Indonesia
        $dial = static::getDialCode($defaultIso, '+62');
        $cleanDial = preg_replace('/\D/', '', $dial);
        return [
            'iso' => $defaultIso,
            'dial_code' => $dial,
            'local_number' => $digitsOnly,
            'full_e164' => $cleanDial . $digitsOnly,
            'formatted' => $dial . ' ' . $digitsOnly,
        ];
    }

    /**
     * Format clean E.164 numeric string without leading + or symbols.
     * E.g. ('ID', '081260840428') => '6281260840428'
     */
    public static function formatE164(string $iso, ?string $localNumber): string
    {
        $dial = static::getDialCode($iso, '+62');
        $cleanDial = preg_replace('/\D/', '', $dial);
        $cleanLocal = static::cleanLocalNumber($localNumber);

        if ($cleanLocal === '') {
            return '';
        }

        return $cleanDial . $cleanLocal;
    }
}
