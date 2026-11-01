/**
 * Shared Enterprise Input Hygiene & Regional Formatting Utilities
 * Synchronized 1:1 with config/country_zones.php & App\Support\InputHygieneStandard.
 */

export const COUNTRY_ZONES_TEMPLATE = [
  { code: 'ID', iso: 'ID', dial_code: '+62', flag: '🇮🇩', emoji: '🇮🇩', name: 'Indonesia' },
  { code: 'SG', iso: 'SG', dial_code: '+65', flag: '🇸🇬', emoji: '🇸🇬', name: 'Singapore' },
  { code: 'MY', iso: 'MY', dial_code: '+60', flag: '🇲🇾', emoji: '🇲🇾', name: 'Malaysia' },
  { code: 'US', iso: 'US', dial_code: '+1', flag: '🇺🇸', emoji: '🇺🇸', name: 'United States' },
  { code: 'AU', iso: 'AU', dial_code: '+61', flag: '🇦🇺', emoji: '🇦🇺', name: 'Australia' },
  { code: 'GB', iso: 'GB', dial_code: '+44', flag: '🇬🇧', emoji: '🇬🇧', name: 'United Kingdom' },
  { code: 'JP', iso: 'JP', dial_code: '+81', flag: '🇯🇵', emoji: '🇯🇵', name: 'Japan' },
  { code: 'DE', iso: 'DE', dial_code: '+49', flag: '🇩🇪', emoji: '🇩🇪', name: 'Germany' },
  { code: 'NL', iso: 'NL', dial_code: '+31', flag: '🇳🇱', emoji: '🇳🇱', name: 'Netherlands' },
  { code: 'AE', iso: 'AE', dial_code: '+971', flag: '🇦🇪', emoji: '🇦🇪', name: 'United Arab Emirates' },
  { code: 'SA', iso: 'SA', dial_code: '+966', flag: '🇸🇦', emoji: '🇸🇦', name: 'Saudi Arabia' },
  { code: 'IN', iso: 'IN', dial_code: '+91', flag: '🇮🇳', emoji: '🇮🇳', name: 'India' },
  { code: 'KR', iso: 'KR', dial_code: '+82', flag: '🇰🇷', emoji: '🇰🇷', name: 'South Korea' },
  { code: 'CN', iso: 'CN', dial_code: '+86', flag: '🇨🇳', emoji: '🇨🇳', name: 'China' },
  { code: 'TH', iso: 'TH', dial_code: '+66', flag: '🇹🇭', emoji: '🇹🇭', name: 'Thailand' },
  { code: 'VN', iso: 'VN', dial_code: '+84', flag: '🇻🇳', emoji: '🇻🇳', name: 'Vietnam' },
  { code: 'PH', iso: 'PH', dial_code: '+63', flag: '🇵🇭', emoji: '🇵🇭', name: 'Philippines' },
  { code: 'HK', iso: 'HK', dial_code: '+852', flag: '🇭🇰', emoji: '🇭🇰', name: 'Hong Kong' },
  { code: 'TW', iso: 'TW', dial_code: '+886', flag: '🇹🇼', emoji: '🇹🇼', name: 'Taiwan' },
  { code: 'CA', iso: 'CA', dial_code: '+1', flag: '🇨🇦', emoji: '🇨🇦', name: 'Canada' },
];

/**
 * Normalize country zone list from server props (config/country_zones.php or config/countries.php).
 */
export function normalizeCountryZones(serverZones) {
  if (!Array.isArray(serverZones) || serverZones.length === 0) {
    return COUNTRY_ZONES_TEMPLATE;
  }
  return serverZones.map((z) => ({
    iso: z.iso || z.code || 'ID',
    code: z.dial_code || (String(z.code || '').startsWith('+') ? z.code : '+62'),
    dial_code: z.dial_code || (String(z.code || '').startsWith('+') ? z.code : '+62'),
    name: z.name || 'Indonesia',
    emoji: z.flag || z.emoji || '🌐',
    flag: z.flag || z.emoji || '🌐',
  }));
}

/**
 * Strictly keep only valid RFC email characters ([a-zA-Z0-9._%+\-@]) and lowercase.
 */
export function sanitizeStrictEmailInput(raw) {
  if (!raw) return '';
  return String(raw)
    .replace(/<[^>]*>/g, '')
    .replace(/[^a-zA-Z0-9._%+\-@]/g, '')
    .toLowerCase();
}

/**
 * Strip non-digits AND immediately strip any leading '0' when user types 0 first,
 * because country zone dial code is already selected.
 */
export function sanitizePhoneLocalDigitsInput(raw, dialCode = '+62') {
  if (!raw) return '';
  let digits = String(raw).replace(/\D/g, '');
  const dialDigits = String(dialCode || '').replace(/\D/g, '');
  if (dialDigits && digits.startsWith(dialDigits) && digits.length > dialDigits.length + 5) {
    digits = digits.slice(dialDigits.length);
  }
  return digits.replace(/^0+/, '');
}

/**
 * Format integer/number with regional thousand separators:
 * - 'id', 'de', 'es' -> 1.500.000
 * - 'en' and others  -> 1,500,000
 */
export function formatRegionalNumberInput(rawValue, locale = 'id') {
  if (rawValue === '' || rawValue === null || rawValue === undefined) return '';
  const digits = String(rawValue).replace(/\D/g, '');
  if (!digits) return '';
  const num = Number(digits);
  if (Number.isNaN(num)) return '';
  const loc = String(locale || 'id').toLowerCase();
  const useDot = loc === 'id' || loc === 'de' || loc === 'es';
  const sep = useDot ? '.' : ',';
  return String(Math.trunc(num)).replace(/\B(?=(\d{3})+(?!\d))/g, sep);
}

/**
 * Parse regional thousand-separated string back to clean integer.
 */
export function parseRegionalNumberInput(formattedValue, fallback = 0) {
  if (formattedValue === '' || formattedValue === null || formattedValue === undefined) return fallback;
  const digits = String(formattedValue).replace(/\D/g, '');
  if (!digits) return fallback;
  const parsed = parseInt(digits, 10);
  return Number.isNaN(parsed) ? fallback : parsed;
}
