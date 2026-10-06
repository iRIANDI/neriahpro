import React, { useState } from 'react';
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
  Send, 
  Loader2, 
  Clock, 
  Code2, 
  Building2, 
  Gift, 
  ChevronDown, 
  ChevronUp, 
  Server, 
  Cpu
} from 'lucide-react';

export default function ArchitecturePricingIsland({ 
  headline = 'INVESTASI TRANSPARAN & TEPAT SASARAN',
  subheadline = 'Dua skenario solusi rekayasa perangkat lunak berskala tinggi: Mulai dari blueprint teknis siap eksekusi hingga pengembangan penuh sistem monolit modern tanpa drama pembengkakan biaya.',
  whatsappNumber = '628123456789',
  featureFlags = {},
  currentLocale = 'id',
  pricingSettings = {}
}) {
  const isEn = currentLocale === 'en' || (typeof window !== 'undefined' && (document.documentElement.lang?.startsWith('en') || document.cookie.includes('neriah_locale=en')));
  
  // Tab switcher
  const [activeTab, setActiveTab] = useState('software'); // 'software' | 'cv'
  
  // Voucher copy state
  const [copiedCode, setCopiedCode] = useState(null);

  // Modal State for Consultation / Booking Intake
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [selectedPackage, setSelectedPackage] = useState('blueprint_advisory');
  const [formData, setFormData] = useState({
    name: '',
    company: '',
    email: '',
    country_code: '+62',
    phone: '',
    voucher_code: '',
    notes: '',
    honeypot: ''
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitSuccess, setSubmitSuccess] = useState(false);
  const [submitError, setSubmitError] = useState(null);

  // FAQ Accordion State
  const [openFaq, setOpenFaq] = useState(null);

  const toggleFaq = (index) => {
    setOpenFaq(openFaq === index ? null : index);
  };

  const copyVoucher = (code) => {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(code);
      setCopiedCode(code);
      setTimeout(() => setCopiedCode(null), 3000);
    }
  };

  const openBookingModal = (packageTier, defaultVoucher = '') => {
    setSelectedPackage(packageTier);
    setFormData(prev => ({
      ...prev,
      voucher_code: defaultVoucher || prev.voucher_code
    }));
    setSubmitSuccess(false);
    setSubmitError(null);
    setIsModalOpen(true);
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmitInquiry = async (e) => {
    e.preventDefault();
    if (formData.honeypot) return; // bot detected

    setIsSubmitting(true);
    setSubmitError(null);

    try {
      const response = await fetch('/api/pricing/inquiry', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          name: formData.name,
          company: formData.company,
          email: formData.email,
          country_code: formData.country_code,
          phone: formData.phone,
          package_tier: selectedPackage,
          voucher_code: formData.voucher_code,
          notes: formData.notes
        })
      });

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw new Error(result.message || (isEn ? 'Failed to submit inquiry.' : 'Gagal mengirimkan formulir konsultasi.'));
      }

      setSubmitSuccess(true);

      // Open WhatsApp chat in new window
      if (result.whatsapp_url) {
        setTimeout(() => {
          window.open(result.whatsapp_url, '_blank');
        }, 800);
      }
    } catch (err) {
      setSubmitError(err.message || (isEn ? 'An error occurred. Please try again.' : 'Terjadi kesalahan sistem. Silakan coba lagi.'));
    } finally {
      setIsSubmitting(false);
    }
  };

  const activeVouchers = [
    {
      code: 'UMKM-SUBSIDI-50',
      label: isEn ? 'UMKM 50% Subsidy' : 'Subsidi 50% UMKM',
      desc: isEn ? 'Special government/community initiative discount for local business digital transformation.' : 'Bantuan stimulus transformasi digital bisnis lokal & wirausaha mandiri.',
      badge: '50% OFF',
      tierTarget: 'umkm_starter'
    },
    {
      code: 'CORP-INNOVATION-15M',
      label: isEn ? 'Enterprise Voucher' : 'Voucher Korporasi',
      desc: isEn ? 'Direct Rp 15,000,000 deduction on Full MVP Rapid Monolith Development.' : 'Potongan langsung Rp 15.000.000 untuk kontrak Full MVP Enterprise Monolith.',
      badge: '-Rp 15.000.000',
      tierTarget: 'full_mvp'
    },
    {
      code: 'ENTERPRISE-SPRINT-25',
      label: isEn ? 'Sprint Kickstart' : 'Diskon Sprint 25%',
      desc: isEn ? '25% discount for 5 full sprints development with dedicated engineers.' : 'Diskon 25% pengerjaan sprint akselerasi MVP sistem berskala tinggi.',
      badge: '25% OFF',
      tierTarget: 'full_mvp'
    }
  ];

  const faqs = [
    {
      q: isEn ? 'Why should we pay Rp 2,500,000 for Blueprint if we already have in-house programmers?' : 'Mengapa butuh Jasa Blueprint (Rp 2.500.000) jika kami sudah memiliki programmer in-house?',
      a: isEn 
        ? 'Most software projects fail not because programmers cannot code, but because of vague specifications, messy database schemas, and scope creep. The Advisory Blueprint gives your team a battle-tested technical roadmap (26-parameter PRD, PostgreSQL Strict ULID ERD, O(1) Keyset pagination, and 5 sprint work breakdowns) saving you months of costly re-engineering.'
        : 'Sebagian besar proyek gagal bukan karena programmer tidak bisa coding, melainkan karena ketiadaan cetak biru arsitektur yang solid: skema database tidak terindeks dengan baik, spesifikasi fitur kabur, dan sprint berantakan. Paket Advisory Blueprint memberikan roadmap teknis siap eksekusi (PRD 26 parameter, skema PostgreSQL Strict ULID, arsitektur pagination O(1), dan WBS 5 sprint) sehingga tim Anda hemat ratusan jam revisi.'
    },
    {
      q: isEn ? 'How does the 50% Down Payment (DP) work for Full MVP Development?' : 'Bagaimana skema pembayaran Uang Muka (DP) 50% untuk Full MVP?',
      a: isEn 
        ? 'We enforce Scope Locking and Digital Legal Contracts. To kickstart the 5 sprints development, clients settle a 50% DP (Rp 25,000,000) securely via Midtrans or Bank Escrow. The remaining 50% is paid upon User Acceptance Testing (UAT) and successful deployment to production VPS.'
        : 'Kami menerapkan transparansi penuh dengan Kontrak Hukum Digital dan Scope Locking. Untuk memulai pengerjaan 5 sprint, klien membayarkan DP 50% (Rp 25.000.000) via Midtrans atau Escrow Bank resmi. Sisa 50% dilunasi setelah User Acceptance Testing (UAT) tuntas dan sistem live di server produksi.'
    },
    {
      q: isEn ? 'Who owns the intellectual property and source code?' : 'Siapakah pemilik hak cipta dan source code aplikasi setelah selesai?',
      a: isEn 
        ? '100% Client Ownership. We transfer full source code, database dumps, server credentials, and documentation without vendor lock-in or recurring proprietary licensing.'
        : '100% Hak Milik Klien. Seluruh kode sumber (Laravel 13, Filament v5, Livewire 4, React Islands), skema database, akun server VPS, dan dokumentasi PRD diserahkan penuh kepada Anda tanpa biaya lisensi tersembunyi.'
    },
    {
      q: isEn ? 'Can UMKM apply promotional vouchers for additional subsidies?' : 'Bagaimana cara UMKM mengklaim voucher subsidi 50%?',
      a: isEn 
        ? 'Simply copy the voucher code UMKM-SUBSIDI-50 and enter it in our fast booking modal or WhatsApp chat. Our team will verify eligibility and apply the subsidy instantly.'
        : 'Cukup salin kode voucher UMKM-SUBSIDI-50 di banner promosi atas, lalu tempelkan pada formulir pemesanan atau konsultasikan via WhatsApp kami. Tim kami akan memverifikasi unit usaha Anda dan memotong harga secara instan.'
    },
    {
      q: isEn ? 'What is the estimated delivery timeline?' : 'Berapa lama estimasi pengerjaan masing-masing paket?',
      a: isEn 
        ? 'Advisory Blueprint PRD is generated and finalized within 24 - 48 hours. Full MVP Rapid Monolith takes 4 to 6 weeks spanning 5 structured sprints.'
        : 'Paket Advisory Blueprint diselesaikan dalam 24 hingga 48 jam kerja setelah sesi discovery awal. Sedangkan Full MVP diselesaikan dalam rentang 4 hingga 6 minggu (5 sprint terstruktur).'
    }
  ];

  return (
    <div className="w-full bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 py-12 sm:py-20 transition-colors">
      <div className="max-w-7xl mx-auto px-4 sm:px-6">

        {/* 1. HEADER SECTION & VALUE PROPOSITION */}
        <div className="text-center max-w-4xl mx-auto mb-10 sm:mb-16">
          <div className="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-mono text-xs uppercase tracking-wider font-bold mb-4 rounded-none">
            <Sparkles className="w-3.5 h-3.5" />
            <span>{isEn ? 'TRANSPARENT VALUE-BASED PRICING' : 'SKEMA INVESTASI TRANSPARAN & TERSTANDAR'}</span>
          </div>
          
          <h1 className="text-3xl sm:text-5xl font-black uppercase tracking-tight font-sans text-zinc-900 dark:text-white mb-4">
            {headline}
          </h1>
          
          <p className="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed max-w-3xl mx-auto">
            {subheadline}
          </p>

          {/* VOUCHER PROMOTION ALERT BAR */}
          <div className="mt-8 p-4 bg-gradient-to-r from-emerald-950/40 via-zinc-900 to-zinc-900 border-2 border-emerald-500/40 text-left rounded-none shadow-lg">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
              <div className="flex items-start gap-3">
                <div className="w-8 h-8 bg-emerald-500 text-black flex items-center justify-center font-bold shrink-0">
                  <Gift className="w-4 h-4" />
                </div>
                <div>
                  <h4 className="text-xs font-mono font-bold uppercase tracking-wider text-emerald-400 flex items-center gap-2">
                    <span>{isEn ? 'ACTIVE STIMULUS & VOUCHERS' : 'PROGRAM STIMULUS & VOUCHER AKTIF'}</span>
                    <span className="px-1.5 py-0.2 bg-emerald-500 text-black text-[9px] font-black">TERBATAS</span>
                  </h4>
                  <p className="text-xs text-zinc-300 font-sans mt-0.5">
                    {pricingSettings.active_promo_banner || (isEn 
                      ? 'Apply voucher codes below to unlock exclusive subsidies for UMKM and corporate innovation.'
                      : 'Klaim subsidi transformasi digital untuk UMKM atau potongan harga khusus bagi korporasi & startup.')}
                  </p>
                </div>
              </div>

              {/* Quick Voucher Pills */}
              <div className="flex flex-wrap items-center gap-2">
                {activeVouchers.map((v) => (
                  <button
                    key={v.code}
                    onClick={() => copyVoucher(v.code)}
                    className="flex items-center gap-2 px-3 py-1.5 bg-zinc-950 border border-zinc-700 hover:border-emerald-500 transition text-left group"
                    title={v.desc}
                  >
                    <span className="font-mono text-[11px] font-bold text-white group-hover:text-emerald-400">
                      {v.code}
                    </span>
                    <span className="text-[10px] px-1 py-0.2 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-bold">
                      {v.badge}
                    </span>
                    {copiedCode === v.code ? (
                      <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400 shrink-0" />
                    ) : (
                      <Copy className="w-3.5 h-3.5 text-zinc-500 group-hover:text-white shrink-0" />
                    )}
                  </button>
                ))}
              </div>
            </div>
            {copiedCode && (
              <p className="text-[11px] font-mono text-emerald-400 mt-2 flex items-center gap-1">
                <Check className="w-3 h-3" />
                {isEn ? `Voucher ${copiedCode} copied! Paste it in the booking form.` : `Kode voucher ${copiedCode} berhasil disalin! Masukkan ke formulir pemesanan.`}
              </p>
            )}
          </div>
        </div>

        {/* 2. THE THREE PRICING TIERS */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 items-stretch mb-16">

          {/* TIER 1: ADVISORY ONLY / ARCHITECTURE BLUEPRINT */}
          <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-800 p-6 sm:p-8 flex flex-col justify-between rounded-none shadow-xs hover:border-emerald-500/60 transition group relative">
            <div>
              <div className="flex items-center justify-between mb-4">
                <span className="font-mono text-xs font-black tracking-wider uppercase text-zinc-500 dark:text-zinc-400">
                  SKENARIO 1 // ADVISORY
                </span>
                <span className="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-[10px] font-bold">
                  ONE-TIME INVESTMENT
                </span>
              </div>

              <h2 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2">
                Blueprint & PRD Architecture
              </h2>

              <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-6">
                {isEn 
                  ? 'Designed for founders and CTOs with an in-house or freelance dev team who need a rock-solid technical blueprint to prevent scope creep and architectural failures.'
                  : 'Solusi ideal bagi founder, CTO, atau manajer IT yang sudah memiliki tim programmer sendiri, namun membutuhkan cetak biru arsitektur enterprise siap kerja tanpa menyewa kami untuk coding.'}
              </p>

              <div className="mb-6 p-4 bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800">
                <span className="text-xs text-zinc-500 dark:text-zinc-400 font-mono block mb-1">Total Biaya Jasa Advisory:</span>
                <div className="flex items-baseline gap-2">
                  <span className="text-3xl sm:text-4xl font-black font-mono text-zinc-900 dark:text-white">
                    Rp {pricingSettings.advisory_price || '2.500.000'}
                  </span>
                  <span className="text-xs font-mono text-zinc-500">/ project</span>
                </div>
                <span className="text-[11px] text-emerald-600 dark:text-emerald-400 font-mono font-bold mt-1 block">
                  &bull; {isEn ? 'Includes full PRD synthesis + ERD schema' : 'Termasuk PRD 26 parameter + Skema DDL'}
                </span>
              </div>

              <div className="space-y-3 mb-8">
                <div className="font-mono text-[11px] font-bold text-zinc-400 uppercase tracking-wider">
                  {isEn ? 'DELIVERABLES INCLUDED:' : 'OUTPUT SPESIFIKASI DIDAPATKAN:'}
                </div>

                {[
                  'PRD 26 Parameter Lengkap (Fungsional, Non-Fungsional, NFR)',
                  'Skema Database PostgreSQL Strict ULID (DDL SQL Siap Pakai)',
                  'Standar O(1) Keyset & Cursor Pagination Guide',
                  'Diagram Alur Sistem (System Architecture & Data Flow)',
                  'Work Breakdown Structure (WBS) 5 Sprint Jira/Linear Ready',
                  'Security & Anti-Malware / DDoS Hardening Checklist',
                  '100% Hak Milik Dokumen & Non-Disclosure Agreement (NDA)',
                ].map((feat, idx) => (
                  <div key={idx} className="flex items-start gap-2.5 text-xs text-zinc-700 dark:text-zinc-300">
                    <Check className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                    <span>{feat}</span>
                  </div>
                ))}
              </div>
            </div>

            <div className="space-y-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
              <button
                onClick={() => openBookingModal('blueprint_advisory')}
                className="w-full bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black py-3 px-4 font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition rounded-none shadow-xs"
              >
                <span>{isEn ? 'ORDER BLUEPRINT NOW' : 'PESAN BLUEPRINT (RP 2.5 JT)'}</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </button>

              <a
                href="/blueprint"
                className="w-full bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 py-2.5 px-4 font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none text-center"
              >
                <Terminal className="w-3.5 h-3.5 text-emerald-500" />
                <span>{isEn ? 'TRY LIVE GENERATOR' : 'COBA GENERATOR BLUEPRINT'}</span>
              </a>
            </div>
          </div>

          {/* TIER 2: FULL MVP DEVELOPMENT (FLAGSHIP ENTERPRISE) */}
          <div className="bg-white dark:bg-zinc-900 border-2 border-emerald-500 dark:border-emerald-500 p-6 sm:p-8 flex flex-col justify-between rounded-none shadow-2xl relative transform lg:-translate-y-2">
            {/* Best Value Badge */}
            <div className="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-emerald-500 text-black px-4 py-1 font-mono text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 shadow-md">
              <Crown className="w-3.5 h-3.5" />
              <span>{isEn ? 'MOST RECOMMENDED // ENTERPRISE' : 'PALING DIMINATI // FULL MVP'}</span>
            </div>

            <div>
              <div className="flex items-center justify-between mb-4 mt-2">
                <span className="font-mono text-xs font-black tracking-wider uppercase text-emerald-600 dark:text-emerald-400">
                  SKENARIO 2 // FULL MONOLITH
                </span>
                <span className="px-2 py-0.5 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-mono text-[10px] font-bold">
                  DP 50% MILESTONE
                </span>
              </div>

              <h2 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2">
                Enterprise Rapid Monolith MVP
              </h2>

              <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-6">
                {isEn 
                  ? 'End-to-end production development. We build your entire scalable web app using Laravel 13, Filament v5, and React 19 Islands. Architecture Blueprint is completely included.'
                  : 'Pengembangan penuh aplikasi web skala jutaan pengguna. Kami membangun seluruh sistem siap produksi dengan kontrak legal, DP 50% bergaransi, dan Blueprint PRD sudah otomatis termasuk di dalamnya.'}
              </p>

              <div className="mb-6 p-4 bg-emerald-500/5 border border-emerald-500/30">
                <span className="text-xs text-zinc-500 dark:text-zinc-400 font-mono block mb-1">Nilai Kontrak Pengembangan Penuh:</span>
                <div className="flex items-baseline gap-2">
                  <span className="text-3xl sm:text-4xl font-black font-mono text-zinc-900 dark:text-white">
                    Rp {pricingSettings.mvp_price || '50.000.000'}
                  </span>
                </div>
                <div className="flex items-center justify-between mt-2 pt-2 border-t border-emerald-500/20">
                  <span className="text-xs font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                    Uang Muka (DP 50%):
                  </span>
                  <span className="text-sm font-mono font-black text-emerald-500">
                    Rp 25.000.000
                  </span>
                </div>
                <span className="text-[10px] text-zinc-500 font-mono mt-1 block">
                  &bull; Pelunasan sisa 50% setelah UAT & Live Production Deploy
                </span>
              </div>

              <div className="space-y-3 mb-8">
                <div className="font-mono text-[11px] font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-1">
                  <Sparkles className="w-3.5 h-3.5" />
                  <span>{isEn ? 'EVERYTHING IN BLUEPRINT PLUS:' : 'SEMUA OUTPUT BLUEPRINT DITAMBAH:'}</span>
                </div>

                {[
                  'Full-Stack Modern Monolith (Laravel 13, Filament v5, React 19)',
                  'Kontrak Hukum Digital Scope-Locked & Legal Security',
                  'Pembayaran DP 50% Aman via Midtrans / Bank Escrow',
                  'Dedicated VPS Hardening, Nginx Tuning, & Redis Setup',
                  'Automated Test Suite (Pest PHP Unit & Feature Tests)',
                  'Integrasi Payment Gateway, WhatsApp API, & Email Gateway',
                  '100% Penyerahan Source Code & Akun Server Klien',
                  'Garansi Perbaikan Bug & SLA Prioritas 3 Bulan Penuh',
                ].map((feat, idx) => (
                  <div key={idx} className="flex items-start gap-2.5 text-xs text-zinc-800 dark:text-zinc-200 font-medium">
                    <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                    <span>{feat}</span>
                  </div>
                ))}
              </div>
            </div>

            <div className="space-y-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
              <button
                onClick={() => openBookingModal('full_mvp', 'CORP-INNOVATION-15M')}
                className="w-full bg-emerald-500 hover:bg-emerald-400 text-black py-3.5 px-4 font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition rounded-none shadow-lg"
              >
                <span>{isEn ? 'START 5 SPRINT DEVELOPMENT (DP 50%)' : 'MULAI SPRINT PROYEK (DP 50%)'}</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </button>

              <button
                onClick={() => openBookingModal('full_mvp')}
                className="w-full bg-transparent hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 py-2.5 px-4 font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none"
              >
                <MessageSquare className="w-3.5 h-3.5 text-emerald-500" />
                <span>{isEn ? 'SCHEDULE TECH SCOPING CALL' : 'KONSULTASI SCOPE TEKNIS'}</span>
              </button>
            </div>
          </div>

          {/* TIER 3: UMKM DIGITAL STARTER & SUBSIDI */}
          <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-800 p-6 sm:p-8 flex flex-col justify-between rounded-none shadow-xs hover:border-emerald-500/60 transition group relative">
            <div>
              <div className="flex items-center justify-between mb-4">
                <span className="font-mono text-xs font-black tracking-wider uppercase text-zinc-500 dark:text-zinc-400">
                  PROGRAM STIMULUS // UMKM
                </span>
                <span className="px-2 py-0.5 bg-amber-500/10 text-amber-500 border border-amber-500/20 font-mono text-[10px] font-bold">
                  SUBSIDI 50% TERSEDIA
                </span>
              </div>

              <h2 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2">
                UMKM Digital Starter
              </h2>

              <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-6">
                {isEn 
                  ? 'Dedicated package for local businesses, shops, and social enterprises moving from manual paperwork to automated web systems with available government/community subsidies.'
                  : 'Solusi terjangkau bagi pemilik usaha lokal, retail, dan yayasan yang ingin beralih dari nota manual ke sistem web app transaksional dengan kuota subsidi voucher.'}
              </p>

              <div className="mb-6 p-4 bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800">
                <span className="text-xs text-zinc-500 dark:text-zinc-400 font-mono block mb-1">Investasi Awal Normal:</span>
                <div className="flex items-baseline gap-2">
                  <span className="text-2xl sm:text-3xl font-black font-mono text-zinc-900 dark:text-white">
                    Rp {pricingSettings.umkm_price || '7.500.000'}
                  </span>
                </div>
                <div className="mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                  <span className="text-[11px] font-mono text-zinc-500">Dengan Voucher Subsidi:</span>
                  <span className="text-xs font-mono font-black text-amber-500">
                    Rp 3.750.000
                  </span>
                </div>
                <span className="text-[10px] text-amber-500 font-mono mt-1 block">
                  &bull; Gunakan Voucher: <strong className="underline">UMKM-SUBSIDI-50</strong>
                </span>
              </div>

              <div className="space-y-3 mb-8">
                <div className="font-mono text-[11px] font-bold text-zinc-400 uppercase tracking-wider">
                  {isEn ? 'PACKAGE HIGHLIGHTS:' : 'FITUR UTAMA DIDAPATKAN:'}
                </div>

                {[
                  'Engine Transaksi & Database Pelanggan Terpusat',
                  'Integrasi Pembayaran Otomatis QRIS & Transfer Bank',
                  'Admin Dashboard Filament v5 Bahasa Indonesia',
                  'Ekspor Laporan Penjualan Excel / PDF Otomatis',
                  'Notifikasi WhatsApp Konfirmasi Pesanan Real-Time',
                  'Setup Domain Bisnis (.id / .com) & Hosting Cepat',
                  'Pelatihan Penggunaan Dashboard via Zoom / Panduan Video',
                ].map((feat, idx) => (
                  <div key={idx} className="flex items-start gap-2.5 text-xs text-zinc-700 dark:text-zinc-300">
                    <Check className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                    <span>{feat}</span>
                  </div>
                ))}
              </div>
            </div>

            <div className="space-y-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
              <button
                onClick={() => openBookingModal('umkm_starter', 'UMKM-SUBSIDI-50')}
                className="w-full bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black py-3 px-4 font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition rounded-none shadow-xs"
              >
                <span>{isEn ? 'APPLY UMKM SUBSIDY' : 'KLAIM SUBSIDI UMKM (50%)'}</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </button>

              <button
                onClick={() => openBookingModal('umkm_starter')}
                className="w-full bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 py-2.5 px-4 font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none text-center"
              >
                <MessageSquare className="w-3.5 h-3.5 text-amber-500" />
                <span>{isEn ? 'CHAT WITH ADVISOR' : 'KONSULTASI KEBUTUHAN UMKM'}</span>
              </button>
            </div>
          </div>

        </div>

        {/* 3. TECHNICAL SPECIFICATION COMPARISON MATRIX */}
        <div className="mb-16 border-2 border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 sm:p-8 rounded-none shadow-xs">
          <div className="mb-6">
            <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white flex items-center gap-2 font-sans">
              <Layers className="w-5 h-5 text-emerald-500" />
              <span>{isEn ? 'TECHNICAL COMPARISON MATRIX' : 'MATRIKS PERBANDINGAN FITUR & DELIVERABLE'}</span>
            </h3>
            <p className="text-xs text-zinc-500 font-sans mt-1">
              {isEn ? 'Side-by-side technical breakdown across all tiers.' : 'Perbandingan transparan parameter teknis antar setiap paket pengerjaan.'}
            </p>
          </div>

          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs font-sans border-collapse">
              <thead>
                <tr className="border-b-2 border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950 font-mono text-[11px] uppercase tracking-wider">
                  <th className="py-3 px-4 font-bold text-zinc-600 dark:text-zinc-400">Parameter Teknis</th>
                  <th className="py-3 px-4 font-bold text-zinc-900 dark:text-white">Advisory Blueprint (Rp 2.5 Juta)</th>
                  <th className="py-3 px-4 font-bold text-emerald-500">Full MVP Monolith (Rp 50 Juta)</th>
                  <th className="py-3 px-4 font-bold text-amber-500">UMKM Starter (Rp 3.75 - 7.5 Juta)</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-200 dark:divide-zinc-800 font-sans">
                <tr>
                  <td className="py-3 px-4 font-bold text-zinc-800 dark:text-zinc-200">Target Persona</td>
                  <td className="py-3 px-4 text-zinc-600 dark:text-zinc-400">Founder & Tim Dev Internal</td>
                  <td className="py-3 px-4 text-zinc-800 dark:text-zinc-200 font-semibold">Scale-Up, Korporasi, Investor Ready</td>
                  <td className="py-3 px-4 text-zinc-600 dark:text-zinc-400">UMKM, Retail, & Usaha Jasa</td>
                </tr>
                <tr>
                  <td className="py-3 px-4 font-bold text-zinc-800 dark:text-zinc-200">Waktu Pengerjaan</td>
                  <td className="py-3 px-4 font-mono text-emerald-600 dark:text-emerald-400 font-bold">24 - 48 Jam Kerja</td>
                  <td className="py-3 px-4 font-mono text-emerald-600 dark:text-emerald-400 font-bold">4 - 6 Minggu (5 Sprint)</td>
                  <td className="py-3 px-4 font-mono text-zinc-600 dark:text-zinc-400">2 - 3 Minggu (2 Sprint)</td>
                </tr>
                <tr>
                  <td className="py-3 px-4 font-bold text-zinc-800 dark:text-zinc-200">PRD 26 Parameter</td>
                  <td className="py-3 px-4 text-emerald-500 font-bold">&check; Lengkap (JSON & Markdown)</td>
                  <td className="py-3 px-4 text-emerald-500 font-bold">&check; Lengkap + Terimplementasi</td>
                  <td className="py-3 px-4 text-zinc-500">Sederhana (Alur Inti)</td>
                </tr>
                <tr>
                  <td className="py-3 px-4 font-bold text-zinc-800 dark:text-zinc-200">PostgreSQL Strict ULID DDL</td>
                  <td className="py-3 px-4 text-emerald-500 font-bold">&check; DDL Script Siap Import</td>
                  <td className="py-3 px-4 text-emerald-500 font-bold">&check; Live di Server VPS</td>
                  <td className="py-3 px-4 text-emerald-500 font-bold">&check; Database Transaksional</td>
                </tr>
                <tr>
                  <td className="py-3 px-4 font-bold text-zinc-800 dark:text-zinc-200">Full-Stack Coding</td>
                  <td className="py-3 px-4 text-zinc-400 font-mono">Dikerjakan Tim Klien Sendiri</td>
                  <td className="py-3 px-4 text-emerald-500 font-bold">&check; Dikerjakan 100% Neriah Pro</td>
                  <td className="py-3 px-4 text-emerald-500 font-bold">&check; Dikerjakan 100% Neriah Pro</td>
                </tr>
                <tr>
                  <td className="py-3 px-4 font-bold text-zinc-800 dark:text-zinc-200">Mekanisme Pembayaran</td>
                  <td className="py-3 px-4 font-mono">100% di Muka</td>
                  <td className="py-3 px-4 font-mono text-emerald-600 dark:text-emerald-400 font-bold">DP 50% + Pelunasan UAT 50%</td>
                  <td className="py-3 px-4 font-mono">DP 50% + Pelunasan UAT 50%</td>
                </tr>
                <tr>
                  <td className="py-3 px-4 font-bold text-zinc-800 dark:text-zinc-200">Garansi & Bug Support</td>
                  <td className="py-3 px-4 text-zinc-500">Revisi Dokumen 7 Hari</td>
                  <td className="py-3 px-4 text-emerald-500 font-bold">&check; 3 Bulan Garansi Bug & Maintenance</td>
                  <td className="py-3 px-4 text-zinc-600 dark:text-zinc-400">1 Bulan Garansi</td>
                </tr>
                <tr>
                  <td className="py-3 px-4 font-bold text-zinc-800 dark:text-zinc-200">Hak Milik Source Code</td>
                  <td className="py-3 px-4 text-emerald-500 font-bold">&check; 100% Klien</td>
                  <td className="py-3 px-4 text-emerald-500 font-bold">&check; 100% Klien (No Vendor Lock-in)</td>
                  <td className="py-3 px-4 text-emerald-500 font-bold">&check; 100% Klien</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        {/* 4. FREQUENTLY ASKED QUESTIONS (FAQ) ACCORDION */}
        <div className="max-w-4xl mx-auto mb-16">
          <div className="text-center mb-8">
            <h3 className="text-2xl sm:text-3xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
              {isEn ? 'FREQUENTLY ASKED QUESTIONS' : 'PERTANYAAN YANG SERING DIAJUKAN (FAQ)'}
            </h3>
            <p className="text-xs text-zinc-500 font-sans mt-1">
              {isEn ? 'Everything you need to know about our pricing and engagement models.' : 'Jawaban lugas seputar model kerjasama, mekanisme pembayaran, dan hak cipta proyek.'}
            </p>
          </div>

          <div className="space-y-3">
            {faqs.map((faq, idx) => (
              <div 
                key={idx}
                className="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 rounded-none overflow-hidden transition"
              >
                <button
                  onClick={() => toggleFaq(idx)}
                  className="w-full py-4 px-5 text-left flex items-center justify-between gap-4 font-sans font-bold text-xs sm:text-sm text-zinc-900 dark:text-white hover:text-emerald-500 dark:hover:text-emerald-400 transition"
                >
                  <span>{faq.q}</span>
                  {openFaq === idx ? (
                    <ChevronUp className="w-4 h-4 text-emerald-500 shrink-0" />
                  ) : (
                    <ChevronDown className="w-4 h-4 text-zinc-400 shrink-0" />
                  )}
                </button>

                <AnimatePresence>
                  {openFaq === idx && (
                    <motion.div
                      initial={{ height: 0, opacity: 0 }}
                      animate={{ height: 'auto', opacity: 1 }}
                      exit={{ height: 0, opacity: 0 }}
                      transition={{ duration: 0.2 }}
                      className="px-5 pb-4 text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed border-t border-zinc-100 dark:border-zinc-800 pt-3"
                    >
                      {faq.a}
                    </motion.div>
                  )}
                </AnimatePresence>
              </div>
            ))}
          </div>
        </div>

        {/* 5. BOTTOM FAST CONTACT CALLOUT */}
        <div className="p-8 sm:p-10 bg-zinc-900 dark:bg-zinc-900/90 border-2 border-zinc-800 text-white text-center rounded-none shadow-xl relative overflow-hidden">
          <div className="absolute top-0 right-0 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
          
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
              className="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-400 text-black py-3 px-6 font-mono text-xs font-black uppercase tracking-wider transition rounded-none flex items-center justify-center gap-2"
            >
              <Send className="w-3.5 h-3.5" />
              <span>{isEn ? 'SUBMIT FORM INTAKE' : 'KIRIM INTAKE PROYEK'}</span>
            </button>

            <a
              href={`https://wa.me/${whatsappNumber}?text=${encodeURIComponent('Halo Lead Architect Neriah Pro, saya ingin mendiskusikan kebutuhan arsitektur dan pengembangan software kami.')}`}
              target="_blank"
              rel="noopener noreferrer"
              className="w-full sm:w-auto bg-zinc-800 hover:bg-zinc-700 text-white border border-zinc-700 py-3 px-6 font-mono text-xs font-bold uppercase tracking-wider transition rounded-none flex items-center justify-center gap-2"
            >
              <MessageSquare className="w-3.5 h-3.5 text-emerald-400" />
              <span>{isEn ? 'CHAT ON WHATSAPP' : 'CHAT WHATSAPP RESMI'}</span>
            </a>
          </div>
        </div>

      </div>

      {/* 6. INTERACTIVE FAST CONSULTATION & BOOKING MODAL (WORLD-CLASS CRM INTAKE) */}
      <AnimatePresence>
        {isModalOpen && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
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
              className="relative w-full max-w-lg bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-emerald-500 shadow-2xl p-6 sm:p-8 z-10 font-sans my-8"
            >
              <button
                onClick={() => setIsModalOpen(false)}
                className="absolute top-4 right-4 p-1.5 text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition"
              >
                <X className="w-5 h-5" />
              </button>

              <div className="mb-6">
                <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                  <ShieldCheck className="w-3 h-3" />
                  <span>{isEn ? 'CONFIDENTIAL & DIRECT TO CRM' : 'KERAHASIAAN DATA TERJAMIN (CRM WORLD-CLASS)'}</span>
                </div>
                <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                  {isEn ? 'FAST PROJECT INTAKE' : 'FORMULIR INTAKE & KONSULTASI'}
                </h3>
                <p className="text-xs text-zinc-500 font-sans mt-0.5">
                  {isEn 
                    ? 'Fill in your details below. Our technical team will reach out within 2 hours.'
                    : 'Lengkapi data kebutuhan Anda di bawah ini. Lead Architect kami akan menghubungi dalam maksimal 2 jam.'}
                </p>
              </div>

              {submitSuccess ? (
                <div className="p-6 bg-emerald-500/10 border border-emerald-500/30 text-center space-y-3">
                  <div className="w-12 h-12 bg-emerald-500 text-black flex items-center justify-center mx-auto">
                    <Check className="w-6 h-6" />
                  </div>
                  <h4 className="font-bold text-sm text-zinc-900 dark:text-white uppercase font-mono">
                    {isEn ? 'INQUIRY SUBMITTED SUCCESSFULLY!' : 'FORMULIR BERHASIL TERCATAT!'}
                  </h4>
                  <p className="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed font-sans">
                    {isEn 
                      ? 'We have recorded your lead into our CRM and initiated a WhatsApp chat for immediate discussion.'
                      : 'Data Anda telah tersimpan di CRM Neriah Pro. Obrolan WhatsApp resmi sedang dibuka secara otomatis.'}
                  </p>
                  <button
                    onClick={() => setIsModalOpen(false)}
                    className="mt-4 px-6 py-2.5 bg-emerald-500 text-black font-mono text-xs font-black uppercase tracking-wider"
                  >
                    {isEn ? 'CLOSE WINDOW' : 'TUTUP JENDELA'}
                  </button>
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

                  {/* Pilihan Paket */}
                  <div>
                    <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                      Pilihan Paket Layanan *
                    </label>
                    <select
                      value={selectedPackage}
                      onChange={(e) => setSelectedPackage(e.target.value)}
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden font-sans"
                      required
                    >
                      <option value="blueprint_advisory">Blueprint & PRD Architecture Only (Rp 2.500.000)</option>
                      <option value="full_mvp">Enterprise Rapid Monolith MVP (Rp 50.000.000 - DP 50%)</option>
                      <option value="umkm_starter">UMKM Digital Starter (Program Subsidi Voucher)</option>
                    </select>
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {/* Nama Lengkap */}
                    <div>
                      <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                        Nama Lengkap *
                      </label>
                      <input
                        type="text"
                        name="name"
                        value={formData.name}
                        onChange={handleInputChange}
                        placeholder="Contoh: Budi Santoso"
                        required
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden"
                      />
                    </div>

                    {/* Perusahaan / Usaha */}
                    <div>
                      <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                        Perusahaan / Bisnis
                      </label>
                      <input
                        type="text"
                        name="company"
                        value={formData.company}
                        onChange={handleInputChange}
                        placeholder="Contoh: PT Inovasi Maju"
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden"
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {/* Email */}
                    <div>
                      <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                        Email Bisnis *
                      </label>
                      <input
                        type="email"
                        name="email"
                        value={formData.email}
                        onChange={handleInputChange}
                        placeholder="budi@perusahaan.com"
                        required
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden"
                      />
                    </div>

                    {/* WhatsApp */}
                    <div>
                      <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                        No. WhatsApp *
                      </label>
                      <div className="flex">
                        <select
                          name="country_code"
                          value={formData.country_code}
                          onChange={handleInputChange}
                          className="bg-zinc-100 dark:bg-zinc-700 border border-r-0 border-zinc-300 dark:border-zinc-600 p-2 text-xs font-mono font-bold rounded-none"
                        >
                          <option value="+62">+62</option>
                          <option value="+65">+65</option>
                          <option value="+1">+1</option>
                          <option value="+44">+44</option>
                          <option value="+81">+81</option>
                        </select>
                        <input
                          type="tel"
                          name="phone"
                          value={formData.phone}
                          onChange={handleInputChange}
                          placeholder="8123456789"
                          required
                          className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden font-mono"
                        />
                      </div>
                    </div>
                  </div>

                  {/* Kode Voucher */}
                  <div>
                    <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                      Kode Voucher Promosi (Opsional)
                    </label>
                    <input
                      type="text"
                      name="voucher_code"
                      value={formData.voucher_code}
                      onChange={(e) => setFormData(prev => ({ ...prev, voucher_code: e.target.value.toUpperCase() }))}
                      placeholder="Contoh: UMKM-SUBSIDI-50 atau CORP-INNOVATION-15M"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden font-mono"
                    />
                  </div>

                  {/* Catatan / Kebutuhan */}
                  <div>
                    <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                      Ringkasan Kebutuhan / Ide Sistem
                    </label>
                    <textarea
                      name="notes"
                      rows="3"
                      value={formData.notes}
                      onChange={handleInputChange}
                      placeholder="Jelaskan secara ringkas sistem yang ingin Anda bangun, target user, atau tantangan arsitektur yang dihadapi..."
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden"
                    />
                  </div>

                  {/* Submit CTA */}
                  <button
                    type="submit"
                    disabled={isSubmitting}
                    className="w-full bg-emerald-500 hover:bg-emerald-400 text-black py-3 px-4 font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition rounded-none shadow-md disabled:opacity-60"
                  >
                    {isSubmitting ? (
                      <>
                        <Loader2 className="w-4 h-4 animate-spin" />
                        <span>{isEn ? 'RECORDING TO CRM...' : 'MENCATAT KE CRM NERIAH...'}</span>
                      </>
                    ) : (
                      <>
                        <Send className="w-4 h-4" />
                        <span>{isEn ? 'SUBMIT INQUIRY & CONNECT WHATSAPP' : 'KIRIM & KONSULTASI VIA WHATSAPP'}</span>
                      </>
                    )}
                  </button>
                </form>
              )}
            </motion.div>
          </div>
        )}
      </AnimatePresence>

    </div>
  );
}
