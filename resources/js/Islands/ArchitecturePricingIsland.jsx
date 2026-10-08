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
  
  // Feature flag check for CV Pro
  const isCvProEnabled = Boolean(featureFlags?.enable_cv_pro);

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
    <div className="w-full bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 py-10 sm:py-16 transition-colors">
      <div className="max-w-7xl mx-auto px-4 sm:px-6">

        {/* DYNAMIC MODULE SWITCHER (PROJECT OS VS UPCOMING CV PRO) */}
        {isCvProEnabled && (
          <div className="flex items-center justify-center mb-8">
            <div className="inline-flex p-1 bg-zinc-200 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs shadow-inner font-mono text-xs">
              <button
                type="button"
                onClick={() => setActiveTab('software')}
                className={`px-4 py-2 font-bold uppercase tracking-wider transition rounded-xs flex items-center gap-2 cursor-pointer ${
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
                className={`px-4 py-2 font-bold uppercase tracking-wider transition rounded-xs flex items-center gap-2 cursor-pointer ${
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
          <div className="max-w-2xl mx-auto my-12 p-8 bg-zinc-100 dark:bg-zinc-900 border-2 border-dashed border-purple-500/40 rounded-xs text-center space-y-4">
            <div className="w-12 h-12 mx-auto rounded-xs bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-purple-500">
              <FileText className="w-6 h-6" />
            </div>
            <h3 className="text-xl font-black uppercase text-zinc-900 dark:text-white">
              CV Pro Studio &bull; {isEn ? 'Module Under Active Development (Q4)' : 'Modul Dalam Pengembangan (Q4)'}
            </h3>
            <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
              {isEn 
                ? 'CV Pro Studio is currently under active sprint development for Q4 release. During our Midtrans merchant compliance review, only Project OS digital architecture services are actively processed for transactions.'
                : 'Modul CV Pro Studio sedang dalam persiapan rilis Q4. Selama periode review kepatuhan merchant Midtrans, hanya layanan rekayasa arsitektur Project OS yang aktif diproses untuk transaksi.'}
            </p>
            <div className="pt-2">
              <button
                type="button"
                onClick={() => setActiveTab('software')}
                className="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-bold text-xs uppercase tracking-wider rounded-xs cursor-pointer transition"
              >
                &larr; {isEn ? 'View Active Project OS Services' : 'Lihat Layanan Aktif Project OS'}
              </button>
            </div>
          </div>
        ) : (
          <>
            {/* 1. CLEAN & CONCISE SINGLE HEADER SECTION */}
            <div className="text-center max-w-3xl mx-auto mb-8">
              <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-mono text-xs uppercase tracking-wider font-bold mb-3 rounded-xs">
                <Sparkles className="w-3.5 h-3.5" />
                <span>{isEn ? 'TRANSPARENT VALUE-BASED PRICING' : 'SKEMA INVESTASI TRANSPARAN & TERSTANDAR'}</span>
              </div>
              
              <h1 className="text-2xl sm:text-4xl font-black uppercase tracking-tight font-sans text-zinc-900 dark:text-white mb-2">
                {isEn ? 'Digital Architecture & Engineering Pricing' : 'Investasi Layanan Rekayasa Sistem'}
              </h1>
              
              <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed">
                {isEn 
                  ? 'Standardized software engineering investment: From instant self-service architectural blueprints to full turnkey Monolith MVP contracts.' 
                  : 'Pilihan investasi rekayasa perangkat lunak terstandarisasi untuk founder & pengembang: Dari cetak biru mandiri (Self-Service) hingga koding penuh turnkey Studio Monolith MVP.'}
              </p>

              {/* COMPACT ENGINEERING ASSURANCE STRIP */}
              <div className="mt-4 py-2 px-3 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex flex-wrap items-center justify-center gap-4 text-[11px] font-mono text-zinc-600 dark:text-zinc-400 rounded-xs">
                <div className="flex items-center gap-1.5">
                  <ShieldCheck className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                  <span>{isEn ? 'Legal Scope-Locked Contract' : 'Kontrak Hukum Scope-Locked'}</span>
                </div>
                <div className="flex items-center gap-1.5">
                  <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                  <span>{isEn ? '50% Milestone DP via Midtrans' : 'DP 50% via Midtrans Snap'}</span>
                </div>
                <div className="flex items-center gap-1.5">
                  <Clock className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                  <span>{isEn ? 'Capacity: Max 2 Projects / Cycle' : 'Kapasitas: Maks. 2 Proyek / Siklus'}</span>
                </div>
              </div>
            </div>

            <div className="mb-20">
              {/* 2. PUNCHY & STREAMLINED 100% SELF-SERVICE NOTICE */}
            <div className="mb-8 p-3.5 sm:p-4 bg-amber-500/10 border border-amber-500/30 text-zinc-900 dark:text-zinc-100 rounded-xs shadow-xs">
              <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-1.5">
                <div className="flex items-center gap-2">
                  <span className="px-1.5 py-0.5 bg-amber-500 text-black font-black text-[10px] font-mono rounded-xs">
                    {isEn ? '⚠️ IMPORTANT' : '⚠️ PENTING'}
                  </span>
                  <span className="font-mono text-xs font-black uppercase tracking-wider text-amber-600 dark:text-amber-400">
                    {isEn ? 'TIERS 01-04: 100% SELF-SERVICE // ZERO NERIAH PRO CODING' : 'PAKET 01-04: 100% SELF-SERVICE // TANPA KODING DARI NERIAH PRO'}
                  </span>
                </div>
                <span className="font-mono text-[10px] px-2 py-0.5 bg-amber-500/20 text-amber-700 dark:text-amber-300 font-bold uppercase rounded-xs">
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
            {/* SPARK / FREE TIER */}
            <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 p-5 sm:p-6 flex flex-col justify-between rounded-none hover:border-cyan-500/60 transition group relative">
              <div>
                <div className="flex items-center justify-between mb-3">
                  <span className="font-mono text-[10px] font-black tracking-wider uppercase text-zinc-500">
                    TIER 01 // AUDIT
                  </span>
                  <span className="px-2 py-0.5 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-mono text-[9px] font-bold">
                    {isEn ? 'FREE GUEST TIER' : 'GRATIS MODE TAMU'}
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
                    <span className="text-[10px] text-zinc-500 font-mono block">
                      {isEn ? 'Digital Access Fee:' : 'Tarif Akses Digital:'}
                    </span>
                    <div className="flex items-baseline gap-1">
                      <span className="text-2xl font-black font-mono text-zinc-900 dark:text-white">
                        Rp {pricingSettings.retail_spark_price || '0'}
                      </span>
                      <span className="text-[10px] font-mono text-emerald-500 font-bold">
                        {isEn ? '/ FREE' : '/ GRATIS'}
                      </span>
                    </div>
                  </div>

                  <div className="pt-2 border-t border-zinc-200 dark:border-zinc-800 text-[10px] font-mono space-y-1">
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Execution:' : 'Pengerjaan:'}</span>
                      <span className="font-bold text-cyan-600 dark:text-cyan-400">
                        {isEn ? '100% Self-Service' : '100% Mandiri'}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Quota Limit:' : 'Batas Kuota:'}</span>
                      <span className="font-bold text-zinc-900 dark:text-white">
                        {isEn ? '2 Idea Audits / Month' : (pricingSettings.retail_spark_limit || '2x Audit / Bulan')}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Reset Cycle:' : 'Siklus Reset:'}</span>
                      <span className="font-bold text-emerald-600 dark:text-emerald-400">
                        {isEn ? '1st of Every Month' : 'Tiap Tgl 1 Awal Bulan'}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Validity Period:' : 'Masa Berlaku:'}</span>
                      <span className="font-bold text-zinc-700 dark:text-zinc-300">
                        {isEn ? '7-Day Guest Session' : '7 Hari Sesi Tamu'}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Login Requirement:' : 'Syarat Login:'}</span>
                      <span className="font-bold text-emerald-600 dark:text-emerald-400">
                        {isEn ? 'No Login Required (Guest)' : 'Tanpa Login (Tamu)'}
                      </span>
                    </div>
                  </div>
                </div>

                <div className="space-y-2 mb-6">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                    {isEn ? 'DELIVERABLES INCLUDED:' : 'OUTPUT SPESIFIKASI DIDAPATKAN:'}
                  </span>
                  {(isEn ? [
                    'Business Viability Analysis & Problem Framing',
                    'Executive Summary & Target Audience Definition',
                    'Top 5 Priority Essential MVP Features',
                    'Complexity Rating & Initial TCO Estimation',
                    'Export Markdown Summary to Local Device',
                  ] : [
                    'Analisis Kelayakan Bisnis & Problem Framing',
                    'Executive Summary & Target Audiens',
                    '5 Fitur Esensial MVP Prioritas',
                    'Estimasi Kompleksitas & TCO Awal',
                    'Ekspor Ringkasan Markdown ke Lokal',
                  ]).map((f, i) => (
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
                    <span className="text-[10px] text-zinc-500 font-mono block">
                      {isEn ? 'Digital License Fee:' : 'Biaya Lisensi Digital:'}
                    </span>
                    <div className="flex items-baseline gap-1">
                      <span className="text-2xl font-black font-mono text-zinc-900 dark:text-white">
                        Rp {pricingSettings.retail_lite_price || '99.000'}
                      </span>
                      <span className="text-[10px] font-mono text-zinc-500">/ project</span>
                    </div>
                  </div>

                  <div className="pt-2 border-t border-zinc-200 dark:border-zinc-800 text-[10px] font-mono space-y-1">
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Execution:' : 'Pengerjaan:'}</span>
                      <span className="font-bold text-cyan-600 dark:text-cyan-400">
                        {isEn ? '100% Self-Service' : '100% Mandiri'}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Quota Limit:' : 'Batas Kuota:'}</span>
                      <span className="font-bold text-zinc-900 dark:text-white">
                        {isEn ? '1 Project (26-Param PRD)' : (pricingSettings.retail_lite_limit || '1 Proyek PRD 26 Param')}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Reset Cycle:' : 'Siklus Reset:'}</span>
                      <span className="font-bold text-zinc-700 dark:text-zinc-300">
                        {isEn ? 'One-Time License (1 Project)' : 'Sekali Bayar (1 Proyek)'}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Validity Period:' : 'Masa Berlaku:'}</span>
                      <span className="font-bold text-emerald-600 dark:text-emerald-400">
                        {isEn ? 'Lifetime Download + 30-Day Rev' : 'Unduh Selamanya + 30hr Rev'}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Login Requirement:' : 'Syarat Login:'}</span>
                      <span className="font-bold text-amber-600 dark:text-amber-400">
                        {isEn ? 'Account Login Required' : 'Wajib Login Akun'}
                      </span>
                    </div>
                  </div>
                </div>

                <div className="space-y-2 mb-6">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                    {isEn ? 'DELIVERABLES INCLUDED:' : 'OUTPUT SPESIFIKASI DIDAPATKAN:'}
                  </span>
                  {(isEn ? [
                    'All Spark Tier Deliverables',
                    'Full 26-Parameter PRD (JSON & Markdown)',
                    'PostgreSQL Strict ULID DDL SQL Schema',
                    'O(1) Keyset & Cursor Pagination Standard',
                    'Work Breakdown Structure (WBS) 2 Sprints',
                  ] : [
                    'Semua Output Spark Tier',
                    'PRD 26 Parameter Lengkap (JSON & MD)',
                    'Skema PostgreSQL Strict ULID DDL SQL',
                    'Standar Keyset O(1) Pagination Rules',
                    'Work Breakdown Structure (WBS) 2 Sprint',
                  ]).map((f, i) => (
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

                <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-1">
                  Pro Production PRD
                </h3>

                <p className="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
                  {isEn ? 'Production blueprint, 6 Mermaid diagrams, Decoupled & Monolith matrix, WBS 5 Sprints.' : 'Cetak biru produksi, 6 diagram Mermaid, matriks arsitektur Decoupled & Monolith, serta WBS 5 sprint.'}
                </p>

                <div className="mb-4 p-3 bg-emerald-500/5 border border-emerald-500/30 space-y-2">
                  <div>
                    <span className="text-[10px] text-zinc-500 font-mono block">
                      {isEn ? 'Digital License Fee:' : 'Biaya Lisensi Digital:'}
                    </span>
                    <div className="flex items-baseline gap-1">
                      <span className="text-2xl font-black font-mono text-zinc-900 dark:text-white">
                        Rp {pricingSettings.retail_pro_price || '399.000'}
                      </span>
                      <span className="text-[10px] font-mono text-zinc-500">/ project</span>
                    </div>
                  </div>

                  <div className="pt-2 border-t border-emerald-500/20 text-[10px] font-mono space-y-1">
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Execution:' : 'Pengerjaan:'}</span>
                      <span className="font-bold text-emerald-500">
                        {isEn ? '100% Self-Service' : '100% Mandiri'}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Quota Limit:' : 'Batas Kuota:'}</span>
                      <span className="font-bold text-zinc-900 dark:text-white">
                        {isEn ? '1 Project (PRD + 5 Sprints WBS)' : (pricingSettings.retail_pro_limit || '1 Proyek PRD + WBS 5 Sprint')}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Reset Cycle:' : 'Siklus Reset:'}</span>
                      <span className="font-bold text-zinc-700 dark:text-zinc-300">
                        {isEn ? 'One-Time License (1 Project)' : 'Sekali Bayar (1 Proyek)'}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Validity Period:' : 'Masa Berlaku:'}</span>
                      <span className="font-bold text-emerald-600 dark:text-emerald-400">
                        {isEn ? 'Lifetime Download + 6 Months AI' : 'Unduh Selamanya + 6bln AI'}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Login Requirement:' : 'Syarat Login:'}</span>
                      <span className="font-bold text-amber-600 dark:text-amber-400">
                        {isEn ? 'Account Login Required' : 'Wajib Login Akun'}
                      </span>
                    </div>
                  </div>
                </div>

                <div className="space-y-2 mb-6">
                  <span className="text-[10px] font-mono font-bold text-emerald-500 uppercase tracking-wider block">
                    {isEn ? 'DELIVERABLES INCLUDED:' : 'OUTPUT SPESIFIKASI DIDAPATKAN:'}
                  </span>
                  {(isEn ? [
                    'All Lite Tier Deliverables',
                    'AI Code-Gen Prompt Ready (.cursorrules)',
                    'White-Label Agency Export',
                    'Decoupled 2026+ Blueprint (Next.js 15, Cloudflare)',
                    '6 Mermaid Diagrams (ERD, Data Flow, Sequence, Gantt)',
                    'WBS 5 Sprints Linear / Jira Ready',
                    'OpenAPI 3.1 & Idempotency Specification',
                    'Anti-AI-Slop & UI Design Tokens Guidelines',
                  ] : [
                    'Semua Output Lite Tier',
                    'AI Code-Gen Prompt Ready (.cursorrules)',
                    'White-Label Agency Export',
                    'Cetak Biru Decoupled 2026+ (Next.js 15, Cloudflare)',
                    '6 Diagram Mermaid (ERD, Data Flow, Sequence, Gantt)',
                    'WBS 5 Sprint Linear / Jira Ready',
                    'OpenAPI 3.1 & Idempotency Specification',
                    'Panduan Anti-AI-Slop & UI Design Tokens',
                  ]).map((f, i) => (
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
                    {isEn ? '+ 1-ON-1 ARCHITECT CALL' : '+ SESI 1-ON-1 LEAD ARCHITECT'}
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
                    <span className="text-[10px] text-zinc-500 font-mono block">
                      {isEn ? 'Advisory Investment:' : 'Investasi Advisory:'}
                    </span>
                    <div className="flex items-baseline gap-1">
                      <span className="text-2xl font-black font-mono text-zinc-900 dark:text-white">
                        Rp {pricingSettings.retail_ultimate_price || '1.490.000'}
                      </span>
                      <span className="text-[10px] font-mono text-zinc-500">/ project</span>
                    </div>
                  </div>

                  <div className="pt-2 border-t border-zinc-200 dark:border-zinc-800 text-[10px] font-mono space-y-1">
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Execution:' : 'Pengerjaan:'}</span>
                      <span className="font-bold text-amber-600 dark:text-amber-400">
                        {isEn ? 'Self-Service + 1-on-1 Call' : 'Mandiri + Sesi 1-on-1'}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Quota Limit:' : 'Batas Kuota:'}</span>
                      <span className="font-bold text-zinc-900 dark:text-white">
                        {isEn ? '1 Enterprise Project' : (pricingSettings.retail_ultimate_limit || '1 Proyek Enterprise')}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Reset Cycle:' : 'Siklus Reset:'}</span>
                      <span className="font-bold text-zinc-700 dark:text-zinc-300">
                        {isEn ? 'One-Time License (1 Project)' : 'Sekali Bayar (1 Proyek)'}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Validity Period:' : 'Masa Berlaku:'}</span>
                      <span className="font-bold text-emerald-600 dark:text-emerald-400">
                        {isEn ? 'Lifetime Download + 1 Year Updates' : 'Unduh Selamanya + 1th Update'}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                      <span>{isEn ? 'Login Requirement:' : 'Syarat Login:'}</span>
                      <span className="font-bold text-amber-600 dark:text-amber-400">
                        {isEn ? 'Verified Account Required' : 'Wajib Akun Terverifikasi'}
                      </span>
                    </div>
                  </div>
                </div>

                <div className="space-y-2 mb-6">
                  <span className="text-[10px] font-mono font-bold text-amber-500 uppercase tracking-wider block">
                    {isEn ? 'DELIVERABLES INCLUDED:' : 'OUTPUT SPESIFIKASI DIDAPATKAN:'}
                  </span>
                  {(isEn ? [
                    'All Pro Production Tier Deliverables',
                    'AI Multi-Model Failover Token Shield Strategy',
                    'Zero-Trust CORS & Anti-Malware Hardening',
                    '1 Scheduled 60-Min Architecture Call (Google Meet)',
                    'Validation & Review by Internal Engineering Team',
                    'Corporate Non-Disclosure Agreement (NDA)',
                  ] : [
                    'Semua Output Pro Production Tier',
                    'AI Multi-Model Failover Token Shield Strategy',
                    'Zero-Trust CORS & Anti-Malware Hardening',
                    '1 Sesi 60 Menit Architecture Call (Google Meet)',
                    'Validasi & Review Tim Engineering Internal',
                    'Non-Disclosure Agreement (NDA) Korporat',
                  ]).map((f, i) => (
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
                    {isEn ? 'SCENARIO 1 // ADVISORY STUDIO' : 'SKENARIO 1 // ADVISORY STUDIO'}
                  </span>
                  <span className="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-[10px] font-bold">
                    {isEn ? 'ONE-TIME INVESTMENT' : 'INVESTASI SATU KALI'}
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
                  <span className="text-xs text-zinc-500 dark:text-zinc-400 font-mono block mb-1">
                    {isEn ? 'Total Advisory Service Fee:' : 'Total Biaya Jasa Advisory:'}
                  </span>
                  <div className="flex items-baseline gap-2">
                    <span className="text-3xl sm:text-4xl font-black font-mono text-zinc-900 dark:text-white">
                      Rp {pricingSettings.advisory_price || '2.500.000'}
                    </span>
                    <span className="text-xs font-mono text-zinc-500">{isEn ? '/ project' : '/ proyek'}</span>
                  </div>
                  <span className="text-[11px] text-emerald-600 dark:text-emerald-400 font-mono font-bold mt-1 block">
                    &bull; {isEn ? 'Includes full PRD synthesis + ERD schema + Scoping Discovery' : 'Termasuk PRD 26 parameter + Skema DDL + Sesi Scoping'}
                  </span>
                  <div className="mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-800 text-[10px] font-mono text-zinc-500">
                    <span>
                      {isEn ? (
                        <>Coding Execution: <strong>Executed by Client Dev Team (Advisory fee deducted from 50% Down Payment if continuing to Full MVP)</strong></>
                      ) : (
                        <>Pengerjaan Koding: <strong>Dieksekusi Tim Klien Sendiri (Biaya Rp 2.5jt memotong DP 50% jika lanjut Full MVP)</strong></>
                      )}
                    </span>
                  </div>
                </div>

                <div className="space-y-3 mb-8">
                  <div className="font-mono text-[11px] font-bold text-zinc-400 uppercase tracking-wider">
                    {isEn ? 'DELIVERABLES INCLUDED:' : 'OUTPUT SPESIFIKASI DIDAPATKAN:'}
                  </div>

                  {(isEn ? [
                    'Comprehensive 26-Parameter PRD (Functional, Non-Functional, NFR)',
                    'PostgreSQL Strict ULID Database Schema (Ready-to-Import DDL SQL)',
                    'O(1) Keyset & Keyset Cursor Pagination Architectural Guide',
                    'System Architecture & End-to-End Data Flow Diagrams',
                    'Work Breakdown Structure (WBS) 5 Sprints Jira/Linear Ready',
                    'Security, Anti-Malware & DDoS Hardening Checklist',
                    '100% Intellectual Property Ownership & Corporate NDA',
                  ] : [
                    'PRD 26 Parameter Lengkap (Fungsional, Non-Fungsional, NFR)',
                    'Skema Database PostgreSQL Strict ULID (DDL SQL Siap Pakai)',
                    'Standar O(1) Keyset & Cursor Pagination Guide',
                    'Diagram Alur Sistem (System Architecture & Data Flow)',
                    'Work Breakdown Structure (WBS) 5 Sprint Jira/Linear Ready',
                    'Security & Anti-Malware / DDoS Hardening Checklist',
                    '100% Hak Milik Dokumen & Non-Disclosure Agreement (NDA)',
                  ]).map((feat, idx) => (
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
                <span>{isEn ? 'EXECUTED 100% BY NERIAH PRO // FULL MVP' : 'DIKERJAKAN 100% OLEH NERIAH PRO // FULL MVP'}</span>
              </div>

              <div>
                <div className="flex items-center justify-between mb-4 mt-2">
                  <span className="font-mono text-xs font-black tracking-wider uppercase text-emerald-600 dark:text-emerald-400">
                    {isEn ? 'SCENARIO 2 // FULL MONOLITH' : 'SKENARIO 2 // FULL MONOLITH'}
                  </span>
                  <span className="px-2 py-0.5 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-mono text-[10px] font-bold">
                    {isEn ? '50% MILESTONE DP' : 'DP 50% MILESTONE'}
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
                  <span className="text-xs text-zinc-500 dark:text-zinc-400 font-mono block mb-1">
                    {isEn ? 'Full Development Contract Value:' : 'Nilai Kontrak Pengembangan Penuh:'}
                  </span>
                  <div className="flex items-baseline gap-2">
                    <span className="text-3xl sm:text-4xl font-black font-mono text-zinc-900 dark:text-white">
                      Rp {pricingSettings.mvp_price || '50.000.000'}
                    </span>
                  </div>
                  <div className="flex items-center justify-between mt-2 pt-2 border-t border-emerald-500/20">
                    <span className="text-xs font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                      {isEn ? 'Down Payment (50% DP):' : 'Uang Muka (DP 50%):'}
                    </span>
                    <span className="text-sm font-mono font-black text-emerald-500">
                      Rp 25.000.000
                    </span>
                  </div>
                  <span className="text-[10px] text-zinc-500 font-mono mt-1 block">
                    {isEn ? '• Remaining 50% settled upon UAT & Live Production Deploy' : '• Pelunasan sisa 50% setelah UAT & Live Production Deploy'}
                  </span>
                  <div className="mt-2 pt-2 border-t border-emerald-500/20 text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                    <span>
                      {isEn 
                        ? 'Coding Execution: 100% Executed by Neriah Pro Senior Software Architects & Engineers' 
                        : 'Pengerjaan Koding: 100% Dikerjakan oleh Software Architect & Engineer Neriah Pro'}
                    </span>
                  </div>
                </div>

                <div className="space-y-3 mb-8">
                  <div className="font-mono text-[11px] font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-1">
                    <Sparkles className="w-3.5 h-3.5" />
                    <span>{isEn ? 'EVERYTHING IN BLUEPRINT PLUS:' : 'SEMUA OUTPUT BLUEPRINT DITAMBAH:'}</span>
                  </div>

                  {(isEn ? [
                    'Full-Stack Modern Monolith (Laravel 13, Filament v5, React 19)',
                    'Digital Scope-Locked Legal Contract & Security Architecture',
                    'Secure 50% Milestone Down Payment via Midtrans / Bank Escrow',
                    'Dedicated VPS Hardening, Nginx Tuning & Redis Setup',
                    'Automated Test Suite (Pest PHP Unit & Feature Tests)',
                    'Payment Gateway, WhatsApp API & Email Gateway Integration',
                    '100% Source Code & Client Server Credentials Handover',
                    'Full 3-Month Priority SLA Bug Warranty & Maintenance',
                  ] : [
                    'Full-Stack Modern Monolith (Laravel 13, Filament v5, React 19)',
                    'Kontrak Hukum Digital Scope-Locked & Legal Security',
                    'Pembayaran DP 50% Aman via Midtrans / Bank Escrow',
                    'Dedicated VPS Hardening, Nginx Tuning, & Redis Setup',
                    'Automated Test Suite (Pest PHP Unit & Feature Tests)',
                    'Integrasi Payment Gateway, WhatsApp API, & Email Gateway',
                    '100% Penyerahan Source Code & Akun Server Klien',
                    'Garansi Perbaikan Bug & SLA Prioritas 3 Bulan Penuh',
                  ]).map((feat, idx) => (
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
                  <span>{isEn ? 'START 5 SPRINT DEVELOPMENT (50% DP)' : 'RESERVASI SPRINT PROYEK (DP 50%)'}</span>
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
                    {isEn ? 'STIMULUS PROGRAM // LOCAL BUSINESS' : 'PROGRAM STIMULUS // UMKM'}
                  </span>
                  <span className="px-2 py-0.5 bg-amber-500/10 text-amber-500 border border-amber-500/20 font-mono text-[10px] font-bold">
                    {isEn ? '50% SUBSIDY // EXECUTED BY NERIAH PRO' : 'SUBSIDI 50% // DIKERJAKAN NERIAH PRO'}
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
                  <span className="text-xs text-zinc-500 dark:text-zinc-400 font-mono block mb-1">
                    {isEn ? 'Standard Initial Investment:' : 'Investasi Awal Normal:'}
                  </span>
                  <div className="flex items-baseline gap-2">
                    <span className="text-2xl sm:text-3xl font-black font-mono text-zinc-900 dark:text-white">
                      Rp {pricingSettings.umkm_price || '7.500.000'}
                    </span>
                  </div>
                  <div className="mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                    <span className="text-[11px] font-mono text-zinc-500">
                      {isEn ? 'With Business Subsidy (50%):' : 'Dengan Subsidi UMKM (50%):'}
                    </span>
                    <span className="text-xs font-mono font-black text-amber-500">
                      Rp 3.750.000
                    </span>
                  </div>
                  <span className="text-[10px] text-amber-500 font-mono mt-1 block">
                    &bull; {isEn ? 'Limited community subsidy quota (2 business slots / month)' : 'Program subsidi terbatas (Alokasi 2 kuota usaha / bulan)'}
                  </span>
                  <div className="mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-800 text-[10px] font-mono text-amber-600 dark:text-amber-400 font-bold">
                    <span>
                      {isEn 
                        ? 'Coding Execution: 100% Executed by Neriah Pro Team Turnkey Ready' 
                        : 'Pengerjaan Koding: 100% Dikerjakan oleh Tim Neriah Pro sampai Siap Pakai'}
                    </span>
                  </div>
                </div>

                <div className="space-y-3 mb-8">
                  <div className="font-mono text-[11px] font-bold text-zinc-400 uppercase tracking-wider">
                    {isEn ? 'PACKAGE HIGHLIGHTS:' : 'FITUR UTAMA DIDAPATKAN:'}
                  </div>

                  {(isEn ? [
                    'Centralized Transaction Engine & Customer Database',
                    'Automated QRIS & Bank Transfer Payment Integration',
                    'Admin Dashboard Filament v5 (Bilingual Native ID/EN)',
                    'Automated Sales Reports Export (Excel / PDF)',
                    'Real-Time WhatsApp Order Confirmation Notifications',
                    'Business Domain Setup (.id / .com) & Fast Cloud Hosting',
                    'Dashboard Training Session via Zoom / Video Guide',
                  ] : [
                    'Engine Transaksi & Database Pelanggan Terpusat',
                    'Integrasi Pembayaran Otomatis QRIS & Transfer Bank',
                    'Admin Dashboard Filament v5 Bahasa Indonesia',
                    'Ekspor Laporan Penjualan Excel / PDF Otomatis',
                    'Notifikasi WhatsApp Konfirmasi Pesanan Real-Time',
                    'Setup Domain Bisnis (.id / .com) & Hosting Cepat',
                    'Pelatihan Penggunaan Dashboard via Zoom / Panduan Video',
                  ]).map((feat, idx) => (
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
                  <span>{isEn ? 'CLAIM 50% SUBSIDY' : 'KLAIM SUBSIDI UMKM (50%)'}</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </button>

                <a
                  href={`https://wa.me/${whatsappNumber}?text=${encodeURIComponent('Halo Lead Architect Neriah Pro, saya ingin konsultasi mengenai Program Subsidi UMKM Digital Starter.')}`}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="w-full bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 py-2.5 px-4 font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none text-center"
                >
                  <MessageSquare className="w-3.5 h-3.5 text-amber-500" />
                  <span>{isEn ? 'CONSULT BUSINESS NEEDS' : 'KONSULTASI KEBUTUHAN UMKM'}</span>
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
                    {isEn ? '1 Full Project (Max 2 Projects/Cycle)' : '1 Proyek Penuh (Kapasitas Maks. 2 Proyek/Siklus)'}
                  </td>
                  <td className="py-3 px-3 text-zinc-700 dark:text-zinc-300">
                    {isEn ? '1 Business System (Monthly Subsidy)' : '1 Sistem Usaha (Alokasi Subsidi Bulanan)'}
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
