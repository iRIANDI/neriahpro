<?php

namespace App\Support;

class CurrencyHelper
{
    /**
     * Parse any currency input (Indonesian 150.000,50 or US 150,000.50) into a clean float or decimal.
     */
    public static function parseDecimal(mixed $state, ?float $default = null): ?float
    {
        if ($state === null || $state === '') {
            return $default;
        }

        if (is_numeric($state)) {
            return (float) $state;
        }

        $val = trim((string) $state);

        // Remove currency symbols (Rp, $, €, etc.)
        $val = preg_replace('/[^\d.,]/', '', $val);

        if ($val === '') {
            return $default;
        }

        // Both dot and comma present
        if (str_contains($val, '.') && str_contains($val, ',')) {
            if (strrpos($val, ',') > strrpos($val, '.')) {
                // Indonesian format: 150.000,50 -> strip dots, replace comma with dot
                $val = str_replace('.', '', $val);
                $val = str_replace(',', '.', $val);
            } else {
                // US/EN format: 150,000.50 -> strip commas
                $val = str_replace(',', '', $val);
            }
        } elseif (str_contains($val, ',')) {
            // Only comma present: could be 150,50 (decimal) or 150,000 (thousands)
            if (preg_match('/,\d{1,2}$/', $val)) {
                $val = str_replace(',', '.', $val);
            } else {
                $val = str_replace(',', '', $val);
            }
        } elseif (str_contains($val, '.')) {
            // Only dot present: could be 150.000 (thousands) or 150000.50 (decimal)
            if (preg_match('/\.\d{3}(?:\.|$)/', $val) && !preg_match('/\.\d{1,2}$/', $val)) {
                $val = str_replace('.', '', $val);
            }
        }

        return is_numeric($val) ? (float) $val : $default;
    }

    /**
     * Format decimal to Indonesian currency string (e.g. 150000.5 -> "150.000,50").
     */
    public static function formatDecimal(mixed $state, int $decimals = 2): ?string
    {
        if ($state === null || $state === '') {
            return null;
        }

        if (!is_numeric($state)) {
            $parsed = static::parseDecimal($state);
            if ($parsed === null) {
                return null;
            }
            $state = $parsed;
        }

        return number_format((float) $state, $decimals, ',', '.');
    }
}

