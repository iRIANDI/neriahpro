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
  Cpu,
  CreditCard,
  ExternalLink
} from 'lucide-react';

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
    name: 'Lite PRD Generator',
    category: 'blueprint_self_service',
    execution_model: 'self_service',
    is_paid: true,
    requires_login: true,
    requires_sprint_batch: false,
    requires_kickoff_slot: false,
  },
  retail_pro: {
    id: 'retail_pro',
    name: 'Pro Production PRD',
    category: 'blueprint_self_service',
    execution_model: 'self_service',
    is_paid: true,
    requires_login: true,
    requires_sprint_batch: false,
    requires_kickoff_slot: false,
  },
  retail_ultimate: {
    id: 'retail_ultimate',
    name: 'Ultimate Advisory',
    category: 'blueprint_self_service',
    execution_model: 'self_service',
    is_paid: true,
    requires_login: true,
    requires_sprint_batch: false,
    requires_kickoff_slot: false,
  },
  full_mvp: {
    id: 'full_mvp',
    name: 'Enterprise Rapid Monolith MVP',
    category: 'studio_engineering',
    execution_model: 'studio_contract',
    is_paid: true,
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
  }
};

export default function ArchitecturePricingIsland({ 
  headline = 'INVESTASI TRANSPARAN & TEPAT SASARAN',
  subheadline = 'Dua skenario solusi rekayasa perangkat lunak berskala tinggi: Mulai dari blueprint teknis siap eksekusi hingga pengembangan penuh sistem monolit modern tanpa drama pembengkakan biaya.',
  whatsappNumber = '628123456789',
  featureFlags = {},
  currentLocale = 'id',
  pricingSettings = {},
  authUser = null
}) {
  const isEn = currentLocale === 'en' || (typeof window !== 'undefined' && (document.documentElement.lang?.startsWith('en') || document.cookie.includes('neriah_locale=en')));
  
  // Dynamic feature flag & admin toggle for CV Pro module switcher
  const isCvProEnabled = Boolean(featureFlags?.enable_cv_pro) && Boolean(featureFlags?.pricing_show_cv_tab);
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
    name: authUser?.name || '',
    company: authUser?.company || '',
    email: authUser?.email || '',
    country_code: '+62',
    phone: authUser?.phone || '',
    voucher_code: '',
    notes: '',
    honeypot: ''
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitSuccess, setSubmitSuccess] = useState(false);
  const [submitError, setSubmitError] = useState(null);
  const [submittedWaUrl, setSubmittedWaUrl] = useState('');
  const [lastOrder, setLastOrder] = useState(null);
  const [paymentStatus, setPaymentStatus] = useState('idle'); // 'idle' | 'pending' | 'success' | 'error'

  // FAQ Accordion State
  const [openFaq, setOpenFaq] = useState(null);

  const toggleFaq = (index) => {
    setOpenFaq(openFaq === index ? null : index);
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
    setIsModalOpen(true);
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const activePkg = PACKAGE_REGISTRY[selectedPackage] || PACKAGE_REGISTRY.full_mvp;
  const isSelfService = activePkg.execution_model === 'self_service';
  const isPaidSelfService = isSelfService && activePkg.is_paid;
  const isRetailTier = isSelfService;

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
          company: formData.company || (isRetailTier ? `${formData.name} (Personal)` : ''),
          email: formData.email,
          country_code: formData.country_code,
          phone: formData.phone,
          package_tier: selectedPackage,
          voucher_code: formData.voucher_code,
          sprint_batch: activePkg.requires_sprint_batch ? sprintBatch : null,
          kickoff_slot: activePkg.requires_kickoff_slot ? kickoffSlot : null,
          has_blueprint: selectedPackage === 'full_mvp' ? hasBlueprint : null,
          blueprint_slug: selectedPackage === 'full_mvp' && hasBlueprint === 'ready' ? blueprintSlug : null,
          umkm_category: selectedPackage === 'umkm_starter' ? umkmCategory : null,
          notes: formData.notes
        })
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
        // Free Spark tier guest mode: launch blueprint generator
        setTimeout(() => {
          window.location.href = '/blueprint?tier=spark';
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
      q: isEn 
        ? 'Why is Blueprint Advisory (Rp 2.5M) priced similarly to UMKM Starter (Rp 3.75M), even though Advisory includes NO coding?' 
        : 'Mengapa harga Blueprint Advisory (Rp 2.5 Jt) mirip dengan UMKM Digital Starter (Rp 3.75 Jt), padahal Advisory tidak termasuk koding?',
      a: isEn
        ? 'The fundamental difference lies in PROJECT COMPLEXITY and WHO EXECUTES CODING! (1) Blueprint Advisory (Rp 2.5M) is tailored for MASSIVE CUSTOM ENTERPRISE SYSTEMS (SaaS platforms, multi-sided marketplaces, fintech, logistics) whose full turnkey development value ranges from Rp 50M to Rp 200M+. On this scale, senior architectural planning (26-parameter PRD, PostgreSQL Strict ULID O(1) DDL, WBS 5 sprints, and 1-on-1 Principal Architect scoping) is critical to prevent hundreds of millions in costly architecture failures. Coding is handled by your internal team (or your advisory fee is 100% credited toward our Full MVP 50% DP). (2) Conversely, UMKM Digital Starter (Rp 3.75M) is a subsidized CSR program (50% off normal Rp 7.5M, capped at 2 businesses/month) for LOCAL RETAIL SHOPS and simple stores. Its workflows are standardized (POS cashier, QRIS, customer DB, WhatsApp receipts), allowing Neriah Pro engineers to code and deploy it quickly without lengthy custom enterprise architecture sessions.'
        : 'Perbedaan mendasarnya terletak pada SKALA SISTEM dan SIAPA YANG MENGODING! (1) Blueprint Advisory (Rp 2.5 Jt) dirancang untuk SISTEM KUSTOM ENTERPRISE BERSKALA BESAR (SaaS, platform marketplace, logistik, fintech) yang biaya pengembangannya mencapai Rp 50 Jt hingga ratusan juta rupiah. Pada skala ini, rancangan teknis (PRD 26 parameter, skema PostgreSQL Strict ULID O(1), WBS 5 sprint, dan sesi 1-on-1 Principal Architect) mutlak dibutuhkan agar developer klien tidak salah bangun dan rugi puluhan juta. Koding dilakukan oleh tim dev klien sendiri (atau biaya Rp 2.5 Jt ini otomatis memotong DP 50% jika lanjut dikodingkan Neriah Pro). (2) Sebaliknya, UMKM Digital Starter (Rp 3.75 Jt) adalah PROGRAM STIMULUS SUBSIDI 50% (dari normal Rp 7.5 Jt, kuota 2 usaha/bulan) KHUSUS TOKO RETAIL/LOKAL. Alur kerjanya terstandar (kasir POS, QRIS otomatis, database pelanggan, notifikasi WhatsApp) sehingga bisa dikodingkan dan dideploy cepat oleh Neriah Pro tanpa memerlukan perancangan arsitektur custom yang rumit.'
    },
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
      q: isEn 
        ? 'What are the "7 Pillars of Software Factory OS" included in Neriah Pro Project OS?' 
        : 'Apa itu "7 Pilar Software Factory OS" yang menjadi standar rekayasa Project OS Neriah Pro?',
      a: isEn
        ? 'The 7 Pillars constitute our turnkey engineering operating system to eliminate software failure: (1) Brain & Contract Spec: 26-parameter PRD, Mermaid dataflows, and OpenAPI 3.1 contracts. (2) Interface & UI/UX: Anti-AI-slop Clean Solid Brutalism with 100% dark/light mode fidelity and 4 core screen wireframes. (3) Scaffold Container Stack: Production-ready Docker Compose (PHP 8.4, PostgreSQL 16, Redis 7, Nginx HTTP/2). (4) Synthetic Vital Data: Seeder engine with 100+ realistic records for immediate testing. (5) AI Agent Intelligence: Strict directives (.cursorrules, AGENTS.md, CLAUDE.md) for zero-hallucination AI pair-programming. (6) QA Assurance: Automated Pest/PHPUnit ApiContractTest suite running 100% green. (7) Server CI/CD Expressway: GitHub Actions pipeline and zero-downtime deploy.sh across 6 production scenarios.'
        : '7 Pilar Software Factory OS adalah sistem operasi rekayasa perangkat lunak enterprise Neriah Pro untuk mengeliminasi risiko kegagalan proyek: (1) Brain & Contract Spec: Dokumen PRD 26 parameter, diagram alur Mermaid, dan spesifikasi OpenAPI 3.1. (2) Interface & UI/UX: Desain Clean Solid Brutalism anti-AI-slop, 100% Dark & Light Mode, serta wireframe 4 layar utama. (3) Scaffold Container Stack: Docker Compose siap produksi (PHP 8.4, PostgreSQL 16, Redis 7, Nginx HTTP/2). (4) Synthetic Vital Data: Engine seeder 100+ baris data realistis siap uji. (5) AI Agent Intelligence: Aturan baku (.cursorrules, AGENTS.md, CLAUDE.md) untuk memandu AI coding agent tanpa halusinasi. (6) QA Assurance: Suite uji otomatis Pest / PHPUnit ApiContractTest yang lolos 100% hijau. (7) Server CI/CD Expressway: Pipeline GitHub Actions dan skrip deploy.sh 6 skenario tanpa downtime.'
    },
    {
      q: isEn 
        ? 'Why does Project OS enforce PostgreSQL Strict ULID and Keyset Cursor Pagination O(1)?' 
        : 'Mengapa Project OS mewajibkan arsitektur PostgreSQL Strict ULID dan Keyset Cursor Pagination O(1)?',
      a: isEn
        ? 'To guarantee enterprise scalability for millions of records and concurrent users without performance degradation. Conventional AUTO_INCREMENT integers leak business volume, fail in distributed databases, and are vulnerable to enumeration attacks. ULIDs (Universally Unique Lexicographically Sortable Identifiers) provide 128-bit distributed uniqueness with sub-millisecond chronological sorting and zero lock contention. Furthermore, conventional OFFSET-based pagination degrades exponentially as page numbers grow (O(N) full-table scanning); Keyset Cursor Pagination guarantees constant O(1) sub-10ms response times even on tables with tens of millions of rows.'
        : 'Untuk menjamin skalabilitas kelas enterprise yang mampu menangani jutaan data dan ribuan pengunjung bersamaan tanpa penurunan performa (anti-lemot). Primary key AUTO_INCREMENT tradisional membocorkan volume transaksi bisnis, rawan serangan enumerasi ID, dan rusak saat migrasi distributed database. Project OS menggunakan ULID (Universally Unique Lexicographically Sortable Identifier) yang aman, acak terdistribusi, namun tetap berurutan kronologis secara presisi. Selain itu, pagination konvensional berbasis OFFSET melambat drastis saat halaman membesar (O(N) scanning); Keyset Cursor Pagination O(1) menjamin kecepatan query stabil di bawah 10 milidetik bahkan pada tabel berisi puluhan juta data.'
    },
    {
      q: isEn 
        ? 'How do clients monitor development progress in real-time via Master Gantt Timeline & Customer Portal?' 
        : 'Bagaimana cara klien memantau progres pengerjaan secara real-time via Master Gantt Timeline & Customer Portal?',
      a: isEn
        ? 'Every client receives access to their dedicated Customer Portal (/customer/dashboard). Inside, an interactive Master Gantt Timeline powered by local Mermaid.js visualization tracks all 5 sprints in real-time. Clients can inspect sprint phases (Sprint 1: DB & Auth, Sprint 2: Core Business Engine, Sprint 3: Integrations & Payments, Sprint 4: QA & Contract Tests, Sprint 5: Hardening & Cloud Deploy), view active milestones, download intermediate deliverables, verify test statuses, and communicate directly with the Lead Architect without opaque "black box" development.'
        : 'Setiap klien mendapatkan akun resmi di Customer Portal Neriah Pro (/customer/dashboard). Di dalamnya, terdapat Master Gantt Timeline interaktif ditenagai pustaka lokal Mermaid.js (zero CDN latency) yang memvisualisasikan seluruh 5 sprint secara real-time. Klien dapat memantau fase sprint (Sprint 1: Fondasi DB & Auth, Sprint 2: Core Engine & Transaksi, Sprint 3: Integrasi Pembayaran Midtrans, Sprint 4: QA & ApiContractTest, Sprint 5: VPS Hardening & Live Deploy), mengecek milestone aktif, mengunduh deliverable parsial, dan berkoordinasi transparan dengan Principal Architect tanpa ada proses "black box".'
    },
    {
      q: isEn 
        ? 'Why does Neriah Pro restrict sprint capacity (Managed Capacity) to max 2-3 parallel projects per batch?' 
        : 'Mengapa Neriah Pro membatasi kapasitas sprint (Managed Capacity) maksimal 2–3 proyek per batch?',
      a: isEn
        ? 'To preserve elite engineering craftsmanship and guarantee zero burnout or "AI-slop" code generation. Software development agencies that take on dozens of concurrent projects inevitably assign junior freelancers or deliver rushed, unstable code. Neriah Pro strictly limits intake to 2-3 projects per batch (Batch 1, Batch 2, Batch Q1). Once batch capacity is full, the schedule locks via our Anti-Collision Engine, ensuring our Senior Architects and Engineers give 100% dedicated, uninterrupted focus to your codebase until production deployment.'
        : 'Demi menjaga standar rekayasa kelas atas (craftsmanship) dan menjamin kode yang dihasilkan bebas dari "AI-slop" atau bug tersembunyi. Agensi software tradisional sering menerima belasan proyek sekaligus hingga developer kelelahan (burnout) dan akhirnya melempar pekerjaan ke freelancer junior yang asal jadi. Neriah Pro secara ketat membatasi kapasitas maksimal 2–3 proyek per batch (Batch 1, Batch 2, Batch Q1). Ketika slot batch terisi, jadwal terkunci via Anti-Collision Engine sehingga tim Senior Architect dan Engineer kami fokus penuh 100% pada sistem Anda hingga live bergaransi di server produksi.'
    },
    {
      q: isEn 
        ? 'How do the AI Coding Directives (.cursorrules, AGENTS.md, CLAUDE.md) empower our team after handover?' 
        : 'Bagaimana aturan agen AI (.cursorrules, AGENTS.md, CLAUDE.md) memberdayakan tim developer kami setelah serah terima?',
      a: isEn
        ? 'Modern development heavily leverages AI assistants like Cursor, Windsurf, GitHub Copilot, and Claude Code. However, without strict architectural context, AI hallucinates legacy syntax, uses vulnerable packages, and introduces O(N) performance regressions. Our Project OS packages embed battle-tested directive files (.cursorrules, AGENTS.md, CLAUDE.md) tailored to your exact stack (Laravel 13, Filament v5, PostgreSQL ULID, Keyset Cursor). When your developers open your project in AI IDEs, the AI assistant immediately respects your architecture, coding conventions, and security rules without continuous re-prompting.'
        : 'Pengembangan software modern kini mengandalkan AI coding tools seperti Cursor, Windsurf, GitHub Copilot, dan Claude Code. Namun tanpa panduan arsitektur yang ketat, AI sering berhalusinasi, menulis kode usang, atau merusak skema database dengan query yang lambat. Deliverable Project OS menyertakan berkas direktif siap pakai (.cursorrules, AGENTS.md, CLAUDE.md) yang disesuaikan persis dengan stack teknologi proyek Anda (Laravel 13, Filament v5, PostgreSQL ULID, Keyset Cursor O(1)). Ketika developer Anda membuka repositori, AI coding assistant akan otomatis mematuhi aturan arsitektur, konvensi penamaan, dan standar keamanan tanpa perlu berulang kali di-prompting manual.'
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
    },
    {
      q: isEn ? 'What is the Refund Policy and Money-Back Guarantee for blueprints and custom development?' : 'Bagaimana kebijakan pengembalian dana (Refund Policy) dan garansi hasil pengerjaan?',
      a: isEn
        ? 'Self-service digital blueprints (Spark, Lite, Pro, Ultimate) deliver instant digital assets and are non-refundable once unlocked/downloaded, except in verified technical delivery failures reported within 7 days. For Custom Engineering Studio contracts, we provide a 30-Day SLA Bug Warranty post-deployment. If a client requests termination before sprint kickoff, the 50% Down Payment is refunded 100% (minus payment gateway administrative fees). Once sprints commence, refunds are evaluated proportionally against unstarted sprint milestones.'
        : 'Produk blueprint digital mandiri (Spark, Lite, Pro, Ultimate) merupakan aset digital instan dan bersifat non-refundable setelah diunduh/dibuka, kecuali terdapat kendala teknis sistem yang terverifikasi dalam 7 hari kerja. Untuk kontrak Custom Engineering Studio, Neriah Pro memberikan Garansi SLA 30 Hari pasca-live di server VPS untuk perbaikan bug gratis. Apabila klien membatalkan proyek sebelum sprint dimulai, DP 50% dikembalikan 100% (dikurangi biaya admin payment gateway Midtrans). Jika pembatalan terjadi saat sprint berjalan, pengembalian dihitung proporsional terhadap milestone sprint yang belum dikerjakan.'
    },
    {
      q: isEn ? 'How does Neriah Pro handle duplicate orders or double payments?' : 'Bagaimana Neriah Pro menangani pesanan ganda (double payment) atau kelebihan transfer?',
      a: isEn
        ? 'Our billing system features an Anti-Collision Engine with ULID order deduplication and Midtrans webhook idempotency. If your bank or e-wallet is debited twice due to a network glitch, our backend instantly detects the collision. The duplicate amount is either auto-refunded to your original payment source within 1-2 business days or credited toward your next sprint milestone balance upon your written consent.'
        : 'Sistem pembayaran Neriah Pro dilengkapi Anti-Collision Engine dengan penomoran pesanan ULID unik dan verifikasi webhook Midtrans yang bersifat idempoten. Jika saldo atau kartu Anda terdebet ganda akibat kendala koneksi bank, sistem kami otomatis mendeteksi transaksi ganda tersebut. Dana lebih akan langsung diproses untuk refund 100% ke rekening asal dalam 1-2 hari kerja, atau dialokasikan sebagai kredit pemotong pelunasan termin berikutnya sesuai persetujuan tertulis Anda.'
    },
    {
      q: isEn ? 'What is the order cancellation policy and procedure?' : 'Bagaimana syarat dan prosedur pembatalan pesanan (Order Cancellation)?',
      a: isEn
        ? 'Unpaid orders in "Awaiting Payment" status expire and cancel automatically after 24 hours without penalty. For active studio projects, cancellation requests must be submitted via email to support@neriahpro.com or through your project client portal before sprint development starts. Once sprint development commences, work completed and repository commits are frozen and handed over in full according to the contract scope.'
        : 'Pesanan dengan status "Awaiting Payment" otomatis kadaluarsa dan dibatalkan secara sistem setelah 24 jam tanpa penalti. Untuk proyek studio aktif, permohonan pembatalan harus dikirimkan melalui email support@neriahpro.com atau portal klien sebelum sprint dimulai. Apabila pembatalan diajukan saat sprint tengah berlangsung, seluruh koding, skema database, dan dokumen yang telah diselesaikan akan diserahterimakan seutuhnya sesuai ruang lingkup termin yang berjalan.'
    }
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
                className={`px-4 py-2 font-bold uppercase tracking-wider transition rounded-none flex items-center gap-2 cursor-pointer ${
                  activeTab === 'software'
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
                className={`px-4 py-2 font-bold uppercase tracking-wider transition rounded-none flex items-center gap-2 cursor-pointer ${
                  activeTab === 'cv'
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

            <div className="mb-16 sm:mb-20">
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
                  <span className="font-mono text-[10px] font-black tracking-wider uppercase text-zinc-500">
                    TIER 01 // AUDIT
                  </span>
                  <span className="px-2 py-0.5 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-mono text-[9px] font-bold">
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
                      <span className="text-[11px] leading-tight">{f}</span>
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

            {/* TIER 02: LITE PRD */}
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

                <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-1.5 font-sans">
                  {isEn ? 'Lite PRD & Database' : 'Lite PRD & Database'}
                </h3>

                <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                  {isEn 
                    ? 'Official 26-parameter PRD document and ready-to-import SQL database schema.' 
                    : 'Dokumen PRD resmi 26 parameter dan skema database SQL siap import untuk developer.'}
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
                    'Database Schema: PostgreSQL Strict ULID DDL SQL (Ready-to-import)',
                    'Performance Standard: O(1) Keyset & Cursor Pagination Directives',
                    'Execution Roadmap: 2 Sprints Work Breakdown Structure (Linear / Jira ready)',
                    'Official Deliverable: Licensed PDF & Markdown Export with SHA-256 Hash',
                  ] : [
                    'Pilar 1 (Spesifikasi Inti): Dokumen PRD lengkap 26 parameter (Bab 1–6)',
                    'Skema Database: SQL PostgreSQL ULID presisi (Siap diimpor)',
                    'Standar Kinerja: Panduan kueri cepat Keyset & Cursor Pagination O(1)',
                    'Peta Kerja Eksekusi: Rencana kerja 2 sprint terstruktur (Linear / Jira ready)',
                    'Ekspor Dokumen Resmi: Berkas PDF & Markdown berlisensi hash SHA-256',
                  ]).map((f, i) => (
                    <div key={i} className="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                      <Check className="w-3.5 h-3.5 text-cyan-500 shrink-0 mt-0.5" />
                      <span className="text-[11px] leading-tight">{f}</span>
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
                  <span>{isEn ? 'GET LITE PRD' : 'PILIH PAKET LITE'}</span>
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
                  <span className="font-mono text-[10px] font-black tracking-wider uppercase text-emerald-600 dark:text-emerald-400">
                    TIER 03 // PRODUCTION
                  </span>
                  <span className="px-2 py-0.5 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-mono text-[9px] font-bold">
                    {isEn ? 'STARTUP & AGENCY' : 'STARTUP & AGENSI'}
                  </span>
                </div>

                <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-1.5 font-sans">
                  {isEn ? 'Pro Production Blueprint' : 'Pro Production Blueprint'}
                </h3>

                <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                  {isEn 
                    ? 'Production blueprint with Docker containers, system diagrams, and AI coding agent rules.' 
                    : 'Cetak biru produksi lengkap dengan Docker, diagram arsitektur, dan panduan AI coding.'}
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
                  <div className="p-2 mb-2 bg-emerald-500/10 border-l-2 border-emerald-500 text-emerald-800 dark:text-emerald-300 font-mono text-[10px] font-bold">
                    ⭐ {isEn ? 'Includes all Lite PRD features, plus:' : 'Mencakup seluruh fitur Lite PRD, ditambah:'}
                  </div>
                  {(isEn ? [
                    'Pillar 3 (Scaffold): Container Stack (docker-compose.yml, PHP 8.4, PG 16, Redis, Nginx)',
                    'Pillar 4 (Seeder): Synthetic Mock Data Seeder Engine (100+ Realistic Records)',
                    'Pillar 5 (AI Agent): AI Coding Agent Directives (.cursorrules, CLAUDE.md, AGENTS.md)',
                    'Visual System Blueprint: 6 Complete Diagrams (Architecture, ERD, Data Flow, Sequence)',
                    'API Foundation: Pre-built RESTful API Routes (Laravel 13 & Next.js App Router)',
                    'White-Label License: Full rights to re-brand and deliver directly to your clients',
                  ] : [
                    'Pilar 3 (Rangka Koding): File kontainer Docker siap jalan (PHP 8.4, PostgreSQL 16, Redis, Nginx)',
                    'Pilar 4 (Data Awal): Generator data awal sintetis (Synthetic Data Seeder 100+ baris)',
                    'Pilar 5 (Panduan AI): Aturan koding untuk agen AI (.cursorrules, CLAUDE.md, AGENTS.md)',
                    'Cetak Biru Visual: 6 Diagram sistem lengkap (Arsitektur, ERD, Alur Data, Sequence)',
                    'Fondasi API: Rute API RESTful siap pakai (Laravel 13 & Next.js App Router)',
                    'Lisensi Bebas Merek: Hak penuh re-brand dan serahkan langsung ke klien Anda',
                  ]).map((f, i) => (
                    <div key={i} className="flex items-start gap-2 text-xs text-zinc-800 dark:text-zinc-200 font-medium">
                      <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                      <span className="text-[11px] leading-tight">{f}</span>
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
                  <span className="font-mono text-[10px] font-black tracking-wider uppercase text-zinc-500">
                    TIER 04 // ENTERPRISE
                  </span>
                  <span className="px-2 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-mono text-[9px] font-bold">
                    {isEn ? 'COMPLETE 7-PILLAR OS' : 'LENGKAP 7 PILAR OS'}
                  </span>
                </div>

                <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-1.5 font-sans">
                  {isEn ? 'Ultimate Factory OS' : 'Ultimate Factory OS'}
                </h3>

                <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                  {isEn 
                    ? 'Complete 7-pillar architecture with UI wireframes, automated testing, and 1-on-1 architect call.' 
                    : 'Arsitektur lengkap 7 pilar, wireframe UI/UX, testing otomatis, dan sesi konsultasi 1-on-1 bersama Arsitek.'}
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
                  <div className="p-2 mb-2 bg-amber-500/10 border-l-2 border-amber-500 text-amber-800 dark:text-amber-300 font-mono text-[10px] font-bold">
                    ⭐ {isEn ? 'Includes all Pro Blueprint features, plus:' : 'Mencakup seluruh fitur Pro Blueprint, ditambah:'}
                  </div>
                  {(isEn ? [
                    'Pillar 2 (UI/UX Design): Design Tokens JSON & 4 Core Screens Wireframe',
                    'Pillar 6 (QA Testing): Automated Contract Feature Testing Suite (Pest/PHPUnit)',
                    'Pillar 7 (DevOps CI/CD): 1-Click Cloud Deployment Pipeline (GitHub Actions & deploy.sh)',
                    'Executive Call: 1 Scheduled 60-Minute 1-on-1 Session with Principal Architect',
                    'Legal Security: Official Corporate Non-Disclosure Agreement (NDA)',
                  ] : [
                    'Pilar 2 (Desain UI/UX): Cetak biru wireframe 4 layar utama & Token Desain (JSON)',
                    'Pilar 6 (Uji Kualitas): Paket pengujian fitur otomatis (ApiContractTest Pest/PHPUnit)',
                    'Pilar 7 (Otomasi Server): Jalur deployment otomatis cloud (GitHub Actions & deploy.sh)',
                    'Sesi Konsultasi: 1 Sesi 60 Menit 1-on-1 bersama Principal Architect',
                    'Perlindungan Hukum: Surat Perjanjian Kerahasiaan (NDA) resmi bertanda tangan digital',
                  ]).map((f, i) => (
                    <div key={i} className="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                      <Check className="w-3.5 h-3.5 text-amber-500 shrink-0 mt-0.5" />
                      <span className="text-[11px] leading-tight">{f}</span>
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
                ? 'End-to-end custom software engineering executed directly by Neriah Pro senior architects and engineers under the Project OS standard. We write every line of production code across all 7 Pillars of Software Factory OS, enforce Managed Sprint Capacity with Anti-Collision batch scheduling, harden dedicated cloud VPS servers, integrate automated payments, and deliver turnkey systems backed by certified digital contracts, real-time Gantt tracking, and a 3-month SLA bug warranty.'
                : 'Layanan pengerjaan software turnkey end-to-end yang dikoding, diuji, dan dideploy langsung oleh tim Senior Software Architect & Engineer Neriah Pro dengan standar Project OS. Kami menulis 100% kode produksi mencakup seluruh 7 Pilar Software Factory OS, menerapkan Managed Sprint Capacity (Kapasitas Terkelola) dengan proteksi anti-tabrakan jadwal (Anti-Collision), mengonfigurasi dedicated server VPS, mengintegrasikan payment gateway otomatis, serta memantau progres sprint transparan via Master Gantt Timeline dengan garansi bug 3 bulan penuh.'}
            </p>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 items-stretch">
            {/* TIER 1: ADVISORY ONLY / ARCHITECTURE BLUEPRINT */}
            <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-800 p-6 sm:p-7 flex flex-col justify-between rounded-none shadow-xs hover:border-emerald-500/60 transition group relative">
              <div>
                <div className="flex items-center justify-between mb-3">
                  <span className="font-mono text-xs font-black tracking-wider uppercase text-zinc-500 dark:text-zinc-400">
                    {isEn ? 'SCENARIO 1 // ADVISORY' : 'SKENARIO 1 // ADVISORY'}
                  </span>
                  <span className="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-[10px] font-bold">
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
                  <div className="p-2 bg-emerald-500/10 border-l-2 border-emerald-500 text-emerald-800 dark:text-emerald-300 font-mono text-[10px] font-bold">
                    ⭐ {isEn ? 'Advisory fee 100% deducted from DP if continuing to Full MVP!' : 'Biaya Rp 2.5 jt otomatis memotong DP jika lanjut ke Full MVP!'}
                  </div>
                </div>

                {/* Features */}
                <div className="space-y-2.5 mb-6">
                  <div className="font-mono text-[11px] font-bold text-zinc-400 uppercase tracking-wider">
                    {isEn ? 'DELIVERABLES INCLUDED:' : 'OUTPUT YANG DIDAPATKAN:'}
                  </div>
                  {(isEn ? [
                    'Comprehensive 26-Parameter PRD Document (Chapters 1-6) + Interactive Mermaid Charts',
                    'PostgreSQL 16 Strict ULID Database Schema & Keyset Cursor O(1) DDL SQL',
                    'Work Breakdown Structure (WBS) 5 Sprints & Master Mermaid Gantt Chart',
                    'Standard AI Coding Agent Directives (.cursorrules, AGENTS.md) for internal devs',
                    '1-on-1 Technical Scoping Session with Principal Architect (Google Meet)',
                    '100% DP Credit: Fee Rp 2.5M automatically deducts 50% DP if upgrading to Full MVP!',
                    'Corporate Non-Disclosure Agreement (NDA) & 100% Client Intellectual Property',
                  ] : [
                    'Dokumen PRD Arsitektur Lengkap 26 Parameter (Bab 1–6) + Diagram Alur Mermaid',
                    'Skema Basis Data PostgreSQL 16 Strict ULID & Keyset Cursor O(1) DDL SQL Siap Import',
                    'Work Breakdown Structure (WBS) 5 Sprint & Visualisasi Master Mermaid Gantt Chart',
                    'Standar Aturan Agen AI (.cursorrules, AGENTS.md) untuk tim developer internal Anda',
                    '1 Sesi Konsultasi & Scoping Teknis 1-on-1 bersama Principal Architect (Google Meet)',
                    'Garansi Kredit DP 100%: Biaya Rp 2.5 Jt otomatis memotong DP 50% jika lanjut ke Full MVP!',
                    'Dokumen Legal NDA Resmi & 100% Hak Milik Dokumen / Kode diserahkan ke klien',
                  ]).map((feat, idx) => (
                    <div key={idx} className="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                      <span>{feat}</span>
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

                  <a
                    href="/blueprint"
                    className="w-full bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 py-2 px-4 font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none text-center"
                  >
                    <Terminal className="w-3.5 h-3.5 text-emerald-500" />
                    <span>{isEn ? 'TRY LIVE GENERATOR' : 'COBA GENERATOR BLUEPRINT'}</span>
                  </a>
                </div>
              </div>
            </div>

            {/* TIER 2: FULL MVP DEVELOPMENT (FLAGSHIP ENTERPRISE) */}
            <div className="bg-white dark:bg-zinc-900 border-2 border-emerald-500 dark:border-emerald-500 p-6 sm:p-7 flex flex-col justify-between rounded-none shadow-2xl relative transform lg:-translate-y-2">
              {/* Best Value Badge */}
              <div className="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-emerald-500 text-black px-4 py-1 font-mono text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 shadow-md">
                <Crown className="w-3.5 h-3.5" />
                <span>{isEn ? '100% CODED BY NERIAH PRO // FULL MVP' : 'DIKERJAKAN 100% OLEH NERIAH PRO // FULL MVP'}</span>
              </div>

              <div>
                <div className="flex items-center justify-between mb-3 mt-2">
                  <span className="font-mono text-xs font-black tracking-wider uppercase text-emerald-600 dark:text-emerald-400">
                    {isEn ? 'SCENARIO 2 // FULL MONOLITH' : 'SKENARIO 2 // FULL MONOLITH'}
                  </span>
                  <span className="px-2 py-0.5 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-mono text-[10px] font-bold">
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
                    <span className="text-2xl sm:text-3xl font-black font-mono text-zinc-900 dark:text-white">
                      Rp {pricingSettings.mvp_price || '50.000.000'}
                    </span>
                    <span className="text-[10px] font-mono text-zinc-500">{isEn ? 'Total Contract' : 'Nilai Kontrak'}</span>
                  </div>
                  <div className="pt-2 border-t border-emerald-500/20 flex items-center justify-between text-xs font-mono">
                    <span className="text-emerald-600 dark:text-emerald-400 font-bold">
                      {isEn ? 'Down Payment (50% DP):' : 'Uang Muka (DP 50%):'}
                    </span>
                    <span className="font-black text-emerald-500">
                      Rp 25.000.000
                    </span>
                  </div>
                  <div className="text-[10px] font-mono text-zinc-500">
                    &bull; {isEn ? 'Remaining 50% settled after UAT & live deployment' : 'Pelunasan sisa 50% setelah UAT & aplikasi live di VPS'}
                  </div>
                </div>

                {/* Inclusive Header & Features */}
                <div className="space-y-2.5 mb-6">
                  <div className="p-2 mb-2 bg-emerald-500/10 border-l-2 border-emerald-500 text-emerald-800 dark:text-emerald-300 font-mono text-[10px] font-bold">
                    ⭐ {isEn ? 'Includes all Blueprint Advisory deliverables + Complete 7 Pillars of Software Factory OS:' : 'Mencakup seluruh hasil Blueprint Advisory + Lengkap 7 Pilar Software Factory OS:'}
                  </div>
                  {(isEn ? [
                    'Managed Sprint Capacity: Reserved slot in Batch 1, 2, or Q1 with Anti-Collision Engine',
                    'Complete 7 Pillars of Software Factory OS Coded End-to-End (Laravel 13, Filament v5, React 19)',
                    'PostgreSQL 16 Strict ULID Database Architecture & Keyset Cursor Pagination O(1)',
                    'Synthetic Vital Data Seeder (100+ realistic records for instant staging testing)',
                    'AI Coding Agent Directives (.cursorrules, CLAUDE.md, AGENTS.md) ready for Cursor & Windsurf',
                    'Automated Feature Contract-First Tests (ApiContractTest Pest / PHPUnit Suite)',
                    'Automated Payments (Midtrans Snap: QRIS, VA, Cards) with Idempotent Anti-Duplicate Webhook',
                    'Real-Time Master Gantt Timeline & Milestone Transparency in Customer Portal',
                    'Dedicated Production VPS Hardening (Docker/Nixpacks, Nginx HTTP/2, SSL, Redis Caching)',
                    'One-Click Cloud CI/CD Pipeline (GitHub Actions & deploy.sh 6 scenarios)',
                    '100% Source Code & Server Credentials Handover (Zero Vendor Lock-in)',
                    '3-Month Full Priority SLA Bug Warranty & Dedicated Lead Architect Maintenance',
                  ] : [
                    'Managed Sprint Capacity: Slot terisolasi di Batch 1, 2, atau Q1 dengan Anti-Collision Engine',
                    'Seluruh 7 Pilar Software Factory OS Dikodingkan Penuh (Laravel 13, Filament v5, React 19)',
                    'Arsitektur Basis Data PostgreSQL 16 Strict ULID & Keyset Cursor Pagination O(1)',
                    'Synthetic Vital Data Seeder Engine (100+ data uji realistis siap pakai)',
                    'Standardisasi Aturan Koding Agen AI (.cursorrules, CLAUDE.md, AGENTS.md)',
                    'Paket Uji Kontrak Fitur Otomatis (ApiContractTest Pest / PHPUnit Suite)',
                    'Integrasi Pembayaran Otomatis (Midtrans Snap: QRIS/VA/Kartu) & Webhook Idempoten',
                    'Pelacakan Real-Time Master Gantt Timeline & Faktur Pajak di Customer Portal',
                    'Konfigurasi Dedicated Server VPS (Docker/Nixpacks, Nginx HTTP/2, SSL, Redis Caching)',
                    'Pipeline Otomasi Cloud CI/CD (GitHub Actions & deploy.sh 6 skenario deploy aman)',
                    '100% Penyerahan Source Code Repo GitHub Privat & Kredensial Server (No Lock-in)',
                    'Garansi Perbaikan Bug SLA Prioritas 3 Bulan Penuh & Maintenance Terjadwal',
                  ]).map((feat, idx) => (
                    <div key={idx} className="flex items-start gap-2 text-xs text-zinc-800 dark:text-zinc-200 font-medium">
                      <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                      <span>{feat}</span>
                    </div>
                  ))}
                </div>
              </div>

              <div>
                <div className="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 text-center mb-3 font-bold">
                  {isEn ? '100% Executed by Neriah Pro Team (Zero Freelancer Drama)' : '100% Dikerjakan Neriah Pro sampai Live (Tanpa Drama Freelancer)'}
                </div>
                <div className="space-y-2 pt-3 border-t border-zinc-200 dark:border-zinc-800">
                  <button
                    onClick={() => openBookingModal('full_mvp')}
                    className="w-full bg-emerald-500 hover:bg-emerald-400 text-black py-3.5 px-4 font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition rounded-none shadow-lg cursor-pointer"
                  >
                    <span>{isEn ? 'RESERVE SPRINT (50% DP)' : 'RESERVASI PROYEK (DP 50%)'}</span>
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
                  <span className="font-mono text-xs font-black tracking-wider uppercase text-zinc-500 dark:text-zinc-400">
                    {isEn ? 'STIMULUS PROGRAM // UMKM' : 'PROGRAM STIMULUS // UMKM'}
                  </span>
                  <span className="px-2 py-0.5 bg-amber-500/10 text-amber-500 border border-amber-500/20 font-mono text-[10px] font-bold">
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
                      <span className="text-2xl font-black font-mono text-amber-500">
                        Rp 3.750.000
                      </span>
                    </div>
                    <span className="text-[10px] font-mono px-1.5 py-0.5 bg-amber-500/20 text-amber-600 dark:text-amber-400 font-bold">
                      SUBSIDI 50%
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
                    'Notifikasi Konfirmasi Pesanan & Faktur Otomatis via WhatsApp Standar E.164 ke Pelanggan',
                    'Setup Cloud SSD VPS Cepat + Domain Bisnis (.id / .com) dengan Enkripsi HTTPS SSL',
                    '100% Dikerjakan sampai Siap Pakai oleh Neriah Pro + Garansi Bug 1 Bulan & Panduan Video',
                  ]).map((feat, idx) => (
                    <div key={idx} className="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5" />
                      <span>{feat}</span>
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
                    href={`https://wa.me/${whatsappNumber}?text=${encodeURIComponent('Halo Lead Architect Neriah Pro, saya ingin konsultasi mengenai Program Subsidi UMKM Digital Starter.')}`}
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
                  <th className="py-3 px-3 font-bold text-zinc-600 dark:text-zinc-400">
                    {isEn ? 'Evaluation Parameter' : 'Parameter Evaluasi'}
                  </th>
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
                    <div className="text-[9px] font-normal text-zinc-500">Rp 50.000.000 ({isEn ? '50% DP' : 'DP 50%'})</div>
                  </th>
                  <th className="py-3 px-3 font-bold text-amber-500">
                    <div>UMKM Starter</div>
                    <div className="text-[9px] font-normal text-zinc-500">{isEn ? 'Rp 3.75 - 7.5 M' : 'Rp 3.75 - 7.5 Jt'}</div>
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-200 dark:divide-zinc-800 font-sans">
                {/* 1. SIAPA YANG MELAKUKAN KODING */}
                <tr className="bg-amber-500/5 dark:bg-amber-500/10">
                  <td className="py-3 px-3 font-bold text-zinc-900 dark:text-white font-mono">
                    {isEn ? 'Who Executes Coding?' : 'Siapa yang Melakukan Koding?'}
                  </td>
                  <td className="py-3 px-3 text-cyan-700 dark:text-cyan-300 font-mono font-bold bg-cyan-500/5">
                    {isEn 
                      ? '100% Self-Service by You / Your Team (Zero Neriah Pro Coding)' 
                      : '100% Mandiri oleh Developer / Tim Anda (Zero Neriah Pro Coding)'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 font-mono">
                    {isEn ? 'Client Dev Team (Guided by Scoping)' : 'Tim Klien Sendiri (Didampingi Scoping)'}
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-mono font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? '100% Executed by Neriah Pro' : '100% Dikerjakan Neriah Pro'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-amber-600 dark:text-amber-400 font-mono font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-amber-500 shrink-0" />
                      <span>{isEn ? '100% Executed by Neriah Pro' : '100% Dikerjakan Neriah Pro'}</span>
                    </span>
                  </td>
                </tr>

                {/* 2. PERSYARATAN AKUN & LOGIN */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">
                    {isEn ? 'Account & Login Requirements' : 'Persyaratan Akun & Login'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5">
                    {isEn ? (
                      <>Spark: <strong>Guest Mode (No Login)</strong><br />Lite, Pro, Ultimate: <strong>Verified Account Required</strong></>
                    ) : (
                      <>Spark: <strong>Guest Mode (Tanpa Login)</strong><br />Lite, Pro, Ultimate: <strong>Wajib Login Akun</strong></>
                    )}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300">
                    {isEn ? 'Official Client Registration Required' : 'Wajib Registrasi Akun Klien Resmi'}
                  </td>
                  <td className="py-3 px-3 text-zinc-800 dark:text-zinc-200 font-semibold">
                    {isEn ? 'Digital Contract & Corporate Account' : 'Kontrak Legal & Akun Korporat'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300">
                    {isEn ? 'Business Client Registration' : 'Registrasi Akun Klien UMKM'}
                  </td>
                </tr>

                {/* 3. BATAS KUOTA & SIKLUS RESET */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">
                    {isEn ? 'Quota Limit & Reset Cycle' : 'Batas Kuota & Siklus Reset'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5">
                    {isEn ? (
                      <>Spark: <strong>2 Audits/Mo (Resets on 1st)</strong><br />Lite/Pro/Ultimate: <strong>1 Project per License</strong></>
                    ) : (
                      <>Spark: <strong>2x Audit/Bulan (Reset tiap tgl 1)</strong><br />Lite/Pro/Ultimate: <strong>1 Proyek per Lisensi</strong></>
                    )}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300">
                    {isEn ? '1 Focused Specification Project' : '1 Proyek Spesifikasi Terfokus'}
                  </td>
                  <td className="py-3 px-3 text-zinc-800 dark:text-zinc-200 font-semibold">
                    {isEn ? '1 Dedicated Enterprise Build (Batch Lock)' : '1 Proyek Enterprise Penuh (Kunci Slot Batch)'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300">
                    {isEn ? '1 Business System (Monthly Subsidy)' : '1 Sistem Usaha (Alokasi Subsidi Bulanan)'}
                  </td>
                </tr>

                {/* 3B. KAPASITAS TERKELOLA & ANTI-TABRAKAN JADWAL */}
                <tr className="bg-emerald-500/5 dark:bg-emerald-500/10">
                  <td className="py-3 px-3 font-bold text-zinc-900 dark:text-white font-mono">
                    {isEn ? 'Managed Capacity & Anti-Collision' : 'Kapasitas Terkelola & Anti-Tabrakan'}
                  </td>
                  <td className="py-3 px-3 text-zinc-500 font-mono bg-cyan-500/5">
                    {isEn ? 'Instant Self-Service' : 'Mandiri Instan'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 font-mono">
                    {isEn ? 'Flexible Scheduling' : 'Jadwal Fleksibel'}
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-mono font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? 'Batch 1, 2, Q1 (Max 2-3/Batch, Anti-Collision Lock)' : 'Batch 1, 2, Q1 (Maks 2-3/Batch, Kunci Anti-Tabrakan)'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-amber-600 dark:text-amber-400 font-mono">
                    {isEn ? 'Quota 2 Slots / Month' : 'Kuota 2 Slot / Bulan'}
                  </td>
                </tr>

                {/* 3C. KELENGKAPAN 7 PILAR SOFTWARE FACTORY OS */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200 font-mono">
                    {isEn ? '7 Pillars of Software Factory OS' : '7 Pilar Software Factory OS'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5 text-[11px]">
                    {isEn ? 'Spark: 1 | Lite: 2 | Pro: 5 | Ultimate: Complete 7' : 'Spark: 1 | Lite: 2 | Pro: 5 | Ultimate: Lengkap 7'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 text-[11px]">
                    {isEn ? 'Pillar 1 (PRD & DDL) + WBS 5 Sprints' : 'Pilar 1 (PRD & DDL) + WBS 5 Sprint'}
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold text-[11px]">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? '100% Complete 7 Pillars Coded & Live' : '100% Seluruh 7 Pilar Dikodingkan sampai Live'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-zinc-600 dark:text-zinc-400 text-[11px]">
                    {isEn ? 'Core Monolith + Filament v5' : 'Engine Transaksi + Filament v5'}
                  </td>
                </tr>

                {/* 3D. MASTER GANTT TIMELINE & CUSTOMER PORTAL */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200 font-mono">
                    {isEn ? 'Master Gantt & Customer Portal' : 'Master Gantt & Customer Portal'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5 text-[11px]">
                    {isEn ? 'Mermaid Gantt Diagram Code' : 'Kode Diagram Mermaid Gantt'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 text-[11px]">
                    {isEn ? 'WBS 5-Sprint Gantt Blueprint' : 'Cetak Biru WBS 5 Sprint Gantt'}
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold text-[11px]">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? 'Interactive Gantt + Customer Portal Live' : 'Interactive Gantt + Akses Customer Portal'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-zinc-600 dark:text-zinc-400 text-[11px]">
                    {isEn ? 'Standard Milestone Tracking' : 'Pelacakan Milestone Standar'}
                  </td>
                </tr>

                {/* 4. MASA BERLAKU & JENDELA REVISI */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">
                    {isEn ? 'Validity Period & Revision Window' : 'Masa Berlaku & Jendela Revisi'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5">
                    {isEn ? (
                      <>Spark: 7-Day Guest Session<br />Lite: Lifetime Download + 30-Day Revisions<br />Pro: Lifetime Download + 6-Mo AI Regen<br />Ultimate: Lifetime + 1-Yr Updates + 60-Day Meet</>
                    ) : (
                      <>Spark: 7hr Guest Session<br />Lite: Unduh Selamanya + 30hr Revisi<br />Pro: Unduh Selamanya + 6bln AI Regen<br />Ultimate: Selamanya + 1th Update + 60hr Meet</>
                    )}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300">
                    {isEn ? 'Lifetime Document + 7-Day Revision Support' : 'Dokumen Selamanya + 7 Hari Pendampingan Revisi'}
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold">
                    {isEn ? '3-Month Bug Warranty & Post-Live SLA' : '3 Bulan Garansi Bug & SLA Maintenance Pasca Live'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300">
                    {isEn ? '1-Month Bug Warranty & Operations Guide' : '1 Bulan Garansi Bug & Panduan Operasional'}
                  </td>
                </tr>

                {/* 5. TARGET PERSONA */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">
                    {isEn ? 'Target Persona' : 'Target Persona'}
                  </td>
                  <td className="py-3 px-3 text-zinc-600 dark:text-zinc-400 bg-cyan-500/5">
                    {isEn ? 'Solo Devs, Tech Leads, Founders, In-House Teams' : 'Solo Dev, Tech Lead, Founder, Agensi yang Koding Sendiri'}
                  </td>
                  <td className="py-3 px-3 text-zinc-600 dark:text-zinc-400">
                    {isEn ? 'CTOs & Founders with Internal Dev Teams' : 'CTO & Founder dengan Tim Dev Internal'}
                  </td>
                  <td className="py-3 px-3 text-zinc-800 dark:text-zinc-200 font-semibold">
                    {isEn ? 'Scale-Ups, Enterprises, Investor-Ready Startups' : 'Scale-Up, Korporasi, Investor Ready'}
                  </td>
                  <td className="py-3 px-3 text-zinc-600 dark:text-zinc-400">
                    {isEn ? 'Local Businesses, Retail Shops, Services & F&B' : 'UMKM, Toko Retail, Usaha Jasa & F&B'}
                  </td>
                </tr>

                {/* 6. WAKTU PENGERJAAN */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">
                    {isEn ? 'Delivery Timeline' : 'Waktu Pengerjaan'}
                  </td>
                  <td className="py-3 px-3 font-mono text-cyan-600 dark:text-cyan-400 font-bold bg-cyan-500/5">
                    {isEn ? 'Instant (Seconds via AI)' : 'Instan (Hitungan Detik via AI)'}
                  </td>
                  <td className="py-3 px-3 font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                    {isEn ? '24 - 48 Business Hours' : '24 - 48 Jam Kerja'}
                  </td>
                  <td className="py-3 px-3 font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                    {isEn ? '4 - 6 Weeks (5 Sprints)' : '4 - 6 Minggu (5 Sprint)'}
                  </td>
                  <td className="py-3 px-3 font-mono text-zinc-600 dark:text-zinc-400">
                    {isEn ? '2 - 3 Weeks (2 Sprints)' : '2 - 3 Minggu (2 Sprint)'}
                  </td>
                </tr>

                {/* 7. PRD 26 PARAMETER */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">
                    {isEn ? '26-Parameter PRD' : 'PRD 26 Parameter'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5">
                    {isEn ? (
                      <>Spark: Lean 5 Features<br />Lite, Pro, Ultimate: <strong>Full (JSON & MD)</strong></>
                    ) : (
                      <>Spark: Lean 5 Fitur<br />Lite, Pro, Ultimate: <strong>Lengkap (JSON & MD)</strong></>
                    )}
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? 'Full (JSON & Markdown)' : 'Lengkap (JSON & Markdown)'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? 'Full + Fully Implemented' : 'Lengkap + Terimplementasi'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-zinc-500">
                    {isEn ? 'Lean (Core Business Workflow)' : 'Sederhana (Alur Inti Usaha)'}
                  </td>
                </tr>

                {/* 8. POSTGRESQL STRICT ULID DDL */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">
                    {isEn ? 'PostgreSQL Strict ULID DDL' : 'PostgreSQL Strict ULID DDL'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5">
                    {isEn ? (
                      <>Lite, Pro, Ultimate: <strong>Ready-to-Import SQL DDL</strong></>
                    ) : (
                      <>Lite, Pro, Ultimate: <strong>DDL SQL Siap Import</strong></>
                    )}
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? 'Ready-to-Import DDL Script' : 'DDL Script Siap Import'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? 'Live on Production VPS Server' : 'Live di Server VPS'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? 'Live Transactional Database' : 'Database Transaksional'}</span>
                    </span>
                  </td>
                </tr>

                {/* 9. CETAK BIRU DECOUPLED 2026+ */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">
                    {isEn ? 'Decoupled Blueprint 2026+' : 'Cetak Biru Decoupled 2026+'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5">
                    {isEn ? (
                      <>Pro & Ultimate: <strong>Next.js 15, Cloudflare, OpenAPI 3.1</strong></>
                    ) : (
                      <>Pro & Ultimate: <strong>Next.js 15, Cloudflare, OpenAPI 3.1</strong></>
                    )}
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
                      <span>{isEn ? 'Isolated API Resources (Headless-Ready)' : 'API Resources Terisolasi (Siap Headless)'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-zinc-500">
                    {isEn ? 'Standardized QRIS & WhatsApp Webhooks' : 'API Webhook QRIS & WhatsApp Terstandar'}
                  </td>
                </tr>

                {/* 9B. SYNTHETIC DATA SEEDER (PILAR 4) */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">
                    {isEn ? 'Synthetic Data Seeder (Pillar 4)' : 'Synthetic Data Seeder (Pilar 4)'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5 text-[11px]">
                    {isEn ? 'Pro/Ultimate: Blueprint Seeder Script' : 'Pro/Ultimate: Cetak Biru Seeder Sintetis'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 text-[11px]">
                    {isEn ? 'Data Dictionary & Mock Specs' : 'Kamus Data & Spesifikasi Mock'}
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold text-[11px]">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? '100+ Realistic Records Seeded Live' : '100+ Baris Data Sintetis Di-seed Live'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-zinc-500 text-[11px]">
                    {isEn ? 'Sample Catalog & Initial Records' : 'Katalog Awal & Data Contoh Toko'}
                  </td>
                </tr>

                {/* 9C. ATURAN KODING AGEN AI (PILAR 5) */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">
                    {isEn ? 'AI Coding Directives (Pillar 5)' : 'Aturan Koding Agen AI (Pilar 5)'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5 text-[11px]">
                    {isEn ? 'Pro/Ultimate: .cursorrules & AGENTS.md' : 'Pro/Ultimate: .cursorrules & AGENTS.md'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 text-[11px]">
                    {isEn ? 'Architecture Rules for Client Team' : 'Template Aturan AI Khusus Tim Klien'}
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold text-[11px]">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? 'Repository-Tailored AI Directives' : 'Ruleset Khusus (.cursorrules, AGENTS.md)'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-zinc-500 text-[11px]">
                    {isEn ? 'Standard Operations Manual' : 'Buku Panduan Operasional Standar'}
                  </td>
                </tr>

                {/* 9D. AUTOMATED CONTRACT TESTS (PILAR 6) */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">
                    {isEn ? 'Contract-First Tests (Pillar 6)' : 'Uji Kontrak Otomatis (Pilar 6)'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5 text-[11px]">
                    {isEn ? 'Ultimate: ApiContractTest Specs' : 'Ultimate: Spesifikasi ApiContractTest'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 text-[11px]">
                    {isEn ? 'Acceptance Criteria & Test Matrix' : 'Kriteria UAT & Matriks Skenario Uji'}
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold text-[11px]">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? 'Automated Pest/PHPUnit Suite (100% Green)' : 'Suite Uji Otomatis Pest (100% Lolos)'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-zinc-500 text-[11px]">
                    {isEn ? 'End-to-End Payment & Order UAT' : 'Verifikasi UAT Alur Transaksi & QRIS'}
                  </td>
                </tr>

                {/* 9E. CLOUD CI/CD & DEPLOY.SH (PILAR 7) */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">
                    {isEn ? 'Cloud CI/CD & deploy.sh (Pillar 7)' : 'Cloud CI/CD & deploy.sh (Pilar 7)'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5 text-[11px]">
                    {isEn ? 'Ultimate: GitHub Actions & deploy.sh Code' : 'Ultimate: GitHub Actions & deploy.sh'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 text-[11px]">
                    {isEn ? 'VPS Topology & Hardening Guide' : 'Panduan Topologi VPS & Hardening'}
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold text-[11px]">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? 'Live CI/CD Pipeline + deploy.sh 6 Scenarios' : 'Pipeline CI/CD Aktif + deploy.sh 6 Skenario'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-zinc-500 text-[11px]">
                    {isEn ? 'Cloud VPS Hosting + Auto SSL HTTPS' : 'Hosting Cloud VPS + SSL HTTPS Otomatis'}
                  </td>
                </tr>

                {/* 9F. PROTEKSI TRANSAKSI IDEMPOTEN */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">
                    {isEn ? 'Idempotent Payment Engine' : 'Proteksi Transaksi Idempoten'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 bg-cyan-500/5 text-[11px]">
                    {isEn ? 'Instant License Delivery' : 'Pengiriman Lisensi Digital Instan'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300 text-[11px]">
                    {isEn ? 'Idempotent Webhook Architecture' : 'Arsitektur Webhook Anti-Tabrakan'}
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold text-[11px]">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? 'Midtrans Snap + Anti-Double Payment Guard' : 'Midtrans Snap + Proteksi Dobel Bayar'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-3 text-zinc-500 text-[11px]">
                    {isEn ? 'Automated Midtrans QRIS & VA' : 'QRIS & Virtual Account Otomatis'}
                  </td>
                </tr>

                {/* 10. MEKANISME PEMBAYARAN */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">
                    {isEn ? 'Payment Structure' : 'Mekanisme Pembayaran'}
                  </td>
                  <td className="py-3 px-3 font-mono bg-cyan-500/5">
                    {isEn ? 'Spark: $0 | Paid: 100% One-Time' : 'Spark: Rp 0 | Berbayar: 100% Sekali Bayar'}
                  </td>
                  <td className="py-3 px-4 font-mono">
                    {isEn ? '100% Upfront (Deducts 50% DP)' : '100% di Muka (Memotong DP 50%)'}
                  </td>
                  <td className="py-3 px-4 font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                    {isEn ? '50% DP + 50% Upon UAT Live' : 'DP 50% + Pelunasan UAT 50%'}
                  </td>
                  <td className="py-3 px-4 font-mono">
                    {isEn ? '50% DP + 50% Upon UAT Live' : 'DP 50% + Pelunasan UAT 50%'}
                  </td>
                </tr>

                {/* 11. HAK MILIK SOURCE CODE */}
                <tr>
                  <td className="py-3 px-3 font-bold text-zinc-800 dark:text-zinc-200">
                    {isEn ? 'Document & Code Ownership' : 'Hak Milik Dokumen / Kode'}
                  </td>
                  <td className="py-3 px-3 text-emerald-600 dark:text-emerald-400 font-bold bg-cyan-500/5">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? '100% Client Ownership' : '100% Milik Klien'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-4 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? '100% Client (NDA)' : '100% Klien (NDA)'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-4 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? '100% Client (No Lock-in)' : '100% Klien (No Lock-in)'}</span>
                    </span>
                  </td>
                  <td className="py-3 px-4 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span className="inline-flex items-center gap-1">
                      <Check className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                      <span>{isEn ? '100% Client' : '100% Klien'}</span>
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
                      <CreditCard className="w-3 h-3" />
                      <span>{isEn ? 'DIRECT CHECKOUT // INSTANT MIDTRANS SNAP' : 'CHECKOUT LANGSUNG // INSTANT MIDTRANS SNAP'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      {isEn ? 'Checkout Ultimate Advisory PRD' : 'Checkout Lisensi Ultimate Advisory PRD'} (Rp {pricingSettings.retail_ultimate_price || '1.490.000'})
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn 
                        ? 'Instant 100% self-service checkout via Midtrans Snap (QRIS, VA Bank, Credit Card). PRD license activates immediately + includes 1 scheduled 60-min Google Meet architecture session.'
                        : 'Pembayaran instan 100% mandiri via Midtrans Snap (QRIS, Virtual Account, Kartu Kredit). Lisensi PRD langsung aktif di akun Anda + termasuk 1 sesi Google Meet 60 menit bersama Lead Architect.'}
                    </p>
                  </>
                ) : selectedPackage === 'retail_pro' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                      <CreditCard className="w-3 h-3" />
                      <span>{isEn ? 'DIRECT CHECKOUT // INSTANT MIDTRANS SNAP' : 'CHECKOUT LANGSUNG // INSTANT MIDTRANS SNAP'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      {isEn ? 'Checkout Pro Production PRD' : 'Checkout Lisensi Pro Production PRD'} (Rp {pricingSettings.retail_pro_price || '399.000'})
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn 
                        ? 'Instant 100% self-service checkout via Midtrans Snap (QRIS, VA, Credit Card). Production blueprint, 6 Mermaid diagrams, Decoupled matrix & WBS 5 Sprints activate immediately.'
                        : 'Pembayaran instan 100% mandiri via Midtrans Snap (QRIS, Virtual Account, Kartu Kredit). Cetak biru produksi, 6 diagram Mermaid, WBS 5 sprint, dan OpenAPI 3.1 langsung aktif di akun Anda.'}
                    </p>
                  </>
                ) : selectedPackage === 'retail_lite' ? (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                      <CreditCard className="w-3 h-3" />
                      <span>{isEn ? 'DIRECT CHECKOUT // INSTANT MIDTRANS SNAP' : 'CHECKOUT LANGSUNG // INSTANT MIDTRANS SNAP'}</span>
                    </div>
                    <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                      {isEn ? 'Checkout Lite PRD Generator' : 'Checkout Lisensi Lite PRD Generator'} (Rp {pricingSettings.retail_lite_price || '99.000'})
                    </h3>
                    <p className="text-xs text-zinc-500 font-sans mt-0.5 leading-relaxed">
                      {isEn 
                        ? 'Instant 100% self-service checkout via Midtrans Snap (QRIS, Virtual Account, Credit Card). Essential 26-parameter PRD + PostgreSQL Strict ULID DDL SQL activate immediately.'
                        : 'Pembayaran instan 100% mandiri via Midtrans Snap (QRIS, Virtual Account BCA/Mandiri/BRI, Kartu Kredit). Kunci lisensi dan akses unduh PRD 26 parameter langsung aktif seketika di akun Anda.'}
                    </p>
                  </>
                ) : (
                  <>
                    <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                      <Terminal className="w-3 h-3" />
                      <span>{isEn ? '100% SELF-SERVICE // GUEST ACCESS (NO LOGIN)' : '100% SELF-SERVICE // AKSES TAMU (TANPA LOGIN)'}</span>
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
                <div className="p-6 bg-emerald-500/10 border border-emerald-500/30 text-center space-y-4">
                  <div className="w-12 h-12 bg-emerald-500 text-black flex items-center justify-center mx-auto">
                    {paymentStatus === 'success' ? <Check className="w-6 h-6" /> : <CreditCard className="w-6 h-6" />}
                  </div>

                  <div>
                    <h4 className="font-bold text-sm text-zinc-900 dark:text-white uppercase font-mono">
                      {paymentStatus === 'success'
                        ? (isEn ? 'PAYMENT CONFIRMED & ACCESS UNLOCKED!' : 'PEMBAYARAN LUNAS & AKSES AKTIF!')
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
                          : 'Pembayaran Anda telah sukses diverifikasi! Dokumen spesifikasi PRD, skema SQL DDL, dan faktur resmi telah aktif di Dashboard Pelanggan Anda.')
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
                      <div className="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
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
                        {lastOrder.redirect_url && (
                          <a
                            href={lastOrder.redirect_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="w-full sm:w-auto px-4 py-3 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-emerald-400 font-mono text-xs font-bold uppercase tracking-wider inline-flex items-center justify-center gap-1.5 border border-emerald-500/40 rounded-none transition"
                          >
                            <ExternalLink className="w-3.5 h-3.5" />
                            <span>{isEn ? 'DIRECT PAYMENT TAB ↗' : 'TAB PEMBAYARAN LANGSUNG ↗'}</span>
                          </a>
                        )}
                      </div>
                    ) : null}

                    {submittedWaUrl && !isRetailTier && (
                      <a
                        href={submittedWaUrl}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="w-full sm:w-auto px-4 py-2.5 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-300 font-mono text-xs font-bold uppercase tracking-wider inline-flex items-center justify-center gap-1.5 border border-zinc-700 rounded-none transition"
                      >
                        <MessageSquare className="w-3.5 h-3.5" />
                        <span>{isEn ? 'WhatsApp Help (Optional)' : 'Bantuan WhatsApp (Opsional)'}</span>
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
                      <optgroup label={isEn ? "INSTANT ARCHITECTURAL BLUEPRINT (100% SELF-SERVICE // ZERO NERIAH PRO CODING)" : "INSTANT ARCHITECTURAL BLUEPRINT (100% SELF-SERVICE // TANPA KODING NERIAH PRO)"}>
                        <option value="retail_spark">{isEn ? 'Spark Free Audit (Free / Guest Mode - Monthly Reset)' : 'Spark Free Audit (Rp 0 - Guest Mode / Reset Tiap Bulan)'}</option>
                        <option value="retail_lite">{isEn ? `Lite PRD Generator (Rp ${pricingSettings.retail_lite_price || '99.000'} - Login Required)` : `Lite PRD Generator (Rp ${pricingSettings.retail_lite_price || '99.000'} - Wajib Login)`}</option>
                        <option value="retail_pro">{isEn ? `Pro Production PRD & WBS (Rp ${pricingSettings.retail_pro_price || '399.000'} - Login Required)` : `Pro Production PRD & WBS (Rp ${pricingSettings.retail_pro_price || '399.000'} - Wajib Login)`}</option>
                        <option value="retail_ultimate">{isEn ? `Ultimate Advisory + 1-on-1 Call (Rp ${pricingSettings.retail_ultimate_price || '1.490.000'} - Verified Account)` : `Ultimate Advisory + 1-on-1 Call (Rp ${pricingSettings.retail_ultimate_price || '1.490.000'} - Akun Terverifikasi)`}</option>
                      </optgroup>
                      <optgroup label={isEn ? "NERIAH PRO CUSTOM ENGINEERING STUDIO (EXECUTED DIRECTLY BY NERIAH PRO)" : "NERIAH PRO CUSTOM ENGINEERING STUDIO (DIKERJAKAN LANGSUNG OLEH NERIAH PRO)"}>
                        <option value="full_mvp">{isEn ? 'Enterprise Rapid Monolith MVP (5 Sprints - 50% DP Rp 25,000,000)' : 'Enterprise Rapid Monolith MVP (5 Sprint - DP 50% Rp 25.000.000)'}</option>
                        <option value="umkm_starter">{isEn ? 'UMKM Digital Starter (50% Subsidy Program - Rp 3,750,000)' : 'UMKM Digital Starter (Program Subsidi 50% - Rp 3.750.000)'}</option>
                        <option value="blueprint_advisory">{isEn ? 'Blueprint & PRD Architecture Advisory (Rp 2,500,000)' : 'Blueprint & PRD Architecture Advisory (Rp 2.500.000)'}</option>
                      </optgroup>
                    </select>
                  </div>

                  {/* BADGE PENEGASAN AKSI DIGITAL INSTAN (KHUSUS PAKET SELF-SERVICE // SPRINT BATCH & KICKOFF DISEMBUNYIKAN) */}
                  {isSelfService && (
                    <div className="p-3 bg-emerald-500/10 border border-emerald-500/30 text-xs font-mono flex items-center gap-2.5 text-emerald-700 dark:text-emerald-400">
                      <Zap className="w-4 h-4 text-emerald-500 shrink-0" />
                      <div className="font-bold tracking-tight">
                        {isEn 
                          ? '⚡ Instant Digital Access — Auto-Generated & Ready for Download Post-Payment' 
                          : '⚡ Akses Digital Instan — Dihasilkan Otomatis & Siap Unduh Pasca Pembayaran'}
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
                          {isEn ? '2. Blueprint PRD Readiness (Scope-Lock Prerequisite) *' : '2. Kesiapan Dokumen Blueprint PRD (Prasyarat Scope-Lock) *'}
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
                            <span className="text-xs font-bold block">{isEn ? 'No Blueprint Yet' : 'Belum Ada Blueprint'}</span>
                            <span className="text-[10px] text-zinc-500 block mt-0.5">{isEn ? 'PRD advisory required first (Fee Rp 2.5m deducts 50% DP)' : 'Wajib diawali PRD (Biaya Rp 2.5jt memotong DP 50%)'}</span>
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
                        <span className="p-1.5 bg-white/60 dark:bg-zinc-900/60 border border-emerald-500/20 block text-center font-bold">🧾 {isEn ? 'Official Invoice' : 'Faktur Pajak'}</span>
                        <span className="p-1.5 bg-white/60 dark:bg-zinc-900/60 border border-emerald-500/20 block text-center font-bold">⚡ {isEn ? 'Instant QRIS / VA' : 'QRIS / VA Instan'}</span>
                      </div>
                    </div>
                  )}

                  {/* 5. KHUSUS SPARK FREE TIER: GUEST MODE EXPLORER */}
                  {selectedPackage === 'retail_spark' && (
                    <div className="p-3.5 bg-cyan-500/10 border border-cyan-500/30 text-xs font-sans space-y-2">
                      <div className="flex items-center justify-between">
                        <div className="flex items-center gap-1.5 font-mono font-bold text-xs text-cyan-600 dark:text-cyan-400 uppercase">
                          <Rocket className="w-3.5 h-3.5 shrink-0" />
                          <span>{isEn ? 'SPARK FREE IDEA AUDIT (GUEST MODE)' : 'SPARK AUDIT IDE GRATIS (GUEST MODE)'}</span>
                        </div>
                        <span className="text-[10px] font-mono font-bold px-2 py-0.5 bg-cyan-500/20 text-cyan-700 dark:text-cyan-300">
                          {isEn ? '2 AUDITS / MONTH' : '2X AUDIT / BULAN'}
                        </span>
                      </div>
                      <p className="text-[11px] text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        {isEn
                          ? 'Spark is 100% free with 2 idea sanity checks per month in Guest Mode (no credit card or registration needed). You can launch it directly now, or submit your details to save audit history:'
                          : 'Paket Spark 100% gratis 2x per bulan dalam mode Tamu (tanpa kartu kredit atau pendaftaran). Anda dapat langsung membuka generator di bawah, atau isi formulir jika ingin menyimpan riwayat audit ke akun:'}
                      </p>
                      <a
                        href="/blueprint?tier=spark"
                        className="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white font-mono text-[11px] font-bold uppercase tracking-wider transition rounded-none mt-1"
                      >
                        <span>{isEn ? '🚀 LAUNCH FREE SPARK GENERATOR (GUEST MODE) →' : '🚀 BUKA GENERATOR SPARK (GUEST MODE) →'}</span>
                      </a>
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
                        {isRetailTier 
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
                        {isRetailTier
                          ? (isEn ? 'Company / Brand (Optional)' : 'Perusahaan / Brand (Opsional)')
                          : (isEn ? 'Company / Business Entity *' : 'Perusahaan / Bisnis *')}
                      </label>
                      <input
                        type="text"
                        name="company"
                        value={formData.company}
                        onChange={handleInputChange}
                        placeholder={isEn ? 'e.g. Acme Corp / Freelance' : 'Contoh: PT Inovasi Maju / Mandiri'}
                        required={!isRetailTier}
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-white rounded-none focus:border-emerald-500 focus:outline-hidden"
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                        {isRetailTier
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
                        {isRetailTier
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
                      {isPaidSelfService && (
                        <span className="block text-[10px] text-zinc-500 dark:text-zinc-400 font-mono mt-1">
                          {isEn ? '↳ Official Midtrans transaction receipts & status delivered here' : '↳ Status transaksi & invoice resmi Midtrans dikirimkan ke nomor ini'}
                        </span>
                      )}
                    </div>
                  </div>

                  {/* Kode Voucher (Private / Partner Referral) */}
                  <div>
                    <label className="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                      {isEn ? 'Promo Code / Partner Voucher (Optional)' : 'Kode Promo / Voucher Partner (Opsional)'}
                    </label>
                    <input
                      type="text"
                      name="voucher_code"
                      value={formData.voucher_code}
                      onChange={(e) => setFormData(prev => ({ ...prev, voucher_code: e.target.value.toUpperCase() }))}
                      placeholder={isEn ? 'Enter partner referral code if available' : 'Masukkan jika memiliki kode partner khusus'}
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
                        <span>{isEn ? 'OPENING MIDTRANS PAYMENT SNAP...' : 'MEMBUKA GATEWAY PEMBAYARAN MIDTRANS...'}</span>
                      </>
                    ) : selectedPackage === 'retail_lite' ? (
                      <>
                        <CreditCard className="w-4 h-4" />
                        <span>{isEn ? `PAY NOW VIA MIDTRANS SNAP (RP ${pricingSettings.retail_lite_price || '99.000'}) →` : `BAYAR VIA MIDTRANS SNAP (RP ${pricingSettings.retail_lite_price || '99.000'}) →`}</span>
                      </>
                    ) : selectedPackage === 'retail_pro' ? (
                      <>
                        <Sparkles className="w-4 h-4" />
                        <span>{isEn ? `PAY NOW VIA MIDTRANS SNAP (RP ${pricingSettings.retail_pro_price || '399.000'}) →` : `BAYAR VIA MIDTRANS SNAP (RP ${pricingSettings.retail_pro_price || '399.000'}) →`}</span>
                      </>
                    ) : selectedPackage === 'retail_ultimate' ? (
                      <>
                        <Crown className="w-4 h-4" />
                        <span>{isEn ? `PAY NOW VIA MIDTRANS SNAP (RP ${pricingSettings.retail_ultimate_price || '1.490.000'}) →` : `BAYAR VIA MIDTRANS SNAP (RP ${pricingSettings.retail_ultimate_price || '1.490.000'}) →`}</span>
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
                        <span>{isEn ? 'LOCK SPRINT BATCH & REVIEW CONTRACT (DP 50%) →' : 'KUNCI SLOT BATCH & LANJUTKAN KONTRAK DP 50% →'}</span>
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
                      {isEn ? 'Instant Payment (QRIS / VA Midtrans)' : 'Pembayaran Instan QRIS / VA'}
                    </span>
                    <span className="flex items-center gap-1">
                      <FileText className="w-3 h-3 text-emerald-500" />
                      {isEn ? 'Official Tax Invoice' : 'Faktur Resmi & Kwitansi'}
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
