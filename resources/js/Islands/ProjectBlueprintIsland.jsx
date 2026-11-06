import React, { useState, useEffect, useRef, useMemo, useCallback } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
  Cpu,
  ShieldCheck,
  Zap,
  Users,
  CheckSquare,
  Layers,
  Clock,
  Sparkles,
  ArrowRight,
  CheckCircle2,
  AlertCircle,
  AlertTriangle,
  FileText,
  Sun,
  Moon,
  Globe,
  Lock,
  DollarSign,
  Upload,
  FileCode,
  FileSpreadsheet,
  File,
  X,
  Copy,
  Download,
  Check,
  RotateCcw,
  Sliders,
  Loader2,
  ChevronDown,
  ChevronUp,
  ChevronLeft,
  ChevronRight,
  Search,
  ArrowUp,
  Compass,
  FileCheck,
  Plus,
  Trash2,
  Edit2,
  Lightbulb,
  Send,
  MessageSquare,
  CreditCard,
  Shield,
  Activity,
  Truck,
  HelpCircle,
  RefreshCw,
  Save,
  Server,
  Database,
  Smartphone,
  Award,
  ShoppingBag,
  Rocket,
  ArrowUpRight
} from 'lucide-react';
import {
  normalizeCountryZones,
  sanitizeStrictEmailInput,
  sanitizePhoneLocalDigitsInput,
  formatRegionalNumberInput,
  COUNTRY_ZONES_TEMPLATE,
} from '../utils/inputHygiene';

import {
  TRANSLATIONS,
  calculateCompleteness,
  PAYMENT_RAIL_OPTIONS,
} from './Blueprint/constants/blueprintTranslations';
import {
  PUBLIC_UX_PRESETS_V10,
  resolveDefaultPublicUxForSector,
  QUICK_IDEA_CATALOG,
  FIELD_MAP,
  CANONICAL_FIELD_MAP,
  FIELD_SECTION_MAP,
  FIELD_LABELS,
} from './Blueprint/constants/blueprintConstants';

import BlueprintSuccessView from './Blueprint/components/BlueprintSuccessView';
import BlueprintSpySidebar from './Blueprint/components/BlueprintSpySidebar';
import BlueprintTopHeader from './Blueprint/components/BlueprintTopHeader';
import BlueprintTrackSwitcher from './Blueprint/components/BlueprintTrackSwitcher';
import BlueprintQuickIdeaSection from './Blueprint/components/BlueprintQuickIdeaSection';
import BlueprintComposableMatrixSection from './Blueprint/components/BlueprintComposableMatrixSection';
import BlueprintDiscoveryParamsSection from './Blueprint/components/BlueprintDiscoveryParamsSection';
import BlueprintPaymentRailModal from './Blueprint/components/BlueprintPaymentRailModal';
import BlueprintKpiCascadeModal from './Blueprint/components/BlueprintKpiCascadeModal';

export default function ProjectBlueprintIsland({ csrfToken, submitUrl, initialData = {}, countries = [], currentLocale = null }) {
  const initialLang = (currentLocale === 'en')
    || (initialData?.language === 'en' || initialData?.locale === 'en')
    || (typeof window !== 'undefined' && (document.documentElement.lang?.startsWith('en') || document.cookie.includes('neriah_locale=en')))
    ? 'en'
    : 'id';
  const [lang, setLang] = useState(initialLang);
  const [isDarkMode, setIsDarkMode] = useState(false);
  const t = TRANSLATIONS[lang];

  // Requested tier from URL query (?tier=spark, ?package=starter, etc.)
  const requestedTier = useMemo(() => {
    try {
      const sp = new URLSearchParams(window.location.search);
      return sp.get('tier') || sp.get('package') || sp.get('package_tier') || initialData.selected_tier || initialData.tier || '';
    } catch (e) {
      return initialData.selected_tier || initialData.tier || '';
    }
  }, [initialData]);

  // Track Default Presets Generator (Prevents Studio 50/50 contract terms from leaking into Retail licenses)
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

  // Execution Track: 'retail' (Jalur Mandiri) vs 'studio' (Jalur Turnkey Dedicated / Mitra Proyek)
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

  const handleTrackChange = (newTrack) => {
    setCurrentTrack(newTrack);
    try {
      const url = new URL(window.location.href);
      url.searchParams.set('track', newTrack);
      window.history.replaceState({}, '', url.toString());
    } catch (e) { }

    // Auto-align fields 21, 22, 23 to match new track to prevent leaking Studio 50/50 into Retail and vice versa
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
  };

  // Country code selector (config/country_zones.php template)
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

  // Top Idea Studio Open/Closed State
  const [isStudioOpen, setIsStudioOpen] = useState(true);

  // Textarea & Dropzone State
  const [ideaText, setIdeaText] = useState(() => initialData?._meta?.raw_idea_text || '');
  const [attachedFiles, setAttachedFiles] = useState([]);
  const [convertedMarkdown, setConvertedMarkdown] = useState(() => initialData?._meta?.converted_markdown || '');
  const [isAnalyzing, setIsAnalyzing] = useState(false);
  const [analysisStep, setAnalysisStep] = useState('');
  const [analysisStepIndex, setAnalysisStepIndex] = useState(1);
  const [analysisError, setAnalysisError] = useState(null);
  const [copySuccess, setCopySuccess] = useState(false);

  // Multi-AI Model Selection & Token Limit Shield
  const [aiModels, setAiModels] = useState({});
  const [selectedAiModel, setSelectedAiModel] = useState('auto');
  const [aiTelemetry, setAiTelemetry] = useState(() => initialData?._meta?.ai_telemetry || null);

  useEffect(() => {
    fetch('/api/ai/models')
      .then(res => res.json())
      .then(data => {
        if (data.success && data.models) {
          setAiModels(data.models);
        }
      })
      .catch(() => { });
  }, []);

  // Derive list of strictly configured AI models (with active API keys from Admin Panel)
  const configuredAiList = useMemo(() => {
    return Object.values(aiModels).filter(m => !!m.has_key);
  }, [aiModels]);

  // Ensure selected AI model snaps to 'auto' if previously pointing to an unconfigured model
  useEffect(() => {
    if (selectedAiModel !== 'auto' && aiModels[selectedAiModel] && !aiModels[selectedAiModel].has_key) {
      setSelectedAiModel('auto');
    }
  }, [selectedAiModel, aiModels]);

  // Proactive Guidance & Co-Pilot States
  const [proactiveSuggestions, setProactiveSuggestions] = useState(() => {
    return initialData.proactive_suggestions || [
      {
        id: 'whatsapp_notif',
        category: 'integration',
        title: lang === 'en' ? 'Automated WhatsApp Alerts' : 'Notifikasi WhatsApp Otomatis',
        desc: lang === 'en' ? 'Real-time dispatch alerts to customer WhatsApp' : 'Kirim update pesanan & resi via WhatsApp API',
        target_field: 'kebutuhanIntegrasi',
        addition: 'WhatsApp Cloud API untuk notifikasi transaksi & alert real-time',
        badge: 'Integrasi',
      },
      {
        id: 'payment_multi_rail',
        category: 'integration',
        title: lang === 'en' ? 'Payment Rails Selector (Bank API / Global / QRIS)' : 'Rel Pembayaran Fleksibel (BRI/BNI, Stripe, Wise, QRIS)',
        desc: lang === 'en' ? 'Click to select: BRI/BNI Direct API, Stripe, Wise, PayPal, or National QRIS' : 'Klik untuk memilih: BRI/BNI Open API, Stripe, Wise, PayPal, atau QRIS Nasional',
        target_field: 'kebutuhanIntegrasi',
        addition: 'Rel Pembayaran Terpilih (Direct Bank API / Stripe / Wise / QRIS)',
        badge: 'Rel Pembayaran',
        is_payment_selector: true,
      },
      {
        id: 'excel_export',
        category: 'feature',
        title: lang === 'en' ? 'Export Excel (.xlsx) & PDF' : 'Ekspor Laporan Excel (.xlsx) & PDF',
        desc: lang === 'en' ? 'Instant operational reconciliation export' : 'Unduh rekap operasional ke Excel & PDF',
        target_field: 'fiturWajib',
        addition: 'Modul Ekspor Laporan: Unduh rekapitulasi data & transaksi ke Microsoft Excel (.xlsx) dan PDF resmi.',
        badge: 'Fitur MVP',
      },
      {
        id: 'approval_role',
        category: 'actor',
        title: lang === 'en' ? 'Supervisor Approval Role' : 'Tingkat Akses Supervisor (Approval)',
        desc: lang === 'en' ? 'Multi-tier verification before critical execution' : 'Otorisasi persetujuan berjenjang sebelum eksekusi',
        target_field: 'aktorSistem',
        addition: 'Supervisor / Manajer: Otorisasi persetujuan berjenjang untuk transaksi & data krusial.',
        badge: 'Aktor RBAC',
      },
      {
        id: 'refund_flow',
        category: 'workflow',
        title: lang === 'en' ? 'Cancellation & Refund Workflow' : 'Alur Pembatalan & Pengembalian Dana',
        desc: lang === 'en' ? 'Automated dispute handling and refund ledger' : 'Alur resmi pembatalan pesanan & pencatatan refund',
        target_field: 'alurKerja',
        addition: 'Alur Pembatalan & Refund: Pengajuan pembatalan dengan alasan -> Verifikasi staf -> Penyesuaian saldo & pengiriman bukti otomatis.',
        badge: 'Alur Kerja',
      },
      {
        id: 'google_sso',
        category: 'feature',
        title: lang === 'en' ? '1-Click Google Sign-In (SSO)' : 'Login 1-Klik Google (Google SSO)',
        desc: lang === 'en' ? 'Frictionless onboarding with Google accounts' : 'Login cepat dan aman dengan akun Google resmi',
        target_field: 'fiturWajib',
        addition: 'Autentikasi 1-Klik Google Sign-In (OAuth 2.0) untuk kenyamanan dan keamanan pengguna.',
        badge: 'Fitur MVP',
      },
      {
        id: 'mobile_offline_sync',
        category: 'integration',
        title: lang === 'en' ? 'Offline-First Local DB Sync' : 'Sinkronisasi Database Lokal & Server',
        desc: lang === 'en' ? 'SQLite local storage with bi-directional server sync' : 'Penyimpanan lokal SQLite dengan auto-sync ke server PostgreSQL',
        target_field: 'kebutuhanIntegrasi',
        addition: 'Sync Engine: Penyimpanan lokal SQLite / Room dengan protokol sinkronisasi delta dua arah (push/pull) ke server PostgreSQL.',
        badge: 'Mobile & Sync',
      },
    ];
  });

  const [appliedSuggestionIds, setAppliedSuggestionIds] = useState(new Set());
  const [coPilotInput, setCoPilotInput] = useState('');
  const [isCoPilotLoading, setIsCoPilotLoading] = useState(false);
  const [paymentModalOpen, setPaymentModalOpen] = useState(false);
  const [pendingPaymentSuggestion, setPendingPaymentSuggestion] = useState(null);
  const [selectedPaymentRailIds, setSelectedPaymentRailIds] = useState(() => new Set(['qris_national']));
  const [selectedIdeaCategory, setSelectedIdeaCategory] = useState(null);

  // Dedicated AI KPI Elaboration & Cascading Engine State
  const [isElaboratingKpi, setIsElaboratingKpi] = useState(false);
  const [showKpiCascadeModal, setShowKpiCascadeModal] = useState(false);
  const [kpiCascadeResult, setKpiCascadeResult] = useState(null);
  const [selectedCascadeFeatures, setSelectedCascadeFeatures] = useState({});
  const [selectedCascadeActors, setSelectedCascadeActors] = useState({});
  const [includeCascadeGherkin, setIncludeCascadeGherkin] = useState(true);
  const [activeKpiCascadeTab, setActiveKpiCascadeTab] = useState('kpis');

  // In-Card Interactive Add Helpers
  const [activeAddKey, setActiveAddKey] = useState(null);
  const [quickInputText, setQuickInputText] = useState('');

  // Toast Notification State
  const [toast, setToast] = useState(null);

  // Centralized Toast Notification (Single Source of Truth - Prevents Double Toast)
  const showLocalToast = useCallback((type, message, title = '') => {
    if (typeof window !== 'undefined' && window.showToast) {
      window.showToast({ type, message, title });
      return;
    }
    setToast({ type, message, title });
    setTimeout(() => {
      setToast(null);
    }, 3800);
  }, []);

  // Anti-Spam Honeypots
  const [honeypot, setHoneypot] = useState('');
  const [honeypotWebsite, setHoneypotWebsite] = useState('');

  const fileInputRef = useRef(null);

  // Auto-Save Telemetry & Hermetic Workspace Isolation (v6.0 Zero Cross-Project Bleed)
  const [autoSaveStatus, setAutoSaveStatus] = useState('idle');
  const [lastSavedTime, setLastSavedTime] = useState(() => {
    const tz = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    return `${tz} WIB`;
  });
  const autoSaveDebounceRef = useRef(null);
  const isExplicitSlugEdit = Boolean(initialData?._meta?.is_editing_slug);
  const isFreshWorkspace = Boolean(initialData?._meta?.is_fresh_workspace) || (!isExplicitSlugEdit && !initialData?._draft_id && !initialData?.namaBisnis);
  const draftIdRef = useRef(
    initialData?._meta?.draft_token
    || initialData?._draft_id
    || ('draft_' + Date.now() + '_' + Math.random().toString(36).slice(2, 10))
  );
  const getScopedStorageKey = useCallback(() => {
    if (isExplicitSlugEdit && initialData?._meta?.is_editing_slug) {
      return `neriah_bp_slug_${initialData._meta.is_editing_slug}`;
    }
    return `neriah_bp_${draftIdRef.current || 'fresh'}`;
  }, [isExplicitSlugEdit, initialData]);

  // Full 25 Fields Form Data (Single Source of Truth)
  const [formData, setFormData] = useState({
    // BLOK A: Bisnis (3)
    namaBisnis: initialData.namaBisnis || '',
    masalahUtama: initialData.masalahUtama || '',
    tujuanUtama: initialData.tujuanUtama || '',

    // BLOK B: Pengguna, RBAC & Platform (3)
    targetAudiens: initialData.targetAudiens || '',
    aktorSistem: initialData.aktorSistem || '',
    targetPlatform: initialData.targetPlatform || 'Modern Web Application Responsive & PWA (Desktop, Tablet & Mobile)',

    // BLOK C: Fitur, Alur & Migrasi (4)
    fiturWajib: initialData.fiturWajib || '',
    fiturTambahan: initialData.fiturTambahan || '',
    alurKerja: initialData.alurKerja || '',
    migrasiData: initialData.migrasiData || 'Database Baru Bersih (Input Mandiri & Dukungan Template CSV)',

    // BLOK D: Integrasi, Estetika & Hosting (4)
    kebutuhanIntegrasi: initialData.kebutuhanIntegrasi || '',
    referensiDesain: initialData.referensiDesain || 'Clean Modern Monolith (Linear.app & Stripe inspired), sharp rectangular borders, dark/light mode fidelity.',
    kesiapanAset: initialData.kesiapanAset || 'Sedang Disiapkan Tim Internal',
    preferensiHosting: initialData.preferensiHosting || 'Managed Dedicated Cloud VPS Neriah Pro (PostgreSQL 16, Redis, Backup Otomatis)',

    // BLOK E: Timeline, Skala, Keamanan & Batasan (6)
    durasiHari: initialData.durasiHari || '30',
    targetWaktu: initialData.targetWaktu || '30 Hari Kerja',
    skalaPengguna: initialData.skalaPengguna || '0 - 100.000 Pengguna / Bulan (Dedicated VPS Monolith)',
    jangkauanPasar: initialData.jangkauanPasar || 'Domestik Indonesia (IDR, Zona WIB/WITA/WIT)',
    outOfScope: initialData.outOfScope || '',
    kepatuhanKeamanan: initialData.kepatuhanKeamanan || 'Standar Web Application & OWASP Top 10 (CSRF, XSS, HTTPS)',

    // BLOK F: Budget, Garansi SLA & PIC (6)
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
      try {
        return new URLSearchParams(window.location.search).get('sector') || '';
      } catch {
        return '';
      }
    })(),
    selectedPrimitives: Array.isArray(initialData.selectedPrimitives) ? initialData.selectedPrimitives : [],
    selectedMenus: Array.isArray(initialData.selectedMenus) ? initialData.selectedMenus : [],
    stackPreset: initialData.stackPreset || (() => {
      try {
        return new URLSearchParams(window.location.search).get('stack') || 'STACK_M1_LARAVEL_MONOLITH';
      } catch {
        return 'STACK_M1_LARAVEL_MONOLITH';
      }
    })(),
    selectedUiVariant: initialData.selectedUiVariant || (() => {
      try {
        return new URLSearchParams(window.location.search).get('ui_variant') || '';
      } catch {
        return '';
      }
    })(),
    publicUxPreset: initialData.publicUxPreset || initialData.public_ux_preset || (() => {
      try {
        return new URLSearchParams(window.location.search).get('public_ux') || '';
      } catch {
        return '';
      }
    })(),
    navigationShell: initialData.navigationShell || (() => {
      try {
        return new URLSearchParams(window.location.search).get('shell') || '';
      } catch {
        return '';
      }
    })(),
    outputLocale: initialData.outputLocale || (() => {
      try {
        return new URLSearchParams(window.location.search).get('output_locale') || initialLang || 'id';
      } catch {
        return initialLang || 'id';
      }
    })(),
    personaMode: initialData.personaMode || (() => {
      try {
        return new URLSearchParams(window.location.search).get('persona') || 'BUSINESS_WIZARD';
      } catch {
        return 'BUSINESS_WIZARD';
      }
    })(),
  });

  // Dynamic Domain Detection for Quick Idea Templates (Auto-Adapts to Client Vision & Keywords)
  const detectedIdeaCategory = useMemo(() => {
    const rawText = `${formData.namaBisnis || ''} ${ideaText || ''} ${formData.primarySector || ''}`.toLowerCase();
    for (const [key, data] of Object.entries(QUICK_IDEA_CATALOG)) {
      if (data.keywords && data.keywords.some(kw => rawText.includes(kw))) {
        return key;
      }
    }
    return 'tourism';
  }, [formData.namaBisnis, ideaText, formData.primarySector]);

  const activeIdeaCategory = selectedIdeaCategory || detectedIdeaCategory;

  // 12-Sector, 36 Visual UI Blueprints, 12 Core Primitives, 9 Curated Tech Stack Presets & 15 Master Pillars (v8.0)
  const [matrixCatalog, setMatrixCatalog] = useState({
    sectors: [],
    primitives: [],
    rule_packs: [],
    stack_presets: [],
    stack_comparison_matrix: [],
    final_eight_layers: [],
    ten_perfection_pillars: [],
    five_lifecycle_features: [],
    five_deterministic_guarantees: [],
    five_enterprise_glue_primitives: [],
    fifteen_architecture_pillars: [],
    five_real_world_elements: [],
    sector_visual_ui_catalog: {},
    navigation_shell_options: {},
    storyboard_wizard: {},
    retention_and_security_matrix: {},
    community_primitives_marketplace: [],
    inclusive_value_metric_packages: {},
    stack_server_sizing: {},
    slop_diff_pairs: [],
  });
  // Progressive Disclosure Mode: 'express' (Founder-friendly default) | 'workbench' (7-Tab Senior Architect Workbench)
  const [matrixMode, setMatrixMode] = useState('express');
  const [activeWorkbenchTab, setActiveWorkbenchTab] = useState('sectors'); // 'sectors' | 'primitives' | 'stacks' | 'brownfield' | 'doctor' | 'menus_glue' | 'runtime_v7'
  const [v7RuntimePreview, setV7RuntimePreview] = useState(null);
  const [activeTopologyFilter, setActiveTopologyFilter] = useState('ALL');
  const [showStackMatrixTable, setShowStackMatrixTable] = useState(false);

  // Layer 8: Interactive Pre-Flight Blueprint Doctor State
  const [doctorLoading, setDoctorLoading] = useState(false);
  const [doctorReport, setDoctorReport] = useState(null);

  // Killer #4: Reverse-Engineer / Brownfield SQL & Migration Importer State
  const [showBrownfieldLab, setShowBrownfieldLab] = useState(false);
  const [brownfieldSqlText, setBrownfieldSqlText] = useState('');
  const [brownfieldLoading, setBrownfieldLoading] = useState(false);
  const [brownfieldReport, setBrownfieldReport] = useState(null);

  const runBrownfieldAudit = async () => {
    const trimmed = (brownfieldSqlText || '').trim();
    if (!trimmed) {
      if (window.showToast) {
        window.showToast({
          type: 'warning',
          title: lang === 'en' ? 'Empty Schema' : 'Skema Kosong',
          message: lang === 'en'
            ? 'Paste legacy CREATE TABLE SQL, Laravel migration, or Prisma schema first.'
            : 'Tempelkan CREATE TABLE SQL lama, migrasi Laravel, atau schema.prisma terlebih dahulu.',
        });
      }
      return;
    }

    setBrownfieldLoading(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const res = await fetch('/api/blueprint/brownfield-audit', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
        },
        body: JSON.stringify({
          schema_text: trimmed,
          project_hint: formData.namaBisnis || '',
        }),
      });
      const data = await res.json();
      if (res.ok && data?.success) {
        setBrownfieldReport(data);
        if (window.showToast) {
          window.showToast({
            type: 'success',
            title: lang === 'en' ? 'Brownfield Audit Complete' : 'Audit Skema Legacy Selesai',
            message: `${data.detected_tables_count || 0} tabel dipindai • Skor ${data.readiness_score}/100 (${data.violations_count} temuan).`,
          });
        }
      } else if (window.showToast) {
        window.showToast({
          type: 'error',
          title: lang === 'en' ? 'Audit Failed' : 'Gagal Audit Skema',
          message: data?.message || 'Tidak dapat memproses skema legacy.',
        });
      }
    } catch {
      if (window.showToast) {
        window.showToast({
          type: 'error',
          title: lang === 'en' ? 'Network Error' : 'Kendala Jaringan',
          message: 'Gagal menghubungi endpoint Brownfield Auditor.',
        });
      }
    } finally {
      setBrownfieldLoading(false);
    }
  };

  const applyBrownfieldRecommendation = () => {
    if (!brownfieldReport?.recommended_sector?.code) return;
    const recSector = brownfieldReport.recommended_sector.code;
    const recPrims = (brownfieldReport.recommended_primitives || []).map((p) => p.code);
    const planText = (brownfieldReport.zero_data_loss_migration_plan || []).join('\n');

    setFormData((prev) => {
      const existing = Array.isArray(prev.selectedPrimitives) ? prev.selectedPrimitives : [];
      const mergedPrims = Array.from(new Set([...recPrims, ...existing]));
      const nextMigrasi = prev.migrasiData
        ? `${prev.migrasiData}\n\n[Brownfield Shadow ULID Plan]\n${planText}`
        : `[Brownfield Shadow ULID Plan — Skor Awal ${brownfieldReport.readiness_score}/100]\n${planText}`;
      const next = {
        ...prev,
        primarySector: recSector,
        selectedPrimitives: mergedPrims,
        migrasiData: nextMigrasi,
      };
      triggerAutoSave(next);
      return next;
    });

    if (window.showToast) {
      window.showToast({
        type: 'success',
        title: lang === 'en' ? 'Applied to Blueprint' : 'Diterapkan ke Blueprint',
        message: lang === 'en'
          ? 'Sector, Core Primitives, and 4-Stage Shadow ULID Migration Plan injected into your form.'
          : 'Sektor utama, Core Primitives, dan rencana migrasi Shadow ULID 4 tahap berhasil disuntikkan ke form.',
      });
    }
  };

  useEffect(() => {
    let active = true;
    fetch('/api/blueprint/matrix-catalog', { headers: { Accept: 'application/json' } })
      .then((res) => res.ok ? res.json() : null)
      .then((json) => {
        if (active && json?.success && json.catalog) {
          setMatrixCatalog((prev) => ({ ...prev, ...json.catalog }));
          // Auto-hydrate mandatory primitives if sector came from Satu Pintu Wizard URL (?sector=...)
          const sectors = json.catalog.sectors || [];
          setFormData((prevForm) => {
            if (!prevForm.primarySector) return prevForm;
            const foundSec = sectors.find((s) => s.code === prevForm.primarySector);
            if (!foundSec || !Array.isArray(foundSec.mandatory_primitives)) return prevForm;
            const existing = Array.isArray(prevForm.selectedPrimitives) ? prevForm.selectedPrimitives : [];
            const merged = Array.from(new Set([...foundSec.mandatory_primitives, ...existing]));
            if (merged.length === existing.length) return prevForm;
            return { ...prevForm, selectedPrimitives: merged };
          });
        }
      })
      .catch(() => { });
    return () => { active = false; };
  }, []);

  const activeMatrixSectorObj = useMemo(() => {
    return (matrixCatalog.sectors || []).find((s) => s.code === formData.primarySector) || null;
  }, [matrixCatalog.sectors, formData.primarySector]);

  const activeStackPresetObj = useMemo(() => {
    const presets = matrixCatalog.stack_presets || [];
    return presets.find((p) => p.code === formData.stackPreset)
      || presets.find((p) => p.is_default)
      || presets[0]
      || null;
  }, [matrixCatalog.stack_presets, formData.stackPreset]);

  const filteredStackPresets = useMemo(() => {
    const presets = matrixCatalog.stack_presets || [];
    if (activeTopologyFilter === 'ALL') return presets;
    return presets.filter((p) => p.topology === activeTopologyFilter);
  }, [matrixCatalog.stack_presets, activeTopologyFilter]);

  const selectStackPreset = useCallback((presetCode) => {
    setFormData((prev) => {
      const next = { ...prev, stackPreset: presetCode };
      triggerAutoSave(next);
      return next;
    });
  }, []);

  const selectMatrixSector = useCallback((sectorCode) => {
    const sec = matrixCatalog.sectors.find((s) => s.code === sectorCode);
    const mandatory = sec?.mandatory_primitives || [];
    const defaultMenuCodes = (sec?.enterprise_menu_matrix?.all_menus_flat || []).map((m) => m.code);
    const defaultVariant = (matrixCatalog.sector_visual_ui_catalog?.[sectorCode]?.[0]) || null;
    setFormData((prev) => {
      const existing = Array.isArray(prev.selectedPrimitives) ? prev.selectedPrimitives : [];
      const mergedPrims = Array.from(new Set([...mandatory, ...existing]));
      const next = {
        ...prev,
        primarySector: sectorCode,
        selectedPrimitives: mergedPrims,
        selectedMenus: defaultMenuCodes,
        selectedUiVariant: defaultVariant?.code || '',
        publicUxPreset: resolveDefaultPublicUxForSector(sectorCode),
        navigationShell: defaultVariant?.default_shell || '',
      };
      triggerAutoSave(next);
      return next;
    });
  }, [matrixCatalog.sectors, matrixCatalog.sector_visual_ui_catalog]);

  // Tahap A (Progressive Disclosure Flow v12.0): Smart Idea Auto-Mapping to 12 Sectors, Primitives, L1-L4 Menus & Zone A+B UI
  const handleQuickDetectSectorFromIdea = useCallback(() => {
    const corpus = `${ideaText || ''} ${formData.namaBisnis || ''} ${formData.masalahUtama || ''}`.toLowerCase();
    const sectors = matrixCatalog.sectors || [];
    const findCode = (numPrefix, fallbackIdx = 0) => {
      const found = sectors.find((s) => String(s.code || '').includes(`SECTOR_${numPrefix}`));
      return found?.code || sectors[fallbackIdx]?.code || 'SECTOR_01_COMMERCE_RETAIL';
    };

    let matchedSector = findCode('01', 0);
    if (/(klinik|rumah sakit|dokter|pasien|rekam medis|rme|satusehat|bpjs|apotek|resep|gigi|odontogram)/i.test(corpus)) {
      matchedSector = findCode('04', 3);
    } else if (/(sekolah|kampus|kursus|bimbel|lms|ujian|cbt|sertifikat|guru|siswa|mahasiswa|pesantren)/i.test(corpus)) {
      matchedSector = findCode('05', 4);
    } else if (/(logistik|ekspedisi|kurir|armada|truk|kargo|pengiriman|resi|manifest|surat jalan|pod)/i.test(corpus)) {
      matchedSector = findCode('06', 5);
    } else if (/(pabrik|manufaktur|produksi|bom|bahan baku|perakitan|qc|mesin|work order)/i.test(corpus)) {
      matchedSector = findCode('07', 6);
    } else if (/(agensi|konsultan|firma hukum|pengacara|notaris|akuntan|kap|timesheet|retainer|milestone)/i.test(corpus)) {
      matchedSector = findCode('08', 7);
    } else if (/(properti|perumahan|konstruksi|kontraktor|kos|apartemen|kavling|rab proyek|mandor|kpr)/i.test(corpus)) {
      matchedSector = findCode('09', 8);
    } else if (/(koperasi|simpan pinjam|fintech|bmt|shu|angsuran|pinjaman|kredit|mikrofinansial)/i.test(corpus)) {
      matchedSector = findCode('10', 9);
    } else if (/(yayasan|ngo|zakat|wakaf|donasi|amal|masjid|gereja|rumah ibadah|panti asuhan|csr|hibah)/i.test(corpus)) {
      matchedSector = findCode('11', 10);
    } else if (/(pemda|pemerintah|dinas|desa|bumn|spbe|e-office|disposisi|perizinan|tte|asn)/i.test(corpus)) {
      matchedSector = findCode('12', 11);
    } else if (/(marketplace|multi-vendor|rekber|escrow|komisi penjual|lapak|split payment)/i.test(corpus)) {
      matchedSector = findCode('03', 2);
    } else if (/(toko|minimarket|supermarket|grosir|eceran|barcode|stok barang|gudang toko|distro)/i.test(corpus)) {
      matchedSector = findCode('02', 1);
    }

    selectMatrixSector(matchedSector);
    const secObj = sectors.find((s) => s.code === matchedSector);
    const secLabel = secObj?.name?.[lang] || secObj?.name?.id || matchedSector;

    showLocalToast(
      'success',
      lang === 'en'
        ? `Auto-mapped your idea to ${secLabel} + Zone A Cockpit + Zone B Public Scrollytelling + L1–L4 Menus!`
        : `Ide Anda otomatis dipetakan ke ${secLabel} + Kokpit Zona A + Scrollytelling Zona B + Peta Menu L1–L4!`,
      lang === 'en' ? 'STAGE A // AUTO SECTOR DETECTION' : 'TAHAP A // DETEKSI SEKTOR OTOMATIS'
    );
  }, [ideaText, formData.namaBisnis, formData.masalahUtama, matrixCatalog.sectors, selectMatrixSector, lang, showLocalToast]);

  const activeEnterpriseMenuMatrix = useMemo(() => {
    const secObj = activeMatrixSectorObj || matrixCatalog.sectors?.[0] || null;
    return secObj?.enterprise_menu_matrix || null;
  }, [activeMatrixSectorObj, matrixCatalog.sectors]);

  const effectiveSelectedMenus = useMemo(() => {
    const flatMenus = activeEnterpriseMenuMatrix?.all_menus_flat || [];
    const allCodes = flatMenus.map((m) => m.code);
    const explicit = Array.isArray(formData.selectedMenus) ? formData.selectedMenus : [];
    if (explicit.length === 0) {
      return allCodes;
    }
    const matchingCurrentSector = explicit.filter((c) => allCodes.includes(c));
    return matchingCurrentSector.length > 0 ? matchingCurrentSector : allCodes;
  }, [activeEnterpriseMenuMatrix, formData.selectedMenus]);

  const toggleEnterpriseMenuCode = useCallback((menuCode) => {
    setFormData((prev) => {
      const flatMenus = activeEnterpriseMenuMatrix?.all_menus_flat || [];
      const allCodes = flatMenus.map((m) => m.code);
      const base = Array.isArray(prev.selectedMenus) && prev.selectedMenus.some((c) => allCodes.includes(c))
        ? prev.selectedMenus.filter((c) => allCodes.includes(c))
        : allCodes;
      const nextMenus = base.includes(menuCode)
        ? base.filter((c) => c !== menuCode)
        : [...base, menuCode];
      const next = { ...prev, selectedMenus: nextMenus };
      triggerAutoSave(next);
      return next;
    });
  }, [activeEnterpriseMenuMatrix]);

  const injectEnterpriseMenusIntoMvp = useCallback(() => {
    const flatMenus = activeEnterpriseMenuMatrix?.all_menus_flat || [];
    const activeItems = flatMenus.filter((m) => effectiveSelectedMenus.includes(m.code));
    if (activeItems.length === 0) return;

    setFormData((prev) => {
      const existing = parseNumberedList(prev.fiturWajib);
      const existingLower = new Set(existing.map((it) => it.toLowerCase()));
      const additions = [];
      activeItems.forEach((m) => {
        const line = `[${m.code}] ${m.title}`;
        if (!existingLower.has(line.toLowerCase())) {
          additions.push(line);
        }
      });
      const mergedList = [...existing, ...additions];
      const next = { ...prev, fiturWajib: stringifyNumberedList(mergedList) };
      triggerAutoSave(next);
      return next;
    });

    showLocalToast(
      'success',
      lang === 'en'
        ? `${activeItems.length} L1–L4 Enterprise Sub-Menus embedded into Phase 1 MVP Features!`
        : `${activeItems.length} Sub-Menu Enterprise L1–L4 berhasil disematkan ke Fitur Wajib MVP!`,
      'L1–L4 MENU MATRIX V6.0'
    );
  }, [activeEnterpriseMenuMatrix, effectiveSelectedMenus, lang, showLocalToast]);

  const toggleMatrixPrimitive = useCallback((primCode) => {
    const mandatory = activeMatrixSectorObj?.mandatory_primitives || [];
    if (mandatory.includes(primCode)) return; // Mandatory primitives of primary sector remain locked
    setFormData((prev) => {
      const existing = Array.isArray(prev.selectedPrimitives) ? prev.selectedPrimitives : [];
      const nextPrims = existing.includes(primCode)
        ? existing.filter((c) => c !== primCode)
        : [...existing, primCode];
      const next = { ...prev, selectedPrimitives: nextPrims };
      triggerAutoSave(next);
      return next;
    });
  }, [activeMatrixSectorObj]);

  const effectivePrimitiveCodes = useMemo(() => {
    const mandatory = activeMatrixSectorObj?.mandatory_primitives || [];
    const selected = Array.isArray(formData.selectedPrimitives) ? formData.selectedPrimitives : [];
    return Array.from(new Set([...mandatory, ...selected]));
  }, [activeMatrixSectorObj, formData.selectedPrimitives]);

  // Cross-Stage Live Causality Sync (v7.0): Sector -> Primitives -> Menus -> Hardware & WebSocket -> DAG -> SOP -> RAB IDR & Scope Hash
  useEffect(() => {
    let active = true;
    const timer = setTimeout(() => {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      fetch('/api/blueprint/topological-preview', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
        },
        body: JSON.stringify({
          primary_sector: formData.primarySector || 'SECTOR_01_COMMERCE_RETAIL',
          selected_primitives: effectivePrimitiveCodes,
          stack_preset: formData.stackPreset || 'STACK_M1_LARAVEL_MONOLITH',
          selected_menus: effectiveSelectedMenus,
          project_name: formData.namaBisnis || '',
          selected_ui_variant: formData.selectedUiVariant || null,
          public_ux_preset: formData.publicUxPreset || resolveDefaultPublicUxForSector(formData.primarySector || 'SECTOR_01_COMMERCE_RETAIL'),
          navigation_shell: formData.navigationShell || null,
          output_locale: formData.outputLocale || 'id',
          persona_mode: formData.personaMode || 'BUSINESS_WIZARD',
        }),
      })
        .then((res) => (res.ok ? res.json() : null))
        .then((json) => {
          if (active && json?.success) {
            setV7RuntimePreview(json);
          }
        })
        .catch(() => { });
    }, 280);
    return () => {
      active = false;
      clearTimeout(timer);
    };
  }, [
    formData.primarySector,
    effectivePrimitiveCodes,
    formData.stackPreset,
    effectiveSelectedMenus,
    formData.namaBisnis,
    formData.selectedUiVariant,
    formData.publicUxPreset,
    formData.navigationShell,
    formData.outputLocale,
    formData.personaMode,
  ]);

  const estimatedTimelineDays = useMemo(() => {
    const raw = String(formData.targetWaktu || '').toLowerCase();
    const numMatch = raw.match(/(\d+)/);
    const n = numMatch ? parseInt(numMatch[1], 10) : 0;
    if (raw.includes('minggu') || raw.includes('week')) return n > 0 ? n * 7 : 21;
    if (raw.includes('bulan') || raw.includes('month')) return n > 0 ? n * 30 : 45;
    if (raw.includes('hari') || raw.includes('day')) return n > 0 ? n : 30;
    if (raw.includes('cepat') || raw.includes('urgent') || raw.includes('segera')) return 21;
    return 45;
  }, [formData.targetWaktu]);

  // Client-Side Instant Pre-Flight Telemetry (before or alongside server Blueprint Doctor)
  const livePreFlightHints = useMemo(() => {
    const corpus = `${ideaText || ''} ${formData.fiturWajib || ''} ${formData.alurKerja || ''} ${formData.fiturTambahan || ''}`.toLowerCase();
    const hints = [];
    const addPrims = [];
    let recommendedStack = null;

    if (
      /(resep|apotek|farmasi|stok|gudang|batch|fefo|fifo|sku|inventori|inventory)/i.test(corpus) &&
      !effectivePrimitiveCodes.includes('PRIMITIVE_INVENTORY_LEDGER')
    ) {
      hints.push({
        code: 'DOC_MISSING_INVENTORY_LEDGER',
        severity: 'CRITICAL',
        title: lang === 'en'
          ? 'Stock / Pharmacy Rx flow detected without Inventory Ledger'
          : 'Alur Resep / Stok Gudang terdeteksi tanpa PRIMITIVE_INVENTORY_LEDGER',
      });
      addPrims.push('PRIMITIVE_INVENTORY_LEDGER');
    }

    if (
      /(jurnal|pencairan|settlement|escrow|komisi|split bill|rekonsiliasi|wallet|dompet|hutang|piutang)/i.test(corpus) &&
      !effectivePrimitiveCodes.includes('PRIMITIVE_DOUBLE_ENTRY_LEDGER')
    ) {
      hints.push({
        code: 'DOC_MISSING_DOUBLE_ENTRY_LEDGER',
        severity: 'CRITICAL',
        title: lang === 'en'
          ? 'Settlement / Commission / Wallet flow detected without Double-Entry Ledger'
          : 'Alur Settlement / Komisi / Dompet terdeteksi tanpa PRIMITIVE_DOUBLE_ENTRY_LEDGER',
      });
      addPrims.push('PRIMITIVE_DOUBLE_ENTRY_LEDGER');
    }

    if (
      /(approval|persetujuan|otorisasi|berjenjang|maker-checker|direksi|kepala cabang)/i.test(corpus) &&
      !effectivePrimitiveCodes.includes('PRIMITIVE_APPROVAL_WORKFLOW')
    ) {
      hints.push({
        code: 'DOC_MISSING_APPROVAL_WORKFLOW',
        severity: 'WARNING',
        title: lang === 'en'
          ? 'Multi-level approval detected without Approval Workflow Engine'
          : 'Alur Otorisasi Berjenjang terdeteksi tanpa PRIMITIVE_APPROVAL_WORKFLOW',
      });
      addPrims.push('PRIMITIVE_APPROVAL_WORKFLOW');
    }

    const currentStack = formData.stackPreset || 'STACK_M1_LARAVEL_MONOLITH';
    if (
      (currentStack.startsWith('STACK_S1') || currentStack.startsWith('STACK_S2')) &&
      estimatedTimelineDays <= 35
    ) {
      hints.push({
        code: 'DOC_TOPOLOGY_TIMELINE_MISMATCH',
        severity: 'WARNING',
        title: lang === 'en'
          ? `Microservices selected on tight ${estimatedTimelineDays}d timeline — M1 Modular Monolith recommended`
          : `Topologi Microservices pada target ketat ${estimatedTimelineDays} hari — disarankan M1 Modular Monolith`,
      });
      recommendedStack = 'STACK_M1_LARAVEL_MONOLITH';
    }

    return {
      hints,
      addPrims: Array.from(new Set(addPrims)),
      recommendedStack,
    };
  }, [ideaText, formData.fiturWajib, formData.alurKerja, formData.fiturTambahan, formData.stackPreset, effectivePrimitiveCodes, estimatedTimelineDays, lang]);

  const runPreFlightDoctor = async () => {
    setDoctorLoading(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const workflowCorpus = [
        ideaText,
        formData.namaBisnis,
        formData.masalahUtama,
        formData.fiturWajib,
        formData.alurKerja,
        formData.fiturTambahan,
      ].filter(Boolean).join('\n');

      const res = await fetch('/api/blueprint/doctor', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
        },
        body: JSON.stringify({
          sector_code: formData.primarySector || (matrixCatalog.sectors?.[0]?.code ?? 'SECTOR_01_COMMERCE_OMNICHANNEL'),
          primitive_codes: effectivePrimitiveCodes,
          stack_preset_code: formData.stackPreset || 'STACK_M1_LARAVEL_MONOLITH',
          workflow_text: workflowCorpus,
          timeline_days: estimatedTimelineDays,
        }),
      });
      const json = await res.json();
      if (res.ok && json?.success && json.doctor) {
        setDoctorReport(json.doctor);
        const bannerMsg = typeof json.doctor.summary_banner === 'object'
          ? (json.doctor.summary_banner?.[lang] || json.doctor.summary_banner?.id || '')
          : (json.doctor.summary_banner || '');
        if (window.showToast) {
          window.showToast({
            type: json.doctor.status === 'OPTIMAL_READY' ? 'success' : 'warning',
            title: `BLUEPRINT DOCTOR: ${json.doctor.readiness_score}/100`,
            message: bannerMsg || 'Diagnosa Pre-Flight arsitektur selesai.',
          });
        }
      }
    } catch {
      if (window.showToast) {
        window.showToast({
          type: 'error',
          title: lang === 'en' ? 'Doctor Unavailable' : 'Gagal Menjalankan Doctor',
          message: lang === 'en' ? 'Could not reach Pre-Flight Blueprint Doctor endpoint.' : 'Tidak dapat menghubungi endpoint Blueprint Doctor.',
        });
      }
    } finally {
      setDoctorLoading(false);
    }
  };

  const applyDoctorAutoFix = () => {
    const serverFix = doctorReport?.one_click_auto_fix;
    const serverHasFix = !!(serverFix?.has_fixes || serverFix?.available);
    const primsToAdd = serverHasFix
      ? (serverFix.add_primitive_codes || [])
      : livePreFlightHints.addPrims;
    const nextStack = serverHasFix
      ? (serverFix.recommended_stack_preset_code || null)
      : livePreFlightHints.recommendedStack;

    if (primsToAdd.length === 0 && !nextStack) return;

    setFormData((prev) => {
      const existing = Array.isArray(prev.selectedPrimitives) ? prev.selectedPrimitives : [];
      const merged = Array.from(new Set([...existing, ...primsToAdd]));
      const next = {
        ...prev,
        selectedPrimitives: merged,
        ...(nextStack ? { stackPreset: nextStack } : {}),
      };
      triggerAutoSave(next);
      return next;
    });

    setDoctorReport(null);
    if (window.showToast) {
      window.showToast({
        type: 'success',
        title: lang === 'en' ? '1-Click Auto-Fix Applied' : '1-Klik Auto-Fix Diterapkan',
        message: lang === 'en'
          ? 'Missing Core Primitives & optimal Topology Preset have been locked into your blueprint.'
          : 'Modul Primitif yang kurang & rekomendasi Topologi Stack berhasil dikunci otomatis ke blueprint Anda.',
      });
    }
  };

  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState(null);
  const [invalidFields, setInvalidFields] = useState([]);
  const [strokeActive, setStrokeActive] = useState(false);
  const strokeTimerRef = useRef(null);
  const [successData, setSuccessData] = useState(null);
  const isLocked = !!successData;

  // Dynamic Completeness Score
  const completeness = useMemo(() => {
    return calculateCompleteness(formData, lang);
  }, [formData, lang]);

  // Floating Left Index & Scroll-Spy States
  const [activeSection, setActiveSection] = useState('section-ide-studio');
  const [activeField, setActiveField] = useState(null);
  const [autoSyncAccordion, setAutoSyncAccordion] = useState(true);
  const activeSectionRef = useRef('section-ide-studio');
  const autoSyncSpyRef = useRef(true);
  const desktopScrollRef = useRef(null);

  useEffect(() => {
    autoSyncSpyRef.current = autoSyncAccordion;
  }, [autoSyncAccordion]);

  const [isIndexExpanded, setIsIndexExpanded] = useState(() => {
    if (typeof window !== 'undefined') {
      return window.innerWidth >= 1280;
    }
    return true;
  });
  const [mobileDrawerOpen, setMobileDrawerOpen] = useState(false);
  const [indexSearchQuery, setIndexSearchQuery] = useState('');
  const [openAccordionGroups, setOpenAccordionGroups] = useState({
    studio: true,
    spec1: false,
    spec2: false,
    final: false,
  });

  const sectionGroupMap = useMemo(() => ({
    'section-ide-studio': 'studio',
    'section-ai-assistant': 'studio',
    'section-composable-matrix': 'studio',
    'section-block-a': 'spec1',
    'section-block-b': 'spec1',
    'section-block-c': 'spec1',
    'section-block-d': 'spec2',
    'section-block-e': 'spec2',
    'section-block-f': 'spec2',
    'section-submit': 'final',
  }), []);

  const syncAccordionToSection = useCallback((secId) => {
    const targetGroup = sectionGroupMap[secId];
    if (!targetGroup) return;

    // Auto-collapse inactive groups, expand active group
    setOpenAccordionGroups({
      studio: targetGroup === 'studio',
      spec1: targetGroup === 'spec1',
      spec2: targetGroup === 'spec2',
      final: targetGroup === 'final',
    });

    // Auto-collapse inactive sub-blocks, expand active section sub-block
    setExpandedSubBlocks({
      [secId]: true,
    });

    // Keep active index item in viewport within sidebar
    setTimeout(() => {
      const container = desktopScrollRef.current;
      if (!container) return;
      const activeEl = container.querySelector(`[data-nav-sec="${secId}"]`);
      if (!activeEl) return;

      const containerRect = container.getBoundingClientRect();
      const elRect = activeEl.getBoundingClientRect();

      if (elRect.top < containerRect.top + 20 || elRect.bottom > containerRect.bottom - 20) {
        const targetScrollTop = container.scrollTop + (elRect.top - containerRect.top) - (container.clientHeight / 2) + (elRect.height / 2);
        container.scrollTo({ top: Math.max(0, targetScrollTop), behavior: 'smooth' });
      }
    }, 60);
  }, [sectionGroupMap]);

  const toggleAccordionGroup = useCallback((grp) => {
    setOpenAccordionGroups(prev => ({ ...prev, [grp]: !prev[grp] }));
  }, []);

  const expandAllGroups = useCallback(() => {
    setAutoSyncAccordion(false);
    setOpenAccordionGroups({
      studio: true,
      spec1: true,
      spec2: true,
      final: true,
    });
  }, []);

  const collapseAllGroups = useCallback(() => {
    setAutoSyncAccordion(false);
    setOpenAccordionGroups({
      studio: false,
      spec1: false,
      spec2: false,
      final: false,
    });
    setExpandedSubBlocks({});
  }, []);

  // Sub-block detail toggle state
  const [expandedSubBlocks, setExpandedSubBlocks] = useState({
    'section-ide-studio': true,
  });

  const toggleSubBlock = useCallback((secId, e) => {
    e?.stopPropagation();
    setExpandedSubBlocks(prev => ({
      ...prev,
      [secId]: !prev[secId],
    }));
  }, []);

  const [indexTopOffset, setIndexTopOffset] = useState(() => {
    if (typeof window !== 'undefined') {
      const heroEl = document.getElementById('blueprint-hero');
      if (heroEl) {
        return Math.max(heroEl.getBoundingClientRect().bottom + 16, 68);
      }
    }
    return 360;
  });

  const jumpToSection = useCallback((id) => {
    if (id === 'section-ide-studio') {
      setIsStudioOpen(true);
    }
    const el = document.getElementById(id);
    if (!el) return;
    const yOffset = -90;
    const y = el.getBoundingClientRect().top + window.pageYOffset + yOffset;
    window.scrollTo({ top: y, behavior: 'smooth' });
    setActiveSection(id);
    activeSectionRef.current = id;
    syncAccordionToSection(id);
    setMobileDrawerOpen(false);

    // Visual pulse outline
    el.classList.add('outline-2', 'outline-emerald-500', 'transition-all');
    setTimeout(() => {
      el.classList.remove('outline-2', 'outline-emerald-500');
    }, 2000);
  }, [syncAccordionToSection]);

  const jumpToField = useCallback((fieldIdOrKey, explicitSectionId) => {
    const rawKey = String(fieldIdOrKey || '').replace(/^field-/, '');
    const canonicalKey = FIELD_MAP[rawKey] || rawKey;
    const finalDomId = String(fieldIdOrKey || '').startsWith('field-') ? fieldIdOrKey : `field-${canonicalKey}`;
    const sectionId = explicitSectionId || FIELD_SECTION_MAP[canonicalKey];

    if (sectionId === 'section-ide-studio') {
      setIsStudioOpen(true);
    }
    const el = document.getElementById(finalDomId);
    if (el) {
      const yOffset = -130;
      const y = el.getBoundingClientRect().top + window.pageYOffset + yOffset;
      window.scrollTo({ top: Math.max(0, y), behavior: 'smooth' });
      if (sectionId) {
        setActiveSection(sectionId);
        activeSectionRef.current = sectionId;
        syncAccordionToSection(sectionId);
      }
      setActiveField(finalDomId);
      setMobileDrawerOpen(false);

      // Flash highlight & stroke alert
      el.classList.add('field-stroke-alert', 'field-stroke-pulsing');
      setTimeout(() => {
        el.classList.remove('field-stroke-pulsing');
      }, 4000);

      // Focus if input or textarea
      const inputEl = el.querySelector('input, textarea, select') || (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' ? el : null);
      if (inputEl) {
        try {
          inputEl.focus({ preventScroll: true });
        } catch (_) { }
      }
    } else if (sectionId) {
      jumpToSection(sectionId);
    }
  }, [jumpToSection, syncAccordionToSection]);

  const sectionIds = useMemo(() => [
    'section-ide-studio',
    'section-ai-assistant',
    'section-composable-matrix',
    'section-block-a',
    'section-block-b',
    'section-block-c',
    'section-block-d',
    'section-block-e',
    'section-block-f',
    'section-submit'
  ], []);

  const allFieldIds = useMemo(() => [
    'field-ideaText', 'field-attachedFiles', 'field-proactiveSuggestions',
    'field-namaBisnis', 'field-masalahUtama', 'field-tujuanUtama',
    'field-targetAudiens', 'field-aktorSistem', 'field-targetPlatform',
    'field-fiturWajib', 'field-fiturTambahan', 'field-alurKerja', 'field-migrasiData',
    'field-kebutuhanIntegrasi', 'field-referensiDesain', 'field-kesiapanAset', 'field-preferensiHosting',
    'field-targetWaktu', 'field-skalaPengguna', 'field-outOfScope', 'field-kepatuhanKeamanan',
    'field-kisaranBudget', 'field-garansiSla', 'field-terminPembayaran', 'field-clientName', 'field-email', 'field-phone',
    'field-readinessScore', 'field-lockBtn'
  ], []);

  useEffect(() => {
    let ticking = false;
    const handleScroll = () => {
      if (!ticking) {
        window.requestAnimationFrame(() => {
          // Dynamic calculation: index stops beneath hero area when hero is visible, clamped nicely
          const heroEl = document.getElementById('blueprint-hero');
          const minTop = 68; // sticky header is ~56px + 12px gap
          if (heroEl) {
            const rect = heroEl.getBoundingClientRect();
            // Guarantee index never overlaps or enters the hero area
            const calculatedTop = Math.max(rect.bottom + 16, minTop);
            setIndexTopOffset(calculatedTop);
          } else {
            setIndexTopOffset(minTop);
          }

          // Active Section & Field Scroll Spy
          const triggerOffset = 220; // reading line in viewport
          const isBottom = (window.innerHeight + window.scrollY) >= (document.documentElement.scrollHeight - 80);

          let current = sectionIds[0];
          if (isBottom) {
            current = 'section-submit';
          } else {
            for (let i = sectionIds.length - 1; i >= 0; i--) {
              const el = document.getElementById(sectionIds[i]);
              if (el) {
                const rect = el.getBoundingClientRect();
                if (rect.top <= triggerOffset) {
                  current = sectionIds[i];
                  break;
                }
              }
            }
          }

          // Field/sub-item active spy
          let currentField = null;
          for (let i = allFieldIds.length - 1; i >= 0; i--) {
            const el = document.getElementById(allFieldIds[i]);
            if (el) {
              const rect = el.getBoundingClientRect();
              if (rect.top <= triggerOffset + 40) {
                currentField = allFieldIds[i];
                break;
              }
            }
          }
          if (currentField) {
            setActiveField(currentField);
          }

          if (current !== activeSectionRef.current) {
            activeSectionRef.current = current;
            setActiveSection(current);

            // Auto-collapse inactive groups/sub-blocks and expand active if autoSync is active
            if (autoSyncSpyRef.current) {
              syncAccordionToSection(current);
            }
          }

          ticking = false;
        });
        ticking = true;
      }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('resize', handleScroll, { passive: true });
    handleScroll();
    return () => {
      window.removeEventListener('scroll', handleScroll);
      window.removeEventListener('resize', handleScroll);
    };
  }, [sectionIds, allFieldIds, syncAccordionToSection]);

  const blockCompletion = useMemo(() => {
    const check = (fields) => {
      let filled = 0;
      fields.forEach(f => {
        const val = formData[f];
        if (Array.isArray(val) ? val.length > 0 : !!val && String(val).trim().length > 0) {
          filled++;
        }
      });
      return { filled, total: fields.length, isComplete: filled === fields.length };
    };

    return {
      blockA: check(['namaBisnis', 'masalahUtama', 'tujuanUtama']),
      blockB: check(['targetAudiens', 'aktorSistem', 'targetPlatform']),
      blockC: check(['fiturWajib', 'fiturTambahan', 'alurKerja', 'migrasiData']),
      blockD: check(['kebutuhanIntegrasi', 'referensiDesain', 'kesiapanAset', 'preferensiHosting']),
      blockE: check(['targetWaktu', 'skalaPengguna', 'outOfScope', 'kepatuhanKeamanan']),
      blockF: check(['kisaranBudget', 'garansiSla', 'terminPembayaran', 'clientName', 'email', 'phone']),
    };
  }, [formData]);

  const indexSections = useMemo(() => [
    {
      id: 'section-ide-studio',
      badge: '⚡',
      group: 'studio',
      title: lang === 'en' ? 'Quick Idea & MarkItDown' : 'Studio Ide & MarkItDown',
      subtitle: lang === 'en' ? 'Vision prompt, document upload, auto-fill' : 'Input ide, upload dokumen, ekstraksi AI',
      countLabel: null,
      isComplete: !!(ideaText && ideaText.length > 20),
      subItems: [
        {
          id: 'field-ideaText',
          code: 'S1',
          label: lang === 'en' ? 'Project Vision Prompt' : 'Teks Visi & Ringkasan Ide',
          isFilled: !!(ideaText && ideaText.trim().length >= 15),
        },
        {
          id: 'field-attachedFiles',
          code: 'S2',
          label: lang === 'en' ? 'MarkItDown Document Attachments' : 'Lampiran Dokumen (MarkItDown)',
          isFilled: attachedFiles.length > 0,
        },
      ],
    },
    {
      id: 'section-ai-assistant',
      badge: '🤖',
      group: 'studio',
      title: lang === 'en' ? 'AI Assistant Co-Pilot' : 'Asisten Proaktif AI',
      subtitle: lang === 'en' ? 'Architecture suggestions & auto-tuning' : 'Rekomendasi arsitektur & panduan sistem',
      countLabel: null,
      isComplete: proactiveSuggestions.length > 0,
      subItems: [
        {
          id: 'field-proactiveSuggestions',
          code: 'P1',
          label: lang === 'en' ? 'Smart Recommendations' : 'Rekomendasi Pintar AI',
          isFilled: proactiveSuggestions.length > 0,
        },
      ],
    },
    {
      id: 'section-composable-matrix',
      badge: '🧩',
      group: 'studio',
      title: lang === 'en' ? '12-Sector Matrix & Doctor v3.0' : 'Matriks 12-Sektor & Doctor v3.0',
      subtitle: lang === 'en' ? 'Sector UI, Primitives, 9 Stacks & Day-2 Ops' : 'UI Industri, Primitives, 9 Stack & 8 Layer Day-2',
      countLabel: `${effectivePrimitiveCodes.length} MOD`,
      isComplete: !!formData.primarySector || effectivePrimitiveCodes.length > 0,
      subItems: [
        {
          id: 'section-composable-matrix',
          code: 'M1',
          label: lang === 'en' ? '12 Sectors & 9 Stack Adapters' : '12 Sektor Bisnis & 9 Adapter Stack',
          isFilled: !!formData.primarySector || !!formData.stackPreset,
        },
        {
          id: 'section-composable-matrix',
          code: 'M2',
          label: lang === 'en' ? 'Pre-Flight Blueprint Doctor & Day-2' : 'Blueprint Doctor & 8 Layer Day-2 Ops',
          isFilled: livePreFlightHints.hints.length === 0,
        },
      ],
    },
    {
      id: 'section-block-a',
      badge: 'A',
      group: 'spec1',
      title: lang === 'en' ? 'Block A: Identity & Goals' : 'Blok A: Identitas & Tujuan',
      subtitle: lang === 'en' ? 'Name, core problem, KPIs' : 'Nama bisnis, masalah utama, KPI',
      countLabel: `${blockCompletion.blockA.filled}/${blockCompletion.blockA.total}`,
      isComplete: blockCompletion.blockA.isComplete,
      subItems: [
        {
          id: 'field-namaBisnis',
          code: 'A1',
          label: lang === 'en' ? 'Application / Domain Name' : 'Nama Aplikasi / Domain Bisnis',
          isFilled: !!formData.namaBisnis && formData.namaBisnis.trim().length >= 3,
        },
        {
          id: 'field-masalahUtama',
          code: 'A2',
          label: lang === 'en' ? 'Core Problem & Pain Points' : 'Masalah Utama & Pain Points',
          isFilled: !!formData.masalahUtama && formData.masalahUtama.trim().length >= 10,
        },
        {
          id: 'field-tujuanUtama',
          code: 'A3',
          label: lang === 'en' ? 'Success Metrics (KPIs)' : 'Tolak Ukur Sukses (Target KPI)',
          isFilled: !!formData.tujuanUtama && formData.tujuanUtama.trim().length >= 5,
        },
      ],
    },
    {
      id: 'section-block-b',
      badge: 'B',
      group: 'spec1',
      title: lang === 'en' ? 'Block B: Target RBAC & Platform' : 'Blok B: Target RBAC & Platform',
      subtitle: lang === 'en' ? 'Audience, user roles, devices' : 'Profil audiens, aktor RBAC, perangkat',
      countLabel: `${blockCompletion.blockB.filled}/${blockCompletion.blockB.total}`,
      isComplete: blockCompletion.blockB.isComplete,
      subItems: [
        {
          id: 'field-targetAudiens',
          code: 'B1',
          label: lang === 'en' ? 'Target Audience Profile' : 'Profil Target Audiens',
          isFilled: !!formData.targetAudiens && formData.targetAudiens.trim().length >= 5,
        },
        {
          id: 'field-aktorSistem',
          code: 'B2',
          label: lang === 'en' ? 'User Roles & RBAC Matrix' : 'Aktor Sistem & Hak Akses RBAC',
          isFilled: !!formData.aktorSistem && formData.aktorSistem.trim().length >= 10,
        },
        {
          id: 'field-targetPlatform',
          code: 'B3',
          label: lang === 'en' ? 'Platform Form Factors' : 'Platform & Perangkat Sasaran',
          isFilled: !!formData.targetPlatform && formData.targetPlatform.trim().length >= 3,
        },
      ],
    },
    {
      id: 'section-block-c',
      badge: 'C',
      group: 'spec1',
      title: lang === 'en' ? 'Block C: MVP Features & Flow' : 'Blok C: Fitur MVP & Alur Kerja',
      subtitle: lang === 'en' ? 'Priority features, user flow, migration' : 'Fitur MVP, diagram alur, data lama',
      countLabel: `${blockCompletion.blockC.filled}/${blockCompletion.blockC.total}`,
      isComplete: blockCompletion.blockC.isComplete,
      subItems: [
        {
          id: 'field-fiturWajib',
          code: 'C1',
          label: lang === 'en' ? 'Phase 1 MVP Features' : 'Fitur Utama Wajib MVP (Fase 1)',
          isFilled: !!formData.fiturWajib && formData.fiturWajib.trim().length >= 10,
        },
        {
          id: 'field-fiturTambahan',
          code: 'C2',
          label: lang === 'en' ? 'Phase 2 Feature Roadmap' : 'Roadmap Fitur (Fase 2)',
          isFilled: !!formData.fiturTambahan && formData.fiturTambahan.trim().length >= 5,
        },
        {
          id: 'field-alurKerja',
          code: 'C3',
          label: lang === 'en' ? 'Operational User Flow' : 'Alur Kerja Operasional Utama',
          isFilled: !!formData.alurKerja && formData.alurKerja.trim().length >= 10,
        },
        {
          id: 'field-migrasiData',
          code: 'C4',
          label: lang === 'en' ? 'Legacy Data Migration' : 'Migrasi Data Warisan (Excel/DB)',
          isFilled: !!formData.migrasiData && formData.migrasiData.trim().length >= 3,
        },
      ],
    },
    {
      id: 'section-block-d',
      badge: 'D',
      group: 'spec2',
      title: lang === 'en' ? 'Block D: Integrations & Hosting' : 'Blok D: Integrasi & Hosting',
      subtitle: lang === 'en' ? 'API gateways, server, UI/UX reference' : 'Integrasi API, server, preferensi UI/UX',
      countLabel: `${blockCompletion.blockD.filled}/${blockCompletion.blockD.total}`,
      isComplete: blockCompletion.blockD.isComplete,
      subItems: [
        {
          id: 'field-kebutuhanIntegrasi',
          code: 'D1',
          label: lang === 'en' ? 'Third-Party Gateways & APIs' : 'Integrasi API & Payment Gateway',
          isFilled: !!formData.kebutuhanIntegrasi && formData.kebutuhanIntegrasi.trim().length >= 3,
        },
        {
          id: 'field-referensiDesain',
          code: 'D2',
          label: lang === 'en' ? 'UI/UX Design References' : 'Tolak Ukur & Referensi Desain',
          isFilled: !!formData.referensiDesain && formData.referensiDesain.trim().length >= 3,
        },
        {
          id: 'field-kesiapanAset',
          code: 'D3',
          label: lang === 'en' ? 'Brand & Content Readiness' : 'Kesiapan Brand & Konten/Aset',
          isFilled: !!formData.kesiapanAset && formData.kesiapanAset.trim().length >= 3,
        },
        {
          id: 'field-preferensiHosting',
          code: 'D4',
          label: lang === 'en' ? 'Cloud & Server Hosting' : 'Infrastruktur & Cloud Hosting',
          isFilled: !!formData.preferensiHosting && formData.preferensiHosting.trim().length >= 3,
        },
      ],
    },
    {
      id: 'section-block-e',
      badge: 'E',
      group: 'spec2',
      title: lang === 'en' ? 'Block E: Scale & Scope Freeze' : 'Blok E: Skala & Batasan Scope',
      subtitle: lang === 'en' ? 'Timeline, security, out-of-scope' : 'Target waktu, security, scope freeze',
      countLabel: `${blockCompletion.blockE.filled}/${blockCompletion.blockE.total}`,
      isComplete: blockCompletion.blockE.isComplete,
      subItems: [
        {
          id: 'field-targetWaktu',
          code: 'E1',
          label: lang === 'en' ? 'Target Go-Live Timeline' : 'Target Peluncuran (Go-Live)',
          isFilled: !!formData.targetWaktu && formData.targetWaktu.trim().length >= 3,
        },
        {
          id: 'field-skalaPengguna',
          code: 'E2',
          label: lang === 'en' ? 'Traffic & Data Concurrency' : 'Estimasi Trafik & Data Konkuren',
          isFilled: !!formData.skalaPengguna && formData.skalaPengguna.trim().length >= 3,
        },
        {
          id: 'field-outOfScope',
          code: 'E3',
          label: lang === 'en' ? 'Scope Boundaries (Anti-Creep)' : 'Batasan Scope (Scope Freeze)',
          isFilled: !!formData.outOfScope && formData.outOfScope.trim().length >= 3,
        },
        {
          id: 'field-kepatuhanKeamanan',
          code: 'E4',
          label: lang === 'en' ? 'Security & OWASP Compliance' : 'Standar Keamanan & Kepatuhan',
          isFilled: !!formData.kepatuhanKeamanan && formData.kepatuhanKeamanan.trim().length >= 3,
        },
      ],
    },
    {
      id: 'section-block-f',
      badge: 'F',
      group: 'spec2',
      title: lang === 'en' ? 'Block F: Budget, SLA & PIC' : 'Blok F: Anggaran, SLA & PIC',
      subtitle: lang === 'en' ? 'Budget, warranty SLA, terms, PIC info' : 'Alokasi budget, SLA garansi, termin, PIC',
      countLabel: `${blockCompletion.blockF.filled}/${blockCompletion.blockF.total}`,
      isComplete: blockCompletion.blockF.isComplete,
      subItems: [
        {
          id: 'field-kisaranBudget',
          code: 'F1',
          label: lang === 'en' ? 'Project Budget Allocation' : 'Alokasi Anggaran Investasi',
          isFilled: !!formData.kisaranBudget && formData.kisaranBudget.trim().length >= 3,
        },
        {
          id: 'field-garansiSla',
          code: 'F2',
          label: lang === 'en' ? 'Post-Launch Bug Warranty SLA' : 'Garansi Bug Pasca-Peluncuran',
          isFilled: !!formData.garansiSla && formData.garansiSla.trim().length >= 3,
        },
        {
          id: 'field-terminPembayaran',
          code: 'F3',
          label: lang === 'en' ? 'Payment Milestone Terms' : 'Termin Pembayaran Proyek',
          isFilled: !!formData.terminPembayaran && formData.terminPembayaran.trim().length >= 3,
        },
        {
          id: 'field-clientName',
          code: 'F4',
          label: lang === 'en' ? 'PIC Full Name' : 'Nama Lengkap PIC Penanggung Jawab',
          isFilled: !!formData.clientName && formData.clientName.trim().length >= 3,
        },
        {
          id: 'field-email',
          code: 'F5',
          label: lang === 'en' ? 'Official PIC Email' : 'Alamat Email Resmi PIC',
          isFilled: !!formData.email && formData.email.includes('@'),
        },
        {
          id: 'field-phone',
          code: 'F6',
          label: lang === 'en' ? 'Active PIC WhatsApp Number' : 'Nomor WhatsApp Aktif PIC',
          isFilled: !!formData.phone && formData.phone.length >= 8,
        },
      ],
    },
    {
      id: 'section-submit',
      badge: '🔒',
      group: 'final',
      title: lang === 'en' ? 'Lock Blueprint & Generate PRD' : 'Kunci Blueprint & Terbitkan PRD',
      subtitle: lang === 'en' ? 'Final lock & PostgreSQL schema' : 'Kunci spesifikasi & skema PostgreSQL',
      countLabel: `${completeness.score}%`,
      isComplete: completeness.score >= 90,
      subItems: [
        {
          id: 'field-readinessScore',
          code: 'L1',
          label: lang === 'en' ? 'Readiness Score >= 90%' : 'Skor Kesiapan >= 90%',
          isFilled: completeness.score >= 90,
        },
        {
          id: 'field-lockBtn',
          code: 'L2',
          label: lang === 'en' ? 'Lock & Issue PRD' : 'Kunci & Terbitkan Dokumen PRD',
          isFilled: isLocked,
        },
      ],
    },
  ], [lang, ideaText, attachedFiles, proactiveSuggestions, blockCompletion, completeness, formData, isLocked, effectivePrimitiveCodes, livePreFlightHints]);

  const isAllSubExpanded = useMemo(() => {
    return indexSections.every(sec => !sec.subItems || sec.subItems.length === 0 || expandedSubBlocks[sec.id]);
  }, [indexSections, expandedSubBlocks]);

  const toggleAllSubBlocks = useCallback(() => {
    if (isAllSubExpanded) {
      setExpandedSubBlocks({});
    } else {
      const next = {};
      indexSections.forEach(sec => {
        if (sec.subItems && sec.subItems.length > 0) {
          next[sec.id] = true;
        }
      });
      setExpandedSubBlocks(next);
    }
  }, [isAllSubExpanded, indexSections]);

  const isSubBlockOpen = useCallback((secId) => {
    if (indexSearchQuery.trim()) return true;
    return !!expandedSubBlocks[secId];
  }, [indexSearchQuery, expandedSubBlocks]);

  const accordionGroups = useMemo(() => [
    {
      key: 'studio',
      title: t.indexGroupStudio,
      desc: t.indexGroupStudioDesc,
      itemCount: 3,
    },
    {
      key: 'spec1',
      title: t.indexGroupSpec1,
      desc: t.indexGroupSpec1Desc,
      itemCount: 3,
    },
    {
      key: 'spec2',
      title: t.indexGroupSpec2,
      desc: t.indexGroupSpec2Desc,
      itemCount: 3,
    },
    {
      key: 'final',
      title: t.indexGroupFinal,
      desc: t.indexSubmitDesc,
      itemCount: 1,
    },
  ], [t]);

  const getActiveSectionTitle = useCallback(() => {
    const map = {
      'section-ide-studio': lang === 'en' ? 'Quick Idea Studio' : 'Studio Ide & MarkItDown',
      'section-ai-assistant': lang === 'en' ? 'AI Co-Pilot' : 'Asisten Proaktif AI',
      'section-composable-matrix': lang === 'en' ? '12-Sector Matrix & Doctor' : 'Matriks 12-Sektor & Doctor',
      'section-block-a': lang === 'en' ? 'Block A: Identity' : 'Blok A: Identitas Bisnis',
      'section-block-b': lang === 'en' ? 'Block B: RBAC & Platform' : 'Blok B: RBAC & Platform',
      'section-block-c': lang === 'en' ? 'Block C: MVP Features' : 'Blok C: Fitur MVP & Alur',
      'section-block-d': lang === 'en' ? 'Block D: Integrations' : 'Blok D: Integrasi & Server',
      'section-block-e': lang === 'en' ? 'Block E: Scale & Scope' : 'Blok E: Skala & Batasan',
      'section-block-f': lang === 'en' ? 'Block F: Budget & PIC' : 'Blok F: Anggaran & PIC',
      'section-submit': lang === 'en' ? 'Lock & Submit' : 'Kunci Spesifikasi PRD',
    };
    return map[activeSection] || (lang === 'en' ? 'Directory' : 'Index Blok');
  }, [activeSection, lang]);

  const filteredIndexSections = useMemo(() => {
    if (!indexSearchQuery.trim()) return indexSections;
    const q = indexSearchQuery.toLowerCase();
    return indexSections.filter(sec =>
      sec.title.toLowerCase().includes(q) ||
      sec.subtitle.toLowerCase().includes(q) ||
      sec.badge.toLowerCase().includes(q) ||
      (sec.subItems && sec.subItems.some(sub =>
        sub.label.toLowerCase().includes(q) ||
        sub.code.toLowerCase().includes(q)
      ))
    );
  }, [indexSections, indexSearchQuery]);

  // Initial local storage hydration (v6.0 Hermetic Workspace Isolation)
  useEffect(() => {
    // Theme setup
    const savedTheme = localStorage.getItem('neriah_theme');
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      setIsDarkMode(true);
      document.documentElement.classList.add('dark');
    } else {
      setIsDarkMode(false);
      document.documentElement.classList.remove('dark');
    }

    // Purge legacy un-namespaced global autosave key so old projects NEVER bleed into new projects
    try {
      localStorage.removeItem('neriah_blueprint_autosave');
    } catch (e) { }

    // Scoped Draft Restoration ONLY when resuming an explicit slug or explicit draft_id (never on fresh workspace)
    if (!isFreshWorkspace && !initialData.namaBisnis) {
      try {
        const localSaved = localStorage.getItem(getScopedStorageKey());
        if (localSaved) {
          const parsed = JSON.parse(localSaved);
          if (parsed && typeof parsed === 'object' && parsed.namaBisnis) {
            setFormData(prev => ({ ...prev, ...parsed }));
            if (parsed.phone) {
              let d = String(parsed.phone).replace(/\D/g, '');
              if (d.startsWith('62')) d = d.substring(2);
              setPhoneDigits(d.replace(/^0+/, ''));
            }
            showLocalToast('info', 'Draft spesifikasi eksplisit berhasil dimuat.', 'PULIHKAN DRAFT');
          }
        }
      } catch (e) {
        console.warn('Failed to parse scoped blueprint draft', e);
      }
    }
  }, []);

  // Theme toggle
  const toggleTheme = () => {
    if (isDarkMode) {
      document.documentElement.classList.remove('dark');
      localStorage.setItem('neriah_theme', 'light');
      setIsDarkMode(false);
    } else {
      document.documentElement.classList.add('dark');
      localStorage.setItem('neriah_theme', 'dark');
      setIsDarkMode(true);
    }
  };

  // Dual-Tier Auto-Save: Scoped LocalStorage (Immediate) + Isolated Server Debounce (1500ms)
  const triggerAutoSave = useCallback((updatedData) => {
    // 1. Immediate Scoped LocalStorage save
    try {
      localStorage.setItem(getScopedStorageKey(), JSON.stringify(updatedData));
    } catch (e) { }

    // 2. Debounced server save (never mutates existing VisionBlueprint unless is_explicit_slug_edit === true)
    setAutoSaveStatus('saving');
    if (autoSaveDebounceRef.current) {
      clearTimeout(autoSaveDebounceRef.current);
    }

    autoSaveDebounceRef.current = setTimeout(async () => {
      try {
        const token = csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch('/api/blueprint/autosave', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            blueprint: updatedData,
            draft_id: draftIdRef.current,
            is_explicit_slug_edit: isExplicitSlugEdit,
            slug: isExplicitSlugEdit ? (initialData?._meta?.is_editing_slug || null) : null,
          }),
        });

        if (response.ok) {
          const result = await response.json();
          if (result.draft_id) draftIdRef.current = result.draft_id;
          if (result.saved_at) setLastSavedTime(result.saved_at);
          setAutoSaveStatus('saved');
        } else {
          setAutoSaveStatus('saved');
        }
      } catch (err) {
        setAutoSaveStatus('saved');
      }
    }, 1500);
  }, [csrfToken, getScopedStorageKey, isExplicitSlugEdit, initialData]);

  // Form Field Updater
  const updateField = (field, value) => {
    // If field was flagged as invalid and user is typing, remove error stroke and index alert immediately
    const isInvalid = invalidFields.some(k => k === field || k === 'field-' + field || k.replace(/^field-/, '') === field);
    if (isInvalid) {
      const el = document.getElementById('field-' + field) || document.getElementById(field);
      if (el) {
        el.classList.remove('field-stroke-alert', 'field-stroke-pulsing');
      }
      setInvalidFields(prev => {
        const next = prev.filter(k => k !== field && k !== 'field-' + field && k.replace(/^field-/, '') !== field);
        if (next.length === 0) {
          setErrorMessage(null);
        }
        return next;
      });
    }

    let sanitizedValue = value;
    if (field === 'email') {
      sanitizedValue = sanitizeStrictEmailInput(value);
    } else if (field === 'durasiHari') {
      sanitizedValue = formatRegionalNumberInput(value, lang);
    }

    setFormData(prev => {
      const next = { ...prev, [field]: sanitizedValue };
      triggerAutoSave(next);
      return next;
    });
  };

  // Phone input sanitizer (config/country_zones.php + instant leading-0 removal)
  const handlePhoneDigitsChange = (e) => {
    const clean = sanitizePhoneLocalDigitsInput(e.target.value, selectedCountryCode);
    setPhoneDigits(clean);
    updateField('phone', clean ? `${selectedCountryCode}${clean}` : '');
  };

  // File Dropzone Handler
  const handleFileChange = (e) => {
    const files = Array.from(e.target.files || []);
    if (!files.length) return;

    if (attachedFiles.length + files.length > 5) {
      setAnalysisError(lang === 'en' ? 'Maximum 5 files can be attached.' : 'Maksimal 5 berkas dapat dilampirkan.');
      return;
    }

    const validFiles = [];
    for (const f of files) {
      if (f.size > 15 * 1024 * 1024) {
        setAnalysisError(lang === 'en' ? `File ${f.name} exceeds 15MB limit.` : `Berkas ${f.name} melebihi batas 15MB.`);
        return;
      }
      validFiles.push(f);
    }

    setAttachedFiles(prev => [...prev, ...validFiles]);
    setAnalysisError(null);
    e.target.value = '';
  };

  const removeFile = (index) => {
    setAttachedFiles(prev => prev.filter((_, i) => i !== index));
  };

  // Apply Quick Idea Template
  const applyTemplate = (tplText) => {
    setIdeaText(tplText);
  };

  // Synthesize Idea & Fill All 25 Fields
  const handleAnalyzeIdea = async (e) => {
    e?.preventDefault();
    setAnalysisError(null);

    // Anti-Spam Honeypots
    if (honeypot.trim() !== '' || honeypotWebsite.trim() !== '') {
      showLocalToast('success', 'Blueprint berhasil dianalisis.');
      return;
    }

    const cleanText = ideaText.trim();
    if (cleanText.length < 15 && attachedFiles.length === 0) {
      setAnalysisError(
        lang === 'en'
          ? 'Please enter at least 15 characters of your project idea, or attach a specification document.'
          : 'Mohon ceritakan ide proyek Anda minimal 15 karakter, atau lampirkan berkas dokumen spesifikasi.'
      );
      return;
    }

    const hasOnlyMdOrTxt = attachedFiles.length > 0 && attachedFiles.every(f => {
      const ext = f.name.split('.').pop().toLowerCase();
      return ['md', 'markdown', 'txt'].includes(ext);
    });

    setIsAnalyzing(true);
    setAnalysisStepIndex(1);
    setAnalysisStep(attachedFiles.length > 0
      ? (hasOnlyMdOrTxt
        ? (lang === 'en' ? 'Step 1/3: Reading specification files & mapping architecture...' : 'Langkah 1/3: Membaca berkas spesifikasi & memetakan arsitektur...')
        : t.step1)
      : (lang === 'en' ? 'Step 1/3: Ingesting idea & extracting system requirements...' : 'Langkah 1/3: Ingesti ide & ekstraksi kebutuhan sistem...'));

    let timer1 = null;
    let timer2 = null;

    try {
      const token = csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const formDataUpload = new FormData();
      formDataUpload.append('idea_text', cleanText);
      formDataUpload.append('_hp_check', honeypot);
      formDataUpload.append('_website', honeypotWebsite);
      formDataUpload.append('locale', lang);
      formDataUpload.append('output_locale', lang);
      formDataUpload.append('outputLocale', lang);
      formDataUpload.append('ai_model', selectedAiModel);
      if (formData.namaBisnis && formData.namaBisnis.trim()) {
        formDataUpload.append('nama_bisnis', formData.namaBisnis.trim());
      }
      if (formData.primarySector) {
        formDataUpload.append('primary_sector', formData.primarySector);
      }
      if (Array.isArray(formData.selectedPrimitives) && formData.selectedPrimitives.length > 0) {
        formDataUpload.append('selected_primitives', JSON.stringify(formData.selectedPrimitives));
      }
      if (effectiveSelectedMenus.length > 0) {
        formDataUpload.append('selected_menus', JSON.stringify(effectiveSelectedMenus));
      }
      if (formData.stackPreset) {
        formDataUpload.append('stack_preset', formData.stackPreset);
      }

      attachedFiles.forEach(file => {
        formDataUpload.append('files[]', file);
      });

      timer1 = setTimeout(() => {
        setAnalysisStepIndex(2);
        setAnalysisStep(lang === 'en'
          ? 'Step 2/3: Modeling domain architecture, RBAC roles & tech stack...'
          : 'Langkah 2/3: Pemodelan domain bisnis, peran RBAC & arsitektur...');
      }, 1500);

      timer2 = setTimeout(() => {
        setAnalysisStepIndex(3);
        setAnalysisStep(lang === 'en'
          ? 'Step 3/3: Auto-filling all 26 Blueprint parameters & scope boundaries...'
          : 'Langkah 3/3: Mengisi otomatis seluruh 26 parameter Blueprint & batasan scope...');
      }, 3500);

      const response = await fetch('/api/blueprint/analyze-idea', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
        },
        body: formDataUpload
      });

      clearTimeout(timer1);
      clearTimeout(timer2);

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw new Error(result.message || (lang === 'en' ? 'Failed to process idea.' : 'Gagal memproses ide.'));
      }

      if (result.ai_telemetry) {
        setAiTelemetry(result.ai_telemetry);
      }

      if (result.notification && result.ai_telemetry?.fallback_occurred) {
        showLocalToast('warning', result.notification, 'AI FAILOVER STATUS');
      }

      // Align track recommendation if available
      if (result.track_recommendation?.recommended_track) {
        handleTrackChange(result.track_recommendation.recommended_track);
      }

      // Populate synthesized blueprint data into all 25 fields while preserving user's explicit Stack Preset
      const synData = result.data || {};
      const resolvedStackPreset = (formData.stackPreset && formData.stackPreset !== 'STACK_M1_LARAVEL_MONOLITH')
        ? formData.stackPreset
        : (synData.stackPreset || formData.stackPreset || 'STACK_M1_LARAVEL_MONOLITH');
      const merged = {
        ...formData,
        ...synData,
        stackPreset: resolvedStackPreset,
        publicUxPreset: synData.publicUxPreset || synData.public_ux_preset || resolveDefaultPublicUxForSector(synData.primarySector || formData.primarySector),
        clientName: synData.clientName || formData.clientName,
        email: sanitizeStrictEmailInput(synData.email || formData.email),
        phone: synData.phone || formData.phone,
      };

      setFormData(merged);
      triggerAutoSave(merged);

      if (synData.proactive_suggestions && Array.isArray(synData.proactive_suggestions)) {
        setProactiveSuggestions(synData.proactive_suggestions);
      }

      if (synData.phone) {
        let d = String(synData.phone).replace(/\D/g, '');
        if (d.startsWith('62')) d = d.substring(2);
        setPhoneDigits(d.replace(/^0+/, ''));
      }

      setConvertedMarkdown(result.converted_markdown || synData._meta?.converted_markdown || '');

      const blockAEl = document.getElementById('section-block-a');
      if (blockAEl) {
        blockAEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }

      showLocalToast(
        'success',
        lang === 'en'
          ? 'All 25 Blueprint parameters populated by AI! Review and fine-tune below.'
          : '25 Parameter Blueprint berhasil diisi otomatis oleh AI! Silakan tinjau dan sesuaikan di bawah.',
        'SINTESIS SUKSES'
      );

    } catch (err) {
      setAnalysisError(err.message || (lang === 'en' ? 'An error occurred. Please try again.' : 'Terjadi kendala. Silakan coba lagi.'));
    } finally {
      if (timer1) clearTimeout(timer1);
      if (timer2) clearTimeout(timer2);
      setIsAnalyzing(false);
      setAnalysisStep('');
      setAnalysisStepIndex(1);
    }
  };

  // Reset / Clear all blueprint form fields back to pristine blank state
  const handleResetForm = async () => {
    const blankForm = {
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
      worldClassTaskMatrix: null,
      agentshieldReport: null,
    };

    try {
      localStorage.removeItem(getScopedStorageKey());
      localStorage.removeItem('neriah_blueprint_autosave');
    } catch (e) { }

    const freshToken = 'draft_' + Date.now() + '_' + Math.random().toString(36).slice(2, 10);
    draftIdRef.current = freshToken;

    setFormData(blankForm);
    setIdeaText('');
    setAttachedFiles([]);
    setConvertedMarkdown('');
    setPhoneDigits('');
    setAiTelemetry(null);
    setDoctorReport(null);
    setBrownfieldReport(null);
    setBrownfieldSqlText('');
    setQuickInputText('');
    setAppliedSuggestionIds(new Set());
    setSuccessData(null);
    setErrorMessage(null);

    try {
      const token = csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      await fetch('/api/blueprint/reset', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ draft_id: freshToken }),
      });
    } catch (e) { }

    if (typeof window !== 'undefined' && window.history && window.history.replaceState) {
      window.history.replaceState({}, document.title, window.location.pathname);
    }

    showLocalToast(
      'success',
      lang === 'en'
        ? 'Clean isolated project workspace initialized! Zero remnants from prior projects.'
        : 'Ruang kerja proyek baru bersih & terisolasi! Tidak ada data proyek lama yang tersisa.',
      'ISOLATED WORKSPACE V6.0'
    );
  };

  // Proactive Co-Pilot: Submit additional requirement via AI Flash
  const handleCoPilotSubmit = async (e) => {
    e?.preventDefault();
    const cleanSupplement = coPilotInput.trim();
    if (!cleanSupplement || isCoPilotLoading) return;

    setIsCoPilotLoading(true);

    try {
      const token = csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const response = await fetch('/api/blueprint/supplement-idea', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          supplement_text: cleanSupplement,
          blueprint: formData,
          locale: lang,
        }),
      });

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw new Error(result.message || (lang === 'en' ? 'Failed to append requirement.' : 'Gagal menempatkan kebutuhan tambahan.'));
      }

      const updated = {
        ...formData,
        ...(result.data || {})
      };

      setFormData(updated);
      triggerAutoSave(updated);

      setCoPilotInput('');
      showLocalToast('success', result.message || (lang === 'en' ? 'Requirement seamlessly added to blueprint!' : 'Kebutuhan berhasil disematkan ke blueprint!'), 'AI FLASH CO-PILOT');

    } catch (err) {
      showLocalToast('error', err.message || 'Kendala saat menambahkan ide.', 'GAGAL');
    } finally {
      setIsCoPilotLoading(false);
    }
  };

  // Apply Proactive Suggestion Chip
  const handleApplySuggestion = (suggestion) => {
    const isPaymentSelector = suggestion.is_payment_selector || suggestion.id === 'payment_multi_rail' || suggestion.id.startsWith('payment_');

    // Interactive selector for payment rails (allowing user to multi-select BRI, BNI, Stripe, Wise, PayPal, QRIS, or No Gateway)
    if (isPaymentSelector) {
      setPendingPaymentSuggestion(suggestion);
      // Pre-populate selected rails from current form data if present
      const currentIntegrations = formData.kebutuhanIntegrasi || '';
      const matchedIds = new Set();
      PAYMENT_RAIL_OPTIONS.forEach(opt => {
        if (currentIntegrations.includes(opt.name) || currentIntegrations.includes(opt.text)) {
          matchedIds.add(opt.id);
        }
      });
      if (matchedIds.size > 0) {
        setSelectedPaymentRailIds(matchedIds);
      } else if (selectedPaymentRailIds.size === 0) {
        setSelectedPaymentRailIds(new Set(['qris_national']));
      }
      setPaymentModalOpen(true);
      return;
    }

    if (appliedSuggestionIds.has(suggestion.id)) return;

    applySuggestionDirectly(suggestion, suggestion.addition);
  };

  const applySuggestionDirectly = (suggestion, additionText) => {
    const targetField = suggestion.target_field || 'fiturWajib';
    const addition = additionText || suggestion.addition;

    setFormData(prev => {
      let updatedValue;
      if (targetField === 'kebutuhanIntegrasi') {
        const existing = (prev[targetField] || '').trim();
        updatedValue = existing ? `${existing.replace(/[,.]\s*$/, '')}, ${addition}` : addition;
      } else {
        const items = parseNumberedList(prev[targetField]);
        items.push(addition);
        updatedValue = stringifyNumberedList(items);
      }
      const next = { ...prev, [targetField]: updatedValue };
      triggerAutoSave(next);
      return next;
    });

    setAppliedSuggestionIds(prev => new Set([...prev, suggestion.id]));
    showLocalToast('success', `${suggestion.title} ${lang === 'en' ? 'added to blueprint!' : 'berhasil ditambahkan ke blueprint!'}`, 'SARAN PROAKTIF');
  };

  const handleTogglePaymentRail = (railId) => {
    setSelectedPaymentRailIds(prev => {
      const next = new Set(prev);
      if (next.has(railId)) {
        next.delete(railId);
      } else {
        next.add(railId);
      }
      return next;
    });
  };

  const handleApplySelectedPaymentRails = () => {
    if (selectedPaymentRailIds.size === 0) {
      showLocalToast('warning', lang === 'en' ? 'Please select at least 1 payment rail.' : 'Silakan pilih minimal 1 rel pembayaran.', 'PERINGATAN');
      return;
    }

    const selectedRails = PAYMENT_RAIL_OPTIONS.filter(o => selectedPaymentRailIds.has(o.id));
    const selectedTexts = selectedRails.map(o => o.text);

    setFormData(prev => {
      // Get current items, remove old payment rail texts to prevent stale/duplicate stacking
      const currentItems = parseCommaList(prev.kebutuhanIntegrasi || '');
      const nonPaymentItems = currentItems.filter(item => {
        return !PAYMENT_RAIL_OPTIONS.some(o => o.text === item || (o.name && item.includes(o.name)));
      });
      const merged = [...nonPaymentItems, ...selectedTexts];
      const next = { ...prev, kebutuhanIntegrasi: stringifyCommaList(merged) };
      triggerAutoSave(next);
      return next;
    });

    if (pendingPaymentSuggestion) {
      setAppliedSuggestionIds(prev => new Set([...prev, pendingPaymentSuggestion.id]));
    }
    showLocalToast('success', `${selectedRails.length} ${lang === 'en' ? 'payment rails applied to Integrations!' : 'rel pembayaran berhasil disematkan ke Integrasi!'}`, 'REL PEMBAYARAN');
    setPaymentModalOpen(false);
    setPendingPaymentSuggestion(null);
  };

  const handleSelectPaymentRail = (rail) => {
    handleTogglePaymentRail(rail.id);
  };

  // In-Card: Add item to a numbered list
  const handleAddListItem = (fieldKey) => {
    const text = quickInputText.trim();
    if (!text) return;

    setFormData(prev => {
      let next;
      if (fieldKey === 'kebutuhanIntegrasi') {
        const items = parseCommaList(prev[fieldKey]);
        items.push(text);
        next = { ...prev, [fieldKey]: stringifyCommaList(items) };
      } else {
        const items = parseNumberedList(prev[fieldKey]);
        items.push(text);
        next = { ...prev, [fieldKey]: stringifyNumberedList(items) };
      }
      triggerAutoSave(next);
      return next;
    });

    setQuickInputText('');
    setActiveAddKey(null);
    showLocalToast('success', lang === 'en' ? 'Item added!' : 'Item baru berhasil ditambahkan!');
  };

  // In-Card: Delete item from list
  const handleDeleteListItem = (fieldKey, index) => {
    setFormData(prev => {
      let next;
      if (fieldKey === 'kebutuhanIntegrasi') {
        const items = parseCommaList(prev[fieldKey]);
        items.splice(index, 1);
        next = { ...prev, [fieldKey]: stringifyCommaList(items) };
      } else {
        const items = parseNumberedList(prev[fieldKey]);
        items.splice(index, 1);
        next = { ...prev, [fieldKey]: stringifyNumberedList(items) };
      }
      triggerAutoSave(next);
      return next;
    });
    showLocalToast('info', lang === 'en' ? 'Item removed.' : 'Item dihapus.');
  };

  // Dedicated AI KPI Deep-Dive & Architectural Cascade Trigger
  const handleTriggerKpiElaboration = async () => {
    if (isElaboratingKpi) return;
    setIsElaboratingKpi(true);

    try {
      const url = initialData?.slug
        ? `/api/blueprint/${initialData.slug}/elaborate-kpi-cascade`
        : '/api/blueprint/elaborate-kpi-cascade';

      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({
          tujuanUtama: formData.tujuanUtama,
          masalahUtama: formData.masalahUtama,
          namaBisnis: formData.namaBisnis,
          deskripsiBisnis: formData.deskripsiBisnis,
          fiturUtama: formData.fiturUtama,
          aktorSistem: formData.aktorSistem,
          skenarioGherkin: formData.skenarioGherkin,
          primarySector: formData.primarySector,
          locale: lang,
        }),
      });

      const data = await response.json();

      if (!response.ok || !data.success) {
        throw new Error(data.message || (lang === 'en' ? 'Failed to elaborate KPIs' : 'Gagal mengelaborasi KPI'));
      }

      setKpiCascadeResult(data);
      const initialFeatMap = {};
      (data.cascaded_impacts?.features || []).forEach((f, idx) => {
        initialFeatMap[f.code || idx] = true;
      });
      setSelectedCascadeFeatures(initialFeatMap);

      const initialActorMap = {};
      (data.cascaded_impacts?.actors || []).forEach((a, idx) => {
        initialActorMap[a.role || idx] = true;
      });
      setSelectedCascadeActors(initialActorMap);

      setIncludeCascadeGherkin(true);
      setShowKpiCascadeModal(true);
      showLocalToast(
        'success',
        lang === 'en' ? 'KPI elaboration and architectural cascade ready for review.' : 'Elaborasi KPI dan matriks dampak arsitektur siap ditinjau.',
        'KPI CASCADE ENGINE'
      );
    } catch (err) {
      console.error('KPI Elaboration error:', err);
      showLocalToast('error', err.message || (lang === 'en' ? 'Error elaborating KPIs' : 'Terjadi kendala saat elaborasi KPI'), 'ERROR');
    } finally {
      setIsElaboratingKpi(false);
    }
  };

  // Apply elaborated KPIs and cascade features/actors/UAT to form state
  const applyKpiCascade = () => {
    if (!kpiCascadeResult) return;

    setFormData((prev) => {
      // 1. Elaborate tujuanUtama
      const newKpiLines = (kpiCascadeResult.kpis || []).map((k, idx) => {
        const text = k.synthesized_text || k.title;
        return `${idx + 1}. ${text}`;
      }).join('\n');

      // 2. Cascade to fiturUtama (append unselected or new features)
      const existingFeatures = prev.fiturUtama || '';
      const selectedFeats = (kpiCascadeResult.cascaded_impacts?.features || [])
        .filter((f, idx) => selectedCascadeFeatures[f.code || idx])
        .map((f) => f.formatted_line || `${f.title}: ${f.spec}`)
        .filter((line) => !existingFeatures.toLowerCase().includes(line.slice(0, 30).toLowerCase()));

      let updatedFeatures = existingFeatures;
      if (selectedFeats.length > 0) {
        const currentFeatList = parseNumberedList(existingFeatures);
        const startIndex = currentFeatList.length;
        const newFeatLines = selectedFeats.map((featText, i) => `${startIndex + i + 1}. [KPI-Linked] ${featText}`);
        updatedFeatures = currentFeatList.length > 0
          ? `${existingFeatures}\n${newFeatLines.join('\n')}`
          : newFeatLines.join('\n');
      }

      // 3. Cascade to aktorSistem (append unselected or new actors)
      const existingActors = prev.aktorSistem || '';
      const selectedActors = (kpiCascadeResult.cascaded_impacts?.actors || [])
        .filter((a, idx) => selectedCascadeActors[a.role || idx])
        .map((a) => a.formatted_line || `${a.role}: ${a.access}`)
        .filter((line) => !existingActors.toLowerCase().includes(line.slice(0, 20).toLowerCase()));

      let updatedActors = existingActors;
      if (selectedActors.length > 0) {
        const currentActorList = parseNumberedList(existingActors);
        const startIndex = currentActorList.length;
        const newActorLines = selectedActors.map((actorText, i) => `${startIndex + i + 1}. ${actorText}`);
        updatedActors = currentActorList.length > 0
          ? `${existingActors}\n${newActorLines.join('\n')}`
          : newActorLines.join('\n');
      }

      // 4. Cascade to skenarioGherkin
      let updatedGherkin = prev.skenarioGherkin || '';
      if (includeCascadeGherkin && kpiCascadeResult.cascaded_impacts?.acceptance_criteria) {
        const gherkinToAdd = kpiCascadeResult.cascaded_impacts.acceptance_criteria;
        if (!updatedGherkin.includes(gherkinToAdd.slice(0, 30))) {
          updatedGherkin = updatedGherkin
            ? `${updatedGherkin}\n\n# --- KPI Acceptance Validation Scenario ---\n${gherkinToAdd}`
            : `# --- KPI Acceptance Validation Scenario ---\n${gherkinToAdd}`;
        }
      }

      const next = {
        ...prev,
        tujuanUtama: newKpiLines || prev.tujuanUtama,
        fiturUtama: updatedFeatures,
        aktorSistem: updatedActors,
        skenarioGherkin: updatedGherkin,
      };

      triggerAutoSave(next);
      return next;
    });

    setShowKpiCascadeModal(false);
    showLocalToast(
      'success',
      lang === 'en'
        ? 'KPIs elaborated and cascaded into Core Features, Actors, and UAT scenarios.'
        : 'KPI berhasil dielaborasi dan berdampak langsung pada Modul Fitur, Aktor RBAC, dan skenario UAT.',
      'SINKRONISASI SUKSES'
    );
  };


  // Helper to trigger stroke on all missing fields and auto-scroll to the first one
  const triggerFieldStrokeAndScroll = useCallback((rawKeys = []) => {
    const canonicalKeys = [...new Set(
      rawKeys.map(k => FIELD_MAP[k] || k).filter(Boolean)
    )];

    if (canonicalKeys.length === 0) return;

    setInvalidFields(canonicalKeys);
    setStrokeActive(true);

    // Auto-expand accordion groups and sub-blocks containing invalid fields
    setOpenAccordionGroups(prev => {
      const next = { ...prev };
      canonicalKeys.forEach(k => {
        const secId = FIELD_SECTION_MAP[k];
        if (secId) {
          const grp = sectionGroupMap[secId];
          if (grp) next[grp] = true;
        }
      });
      return next;
    });

    setExpandedSubBlocks(prev => {
      const next = { ...prev };
      canonicalKeys.forEach(k => {
        const secId = FIELD_SECTION_MAP[k];
        if (secId) {
          next[secId] = true;
        }
      });
      return next;
    });

    if (strokeTimerRef.current) {
      clearTimeout(strokeTimerRef.current);
    }

    // Apply stroke and pulse to all missing fields
    canonicalKeys.forEach(k => {
      const el = document.getElementById('field-' + k);
      if (el) {
        el.classList.add('field-stroke-alert', 'field-stroke-pulsing');
      }
    });

    // Auto-scroll to the FIRST missing field
    const firstKey = canonicalKeys.find(k => !!document.getElementById('field-' + k)) || canonicalKeys[0];
    const targetEl = document.getElementById('field-' + firstKey);

    if (targetEl) {
      const rect = targetEl.getBoundingClientRect();
      const y = window.pageYOffset + rect.top;
      window.scrollTo({ top: Math.max(0, y - 130), behavior: 'smooth' });

      setTimeout(() => {
        const input = targetEl.querySelector('input, textarea, select');
        if (input) input.focus();
      }, 400);

      const secId = FIELD_SECTION_MAP[firstKey];
      if (secId) {
        setActiveSection(secId);
        syncAccordionToSection(secId);
      }
      setActiveField('field-' + firstKey);
    }

    // After 5 seconds, stop the pulsing animation but retain the solid alert border until filled
    strokeTimerRef.current = setTimeout(() => {
      canonicalKeys.forEach(k => {
        const el = document.getElementById('field-' + k);
        if (el) {
          el.classList.remove('field-stroke-pulsing');
        }
      });
      setStrokeActive(false);
    }, 5000);
  }, [sectionGroupMap, syncAccordionToSection]);

  // Final Blueprint Submission (Generate Contract & PRD)
  const handleFinalSubmit = async (e) => {
    e?.preventDefault();
    setErrorMessage(null);

    // Client-side validation: detect all unfilled required fields
    const missing = [];
    if (!formData.namaBisnis?.trim()) missing.push('namaBisnis');
    if (!formData.masalahUtama?.trim()) missing.push('masalahUtama');
    if (!formData.tujuanUtama?.trim()) missing.push('tujuanUtama');
    if (!formData.targetAudiens?.trim()) missing.push('targetAudiens');
    if (!formData.aktorSistem?.trim()) missing.push('aktorSistem');
    if (!formData.fiturWajib?.trim()) missing.push('fiturWajib');
    if (!formData.alurKerja?.trim()) missing.push('alurKerja');
    if (!formData.clientName?.trim()) missing.push('clientName');
    if (!formData.email?.trim()) missing.push('email');
    if (!formData.phone?.trim()) missing.push('phone');

    if (missing.length > 0) {
      const msg = lang === 'en'
        ? `Please complete ${missing.length} required field(s) before locking your blueprint.`
        : `Terdapat ${missing.length} parameter wajib yang belum diisi. Layar telah otomatis diarahkan ke field pertama yang perlu dilengkapi.`;
      setErrorMessage(msg);
      triggerFieldStrokeAndScroll(missing);
      showLocalToast(
        'error',
        lang === 'en'
          ? `Missing ${missing.length} required field(s). Please review highlighted inputs.`
          : `Mohon lengkapi ${missing.length} parameter wajib yang disorot.`
      );
      return;
    }

    setIsSubmitting(true);

    try {
      const resolvedPubUx = formData.publicUxPreset || resolveDefaultPublicUxForSector(formData.primarySector);
      const payload = {
        ...formData,
        outputLocale: lang,
        output_locale: lang,
        publicUxPreset: resolvedPubUx,
        public_ux_preset: resolvedPubUx,
        selectedMenus: effectiveSelectedMenus,
        selected_menus: effectiveSelectedMenus,
        isolation_token: draftIdRef.current,
        track: currentTrack,
        selected_track: currentTrack,
        phone: formData.phone,
        country_code: selectedCountryCode,
        _hp_check: honeypot,
        _website: honeypotWebsite,
        locale: lang,
      };

      const response = await fetch(submitUrl || '/api/vision-blueprint', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify(payload),
      });

      const result = await response.json();

      if (response.ok && result.success) {
        // Clear any remaining stroke alert classes
        invalidFields.forEach(k => {
          const el = document.getElementById('field-' + k);
          if (el) el.classList.remove('field-stroke-alert', 'field-stroke-pulsing');
        });
        setInvalidFields([]);
        try {
          localStorage.removeItem(getScopedStorageKey());
          localStorage.removeItem('neriah_blueprint_autosave');
        } catch (e) { }
        setSuccessData(result.data);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      } else {
        let missingKeys = [];
        if (result.errors && typeof result.errors === 'object') {
          missingKeys = Object.keys(result.errors).map(k => FIELD_MAP[k] || k);
        }

        const errorDetails = result.errors
          ? Object.values(result.errors).flat().join(', ')
          : (result.message || 'Terjadi kesalahan saat memproses data.');
        setErrorMessage(errorDetails);

        if (missingKeys.length > 0) {
          triggerFieldStrokeAndScroll(missingKeys);
          showLocalToast(
            'error',
            lang === 'en'
              ? `${missingKeys.length} required parameter(s) need attention.`
              : `${missingKeys.length} parameter wajib belum lengkap. Periksa field yang disorot.`
          );
        }
      }
    } catch (err) {
      setErrorMessage(
        lang === 'en'
          ? 'Network or server communication error. Please check your connection and try again.'
          : 'Terjadi gangguan jaringan atau koneksi. Silakan coba sesaat lagi.'
      );
    } finally {
      setIsSubmitting(false);
    }
  };

  const panelClass = "bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-none p-6 sm:p-8 transition-colors duration-200 scroll-mt-24";
  const inputClass = "w-full px-4 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-0 outline-none rounded-none transition font-sans";
  const labelClass = "block text-xs font-mono uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5 font-bold";

  // SUCCESS VIEW

  // SUCCESS VIEW
  if (successData) {
    return (
      <BlueprintSuccessView
        successData={successData}
        formData={formData}
        currentTrack={currentTrack}
        lang={lang}
        t={t}
        onResetForm={handleResetForm}
      />
    );
  }

  return (
    <div className={`w-full py-8 px-4 sm:px-6 font-sans relative transition-all duration-200 ${
      isIndexExpanded
        ? 'xl:pl-[310px] xl:pr-6 2xl:pl-[330px] 2xl:pr-10 max-w-none'
        : 'xl:pl-20 xl:pr-6 2xl:pl-24 2xl:pr-10 max-w-none'
    }`}>
      <style>{`
        @keyframes fieldStrokePulse {
          0%, 100% {
            box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.85), 0 0 22px rgba(244, 63, 94, 0.35);
            border-color: #f43f5e;
          }
          50% {
            box-shadow: 0 0 0 6px rgba(244, 63, 94, 0.3), 0 0 32px rgba(244, 63, 94, 0.65);
            border-color: #fb7185;
          }
        }
        .field-stroke-alert {
          position: relative !important;
          border: 2px solid #f43f5e !important;
          background-color: rgba(244, 63, 94, 0.04) !important;
          padding: 0.75rem !important;
          margin-left: -0.75rem !important;
          margin-right: -0.75rem !important;
          border-radius: 0px !important;
          transition: all 0.25s ease-in-out !important;
        }
        .dark .field-stroke-alert {
          background-color: rgba(244, 63, 94, 0.08) !important;
        }
        .field-stroke-pulsing {
          animation: fieldStrokePulse 1.2s ease-in-out infinite !important;
        }
        .field-stroke-alert input,
        .field-stroke-alert textarea,
        .field-stroke-alert select {
          border-color: #f43f5e !important;
        }
      `}</style>

      {/* FLOATING TOAST NOTIFICATION */}
      <AnimatePresence>
        {toast && (
          <motion.div
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
            className={`fixed top-4 right-4 z-50 p-4 border max-w-sm rounded-none font-mono text-xs shadow-lg flex items-start gap-3 ${toast.type === 'error'
              ? 'bg-red-950 border-red-500 text-red-200'
              : toast.type === 'warning'
                ? 'bg-amber-950 border-amber-500 text-amber-200'
                : toast.type === 'info'
                  ? 'bg-blue-950 border-blue-500 text-blue-200'
                  : 'bg-zinc-900 border-emerald-500 text-emerald-400'
              }`}
          >
            {toast.type === 'error' ? (
              <AlertCircle className="w-4 h-4 text-red-400 shrink-0 mt-0.5" />
            ) : toast.type === 'info' ? (
              <Check className="w-4 h-4 text-blue-400 shrink-0 mt-0.5" />
            ) : (
              <CheckCircle2 className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
            )}
            <div className="flex-1">
              {toast.title && <span className="font-bold block uppercase tracking-wider mb-0.5">{toast.title}</span>}
              <p className="font-sans text-xs text-zinc-200">{toast.message}</p>
            </div>
            <button
              type="button"
              onClick={() => setToast(null)}
              className="text-zinc-400 hover:text-white"
            >
              <X className="w-3.5 h-3.5" />
            </button>
          </motion.div>
        )}
      </AnimatePresence>

      {/* 1. FLOATING LEFT HUD INDEX */}
      <BlueprintSpySidebar
        isIndexExpanded={isIndexExpanded}
        setIsIndexExpanded={setIsIndexExpanded}
        mobileDrawerOpen={mobileDrawerOpen}
        setMobileDrawerOpen={setMobileDrawerOpen}
        indexTopOffset={indexTopOffset}
        indexSections={indexSections}
        openAccordionGroups={openAccordionGroups}
        toggleAccordionGroup={toggleAccordionGroup}
        expandedSubBlocks={expandedSubBlocks}
        toggleSubBlock={toggleSubBlock}
        expandAllGroups={expandAllGroups}
        collapseAllGroups={collapseAllGroups}
        activeSection={activeSection}
        activeField={activeField}
        invalidFields={invalidFields}
        indexSearchQuery={indexSearchQuery}
        setIndexSearchQuery={setIndexSearchQuery}
        jumpToSection={jumpToSection}
        jumpToField={jumpToField}
        desktopScrollRef={desktopScrollRef}
        completeness={completeness}
        lang={lang}
        t={t}
      />

      {/* 2. SATU PINTU & TOP TOOLBAR */}
      <BlueprintTopHeader
        currentTrack={currentTrack}
        lang={lang}
        setLang={setLang}
        isDarkMode={isDarkMode}
        toggleTheme={toggleTheme}
        autoSaveStatus={autoSaveStatus}
        lastSavedTime={lastSavedTime}
        completeness={completeness}
        formData={formData}
        setFormData={setFormData}
        handleResetForm={handleResetForm}
        t={t}
      />

      {/* 3. EXECUTION TRACK SWITCHER & READINESS BANNER */}
      <BlueprintTrackSwitcher
        currentTrack={currentTrack}
        handleTrackChange={handleTrackChange}
        completeness={completeness}
        lang={lang}
        t={t}
      />

      {/* 4. SECTION 1: QUICK IDEA STUDIO & PROACTIVE AI ASSISTANT */}
      <BlueprintQuickIdeaSection
        isStudioOpen={isStudioOpen}
        setIsStudioOpen={setIsStudioOpen}
        lang={lang}
        t={t}
        detectedIdeaCategory={detectedIdeaCategory}
        activeIdeaCategory={activeIdeaCategory}
        selectedIdeaCategory={selectedIdeaCategory}
        setSelectedIdeaCategory={setSelectedIdeaCategory}
        applyTemplate={applyTemplate}
        aiModels={aiModels}
        selectedAiModel={selectedAiModel}
        setSelectedAiModel={setSelectedAiModel}
        aiTelemetry={aiTelemetry}
        ideaText={ideaText}
        setIdeaText={setIdeaText}
        textareaRef={textareaRef}
        isAnalyzing={isAnalyzing}
        analysisStep={analysisStep}
        analysisStepIndex={analysisStepIndex}
        analysisError={analysisError}
        copySuccess={copySuccess}
        honeypot={honeypot}
        setHoneypot={setHoneypot}
        honeypotWebsite={honeypotWebsite}
        setHoneypotWebsite={setHoneypotWebsite}
        attachedFiles={attachedFiles}
        convertedMarkdown={convertedMarkdown}
        handleAnalyzeIdea={handleAnalyzeIdea}
        handleDrop={handleDrop}
        handleFileSelect={handleFileSelect}
        removeFile={removeFile}
        copyMarkdown={copyMarkdown}
        downloadMarkdown={downloadMarkdown}
        proactiveSuggestions={proactiveSuggestions}
        appliedSuggestionIds={appliedSuggestionIds}
        applySuggestion={applySuggestion}
        isApplyingSuggestion={isApplyingSuggestion}
        coPilotInput={coPilotInput}
        setCoPilotInput={setCoPilotInput}
        isCoPilotLoading={isCoPilotLoading}
        handleCoPilotSubmit={handleCoPilotSubmit}
      />

      {/* 5. THE COMPLETE 25-FIELD BLUEPRINT FORM */}
      <form onSubmit={handleFinalSubmit} className="space-y-8">
        {matrixCatalog.sectors.length > 0 && (
          <BlueprintComposableMatrixSection
            matrixCatalog={matrixCatalog}
            setMatrixCatalog={setMatrixCatalog}
            formData={formData}
            setFormData={setFormData}
            triggerAutoSave={triggerAutoSave}
            activeMatrixSectorObj={activeMatrixSectorObj}
            matrixMode={matrixMode}
            setMatrixMode={setMatrixMode}
            activeWorkbenchTab={activeWorkbenchTab}
            setActiveWorkbenchTab={setActiveWorkbenchTab}
            activeTopologyFilter={activeTopologyFilter}
            setActiveTopologyFilter={setActiveTopologyFilter}
            showStackMatrixTable={showStackMatrixTable}
            setShowStackMatrixTable={setShowStackMatrixTable}
            v7RuntimePreview={v7RuntimePreview}
            setV7RuntimePreview={setV7RuntimePreview}
            selectMatrixSector={selectMatrixSector}
            selectStackPreset={selectStackPreset}
            handleQuickDetectSectorFromIdea={handleQuickDetectSectorFromIdea}
            doctorLoading={doctorLoading}
            doctorReport={doctorReport}
            runPreFlightDoctorAudit={runPreFlightDoctorAudit}
            applyDoctorAutoFix={applyDoctorAutoFix}
            livePreFlightHints={livePreFlightHints}
            showBrownfieldLab={showBrownfieldLab}
            setShowBrownfieldLab={setShowBrownfieldLab}
            brownfieldSqlText={brownfieldSqlText}
            setBrownfieldSqlText={setBrownfieldSqlText}
            brownfieldLoading={brownfieldLoading}
            brownfieldReport={brownfieldReport}
            runBrownfieldAudit={runBrownfieldAudit}
            applyBrownfieldRecommendation={applyBrownfieldRecommendation}
            panelClass={panelClass}
            lang={lang}
            t={t}
            initialData={initialData}
          />
        )}

        <BlueprintDiscoveryParamsSection
          formData={formData}
          updateField={updateField}
          panelClass={panelClass}
          labelClass={labelClass}
          inputClass={inputClass}
          selectClass={selectClass}
          invalidFields={invalidFields}
          errorMessage={errorMessage}
          setErrorMessage={setErrorMessage}
          isSubmitting={isSubmitting}
          strokeActive={strokeActive}
          lang={lang}
          t={t}
          jumpToField={jumpToField}
          handleFinalSubmit={handleFinalSubmit}
          countryList={countryList}
          selectedCountryCode={selectedCountryCode}
          setSelectedCountryCode={setSelectedCountryCode}
          currentCountry={currentCountry}
          phoneDigits={phoneDigits}
          handlePhoneDigitsChange={handlePhoneDigitsChange}
          currentTrack={currentTrack}
          isElaboratingKpi={isElaboratingKpi}
          elaborateKpiCascade={elaborateKpiCascade}
          setPaymentModalOpen={setPaymentModalOpen}
          setPendingPaymentSuggestion={setPendingPaymentSuggestion}
          activeAddKey={activeAddKey}
          setActiveAddKey={setActiveAddKey}
          newFeatureInput={newFeatureInput}
          setNewFeatureInput={setNewFeatureInput}
        />
      </form>

      {/* 6. MODALS */}
      <BlueprintPaymentRailModal
        isOpen={paymentModalOpen}
        onClose={() => {
          setPaymentModalOpen(false);
          setPendingPaymentSuggestion(null);
        }}
        selectedPaymentRailIds={selectedPaymentRailIds}
        setSelectedPaymentRailIds={setSelectedPaymentRailIds}
        onApply={applyPaymentRailsSelection}
        lang={lang}
      />

      <BlueprintKpiCascadeModal
        isOpen={showKpiCascadeModal}
        onClose={() => setShowKpiCascadeModal(false)}
        kpiCascadeResult={kpiCascadeResult}
        activeKpiCascadeTab={activeKpiCascadeTab}
        setActiveKpiCascadeTab={setActiveKpiCascadeTab}
        selectedCascadeFeatures={selectedCascadeFeatures}
        setSelectedCascadeFeatures={setSelectedCascadeFeatures}
        selectedCascadeActors={selectedCascadeActors}
        setSelectedCascadeActors={setSelectedCascadeActors}
        includeCascadeGherkin={includeCascadeGherkin}
        setIncludeCascadeGherkin={setIncludeCascadeGherkin}
        onApply={applyKpiCascade}
        lang={lang}
      />
    </div>
  );
}
