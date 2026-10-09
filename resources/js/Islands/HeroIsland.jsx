import React, { useState, useRef } from 'react';
import { 
  ArrowRight, 
  Sparkles, 
  Terminal,
  Paperclip,
  FileText,
  Image as ImageIcon,
  X,
  Loader2,
  ShieldCheck,
  CheckCircle2,
  AlertCircle,
  ShoppingBag,
  Rocket,
  Layers,
  Database,
  Cpu,
  Check,
  Code2,
  ArrowDown,
  Building2,
  Clock,
  ExternalLink
} from 'lucide-react';

export default function HeroIsland({ 
  headline, 
  subheadline, 
  cta_text, 
  cta_link, 
  featureFlags, 
  currentLocale,
  pricingSettings,
  authUser,
  whatsappNumber
}) {
  const isMidtransStrict = Boolean(featureFlags?.midtrans_mode);
  const isCvProEnabled = !isMidtransStrict && (featureFlags?.enable_cv_pro !== false);
  const isBlueprintEnabled = featureFlags?.enable_vision_blueprint !== false;

  const isEn = currentLocale === 'en' || (typeof window !== 'undefined' && (document.documentElement.lang?.startsWith('en') || document.cookie.includes('neriah_locale=en')));

  // Pricing constants from settings
  const retailLitePrice = pricingSettings?.retail_lite_price || '99.000';
  const retailProPrice = pricingSettings?.retail_pro_price || '399.000';
  const retailUltimatePrice = pricingSettings?.retail_ultimate_price || '1.490.000';
  const umkmPrice = pricingSettings?.umkm_price || '7.500.000';
  const mvpPrice = pricingSettings?.mvp_price || '50.000.000';

  // State for the Unified Idea & MarkItDown Workspace
  const [ideaText, setIdeaText] = useState('');
  const [attachedFiles, setAttachedFiles] = useState([]);
  const [honeypot, setHoneypot] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [loadingStep, setLoadingStep] = useState('');
  const [errorMessage, setErrorMessage] = useState(null);

  // State for Dual-Track Recommendation Bridge Modal
  const [synthesizedResult, setSynthesizedResult] = useState(null);
  const [isRecommendationModalOpen, setIsRecommendationModalOpen] = useState(false);

  const fileInputRef = useRef(null);

  const scrollTo = (id) => {
    const el = document.getElementById(id);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth' });
    }
  };

  const handleFileChange = (e) => {
    if (!e.target.files) return;
    const newFiles = Array.from(e.target.files);
    
    // Check total limit (max 5 files)
    if (attachedFiles.length + newFiles.length > 5) {
      setErrorMessage(isEn ? 'Maximum 5 files can be attached.' : 'Maksimal 5 berkas dapat dilampirkan.');
      return;
    }

    // Filter file sizes (max 15MB each)
    const validFiles = [];
    for (const f of newFiles) {
      if (f.size > 15 * 1024 * 1024) {
        setErrorMessage(isEn ? `File ${f.name} exceeds 15MB limit.` : `Berkas ${f.name} melebihi batas 15MB.`);
        return;
      }
      validFiles.push(f);
    }

    setAttachedFiles(prev => [...prev, ...validFiles]);
    setErrorMessage(null);
    e.target.value = '';
  };

  const removeFile = (index) => {
    setAttachedFiles(prev => prev.filter((_, i) => i !== index));
  };

  const getTrackUrl = (track) => {
    const base = synthesizedResult?.redirect_url || '/blueprint';
    const separator = base.includes('?') ? '&' : '?';
    return `${base}${separator}track=${track}`;
  };

  const handleSubmitIdea = async (e) => {
    e.preventDefault();
    setErrorMessage(null);

    // Anti-Spam Check: Honeypot
    if (honeypot.trim() !== '') {
      window.location.href = '/blueprint';
      return;
    }

    // Minimum check: at least 15 chars or attached file
    const cleanText = ideaText.trim();
    if (cleanText.length < 15 && attachedFiles.length === 0) {
      setErrorMessage(isEn 
        ? 'Please write at least 15 characters of your project idea, or attach a specification document.' 
        : 'Mohon ceritakan ide proyek Anda minimal 15 karakter, atau lampirkan berkas dokumen pendukung.');
      return;
    }

    const hasOnlyMdOrTxt = attachedFiles.length > 0 && attachedFiles.every(f => {
      const ext = f.name.split('.').pop().toLowerCase();
      return ['md', 'markdown', 'txt'].includes(ext);
    });

    setIsSubmitting(true);
    setLoadingStep(attachedFiles.length > 0 
      ? (hasOnlyMdOrTxt
          ? (isEn ? 'Reading specification files & mapping architecture...' : 'Membaca berkas spesifikasi & memetakan arsitektur...')
          : (isEn ? 'Converting documents via MarkItDown...' : 'Mengonversi dokumen via MarkItDown...'))
      : (isEn ? 'Analyzing project architecture...' : 'Menganalisis arsitektur proyek...'));

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const formData = new FormData();
      formData.append('idea_text', cleanText);
      formData.append('_hp_check', honeypot);
      formData.append('locale', isEn ? 'en' : 'id');

      attachedFiles.forEach(file => {
        formData.append('files[]', file);
      });

      const timer = setTimeout(() => {
        setLoadingStep(isEn ? 'Synthesizing RBAC, ERD & Milestones...' : 'Menyusun RBAC, Skema ERD & Milestone...');
      }, 1200);

      const response = await fetch('/api/blueprint/analyze-idea', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: formData
      });

      clearTimeout(timer);

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw new Error(result.message || (isEn ? 'Failed to process idea.' : 'Gagal memproses ide.'));
      }

      setIsSubmitting(false);
      setLoadingStep('');

      // Open the Dual-Track Architectural Recommendation Bridge Modal if recommendation payload exists
      if (result.track_recommendation) {
        setSynthesizedResult(result);
        setIsRecommendationModalOpen(true);
      } else {
        setLoadingStep(isEn ? 'Redirecting to Blueprint Workspace...' : 'Mengarahkan ke Ruang Penyesuaian Blueprint...');
        window.location.href = result.redirect_url || '/blueprint';
      }

    } catch (err) {
      setIsSubmitting(false);
      setLoadingStep('');
      setErrorMessage(err.message || (isEn ? 'An error occurred. Please try again.' : 'Terjadi kendala. Silakan coba lagi.'));
    }
  };

  return (
    <section className="relative w-full pt-28 pb-16 px-4 sm:px-6 bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 border-b border-zinc-200 dark:border-zinc-800 transition-colors font-sans overflow-hidden">
      
      {/* Background Subtle Tech Grid */}
      <div className="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none" />

      <div className="max-w-6xl mx-auto relative z-10">
        
        {/* ========================================================================= */}
        {/* 1. MASTER HERO: AUTHORITATIVE VALUE PROPOSITION                           */}
        {/* ========================================================================= */}
        <div className="max-w-4xl">
          {/* Status Pill */}
          <div className="inline-flex items-center gap-2 px-3 py-1 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase tracking-widest mb-5 rounded-none">
            <span className="w-2 h-2 bg-emerald-500 rounded-none animate-pulse"></span>
            <span>NERIAH PRO // DUAL-TRACK SOFTWARE ENGINEERING &amp; ARCHITECTURE PLATFORM</span>
          </div>

          {/* Master Headline */}
          <h1 className="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold uppercase tracking-tight leading-[1.08] mb-5 text-zinc-900 dark:text-zinc-50 font-sans">
            {headline || (isEn 
              ? 'ENTERPRISE SOFTWARE ARCHITECTURE & ENGINEERING PLATFORM.'
              : 'PUSAT ARSITEKTUR & REKAYASA SISTEM DIGITAL KELAS ENTERPRISE.')}
          </h1>

          {/* Subheadline: Dual-Track Clarification */}
          <p className="text-sm sm:text-base md:text-lg text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-8 max-w-3xl">
            {subheadline || (isEn
              ? <>Two modern engineering paths: Acquire instant self-service software factory licenses (<strong>Retail</strong>) to deploy with your own team, or build mission-critical systems with our dedicated software engineering studio (<strong>Project Studio</strong>).</>
              : <>Dua jalur solusi rekayasa modern untuk bisnis dan founder: Beli lisensi arsitektur siap pakai (<strong>Retail</strong>) untuk di-deploy mandiri, atau bangun sistem skala besar bersama tim dedicated engineer kami (<strong>Project Studio</strong>).</>)}
          </p>

          {/* Dual Action Primary CTA Buttons */}
          <div className="flex flex-wrap items-center gap-4 mb-8 font-mono text-xs uppercase tracking-wider font-bold">
            <a
              href="/pricing"
              className="bg-emerald-600 hover:bg-emerald-500 text-black font-black py-3.5 px-6 rounded-none transition flex items-center gap-2 cursor-pointer shadow-xs"
            >
              <ShoppingBag className="w-4 h-4 text-black" />
              <span>{isEn ? `Explore Pricing & Packages →` : `Jelajahi Paket & Harga Investasi →`}</span>
            </a>

            <button
              type="button"
              onClick={() => scrollTo('interactive-discovery')}
              className="border-2 border-zinc-900 dark:border-zinc-200 hover:bg-zinc-900 hover:text-white dark:hover:bg-zinc-100 dark:hover:text-black text-zinc-900 dark:text-zinc-100 py-3.5 px-6 rounded-none transition flex items-center gap-2 cursor-pointer"
            >
              <Rocket className="w-4 h-4 text-emerald-500" />
              <span>{isEn ? 'Dedicated Studio Consultation ↓' : 'Konsultasi Proyek Studio Dedicated ↓'}</span>
            </button>

            <a
              href="/blueprint"
              className="text-zinc-600 dark:text-zinc-400 hover:text-emerald-500 py-3.5 px-3 transition flex items-center gap-1.5 underline decoration-zinc-400 dark:decoration-zinc-600 underline-offset-4"
            >
              <span>{isEn ? 'Blueprint Workspace ↗' : 'Ruang Blueprint ↗'}</span>
            </a>
          </div>

          {/* Quick Trust Pillars Strip */}
          <div className="flex flex-wrap items-center gap-2 sm:gap-4 text-[11px] font-mono text-zinc-500 dark:text-zinc-400 pb-10">
            <span className="inline-flex items-center gap-1">
              <Check className="w-3.5 h-3.5 text-emerald-500" /> Strict ULID PostgreSQL
            </span>
            <span className="text-zinc-300 dark:text-zinc-700 hidden sm:inline">&bull;</span>
            <span className="inline-flex items-center gap-1">
              <Check className="w-3.5 h-3.5 text-emerald-500" /> Clean Solid Monolith
            </span>
            <span className="text-zinc-300 dark:text-zinc-700 hidden sm:inline">&bull;</span>
            <span className="inline-flex items-center gap-1">
              <Check className="w-3.5 h-3.5 text-emerald-500" /> Microsoft MarkItDown Engine
            </span>
            <span className="text-zinc-300 dark:text-zinc-700 hidden sm:inline">&bull;</span>
            <span className="inline-flex items-center gap-1">
              <Check className="w-3.5 h-3.5 text-emerald-500" /> Garansi SLA &amp; Escrow Kontrak
            </span>
          </div>
        </div>

        {/* ========================================================================= */}
        {/* 2. SECTION: DUAL-TRACK COMPARISON MATRIX (SIDE-BY-SIDE SPLIT CARDS)       */}
        {/* ========================================================================= */}
        <div id="dual-track-matrix" className="pt-12 pb-14 border-t border-zinc-200 dark:border-zinc-800 scroll-mt-20">
          
          <div className="mb-8">
            <div className="flex items-center gap-2 font-mono text-xs uppercase tracking-wider text-emerald-600 dark:text-emerald-400 font-bold mb-2">
              <Layers className="w-4 h-4 text-emerald-500" />
              <span>{isEn ? 'TWO ENGINEERING PATHS // CHOOSE ACCORDING TO YOUR NEEDS' : 'DUA JALUR REKAYASA SISTEM // PILIH SESUAI KEBUTUHAN ANDA'}</span>
            </div>
            <h2 className="text-2xl sm:text-3xl font-extrabold uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {isEn ? 'Self-Service Licenses vs Turnkey Engineering Studio.' : 'Lisensi Mandiri Instan vs Studio Rekayasa Turnkey.'}
            </h2>
            <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 max-w-3xl mt-1.5 leading-relaxed">
              {isEn
                ? 'Select the path that matches your timeline, technical capability, and budget. Transparent deliverables with zero hidden lock-in.'
                : 'Pilih jalur yang paling sesuai dengan kapasitas teknis, timeline, dan anggaran bisnis Anda. Transparan tanpa biaya tersembunyi.'}
            </p>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
            
            {/* ---------------- CARD 1: RETAIL SOFTWARE FACTORY OS ---------------- */}
            <div className="bg-white dark:bg-zinc-900 border-2 border-emerald-500/80 dark:border-emerald-500/70 p-6 sm:p-8 flex flex-col justify-between rounded-none shadow-xs relative">
              <div className="absolute top-0 right-0 bg-emerald-500 text-black font-mono text-[10px] font-black uppercase px-3 py-1 tracking-wider">
                {isEn ? 'SELF-SERVICE OS' : 'LISENSI DIGITAL'}
              </div>

              <div>
                <div className="font-mono text-xs font-bold uppercase text-emerald-600 dark:text-emerald-400 tracking-wider mb-1">
                  {isEn ? 'PATH 01 // DIGITAL RETAIL LICENSE' : 'JALUR 01 // LISENSI DIGITAL RETAIL'}
                </div>
                <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2">
                  Software Factory OS
                </h3>
                <p className="text-xs text-zinc-600 dark:text-zinc-400 mb-5 leading-relaxed">
                  {isEn 
                    ? 'Targeted for Software Engineers, Solo Founders, CTOs, & Independent Teams who have coding capacity and want battle-tested enterprise architecture instantly.'
                    : 'Untuk Developer, Solo Founder, CTO, & Tim Teknis Mandiri yang punya kapasitas koding sendiri dan butuh arsitektur kelas enterprise tanpa bikin dari nol.'}
                </p>

                {/* Price Display */}
                <div className="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 mb-6 rounded-none">
                  <div className="text-[10px] font-mono text-zinc-500 uppercase tracking-wider mb-0.5">
                    {isEn ? 'INVESTMENT' : 'BIAYA INVESTASI'}
                  </div>
                  <div className="flex items-baseline gap-2">
                    <span className="text-2xl sm:text-3xl font-black font-mono text-zinc-900 dark:text-white">
                      Rp {retailLitePrice}
                    </span>
                    <span className="text-xs font-mono text-zinc-500">
                      {isEn ? `up to Rp ${retailUltimatePrice} (One-Time)` : `s/d Rp ${retailUltimatePrice} (Sekali Bayar)`}
                    </span>
                  </div>
                  <div className="text-[11px] font-mono text-emerald-600 dark:text-emerald-400 mt-1">
                    {isEn ? '✓ Lifetime access & permanent downloads in Client Portal' : '✓ Akses seumur hidup & unduh permanen di Portal Klien'}
                  </div>
                </div>

                {/* Feature List */}
                <div className="space-y-3 mb-8 text-xs font-sans text-zinc-700 dark:text-zinc-300">
                  <div className="flex items-start gap-2.5">
                    <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                    <span><strong>Product Requirements Document (PRD)</strong> lengkap 20+ halaman berstandar Fortune 500 (User Stories, Non-Functional Req, RBAC).</span>
                  </div>
                  <div className="flex items-start gap-2.5">
                    <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                    <span><strong>PostgreSQL Strict ULID DDL</strong> skema basis data terdistribusi tahan jutaan transaksi (Zero conflict, O(1) performance).</span>
                  </div>
                  <div className="flex items-start gap-2.5">
                    <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                    <span><strong>Scaffold Starter Repository ZIP</strong> siap koding (Laravel 13, Filament v5, Inertia/Livewire 4).</span>
                  </div>
                  <div className="flex items-start gap-2.5">
                    <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                    <span><strong>AI Coding Directives (.cursorrules)</strong> panduan prompt anti-halusinasi untuk Cursor &amp; Claude Code.</span>
                  </div>
                  <div className="flex items-start gap-2.5">
                    <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                    <span><strong>100% Instant Delivery:</strong> Bayar via QRIS/Midtrans, langsung unduh di Portal Klien dalam hitungan menit.</span>
                  </div>
                </div>
              </div>

              <div>
                <a
                  href="/pricing#retail"
                  className="w-full bg-emerald-600 hover:bg-emerald-500 text-black font-mono font-black text-xs uppercase py-3.5 px-5 rounded-none transition flex items-center justify-center gap-2 cursor-pointer shadow-xs"
                >
                  <ShoppingBag className="w-4 h-4 text-black" />
                  <span>{isEn ? 'View Retail Packages (Spark, Lite, Pro, Ultimate) →' : 'Lihat Paket Lisensi Retail (Spark, Lite, Pro, Ultimate) →'}</span>
                </a>
              </div>
            </div>

            {/* ---------------- CARD 2: DEDICATED ENGINEERING STUDIO ---------------- */}
            <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-700 hover:border-zinc-900 dark:hover:border-zinc-500 p-6 sm:p-8 flex flex-col justify-between rounded-none shadow-xs relative transition">
              <div className="absolute top-0 right-0 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-black font-mono text-[10px] font-black uppercase px-3 py-1 tracking-wider">
                {isEn ? 'TURNKEY STUDIO' : 'DEDICATED STUDIO'}
              </div>

              <div>
                <div className="font-mono text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400 tracking-wider mb-1">
                  {isEn ? 'PATH 02 // FULL-SERVICE ENGINEERING STUDIO' : 'JALUR 02 // FULL-SERVICE DEDICATED STUDIO'}
                </div>
                <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2">
                  Dedicated Software Studio
                </h3>
                <p className="text-xs text-zinc-600 dark:text-zinc-400 mb-5 leading-relaxed">
                  {isEn 
                    ? 'Targeted for Corporations, Growing Businesses, & Non-Technical Founders who need end-to-end software built, tested, and deployed with SLA guarantees.'
                    : 'Untuk Perusahaan, Korporasi, & Pemilik Bisnis yang butuh aplikasi jadi bergaransi tanpa repot merekrut, mengelola, dan menggaji tim software engineer.'}
                </p>

                {/* Price Display */}
                <div className="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 mb-6 rounded-none">
                  <div className="text-[10px] font-mono text-zinc-500 uppercase tracking-wider mb-0.5">
                    {isEn ? 'INVESTMENT' : 'BIAYA INVESTASI'}
                  </div>
                  <div className="flex items-baseline gap-2">
                    <span className="text-2xl sm:text-3xl font-black font-mono text-zinc-900 dark:text-white">
                      Rp {umkmPrice}
                    </span>
                    <span className="text-xs font-mono text-zinc-500">
                      {isEn ? `(UMKM) / Rp ${mvpPrice}+ (Full MVP)` : `(UMKM) s/d Rp ${mvpPrice}+ (Full MVP)`}
                    </span>
                  </div>
                  <div className="text-[11px] font-mono text-blue-600 dark:text-blue-400 mt-1">
                    {isEn ? '✓ 50% Milestone Escrow & Legally-binding SLA Contract' : '✓ Pembayaran Bertahap DP 50% & Kontrak Digital Bersertifikat'}
                  </div>
                </div>

                {/* Feature List */}
                <div className="space-y-3 mb-8 text-xs font-sans text-zinc-700 dark:text-zinc-300">
                  <div className="flex items-start gap-2.5">
                    <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                    <span><strong>Dedicated Fullstack &amp; DevOps Engineering:</strong> Menulis 100% kode produksi mencakup seluruh 7 Pilar Software Factory OS.</span>
                  </div>
                  <div className="flex items-start gap-2.5">
                    <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                    <span><strong>Managed Capacity &amp; Anti-Collision:</strong> Slot terisolasi per batch (Batch 1, 2, Q1) dengan pelacakan Master Gantt di Customer Portal.</span>
                  </div>
                  <div className="flex items-start gap-2.5">
                    <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                    <span><strong>Skema Escrow Bertahap:</strong> DP 50% saat penguncian kontrak &amp; 50% pelunasan setelah lolos User Acceptance Test (UAT).</span>
                  </div>
                  <div className="flex items-start gap-2.5">
                    <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                    <span><strong>Kontrak Digital Legal Bersertifikat</strong> dengan jaminan SLA, NDA kerahasiaan, dan transfer hak cipta 100% (No Lock-in).</span>
                  </div>
                  <div className="flex items-start gap-2.5">
                    <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                    <span><strong>Full Deployment Siap Pakai</strong> ke Cloud Production VPS (Docker/Nixpacks, Nginx HTTP/2, SSL, Domain).</span>
                  </div>
                  <div className="flex items-start gap-2.5">
                    <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                    <span><strong>Garansi Bebas Bug &amp; Pemeliharaan</strong> berkala pasca-rilis dengan response time maksimal 2 jam kerja.</span>
                  </div>
                </div>
              </div>

              <div className="flex flex-col sm:flex-row gap-2.5">
                <button
                  type="button"
                  onClick={() => scrollTo('interactive-discovery')}
                  className="flex-1 bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black font-mono font-bold text-xs uppercase py-3.5 px-4 rounded-none transition flex items-center justify-center gap-2 cursor-pointer shadow-xs"
                >
                  <Rocket className="w-4 h-4 text-emerald-500" />
                  <span>{isEn ? 'Scope Studio Project ↓' : 'Rancang Scope Studio ↓'}</span>
                </button>
                <a
                  href="/pricing#studio"
                  className="border-2 border-zinc-900 dark:border-zinc-300 hover:bg-zinc-900 hover:text-white dark:hover:bg-zinc-100 dark:hover:text-black text-zinc-900 dark:text-zinc-100 font-mono font-bold text-xs uppercase py-3.5 px-4 rounded-none transition flex items-center justify-center gap-1.5"
                >
                  <span>{isEn ? 'Studio Tiers →' : 'Paket Studio →'}</span>
                </a>
              </div>
            </div>

          </div>
        </div>

        {/* ========================================================================= */}
        {/* 3. SECTION: INTERACTIVE AI DISCOVERY & MARKITDOWN SYNTHESIS               */}
        {/* ========================================================================= */}
        {isBlueprintEnabled && (
          <div id="interactive-discovery" className="pt-12 pb-14 border-t border-zinc-200 dark:border-zinc-800 scroll-mt-20">
            
            <div className="mb-6">
              <div className="flex items-center gap-2 font-mono text-xs uppercase tracking-wider text-emerald-600 dark:text-emerald-400 font-bold mb-2">
                <Sparkles className="w-4 h-4 text-emerald-500" />
                <span>{isEn ? 'AI ARCHITECTURAL DISCOVERY // INTERACTIVE BLUEPRINT SYNTHESIZER' : 'AI ARCHITECTURAL DISCOVERY // SINTESIS BLUEPRINT INTERAKTIF'}</span>
              </div>
              <h2 className="text-2xl sm:text-3xl font-extrabold uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
                {isEn ? 'Free Simulation: Map Your System Architecture in Seconds.' : 'Uji Coba Gratis: Petakan Kebutuhan Arsitektur Sistem Anda.'}
              </h2>
              <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 max-w-3xl mt-1 leading-relaxed">
                {isEn
                  ? 'Pour your application ideas or attach specification documents/wireframe sketches. Microsoft MarkItDown + AI will structure your architecture, estimate scope, and advise whether Retail License or Dedicated Studio is the optimal fit.'
                  : 'Curahkan ide aplikasi, proses operasional, atau lampirkan berkas dokumen spesifikasi/sketsa Anda (PDF, DOCX, XLSX, TXT, Wireframe). Microsoft MarkItDown + AI kami akan membedah arsitektur dan merekomendasikan apakah cukup dengan Lisensi Retail atau butuh Tim Studio Dedicated.'}
              </p>
            </div>

            <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 p-5 sm:p-7 rounded-none shadow-none max-w-5xl">
              
              <div className="flex flex-wrap items-center justify-between gap-2 mb-3">
                <span className="font-mono text-xs uppercase tracking-wider font-bold text-zinc-800 dark:text-zinc-200">
                  {isEn ? 'WORKSPACE: ENTER PROJECT SPECIFICATION' : 'RUANG KERJA: MASUKKAN SPESIFIKASI PROYEK'}
                </span>
                <span className="text-[11px] font-mono text-zinc-500 dark:text-zinc-400">
                  {isEn ? 'Powered by MarkItDown + Multi-Model AI' : 'Didukung MarkItDown + Multi-Model AI'}
                </span>
              </div>

              <form onSubmit={handleSubmitIdea} className="space-y-3">
                
                {/* Anti-Spam Honeypot Field */}
                <input
                  type="text"
                  name="_hp_check"
                  value={honeypot}
                  onChange={(e) => setHoneypot(e.target.value)}
                  tabIndex="-1"
                  autoComplete="off"
                  className="hidden"
                  aria-hidden="true"
                />

                {/* Single Comprehensive Idea Textarea */}
                <div className="relative">
                  <textarea
                    rows={4}
                    value={ideaText}
                    onChange={(e) => setIdeaText(e.target.value)}
                    disabled={isSubmitting}
                    placeholder={isEn 
                      ? 'Describe your application vision, business workflow, or operational pain points in your own words... (e.g. "We want an inter-island heavy equipment rental platform. Users rent by day, vendors verify fleet availability, with milestone payments, container GPS tracking, and automated WhatsApp invoices...")'
                      : 'Ceritakan ide aplikasi, alur kerja bisnis, atau masalah operasional Anda secara bebas di sini... (Misal: "Saya ingin membuat aplikasi sewa alat berat antar pulau. Pengguna bisa sewa per hari, vendor verifikasi armada, ada pembayaran bertahap, pelacakan GPS kontainer, dan invoice otomatis via WhatsApp...")'}
                    className="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-xs sm:text-sm font-sans rounded-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition resize-y min-h-[110px]"
                  />
                  
                  {/* Character counter */}
                  <div className="absolute bottom-2.5 right-3 text-[10px] font-mono text-zinc-400 select-none pointer-events-none">
                    {ideaText.length} {isEn ? 'chars' : 'karakter'}
                  </div>
                </div>

                {/* Attached Files List */}
                {attachedFiles.length > 0 && (
                  <div className="flex flex-wrap gap-2 pt-1 pb-1">
                    {attachedFiles.map((file, idx) => {
                      const isImg = file.type.startsWith('image/');
                      return (
                        <div 
                          key={idx} 
                          className="inline-flex items-center gap-2 px-2.5 py-1.5 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-xs font-mono text-zinc-800 dark:text-zinc-200 rounded-none"
                        >
                          {isImg ? (
                            <ImageIcon className="w-3.5 h-3.5 text-blue-500" />
                          ) : (
                            <FileText className="w-3.5 h-3.5 text-emerald-500" />
                          )}
                          <span className="max-w-[160px] sm:max-w-[240px] truncate">{file.name}</span>
                          <span className="text-[10px] text-zinc-400">({Math.round(file.size / 1024)} KB)</span>
                          <button
                            type="button"
                            onClick={() => removeFile(idx)}
                            disabled={isSubmitting}
                            className="hover:text-red-500 transition p-0.5 text-zinc-400"
                            title="Hapus berkas"
                          >
                            <X className="w-3.5 h-3.5" />
                          </button>
                        </div>
                      );
                    })}
                  </div>
                )}

                {/* Action Toolbar */}
                <div className="flex flex-wrap items-center justify-between gap-3 pt-1">
                  
                  {/* File Attachment Trigger */}
                  <div className="flex items-center gap-2">
                    <input
                      type="file"
                      ref={fileInputRef}
                      onChange={handleFileChange}
                      multiple
                      accept=".pdf,.doc,.docx,.txt,.md,.rtf,.csv,.tsv,.xlsx,.pptx,image/*"
                      className="hidden"
                    />
                    <button
                      type="button"
                      onClick={() => fileInputRef.current?.click()}
                      disabled={isSubmitting}
                      className="inline-flex items-center gap-1.5 px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono font-bold border border-zinc-300 dark:border-zinc-700 rounded-none transition cursor-pointer"
                    >
                      <Paperclip className="w-3.5 h-3.5 text-zinc-500" />
                      <span>{isEn ? 'Attach Docs / Wireframes' : 'Lampirkan Dokumen / Gambar'}</span>
                    </button>

                    <span className="hidden sm:inline-block text-[11px] text-zinc-500 dark:text-zinc-400 font-mono">
                      (PDF, DOCX, XLSX, TXT, Wireframe - Max 15MB)
                    </span>
                  </div>

                  {/* Primary Submit Button */}
                  <button
                    type="submit"
                    disabled={isSubmitting}
                    className="w-full sm:w-auto bg-zinc-900 hover:bg-black dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white dark:text-black font-mono text-xs font-black uppercase tracking-wider py-2.5 px-6 rounded-none transition flex items-center justify-center gap-2 disabled:opacity-50 cursor-pointer"
                  >
                    {isSubmitting ? (
                      <>
                        <Loader2 className="w-3.5 h-3.5 animate-spin" />
                        <span>{loadingStep || (isEn ? 'Processing...' : 'Memproses...')}</span>
                      </>
                    ) : (
                      <>
                        <Sparkles className="w-3.5 h-3.5" />
                        <span>{isEn ? 'Synthesize & Review Blueprint →' : 'Sintesis & Tinjau Blueprint →'}</span>
                      </>
                    )}
                  </button>
                </div>

                {/* Error Message Display */}
                {errorMessage && (
                  <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-xs font-mono flex items-center gap-2 rounded-none">
                    <AlertCircle className="w-4 h-4 shrink-0" />
                    <span>{errorMessage}</span>
                  </div>
                )}
              </form>

              <div className="text-[11px] text-zinc-500 dark:text-zinc-400 mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/60 font-mono">
                <span>
                  &bull; {isEn 
                    ? 'MarkItDown converts all documents to clean Markdown. AI synthesizes architecture without prompt drift.'
                    : 'MarkItDown mengonversi berkas dokumen ke Markdown. AI murni fokus pada sintesis arsitektur teruji.'}
                </span>
              </div>
            </div>

          </div>
        )}

        {/* ========================================================================= */}
        {/* 4. SECTION: TANGIBLE DELIVERABLES SHOWCASE (WHAT YOU GET)                 */}
        {/* ========================================================================= */}
        <div className="pt-12 pb-14 border-t border-zinc-200 dark:border-zinc-800">
          <div className="mb-8">
            <div className="flex items-center gap-2 font-mono text-xs uppercase tracking-wider text-emerald-600 dark:text-emerald-400 font-bold mb-2">
              <CheckCircle2 className="w-4 h-4 text-emerald-500" />
              <span>{isEn ? 'STANDARDIZED ARTIFACTS // WHAT YOU RECEIVE' : 'BUKTI FISIK HASIL REKAYASA // WHAT YOU RECEIVE'}</span>
            </div>
            <h2 className="text-2xl sm:text-3xl font-extrabold uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {isEn ? 'Concrete, Production-Grade Engineering Deliverables.' : 'Output Konkret Berstandar Industri Tinggi.'}
            </h2>
            <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 max-w-3xl mt-1 leading-relaxed">
              {isEn
                ? 'Whether you purchase a Retail Software Factory OS or commission a Dedicated Engineering Studio build, every artifact is structured to eliminate technical debt.'
                : 'Baik Anda membeli Lisensi Digital Retail maupun mempercayakan Proyek ke Studio Dedicated, seluruh artefak dirancang untuk meniadakan technical debt.'}
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div className="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs">
              <div className="w-8 h-8 bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center mb-3">
                <FileText className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              </div>
              <div className="font-mono text-[10px] text-zinc-400 uppercase tracking-wider mb-1">
                {isEn ? 'BLUEPRINT DOCUMENT' : 'DOKUMEN SPESIFIKASI'}
              </div>
              <h4 className="font-bold text-sm text-zinc-900 dark:text-zinc-100 mb-2">
                Fortune 500-Grade PRD
              </h4>
              <p className="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                {isEn 
                  ? 'Comprehensive 20+ page Product Requirements Document detailing user stories, edge cases, RBAC matrices, and non-functional scalability metrics.'
                  : 'Dokumen PRD 20+ halaman lengkap berisi user stories, edge cases, RBAC permission, dan metrik skalabilitas non-fungsional.'}
              </p>
            </div>

            <div className="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs">
              <div className="w-8 h-8 bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center mb-3">
                <Database className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              </div>
              <div className="font-mono text-[10px] text-zinc-400 uppercase tracking-wider mb-1">
                {isEn ? 'DATABASE ARCHITECTURE' : 'STRUKTUR BASIS DATA'}
              </div>
              <h4 className="font-bold text-sm text-zinc-900 dark:text-zinc-100 mb-2">
                PostgreSQL Strict ULID DDL
              </h4>
              <p className="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                {isEn 
                  ? 'Ready-to-execute SQL DDL schema with 26-char ULID primary keys, index strategies, and foreign keys capable of scaling to millions of records.'
                  : 'Skema SQL DDL siap jalankan dengan primary key ULID 26 karakter, strategi indeks O(1), tahan jutaan data tanpa konflik autoincrement.'}
              </p>
            </div>

            <div className="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs">
              <div className="w-8 h-8 bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center mb-3">
                <Code2 className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              </div>
              <div className="font-mono text-[10px] text-zinc-400 uppercase tracking-wider mb-1">
                {isEn ? 'SOURCE CODE REPO' : 'SOURCE CODE REPOSITORY'}
              </div>
              <h4 className="font-bold text-sm text-zinc-900 dark:text-zinc-100 mb-2">
                Scaffold Project Starter ZIP
              </h4>
              <p className="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                {isEn 
                  ? 'Pre-wired codebase with Laravel 13, Filament v5 admin, auth scaffolding, cursorrules, and zero bloated dependencies.'
                  : 'Repositori koding siap deploy dengan Laravel 13, Filament v5 admin, otentikasi siap pakai, aturan koding AI, dan zero boilerplate.'}
              </p>
            </div>

            <div className="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs">
              <div className="w-8 h-8 bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center mb-3">
                <ShieldCheck className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              </div>
              <div className="font-mono text-[10px] text-zinc-400 uppercase tracking-wider mb-1">
                {isEn ? 'CLIENT DASHBOARD' : 'DASHBOARD TERPADU'}
              </div>
              <h4 className="font-bold text-sm text-zinc-900 dark:text-zinc-100 mb-2">
                Client Portal &amp; Tax Invoices
              </h4>
              <p className="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                {isEn 
                  ? 'Centralized dashboard for permanent file downloads, official tax receipts (Faktur Pajak), and digital contract records.'
                  : 'Portal klien terpadu untuk unduhan berkas permanen, faktur pajak resmi, kwitansi pembayaran, serta arsip kontrak digital.'}
              </p>
            </div>

          </div>
        </div>

        {/* ========================================================================= */}
        {/* 5. ARCHITECTURE PRECISION TRUST METRICS (BOTTOM BAR)                      */}
        {/* ========================================================================= */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-3.5 border-t border-zinc-200 dark:border-zinc-800 pt-7 text-xs font-mono">
          <div className="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <span className="text-zinc-400 block text-[10px] uppercase">{isEn ? 'CORE ARCHITECTURE' : 'ARSITEKTUR CORE'}</span>
            <span className="font-bold text-zinc-900 dark:text-white text-xs sm:text-sm">High-Availability Monolith</span>
          </div>
          <div className="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <span className="text-zinc-400 block text-[10px] uppercase">{isEn ? 'DATABASE STANDARD' : 'STANDAR BASIS DATA'}</span>
            <span className="font-bold text-emerald-600 dark:text-emerald-400 text-xs sm:text-sm">Distributed ULID Engine</span>
          </div>
          <div className="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <span className="text-zinc-400 block text-[10px] uppercase">{isEn ? 'DOC CONVERSION' : 'KONVERSI DOKUMEN'}</span>
            <span className="font-bold text-zinc-900 dark:text-white text-xs sm:text-sm">Microsoft MarkItDown</span>
          </div>
          <div className="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <span className="text-zinc-400 block text-[10px] uppercase">{isEn ? 'INFRASTRUCTURE' : 'INFRASTRUKTUR'}</span>
            <span className="font-bold text-zinc-900 dark:text-white text-xs sm:text-sm">Dedicated Cloud VPS</span>
          </div>
        </div>

      </div>

      {/* ========================================================================= */}
      {/* 6. MODAL: DUAL-TRACK ARCHITECTURAL RECOMMENDATION BRIDGE                  */}
      {/* ========================================================================= */}
      {isRecommendationModalOpen && synthesizedResult && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 overflow-y-auto">
          <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 w-full max-w-4xl max-h-[92vh] flex flex-col rounded-none shadow-2xl overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-200">
            
            {/* Modal Topbar Header */}
            <div className="p-4 sm:p-6 bg-zinc-900 text-white flex items-start justify-between gap-4 border-b border-zinc-800 shrink-0">
              <div>
                <div className="flex flex-wrap items-center gap-2 mb-1.5">
                  <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 font-mono text-[10px] font-bold uppercase tracking-wider">
                    <Sparkles className="w-3 h-3" />
                    {isEn ? 'AI SYNTHESIS COMPLETE' : 'AI SINTESIS SELESAI'}
                  </span>
                  <span className="text-[11px] font-mono text-zinc-400">
                    {synthesizedResult.project_name || 'Project Blueprint'}
                  </span>
                </div>
                <h3 className="text-lg sm:text-2xl font-black uppercase tracking-tight text-white">
                  {isEn ? 'System Execution Track Recommendation' : 'Rekomendasi Jalur Eksekusi Sistem'}
                </h3>
                <p className="text-xs sm:text-sm text-zinc-300 font-sans mt-1 max-w-2xl leading-relaxed">
                  {isEn 
                    ? 'Based on your architectural complexity, select the execution path that matches your internal technical resources:'
                    : 'Setelah AI menganalisis kompleksitas ide Anda, sistem memetakan dua opsi jalur yang paling efisien:'}
                </p>
              </div>
              <button
                type="button"
                onClick={() => setIsRecommendationModalOpen(false)}
                className="p-1.5 text-zinc-400 hover:text-white bg-zinc-800 hover:bg-zinc-700 rounded-none transition cursor-pointer"
                title={isEn ? 'Close' : 'Tutup'}
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Modal Scrollable Body */}
            <div className="p-4 sm:p-6 overflow-y-auto space-y-6">
              
              {/* Architectural Assessment Banner */}
              {synthesizedResult.track_recommendation && (
                <div className="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                  <div className="flex flex-wrap items-center justify-between gap-2 mb-2 font-mono text-xs">
                    <div className="flex items-center gap-2">
                      <Cpu className="w-4 h-4 text-emerald-500" />
                      <span className="font-bold text-zinc-900 dark:text-white uppercase tracking-wider">
                        {isEn ? 'SYSTEM COMPLEXITY METRICS:' : 'METRIK KOMPLEKSITAS SISTEM:'}
                      </span>
                    </div>
                    <span className="px-2.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-500/30 text-[11px]">
                      {synthesizedResult.track_recommendation.complexity?.label}
                    </span>
                  </div>

                  <div className="text-xs font-mono text-zinc-600 dark:text-zinc-400 mb-2">
                    {synthesizedResult.track_recommendation.complexity?.summary}
                  </div>

                  {synthesizedResult.track_recommendation.rationale && (
                    <p className="text-xs font-sans text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 p-2.5 border border-zinc-200 dark:border-zinc-800 leading-relaxed">
                      <strong className="text-zinc-900 dark:text-zinc-100">{isEn ? 'AI Analysis Note: ' : 'Catatan Analisis AI: '}</strong>
                      {synthesizedResult.track_recommendation.rationale}
                    </p>
                  )}
                </div>
              )}

              {/* The 2 Side-by-Side Dual-Track Cards */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                
                {/* CARD 1: JALUR MANDIRI (RETAIL) */}
                <div className={`p-5 sm:p-6 border-2 flex flex-col justify-between rounded-none relative transition ${
                  synthesizedResult.track_recommendation?.recommended_track === 'retail'
                    ? 'border-emerald-500 bg-emerald-500/5 dark:bg-emerald-500/[0.03]'
                    : 'border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900'
                }`}>
                  {synthesizedResult.track_recommendation?.recommended_track === 'retail' && (
                    <div className="absolute -top-3 left-4 bg-emerald-500 text-black font-mono text-[9px] font-black uppercase px-2.5 py-0.5 tracking-wider shadow-xs">
                      {isEn ? 'AI RECOMMENDED FOR YOU' : 'REKOMENDASI AI UNTUK ANDA'}
                    </div>
                  )}

                  <div>
                    <div className="font-mono text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-1">
                      {isEn ? 'PATH 01 // DIGITAL RETAIL LICENSE' : 'JALUR 01 // LISENSI DIGITAL RETAIL'}
                    </div>
                    <h4 className="text-lg sm:text-xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-1.5">
                      {isEn ? 'Self-Service Track' : 'Jalur Mandiri'}
                    </h4>
                    <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                      {isEn
                        ? 'If you have an in-house programming/coding team and only need the architecture blueprint to build independently.'
                        : 'Jika Anda memiliki tim programmer/coding sendiri dan hanya butuh cetak biru arsitektur untuk dieksekusi mandiri.'}
                    </p>

                    {/* Pricing Tag */}
                    <div className="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 mb-4">
                      <div className="text-[10px] font-mono text-zinc-500 uppercase">
                        {isEn ? 'INVESTMENT' : 'BIAYA INVESTASI'}
                      </div>
                      <div className="text-lg font-black font-mono text-zinc-900 dark:text-white">
                        Rp {retailLitePrice} <span className="text-xs font-normal text-zinc-500">s/d Rp {retailUltimatePrice}</span>
                      </div>
                      <div className="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 mt-0.5">
                        {isEn ? 'One-time payment • Lifetime access' : 'Sekali bayar • Akses permanen & unduh instan'}
                      </div>
                    </div>

                    {/* Tiers Included in Retail Track */}
                    <div className="mb-4 p-2.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-[11px] font-mono">
                      <div className="text-zinc-500 uppercase font-bold text-[9px] mb-1.5 flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-1">
                        <span>{isEn ? 'AVAILABLE RETAIL TIERS:' : 'PILIHAN PAKET LISENSI RETAIL:'}</span>
                        <span className="text-emerald-500 font-bold">4 Pilihan</span>
                      </div>
                      <div className="space-y-1 text-zinc-700 dark:text-zinc-300">
                        <div className="flex justify-between items-center">
                          <span>• <strong>Spark:</strong> DDL PostgreSQL &amp; AI Rules</span>
                          <span className="text-zinc-500">Rp 99.000</span>
                        </div>
                        <div className="flex justify-between items-center">
                          <span>• <strong>Starter:</strong> PRD Core &amp; DDL Keyset</span>
                          <span className="text-zinc-500">Rp 299.000</span>
                        </div>
                        <div className="flex justify-between items-center">
                          <span>• <strong>Pro:</strong> Full PRD 26 Param + Docker</span>
                          <span className="text-zinc-500">Rp 699.000</span>
                        </div>
                        <div className="flex justify-between items-center text-emerald-600 dark:text-emerald-400 font-bold">
                          <span>• <strong>Ultimate:</strong> Full 7 Software Factory OS</span>
                          <span>Rp 1.490.000</span>
                        </div>
                      </div>
                    </div>

                    {/* Deliverables */}
                    <div className="space-y-2 text-xs font-sans text-zinc-700 dark:text-zinc-300 mb-6">
                      <div className="flex items-start gap-2">
                        <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                        <span>Dokumen PRD 26 parameter lengkap berstandar Fortune 500</span>
                      </div>
                      <div className="flex items-start gap-2">
                        <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                        <span>Skema DDL PostgreSQL Strict ULID (O(1) keyset cursor)</span>
                      </div>
                      <div className="flex items-start gap-2">
                        <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                        <span>Docker container &amp; Modern Monolith config</span>
                      </div>
                      <div className="flex items-start gap-2">
                        <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                        <span>Aturan AI coding agent (.cursorrules &amp; AGENTS.md)</span>
                      </div>
                    </div>
                  </div>

                  <a
                    href={getTrackUrl('retail')}
                    className="w-full bg-emerald-600 hover:bg-emerald-500 text-black font-mono font-black text-xs uppercase py-3 px-4 rounded-none transition flex items-center justify-center gap-2 cursor-pointer shadow-xs text-center"
                  >
                    <ShoppingBag className="w-4 h-4 text-black" />
                    <span>{isEn ? 'Choose Retail Track & View PRD →' : 'Pilih Jalur Mandiri (Retail) →'}</span>
                  </a>
                </div>

                {/* CARD 2: JALUR TURNKEY (STUDIO) */}
                <div className={`p-5 sm:p-6 border-2 flex flex-col justify-between rounded-none relative transition ${
                  synthesizedResult.track_recommendation?.recommended_track === 'studio'
                    ? 'border-emerald-500 bg-emerald-500/5 dark:bg-emerald-500/[0.03]'
                    : 'border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900'
                }`}>
                  {synthesizedResult.track_recommendation?.recommended_track === 'studio' && (
                    <div className="absolute -top-3 left-4 bg-emerald-500 text-black font-mono text-[9px] font-black uppercase px-2.5 py-0.5 tracking-wider shadow-xs">
                      {isEn ? 'AI RECOMMENDED FOR YOU' : 'REKOMENDASI AI UNTUK ANDA'}
                    </div>
                  )}

                  <div>
                    <div className="font-mono text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1">
                      {isEn ? 'PATH 02 // DEDICATED STUDIO TURNKEY' : 'JALUR 02 // DEDICATED STUDIO TURNKEY'}
                    </div>
                    <h4 className="text-lg sm:text-xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-1.5">
                      {isEn ? 'Turnkey Studio Track' : 'Jalur Turnkey (Studio)'}
                    </h4>
                    <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                      {isEn
                        ? 'If you need Neriah Pro engineers to code, test (Pest ApiContractTest), and deploy live to production VPS cloud.'
                        : 'Jika Anda butuh tim Neriah Pro yang mengoding, membangun, menguji (Pest ApiContractTest), hingga live deploy di server VPS.'}
                    </p>

                    {/* Pricing Tag */}
                    <div className="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 mb-4">
                      <div className="text-[10px] font-mono text-zinc-500 uppercase">
                        {isEn ? 'INVESTMENT' : 'BIAYA INVESTASI'}
                      </div>
                      <div className="text-lg font-black font-mono text-zinc-900 dark:text-white">
                        Rp {pricingSettings?.studio_umkm_price || '3.750.000'} <span className="text-xs font-normal text-zinc-500">(UMKM) s/d Rp {pricingSettings?.studio_mvp_price || '50.000.000'}</span>
                      </div>
                      <div className="text-[10px] font-mono text-blue-600 dark:text-blue-400 mt-0.5">
                        {isEn ? 'Staged 50% DP Escrow • SLA Contract' : 'Skema Escrow DP 50% • Kontrak Legal Bersertifikat'}
                      </div>
                    </div>

                    {/* Tiers Included in Studio Track */}
                    <div className="mb-4 p-2.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-[11px] font-mono">
                      <div className="text-zinc-500 uppercase font-bold text-[9px] mb-1.5 flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-1">
                        <span>{isEn ? 'AVAILABLE STUDIO TIERS:' : 'PILIHAN PAKET DEDICATED STUDIO:'}</span>
                        <span className="text-blue-500 font-bold">3 Pilihan</span>
                      </div>
                      <div className="space-y-1 text-zinc-700 dark:text-zinc-300">
                        <div className="flex justify-between items-center">
                          <span>• <strong>Advisory Tier:</strong> Bedah PRD (Potong DP 100%)</span>
                          <span className="text-zinc-500">Rp 2.500.000</span>
                        </div>
                        <div className="flex justify-between items-center">
                          <span>• <strong>UMKM Digital:</strong> Web Kasir / Bisnis Siap Pakai</span>
                          <span className="text-zinc-500">Rp 3.750.000</span>
                        </div>
                        <div className="flex justify-between items-center text-emerald-600 dark:text-emerald-400 font-bold">
                          <span>• <strong>Full Monolith MVP:</strong> 100% Turnkey + SLA</span>
                          <span>Rp 50.000.000</span>
                        </div>
                      </div>
                    </div>

                    {/* Deliverables */}
                    <div className="space-y-2 text-xs font-sans text-zinc-700 dark:text-zinc-300 mb-6">
                      <div className="flex items-start gap-2">
                        <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                        <span>100% turnkey coding oleh tim Senior Architect &amp; DevOps</span>
                      </div>
                      <div className="flex items-start gap-2">
                        <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                        <span>Managed Sprint Capacity terisolasi (Anti-Collision Batch)</span>
                      </div>
                      <div className="flex items-start gap-2">
                        <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                        <span>Kontrak legal SLA bersertifikat &amp; serah terima hak cipta 100%</span>
                      </div>
                      <div className="flex items-start gap-2">
                        <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                        <span>Full deployment ke Cloud VPS Production &amp; Garansi Bug</span>
                      </div>
                    </div>
                  </div>

                  <a
                    href={getTrackUrl('studio')}
                    className="w-full bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black font-mono font-bold text-xs uppercase py-3 px-4 rounded-none transition flex items-center justify-center gap-2 cursor-pointer shadow-xs text-center"
                  >
                    <Rocket className="w-4 h-4 text-emerald-500" />
                    <span>{isEn ? 'Choose Studio Track & Review SLA →' : 'Pilih Jalur Turnkey (Studio) →'}</span>
                  </a>
                </div>

              </div>

            </div>

            {/* Modal Footer */}
            <div className="p-3 sm:p-4 bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-xs font-mono shrink-0">
              <span className="text-zinc-500 text-[11px]">
                {isEn ? 'You can switch between Retail and Studio tracks anytime in the blueprint workspace.' : 'Anda tetap dapat beralih jalur kapan saja di ruang blueprint.'}
              </span>
              <button
                type="button"
                onClick={() => setSynthesizedResult(null)}
                className="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-xs font-mono uppercase cursor-pointer"
              >
                {isEn ? 'Close' : 'Tutup'}
              </button>
            </div>

          </div>
        </div>
      )}
    </section>
  );
}
