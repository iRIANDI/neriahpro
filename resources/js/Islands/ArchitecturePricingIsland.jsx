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
          sprint_batch: selectedPackage === 'full_mvp' ? sprintBatch : null,
          kickoff_slot: selectedPackage === 'full_mvp' ? kickoffSlot : null,
          has_blueprint: selectedPackage === 'full_mvp' ? hasBlueprint : null,
          blueprint_slug: selectedPackage === 'full_mvp' && hasBlueprint === 'ready' ? blueprintSlug : null,
          umkm_category: selectedPackage === 'umkm_starter' ? umkmCategory : null,
          notes: formData.notes
        })
      });

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw new Error(result.message || (isEn ? 'Failed to record reservation.' : 'Gagal mencatat reservasi jadwal.'));
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

  const faqs = [
    {
      q: isEn ? 'Are Instant Architectural Blueprint packages 100% self-service without Neriah Pro coding?' : 'Apakah paket Instant Architectural Blueprint 100% self-service tanpa keterlibatan koding Neriah Pro?',
      a: isEn 
        ? 'Yes, absolutely. Instant Architectural Blueprint packages (Spark, Lite, Pro, Ultimate) are pure digital architectural specifications and engineering deliverables (26-parameter PRD, PostgreSQL Strict ULID DDL SQL, Mermaid diagrams, Jira/Linear WBS, Decoupled 2026+ matrix, OpenAPI 3.1 contracts). They are designed for you, your in-house engineering team, freelancers, or AI coding agents to build the software independently. Neriah Pro does NOT write application code for these self-service packages. If you need Neriah Pro engineers to build, code, and deploy your software turnkey, select our Custom Engineering Studio contracts below (Full MVP Monolith or UMKM Starter).'
        : 'Ya, benar 100%. Paket Instant Architectural Blueprint (Spark, Lite, Pro, Ultimate) adalah produk spesifikasi arsitektur digital mandiri (PRD 26 parameter, skema DDL PostgreSQL Strict ULID, Diagram Mermaid, WBS sprint Jira-ready, matriks Decoupled 2026+, dan kontrak OpenAPI 3.1). Seluruh aset ini diperuntukkan bagi Anda, tim programmer in-house, freelancer, atau AI coding agent Anda untuk membangun dan mengoding aplikasinya sendiri. Neriah Pro sama sekali TIDAK terlibat dalam penulisan koding untuk paket self-service ini. Apabila Anda membutuhkan tim arsitek dan engineer Neriah Pro untuk mengoding, menguji, dan mendeploy aplikasi sampai siap pakai di server VPS, silakan pilih Layanan Custom Engineering Studio kami di bawah (Full MVP Monolith atau UMKM Starter).'
    },
    {
      q: isEn ? 'Do I need to log in to subscribe or use the Instant Blueprint packages?' : 'Apakah perlu login untuk berlangganan atau menggunakan paket Instant Blueprint?',
      a: isEn
        ? 'For the Spark (Free) tier, NO login is required — you can generate quick idea audits instantly in Guest Mode without creating an account. For paid packages (Lite, Pro, Ultimate), logging in or creating a Neriah Pro account is mandatory. An account ensures your digital license, persistent PRD version history, lifetime download access, and AI re-prompt tokens are securely preserved.'
        : 'Untuk paket Spark (Free), Anda TIDAK PERLU login sama sekali — Anda dapat langsung menggunakannya dalam Guest Mode tanpa registrasi ataupun kartu kredit. Namun untuk paket berbayar (Lite, Pro, dan Ultimate), Anda diwajibkan login atau mendaftar akun Neriah Pro terlebih dahulu. Kepemilikan akun ini diperlukan untuk mencatat lisensi digital resmi, mengamankan hak unduh seumur hidup (lifetime download), serta menyimpan riwayat versi dokumen PRD dan token regenerasi AI Anda.'
    },
    {
      q: isEn ? 'What are the usage limits and reset cycles for the free Spark package?' : 'Berapa batas kuota dan siklus reset untuk paket gratis (Spark)?',
      a: isEn
        ? 'The free Spark tier includes 2 idea audits per month per guest session. This free quota automatically resets back to 2 credits on the 1st of every month (00:00 UTC). Unsaved guest sessions expire in 7 days, so we recommend downloading your Markdown or PDF summary immediately.'
        : 'Paket gratis Spark memberikan kuota 2x audit ide & lean PRD per bulan per pengguna/perangkat. Kuota gratis ini otomatis di-reset kembali menjadi 2x kuota baru pada setiap tanggal 1 awal bulan baru (pukul 00:00 WIB). Sesi browser tamu aktif selama 7 hari, sehingga disarankan untuk segera mengekspor atau mengunduh ringkasan dokumen Markdown/PDF ke komputer atau ponsel Anda.'
    },
    {
      q: isEn ? 'What is the validity period and revision window for each package?' : 'Berapa lama masa berlaku akses dokumen dan jendela revisi untuk masing-masing paket?',
      a: isEn
        ? 'All paid tiers (Lite, Pro, Ultimate) grant lifetime download and reading access to your generated blueprints. In addition: Lite provides a 30-day form parameter revision window; Pro provides a 6-month window for unlimited AI re-prompts, schema tweaks, and architecture regenerations; Ultimate provides 1 year of priority enterprise updates plus a 60-day window to schedule your 60-minute 1-on-1 Google Meet session with our Lead Architect. For custom development contracts, Full MVP includes a 3-month SLA bug warranty post-deployment.'
        : 'Semua paket berbayar (Lite, Pro, Ultimate) memberikan hak akses dan unduh dokumen seumur hidup (Lifetime Download). Selain itu: Paket Lite memiliki jendela revisi form parameter selama 30 hari; Paket Pro menyediakan 6 bulan akses regenerasi AI sepuasnya (unlimited re-prompt) dan pembaruan arsitektur; Paket Ultimate mencakup 1 tahun prioritas pembaruan skema enterprise serta jendela 60 hari untuk memesan sesi Google Meet 60 menit bersama Lead Architect Neriah Pro. Untuk kontrak custom development, Full MVP dilengkapi 3 bulan garansi bug dan SLA prioritas pasca-peluncuran live.'
    },
    {
      q: isEn ? 'Why should we pay Rp 2,500,000 for Advisory Blueprint if we already have in-house programmers?' : 'Mengapa butuh Jasa Blueprint Studio (Rp 2.500.000) jika kami sudah memiliki programmer in-house?',
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
      q: isEn ? 'Who owns the intellectual property and source code in Neriah Pro Studio contracts?' : 'Siapakah pemilik hak cipta dan source code aplikasi pada pengerjaan Neriah Pro Studio?',
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
      q: isEn ? 'What is the estimated delivery timeline for Studio contracts?' : 'Berapa lama estimasi pengerjaan masing-masing paket Studio Neriah Pro?',
      a: isEn 
        ? 'Advisory Blueprint PRD is generated and finalized within 24 - 48 hours. Full MVP Rapid Monolith takes 4 to 6 weeks spanning 5 structured sprints.'
        : 'Paket Advisory Blueprint diselesaikan dalam 24 hingga 48 jam kerja setelah sesi discovery awal. Sedangkan Full MVP diselesaikan dalam rentang 4 hingga 6 minggu (5 sprint terstruktur).'
    },
    {
      q: isEn ? 'Does Project OS support Decoupled / Headless architecture as well as Monolith?' : 'Apakah Project OS mendukung arsitektur Decoupled / Headless atau hanya Monolith?',
      a: isEn
        ? 'Yes, 100% supports both paradigms! Project OS evaluates your business domain: if your team requires independent frontends (Next.js 15 App Router / Nuxt 3) or mobile apps (Flutter / React Native) with headless APIs, it outputs a complete 2026+ Decoupled blueprint (Cloudflare Pages edge, Coolify VPS backend, Cloudflare R2 $0 egress, OpenAPI 3.1 & Scalar docs, X-Idempotency-Key guard, and Keyset O(1) pagination) with lean infrastructure costs.'
        : 'Ya, 100% mendukung kedua paradigma! Project OS mengevaluasi domain bisnis Anda secara cerdas: jika tim Anda membutuhkan frontend independen (Next.js 15 App Router / Nuxt 3) atau mobile multi-platform (Flutter / React Native) dengan headless API, sistem otomatis menerbitkan cetak biru Decoupled 2026+ lengkap (Cloudflare Pages edge, Coolify VPS backend, Cloudflare R2 $0 egress, kontrak OpenAPI 3.1 & Scalar docs, proteksi X-Idempotency-Key, dan Keyset O(1) pagination) dengan biaya server yang sangat efisien.'
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

          {/* ENGINEERING DISCIPLINE & ASSURANCE BAR (CLEAN, SOLID, NO GRADIENTS, NO VOUCHER DUMP) */}
          <div className="mt-8 py-3 px-4 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex flex-wrap items-center justify-around gap-4 text-xs font-mono text-zinc-600 dark:text-zinc-400">
            <div className="flex items-center gap-2">
              <ShieldCheck className="w-4 h-4 text-emerald-500 shrink-0" />
              <span>{isEn ? 'Legal Scope-Locked Digital Contract' : 'Kontrak Hukum Digital Scope-Locked'}</span>
            </div>
            <div className="flex items-center gap-2">
              <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0" />
              <span>{isEn ? '50% Milestone DP via Midtrans' : 'DP 50% Milestone Terproteksi'}</span>
            </div>
            <div className="flex items-center gap-2">
              <Clock className="w-4 h-4 text-emerald-500 shrink-0" />
              <span>{isEn ? 'Strict Sprint Capacity: Max 2 Projects / Cycle' : 'Kapasitas Terjadwal: Maks. 2 Proyek / Siklus'}</span>
            </div>
          </div>
        </div>

        {/* 2. INSTANT ARCHITECTURAL BLUEPRINT PACKAGES (100% SELF-SERVICE // NO NERIAH PRO CODING) */}
        <div className="mb-20">
          <div className="text-center max-w-3xl mx-auto mb-8">
            <div className="inline-flex items-center gap-2 px-3 py-1 bg-cyan-500/10 border border-cyan-500/30 text-cyan-600 dark:text-cyan-400 font-mono text-xs uppercase tracking-wider font-bold mb-3 rounded-none">
              <Terminal className="w-3.5 h-3.5" />
              <span>{isEn ? 'PROJECT OS // 100% SELF-SERVICE DIGITAL BLUEPRINT' : 'PROJECT OS // 100% SELF-SERVICE DIGITAL GENERATOR'}</span>
            </div>
            <h2 className="text-2xl sm:text-4xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2 font-sans">
              {isEn ? 'Instant Architectural Blueprint Packages' : 'Pilihan Paket Instant Architectural Blueprint'}
            </h2>
            <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed">
              {isEn
                ? 'Generate enterprise-grade specifications instantly without hiring an architect. Ranging from free vision audits to production-grade Decoupled and Monolith blueprints with Jira/Linear WBS. Built for developers and founders to execute on their own.'
                : 'Hasilkan dokumen spesifikasi arsitektur enterprise berkualitas Principal Architect secara instan dan mandiri: Mulai dari audit ide gratis hingga cetak biru siap bangun dengan WBS 5 sprint dan rekomendasi arsitektur Decoupled 2026+.'}
            </p>
          </div>

          {/* PROMINENT DISCLAIMER: 100% SELF-SERVICE // ZERO NERIAH PRO CODING */}
          <div className="mb-8 p-4 sm:p-5 bg-amber-500/10 border-2 border-amber-500/30 text-zinc-900 dark:text-zinc-100 rounded-none shadow-xs">
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-2">
              <div className="flex items-center gap-2">
                <span className="p-1 px-2 bg-amber-500 text-black font-black text-xs font-mono">⚠️ PENTING</span>
                <span className="font-mono text-xs font-black uppercase tracking-wider text-amber-600 dark:text-amber-400">
                  {isEn ? '100% SELF-SERVICE // ZERO NERIAH PRO CODING' : '100% SELF-SERVICE // TIDAK ADA KODING DARI NERIAH PRO'}
                </span>
              </div>
              <span className="font-mono text-[10px] px-2 py-0.5 bg-amber-500/20 text-amber-700 dark:text-amber-300 font-bold uppercase">
                {isEn ? 'SELF-EXECUTED BY CLIENT' : 'DIKERJAKAN OLEH DEVELOPER ANDA'}
              </span>
            </div>
            <p className="text-xs sm:text-sm text-zinc-700 dark:text-zinc-300 font-sans leading-relaxed">
              {pricingSettings.retail_disclaimer || (isEn 
                ? 'All Instant Architectural Blueprint packages below are 100% self-service digital deliverables (PRD, PostgreSQL Strict ULID DDL SQL, Mermaid diagrams, Jira WBS, OpenAPI 3.1 contracts). They are used directly by you, your in-house engineering team, freelancers, or AI coding agents to build your own application. Neriah Pro does NOT write application code for these packages.' 
                : 'Seluruh paket Instant Architectural Blueprint di bawah ini adalah produk spesifikasi arsitektur digital mandiri (100% Self-Service). Dihasilkan secara instan oleh AI Project OS untuk digunakan langsung oleh Anda, tim in-house programmer, agensi, atau AI coding agent Anda dalam membangun sistem sendiri. Neriah Pro sama sekali TIDAK terlibat dalam penulisan koding untuk paket ini.')}
            </p>
            <div className="mt-3 pt-3 border-t border-amber-500/20 flex flex-wrap items-center justify-between gap-y-2 text-[11px] font-mono text-zinc-600 dark:text-zinc-400">
              <div className="flex items-center gap-1.5">
                <ShieldCheck className="w-3.5 h-3.5 text-amber-500 shrink-0" />
                <span><strong>{isEn ? 'Access & Login Policy:' : 'Kebijakan Akses & Login:'}</strong> {pricingSettings.retail_login_policy || 'Guest Mode untuk Spark (Free). Wajib Login / Akun untuk paket Lite, Pro, & Ultimate guna proteksi dokumen & lisensi.'}</span>
              </div>
              <div className="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-bold">
                <ArrowRight className="w-3.5 h-3.5 shrink-0" />
                <span>{isEn ? 'Need Neriah Pro to build & code the app? See Studio Contracts below.' : 'Ingin Neriah Pro yang mengoding & mendeploy aplikasi? Lihat Layanan Studio di bawah.'}</span>
              </div>
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 items-stretch">
            {/* SPARK / FREE TIER */}
            <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 p-5 sm:p-6 flex flex-col justify-between rounded-none hover:border-cyan-500/60 transition group relative">
              <div>
                <div className="flex items-center justify-between mb-3">
                  <span className="font-mono text-[10px] font-black tracking-wider uppercase text-zinc-500">
                    TIER 01 // AUDIT
                  </span>
                  <span className="px-2 py-0.5 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-mono text-[9px] font-bold">
                    FREE GUEST TIER
                  </span>
                </div>

                <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-1">
                  Spark / Free Audit
                </h3>

                <p className="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                  {isEn ? 'Quick idea sanity check, core problem statement & lean MVP scoping.' : 'Audit cepat kelayakan ide bisnis, pemetaan masalah utama, dan cakupan lean MVP.'}
                </p>

                <div className="mb-4 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 space-y-2">
                  <div>
                    <span className="text-[10px] text-zinc-500 font-mono block">Tarif Akses Digital:</span>
                    <div className="flex items-baseline gap-1">
                      <span className="text-2xl font-black font-mono text-zinc-900 dark:text-white">
                        Rp {pricingSettings.retail_spark_price || '0'}
                      </span>
                      <span className="text-[10px] font-mono text-emerald-500 font-bold">/ GRATIS</span>
                    </div>
                  </div>

                  <div className="pt-2 border-t border-zinc-200 dark:border-zinc-800 text-[10px] font-mono space-y-1">
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Pengerjaan:</span>
                      <span className="font-bold text-cyan-600 dark:text-cyan-400">100% Mandiri</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Batas Kuota:</span>
                      <span className="font-bold text-zinc-900 dark:text-white">{pricingSettings.retail_spark_limit || '2x Audit / Bulan'}</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Siklus Reset:</span>
                      <span className="font-bold text-emerald-600 dark:text-emerald-400">Tiap Tgl 1 Awal Bulan</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Masa Berlaku:</span>
                      <span className="font-bold text-zinc-700 dark:text-zinc-300">7 Hari Guest Session</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Syarat Login:</span>
                      <span className="font-bold text-emerald-600 dark:text-emerald-400">Tanpa Login (Guest)</span>
                    </div>
                  </div>
                </div>

                <div className="space-y-2 mb-6">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">OUTPUT DIDAPATKAN:</span>
                  {[
                    'Analisis Kelayakan Bisnis & Problem Framing',
                    'Executive Summary & Target Audiens',
                    '5 Fitur Esensial MVP Prioritas',
                    'Estimasi Kompleksitas & TCO Awal',
                    'Ekspor Ringkasan Markdown ke Lokal',
                  ].map((f, i) => (
                    <div key={i} className="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                      <Check className="w-3.5 h-3.5 text-cyan-500 shrink-0 mt-0.5" />
                      <span className="text-[11px] leading-tight">{f}</span>
                    </div>
                  ))}
                </div>
              </div>

              <a
                href="/blueprint?tier=spark"
                className="w-full bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-white py-2.5 px-3 font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none text-center"
              >
                <span>{isEn ? 'TRY LIVE FREE (GUEST)' : 'COBA GRATIS SEKARANG (GUEST)'}</span>
                <ArrowRight className="w-3 h-3" />
              </a>
            </div>

            {/* LITE PRD TIER */}
            <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 p-5 sm:p-6 flex flex-col justify-between rounded-none hover:border-cyan-500/60 transition group relative">
              <div>
                <div className="flex items-center justify-between mb-3">
                  <span className="font-mono text-[10px] font-black tracking-wider uppercase text-zinc-500">
                    TIER 02 // ESSENTIAL
                  </span>
                  <span className="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-[9px] font-bold">
                    SOLO / DEV
                  </span>
                </div>

                <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-1">
                  Lite PRD Generator
                </h3>

                <p className="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                  {isEn ? '26 Structured Parameters PRD + PostgreSQL Strict ULID DDL schema.' : 'Spesifikasi PRD 26 parameter lengkap + skema SQL DDL PostgreSQL Strict ULID siap eksekusi.'}
                </p>

                <div className="mb-4 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 space-y-2">
                  <div>
                    <span className="text-[10px] text-zinc-500 font-mono block">Biaya Lisensi Digital:</span>
                    <div className="flex items-baseline gap-1">
                      <span className="text-2xl font-black font-mono text-zinc-900 dark:text-white">
                        Rp {pricingSettings.retail_lite_price || '99.000'}
                      </span>
                      <span className="text-[10px] font-mono text-zinc-500">/ project</span>
                    </div>
                  </div>

                  <div className="pt-2 border-t border-zinc-200 dark:border-zinc-800 text-[10px] font-mono space-y-1">
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Pengerjaan:</span>
                      <span className="font-bold text-cyan-600 dark:text-cyan-400">100% Mandiri</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Batas Kuota:</span>
                      <span className="font-bold text-zinc-900 dark:text-white">{pricingSettings.retail_lite_limit || '1 Proyek PRD 26 Param'}</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Siklus Reset:</span>
                      <span className="font-bold text-zinc-700 dark:text-zinc-300">Sekali Bayar (1 Proyek)</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Masa Berlaku:</span>
                      <span className="font-bold text-emerald-600 dark:text-emerald-400">Unduh Selamanya + 30hr Rev</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Syarat Login:</span>
                      <span className="font-bold text-amber-600 dark:text-amber-400">Wajib Login Akun</span>
                    </div>
                  </div>
                </div>

                <div className="space-y-2 mb-6">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">OUTPUT DIDAPATKAN:</span>
                  {[
                    'Semua Output Spark Tier',
                    'PRD 26 Parameter Lengkap (JSON & MD)',
                    'Skema PostgreSQL Strict ULID DDL SQL',
                    'Standar Keyset O(1) Pagination Rules',
                    'Work Breakdown Structure (WBS) 2 Sprint',
                  ].map((f, i) => (
                    <div key={i} className="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                      <Check className="w-3.5 h-3.5 text-cyan-500 shrink-0 mt-0.5" />
                      <span className="text-[11px] leading-tight">{f}</span>
                    </div>
                  ))}
                </div>
              </div>

              <button
                type="button"
                onClick={() => openBookingModal('retail_lite')}
                className="w-full bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black py-2.5 px-3 font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none cursor-pointer"
              >
                <span>{isEn ? 'GET LITE PRD' : 'PILIH PAKET LITE'}</span>
                <ArrowRight className="w-3 h-3" />
              </button>
            </div>

            {/* PRO PRD TIER (BEST VALUE) */}
            <div className="bg-white dark:bg-zinc-900 border-2 border-emerald-500 p-5 sm:p-6 flex flex-col justify-between rounded-none shadow-xl relative transform xl:-translate-y-1">
              <div className="absolute -top-3 left-1/2 -translate-x-1/2 bg-emerald-500 text-black px-3 py-0.5 font-mono text-[9px] font-black uppercase tracking-widest shadow-xs">
                BEST VALUE // DEVELOPER FAVORITE
              </div>

              <div>
                <div className="flex items-center justify-between mb-3 mt-1">
                  <span className="font-mono text-[10px] font-black tracking-wider uppercase text-emerald-600 dark:text-emerald-400">
                    TIER 03 // PRODUCTION
                  </span>
                  <span className="px-2 py-0.5 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-mono text-[9px] font-bold">
                    STARTUP &amp; AGENCY
                  </span>
                </div>

                <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-1">
                  Pro Production PRD
                </h3>

                <p className="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                  {isEn ? 'Production blueprint, 6 Mermaid diagrams, Decoupled & Monolith matrix, WBS 5 Sprints.' : 'Cetak biru produksi, 6 diagram Mermaid, matriks arsitektur Decoupled & Monolith, serta WBS 5 sprint.'}
                </p>

                <div className="mb-4 p-3 bg-emerald-500/5 border border-emerald-500/30 space-y-2">
                  <div>
                    <span className="text-[10px] text-zinc-500 font-mono block">Biaya Lisensi Digital:</span>
                    <div className="flex items-baseline gap-1">
                      <span className="text-2xl font-black font-mono text-zinc-900 dark:text-white">
                        Rp {pricingSettings.retail_pro_price || '399.000'}
                      </span>
                      <span className="text-[10px] font-mono text-zinc-500">/ project</span>
                    </div>
                  </div>

                  <div className="pt-2 border-t border-emerald-500/20 text-[10px] font-mono space-y-1">
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Pengerjaan:</span>
                      <span className="font-bold text-emerald-500">100% Mandiri</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Batas Kuota:</span>
                      <span className="font-bold text-zinc-900 dark:text-white">{pricingSettings.retail_pro_limit || '1 Proyek PRD + WBS 5 Sprint'}</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Siklus Reset:</span>
                      <span className="font-bold text-zinc-700 dark:text-zinc-300">Sekali Bayar (1 Proyek)</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Masa Berlaku:</span>
                      <span className="font-bold text-emerald-600 dark:text-emerald-400">Unduh Selamanya + 6bln AI</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Syarat Login:</span>
                      <span className="font-bold text-amber-600 dark:text-amber-400">Wajib Login Akun</span>
                    </div>
                  </div>
                </div>

                <div className="space-y-2 mb-6">
                  <span className="text-[10px] font-mono font-bold text-emerald-500 uppercase tracking-wider block">OUTPUT DIDAPATKAN:</span>
                  {[
                    'Semua Output Lite Tier',
                    'Cetak Biru Decoupled 2026+ (Next.js 15, Cloudflare)',
                    '6 Diagram Mermaid (ERD, Data Flow, Sequence, Gantt)',
                    'WBS 5 Sprint Linear / Jira Ready',
                    'OpenAPI 3.1 & Idempotency Specification',
                    'Panduan Anti-AI-Slop & UI Design Tokens',
                  ].map((f, i) => (
                    <div key={i} className="flex items-start gap-2 text-xs text-zinc-800 dark:text-zinc-200 font-medium">
                      <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                      <span className="text-[11px] leading-tight">{f}</span>
                    </div>
                  ))}
                </div>
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

            {/* ULTIMATE ENTERPRISE ADVISORY */}
            <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 p-5 sm:p-6 flex flex-col justify-between rounded-none hover:border-amber-500/60 transition group relative">
              <div>
                <div className="flex items-center justify-between mb-3">
                  <span className="font-mono text-[10px] font-black tracking-wider uppercase text-zinc-500">
                    TIER 04 // ENTERPRISE
                  </span>
                  <span className="px-2 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-mono text-[9px] font-bold">
                    + 1-ON-1 CALL
                  </span>
                </div>

                <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-1">
                  Ultimate Advisory
                </h3>

                <p className="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                  {isEn ? 'Full PRD, Multi-AI Failover Hub, Threat Shield, and 1-on-1 Scoping Consultation.' : 'PRD Ultimate, Multi-AI Failover Hub, Threat Shield, dan 1 sesi konsultasi langsung dengan Lead Architect.'}
                </p>

                <div className="mb-4 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 space-y-2">
                  <div>
                    <span className="text-[10px] text-zinc-500 font-mono block">Investasi Advisory:</span>
                    <div className="flex items-baseline gap-1">
                      <span className="text-2xl font-black font-mono text-zinc-900 dark:text-white">
                        Rp {pricingSettings.retail_ultimate_price || '1.490.000'}
                      </span>
                      <span className="text-[10px] font-mono text-zinc-500">/ project</span>
                    </div>
                  </div>

                  <div className="pt-2 border-t border-zinc-200 dark:border-zinc-800 text-[10px] font-mono space-y-1">
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Pengerjaan:</span>
                      <span className="font-bold text-amber-600 dark:text-amber-400">Mandiri + 1-on-1 Call</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Batas Kuota:</span>
                      <span className="font-bold text-zinc-900 dark:text-white">{pricingSettings.retail_ultimate_limit || '1 Proyek Enterprise'}</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Siklus Reset:</span>
                      <span className="font-bold text-zinc-700 dark:text-zinc-300">Sekali Bayar (1 Proyek)</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Masa Berlaku:</span>
                      <span className="font-bold text-emerald-600 dark:text-emerald-400">Unduh Selamanya + 1th Update</span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>Syarat Login:</span>
                      <span className="font-bold text-amber-600 dark:text-amber-400">Wajib Akun Terverifikasi</span>
                    </div>
                  </div>
                </div>

                <div className="space-y-2 mb-6">
                  <span className="text-[10px] font-mono font-bold text-amber-500 uppercase tracking-wider block">OUTPUT DIDAPATKAN:</span>
                  {[
                    'Semua Output Pro Production Tier',
                    'AI Multi-Model Failover Token Shield Strategy',
                    'Zero-Trust CORS & Anti-Malware Hardening',
                    '1 Sesi 60 Menit Architecture Call (Google Meet)',
                    'Validasi & Review Tim Engineering Internal',
                    'Non-Disclosure Agreement (NDA) Korporat',
                  ].map((f, i) => (
                    <div key={i} className="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                      <Check className="w-3.5 h-3.5 text-amber-500 shrink-0 mt-0.5" />
                      <span className="text-[11px] leading-tight">{f}</span>
                    </div>
                  ))}
                </div>
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

        {/* 3. NERIAH PRO CUSTOM ENGINEERING STUDIO (FULL DEVELOPMENT CONTRACTS BY NERIAH PRO) */}
        <div className="mb-20">
          <div className="text-center max-w-3xl mx-auto mb-10">
            <div className="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-mono text-xs uppercase tracking-wider font-bold mb-3 rounded-none">
              <Crown className="w-3.5 h-3.5" />
              <span>{isEn ? 'NERIAH PRO ENGINEERING STUDIO // FULL CUSTOM CONTRACTS' : 'NERIAH PRO ENGINEERING STUDIO // KONTRAK PENGERJAAN PENUH'}</span>
            </div>
            <h2 className="text-2xl sm:text-4xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2 font-sans">
              {isEn ? 'Full Custom Engineering & Development Contracts' : 'Layanan Pengembangan Penuh & Pengerjaan Kode oleh Tim Neriah Pro'}
            </h2>
            <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed">
              {isEn
                ? 'End-to-end custom software engineering executed directly by Neriah Pro senior architects and engineers. We write every line of code, provision VPS servers, setup databases, integrate payments, and deliver turnkey production systems with digital legal contracts and bug warranty.'
                : 'Proyek di bawah ini dikerjakan, dikoding, diuji, dan dideploy langsung oleh tim Senior Software Architect & Engineer Neriah Pro end-to-end. Kami menulis seluruh kode sumber, menyusun basis data skala jutaan baris, mengintegrasikan payment gateway, serta mengonfigurasi dedicated server VPS siap pakai dengan kontrak hukum digital dan garansi bug pasca-peluncuran.'}
            </p>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 items-stretch">
            {/* TIER 1: ADVISORY ONLY / ARCHITECTURE BLUEPRINT */}
            <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-800 p-6 sm:p-8 flex flex-col justify-between rounded-none shadow-xs hover:border-emerald-500/60 transition group relative">
              <div>
                <div className="flex items-center justify-between mb-4">
                  <span className="font-mono text-xs font-black tracking-wider uppercase text-zinc-500 dark:text-zinc-400">
                    SKENARIO 1 // ADVISORY STUDIO
                  </span>
                  <span className="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-[10px] font-bold">
                    ONE-TIME INVESTMENT
                  </span>
                </div>

                <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2">
                  Blueprint &amp; PRD Architecture Advisory
                </h3>

                <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-6">
                  {isEn 
                    ? 'Designed for founders and CTOs with an in-house or freelance dev team who need a rock-solid technical blueprint to prevent scope creep and architectural failures.'
                    : 'Solusi ideal bagi founder, CTO, atau manajer IT yang sudah memiliki tim programmer sendiri, namun membutuhkan pendampingan cetak biru arsitektur enterprise siap kerja tanpa menyewa kami untuk coding.'}
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
                    &bull; {isEn ? 'Includes full PRD synthesis + ERD schema + Scoping Discovery' : 'Termasuk PRD 26 parameter + Skema DDL + Sesi Scoping'}
                  </span>
                  <div className="mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-800 text-[10px] font-mono text-zinc-500">
                    <span>Pengerjaan Koding: <strong>Dieksekusi Tim Klien Sendiri (Biaya Rp 2.5jt memotong DP 50% jika lanjut Full MVP)</strong></span>
                  </div>
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
                  type="button"
                  onClick={() => openBookingModal('blueprint_advisory')}
                  className="w-full bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black py-3 px-4 font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition rounded-none shadow-xs text-center cursor-pointer"
                >
                  <span>{isEn ? 'ORDER ADVISORY NOW' : 'PESAN JASA ADVISORY (RP 2.5 JT)'}</span>
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
                <span>{isEn ? 'DIKERJAKAN 100% OLEH NERIAH PRO // FULL MVP' : 'DIKERJAKAN 100% OLEH NERIAH PRO // FULL MVP'}</span>
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

                <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2">
                  Enterprise Rapid Monolith MVP
                </h3>

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
                    &bull; Pelunasan sisa 50% setelah UAT &amp; Live Production Deploy
                  </span>
                  <div className="mt-2 pt-2 border-t border-emerald-500/20 text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                    <span>Pengerjaan Koding: 100% Dikerjakan oleh Software Architect &amp; Engineer Neriah Pro</span>
                  </div>
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
                  onClick={() => openBookingModal('full_mvp')}
                  className="w-full bg-emerald-500 hover:bg-emerald-400 text-black py-3.5 px-4 font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition rounded-none shadow-lg cursor-pointer"
                >
                  <span>{isEn ? 'START 5 SPRINT DEVELOPMENT (DP 50%)' : 'RESERVASI SPRINT PROYEK (DP 50%)'}</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </button>

                <button
                  onClick={() => openBookingModal('full_mvp')}
                  className="w-full bg-transparent hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 py-2.5 px-4 font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none cursor-pointer"
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
                    SUBSIDI 50% // DIKERJAKAN NERIAH PRO
                  </span>
                </div>

                <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2">
                  UMKM Digital Starter
                </h3>

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
                    <span className="text-[11px] font-mono text-zinc-500">Dengan Subsidi UMKM (50%):</span>
                    <span className="text-xs font-mono font-black text-amber-500">
                      Rp 3.750.000
                    </span>
                  </div>
                  <span className="text-[10px] text-amber-500 font-mono mt-1 block">
                    &bull; {isEn ? 'Limited community subsidy quota (2 business slots / month)' : 'Program subsidi terbatas (Alokasi 2 kuota usaha / bulan)'}
                  </span>
                  <div className="mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-800 text-[10px] font-mono text-amber-600 dark:text-amber-400 font-bold">
                    <span>Pengerjaan Koding: 100% Dikerjakan oleh Tim Neriah Pro sampai Siap Pakai</span>
                  </div>
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
                  className="w-full bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black py-3 px-4 font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition rounded-none shadow-xs cursor-pointer"
                >
                  <span>{isEn ? 'APPLY UMKM SUBSIDY' : 'KLAIM SUBSIDI UMKM (50%)'}</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </button>

                <a
                  href={`https://wa.me/${whatsappNumber}?text=${encodeURIComponent('Halo Lead Architect Neriah Pro, saya ingin konsultasi mengenai Program Subsidi UMKM Digital Starter.')}`}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="w-full bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 py-2.5 px-4 font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none text-center"
                >
                  <MessageSquare className="w-3.5 h-3.5 text-amber-500" />
                  <span>{isEn ? 'CHAT WITH ADVISOR' : 'KONSULTASI KEBUTUHAN UMKM'}</span>
                </a>
              </div>
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
              {isEn ? 'Transparent side-by-side technical breakdown across self-service tools and engineering studio contracts.' : 'Perbandingan transparan parameter teknis antara generator mandiri (self-service) dan kontrak rekayasa penuh studio Neriah Pro.'}
            </p>
          </div>

          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs font-sans border-collapse">
              <thead>
                <tr className="border-b-2 border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950 font-mono text-[11px] uppercase tracking-wider">
                  <th className="py-3 px-3 font-bold text-zinc-600 dark:text-zinc-400">Parameter Evaluasi</th>
                  <th className="py-3 px-3 font-bold text-cyan-600 dark:text-cyan-400 bg-cyan-500/5">
                    <div>Instant Blueprint</div>
                    <div className="text-[9px] font-normal text-zinc-500">Spark / Lite / Pro / Ultimate</div>
                  </th>
                  <th className="py-3 px-3 font-bold text-zinc-900 dark:text-white">
                    <div>Advisory Studio</div>
                    <div className="text-[9px] font-normal text-zinc-500">Rp 2.500.000</div>
                  </th>
                  <th className="py-3 px-3 font-bold text-emerald-500">
                    <div>Full MVP Monolith</div>
                    <div className="text-[9px] font-normal text-zinc-500">Rp 50.000.000 (DP 50%)</div>
                  </th>
                  <th className="py-3 px-3 font-bold text-amber-500">
                    <div>UMKM Starter</div>
                    <div className="text-[9px] font-normal text-zinc-500">Rp 3.75 - 7.5 Jt</div>
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-200 dark:divide-zinc-800 font-sans">
                {/* 1. SIAPA YANG MELAKUKAN KODING */}
                <tr className="bg-amber-500/5 dark:bg-amber-500/10">
                  <td className="py-3 px-3 font-bold text-zinc-900 dark:text-white font-mono">Siapa yang Melakukan Koding?</td>
                  <td className="py-3 px-3 text-cyan-700 dark:text-cyan-300 font-mono font-bold bg-cyan-500/5">
                    100% Mandiri oleh Developer / Tim Anda (Zero Neriah Pro Coding)
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 font-mono">
                    Tim Klien Sendiri (Didampingi Scoping)
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-mono font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>100% Dikerjakan Neriah Pro</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-amber-600 dark:text-amber-400 font-mono font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-amber-500 shrink-0" />
                      <span>100% Dikerjakan Neriah Pro</span>
                    </span>
                  </td>
                </tr>

                {/* 2. PERSYARATAN AKUN & LOGIN */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">Persyaratan Akun &amp; Login</td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5">
                    Spark: <strong>Guest Mode (Tanpa Login)</strong><br />
                    Lite, Pro, Ultimate: <strong>Wajib Login Akun</strong>
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300">
                    Wajib Registrasi Akun Klien Resmi
                  </td>
                  <td className="py-3 px-3 text-zinc-800 dark:text-zinc-200 font-semibold">
                    Kontrak Legal &amp; Akun Korporat
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300">
                    Registrasi Akun Klien UMKM
                  </td>
                </tr>

                {/* 3. BATAS KUOTA & SIKLUS RESET */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">Batas Kuota &amp; Siklus Reset</td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5">
                    Spark: <strong>2x Audit/Bulan (Reset tiap tgl 1)</strong><br />
                    Lite/Pro/Ultimate: <strong>1 Proyek per Lisensi</strong>
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300">
                    1 Proyek Spesifikasi Terfokus
                  </td>
                  <td className="py-3 px-3 text-zinc-800 dark:text-zinc-200 font-semibold">
                    1 Proyek Penuh (Kapasitas Maks. 2 Proyek/Siklus)
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300">
                    1 Sistem Usaha (Alokasi Subsidi Bulanan)
                  </td>
                </tr>

                {/* 4. MASA BERLAKU & JENDELA REVISI */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">Masa Berlaku &amp; Jendela Revisi</td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5">
                    Spark: 7hr Guest Session<br />
                    Lite: Unduh Selamanya + 30hr Revisi<br />
                    Pro: Unduh Selamanya + 6bln AI Regen<br />
                    Ultimate: Selamanya + 1th Update + 60hr Meet
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300">
                    Dokumen Selamanya + 7 Hari Pendampingan Revisi
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold">
                    3 Bulan Garansi Bug &amp; SLA Maintenance Pasca Live
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300">
                    1 Bulan Garansi Bug &amp; Panduan Operasional
                  </td>
                </tr>

                {/* 5. TARGET PERSONA */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">Target Persona</td>
                  <td className="py-3 px-3 text-zinc-600 dark:text-zinc-400 bg-cyan-500/5">
                    Solo Dev, Tech Lead, Founder, Agensi yang Koding Sendiri
                  </td>
                  <td className="py-3 px-3 text-zinc-600 dark:text-zinc-400">
                    CTO &amp; Founder dengan Tim Dev Internal
                  </td>
                  <td className="py-3 px-3 text-zinc-800 dark:text-zinc-200 font-semibold">
                    Scale-Up, Korporasi, Investor Ready
                  </td>
                  <td className="py-3 px-3 text-zinc-600 dark:text-zinc-400">
                    UMKM, Toko Retail, Usaha Jasa &amp; F&amp;B
                  </td>
                </tr>

                {/* 6. WAKTU PENGERJAAN */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">Waktu Pengerjaan</td>
                  <td className="py-3 px-3 font-mono text-cyan-600 dark:text-cyan-400 font-bold bg-cyan-500/5">
                    Instan (Hitungan Detik via AI)
                  </td>
                  <td className="py-3 px-3 font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                    24 - 48 Jam Kerja
                  </td>
                  <td className="py-3 px-3 font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                    4 - 6 Minggu (5 Sprint)
                  </td>
                  <td className="py-3 px-3 font-mono text-zinc-600 dark:text-zinc-400">
                    2 - 3 Minggu (2 Sprint)
                  </td>
                </tr>

                {/* 7. PRD 26 PARAMETER */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">PRD 26 Parameter</td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5">
                    Spark: Lean 5 Fitur<br />
                    Lite, Pro, Ultimate: <strong>Lengkap (JSON &amp; MD)</strong>
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>Lengkap (JSON &amp; Markdown)</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>Lengkap + Terimplementasi</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-zinc-500">Sederhana (Alur Inti Usaha)</td>
                </tr>

                {/* 8. POSTGRESQL STRICT ULID DDL */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">PostgreSQL Strict ULID DDL</td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5">
                    Lite, Pro, Ultimate: <strong>DDL SQL Siap Import</strong>
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>DDL Script Siap Import</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>Live di Server VPS</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>Database Transaksional</span>
                    </span>
                  </td>
                </tr>

                {/* 9. CETAK BIRU DECOUPLED 2026+ */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">Cetak Biru Decoupled 2026+</td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5">
                    Pro &amp; Ultimate: <strong>Next.js 15, Cloudflare, OpenAPI 3.1</strong>
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>Tools Matrix, Server Topology, OpenAPI 3.1</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>API Resources Terisolasi (Siap Headless)</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-zinc-500">API Webhook QRIS &amp; WhatsApp Terstandar</td>
                </tr>

                {/* 10. MEKANISME PEMBAYARAN */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">Mekanisme Pembayaran</td>
                  <td className="py-3 px-3 font-mono bg-cyan-500/5">
                    Spark: Rp 0 | Berbayar: 100% Sekali Bayar
                  </td>
                  <td className="py-3 px-4 font-mono">100% di Muka (Memotong DP 50%)</td>
                  <td className="py-3 px-4 font-mono text-emerald-600 dark:text-emerald-400 font-bold">DP 50% + Pelunasan UAT 50%</td>
                  <td className="py-3 px-4 font-mono">DP 50% + Pelunasan UAT 50%</td>
                </tr>

                {/* 11. HAK MILIK SOURCE CODE */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">Hak Milik Dokumen / Kode</td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold bg-cyan-500/5">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>100% Milik Klien</span>
                    </span>
                  </td>
                  <td className="py-3 px-4 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>100% Klien (NDA)</span>
                    </span>
                  </td>
                  <td className="py-3 px-4 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>100% Klien (No Lock-in)</span>
                    </span>
                  </td>
                  <td className="py-3 px-4 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>100% Klien</span>
                    </span>
                  </td>
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
              className="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-400 text-black py-3 px-6 font-mono text-xs font-black uppercase tracking-wider transition rounded-none flex items-center justify-center gap-2 cursor-pointer"
            >
              <Clock className="w-3.5 h-3.5" />
              <span>{isEn ? 'RESERVE SCOPING SESSION' : 'RESERVASI JADWAL SCOPING'}</span>
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

      {/* 6. INTERACTIVE SPRINT CAPACITY & DIRECT SELECTION MODAL (ANTI-COLLISION TIME MANAGEMENT) */}
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
              className="relative w-full max-w-xl bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-emerald-500 shadow-2xl p-6 sm:p-8 z-10 font-sans my-8"
            >
              <button
                onClick={() => setIsModalOpen(false)}
                className="absolute top-4 right-4 p-1.5 text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition cursor-pointer"
              >
                <X className="w-5 h-5" />
              </button>

              {/* Dynamic Header Based on Selected Package */}
              {/* Dynamic Header Based on Selected Package */}
              <div className="mb-6">
                {selectedPackage === 'full_mvp' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                      <Clock className="w-3 h-3" />
                      <span>{isEn ? 'NERIAH PRO STUDIO // MAX 2 PROJECTS / CYCLE' : 'NERIAH PRO STUDIO // MAKSIMAL 2 PROYEK / SIKLUS'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      {isEn ? 'RESERVE SPRINT BATCH & CONTRACT' : 'RESERVASI BATCH SPRINT & KONTRAK MVP'}
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn 
                        ? 'End-to-end production software engineering executed 100% by Neriah Pro architects and engineers with digital legal contract.'
                        : 'Pengembangan penuh aplikasi web skala jutaan pengguna yang dikerjakan 100% oleh tim Neriah Pro dengan kontrak hukum digital dan DP 50% terproteksi.'}
                    </p>
                  </>
                ) : selectedPackage === 'umkm_starter' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                      <Sparkles className="w-3 h-3" />
                      <span>{isEn ? 'NERIAH PRO STUDIO // LOCAL BUSINESS SUBSIDY' : 'NERIAH PRO STUDIO // PROGRAM SUBSIDI UMKM 50%'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      {isEn ? 'CLAIM 50% SUBSIDY QUOTA' : 'KLAIM KUOTA SUBSIDI 50% (RP 3.750.000)'}
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn 
                        ? 'Digital transformation system built directly by Neriah Pro. Turnkey ready with Indonesian admin dashboard and payment integration.'
                        : 'Aplikasi web transaksional yang dibangun langsung oleh tim Neriah Pro sampai live. Termasuk admin dashboard bahasa Indonesia dan payment gateway QRIS.'}
                    </p>
                  </>
                ) : selectedPackage === 'blueprint_advisory' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                      <FileText className="w-3 h-3" />
                      <span>{isEn ? 'NERIAH PRO STUDIO // ARCHITECTURE ADVISORY' : 'NERIAH PRO STUDIO // JASA ADVISORY BLUEPRINT'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      {isEn ? 'ORDER ADVISORY BLUEPRINT (RP 2.500.000)' : 'PESAN JASA ADVISORY BLUEPRINT (RP 2.5 JT)'}
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn 
                        ? 'Receive full 26-parameter PRD, PostgreSQL Strict ULID ERD, and 5-sprint WBS with personal discovery call. Biaya Rp 2.5jt memotong DP 50% if upgrading to Full MVP.'
                        : 'Dapatkan cetak biru teknis lengkap (PRD 26 parameter, skema DDL PostgreSQL Strict ULID, dan WBS 5 Sprint) dengan pendampingan scoping Lead Architect.'}
                    </p>
                  </>
                ) : selectedPackage === 'retail_ultimate' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                      <Crown className="w-3 h-3" />
                      <span>100% SELF-SERVICE // + 1-ON-1 ARCHITECT CALL</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      Ultimate Advisory PRD (Rp {pricingSettings.retail_ultimate_price || '1.490.000'})
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn 
                        ? 'Self-service enterprise PRD with AI Failover Token Shield + 1 scheduled 60-min Google Meet architecture session. Koding tetap dilakukan mandiri oleh tim Anda.'
                        : 'Paket PRD enterprise mandiri dengan AI Failover Shield + 1 sesi Google Meet 60 menit bersama Lead Architect. Pengerjaan koding tetap dieksekusi oleh tim developer Anda sendiri.'}
                    </p>
                  </>
                ) : selectedPackage === 'retail_pro' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                      <Sparkles className="w-3 h-3" />
                      <span>100% SELF-SERVICE // WAJIB LOGIN AKUN</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      Pro Production PRD (Rp {pricingSettings.retail_pro_price || '399.000'})
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn 
                        ? 'Production blueprint, 6 Mermaid diagrams, Decoupled 2026+ matrix & WBS 5 Sprints. 100% self-service for your team to build.'
                        : 'Cetak biru produksi, matriks Decoupled 2026+, 6 diagram Mermaid, WBS 5 sprint, dan OpenAPI 3.1. Digunakan mandiri oleh tim developer Anda (Neriah Pro tidak coding).'}
                    </p>
                  </>
                ) : selectedPackage === 'retail_lite' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                      <Terminal className="w-3 h-3" />
                      <span>100% SELF-SERVICE // WAJIB LOGIN AKUN</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      Lite PRD Generator (Rp {pricingSettings.retail_lite_price || '99.000'})
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn 
                        ? 'Essential 26-parameter PRD + PostgreSQL Strict ULID DDL SQL. 100% self-service for your team to build.'
                        : 'Spesifikasi PRD 26 parameter esensial + skema SQL DDL PostgreSQL Strict ULID siap eksekusi. Digunakan mandiri oleh tim developer Anda.'}
                    </p>
                  </>
                ) : (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                      <Terminal className="w-3 h-3" />
                      <span>100% SELF-SERVICE // GUEST ACCESS (NO LOGIN)</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      Spark Free Idea Audit (Rp 0)
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn 
                        ? 'Quick sanity check and 5 essential MVP features in Guest Mode. 2 free audits per month, auto-reset on the 1st of every month.'
                        : 'Audit cepat kelayakan ide dan pemetaan 5 fitur MVP. Kuota gratis 2x per bulan, otomatis di-reset kembali setiap tanggal 1 awal bulan baru.'}
                    </p>
                  </>
                )}
              </div>

              {submitSuccess ? (
                <div className="p-6 bg-emerald-500/10 border border-emerald-500/30 text-center space-y-3">
                  <div className="w-12 h-12 bg-emerald-500 text-black flex items-center justify-center mx-auto">
                    <Check className="w-6 h-6" />
                  </div>
                  <h4 className="font-bold text-sm text-zinc-900 dark:text-white uppercase font-mono">
                    {isEn ? 'RESERVATION RECORDED SUCCESSFULLY!' : 'RESERVASI JADWAL BERHASIL TERCATAT!'}
                  </h4>
                  <p className="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed font-sans">
                    {isEn 
                      ? 'Your slot and timeline requirements have been logged into our CRM. WhatsApp coordination is opening automatically.'
                      : 'Slot jadwal dan spesifikasi kebutuhan Anda telah tercatat rapi di CRM Neriah Pro. Obrolan WhatsApp resmi dengan Lead Architect sedang dibuka otomatis.'}
                  </p>
                  <button
                    onClick={() => setIsModalOpen(false)}
                    className="mt-4 px-6 py-2.5 bg-emerald-500 text-black font-mono text-xs font-black uppercase tracking-wider cursor-pointer"
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

                  {/* Pilihan Paket Switcher */}
                  <div>
                    <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                      Paket Layanan Terpilih *
                    </label>
                    <select
                      value={selectedPackage}
                      onChange={(e) => setSelectedPackage(e.target.value)}
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden font-sans"
                      required
                    >
                      <optgroup label="INSTANT ARCHITECTURAL BLUEPRINT (100% SELF-SERVICE // ZERO NERIAH PRO CODING)">
                        <option value="retail_spark">Spark Free Audit (Rp 0 - Guest Mode / Reset Tiap Bulan)</option>
                        <option value="retail_lite">Lite PRD Generator (Rp {pricingSettings.retail_lite_price || '99.000'} - Wajib Login)</option>
                        <option value="retail_pro">Pro Production PRD &amp; WBS (Rp {pricingSettings.retail_pro_price || '399.000'} - Wajib Login)</option>
                        <option value="retail_ultimate">Ultimate Advisory + 1-on-1 Call (Rp {pricingSettings.retail_ultimate_price || '1.490.000'} - Akun Terverifikasi)</option>
                      </optgroup>
                      <optgroup label="NERIAH PRO CUSTOM ENGINEERING STUDIO (DIKERJAKAN LANGSUNG OLEH NERIAH PRO)">
                        <option value="full_mvp">Enterprise Rapid Monolith MVP (5 Sprint - DP 50% Rp 25.000.000)</option>
                        <option value="umkm_starter">UMKM Digital Starter (Program Subsidi 50% - Rp 3.750.000)</option>
                        <option value="blueprint_advisory">Blueprint &amp; PRD Architecture Advisory (Rp 2.500.000)</option>
                      </optgroup>
                    </select>
                  </div>

                  {/* 1. KHUSUS FULL MVP: PILIHAN BATCH WAKTU & ANTI-TABRAKAN */}
                  {selectedPackage === 'full_mvp' && (
                    <div className="space-y-3 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                      <div>
                        <div className="flex items-center justify-between mb-1.5">
                          <label className="text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase">
                            1. Pilih Batch Waktu Sprint (Kapasitas Terkelola) *
                          </label>
                          <span className="text-[10px] font-mono text-emerald-500 font-bold">ANTI-COLLISION</span>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-2">
                          {[
                            { id: 'Batch 1 (15 Okt - 25 Nov 2026)', label: 'Batch 1', dates: '15 Okt - 25 Nov', slot: 'SISA 1 SLOT', highlight: true },
                            { id: 'Batch 2 (01 Des 2026 - 15 Jan 2027)', label: 'Batch 2', dates: '01 Des - 15 Jan', slot: 'TERSEDIA 2 SLOT', highlight: false },
                            { id: 'Batch Q1 2027 (Mulai Feb 2027)', label: 'Batch Q1 2027', dates: 'Mulai Feb 2027', slot: 'RESERVASI AWAL', highlight: false },
                          ].map((b) => (
                            <button
                              key={b.id}
                              type="button"
                              onClick={() => setSprintBatch(b.id)}
                              className={`p-2.5 text-left border rounded-none transition font-sans cursor-pointer ${
                                sprintBatch === b.id
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
                          2. Kesiapan Dokumen Blueprint PRD (Prasyarat Scope-Lock) *
                        </label>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                          <button
                            type="button"
                            onClick={() => setHasBlueprint('no')}
                            className={`p-2.5 text-left border rounded-none transition cursor-pointer ${
                              hasBlueprint === 'no'
                                ? 'border-emerald-500 bg-emerald-500/10 text-zinc-900 dark:text-white'
                                : 'border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-400'
                            }`}
                          >
                            <span className="text-xs font-bold block">Belum Ada Blueprint</span>
                            <span className="text-[10px] text-zinc-500 block mt-0.5">Wajib diawali PRD (Biaya Rp 2.5jt memotong DP 50%)</span>
                          </button>
                          <button
                            type="button"
                            onClick={() => setHasBlueprint('ready')}
                            className={`p-2.5 text-left border rounded-none transition cursor-pointer ${
                              hasBlueprint === 'ready'
                                ? 'border-emerald-500 bg-emerald-500/10 text-zinc-900 dark:text-white'
                                : 'border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-400'
                            }`}
                          >
                            <span className="text-xs font-bold block">Sudah Ada Dokumen PRD</span>
                            <span className="text-[10px] text-zinc-500 block mt-0.5">Langsung review kontrak & lock slot DP 50%</span>
                          </button>
                        </div>
                        {hasBlueprint === 'ready' && (
                          <div className="mt-2">
                            <input
                              type="text"
                              value={blueprintSlug}
                              onChange={(e) => setBlueprintSlug(e.target.value)}
                              placeholder="Masukkan Slug Blueprint / ID Dokumen (Contoh: prd-nama-proyek)"
                              className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden font-mono"
                            />
                          </div>
                        )}
                      </div>

                      {/* Slot Waktu Kickoff Sync */}
                      <div>
                        <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1.5">
                          3. Pilihan Waktu Kickoff Sync dengan Lead Architect (15-30 Menit) *
                        </label>
                        <div className="grid grid-cols-3 gap-2">
                          {[
                            { id: 'Pagi (09:30 - 10:30 WIB)', label: 'Pagi', time: '09:30 WIB' },
                            { id: 'Siang (13:30 - 14:30 WIB)', label: 'Siang', time: '13:30 WIB' },
                            { id: 'Sore (16:00 - 17:00 WIB)', label: 'Sore', time: '16:00 WIB' },
                          ].map((s) => (
                            <button
                              key={s.id}
                              type="button"
                              onClick={() => setKickoffSlot(s.id)}
                              className={`p-2 text-center border rounded-none transition cursor-pointer font-mono text-[10px] font-bold ${
                                kickoffSlot === s.id
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
                          Kategori Bidang Usaha Anda *
                        </label>
                        <div className="grid grid-cols-2 gap-2">
                          {[
                            'Toko Retail & Grosir',
                            'Kuliner / Cafe & Resto (F&B)',
                            'Jasa Profesional & Servis',
                            'Yayasan & Komunitas Sosial',
                          ].map((cat) => (
                            <button
                              key={cat}
                              type="button"
                              onClick={() => setUmkmCategory(cat)}
                              className={`p-2.5 text-left border rounded-none transition cursor-pointer font-sans text-xs ${
                                umkmCategory === cat
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
                        <span className="font-bold text-amber-600 dark:text-amber-400 block mb-0.5">SKEMA SUBSIDI DITERAPKAN OTOMATIS:</span>
                        <span>Investasi Normal Rp 7.500.000 dipotong 50% menjadi <strong>Rp 3.750.000</strong>. Skema pembayaran: DP 50% (Rp 1.875.000) saat mulai, pelunasan sisa 50% setelah live.</span>
                      </div>
                    </div>
                  )}

                  {/* 3. KHUSUS BLUEPRINT ADVISORY: DIRECT LINK CALLOUT */}
                  {selectedPackage === 'blueprint_advisory' && (
                    <div className="p-3 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-center space-y-2">
                      <span className="text-xs text-zinc-600 dark:text-zinc-300 block">
                        Ingin langsung mengisi 26 parameter kebutuhan teknis dan menerbitkan PRD sekarang?
                      </span>
                      <a
                        href="/blueprint?package=blueprint_advisory"
                        className="inline-flex items-center justify-center gap-2 px-4 py-2 bg-zinc-900 dark:bg-white text-white dark:text-black font-mono text-xs font-black uppercase tracking-wider transition rounded-none shadow-xs"
                      >
                        <span>BUKA GENERATOR PRD LANGSUNG &rarr;</span>
                      </a>
                    </div>
                  )}

                  {/* 4. KHUSUS PAKET SELF-SERVICE: DIRECT GENERATOR CALLOUT & SELF-SERVICE BADGE */}
                  {['retail_spark', 'retail_lite', 'retail_pro', 'retail_ultimate'].includes(selectedPackage) && (
                    <div className="p-3 bg-cyan-500/10 border border-cyan-500/30 text-xs font-mono text-zinc-800 dark:text-zinc-200 space-y-1.5">
                      <div className="flex items-center gap-1.5 font-bold text-cyan-600 dark:text-cyan-400">
                        <Terminal className="w-3.5 h-3.5 shrink-0" />
                        <span>100% SELF-SERVICE // ZERO NERIAH PRO CODING</span>
                      </div>
                      <p className="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                        {isEn 
                          ? 'This package is for you or your engineering team to build on your own. Submit this form for sales inquiry/corporate invoicing, or launch the generator immediately:' 
                          : 'Paket ini digunakan mandiri oleh tim/developer Anda. Isi formulir jika ingin penawaran resmi/faktur korporasi, atau klik tombol di bawah untuk langsung membuka generator:'}
                      </p>
                      <a
                        href={selectedPackage === 'retail_spark' ? '/blueprint?tier=spark' : `/blueprint?tier=${selectedPackage.replace('retail_', '')}`}
                        className="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white font-mono text-[11px] font-bold uppercase tracking-wider transition rounded-none mt-1"
                      >
                        <span>{isEn ? 'OPEN BLUEPRINT GENERATOR NOW' : 'BUKA GENERATOR BLUEPRINT SEKARANG'} &rarr;</span>
                      </a>
                    </div>
                  )}

                  {/* FORM IDENTITAS INTI */}
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                    <div>
                      <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                        Nama Lengkap / PIC *
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

                    <div>
                      <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                        Perusahaan / Bisnis *
                      </label>
                      <input
                        type="text"
                        name="company"
                        value={formData.company}
                        onChange={handleInputChange}
                        placeholder="Contoh: PT Inovasi Maju"
                        required
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden"
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
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

                  {/* Kode Voucher (Private / Partner Referral) */}
                  <div>
                    <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                      Kode Promo / Voucher Partner (Opsional)
                    </label>
                    <input
                      type="text"
                      name="voucher_code"
                      value={formData.voucher_code}
                      onChange={(e) => setFormData(prev => ({ ...prev, voucher_code: e.target.value.toUpperCase() }))}
                      placeholder="Masukkan jika memiliki kode partner khusus"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden font-mono"
                    />
                  </div>

                  {/* Submit CTA */}
                  <button
                    type="submit"
                    disabled={isSubmitting}
                    className="w-full bg-emerald-500 hover:bg-emerald-400 text-black py-3.5 px-4 font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition rounded-none shadow-md disabled:opacity-60 cursor-pointer"
                  >
                    {isSubmitting ? (
                      <>
                        <Loader2 className="w-4 h-4 animate-spin" />
                        <span>{isEn ? 'SECURING YOUR TIME SLOT...' : 'MENGUNCI SLOT JADWAL PROYEK...'}</span>
                      </>
                    ) : selectedPackage === 'full_mvp' ? (
                      <>
                        <Clock className="w-4 h-4" />
                        <span>{isEn ? 'LOCK SPRINT BATCH & REVIEW CONTRACT (DP 50%)' : 'KUNCI SLOT BATCH & LANJUTKAN KONTRAK DP 50%'}</span>
                      </>
                    ) : selectedPackage === 'umkm_starter' ? (
                      <>
                        <Check className="w-4 h-4" />
                        <span>{isEn ? 'CLAIM 50% SUBSIDY & ACTIVATE ONBOARDING' : 'KLAIM SUBSIDI 50% & KONSULTASI ONBOARDING'}</span>
                      </>
                    ) : (
                      <>
                        <Send className="w-4 h-4" />
                        <span>{isEn ? 'SUBMIT ADVISORY INQUIRY' : 'KIRIM INQUIRY ARSITEKTUR BLUEPRINT'}</span>
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
