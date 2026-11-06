import { useState, useMemo, useCallback, useRef } from 'react';
import { TRANSLATIONS, calculateCompleteness } from '../constants/blueprintTranslations';
import {
  normalizeCountryZones,
  sanitizePhoneLocalDigitsInput,
  COUNTRY_ZONES_TEMPLATE,
} from '../../utils/inputHygiene';

export function useBlueprintFormState({ initialData = {}, countries = [], currentLocale = null }) {
  const initialLang = (currentLocale === 'en')
    || (initialData?.language === 'en' || initialData?.locale === 'en')
    || (typeof window !== 'undefined' && (document.documentElement.lang?.startsWith('en') || document.cookie.includes('neriah_locale=en')))
    ? 'en'
    : 'id';

  const [lang, setLang] = useState(initialLang);
  const [isDarkMode, setIsDarkMode] = useState(false);
  const t = TRANSLATIONS[lang] || TRANSLATIONS.id;

  // Requested tier from URL query (?tier=spark, ?package=starter, etc.)
  const requestedTier = useMemo(() => {
    try {
      const sp = new URLSearchParams(window.location.search);
      return sp.get('tier') || sp.get('package') || sp.get('package_tier') || initialData.selected_tier || initialData.tier || '';
    } catch (e) {
      return initialData.selected_tier || initialData.tier || '';
    }
  }, [initialData]);

  // Track Default Presets Generator
  const getTrackDefaults = useCallback((track, tier = '') => {
    const isEn = lang === 'en';
    const cleanTier = String(tier || requestedTier || '').toLowerCase();
    if (track === 'retail') {
      let defaultBudget = isEn
        ? 'Spark: $0 / Free (Architectural Audit & Blueprint Access)'
        : 'Spark: Rp 0 (Akses Audit Arsitektur & Cetak Biru Gratis)';
      let defaultTermin = isEn
        ? 'Spark Package (Free): $0 / Free Instant Architectural Audit & Blueprint Access'
        : 'Paket Spark (Gratis): Rp 0 / Akses Audit Arsitektur & Cetak Biru Gratis 100%';

      if (cleanTier === 'starter' || cleanTier === 'lite' || cleanTier === 'retail_starter' || cleanTier === 'retail_lite') {
        defaultBudget = isEn ? 'Lite: Rp 99,000 (PRD 26 Parameters, Strict ULID DDL & AI Directives)' : 'Lite: Rp 99.000 (PRD 26 Parameter, Schema DDL & AI Directives)';
        defaultTermin = isEn ? 'Instant 100% Full Payment: Midtrans Snap QRIS / Virtual Account (Immediate Download)' : 'Pembayaran Penuh 100% Instan: QRIS / Virtual Account Midtrans Snap (Langsung Unduh Berkas)';
      } else if (cleanTier === 'pro' || cleanTier === 'retail_pro') {
        defaultBudget = isEn ? 'Pro: Rp 399,000 (Full Architecture Bundle: DDL, 7-Phase Prompts, Docker & Seeder)' : 'Pro: Rp 399.000 (Bundel Lengkap DDL, 7-Fase Prompt AI, Docker & Seeder)';
        defaultTermin = isEn ? 'Instant 100% Full Payment: Midtrans Snap QRIS / Virtual Account (Immediate Download)' : 'Pembayaran Penuh 100% Instan: QRIS / Virtual Account Midtrans Snap (Langsung Unduh Berkas)';
      } else if (cleanTier === 'ultimate' || cleanTier === 'retail_ultimate') {
        defaultBudget = isEn ? 'Ultimate: Rp 1,490,000 (Full 10 Pillars Software Factory OS + Live MCP Kit)' : 'Ultimate: Rp 1.490.000 (Full 10 Pilar Software Factory OS + Live MCP Kit)';
        defaultTermin = isEn ? 'Instant 100% Full Payment: Midtrans Snap QRIS / Virtual Account (Immediate Download)' : 'Pembayaran Penuh 100% Instan: QRIS / Virtual Account Midtrans Snap (Langsung Unduh Berkas)';
      }

      return {
        kisaranBudget: defaultBudget,
        garansiSla: isEn
          ? 'Self-Service Delivery: Instant PRD Fortune 500, Strict ULID DDL & Docker Starter Kit Handover'
          : 'Lisensi Mandiri: Penyerahan Langsung Dokumen PRD Fortune 500, Skema DDL Strict ULID & Docker Starter Kit',
        terminPembayaran: defaultTermin,
      };
    }

    // Studio Defaults
    return {
      kisaranBudget: isEn
        ? '$1,000 - $3,000 USD (Growth / Custom Business Portal - Multi-Role & Gateway)'
        : 'Rp 15.000.000 - Rp 35.000.000 (Growth / Custom Business Portal - Multi-Role & Gateway)',
      garansiSla: isEn
        ? '30 Days Post-Launch Bug Warranty Free of Charge + Full Private GitHub Repository Handover'
        : '30 Hari Garansi Bug Pascameluncur Bebas Biaya + Penyerahan Akses Penuh Private Repo GitHub',
      terminPembayaran: isEn
        ? 'Standard 50/50: 50% Kickoff DP & 50% Final Settlement Post-UAT Acceptance & Key Handover (via Midtrans Snap)'
        : 'Termin Standar 50/50: 50% DP Kickoff & 50% Pelunasan setelah lolos UAT & Serah Terima Kunci (via Midtrans Snap)',
    };
  }, [lang, requestedTier]);

  // Execution Track: 'retail' vs 'studio'
  const [currentTrack, setCurrentTrack] = useState(() => {
    try {
      const urlParams = new URLSearchParams(window.location.search);
      const qTrack = (urlParams.get('track') || urlParams.get('mode') || '').toLowerCase();
      if (qTrack === 'studio' || qTrack === 'mitra' || qTrack === 'mitra_turnkey_project') return 'studio';
      if (qTrack === 'retail' || qTrack === 'retail_self_dev') return 'retail';
      const qTier = urlParams.get('tier') || urlParams.get('package') || urlParams.get('package_tier');
      if (qTier && (qTier.includes('retail') || ['spark', 'starter', 'pro', 'ultimate', 'lite'].includes(qTier.toLowerCase()))) {
        return 'retail';
      }
      if (qTier && ['advisory', 'starter_umkm', 'umkm_starter', 'mvp', 'full_mvp', 'blueprint_advisory'].includes(qTier.toLowerCase())) {
        return 'studio';
      }
    } catch (e) { }
    if (initialData?.selected_track === 'studio' || initialData?.track === 'studio') return 'studio';
    if (initialData?.selected_tier && ['spark', 'starter', 'pro', 'ultimate', 'lite'].includes(String(initialData.selected_tier).toLowerCase())) return 'retail';
    return 'retail';
  });

  const handleTrackChange = useCallback((newTrack) => {
    setCurrentTrack(newTrack);
    try {
      const url = new URL(window.location.href);
      url.searchParams.set('track', newTrack);
      window.history.replaceState({}, '', url.toString());
    } catch (e) { }

    const trackDefs = getTrackDefaults(newTrack, requestedTier);
    setFormData(prev => {
      const isRetailTarget = newTrack === 'retail';
      let nextBudget = prev.kisaranBudget;
      let nextSla = prev.garansiSla;
      let nextTermin = prev.terminPembayaran;

      if (isRetailTarget) {
        if (!nextBudget || nextBudget.includes('15.000.000') || nextBudget.includes('Growth') || nextBudget.includes('50.000.000') || nextBudget.includes('Advisory') || nextBudget.includes('Starter UMKM')) {
          nextBudget = trackDefs.kisaranBudget;
        }
        if (!nextSla || nextSla.includes('Garansi Bug') || nextSla.includes('Pascameluncur')) {
          nextSla = trackDefs.garansiSla;
        }
        if (!nextTermin || nextTermin.includes('50/50') || nextTermin.includes('Milestone') || nextTermin.includes('DP Kickoff')) {
          nextTermin = trackDefs.terminPembayaran;
        }
      } else {
        if (!nextBudget || nextBudget.includes('Spark') || nextBudget.includes('Lite: Rp 99') || nextBudget.includes('Starter: Rp 190') || nextBudget.includes('Pro: Rp 399') || nextBudget.includes('Pro: Rp 490') || nextBudget.includes('Ultimate: Rp 1.490')) {
          nextBudget = trackDefs.kisaranBudget;
        }
        if (!nextSla || nextSla.includes('Lisensi Mandiri') || nextSla.includes('Akses Seumur Hidup') || nextSla.includes('Dukungan Arsitektur')) {
          nextSla = trackDefs.garansiSla;
        }
        if (!nextTermin || nextTermin.includes('Spark') || nextTermin.includes('Pembayaran Penuh 100%') || nextTermin.includes('Self-Service Delivery')) {
          nextTermin = trackDefs.terminPembayaran;
        }
      }

      return {
        ...prev,
        kisaranBudget: nextBudget,
        garansiSla: nextSla,
        terminPembayaran: nextTermin,
      };
    });
  }, [getTrackDefaults, requestedTier]);

  // Country code selector
  const countryList = useMemo(() => normalizeCountryZones(countries || COUNTRY_ZONES_TEMPLATE), [countries]);

  const [selectedCountryCode, setSelectedCountryCode] = useState(() => {
    if (initialData.country_code) return initialData.country_code;
    return '+62';
  });

  const currentCountry = useMemo(() => {
    return countryList.find(c => c.code === selectedCountryCode) || countryList[0] || { code: '+62', emoji: '🇮🇩', name: 'Indonesia' };
  }, [countryList, selectedCountryCode]);

  const [phoneDigits, setPhoneDigits] = useState(() => {
    if (initialData.phone) {
      let d = String(initialData.phone).replace(/\D/g, '');
      if (d.startsWith('62')) d = d.substring(2);
      return d.replace(/^0+/, '');
    }
    return '';
  });

  // Full 25 Fields Form Data
  const [formData, setFormData] = useState({
    namaBisnis: initialData.namaBisnis || '',
    masalahUtama: initialData.masalahUtama || '',
    tujuanUtama: initialData.tujuanUtama || '',
    targetAudiens: initialData.targetAudiens || '',
    aktorSistem: initialData.aktorSistem || '',
    targetPlatform: initialData.targetPlatform || 'Modern Web Application Responsive & PWA (Desktop, Tablet & Mobile)',
    fiturWajib: initialData.fiturWajib || '',
    fiturTambahan: initialData.fiturTambahan || '',
    alurKerja: initialData.alurKerja || '',
    migrasiData: initialData.migrasiData || 'Database Baru Bersih (Input Mandiri & Dukungan Template CSV)',
    kebutuhanIntegrasi: initialData.kebutuhanIntegrasi || '',
    referensiDesain: initialData.referensiDesain || 'Clean Modern Monolith (Linear.app & Stripe inspired), sharp rectangular borders, dark/light mode fidelity.',
    kesiapanAset: initialData.kesiapanAset || 'Sedang Disiapkan Tim Internal',
    preferensiHosting: initialData.preferensiHosting || 'Managed Dedicated Cloud VPS Neriah Pro (PostgreSQL 16, Redis, Backup Otomatis)',
    durasiHari: initialData.durasiHari || '30',
    targetWaktu: initialData.targetWaktu || '30 Hari Kerja',
    skalaPengguna: initialData.skalaPengguna || '0 - 100.000 Pengguna / Bulan (Dedicated VPS Monolith)',
    jangkauanPasar: initialData.jangkauanPasar || 'Domestik Indonesia (IDR, Zona WIB/WITA/WIT)',
    outOfScope: initialData.outOfScope || '',
    kepatuhanKeamanan: initialData.kepatuhanKeamanan || 'Standar Web Application & OWASP Top 10 (CSRF, XSS, HTTPS)',
    kisaranBudget: (currentTrack === 'retail' && (!initialData.kisaranBudget || initialData.kisaranBudget.includes('15.000.000') || initialData.kisaranBudget.includes('Growth') || initialData.kisaranBudget.includes('50.000.000')))
      ? getTrackDefaults('retail', requestedTier).kisaranBudget
      : (initialData.kisaranBudget || 'Rp 15.000.000 - Rp 35.000.000 (Growth / Custom Business Portal - Multi-Role & Gateway)'),
    garansiSla: (currentTrack === 'retail' && (!initialData.garansiSla || initialData.garansiSla.includes('Garansi Bug') || initialData.garansiSla.includes('Pascameluncur')))
      ? getTrackDefaults('retail', requestedTier).garansiSla
      : (initialData.garansiSla || '30 Hari Garansi Bug Pascameluncur Bebas Biaya + Penyerahan Akses Penuh Private Repo GitHub'),
    terminPembayaran: (currentTrack === 'retail' && (!initialData.terminPembayaran || initialData.terminPembayaran.includes('50/50') || initialData.terminPembayaran.includes('Milestone') || initialData.terminPembayaran.includes('DP Kickoff')))
      ? getTrackDefaults('retail', requestedTier).terminPembayaran
      : (initialData.terminPembayaran || 'Termin Standar 50/50: 50% DP Kickoff & 50% Pelunasan setelah lolos UAT & Serah Terima Kunci (via Midtrans Snap)'),
    clientName: initialData.clientName || '',
    email: initialData.email || '',
    phone: initialData.phone || '',
    primarySector: initialData.primarySector || (() => {
      try { return new URLSearchParams(window.location.search).get('sector') || ''; } catch { return ''; }
    })(),
    selectedPrimitives: Array.isArray(initialData.selectedPrimitives) ? initialData.selectedPrimitives : [],
    selectedMenus: Array.isArray(initialData.selectedMenus) ? initialData.selectedMenus : [],
    stackPreset: initialData.stackPreset || (() => {
      try { return new URLSearchParams(window.location.search).get('stack') || 'STACK_M1_LARAVEL_MONOLITH'; } catch { return 'STACK_M1_LARAVEL_MONOLITH'; }
    })(),
    selectedUiVariant: initialData.selectedUiVariant || (() => {
      try { return new URLSearchParams(window.location.search).get('ui_variant') || ''; } catch { return ''; }
    })(),
    publicUxPreset: initialData.publicUxPreset || initialData.public_ux_preset || (() => {
      try { return new URLSearchParams(window.location.search).get('public_ux') || ''; } catch { return ''; }
    })(),
    navigationShell: initialData.navigationShell || (() => {
      try { return new URLSearchParams(window.location.search).get('shell') || ''; } catch { return ''; }
    })(),
    outputLocale: initialData.outputLocale || (() => {
      try { return new URLSearchParams(window.location.search).get('output_locale') || initialLang || 'id'; } catch { return initialLang || 'id'; }
    })(),
    personaMode: initialData.personaMode || (() => {
      try { return new URLSearchParams(window.location.search).get('persona') || 'BUSINESS_WIZARD'; } catch { return 'BUSINESS_WIZARD'; }
    })(),
  });

  const updateField = useCallback((field, value) => {
    setFormData(prev => ({ ...prev, [field]: value }));
  }, []);

  const handlePhoneDigitsChange = useCallback((e) => {
    const rawDigits = sanitizePhoneLocalDigitsInput(e.target.value);
    setPhoneDigits(rawDigits);
    updateField('phone', rawDigits ? `${selectedCountryCode}${rawDigits}` : '');
  }, [selectedCountryCode, updateField]);

  // Submission & Validation States
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState(null);
  const [invalidFields, setInvalidFields] = useState([]);
  const [strokeActive, setStrokeActive] = useState(false);
  const strokeTimerRef = useRef(null);
  const [successData, setSuccessData] = useState(null);

  const completeness = useMemo(() => {
    return calculateCompleteness(formData, lang);
  }, [formData, lang]);

  const toggleTheme = useCallback(() => {
    setIsDarkMode(prev => {
      const next = !prev;
      if (typeof document !== 'undefined') {
        if (next) {
          document.documentElement.classList.add('dark');
        } else {
          document.documentElement.classList.remove('dark');
        }
      }
      return next;
    });
  }, []);

  const handleResetForm = useCallback(() => {
    setFormData({
      namaBisnis: '',
      masalahUtama: '',
      tujuanUtama: '',
      targetAudiens: '',
      aktorSistem: '',
      targetPlatform: 'Modern Web Application Responsive & PWA (Desktop, Tablet & Mobile)',
      fiturWajib: '',
      fiturTambahan: '',
      alurKerja: '',
      migrasiData: 'Database Baru Bersih (Input Mandiri & Dukungan Template CSV)',
      kebutuhanIntegrasi: '',
      referensiDesain: 'Clean Modern Monolith (Linear.app & Stripe inspired), sharp rectangular borders, dark/light mode fidelity.',
      kesiapanAset: 'Sedang Disiapkan Tim Internal',
      preferensiHosting: 'Managed Dedicated Cloud VPS Neriah Pro (PostgreSQL 16, Redis, Backup Otomatis)',
      durasiHari: '30',
      targetWaktu: '30 Hari Kerja',
      skalaPengguna: '0 - 100.000 Pengguna / Bulan (Dedicated VPS Monolith)',
      jangkauanPasar: 'Domestik Indonesia (IDR, Zona WIB/WITA/WIT)',
      outOfScope: '',
      kepatuhanKeamanan: 'Standar Web Application & OWASP Top 10 (CSRF, XSS, HTTPS)',
      kisaranBudget: getTrackDefaults(currentTrack, requestedTier).kisaranBudget,
      garansiSla: getTrackDefaults(currentTrack, requestedTier).garansiSla,
      terminPembayaran: getTrackDefaults(currentTrack, requestedTier).terminPembayaran,
      clientName: '',
      email: '',
      phone: '',
      primarySector: '',
      selectedPrimitives: [],
      selectedMenus: [],
      stackPreset: 'STACK_M1_LARAVEL_MONOLITH',
      selectedUiVariant: '',
      publicUxPreset: '',
      navigationShell: '',
      outputLocale: lang,
      personaMode: 'BUSINESS_WIZARD',
    });
    setPhoneDigits('');
    setInvalidFields([]);
    setErrorMessage(null);
    if (typeof localStorage !== 'undefined') {
      localStorage.removeItem('neriahpro_blueprint_draft');
    }
  }, [currentTrack, getTrackDefaults, lang, requestedTier]);

  return {
    lang,
    setLang,
    t,
    isDarkMode,
    toggleTheme,
    currentTrack,
    setCurrentTrack,
    handleTrackChange,
    getTrackDefaults,
    countryList,
    selectedCountryCode,
    setSelectedCountryCode,
    currentCountry,
    phoneDigits,
    setPhoneDigits,
    handlePhoneDigitsChange,
    formData,
    setFormData,
    updateField,
    completeness,
    isSubmitting,
    setIsSubmitting,
    errorMessage,
    setErrorMessage,
    invalidFields,
    setInvalidFields,
    strokeActive,
    setStrokeActive,
    strokeTimerRef,
    successData,
    setSuccessData,
    handleResetForm,
  };
}
