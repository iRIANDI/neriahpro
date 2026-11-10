import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
  Check,
  Sparkles,
  Zap,
  Crown,
  ShieldCheck,
  HelpCircle,
  ArrowRight,
  Layers,
  FileText,
  Lock,
  Terminal,
  Database,
  Rocket,
  Copy,
  CheckCircle2,
  X,
  MessageSquare,
  MessageCircle,
  Send,
  Loader2,
  Clock,
  Code2,
  Building2,
  Gift,
  ChevronDown,
  ChevronUp,
  Server,
  Cpu,
  CreditCard,
  ExternalLink,
  ShoppingBag,
  Search,
  Upload,
  FileCheck
} from 'lucide-react';
import {
  normalizeCountryZones,
  sanitizeStrictEmailInput,
  sanitizePhoneLocalDigitsInput,
  formatRegionalNumberInput,
  parseRegionalNumberInput,
  COUNTRY_ZONES_TEMPLATE,
} from '../utils/inputHygiene';

/**
 * Concrete Package Registry for fundamental architectural consistency.
 * Zero string prefix/suffix matching (anti-dosa hardcode).
 */
export const PACKAGE_REGISTRY = {
  retail_spark: {
    id: 'retail_spark',
    name: 'Spark Free Idea Audit',
    category: 'blueprint_self_service',
    execution_model: 'self_service',
    is_paid: false,
    requires_login: false,
    requires_sprint_batch: false,
    requires_kickoff_slot: false,
  },
  retail_lite: {
    id: 'retail_lite',
    name: 'Starter AI Architect',
    category: 'blueprint_self_service',
    execution_model: 'self_service',
    is_paid: true,
    requires_login: true,
    requires_sprint_batch: false,
    requires_kickoff_slot: false,
  },
  retail_pro: {
    id: 'retail_pro',
    name: 'Pro Agentic Blueprint',
    category: 'blueprint_self_service',
    execution_model: 'self_service',
    is_paid: true,
    requires_login: true,
    requires_sprint_batch: false,
    requires_kickoff_slot: false,
  },
  retail_ultimate: {
    id: 'retail_ultimate',
    name: 'Ultimate Architecture Blueprint',
    category: 'blueprint_self_service',
    execution_model: 'self_service',
    is_paid: true,
    requires_login: true,
    requires_sprint_batch: false,
    requires_kickoff_slot: false,
  },
  full_mvp: {
    id: 'full_mvp',
    name: 'Custom Full Monolith MVP',
    category: 'studio_engineering',
    execution_model: 'studio_contract',
    is_paid: false,
    requires_login: false,
    requires_sprint_batch: true,
    requires_kickoff_slot: true,
  },
  umkm_starter: {
    id: 'umkm_starter',
    name: 'UMKM Digital Starter',
    category: 'studio_engineering',
    execution_model: 'studio_contract',
    is_paid: true,
    requires_login: false,
    requires_sprint_batch: false,
    requires_kickoff_slot: false,
  },
  blueprint_advisory: {
    id: 'blueprint_advisory',
    name: 'Blueprint & PRD Architecture Advisory',
    category: 'studio_engineering',
    execution_model: 'studio_contract',
    is_paid: true,
    requires_login: false,
    requires_sprint_batch: false,
    requires_kickoff_slot: false,
  },
  impact_nonprofit_grant: {
    id: 'impact_nonprofit_grant',
    name: 'Impact & Non-Profit CSR Grant (70%–100% Subsidy)',
    category: 'blueprint_self_service',
    execution_model: 'self_service',
    is_paid: false,
    requires_login: false,
    requires_sprint_batch: false,
    requires_kickoff_slot: false,
  },
  single_app_umkm_pass: {
    id: 'single_app_umkm_pass',
    name: 'Single-App UMKM Pass (Pay-Per-Project)',
    category: 'blueprint_self_service',
    execution_model: 'self_service',
    is_paid: true,
    requires_login: true,
    requires_sprint_batch: false,
    requires_kickoff_slot: false,
  },
  agency_whitelabel_partner: {
    id: 'agency_whitelabel_partner',
    name: 'Agency Partner & White-Label Studio (Multi-Seat)',
    category: 'blueprint_self_service',
    execution_model: 'self_service',
    is_paid: true,
    requires_login: true,
    requires_sprint_batch: false,
    requires_kickoff_slot: false,
  }
};

export default function ArchitecturePricingIsland({
  headline = 'INVESTASI TRANSPARAN & TEPAT SASARAN',
  subheadline = 'Dua skenario solusi rekayasa perangkat lunak berskala tinggi: Mulai dari blueprint teknis siap eksekusi hingga pengembangan penuh sistem monolit modern tanpa drama pembengkakan biaya.',
  whatsappNumber = '6281380448253',
  featureFlags = {},
  currentLocale = 'id',
  pricingSettings = {},
  authUser = null,
  countries = []
}) {
  const isEn = currentLocale === 'en' || (typeof window !== 'undefined' && (document.documentElement.lang?.startsWith('en') || document.cookie.includes('neriah_locale=en')));
  const countryList = normalizeCountryZones(countries || COUNTRY_ZONES_TEMPLATE);

  // Dynamic feature flag & admin toggle for CV Pro module switcher
  const isCvProEnabled = Boolean(featureFlags?.enable_cv_pro) && Boolean(featureFlags?.pricing_show_cv_tab);
  const [activeTab, setActiveTab] = useState('software'); // 'software' | 'cv'

  // Pricing Section Tab: 'retail' | 'project' | 'sectoral' | 'matrix' | 'faq'
  const [pricingSectionTab, setPricingSectionTab] = useState(() => {
    if (typeof window !== 'undefined') {
      const hash = window.location.hash.toLowerCase().replace('#', '');
      if (['retail', 'instant'].includes(hash)) return 'retail';
      if (['project', 'studio'].includes(hash)) return 'project';
      if (['sectoral', 'sectors', 'ui-ux', 'killer'].includes(hash)) return 'sectoral';
      if (['matrix', 'comparison'].includes(hash)) return 'matrix';
      if (['faq', 'faqs'].includes(hash)) return 'faq';
    }
    return 'retail';
  });

  const [selectedSectorIdx, setSelectedSectorIdx] = useState(0);
  const [selectedUiVariantIdx, setSelectedUiVariantIdx] = useState(0); // 0: Option A | 1: Option B | 2: Option C (36 Visual Blueprints)
  const [wizardOutputLocale, setWizardOutputLocale] = useState('id'); // 'id' | 'en' | 'ar' | 'ja' | 'zh-CN' | 'es' | 'de'
  const [wizardPersonaMode, setWizardPersonaMode] = useState('BUSINESS_WIZARD'); // 'BUSINESS_WIZARD' | 'ARCHITECT_PRO'
  const [activeSlopDiffIdx, setActiveSlopDiffIdx] = useState(0);
  const [wizardMode, setWizardMode] = useState(() => {
    if (typeof window !== 'undefined') {
      const hash = window.location.hash.toLowerCase().replace('#', '');
      if (['project', 'studio'].includes(hash)) return 'MITRA_TURNKEY_PROJECT';
    }
    return 'RETAIL_SELF_DEV';
  }); // 'RETAIL_SELF_DEV' | 'MITRA_TURNKEY_PROJECT'
  const [wizardStackCode, setWizardStackCode] = useState('STACK_M1_LARAVEL_MONOLITH');
  const [wizardRetailTier, setWizardRetailTier] = useState('retail_pro');
  const [wizardMitraTier, setWizardMitraTier] = useState('full_mvp');
  const [satuPintuReturnQuery, setSatuPintuReturnQuery] = useState('');
  const [showSatuPintuWizard, setShowSatuPintuWizard] = useState(true);

  useEffect(() => {
    const handleHash = () => {
      const hash = window.location.hash.toLowerCase().replace('#', '');
      if (['retail', 'instant'].includes(hash)) {
        setPricingSectionTab('retail');
        setWizardMode('RETAIL_SELF_DEV');
      } else if (['project', 'studio'].includes(hash)) {
        setPricingSectionTab('project');
        setWizardMode('MITRA_TURNKEY_PROJECT');
      } else if (['sectoral', 'sectors', 'ui-ux', 'killer'].includes(hash)) setPricingSectionTab('sectoral');
      else if (['matrix', 'comparison'].includes(hash)) setPricingSectionTab('matrix');
      else if (['faq', 'faqs'].includes(hash)) setPricingSectionTab('faq');
    };
    window.addEventListener('hashchange', handleHash);
    return () => window.removeEventListener('hashchange', handleHash);
  }, []);

  const switchPricingTab = (tab) => {
    setPricingSectionTab(tab);
    if (tab === 'retail') {
      setWizardMode('RETAIL_SELF_DEV');
    } else if (tab === 'project') {
      setWizardMode('MITRA_TURNKEY_PROJECT');
    }
    if (typeof window !== 'undefined' && window.history?.replaceState) {
      window.history.replaceState(null, '', `#${tab}`);
    }
  };

  // Modal State for Capacity Reservation & Direct Selection
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [selectedPackage, setSelectedPackage] = useState('full_mvp');

  // Sprint Capacity & Time Management States (Anti-Collision Architecture)
  const [sprintBatch, setSprintBatch] = useState('Batch 1 (15 Okt - 25 Nov 2026)');
  const [kickoffSlot, setKickoffSlot] = useState('Pagi (09:30 - 10:30 WIB)');
  const [hasBlueprint, setHasBlueprint] = useState('no'); // 'no' | 'ready'
  const [blueprintSlug, setBlueprintSlug] = useState('');
  const [umkmCategory, setUmkmCategory] = useState('Toko Retail & Grosir');

  const [formData, setFormData] = useState({
    name: authUser?.name || '',
    company: authUser?.company || '',
    email: authUser?.email || '',
    country_code: '+62',
    phone: authUser?.phone ? sanitizePhoneLocalDigitsInput(authUser.phone, '+62') : '',
    estimated_budget_display: '',
    voucher_code: '',
    notes: '',
    legal_decree_number: '',
    organization_type: 'NONPROFIT_YAYASAN',
    honeypot: ''
  });
  const [legalDocumentFile, setLegalDocumentFile] = useState(null);
  const [isDraggingFile, setIsDraggingFile] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitSuccess, setSubmitSuccess] = useState(false);
  const [submitError, setSubmitError] = useState(null);
  const [submittedWaUrl, setSubmittedWaUrl] = useState('');
  const [lastOrder, setLastOrder] = useState(null);
  const [paymentStatus, setPaymentStatus] = useState('idle'); // 'idle' | 'pending' | 'success' | 'error'
  const [agreeTerms, setAgreeTerms] = useState(false);

  // Tab 04 Comparison Matrix & Tab 05 FAQ Search / Filter / Density States (v12.0 Paripurna)
  const [matrixSearchQuery, setMatrixSearchQuery] = useState('');
  const [matrixCategoryFilter, setMatrixCategoryFilter] = useState('ALL');
  const [matrixDensity, setMatrixDensity] = useState('normal'); // 'compact' | 'normal' | 'relaxed'
  const [faqSearchQuery, setFaqSearchQuery] = useState('');
  const [faqCategoryFilter, setFaqCategoryFilter] = useState('ALL');
  const [expandAllFaqs, setExpandAllFaqs] = useState(false);

  // FAQ Accordion State
  const [openFaq, setOpenFaq] = useState(0);

  const toggleFaq = (index) => {
    setOpenFaq(openFaq === index ? null : index);
  };


  const formatPillar = (text) => {
    if (typeof text !== 'string') return text;
    return text.split(/(Pilar \d+|Pillar \d+|P\d+|20 Pilar|20 Pillars|10 Pilar|10 Pillars)/i).map((part, i) => {
      const match = part.match(/(?:Pil[l]?ar (\d+)|P(\d+)|(20) Pil[l]?ars?|(10) Pil[l]?ars?)/i);
      if (match) {
        const pillarNum = match[1] || match[2] || match[3] || match[4] || '20';
        const colors = {
          '1': 'text-blue-600 dark:text-blue-400 font-black px-1.5 py-0.5 bg-blue-500/10 rounded-xs border border-blue-500/20',
          '2': 'text-purple-600 dark:text-purple-400 font-black px-1.5 py-0.5 bg-purple-500/10 rounded-xs border border-purple-500/20',
          '3': 'text-yellow-600 dark:text-yellow-400 font-black px-1.5 py-0.5 bg-yellow-500/10 rounded-xs border border-yellow-500/20',
          '4': 'text-emerald-600 dark:text-emerald-400 font-black px-1.5 py-0.5 bg-emerald-500/10 rounded-xs border border-emerald-500/20',
          '5': 'text-pink-600 dark:text-pink-400 font-black px-1.5 py-0.5 bg-pink-500/10 rounded-xs border border-pink-500/20',
          '6': 'text-orange-600 dark:text-orange-400 font-black px-1.5 py-0.5 bg-orange-500/10 rounded-xs border border-orange-500/20',
          '7': 'text-rose-600 dark:text-rose-400 font-black px-1.5 py-0.5 bg-rose-500/10 rounded-xs border border-rose-500/20',
          '8': 'text-cyan-600 dark:text-cyan-400 font-black px-1.5 py-0.5 bg-cyan-500/10 rounded-xs border border-cyan-500/20',
          '9': 'text-indigo-600 dark:text-indigo-400 font-black px-1.5 py-0.5 bg-indigo-500/10 rounded-xs border border-indigo-500/20',
          '10': 'text-teal-600 dark:text-teal-400 font-black px-1.5 py-0.5 bg-teal-500/10 rounded-xs border border-teal-500/20',
          '11': 'text-sky-600 dark:text-sky-400 font-black px-1.5 py-0.5 bg-sky-500/10 rounded-xs border border-sky-500/20',
          '12': 'text-lime-600 dark:text-lime-400 font-black px-1.5 py-0.5 bg-lime-500/10 rounded-xs border border-lime-500/20',
          '13': 'text-amber-600 dark:text-amber-400 font-black px-1.5 py-0.5 bg-amber-500/10 rounded-xs border border-amber-500/20',
          '14': 'text-fuchsia-600 dark:text-fuchsia-400 font-black px-1.5 py-0.5 bg-fuchsia-500/10 rounded-xs border border-fuchsia-500/20',
          '15': 'text-violet-600 dark:text-violet-400 font-black px-1.5 py-0.5 bg-violet-500/10 rounded-xs border border-violet-500/20',
          '16': 'text-emerald-700 dark:text-emerald-300 font-black px-1.5 py-0.5 bg-emerald-500/15 rounded-xs border border-emerald-500/30',
          '17': 'text-blue-700 dark:text-blue-300 font-black px-1.5 py-0.5 bg-blue-500/15 rounded-xs border border-blue-500/30',
          '18': 'text-teal-700 dark:text-teal-300 font-black px-1.5 py-0.5 bg-teal-500/15 rounded-xs border border-teal-500/30',
          '19': 'text-rose-700 dark:text-rose-300 font-black px-1.5 py-0.5 bg-rose-500/15 rounded-xs border border-rose-500/30',
          '20': 'text-emerald-800 dark:text-emerald-200 font-black px-1.5 py-0.5 bg-emerald-500/20 rounded-xs border border-emerald-500/40',
        };
        return <span key={i} className={colors[pillarNum] || 'text-zinc-600 dark:text-zinc-400 font-black px-1.5 py-0.5 bg-zinc-500/10 rounded-xs'}>{part}</span>;
      }
      return part;
    });
  };

  const ensureSnapReady = (snapUrl, clientKey) => {
    return new Promise((resolve) => {
      if (typeof window !== 'undefined' && window.snap && typeof window.snap.pay === 'function') {
        return resolve(true);
      }
      if (typeof document === 'undefined') return resolve(false);

      const src = snapUrl || 'https://app.sandbox.midtrans.com/snap/snap.js';
      let script = document.querySelector(`script[src*="snap.js"]`) || document.getElementById('midtrans-snap-script');
      if (!script) {
        script = document.createElement('script');
        script.id = 'midtrans-snap-script';
        script.src = src;
        if (clientKey) script.setAttribute('data-client-key', clientKey);
        document.head.appendChild(script);
      }

      let attempts = 0;
      const interval = setInterval(() => {
        attempts++;
        if (typeof window !== 'undefined' && window.snap && typeof window.snap.pay === 'function') {
          clearInterval(interval);
          resolve(true);
        } else if (attempts > 30) {
          clearInterval(interval);
          resolve(false);
        }
      }, 150);
    });
  };

  const triggerSnapPayment = async (token, orderId, redirectUrl = null, snapUrl = null, clientKey = null) => {
    if (!token) {
      if (redirectUrl) {
        window.open(redirectUrl, '_blank') || (window.location.href = redirectUrl);
      }
      return;
    }

    const ready = await ensureSnapReady(snapUrl, clientKey);

    if (ready && typeof window !== 'undefined' && window.snap && typeof window.snap.pay === 'function') {
      try {
        window.snap.pay(token, {
          onSuccess: function (resultSnap) {
            setPaymentStatus('success');
            if (window.showToast) {
              window.showToast({
                type: 'success',
                title: isEn ? 'PAYMENT SUCCESSFUL!' : 'PEMBAYARAN BERHASIL!',
                message: isEn
                  ? 'Your order has been verified. Redirecting to your dashboard...'
                  : 'Pembayaran Anda telah diverifikasi! Mengarahkan ke Dashboard...',
                duration: 4000
              });
            }
            setTimeout(() => {
              const targetPkg = PACKAGE_REGISTRY[selectedPackage] || PACKAGE_REGISTRY.full_mvp;
              const isPaidTarget = targetPkg.execution_model === 'self_service' && targetPkg.is_paid;
              window.location.href = isPaidTarget
                ? '/customer/dashboard#licenses'
                : '/customer/dashboard#projects';
            }, 1800);
          },
          onPending: function (resultSnap) {
            setPaymentStatus('pending');
            if (window.showToast) {
              window.showToast({
                type: 'info',
                title: isEn ? 'WAITING FOR PAYMENT' : 'MENUNGGU PEMBAYARAN',
                message: isEn
                  ? 'Please complete payment using the displayed QRIS / Virtual Account.'
                  : 'Silakan selesaikan pembayaran sesuai instruksi QRIS / Virtual Account.',
                duration: 6000
              });
            }
          },
          onError: function (resultSnap) {
            setPaymentStatus('error');
            if (window.showToast) {
              window.showToast({
                type: 'error',
                title: isEn ? 'PAYMENT FAILED' : 'PEMBAYARAN GAGAL',
                message: isEn ? 'Payment was cancelled or rejected.' : 'Pembayaran dibatalkan atau ditolak.'
              });
            }
          },
          onClose: function () {
            if (window.showToast) {
              window.showToast({
                type: 'info',
                title: isEn ? 'PAYMENT WINDOW CLOSED' : 'PROMPT DITUTUP',
                message: isEn ? 'You can click "Pay with Midtrans Snap" anytime to continue.' : 'Anda dapat menekan tombol bayar kapan saja untuk melanjutkan.'
              });
            }
          }
        });
        return;
      } catch (e) {
        console.error('Midtrans Snap pay error:', e);
      }
    }

    // Direct fallback if popup failed or blocked:
    if (redirectUrl) {
      if (window.showToast) {
        window.showToast({
          type: 'info',
          title: isEn ? 'OPENING MIDTRANS PAYMENT' : 'MEMBUKA GATEWAY PEMBAYARAN',
          message: isEn ? 'Redirecting to Midtrans secure payment window...' : 'Membuka jendela pembayaran resmi Midtrans...'
        });
      }
      window.open(redirectUrl, '_blank') || (window.location.href = redirectUrl);
    } else {
      if (window.showToast) {
        window.showToast({
          type: 'warning',
          title: isEn ? 'LOADING MIDTRANS' : 'MEMUAT MIDTRANS',
          message: isEn ? 'Snap is loading, please click Pay again in a moment.' : 'Sistem Snap sedang dimuat, silakan klik tombol bayar sekali lagi.'
        });
      }
    }
  };

  const openBookingModal = (packageTier, defaultVoucher = '') => {
    setSelectedPackage(packageTier);
    setFormData(prev => ({
      ...prev,
      name: prev.name || authUser?.name || '',
      email: prev.email || authUser?.email || '',
      phone: prev.phone || authUser?.phone || '',
      company: prev.company || authUser?.company || '',
      voucher_code: defaultVoucher || prev.voucher_code
    }));
    setSubmitSuccess(false);
    setSubmittedWaUrl('');
    setSubmitError(null);
    setLastOrder(null);
    setPaymentStatus('idle');
    setAgreeTerms(false);
    setLegalDocumentFile(null);
    setIsDraggingFile(false);
    setIsModalOpen(true);
  };

  // Defensive alias so any retail license button invoking openRetailModal() routes cleanly to openBookingModal()
  const openRetailModal = (packageTier, defaultVoucher = '') => openBookingModal(packageTier, defaultVoucher);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    if (name === 'email') {
      setFormData(prev => ({ ...prev, email: sanitizeStrictEmailInput(value) }));
      return;
    }
    if (name === 'phone') {
      setFormData(prev => ({ ...prev, phone: sanitizePhoneLocalDigitsInput(value, prev.country_code) }));
      return;
    }
    if (name === 'country_code') {
      setFormData(prev => ({
        ...prev,
        country_code: value,
        phone: sanitizePhoneLocalDigitsInput(prev.phone, value),
      }));
      return;
    }
    if (name === 'estimated_budget_display') {
      setFormData(prev => ({
        ...prev,
        estimated_budget_display: formatRegionalNumberInput(value, isEn ? 'en' : 'id'),
      }));
      return;
    }
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const activePkg = PACKAGE_REGISTRY[selectedPackage] || PACKAGE_REGISTRY.full_mvp;
  const isSelfService = activePkg.execution_model === 'self_service';
  const isPaidSelfService = isSelfService && activePkg.is_paid;
  const isRetailTier = isSelfService;

  const handleSubmitInquiry = async (e) => {
    e.preventDefault();
    if (formData.honeypot) return; // bot detected

    if (!agreeTerms && selectedPackage !== 'retail_spark') {
      if (window.showToast) {
        window.showToast({
          type: 'warning',
          title: isEn ? 'AGREEMENT REQUIRED' : 'PERSETUJUAN DIPERLUKAN',
          message: isEn
            ? 'Please check and agree to the Terms of Service and Refund Policy to proceed.'
            : 'Anda wajib mencentang persetujuan Syarat & Ketentuan serta Kebijakan Pengembalian Dana untuk melanjutkan pemesanan.'
        });
      }
      return;
    }

    if (selectedPackage === 'impact_nonprofit_grant') {
      if (!formData.legal_decree_number?.trim()) {
        const msg = isEn
          ? 'Please enter your official Decree / AHU / Deed registration number for CSR grant verification.'
          : 'Harap masukkan Nomor SK Kemenkumham / Akta Notaris / Sertifikat Lembaga untuk verifikasi subsidi CSR.';
        if (window.showToast) {
          window.showToast({
            type: 'warning',
            title: isEn ? 'LEGAL NUMBER REQUIRED' : 'NOMOR SK / AKTA WAJIB',
            message: msg
          });
        }
        setSubmitError(msg);
        return;
      }
      if (!legalDocumentFile) {
        const msg = isEn
          ? 'Please attach a scan of your official legal deed / decree document (PDF, JPG, or PNG).'
          : 'Harap lampirkan pindaian berkas dokumen legalitas resmi (PDF, JPG, atau PNG) sebagai bukti berhak menerima subsidi 100% CSR.';
        if (window.showToast) {
          window.showToast({
            type: 'warning',
            title: isEn ? 'DOCUMENT FILE REQUIRED' : 'BERKAS DOKUMEN WAJIB',
            message: msg
          });
        }
        setSubmitError(msg);
        return;
      }
    }

    setIsSubmitting(true);
    setSubmitError(null);

    try {
      const payload = new FormData();
      payload.append('name', formData.name);
      payload.append('company', formData.company || (isRetailTier ? `${formData.name} (Personal)` : ''));
      payload.append('email', formData.email);
      payload.append('country_code', formData.country_code);
      payload.append('phone', formData.phone);
      payload.append('package_tier', selectedPackage);
      if (formData.voucher_code) payload.append('voucher_code', formData.voucher_code);
      if (activePkg.requires_sprint_batch && sprintBatch) payload.append('sprint_batch', sprintBatch);
      if (activePkg.requires_kickoff_slot && kickoffSlot) payload.append('kickoff_slot', kickoffSlot);
      if (selectedPackage === 'full_mvp' && hasBlueprint) payload.append('has_blueprint', hasBlueprint);
      if (selectedPackage === 'full_mvp' && hasBlueprint === 'ready' && blueprintSlug) payload.append('blueprint_slug', blueprintSlug);
      if (selectedPackage === 'umkm_starter' && umkmCategory) payload.append('umkm_category', umkmCategory);
      if (formData.notes) payload.append('notes', formData.notes);
      if (formData.estimated_budget_display) payload.append('budget_range', formData.estimated_budget_display);
      if (selectedPackage === 'impact_nonprofit_grant') {
        payload.append('legal_decree_number', formData.legal_decree_number || '');
        payload.append('organization_type', formData.organization_type || 'NONPROFIT_YAYASAN');
        if (legalDocumentFile) {
          payload.append('legal_document', legalDocumentFile);
        }
      }

      const response = await fetch('/api/pricing/inquiry', {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: payload
      });

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw new Error(result.message || (isEn ? 'Failed to process checkout.' : 'Gagal memproses checkout transaksi.'));
      }

      setSubmitSuccess(true);
      if (result.whatsapp_url && !isRetailTier) {
        setSubmittedWaUrl(result.whatsapp_url);
      } else {
        setSubmittedWaUrl('');
      }
      setLastOrder(result);

      // If it's a paid package with a Midtrans Snap token, launch Snap payment modal directly!
      if (result.is_paid_package && result.snap_token) {
        setTimeout(() => {
          triggerSnapPayment(
            result.snap_token,
            result.order_id,
            result.redirect_url,
            result.snap_url,
            result.client_key
          );
        }, 150);
      } else if (selectedPackage === 'retail_spark') {
        // Free Spark tier guest mode: launch blueprint generator (Phase 1-2 Teaser Only, 2x/month quota)
        setTimeout(() => {
          window.location.href = `/blueprint?tier=spark${satuPintuReturnQuery || ''}`;
        }, 600);
      }
    } catch (err) {
      setSubmitError(err.message || (isEn ? 'An error occurred. Please try again.' : 'Terjadi kesalahan sistem. Silakan coba lagi.'));
    } finally {
      setIsSubmitting(false);
    }
  };

  const faqs = [
    {
      category: 'SATU_PINTU',
      q: isEn
        ? 'Why does the One-Stop Wizard (v12.0) require selecting a SaaS License or Mitra Contract before unlocking full blueprints (No Unpaid Studio Bypass)?'
        : 'Mengapa Satu Pintu Wizard v12.0 mengunci akses cetak biru penuh ke pemilihan Paket SaaS atau Kontrak Mitra (Tanpa Tombol Bypass Gratis)?',
      a: isEn
        ? 'To guarantee 100% commercial consistency between the One-Stop configurator and our official 7 Retail SaaS Tiers & 3 Mitra Studio Contracts below. In One-Stop Wizard v12.0, every left-panel selection (Step B 12 Sectors, Step C Stack Topology, Step D 36 Visual UI + 6 Scrollytelling Presets, Step E1 Persona Mode, Step E2 Tier-1 Regulation) dynamically updates the right-hand summary in real time and locks directly into Step F (your chosen SaaS License or Mitra Contract). Server-side validation in BlueprintController::create() also enforces entitlement so unpaid sessions cannot bypass paid Pro/Ultimate/Agency tiers.'
        : 'Demi menjaga konsistensi komersial 100% antara konfigurator Satu Pintu di atas dengan daftar 7 Paket SaaS Retail & 3 Kontrak Mitra di bawahnya. Pada Satu Pintu v12.0, setiap pilihan di panel kiri (Langkah B 12 Sektor, Langkah C Topologi Stack, Langkah D 36 Visual UI + 6 Preset Scrollytelling, Langkah E1 Mode Persona, Langkah E2 Regulasi Tier-1) langsung memperbarui panel status kanan secara real-time dan dikunci ke Langkah F (pilihan Lisensi SaaS atau Kontrak Mitra). Proteksi server di BlueprintController::create() juga memblokir bypass URL langsung tanpa lisensi aktif.'
    },
    {
      category: 'SATU_PINTU',
      q: isEn
        ? 'Why is "Impact & Non-Profit Grant (Yayasan, NGO, Rumah Ibadah & Riset)" priced at Rp 0 (Verified CSR), and who is eligible?'
        : 'Mengapa paket "Hibah Yayasan, NGO, Rumah Ibadah & Riset" bertarif Rp 0 (Terverifikasi CSR) dan apa syarat wajibnya?',
      a: isEn
        ? 'Its real commercial value is Rp 1,490,000 (equivalent to Ultimate Architecture OS), which Neriah Pro subsidizes 100% to Rp 0 under our Corporate Social Responsibility (CSR) & Academic Impact program. It is STRICTLY reserved for non-commercial social foundations, places of worship (Mosques, Churches, Temples, Viharas), charity schools, and university research teams requiring ISAK 35 / PSAK 109 donor transparency. Applicants must upload or verify official legal standing (Akta Notaris Yayasan / SK Kemenkumham / Campus Research Letter). Commercial businesses (PT, CV, Stores, Startups) are NOT eligible and must choose Single-App UMKM Pass, Pro Agentic, or Ultimate OS.'
        : 'Nilai komersial aslinya adalah Rp 1.490.000 (setara paket Ultimate Architecture OS), namun disubsidi 100% menjadi Rp 0 melalui program Tanggung Jawab Sosial (CSR) Neriah Pro. Paket ini KHUSUS diperuntukkan bagi entitas non-komersial: Yayasan Sosial/Kemanusiaan, Rumah Ibadah (Masjid, Gereja, Pura, Vihara), Sekolah Amal, dan Riset Akademik Kampus yang membutuhkan akuntabilitas dana umat/donatur sesuai standar ISAK 35 / PSAK 109. Pemohon WAJIB melampirkan verifikasi dokumen legalitas resmi (Akta Notaris Yayasan / SK Kemenkumham / Surat Tugas Riset Kampus). Bisnis komersial (PT, CV, Toko, Startup) TIDAK berhak mengklaim hibah ini dan wajib memilih Single-App Pass, Pro Agentic, atau Ultimate OS.'
    },
    {
      category: 'UI_UX_12_SEKTOR',
      q: isEn
        ? 'How does Cross-Stage Causal Synergy work from Stage 1 (Sector & Visual UI) all the way to Stage 7 (BAST & 90-Day SLA)?'
        : 'Bagaimana cara kerja Sinergi Kausal Lintas-Tahap (Cross-Stage Synergy) dari Tahap 1 (Sektor & Visual UI) hingga Tahap 7 (BAST & Garansi SLA 90 Hari)?',
      a: isEn
        ? 'Nothing in Project OS v12.0 is an isolated form! Changing your Sector (12 Taxonomies), Stack (M1/D1/S1), Internal Cockpit (Zone A: 36 Visual UIs), or Public Scrollytelling Preset (Zone B: 6 60fps Presets) in Stage 1 automatically recalculates: (Stage 2) PostgreSQL 16 Strict ULID tables & Dual-Persona Storyboard; (Stage 3) Algorithmic Man-Hours RAB, SHA-256 Scope Lock & Canary IP Watermark; (Stage 4) 7-Phase Prompt Sequencer & Living CVE Autopatch; (Stage 5) Schema.org Multi-Graph JSON-LD, /llms.txt GEO Citation, Offline Outbox PWA & UU PDP Blind Index; (Stage 6) Co-Design Alignment Room & Sector UAT Checklist; and (Stage 7) Digital BAST + 90-Day Post-BAST SLA Bug-Warranty.'
        : 'Tidak ada satu pun fitur di Project OS v12.0 yang berdiri sendiri! Mengubah Sektor (12 Taksonomi), Stack (M1/D1/S1), Kokpit Internal (Zona A: 36 Visual UI), atau Preset Scrollytelling Publik (Zona B: 6 Preset 60fps) pada Tahap 1 secara otomatis menghitung ulang: (Tahap 2) Tabel PostgreSQL 16 Strict ULID & Storyboard Dual-Persona; (Tahap 3) RAB Jam Kerja Algoritmik, Segel Kontrak SHA-256 & Canary IP Watermark; (Tahap 4) 7-Fase Prompt AI & Living CVE Autopatch; (Tahap 5) Schema.org Multi-Graph JSON-LD, GEO /llms.txt, Offline Outbox PWA & Blind Index UU PDP; (Tahap 6) Co-Design Alignment Room & Checklist UAT Sektoral; hingga (Tahap 7) BAST Digital + Garansi Bug SLA 90 Hari.'
    },
    {
      category: 'SEO_GEO_UU_PDP',
      q: isEn
        ? 'How does PRIMITIVE_SEO_GEO_AUTHORITY_ENGINE get our application cited in the Top 3 of ChatGPT Search, Perplexity, Claude, and Google AI Overviews?'
        : 'Bagaimana PRIMITIVE_SEO_GEO_AUTHORITY_ENGINE membuat aplikasi kami dikutip di Top-3 ChatGPT Search, Perplexity, Claude, dan Google AI Overviews?',
      a: isEn
        ? 'Traditional SEO only targets blue links; Generative Engine Optimization (GEO) feeds structured facts directly to AI citation crawlers (OAI-SearchBot, PerplexityBot, ClaudeBot, Google-Extended). Project OS v12.0 compiles 5 automated GEO engines: (1) Multi-Graph Schema.org JSON-LD (@graph combining Organization, SoftwareApplication, FAQPage + Sector-Specific types like Product, LodgingBusiness, MedicalClinic, Course, Restaurant, Event, NGO); (2) Native /llms.txt and /llms-full.txt markdown indexes; (3) Semantic HTML Answer Capsules (50-word factual TL;DR + <table> comparison matrix); (4) IndexNow instant ping on save; and (5) Local 1200x630 OpenGraph & QR Citation Cards rendered via PHP GD/SVG with zero external CDN latency.'
        : 'SEO lama hanya mengejar halaman pencarian biasa, sedangkan Generative Engine Optimization (GEO) menyuplai fakta terstruktur langsung ke mesin pencari AI (OAI-SearchBot, PerplexityBot, ClaudeBot, Google-Extended). Project OS v12.0 membekali 5 mesin otomatis: (1) Multi-Graph Schema.org JSON-LD (@graph berisi Organization, SoftwareApplication, FAQPage + tipe spesifik 12 sektor seperti Product, LodgingBusiness, MedicalClinic, Course, Restaurant, Event, NGO); (2) Endpoint native /llms.txt & /llms-full.txt; (3) Kapsul Jawaban Semantik (50 kata TL;DR faktual + tabel HTML <table>); (4) IndexNow Instant Ping saat halaman disimpan; dan (5) Kartu OpenGraph 1200x630 + QR lokal berbasis PHP GD/SVG tanpa latensi CDN luar.'
    },
    {
      category: 'SEO_GEO_UU_PDP',
      q: isEn
        ? 'How does PRIMITIVE_UU_PDP_PRIVACY_VAULT comply with Indonesia UU PDP No. 27/2022 & GDPR without breaking database search or financial audit ledgers?'
        : 'Bagaimana PRIMITIVE_UU_PDP_PRIVACY_VAULT mematuhi UU PDP No. 27/2022 & GDPR tanpa merusak fitur pencarian database maupun buku besar akuntansi?',
      a: isEn
        ? 'Encrypting NIK, Tax ID (NPWP), phone, or medical diagnoses with standard AES-256 makes SQL WHERE queries impossible without full-table decryption. Our HasEncryptedPiiWithBlindIndex trait pairs Laravel AES-256 encrypted casting with a deterministic 64-character HMAC-SHA256 blind index column (*_blind_index) derived from a dedicated pepper key—preserving O(1) exact-match B-Tree lookups while keeping raw database dumps unreadable. Furthermore, ExportAndRedactUserPiiAction provides 1-click JSON Data Portability (Article 31) and Ledger-Safe Right-to-Erasure (Article 34) that anonymizes personal identity into [REDACTED_UU_PDP_<ULID>] while strictly keeping financial Transaction rows intact for 10-year tax compliance.'
        : 'Mengenkripsi NIK, NPWP, nomor telepon, atau rekam medis dengan AES-256 biasa membuat fitur pencarian SQL WHERE lumpuh. Trait HasEncryptedPiiWithBlindIndex kami menggabungkan enkripsi AES-256 dengan kolom indeks buta deterministik 64-karakter HMAC-SHA256 (*_blind_index)—sehingga pencarian exact-match tetap secepat O(1) sub-10ms namun isi tabel tetap terenkripsi rapat jika di-dump. Selain itu, ExportAndRedactUserPiiAction menyediakan Ekspor Portabilitas Data JSON 1-Klik (Pasal 31) dan Hak Penghapusan Aman-Akuntansi (Pasal 34) yang menyamarkan identitas menjadi [REDACTED_UU_PDP_<ULID>] tanpa menghapus baris transaksi jurnal keuangan (patuh retensi pajak 10 tahun).'
    },
    {
      category: 'SEO_GEO_UU_PDP',
      q: isEn
        ? 'What happens when field staff, couriers, or cashiers lose internet connection (PRIMITIVE_OFFLINE_OUTBOX_PWA)?'
        : 'Apa yang terjadi jika kurir lapangan, kasir POS, atau petugas gudang kehilangan sinyal internet (PRIMITIVE_OFFLINE_OUTBOX_PWA)?',
      a: isEn
        ? 'Operational transactions NEVER fail or duplicate on spotty 3G/basement Wi-Fi! When navigator.onLine drops, our OfflineOutboxQueue (IndexedDB + Service Worker /sw-offline-outbox.js) intercepts the mutation, mints a client-side 26-character ULID + X-Idempotency-Key, saves the payload in the local encrypted Outbox Queue, and displays an Amber [OFFLINE QUEUED — WILL SYNC AUTOMATICALLY] badge. The moment connectivity returns, Background Sync replays queued mutations in chronological ULID order while server-side idempotency locks prevent double billing or double stock deduction.'
        : 'Transaksi operasional TIDAK AKAN hilang atau tercatat ganda meski sinyal hilang di basement/pelosok! Saat koneksi terputus, OfflineOutboxQueue (IndexedDB + Service Worker /sw-offline-outbox.js) menangkap transaksi, mencetak 26-karakter ULID + X-Idempotency-Key di sisi perangkat, menyimpannya di antrean Outbox lokal, dan menampilkan badge Amber [OFFLINE QUEUED — AKAN SINKRON OTOMATIS]. Begitu sinyal pulih, sistem menyinkronkan antrean secara berurutan tanpa risiko dobel bayar atau dobel potong stok.'
    },
    {
      category: 'SLA_GARANSI_HUKUM',
      q: isEn
        ? 'How does the Post-BAST 90-Day Contractual SLA Bug-Warranty Tracker separate free bug fixes from paid Change Requests (v1.1)?'
        : 'Bagaimana Pelacak Garansi Bug SLA 90 Hari Pasca-BAST memisahkan perbaikan bug gratis (Rp 0) dari tambahan fitur berbayar (Change Request v1.1)?',
      a: isEn
        ? 'The moment Digital BAST is signed, Project OS automatically activates a 90-day SLA warranty timer (sla_warranty_expires_at). When a client submits an issue in /customer/dashboard (with reproduction steps and stack trace), PosSlaAndSandboxEngineService compares the report against the SHA-256 Locked PRD Scope: (1) If it is a regression or defect within locked v1.0 modules during the 90-day window, it is classified as BUG_WARRANTY_COVERED (Rp 0 Free Repair + auto-generated Developer Bugfix Prompt); (2) If it requests a new module outside v1.0 or after 90 days, it routes cleanly to NEW_SCOPE_PAID_DELTA (Change Request v1.1) — eliminating post-handover disputes.'
        : 'Begitu berita acara BAST Digital ditandatangani, Project OS otomatis mengaktifkan penghitung mundur Garansi SLA 90 Hari (sla_warranty_expires_at). Saat klien melapor kendala di /customer/dashboard, PosSlaAndSandboxEngineService mencocokkan laporan dengan Scope PRD bersegel SHA-256: (1) Jika merupakan error/bug pada modul v1.0 yang terkunci selama masa 90 hari, tiket diklasifikasikan sebagai BUG_WARRANTY_COVERED (Perbaikan Gratis Rp 0 + otomatis menerbitkan Developer Bugfix Prompt); (2) Jika merupakan permintaan fitur baru di luar kontrak v1.0, sistem mengarahkannya ke jalur NEW_SCOPE_PAID_DELTA (Addendum v1.1) secara adil tanpa debat kusir.'
    },
    {
      category: 'SLA_GARANSI_HUKUM',
      q: isEn
        ? 'How do Pre-Migration Atomic Rollback (./deploy.sh rollback), Local Webhook Simulator, and Multi-Entity Holding Consolidation work?'
        : 'Bagaimana cara kerja Pre-Migration Atomic Rollback (./deploy.sh rollback), Simulator Webhook Lokal, dan Konsolidasi Multi-Cabang/Holding?',
      a: isEn
        ? '(1) Zero-Fear Deployment: Every ./deploy.sh 2 run automatically captures a compressed pre-migration snapshot (storage/backups/pre_migrate_*.sql.gz, 7-day retention) before touching schemas; if anything fails, ./deploy.sh rollback (Scenario 7) restores code and database in seconds. (2) Chaos Sandbox Console: In non-production environments (APP_ENV !== "production"), developers can trigger simulated QRIS settlement, expired VA, and 3x concurrent duplicate webhook attacks with 1 click (strictly blocked 403 in production). (3) Multi-Entity Holding: PosOrganizationWorkspace supports self-referencing parent_organization_id ULID hierarchy for Holding -> Subsidiary -> Branch consolidation with inter-company journal elimination.'
        : '(1) Deploy Tanpa Cemas: Setiap eksekusi ./deploy.sh 2 otomatis mencadangkan snapshot database terkompresi (storage/backups/pre_migrate_*.sql.gz, retensi 7 hari) sebelum migrasi; jika terjadi kendala, perintah ./deploy.sh rollback (Skenario 7) memulihkan kode dan database dalam hitungan detik. (2) Konsol Simulasi Webhook: Di lingkungan lokal/staging (APP_ENV !== "production"), developer dapat menguji simulasi lunas QRIS, VA kadaluarsa, hingga serangan 3x webhook paralel dengan 1 klik (otomatis diblokir 403 di produksi). (3) Multi-Cabang & Holding: PosOrganizationWorkspace mendukung hierarki ULID parent_organization_id (Induk Holding -> Anak Perusahaan -> Cabang) dengan eliminasi transaksi antar-entitas.'
    },
    {
      category: 'AI_MCP_SECURITY',
      q: isEn
        ? 'How do NeriahPro AgentShield, Living CVE Autopatch, Canary IP Watermark, and the SHA-256 Cryptographic Audit Log protect our enterprise?'
        : 'Bagaimana NeriahPro AgentShield, Living CVE Autopatch, Canary IP Watermark, dan Audit Log Kriptografi SHA-256 melindungi sistem enterprise kami?',
      a: isEn
        ? 'Project OS v9–v11 embeds military-grade governance: (1) AgentShield 3-Gate Firewall sanitizes RAG inputs against prompt injection, blocks unauthorized tool execution, and verifies Composer/NPM lockfiles against typosquatting; (2) Living CVE Autopatch monitors stack advisories and generates non-breaking upgrade playbooks; (3) Zero-Width Canary IP Watermark embeds invisible cryptographic provenance into generated artifacts to deter blueprint piracy; and (4) Every AI tool call, payment settlement, and PII erasure is recorded in an immutable SHA-256 hash-chained Audit Log exportable to 3-sheet CPA Excel (.xlsx).'
        : 'Project OS v9–v12 menanamkan tata kelola keamanan berlapis: (1) Firewall 3-Gerbang AgentShield menyaring input RAG dari serangan Prompt Injection, mengunci izin eksekusi 49 tool AI, dan memvalidasi paket Composer/NPM dari bahaya typosquatting; (2) Living CVE Autopatch memantau celah keamanan stack dan menyiapkan panduan patch otomatis; (3) Zero-Width Canary IP Watermark menyisipkan tanda air kriptografi tak kasat mata untuk melacak kebocoran dokumen; serta (4) Seluruh aksi AI, pembayaran, dan penghapusan data tercatat dalam rantai hash Audit Log SHA-256 yang dapat diekspor ke Excel (.xlsx) 3-Sheet standar akuntan publik.'
    },
    {
      category: 'UI_UX_12_SEKTOR',
      q: isEn
        ? 'What is the difference between Zone A (36 Internal Sector Cockpits) and Zone B (6 Public Scrollytelling Presets), plus Dual-Persona Studio Mode?'
        : 'Apa perbedaan Zona A (36 Kokpit Operasional Internal) dengan Zona B (6 Preset Scrollytelling Publik), serta fitur Mode Studio Dual-Persona?',
      a: isEn
        ? 'Great applications require two distinct visual ergonomics: Zone A governs your internal back-office/operational dashboard (36 deterministic Clean Solid Brutalist cockpits across 12 sectors—zero decorative clutter, keyboard shortcuts, sticky headers, O(1) cursor pagination), while Zone B governs your public-facing landing/product showcase (6 60fps CSS Scroll-Driven Scrollytelling presets like Sticky Frame Scrubbing, Bento Perspective Grid, and Interactive Before/After Slider with automatic prefers-reduced-motion fallback). Furthermore, inside /blueprint/studio, non-technical founders can use 5-Step Business Storyboard Mode while engineers toggle seamlessly into 3-Pane Architect Mode and the Co-Design Alignment Room.'
        : 'Aplikasi kelas dunia memisahkan dua zona visual secara tegas: Zona A mengatur kokpit kerja internal/back-office (36 tata letak Clean Solid Brutalism di 12 sektor—bebas dekorasi mengganggu, penuh shortcut keyboard, sticky header, dan cursor pagination O(1)), sedangkan Zona B mengatur halaman depan/publik (6 preset Scrollytelling 60fps berbasis CSS Scroll-Driven seperti Sticky Frame Scrubbing, Bento Perspective Grid, dan Before/After Slider lengkap dengan fallback prefers-reduced-motion). Di dalam /blueprint/studio, klien awam juga dapat memilih Mode Bisnis 5-Langkah sementara tim teknis menggunakan Mode Arsitek 3-Pane dan Co-Design Alignment Room.'
    },
    {
      category: 'SATU_PINTU',
      q: isEn
        ? 'Why is Blueprint Advisory (Rp 2.5M) priced similarly to UMKM Starter (Rp 3.75M), even though Advisory includes NO coding?'
        : 'Mengapa harga Blueprint Advisory (Rp 2.5 Jt) mirip dengan UMKM Digital Starter (Rp 3.75 Jt), padahal Advisory tidak termasuk koding?',
      a: isEn
        ? 'The fundamental difference lies in PROJECT COMPLEXITY and WHO EXECUTES CODING! (1) Blueprint Advisory (Rp 2.5M) is tailored for MASSIVE CUSTOM ENTERPRISE SYSTEMS (SaaS platforms, multi-sided marketplaces, fintech, logistics) whose full turnkey development value ranges from Rp 50M to Rp 200M+. On this scale, senior architectural planning (26-parameter PRD, PostgreSQL Strict ULID O(1) DDL, WBS 5 sprints, and 1-on-1 Principal Architect scoping) is critical to prevent hundreds of millions in costly architecture failures. Coding is handled by your internal team (or your advisory fee is 100% credited toward our Full MVP 50% DP). (2) Conversely, UMKM Digital Starter (Rp 3.75M) is a subsidized CSR program (50% off normal Rp 7.5M, capped at 2 businesses/month) for LOCAL RETAIL SHOPS and simple stores. Its workflows are standardized (POS cashier, QRIS, customer DB, WhatsApp receipts), allowing Neriah Pro engineers to code and deploy it quickly without lengthy custom enterprise architecture sessions.'
        : 'Perbedaan mendasarnya terletak pada SKALA SISTEM dan SIAPA YANG MENGODING! (1) Blueprint Advisory (Rp 2.5 Jt) dirancang untuk SISTEM KUSTOM ENTERPRISE BERSKALA BESAR (SaaS, platform marketplace, logistik, fintech) yang biaya pengembangannya mencapai Rp 50 Jt hingga ratusan juta rupiah. Pada skala ini, rancangan teknis (PRD 26 parameter, skema PostgreSQL Strict ULID O(1), WBS 5 sprint, dan sesi 1-on-1 Principal Architect) mutlak dibutuhkan agar developer klien tidak salah bangun dan rugi puluhan juta. Koding dilakukan oleh tim dev klien sendiri (atau biaya Rp 2.5 Jt ini otomatis memotong DP 50% jika lanjut dikodingkan Neriah Pro). (2) Sebaliknya, UMKM Digital Starter (Rp 3.75 Jt) adalah PROGRAM STIMULUS SUBSIDI 50% (dari normal Rp 7.5 Jt, kuota 2 usaha/bulan) KHUSUS TOKO RETAIL/LOKAL. Alur kerjanya terstandar (kasir POS, QRIS otomatis, database pelanggan, notifikasi WhatsApp) sehingga bisa dikodingkan dan dideploy cepat oleh Neriah Pro tanpa memerlukan perancangan arsitektur custom yang rumit.'
    },
    {
      category: 'SATU_PINTU',
      q: isEn ? 'Are Instant Architectural Blueprint packages 100% self-service without Neriah Pro coding?' : 'Apakah paket Instant Architectural Blueprint 100% self-service tanpa keterlibatan koding Neriah Pro?',
      a: isEn
        ? 'Yes, absolutely. Instant Architectural Blueprint packages (Spark, Starter, Single-App Pass, Pro Agentic, Agency White-Label, Ultimate OS) are pure digital architectural specifications and engineering deliverables (26-parameter PRD, PostgreSQL Strict ULID DDL SQL, Mermaid diagrams, Jira/Linear WBS, Decoupled 2026+ matrix, OpenAPI 3.1 contracts, and 20 Paripurna Pillars). They are designed for you, your in-house engineering team, freelancers, or AI coding agents to build the software independently. Neriah Pro does NOT write application code for these self-service packages. If you need Neriah Pro engineers to build, code, and deploy your software turnkey, select our Custom Engineering Studio contracts (Full MVP Monolith or UMKM Starter).'
        : 'Ya, benar 100%. Paket Instant Architectural Blueprint (Spark, Starter, Single-App Pass, Pro Agentic, Agency White-Label, Ultimate OS) adalah produk spesifikasi arsitektur digital mandiri (PRD 26 parameter, skema DDL PostgreSQL Strict ULID, Diagram Mermaid, WBS sprint Jira-ready, matriks Decoupled 2026+, kontrak OpenAPI 3.1, dan 20 Pilar Paripurna). Seluruh aset ini diperuntukkan bagi Anda, tim programmer in-house, freelancer, atau AI coding agent Anda untuk membangun dan mengoding aplikasinya sendiri. Neriah Pro sama sekali TIDAK terlibat dalam penulisan koding untuk paket self-service ini. Apabila Anda membutuhkan tim arsitek dan engineer Neriah Pro untuk mengoding, menguji, dan mendeploy aplikasi sampai siap pakai di server VPS, silakan pilih Layanan Custom Engineering Studio kami (Full MVP Monolith atau UMKM Starter).'
    },
    {
      category: 'SATU_PINTU',
      q: isEn ? 'Do I need to log in to subscribe or use the Instant Blueprint packages?' : 'Apakah perlu login untuk berlangganan atau menggunakan paket Instant Blueprint?',
      a: isEn
        ? 'For the Spark (Free) tier, NO login is required — you can generate quick Phase 1–2 idea audits instantly in Guest Mode without creating an account. For paid packages (Starter, Single-App Pass, Pro Agentic, Agency, Ultimate) and verified CSR Grants, logging in or creating a Neriah Pro account is mandatory. An account ensures your digital license, persistent PRD version history, lifetime download access, and AI re-prompt tokens are securely preserved.'
        : 'Untuk paket Spark (Free), Anda TIDAK PERLU login sama sekali — Anda dapat langsung menggunakannya dalam Guest Mode untuk audit konsep Fase 1–2 tanpa registrasi ataupun kartu kredit. Namun untuk paket berbayar (Starter, Single-App Pass, Pro Agentic, Agency, Ultimate) serta Hibah CSR Terverifikasi, Anda diwajibkan login atau mendaftar akun Neriah Pro terlebih dahulu. Kepemilikan akun ini diperlukan untuk mencatat lisensi digital resmi, mengamankan hak unduh seumur hidup (lifetime download), serta menyimpan riwayat versi dokumen PRD dan token regenerasi AI Anda.'
    },
    {
      category: 'SATU_PINTU',
      q: isEn ? 'What are the usage limits and reset cycles for the free Spark package?' : 'Berapa batas kuota dan siklus reset untuk paket gratis (Spark)?',
      a: isEn
        ? 'The free Spark tier includes 2 idea audits (Phase 1–2) per month per guest session. This free quota automatically resets back to 2 credits on the 1st of every month (00:00 UTC). Unsaved guest sessions expire in 7 days, so we recommend downloading your Markdown or PDF summary immediately.'
        : 'Paket gratis Spark memberikan kuota 2x audit ide & lean PRD (Fase 1–2) per bulan per pengguna/perangkat. Kuota gratis ini otomatis di-reset kembali menjadi 2x kuota baru pada setiap tanggal 1 awal bulan baru (pukul 00:00 WIB). Sesi browser tamu aktif selama 7 hari, sehingga disarankan untuk segera mengekspor atau mengunduh ringkasan dokumen Markdown/PDF ke komputer atau ponsel Anda.'
    },
    {
      category: 'SLA_GARANSI_HUKUM',
      q: isEn ? 'What is the validity period and revision window for each package?' : 'Berapa lama masa berlaku akses dokumen dan jendela revisi untuk masing-masing paket?',
      a: isEn
        ? 'All paid tiers (Starter, Single-App Pass, Pro Agentic, Agency, Ultimate) grant lifetime download and reading access to your generated blueprints. In addition: Starter provides a 30-day form parameter revision window; Pro Agentic provides a 6-month window for unlimited AI re-prompts, schema tweaks, and architecture regenerations; Ultimate OS provides 1 year of priority enterprise updates plus a 60-day window to schedule your 60-minute 1-on-1 Google Meet session with our Lead Architect. For custom development contracts, Full MVP includes a 90-day (3-month) Post-BAST Contractual SLA bug warranty.'
        : 'Semua paket berbayar (Starter, Single-App Pass, Pro Agentic, Agency, Ultimate) memberikan hak akses dan unduh dokumen seumur hidup (Lifetime Download). Selain itu: Paket Starter memiliki jendela revisi form parameter selama 30 hari; Paket Pro Agentic menyediakan 6 bulan akses regenerasi AI sepuasnya (unlimited re-prompt) dan pembaruan arsitektur; Paket Ultimate OS mencakup 1 tahun prioritas pembaruan skema enterprise serta jendela 60 hari untuk memesan sesi Google Meet 60 menit bersama Lead Architect Neriah Pro. Untuk kontrak custom development, Full MVP dilengkapi 90 hari (3 bulan) Garansi Bug SLA Kontraktual pasca-BAST Digital.'
    },
    {
      category: 'SATU_PINTU',
      q: isEn ? 'Why should we pay Rp 2,500,000 for Advisory Blueprint if we already have in-house programmers?' : 'Mengapa butuh Jasa Blueprint Studio (Rp 2.500.000) jika kami sudah memiliki programmer in-house?',
      a: isEn
        ? 'Most software projects fail not because programmers cannot code, but because of vague specifications, messy database schemas, and scope creep. The Advisory Blueprint gives your team a battle-tested technical roadmap (26-parameter PRD, PostgreSQL Strict ULID ERD, O(1) Keyset pagination, and 5 sprint work breakdowns) saving you months of costly re-engineering—plus 100% of this fee deducts your 50% DP if you later upgrade to Full Turnkey MVP.'
        : 'Sebagian besar proyek gagal bukan karena programmer tidak bisa coding, melainkan karena ketiadaan cetak biru arsitektur yang solid: skema database tidak terindeks dengan baik, spesifikasi fitur kabur, dan sprint berantakan. Paket Advisory Blueprint memberikan roadmap teknis siap eksekusi (PRD 26 parameter, skema PostgreSQL Strict ULID, arsitektur pagination O(1), dan WBS 5 sprint) sehingga tim Anda hemat ratusan jam revisi—serta 100% biayanya memotong DP 50% jika Anda lanjut ke Full Turnkey MVP.'
    },
    {
      category: 'AI_MCP_SECURITY',
      q: isEn
        ? 'What are the "20 Pillars of Software Factory OS v12.0 Paripurna" included in Neriah Pro Project OS?'
        : 'Apa itu "20 Pilar Software Factory OS v12.0 Paripurna" yang menjadi standar rekayasa Project OS Neriah Pro?',
      a: isEn
        ? 'Project OS v12.0 expands our core 10 Pillars into 20 Paripurna Engineering Pillars: (1–10) 26-Parameter PRD, Clean Solid Brutalist UI/UX, Docker PHP 8.5/PG16/Redis Stack, 100+ Realistic ID Domain Seeders, AI Agent Directives (.cursorrules/AGENTS.md), Automated Pest/Playwright Contract Suite, GitHub Actions + deploy.sh CI/CD, Health Probes (/healthz), Encrypted Disaster Recovery Drills, and Scalar OpenAPI 3.1 Docs; PLUS (11–20) 12 Sectoral × 36 Visual UI Cockpits, Zone B 60fps Scrollytelling Presets, Dual-Persona Studio & Co-Design Room, 7-Phase Prompt Sequencer & CLI /init, Live 2-Way MCP Server Bridge, NeriahPro AgentShield & Living CVE Autopatch, Multi-Graph Schema.org + /llms.txt GEO Authority, UU PDP AES-256 + HMAC-SHA256 Blind Index Vault, Offline Outbox PWA Background Sync, and Post-BAST 90-Day SLA Triage + Multi-Entity Holding Consolidation.'
        : 'Project OS v12.0 memperluas 10 Pilar inti menjadi 20 Pilar Rekayasa Paripurna: (1–10) PRD 26 Parameter, UI/UX Clean Solid Brutalism, Docker PHP 8.5/PG16/Redis, 100+ Seeder Data Realistis Indonesia, Direktif AI (.cursorrules/AGENTS.md), Suite Uji Kontrak Pest/Playwright, CI/CD GitHub Actions + deploy.sh, Health Probes (/healthz), Backup Terenkripsi & DR Runbook, serta Scalar OpenAPI 3.1; DITAMBAH (11–20) 12 Sektor × 36 Visual UI Kokpit, Zona B 6 Preset Scrollytelling 60fps, Dual-Persona Studio & Co-Design Room, 7-Fase Prompt & CLI /init, Jembatan Live MCP 2-Arah, NeriahPro AgentShield & Living CVE Autopatch, Schema.org Multi-Graph + GEO /llms.txt, Brankas Privasi UU PDP Blind Index HMAC-SHA256, Offline Outbox PWA Anti-Sinyal-Buruk, serta Garansi SLA 90 Hari Pasca-BAST + Konsolidasi Holding Multi-Cabang.'
    },
    {
      category: 'UI_UX_12_SEKTOR',
      q: isEn
        ? 'Why does Project OS enforce PostgreSQL Strict ULID and Keyset Cursor Pagination O(1)?'
        : 'Mengapa Project OS mewajibkan arsitektur PostgreSQL Strict ULID dan Keyset Cursor Pagination O(1)?',
      a: isEn
        ? 'To guarantee enterprise scalability for millions of records and concurrent users without performance degradation. Conventional AUTO_INCREMENT integers leak business volume, fail in distributed databases, and are vulnerable to enumeration attacks. ULIDs (Universally Unique Lexicographically Sortable Identifiers, VARCHAR(26) via ->ulid("id")->primary()) provide 128-bit distributed uniqueness with sub-millisecond chronological sorting and zero PostgreSQL UUID type-cast errors. Furthermore, Keyset Cursor Pagination guarantees constant O(1) sub-10ms response times even on tables with tens of millions of rows.'
        : 'Untuk menjamin skalabilitas kelas enterprise yang mampu menangani jutaan data dan ribuan pengunjung bersamaan tanpa penurunan performa (anti-lemot). Primary key AUTO_INCREMENT tradisional membocorkan volume transaksi bisnis, rawan serangan enumerasi ID, dan rusak saat migrasi distributed database. Project OS menggunakan ULID 26-karakter (->ulid("id")->primary()) yang aman, terdistribusi, berurutan kronologis, dan bebas error tipe UUID PostgreSQL. Selain itu, Keyset Cursor Pagination O(1) menjamin kecepatan query stabil di bawah 10 milidetik bahkan pada tabel berisi puluhan juta data.'
    },
    {
      category: 'SLA_GARANSI_HUKUM',
      q: isEn
        ? 'How do clients monitor development progress in real-time via Master Gantt Timeline & Customer Portal?'
        : 'Bagaimana cara klien memantau progres pengerjaan secara real-time via Master Gantt Timeline & Customer Portal?',
      a: isEn
        ? 'Every client receives access to their dedicated Customer Portal (/customer/dashboard). Inside, an interactive Master Gantt Timeline powered by local Mermaid.js visualization tracks all 5 sprints in real-time alongside 9 transparency tabs (Deliverables, Payments, Legal Contracts, Change Requests, Co-Design Alignment, and Post-BAST 90-Day SLA & UU PDP Privacy Vault).'
        : 'Setiap klien mendapatkan akun resmi di Customer Portal Neriah Pro (/customer/dashboard). Di dalamnya terdapat Master Gantt Timeline interaktif berbasis pustaka lokal Mermaid.js (zero CDN latency) yang memvisualisasikan seluruh 5 sprint secara real-time beserta 9 tab transparansi penuh (Deliverable, Pembayaran, Kontrak Legal SHA-256, Change Request, Co-Design Room, hingga Garansi SLA 90 Hari & Brankas Privasi UU PDP).'
    },
    {
      category: 'SLA_GARANSI_HUKUM',
      q: isEn
        ? 'Why does Neriah Pro restrict sprint capacity (Managed Capacity) to max 2-3 parallel projects per batch?'
        : 'Mengapa Neriah Pro membatasi kapasitas sprint (Managed Capacity) maksimal 2–3 proyek per batch?',
      a: isEn
        ? 'To preserve elite engineering craftsmanship and guarantee zero burnout or "AI-slop" code generation. Neriah Pro strictly limits intake to 2-3 projects per batch (Batch 1, Batch 2, Batch Q1). Once batch capacity is full, the schedule locks via our Anti-Collision Engine, ensuring our Senior Architects and Engineers give 100% dedicated, uninterrupted focus to your codebase until production deployment.'
        : 'Demi menjaga standar rekayasa kelas atas (craftsmanship) dan menjamin kode yang dihasilkan bebas dari "AI-slop" atau bug tersembunyi. Neriah Pro secara ketat membatasi kapasitas maksimal 2–3 proyek per batch (Batch 1, Batch 2, Batch Q1). Ketika slot batch terisi, jadwal terkunci via Anti-Collision Engine sehingga tim Senior Architect dan Engineer kami fokus penuh 100% pada sistem Anda hingga live bergaransi di server produksi.'
    },
    {
      category: 'AI_MCP_SECURITY',
      q: isEn
        ? 'How do the AI Coding Directives (.cursorrules, AGENTS.md, CLAUDE.md) empower our team after handover?'
        : 'Bagaimana aturan agen AI (.cursorrules, AGENTS.md, CLAUDE.md) memberdayakan tim developer kami setelah serah terima?',
      a: isEn
        ? 'Our Project OS packages embed battle-tested directive files (.cursorrules, AGENTS.md, CLAUDE.md, SYMBOL_MANIFEST.json) tailored to your exact stack (Laravel 13, Filament v5, PostgreSQL ULID, Keyset Cursor). When your developers open your project in Cursor, Claude Code, Windsurf, or Antigravity IDE, the AI assistant immediately respects your architecture, coding conventions, and security rules without continuous re-prompting.'
        : 'Deliverable Project OS menyertakan berkas direktif siap pakai (.cursorrules, AGENTS.md, CLAUDE.md, SYMBOL_MANIFEST.json) yang disesuaikan persis dengan stack teknologi proyek Anda (Laravel 13, Filament v5, PostgreSQL ULID, Keyset Cursor O(1)). Ketika developer Anda membuka repositori di Cursor, Claude Code, Windsurf, atau Antigravity IDE, asisten AI otomatis mematuhi aturan arsitektur, konvensi penamaan, dan standar keamanan tanpa perlu di-prompting manual berulang kali.'
    },
    {
      category: 'SLA_GARANSI_HUKUM',
      q: isEn ? 'How does the Escrow Milestone / Down Payment (DP) work for Full MVP Development?' : 'Bagaimana skema pembayaran Escrow Bertahap / Uang Muka (DP) untuk Full MVP?',
      a: isEn
        ? 'We enforce Scope Locking and Digital Legal Contracts (SPK SHA-256). Clients can settle via 3-Stage Escrow Milestones (30% DP Kickoff [Phase 1–2], 50% Live Staging Verification [Phase 3–5], and 20% Final BAST Handover [Phase 6–7]) or 50%/50% Termin via Midtrans Snap / Official Bank Transfer.'
        : 'Kami menerapkan transparansi penuh dengan Kontrak Hukum Digital (SPK SHA-256) dan Scope Locking. Klien dilindungi skema Escrow 3-Termin Bertahap (DP 30% Kickoff [Fase 1–2], Termin 50% Verifikasi Live Staging [Fase 3–5], dan Pelunasan 20% Serah Terima BAST [Fase 6–7]) atau skema 50%/50% via Midtrans Snap / Transfer Bank resmi.'
    },
    {
      category: 'SLA_GARANSI_HUKUM',
      q: isEn ? 'Who owns the intellectual property and source code in Neriah Pro Studio contracts?' : 'Siapakah pemilik hak cipta dan source code aplikasi pada pengerjaan Neriah Pro Studio?',
      a: isEn
        ? '100% Client Ownership. Upon final BAST settlement, we transfer full source code, database dumps, server credentials, and documentation without vendor lock-in or recurring proprietary licensing.'
        : '100% Hak Milik Klien. Setelah pelunasan BAST, seluruh kode sumber (Laravel 13, Filament v5, Livewire 4, React Islands), skema database, akun server VPS, dan dokumentasi PRD diserahkan penuh kepada Anda tanpa biaya lisensi berulang atau vendor lock-in.'
    },
    {
      category: 'UI_UX_12_SEKTOR',
      q: isEn ? 'Why does Project OS classify applications into 12 Sectoral Industrial UI/UX Archetypes instead of standard CRUD tables?' : 'Mengapa Project OS memetakan aplikasi ke 12 Sectoral Industrial UI/UX Archetypes dan melarang tabel CRUD generik?',
      a: isEn
        ? 'Real-world operational software fails when every industry is forced into the same generic CRUD table. A retail cashier needs a mouse-free 100vh Split POS Terminal (F1/F2/F8 shortcuts), a doctor needs a 3-Pane Clinical Cockpit (Queue + Allergy Banner | SOAP | FEFO Rx) with zero stacked modals, and an accountant needs a balanced Spreadsheet Ledger locking [Post Journal] until Debit == Credit. Project OS compiles exact industry ergonomics, keyboard shortcuts, and Indonesian Realistic Domain Seeders automatically.'
        : 'Aplikasi bisnis nyata gagal di lapangan jika seluruh industri dipaksa memakai tabel CRUD generik yang sama. Kasir toko membutuhkan Split POS Terminal 100vh bebas mouse (pintasan F1/F2/F8), dokter membutuhkan Clinical 3-Pane Cockpit (Antrean + Banner Alergi | SOAP | Resep FEFO) tanpa tumpukan modal, dan akuntan membutuhkan Spreadsheet Ledger yang mengunci tombol [Post Journal] hingga Debit == Kredit. Project OS mengompilasi ergonomi spesifik industri beserta Realistic Domain Seeder Indonesia secara otomatis.'
    },
    {
      category: 'AI_MCP_SECURITY',
      q: isEn ? 'How do the Phased Prompt Sequencer (Phase 1–7), Brownfield Importer, and 1-Command CLI Bootstrapper work?' : 'Bagaimana cara kerja Phased Prompt Sequencer (Fase 1–7), Brownfield SQL Importer, dan 1-Command CLI Bootstrapper?',
      a: isEn
        ? 'Instead of dumping a massive 50-page PRD into an AI IDE at once (which triggers context-overflow and broken code), Project OS slices execution into 7 deterministic phases (.agents/prompts/01-foundation.md through 07-security-redteam.md), enforces TDD 100% green before UI, includes ./verify-blueprint.sh to catch UI slop, audits legacy SQL schemas for Zero-Data-Loss Shadow ULID migration, and initializes your entire repository in 3 seconds via curl -sL https://neriahpro.com/init/{ULID} | bash.'
        : 'Daripada menyuapi AI coding agent dengan satu dokumen raksasa sekaligus (yang menyebabkan context overflow dan halusinasi), Project OS memecah eksekusi ke dalam 7 fase deterministik (.agents/prompts/01-foundation.md s/d 07-security-redteam.md), mewajibkan 100% test hijau sebelum menyentuh UI, menyertakan ./verify-blueprint.sh pencegah UI slop, mengaudit database SQL warisan untuk migrasi Shadow ULID tanpa kehilangan data, serta menginisialisasi repositori dalam 3 detik cukup dengan satu perintah terminal: curl -sL https://neriahpro.com/init/{ULID} | bash.'
    },
    {
      category: 'AI_MCP_SECURITY',
      q: isEn ? 'What happens at Day-30 or Day-90 when I want to add new features (v1.0 -> v1.1) without breaking my live production app?' : 'Apa yang terjadi pada Hari ke-30 atau ke-90 saat saya ingin menambah fitur baru (v1.0 -> v1.1) tanpa merusak kode produksi yang sudah jalan?',
      a: isEn
        ? 'Project OS includes an Iterative Change Request & Surgical Delta Generator. Instead of rewriting your application, it computes the exact delta between v1.0 and v1.1 and outputs ONLY additive strict-ULID migrations (zero DROP TABLE), graph.json diffs, Gemini tool deltas, and Zero-Regression tests. Plus, via the Live MCP Server Bridge (neriahpro-mcp .mcp.json), Claude Code, Cursor, and Antigravity IDE read your blueprint directly and mark phases Verified ✅ in real time.'
        : 'Project OS dilengkapi Iterative Change Request & Surgical Delta Generator (v1.0 -> v1.1). Alih-alih menulis ulang aplikasi dan berisiko merusak tabel yang sudah live, sistem menghitung selisih presisi dan HANYA menghasilkan migrasi ULID aditif (tanpa DROP TABLE), pembaruan graph.json, serta tes regresi otomatis. Ditambah lagi, melalui Live MCP Server Bridge (neriahpro-mcp .mcp.json), Claude Code, Cursor, dan Antigravity IDE terhubung langsung dua arah tanpa copy-paste manual.'
    }
  ];

  const SECTORAL_ARCHETYPES_SHOWCASE = [
    {
      num: '01',
      code: 'SECTOR_01_COMMERCE_OMNICHANNEL',
      name: isEn ? 'Commerce, POS & Omnichannel' : 'Perdagangan, Ritel POS & Distribusi',
      primary: 'POS_SPLIT_TERMINAL',
      secondary: 'DENSE_SKU_MATRIX',
      seeder: 'RealisticCommercePosSeeder',
      shortcuts: ['F1 Focus Barcode', 'F2 Search SKU', 'F4 Hold Cart', 'F8 Pay QRIS/Cash', 'Enter +1 Qty'],
      panes: [
        { span: '8-COL LEFT', title: isEn ? '100vh Zero-Scroll SKU Grid & 2D Size×Color Matrix' : 'Grid SKU Cepat 100vh Tanpa Scroll & Matriks 2D Ukuran×Warna' },
        { span: '4-COL RIGHT', title: isEn ? 'Sticky Cart, Tiered Discount & Touch Virtual Numpad' : 'Keranjang Belanja Sticky, Diskon Bertingkat & Virtual Numpad' },
      ],
      ban: isEn ? 'FORBIDDEN: Multi-page checkout reloads or mouse-dependent dropdowns.' : 'DILARANG: Checkout pindah halaman lambat atau form dropdown yang bergantung pada mouse.',
    },
    {
      num: '02',
      code: 'SECTOR_02_HOSPITALITY_SPACE',
      name: isEn ? 'Hospitality, Property & Space Booking' : 'Perhotelan, Properti & Reservasi Aset',
      primary: 'HORIZONTAL_RESOURCE_GANTT',
      secondary: 'TIME_SLOT_HEATMAP',
      seeder: 'RealisticHospitalityBookingSeeder',
      shortcuts: ['Click Empty Slot → Slide-Over', 'G Toggle Gantt/Heatmap', 'M Toggle Maintenance'],
      panes: [
        { span: 'STICKY Y-AXIS', title: isEn ? 'Rooms / Courts / Fleet Units + Clean/Dirty Badge' : 'Daftar Kamar / Lapangan / Armada + Badge Clean/Dirty/Maintenance' },
        { span: '06:00–24:00 X-AXIS', title: isEn ? 'PostgreSQL GiST Exclusion Blocks + 0-Reload Slide-Over' : 'Blok Waktu GiST Anti-Bentrok + Slide-Over Booking Tanpa Reload' },
      ],
      ban: isEn ? 'FORBIDDEN: Plain text date tables that hide overlapping slot collisions.' : 'DILARANG: Tabel daftar tanggal teks biasa yang menyembunyikan bentrok jadwal.',
    },
    {
      num: '03',
      code: 'SECTOR_03_HEALTHCARE_CLINICAL',
      name: isEn ? 'Healthcare, Clinic EMR & Pharmacy' : 'Layanan Kesehatan, EMR Klinik & Apotek',
      primary: 'CLINICAL_3_PANE_COCKPIT',
      secondary: 'TIMELINE_SOAP_EMR',
      seeder: 'RealisticClinicalEmrSeeder',
      shortcuts: ['Alt+1 Queue', 'Alt+2 SOAP Input', 'Alt+3 FEFO Rx & ICD-10', 'Ctrl+Enter Sign EMR'],
      panes: [
        { span: '2-COL LEFT', title: isEn ? 'Poly Queue + Sticky Rose-950 Drug Allergy Banner' : 'Antrean Poli + Banner Merah Peringatan Alergi Obat Akut' },
        { span: '6-COL CENTER', title: isEn ? 'Chronological EMR & Structured S-O-A-P Workspace' : 'Riwayat Rekam Medis Kronologis & Form S-O-A-P Terstruktur' },
        { span: '4-COL RIGHT', title: isEn ? 'ICD-10 Search & FEFO Batch Expiry E-Prescription' : 'Pencarian ICD-10 & E-Resep Stok Apotek Batch/Expiry FEFO' },
      ],
      ban: isEn ? 'FORBIDDEN: Stacked modals hiding patient allergies or silent edits on signed EMRs.' : 'DILARANG: Modal bertumpuk yang menutupi alergi pasien atau edit diam-diam pada EMR terkunci.',
    },
    {
      num: '04',
      code: 'SECTOR_04_FINTECH_LEDGER',
      name: isEn ? 'Fintech, Accounting & Double-Entry Ledger' : 'Keuangan, Akuntansi & Double-Entry Ledger',
      primary: 'SPREADSHEET_LEDGER_GRID',
      secondary: 'SIDE_BY_SIDE_RECONCILIATION',
      seeder: 'RealisticFintechLedgerSeeder',
      shortcuts: ['Tab Next Cell', 'Ctrl+Shift+B Auto-Balance', 'Ctrl+Enter Post Balanced Journal'],
      panes: [
        { span: 'COMPACT LEDGER', title: isEn ? 'py-1.5 Tabular-Nums DECIMAL(19,4) CoA Journal Grid' : 'Grid Jurnal CoA Padat py-1.5 Tabular-Nums DECIMAL(19,4)' },
        { span: 'STICKY FOOTER', title: isEn ? 'Real-Time SUM(Debit)==SUM(Credit) Gatekeeper (Delta=0)' : 'Kalkulator Live SUM(Debit)==SUM(Kredit); Kunci [Post] jika Selisih != 0' },
      ],
      ban: isEn ? 'FORBIDDEN: Floating-point math or allowing unbalanced journal submissions.' : 'DILARANG: Tipe FLOAT/DOUBLE atau mengizinkan tombol Post aktif saat jurnal belum seimbang.',
    },
    {
      num: '05',
      code: 'SECTOR_05_INDUSTRIAL_MRP',
      name: isEn ? 'Manufacturing, MRP & Shop Floor' : 'Manufaktur, Pabrik MRP & Konstruksi',
      primary: 'MULTI_LEVEL_BOM_TREE',
      secondary: 'SHOP_FLOOR_KANBAN',
      seeder: 'RealisticManufacturingMrpSeeder',
      shortcuts: ['Space Expand BoM Node', 'F9 Record Scrap/Yield'],
      panes: [
        { span: 'BOM TREE', title: isEn ? 'Collapsible Raw → Semi → Finished Good HPP Roll-Up' : 'Pohon BoM Bertingkat Bahan Baku → WIP → Barang Jadi + HPP Roll-Up' },
        { span: 'GLOVE TOUCH UI', title: isEn ? 'h-14 High-Contrast Shop Floor Start/Pause/Scrap Buttons' : 'Mode Operator Pabrik Tombol Besar h-14 Ramah Sarung Tangan' },
      ],
      ban: isEn ? 'FORBIDDEN: Tiny 10px links unusable by factory floor operators.' : 'DILARANG: Tombol/teks kecil yang mustahil ditekan operator lantai produksi.',
    },
    {
      num: '06',
      code: 'SECTOR_06_LOGISTICS_FLEET',
      name: isEn ? 'Logistics, Supply Chain & Fleet PoD' : 'Logistik, Ekspedisi & Manajemen Armada',
      primary: 'SPLIT_MAP_DISPATCHER',
      secondary: 'STEPPER_MANIFEST_TRACKER',
      seeder: 'RealisticLogisticsFleetSeeder',
      shortcuts: ['Ctrl+M Assign Waybill', 'Ctrl+P Print Manifest QR'],
      panes: [
        { span: '50% DISPATCH', title: isEn ? 'Unassigned Waybills + Truck Volume/Weight Load Bar' : 'Daftar Resi + Progress Bar Kapasitas Kubikasi/Tonase Truk' },
        { span: '50% ROUTE / POD', title: isEn ? 'Mobile PWA Sticky Bottom Bar + Canvas Signature PoD' : 'PWA Kurir 1-Jempol + Bukti Foto Kamera & Tanda Tangan Kanvas PoD' },
      ],
      ban: isEn ? 'FORBIDDEN: Desktop-only tables forced onto field couriers.' : 'DILARANG: Memaksa kurir lapangan memakai tabel desktop lebar di layar HP.',
    },
    {
      num: '07',
      code: 'SECTOR_07_EDTECH_LMS',
      name: isEn ? 'EdTech, Campus SIAKAD & LMS' : 'Pendidikan, SIAKAD Kampus & Pelatihan',
      primary: 'COHORT_GRADEBOOK_MATRIX',
      secondary: 'DISTRACTION_FREE_PLAYER',
      seeder: 'RealisticEdtechCohortSeeder',
      shortcuts: ['Arrow Keys Navigate Cells', 'H/I/S/A Instant Attendance'],
      panes: [
        { span: '2D GRADEBOOK', title: isEn ? 'Student × Session 1..16 Inline Matrix + 1-Click H/I/S/A' : 'Matriks 2D Siswa × Pertemuan 1..16 + Presensi 1-Klik H/I/S/A' },
        { span: 'EXAM PLAYER', title: isEn ? 'Collapsible Curriculum Tree + Server-Synced Exam Timer' : 'Player Materi Bebas Distraksi + Timer Ujian Anti-Reset Refresh' },
      ],
      ban: isEn ? 'FORBIDDEN: Opening 30 separate modals just to grade 30 students.' : 'DILARANG: Membuka 30 modal terpisah hanya untuk menginput nilai 30 siswa.',
    },
    {
      num: '08',
      code: 'SECTOR_08_PROFESSIONAL_PSA',
      name: isEn ? 'Agency, Consulting & Legal PSA' : 'Jasa Profesional, Agensi & Konsultan Hukum',
      primary: 'MILESTONE_KANBAN_BOARD',
      secondary: 'PERSISTENT_FLOATING_TIMER',
      seeder: 'RealisticProfessionalPsaSeeder',
      shortcuts: ['Alt+T Toggle Billable Timer', 'Alt+I Convert Timesheet → Invoice'],
      panes: [
        { span: 'PERSISTENT BAR', title: isEn ? 'Floating Billable Stopwatch Surviving Page Navigation' : 'Stopwatch Jam Kerja Melayang yang Tetap Hidup Saat Pindah Halaman' },
        { span: 'CLIENT PORTAL', title: isEn ? 'Clean hideNav Deliverable Sign-Off & Scope Hash Seal' : 'Portal Persetujuan Klien Bersih (hideNav) + Segel Hash SHA-256' },
      ],
      ban: isEn ? 'FORBIDDEN: Exposing internal staff cost rates on client-facing views.' : 'DILARANG: Menampilkan tarif modal internal staf pada layar portal klien.',
    },
    {
      num: '09',
      code: 'SECTOR_09_FNB_CULINARY',
      name: isEn ? 'F&B, Cloud Kitchen KDS & Table Map' : 'Kuliner, Restoran, Cloud Kitchen & KDS',
      primary: 'KDS_TICKET_BOARD',
      secondary: 'FLOOR_TABLE_MAP',
      seeder: 'RealisticFnbKitchenKdsSeeder',
      shortcuts: ['Space Bump Ticket', '1–9 Select Table Zone'],
      panes: [
        { span: 'DARK KDS BOARD', title: isEn ? 'Forced Dark SLA Tickets (<5m Emerald, >10m Pulsing Rose)' : 'Layar Dapur Dark Mode dengan Indikator SLA (<5m Hijau, >10m Merah)' },
        { span: 'MODIFIER GRID', title: isEn ? '1-Tap Sugar/Ice/Topping Button Grid + Split/Merge Bill' : 'Grid Tombol Cepat Level Gula/Es/Topping + Pisah/Gabung Tagihan Meja' },
      ],
      ban: isEn ? 'FORBIDDEN: Glaring white KDS screens in hot kitchens or slow modifier dropdowns.' : 'DILARANG: Layar KDS putih menyilaukan di dapur atau dropdown topping yang lambat.',
    },
    {
      num: '10',
      code: 'SECTOR_10_MEDIA_TICKETING',
      name: isEn ? 'Event Ticketing, Venue & Creator DAM' : 'Media, Tiket Event, Konser & Ekonomi Kreator',
      primary: 'INTERACTIVE_SEAT_MAP',
      secondary: 'DAM_MASONRY_GRID',
      seeder: 'RealisticTicketingEventSeeder',
      shortcuts: ['Esc Clear Seat Selection', 'S Focus Gate QR Scanner'],
      panes: [
        { span: 'SEAT MAP + TTL', title: isEn ? 'Interactive VIP/CAT Grid + Sticky 10:00 Hold Countdown' : 'Peta Kursi Interaktif VIP/Festival + Banner Hitung Mundur Kunci 10:00' },
        { span: 'CREATOR ROYALTY', title: isEn ? 'DAM Asset Grid + Transparent Gross/Fee/Net Split Ledger' : 'Galeri Aset Digital + Buku Besar Bagi Hasil Royalti Kreator Transparan' },
      ],
      ban: isEn ? 'FORBIDDEN: Releasing held seats without visible countdown warning.' : 'DILARANG: Melepas kursi pesanan tanpa banner hitung mundur yang jelas.',
    },
    {
      num: '11',
      code: 'SECTOR_11_HRIS_INTERNAL_OPS',
      name: isEn ? 'HRIS, Shift Roster & Payroll PPh21' : 'HRIS, Roster Shift, Absensi & Payroll PPh21',
      primary: 'SHIFT_ROSTER_MATRIX',
      secondary: 'PAYSLIP_SIMULATOR_VIEW',
      seeder: 'RealisticHrisPayrollSeeder',
      shortcuts: ['C Copy Last Week Roster', 'P Run Payroll Simulation'],
      panes: [
        { span: 'ROSTER MATRIX', title: isEn ? 'Employee × Date Grid + Night→Morning Red Clash Alert' : 'Matriks Karyawan × Tanggal + Peringatan Merah Bentrok Malam→Pagi' },
        { span: '3-COL PAYROLL', title: isEn ? 'Gross | PPh21 TER & BPJS Deductions | Net THP + MoM Delta' : '3 Kolom Simulasi Gaji: Bruto | Potongan PPh21 TER & BPJS | Net THP' },
      ],
      ban: isEn ? 'FORBIDDEN: Finalizing payroll runs without MoM anomaly review.' : 'DILARANG: Mengunci penggajian massal tanpa layar pratinjau selisih bulan lalu.',
    },
    {
      num: '12',
      code: 'SECTOR_12_GOVTECH_NONPROFIT',
      name: isEn ? 'GovTech, Non-Profit, Zakat & Grants' : 'Layanan Publik, Yayasan, Donasi & Hibah (ISAK 35)',
      primary: 'TRANSPARENCY_FUND_TRACKER',
      secondary: 'DONOR_IMPACT_PORTAL',
      seeder: 'RealisticNonprofitFundSeeder',
      shortcuts: ['V Verify Geo Evidence', 'H Copy SHA-256 Receipt Hash'],
      panes: [
        { span: 'ISAK 35 SPLIT', title: isEn ? 'Restricted vs Unrestricted Fund Balance & Disbursement' : 'Pemisahan Ketat Saldo Dana Terikat vs Tidak Terikat (Standar ISAK 35)' },
        { span: 'PUBLIC TRUST', title: isEn ? 'Field Geo/Photo Timeline + SHA-256 Tamper-Evident Badge' : 'Timeline Bukti Foto/Lokasi Penyaluran + Lencana Hash Audit SHA-256' },
      ],
      ban: isEn ? 'FORBIDDEN: Co-mingling restricted program funds with operational expenses.' : 'DILARANG: Mencampur saldo dana donasi terikat dengan kas operasional umum.',
    },
  ];

  // v8.0: 36 Concrete Visual Frontend UI/UX Blueprints (12 Functional Sectors x 3 Visual Variants)
  const SECTOR_VISUAL_VARIANTS_36 = {
    SECTOR_01_COMMERCE_OMNICHANNEL: [
      { code: 'UI_01A_POS_SPLIT_TERMINAL', badge: 'OPSI A • SPEED CASHIER', shell: 'FULLSCREEN_OPERATIONAL_KIOSK', title: isEn ? 'Split Cashier & <30ms Barcode Terminal' : 'Terminal Kasir Split-Screen & Scan Barcode <30ms', panes: ['8-COL: Grid SKU Cepat + F1 Barcode + Matriks 2D Ukuran×Warna', '4-COL: Keranjang Sticky + Diskon Bertingkat + Virtual Numpad'] },
      { code: 'UI_01B_OMNICHANNEL_TOUCH_KIOSK', badge: 'OPSI B • TOUCH KIOSK', shell: 'FULLSCREEN_OPERATIONAL_KIOSK', title: isEn ? 'Visual Touch Card Grid & Self-Checkout Kiosk' : 'Katalog Visual Touch Grid & Self-Checkout Kiosk', panes: ['2-COL: Kategori Cepat', '6-COL: Kartu Produk Kontras Tinggi', '4-COL: QRIS SVG + E-Struk WA'] },
      { code: 'UI_01C_B2B_WHOLESALE_MATRIX', badge: 'OPSI C • B2B GROSIR', shell: 'SIDEBAR_ERP_DENSE', title: isEn ? 'B2B Wholesale Matrix & Multi-Warehouse Credit' : 'Matriks Grosir B2B, Multi-Gudang & Piutang Tempo', panes: ['9-COL: Grid Order Karton Padat + Plafon Kredit', '3-COL: Surat Jalan + Tempo TOP 30/60h'] },
    ],
    SECTOR_02_HOSPITALITY_SPACE: [
      { code: 'UI_02A_HORIZONTAL_RESOURCE_GANTT', badge: 'OPSI A • FRONTDESK GANTT', shell: 'DUAL_PANE_SPLIT_WORKBENCH', title: isEn ? 'Horizontal Resource Gantt & 0-Reload Slide-Over' : 'Kalender Gantt Aset Horizontal & Slide-Over Booking', panes: ['3-COL: Unit Kamar/Lapangan Sticky', '6-COL: Timeline 06:00–24:00 GiST', '3-COL: Drawer Walk-In QRIS'] },
      { code: 'UI_02B_VISUAL_COURT_FLOOR_MAP', badge: 'OPSI B • FLOOR DENAH 2D', shell: 'FULLSCREEN_OPERATIONAL_KIOSK', title: isEn ? '2D Interactive Venue/Room Floor Map & Live Timer' : 'Peta Denah Visual Lapangan / Kamar & Timer Durasi', panes: ['8-COL: Denah 2D Unit + Sisa Durasi Live + Relay IoT', '4-COL: Housekeeping Clean/Dirty + Add-On'] },
      { code: 'UI_02C_PUBLIC_BOOKING_CALENDAR', badge: 'OPSI C • GUEST PORTAL PWA', shell: 'MOBILE_FIRST_THUMB_PWA', title: isEn ? 'Mobile-First Self-Booking & Membership Pass' : 'Portal Booking Pelanggan Mandiri & Kuota Member', panes: ['7-COL: Pilih Tanggal/Jam + Kuota Member', '5-COL: Kunci 15m + Tiket QR Gate'] },
    ],
    SECTOR_03_HEALTHCARE_CLINICAL: [
      { code: 'UI_03A_CLINICAL_3_PANE_COCKPIT', badge: 'OPSI A • ZERO-MODAL EMR', shell: 'DUAL_PANE_SPLIT_WORKBENCH', title: isEn ? '3-Pane Doctor S-O-A-P & FEFO Rx Cockpit' : 'Kokpit Dokter 3-Panel Bebas Modal (S-O-A-P + E-Resep)', panes: ['2-COL: Antrean + Banner Alergi Merah', '6-COL: S-O-A-P + ICD-10', '4-COL: E-Resep Stok FEFO'] },
      { code: 'UI_03B_OUTPATIENT_POLY_QUEUE_TV', badge: 'OPSI B • ADMISI & TRIASE', shell: 'SIDEBAR_ERP_DENSE', title: isEn ? 'Fast NIK Registration, Nurse Triage & TV Queue' : 'Admisi Pendaftaran Cepat, Triase Perawat & TV Poli', panes: ['5-COL: Lookup NIK/BPJS + Struk Antrean', '7-COL: Tanda Vital + WebSocket TV Panggil'] },
      { code: 'UI_03C_PHARMACY_DISPENSING_BENCH', badge: 'OPSI C • FARMASI & KASIR', shell: 'FULLSCREEN_OPERATIONAL_KIOSK', title: isEn ? 'Pharmacy FEFO Dispensing Bench & Clinic Billing' : 'Meja Racik Apotek FEFO, Etiket Barcode & Kasir Klinik', panes: ['4-COL: Antrean E-Resep Masuk', '5-COL: Alokasi Batch Expiry FEFO', '3-COL: Tagihan Gabungan'] },
    ],
    SECTOR_04_FINTECH_LEDGER: [
      { code: 'UI_04A_SPREADSHEET_LEDGER_GRID', badge: 'OPSI A • DOUBLE-ENTRY GRID', shell: 'SIDEBAR_ERP_DENSE', title: isEn ? 'Compact DECIMAL(19,4) Journal & Balance Lock' : 'Grid Jurnal Buku Besar Padat & Pengunci Balance', panes: ['8-COL: Baris CoA Tabular-Nums + Sticky Dr==Cr', '4-COL: Kunci Periode Fiskal + Jurnal Balik'] },
      { code: 'UI_04B_DUAL_BANK_RECONCILIATION', badge: 'OPSI B • REKONSILIASI 50/50', shell: 'DUAL_PANE_SPLIT_WORKBENCH', title: isEn ? 'Side-by-Side Bank Feed & System Ledger Match' : 'Meja Rekonsiliasi Mutasi Bank Bersebelahan (50/50)', panes: ['6-COL: Mutasi Rekening / Webhook', '6-COL: Jurnal Sistem + Auto-Match 1-Klik'] },
      { code: 'UI_04C_CFO_TREASURY_APPROVAL_DECK', badge: 'OPSI C • CFO TREASURY', shell: 'TOPBAR_EXECUTIVE_DECK', title: isEn ? 'CFO Multi-Bank Cashflow & 4-Eyes Payout Deck' : 'Kokpit Kas Eksekutif CFO & Approval Pencairan 4-Mata', panes: ['7-COL: Likuiditas Multi-Bank + Umur AR/AP', '5-COL: Antrean Maker-Checker + Segel Hash'] },
    ],
    SECTOR_05_INDUSTRIAL_MRP: [
      { code: 'UI_05A_MULTI_LEVEL_BOM_TREE', badge: 'OPSI A • POHON BOM & HPP', shell: 'SIDEBAR_ERP_DENSE', title: isEn ? 'Multi-Level BoM Tree & Live Unit HPP Roll-Up' : 'Pohon BoM Bertingkat & Kalkulator HPP Produksi', panes: ['7-COL: Pohon BoM Lipat + Toleransi Scrap %', '5-COL: Cek Stok Bahan Baku + Rilis WO'] },
      { code: 'UI_05B_GLOVE_TOUCH_SHOP_FLOOR', badge: 'OPSI B • TABLET PABRIK h-14', shell: 'FULLSCREEN_OPERATIONAL_KIOSK', title: isEn ? 'Glove-Friendly h-14 Shop Floor Operator Kiosk' : 'Layar Tablet Lantai Pabrik Kontras Ekstrem (Tombol h-14)', panes: ['5-COL: Target Batch vs Aktual + Scan Lot', '7-COL: Tombol Raksasa [START / SCRAP / FINISH]'] },
      { code: 'UI_05C_WORK_CENTER_CAPACITY_BOARD', badge: 'OPSI C • PPIC & QC GATE', shell: 'DUAL_PANE_SPLIT_WORKBENCH', title: isEn ? 'PPIC Machine Load Kanban & QC Inspection Gate' : 'Papan Kapasitas Mesin PPIC & Gerbang Inspeksi QC', panes: ['8-COL: Beban Jam Mesin + Kanban Lini', '4-COL: Checklist Lulus/Karantina QC'] },
    ],
    SECTOR_06_LOGISTICS_FLEET: [
      { code: 'UI_06A_SPLIT_MAP_DISPATCHER', badge: 'OPSI A • DISPATCHER ARMADA', shell: 'DUAL_PANE_SPLIT_WORKBENCH', title: isEn ? '50/50 Truck Tonnage/CBM Load & Live Manifest' : 'Kokpit Dispatcher Muatan Truk & Pelacakan Manifes', panes: ['6-COL: Resi + Bar Tonase/Kubikasi Truk', '6-COL: Milestone Pengiriman + Rekonsiliasi COD'] },
      { code: 'UI_06B_COURIER_THUMB_POD_PWA', badge: 'OPSI B • PWA KURIR 1-JEMPOL', shell: 'MOBILE_FIRST_THUMB_PWA', title: isEn ? '1-Thumb Courier Stop Cards + Photo/Signature PoD' : 'PWA Kurir Lapangan 1-Jempol & Bukti Foto/TTD PoD', panes: ['5-COL: Kartu Rute Antar + Bar Jempol Bawah', '7-COL: Verifikasi Foto Geo + TTD Kanvas'] },
      { code: 'UI_06C_WAREHOUSE_CROSSDOCK_SCANNER', badge: 'OPSI C • SORTIR GUDANG HUB', shell: 'FULLSCREEN_OPERATIONAL_KIOSK', title: isEn ? 'High-Speed Hub Cross-Docking & Misroute Guard' : 'Terminal Sortir Gudang Hub Inbound/Outbound Cepat', panes: ['7-COL: Scan Cepat <30ms + Peringatan Salah Rute', '5-COL: Segel Karung Master + Label Thermal'] },
    ],
    SECTOR_07_EDTECH_LMS: [
      { code: 'UI_07A_COHORT_GRADEBOOK_MATRIX', badge: 'OPSI A • MATRIKS NILAI 2D', shell: 'SIDEBAR_ERP_DENSE', title: isEn ? '2D Arrow-Key Gradebook & 1-Click H/I/S/A Matrix' : 'Matriks Buku Nilai 2D & Presensi 1-Klik (H/I/S/A)', panes: ['9-COL: Grid Siswa × Sesi 1..16 Bebas Modal', '3-COL: Bobot Kompetensi + Cetak Transkrip'] },
      { code: 'UI_07B_DISTRACTION_FREE_EXAM_PLAYER', badge: 'OPSI B • PLAYER LMS & CBT', shell: 'FULLSCREEN_OPERATIONAL_KIOSK', title: isEn ? 'Distraction-Free Course Player & Server-Timed CBT' : 'Ruang Belajar Bebas Distraksi & Ujian Timer Server', panes: ['3-COL: Pohon Silabus', '6-COL: Materi/Soal Fokus', '3-COL: Peta Nomor Soal + Timer Server'] },
      { code: 'UI_07C_CAMPUS_SIAKAD_FINANCE_PORTAL', badge: 'OPSI C • PORTAL SIAKAD/SPP', shell: 'TOPBAR_EXECUTIVE_DECK', title: isEn ? 'Student/Parent SIAKAD, Timetable & Tuition VA' : 'Portal SIAKAD Wali Murid/Mahasiswa, KRS & Tagihan SPP', panes: ['7-COL: Jadwal Anti-Bentrok + Rapor/IPK', '5-COL: Cicilan SPP/UKT + Beasiswa + QRIS'] },
    ],
    SECTOR_08_PROFESSIONAL_PSA: [
      { code: 'UI_08A_MILESTONE_KANBAN_TIMER', badge: 'OPSI A • KANBAN + STOPWATCH', shell: 'SIDEBAR_ERP_DENSE', title: isEn ? 'Deliverable Kanban + Persistent Billable Stopwatch' : 'Papan Milestone Proyek + Stopwatch Kerja Melayang', panes: ['8-COL: Bar Timer Anti-Putus + Milestone Kanban', '4-COL: Konversi Timesheet -> Invoice Termin'] },
      { code: 'UI_08B_CLIENT_PORTAL_SCOPE_SEAL', badge: 'OPSI B • PORTAL WHITE-LABEL', shell: 'TOPBAR_EXECUTIVE_DECK', title: isEn ? 'White-Label Client Approval, UAT & Digital BAST' : 'Portal Klien White-Label, UAT & BAST Digital', panes: ['7-COL: Progres 7 Fase + Checklist UAT Klien', '5-COL: Segel SPK SHA-256 + TTD BAST Digital'] },
      { code: 'UI_08C_RETAINER_RESOURCE_UTILIZATION', badge: 'OPSI C • FIRMA HUKUM / KAP', shell: 'SIDEBAR_ERP_DENSE', title: isEn ? 'Partner Utilization, Retainer Burn & Matter Vault' : 'Matriks Beban Konsultan, Kuota Retainer & Brankas Dokumen', panes: ['6-COL: Sisa Jam Retainer + Margin Proyek', '6-COL: Brankas Dokumen Signed URL 5 Menit'] },
    ],
    SECTOR_09_FNB_CULINARY: [
      { code: 'UI_09A_DARK_KDS_AND_MODIFIER_POS', badge: 'OPSI A • POS + DARK KDS', shell: 'FULLSCREEN_OPERATIONAL_KIOSK', title: isEn ? '1-Tap Sugar/Ice Modifier POS & Forced-Dark KDS' : 'Kasir Modifier 1-Sentuh & Layar Dapur Dark KDS', panes: ['6-COL: Grid Gula/Es/Topping Tanpa Dropdown', '6-COL: Layar Dapur Dark Mode SLA <5m/>10m'] },
      { code: 'UI_09B_INTERACTIVE_DINEIN_TABLE_MAP', badge: 'OPSI B • PETA MEJA & SPLIT BILL', shell: 'DUAL_PANE_SPLIT_WORKBENCH', title: isEn ? 'Dine-In Floor Table Map & Split/Merge Bill' : 'Peta Meja Restoran (Indoor/VIP) & Pisah/Gabung Bill', panes: ['7-COL: Denah Meja + Pindah/Gabung Meja', '5-COL: Pisah Tagihan per Item + PB1 & Service'] },
      { code: 'UI_09C_QR_TABLE_SELF_ORDER_MENU', badge: 'OPSI C • SCAN QR MEJA', shell: 'MOBILE_FIRST_THUMB_PWA', title: isEn ? 'Zero-App Signed Table QR Menu & Multi-Station' : 'Menu Pesan Mandiri QR Meja & Multi-Dapur/Bar', panes: ['6-COL: Menu HP Tamu Ber-Token + Bayar QRIS', '6-COL: Routing Otomatis Bar vs Dapur Panas'] },
    ],
    SECTOR_10_MEDIA_TICKETING: [
      { code: 'UI_10A_INTERACTIVE_SEAT_MAP_TTL', badge: 'OPSI A • PETA KURSI + TTL 10M', shell: 'DUAL_PANE_SPLIT_WORKBENCH', title: isEn ? 'Interactive Venue Seat Map + 10:00 Hold Countdown' : 'Peta Kursi Konser Interaktif & Hitung Mundur 10:00', panes: ['8-COL: Grid Kursi VIP/CAT + Kunci Kursi Atomic', '4-COL: Antrean Fair-Play + Tiket QR Dinamis'] },
      { code: 'UI_10B_GATE_WRISTBAND_SCANNER', badge: 'OPSI B • SCANNER GATE <150MS', shell: 'FULLSCREEN_OPERATIONAL_KIOSK', title: isEn ? '<150ms Gate QR/RFID Scanner & Anti-Passback' : 'Layar Scanner Gate Masuk Event & Anti-Tiket Ganda', panes: ['7-COL: Layar HIJAU SAH vs MERAH DUPLIKAT', '5-COL: Kapasitas Zona Live + Cache Offline'] },
      { code: 'UI_10C_CREATOR_DAM_ROYALTY_DESK', badge: 'OPSI C • GALERI DAM & ROYALTI', shell: 'SIDEBAR_ERP_DENSE', title: isEn ? 'Digital Asset Licensing & 70:30 Royalty Ledger' : 'Galeri Lisensi Aset Digital (DAM) & Bagi Hasil Royalti', panes: ['7-COL: Preview Watermark + Signed URL 5m', '5-COL: Kalkulator Bagi Hasil Kreator Otomatis'] },
    ],
    SECTOR_11_HRIS_INTERNAL_OPS: [
      { code: 'UI_11A_SHIFT_ROSTER_CLASH_MATRIX', badge: 'OPSI A • ROSTER & PAYROLL', shell: 'SIDEBAR_ERP_DENSE', title: isEn ? '2D Shift Roster Clash Matrix & PPh21 Simulator' : 'Matriks Roster Shift 2D + Simulasi Gaji PPh21 TER', panes: ['7-COL: Grid Staf × Tanggal + Deteksi Malam->Pagi', '5-COL: 3 Kolom Bruto | Pajak/BPJS | THP'] },
      { code: 'UI_11B_ESS_ATTENDANCE_APPROVAL_HUB', badge: 'OPSI B • PORTAL ESS + ZKTeco', shell: 'MOBILE_FIRST_THUMB_PWA', title: isEn ? 'Mobile ESS Geofence Clock-In + ZKTeco Biometric' : 'Portal Mandiri Karyawan (ESS) GPS + Mesin ZKTeco', panes: ['5-COL: Absen Selfie GPS + Cuti + Slip PDF', '7-COL: Sinkronisasi ZKTeco ADMS + Approval Lembur'] },
      { code: 'UI_11C_INTERNAL_ASSET_PROCUREMENT', badge: 'OPSI C • ASET KANTOR & PR/PO', shell: 'SIDEBAR_ERP_DENSE', title: isEn ? 'QR Asset Custody, PR->PO->GRN & Budget Ceiling' : 'Manajemen Aset QR Kantor, PR/PO & Plafon Anggaran', panes: ['7-COL: Alur PR -> PO -> GRN + Kunci Plafon', '5-COL: BAST Serah Terima Laptop/Aset QR'] },
    ],
    SECTOR_12_GOVTECH_NONPROFIT: [
      { code: 'UI_12A_ISAK35_RESTRICTED_FUND_SPLITTER', badge: 'OPSI A • BUKU BESAR ISAK 35', shell: 'SIDEBAR_ERP_DENSE', title: isEn ? 'Restricted vs Unrestricted Fund Accounting Desk' : 'Buku Besar Pemisah Dana Terikat vs Operasional (Yayasan)', panes: ['7-COL: Saldo Donasi/Hibah Terikat Anti-Campur', '5-COL: Pencairan Berjenjang + Laporan ISAK 35/ASC 958'] },
      { code: 'UI_12B_PUBLIC_DONOR_TRUST_PORTAL', badge: 'OPSI B • PORTAL TRANSPARANSI', shell: 'TOPBAR_EXECUTIVE_DECK', title: isEn ? 'Public Donor Impact Portal & SHA-256 Verifier' : 'Portal Donasi Publik Transparan & Verifikasi SHA-256', panes: ['6-COL: Progres Dana + Bukti Foto/GPS Lapangan', '6-COL: Donasi Cepat QRIS + Kwitansi Ber-Hash'] },
      { code: 'UI_12C_FIELD_AID_DISTRIBUTION_DESK', badge: 'OPSI C • RELAWAN PENYALURAN', shell: 'MOBILE_FIRST_THUMB_PWA', title: isEn ? 'Beneficiary NIK Deduplication & Field Aid PWA' : 'PWA Relawan Pendataan Penerima Bantuan Anti-Ganda', panes: ['5-COL: Cek Duplikasi NIK + Scan Kupon Bantuan', '7-COL: Kuota Wilayah + Laporan Dampak Otomatis'] },
    ],
  };

  const TIER1_JURISDICTION_LOCALES = [
    { code: 'id', flag: '🇮🇩', label: 'ID — Indonesia (UU PDP • PSAK 109 / ISAK 35 • PPh 21 TER • QRIS)', dir: 'ltr' },
    { code: 'en', flag: '🇺🇸', label: 'EN — International / US-SG (SOC2 • FASB ASC 958 • HIPAA • Stripe)', dir: 'ltr' },
    { code: 'ar', flag: '🇸🇦', label: 'AR — العربية / MENA [Auto dir="rtl"] (ZATCA Fatoora • AAOIFI)', dir: 'rtl' },
    { code: 'ja', flag: '🇯🇵', label: 'JA — 日本語 / Japan (APPI • Qualified Invoice JCT)', dir: 'ltr' },
    { code: 'zh-CN', flag: '🇨🇳', label: 'ZH — 简体中文 / APAC (PIPL • Golden Tax Fapiao)', dir: 'ltr' },
    { code: 'de', flag: '🇩🇪', label: 'DE — Deutsch / EU DACH (GDPR • GoBD • XRechnung)', dir: 'ltr' },
    { code: 'es', flag: '🇪🇸', label: 'ES — Español / LATAM-EU (GDPR • CFDI / FacturaE)', dir: 'ltr' },
  ];

  const KILLER_ENGINES_SHOWCASE = [
    {
      num: '01',
      badge: 'ANTI-OVERFLOW',
      title: isEn ? 'Phased Prompt Sequencer (Phase 1 to 6)' : 'Phased Prompt Sequencer (Fase 1 s/d 6)',
      desc: isEn
        ? 'Slices execution into .agents/prompts/01-foundation.md through 06-deployment.md. Enforces 100% green Acceptance Tests in Phase 4 BEFORE touching any UI in Phase 5.'
        : 'Memecah eksekusi ke .agents/prompts/01-foundation.md s/d 06-deployment.md. Mewajibkan 100% Acceptance Test hijau di Fase 4 SEBELUM menyentuh UI di Fase 5.',
    },
    {
      num: '02',
      badge: 'INDONESIAN DATA',
      title: isEn ? 'Realistic Domain Seeders & Stress Factory' : 'Realistic Domain Seeders & Stress Factory',
      desc: isEn
        ? 'Strictly bans "Test Name 1" & "Lorem ipsum". Auto-generates authentic Indonesian SKUs, ICD-10 clinical codes, PSAK CoA ledgers, and PPh21 TER fixtures.'
        : 'Melarang keras "Test Name 1" & "Lorem ipsum". Menghasilkan SKU ritel Indonesia asli, kode medis ICD-10, CoA PSAK, dan tarif PPh21 TER siap demo ke investor.',
    },
    {
      num: '03',
      badge: 'LIVE TOPOLOGY',
      title: isEn ? 'Visual ERD, State & UI Wireframe Previewer' : 'Visual ERD, State & UI Wireframe Previewer',
      desc: isEn
        ? 'Inspect PostgreSQL 16 ULID ERDs, State Machine transitions, and industry-specific multi-pane UI wireframes visually before writing a single line of code.'
        : 'Inspeksi diagram ERD PostgreSQL 16 ULID, transisi State Machine, dan pratinjau tata letak panel UI spesifik industri sebelum menulis satu baris kode pun.',
    },
    {
      num: '04',
      badge: 'ZERO DATA LOSS',
      title: isEn ? 'Reverse-Engineer / Brownfield SQL Importer' : 'Reverse-Engineer / Brownfield SQL Importer',
      desc: isEn
        ? 'Paste legacy CREATE TABLE SQL, Laravel migrations, or Prisma schemas to detect AUTO_INCREMENT/FLOAT/CASCADE debt and get a 4-stage Shadow ULID migration plan.'
        : 'Tempelkan SQL lama, migrasi Laravel, atau schema.prisma untuk mendeteksi utang teknis AUTO_INCREMENT/FLOAT/CASCADE dan memperoleh rencana migrasi Shadow ULID 4 tahap.',
    },
    {
      num: '05',
      badge: 'CI GATEKEEPER',
      title: isEn ? 'Blueprint Drift Auditor & ./verify-blueprint.sh' : 'Blueprint Drift Auditor & ./verify-blueprint.sh',
      desc: isEn
        ? 'Ships a standalone POSIX verify-blueprint.sh scanner & php artisan pos:audit CLI that catches rounded-full, gradients, alert(), or offset pagination and prints Auto-Fix Prompts.'
        : 'Menyertakan pemindai verify-blueprint.sh & php artisan pos:audit yang menangkap pelanggaran rounded-full, gradien, alert(), atau offset pagination beserta Auto-Fix Prompt.',
    },
    {
      num: '06',
      badge: '3-SEC INIT',
      title: isEn ? 'One-Command CLI Workspace Bootstrapper' : 'One-Command CLI Workspace Bootstrapper',
      desc: isEn
        ? 'Run curl -sL https://neriahpro.com/init/{ULID} | bash to materialize AGENTS.md, .cursorrules, graph.json, 6 phased prompts, and verify-blueprint.sh in 3 seconds.'
        : 'Jalankan curl -sL https://neriahpro.com/init/{ULID} | bash untuk menanamkan AGENTS.md, .cursorrules, graph.json, 6 phased prompts, dan verify-blueprint.sh dalam 3 detik.',
    },
  ];

  return (
    <div id="pricing-matrix" className="w-full bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 pt-4 sm:pt-6 pb-12 sm:pb-16 transition-colors scroll-mt-16">
      <div className="max-w-7xl mx-auto px-4 sm:px-6">

        {/* DYNAMIC MODULE SWITCHER (PROJECT OS VS UPCOMING CV PRO - DITENTUKAN DARI BACKEND ADMIN) */}
        {isCvProEnabled && (
          <div className="flex items-center justify-center pt-1 mb-6 sm:mb-8">
            <div className="inline-flex p-1 bg-zinc-200 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-none shadow-inner font-mono text-xs">
              <button
                type="button"
                onClick={() => setActiveTab('software')}
                className={`px-4 py-2 font-bold uppercase tracking-wider transition rounded-none flex items-center gap-2 cursor-pointer ${activeTab === 'software'
                  ? 'bg-zinc-900 text-white dark:bg-emerald-500 dark:text-black shadow-xs'
                  : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'
                  }`}
              >
                <Cpu className="w-3.5 h-3.5 text-emerald-500 dark:text-black" />
                <span>Project OS // Digital Architecture</span>
                <span className="px-1.5 py-0.2 bg-emerald-500/20 text-emerald-700 dark:text-black font-black text-[9px]">ACTIVE</span>
              </button>

              <button
                type="button"
                onClick={() => setActiveTab('cv')}
                className={`px-4 py-2 font-bold uppercase tracking-wider transition rounded-none flex items-center gap-2 cursor-pointer ${activeTab === 'cv'
                  ? 'bg-zinc-900 text-white dark:bg-purple-500 dark:text-white shadow-xs'
                  : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-300'
                  }`}
              >
                <FileText className="w-3.5 h-3.5 text-purple-400" />
                <span>CV Pro Studio</span>
                <span className="px-1.5 py-0.2 bg-zinc-300 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-bold text-[9px]">UPCOMING</span>
              </button>
            </div>
          </div>
        )}

        {/* IF CV TAB IS CLICKED: SHOW CLEAN UPCOMING PREVIEW */}
        {isCvProEnabled && activeTab === 'cv' ? (
          <div className="max-w-2xl mx-auto my-12 p-8 bg-zinc-100 dark:bg-zinc-900 border-2 border-dashed border-purple-500/40 rounded-none text-center space-y-4">
            <div className="w-12 h-12 mx-auto rounded-none bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-purple-500">
              <FileText className="w-6 h-6" />
            </div>
            <h3 className="text-xl font-black uppercase text-zinc-900 dark:text-white font-mono">
              CV Pro Studio &bull; {isEn ? 'Module Under Active Development (Q4)' : 'Modul Dalam Pengembangan (Q4)'}
            </h3>
            <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed font-sans">
              {isEn
                ? 'CV Pro Studio is currently under active sprint development for Q4 release. During our Midtrans merchant compliance review, only Project OS digital architecture services are actively processed for transactions.'
                : 'Modul CV Pro Studio sedang dalam persiapan rilis Q4. Selama periode review kepatuhan merchant Midtrans, hanya layanan rekayasa arsitektur Project OS yang aktif diproses untuk transaksi.'}
            </p>
            <div className="pt-2">
              <button
                type="button"
                onClick={() => setActiveTab('software')}
                className="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-bold text-xs uppercase tracking-wider rounded-none cursor-pointer transition"
              >
                &larr; {isEn ? 'View Active Project OS Services' : 'Lihat Layanan Aktif Project OS'}
              </button>
            </div>
          </div>
        ) : (
          <>
            {/* 1. CLEAN & CONCISE SINGLE HEADER SECTION */}
            <div className="text-center max-w-3xl mx-auto mb-6 sm:mb-8">
              <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-mono text-xs uppercase tracking-wider font-bold mb-2.5 rounded-none">
                <Sparkles className="w-3.5 h-3.5" />
                <span>{isEn ? 'TRANSPARENT VALUE-BASED PRICING' : 'SKEMA INVESTASI TRANSPARAN & TERSTANDAR'}</span>
              </div>

              <h1 className="text-2xl sm:text-3xl md:text-4xl font-black uppercase tracking-tight font-sans text-zinc-900 dark:text-white mb-2 leading-tight">
                {isEn ? 'Digital Architecture & Engineering Pricing' : 'Investasi Layanan Rekayasa Sistem'}
              </h1>

              <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed max-w-2xl mx-auto">
                {isEn
                  ? 'Standardized software engineering investment: From instant self-service architectural blueprints to full turnkey Monolith MVP contracts.'
                  : 'Pilihan investasi rekayasa perangkat lunak terstandarisasi untuk founder & pengembang: Dari cetak biru mandiri (Self-Service) hingga koding penuh turnkey Studio Monolith MVP.'}
              </p>
            </div>

            {/* 1.5 SATU PINTU END-TO-END GUIDED WIZARD & LIVE ALGORITHMIC RAB SIMULATOR (V5.0) */}
            {(() => {
              const parseCleanInt = (val, fallback) => {
                const n = parseInt(String(val ?? '').replace(/\D+/g, ''), 10);
                return Number.isFinite(n) && n > 0 ? n : fallback;
              };
              const parseCleanFloat = (val, fallback) => {
                const n = parseFloat(String(val ?? '').replace(/[^0-9.]+/g, ''));
                return Number.isFinite(n) && n > 0 ? n : fallback;
              };
              const formatIdrPlain = (num) => formatRegionalNumberInput(Math.round(num), isEn ? 'en' : 'id');

              const sectorComplexityMap = [
                { tables: 9, states: 7, locks: 3, integrations: 2, recommendedTierKey: 'retail_pro', recommendedRetail: 'pro', retailLabel: 'Pro Agentic (Rp ' + (pricingSettings.retail_pro_price || '399.000') + ')' },
                { tables: 8, states: 6, locks: 3, integrations: 2, recommendedTierKey: 'retail_pro', recommendedRetail: 'pro', retailLabel: 'Pro Agentic (Rp ' + (pricingSettings.retail_pro_price || '399.000') + ')' },
                { tables: 12, states: 9, locks: 4, integrations: 3, recommendedTierKey: 'retail_ultimate', recommendedRetail: 'ultimate', retailLabel: 'Ultimate OS (Rp ' + (pricingSettings.retail_ultimate_price || '1.490.000') + ')' },
                { tables: 11, states: 8, locks: 5, integrations: 3, recommendedTierKey: 'retail_ultimate', recommendedRetail: 'ultimate', retailLabel: 'Ultimate OS (Rp ' + (pricingSettings.retail_ultimate_price || '1.490.000') + ')' },
                { tables: 11, states: 8, locks: 3, integrations: 2, recommendedTierKey: 'retail_ultimate', recommendedRetail: 'ultimate', retailLabel: 'Ultimate OS (Rp ' + (pricingSettings.retail_ultimate_price || '1.490.000') + ')' },
                { tables: 10, states: 9, locks: 3, integrations: 3, recommendedTierKey: 'retail_pro', recommendedRetail: 'pro', retailLabel: 'Pro Agentic (Rp ' + (pricingSettings.retail_pro_price || '399.000') + ')' },
                { tables: 8, states: 6, locks: 2, integrations: 2, recommendedTierKey: 'retail_pro', recommendedRetail: 'pro', retailLabel: 'Pro Agentic (Rp ' + (pricingSettings.retail_pro_price || '399.000') + ')' },
                { tables: 8, states: 6, locks: 2, integrations: 2, recommendedTierKey: 'retail_pro', recommendedRetail: 'pro', retailLabel: 'Pro Agentic (Rp ' + (pricingSettings.retail_pro_price || '399.000') + ')' },
                { tables: 9, states: 7, locks: 2, integrations: 2, recommendedTierKey: 'retail_pro', recommendedRetail: 'pro', retailLabel: 'Pro Agentic (Rp ' + (pricingSettings.retail_pro_price || '399.000') + ')' },
                { tables: 9, states: 7, locks: 3, integrations: 2, recommendedTierKey: 'retail_ultimate', recommendedRetail: 'ultimate', retailLabel: 'Ultimate OS (Rp ' + (pricingSettings.retail_ultimate_price || '1.490.000') + ')' },
                { tables: 10, states: 8, locks: 3, integrations: 3, recommendedTierKey: 'retail_ultimate', recommendedRetail: 'ultimate', retailLabel: 'Ultimate OS (Rp ' + (pricingSettings.retail_ultimate_price || '1.490.000') + ')' },
                { tables: 10, states: 8, locks: 3, integrations: 2, recommendedTierKey: 'impact_nonprofit_grant', recommendedRetail: 'grant', retailLabel: isEn ? 'CSR Non-Profit Grant (Rp 0 Subsidized)' : 'Hibah CSR Yayasan / NGO (Rp 0 Terverifikasi)' },
              ];

              const wizardSector = SECTORAL_ARCHETYPES_SHOWCASE[selectedSectorIdx] || SECTORAL_ARCHETYPES_SHOWCASE[0];
              const comp = sectorComplexityMap[selectedSectorIdx] || sectorComplexityMap[0];
              const sectorVisualVariants = SECTOR_VISUAL_VARIANTS_36[wizardSector.code] || SECTOR_VISUAL_VARIANTS_36.SECTOR_01_COMMERCE_OMNICHANNEL;
              const activeVisualUi = sectorVisualVariants[selectedUiVariantIdx] || sectorVisualVariants[0];
              const activeJurisdiction = TIER1_JURISDICTION_LOCALES.find((l) => l.code === wizardOutputLocale) || TIER1_JURISDICTION_LOCALES[0];

              const stackOptions = [
                { code: 'STACK_M1_LARAVEL_MONOLITH', short: 'M1 Monolith (1.0x)', mult: 1.0, desc: 'Laravel 13 + Filament v5 + Livewire 4 + PG 16' },
                { code: 'STACK_D1_LARAVEL_NEXTJS', short: 'D1 Decoupled (1.25x)', mult: 1.25, desc: 'Laravel 13 API + Next.js 15 App Router + OpenAPI' },
                { code: 'STACK_S1_POLYGLOT_CORE', short: 'S1 Polyglot (1.5x)', mult: 1.5, desc: 'Go/Rust High-Throughput Core + Laravel Control Plane' },
              ];
              const activeStack = stackOptions.find((s) => s.code === wizardStackCode) || stackOptions[0];

              const hourlyRate = parseCleanInt(pricingSettings.mitra_man_hour_rate_idr, 350000);
              const hrsTable = parseCleanFloat(pricingSettings.mitra_hours_per_table, 4.0);
              const hrsState = parseCleanFloat(pricingSettings.mitra_hours_per_state, 3.0);
              const hrsLock = parseCleanFloat(pricingSettings.mitra_hours_per_lock, 6.0);
              const baseSetupHours = parseCleanFloat(pricingSettings.mitra_base_setup_hours, 24.0);
              const terminScheme = pricingSettings.mitra_termin_scheme || '30_50_20';

              const uiComplexityBonus = selectedUiVariantIdx === 0 ? 0 : (selectedUiVariantIdx === 1 ? 4.0 : 6.0);
              const rawHours = baseSetupHours + (comp.tables * hrsTable) + (comp.states * hrsState) + (comp.locks * hrsLock) + (comp.integrations * 8.0) + uiComplexityBonus;
              const totalHours = Math.round(rawHours * activeStack.mult);
              const workingDays = Math.max(10, Math.ceil(totalHours / 7));
              const estimatedRabIdr = Math.round((totalHours * hourlyRate) / 50000) * 50000;
              const dp30 = Math.round(estimatedRabIdr * (terminScheme === '50_50' ? 0.5 : 0.3));
              const milestone50 = terminScheme === '50_50' ? 0 : Math.round(estimatedRabIdr * 0.5);
              const finalBast = estimatedRabIdr - dp30 - milestone50;
              const blueprintQueryTail = `&sector=${encodeURIComponent(wizardSector.code)}&stack=${encodeURIComponent(wizardStackCode)}&ui_variant=${encodeURIComponent(activeVisualUi.code)}&shell=${encodeURIComponent(activeVisualUi.shell)}&persona=${encodeURIComponent(wizardPersonaMode)}&output_locale=${encodeURIComponent(wizardOutputLocale)}`;

              const retailPackageCatalog = {
                retail_pro: {
                  key: 'retail_pro',
                  shortName: 'Pro Agentic Blueprint',
                  priceStr: `Rp ${pricingSettings.retail_pro_price || '399.000'}`,
                  priceSub: isEn ? 'One-time official SaaS license' : 'Sekali bayar lisensi SaaS resmi',
                  badge: isEn ? 'POPULAR CHOICE' : 'PILIHAN UTAMA',
                  desc: isEn
                    ? 'Full PRD 26 Parameters, PostgreSQL 16 Strict ULID DDL, 7-Phase AI Prompts, WBS 5-Sprint Gantt & Canary IP Seal.'
                    : 'PRD 26 Parameter utuh, DDL PostgreSQL 16 Strict ULID, 7-Fase Prompt AI, WBS 5-Sprint Gantt & Segel Canary IP.',
                },
                retail_ultimate: {
                  key: 'retail_ultimate',
                  shortName: 'Ultimate Architecture OS',
                  priceStr: `Rp ${pricingSettings.retail_ultimate_price || '1.490.000'}`,
                  priceSub: isEn ? 'Includes 1-on-1 Architect Call' : 'Termasuk Telepon Arsitek 1-on-1',
                  badge: isEn ? 'ENTERPRISE ARCHITECT' : 'ARSITEKTUR LENGKAP',
                  desc: isEn
                    ? 'Everything in Pro + Live 1-on-1 Technical Scoping Call (60 min) with Principal Architect, Living CVE Autopatch & AgentShield Vault.'
                    : 'Semua fitur Pro + Sesi Telepon Teknis 1-on-1 (60 menit) bersama Principal Architect, Living CVE Autopatch & Brankas AgentShield.',
                },
                single_app_umkm_pass: {
                  key: 'single_app_umkm_pass',
                  shortName: 'Single-App UMKM Pass',
                  priceStr: `Rp ${pricingSettings.single_app_pass_price || '299.000'}`,
                  priceSub: isEn ? 'Pay-per-project (1 app lifetime)' : 'Sekali bayar 1 aplikasi selamanya',
                  badge: isEn ? 'PAY-PER-APP' : '1 APLIKASI PENUH',
                  desc: isEn
                    ? 'Pay once for 1 complete app launch: locked 36 Visual UI Sektor, production DB schema, 7-Phase Prompts, & Zero-Slop Certified.'
                    : 'Bayar sekali untuk peluncuran 1 aplikasi penuh: terkunci 36 UI Visual Sektoral, skema DB produksi, 7-Fase Prompt & Bersertifikat Zero-Slop.',
                },
                retail_lite: {
                  key: 'retail_lite',
                  shortName: 'Starter AI Architect',
                  priceStr: `Rp ${pricingSettings.retail_lite_price || '99.000'}`,
                  priceSub: isEn ? 'Fast concept sanity check' : 'Uji konsep arsitektur cepat',
                  badge: isEn ? 'BUDGET STARTER' : 'STARTER EKONOMIS',
                  desc: isEn
                    ? 'Essential architecture starter: Phase 1–3 Discovery & Concept Audit, core ULID tables, and initial blueprint scaffolding.'
                    : 'Paket dasar arsitektur: Audit Discovery & Konsep Fase 1–3, tabel ULID utama, dan scaffolding cetak biru perdana.',
                },
                agency_whitelabel_partner: {
                  key: 'agency_whitelabel_partner',
                  shortName: 'Agency Partner White-Label',
                  priceStr: `Rp ${pricingSettings.agency_whitelabel_price || '990.000'}`,
                  priceSub: isEn ? '5 seats studio pack' : 'Paket studio agensi 5 kursi',
                  badge: isEn ? 'AGENCY B2B2C' : 'MULTI-KLIEN AGENSI',
                  desc: isEn
                    ? 'Multi-seat agency studio: White-Label PDF proposal, custom branding, client viewer role, & multi-client project isolation.'
                    : 'Studio agensi multi-seat: Proposal PDF White-Label merek sendiri, peran peninjau klien, & isolasi proyek multi-klien.',
                },
                impact_nonprofit_grant: {
                  key: 'impact_nonprofit_grant',
                  shortName: isEn ? 'Impact & Non-Profit Grant' : 'Hibah Yayasan, NGO & Rumah Ibadah',
                  priceStr: 'Rp 0',
                  priceSub: isEn ? '100% CSR Subsidized (Real Value Rp 1.490.000)' : 'Subsidi Penuh CSR 100% (Nilai Riil Rp 1.490.000)',
                  badge: isEn ? 'CSR GRANT' : 'HIBAH SOSIAL CSR',
                  desc: isEn
                    ? 'Real value Rp 1.490.000 fully subsidized to Rp 0 for registered NGOs, religious houses, social schools & academic research (Requires legal doc verification).'
                    : 'Nilai riil Rp 1.490.000 disubsidi 100% menjadi Rp 0 khusus Yayasan, Rumah Ibadah, Sekolah Sosial & Riset Kampus (Wajib verifikasi akta/SK Kemenkumham).',
                },
                retail_spark: {
                  key: 'retail_spark',
                  shortName: 'Spark Guest Idea Audit',
                  priceStr: 'Rp 0',
                  priceSub: isEn ? 'Free Guest Mode (2x/month)' : 'Mode Tamu Gratis (Kuota 2x/bulan)',
                  badge: isEn ? 'GUEST EXPLORER' : 'KUOTA TAMU',
                  desc: isEn
                    ? 'Free guest quota: Idea exploration and Phase 1–2 readiness audit. Code exports & full blueprints require paid SaaS license.'
                    : 'Kuota tamu gratis: Eksplorasi ide dan audit kelayakan Fase 1–2. Ekspor kode dan cetak biru penuh memerlukan lisensi berbayar.',
                },
              };

              const activeRetailPkg = retailPackageCatalog[wizardRetailTier] || retailPackageCatalog['retail_pro'];

              const retailWizardPackages = [
                { key: 'retail_pro', label: `Pro Agentic Blueprint — Rp ${pricingSettings.retail_pro_price || '399.000'} (${isEn ? 'Recommended' : 'Rekomendasi Utama'})` },
                { key: 'retail_ultimate', label: `Ultimate Architecture OS + 1-on-1 Call — Rp ${pricingSettings.retail_ultimate_price || '1.490.000'}` },
                { key: 'single_app_umkm_pass', label: `Single-App UMKM Pass — Rp ${pricingSettings.single_app_pass_price || '299.000'} (${isEn ? '1 App One-Time' : 'Sekali Bayar 1 Aplikasi'})` },
                { key: 'retail_lite', label: `Starter AI Architect — Rp ${pricingSettings.retail_lite_price || '99.000'}` },
                { key: 'agency_whitelabel_partner', label: `Agency Partner White-Label (5-Seat) — Rp ${pricingSettings.agency_whitelabel_price || '990.000'}` },
                { key: 'impact_nonprofit_grant', label: `${isEn ? 'CSR Non-Profit / NGO Grant' : 'Hibah CSR Yayasan / NGO'} — Rp ${pricingSettings.impact_grant_price || '0'} (${isEn ? 'Doc Verification' : 'Verifikasi Akta'})` },
                { key: 'retail_spark', label: `Spark Guest Idea Audit — Rp 0 (${isEn ? '2x/mo Quota • Phase 1–2 Only' : 'Kuota 2x/bln • Terbatas Fase 1–2'})` },
              ];

              const mitraWizardPackages = [
                { key: 'full_mvp', label: `${isEn ? 'Custom Full Turnkey MVP (Escrow RAB)' : 'Custom Full Turnkey MVP (Escrow RAB Algoritmik)'} — Rp ${formatIdrPlain(estimatedRabIdr)}` },
                { key: 'umkm_starter', label: `${isEn ? 'UMKM Digital Starter (50% Subsidy)' : 'UMKM Digital Starter (Subsidi 50%)'} — Rp ${pricingSettings.umkm_price || '3.750.000'}` },
                { key: 'blueprint_advisory', label: `${isEn ? '1-on-1 Principal Architect Advisory (100% DP Credit)' : '1-on-1 Advisory Blueprint (Potong DP 100%)'} — Rp ${pricingSettings.advisory_price || '2.500.000'}` },
              ];

              const mitraPackageCatalog = {
                full_mvp: {
                  key: 'full_mvp',
                  shortName: isEn ? 'Custom Full Turnkey MVP' : 'Custom Full Turnkey MVP',
                  priceStr: `Rp ${formatIdrPlain(estimatedRabIdr)}`,
                  priceSub: `${totalHours} ${isEn ? 'Man-Hours' : 'Jam Kerja'} (${workingDays} ${isEn ? 'Work Days' : 'Hari Kerja'})`,
                  badge: isEn ? 'TURNKEY ESCROW SLA' : 'TERIMA BERES ESCROW',
                  desc: isEn
                    ? '100% Coded & Deployed by Neriah Pro Senior Team: 20 Paripurna Pillars, SPK SHA-256 Scope Lock, Live Staging & 90-Day Post-BAST SLA.'
                    : '100% Dikodingkan & Dideploy Tim Senior Neriah Pro: 20 Pilar Paripurna, Kontrak SPK SHA-256, Live Staging & Garansi SLA 90 Hari.',
                },
                umkm_starter: {
                  key: 'umkm_starter',
                  shortName: isEn ? 'UMKM Digital Starter (50% Subsidy)' : 'UMKM Digital Starter (Subsidi 50%)',
                  priceStr: `Rp ${pricingSettings.umkm_price || '3.750.000'}`,
                  priceSub: isEn ? '2–3 Weeks (50% DP + 50% UAT)' : '2–3 Minggu (DP 50% + Pelunasan UAT 50%)',
                  badge: isEn ? '50% UMKM SUBSIDY' : 'SUBSIDI UMKM 50%',
                  desc: isEn
                    ? 'Subsidized from normal Rp 7.5M (max 2 slots/month) for local stores & F&B: Turnkey POS/QRIS, Customer DB, WhatsApp receipt & 30-Day SLA.'
                    : 'Subsidi 50% dari normal Rp 7,5 Jt (kuota 2 usaha/bulan) khusus toko ritel & F&B: Terima beres POS/QRIS, DB Pelanggan & Garansi 30 Hari.',
                },
                blueprint_advisory: {
                  key: 'blueprint_advisory',
                  shortName: isEn ? '1-on-1 Principal Architect Advisory' : '1-on-1 Advisory Blueprint Studio',
                  priceStr: `Rp ${pricingSettings.advisory_price || '2.500.000'}`,
                  priceSub: isEn ? '24–48 Hours • 100% Deducts MVP DP' : '24–48 Jam Kerja • Potong DP MVP 100%',
                  badge: isEn ? '100% DP CREDIT' : 'POTONG DP 100%',
                  desc: isEn
                    ? '1-on-1 Principal Architect technical scoping, bespoke 26-Parameter PRD, Strict ULID DDL & WBS 5 Sprints. 100% credited if you proceed to Full MVP.'
                    : 'Scoping teknis 1-on-1 bersama Principal Architect, PRD 26 Parameter kustom, DDL Strict ULID & WBS 5 Sprint. Memotong DP 50% Full MVP secara penuh.',
                },
              };

              const activeMitraPkg = mitraPackageCatalog[wizardMitraTier] || mitraPackageCatalog['full_mvp'];

              const launchSatuPintuCheckout = (pkgKey) => {
                const summaryLine = `[${isEn ? 'ONE-STOP' : 'SATU PINTU'} v12.0 LOCKED] Sector=${wizardSector.code} | Stack=${wizardStackCode} | UI=${activeVisualUi.code} (${activeVisualUi.shell}) | Persona=${wizardPersonaMode} | Locale=${wizardOutputLocale.toUpperCase()}${wizardMode === 'MITRA_TURNKEY_PROJECT' ? ` | EstRAB=Rp ${formatIdrPlain(estimatedRabIdr)} (${totalHours}h)` : ''}`;
                setSatuPintuReturnQuery(blueprintQueryTail);
                openBookingModal(pkgKey);
                setFormData((prev) => ({
                  ...prev,
                  notes: prev.notes && (prev.notes.includes('[SATU PINTU') || prev.notes.includes('[ONE-STOP'))
                    ? prev.notes.replace(/\[(SATU PINTU|ONE-STOP)[^\n]*/g, summaryLine)
                    : (prev.notes ? `${summaryLine}\n${prev.notes}` : summaryLine),
                }));
              };

              const scrollToCatalogSection = (tabId) => {
                switchPricingTab(tabId);
                if (typeof document !== 'undefined') {
                  const anchorEl = document.getElementById(`tab-btn-${tabId}`);
                  if (anchorEl) {
                    anchorEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                  }
                }
              };

              return (
                <div className="max-w-7xl mx-auto mb-8 px-2 sm:px-4">
                  <div className="border-2 border-zinc-900 dark:border-zinc-700 bg-white dark:bg-zinc-900 rounded-none shadow-xs">
                    {/* Top Satu Pintu Bar */}
                    <div className="px-4 sm:px-6 py-3 bg-zinc-950 text-white dark:bg-zinc-950 border-b border-zinc-800 flex flex-wrap items-center justify-between gap-2">
                      <div className="flex flex-wrap items-center gap-2 font-mono text-[11px] font-black uppercase tracking-wider">
                        <span className="px-2 py-0.5 bg-emerald-500 text-zinc-950 font-black">{isEn ? 'ONE-STOP WIZARD V12.0' : 'SATU PINTU WIZARD V12.0'}</span>
                        <span>{isEn ? 'UNIFIED SAAS & MITRA COMMERCIAL CONFIGURATOR • 12 SECTORS • 20 PILLARS LOCKED' : 'KONFIGURATOR TERPADU PAKET SAAS & MITRA • 12 SEKTOR • 20 PILAR PARIPURNA'}</span>
                      </div>
                      <button
                        type="button"
                        onClick={() => setShowSatuPintuWizard((v) => !v)}
                        className="px-2.5 py-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-100 border border-zinc-700 font-mono text-[10px] font-black uppercase rounded-none cursor-pointer"
                      >
                        {showSatuPintuWizard ? (isEn ? 'Minimize Wizard ▲' : 'Ringkas Wizard ▲') : (isEn ? 'Expand Guided Wizard ▼' : 'Buka Panduan & Simulasi RAB ▼')}
                      </button>
                    </div>

                    {/* 5-Step End-to-End Journey Strip */}
                    <div className="grid grid-cols-2 sm:grid-cols-5 divide-y sm:divide-y-0 sm:divide-x divide-zinc-200 dark:divide-zinc-800 bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 font-mono text-[10px]">
                      {[
                        { step: '01', label: isEn ? 'Path, 12 Sectors & 36 Visual UI' : 'Pilih Jalur, 12 Sektor & 36 UI Visual', active: true },
                        { step: '02', label: isEn ? 'Dual-Persona Storyboard / 3-Pane' : 'Mode Bisnis Awam / 3-Pane Arsitek', active: false },
                        { step: '03', label: isEn ? 'License / Grant / SPK SHA-256' : 'Lisensi / Hibah CSR / SPK SHA-256', active: false },
                        { step: '04', label: isEn ? '7-Phase Prompts / Escrow Staging' : '7 Fase AI / Escrow & Live Staging', active: false },
                        { step: '05', label: isEn ? 'Canary IP Seal / UAT & BAST' : 'Segel Canary IP / UAT & BAST Digital', active: false },
                      ].map((st) => (
                        <div key={st.step} className={`px-3 py-2 flex items-center gap-2 ${st.active ? 'bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-950 dark:text-emerald-200 font-black' : 'text-zinc-700 dark:text-zinc-400 font-bold'}`}>
                          <span className={`px-1.5 py-0.5 border ${st.active ? 'bg-zinc-950 text-emerald-400 border-zinc-950 dark:bg-emerald-400 dark:text-zinc-950 dark:border-emerald-400' : 'bg-white dark:bg-zinc-900 border-zinc-300 dark:border-zinc-700'}`}>
                            {st.step}
                          </span>
                          <span className="leading-tight">{st.label}</span>
                        </div>
                      ))}
                    </div>

                    {showSatuPintuWizard && (
                      <div className="p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
                        {/* Left Column: Step A, B, C, D & E Controls */}
                        <div className="lg:col-span-7 space-y-4">
                          <div>
                            <div className="font-mono text-[10px] font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                              {isEn ? 'STEP A — WHO WRITES THE APPLICATION CODE?' : 'LANGKAH A — SIAPA YANG MENGODING APLIKASI ANDA?'}
                            </div>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                              <button
                                type="button"
                                id="wizard-step-a-retail"
                                onClick={() => switchPricingTab('retail')}
                                className={`p-3 text-left border-2 transition rounded-none cursor-pointer ${wizardMode === 'RETAIL_SELF_DEV'
                                  ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-950/80 dark:text-white dark:border-emerald-500'
                                  : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-900 border-zinc-300 dark:bg-zinc-950 dark:hover:bg-zinc-800/80 dark:text-zinc-100 dark:border-zinc-800'
                                  }`}
                              >
                                <div className="flex items-center justify-between gap-2 font-mono text-[10px] font-black uppercase">
                                  <span className={wizardMode === 'RETAIL_SELF_DEV' ? 'text-emerald-400' : 'text-emerald-800 dark:text-emerald-400'}>⚡ {isEn ? 'RETAIL VERSION' : 'VERSI RETAIL'}</span>
                                  <span className="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 dark:bg-emerald-900 dark:text-emerald-200 border border-emerald-400 dark:border-emerald-700">Rp 0 – Rp {pricingSettings.retail_ultimate_price || '1.490.000'}</span>
                                </div>
                                <div className="font-bold text-xs mt-1">
                                  {isEn ? 'Self-Service Blueprint & IDE Kit' : 'Koding Mandiri / Tim Developer Sendiri'}
                                </div>
                                <p className={`text-[11px] mt-0.5 leading-snug ${wizardMode === 'RETAIL_SELF_DEV' ? 'text-zinc-300' : 'text-zinc-600 dark:text-zinc-400'}`}>
                                  {isEn ? 'Instant PRD, ULID SQL, 7-Phase AI Prompts, 36 Visual UI & Live MCP.' : 'Unduh instan PRD 26 Parameter, SQL ULID, 7-Fase Prompt AI, CLI /init & Live MCP.'}
                                </p>
                              </button>

                              <button
                                type="button"
                                id="wizard-step-a-mitra"
                                onClick={() => switchPricingTab('project')}
                                className={`p-3 text-left border-2 transition rounded-none cursor-pointer ${wizardMode === 'MITRA_TURNKEY_PROJECT'
                                  ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-amber-950/70 dark:text-white dark:border-amber-500'
                                  : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-900 border-zinc-300 dark:bg-zinc-950 dark:hover:bg-zinc-800/80 dark:text-zinc-100 dark:border-zinc-800'
                                  }`}
                              >
                                <div className="flex items-center justify-between gap-2 font-mono text-[10px] font-black uppercase">
                                  <span className={wizardMode === 'MITRA_TURNKEY_PROJECT' ? 'text-amber-300' : 'text-amber-900 dark:text-amber-300'}>🤝 {isEn ? 'PARTNER PROJECT VERSION' : 'VERSI MITRA PROYEK'}</span>
                                  <span className="px-1.5 py-0.5 bg-amber-100 text-amber-950 dark:bg-amber-900/80 dark:text-amber-200 border border-amber-400 dark:border-amber-700">TURNKEY SLA</span>
                                </div>
                                <div className="font-bold text-xs mt-1">
                                  {isEn ? '100% Coded by Neriah Pro Studio' : 'Terima Beres 100% Dikodingkan Neriah Pro'}
                                </div>
                                <p className={`text-[11px] mt-0.5 leading-snug ${wizardMode === 'MITRA_TURNKEY_PROJECT' ? 'text-zinc-300' : 'text-zinc-600 dark:text-zinc-400'}`}>
                                  {isEn ? 'Algorithmic Fair RAB, SPK SHA-256 Lock, Live Staging, UAT & Digital BAST.' : 'RAB Algoritmik Fair, Kontrak SPK SHA-256, Live Staging, Checklist UAT & BAST Digital.'}
                                </p>
                              </button>
                            </div>
                          </div>

                          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                              <label className="block font-mono text-[10px] font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">
                                {isEn ? 'STEP B — SELECT BUSINESS SECTOR (12 TAXONOMIES)' : 'LANGKAH B — PILIH SEKTOR BISNIS (12 SEKTOR)'}
                              </label>
                              <select
                                value={selectedSectorIdx}
                                onChange={(e) => {
                                  const nextIdx = Number(e.target.value);
                                  const nextComp = sectorComplexityMap[nextIdx] || sectorComplexityMap[0];
                                  setSelectedSectorIdx(nextIdx);
                                  setSelectedUiVariantIdx(0);
                                  setWizardRetailTier(nextComp.recommendedTierKey || (nextComp.recommendedRetail === 'ultimate' ? 'retail_ultimate' : 'retail_pro'));
                                }}
                                className="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 font-mono text-xs font-bold rounded-none focus:border-emerald-500 outline-none"
                              >
                                {SECTORAL_ARCHETYPES_SHOWCASE.map((sec, sIdx) => (
                                  <option key={sec.code} value={sIdx}>
                                    #{sec.num} — {sec.name} ({sec.primary})
                                  </option>
                                ))}
                              </select>
                            </div>

                            <div>
                              <label className="block font-mono text-[10px] font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">
                                {isEn ? 'STEP C — ARCHITECTURE TOPOLOGY PRESET' : 'LANGKAH C — TOPOLOGI ARSITEKTUR SISTEM'}
                              </label>
                              <div className="grid grid-cols-3 gap-1.5">
                                {stackOptions.map((st) => {
                                  const active = st.code === wizardStackCode;
                                  return (
                                    <button
                                      key={st.code}
                                      type="button"
                                      onClick={() => setWizardStackCode(st.code)}
                                      className={`px-2 py-2 border font-mono text-[10px] font-black uppercase rounded-none transition cursor-pointer ${active
                                        ? 'bg-zinc-950 text-emerald-400 border-zinc-950 dark:bg-emerald-500 dark:text-zinc-950 dark:border-emerald-500'
                                        : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-800 border-zinc-300 dark:bg-zinc-950 dark:hover:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700'
                                        }`}
                                      title={st.desc}
                                    >
                                      {st.short}
                                    </button>
                                  );
                                })}
                              </div>
                            </div>
                          </div>

                          {/* STEP D: VISUAL FRONTEND UI/UX SELECTOR (12 SECTORS x 3 VARIANTS = 36 VISUAL BLUEPRINTS) */}
                          <div className="p-3 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-300 dark:border-zinc-800 space-y-2.5">
                            <div className="flex flex-wrap items-center justify-between gap-2">
                              <span className="font-mono text-[10px] font-black uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
                                {isEn ? 'STEP D — CHOOSE VISUAL FRONTEND UI/UX BLUEPRINT (3 OPTIONS PER SECTOR)' : 'LANGKAH D — PILIH OPSI TAMPILAN VISUAL FRONTEND UI/UX (PASTI & TERKUNCI)'}
                              </span>
                              <span className="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[9px] font-black">
                                {activeVisualUi.code} &bull; {activeVisualUi.shell}
                              </span>
                            </div>
                            <div className="grid grid-cols-1 sm:grid-cols-3 gap-2">
                              {sectorVisualVariants.map((uiOpt, uIdx) => {
                                const isUiActive = uIdx === selectedUiVariantIdx;
                                return (
                                  <button
                                    key={uiOpt.code}
                                    type="button"
                                    onClick={() => setSelectedUiVariantIdx(uIdx)}
                                    className={`p-2.5 text-left border transition rounded-none cursor-pointer ${isUiActive
                                      ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-950/90 dark:text-white dark:border-emerald-400'
                                      : 'bg-white hover:bg-zinc-100 text-zinc-900 border-zinc-300 dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700'
                                      }`}
                                  >
                                    <div className={`font-mono text-[9px] font-black uppercase ${isUiActive ? 'text-emerald-400' : 'text-emerald-900 dark:text-emerald-300'}`}>
                                      {uiOpt.badge}
                                    </div>
                                    <div className="text-[11px] font-bold leading-snug mt-0.5">
                                      {uiOpt.title}
                                    </div>
                                  </button>
                                );
                              })}
                            </div>
                            {/* Live Visual Multi-Pane Wireframe Preview */}
                            <div className={`grid grid-cols-1 ${activeVisualUi.panes.length >= 3 ? 'sm:grid-cols-3' : 'sm:grid-cols-2'} gap-2 pt-1`}>
                              {activeVisualUi.panes.map((paneText, pIdx) => (
                                <div key={pIdx} className="px-2.5 py-2 bg-white dark:bg-zinc-900 border border-dashed border-zinc-400 dark:border-zinc-700 font-mono text-[10px]">
                                  <span className="font-black text-emerald-900 dark:text-emerald-300 block">
                                    [PANEL {pIdx + 1}]
                                  </span>
                                  <span className="text-zinc-800 dark:text-zinc-200 font-bold leading-tight block mt-0.5">
                                    {paneText}
                                  </span>
                                </div>
                              ))}
                            </div>
                          </div>

                          {/* STEP E: ADAPTIVE DUAL-PERSONA UX + TIER-1 JURISDICTION & RTL COMPLIANCE */}
                          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                              <label className="block font-mono text-[10px] font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">
                                {isEn ? 'STEP E1 — STUDIO PERSONA MODE' : 'LANGKAH E1 — MODE ANTARMUKA STUDIO'}
                              </label>
                              <div className="grid grid-cols-2 gap-1.5">
                                {[
                                  { id: 'BUSINESS_WIZARD', label: isEn ? '🧭 Business Awam (5-Step)' : '🧭 Mode Bisnis / Awam' },
                                  { id: 'ARCHITECT_PRO', label: isEn ? '⚡ Architect Pro (3-Pane)' : '⚡ Mode Arsitek 3-Pane' },
                                ].map((pm) => {
                                  const activePm = wizardPersonaMode === pm.id;
                                  return (
                                    <button
                                      key={pm.id}
                                      type="button"
                                      onClick={() => setWizardPersonaMode(pm.id)}
                                      className={`px-2 py-2 border font-mono text-[10px] font-black rounded-none transition cursor-pointer ${activePm
                                        ? 'bg-zinc-950 text-emerald-400 border-zinc-950 dark:bg-emerald-500 dark:text-zinc-950 dark:border-emerald-500'
                                        : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-800 border-zinc-300 dark:bg-zinc-950 dark:hover:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700'
                                        }`}
                                    >
                                      {pm.label}
                                    </button>
                                  );
                                })}
                              </div>
                            </div>

                            <div>
                              <label className="block font-mono text-[10px] font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">
                                {isEn ? 'STEP E2 — TIER-1 OUTPUT LOCALE & COMPLIANCE' : 'LANGKAH E2 — BAHASA & REGULASI NEGARA (TIER-1)'}
                              </label>
                              <select
                                value={wizardOutputLocale}
                                onChange={(e) => setWizardOutputLocale(e.target.value)}
                                className="w-full px-2.5 py-2 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 font-mono text-[11px] font-bold rounded-none focus:border-emerald-500 outline-none"
                              >
                                {TIER1_JURISDICTION_LOCALES.map((loc) => (
                                  <option key={loc.code} value={loc.code}>
                                    {loc.flag} {loc.label}
                                  </option>
                                ))}
                              </select>
                            </div>
                          </div>

                          <div className="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex flex-wrap items-center justify-between gap-2 text-[11px] font-mono">
                            <span className="font-bold text-zinc-800 dark:text-zinc-200">
                              {isEn ? 'Locked UI:' : 'UI Terkunci:'} <strong className="text-emerald-900 dark:text-emerald-300">{activeVisualUi.code}</strong> &bull; <strong>{activeJurisdiction.dir === 'rtl' ? 'RTL Logical CSS' : 'LTR Logical CSS'}</strong>
                            </span>
                            <span className="px-2 py-0.5 bg-zinc-200/80 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-200 font-black text-[10px] tabular-nums">
                              {comp.tables + 5} {isEn ? 'ULID Tables • 20 Complete Pillars • SEO/GEO + Data Privacy' : 'Tabel ULID • 20 Pilar Paripurna • SEO/GEO + UU PDP'}
                            </span>
                          </div>
                        </div>

                        {/* Right Column: Satu Pintu Unified Commercial Gate (Strictly Bound to SaaS & Mitra Tiers) */}
                        <div className="lg:col-span-5 border-2 border-emerald-500/80 dark:border-emerald-600 bg-zinc-50 dark:bg-zinc-950 p-4 flex flex-col justify-between">
                          {wizardMode === 'RETAIL_SELF_DEV' ? (
                            <div className="space-y-3">
                              <div className="flex items-center justify-between gap-2">
                                <span className="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black uppercase">
                                  {isEn ? 'RETAIL SAAS // ONE-STOP V12.0' : 'RETAIL SAAS // SATU PINTU V12.0'}
                                </span>
                                <span className="font-mono text-[10px] font-bold text-zinc-700 dark:text-zinc-300">
                                  {isEn ? 'SaaS Entitlement Gate' : 'Gerbang Resmi Paket SaaS'}
                                </span>
                              </div>
                              {/* 1. KOTAK STATUS PAKET TERPILIH (SEKETIKA BEREAKSI TERHADAP DROPDOWN LANGKAH F) */}
                              <div className="p-3 bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-emerald-500 space-y-1 rounded-none shadow-xs">
                                <div className="flex items-center justify-between gap-2">
                                  <span className="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300 tracking-wider">
                                    {isEn ? 'CURRENTLY SELECTED PACKAGE:' : 'PAKET LISENSI YANG ANDA PILIH:'}
                                  </span>
                                  <span className="px-1.5 py-0.5 bg-emerald-600 text-white font-mono text-[9px] font-black uppercase">
                                    {activeRetailPkg.badge}
                                  </span>
                                </div>
                                <div className="flex items-baseline justify-between gap-2">
                                  <div className="text-lg sm:text-xl font-black font-mono text-zinc-950 dark:text-white tabular-nums">
                                    {activeRetailPkg.shortName}
                                  </div>
                                  <div className="text-lg sm:text-xl font-black font-mono text-emerald-800 dark:text-emerald-400 tabular-nums">
                                    {activeRetailPkg.priceStr}
                                  </div>
                                </div>
                                <div className="text-[11px] font-mono text-zinc-600 dark:text-zinc-400 pt-0.5">
                                  {activeRetailPkg.priceSub}
                                </div>
                                <p className="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed pt-1 border-t border-zinc-200 dark:border-zinc-800">
                                  {activeRetailPkg.desc}
                                </p>
                              </div>

                              {/* 2. REKOMENDASI PINTAR KOMPLEKSITAS SEKTOR */}
                              <div className="flex flex-wrap items-center justify-between gap-2 p-2 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 text-[11px] font-mono">
                                <div className="text-zinc-800 dark:text-zinc-200">
                                  {wizardRetailTier === comp.recommendedTierKey ? (
                                    <span className="text-emerald-900 dark:text-emerald-300 font-bold">
                                      ✅ {isEn ? 'Matches complexity recommendation for ' : 'Pilihan Anda sesuai rekomendasi '} <strong>{wizardSector.title}</strong>
                                    </span>
                                  ) : (
                                    <span>
                                      💡 {isEn ? 'Sector recommendation: ' : 'Rekomendasi sektor: '}
                                      <strong className="text-zinc-950 dark:text-white">{comp.retailLabel}</strong>
                                    </span>
                                  )}
                                </div>
                                {wizardRetailTier !== comp.recommendedTierKey && (
                                  <button
                                    type="button"
                                    onClick={() => setWizardRetailTier(comp.recommendedTierKey)}
                                    className="px-2 py-0.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 text-[10px] font-black uppercase tracking-wider rounded-none cursor-pointer shrink-0 transition"
                                  >
                                    {isEn ? 'Apply Recommended Tier' : 'Terapkan Rekomendasi'}
                                  </button>
                                )}
                              </div>

                              {/* 3. RINGKASAN SPESIFIKASI LIVE REAKTIF (SEKETIKA BEREAKSI SAAT MEMILIH MENU SEBELAH KIRI) */}
                              <div className="p-2.5 bg-zinc-100/90 dark:bg-zinc-900/90 border border-zinc-300 dark:border-zinc-800 font-mono text-[10px] space-y-1.5">
                                <div className="text-[10px] font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300 flex items-center justify-between">
                                  <span>{isEn ? 'LIVE SPECIFICATION SYNC (FROM LEFT PANEL):' : 'SPESIFIKASI AKTIF (DARI PILIHAN SEBELAH KIRI):'}</span>
                                  <span className="text-emerald-800 dark:text-emerald-400 font-black">{isEn ? '● LOCKED REAL-TIME' : '● TERKUNCI REAL-TIME'}</span>
                                </div>
                                <div className="grid grid-cols-2 gap-1.5 text-zinc-900 dark:text-zinc-100">
                                  <div className="p-1.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                    <span className="text-zinc-500 block text-[9px] uppercase font-bold">{isEn ? 'Sector' : 'Sektor Bisnis (Langkah B)'}</span>
                                    <strong className="truncate block font-black text-emerald-950 dark:text-emerald-300">{wizardSector.code.slice(7, 9)} &bull; {wizardSector.title}</strong>
                                  </div>
                                  <div className="p-1.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                    <span className="text-zinc-500 block text-[9px] uppercase font-bold">{isEn ? 'Architecture Topology' : 'Topologi Stack (Langkah C)'}</span>
                                    <strong className="truncate block font-black">{activeStack.short}</strong>
                                  </div>
                                  <div className="p-1.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                    <span className="text-zinc-500 block text-[9px] uppercase font-bold">{isEn ? 'Frontend Visual UI' : 'Frontend Visual UI (Langkah D)'}</span>
                                    <strong className="truncate block font-black text-cyan-900 dark:text-cyan-300">{activeVisualUi.code}</strong>
                                  </div>
                                  <div className="p-1.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                    <span className="text-zinc-500 block text-[9px] uppercase font-bold">{isEn ? 'Database & Scale' : 'Skema Database & Pilar'}</span>
                                    <strong className="truncate block font-black">{comp.tables + 5} {isEn ? 'ULID Tables • 20 Pillars' : 'Tabel ULID • 20 Pilar'}</strong>
                                  </div>
                                  <div className="p-1.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                    <span className="text-zinc-500 block text-[9px] uppercase font-bold">{isEn ? 'Studio Interface' : 'Mode Antarmuka (Langkah E1)'}</span>
                                    <strong className="truncate block font-black">{wizardPersonaMode === 'ARCHITECT_PRO' ? (isEn ? 'Architect Pro (3-Pane)' : 'Mode Arsitek 3-Pane') : (isEn ? 'Business / Visual Mode' : 'Mode Bisnis / Awam')}</strong>
                                  </div>
                                  <div className="p-1.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                    <span className="text-zinc-500 block text-[9px] uppercase font-bold">{isEn ? 'Jurisdiction & Locale' : 'Regulasi & Bahasa (Langkah E2)'}</span>
                                    <strong className="truncate block font-black">{activeJurisdiction.flag} {activeJurisdiction.code.toUpperCase()} ({activeJurisdiction.dir.toUpperCase()})</strong>
                                  </div>
                                </div>
                              </div>

                              <div className="pt-2 border-t border-zinc-200 dark:border-zinc-800 space-y-2.5">
                                <div>
                                  <label className="block font-mono text-[10px] font-black uppercase tracking-wider text-zinc-800 dark:text-zinc-200 mb-1">
                                    {isEn ? 'STEP F — SELECT OFFICIAL RETAIL SAAS PACKAGE *' : 'LANGKAH F — PILIH PAKET LISENSI SAAS RETAIL *'}
                                  </label>
                                  <select
                                    id="wizard-step-f-retail-package"
                                    value={wizardRetailTier}
                                    onChange={(e) => setWizardRetailTier(e.target.value)}
                                    className="w-full px-2.5 py-2 bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-emerald-500 text-zinc-950 dark:text-zinc-100 font-mono text-[11px] font-bold rounded-none focus:border-emerald-500 outline-none"
                                  >
                                    {retailWizardPackages.map((pkgOpt) => (
                                      <option key={pkgOpt.key} value={pkgOpt.key}>
                                        {pkgOpt.label}
                                      </option>
                                    ))}
                                  </select>
                                </div>

                                {wizardRetailTier === 'impact_nonprofit_grant' ? (
                                  <div className="p-2.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700 font-mono text-[10px] leading-snug space-y-1">
                                    <div className="font-black uppercase flex items-center gap-1 text-amber-950 dark:text-amber-200">
                                      <span>🕊️ {isEn ? 'CSR NON-PROFIT GRANT NOTICE (RP 0):' : 'KETENTUAN HIBAH SOSIAL CSR (RP 0):'}</span>
                                    </div>
                                    <p>
                                      {isEn
                                        ? 'Real value Rp 1.490.000 fully subsidized to Rp 0 for registered non-profit foundations, places of worship, social schools, and academic research (ISAK 35 / PSAK 109). Legal document verification (Akta / SK Kemenkumham) is required post-submission.'
                                        : 'Nilai riil Rp 1.490.000 disubsidi 100% menjadi Rp 0 KHUSUS untuk Yayasan Sosial, Rumah Ibadah, Sekolah Non-Profit, dan Riset Akademik (ISAK 35 / PSAK 109). Wajib verifikasi Akta / SK Kemenkumham pasca pengajuan.'}
                                    </p>
                                    {selectedSectorIdx !== 11 && selectedSectorIdx !== 6 && (
                                      <div className="font-bold text-[9px] text-amber-950 dark:text-amber-200 pt-1 border-t border-amber-300 dark:border-amber-800">
                                        {isEn
                                          ? `⚠️ Note: You selected commercial sector ${wizardSector.title}. Commercial entities should select Single-App Pass, Pro Agentic, or Ultimate OS.`
                                          : `⚠️ Catatan: Anda memilih sektor bisnis komersial (${wizardSector.title}). Usaha komersial wajib menggunakan Single-App Pass, Pro Agentic, atau Ultimate OS.`}
                                      </div>
                                    )}
                                  </div>
                                ) : wizardRetailTier === 'retail_spark' ? (
                                  <div className="p-2 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/70 dark:text-amber-200 dark:border-amber-700 font-mono text-[10px] leading-snug">
                                    {isEn
                                      ? 'Guest Spark Quota (2x/month): Unlocks Phase 1–2 Idea Audit only. Phases 3–7, Full SQL/Docker ZIP & Live MCP require upgrading to a paid SaaS license.'
                                      : 'Kuota Tamu Spark (2x/bulan): Hanya membuka Audit Ide Fase 1–2. Fase 3–7, Ekspor Kode/Docker penuh & Live MCP wajib menggunakan lisensi SaaS berbayar.'}
                                  </div>
                                ) : (
                                  <div className="p-2 bg-emerald-100/80 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/70 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] leading-snug">
                                    {isEn
                                      ? 'All One-Stop selections (Sector, Stack, Visual UI & Regulation) attach directly to your SaaS license checkout & Customer Portal.'
                                      : 'Seluruh konfigurasi Satu Pintu (Sektor, Stack, Visual UI & Regulasi) otomatis dikunci ke Lisensi SaaS & Dashboard Akun Anda pasca-checkout.'}
                                  </div>
                                )}

                                <button
                                  type="button"
                                  id="wizard-retail-buy-license-btn"
                                  onClick={() => launchSatuPintuCheckout(wizardRetailTier)}
                                  className="w-full py-2.5 px-3 bg-emerald-600 hover:bg-emerald-500 text-white font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 rounded-none transition cursor-pointer text-center"
                                >
                                  <span>
                                    {wizardRetailTier === 'retail_spark'
                                      ? (isEn ? '🧪 Claim Free Spark Audit Quota (Phase 1–2) →' : '🧪 Klaim Kuota Audit Gratis Spark (Fase 1–2) →')
                                      : wizardRetailTier === 'impact_nonprofit_grant'
                                        ? (isEn ? '🕊️ Apply CSR Grant Verification (Rp 0) →' : '🕊️ Ajukan Verifikasi Hibah CSR (Rp 0) →')
                                        : (isEn ? `💳 Activate ${activeRetailPkg.shortName} (${activeRetailPkg.priceStr}) →` : `💳 Aktifkan Lisensi ${activeRetailPkg.shortName} (${activeRetailPkg.priceStr}) →`)}
                                  </span>
                                </button>

                                <button
                                  type="button"
                                  onClick={() => scrollToCatalogSection('retail')}
                                  className="w-full py-2 px-2.5 bg-white hover:bg-zinc-100 text-zinc-950 dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:text-zinc-100 font-mono text-[10px] font-black uppercase tracking-wider border border-zinc-400 dark:border-zinc-700 rounded-none cursor-pointer transition text-center"
                                >
                                  <span>{isEn ? '↓ Compare All 7 Retail SaaS Packages Below' : '↓ Bandingkan Rincian 7 Paket SaaS Retail di Bawah'}</span>
                                </button>
                              </div>
                            </div>
                          ) : (
                            <div className="space-y-3">
                              <div className="flex items-center justify-between gap-2">
                                <span className="px-2 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700 font-mono text-[10px] font-black uppercase">
                                  {isEn ? 'MITRA TURNKEY // ESCROW WBS V12.0' : 'MITRA TURNKEY // ESCROW RAB V12.0'}
                                </span>
                                <span className="font-mono text-[10px] font-bold text-zinc-700 dark:text-zinc-300 tabular-nums">
                                  Rp {formatIdrPlain(hourlyRate)}/{isEn ? 'hr' : 'jam'} &bull; {activeStack.mult}x
                                </span>
                              </div>

                              {/* 1. KOTAK KONTRAK MITRA TERPILIH (REAKTIF TERHADAP DROPDOWN LANGKAH F & PILIHAN KIRI) */}
                              <div className="p-3 bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-amber-500 space-y-1 rounded-none shadow-xs">
                                <div className="flex items-center justify-between gap-2">
                                  <span className="font-mono text-[10px] font-black uppercase text-amber-900 dark:text-amber-300 tracking-wider">
                                    {isEn ? 'CURRENTLY SELECTED MITRA CONTRACT:' : 'KONTRAK MITRA YANG ANDA PILIH:'}
                                  </span>
                                  <span className="px-1.5 py-0.5 bg-amber-500 text-zinc-950 font-mono text-[9px] font-black uppercase">
                                    {activeMitraPkg.badge}
                                  </span>
                                </div>
                                <div className="flex items-baseline justify-between gap-2">
                                  <div className="text-base sm:text-lg font-black font-mono text-zinc-950 dark:text-white">
                                    {activeMitraPkg.shortName}
                                  </div>
                                  <div className="text-lg sm:text-xl font-black font-mono text-amber-900 dark:text-amber-300 tabular-nums">
                                    {activeMitraPkg.priceStr}
                                  </div>
                                </div>
                                <div className="text-[11px] font-mono text-emerald-800 dark:text-emerald-400 font-bold tabular-nums">
                                  {activeMitraPkg.priceSub}
                                </div>
                                <p className="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed pt-1 border-t border-zinc-200 dark:border-zinc-800">
                                  {activeMitraPkg.desc}
                                </p>
                              </div>

                              {/* 2. RINCIAN TERMIN ESCROW / PEMBAYARAN DINAMIS */}
                              {wizardMitraTier === 'full_mvp' ? (
                                <div className="grid grid-cols-3 gap-1.5 font-mono text-[10px] tabular-nums">
                                  <div className="p-1.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800">
                                    <div className="text-zinc-600 dark:text-zinc-400 font-bold">{terminScheme === '50_50' ? 'DP 50%' : 'DP 30% [F1-2]'}</div>
                                    <div className="font-black text-zinc-950 dark:text-zinc-100">Rp {formatIdrPlain(dp30)}</div>
                                  </div>
                                  <div className="p-1.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800">
                                    <div className="text-zinc-600 dark:text-zinc-400 font-bold">{terminScheme === '50_50' ? 'Staging [F3-5]' : 'Termin 50% [F3-5]'}</div>
                                    <div className="font-black text-zinc-950 dark:text-zinc-100">{terminScheme === '50_50' ? 'Included' : `Rp ${formatIdrPlain(milestone50)}`}</div>
                                  </div>
                                  <div className="p-1.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800">
                                    <div className="text-zinc-600 dark:text-zinc-400 font-bold">{terminScheme === '50_50' ? 'BAST 50%' : 'BAST 20% [F6-7]'}</div>
                                    <div className="font-black text-emerald-800 dark:text-emerald-400">Rp {formatIdrPlain(finalBast)}</div>
                                  </div>
                                </div>
                              ) : wizardMitraTier === 'umkm_starter' ? (
                                <div className="grid grid-cols-2 gap-1.5 font-mono text-[10px] tabular-nums">
                                  <div className="p-1.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800">
                                    <div className="text-zinc-600 dark:text-zinc-400 font-bold">{isEn ? 'Kickoff DP 50%' : 'DP 50% (Kickoff)'}</div>
                                    <div className="font-black text-zinc-950 dark:text-zinc-100">Rp {formatIdrPlain(1875000)}</div>
                                  </div>
                                  <div className="p-1.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800">
                                    <div className="text-zinc-600 dark:text-zinc-400 font-bold">{isEn ? 'UAT Settlement 50%' : 'Pelunasan UAT 50%'}</div>
                                    <div className="font-black text-emerald-800 dark:text-emerald-400">Rp {formatIdrPlain(1875000)}</div>
                                  </div>
                                </div>
                              ) : (
                                <div className="p-2 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/70 dark:text-amber-200 dark:border-amber-700 font-mono text-[10px]">
                                  ✅ {isEn ? '100% Advisory Fee (Rp 2.5M) automatically deducts your 50% DP if you proceed to Full Turnkey MVP.' : '100% Biaya Advisory (Rp 2,5 Jt) otomatis memotong DP 50% apabila Anda lanjut ke Full Turnkey MVP.'}
                                </div>
                              )}

                              {/* 3. RINGKASAN SPESIFIKASI LIVE REAKTIF DARI PANEL KIRI */}
                              <div className="p-2.5 bg-zinc-100/90 dark:bg-zinc-900/90 border border-zinc-300 dark:border-zinc-800 font-mono text-[10px] space-y-1.5">
                                <div className="text-[10px] font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300 flex items-center justify-between">
                                  <span>{isEn ? 'LIVE SPECIFICATION SYNC (FROM LEFT PANEL):' : 'SPESIFIKASI AKTIF (DARI PILIHAN SEBELAH KIRI):'}</span>
                                  <span className="text-amber-900 dark:text-amber-300 font-black">● SPK SHA-256 SYNC</span>
                                </div>
                                <div className="grid grid-cols-2 gap-1.5 text-zinc-900 dark:text-zinc-100">
                                  <div className="p-1.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                    <span className="text-zinc-500 block text-[9px] uppercase font-bold">{isEn ? 'Sector' : 'Sektor Bisnis (Langkah B)'}</span>
                                    <strong className="truncate block font-black text-amber-950 dark:text-amber-300">{wizardSector.code.slice(7, 9)} &bull; {wizardSector.name}</strong>
                                  </div>
                                  <div className="p-1.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                    <span className="text-zinc-500 block text-[9px] uppercase font-bold">{isEn ? 'Architecture Topology' : 'Topologi Stack (Langkah C)'}</span>
                                    <strong className="truncate block font-black">{activeStack.short}</strong>
                                  </div>
                                  <div className="p-1.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                    <span className="text-zinc-500 block text-[9px] uppercase font-bold">{isEn ? 'Frontend Visual UI' : 'Frontend Visual UI (Langkah D)'}</span>
                                    <strong className="truncate block font-black text-cyan-900 dark:text-cyan-300">{activeVisualUi.code}</strong>
                                  </div>
                                  <div className="p-1.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                    <span className="text-zinc-500 block text-[9px] uppercase font-bold">{isEn ? 'Post-BAST Warranty & Pillars' : 'Garansi SLA & Pilar'}</span>
                                    <strong className="truncate block font-black">{isEn ? '90-Day SLA • 20 Pillars' : 'SLA 90 Hari • 20 Pilar'}</strong>
                                  </div>
                                </div>
                              </div>

                              <div className="space-y-2.5 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                                <div>
                                  <label className="block font-mono text-[10px] font-black uppercase tracking-wider text-zinc-800 dark:text-zinc-200 mb-1">
                                    {isEn ? 'STEP F — SELECT MITRA STUDIO CONTRACT *' : 'LANGKAH F — PILIH KONTRAK REKAYASA MITRA STUDIO *'}
                                  </label>
                                  <select
                                    id="wizard-step-f-mitra-package"
                                    value={wizardMitraTier}
                                    onChange={(e) => setWizardMitraTier(e.target.value)}
                                    className="w-full px-2.5 py-2 bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-amber-500 text-zinc-950 dark:text-zinc-100 font-mono text-[11px] font-bold rounded-none focus:border-amber-500 outline-none"
                                  >
                                    {mitraWizardPackages.map((mOpt) => (
                                      <option key={mOpt.key} value={mOpt.key}>
                                        {mOpt.label}
                                      </option>
                                    ))}
                                  </select>
                                </div>

                                <button
                                  type="button"
                                  id="wizard-mitra-lock-contract-btn"
                                  onClick={() => launchSatuPintuCheckout(wizardMitraTier)}
                                  className="w-full py-2.5 px-3 bg-zinc-950 hover:bg-zinc-800 text-amber-300 dark:bg-amber-500 dark:hover:bg-amber-400 dark:text-zinc-950 font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 rounded-none transition cursor-pointer text-center"
                                >
                                  <span>
                                    {isEn
                                      ? `🗓️ Lock ${activeMitraPkg.shortName} (${activeMitraPkg.priceStr}) →`
                                      : `🗓️ Kunci Kontrak ${activeMitraPkg.shortName} (${activeMitraPkg.priceStr}) →`}
                                  </span>
                                </button>

                                <button
                                  type="button"
                                  onClick={() => scrollToCatalogSection('project')}
                                  className="w-full py-2 px-2.5 bg-white hover:bg-zinc-100 text-zinc-950 dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:text-zinc-100 font-mono text-[10px] font-black uppercase tracking-wider border border-zinc-400 dark:border-zinc-700 rounded-none cursor-pointer transition text-center"
                                >
                                  <span>{isEn ? '↓ Compare Mitra Studio Contracts Below' : '↓ Bandingkan Rincian Kontrak Mitra di Bawah'}</span>
                                </button>
                              </div>
                            </div>
                          )}
                        </div>
                      </div>
                    )}
                  </div>
                </div>
              );
            })()}

            {/* 2. DYNAMIC 5-TAB NAVIGATION SWITCHER (RETAIL | STUDIO | 12-SECTOR UI & KILLER OS | MATRIX | FAQ) */}
            <div className="flex justify-center mb-8 sm:mb-10 w-full px-2 sm:px-4">
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 p-1.5 bg-zinc-100 dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-800 rounded-none shadow-xs font-mono text-xs w-full max-w-7xl gap-1.5 sm:gap-2">

                {[
                  {
                    id: 'retail',
                    num: '01',
                    title: isEn ? 'Retail Packages' : 'Paket Retail',
                    sub: '7 SAAS TIERS V12.0',
                    icon: ShoppingBag,
                  },
                  {
                    id: 'project',
                    num: '02',
                    title: isEn ? 'Mitra & Studio' : 'Mitra & Studio',
                    sub: 'TURNKEY ESCROW V12.0',
                    icon: Rocket,
                  },
                  {
                    id: 'sectoral',
                    num: '03',
                    title: isEn ? '12-Sector 36 UI & OS' : '12 Sektor 36 UI & OS',
                    sub: '36 UI + 6 SCROLL V12.0',
                    icon: Terminal,
                  },
                  {
                    id: 'matrix',
                    num: '04',
                    title: isEn ? 'Comparison Matrix' : 'Matriks Perbandingan',
                    sub: isEn ? '20 PILLARS V12.0' : '20 PILAR V12.0',
                    icon: Layers,
                  },
                  {
                    id: 'faq',
                    num: '05',
                    title: isEn ? 'Project OS FAQ' : 'FAQ Project OS',
                    sub: `${faqs.length} ${isEn ? 'FAQS V12.0' : 'FAQ V12.0'}`,
                    icon: HelpCircle,
                  }
                ].map((tab) => {
                  const Icon = tab.icon;
                  const isActive = pricingSectionTab === tab.id;
                  return (
                    <button
                      key={tab.id}
                      type="button"
                      id={`tab-btn-${tab.id}`}
                      onClick={() => switchPricingTab(tab.id)}
                      className={`group px-3.5 py-3 uppercase tracking-wider transition rounded-none flex items-center justify-center gap-2.5 cursor-pointer border-2 ${isActive
                        ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-white dark:text-zinc-950 dark:border-white shadow-sm font-mono'
                        : 'bg-white/70 hover:bg-white text-zinc-700 hover:text-zinc-950 border-zinc-200/90 hover:border-zinc-400 dark:bg-zinc-950/70 dark:hover:bg-zinc-800/80 dark:text-zinc-300 dark:hover:text-white dark:border-zinc-800/90 dark:hover:border-zinc-700 font-mono'
                        }`}
                    >
                      <Icon className={`w-4 h-4 shrink-0 transition-colors ${isActive
                        ? 'text-emerald-400 dark:text-emerald-600'
                        : 'text-zinc-500 dark:text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-white'
                        }`} />
                      <div className="flex flex-col items-start leading-tight text-left">
                        <span className={`whitespace-nowrap font-bold text-xs ${isActive
                          ? 'text-white dark:text-zinc-950 font-black'
                          : 'text-zinc-800 dark:text-zinc-200 group-hover:text-zinc-950 dark:group-hover:text-white'
                          }`}>
                          {tab.num}. {tab.title}
                        </span>
                        <span className={`whitespace-nowrap text-[9px] font-mono font-black tracking-widest uppercase mt-0.5 px-1.5 py-0.2 rounded-none border ${isActive
                          ? 'bg-zinc-800 text-emerald-400 border-zinc-700 dark:bg-emerald-100 dark:text-emerald-950 dark:border-emerald-300'
                          : 'bg-zinc-200/60 text-zinc-600 border-zinc-300/80 dark:bg-zinc-900 dark:text-zinc-400 dark:border-zinc-800 group-hover:text-zinc-800 dark:group-hover:text-zinc-200'
                          }`}>
                          {tab.sub}
                        </span>
                      </div>
                    </button>
                  );
                })}

              </div>
            </div>

            {/* ========================================================================= */}
            {/* TAB 1: RETAIL SOFTWARE FACTORY OS (SELF-SERVICE BLUEPRINTS)               */}
            {/* ========================================================================= */}
            {pricingSectionTab === 'retail' && (
              <motion.div
                key="retail-tab-content"
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.2 }}
                className="mb-16 sm:mb-20"
              >
                {/* 2. PUNCHY & STREAMLINED 100% SELF-SERVICE NOTICE */}
                <div className="mb-6 sm:mb-8 p-3.5 sm:p-4 bg-amber-500/10 border border-amber-500/30 text-zinc-900 dark:text-zinc-100 rounded-none shadow-xs">
                  <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-1.5">
                    <div className="flex items-center gap-2">
                      <span className="px-1.5 py-0.5 bg-amber-500 text-black font-black text-[10px] font-mono rounded-none">
                        {isEn ? '⚠️ IMPORTANT' : '⚠️ PENTING'}
                      </span>
                      <span className="font-mono text-xs font-black uppercase tracking-wider text-amber-600 dark:text-amber-400">
                        {isEn ? 'TIERS 01-04: 100% SELF-SERVICE // ZERO NERIAH PRO CODING' : 'PAKET 01-04: 100% SELF-SERVICE // TANPA KODING DARI NERIAH PRO'}
                      </span>
                    </div>
                    <span className="font-mono text-[10px] px-2 py-0.5 bg-amber-500/20 text-amber-700 dark:text-amber-300 font-bold uppercase rounded-none">
                      {isEn ? 'CLIENT-EXECUTED' : 'DIKERJAKAN OLEH DEVELOPER ANDA'}
                    </span>
                  </div>
                  <p className="text-xs text-zinc-700 dark:text-zinc-300 font-sans leading-relaxed">
                    {isEn
                      ? 'All Instant Blueprint packages below are digital architectural deliverables (PRD, PostgreSQL ULID DDL, Mermaid diagrams, WBS, OpenAPI 3.1) for you and your developers to build and code on your own. No coding or application building by Neriah Pro. For full turnkey coding by Neriah Pro engineers, see Studio Contracts below.'
                      : (pricingSettings.retail_disclaimer || 'Seluruh paket Instant Blueprint di bawah ini adalah cetak biru spesifikasi arsitektur mandiri (100% Self-Service) untuk dikerjakan langsung oleh Anda atau tim developer Anda sendiri. Tidak ada koding atau pembuatan aplikasi oleh Neriah Pro. Butuh tim Neriah Pro yang mengoding aplikasi siap pakai? Pilih Layanan Studio di bagian bawah.')}
                  </p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 items-stretch">
                  {/* TIER 01: SPARK / FREE AUDIT */}
                  <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 p-5 sm:p-6 flex flex-col justify-between rounded-none hover:border-cyan-500/60 transition group relative">
                    <div>
                      <div className="flex items-center justify-between mb-3">
                        <span className="font-mono text-[10px] font-black tracking-wider uppercase text-zinc-700 dark:text-zinc-300">
                          TIER 01 // AUDIT
                        </span>
                        <span className="px-2 py-0.5 bg-cyan-100 text-cyan-950 border border-cyan-400 dark:bg-cyan-950/80 dark:text-cyan-200 dark:border-cyan-700 font-mono text-[9px] font-black">
                          {isEn ? 'FREE GUEST MODE' : 'GRATIS / MODE TAMU'}
                        </span>
                      </div>

                      <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-1.5 font-sans">
                        {isEn ? 'Spark (Idea Audit)' : 'Spark (Audit Ide)'}
                      </h3>

                      <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                        {isEn
                          ? 'Validate business viability, market problem, and core MVP scope in 60 seconds.'
                          : 'Validasi kelayakan ide bisnis, target audiens, dan ruang lingkup dasar MVP dalam 60 detik.'}
                      </p>

                      {/* Compact Price Box */}
                      <div className="mb-4 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 space-y-1.5">
                        <div className="flex items-baseline justify-between">
                          <span className="text-2xl font-black font-mono text-zinc-900 dark:text-white">
                            Rp {pricingSettings.retail_spark_price || '0'}
                          </span>
                          <span className="text-[10px] font-mono font-bold text-emerald-600 dark:text-emerald-400">
                            {isEn ? '100% FREE' : 'GRATIS'}
                          </span>
                        </div>
                        <div className="text-[10px] font-mono text-zinc-500 space-y-0.5 pt-1 border-t border-zinc-200 dark:border-zinc-800">
                          <div>&bull; {isEn ? '2 Audits / month (Resets 1st of month)' : (pricingSettings.retail_spark_limit || '2x Audit / bulan (Reset tgl 1)')}</div>
                          <div>&bull; {isEn ? 'Guest mode (No login required)' : 'Mode tamu (Langsung coba tanpa login)'}</div>
                        </div>
                      </div>

                      {/* Features */}
                      <div className="space-y-2 mb-6">
                        <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                          {isEn ? 'DELIVERABLES INCLUDED:' : 'OUTPUT YANG DIDAPATKAN:'}
                        </span>
                        {(isEn ? [
                          'Business Viability & Problem Framing',
                          'Top 5 Priority Essential MVP Features',
                          'Complexity Rating & Development Timeline',
                          'Export Markdown Summary to Device (.md)',
                        ] : [
                          'Analisis kelayakan & validasi masalah bisnis',
                          '5 Rekomendasi fitur prioritas utama MVP',
                          'Estimasi waktu & kompleksitas sistem',
                          'Ekspor ringkasan dokumen Markdown (.md)',
                        ]).map((f, i) => (
                          <div key={i} className="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                            <Check className="w-3.5 h-3.5 text-cyan-500 shrink-0 mt-0.5" />
                            <span className="text-[11px] leading-tight">{formatPillar(f)}</span>
                          </div>
                        ))}
                      </div>
                    </div>

                    <div>
                      <div className="text-[10px] font-mono text-zinc-400 text-center mb-3">
                        {isEn ? '100% Self-Service (Coded by your team)' : '100% Mandiri (Koding oleh tim Anda sendiri)'}
                      </div>
                      <a
                        href="/blueprint?tier=spark"
                        className="w-full bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-white py-2.5 px-3 font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none text-center"
                      >
                        <span>{isEn ? 'TRY LIVE FREE (GUEST)' : 'COBA GRATIS SEKARANG'}</span>
                        <ArrowRight className="w-3 h-3" />
                      </a>
                    </div>
                  </div>

                  {/* TIER 02: STARTER AI ARCHITECT */}
                  <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 p-5 sm:p-6 flex flex-col justify-between rounded-none hover:border-cyan-500/60 transition group relative">
                    <div>
                      <div className="flex items-center justify-between mb-3">
                        <span className="font-mono text-[10px] font-black tracking-wider uppercase text-zinc-700 dark:text-zinc-300">
                          TIER 02 // ESSENTIAL
                        </span>
                        <span className="px-2 py-0.5 bg-zinc-200 text-zinc-950 border border-zinc-300 dark:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 font-mono text-[9px] font-black">
                          SOLO / DEV
                        </span>
                      </div>

                      <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-1.5 font-sans">
                        {isEn ? 'Starter AI Architect' : 'Starter AI Architect'}
                      </h3>

                      <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                        {isEn
                          ? 'AI-generated 26-parameter PRD document and ready-to-import SQL database schema.'
                          : 'Dokumen PRD 26 parameter hasil generasi AI dan skema database SQL siap import untuk startup Anda.'}
                      </p>

                      {/* Compact Price Box */}
                      <div className="mb-4 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 space-y-1.5">
                        <div className="flex items-baseline justify-between">
                          <span className="text-2xl font-black font-mono text-zinc-900 dark:text-white">
                            Rp {pricingSettings.retail_lite_price || '99.000'}
                          </span>
                          <span className="text-[10px] font-mono text-zinc-500">
                            {isEn ? '/ project' : '/ proyek'}
                          </span>
                        </div>
                        <div className="text-[10px] font-mono text-zinc-500 space-y-0.5 pt-1 border-t border-zinc-200 dark:border-zinc-800">
                          <div>&bull; {isEn ? 'One-time license • Lifetime download' : 'Lisensi sekali bayar • Unduh selamanya'}</div>
                          <div>&bull; {isEn ? '30-day form parameter revision' : (pricingSettings.retail_lite_limit || 'Akses revisi form 30 hari')}</div>
                        </div>
                      </div>

                      {/* Inclusive Header & Features */}
                      <div className="space-y-2 mb-6">
                        <div className="p-2 mb-2 bg-zinc-100 dark:bg-zinc-800 border-l-2 border-cyan-500 text-zinc-800 dark:text-zinc-200 font-mono text-[10px] font-bold">
                          ⭐ {isEn ? 'Includes all Spark Free features, plus:' : 'Mencakup seluruh fitur Spark, ditambah:'}
                        </div>
                        {(isEn ? [
                          'Pillar 1 (Core Spec): Full 26-Parameter PRD Document (Chapters 1-6)',
                          'Pillar 3 (Database Schema): PostgreSQL Strict ULID DDL SQL (Ready-to-import)',
                          'Performance Standard: O(1) Keyset & Cursor Pagination Directives',
                          '6 AI Agent Rules (.cursorrules, CLAUDE.md, AGENTS.md, Codex, Windsurf, Copilot)',
                          'One-Command Workspace Bootstrapper: curl -sL /init/{ulid} | bash',
                          'Execution Roadmap: 2 Sprints Work Breakdown Structure (Linear / Jira ready)',
                          'Official Deliverable: Licensed PDF & Markdown Export with SHA-256 Hash',
                        ] : [
                          'Pilar 1 (Spesifikasi Inti): Dokumen PRD lengkap 26 parameter (Bab 1–6)',
                          'Pilar 3 (Skema Database): SQL PostgreSQL ULID presisi (Siap diimpor)',
                          'Standar Kinerja: Panduan kueri cepat Keyset & Cursor Pagination O(1)',
                          'Aturan 6 Agen AI Koding (.cursorrules, CLAUDE.md, AGENTS.md, Codex, Windsurf, Copilot)',
                          'Bootstrapper 1-Perintah Terminal: curl -sL /init/{ulid} | bash',
                          'Peta Kerja Eksekusi: Rencana kerja 2 sprint terstruktur (Linear / Jira ready)',
                          'Ekspor Dokumen Resmi: Berkas PDF & Markdown berlisensi hash SHA-256',
                        ]).map((f, i) => (
                          <div key={i} className="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                            <Check className="w-3.5 h-3.5 text-cyan-500 shrink-0 mt-0.5" />
                            <span className="text-[11px] leading-tight">{formatPillar(f)}</span>
                          </div>
                        ))}
                      </div>
                    </div>

                    <div>
                      <div className="text-[10px] font-mono text-zinc-400 text-center mb-3">
                        {isEn ? '100% Self-Service (Coded by your team)' : '100% Mandiri (Koding oleh tim Anda sendiri)'}
                      </div>
                      <button
                        type="button"
                        onClick={() => openBookingModal('retail_lite')}
                        className="w-full bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black py-2.5 px-3 font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none cursor-pointer"
                      >
                        <span>{isEn ? 'GET STARTER PACK' : 'PILIH PAKET STARTER'}</span>
                        <ArrowRight className="w-3 h-3" />
                      </button>
                    </div>
                  </div>

                  {/* TIER 03: PRO PRODUCTION (BEST VALUE) */}
                  <div className="bg-white dark:bg-zinc-900 border-2 border-emerald-500 p-5 sm:p-6 flex flex-col justify-between rounded-none shadow-xl relative transform xl:-translate-y-1">
                    <div className="absolute -top-3 left-1/2 -translate-x-1/2 bg-emerald-500 text-black px-3 py-0.5 font-mono text-[9px] font-black uppercase tracking-widest shadow-xs">
                      {isEn ? 'BEST VALUE // DEVELOPER FAVORITE' : 'PILIHAN TERBAIK // FAVORIT DEVELOPER'}
                    </div>

                    <div>
                      <div className="flex items-center justify-between mb-3 mt-1">
                        <span className="font-mono text-[10px] font-black tracking-wider uppercase text-emerald-800 dark:text-emerald-400">
                          TIER 03 // PRODUCTION
                        </span>
                        <span className="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[9px] font-black">
                          {isEn ? 'STARTUP & AGENCY' : 'STARTUP & AGENSI'}
                        </span>
                      </div>

                      <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-1.5 font-sans">
                        {isEn ? 'Pro Agentic Blueprint' : 'Pro Agentic Blueprint'}
                      </h3>

                      <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                        {isEn
                          ? 'Complete 7-pillar OS blueprint with Docker, system diagrams, and strict AI coding rules.'
                          : 'Cetak biru OS 7-pilar lengkap dengan Docker, diagram sistem, dan instruksi AI agentic yang ketat.'}
                      </p>

                      {/* Compact Price Box */}
                      <div className="mb-4 p-3 bg-emerald-500/5 border border-emerald-500/30 space-y-1.5">
                        <div className="flex items-baseline justify-between">
                          <span className="text-2xl font-black font-mono text-zinc-900 dark:text-white">
                            Rp {pricingSettings.retail_pro_price || '399.000'}
                          </span>
                          <span className="text-[10px] font-mono text-zinc-500">
                            {isEn ? '/ project' : '/ proyek'}
                          </span>
                        </div>
                        <div className="text-[10px] font-mono text-zinc-500 space-y-0.5 pt-1 border-t border-emerald-500/20">
                          <div>&bull; {isEn ? 'One-time license • Lifetime download' : 'Lisensi sekali bayar • Unduh selamanya'}</div>
                          <div>&bull; {isEn ? '6 Months AI regeneration access' : (pricingSettings.retail_pro_limit || 'Akses regenerasi AI 6 bulan')}</div>
                        </div>
                      </div>

                      {/* Inclusive Header & Features */}
                      <div className="space-y-2 mb-6">
                        <div className="p-2 mb-2 bg-emerald-100 dark:bg-emerald-950/80 border-l-2 border-emerald-600 dark:border-emerald-400 text-emerald-950 dark:text-emerald-200 font-mono text-[10px] font-black">
                          ⭐ {isEn ? 'Includes all Starter AI Architect features, plus:' : 'Mencakup seluruh fitur Starter AI Architect, ditambah:'}
                        </div>
                        {(isEn ? [
                          'Pillar 3 (Scaffold): Production Docker Compose Starter Kit (22+ Files, PG16 ULID, Redis, Nginx)',
                          'Pillar 4 (Seeder): Synthetic Mock Data Seeder Engine (100+ Realistic Records)',
                          'Pillar 5 (AI Agent): Full 6-Engine AI Coding Agent Directives (Antigravity, Claude, Codex, Cursor)',
                          'Workspace Auto-Save: Isolated Custom Folder Name with Cloud Auto-Sync per Project',
                          'Download Center: 24-Hour Asynchronous Artifact Queue (ZIP, PDF, MD) with Countdown Ticker',
                          'Context Architecture: Tri-Hybrid Memory (CAG Prompt Caching O(1) + RAG Dense Retrieval)',
                          'ECC Universal v2.2.3 Ecosystem: Multi-Agent Council & Continuous Learning Harness',
                          'Visual System Blueprint: 6 Complete Diagrams (Architecture, ERD, Data Flow, Sequence)',
                          'Change Request (CR) / Addendum Module: Integrated form for scope updates',
                          'API Foundation: Pre-built RESTful API Routes (Laravel 13 & Next.js App Router)',
                          'White-Label License: Full rights to re-brand and deliver directly to your clients',
                        ] : [
                          'Pilar 3 (Rangka Koding): Starter Kit Docker Compose 22+ Berkas Lengkap & Ekspor ZIP Instan',
                          'Pilar 4 (Data Awal): Generator data awal sintetis (Synthetic Data Seeder 100+ baris)',
                          'Pilar 5 (Panduan AI): Aturan 6 Agen AI Koding Lengkap (Antigravity IDE, Claude, Codex, Cursor)',
                          'Auto-Save Workspace: Nama Folder Kustom Terisolasi dengan Auto-Save Cloud Per Proyek',
                          'Download Center: Antrean Berkas Asinkron 24 Jam (ZIP, PDF, MD) dengan Countdown Ticker',
                          'Arsitektur Konteks: Memori Tri-Hybrid (CAG Prompt Caching O(1) + RAG Vektor Dense)',
                          'Ekosistem ECC v2.2.3: Multi-Agent Council, Continuous Learning & Eval Harness',
                          'Cetak Biru Visual: 6 Diagram sistem lengkap (Arsitektur, ERD, Alur Data, Sequence)',
                          'Modul Change Request (CR) / Addendum: Formulir resmi pengajuan revisi scope',
                          'Fondasi API: Rute API RESTful siap pakai (Laravel 13 & Next.js App Router)',
                          'Lisensi Bebas Merek: Hak penuh re-brand dan serahkan langsung ke klien Anda',
                        ]).map((f, i) => (
                          <div key={i} className="flex items-start gap-2 text-xs text-zinc-800 dark:text-zinc-200 font-medium">
                            <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                            <span className="text-[11px] leading-tight">{formatPillar(f)}</span>
                          </div>
                        ))}
                      </div>
                    </div>

                    <div>
                      <div className="text-[10px] font-mono text-zinc-400 text-center mb-3">
                        {isEn ? '100% Self-Service (Coded by your team)' : '100% Mandiri (Koding oleh tim Anda sendiri)'}
                      </div>
                      <button
                        type="button"
                        onClick={() => openBookingModal('retail_pro')}
                        className="w-full bg-emerald-500 hover:bg-emerald-400 text-black py-2.5 px-3 font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none shadow-md cursor-pointer"
                      >
                        <span>{isEn ? 'GET PRO BLUEPRINT' : 'PILIH PAKET PRO'}</span>
                        <ArrowRight className="w-3 h-3" />
                      </button>
                    </div>
                  </div>

                  {/* TIER 04: ULTIMATE SOFTWARE FACTORY OS */}
                  <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 p-5 sm:p-6 flex flex-col justify-between rounded-none hover:border-amber-500/60 transition group relative">
                    <div>
                      <div className="flex items-center justify-between mb-3">
                        <span className="font-mono text-[10px] font-black tracking-wider uppercase text-zinc-700 dark:text-zinc-300">
                          TIER 04 // ULTIMATE ARCHITECTURE
                        </span>
                        <span className="px-2 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700 font-mono text-[9px] font-black">
                          {formatPillar(isEn ? 'PARIPURNA 20-PILLAR OS V12.0' : 'PARIPURNA 20 PILAR OS V12.0')}
                        </span>
                      </div>

                      <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-1.5 font-sans">
                        {isEn ? 'Ultimate Architecture Blueprint OS' : 'Ultimate Architecture Blueprint OS'}
                      </h3>

                      <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                        {isEn
                          ? 'Complete 20-pillar architecture with Pest & Playwright automated testing, GitHub Actions CI/CD, KAG Knowledge Graph, Health Probes, and 1-on-1 Architect session.'
                          : 'Arsitektur lengkap 20-pilar dengan pengujian Pest & Playwright otomatis, GitHub Actions CI/CD, KAG Knowledge Graph, Health Probes, dan sesi konsultasi 1-on-1 dengan Arsitek.'}
                      </p>

                      {/* Compact Price Box */}
                      <div className="mb-4 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 space-y-1.5">
                        <div className="flex items-baseline justify-between">
                          <span className="text-2xl font-black font-mono text-zinc-900 dark:text-white">
                            Rp {pricingSettings.retail_ultimate_price || '1.490.000'}
                          </span>
                          <span className="text-[10px] font-mono text-zinc-500">
                            {isEn ? '/ project' : '/ proyek'}
                          </span>
                        </div>
                        <div className="text-[10px] font-mono text-zinc-500 space-y-0.5 pt-1 border-t border-zinc-200 dark:border-zinc-800">
                          <div>&bull; {isEn ? 'One-time license • Lifetime download' : 'Lisensi sekali bayar • Unduh selamanya'}</div>
                          <div>&bull; {isEn ? '1 Year priority architecture updates' : (pricingSettings.retail_ultimate_limit || 'Pembaruan prioritas 1 tahun')}</div>
                        </div>
                      </div>

                      {/* Inclusive Header & Features */}
                      <div className="space-y-2 mb-6">
                        <div className="p-2 mb-2 bg-amber-100 dark:bg-amber-950/80 border-l-2 border-amber-500 dark:border-amber-400 text-amber-950 dark:text-amber-200 font-mono text-[10px] font-black">
                          ⭐ {isEn ? 'Includes all Pro Blueprint features, plus:' : 'Mencakup seluruh fitur Pro Blueprint, ditambah:'}
                        </div>
                        {(isEn ? [
                          'Pillars P01–P03 (Core): 12 Sectors, 9 Stacks, Strict ULID & Keyset Cursor O(1)',
                          'Pillars P04–P06 (UI/UX): 36 Operational Cockpits + 6 60fps Scrollytelling Presets (Zone A + B)',
                          'Pillars P07–P08 (AI Engine): KAG Knowledge Graph + CAG Prompt Caching + 49 Native Tools Calling',
                          'Pillars P09–P11 (Security): AgentShield Red-Team, SHA-256 Audit Trail Chain & UU PDP Vault',
                          'Pillars P12–P15 (SaaS): One-Stop Commercial Gate, Escrow Midtrans Snap & 90-Day SLA Warranty',
                          'Pillars P16–P17 (Authority): 12-Sector Schema.org JSON-LD & Native /llms.txt AI Citation Engine',
                          'Pillars P18–P20 (DevOps): Offline PWA Outbox, Pre-Migration pg_dump Guard & 1-Click Rollback',
                          'Executive 1-on-1 Call: 60-Minute Direct Strategic Session with Principal Architect',
                          'Legal Security: Official Corporate Non-Disclosure Agreement (NDA) with Digital Signature',
                        ] : [
                          'Pilar P01–P03 (Arsitektur Inti): 12 Sektor, 9 Stack, Strict ULID & Keyset Cursor O(1)',
                          'Pilar P04–P06 (Desain UI/UX): 36 Cockpit Operasional + 6 Preset Scrollytelling 60fps (Zona A & B)',
                          'Pilar P07–P08 (Mesin AI): KAG Knowledge Graph + CAG Prompt Caching + 49 Native Tool Calling',
                          'Pilar P09–P11 (Keamanan): AgentShield Red-Team, Rantai Audit SHA-256 & Brankas UU PDP PII',
                          'Pilar P12–P15 (Komersial): Gerbang Satu Pintu, Escrow Midtrans Snap & Garansi SLA 90 Hari',
                          'Pilar P16–P17 (Otoritas): Schema.org Multi-Graph JSON-LD & Mesin Sitasi AI /llms.txt',
                          'Pilar P18–P20 (DevOps & Ops): Offline PWA Outbox, Guard pg_dump Pra-Migrasi & 1-Click Rollback',
                          'Sesi Eksekutif 1-on-1: 60 Menit Konsultasi Strategis Langsung bersama Principal Architect',
                          'Perlindungan Hukum: Surat Perjanjian Kerahasiaan (NDA) resmi bertanda tangan digital',
                        ]).map((f, i) => (
                          <div key={i} className="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                            <Check className="w-3.5 h-3.5 text-amber-500 shrink-0 mt-0.5" />
                            <span className="text-[11px] leading-tight">{formatPillar(f)}</span>
                          </div>
                        ))}
                      </div>
                    </div>

                    <div>
                      <div className="text-[10px] font-mono text-zinc-400 text-center mb-3">
                        {isEn ? 'Self-Service + 1-on-1 Architect Session' : 'Mandiri + 1 Sesi Konsultasi Arsitek'}
                      </div>
                      <button
                        type="button"
                        onClick={() => openBookingModal('retail_ultimate')}
                        className="w-full bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black py-2.5 px-3 font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none cursor-pointer"
                      >
                        <span>{isEn ? 'BOOK ULTIMATE ADVISORY' : 'PESAN ULTIMATE ADVISORY'}</span>
                        <ArrowRight className="w-3 h-3" />
                      </button>
                    </div>
                  </div>
                </div>

                {/* V8.0 INCLUSIVE HYBRID VALUE-METRIC TIERS (CSR GRANT, SINGLE-APP UMKM PASS, AGENCY WHITE-LABEL) */}
                <div className="mt-8 border-2 border-zinc-900 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 sm:p-6 rounded-none space-y-5">
                  <div className="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-zinc-200 dark:border-zinc-800">
                    <div>
                      <span className="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black uppercase">
                        INCLUSIVE HYBRID VALUE-METRIC MODEL V8.0
                      </span>
                      <h3 className="text-base sm:text-lg font-black text-zinc-950 dark:text-white uppercase tracking-tight mt-1">
                        {isEn
                          ? 'Inclusive Access: Non-Profit CSR Grant, Pay-Per-App UMKM Pass & Agency White-Label'
                          : 'Akses Inklusif: Hibah CSR Yayasan/NGO, Pas Sekali Bayar UMKM & Lisensi Agensi White-Label'}
                      </h3>
                    </div>
                    <span className="font-mono text-[10px] font-black text-zinc-700 dark:text-zinc-300 uppercase">
                      {isEn ? 'Zero Subscription Fatigue • Canary IP Protected' : 'Tanpa Jebakan Langganan Bulanan • Proteksi IP Canary'}
                    </span>
                  </div>

                  <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    {/* 1. Impact & Non-Profit Grant */}
                    <div className="border-2 border-emerald-500/80 dark:border-emerald-600 bg-zinc-50 dark:bg-zinc-950 p-4 flex flex-col justify-between">
                      <div className="space-y-2.5">
                        <div className="flex items-center justify-between gap-2 font-mono text-[10px] font-black uppercase">
                          <span className="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700">
                            {isEn ? '100% CSR SUBSIDY' : 'SUBSIDI CSR 100%'}
                          </span>
                          <span className="text-emerald-900 dark:text-emerald-300 font-bold">ISAK 35 / ASC 958</span>
                        </div>
                        <div className="font-black text-sm text-zinc-950 dark:text-white">
                          {isEn ? 'Impact, NGO & Non-Profit Grant' : 'Hibah Yayasan, NGO, Rumah Ibadah & Riset'}
                        </div>
                        <div className="space-y-1">
                          <div className="flex items-baseline gap-2">
                            <span className="text-xs line-through text-zinc-500 font-mono font-bold">
                              Rp 1.490.000
                            </span>
                            <span className="text-xl font-black font-mono text-emerald-900 dark:text-emerald-300 tabular-nums">
                              Rp {pricingSettings.impact_grant_price || '0'}
                            </span>
                            <span className="text-[10px] font-mono font-bold text-zinc-600 dark:text-zinc-400">
                              {isEn ? '(Subsidized to Rp 0)' : '(Disubsidi Rp 0 Penuh)'}
                            </span>
                          </div>
                          <div className="p-1.5 bg-emerald-100/60 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-[10px] font-mono text-emerald-950 dark:text-emerald-200 font-bold">
                            {isEn
                              ? '📋 Legal Document Required: Akta Notaris / SK Kemenkumham / NGO Decree'
                              : '📋 Syarat Wajib: Verifikasi Akta Notaris / SK Kemenkumham Lembaga Sosial'}
                          </div>
                        </div>
                        <p className="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed">
                          {isEn
                            ? 'Special CSR allocation from Neriah Pro for non-commercial social institutions, mosques/churches, orphanages, and campus research: Full Restricted vs Unrestricted Fund Accounting blueprint (ISAK 35/PSAK 109), donor SHA-256 transparency ledger, and statutory reporting.'
                            : 'Alokasi khusus program CSR Neriah Pro untuk lembaga sosial non-komersial, rumah ibadah, panti asuhan, dan riset kampus: Blueprint Buku Besar Dana Terikat vs Bebas (ISAK 35/PSAK 109), portal transparansi donatur SHA-256, dan pelaporan akuntansi nirlaba.'}
                        </p>
                      </div>
                      <button
                        type="button"
                        onClick={() => openBookingModal('impact_nonprofit_grant', 'CSR-IMPACT-100')}
                        className="mt-4 w-full py-2.5 px-3 bg-emerald-600 hover:bg-emerald-500 text-white font-mono text-xs font-black uppercase tracking-wider rounded-none transition cursor-pointer"
                      >
                        {isEn ? 'Apply CSR Grant Access (Rp 0) →' : 'Ajukan Hibah CSR Non-Profit (Rp 0) →'}
                      </button>
                    </div>

                    {/* 2. Single-App UMKM Pass */}
                    <div className="border-2 border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950 p-4 flex flex-col justify-between">
                      <div className="space-y-2.5">
                        <div className="flex items-center justify-between gap-2 font-mono text-[10px] font-black uppercase">
                          <span className="px-2 py-0.5 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950">
                            PAY-PER-PROJECT
                          </span>
                          <span className="text-zinc-700 dark:text-zinc-300">{isEn ? 'NO MONTHLY FEE' : 'SEKALI BAYAR'}</span>
                        </div>
                        <div className="font-black text-sm text-zinc-950 dark:text-white">
                          Single-App UMKM Pass
                        </div>
                        <div className="text-xl font-black font-mono text-zinc-950 dark:text-white tabular-nums">
                          Rp {pricingSettings.single_app_pass_price || '299.000'} <span className="text-xs font-bold text-zinc-600 dark:text-zinc-400">/ {isEn ? '1 App Forever' : '1 Aplikasi Penuh'}</span>
                        </div>
                        <p className="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed">
                          {isEn
                            ? 'Pay only when launching a business app: 1 complete production blueprint, 36 Visual Sector UI selection, PostgreSQL 16 ULID SQL, 7-Phase AI Prompts, & Zero-Slop Certified Badge.'
                            : 'Bayar hanya saat membangun 1 aplikasi usaha: cetak biru produksi utuh, pilihan 36 UI Visual Sektoral, SQL ULID PostgreSQL 16, 7-Fase Prompt AI, & Lencana Zero-Slop Certified.'}
                        </p>
                      </div>
                      <button
                        type="button"
                        onClick={() => openBookingModal('single_app_umkm_pass')}
                        className="mt-4 w-full py-2.5 px-3 bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-950 font-mono text-xs font-black uppercase tracking-wider rounded-none transition cursor-pointer"
                      >
                        {isEn ? 'Buy Single-App Pass →' : 'Beli Pas 1 Aplikasi UMKM →'}
                      </button>
                    </div>

                    {/* 3. Agency Partner & White-Label Studio */}
                    <div className="border-2 border-amber-500/80 dark:border-amber-600 bg-zinc-50 dark:bg-zinc-950 p-4 flex flex-col justify-between">
                      <div className="space-y-2.5">
                        <div className="flex items-center justify-between gap-2 font-mono text-[10px] font-black uppercase">
                          <span className="px-2 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700">
                            AGENCY B2B2C • 5 SEATS
                          </span>
                          <span className="text-amber-900 dark:text-amber-300">WHITE-LABEL PDF</span>
                        </div>
                        <div className="font-black text-sm text-zinc-950 dark:text-white">
                          Agency Partner & White-Label Studio
                        </div>
                        <div className="text-xl font-black font-mono text-zinc-950 dark:text-white tabular-nums">
                          Rp {pricingSettings.agency_whitelabel_price || '990.000'} <span className="text-xs font-bold text-zinc-600 dark:text-zinc-400">/ {isEn ? 'Multi-Client Pack' : 'Paket Agensi'}</span>
                        </div>
                        <p className="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed">
                          {isEn
                            ? 'Export official A4 BoQ/RAB, SPK Contract, UAT & Digital BAST branded with YOUR own Software House Logo & Legal Entity + 70:30 Marketplace Revenue Share & 1-Click MCP Revoke.'
                            : 'Ekspor Proposal RAB, Kontrak SPK, UAT & BAST Digital A4 resmi ber-Logo & Nama PT Software House Anda sendiri + Bagi Hasil 70:30 Marketplace Primitif & 1-Klik Cabut Token MCP.'}
                        </p>
                      </div>
                      <button
                        type="button"
                        onClick={() => openBookingModal('agency_whitelabel_partner')}
                        className="mt-4 w-full py-2.5 px-3 bg-amber-500 hover:bg-amber-400 text-zinc-950 font-mono text-xs font-black uppercase tracking-wider rounded-none transition cursor-pointer"
                      >
                        {isEn ? 'Activate Agency White-Label →' : 'Aktifkan Lisensi Agensi White-Label →'}
                      </button>
                    </div>
                  </div>

                  {/* 5 Retention Hooks + 5-Layer Banking-Grade IP Security Strip */}
                  <div className="grid grid-cols-2 sm:grid-cols-5 gap-2 pt-3 border-t border-zinc-200 dark:border-zinc-800 font-mono text-[10px]">
                    {[
                      { tag: 'HOOK 01', text: isEn ? 'Living CVE Alerts + 1-Click Patch' : 'Notifikasi CVE + Prompt Patch 1-Klik' },
                      { tag: 'HOOK 02', text: isEn ? 'AES-256 Custom Logic Vault' : 'Brankas AES-256 Logika Rahasia Klien' },
                      { tag: 'HOOK 03', text: isEn ? 'Zero-Width Canary IP Watermark' : 'Watermark Forensik Canary Anti-Bajak' },
                      { tag: 'HOOK 04', text: isEn ? '1-Click Scoped MCP Token Revoke' : '1-Klik Cabut Token MCP Freelancer' },
                      { tag: 'HOOK 05', text: isEn ? '70:30 Verified Marketplace' : 'Marketplace Modul Komunitas 70:30' },
                    ].map((hk) => (
                      <div key={hk.tag} className="p-2 bg-zinc-100 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800">
                        <span className="font-black text-emerald-900 dark:text-emerald-300 block">{hk.tag}</span>
                        <span className="font-bold text-zinc-800 dark:text-zinc-200 leading-tight block mt-0.5">{hk.text}</span>
                      </div>
                    ))}
                  </div>
                </div>

                {/* Quick Switcher to Studio */}
                <div className="mt-8 p-4 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                  <div className="flex items-center gap-3">
                    <Rocket className="w-5 h-5 text-emerald-500 shrink-0" />
                    <div className="text-xs">
                      <span className="font-bold text-zinc-900 dark:text-white block font-sans">
                        {isEn ? 'Need our engineers to code and build the entire system turnkey?' : 'Butuh tim Neriah Pro yang mengoding dan mendeploy sistem 100% turnkey?'}
                      </span>
                      <span className="text-zinc-500 text-[11px]">
                        {isEn ? 'Explore our dedicated Full MVP Monolith & UMKM Starter packages.' : 'Pelajari paket Full Monolith MVP & Program Subsidi UMKM Starter kami.'}
                      </span>
                    </div>
                  </div>
                  <button
                    type="button"
                    onClick={() => switchPricingTab('project')}
                    className="px-4 py-2 bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black font-mono text-xs font-bold uppercase tracking-wider rounded-none shrink-0 transition cursor-pointer"
                  >
                    {isEn ? 'View Studio Packages →' : 'Lihat Paket Studio →'}
                  </button>
                </div>
              </motion.div>
            )}

            {/* ========================================================================= */}
            {/* TAB 2: NERIAH PRO CUSTOM ENGINEERING STUDIO (FULL DEVELOPMENT CONTRACTS) */}
            {/* ========================================================================= */}
            {pricingSectionTab === 'project' && (
              <motion.div
                key="project-tab-content"
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.2 }}
                className="mb-20"
              >
                <div className="text-center max-w-3xl mx-auto mb-8 sm:mb-10">
                  <div className="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-mono text-xs uppercase tracking-wider font-bold mb-2.5 rounded-none">
                    <Crown className="w-3.5 h-3.5" />
                    <span>{isEn ? 'NERIAH PRO STUDIO // TURNKEY CONTRACTS' : 'NERIAH PRO STUDIO // KONTRAK TURNKEY'}</span>
                  </div>
                  <h2 className="text-xl sm:text-3xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2 font-sans">
                    {isEn ? 'Turnkey Software Engineering by Senior Architects' : 'Kontrak Rekayasa Software & Kode oleh Tim Senior Architect'}
                  </h2>
                  <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed max-w-2xl mx-auto mb-4">
                    {isEn
                      ? 'Full turnkey production development across all 20 Pillars of Software Factory OS v12.0. Includes Midtrans Snap Escrow (50/50 & 3-Milestone), SHA-256 Scope Lock, dedicated VPS hardening, isolated sprint batches (Anti-Collision), and a full 90-day SLA bug warranty.'
                      : 'Pengerjaan kode produksi turnkey (20 Pilar Software Factory OS v12.0) langsung oleh tim Senior Architect Neriah Pro. Dilengkapi Escrow Midtrans Snap (50/50 & 3-Milestone), Kunci Lingkup SHA-256, kapasitas sprint terisolasi (Anti-Collision), dedicated cloud VPS, dan garansi bug SLA 90 hari penuh.'}
                  </p>
                  <div className="inline-flex flex-wrap items-center justify-center gap-2 text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300">
                    <span className="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700">
                      {isEn ? '⚡ 100% Turnkey Code' : '⚡ 100% Kode Turnkey'}
                    </span>
                    <span className="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700">
                      {isEn ? '🛡️ Scheduled Batches (Anti-Collision)' : '🛡️ Batch Terjadwal (Anti-Collision)'}
                    </span>
                    <span className="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700">
                      {isEn ? '🔒 3-Month SLA Warranty' : '🔒 Garansi SLA 3 Bulan'}
                    </span>
                  </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 items-stretch">
                  {/* TIER 1: ADVISORY ONLY / ARCHITECTURE BLUEPRINT */}
                  <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-800 p-6 sm:p-7 flex flex-col justify-between rounded-none shadow-xs hover:border-emerald-500/60 transition group relative">
                    <div>
                      <div className="flex items-center justify-between mb-3">
                        <span className="font-mono text-xs font-black tracking-wider uppercase text-zinc-700 dark:text-zinc-300">
                          {isEn ? 'SCENARIO 1 // ADVISORY' : 'SKENARIO 1 // ADVISORY'}
                        </span>
                        <span className="px-2 py-0.5 bg-zinc-200 text-zinc-950 border border-zinc-300 dark:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 font-mono text-[10px] font-black">
                          {isEn ? 'ARCHITECTURE ONLY (ZERO CODING)' : 'CETAK BIRU TEKNIS (TANPA KODING)'}
                        </span>
                      </div>

                      <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2 font-sans">
                        {isEn ? 'Blueprint & PRD Advisory' : 'Blueprint & PRD Advisory'}
                      </h3>

                      <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                        {isEn
                          ? 'Technical architectural blueprint for founders & CTOs planning complex custom enterprise systems (SaaS, platforms, fintech) with their own dev team. Neriah Pro does not write code for this advisory tier.'
                          : 'Cetak biru arsitektur enterprise untuk founder & CTO yang merancang sistem custom kompleks (SaaS, marketplace, fintech) bersama tim dev sendiri. Tanpa jasa koding dari Neriah Pro.'}
                      </p>

                      {/* Price & DP Credit Guarantee Box */}
                      <div className="mb-5 p-3.5 bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 space-y-2">
                        <div className="flex items-baseline justify-between">
                          <span className="text-2xl sm:text-3xl font-black font-mono text-zinc-900 dark:text-white">
                            Rp {pricingSettings.advisory_price || '2.500.000'}
                          </span>
                          <span className="text-xs font-mono text-zinc-500">{isEn ? '/ project' : '/ proyek'}</span>
                        </div>
                        <div className="p-2 bg-emerald-100 dark:bg-emerald-950/80 border-l-2 border-emerald-600 dark:border-emerald-400 text-emerald-950 dark:text-emerald-200 font-mono text-[10px] font-black">
                          ⭐ {isEn ? 'Advisory fee 100% deducted from DP if continuing to Full MVP!' : 'Biaya Rp 2.5 jt otomatis memotong DP jika lanjut ke Full MVP!'}
                        </div>
                      </div>

                      {/* Features */}
                      <div className="space-y-2.5 mb-6">
                        <div className="font-mono text-[11px] font-bold text-zinc-400 uppercase tracking-wider">
                          {isEn ? 'DELIVERABLES INCLUDED:' : 'OUTPUT YANG DIDAPATKAN:'}
                        </div>
                        {(isEn ? [
                          'Pillar 1 (PRD & Spec): Comprehensive 26-Parameter PRD Document (Chapters 1-6) + Interactive Mermaid Charts',
                          'Pillar 3 (Database Architecture): PostgreSQL 16 Strict ULID Database Schema & Keyset Cursor O(1) DDL SQL',
                          'Pillar 5 (AI Agent Rules): Standard AI Coding Agent Directives (.cursorrules, AGENTS.md) for internal devs',
                          'Execution Roadmap: Work Breakdown Structure (WBS) 5 Sprints & Master Mermaid Gantt Chart',
                          'Executive Scoping: 1-on-1 Technical Scoping Session with Principal Architect (Google Meet)',
                          '100% DP Credit: Fee Rp 2.5M automatically deducts 50% DP if upgrading to Full MVP!',
                          'Corporate Non-Disclosure Agreement (NDA) & 100% Client Intellectual Property',
                        ] : [
                          'Pilar 1 (PRD & Kontrak): Dokumen PRD Arsitektur Lengkap 26 Parameter (Bab 1–6) + Diagram Alur Mermaid',
                          'Pilar 3 (Fondasi Data): Skema Basis Data PostgreSQL 16 Strict ULID & Keyset Cursor O(1) DDL SQL Siap Import',
                          'Pilar 5 (Panduan AI): Standar Aturan Agen AI (.cursorrules, AGENTS.md) untuk tim developer internal Anda',
                          'Peta Kerja Eksekusi: Work Breakdown Structure (WBS) 5 Sprint & Visualisasi Master Mermaid Gantt Chart',
                          'Sesi Scoping Eksekutif: 1 Sesi Scoping Teknis 1-on-1 bersama Principal Architect (Google Meet)',
                          'Garansi Kredit DP 100%: Biaya Rp 2.5 Jt otomatis memotong DP 50% jika lanjut ke Full MVP!',
                          'Dokumen Legal NDA Resmi & 100% Hak Milik Dokumen / Kode diserahkan ke klien',
                        ]).map((feat, idx) => (
                          <div key={idx} className="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                            <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                            <span className="text-[11px] leading-tight">{formatPillar(feat)}</span>
                          </div>
                        ))}
                      </div>
                    </div>

                    <div>
                      <div className="text-[10px] font-mono text-zinc-500 dark:text-zinc-400 text-center mb-3 font-semibold">
                        {isEn ? '⚠️ Architecture & Blueprint Only (Zero Coding by Neriah Pro)' : '⚠️ Cetak Biru & Roadmap Saja (Koding oleh Tim Anda)'}
                      </div>
                      <div className="space-y-2 pt-3 border-t border-zinc-200 dark:border-zinc-800">
                        <button
                          type="button"
                          onClick={() => openBookingModal('blueprint_advisory')}
                          className="w-full bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black py-3 px-4 font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition rounded-none shadow-xs text-center cursor-pointer"
                        >
                          <span>{isEn ? 'ORDER ADVISORY (RP 2.5M)' : 'PESAN JASA ADVISORY (RP 2.5 JT)'}</span>
                          <ArrowRight className="w-3.5 h-3.5" />
                        </button>

                        <button
                          type="button"
                          onClick={() => switchPricingTab('retail')}
                          className="w-full bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 py-2 px-4 font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none text-center cursor-pointer"
                        >
                          <Terminal className="w-3.5 h-3.5 text-emerald-500" />
                          <span>{isEn ? 'SEE SELF-SERVICE SAAS PACKAGES' : 'LIHAT PAKET SAAS MANDIRI'}</span>
                        </button>
                      </div>
                    </div>
                  </div>

                  {/* TIER 2: FULL MVP DEVELOPMENT (FLAGSHIP ENTERPRISE) */}
                  <div className="bg-white dark:bg-zinc-900 border-2 border-emerald-500 dark:border-emerald-500 p-6 sm:p-7 flex flex-col justify-between rounded-none shadow-2xl relative transform lg:-translate-y-2">
                    {/* Best Value Badge */}
                    <div className="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-emerald-500 text-white px-4 py-1 font-mono text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 shadow-md">
                      <Crown className="w-3.5 h-3.5" />
                      <span>{isEn ? '100% CODED BY NERIAH PRO // FULL MVP' : 'DIKERJAKAN 100% OLEH NERIAH PRO // FULL MVP'}</span>
                    </div>

                    <div>
                      <div className="flex items-center justify-between mb-3 mt-2">
                        <span className="font-mono text-xs font-black tracking-wider uppercase text-emerald-800 dark:text-emerald-400">
                          {isEn ? 'SCENARIO 2 // FULL MONOLITH' : 'SKENARIO 2 // FULL MONOLITH'}
                        </span>
                        <span className="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black">
                          {isEn ? '50% MILESTONE DP' : 'DP 50% MILESTONE'}
                        </span>
                      </div>

                      <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2 font-sans">
                        {isEn ? 'Enterprise Monolith MVP' : 'Full Turnkey Monolith MVP'}
                      </h3>

                      <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                        {isEn
                          ? 'Turnkey production web app built end-to-end by Neriah Pro Senior Architects. Blueprint & legal contract included.'
                          : 'Aplikasi web skala enterprise dibangun dari nol sampai live di server VPS. Dikerjakan langsung oleh Senior Software Architect Neriah Pro.'}
                      </p>

                      {/* Compact Price & Milestone Box */}
                      <div className="mb-5 p-3.5 bg-emerald-500/5 border border-emerald-500/30 space-y-2">
                        <div className="flex items-baseline justify-between">
                          <span className="text-xl sm:text-2xl font-black font-mono text-zinc-900 dark:text-white">
                            {isEn ? 'Algorithmic Fair RAB' : 'RAB Algoritmik Transparan'}
                          </span>
                          <span className="text-[10px] font-mono font-black text-emerald-800 dark:text-emerald-400">
                            Rp {pricingSettings.mitra_man_hour_rate_idr ? new Intl.NumberFormat('id-ID').format(parseInt(String(pricingSettings.mitra_man_hour_rate_idr).replace(/\D+/g, ''), 10) || 350000) : '350.000'}/jam
                          </span>
                        </div>
                        <div className="pt-2 border-t border-emerald-500/20 flex items-center justify-between text-xs font-mono">
                          <span className="text-emerald-800 dark:text-emerald-300 font-bold">
                            {isEn ? 'Protected Milestones:' : 'Termin Terproteksi:'}
                          </span>
                          <span className="font-black text-emerald-900 dark:text-emerald-400">
                            {(pricingSettings.mitra_termin_scheme || '30_50_20') === '50_50'
                              ? (isEn ? '50% DP & 50% BAST' : '50% DP & 50% BAST')
                              : (isEn ? '30% DP • 50% Staging • 20% BAST' : '30% DP • 50% Staging • 20% BAST')}
                          </span>
                        </div>
                        <div className="text-[10px] font-mono text-zinc-600 dark:text-zinc-400">
                          &bull; {isEn ? 'Exact cost derived from ULID tables, state machines & stack multiplier (zero guesswork)' : 'Dihitung matematis dari jumlah tabel ULID, state machine & pengali topologi (anti mark-up buta)'}
                        </div>
                      </div>

                      {/* Inclusive Header & Features */}
                      <div className="space-y-2.5 mb-6">
                        <div className="p-2 mb-2 bg-emerald-100 dark:bg-emerald-950/80 border-l-2 border-emerald-600 dark:border-emerald-400 text-emerald-950 dark:text-emerald-200 font-mono text-[10px] font-black">
                          ⭐ {formatPillar(isEn ? 'Includes all Blueprint Advisory deliverables + Complete 20 Pillars of Software Factory OS v12.0:' : 'Mencakup seluruh hasil Blueprint Advisory + Lengkap 20 Pilar Software Factory OS v12.0:')}
                        </div>
                        {(isEn ? [
                          'Pillars P01–P03 (Core Engine): 26-Parameter PRD, PostgreSQL 16 Strict ULID & Keyset O(1), Docker Stack (PHP 8.4, PG16, Redis)',
                          'Pillars P04–P06 (UI/UX & Logic): 36 Operational Cockpits + 6 60fps Scrollytelling Presets, Dual-Persona Studio, match() ACID Ledger',
                          'Pillars P07–P08 (AI Context): KAG Knowledge Graph + CAG Prompt Caching + 49 Native Tool Calling Functions & Live MCP Bridge',
                          'Pillars P09–P11 (Security): AgentShield Red-Team, Tamper-Evident SHA-256 Audit Trail Chain, UU PDP PII Vault & Blind Index',
                          'Pillars P12–P15 (SaaS & Escrow): One-Stop Commercial Gate, Turnkey Escrow Midtrans Snap (50/50 & 3-Milestone), SHA-256 Scope Lock, 90-Day SLA Bug-Warranty',
                          'Pillars P16–P17 (Authority): 12-Sector Multi-Graph Schema.org JSON-LD & Native /llms.txt AI Citation GEO Engine',
                          'Pillars P18–P20 (DevOps & SOP): Offline PWA Outbox Sync, Pre-Migration pg_dump Guard & 1-Click Rollback, Role Onboarding & Client A4 SOP',
                          'Pillar 4 (Realistic Data): Synthetic Mock Data Seeder Engine (100+ records for instant staging)',
                          'Pillar 6 (Automated Testing): ApiContractTest Pest & Playwright Browser Automation Suite (100% Green)',
                          'Pillar 7 (DevOps CI/CD): 1-Click Cloud Deployment Pipeline (GitHub Actions & deploy.sh 6 scenarios)',
                          'Managed Sprint Capacity: Reserved slot in Batch 1, 2, or Q1 with Anti-Collision Engine',
                          'Automated Payments: Midtrans Snap (QRIS, VA, Cards) with Idempotent Anti-Duplicate Webhook',
                          'Master Gantt Timeline Real-Time & Milestone Transparency in Customer Portal',
                          '100% Private GitHub Repo & Dedicated VPS Credentials Handover (Zero Vendor Lock-in)',
                          '90-Day Full Priority SLA Bug Warranty & Maintenance by Lead Architect',
                          'Official Change Request (CR) / Addendum Module via Customer Portal Dashboard',
                        ] : [
                          'Pilar P01–P03 (Arsitektur Inti): PRD 26 Parameter, PostgreSQL 16 Strict ULID & Keyset O(1), Docker Stack (PHP 8.4, PG16, Redis)',
                          'Pilar P04–P06 (UI/UX & Logika): 36 Kokpit Operasional + 6 Preset Scrollytelling 60fps, Dual-Persona Studio, match() ACID Ledger',
                          'Pilar P07–P08 (Konteks AI): KAG Knowledge Graph + CAG Prompt Caching + 49 Native Tool Calling Function & Jembatan Live MCP',
                          'Pilar P09–P11 (Keamanan): AgentShield Red-Team, Rantai Audit SHA-256 Kriptografis, Brankas Privasi UU PDP & Blind Index O(1)',
                          'Pilar P12–P15 (SaaS & Escrow): Gerbang Satu Pintu, Escrow Midtrans Snap (50/50 & 3-Milestone), Segel Scope SHA-256, Garansi SLA 90 Hari',
                          'Pilar P16–P17 (Otoritas): Multi-Graph Schema.org JSON-LD 12 Sektor & Mesin Sitasi GEO /llms.txt',
                          'Pilar P18–P20 (DevOps & SOP): Offline PWA Outbox Sync, Guard pg_dump Pra-Migrasi & 1-Click Rollback, Panduan Peran & Buku SOP A4',
                          'Pilar 4 (Data Nyata): Synthetic Mock Data Seeder Engine (100+ baris data realistis staging)',
                          'Pilar 6 (Uji Mutu Otomatis): ApiContractTest Pest & Otomasi Browser Playwright E2E (100% Lolos)',
                          'Pilar 7 (Jalur CI/CD Cloud): GitHub Actions & deploy.sh 6 skenario deploy zero-downtime',
                          'Managed Sprint Capacity: Slot terisolasi di Batch 1, 2, atau Q1 dengan Anti-Collision Engine',
                          'Integrasi Pembayaran Otomatis: Midtrans Snap (QRIS/VA/Kartu) & Webhook Idempoten Anti-Duplikasi',
                          'Master Gantt Timeline Real-Time & Transparansi Milestone di Customer Portal',
                          '100% Penyerahan Source Code Repo GitHub Privat & Kredensial Server VPS (No Lock-in)',
                          'Garansi SLA Prioritas 90 Hari Penuh & Maintenance Terjadwal Langsung oleh Lead Architect',
                          'Modul Change Request (CR) / Addendum Resmi terintegrasi di Customer Portal',
                        ]).map((feat, idx) => (
                          <div key={idx} className="flex items-start gap-2 text-xs text-zinc-800 dark:text-zinc-200 font-medium">
                            <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                            <span className="text-[11px] leading-tight">{formatPillar(feat)}</span>
                          </div>
                        ))}
                      </div>
                    </div>

                    <div>
                      <div className="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 text-center mb-3 font-bold">
                        {isEn ? '100% Executed Directly by Yoseph Iriandi (Zero Junior Subcontracting)' : '100% Dikerjakan Langsung oleh Yoseph Iriandi (Tanpa Lempar ke Junior)'}
                      </div>
                      <div className="space-y-2 pt-3 border-t border-zinc-200 dark:border-zinc-800">
                        <button
                          onClick={() => openBookingModal('full_mvp')}
                          className="w-full bg-emerald-500 hover:bg-emerald-400 text-white py-3.5 px-4 font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition rounded-none shadow-lg cursor-pointer"
                        >
                          <span>{isEn ? 'RESERVE SPRINT & SCOPING SESSION' : 'RESERVASI BATCH SPRINT & SCOPING'}</span>
                          <ArrowRight className="w-3.5 h-3.5" />
                        </button>

                        <button
                          onClick={() => openBookingModal('full_mvp')}
                          className="w-full bg-transparent hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 py-2 px-4 font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none cursor-pointer"
                        >
                          <MessageSquare className="w-3.5 h-3.5 text-emerald-500" />
                          <span>{isEn ? 'TECH SCOPING CALL' : 'KONSULTASI SCOPE TEKNIS'}</span>
                        </button>
                      </div>
                    </div>
                  </div>

                  {/* TIER 3: UMKM DIGITAL STARTER & SUBSIDI */}
                  <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-800 p-6 sm:p-7 flex flex-col justify-between rounded-none shadow-xs hover:border-emerald-500/60 transition group relative">
                    <div>
                      <div className="flex items-center justify-between mb-3">
                        <span className="font-mono text-xs font-black tracking-wider uppercase text-zinc-700 dark:text-zinc-300">
                          {isEn ? 'STIMULUS PROGRAM // UMKM' : 'PROGRAM STIMULUS // UMKM'}
                        </span>
                        <span className="px-2 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700 font-mono text-[10px] font-black">
                          {isEn ? 'TURNKEY RETAIL APP (100% CODED)' : 'APLIKASI RITEL JADI (100% DIKODINGKAN)'}
                        </span>
                      </div>

                      <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2 font-sans">
                        {isEn ? 'UMKM Digital Starter' : 'UMKM Digital Starter'}
                      </h3>

                      <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                        {isEn
                          ? 'Turnkey transactional web app for local retail shops, salons, clinics, & simple commerce. Not for complex custom platforms. 100% built and deployed to production by Neriah Pro.'
                          : 'Aplikasi web kasir & transaksi siap pakai khusus toko retail, kuliner, & usaha jasa lokal. Bukan untuk sistem custom rumit. 100% dikodingkan dan dideploy sampai live oleh Neriah Pro.'}
                      </p>

                      {/* Price & Subsidy Box */}
                      <div className="mb-5 p-3.5 bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 space-y-2">
                        <div className="flex items-baseline justify-between">
                          <div>
                            <span className="text-xs text-zinc-400 line-through mr-2">Rp {pricingSettings.umkm_price || '7.500.000'}</span>
                            <span className="text-2xl font-black font-mono text-amber-800 dark:text-amber-400">
                              Rp 3.750.000
                            </span>
                          </div>
                          <span className="text-[10px] font-mono px-2 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700 font-black">
                            {isEn ? '50% SUBSIDY' : 'SUBSIDI 50%'}
                          </span>
                        </div>
                        <div className="text-[10px] font-mono text-zinc-500 pt-1 border-t border-zinc-200 dark:border-zinc-800">
                          &bull; {isEn ? 'Voucher "UMKM-SUBSIDI-50" (Quota 2 slots / month)' : 'Gunakan voucher "UMKM-SUBSIDI-50" (Kuota 2 usaha / bln)'}
                        </div>
                      </div>

                      {/* Features */}
                      <div className="space-y-2.5 mb-6">
                        <div className="font-mono text-[11px] font-bold text-zinc-400 uppercase tracking-wider">
                          {isEn ? 'DELIVERABLES INCLUDED:' : 'OUTPUT YANG DIDAPATKAN:'}
                        </div>
                        {(isEn ? [
                          'Centralized Transaction Engine & Customer Database (Laravel 13 & PostgreSQL Strict ULID)',
                          'Automated Instant QRIS & Bank Virtual Account Payments (Midtrans Snap Integration)',
                          'Admin Dashboard Filament v5 in Indonesian (Easy Orders, Products & Sales Tracking)',
                          'Automated Financial & Sales Reports Export (Excel Spreadsheet & PDF Format)',
                          'Real-Time E.164 WhatsApp Order Confirmations & Tax Invoices to Customers',
                          'Fast Cloud SSD VPS Hosting Setup + Custom Business Domain (.id / .com) with HTTPS SSL',
                          '100% Coded & Deployed by Neriah Pro Team + 1-Month Priority Bug Warranty & Video Guide',
                        ] : [
                          'Sistem Transaksi, Kasir & Database Pelanggan Terpusat (Laravel 13 & PostgreSQL Strict ULID)',
                          'Pembayaran Otomatis Instan QRIS & Virtual Account Bank (Integrasi Midtrans Snap)',
                          'Dashboard Admin Filament v5 Bahasa Indonesia (Kelola Pesanan, Stok, & Laporan Penjualan)',
                          'Ekspor Laporan Keuangan & Penjualan Otomatis (Format Excel & Dokumen PDF)',
                          'Notifikasi Konfirmasi Pesanan & Invoice Otomatis via WhatsApp Standar E.164 ke Pelanggan',
                          'Setup Cloud SSD VPS Cepat + Domain Bisnis (.id / .com) dengan Enkripsi HTTPS SSL',
                          '100% Dikerjakan sampai Siap Pakai oleh Neriah Pro + Garansi Bug 1 Bulan & Panduan Video',
                        ]).map((feat, idx) => (
                          <div key={idx} className="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                            <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                            <span className="text-[11px] leading-tight">{formatPillar(feat)}</span>
                          </div>
                        ))}
                      </div>
                    </div>

                    <div>
                      <div className="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 text-center mb-3 font-semibold">
                        {isEn ? '✅ 100% Coded & Deployed by Neriah Pro (No Programmer Needed)' : '✅ 100% Dikodingkan & Dideploy Neriah Pro (Tinggal Pakai, Bebas Rekrut IT)'}
                      </div>
                      <div className="space-y-2 pt-3 border-t border-zinc-200 dark:border-zinc-800">
                        <button
                          onClick={() => openBookingModal('umkm_starter', 'UMKM-SUBSIDI-50')}
                          className="w-full bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black py-3 px-4 font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition rounded-none shadow-xs cursor-pointer"
                        >
                          <span>{isEn ? 'CLAIM 50% SUBSIDY' : 'KLAIM SUBSIDI UMKM (50%)'}</span>
                          <ArrowRight className="w-3.5 h-3.5" />
                        </button>

                        <a
                          href={`https://wa.me/${whatsappNumber}?text=${encodeURIComponent(isEn ? 'Hello Yoseph Iriandi, I would like to consult regarding the Digital Starter program at Neriah Pro.' : 'Halo Yoseph Iriandi, saya ingin konsultasi mengenai Program Subsidi UMKM Digital Starter di Neriah Pro.')}`}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="w-full bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 py-2 px-4 font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none text-center"
                        >
                          <MessageSquare className="w-3.5 h-3.5 text-amber-500" />
                          <span>{isEn ? 'CONSULT ON WHATSAPP' : 'KONSULTASI WHATSAPP'}</span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                {/* 5 COMMERCIAL-TO-CODE BRIDGES FOR VERSI MITRA PROYEK V5.0 */}
                <div className="mt-8 border-2 border-zinc-300 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5 sm:p-6 rounded-none">
                  <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 mb-4 border-b border-zinc-200 dark:border-zinc-800">
                    <div>
                      <span className="font-mono text-[10px] font-black uppercase tracking-widest text-amber-900 dark:text-amber-300">
                        PROJECT OS V5.0 // 5 COMMERCIAL-TO-CODE BRIDGES (VERSI MITRA PROYEK)
                      </span>
                      <h3 className="text-base sm:text-lg font-black uppercase text-zinc-950 dark:text-white mt-0.5">
                        {isEn
                          ? '5 Legal & Engineering Protections Connecting Client Quotation to Live Code'
                          : '5 Jembatan Komersial & Hukum: Anti-Underpricing, Anti-Scope-Creep & BAST Digital'}
                      </h3>
                    </div>
                    <button
                      type="button"
                      onClick={() => openBookingModal('full_mvp')}
                      className="px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 text-amber-300 dark:bg-amber-500 dark:hover:bg-amber-400 dark:text-zinc-950 font-mono text-xs font-black uppercase tracking-wider rounded-none transition shrink-0 text-center cursor-pointer"
                    >
                      {isEn ? 'Lock Mitra Contract & Sprint →' : 'Kunci Kontrak Mitra & Sprint →'}
                    </button>
                  </div>
                  <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
                    {[
                      {
                        num: '01',
                        title: isEn ? 'Algorithmic RAB & WBS' : 'Algorithmic RAB & WBS',
                        desc: isEn
                          ? 'Computes Man-Hours, Working Days & 3-Stage Termin mathematically from ULID tables, states, locks & topology multiplier.'
                          : 'Menghitung Man-Hours, Hari Kerja & Termin 30/50/20 otomatis dari jumlah tabel ULID, state machine & pengali topologi.',
                      },
                      {
                        num: '02',
                        title: isEn ? 'SPK SHA-256 Scope Freeze' : 'SPK SHA-256 Scope Freeze',
                        desc: isEn
                          ? 'Seals Blueprint v1.0 with a cryptographic SHA-256 hash printed directly inside the official A4 SPK/PKS appendix.'
                          : 'Mengunci Blueprint v1.0 dengan segel SHA-256 yang tercetak langsung pada Lampiran Teknis SPK/PKS A4 resmi.',
                      },
                      {
                        num: '03',
                        title: isEn ? 'Paid Delta Addendum v1.1' : 'Addendum Berbayar v1.1',
                        desc: isEn
                          ? 'Mid-sprint feature requests auto-calculate Delta Man-Hours & generate official Midtrans Addendum Invoices (NPRO-ADD).'
                          : 'Tambahan fitur di tengah jalan otomatis menghitung selisih jam kerja & menerbitkan Invoice Addendum Midtrans resmi.',
                      },
                      {
                        num: '04',
                        title: isEn ? '7-Milestone Gantt & Staging' : '7-Milestone Gantt & Staging',
                        desc: isEn
                          ? 'Syncs Phases 1–7 from IDE MCP directly into Client Milestones with real-time progress & Live Staging URL.'
                          : 'Menyinkronkan Fase 1–7 dari MCP IDE langsung ke 7 Milestone Klien beserta URL Live Staging secara real-time.',
                      },
                      {
                        num: '05',
                        title: isEn ? 'Interactive UAT & Digital BAST' : 'Checklist UAT & BAST Digital',
                        desc: isEn
                          ? 'Turns Given-When-Then acceptance criteria into a client sign-off checklist that issues Digital BAST & Final Invoice.'
                          : 'Mengubah matriks Given-When-Then menjadi checklist UAT klien; saat 100% lolos otomatis menerbitkan BAST Digital resmi.',
                      },
                    ].map((bridge) => (
                      <div key={bridge.num} className="p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex flex-col justify-between">
                        <div>
                          <div className="font-mono text-[10px] font-black text-emerald-800 dark:text-emerald-400 mb-1">
                            BRIDGE #{bridge.num}
                          </div>
                          <div className="font-bold text-xs text-zinc-950 dark:text-zinc-100 mb-1">
                            {bridge.title}
                          </div>
                          <p className="text-[11px] text-zinc-700 dark:text-zinc-400 leading-relaxed">
                            {bridge.desc}
                          </p>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>

                {/* Quick Switcher to Retail Licenses */}
                <div className="mt-8 p-4 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                  <div className="flex items-center gap-3">
                    <ShoppingBag className="w-5 h-5 text-emerald-500 shrink-0" />
                    <div className="text-xs">
                      <span className="font-bold text-zinc-900 dark:text-white block font-sans">
                        {isEn ? 'Already have an in-house engineering team to write code?' : 'Sudah memiliki tim programmer in-house sendiri untuk mengoding?'}
                      </span>
                      <span className="text-zinc-500 text-[11px]">
                        {isEn ? 'Save tens of millions with our self-service Instant Architectural Blueprints.' : 'Hemat puluhan juta rupiah dengan Lisensi Retail Instant Blueprint mandiri kami.'}
                      </span>
                    </div>
                  </div>
                  <button
                    type="button"
                    onClick={() => switchPricingTab('retail')}
                    className="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-mono text-xs font-bold uppercase tracking-wider rounded-none shrink-0 transition cursor-pointer"
                  >
                    {isEn ? 'View Retail Licenses →' : 'Lihat Lisensi Retail →'}
                  </button>
                </div>
              </motion.div>
            )}

            {/* ========================================================================= */}
            {/* TAB 3: 12 SECTORAL INDUSTRIAL UI/UX ARCHETYPES & 6 KILLER MOAT ENGINES     */}
            {/* ========================================================================= */}
            {pricingSectionTab === 'sectoral' && (() => {
              const activeSectorItem = SECTORAL_ARCHETYPES_SHOWCASE[selectedSectorIdx] || SECTORAL_ARCHETYPES_SHOWCASE[0];
              const sampleCliCmd = 'curl -sL https://neriahpro.com/init/01J9POSBLUEPRINTULID000001 | bash';
              const copyCliSample = () => {
                if (navigator?.clipboard?.writeText) {
                  navigator.clipboard.writeText(sampleCliCmd);
                }
                if (window.showToast) {
                  window.showToast({
                    type: 'success',
                    title: isEn ? 'CLI Command Copied' : 'Perintah CLI Disalin',
                    message: isEn
                      ? 'Generate your own ULID Blueprint in Studio to get your dedicated 3-second /init/{ULID} bootstrapper.'
                      : 'Buat Blueprint ULID Anda di Studio untuk mendapatkan skrip bootstrapper 3 detik /init/{ULID} khusus proyek Anda.',
                  });
                }
              };

              return (
                <motion.div
                  key="sectoral-tab-content"
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.2 }}
                  className="mb-16 space-y-8"
                >
                  {/* A. TOP HERO & ONE-COMMAND CLI BOOTSTRAPPER BAR */}
                  <div className="border-2 border-zinc-300 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 sm:p-8 rounded-none">
                    <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                      <div className="space-y-2 max-w-3xl">
                        <div className="inline-flex items-center gap-2 px-2.5 py-1 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black uppercase">
                          <span>PROJECT OS V12.0 // 36 UI + 6 SCROLL COCKPITS</span>
                        </div>
                        <h2 className="text-xl sm:text-2xl md:text-3xl font-black uppercase tracking-tight text-zinc-950 dark:text-white font-sans">
                          {isEn
                            ? '12 Sectoral Industrial UI Cockpits (36 Visuals + 6 Scroll Presets) & 6 Killer Engines V12.0'
                            : '12 Arketipe UI/UX Industri (36 Visual + 6 Preset Scroll) & 6 Mesin Orkestrasi Anti-Slop V12.0'}
                        </h2>
                        <p className="text-xs sm:text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">
                          {isEn
                            ? 'Generic AI tools dump identical 5-column CRUD tables onto POS cashiers, ICU doctors, and warehouse dispatchers. Project OS mechanically enforces domain-ergonomic multi-pane cockpits, keyboard-first shortcuts, Indonesian domain seeders, 6-phase prompts, and a 3-second CLI workspace bootstrapper.'
                            : 'Generator AI generik memaksakan tabel CRUD 5 kolom yang sama untuk kasir ritel, dokter IGD, hingga akuntan jurnal. Project OS mengunci kokpit multi-panel spesifik profesi, pintasan keyboard kasir/klinis, data seeder Indonesia asli, 6 fase prompt bebas context-overflow, hingga bootstrapper terminal 3 detik.'}
                        </p>
                      </div>

                      <div className="w-full lg:w-auto shrink-0 bg-zinc-950 text-zinc-100 border border-zinc-800 p-4 rounded-none space-y-2.5">
                        <div className="flex items-center justify-between gap-3 font-mono text-[10px] uppercase">
                          <span className="text-emerald-400 font-black">⚡ 3-SECOND WORKSPACE INIT</span>
                          <span className="px-1.5 py-0.5 bg-zinc-800 text-zinc-200 border border-zinc-700 font-bold">POSIX SH</span>
                        </div>
                        <div className="flex items-center gap-2 bg-black border border-zinc-800 px-3 py-2 font-mono text-[11px] text-emerald-300 overflow-x-auto">
                          <code className="whitespace-nowrap">{sampleCliCmd}</code>
                          <button
                            type="button"
                            onClick={copyCliSample}
                            className="px-2 py-1 bg-zinc-800 hover:bg-zinc-700 text-white font-mono text-[10px] font-black uppercase border border-zinc-600 shrink-0 cursor-pointer"
                          >
                            <Copy className="w-3 h-3 inline mr-1" />
                            {isEn ? 'Copy' : 'Salin'}
                          </button>
                        </div>
                        <div className="flex items-center justify-between gap-2 text-[10px] font-mono text-zinc-400">
                          <span>+ ./verify-blueprint.sh (CI Gate)</span>
                          <button
                            type="button"
                            onClick={() => openBookingModal('retail_pro')}
                            className="text-emerald-400 hover:text-emerald-300 font-black uppercase underline cursor-pointer"
                          >
                            {isEn ? 'Activate Studio License →' : 'Aktifkan Lisensi Studio →'}
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* B. INTERACTIVE 12-SECTOR INDUSTRIAL UI/UX WIREFRAME EXPLORER */}
                  <div className="grid grid-cols-1 lg:grid-cols-12 gap-5">
                    {/* Left Selector: 12 Sectors */}
                    <div className="lg:col-span-5 border-2 border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 rounded-none">
                      <div className="flex items-center justify-between gap-2 pb-3 mb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <span className="font-mono text-xs font-black uppercase text-zinc-950 dark:text-zinc-100">
                          {isEn ? 'Select Business Sector (01–12)' : 'Pilih Sektor Fungsional (01–12)'}
                        </span>
                        <span className="px-2 py-0.5 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 font-mono text-[10px] font-black">
                          ZERO CRUD SLOP
                        </span>
                      </div>
                      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-1.5 max-h-[540px] overflow-y-auto pr-1">
                        {SECTORAL_ARCHETYPES_SHOWCASE.map((sec, idx) => {
                          const active = idx === selectedSectorIdx;
                          return (
                            <button
                              key={sec.code}
                              type="button"
                              onClick={() => {
                                setSelectedSectorIdx(idx);
                                setSelectedUiVariantIdx(0);
                              }}
                              className={`w-full text-left px-3 py-2.5 border transition rounded-none cursor-pointer flex items-center justify-between gap-2 ${active
                                ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-950/80 dark:text-white dark:border-emerald-500'
                                : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-900 border-zinc-200 hover:border-zinc-400 dark:bg-zinc-950 dark:hover:bg-zinc-800/80 dark:text-zinc-200 dark:border-zinc-800'
                                }`}
                            >
                              <div className="min-w-0">
                                <div className="font-mono text-[10px] font-black uppercase truncate">
                                  <span className={active ? 'text-emerald-400' : 'text-emerald-800 dark:text-emerald-400'}>
                                    #{sec.num}
                                  </span>{' '}
                                  &bull; {sec.primary}
                                </div>
                                <div className={`text-xs font-bold truncate mt-0.5 ${active ? 'text-white' : 'text-zinc-900 dark:text-zinc-100'}`}>
                                  {sec.name}
                                </div>
                              </div>
                              <ArrowRight className={`w-3.5 h-3.5 shrink-0 ${active ? 'text-emerald-400' : 'text-zinc-400'}`} />
                            </button>
                          );
                        })}
                      </div>
                    </div>

                    {/* Right Live Cockpit Wireframe Inspector */}
                    <div className="lg:col-span-7 border-2 border-zinc-300 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5 sm:p-6 rounded-none flex flex-col justify-between">
                      {(() => {
                        const tab3Variants = SECTOR_VISUAL_VARIANTS_36[activeSectorItem.code] || SECTOR_VISUAL_VARIANTS_36.SECTOR_01_COMMERCE_OMNICHANNEL;
                        const activeTab3Ui = tab3Variants[selectedUiVariantIdx] || tab3Variants[0];
                        return (
                          <>
                            <div className="space-y-5">
                              <div className="flex flex-wrap items-start justify-between gap-3 pb-4 border-b border-zinc-200 dark:border-zinc-800">
                                <div>
                                  <div className="font-mono text-[10px] font-black uppercase text-emerald-800 dark:text-emerald-400">
                                    SECTOR #{activeSectorItem.num} // {activeSectorItem.code}
                                  </div>
                                  <h3 className="text-lg sm:text-xl font-black text-zinc-950 dark:text-white mt-0.5">
                                    {activeSectorItem.name}
                                  </h3>
                                </div>
                                <div className="flex flex-wrap items-center gap-1.5">
                                  <span className="px-2.5 py-1 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black">
                                    UI: {activeTab3Ui.code}
                                  </span>
                                  <span className="px-2.5 py-1 bg-zinc-100 text-zinc-950 border border-zinc-400 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 font-mono text-[10px] font-black">
                                    {activeTab3Ui.shell}
                                  </span>
                                </div>
                              </div>

                              {/* v8.0 3-Option Visual Frontend UI/UX Selector per Sector */}
                              <div className="space-y-2">
                                <div className="flex items-center justify-between gap-2">
                                  <span className="font-mono text-[10px] font-black uppercase text-zinc-800 dark:text-zinc-200">
                                    {isEn ? 'Choose Your Visual Frontend UI/UX Variant (3 Blueprints per Sector)' : 'Pilih Varian Visual Frontend UI/UX Proyek Anda (3 Opsi per Sektor)'}
                                  </span>
                                  <span className="font-mono text-[10px] font-bold text-emerald-900 dark:text-emerald-300">
                                    36 OPERATIONAL COCKPITS V12.0 // DUAL-ZONE UI
                                  </span>
                                </div>
                                <div className="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                  {tab3Variants.map((vr, vIdx) => {
                                    const activeVr = vIdx === selectedUiVariantIdx;
                                    return (
                                      <button
                                        key={vr.code}
                                        type="button"
                                        onClick={() => setSelectedUiVariantIdx(vIdx)}
                                        className={`p-2.5 text-left border transition rounded-none cursor-pointer ${activeVr
                                          ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-950/90 dark:text-white dark:border-emerald-400'
                                          : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-900 border-zinc-300 dark:bg-zinc-950 dark:hover:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-800'
                                          }`}
                                      >
                                        <div className={`font-mono text-[9px] font-black uppercase ${activeVr ? 'text-emerald-400' : 'text-emerald-900 dark:text-emerald-300'}`}>
                                          {vr.badge}
                                        </div>
                                        <div className="text-xs font-bold leading-snug mt-0.5">
                                          {vr.title}
                                        </div>
                                      </button>
                                    );
                                  })}
                                </div>
                              </div>

                              {/* Simulated Multi-Pane Industrial Wireframe (Driven by Selected Variant) */}
                              <div>
                                <div className="flex items-center justify-between gap-2 mb-2">
                                  <span className="font-mono text-[11px] font-black uppercase text-zinc-800 dark:text-zinc-200">
                                    {isEn ? 'Locked Visual Wireframe Topology' : 'Topologi Panel Visual Terkunci (Multi-Pane Wireframe)'}
                                  </span>
                                  <span className="font-mono text-[10px] font-bold text-zinc-600 dark:text-zinc-400">
                                    .agents/UI_UX_CONTRACT.md
                                  </span>
                                </div>
                                <div className={`grid grid-cols-1 ${activeTab3Ui.panes.length >= 3 ? 'md:grid-cols-3' : 'md:grid-cols-2'} gap-3 p-3.5 bg-zinc-100 dark:bg-zinc-950 border-2 border-zinc-300 dark:border-zinc-800`}>
                                  {activeTab3Ui.panes.map((paneText, pIdx) => {
                                    const [spanTag, ...restTitle] = String(paneText).split(':');
                                    return (
                                      <div
                                        key={pIdx}
                                        className="p-3.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 flex flex-col justify-between min-h-[112px]"
                                      >
                                        <div className="flex items-center justify-between gap-1.5 mb-2">
                                          <span className="px-2 py-0.5 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 font-mono text-[9px] font-black uppercase">
                                            {spanTag}
                                          </span>
                                          <span className="font-mono text-[9px] font-bold text-emerald-800 dark:text-emerald-400">
                                            LOCKED PANE #{pIdx + 1}
                                          </span>
                                        </div>
                                        <p className="text-xs font-bold text-zinc-950 dark:text-zinc-100 leading-snug">
                                          {restTitle.join(':').trim() || paneText}
                                        </p>
                                      </div>
                                    );
                                  })}
                                </div>
                              </div>

                              {/* Keyboard Shortcuts & Realistic Indonesian Domain Seeder */}
                              <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div className="p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                  <div className="font-mono text-[10px] font-black uppercase text-zinc-700 dark:text-zinc-400 mb-2">
                                    {isEn ? 'Keyboard-First Operator Shortcuts' : 'Pintasan Keyboard Operator Wajib'}
                                  </div>
                                  <div className="flex flex-wrap gap-1.5">
                                    {activeSectorItem.shortcuts.map((sc, sIdx) => (
                                      <span
                                        key={sIdx}
                                        className="px-2 py-1 bg-white dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-400 dark:border-zinc-700 font-mono text-[10px] font-black"
                                      >
                                        ⌨ {sc}
                                      </span>
                                    ))}
                                  </div>
                                </div>

                                <div className="p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                  <div className="font-mono text-[10px] font-black uppercase text-zinc-700 dark:text-zinc-400 mb-2">
                                    {isEn ? 'Realistic Indonesian Domain Seeder' : 'Seeder Data Kontekstual Indonesia'}
                                  </div>
                                  <div className="font-mono text-xs font-black text-emerald-900 dark:text-emerald-300">
                                    database/seeders/{activeSectorItem.seeder}.php
                                  </div>
                                  <div className="text-[11px] text-zinc-700 dark:text-zinc-400 mt-1">
                                    {isEn ? 'Zero "Lorem ipsum" / "Test User 1" — Investor-demo ready.' : 'Bebas "Lorem ipsum" / "Test User 1" — Langsung siap demo investor.'}
                                  </div>
                                </div>
                              </div>

                              {/* Sectoral Anti-Slop Ban */}
                              <div className="p-3.5 bg-amber-100/80 dark:bg-amber-950/50 border border-amber-400 dark:border-amber-700 text-amber-950 dark:text-amber-200 font-mono text-xs font-bold">
                                🚫 {activeSectorItem.ban}
                              </div>
                            </div>

                            <div className="mt-5 pt-4 border-t border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                              <span className="text-xs text-zinc-700 dark:text-zinc-300 font-sans">
                                {isEn
                                  ? `Locks ${activeTab3Ui.code} directly into Phase 5 UI Prompt & SHA-256 Scope.`
                                  : `Mengunci ${activeTab3Ui.code} langsung ke Prompt Fase 5 & Segel Scope SHA-256.`}
                              </span>
                              <div className="flex flex-wrap items-center gap-2">
                                <button
                                  type="button"
                                  onClick={() => {
                                    setFormData((prev) => ({
                                      ...prev,
                                      notes: `[SECTORAL UI LOCKED] Sector=${activeSectorItem.code} | UI=${activeTab3Ui.code} (${activeTab3Ui.shell})`,
                                    }));
                                    openBookingModal('retail_pro');
                                  }}
                                  className="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:text-zinc-950 font-mono text-xs font-black uppercase tracking-wider rounded-none transition cursor-pointer"
                                >
                                  {isEn ? '💳 Activate Retail License with This UI →' : '💳 Aktifkan Lisensi Retail dengan UI Ini →'}
                                </button>
                                <button
                                  type="button"
                                  onClick={() => {
                                    setFormData((prev) => ({
                                      ...prev,
                                      notes: `[MITRA UI LOCKED] Sector=${activeSectorItem.code} | UI=${activeTab3Ui.code} (${activeTab3Ui.shell})`,
                                    }));
                                    openBookingModal('full_mvp');
                                  }}
                                  className="px-3.5 py-2 bg-amber-100 hover:bg-amber-200 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:hover:bg-amber-900 dark:text-amber-200 dark:border-amber-700 font-mono text-xs font-black uppercase rounded-none transition cursor-pointer"
                                >
                                  {isEn ? '🗓️ Lock Mitra Turnkey' : '🗓️ Kunci Kontrak Mitra'}
                                </button>
                                <button
                                  type="button"
                                  onClick={() => switchPricingTab('retail')}
                                  className="px-3.5 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-950 border border-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-100 dark:border-zinc-700 font-mono text-xs font-bold uppercase rounded-none cursor-pointer"
                                >
                                  {isEn ? 'See Retail Pricing' : 'Lihat Harga Retail'}
                                </button>
                              </div>
                            </div>
                          </>
                        );
                      })()}
                    </div>
                  </div>

                  {/* C. 6 KILLER DEVELOPER-RETENTION & ZERO-TO-PRODUCTION ENGINES */}
                  <div className="border-2 border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 sm:p-8 rounded-none">
                    <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-6">
                      <div>
                        <span className="font-mono text-[10px] font-black uppercase tracking-widest text-emerald-800 dark:text-emerald-400">
                          COMPETITIVE MOAT // ANTI-CONTEXT-OVERFLOW & ZERO DATA LOSS
                        </span>
                        <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-950 dark:text-white mt-1">
                          {isEn
                            ? '6 Killer Features Why Developers Never Leave Project OS'
                            : '6 Killer Engine Pengunci Produktivitas Developer & Agensi'}
                        </h3>
                      </div>
                      <span className="px-2.5 py-1 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 font-mono text-[10px] font-black uppercase self-start sm:self-auto">
                        6 / 6 ACTIVE IN PRODUCTION
                      </span>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                      {KILLER_ENGINES_SHOWCASE.map((ke) => (
                        <div
                          key={ke.num}
                          className="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 transition flex flex-col justify-between"
                        >
                          <div>
                            <div className="flex items-center justify-between gap-2 mb-2.5">
                              <span className="px-2 py-0.5 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 font-mono text-[10px] font-black">
                                KILLER #{ke.num}
                              </span>
                              <span className="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[9px] font-black uppercase">
                                {ke.badge}
                              </span>
                            </div>
                            <h4 className="text-sm font-black text-zinc-950 dark:text-white mb-1.5">
                              {ke.title}
                            </h4>
                            <p className="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">
                              {ke.desc}
                            </p>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>

                  {/* Section E: Screen 4 — Side-by-Side Anti-Slop Diff Previewer + 5 Day-30 to Day-365 Lifecycle Engines */}
                  <div className="border-2 border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 sm:p-8 rounded-none shadow-xs space-y-6">
                    <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-4">
                      <div>
                        <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black uppercase mb-2">
                          <Code2 className="w-3 h-3" />
                          <span>{isEn ? 'SCREEN 4 • SIDE-BY-SIDE ANTI-SLOP DIFF & DAY-30+ LIFECYCLE V12.0' : 'BUKTI ARSITEKTUR • PERBANDINGAN NYATA KODE AI SLOP VS PROJECT OS V12.0'}</span>
                        </div>
                        <h3 className="text-lg sm:text-xl font-black uppercase text-zinc-950 dark:text-white font-mono">
                          {isEn ? 'Raw AI Prompt Slop vs NeriahPro Deterministic Engineering' : 'Tanpa NeriahPro (Raw AI Prompt) vs Dengan Project OS v12.0'}
                        </h3>
                      </div>
                      <button
                        type="button"
                        onClick={() => openBookingModal('retail_pro')}
                        className="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-mono text-xs font-black uppercase border border-emerald-500 rounded-none transition shrink-0 cursor-pointer"
                      >
                        <Terminal className="w-4 h-4" />
                        <span>{isEn ? 'ACTIVATE PRO AGENTIC STUDIO LICENSE' : 'AKTIFKAN LISENSI PRO AGENTIC STUDIO'}</span>
                        <ArrowRight className="w-3.5 h-3.5" />
                      </button>
                    </div>

                    {(() => {
                      const SLOP_DIFFS = [
                        {
                          label: isEn ? '1. Stock & Financial Concurrency' : '1. Stok & Saldo Keuangan Paralel',
                          rawTitle: isEn ? '❌ WITHOUT NERIAHPRO: Race Condition & Float Drift' : '❌ TANPA NERIAHPRO: Stok Minus & Selisih Koma Float',
                          rawCode: `// Raw AI Slop: AUTO_INCREMENT + float + static UPDATE (oversells!)
$table->id();
$table->float('price');
$product = Product::find($request->id);
if ($product->stock >= $qty) {
    $product->update(['stock' => $product->stock - $qty]);
}`,
                          posTitle: isEn ? '✅ WITH PROJECT OS V12.0: ULID + FOR UPDATE + DECIMAL(19,4)' : '✅ DENGAN PROJECT OS V12.0: ULID + Kunci Atomik + DECIMAL(19,4)',
                          posCode: `// Deterministic Contract: 26-char ULID + Pessimistic Lock + Movement Ledger
$table->ulid('id')->primary();
$table->decimal('unit_price_snapshot', 19, 4);
DB::transaction(function () use ($skuUlid, $qty, $orgId) {
    $stock = StockBalance::where('organization_id', $orgId)
        ->where('id', $skuUlid)->lockForUpdate()->firstOrFail();
    InventoryMovement::appendLockedDelta($stock, -$qty);
});`
                        },
                        {
                          label: isEn ? '2. Complex Discount & Penalty Rules' : '2. Aturan Diskon Bertingkat & Denda',
                          rawTitle: isEn ? '❌ WITHOUT NERIAHPRO: Nested If-Else Spaghetti' : '❌ TANPA NERIAHPRO: Spaghetti If-Else Bersarang',
                          rawCode: `// Unmaintainable AI Slop: magic strings & ambiguous precedence
if ($tier == 'vip') {
    if ($qty > 100) {
        if ($coupon != '') { $disc = $total * 0.25; }
    }
} else if ($tier == 'reseller') { /* ... 80 lines ... */ }`,
                          posTitle: isEn ? '✅ WITH PROJECT OS V12.0: Spreadsheet Decision Matrix -> match()' : '✅ DENGAN PROJECT OS V12.0: Tabel Keputusan -> PHP 8.4+ match()',
                          posCode: `// Compiled from Studio Decision Matrix (Zero Nested If-Else Slop)
return match (true) {
    $ctx->tier === 'DISTRIBUTOR_VIP' && $ctx->qty >= 100
        => MoneyDecimal::roundHalfUp($ctx->base * '0.8200', 4),
    $ctx->tier === 'RESELLER' && $ctx->qty >= 24
        => MoneyDecimal::roundHalfUp($ctx->base * '0.9000', 4),
    default => $ctx->base,
};`
                        },
                        {
                          label: isEn ? '3. Day-30 Evolution & IDE Sync' : '3. Evolusi Fitur v1.1 & Sinkronisasi IDE',
                          rawTitle: isEn ? '❌ WITHOUT NERIAHPRO: Destructive Rewrite & Stale Docs' : '❌ TANPA NERIAHPRO: Tabel Lama Tertimpa & Copy-Paste Manual',
                          rawCode: `# Developer re-prompts AI 2 months later:
"Add loyalty points to checkout"
# Result: AI runs migrate:fresh, drops v1.0 columns,
# forgets organization_id tenant isolation, breaks checkout.`,
                          posTitle: isEn ? '✅ WITH PROJECT OS V12.0: Surgical Delta v1.1 + Live MCP Bridge' : '✅ DENGAN PROJECT OS V12.0: Delta Aditif v1.1 + Live MCP Server',
                          posCode: `# Connected via .mcp.json (Claude Code / Cursor / Antigravity):
> neriahpro_create_delta_cr("Add Loyalty Points Ledger")
# Output: Additive-only ULID migration (v1.0 -> v1.1),
# graph.json diff, TenantIsolationSecurityTest, Verified ✅`
                        }
                      ];
                      const activeDiff = SLOP_DIFFS[activeSlopDiffIdx] || SLOP_DIFFS[0];

                      return (
                        <div className="space-y-4">
                          <div className="flex flex-wrap gap-2 font-mono text-xs">
                            {SLOP_DIFFS.map((item, idx) => (
                              <button
                                key={idx}
                                type="button"
                                onClick={() => setActiveSlopDiffIdx(idx)}
                                className={`px-3 py-1.5 border rounded-none cursor-pointer transition ${activeSlopDiffIdx === idx
                                  ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-500 dark:text-zinc-950 dark:border-emerald-400 font-black'
                                  : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-900 border-zinc-300 dark:bg-zinc-950 dark:hover:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 font-bold'
                                  }`}
                              >
                                {item.label}
                              </button>
                            ))}
                          </div>

                          <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div className="border border-red-400 dark:border-red-900/80 bg-red-50/50 dark:bg-zinc-950 p-4 space-y-2">
                              <div className="font-mono text-xs font-black text-red-950 dark:text-red-300">
                                {activeDiff.rawTitle}
                              </div>
                              <pre className="p-3 bg-zinc-950 text-red-300 border border-zinc-800 font-mono text-[11px] leading-relaxed overflow-x-auto whitespace-pre-wrap">
                                {activeDiff.rawCode}
                              </pre>
                            </div>
                            <div className="border border-emerald-500 dark:border-emerald-700 bg-emerald-50/50 dark:bg-zinc-950 p-4 space-y-2">
                              <div className="font-mono text-xs font-black text-emerald-950 dark:text-emerald-300">
                                {activeDiff.posTitle}
                              </div>
                              <pre className="p-3 bg-zinc-950 text-emerald-300 border border-zinc-800 font-mono text-[11px] leading-relaxed overflow-x-auto whitespace-pre-wrap">
                                {activeDiff.posCode}
                              </pre>
                            </div>
                          </div>
                        </div>
                      );
                    })()}

                    {/* 5 Day-30 to Day-365 Lifecycle Retention Engines */}
                    <div className="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                      <div className="font-mono text-xs font-black uppercase text-zinc-950 dark:text-zinc-100 mb-3">
                        {isEn ? '5 DAY-30 TO DAY-365 LIFECYCLE RETENTION ENGINES (INCLUDED IN V4.0)' : '5 MESIN SIKLUS HIDUP HARI KE-30 S/D HARI KE-365 (TERMASUK DI V4.0)'}
                      </div>
                      <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
                        {[
                          {
                            tag: 'DELTA v1.0 → v1.1',
                            title: isEn ? 'Surgical Delta CR Generator' : 'Generator Delta Fitur v1.1',
                            desc: isEn ? 'Additive-only ULID migrations, graph.json diff & regression suite without touching live tables.' : 'Migrasi ULID murni aditif, selisih graph.json & tes regresi tanpa merusak tabel produksi.'
                          },
                          {
                            tag: 'NERIAHPRO-MCP',
                            title: isEn ? 'Live MCP Server Bridge' : 'Jembatan MCP IDE Langsung',
                            desc: isEn ? 'JSON-RPC 2.0 .mcp.json sync for Claude Code, Cursor & Antigravity IDE with live Verified ✅.' : 'Koneksi langsung .mcp.json ke Claude Code, Cursor & Antigravity IDE dengan status Verified ✅ otomatis.'
                          },
                          {
                            tag: 'MATCH() MATRIX',
                            title: isEn ? 'Decision Table Compiler' : 'Tabel Keputusan match()',
                            desc: isEn ? 'Spreadsheet truth-tables compiled into PHP 8.4+ match() with DECIMAL(19,4) precision.' : 'Tabel kebenaran diskon/komisi/denda dikompilasi ke PHP 8.4+ match() tanpa if-else bersarang.'
                          },
                          {
                            tag: 'PHASE 07 RED-TEAM',
                            title: isEn ? 'Tenant-Leakage Security Suite' : 'Uji Penetrasi Otomatis Fase 7',
                            desc: isEn ? 'Automated tests for cross-tenant IDOR (404), Livewire #[Locked] tamper & Prompt Injection.' : 'Pengujian otomatis kebocoran lintas-tenant IDOR (404), Livewire #[Locked] & Prompt Injection.'
                          },
                          {
                            tag: 'POS:CHECK-ENV',
                            title: isEn ? '.env.blueprint Secret Vault' : 'Brankas .env.blueprint',
                            desc: isEn ? 'Pre-flight Artisan env validator per sector & payment/WA gateway before Coolify deploy.' : 'Validasi otomatis variabel environment gateway pembayaran/WA sebelum deploy ke VPS/Coolify.'
                          }
                        ].map((lf, idx) => (
                          <div key={idx} className="p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 hover:border-zinc-500 dark:hover:border-emerald-600 transition">
                            <span className="inline-block px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[9px] font-black mb-2">
                              {lf.tag}
                            </span>
                            <div className="text-xs font-black text-zinc-950 dark:text-white mb-1">{lf.title}</div>
                            <p className="text-[11px] text-zinc-700 dark:text-zinc-300 leading-relaxed">{lf.desc}</p>
                          </div>
                        ))}
                      </div>
                    </div>

                    {/* v7.0: 5 Real-World Field Runtime Contracts & Master 15 Architecture Pillars (100% Architectural Freeze) */}
                    <div className="pt-4 border-t-2 border-emerald-600 dark:border-emerald-500 space-y-4">
                      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                          <div className="flex flex-wrap items-center gap-2 mb-1">
                            <span className="px-2 py-0.5 bg-zinc-950 text-white dark:bg-emerald-400 dark:text-zinc-950 font-mono text-[10px] font-black uppercase">
                              PROJECT OS v7.0 • 100% ARCHITECTURAL FREEZE
                            </span>
                            <span className="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black uppercase">
                              {isEn ? '5 REAL-WORLD ELEMENTS + 15 MASTER PILLARS' : '5 ELEMEN DUNIA NYATA + 15 PILAR UTAMA'}
                            </span>
                          </div>
                          <div className="font-mono text-xs font-black uppercase text-zinc-950 dark:text-zinc-100">
                            {isEn
                              ? 'FIELD HARDWARE, ZERO-POLLING WEBSOCKET, 0% N+1 PARTITIONING, SYNTAX LOCK 2026 & BAST CLIENT SOP'
                              : 'JEMBATAN HARDWARE FISIK, WEBSOCKET TANPA POLLING, PARTISI PG16 0% N+1, SYNTAX LOCK 2026 & BUKU SOP BAST'}
                          </div>
                        </div>
                        <button
                          type="button"
                          onClick={() => openBookingModal('retail_ultimate')}
                          className="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 border border-emerald-700 font-mono text-[10px] font-black uppercase rounded-none transition whitespace-nowrap cursor-pointer"
                        >
                          {isEn ? 'UNLOCK ULTIMATE OS LICENSE →' : 'AKTIFKAN LISENSI ULTIMATE OS →'}
                        </button>
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
                        {[
                          {
                            tag: 'RW-01 • <30MS HARDWARE',
                            title: isEn ? 'Peripheral & Dual-Screen Bridge' : 'Scanner Fisik & Layar Kedua QRIS',
                            desc: isEn
                              ? 'Global <30ms Barcode/RFID Keyboard Wedge, BroadcastChannel Dual-Screen sync, 100% local Dynamic QRIS & <150ms Turnstile/ZKTeco webhook.'
                              : 'Listener scanner Barcode/RFID global <30ms tanpa fokus input, sinkron layar pelanggan BroadcastChannel + QRIS Dinamis lokal & ZKTeco/Gate <150ms.'
                          },
                          {
                            tag: 'RW-02 • ZERO WIRE:POLL',
                            title: isEn ? 'Event-Driven WebSocket Contract' : 'WebSocket Real-Time Tanpa Polling',
                            desc: isEn
                              ? 'Strict ban on wire:poll.<30s! Laravel Reverb / Soketi / NATS broadcasts on private tenant channel organization.{ulid}.{module}.'
                              : 'Larangan mutlak wire:poll.<30s! Broadcast instan via Reverb/Soketi/NATS di kanal privat terisolasi organization.{ulid}.{module}.'
                          },
                          {
                            tag: 'RW-03 • 0% N+1 + PG16',
                            title: isEn ? 'Strict N+1 Ban & Range Partition' : 'Anti N+1 & Partisi Kuartalan PG16',
                            desc: isEn
                              ? 'Model::shouldBeStrict(!isProduction()), tenant composite indexes & PostgreSQL 16 PARTITION BY RANGE (created_at) for 100M+ rows.'
                              : 'Model::shouldBeStrict aktif otomatis, composite index per tenant & partisi kuartalan PostgreSQL 16 siap ratusan juta baris.'
                          },
                          {
                            tag: 'RW-04 • SYNTAX LOCK 2026',
                            title: isEn ? 'Self-Healing Error Dictionary' : 'Kamus Anti-Halusinasi 2026',
                            desc: isEn
                              ? 'Stack-tailored SYNTAX_LOCK_2026.md & TROUBLESHOOTING_INSTINCTS.md locking Filament v5 Schema, Livewire 4 #[Locked] & FrankenPHP.'
                              : 'SYNTAX_LOCK_2026.md sesuai Stack M1–S2 mengunci sintaks Filament v5 Schema, Livewire 4 #[Locked], x-teleport & FrankenPHP.'
                          },
                          {
                            tag: 'RW-05 • BAST CLIENT SOP',
                            title: isEn ? 'Role Onboarding & A4 SOP Pack' : 'Tur Interaktif & Buku SOP BAST',
                            desc: isEn
                              ? 'Auto-generated SmartGuide.php per RBAC role + printable A4 CLIENT_SOP_AND_TRAINING_MANUAL.md reactively synced to L1–L4 menus.'
                              : 'Halaman SmartGuide.php per peran staf + Buku SOP Operasional A4 siap cetak BAST yang tersinkron reaktif dari 25 Menu L1–L4.'
                          }
                        ].map((rw, idx) => (
                          <div key={idx} className="p-3.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 hover:border-emerald-600 dark:hover:border-emerald-500 transition">
                            <span className="inline-block px-2 py-0.5 bg-zinc-950 text-white dark:bg-emerald-950 dark:text-emerald-200 border border-zinc-950 dark:border-emerald-700 font-mono text-[9px] font-black mb-2">
                              {rw.tag}
                            </span>
                            <div className="text-xs font-black text-zinc-950 dark:text-white mb-1">{rw.title}</div>
                            <p className="text-[11px] text-zinc-700 dark:text-zinc-300 leading-relaxed">{rw.desc}</p>
                          </div>
                        ))}
                      </div>

                      {/* 15 Master Architecture Pillars Matrix */}
                      <div className="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800">
                        <div className="flex flex-wrap items-center justify-between gap-2 mb-3">
                          <span className="font-mono text-[11px] font-black uppercase text-zinc-950 dark:text-zinc-100">
                            {isEn ? '20 CANONICAL ARCHITECTURE PILLARS — 100% LOCK & FREEZE MATRIX V12.0' : 'MATRIKS 20 PILAR UTAMA PROJECT OS V12.0 — 100% LOCK & FREEZE'}
                          </span>
                          <span className="font-mono text-[10px] font-bold text-emerald-900 dark:text-emerald-300">
                            {isEn ? 'Unified Causal Chain: 7 Retail SaaS Tiers & Turnkey Escrow' : 'Rantai Kausalitas Satu Pintu: 7 SaaS Tiers & Turnkey Escrow'}
                          </span>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-2">
                          {[
                            { no: '01', id: 'Intake Multi-Modal & Legacy SQL', en: 'Multi-Modal Intake & Legacy SQL', artifact: 'MarkItDown + Shadow ULID' },
                            { no: '02', id: '12 Sektor × 12 Primitives × 9 Stack', en: '12 Sectors × 12 Primitives × 9 Stacks', artifact: 'BLUEPRINT_SCHEMA.json' },
                            { no: '03', id: 'Skema PG16 ULID, Partisi & 0% N+1', en: 'PG16 ULID, Partitioning & 0% N+1', artifact: 'pg16_range_partitioning.sql' },
                            { no: '04', id: 'Zona A: 36 Kokpit Operasional Sektoral', en: 'Zone A: 36 Sector Cockpits (Zero-Slop)', artifact: 'UI_UX_DESIGN_CONTRACT.md' },
                            { no: '05', id: 'Zona B: 6 Preset Scrollytelling 60fps', en: 'Zone B: 6 60fps Scrollytelling Presets', artifact: 'scrollytelling-presets.css' },
                            { no: '06', id: 'Tabel Keputusan match() & ACID Saga', en: 'match() Decision Matrix & ACID Saga', artifact: 'DeterministicDecisionMatrix.php' },
                            { no: '07', id: 'Konteks AI Tri-Hybrid: KAG + CAG + RAG', en: 'Tri-Hybrid AI: KAG + CAG + RAG Engine', artifact: 'ai_memory_architecture.json' },
                            { no: '08', id: '49 Native Tool Calling & Live MCP', en: '49 Native Tool Calling & Live MCP Bridge', artifact: '/init/{ulid} + .mcp.json' },
                            { no: '09', id: 'AgentShield Red-Team & Anti-AI Malware', en: 'AgentShield Red-Team & Anti-Malware', artifact: 'verify-blueprint.sh' },
                            { no: '10', id: 'Rantai Audit SHA-256 Kriptografis', en: 'Cryptographic SHA-256 Audit Trail Chain', artifact: 'AuditLog::record' },
                            { no: '11', id: 'Brankas Privasi UU PDP & Blind Index O(1)', en: 'UU PDP Privacy Vault & Blind Index O(1)', artifact: 'HasEncryptedPiiWithBlindIndex.php' },
                            { no: '12', id: 'Gerbang Lisensi Komersial Satu Pintu', en: 'One-Stop Commercial SaaS Gate', artifact: 'SatuPintuEntitlementLock.php' },
                            { no: '13', id: 'Turnkey Escrow Midtrans Snap (50/50)', en: 'Turnkey Escrow Midtrans Snap (50/50)', artifact: 'MidtransEscrowPaymentService.php' },
                            { no: '14', id: 'RAB Jam Kerja & Segel Lingkup SHA-256', en: 'Algorithmic Fair RAB & SHA-256 Scope Lock', artifact: 'SHA-256 Scope Lock SPK' },
                            { no: '15', id: 'BAST Digital & Garansi Bug SLA 90 Hari', en: 'Digital BAST & 90-Day SLA Bug-Warranty', artifact: 'DigitalBastService.php' },
                            { no: '16', id: 'Multi-Graph Schema.org JSON-LD (12 Sektor)', en: 'Multi-Graph Schema.org JSON-LD (12 Sectors)', artifact: 'MultiGraphSchemaOrgService.php' },
                            { no: '17', id: 'Otoritas Sitasi GEO /llms.txt & IndexNow', en: 'GEO /llms.txt Citation & IndexNow Ping', artifact: '/llms.txt + /llms-full.txt' },
                            { no: '18', id: 'Offline PWA Outbox Sync Tanpa Putus', en: 'Offline PWA Outbox & Background Sync', artifact: 'IndexedDB Outbox + SW' },
                            { no: '19', id: 'Guard pg_dump Pra-Migrasi & Rollback', en: 'Pre-Migration pg_dump Guard & Rollback', artifact: 'pre_migrate_dump_guard.sh' },
                            { no: '20', id: 'Panduan Peran Staf & Paket SOP A4 Klien', en: 'Role Onboarding & Client A4 SOP Pack', artifact: 'CLIENT_SOP_MANUAL.md' },
                          ].map((p) => (
                            <div key={p.no} className="p-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-emerald-500 transition flex flex-col justify-between">
                              <div>
                                <div className="flex items-center justify-between gap-1 mb-1">
                                  <span className="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[9px] font-black">
                                    {isEn ? 'PILLAR' : 'PILAR'} {p.no}
                                  </span>
                                  <span className="font-mono text-[9px] font-black text-emerald-900 dark:text-emerald-300">100%</span>
                                </div>
                                <div className="text-[11px] font-black text-zinc-950 dark:text-zinc-100 leading-snug">
                                  {isEn ? p.en : p.id}
                                </div>
                              </div>
                              <div className="mt-1.5 pt-1 border-t border-zinc-100 dark:border-zinc-800 font-mono text-[9px] font-bold text-zinc-600 dark:text-zinc-400 truncate">
                                {p.artifact}
                              </div>
                            </div>
                          ))}
                        </div>
                      </div>

                      {/* ========================================================================= */}
                      {/* V12.0: 3-STAGE PROGRESSIVE DISCLOSURE FLOW & ANTI-OVER-PLANNING GUARD    */}
                      {/* ========================================================================= */}
                      <div className="p-4 sm:p-6 bg-zinc-50 dark:bg-zinc-950 border-t-2 border-zinc-900 dark:border-zinc-700 space-y-5">
                        {/* 1. 3-Stage Progressive Disclosure ("Show the Full Ferrari, Lock the Ignition") */}
                        <div className="p-4 bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-emerald-500/80 space-y-3">
                          <div className="flex flex-wrap items-center justify-between gap-2">
                            <div className="flex items-center gap-2">
                              <span className="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black uppercase">
                                PROGRESSIVE DISCLOSURE FLOW V12.0
                              </span>
                              <h5 className="font-mono text-xs sm:text-sm font-black uppercase text-zinc-950 dark:text-zinc-100">
                                {isEn
                                  ? '"Show the Full Ferrari, Lock the Ignition" — High-Retention 3-Stage Guided Flow + 0% DOM Leak Gate'
                                  : '"Show the Full Ferrari, Lock the Ignition" — Alur 3-Tahap Tanpa Kebingungan + Gerbang Server 0% Leak'}
                              </h5>
                            </div>
                            <span className="px-2 py-0.5 bg-zinc-950 text-emerald-400 dark:bg-emerald-500 dark:text-zinc-950 font-mono text-[9px] font-black uppercase">
                              0% F12 INSPECT ELEMENT LEAK
                            </span>
                          </div>

                          <div className="grid grid-cols-1 md:grid-cols-3 gap-3 font-sans text-xs">
                            {[
                              {
                                badge: isEn ? 'STAGE A // ZERO-JARGON INTAKE' : 'TAHAP A // INTAKE TANPA PUSING',
                                title: isEn ? 'Smart Idea Auto-Detection' : 'Deteksi Sektor & Modul Otomatis',
                                desc: isEn
                                  ? 'Type your business idea in plain words and click [✨ Auto-Detect Sector & Modules]. Automatically maps Sector #01–#12, Transaction Primitives, L1–L4 Menus, and Zone A+B UI.'
                                  : 'Ketik ide bisnis dalam bahasa sehari-hari lalu klik [✨ Deteksi Sektor & Modul Otomatis]. Sistem otomatis memetakan Sektor #01–#12, Primitif Transaksi, Peta Menu L1–L4, dan Zona UI A+B.',
                              },
                              {
                                badge: isEn ? 'STAGE B // SCREEN 1 WOW PROPOSAL' : 'TAHAP B // LAYAR 1 PROPOSAL BISNIS',
                                title: isEn ? 'Website Architecture Proposal (WOW View)' : 'Proposal Arsitektur Bisnis (Ruang Kagum)',
                                desc: isEn
                                  ? 'Welcomes clients with a human-friendly Executive Proposal, auto-expanded 5-Division Operational Menu Map (L1–L4), Zone A+B Screen Preview, and Engineering Cost Saved Calculator.'
                                  : 'Menyambut klien dengan Proposal Eksekutif ramah manusia, Peta Menu 5 Divisi Operasional (L1–L4) yang langsung terbuka, Pratinjau Layar Zona A+B, dan Kalkulasi Penghematan Biaya Rekayasa.',
                              },
                              {
                                badge: isEn ? 'STAGE C // SCREEN 2 DUAL STUDIO' : 'TAHAP C // LAYAR 2 STUDIO MODE GANDA',
                                title: isEn ? 'Vibe Coder Guided vs Architect Pro + Rescue' : 'Mode Terpandu Vibe Coder vs Arsitek Pro + Tombol Penyelamat',
                                desc: isEn
                                  ? 'Step-by-step copy-paste checklist for Cursor/Claude Code OR full ULID DDL & SYMBOL_MANIFEST.json for Architects. Includes 100% Retail-to-Mitra DP Rescue Credit!'
                                  : 'Checklist tempel-jalan untuk Cursor/Claude Code ATAU mode Arsitek penuh. Dilengkapi Tombol Penyelamat Konversi ke Mitra Proyek (100% biaya Retail memotong DP Mitra)!',
                              },
                            ].map((stItem) => (
                              <div key={stItem.badge} className="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 space-y-1.5">
                                <span className="inline-block px-1.5 py-0.5 bg-cyan-100 text-cyan-950 border border-cyan-400 dark:bg-cyan-950/80 dark:text-cyan-200 dark:border-cyan-700 font-mono text-[9px] font-black">
                                  {stItem.badge}
                                </span>
                                <div className="font-mono text-xs font-black text-zinc-950 dark:text-zinc-100">
                                  {stItem.title}
                                </div>
                                <p className="text-[11px] text-zinc-700 dark:text-zinc-300 leading-relaxed">
                                  {stItem.desc}
                                </p>
                              </div>
                            ))}
                          </div>
                        </div>

                        {/* 2. Mandatory Anti-Over-Planning (Scope Paralysis Guard) */}
                        <div className="p-4 sm:p-5 bg-amber-100/90 dark:bg-amber-950/50 border-2 border-amber-600 dark:border-amber-500 space-y-3">
                          <div className="flex flex-wrap items-center justify-between gap-2">
                            <span className="px-2 py-0.5 bg-zinc-950 text-amber-300 font-mono text-[10px] font-black uppercase tracking-wider">
                              {isEn
                                ? '🛡️ ANTI-OVER-PLANNING GUARD // 100% MATURITY REACHED — ACTIVATE SCOPE FREEZE'
                                : '🛡️ PROTEKSI ANTI-OVER-PLANNING // BLUEPRINT 100% MATANG — AKTIFKAN SCOPE FREEZE'}
                            </span>
                            <a
                              href="/blueprint/studio?tab=proposal_wow"
                              className="px-3 py-1 bg-zinc-950 hover:bg-zinc-800 text-emerald-400 font-mono text-[10px] font-black uppercase rounded-none transition"
                            >
                              {isEn ? '⚡ OPEN 3-PANE STUDIO & EXECUTE →' : '⚡ BUKA 3-PANE STUDIO & EKSEKUSI →'}
                            </a>
                          </div>

                          <h5 className="font-mono text-sm sm:text-base font-black text-amber-950 dark:text-amber-200 uppercase">
                            {isEn
                              ? 'Important Warning: The Danger of Over-Planning (Scope Paralysis)'
                              : 'Peringatan Penting: Bahaya Over-Planning (Scope Paralysis)'}
                          </h5>

                          <div className="space-y-2 text-xs text-zinc-900 dark:text-zinc-100 leading-relaxed font-sans">
                            <p>
                              {isEn
                                ? 'In product engineering, once a blueprint reaches 100%, the biggest enemy is no longer "missing features", but delaying code writing by endlessly hunting for extra features on paper.'
                                : 'Dalam rekayasa produk, ketika sebuah blueprint sudah mencapai 100%, musuh terbesar berikutnya bukanlah "fitur yang kurang", melainkan menunda penulisan kode karena terus mencari tambahan fitur di atas kertas.'}
                            </p>
                            <p className="font-bold">
                              {isEn
                                ? 'All the ammunition is already in your hands. The highest-impact step right now is to stop adding new concepts (SCOPE FREEZE) and start executing the foundation of neriahpro.com step by step in your IDE. Where would you like us to start executing the code today?'
                                : 'Semua amunisi sudah ada di tangan Anda. Langkah paling berdampak tinggi sekarang adalah berhenti menambah konsep baru (SCOPE FREEZE) dan mulai mengeksekusi pembangunan fondasi neriahpro.com tahap demi tahap di IDE Anda. Dari mana Anda ingin kita mulai mengeksekusi kodenya hari ini?'}
                            </p>
                          </div>

                          <div className="grid grid-cols-1 md:grid-cols-3 gap-2.5 pt-1">
                            {[
                              {
                                stage: 1,
                                label: isEn
                                  ? 'Start Stage 1 Execution: Generate Full Internal Database Migrations for neriahpro.com'
                                  : 'Mulai Eksekusi Tahap 1: Buat File Migrasi Lengkap Database Internal neriahpro.com',
                                href: '/blueprint/studio?tab=schema&exec_stage=stage_1_migrations',
                              },
                              {
                                stage: 2,
                                label: isEn
                                  ? 'Start Stage 2 Execution: Generate Master Seeder for 12 Sectors, 9 Stacks & L1-L4 Menu Matrix'
                                  : 'Mulai Eksekusi Tahap 2: Buat Seeder Master 12 Sektor, 9 Stack & L1-L4 Menu Matrix',
                                href: '/blueprint/studio?tab=menu_map&exec_stage=stage_2_seeders',
                              },
                              {
                                stage: 3,
                                label: isEn
                                  ? 'Start Stage 3 Execution: Build SYMBOL_MANIFEST.json & Phased Prompt Compiler'
                                  : 'Mulai Eksekusi Tahap 3: Bangun Compiler SYMBOL_MANIFEST.json & Phased Prompt',
                                href: '/blueprint/studio?tab=prompts&exec_stage=stage_3_compiler',
                              },
                            ].map((execBtn) => (
                              <a
                                key={execBtn.stage}
                                href={execBtn.href}
                                className="p-3 bg-white hover:bg-zinc-950 text-zinc-950 hover:text-amber-300 dark:bg-zinc-900 dark:hover:bg-amber-500 dark:text-zinc-100 dark:hover:text-zinc-950 border-2 border-zinc-900 dark:border-amber-500 font-mono text-[11px] font-black leading-snug transition rounded-none flex flex-col justify-between gap-2"
                              >
                                <span className="text-[9px] uppercase tracking-wider opacity-80">
                                  [STAGE 0{execBtn.stage} // SCOPE FREEZE READY]
                                </span>
                                <span>{execBtn.label} →</span>
                              </a>
                            ))}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </motion.div>
              );
            })()}

            {/* ========================================================================= */}
            {/* TAB 4: TECHNICAL SPECIFICATION COMPARISON MATRIX (V12.0 20 PILLARS)       */}
            {/* ========================================================================= */}
            {pricingSectionTab === 'matrix' && (() => {
              const matrixCategories = [
                { id: 'ALL', label: isEn ? 'All 28 Parameters' : 'Semua 28 Parameter' },
                { id: 'COMMERCIAL', label: isEn ? '1. One-Stop & Commercial' : '1. Satu Pintu & Lisensi' },
                { id: 'ARCHITECTURE', label: isEn ? '2. DB ULID, Stack & Scale' : '2. DB ULID, Stack & Skala' },
                { id: 'UI_UX', label: isEn ? '3. 12-Sector 36 UI & 6 Scroll' : '3. 12 Sektor 36 UI & 6 Scroll' },
                { id: 'AI_SECURITY', label: isEn ? '4. AI 49 Tools, KAG, CAG, RAG & Shield' : '4. AI 49 Tool, KAG, CAG, RAG & Shield' },
                { id: 'SEO_GEO_V12', label: isEn ? '5. 20 Pillars, SEO/GEO, UU PDP & Escrow' : '5. 20 Pilar, SEO/GEO, UU PDP & Escrow' },
              ];

              const matrixRows = [
                {
                  cat: 'COMMERCIAL',
                  param: isEn ? '01. Who Executes Application Coding?' : '01. Siapa yang Melakukan Koding Aplikasi?',
                  sparkGrant: isEn ? 'Spark: Self-Service (Phase 1–2) | CSR Grant: Non-Profit Dev Team (Subsidized Rp 0)' : 'Spark: Mandiri (Fase 1–2) | Hibah CSR: Tim Yayasan Non-Profit (Subsidi Rp 0)',
                  starterPass: isEn ? '100% Self-Service by Your Team / AI IDE (Zero Neriah Pro Coding)' : '100% Mandiri oleh Tim Anda / AI IDE (Zero Neriah Pro Coding)',
                  proAgencyUlt: isEn ? '100% Self-Service + 7-Phase Prompt Sequencer (Ultimate: +60m Call)' : '100% Mandiri + 7-Fase Prompt AI (Ultimate: +Sesi Telepon 60m)',
                  advisoryUmkm: isEn ? 'Advisory: Client Dev Team | UMKM Starter: 100% Coded by Neriah Pro' : 'Advisory: Tim Dev Klien | UMKM Starter: 100% Dikodingkan Neriah Pro',
                  fullMvp: isEn ? '✅ 100% Turnkey Coded, Tested & Deployed by Neriah Pro Senior Team' : '✅ 100% Dikodingkan, Diuji & Dideploy Penuh oleh Tim Senior Neriah Pro',
                },
                {
                  cat: 'COMMERCIAL',
                  param: isEn ? '02. One-Stop Commercial Gate & Entitlement Lock' : '02. Gerbang Satu Pintu & Kunci Lisensi Resmi',
                  sparkGrant: isEn ? 'Spark: 2x/Mo Guest Quota | CSR Grant: Requires Akta / SK Kemenkumham Verification' : 'Spark: Kuota Tamu 2x/Bln | Hibah CSR: Wajib Verifikasi Akta / SK Kemenkumham',
                  starterPass: isEn ? 'Starter (Rp 99K): Phase 1–3 | Single-App Pass (Rp 299K): 1 App Full Lock' : 'Starter (Rp 99rb): Fase 1–3 | Single-App Pass (Rp 299rb): Kunci Penuh 1 Aplikasi',
                  proAgencyUlt: isEn ? 'Pro (Rp 399K) / Agency 5-Seat (Rp 990K) / Ultimate (Rp 1.49M): Full Unlock' : 'Pro (Rp 399rb) / Agency 5-Kursi (Rp 990rb) / Ultimate (Rp 1,49 Jt): Terbuka Penuh',
                  advisoryUmkm: isEn ? 'Advisory (Rp 2.5M, 100% DP Credit) | UMKM Starter (Rp 3.75M, 50% Subsidy)' : 'Advisory (Rp 2,5 Jt, Potong DP 100%) | UMKM Starter (Rp 3,75 Jt, Subsidi 50%)',
                  fullMvp: isEn ? '✅ Algorithmic Man-Hours Escrow (30% DP / 50% Staging / 20% BAST)' : '✅ Escrow Jam Kerja Algoritmik (DP 30% / Staging 50% / BAST 20%)',
                },
                {
                  cat: 'COMMERCIAL',
                  param: isEn ? '03. Managed Sprint Capacity & Anti-Collision Lock' : '03. Kapasitas Sprint Terkelola & Kunci Anti-Tabrakan',
                  sparkGrant: isEn ? 'Instant Self-Service Cloud Compiler' : 'Kompilator Cloud Mandiri Instan',
                  starterPass: isEn ? 'Instant Self-Service Cloud Compiler' : 'Kompilator Cloud Mandiri Instan',
                  proAgencyUlt: isEn ? 'Instant Self-Service + Priority Queue (Ultimate: 1-on-1 Slot)' : 'Mandiri Instan + Antrean Prioritas (Ultimate: Slot 1-on-1)',
                  advisoryUmkm: isEn ? 'Advisory: 24–48h | UMKM Starter: Capped 2 Businesses/Month' : 'Advisory: 24–48 Jam | UMKM Starter: Kuota Maks 2 Usaha/Bulan',
                  fullMvp: isEn ? '✅ Strictly Capped 2–3 Parallel Projects/Batch (Zero Burnout / Zero Slop)' : '✅ Dibatasi Ketat Maks 2–3 Proyek/Batch (Fokus Penuh Tanpa AI-Slop)',
                },
                {
                  cat: 'COMMERCIAL',
                  param: isEn ? '04. Intellectual Property, Source Code & White-Label Rights' : '04. Hak Milik Intelektual, Source Code & White-Label',
                  sparkGrant: isEn ? '100% Client / Non-Profit Foundation Ownership (ISAK 35 Compliant)' : '100% Hak Milik Pemohon / Yayasan (Kepatuhan ISAK 35 / PSAK 109)',
                  starterPass: isEn ? '100% Client Ownership (Zero Vendor Lock-In)' : '100% Hak Milik Klien (Tanpa Vendor Lock-In)',
                  proAgencyUlt: isEn ? '100% Client Ownership + Agency Tier Includes Custom Brand White-Label PDF' : '100% Milik Klien + Paket Agency Termasuk PDF White-Label Merek Sendiri',
                  advisoryUmkm: isEn ? '100% Client Ownership (NDA Protected)' : '100% Hak Milik Klien (Dilindungi NDA Resmi)',
                  fullMvp: isEn ? '✅ 100% Full Source Code, DB, Docker & VPS Handover Upon Final BAST' : '✅ 100% Serah Terima Penuh Source Code, DB, Docker & VPS Saat BAST',
                },
                {
                  cat: 'ARCHITECTURE',
                  param: isEn ? '05. 20 Pillars of Software Factory OS v12.0 Paripurna' : '05. Kelengkapan 20 Pilar Software Factory OS v12.0 Paripurna',
                  sparkGrant: isEn ? 'Spark: Pillar 1–2 Audit | Verified CSR Grant: Complete 20 Pillars (Subsidized)' : 'Spark: Audit Pilar 1–2 | Hibah CSR Terverifikasi: Lengkap 20 Pilar (Subsidi)',
                  starterPass: isEn ? 'Starter: 5 Core Pillars | Single-App UMKM Pass: 20 Pillars (1 App Scope)' : 'Starter: 5 Pilar Inti | Single-App UMKM Pass: 20 Pilar (1 Aplikasi)',
                  proAgencyUlt: isEn ? '✅ Complete 20 Paripurna Pillars Specification & Export Bundle' : '✅ Spesifikasi & Ekspor Lengkap 20 Pilar Rekayasa Paripurna',
                  advisoryUmkm: isEn ? 'Advisory: 20-Pillar Blueprint + WBS | UMKM: Core Transactional Pillars Live' : 'Advisory: Cetak Biru 20 Pilar + WBS | UMKM: Pilar Transaksi Inti Live',
                  fullMvp: isEn ? '✅ 100% All 20 Paripurna Pillars Coded, Verified & Deployed to Production' : '✅ 100% Seluruh 20 Pilar Paripurna Dikodingkan & Live di Server Produksi',
                },
                {
                  cat: 'ARCHITECTURE',
                  param: isEn ? '06. PostgreSQL 16 Strict 26-Char ULID & Keyset Cursor O(1)' : '06. PostgreSQL 16 Strict ULID 26-Karakter & Keyset Cursor O(1)',
                  sparkGrant: isEn ? 'Spark: Entity Summary | CSR Grant: Full Strict ULID DDL + O(1) Cursor Spec' : 'Spark: Ringkasan Entitas | Hibah CSR: DDL Strict ULID + Spesifikasi Cursor O(1)',
                  starterPass: isEn ? 'Ready-to-Import Strict ULID SQL Schema (Zero UUID Cast Error)' : 'Skema SQL Strict ULID Siap Impor (Bebas Error Tipe UUID PostgreSQL)',
                  proAgencyUlt: isEn ? '✅ Full Strict ULID DDL + Quarterly Range Partitioning + 0% N+1 Guard' : '✅ DDL Strict ULID + Partisi Kuartalan PG16 + Proteksi 0% N+1',
                  advisoryUmkm: isEn ? 'Advisory: Bespoke ULID ERD | UMKM: Live ULID Transactional Database' : 'Advisory: ERD ULID Kustom | UMKM: Database Transaksional ULID Live',
                  fullMvp: isEn ? '✅ Sub-10ms O(1) Keyset Cursor & Strict ULID Live on Production PG16' : '✅ Kecepatan Sub-10ms O(1) Keyset Cursor & Strict ULID Live di PG16',
                },
                {
                  cat: 'ARCHITECTURE',
                  param: isEn ? '07. 9 Stack Topology Adapters (Monolith M1, Decoupled D1, Polyglot S1)' : '07. 9 Adapter Topologi Tech Stack (Monolith M1, Decoupled D1, Polyglot S1)',
                  sparkGrant: isEn ? 'Topology Recommendation & TCO Comparison' : 'Rekomendasi Topologi & Perbandingan Biaya Server (TCO)',
                  starterPass: isEn ? '1 Selected Stack Adapter Locked per Project' : '1 Adapter Tech Stack Terkunci Sesuai Proyek',
                  proAgencyUlt: isEn ? '✅ All 9 Stack Topologies (Laravel 13, Next.js 15, Go/Rust, OpenAPI 3.1)' : '✅ Bebas Pilih 9 Topologi (Laravel 13, Next.js 15, Go/Rust, OpenAPI 3.1)',
                  advisoryUmkm: isEn ? 'Advisory: Full Topology Matrix | UMKM: M1 Laravel 13 + Filament v5' : 'Advisory: Matriks Topologi | UMKM: M1 Laravel 13 + Filament v5',
                  fullMvp: isEn ? '✅ Turnkey M1 Monolith / D1 Headless / S1 Polyglot Coded to Spec' : '✅ Turnkey M1 Monolith / D1 Headless / S1 Polyglot Sesuai Kontrak',
                },
                {
                  cat: 'ARCHITECTURE',
                  param: isEn ? '08. Deterministic Decision Matrix match() & ACID Financial Ledger' : '08. Tabel Keputusan Deterministik match() & Buku Besar ACID DECIMAL(19,4)',
                  sparkGrant: isEn ? 'CSR Grant: Balanced Donor/Fund Ledger Rules (ISAK 35)' : 'Hibah CSR: Aturan Jurnal Dana Umat/Donatur Seimbang (ISAK 35)',
                  starterPass: isEn ? 'Single-App Pass: Deterministic Pricing/Discount match() Compiler' : 'Single-App Pass: Kompilator Aturan Diskon/Tarif match() Deterministik',
                  proAgencyUlt: isEn ? '✅ Spreadsheet Decision Matrix -> PHP 8.4+ match() + Double-Entry Lock' : '✅ Spreadsheet Decision Matrix -> PHP 8.4+ match() + Kunci Debit==Kredit',
                  advisoryUmkm: isEn ? 'Included in Financial & Inventory State Machine Spec' : 'Termasuk dalam Spesifikasi State Machine Keuangan & Stok',
                  fullMvp: isEn ? '✅ 100% Implemented with Pessimistic Row Locking (SELECT FOR UPDATE)' : '✅ 100% Terimplementasi dengan Pessimistic Locking (SELECT FOR UPDATE)',
                },
                {
                  cat: 'ARCHITECTURE',
                  param: isEn ? '09. Brownfield Legacy SQL Importer & Zero-Data-Loss Shadow ULID' : '09. Impor Database SQL Lama (Brownfield) & Migrasi Shadow ULID',
                  sparkGrant: isEn ? 'CSR Grant: Legacy Donor Spreadsheet/SQL Migration Guide' : 'Hibah CSR: Panduan Migrasi Data Donatur/Jemaat Lama',
                  starterPass: isEn ? 'Single-App Pass: Legacy Schema Audit & Shadow ULID Plan' : 'Single-App Pass: Audit Skema Lama & Rencana Shadow ULID',
                  proAgencyUlt: isEn ? '✅ Automated Legacy SQL Parser + Dual-Write Shadow ULID Migration Script' : '✅ Parser SQL Lama Otomatis + Skrip Migrasi Dual-Write Shadow ULID',
                  advisoryUmkm: isEn ? 'Advisory: Architect-Guided Legacy Cutover Playbook' : 'Advisory: Playbook Cutover Sistem Lama Didampingi Arsitek',
                  fullMvp: isEn ? '✅ Turnkey Legacy Data Migration & Reconciliation Verified by Engineers' : '✅ Migrasi Data Lama Terima Beres & Rekonsiliasi oleh Tim Engineer',
                },
                {
                  cat: 'UI_UX',
                  param: isEn ? '10. Zone A: 12 Functional Sectors × 36 Internal Operational UI Cockpits' : '10. Zona A: 12 Sektor Fungsional × 36 Kokpit UI Operasional Internal',
                  sparkGrant: isEn ? 'Visual UI Preview + CSR Sector 12 Donor Transparency Cockpit' : 'Pratinjau Visual UI + Kokpit Transparansi Donatur Sektor 12',
                  starterPass: isEn ? 'Locks Selected Sector Visual UI Variant (e.g. POS Split, Clinical 3-Pane)' : 'Mengunci Varian Visual UI Sektor Terpilih (POS Split, Klinis 3-Pane, dll.)',
                  proAgencyUlt: isEn ? '✅ Full 36 Visual UI Blueprints + Keyboard Shortcuts + Realistic ID Seeder' : '✅ 36 Cetak Biru Visual UI + Shortcut Keyboard + Seeder Data Indonesia',
                  advisoryUmkm: isEn ? 'Advisory: Ergonomic Spec | UMKM: Built Primary Sector Cockpit' : 'Advisory: Spesifikasi Ergonomi | UMKM: Kokpit Utama Sektor Terbangun',
                  fullMvp: isEn ? '✅ 100% Custom Coded Multi-Pane Sector Cockpit + Hardware/Thermal Print' : '✅ 100% Dikodingkan Kokpit Multi-Pane Spesifik Industri + Cetak Thermal',
                },
                {
                  cat: 'UI_UX',
                  param: isEn ? '11. Zone B: 6 Public Scrollytelling 60fps Showcase Presets (scrolly_telling.md)' : '11. Zona B: 6 Preset Scrollytelling 60fps Halaman Publik (scrolly_telling.md)',
                  sparkGrant: isEn ? 'CSR Grant: Story-Driven Donor Impact Scrollytelling Spec' : 'Hibah CSR: Spesifikasi Scrollytelling Laporan Dampak Sosial/Donasi',
                  starterPass: isEn ? 'Single-App Pass: CSS Scroll-Driven Showcase Contract + Reduced-Motion Guard' : 'Single-App Pass: Kontrak Showcase CSS Scroll-Driven + Aksesibilitas',
                  proAgencyUlt: isEn ? '✅ All 6 Scrollytelling Presets (Sticky Scrubbing, Bento 3D, Before/After)' : '✅ 6 Preset Scrollytelling (Sticky Scrubbing, Bento 3D, Before/After)',
                  advisoryUmkm: isEn ? 'Included in Public Conversion Landing Architecture' : 'Termasuk dalam Arsitektur Landing Page Konversi Publik',
                  fullMvp: isEn ? '✅ 60fps GPU-Accelerated Public Showcase Coded with >95 Lighthouse Score' : '✅ Halaman Publik 60fps Akselerasi GPU Dikodingkan (Skor Lighthouse >95)',
                },
                {
                  cat: 'UI_UX',
                  param: isEn ? '12. Adaptive Dual-Persona Studio (5-Step Business vs 3-Pane Architect)' : '12. Studio Dual-Persona Adaptif (Mode Bisnis 5-Langkah vs Arsitek 3-Pane)',
                  sparkGrant: isEn ? '5-Step Business Storyboard Mode (Zero Jargon)' : 'Mode Bisnis / Awam 5-Langkah (Bebas Istilah Teknis Rumit)',
                  starterPass: isEn ? 'Both 5-Step Business Mode & 3-Pane Architect Studio' : 'Mendukung Mode Bisnis 5-Langkah & Mode Arsitek 3-Pane',
                  proAgencyUlt: isEn ? '✅ Instant Toggle + Co-Design Alignment Room (Business Intent <-> Tech Spec)' : '✅ Toggle Instan + Co-Design Alignment Room (Bahasa Bisnis <-> Teknis)',
                  advisoryUmkm: isEn ? 'Live Co-Design Alignment Room with Principal Architect' : 'Ruang Kolaborasi Co-Design Langsung Bersama Principal Architect',
                  fullMvp: isEn ? '✅ Full Co-Design Room + Annotated Visual Wireframe Sign-Off per Sprint' : '✅ Ruang Co-Design Penuh + Persetujuan Wireframe Visual per Sprint',
                },
                {
                  cat: 'UI_UX',
                  param: isEn ? '13. 6 Global Tier-1 Jurisdictions, Bilingual ID/EN & Logical RTL/LTR CSS' : '13. 6 Yurisdiksi Regulasi Global Tier-1, Dwibahasa ID/EN & CSS Logis RTL/LTR',
                  sparkGrant: isEn ? 'ID (UU PDP / ISAK 35) & EN International Parity' : 'Paritas Bahasa Indonesia (UU PDP / ISAK 35) & Inggris (EN)',
                  starterPass: isEn ? 'ID, EN, SG, EU, US, AR (RTL Logical CSS Properties Ready)' : 'Mendukung Regulasi ID, EN, SG, EU, US, AR (Siap CSS Logis RTL Arab)',
                  proAgencyUlt: isEn ? '✅ All 6 Jurisdictions (UU PDP, PDPA, GDPR, HIPAA/SOC2, ZATCA RTL, PIPL)' : '✅ 6 Yurisdiksi Penuh (UU PDP, PDPA, GDPR, HIPAA/SOC2, ZATCA RTL, PIPL)',
                  advisoryUmkm: isEn ? 'Tailored to Target Country Regulatory Compliance' : 'Disesuaikan dengan Regulasi Negara Target Operasi Klien',
                  fullMvp: isEn ? '✅ TranslatableTabs ID/EN + Zero Page-Level LocaleSwitcher + Regional Inputs' : '✅ TranslatableTabs ID/EN + Tanpa Dropdown Bahasa Membingungkan',
                },
                {
                  cat: 'UI_UX',
                  param: isEn ? '14. Universal Input Hygiene (RFC Email, Regional Separator, E.164 Phone)' : '14. Standar Higienitas Input (Email RFC, Pemisah Ribuan, Telepon Zona Negara)',
                  sparkGrant: isEn ? '✅ Enforced Across All Forms (Auto-Strips Leading 0 on Phone)' : '✅ Aktif di Seluruh Form (Otomatis Hapus Angka 0 di Depan Nomor HP)',
                  starterPass: isEn ? '✅ Enforced + Exported in InputHygieneStandard.php & inputHygiene.js' : '✅ Aktif + Disertakan di InputHygieneStandard.php & inputHygiene.js',
                  proAgencyUlt: isEn ? '✅ Enforced + Automated Pest Hygiene Contract Assertions' : '✅ Aktif + Pengujian Otomatis Kontrak Higienitas Input Pest',
                  advisoryUmkm: isEn ? '✅ Standard in All Studio Forms & Checkout' : '✅ Standar Baku di Seluruh Form Studio & Kasir',
                  fullMvp: isEn ? '✅ 100% Built into Every Filament v5, Livewire 4 & React Form' : '✅ 100% Terpasang di Seluruh Form Filament v5, Livewire 4 & React',
                },
                {
                  cat: 'AI_SECURITY',
                  param: isEn ? '15. Phased Prompt Sequencer (Phase 1–7), Kahn DAG & 3-Sec CLI /init/{ULID}' : '15. 7-Fase Prompt AI Berurutan, Kahn DAG Chunker & CLI 3-Detik /init/{ULID}',
                  sparkGrant: isEn ? 'Spark: Phase 1–2 Only | CSR Grant: Full Phase 1–7 + CLI /init/{ULID}' : 'Spark: Hanya Fase 1–2 | Hibah CSR: Lengkap Fase 1–7 + CLI /init/{ULID}',
                  starterPass: isEn ? 'Starter: Phase 1–3 | Single-App Pass: Full Phase 1–7 + ./verify-blueprint.sh' : 'Starter: Fase 1–3 | Single-App Pass: Lengkap Fase 1–7 + ./verify-blueprint.sh',
                  proAgencyUlt: isEn ? '✅ Full Phase 1–7 + SYMBOL_MANIFEST.json + curl -sL /init/{ULID} | bash' : '✅ Lengkap Fase 1–7 + SYMBOL_MANIFEST.json + curl -sL /init/{ULID} | bash',
                  advisoryUmkm: isEn ? 'Included for In-House Team Execution' : 'Disertakan untuk Eksekusi Tim Internal Klien',
                  fullMvp: isEn ? '✅ Executed & Verified 100% Green by Neriah Pro Engineers' : '✅ Dieksekusi & Diverifikasi 100% Hijau oleh Tim Engineer Neriah Pro',
                },
                {
                  cat: 'AI_SECURITY',
                  param: isEn ? '16. 49 Native AI Tools (KAG + CAG + Hybrid RAG) & Live 2-Way MCP Bridge' : '16. 49 Native AI Tool Calling (KAG + CAG + RAG) & Jembatan Live MCP 2-Arah',
                  sparkGrant: isEn ? 'CSR Grant: Live .mcp.json Bridge for Claude Code, Cursor & Antigravity' : 'Hibah CSR: Jembatan .mcp.json Live untuk Claude Code, Cursor & Antigravity',
                  starterPass: isEn ? 'Single-App Pass: .mcp.json Bridge + SYNTAX_LOCK_2026.md Dictionary' : 'Single-App Pass: Jembatan .mcp.json + Kamus SYNTAX_LOCK_2026.md',
                  proAgencyUlt: isEn ? '✅ Full 2-Way MCP Sync (pos_get_phase_prompt / pos_mark_phase_verified)' : '✅ Sinkronisasi 2-Arah MCP (pos_get_phase_prompt / pos_mark_phase_verified)',
                  advisoryUmkm: isEn ? 'Advisory: AI Architecture Mapping | UMKM: Admin Copilot Ready' : 'Advisory: Pemetaan AI | UMKM: Siap Asisten Admin Copilot',
                  fullMvp: isEn ? '✅ Full Backend Admin AI Agentic Engine (KAG, CAG, RAG & 49 Tools)' : '✅ Engine AI Agentic Backend Admin Penuh (KAG, CAG, RAG & 49 Tool)',
                },
                {
                  cat: 'AI_SECURITY',
                  param: isEn ? '17. NeriahPro AgentShield (3-Gate Firewall), Living CVE & Red-Team Suite' : '17. NeriahPro AgentShield (Firewall 3-Gerbang), Living CVE & Red-Team Suite',
                  sparkGrant: isEn ? 'CSR Grant: Full AgentShield & TenantIsolationSecurityTest.php' : 'Hibah CSR: Proteksi AgentShield & TenantIsolationSecurityTest.php',
                  starterPass: isEn ? 'Single-App Pass: Phase 7 Red-Team IDOR/Prompt-Injection Test Suite' : 'Single-App Pass: Suite Uji Red-Team Fase 7 (Anti-IDOR & Prompt Injection)',
                  proAgencyUlt: isEn ? '✅ AgentShield Firewall + Living CVE Autopatch + Composer/NPM Lock Guard' : '✅ Firewall AgentShield + Living CVE Autopatch + Kunci Paket Aman',
                  advisoryUmkm: isEn ? 'Security Threat Modeling & OWASP Top 10 Hardening' : 'Pemodelan Ancaman Keamanan & Hardening OWASP Top 10',
                  fullMvp: isEn ? '✅ Pen-Tested & Hardened with CSP, Honeypot, Rate Limiter & AgentShield' : '✅ Lolos Uji Penetrasi dengan CSP Ketat, Honeypot, Rate Limit & AgentShield',
                },
                {
                  cat: 'AI_SECURITY',
                  param: isEn ? '18. Zero-Width Canary IP Watermark & SHA-256 Cryptographic Audit Log (.xlsx)' : '18. Segel Canary IP Tak Kasat Mata & Audit Log Kriptografi SHA-256 (.xlsx)',
                  sparkGrant: isEn ? 'Cryptographic Provenance Hash Seal' : 'Segel Hash Keaslian Dokumen Kriptografi',
                  starterPass: isEn ? 'Canary IP Watermark + Immutable SHA-256 Scope Hash' : 'Tanda Air Canary IP + Segel Hash Scope SHA-256 Anti-Manipulasi',
                  proAgencyUlt: isEn ? '✅ Canary IP + SHA-256 Hash-Chain Audit Log + 3-Sheet CPA Excel Export' : '✅ Canary IP + Rantai Hash Audit Log SHA-256 + Ekspor Excel Akuntan 3-Sheet',
                  advisoryUmkm: isEn ? 'Tamper-Proof SPK Contract SHA-256 Signature' : 'Tanda Tangan Kontrak SPK Bersegel SHA-256 Anti-Sengketa',
                  fullMvp: isEn ? '✅ Full Forensic Audit Log Explorer + 1-Click CPA Excel (.xlsx) in Admin' : '✅ Penjelajah Audit Log Forensik + Ekspor Excel (.xlsx) 1-Klik di Admin',
                },
                {
                  cat: 'SEO_GEO_V12',
                  param: isEn ? '19. Multi-Graph Schema.org JSON-LD (@graph Across All 12 Sectors)' : '19. Multi-Graph Schema.org JSON-LD (@graph Spesifik 12 Sektor Industri)',
                  sparkGrant: isEn ? 'CSR Grant: NGO, DonateAction, Project & MonetaryGrant Schema Graph' : 'Hibah CSR: Skema JSON-LD NGO, DonateAction, Project & MonetaryGrant',
                  starterPass: isEn ? 'Single-App Pass: Organization, SoftwareApplication, FAQPage & Sector Graph' : 'Single-App Pass: Graph Organization, SoftwareApplication, FAQPage & Sektor',
                  proAgencyUlt: isEn ? '✅ Full 12-Sector @graph (Product, Lodging, MedicalClinic, Course, Menu, Event)' : '✅ @graph 12 Sektor (Product, Lodging, MedicalClinic, Course, Menu, Event)',
                  advisoryUmkm: isEn ? 'LocalBusiness / Store / Restaurant Schema.org JSON-LD Configured' : 'Terpasang Schema.org JSON-LD LocalBusiness / Toko / Restoran',
                  fullMvp: isEn ? '✅ Dynamic Admin-Editable Schema.org + Cache::rememberForever() Auto-Flush' : '✅ Schema.org Dinamis via Admin + Cache::rememberForever() Auto-Flush',
                },
                {
                  cat: 'SEO_GEO_V12',
                  param: isEn ? '20. GEO Citation Authority (/llms.txt, /llms-full.txt & 50-Word Answer Capsules)' : '20. Otoritas Sitasi AI GEO (/llms.txt, /llms-full.txt & Kapsul Jawaban 50 Kata)',
                  sparkGrant: isEn ? 'CSR Grant: Public Impact Citation Feed for AI Search Engines' : 'Hibah CSR: Umpan Sitasi Dampak Sosial untuk Mesin Pencari AI',
                  starterPass: isEn ? 'Single-App Pass: Auto-Generated /llms.txt & Semantic HTML Comparison Table' : 'Single-App Pass: Generator Otomatis /llms.txt & Tabel HTML Semantik',
                  proAgencyUlt: isEn ? '✅ Top-3 AI Citation Engine (ChatGPT Search, Perplexity, Claude, Gemini)' : '✅ Mesin Sitasi Top-3 AI (ChatGPT Search, Perplexity, Claude, Gemini)',
                  advisoryUmkm: isEn ? 'Included in Public SEO/GEO Discovery Blueprint' : 'Termasuk dalam Cetak Biru Penemuan SEO/GEO Publik',
                  fullMvp: isEn ? '✅ Live /llms.txt, /llms-full.txt & Semantic Answer Capsules on Production' : '✅ Endpoint /llms.txt, /llms-full.txt & Kapsul Jawaban Semantik Aktif Live',
                },
                {
                  cat: 'SEO_GEO_V12',
                  param: isEn ? '21. IndexNow Instant Ping, AI Crawler robots.txt & Local 1200×630 OG Card' : '21. IndexNow Instant Ping, robots.txt Bot AI & Kartu OG 1200×630 Lokal',
                  sparkGrant: isEn ? 'CSR Grant: Local PHP GD/SVG OpenGraph & QR Verification Card' : 'Hibah CSR: Kartu OpenGraph 1200×630 & QR Lokal Berbasis PHP GD/SVG',
                  starterPass: isEn ? 'Single-App Pass: IndexNow Key + OAI-SearchBot/PerplexityBot Allowlist' : 'Single-App Pass: Kunci IndexNow + Whitelist Bot AI di robots.txt',
                  proAgencyUlt: isEn ? '✅ Zero-CDN Local 1200×630 OG PNG/SVG + IndexNow Ping on Model Save' : '✅ Kartu OG 1200×630 Lokal (Zero CDN) + Ping IndexNow Otomatis',
                  advisoryUmkm: isEn ? 'Automated Sitemap.xml & Social Share Preview Cards' : 'Sitemap.xml Otomatis & Kartu Pratinjau Share Media Sosial',
                  fullMvp: isEn ? '✅ 1-Click IndexNow Trigger in Admin Health Pulse + Local OG Generator' : '✅ Tombol Ping IndexNow 1-Klik di Admin Health Pulse + Generator OG Lokal',
                },
                {
                  cat: 'SEO_GEO_V12',
                  param: isEn ? '22. UU PDP No. 27/2022 Privacy Vault (AES-256 + HMAC-SHA256 Blind Index O(1))' : '22. Brankas Privasi UU PDP No. 27/2022 (AES-256 + Blind Index HMAC-SHA256 O(1))',
                  sparkGrant: isEn ? 'CSR Grant: Encrypted Donor/Patient NIK & Phone with O(1) Blind Index' : 'Hibah CSR: Enkripsi NIK/HP Donatur & Pasien dengan Blind Index O(1)',
                  starterPass: isEn ? 'Single-App Pass: HasEncryptedPiiWithBlindIndex Trait Included' : 'Single-App Pass: Disertakan Trait HasEncryptedPiiWithBlindIndex',
                  proAgencyUlt: isEn ? '✅ 64-Char HMAC-SHA256 Blind Index + 1-Click JSON Portability & Safe Erasure' : '✅ Blind Index 64-Karakter + Ekspor JSON 1-Klik & Anonimisasi Aman-Jurnal',
                  advisoryUmkm: isEn ? 'UU PDP Compliance Architecture & Consent Banner' : 'Arsitektur Kepatuhan UU PDP & Banner Persetujuan Privasi',
                  fullMvp: isEn ? '✅ Live Self-Service Privacy Vault in /customer/dashboard (Preserves Ledger)' : '✅ Brankas Privasi Mandiri Aktif di /customer/dashboard (Jurnal Utuh)',
                },
                {
                  cat: 'SEO_GEO_V12',
                  param: isEn ? '23. Bad-Signal Offline Resilience (IndexedDB ULID Outbox + PWA Background Sync)' : '23. Ketahanan Sinyal Buruk (IndexedDB ULID Outbox + Sinkronisasi Latar PWA)',
                  sparkGrant: isEn ? 'CSR Grant: Field Relief/Donation Offline Queue with X-Idempotency-Key' : 'Hibah CSR: Antrean Offline Relawan Lapangan Berproteksi X-Idempotency-Key',
                  starterPass: isEn ? 'Single-App Pass: /sw-offline-outbox.js + offlineOutboxQueue.js Bundle' : 'Single-App Pass: Paket /sw-offline-outbox.js + offlineOutboxQueue.js',
                  proAgencyUlt: isEn ? '✅ Client-Side 26-Char ULID Minting + Auto Background Replay on Reconnect' : '✅ Cetak ULID 26-Karakter di Lokal + Sinkronisasi Otomatis Saat Sinyal Pulih',
                  advisoryUmkm: isEn ? 'UMKM Cashier & Courier Spotty-Signal Protection' : 'Proteksi Kasir & Kurir Saat Sinyal Internet Tidak Stabil',
                  fullMvp: isEn ? '✅ Zero-Duplicate Offline-First Mutation Engine Verified Under Chaos Test' : '✅ Engine Transaksi Anti-Putus & Anti-Dobel Teruji Simulasi Chaos',
                },
                {
                  cat: 'SEO_GEO_V12',
                  param: isEn ? '24. Post-BAST 90-Day Contractual SLA Bug-Warranty vs Paid Delta v1.1 Triage' : '24. Triase Garansi Bug SLA 90 Hari Pasca-BAST vs Tambahan Fitur Berbayar v1.1',
                  sparkGrant: isEn ? 'Self-Service Iterative Delta Generator (v1.0 -> v1.1)' : 'Generator Delta Iteratif Mandiri (v1.0 -> v1.1)',
                  starterPass: isEn ? '30-Day Parameter Revision Window + Surgical Delta Spec' : 'Jendela Revisi Parameter 30 Hari + Spesifikasi Delta Aditif',
                  proAgencyUlt: isEn ? '6–12 Months AI Re-Prompt & Additive Schema Delta (Zero DROP TABLE)' : '6–12 Bulan Regenerasi AI & Delta Skema Aditif (Tanpa DROP TABLE)',
                  advisoryUmkm: isEn ? 'UMKM Starter: 30-Day Post-Live SLA Bug Warranty' : 'UMKM Starter: Garansi Bug SLA 30 Hari Pasca-Live',
                  fullMvp: isEn ? '✅ Auto 90-Day SLA Timer + Free Bugfix Prompt vs Paid CR v1.1 Triage' : '✅ Timer SLA 90 Hari Otomatis + Triase Perbaikan Bug Rp 0 vs Addendum v1.1',
                },
                {
                  cat: 'SEO_GEO_V12',
                  param: isEn ? '25. Pre-Migration Atomic pg_dump Snapshot & 1-Command ./deploy.sh rollback' : '25. Snapshot Database pg_dump Pra-Migrasi & Rollback 1-Perintah (./deploy.sh rollback)',
                  sparkGrant: isEn ? 'CSR Grant: Safe Deployment & Backup Runbook' : 'Hibah CSR: Panduan Deployment Aman & Runbook Backup',
                  starterPass: isEn ? 'Single-App Pass: 7-Scenario deploy.sh Script Included' : 'Single-App Pass: Disertakan Skrip deploy.sh 7 Skenario',
                  proAgencyUlt: isEn ? '✅ Pre-Migrate .sql.gz Auto-Snapshot (7-Day Retention) + Scenario 7 Rollback' : '✅ Snapshot Otomatis .sql.gz Pra-Migrasi (Retensi 7 Hari) + Rollback Skenario 7',
                  advisoryUmkm: isEn ? 'Automated Daily Cloud VPS Backups' : 'Backup Harian Otomatis di Server Cloud VPS',
                  fullMvp: isEn ? '✅ Zero-Downtime CI/CD + Instant Atomic Code & DB Rollback Verified' : '✅ CI/CD Tanpa Downtime + Pemulihan Instan Kode & DB Terverifikasi',
                },
                {
                  cat: 'SEO_GEO_V12',
                  param: isEn ? '26. Local Webhook Chaos Simulator (APP_ENV !== "production") & Idempotency' : '26. Simulator Chaos Webhook Lokal (Non-Produksi) & Idempotensi Pembayaran',
                  sparkGrant: isEn ? 'Idempotent Webhook Signature Verification Spec' : 'Spesifikasi Verifikasi Signature Webhook Idempoten',
                  starterPass: isEn ? 'Single-App Pass: QRIS/VA Webhook Simulator Script (Blocked 403 in Prod)' : 'Single-App Pass: Simulator Webhook QRIS/VA (Otomatis Diblokir 403 di Prod)',
                  proAgencyUlt: isEn ? '✅ 1-Click Simulate QRIS Success, Expired VA & 3x Duplicate Attack' : '✅ Simulasi 1-Klik Lunas QRIS, VA Kadaluarsa & Serangan 3x Webhook Ganda',
                  advisoryUmkm: isEn ? 'Midtrans QRIS & Virtual Account Anti-Double-Pay Guard' : 'Proteksi Anti-Dobel Bayar Midtrans QRIS & Virtual Account',
                  fullMvp: isEn ? '✅ Live Chaos Sandbox Console in Admin Health Pulse + Auto-Refund/Credit' : '✅ Konsol Simulasi Chaos di Admin Health Pulse + Proteksi Dobel Bayar',
                },
                {
                  cat: 'SEO_GEO_V12',
                  param: isEn ? '27. Multi-Entity Holding, Subsidiary & Branch Consolidation (parent_organization_id)' : '27. Konsolidasi Holding, Anak Perusahaan & Multi-Cabang (parent_organization_id)',
                  sparkGrant: isEn ? 'CSR Grant: Central Foundation -> Regional Chapter Consolidation' : 'Hibah CSR: Konsolidasi Yayasan Pusat -> Cabang Daerah',
                  starterPass: isEn ? 'Single-Organization Workspace Isolation' : 'Isolasi Workspace Organisasi Tunggal',
                  proAgencyUlt: isEn ? '✅ Self-Referencing ULID Hierarchy + Inter-Company Elimination Spec' : '✅ Hierarki ULID Induk-Cabang + Spesifikasi Eliminasi Jurnal Antar-Cabang',
                  advisoryUmkm: isEn ? 'Advisory: Holding Consolidation Blueprint | UMKM: Multi-Outlet Ready' : 'Advisory: Cetak Biru Holding | UMKM: Siap Ekspansi Multi-Cabang',
                  fullMvp: isEn ? '✅ Live Holding -> Subsidiary -> Branch Rollup & Eliminated P&L Reports' : '✅ Konsolidasi Holding -> Anak -> Cabang & Laporan Laba-Rugi Tereliminasi',
                },
                {
                  cat: 'AI_SECURITY',
                  param: isEn ? '28. Role-Based Smart Guide Backend Admin & Printable A4 BAST SOP Manual' : '28. Smart Guide Backend Admin Berbasis Peran & Buku SOP A4 Siap Cetak BAST',
                  sparkGrant: isEn ? 'CSR Grant: Volunteer & Treasurer Onboarding SOP Guide' : 'Hibah CSR: Buku Panduan SOP Relawan & Bendahara Yayasan',
                  starterPass: isEn ? 'Single-App Pass: Auto-Generated CLIENT_SOP_AND_TRAINING_MANUAL.md' : 'Single-App Pass: Generator Otomatis CLIENT_SOP_AND_TRAINING_MANUAL.md',
                  proAgencyUlt: isEn ? '✅ Interactive Filament v5 SmartGuidePage (18 Sections ID/EN) + A4 SOP Pack' : '✅ SmartGuidePage Filament v5 Interaktif (18 Bab ID/EN) + Buku SOP A4',
                  advisoryUmkm: isEn ? 'Staff Training Guide & Video/Document Handover' : 'Panduan Pelatihan Staf & Dokumen Serah Terima Operasional',
                  fullMvp: isEn ? '✅ Live Role-Filtered Smart Guide + Signed Digital BAST & Training Session' : '✅ Smart Guide Sesuai Role RBAC + BAST Digital & Sesi Pelatihan Tim',
                },
              ];

              const filteredMatrixRows = matrixRows.filter((row) => {
                const matchCat = matrixCategoryFilter === 'ALL' || row.cat === matrixCategoryFilter;
                if (!matchCat) return false;
                if (!matrixSearchQuery.trim()) return true;
                const q = matrixSearchQuery.toLowerCase();
                return (
                  row.param.toLowerCase().includes(q) ||
                  row.sparkGrant.toLowerCase().includes(q) ||
                  row.starterPass.toLowerCase().includes(q) ||
                  row.proAgencyUlt.toLowerCase().includes(q) ||
                  row.advisoryUmkm.toLowerCase().includes(q) ||
                  row.fullMvp.toLowerCase().includes(q)
                );
              });

              const cellPad = matrixDensity === 'compact' ? 'py-2 px-2.5 text-[11px]' : matrixDensity === 'relaxed' ? 'py-4 px-4 text-xs' : 'py-3 px-3 text-xs';

              const twentyPillarsComparison = [
                { no: '01', name: isEn ? 'Intake & Legacy SQL' : 'Intake & Impor SQL Lama', slop: isEn ? 'Manual copy-paste, loses legacy FKs' : 'Copy-paste manual, relasi tabel lama rusak', pos: isEn ? 'MarkItDown OCR + Shadow ULID Zero-Loss Cutover' : 'MarkItDown OCR + Migrasi Shadow ULID Tanpa Hilang Data' },
                { no: '02', name: isEn ? '26-Parameter PRD & WBS' : 'PRD 26 Parameter & WBS', slop: isEn ? 'Vague 1-page chat summary' : 'Ringkasan chat 1 halaman yang kabur', pos: isEn ? '26-Param PRD, Mermaid Gantt & OpenAPI 3.1 Contract' : 'PRD 26 Parameter, Mermaid Gantt & Kontrak OpenAPI 3.1' },
                { no: '03', name: isEn ? 'PG16 Strict ULID & O(1)' : 'PG16 Strict ULID & Cursor O(1)', slop: isEn ? 'AUTO_INCREMENT + slow OFFSET paginate()' : 'AUTO_INCREMENT + paginate() OFFSET yang lambat', pos: isEn ? '26-Char ULID PK + Sub-10ms Keyset cursorPaginate()' : 'ULID 26-Karakter + Keyset cursorPaginate() O(1) <10ms' },
                { no: '04', name: isEn ? '12 Sectors × 36 Visual UI' : '12 Sektor × 36 Visual UI', slop: isEn ? 'Generic CRUD tables for every industry' : 'Tabel CRUD generik yang sama untuk semua bisnis', pos: isEn ? '36 Deterministic Sector Cockpits + Keyboard Shortcuts' : '36 Kokpit Spesifik Industri + Pintasan Keyboard' },
                { no: '05', name: isEn ? 'Zone B 60fps Scrollytelling' : 'Zona B Scrollytelling 60fps', slop: isEn ? 'Heavy JS libraries & gaudy gradients' : 'Animasi berat bikin lag & gradien murahan', pos: isEn ? '6 CSS Scroll-Driven 60fps Presets + Reduced-Motion' : '6 Preset CSS Scroll-Driven 60fps + Aksesibilitas' },
                { no: '06', name: isEn ? 'Dual-Persona & Co-Design' : 'Dual-Persona & Co-Design', slop: isEn ? 'Clients confused by raw code/SQL' : 'Klien awam bingung melihat kode/SQL mentah', pos: isEn ? '5-Step Business Mode <-> 3-Pane Architect + Co-Design' : 'Mode Bisnis 5-Langkah <-> Arsitek 3-Pane + Co-Design' },
                { no: '07', name: isEn ? 'Decision Matrix match()' : 'Tabel Keputusan match()', slop: isEn ? 'Spaghetti nested if/else & float bugs' : 'Kode spaghetti if-else bersarang & salah hitung koma', pos: isEn ? 'Deterministic PHP 8.4+ match() + DECIMAL(19,4) ACID' : 'Kompilasi PHP 8.4+ match() + Akuntansi DECIMAL(19,4)' },
                { no: '08', name: isEn ? '7-Phase Prompts & /init' : '7-Fase Prompt & CLI /init', slop: isEn ? 'Single giant prompt -> AI context overflow' : 'Satu prompt raksasa -> AI halusinasi & error', pos: isEn ? '7 Kahn-Sorted Phases + 3-Sec curl /init/{ULID} | bash' : '7 Fase Terurut Kahn DAG + Inisialisasi CLI 3 Detik' },
                { no: '09', name: isEn ? 'Live 2-Way MCP Bridge' : 'Jembatan Live MCP 2-Arah', slop: isEn ? 'Zero IDE awareness of blueprint state' : 'IDE tidak tahu status progres cetak biru', pos: isEn ? 'Live .mcp.json + 49 Native AI Tools (KAG/CAG/RAG)' : 'Sinkronisasi .mcp.json + 49 Tool AI (KAG/CAG/RAG)' },
                { no: '10', name: isEn ? 'AgentShield & Living CVE' : 'AgentShield & Living CVE', slop: isEn ? 'Vulnerable to Prompt Injection & IDOR' : 'Rawan kebocoran IDOR & serangan Prompt Injection', pos: isEn ? '3-Gate AgentShield + Phase 7 Red-Team + CVE Autopatch' : 'Firewall AgentShield + Uji Red-Team Fase 7 + Patch CVE' },
                { no: '11', name: isEn ? 'Canary IP & SHA-256 Audit' : 'Canary IP & Audit SHA-256', slop: isEn ? 'Mutable logs & unprotected documents' : 'Log mudah dihapus & dokumen rawan dibajak', pos: isEn ? 'Zero-Width Canary IP + SHA-256 Hash Chain (.xlsx)' : 'Tanda Air Canary IP + Rantai Hash SHA-256 (.xlsx)' },
                { no: '12', name: isEn ? 'One-Stop Commercial Gate' : 'Gerbang Komersial Satu Pintu', slop: isEn ? 'Inconsistent free bypass vs pricing cards' : 'Tombol loncat gratis tidak sinkron dengan tabel harga', pos: isEn ? 'Unified Step A–F Configurator + Backend Entitlement Lock' : 'Konfigurator Terpadu Langkah A–F + Kunci Lisensi Server' },
                { no: '13', name: isEn ? 'CSR Non-Profit Grant (Rp 0)' : 'Hibah Sosial CSR (Rp 0)', slop: isEn ? 'Ambiguous Rp 0 pricing abused by commercial' : 'Harga Rp 0 ambigu & disalahgunakan bisnis komersial', pos: isEn ? 'Rp 1.49M Subsidized 100% -> Rp 0 (Mandatory Akta Check)' : 'Nilai Rp 1,49 Jt Disubsidi 100% -> Rp 0 (Wajib Akta/SK)' },
                { no: '14', name: isEn ? '12-Sector Schema.org @graph' : 'Schema.org @graph 12 Sektor', slop: isEn ? 'Missing or generic WebPage JSON-LD' : 'Tanpa JSON-LD atau hanya WebPage generik', pos: isEn ? 'Multi-Graph @graph Tailored to All 12 Industry Sectors' : 'Multi-Graph @graph Spesifik 12 Sektor Industri' },
                { no: '15', name: isEn ? 'GEO /llms.txt & OG Cards' : 'GEO /llms.txt & Kartu OG', slop: isEn ? 'Invisible to ChatGPT, Perplexity & Claude' : 'Tidak terbaca oleh ChatGPT Search, Perplexity & Claude', pos: isEn ? '/llms.txt + 50-Word Capsules + IndexNow + Local OG' : '/llms.txt + Kapsul 50 Kata + IndexNow + OG Lokal' },
                { no: '16', name: isEn ? 'UU PDP Blind Index Vault' : 'Brankas Blind Index UU PDP', slop: isEn ? 'Plaintext NIK/Phone or broken WHERE search' : 'NIK/HP teks telanjang atau pencarian SQL lumpuh', pos: isEn ? 'AES-256 + 64-Char HMAC-SHA256 Blind Index O(1) + Redact' : 'AES-256 + Blind Index HMAC-SHA256 O(1) + Anonimisasi' },
                { no: '17', name: isEn ? 'Offline Outbox PWA Sync' : 'Offline Outbox PWA Anti-Putus', slop: isEn ? 'Lost transactions & double-submit on 3G' : 'Transaksi hilang atau tercatat ganda saat sinyal buruk', pos: isEn ? 'IndexedDB 26-Char ULID Outbox + X-Idempotency-Key Sync' : 'Antrean Outbox ULID IndexedDB + Sinkronisasi Idempoten' },
                { no: '18', name: isEn ? 'Post-BAST 90-Day SLA Triage' : 'Triase SLA 90 Hari Pasca-BAST', slop: isEn ? 'Endless WhatsApp arguments over bugs vs CR' : 'Debat kusir via WA antara perbaikan bug vs fitur baru', pos: isEn ? 'Auto 90-Day Timer: Free Bugfix Prompt vs Paid Delta v1.1' : 'Timer 90 Hari: Perbaikan Bug Rp 0 vs Addendum v1.1' },
                { no: '19', name: isEn ? 'Atomic pg_dump & Rollback' : 'Snapshot pg_dump & Rollback', slop: isEn ? 'Broken migration corrupts live production DB' : 'Migrasi gagal merusak database produksi tanpa backup', pos: isEn ? 'Auto pre_migrate_*.sql.gz + 1-Command ./deploy.sh rollback' : 'Snapshot Otomatis .sql.gz + ./deploy.sh rollback Instan' },
                { no: '20', name: isEn ? 'Chaos Sandbox & Holding' : 'Chaos Sandbox & Holding', slop: isEn ? 'Untested webhooks & single-branch lock-in' : 'Webhook tidak teruji & mentok di satu cabang saja', pos: isEn ? 'Local Webhook Simulator + Multi-Entity Holding Rollup' : 'Simulator Webhook Lokal + Konsolidasi Induk-Cabang' },
              ];

              return (
                <motion.div
                  key="matrix-tab-content"
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.2 }}
                  className="mb-16 space-y-8"
                >
                  {/* PART A: 28-PARAMETER PACKAGE & CONTRACT COMPARISON MATRIX */}
                  <div className="border-2 border-zinc-900 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 sm:p-8 rounded-none shadow-xs">
                    <div className="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-6 pb-4 border-b-2 border-zinc-200 dark:border-zinc-800">
                      <div>
                        <div className="inline-flex items-center gap-2 px-2.5 py-1 bg-zinc-950 text-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 border border-zinc-800 dark:border-emerald-700 font-mono text-[10px] font-black uppercase tracking-wider mb-2">
                          <span>PROJECT OS V12.0 PARIPURNA</span>
                          <span>&bull;</span>
                          <span>{isEn ? '28 EVALUATION PARAMETERS × 20 PILLARS' : '28 PARAMETER EVALUASI × 20 PILAR REKAYASA'}</span>
                        </div>
                        <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-950 dark:text-white flex items-center gap-2 font-sans">
                          <Layers className="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                          <span>{isEn ? 'COMPREHENSIVE TECHNICAL & COMMERCIAL COMPARISON MATRIX' : 'MATRIKS PERBANDINGAN LENGKAP: 7 PAKET RETAIL SAAS VS 3 KONTRAK MITRA STUDIO'}</span>
                        </h3>
                        <p className="text-xs text-zinc-700 dark:text-zinc-300 font-sans mt-1">
                          {isEn
                            ? '100% transparent side-by-side breakdown across all 7 Self-Service Retail SaaS Tiers (including Verified CSR Grant Rp 0) and 3 Turnkey Mitra Engineering Contracts.'
                            : 'Perbandingan transparan 28 parameter teknis, keamanan, SEO/GEO, UU PDP, dan komersial di seluruh 7 Paket Lisensi SaaS Retail (termasuk Hibah CSR Rp 0) serta 3 Kontrak Mitra Studio.'}
                        </p>
                      </div>

                      {/* Density Switcher */}
                      <div className="flex items-center gap-1 self-start lg:self-end bg-zinc-100 dark:bg-zinc-950 p-1 border border-zinc-300 dark:border-zinc-800 font-mono text-[10px]">
                        <span className="px-2 py-1 text-zinc-600 dark:text-zinc-400 font-bold uppercase">{isEn ? 'Density:' : 'Kerapatan:'}</span>
                        {[
                          { id: 'compact', label: 'Compact' },
                          { id: 'normal', label: 'Normal' },
                          { id: 'relaxed', label: 'Relaxed' },
                        ].map((d) => (
                          <button
                            key={d.id}
                            type="button"
                            onClick={() => setMatrixDensity(d.id)}
                            className={`px-2 py-1 font-black uppercase rounded-none cursor-pointer transition ${matrixDensity === d.id
                              ? 'bg-zinc-950 text-white dark:bg-emerald-500 dark:text-zinc-950'
                              : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-800'
                              }`}
                          >
                            {d.label}
                          </button>
                        ))}
                      </div>
                    </div>

                    {/* Search Bar with Instant "X" Clear + Category Filter Pills */}
                    <div className="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 mb-5">
                      <div className="relative flex-1 max-w-md">
                        <Search className="w-3.5 h-3.5 text-zinc-500 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                        <input
                          type="text"
                          value={matrixSearchQuery}
                          onChange={(e) => setMatrixSearchQuery(e.target.value)}
                          placeholder={isEn ? 'Search 28 parameters (e.g. ULID, CSR, llms.txt, UU PDP, SLA)...' : 'Cari dari 28 parameter (mis: ULID, CSR, llms.txt, UU PDP, SLA)...'}
                          className="w-full pl-8 pr-8 py-2 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 font-mono text-xs rounded-none focus:border-emerald-500 outline-none"
                        />
                        {matrixSearchQuery && (
                          <button
                            type="button"
                            onClick={() => setMatrixSearchQuery('')}
                            title={isEn ? 'Clear search' : 'Hapus pencarian'}
                            className="absolute right-2 top-1/2 -translate-y-1/2 px-1.5 py-0.5 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 font-mono text-[10px] font-black rounded-none cursor-pointer"
                          >
                            ✕
                          </button>
                        )}
                      </div>

                      <div className="flex flex-wrap items-center gap-1.5">
                        {matrixCategories.map((cat) => {
                          const active = matrixCategoryFilter === cat.id;
                          return (
                            <button
                              key={cat.id}
                              type="button"
                              onClick={() => setMatrixCategoryFilter(cat.id)}
                              className={`px-2.5 py-1.5 font-mono text-[10px] font-black uppercase border rounded-none cursor-pointer transition ${active
                                ? 'bg-zinc-950 text-emerald-400 border-zinc-950 dark:bg-emerald-500 dark:text-zinc-950 dark:border-emerald-500'
                                : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-800 border-zinc-300 dark:bg-zinc-950 dark:hover:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700'
                                }`}
                            >
                              {cat.label}
                            </button>
                          );
                        })}
                      </div>
                    </div>

                    <div className="overflow-x-auto border border-zinc-300 dark:border-zinc-800">
                      <table className="w-full text-left font-sans border-collapse">
                        <thead className="sticky top-0 z-20 bg-zinc-100/95 dark:bg-zinc-950/95 backdrop-blur-md border-b-2 border-zinc-400 dark:border-zinc-700 font-mono text-[10px] uppercase tracking-wider">
                          <tr>
                            <th className="py-3.5 px-3 font-black text-zinc-900 dark:text-zinc-100 min-w-[210px] bg-zinc-100 dark:bg-zinc-950">
                              <div>{isEn ? 'Evaluation Parameter (28)' : 'Parameter Evaluasi (28)'}</div>
                              <div className="text-[9px] font-bold text-zinc-600 dark:text-zinc-400 mt-0.5">
                                {isEn ? 'Showing ' : 'Menampilkan '} {filteredMatrixRows.length} / {matrixRows.length}
                              </div>
                            </th>
                            <th className="py-3.5 px-3 font-black text-amber-950 dark:text-amber-300 bg-amber-100/60 dark:bg-amber-950/40 min-w-[185px]">
                              <div>Spark &amp; Hibah CSR</div>
                              <div className="text-[9px] font-bold text-amber-900 dark:text-amber-200 mt-0.5">
                                Rp 0 ({isEn ? 'Guest 2x / Verified NGO' : 'Tamu 2x / Yayasan Akta'})
                              </div>
                            </th>
                            <th className="py-3.5 px-3 font-black text-cyan-950 dark:text-cyan-300 bg-cyan-100/60 dark:bg-cyan-950/40 min-w-[195px]">
                              <div>Starter &amp; UMKM Pass</div>
                              <div className="text-[9px] font-bold text-cyan-900 dark:text-cyan-200 mt-0.5">
                                Rp {pricingSettings.retail_lite_price || '99.000'} / Rp {pricingSettings.single_app_pass_price || '299.000'}
                              </div>
                            </th>
                            <th className="py-3.5 px-3 font-black text-emerald-950 dark:text-emerald-300 bg-emerald-100/70 dark:bg-emerald-950/50 min-w-[210px]">
                              <div>Pro / Agency / Ultimate</div>
                              <div className="text-[9px] font-bold text-emerald-900 dark:text-emerald-200 mt-0.5">
                                Rp {pricingSettings.retail_pro_price || '399.000'} – Rp {pricingSettings.retail_ultimate_price || '1.490.000'}
                              </div>
                            </th>
                            <th className="py-3.5 px-3 font-black text-zinc-950 dark:text-zinc-100 bg-zinc-200/70 dark:bg-zinc-900 min-w-[195px]">
                              <div>Advisory &amp; UMKM Studio</div>
                              <div className="text-[9px] font-bold text-zinc-700 dark:text-zinc-300 mt-0.5">
                                Rp {pricingSettings.advisory_price || '2.500.000'} / Rp {pricingSettings.umkm_price || '3.750.000'}
                              </div>
                            </th>
                            <th className="py-3.5 px-3 font-black text-emerald-950 dark:text-emerald-200 bg-emerald-200/70 dark:bg-emerald-950/80 min-w-[220px]">
                              <div>Full Turnkey MVP (Mitra)</div>
                              <div className="text-[9px] font-bold text-emerald-900 dark:text-emerald-300 mt-0.5">
                                {isEn ? 'Escrow RAB + 90-Day SLA' : 'Escrow RAB + Garansi SLA 90 Hari'}
                              </div>
                            </th>
                          </tr>
                        </thead>
                        <tbody className="divide-y divide-zinc-200 dark:divide-zinc-800">
                          {filteredMatrixRows.map((r, rIdx) => (
                            <tr
                              key={r.param}
                              className={`${rIdx % 2 === 0 ? 'bg-white dark:bg-zinc-950' : 'bg-zinc-50/70 dark:bg-zinc-900/50'} hover:bg-zinc-100/90 dark:hover:bg-zinc-800/70 transition`}
                            >
                              <td className={`${cellPad} font-bold text-zinc-950 dark:text-white font-mono border-r border-zinc-200 dark:border-zinc-800`}>
                                {r.param}
                              </td>
                              <td className={`${cellPad} text-zinc-800 dark:text-zinc-200 border-r border-zinc-200 dark:border-zinc-800`}>
                                {r.sparkGrant}
                              </td>
                              <td className={`${cellPad} text-zinc-800 dark:text-zinc-200 border-r border-zinc-200 dark:border-zinc-800`}>
                                {r.starterPass}
                              </td>
                              <td className={`${cellPad} text-emerald-950 dark:text-emerald-200 font-semibold bg-emerald-50/40 dark:bg-emerald-950/20 border-r border-zinc-200 dark:border-zinc-800`}>
                                {r.proAgencyUlt}
                              </td>
                              <td className={`${cellPad} text-zinc-800 dark:text-zinc-200 border-r border-zinc-200 dark:border-zinc-800`}>
                                {r.advisoryUmkm}
                              </td>
                              <td className={`${cellPad} text-emerald-950 dark:text-emerald-200 font-bold bg-emerald-100/40 dark:bg-emerald-950/40`}>
                                {r.fullMvp}
                              </td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </div>
                  </div>

                  {/* PART B: 20-PILLAR PARIPURNA ARCHITECTURAL SUPERIORITY MATRIX (RAW AI SLOP VS PROJECT OS V12.0) */}
                  <div className="border-2 border-zinc-900 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950 p-5 sm:p-8 rounded-none shadow-xs">
                    <div className="flex flex-wrap items-center justify-between gap-2 mb-4">
                      <div>
                        <span className="px-2 py-0.5 bg-emerald-600 text-white font-mono text-[10px] font-black uppercase">
                          {isEn ? '20 COMPLETE PILLARS V12.0 // ZERO-FAILURE GUARANTEE' : '20 PILAR PARIPURNA V12.0 // ZERO-FAILURE GUARANTEE'}
                        </span>
                        <h4 className="text-lg sm:text-xl font-black uppercase text-zinc-950 dark:text-white font-sans mt-1">
                          {isEn
                            ? 'WHY RAW PROMPTING & CONVENTIONAL AGENCIES FAIL VS NERIAHPRO PROJECT OS V12.0'
                            : 'MENGAPA PROMPT AI BIASA & AGENSI KONVENSIONAL GAGAL VS NERIAHPRO PROJECT OS V12.0'}
                        </h4>
                      </div>
                      <span className="font-mono text-[11px] font-bold text-emerald-900 dark:text-emerald-300">
                        {isEn ? 'All 20 Pillars Locked End-to-End' : 'Seluruh 20 Pilar Terkunci dari Hulu ke Hilir'}
                      </span>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2.5">
                      {twentyPillarsComparison.map((p) => (
                        <div
                          key={p.no}
                          className="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 hover:border-emerald-600 dark:hover:border-emerald-500 transition flex flex-col justify-between"
                        >
                          <div>
                            <div className="flex items-center justify-between gap-2 mb-1.5">
                              <span className="px-1.5 py-0.5 bg-zinc-950 text-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 border border-zinc-800 dark:border-emerald-700 font-mono text-[9px] font-black">
                                {isEn ? 'PILLAR' : 'PILAR'} {p.no}
                              </span>
                              <span className="font-mono text-[9px] font-black text-emerald-800 dark:text-emerald-400">
                                100% LOCKED
                              </span>
                            </div>
                            <div className="font-mono text-xs font-black text-zinc-950 dark:text-white mb-2">
                              {p.name}
                            </div>
                            <div className="p-1.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-[10px] text-rose-950 dark:text-rose-200 mb-1.5 leading-snug">
                              <strong className="font-mono uppercase block text-[9px]">✕ {isEn ? 'Raw AI / Old Agency:' : 'AI Biasa / Agensi Lama:'}</strong>
                              {p.slop}
                            </div>
                          </div>
                          <div className="p-1.5 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-300 dark:border-emerald-800 text-[10px] text-emerald-950 dark:text-emerald-200 font-semibold leading-snug">
                            <strong className="font-mono uppercase block text-[9px]">✓ {isEn ? 'Project OS v12.0:' : 'Project OS v12.0:'}</strong>
                            {p.pos}
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                </motion.div>
              );
            })()}

            {/* ========================================================================= */}
            {/* TAB 5: FREQUENTLY ASKED QUESTIONS (FAQ) ACCORDION V12.0                   */}
            {/* ========================================================================= */}
            {pricingSectionTab === 'faq' && (() => {
              const faqCategories = [
                { id: 'ALL', label: isEn ? `All (${faqs.length})` : `Semua (${faqs.length})` },
                { id: 'SATU_PINTU', label: isEn ? 'One-Stop & CSR Rp 0' : 'Satu Pintu & Hibah CSR Rp 0' },
                { id: 'UI_UX_12_SEKTOR', label: isEn ? '12 Sectors, 36 UI & Scrollytelling' : '12 Sektor, 36 UI & Scrollytelling' },
                { id: 'SEO_GEO_UU_PDP', label: isEn ? 'SEO/GEO, UU PDP & Offline PWA' : 'SEO/GEO, UU PDP & Offline PWA' },
                { id: 'AI_MCP_SECURITY', label: isEn ? 'AI 49 Tools, MCP & AgentShield' : 'AI 49 Tool, MCP & AgentShield' },
                { id: 'SLA_GARANSI_HUKUM', label: isEn ? 'Escrow, 90-Day SLA & Rollback' : 'Escrow, Garansi SLA 90 Hari & Rollback' },
              ];

              const filteredFaqs = faqs
                .map((item, originalIdx) => ({ ...item, originalIdx }))
                .filter((item) => {
                  const matchCat = faqCategoryFilter === 'ALL' || item.category === faqCategoryFilter;
                  if (!matchCat) return false;
                  if (!faqSearchQuery.trim()) return true;
                  const q = faqSearchQuery.toLowerCase();
                  return item.q.toLowerCase().includes(q) || item.a.toLowerCase().includes(q);
                });

              const categoryBadgeLabel = {
                SATU_PINTU: isEn ? 'ONE-STOP & COMMERCIAL' : 'SATU PINTU & KOMERSIAL',
                UI_UX_12_SEKTOR: isEn ? '12 SECTORS & DUAL-ZONE UI' : '12 SEKTOR & DUAL-ZONE UI',
                SEO_GEO_UU_PDP: isEn ? 'SEO/GEO, UU PDP & PWA' : 'SEO/GEO, UU PDP & PWA',
                AI_MCP_SECURITY: isEn ? 'AI, MCP & AGENTSHIELD' : 'AI, MCP & AGENTSHIELD',
                SLA_GARANSI_HUKUM: isEn ? 'ESCROW, 90-DAY SLA & OPS' : 'ESCROW, SLA 90 HARI & OPS',
              };

              return (
                <motion.div
                  key="faq-tab-content"
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.2 }}
                  className="max-w-5xl mx-auto mb-16"
                >
                  <div className="text-center mb-6">
                    <div className="inline-flex items-center gap-2 px-2.5 py-1 bg-zinc-950 text-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 border border-zinc-800 dark:border-emerald-700 font-mono text-[10px] font-black uppercase tracking-wider mb-2">
                      <span>KNOWLEDGE BASE &amp; FAQ V12.0 PARIPURNA</span>
                      <span>&bull;</span>
                      <span>{faqs.length} {isEn ? 'VERIFIED ANSWERS' : 'JAWABAN TERVERIFIKASI'}</span>
                    </div>
                    <h3 className="text-2xl sm:text-3xl font-black uppercase tracking-tight text-zinc-950 dark:text-white font-sans">
                      {isEn ? 'FREQUENTLY ASKED QUESTIONS (PROJECT OS V12.0)' : 'PERTANYAAN YANG SERING DIAJUKAN (FAQ PROJECT OS V12.0)'}
                    </h3>
                    <p className="text-xs text-zinc-700 dark:text-zinc-300 font-sans mt-1 max-w-2xl mx-auto">
                      {isEn
                        ? 'Complete answers covering One-Stop Wizard commercial lock, CSR Non-Profit Grant verification, 12 Sectors × 36 Visual UIs, SEO/GEO /llms.txt, UU PDP Blind Index, Offline Outbox PWA, and 90-Day Post-BAST SLA.'
                        : 'Jawaban tuntas mengenai Gerbang Satu Pintu, syarat Hibah CSR Rp 0, 12 Sektor × 36 Visual UI, SEO/GEO /llms.txt, Blind Index UU PDP, Offline Outbox PWA, hingga Garansi Bug SLA 90 Hari Pasca-BAST.'}
                    </p>
                  </div>

                  {/* FAQ Search + Expand All Toggle + Category Filter Pills */}
                  <div className="p-4 bg-zinc-100 dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-800 mb-6 space-y-3">
                    <div className="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5">
                      <div className="relative flex-1">
                        <Search className="w-3.5 h-3.5 text-zinc-500 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                        <input
                          type="text"
                          value={faqSearchQuery}
                          onChange={(e) => setFaqSearchQuery(e.target.value)}
                          placeholder={isEn ? 'Search FAQ (e.g. CSR Rp 0, One-Stop, llms.txt, UU PDP, Offline, SLA 90)...' : 'Cari pertanyaan FAQ (mis: CSR Rp 0, Satu Pintu, llms.txt, UU PDP, Offline, SLA 90)...'}
                          className="w-full pl-8 pr-8 py-2 bg-white dark:bg-zinc-950 border-2 border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 font-mono text-xs rounded-none focus:border-emerald-500 outline-none"
                        />
                        {faqSearchQuery && (
                          <button
                            type="button"
                            onClick={() => setFaqSearchQuery('')}
                            title={isEn ? 'Clear search' : 'Hapus pencarian'}
                            className="absolute right-2 top-1/2 -translate-y-1/2 px-1.5 py-0.5 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 font-mono text-[10px] font-black rounded-none cursor-pointer"
                          >
                            ✕
                          </button>
                        )}
                      </div>
                      <button
                        type="button"
                        onClick={() => setExpandAllFaqs((v) => !v)}
                        className="px-3 py-2 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 font-mono text-[10px] font-black uppercase tracking-wider rounded-none cursor-pointer shrink-0 transition"
                      >
                        {expandAllFaqs
                          ? (isEn ? '▲ Collapse All Answers' : '▲ Tutup Semua Jawaban')
                          : (isEn ? '▼ Expand All Answers' : '▼ Buka Semua Jawaban')}
                      </button>
                    </div>

                    <div className="flex flex-wrap items-center gap-1.5">
                      {faqCategories.map((cat) => {
                        const active = faqCategoryFilter === cat.id;
                        return (
                          <button
                            key={cat.id}
                            type="button"
                            onClick={() => setFaqCategoryFilter(cat.id)}
                            className={`px-2.5 py-1 font-mono text-[10px] font-black uppercase border rounded-none cursor-pointer transition ${active
                              ? 'bg-emerald-600 text-white border-emerald-600 dark:bg-emerald-500 dark:text-zinc-950 dark:border-emerald-500'
                              : 'bg-white hover:bg-zinc-50 text-zinc-800 border-zinc-300 dark:bg-zinc-950 dark:hover:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700'
                              }`}
                          >
                            {cat.label}
                          </button>
                        );
                      })}
                    </div>
                  </div>

                  <div className="space-y-3">
                    {filteredFaqs.map((faq) => {
                      const isExpanded = expandAllFaqs || openFaq === faq.originalIdx;
                      return (
                        <div
                          key={faq.originalIdx}
                          className="border-2 border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 bg-white dark:bg-zinc-900 rounded-none overflow-hidden transition"
                        >
                          <button
                            type="button"
                            onClick={() => toggleFaq(faq.originalIdx)}
                            className="w-full py-4 px-5 text-left flex items-start justify-between gap-4 font-sans font-bold text-xs sm:text-sm text-zinc-950 dark:text-white hover:text-emerald-700 dark:hover:text-emerald-400 transition cursor-pointer"
                          >
                            <div className="space-y-1">
                              <div className="flex flex-wrap items-center gap-2">
                                <span className="px-1.5 py-0.5 bg-zinc-900 text-emerald-400 dark:bg-zinc-800 dark:text-emerald-300 font-mono text-[9px] font-black">
                                  Q{String(faq.originalIdx + 1).padStart(2, '0')}
                                </span>
                                {faq.category && (
                                  <span className="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[9px] font-black uppercase">
                                    {categoryBadgeLabel[faq.category] || faq.category}
                                  </span>
                                )}
                              </div>
                              <div className="leading-snug">{faq.q}</div>
                            </div>
                            {isExpanded ? (
                              <ChevronUp className="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-1" />
                            ) : (
                              <ChevronDown className="w-4 h-4 text-zinc-500 shrink-0 mt-1" />
                            )}
                          </button>

                          <AnimatePresence>
                            {isExpanded && (
                              <motion.div
                                initial={{ height: 0, opacity: 0 }}
                                animate={{ height: 'auto', opacity: 1 }}
                                exit={{ height: 0, opacity: 0 }}
                                transition={{ duration: 0.2 }}
                                className="px-5 pb-4 text-xs text-zinc-800 dark:text-zinc-200 font-sans leading-relaxed border-t border-zinc-200 dark:border-zinc-800 pt-3 bg-zinc-50/60 dark:bg-zinc-950/50"
                              >
                                {faq.a}
                              </motion.div>
                            )}
                          </AnimatePresence>
                        </div>
                      );
                    })}
                  </div>
                </motion.div>
              );
            })()}

            {/* 5. BOTTOM FAST CONTACT CALLOUT */}
            <div className="p-8 sm:p-10 bg-zinc-900 dark:bg-zinc-900/90 border-2 border-zinc-800 text-white text-center rounded-none shadow-xl relative overflow-hidden">
              <h3 className="text-xl sm:text-3xl font-black uppercase tracking-tight font-sans mb-3">
                {isEn ? 'READY TO DISCUSS YOUR ARCHITECTURE SCOPE?' : 'PUNYA KEBUTUHAN KHUSUS DI LUAR PAKET DI ATAS?'}
              </h3>
              <p className="text-xs sm:text-sm text-zinc-400 font-sans max-w-2xl mx-auto mb-6 leading-relaxed">
                {isEn
                  ? 'Book a direct technical scoping consultation with our lead software architect. No pushy sales, strictly engineering clarity.'
                  : 'Diskusikan langsung dengan tim Lead Software Architect kami via sesi scoping teknis 15 menit. Bebas tekanan sales, fokus 100% pada kejelasan solusi.'}
              </p>

              <div className="flex flex-col sm:flex-row items-center justify-center gap-3">
                <button
                  onClick={() => openBookingModal('full_mvp')}
                  className="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-400 text-white py-3 px-6 font-mono text-xs font-black uppercase tracking-wider transition rounded-none flex items-center justify-center gap-2 cursor-pointer"
                >
                  <Clock className="w-3.5 h-3.5" />
                  <span>{isEn ? 'RESERVE SCOPING SESSION' : 'RESERVASI JADWAL SCOPING'}</span>
                </button>

                <a
                  href={`https://wa.me/${whatsappNumber}?text=${encodeURIComponent(isEn ? 'Hello Yoseph Iriandi, I would like to discuss our software architecture and development requirements at Neriah Pro.' : 'Halo Yoseph Iriandi, saya ingin mendiskusikan kebutuhan arsitektur dan pengembangan software kami di Neriah Pro.')}`}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="w-full sm:w-auto bg-zinc-800 hover:bg-zinc-700 text-white border border-zinc-700 py-3 px-6 font-mono text-xs font-bold uppercase tracking-wider transition rounded-none flex items-center justify-center gap-2"
                >
                  <MessageSquare className="w-3.5 h-3.5 text-emerald-400" />
                  <span>{isEn ? 'CHAT ON WHATSAPP' : 'CHAT WHATSAPP RESMI'}</span>
                </a>
              </div>
            </div>
          </>
        )}

      </div>

      {/* 6. INTERACTIVE SPRINT CAPACITY & DIRECT SELECTION MODAL (ANTI-COLLISION TIME MANAGEMENT) */}
      <AnimatePresence>
        {isModalOpen && (
          <div className="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 flex items-start justify-center min-h-full py-10 sm:py-16">
            {/* Backdrop */}
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={() => setIsModalOpen(false)}
              className="fixed inset-0 bg-black/80 backdrop-blur-sm"
            />

            {/* Modal Box */}
            <motion.div
              initial={{ scale: 0.95, opacity: 0, y: 10 }}
              animate={{ scale: 1, opacity: 1, y: 0 }}
              exit={{ scale: 0.95, opacity: 0, y: 10 }}
              className="relative w-full max-w-xl bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-emerald-500 shadow-2xl p-6 sm:p-8 pt-8 z-10 font-sans my-auto"
            >
              <button
                onClick={() => setIsModalOpen(false)}
                className="absolute top-4 right-4 p-1.5 text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition cursor-pointer"
              >
                <X className="w-5 h-5" />
              </button>

              {/* Dynamic Header Based on Selected Package */}
              <div className="mb-6 pt-2 pr-8">
                {selectedPackage === 'full_mvp' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black uppercase tracking-wider mb-2">
                      <Clock className="w-3 h-3" />
                      <span>{isEn ? 'INDEPENDENT DEVELOPER // MAX 2 PROJECTS / CYCLE' : 'PENGEMBANG INDEPENDEN // MAKSIMAL 2 PROYEK / SIKLUS'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      {isEn ? 'RESERVE SPRINT BATCH & SCOPING SESSION' : 'RESERVASI BATCH SPRINT & SESI SCOPING'}
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn
                        ? 'End-to-end production software engineering executed directly by Lead Architect Yoseph Iriandi under Developer Service Agreement.'
                        : 'Pengembangan penuh sistem web monolit modern yang dikerjakan langsung oleh Senior Architect Yoseph Iriandi dengan Developer Service Agreement dan termin bertahap.'}
                    </p>
                  </>
                ) : selectedPackage === 'umkm_starter' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700 font-mono text-[10px] font-black uppercase tracking-wider mb-2">
                      <Sparkles className="w-3 h-3" />
                      <span>{isEn ? 'INDEPENDENT DEVELOPER // LOCAL BUSINESS SUBSIDY' : 'PENGEMBANG INDEPENDEN // PROGRAM SUBSIDI UMKM 50%'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      {isEn ? 'CLAIM 50% SUBSIDY QUOTA' : 'KLAIM KUOTA SUBSIDI 50% (RP 3.750.000)'}
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn
                        ? 'Digital transformation system built directly by Lead Architect Yoseph Iriandi. Turnkey ready with Indonesian admin dashboard and payment integration.'
                        : 'Aplikasi web transaksional yang dibangun langsung oleh Yoseph Iriandi sampai live. Termasuk admin dashboard bahasa Indonesia dan payment gateway QRIS.'}
                    </p>
                  </>
                ) : selectedPackage === 'blueprint_advisory' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-zinc-200 text-zinc-950 border border-zinc-300 dark:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 font-mono text-[10px] font-black uppercase tracking-wider mb-2">
                      <FileText className="w-3 h-3" />
                      <span>{isEn ? 'INDEPENDENT DEVELOPER // ARCHITECTURE ADVISORY' : 'PENGEMBANG INDEPENDEN // JASA ADVISORY BLUEPRINT'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      {isEn ? 'ORDER ADVISORY BLUEPRINT (RP 2.500.000)' : 'PESAN JASA ADVISORY BLUEPRINT (RP 2.5 JT)'}
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn
                        ? 'Receive full 26-parameter PRD, PostgreSQL Strict ULID ERD, and 5-sprint WBS with personal discovery call. Rp 2.5M fee is 100% credited toward DP if upgrading to Full MVP.'
                        : 'Dapatkan cetak biru teknis lengkap (PRD 26 parameter, skema DDL PostgreSQL Strict ULID, dan WBS 5 Sprint) dengan pendampingan scoping Lead Architect Yoseph Iriandi (Memotong DP 100%).'}
                    </p>
                  </>
                ) : selectedPackage === 'retail_ultimate' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700 font-mono text-[10px] font-black uppercase tracking-wider mb-2">
                      <CreditCard className="w-3 h-3" />
                      <span>{isEn ? 'DIRECT CHECKOUT // INSTANT MIDTRANS SNAP' : 'CHECKOUT LANGSUNG // INSTANT MIDTRANS SNAP'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      {isEn ? 'Checkout Ultimate Architecture Blueprint' : 'Checkout Lisensi Ultimate Architecture Blueprint'} (Rp {pricingSettings.retail_ultimate_price || '1.490.000'})
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn
                        ? 'Instant 100% self-service checkout via Midtrans Snap (QRIS, VA Bank, Credit Card). PRD license activates immediately + includes 1 scheduled 60-min Google Meet architecture session.'
                        : 'Pembayaran instan 100% mandiri via Midtrans Snap (QRIS, Virtual Account, Kartu Kredit). Lisensi PRD langsung aktif di akun Anda + termasuk 1 sesi Google Meet 60 menit bersama Lead Architect.'}
                    </p>
                  </>
                ) : selectedPackage === 'retail_pro' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black uppercase tracking-wider mb-2">
                      <CreditCard className="w-3 h-3" />
                      <span>{isEn ? 'DIRECT CHECKOUT // INSTANT MIDTRANS SNAP' : 'CHECKOUT LANGSUNG // INSTANT MIDTRANS SNAP'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      {isEn ? 'Checkout Pro Agentic Blueprint' : 'Checkout Lisensi Pro Agentic Blueprint'} (Rp {pricingSettings.retail_pro_price || '399.000'})
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn
                        ? 'Instant 100% self-service checkout via Midtrans Snap (QRIS, VA, Credit Card). Production blueprint, 6 Mermaid diagrams, Decoupled matrix & WBS 5 Sprints activate immediately.'
                        : 'Pembayaran instan 100% mandiri via Midtrans Snap (QRIS, Virtual Account, Kartu Kredit). Cetak biru produksi, 6 diagram Mermaid, WBS 5 sprint, dan OpenAPI 3.1 langsung aktif di akun Anda.'}
                    </p>
                  </>
                ) : selectedPackage === 'retail_lite' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-cyan-100 text-cyan-950 border border-cyan-400 dark:bg-cyan-950/80 dark:text-cyan-200 dark:border-cyan-700 font-mono text-[10px] font-black uppercase tracking-wider mb-2">
                      <CreditCard className="w-3 h-3" />
                      <span>{isEn ? 'DIRECT CHECKOUT // INSTANT MIDTRANS SNAP' : 'CHECKOUT LANGSUNG // INSTANT MIDTRANS SNAP'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      {isEn ? 'Checkout Starter AI Architect' : 'Checkout Lisensi Starter AI Architect'} (Rp {pricingSettings.retail_lite_price || '99.000'})
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn
                        ? 'Instant 100% self-service checkout via Midtrans Snap (QRIS, Virtual Account, Credit Card). Essential 26-parameter PRD + PostgreSQL Strict ULID DDL SQL activate immediately.'
                        : 'Pembayaran instan 100% mandiri via Midtrans Snap (QRIS, Virtual Account BCA/Mandiri/BRI, Kartu Kredit). Kunci lisensi dan akses unduh PRD 26 parameter langsung aktif seketika di akun Anda.'}
                    </p>
                  </>
                ) : selectedPackage === 'single_app_umkm_pass' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black uppercase tracking-wider mb-2">
                      <CreditCard className="w-3 h-3" />
                      <span>{isEn ? 'ONE-TIME PASS // LOCK 1 APPLICATION PERMANENTLY' : 'SEKALI BAYAR // KUNCI 1 APLIKASI PERMANEN'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      {isEn ? 'Checkout Single-App UMKM Pass' : 'Checkout Single-App UMKM Pass'} (Rp {pricingSettings.single_app_pass_price || '299.000'})
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn
                        ? 'One-time payment (no monthly subscription) to permanently unlock all 7 AI Prompt Phases, full ULID DDL, and SYMBOL_MANIFEST.json for 1 application project.'
                        : 'Sekali bayar tanpa langganan bulanan untuk membuka penuh 7 Fase Prompt AI, skema SQL ULID, dan SYMBOL_MANIFEST.json khusus 1 proyek aplikasi UMKM Anda.'}
                    </p>
                  </>
                ) : selectedPackage === 'agency_whitelabel_partner' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700 font-mono text-[10px] font-black uppercase tracking-wider mb-2">
                      <Crown className="w-3 h-3" />
                      <span>{isEn ? 'AGENCY 5-SEAT // WHITE-LABEL PROPOSAL & ZIP EXPORT' : 'AGENSI 5-KURSI // EKSPOR PROPOSAL & ZIP WHITE-LABEL'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      {isEn ? 'Checkout Agency White-Label Partner' : 'Checkout Agency White-Label Partner'} (Rp {pricingSettings.agency_whitelabel_price || '990.000'})
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn
                        ? 'Up to 25 blueprints/month, 5 team seats, and custom white-label PDF/ZIP architecture proposals bearing your software house logo.'
                        : 'Kuota 25 blueprint/bulan, akses kolaborasi 5 kursi tim, serta ekspor dokumen proposal PDF/ZIP berlogo software house / agensi Anda sendiri.'}
                    </p>
                  </>
                ) : selectedPackage === 'impact_nonprofit_grant' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-cyan-100 text-cyan-950 border border-cyan-400 dark:bg-cyan-950/80 dark:text-cyan-200 dark:border-cyan-700 font-mono text-[10px] font-black uppercase tracking-wider mb-2">
                      <ShieldCheck className="w-3 h-3" />
                      <span>{isEn ? '100% CSR CROSS-SUBSIDY // VERIFIED NON-PROFIT GRANT' : 'SUBSIDI SILANG CSR 100% // HIBAH YAYASAN & RUMAH IBADAH'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      {isEn ? 'Impact & Non-Profit CSR Grant (Rp 0 — Subsidized)' : 'Hibah CSR Yayasan, NGO, Rumah Ibadah & Riset (Rp 0)'}
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn
                        ? 'Commercial value Rp 1,490,000 subsidized 100% to Rp 0 for verified non-profits, houses of worship, NGOs, and academic research (ISAK 35 / PSAK 109 compliant).'
                        : 'Nilai komersial Rp 1.490.000 disubsidi 100% menjadi Rp 0 khusus Yayasan Sosial, Lembaga Zakat/Wakaf, Rumah Ibadah, dan Riset Akademik terverifikasi.'}
                    </p>
                  </>
                ) : (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-cyan-100 text-cyan-950 border border-cyan-400 dark:bg-cyan-950/80 dark:text-cyan-200 dark:border-cyan-700 font-mono text-[10px] font-black uppercase tracking-wider mb-2">
                      <Terminal className="w-3 h-3" />
                      <span>{isEn ? '100% SELF-SERVICE // GUEST ACCESS (NO CREDIT CARD)' : '100% SELF-SERVICE // AKSES TAMU (TANPA KARTU KREDIT)'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      Spark Free Idea Audit (Rp 0)
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn
                        ? 'Quick sanity check, Executive Architecture Proposal (WOW View), and Phase 1 + 2 Core ULID Table Preview in Guest Mode. 2 free audits per month.'
                        : 'Audit cepat kelayakan ide, Proposal Arsitektur Bisnis (Layar 1), serta Pratinjau Fase 1 + 2 Tabel Inti ULID. Kuota gratis 2x per bulan.'}
                    </p>
                  </>
                )}
              </div>

              {submitSuccess ? (
                <div className="p-6 bg-emerald-500/10 border border-emerald-500/30 text-center space-y-4">
                  <div className="w-12 h-12 bg-emerald-500 text-black flex items-center justify-center mx-auto">
                    {paymentStatus === 'success' ? <Check className="w-6 h-6" /> : <CreditCard className="w-6 h-6" />}
                  </div>

                  <div>
                    <h4 className="font-bold text-sm text-zinc-900 dark:text-white uppercase font-mono">
                      {paymentStatus === 'success'
                        ? (isEn ? 'PAYMENT CONFIRMED & ACCESS UNLOCKED!' : 'PEMBAYARAN LUNAS & AKSES AKTIF!')
                        : selectedPackage === 'impact_nonprofit_grant'
                          ? (isEn ? 'CSR GRANT APPLICATION & LEGAL PROOF SUBMITTED!' : 'PENGAJUAN HIBAH CSR & BERKAS LEGALITAS BERHASIL DIKIRIM!')
                          : lastOrder?.is_paid_package
                            ? (isEn ? 'ORDER ISSUED // READY FOR MIDTRANS PAYMENT' : 'PESANAN TERBIT // SIAP DIBAYAR VIA MIDTRANS')
                            : (isEn ? 'INQUIRY SUBMITTED SUCCESSFULLY!' : 'PERMINTAAN KONSULTASI TERCATAT!')}
                    </h4>

                    {lastOrder?.order_id && (
                      <span className="inline-block mt-1 font-mono text-[11px] text-zinc-500">
                        Order ID: <strong className="text-zinc-800 dark:text-zinc-200">{lastOrder.order_id}</strong>
                        {lastOrder.formatted_net_price ? ` // Total: ${lastOrder.formatted_net_price}` : ''}
                      </span>
                    )}
                  </div>

                  <p className="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed font-sans max-w-lg mx-auto">
                    {paymentStatus === 'success'
                      ? (isEn
                        ? 'Your payment was successfully verified! Blueprint specification files, SQL schemas, and official invoices are available in your Customer Dashboard.'
                        : 'Pembayaran Anda telah sukses diverifikasi! Dokumen spesifikasi PRD, skema SQL DDL, dan invoice resmi telah aktif di Dashboard Pelanggan Anda.')
                      : selectedPackage === 'impact_nonprofit_grant'
                        ? (isEn
                          ? 'Your non-profit grant application, legal decree registration number, and verified legal documentation have been securely vaulted (SHA-256 integrity). Our team will audit the documents within 1x24 hours to activate your subsidised Pro Quota.'
                          : 'Pengajuan hibah CSR, nomor registrasi SK/Akta, dan berkas legalitas lembaga Anda telah tersimpan aman dalam vault terenkripsi (integritas SHA-256). Tim audit kami akan memverifikasi berkas dalam 1x24 jam untuk mengaktifkan Kuota Lisensi Pro bersubsidi Anda.')
                        : lastOrder?.is_paid_package
                          ? (isEn
                            ? 'Complete your payment securely via Midtrans Snap (QRIS, BCA/Mandiri/BRI Virtual Account, Credit Card). Your digital license will activate immediately upon payment.'
                            : 'Selesaikan pembayaran via Midtrans Snap (QRIS, BCA/Mandiri/BRI Virtual Account, Kartu Kredit). Lisensi spesifikasi digital langsung aktif otomatis setelah pembayaran.')
                          : (isEn
                            ? 'Your slot and timeline requirements have been logged into our CRM. Our Lead Architect will review and contact you promptly.'
                            : 'Slot jadwal dan spesifikasi kebutuhan Anda telah tercatat di CRM Neriah Pro. Lead Architect kami akan segera meninjau dan menghubungi Anda.')}
                  </p>

                  <div className="flex flex-col sm:flex-row items-center justify-center gap-2 pt-2">
                    {paymentStatus === 'success' ? (
                      <a
                        href={isPaidSelfService ? '/customer/dashboard#licenses' : '/customer/dashboard#projects'}
                        className="w-full sm:w-auto px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-black font-mono text-xs font-black uppercase tracking-wider inline-flex items-center justify-center gap-1.5 transition rounded-none cursor-pointer"
                      >
                        <Sparkles className="w-4 h-4" />
                        <span>{isEn ? 'OPEN CLIENT DASHBOARD →' : 'BUKA DASHBOARD PELANGGAN →'}</span>
                      </a>
                    ) : lastOrder?.snap_token ? (
                      <button
                        type="button"
                        onClick={() => triggerSnapPayment(
                          lastOrder.snap_token,
                          lastOrder.order_id,
                          lastOrder.redirect_url,
                          lastOrder.snap_url,
                          lastOrder.client_key
                        )}
                        className="w-full sm:w-auto px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-black font-mono text-xs font-black uppercase tracking-wider inline-flex items-center justify-center gap-2 transition cursor-pointer shadow-lg rounded-none"
                      >
                        <CreditCard className="w-4 h-4" />
                        <span>{isEn ? 'PAY VIA MIDTRANS SNAP NOW →' : 'BAYAR VIA MIDTRANS SNAP (QRIS / VA) →'}</span>
                      </button>
                    ) : null}

                    {submittedWaUrl && !isRetailTier && (
                      <a
                        href={submittedWaUrl}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="w-full sm:w-auto px-5 py-3 bg-emerald-500 hover:bg-emerald-400 text-black font-mono text-xs font-black uppercase tracking-wider inline-flex items-center justify-center gap-2 border border-emerald-400 rounded-none transition shadow-lg"
                      >
                        <MessageCircle className="w-4 h-4" />
                        <span>{isEn ? 'CONNECT DIRECTLY ON WHATSAPP →' : 'HUBUNGI LEAD ARCHITECT DI WHATSAPP →'}</span>
                      </a>
                    )}

                    <button
                      type="button"
                      onClick={() => setIsModalOpen(false)}
                      className="w-full sm:w-auto px-4 py-2.5 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 font-mono text-xs font-bold uppercase tracking-wider cursor-pointer rounded-none transition"
                    >
                      {isEn ? 'CLOSE' : 'TUTUP'}
                    </button>
                  </div>
                </div>
              ) : (
                <form onSubmit={handleSubmitInquiry} className="space-y-4">
                  {/* Honeypot field for anti-bot defense */}
                  <input
                    type="text"
                    name="honeypot"
                    value={formData.honeypot}
                    onChange={handleInputChange}
                    className="hidden"
                    tabIndex="-1"
                    autoComplete="off"
                  />

                  {submitError && (
                    <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-xs font-mono">
                      {submitError}
                    </div>
                  )}

                  {/* Pilihan Paket Switcher */}
                  <div>
                    <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                      {isEn ? 'Selected Service Package *' : 'Paket Layanan Terpilih *'}
                    </label>
                    <select
                      value={selectedPackage}
                      onChange={(e) => setSelectedPackage(e.target.value)}
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden font-sans"
                      required
                    >
                      <optgroup label={isEn ? "INSTANT ARCHITECTURAL BLUEPRINT (100% SELF-SERVICE // ZERO CONSULTANT MARKUP)" : "INSTANT ARCHITECTURAL BLUEPRINT (100% SELF-SERVICE // TANPA BIAYA KONSULTAN)"}>
                        <option value="retail_spark">{isEn ? 'Spark Free Audit (Free / Guest Mode - Monthly Reset)' : 'Spark Free Audit (Rp 0 - Guest Mode / Reset Tiap Bulan)'}</option>
                        <option value="retail_lite">{isEn ? `Starter AI Architect (Rp ${pricingSettings.retail_lite_price || '99.000'} - Login Required)` : `Starter AI Architect (Rp ${pricingSettings.retail_lite_price || '99.000'} - Wajib Login)`}</option>
                        <option value="retail_pro">{isEn ? `Pro Agentic Blueprint (Rp ${pricingSettings.retail_pro_price || '399.000'} - Login Required)` : `Pro Agentic Blueprint (Rp ${pricingSettings.retail_pro_price || '399.000'} - Wajib Login)`}</option>
                        <option value="retail_ultimate">{isEn ? `Ultimate Architecture Blueprint + 1-on-1 Call (Rp ${pricingSettings.retail_ultimate_price || '1.490.000'} - Verified Account)` : `Ultimate Architecture Blueprint + 1-on-1 Call (Rp ${pricingSettings.retail_ultimate_price || '1.490.000'} - Akun Terverifikasi)`}</option>
                      </optgroup>
                      <optgroup label={isEn ? "INCLUSIVE HYBRID VALUE-METRIC & CSR TIERS (PROJECT OS V8.0)" : "PAKET INKLUSIF HIBAH CSR, PAS UMKM & AGENSI WHITE-LABEL (V8.0)"}>
                        <option value="impact_nonprofit_grant">{isEn ? `Impact & Non-Profit CSR Grant (70%–100% Subsidy / Rp ${pricingSettings.impact_grant_price || '0'})` : `Hibah CSR Yayasan / NGO / Rumah Ibadah (Subsidi 70%–100% / Rp ${pricingSettings.impact_grant_price || '0'})`}</option>
                        <option value="single_app_umkm_pass">{isEn ? `Single-App UMKM Pass (Rp ${pricingSettings.single_app_pass_price || '299.000'} - One-Time Per App)` : `Single-App UMKM Pass (Rp ${pricingSettings.single_app_pass_price || '299.000'} - Sekali Bayar 1 Aplikasi)`}</option>
                        <option value="agency_whitelabel_partner">{isEn ? `Agency Partner & White-Label Studio 5-Seat (Rp ${pricingSettings.agency_whitelabel_price || '990.000'})` : `Agency Partner & White-Label Studio 5-Seat (Rp ${pricingSettings.agency_whitelabel_price || '990.000'})`}</option>
                      </optgroup>
                      <optgroup label={isEn ? "INDEPENDENT DEVELOPER ENGINEERING (EXECUTED DIRECTLY BY YOSEPH IRIANDI)" : "LAYANAN REKAYASA PENGEMBANG INDEPENDEN (DIKERJAKAN LANGSUNG OLEH YOSEPH IRIANDI)"}>
                        <option value="full_mvp">{isEn ? 'Custom Full Monolith MVP (5 Sprints - Scoping & Custom Quotation)' : 'Custom Full Monolith MVP (5 Sprint - Sesi Scoping & Penawaran Kustom)'}</option>
                        <option value="umkm_starter">{isEn ? 'UMKM Digital Starter (50% Subsidy Program - Rp 3,750,000)' : 'UMKM Digital Starter (Program Subsidi 50% - Rp 3.750.000)'}</option>
                        <option value="blueprint_advisory">{isEn ? 'Blueprint & PRD Architecture Advisory (Rp 2,500,000)' : 'Blueprint & PRD Architecture Advisory (Rp 2.500.000)'}</option>
                      </optgroup>
                    </select>
                  </div>

                  {/* BADGE PENEGASAN AKSI DIGITAL INSTAN (MENYESUAIKAN PAKET GRATIS VS BERBAYAR) */}
                  {isSelfService && (
                    <div className={`p-3 border text-xs font-mono flex items-center gap-2.5 ${selectedPackage === 'retail_spark' || selectedPackage === 'impact_nonprofit_grant'
                      ? 'bg-cyan-500/10 border-cyan-500/30 text-cyan-700 dark:text-cyan-300'
                      : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-700 dark:text-emerald-400'
                      }`}>
                      <Zap className={`w-4 h-4 shrink-0 ${selectedPackage === 'retail_spark' || selectedPackage === 'impact_nonprofit_grant' ? 'text-cyan-500' : 'text-emerald-500'}`} />
                      <div className="font-bold tracking-tight">
                        {selectedPackage === 'retail_spark'
                          ? (isEn
                            ? '🚀 Free Guest Sandbox Quota (Rp 0 • 2x/Month) — Fill Details Below & Click the Single Launch Button at the Bottom'
                            : '🚀 Kuota Tamu Sandbox Gratis (Rp 0 • 2x/Bulan) — Lengkapi Data di Bawah & Klik Satu Tombol Mulai di Paling Bawah')
                          : selectedPackage === 'impact_nonprofit_grant'
                            ? (isEn
                              ? '🕊️ 100% CSR Cross-Subsidy (Rp 0) — Submit Foundation / NGO Details Below to Activate Pro Quota'
                              : '🕊️ Subsidi Silang CSR 100% (Rp 0) — Lengkapi Data Yayasan / NGO / Rumah Ibadah di Bawah untuk Aktivasi Kuota Pro')
                            : (isEn
                              ? '⚡ Instant Digital Access — Auto-Generated & Ready for Download Post-Payment'
                              : '⚡ Akses Digital Instan — Dihasilkan Otomatis & Siap Unduh Pasca Pembayaran')}
                      </div>
                    </div>
                  )}

                  {/* 1. KHUSUS FULL MVP: PILIHAN BATCH WAKTU & ANTI-TABRAKAN */}
                  {selectedPackage === 'full_mvp' && (
                    <div className="space-y-3 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                      <div>
                        <div className="flex items-center justify-between mb-1.5">
                          <label className="text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase">
                            {isEn ? '1. Select Sprint Time Batch (Managed Capacity) *' : '1. Pilih Batch Waktu Sprint (Kapasitas Terkelola) *'}
                          </label>
                          <span className="text-[10px] font-mono text-emerald-500 font-bold">ANTI-COLLISION</span>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-2">
                          {[
                            { id: 'Batch 1 (15 Okt - 25 Nov 2026)', label: 'Batch 1', dates: isEn ? 'Oct 15 - Nov 25' : '15 Okt - 25 Nov', slot: isEn ? '1 SLOT LEFT' : 'SISA 1 SLOT', highlight: true },
                            { id: 'Batch 2 (01 Des 2026 - 15 Jan 2027)', label: 'Batch 2', dates: isEn ? 'Dec 01 - Jan 15' : '01 Des - 15 Jan', slot: isEn ? '2 SLOTS AVAILABLE' : 'TERSEDIA 2 SLOT', highlight: false },
                            { id: 'Batch Q1 2027 (Mulai Feb 2027)', label: 'Batch Q1 2027', dates: isEn ? 'Starting Feb 2027' : 'Mulai Feb 2027', slot: isEn ? 'EARLY BIRD' : 'RESERVASI AWAL', highlight: false },
                          ].map((b) => (
                            <button
                              key={b.id}
                              type="button"
                              onClick={() => setSprintBatch(b.id)}
                              className={`p-2.5 text-left border rounded-none transition font-sans cursor-pointer ${sprintBatch === b.id
                                ? 'border-emerald-500 bg-emerald-500/10 text-zinc-900 dark:text-white'
                                : 'border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-400 hover:border-zinc-400'
                                }`}
                            >
                              <span className="font-mono text-[9px] font-bold block text-emerald-600 dark:text-emerald-400">{b.slot}</span>
                              <span className="text-xs font-black block mt-0.5">{b.label}</span>
                              <span className="text-[10px] font-mono text-zinc-500 block">{b.dates}</span>
                            </button>
                          ))}
                        </div>
                      </div>

                      {/* Status Kesiapan Blueprint */}
                      <div>
                        <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1.5">
                          {isEn ? '2. Blueprint PRD Readiness (Scope-Lock Prerequisite) *' : '2. Kesiapan Dokumen Blueprint PRD (Prasyarat Scope-Lock) *'}
                        </label>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                          <button
                            type="button"
                            onClick={() => setHasBlueprint('no')}
                            className={`p-2.5 text-left border rounded-none transition cursor-pointer ${hasBlueprint === 'no'
                              ? 'border-emerald-500 bg-emerald-500/10 text-zinc-900 dark:text-white'
                              : 'border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-400'
                              }`}
                          >
                            <span className="text-xs font-bold block">{isEn ? 'No Blueprint Yet' : 'Belum Ada Blueprint'}</span>
                            <span className="text-[10px] text-zinc-500 block mt-0.5">{isEn ? 'PRD advisory required first (Fee Rp 2.5m deducts 50% DP)' : 'Wajib diawali PRD (Biaya Rp 2.5jt memotong DP 50%)'}</span>
                          </button>
                          <button
                            type="button"
                            onClick={() => setHasBlueprint('ready')}
                            className={`p-2.5 text-left border rounded-none transition cursor-pointer ${hasBlueprint === 'ready'
                              ? 'border-emerald-500 bg-emerald-500/10 text-zinc-900 dark:text-white'
                              : 'border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-400'
                              }`}
                          >
                            <span className="text-xs font-bold block">{isEn ? 'PRD Document Ready' : 'Sudah Ada Dokumen PRD'}</span>
                            <span className="text-[10px] text-zinc-500 block mt-0.5">{isEn ? 'Direct contract review & lock 50% DP slot' : 'Langsung review kontrak & lock slot DP 50%'}</span>
                          </button>
                        </div>
                        {hasBlueprint === 'ready' && (
                          <div className="mt-2">
                            <input
                              type="text"
                              value={blueprintSlug}
                              onChange={(e) => setBlueprintSlug(e.target.value)}
                              placeholder={isEn ? 'Enter Blueprint Slug / Document ID (e.g. prd-project-name)' : 'Masukkan Slug Blueprint / ID Dokumen (Contoh: prd-nama-proyek)'}
                              className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden font-mono"
                            />
                          </div>
                        )}
                      </div>

                      {/* Slot Waktu Kickoff Sync */}
                      <div>
                        <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1.5">
                          {isEn ? '3. Kickoff Sync Window with Lead Architect (15-30 Mins) *' : '3. Pilihan Waktu Kickoff Sync dengan Lead Architect (15-30 Menit) *'}
                        </label>
                        <div className="grid grid-cols-3 gap-2">
                          {[
                            { id: 'Pagi (09:30 - 10:30 WIB)', label: isEn ? 'Morning' : 'Pagi', time: isEn ? '09:30 UTC+7' : '09:30 WIB' },
                            { id: 'Siang (13:30 - 14:30 WIB)', label: isEn ? 'Afternoon' : 'Siang', time: isEn ? '13:30 UTC+7' : '13:30 WIB' },
                            { id: 'Sore (16:00 - 17:00 WIB)', label: isEn ? 'Late Afternoon' : 'Sore', time: isEn ? '16:00 UTC+7' : '16:00 WIB' },
                          ].map((s) => (
                            <button
                              key={s.id}
                              type="button"
                              onClick={() => setKickoffSlot(s.id)}
                              className={`p-2 text-center border rounded-none transition cursor-pointer font-mono text-[10px] font-bold ${kickoffSlot === s.id
                                ? 'border-emerald-500 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                : 'border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-400'
                                }`}
                            >
                              <span className="block font-bold">{s.label}</span>
                              <span className="block text-[9px] text-zinc-500 font-normal">{s.time}</span>
                            </button>
                          ))}
                        </div>
                      </div>
                    </div>
                  )}

                  {/* 2. KHUSUS UMKM: PILIHAN KATEGORI USAHA & SUBSIDI */}
                  {selectedPackage === 'umkm_starter' && (
                    <div className="space-y-3 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                      <div>
                        <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1.5">
                          {isEn ? 'Your Business Industry / Category *' : 'Kategori Bidang Usaha Anda *'}
                        </label>
                        <div className="grid grid-cols-2 gap-2">
                          {(isEn ? [
                            'Retail & Wholesale Stores',
                            'Culinary / Cafe & Resto (F&B)',
                            'Professional Services & Repair',
                            'Non-Profit & Social Community',
                          ] : [
                            'Toko Retail & Grosir',
                            'Kuliner / Cafe & Resto (F&B)',
                            'Jasa Profesional & Servis',
                            'Yayasan & Komunitas Sosial',
                          ]).map((cat) => (
                            <button
                              key={cat}
                              type="button"
                              onClick={() => setUmkmCategory(cat)}
                              className={`p-2.5 text-left border rounded-none transition cursor-pointer font-sans text-xs ${umkmCategory === cat
                                ? 'border-amber-500 bg-amber-500/10 text-zinc-900 dark:text-white font-bold'
                                : 'border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-400'
                                }`}
                            >
                              {cat}
                            </button>
                          ))}
                        </div>
                      </div>

                      <div className="p-3 bg-amber-500/10 border border-amber-500/30 text-xs font-mono text-zinc-800 dark:text-zinc-200">
                        <span className="font-bold text-amber-600 dark:text-amber-400 block mb-0.5">
                          {isEn ? 'SUBSIDY SCHEME APPLIED AUTOMATICALLY:' : 'SKEMA SUBSIDI DITERAPKAN OTOMATIS:'}
                        </span>
                        <span>
                          {isEn
                            ? 'Standard Investment of Rp 7,500,000 is 50% subsidized to Rp 3,750,000. Payment structure: 50% DP (Rp 1,875,000) at kickoff, remaining 50% upon live deployment.'
                            : 'Investasi Normal Rp 7.500.000 dipotong 50% menjadi Rp 3.750.000. Skema pembayaran: DP 50% (Rp 1.875.000) saat mulai, pelunasan sisa 50% setelah live.'}
                        </span>
                      </div>
                    </div>
                  )}

                  {/* 3. KHUSUS BLUEPRINT ADVISORY: INFORMASI ADVISORY STUDIO */}
                  {selectedPackage === 'blueprint_advisory' && (
                    <div className="p-3.5 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-xs font-sans space-y-1.5">
                      <div className="flex items-center gap-1.5 font-mono font-bold text-xs text-zinc-900 dark:text-white uppercase">
                        <FileText className="w-3.5 h-3.5 text-zinc-600 dark:text-zinc-400 shrink-0" />
                        <span>{isEn ? 'NERIAH PRO STUDIO // 1-ON-1 ARCHITECTURAL ADVISORY' : 'NERIAH PRO STUDIO // SESI 1-ON-1 ARCHITECTURAL ADVISORY'}</span>
                      </div>
                      <p className="text-[11px] text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        {isEn
                          ? 'This package includes a 60-min technical discovery call with our Lead Architect to structure your PRD, DDL SQL, and WBS. The Rp 2,500,000 fee directly credits toward your 50% DP if upgrading to Full MVP.'
                          : 'Paket advisory mencakup sesi scoping teknis 1-on-1 bersama Lead Software Architect untuk menyusun PRD 26 parameter, skema DDL SQL, dan WBS 5 Sprint. Biaya Rp 2.500.000 langsung memotong DP 50% jika melanjutkan pengerjaan Full MVP.'}
                      </p>
                    </div>
                  )}

                  {/* 4. KHUSUS PAKET SELF-SERVICE BERBAYAR: RINGKASAN LISENSI CEPAT */}
                  {isPaidSelfService && (
                    <div className="p-3 bg-emerald-500/10 border border-emerald-500/30 text-xs font-mono space-y-2">
                      <div className="flex items-center justify-between">
                        <div className="flex items-center gap-1.5 font-bold text-emerald-600 dark:text-emerald-400 uppercase text-xs">
                          <Terminal className="w-3.5 h-3.5 shrink-0" />
                          <span>{isEn ? 'DIRECT DIGITAL LICENSE CHECKOUT' : 'CHECKOUT LISENSI DIGITAL INSTAN'}</span>
                        </div>
                        <span className="text-[10px] font-bold px-2 py-0.5 bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">
                          MIDTRANS SNAP
                        </span>
                      </div>
                      <div className="grid grid-cols-3 gap-1.5 text-[10px] text-zinc-600 dark:text-zinc-300">
                        <span className="p-1.5 bg-white/60 dark:bg-zinc-900/60 border border-emerald-500/20 block text-center font-bold">📦 {isEn ? 'Lifetime Access' : 'Akses Selamanya'}</span>
                        <span className="p-1.5 bg-white/60 dark:bg-zinc-900/60 border border-emerald-500/20 block text-center font-bold">🧾 {isEn ? 'Official Invoice' : 'Bukti Pembayaran Sah'}</span>
                        <span className="p-1.5 bg-white/60 dark:bg-zinc-900/60 border border-emerald-500/20 block text-center font-bold">⚡ {isEn ? 'Instant QRIS / VA' : 'QRIS / VA Instan'}</span>
                      </div>
                    </div>
                  )}

                  {/* 5. KHUSUS SPARK FREE TIER: GUEST MODE EXPLORER (SINGLE UNIFIED FLOW — ZERO DUPLICATE BYPASS BUTTON) */}
                  {selectedPackage === 'retail_spark' && (
                    <div className="p-3.5 bg-cyan-500/10 border border-cyan-500/30 text-xs font-sans space-y-2">
                      <div className="flex items-center justify-between">
                        <div className="flex items-center gap-1.5 font-mono font-bold text-xs text-cyan-700 dark:text-cyan-300 uppercase">
                          <Rocket className="w-3.5 h-3.5 shrink-0" />
                          <span>{isEn ? 'SPARK FREE IDEA AUDIT (GUEST SANDBOX MODE)' : 'SPARK AUDIT IDE GRATIS (GUEST SANDBOX MODE)'}</span>
                        </div>
                        <span className="text-[10px] font-mono font-black px-2 py-0.5 bg-cyan-100 text-cyan-950 border border-cyan-400 dark:bg-cyan-950/80 dark:text-cyan-200 dark:border-cyan-700">
                          {isEn ? '2 AUDITS / MONTH • RP 0' : '2X AUDIT / BULAN • RP 0'}
                        </span>
                      </div>
                      <p className="text-[11px] text-zinc-700 dark:text-zinc-300 leading-relaxed">
                        {isEn
                          ? 'Spark is 100% free (no credit card required) and unlocks Screen 1 (Website Architecture Proposal WOW View), Phase 1 Foundation, and 2 Core ULID Tables. Fill in your name, email, and WhatsApp below, then click the green launch button at the bottom:'
                          : 'Paket Spark 100% gratis (tanpa kartu kredit) dan membuka Layar 1 (Proposal Arsitektur Bisnis WOW View), Fase 1 Fondasi, serta 2 Tabel Inti ULID. Silakan isi nama, email, dan nomor WhatsApp di bawah lalu klik satu tombol mulai di bagian paling bawah:'}
                      </p>
                    </div>
                  )}

                  {/* ACCOUNT BADGE FOR LOGGED-IN USERS (RETAIL) */}
                  {authUser && isRetailTier && (
                    <div className="p-3 bg-emerald-500/10 border border-emerald-500/30 text-xs font-mono flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                      <div className="flex items-center gap-2">
                        <Check className="w-4 h-4 text-emerald-500 shrink-0" />
                        <span className="text-zinc-900 dark:text-zinc-100">
                          {isEn ? 'Connected Account:' : 'Akun Terverifikasi:'} <strong>{authUser.name}</strong> ({authUser.email})
                        </span>
                      </div>
                      <span className="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-wider">
                        {isEn ? 'AUTO-LINKED TO DASHBOARD' : 'LISENSI OTOMATIS TERSIMPAN'}
                      </span>
                    </div>
                  )}

                  {/* FORM IDENTITAS INTI */}
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                    <div>
                      <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                        {selectedPackage === 'retail_spark'
                          ? (isEn ? 'Guest Full Name (Audit Log) *' : 'Nama Lengkap Pengunjung (Audit Tamu) *')
                          : selectedPackage === 'impact_nonprofit_grant'
                            ? (isEn ? 'Foundation / NGO Representative Name *' : 'Nama Lengkap Pengurus Yayasan / NGO *')
                            : isRetailTier
                              ? (isEn ? 'License Holder Full Name *' : 'Nama Lengkap Pemilik Lisensi *')
                              : (isEn ? 'Full Name / Contact Person *' : 'Nama Lengkap / PIC *')}
                      </label>
                      <input
                        type="text"
                        name="name"
                        value={formData.name}
                        onChange={handleInputChange}
                        placeholder={isEn ? 'e.g. John Doe' : 'Contoh: Budi Santoso'}
                        required
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden"
                      />
                    </div>

                    <div>
                      <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                        {selectedPackage === 'impact_nonprofit_grant'
                          ? (isEn ? 'Foundation / House of Worship / Campus Name *' : 'Nama Yayasan / Rumah Ibadah / Kampus *')
                          : isRetailTier
                            ? (isEn ? 'Company / Brand (Optional)' : 'Perusahaan / Brand (Opsional)')
                            : (isEn ? 'Company / Business Entity *' : 'Perusahaan / Bisnis *')}
                      </label>
                      <input
                        type="text"
                        name="company"
                        value={formData.company}
                        onChange={handleInputChange}
                        placeholder={selectedPackage === 'impact_nonprofit_grant'
                          ? (isEn ? 'e.g. Yayasan Amal Nusantara' : 'Contoh: Yayasan Amal Nusantara')
                          : (isEn ? 'e.g. Acme Corp / Freelance' : 'Contoh: PT Inovasi Maju / Mandiri')}
                        required={!isRetailTier || selectedPackage === 'impact_nonprofit_grant'}
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden"
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                        {selectedPackage === 'retail_spark'
                          ? (isEn ? 'Active Email (Audit Summary & Link) *' : 'Email Aktif (Pengiriman Ringkasan Audit) *')
                          : isRetailTier
                            ? (isEn ? 'Email (License & PRD Delivery) *' : 'Email (Kunci Lisensi & Unduhan PRD) *')
                            : (isEn ? 'Business Email *' : 'Email Bisnis *')}
                      </label>
                      <input
                        type="email"
                        name="email"
                        value={formData.email}
                        onChange={handleInputChange}
                        placeholder={isEn ? 'john@company.com' : 'budi@perusahaan.com'}
                        required
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden"
                      />
                      {isPaidSelfService && (
                        <span className="block text-[10px] text-zinc-500 dark:text-zinc-400 font-mono mt-1">
                          {isEn ? '↳ License key & lifetime PRD download sent here' : '↳ Kunci lisensi & link download PRD dikirim ke sini'}
                        </span>
                      )}
                    </div>

                    <div>
                      <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                        {selectedPackage === 'retail_spark' || selectedPackage === 'impact_nonprofit_grant'
                          ? (isEn ? 'Active WhatsApp / Mobile (E.164 Zone) *' : 'No. WhatsApp / HP Aktif (Zona Negara) *')
                          : isRetailTier
                            ? (isEn ? 'Phone / Mobile (For Midtrans Payment Status) *' : 'No. Ponsel / HP (Notifikasi Pembayaran Midtrans) *')
                            : (isEn ? 'WhatsApp Phone *' : 'No. WhatsApp *')}
                      </label>
                      <div className="flex">
                        <select
                          name="country_code"
                          value={formData.country_code}
                          onChange={handleInputChange}
                          className="bg-zinc-100 dark:bg-zinc-700 border border-r-0 border-zinc-300 dark:border-zinc-600 p-2 text-xs font-mono font-bold rounded-none"
                        >
                          {countryList.map((zone) => (
                            <option key={`${zone.iso}-${zone.dial_code}`} value={zone.dial_code}>
                              {zone.flag} {zone.dial_code} ({zone.iso})
                            </option>
                          ))}
                        </select>
                        <input
                          type="tel"
                          name="phone"
                          value={formData.phone}
                          onChange={handleInputChange}
                          inputMode="numeric"
                          placeholder="8123456789"
                          required
                          className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden font-mono tabular-nums"
                        />
                      </div>
                      {isPaidSelfService && (
                        <span className="block text-[10px] text-zinc-500 dark:text-zinc-400 font-mono mt-1">
                          {isEn ? '↳ Official Midtrans transaction receipts & status delivered here' : '↳ Status transaksi & invoice resmi Midtrans dikirimkan ke nomor ini'}
                        </span>
                      )}
                    </div>
                  </div>

                  {/* Kode Voucher & Estimasi Pagu Anggaran (Regional Thousand Separator) */}
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                        {isEn ? 'Promo Code / Partner Voucher (Optional)' : 'Kode Promo / Voucher Partner (Opsional)'}
                      </label>
                      <input
                        type="text"
                        name="voucher_code"
                        value={formData.voucher_code}
                        onChange={(e) => setFormData(prev => ({ ...prev, voucher_code: e.target.value.toUpperCase() }))}
                        placeholder={isEn ? 'Enter partner referral code' : 'Masukkan jika memiliki kode'}
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden font-mono"
                      />
                    </div>
                    <div>
                      <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                        {isEn ? 'Target Budget IDR (Optional • Regional Format)' : 'Estimasi Anggaran IDR (Opsional • Pemisah Ribuan)'}
                      </label>
                      <input
                        type="text"
                        name="estimated_budget_display"
                        value={formData.estimated_budget_display || ''}
                        onChange={handleInputChange}
                        inputMode="numeric"
                        placeholder={isEn ? 'e.g. 50,000,000' : 'Mis: 50.000.000'}
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden font-mono tabular-nums"
                      />
                    </div>
                  </div>

                  {/* Verifikasi Dokumen Legalitas CSR (Khusus Paket Impact & Non-Profit Grant) */}
                  {selectedPackage === 'impact_nonprofit_grant' && (
                    <div className="p-3.5 bg-cyan-50 dark:bg-cyan-950/40 border border-cyan-300 dark:border-cyan-800 space-y-3">
                      <div className="flex items-start gap-2.5">
                        <div className="p-1.5 bg-cyan-500 text-black shrink-0">
                          <FileCheck className="w-4 h-4" />
                        </div>
                        <div>
                          <h5 className="text-xs font-mono font-black text-cyan-950 dark:text-cyan-200 uppercase tracking-tight">
                            {isEn ? 'Mandatory Legal Verification Documents (ISAK 35 / PSAK 109)' : 'Dokumen Verifikasi Legalitas Wajib (ISAK 35 / PSAK 109)'}
                          </h5>
                          <p className="text-[11px] text-cyan-900/80 dark:text-cyan-300/80 font-sans leading-relaxed mt-0.5">
                            {isEn
                              ? 'To prevent fraudulent claims and ensure the 100% subsidy reaches legitimate public-service institutions, please select your organization type, enter your registration/decree number, and upload proof of legal standing.'
                              : 'Untuk mencegah penyalahgunaan subsidi 100% dan memastikan tepat sasaran bagi lembaga pelayanan publik yang sah, pilih jenis lembaga, masukkan nomor SK/Akta resmi, dan unggah pindaian bukti dokumen.'}
                          </p>
                        </div>
                      </div>

                      {/* Dropdown Kategori Lembaga & Nomor SK */}
                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div>
                          <label className="block text-[11px] font-mono font-bold text-zinc-800 dark:text-zinc-200 uppercase mb-1">
                            {isEn ? 'Organization Type *' : 'Kategori Lembaga *'}
                          </label>
                          <select
                            name="organization_type"
                            value={formData.organization_type || 'NONPROFIT_YAYASAN'}
                            onChange={(e) => setFormData(prev => ({ ...prev, organization_type: e.target.value }))}
                            className="w-full bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2 text-xs text-zinc-900 dark:text-white rounded-none focus:border-cyan-500 focus:outline-hidden font-mono"
                          >
                            <option value="NONPROFIT_YAYASAN">
                              {isEn ? 'Foundation / Social Charity (Yayasan)' : 'Yayasan Sosial / Kemanusiaan'}
                            </option>
                            <option value="RELIGIOUS_HOUSE">
                              {isEn ? 'House of Worship (Mosque/Church/Temple)' : 'Rumah Ibadah (Masjid / Gereja / Vihara / Pura)'}
                            </option>
                            <option value="NGO_SOCIAL">
                              {isEn ? 'Non-Profit NGO / Zakat & Waqf Institution' : 'Lembaga Zakat / Wakaf / NGO Nirlaba'}
                            </option>
                            <option value="ACADEMIC_CAMPUS">
                              {isEn ? 'Academic Research / Campus Lab' : 'Lembaga Riset / Kampus Terakreditasi'}
                            </option>
                          </select>
                        </div>

                        <div>
                          <label className="block text-[11px] font-mono font-bold text-zinc-800 dark:text-zinc-200 uppercase mb-1">
                            {isEn ? 'Decree / Deed Registration No. *' : 'Nomor SK Kemenkumham / Akta Notaris *'}
                          </label>
                          <input
                            type="text"
                            name="legal_decree_number"
                            value={formData.legal_decree_number || ''}
                            onChange={(e) => setFormData(prev => ({ ...prev, legal_decree_number: e.target.value }))}
                            placeholder={isEn ? 'e.g. AHU-0012345.AH.01.04.Tahun 2020' : 'Mis: AHU-0012345.AH.01.04.Tahun 2020 / No. Akta Notaris'}
                            required
                            className="w-full bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2 text-xs text-zinc-900 dark:text-white rounded-none focus:border-cyan-500 focus:outline-hidden font-mono"
                          />
                        </div>
                      </div>

                      {/* File Upload Zone */}
                      <div className="space-y-1.5 pt-1">
                        <label className="block text-[11px] font-mono font-bold text-zinc-800 dark:text-zinc-200 uppercase">
                          {isEn ? 'Legal Document File (PDF, JPG, PNG, Max 10MB) *' : 'Berkas Pindaian Dokumen Legalitas (PDF, JPG, PNG, Maks 10MB) *'}
                        </label>

                        {legalDocumentFile ? (
                          <div className="flex items-center justify-between p-3 bg-white dark:bg-zinc-900 border border-cyan-400 dark:border-cyan-700">
                            <div className="flex items-center gap-2.5 overflow-hidden">
                              <div className="p-1.5 bg-cyan-100 dark:bg-cyan-950 text-cyan-900 dark:text-cyan-200 shrink-0">
                                <FileCheck className="w-4 h-4" />
                              </div>
                              <div className="truncate">
                                <p className="text-xs font-mono font-bold text-zinc-900 dark:text-white truncate">
                                  {legalDocumentFile.name}
                                </p>
                                <div className="flex items-center gap-2 text-[10px] text-zinc-500 font-mono">
                                  <span>{(legalDocumentFile.size / 1024 / 1024).toFixed(2)} MB</span>
                                  <span>•</span>
                                  <span className="text-cyan-700 dark:text-cyan-300 font-semibold">
                                    {isEn ? 'Encrypted Vault Storage' : 'Tersimpan Terisolasi'}
                                  </span>
                                </div>
                              </div>
                            </div>
                            <button
                              type="button"
                              onClick={() => setLegalDocumentFile(null)}
                              className="p-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-500 hover:text-red-500 transition cursor-pointer shrink-0 ml-2"
                              title={isEn ? 'Remove file' : 'Hapus file'}
                            >
                              <X className="w-4 h-4" />
                            </button>
                          </div>
                        ) : (
                          <div
                            onDragOver={(e) => { e.preventDefault(); setIsDraggingFile(true); }}
                            onDragLeave={() => setIsDraggingFile(false)}
                            onDrop={(e) => {
                              e.preventDefault();
                              setIsDraggingFile(false);
                              if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                                const file = e.dataTransfer.files[0];
                                if (file.size > 10 * 1024 * 1024) {
                                  if (window.showToast) {
                                    window.showToast({
                                      type: 'error',
                                      title: isEn ? 'FILE TOO LARGE' : 'UKURAN FILE TERLALU BESAR',
                                      message: isEn ? 'Maximum document file size is 10MB.' : 'Ukuran maksimal berkas dokumen adalah 10MB.'
                                    });
                                  }
                                  return;
                                }
                                setLegalDocumentFile(file);
                              }
                            }}
                            className={`border-2 border-dashed p-4 text-center transition cursor-pointer ${
                              isDraggingFile
                                ? 'border-cyan-500 bg-cyan-500/10'
                                : 'border-zinc-300 dark:border-zinc-700 hover:border-cyan-500 bg-white/60 dark:bg-zinc-900/40'
                            }`}
                            onClick={() => {
                              const input = document.getElementById('csr-legal-document-input');
                              if (input) input.click();
                            }}
                          >
                            <input
                              id="csr-legal-document-input"
                              type="file"
                              accept=".pdf,.jpg,.jpeg,.png,.webp"
                              className="hidden"
                              onChange={(e) => {
                                if (e.target.files && e.target.files[0]) {
                                  const file = e.target.files[0];
                                  if (file.size > 10 * 1024 * 1024) {
                                    if (window.showToast) {
                                      window.showToast({
                                        type: 'error',
                                        title: isEn ? 'FILE TOO LARGE' : 'UKURAN FILE TERLALU BESAR',
                                        message: isEn ? 'Maximum document file size is 10MB.' : 'Ukuran maksimal berkas dokumen adalah 10MB.'
                                      });
                                    }
                                    return;
                                  }
                                  setLegalDocumentFile(file);
                                }
                              }}
                            />
                            <Upload className="w-6 h-6 mx-auto mb-2 text-cyan-600 dark:text-cyan-400" />
                            <p className="text-xs font-mono font-bold text-zinc-800 dark:text-zinc-200">
                              {isEn ? 'Drop Akta / SK file here, or click to browse' : 'Tarik & lepas file Akta / SK ke sini, atau klik untuk memilih'}
                            </p>
                            <p className="text-[10px] text-zinc-500 font-sans mt-0.5">
                              {isEn ? 'PDF, JPG, PNG, WEBP (Max 10MB). Scanned clearly with official stamp.' : 'PDF, JPG, PNG, WEBP (Maks 10MB). Pindaian jelas dengan stempel resmi.'}
                            </p>
                          </div>
                        )}
                      </div>
                    </div>
                  )}

                  {/* Persetujuan Syarat & Ketentuan serta Kebijakan Refund */}
                  {selectedPackage !== 'retail_spark' && (
                    <div className="pt-2 border-t border-zinc-200 dark:border-zinc-800">
                      <label className="flex items-start gap-2.5 cursor-pointer select-none">
                        <input
                          type="checkbox"
                          checked={agreeTerms}
                          onChange={(e) => setAgreeTerms(e.target.checked)}
                          required
                          className="mt-0.5 w-4 h-4 rounded-xs border-zinc-300 dark:border-zinc-700 text-emerald-600 focus:ring-emerald-500 cursor-pointer"
                        />
                        <span className="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed">
                          {isEn ? (
                            <>
                              I have read and agree to the{' '}
                              <a href="/terms" target="_blank" className="font-bold underline text-zinc-900 dark:text-white hover:text-emerald-500">
                                Terms of Service
                              </a>
                              ,{' '}
                              <a href="/privacy-policy" target="_blank" className="font-bold underline text-zinc-900 dark:text-white hover:text-emerald-500">
                                Privacy Policy
                              </a>
                              , and{' '}
                              <a href="/refund-policy" target="_blank" className="font-bold underline text-zinc-900 dark:text-white hover:text-emerald-500">
                                Refund &amp; Warranty Policy
                              </a>
                              .
                            </>
                          ) : (
                            <>
                              Saya telah membaca dan menyetujui{' '}
                              <a href="/terms" target="_blank" className="font-bold underline text-zinc-900 dark:text-white hover:text-emerald-500">
                                Syarat &amp; Ketentuan Layanan
                              </a>
                              ,{' '}
                              <a href="/privacy-policy" target="_blank" className="font-bold underline text-zinc-900 dark:text-white hover:text-emerald-500">
                                Kebijakan Privasi
                              </a>
                              , serta{' '}
                              <a href="/refund-policy" target="_blank" className="font-bold underline text-zinc-900 dark:text-white hover:text-emerald-500">
                                Garansi &amp; Kebijakan Pengembalian Dana (Refund Policy)
                              </a>
                              .
                            </>
                          )}
                        </span>
                      </label>
                    </div>
                  )}

                  {/* Submit CTA */}
                  <button
                    type="submit"
                    disabled={isSubmitting || (!agreeTerms && !['retail_spark', 'impact_nonprofit_grant'].includes(selectedPackage))}
                    className={`w-full bg-emerald-500 hover:bg-emerald-400 text-black py-3.5 px-4 font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition rounded-none shadow-md cursor-pointer ${(!agreeTerms && !['retail_spark', 'impact_nonprofit_grant'].includes(selectedPackage)) ? 'opacity-50 cursor-not-allowed' : ''
                      }`}
                  >
                    {isSubmitting ? (
                      <>
                        <Loader2 className="w-4 h-4 animate-spin" />
                        <span>
                          {selectedPackage === 'retail_spark' || selectedPackage === 'impact_nonprofit_grant'
                            ? (isEn ? 'LAUNCHING ARCHITECTURE STUDIO...' : 'MENYIAPKAN RUANG ARSITEKTUR STUDIO...')
                            : (isEn ? 'OPENING MIDTRANS PAYMENT SNAP...' : 'MEMBUKA GATEWAY PEMBAYARAN MIDTRANS...')}
                        </span>
                      </>
                    ) : selectedPackage === 'retail_lite' ? (
                      <>
                        <CreditCard className="w-4 h-4" />
                        <span>{isEn ? `PAY NOW VIA MIDTRANS SNAP (RP ${pricingSettings.retail_lite_price || '99.000'}) →` : `BAYAR VIA MIDTRANS SNAP (RP ${pricingSettings.retail_lite_price || '99.000'}) →`}</span>
                      </>
                    ) : selectedPackage === 'single_app_umkm_pass' ? (
                      <>
                        <CreditCard className="w-4 h-4" />
                        <span>{isEn ? `PAY NOW VIA MIDTRANS SNAP (RP ${pricingSettings.single_app_pass_price || '299.000'}) →` : `BAYAR VIA MIDTRANS SNAP (RP ${pricingSettings.single_app_pass_price || '299.000'}) →`}</span>
                      </>
                    ) : selectedPackage === 'retail_pro' ? (
                      <>
                        <Sparkles className="w-4 h-4" />
                        <span>{isEn ? `PAY NOW VIA MIDTRANS SNAP (RP ${pricingSettings.retail_pro_price || '399.000'}) →` : `BAYAR VIA MIDTRANS SNAP (RP ${pricingSettings.retail_pro_price || '399.000'}) →`}</span>
                      </>
                    ) : selectedPackage === 'agency_whitelabel_partner' ? (
                      <>
                        <Crown className="w-4 h-4" />
                        <span>{isEn ? `PAY NOW VIA MIDTRANS SNAP (RP ${pricingSettings.agency_whitelabel_price || '990.000'}) →` : `BAYAR VIA MIDTRANS SNAP (RP ${pricingSettings.agency_whitelabel_price || '990.000'}) →`}</span>
                      </>
                    ) : selectedPackage === 'retail_ultimate' ? (
                      <>
                        <Crown className="w-4 h-4" />
                        <span>{isEn ? `PAY NOW VIA MIDTRANS SNAP (RP ${pricingSettings.retail_ultimate_price || '1.490.000'}) →` : `BAYAR VIA MIDTRANS SNAP (RP ${pricingSettings.retail_ultimate_price || '1.490.000'}) →`}</span>
                      </>
                    ) : selectedPackage === 'impact_nonprofit_grant' ? (
                      <>
                        <ShieldCheck className="w-4 h-4" />
                        <span>{isEn ? 'APPLY FOR 100% CSR SUBSIDY GRANT (RP 0) →' : 'AJUKAN HIBAH SUBSIDI CSR 100% (RP 0) →'}</span>
                      </>
                    ) : selectedPackage === 'retail_spark' ? (
                      <>
                        <Rocket className="w-4 h-4" />
                        <span>{isEn ? 'LAUNCH FREE IDEA AUDIT (GUEST MODE) →' : 'MULAI AUDIT IDE GRATIS (GUEST MODE) →'}</span>
                      </>
                    ) : selectedPackage === 'blueprint_advisory' ? (
                      <>
                        <FileText className="w-4 h-4" />
                        <span>{isEn ? 'ORDER ADVISORY BLUEPRINT (RP 2.500.000) →' : 'PESAN JASA ADVISORY BLUEPRINT (RP 2.500.000) →'}</span>
                      </>
                    ) : selectedPackage === 'full_mvp' ? (
                      <>
                        <Clock className="w-4 h-4" />
                        <span>{isEn ? 'RESERVE SPRINT BATCH & SCHEDULE SCOPING →' : 'RESERVASI BATCH SPRINT & JADWAL SCOPING →'}</span>
                      </>
                    ) : selectedPackage === 'umkm_starter' ? (
                      <>
                        <Check className="w-4 h-4" />
                        <span>{isEn ? 'CLAIM 50% SUBSIDY & ACTIVATE ONBOARDING →' : 'KLAIM SUBSIDI 50% & KONSULTASI ONBOARDING →'}</span>
                      </>
                    ) : (
                      <>
                        <Send className="w-4 h-4" />
                        <span>{isEn ? 'SUBMIT CONSULTATION INQUIRY →' : 'KIRIM PERMINTAAN KONSULTASI →'}</span>
                      </>
                    )}
                  </button>

                  {/* Trust & Guarantee Badges */}
                  <div className="pt-2 flex flex-wrap items-center justify-center gap-x-4 gap-y-1.5 text-[10px] font-mono text-zinc-500 dark:text-zinc-400">
                    <span className="flex items-center gap-1">
                      <ShieldCheck className="w-3 h-3 text-emerald-500" />
                      {isEn ? '256-Bit SSL Encryption' : 'Enkripsi SSL 256-Bit'}
                    </span>
                    <span className="flex items-center gap-1">
                      <Check className="w-3 h-3 text-emerald-500" />
                      {selectedPackage === 'retail_spark'
                        ? (isEn ? 'No Credit Card Required (Rp 0)' : 'Tanpa Kartu Kredit (Rp 0)')
                        : selectedPackage === 'impact_nonprofit_grant'
                          ? (isEn ? '100% CSR Cross-Subsidy (ISAK 35)' : 'Subsidi Silang CSR 100% (ISAK 35)')
                          : (isEn ? 'Instant Payment (QRIS / VA Midtrans < Rp 10M)' : 'Pembayaran Instan (QRIS / VA Midtrans < Rp 10 Jt)')}
                    </span>
                    <span className="flex items-center gap-1">
                      <FileText className="w-3 h-3 text-emerald-500" />
                      {selectedPackage === 'retail_spark'
                        ? (isEn ? '0% DOM Leak Server Gate' : 'Gerbang Server 0% Leak')
                        : (isEn ? 'Official Commercial Invoice' : 'Bukti Pembayaran & Kwitansi Resmi')}
                    </span>
                  </div>
                </form>
              )}
            </motion.div>
          </div>
        )}
      </AnimatePresence>

    </div>
  );
}
