import React, { useState, useEffect } from 'react';
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
  FileText,
  Sun,
  Moon,
  Globe,
  Lock,
  DollarSign
} from 'lucide-react';

const TRANSLATIONS = {
  id: {
    badge: "Arsitektur Terpusat Modern Monolith",
    execTitle: "1. Executive Technical Discovery",
    execDesc: "Untuk mencapai efisiensi biaya server dan kecepatan peluncuran (Rapid Time-to-Market) tanpa mengorbankan keamanan, kami merekomendasikan infrastruktur Modern Monolith (Laravel 13 & Filament PHP). Sistem ini memungkinkan pengembangan fitur manajemen data secara kilat dengan standar enterprise.",
    adminTitle: "Pusat Kendali (Admin Panel)",
    adminDesc: "Menggunakan Filament PHP untuk merender dasbor kelas enterprise dengan filter data canggih, metrik analitik, dan tabel dinamis seketika.",
    vpsTitle: "Keamanan & Performa (Dedicated VPS)",
    vpsDesc: "Sistem di-host di Virtual Private Server terdedikasi berbasis Nixpacks & Docker untuk menghindari resource sharing, menjamin isolasi data 100%.",
    
    formTitle: "2. Vision Blueprint & Scope Agreement",
    formSubtitle: "Mohon lengkapi kuesioner ini. Data ini akan disintesis menjadi Product Requirements Document (PRD), skema tabel database ERD, dan acuan penguncian kontrak kerja.",
    
    contactTitle: "Informasi Penanggung Jawab Proyek (PIC)",
    clientNameLabel: "Nama Lengkap PIC",
    clientNamePh: "Misal: Budi Santoso",
    emailLabel: "Email Resmi",
    emailPh: "budi@perusahaan.com",
    phoneLabel: "WhatsApp / No. Telepon",
    phonePh: "812-3456-7890",
    phoneNote: "Nomor WhatsApp tanpa angka 0 di awal.",

    blockA: "A. Konteks Bisnis & Tolak Ukur",
    namaBisnisLabel: "Nama Proyek / Bisnis",
    namaBisnisPh: "Misal: Neriah Logistik Hub, Klinik Sehat...",
    masalahLabel: "Masalah Utama yang Ingin Diselesaikan?",
    masalahPh: "Misal: Saat ini pendaftaran masih manual pakai kertas, data sering tercecer dan rekap butuh 3 hari...",
    tujuanLabel: "Tolak Ukur Kesuksesan (KPI)",
    tujuanPh: "Misal: Rekapitulasi menjadi otomatis real-time dan laporan harian bisa diunduh format PDF...",

    blockB: "B. Pengguna & Hak Akses (RBAC)",
    audiensLabel: "Target Audiens / Pengunjung Utama",
    audiensPh: "Misal: Calon pelamar kerja, klien B2B, pelanggan umum...",
    aktorLabel: "Aktor Sistem (Siapa saja yang akan login?)",
    aktorPh: "Misal: 1. Superadmin (Full Control), 2. Staff Operator (Verifikasi), 3. Klien (Isi Formulir & Pantau Status).",

    blockC: "C. Fungsionalitas & User Flow",
    fiturWajibLabel: "Fitur Wajib (Fase 1 - MVP Peluncuran)",
    fiturWajibPh: "Sebutkan poin per poin. Misal: 1. Formulir pendaftaran pelamar, 2. Database tabel filter, 3. Ekspor Excel & PDF...",
    fiturTambahanLabel: "Fitur Tambahan (Fase 2 - Roadmap)",
    fiturTambahanPh: "Misal: Notifikasi WhatsApp otomatis, Integrasi absensi GPS, Dark Mode...",
    alurKerjaLabel: "Alur Kerja Utama (User Flow)",
    alurKerjaPh: "Ceritakan urutannya: Pengguna buka web -> Isi form -> Validasi data -> Admin approve di dasbor...",

    blockD: "D. Integrasi, Timeline & Kesiapan Aset",
    integrasiLabel: "Kebutuhan Integrasi Pihak Ketiga",
    integrasiPh: "Misal: Payment Gateway Midtrans, WhatsApp API, Cloudflare...",
    desainLabel: "Referensi Inspirasi Desain",
    desainPh: "Misal: Linear.app, Vercel, Stripe (Minimalis, Sharp, Clean)...",
    asetLabel: "Kesiapan Aset (Logo, Teks, Foto)",
    asetOptions: ["Belum Siap Sama Sekali", "Sedang Disiapkan Tim Internal", "Sudah Siap Lengkap"],
    
    timelineTitle: "Estimasi Durasi Pengerjaan yang Diminta (Hari Kerja)",
    targetWaktuLabel: "Target Rilis (Deadline)",
    targetWaktuPh: "Misal: Akhir Bulan Ini, ASAP...",
    daysSuffix: "Hari Kerja",

    blockE: "E. Skala, Batasan Negatif & Kepatuhan (Ultimate Scope Lock)",
    skalaLabel: "Target Skala Pengguna / Bulan (Volume Trafik)",
    skalaOptions: [
      "0 - 100.000 Pengguna / Bulan (Dedicated VPS Monolith)",
      "100.000 - 5.000.000 Pengguna / Bulan (Horizontal Scaled Monolith)",
      "> 50.000.000 Pengguna Global Lintas Benua (Decoupled Microservices)"
    ],
    jangkauanLabel: "Jangkauan Pasar & Lokalisasi Geografis",
    jangkauanOptions: [
      "Domestik Indonesia (IDR, Zona WIB/WITA/WIT)",
      "Regional Asia Tenggara (Multi-Currency IDR/SGD/MYR, Bilingual ID/EN)",
      "Global Multi-Benua (Multi-Currency USD/EUR, Multi-Language, GDPR/CCPA)"
    ],
    outOfScopeLabel: "Batasan Negatif (Fitur yang EKSPLISIT TIDAK DIBUAT di Fase 1)",
    outOfScopePh: "Tuliskan apa saja yang TIDAK termasuk dalam MVP Fase 1 ini agar terhindar dari scope creep. Misal: 1. Tidak ada aplikasi native iOS/Android (fokus Web Responsive PWA), 2. Tidak ada multi-gudang luar negeri, 3. Tidak ada integrasi printer thermal fisik Bluetooth di tahap awal...",
    kepatuhanLabel: "Standar Kepatuhan Keamanan & Regulasi",
    kepatuhanOptions: [
      "Standar Web Application & OWASP Top 10 (CSRF, XSS, HTTPS)",
      "Data Pribadi Sensitif & Kepatuhan UU PDP (Enkripsi At-Rest & In-Transit, Audit Trail)",
      "Standar Finansial & Pembayaran Ketat (PCI-DSS Scoped, Escrow Isolation)"
    ],
    budgetLabel: "Kisaran Alokasi Budget Klien (Investasi Sistem)",
    budgetOptions: [
      "Rp 5.000.000 - Rp 15.000.000 (Starter / UMKM / Komunitas / Personal - Lean Modular Web)",
      "Rp 15.000.000 - Rp 35.000.000 (Growth / Custom Business Portal - Multi-Role & Gateway)",
      "Rp 35.000.000 - Rp 75.000.000 (Scale-Up / Platform Komersial - Modern Monolith High-Concurrency)",
      "> Rp 75.000.000 (Enterprise / AI-Augmented / Priority Swarm Ready)"
    ],

    scopeLockNotice: "Perhatian: Fitur yang disetujui dalam kuesioner ini akan dikunci dalam kontrak resmi. Penambahan fitur baru di luar ruang lingkup ini akan diakomodasi melalui Change Request (CR) / Addendum terpisah.",

    submitBtn: "Kunci Blueprint & Generate Ultimate PRD",
    submittingBtn: "Menyusun Blueprint & Skema ERD...",
    
    successTitle: "Transmisi Blueprint Berhasil!",
    successDesc: "Data spesifikasi proyek telah terekam dan disintesis menjadi dokumen PRD & Skema Database ERD standar PostgreSQL ULID.",
    openPrdBtn: "Buka Dokumen Ultimate PRD Sekarang",
    newProjectBtn: "Input Proyek Baru"
  },
  en: {
    badge: "Centralized Architecture Modern Monolith",
    execTitle: "1. Executive Technical Discovery",
    execDesc: "To achieve optimal server cost efficiency and rapid time-to-market without compromising security, we recommend a Modern Monolith infrastructure (Laravel 13 & Filament PHP). This system enables lightning-fast data management and rapid prototyping at enterprise grade.",
    adminTitle: "Command Center (Admin Panel)",
    adminDesc: "Powered by Filament PHP to render enterprise-grade dashboards with dynamic data filtering, analytics metrics, and instant responsive tables.",
    vpsTitle: "Security & Performance (Dedicated VPS)",
    vpsDesc: "Hosted on a dedicated Virtual Private Server via Nixpacks & Docker to eliminate resource sharing and enforce strict 100% data isolation.",
    
    formTitle: "2. Vision Blueprint & Scope Agreement",
    formSubtitle: "Please complete this questionnaire thoroughly. These inputs will synthesize into a comprehensive Product Requirements Document (PRD), ERD schema, and legally binding contract baseline.",
    
    contactTitle: "Project Manager / PIC Contact Details",
    clientNameLabel: "Full Name (PIC)",
    clientNamePh: "e.g. John Doe",
    emailLabel: "Official Email Address",
    emailPh: "john@company.com",
    phoneLabel: "Phone / WhatsApp",
    phonePh: "812-3456-7890",
    phoneNote: "Phone digits without leading 0.",

    blockA: "A. Business Context & Success Metrics",
    namaBisnisLabel: "Project / Business Name",
    namaBisnisPh: "e.g. Apex Logistics Hub, Health Clinic...",
    masalahLabel: "Core Problem to Solve?",
    masalahPh: "e.g. Current manual paper filing causes lost records and reconciliation takes 3 days...",
    tujuanLabel: "Success Metrics (KPIs)",
    tujuanPh: "e.g. Automated real-time data aggregation and instant downloadable PDF audit reports...",

    blockB: "B. Users & Role-Based Access Control",
    audiensLabel: "Target Audience / Primary Visitors",
    audiensPh: "e.g. Job applicants, B2B enterprise clients, general public...",
    aktorLabel: "System Actors (Who will log in?)",
    aktorPh: "e.g. 1. Superadmin (Full Access), 2. Operations Staff (Review), 3. Client (Submission & Tracking).",

    blockC: "C. System Functionality & User Flow",
    fiturWajibLabel: "Mandatory Core Features (Phase 1 - MVP)",
    fiturWajibPh: "List item by item. e.g. 1. Registration form, 2. Filterable database table, 3. Excel & PDF export...",
    fiturTambahanLabel: "Secondary Features (Phase 2 - Roadmap)",
    fiturTambahanPh: "e.g. Automated WhatsApp notifications, GPS attendance integration, Dark Mode...",
    alurKerjaLabel: "Primary User Flow",
    alurKerjaPh: "Detail the steps: User visits portal -> Fills form -> Data validated -> Admin approves in dashboard...",

    blockD: "D. Integrations, Timeline & Asset Readiness",
    integrasiLabel: "Third-Party Integration Needs",
    integrasiPh: "e.g. Midtrans Payment Gateway, WhatsApp API, Cloudflare...",
    desainLabel: "Design Inspiration & Style References",
    desainPh: "e.g. Linear.app, Vercel, Stripe (Minimalist, Sharp, Clean)...",
    asetLabel: "Asset Readiness (Logo, Copy, Media)",
    asetOptions: ["Not Ready At All", "In Preparation by Internal Team", "Fully Ready & Packaged"],
    
    timelineTitle: "Requested Project Duration (Working Days)",
    targetWaktuLabel: "Target Launch Deadline",
    targetWaktuPh: "e.g. End of this month, Q4, ASAP...",
    daysSuffix: "Working Days",

    blockE: "E. Scale Reach, Strict Boundaries & Compliance (Scope Lock)",
    skalaLabel: "Target Monthly Active Users (Scale Reach)",
    skalaOptions: [
      "0 - 100,000 Users / Month (Dedicated VPS Monolith)",
      "100,000 - 5,000,000 Users / Month (Horizontal Scaled Monolith)",
      "> 50,000,000 Global Multi-Continent Users (Decoupled Microservices)"
    ],
    jangkauanLabel: "Market Reach & Geographic Localization",
    jangkauanOptions: [
      "Domestic Indonesia (IDR, WIB/WITA/WIT, PDP Act Compliance)",
      "Southeast Asia Regional (Multi-Currency IDR/SGD/MYR, Bilingual ID/EN)",
      "Global Multi-Continent (Multi-Currency USD/EUR, Multi-Language, GDPR/CCPA)"
    ],
    outOfScopeLabel: "Explicit Out-of-Scope (Features Strictly Excluded from Phase 1)",
    outOfScopePh: "CRITICAL: Explicitly specify what will NOT be built in Phase 1 MVP to avoid scope creep. e.g.: 1. No native iOS/Android apps (focused on Responsive Web PWA), 2. No multi-warehouse international customs calculation, 3. No physical Bluetooth thermal printer integration in initial release.",
    kepatuhanLabel: "Security Compliance & Regulatory Standards",
    kepatuhanOptions: [
      "Standard Web Application & OWASP Top 10 (CSRF, XSS, HTTPS)",
      "Sensitive Personal Data & PDP Act (At-Rest & In-Transit Encryption, Audit Trail)",
      "Strict Financial & Payment Standards (PCI-DSS Scoped, Escrow Isolation)"
    ],
    budgetLabel: "Client Budget Range (System Investment Plan)",
    budgetOptions: [
      "Rp 5,000,000 - Rp 15,000,000 (Starter / SME / Community / Personal - Lean Modular Web)",
      "Rp 15,000,000 - Rp 35,000,000 (Growth / Custom Business Portal - Multi-Role & Gateway)",
      "Rp 35,000,000 - Rp 75,000,000 (Scale-Up / Commercial Platform - Modern Monolith High-Concurrency)",
      "> Rp 75,000,000 (Enterprise / AI-Augmented / Priority Swarm Ready)"
    ],

    scopeLockNotice: "Notice: The scope approved in this questionnaire will be locked into a formal digital contract. Any feature requests outside this document will be handled through a formal Change Request (CR) / Addendum.",

    submitBtn: "Lock Blueprint & Synthesize Ultimate PRD",
    submittingBtn: "Synthesizing Blueprint & ERD Schema...",
    
    successTitle: "Blueprint Transmitted Successfully!",
    successDesc: "Project specifications have been captured and synthesized into an enterprise PRD and PostgreSQL ULID ERD schema.",
    openPrdBtn: "Open Ultimate PRD Document",
    newProjectBtn: "Submit Another Project"
  }
};

const HelperTooltip = ({ content }) => (
  <div className="group relative inline-block ml-2 cursor-help align-middle">
    <AlertCircle className="w-4 h-4 text-zinc-400 hover:text-emerald-500 transition-colors" />
    <div className="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-56 p-3 bg-zinc-900 dark:bg-black text-white text-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-10 font-sans shadow-lg text-left border border-zinc-700">
      <span className="font-bold text-emerald-400">Insight:</span> {content}
    </div>
  </div>
);

export default function ProjectBlueprintIsland({ csrfToken, submitUrl, initialData = {}, countries = [] }) {
  const [lang, setLang] = useState('id');
  const [isDarkMode, setIsDarkMode] = useState(false);

  // Robust Country list with fallback
  const countryList = (countries && Array.isArray(countries) && countries.length > 0)
    ? countries
    : [
        { name: 'Indonesia', code: '+62', emoji: '🇮🇩', iso: 'ID' },
        { name: 'Malaysia', code: '+60', emoji: '🇲🇾', iso: 'MY' },
        { name: 'Singapore', code: '+65', emoji: '🇸🇬', iso: 'SG' },
        { name: 'United States', code: '+1', emoji: '🇺🇸', iso: 'US' },
        { name: 'Australia', code: '+61', emoji: '🇦🇺', iso: 'AU' },
      ];

  useEffect(() => {
    // Detect system or stored theme
    const savedTheme = localStorage.getItem('neriah_theme');
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      setIsDarkMode(true);
      document.documentElement.classList.add('dark');
    } else {
      setIsDarkMode(false);
      document.documentElement.classList.remove('dark');
    }

    // Prefill from URL query params (Simulator handoff)
    if (typeof window !== 'undefined') {
      const searchParams = new URLSearchParams(window.location.search);
      const urlName = searchParams.get('name');
      const urlProblem = searchParams.get('problem');
      if (urlName || urlProblem) {
        setFormData(prev => ({
          ...prev,
          namaBisnis: urlName || prev.namaBisnis,
          masalahUtama: urlProblem || prev.masalahUtama,
        }));
      }
    }
  }, []);

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

  const t = TRANSLATIONS[lang];

  const [formData, setFormData] = useState({
    namaBisnis: initialData.namaBisnis || '',
    clientName: initialData.clientName || '',
    email: initialData.email || '',
    phone: initialData.phone || '',
    masalahUtama: initialData.masalahUtama || '',
    tujuanUtama: initialData.tujuanUtama || '',
    targetAudiens: initialData.targetAudiens || '',
    aktorSistem: initialData.aktorSistem || '',
    fiturWajib: initialData.fiturWajib || '',
    fiturTambahan: initialData.fiturTambahan || '',
    alurKerja: initialData.alurKerja || '',
    kebutuhanIntegrasi: initialData.kebutuhanIntegrasi || '',
    referensiDesain: initialData.referensiDesain || '',
    kesiapanAset: initialData.kesiapanAset || 'Sedang Disiapkan',
    durasiHari: initialData.durasiHari || '30',
    targetWaktu: initialData.targetWaktu || '30 Hari Kerja',
    skalaPengguna: initialData.skalaPengguna || '0 - 100.000 Pengguna / Bulan (Dedicated VPS Monolith)',
    jangkauanPasar: initialData.jangkauanPasar || 'Domestik Indonesia (IDR, Zona WIB/WITA/WIT)',
    outOfScope: initialData.outOfScope || '',
    kepatuhanKeamanan: initialData.kepatuhanKeamanan || 'Standar Web Application & OWASP Top 10 (CSRF, XSS, HTTPS)',
    kisaranBudget: initialData.kisaranBudget || 'Rp 15.000.000 - Rp 35.000.000 (Growth / Custom Business Portal - Multi-Role & Gateway)',
  });

  // Country Code and Phone Digits (Anti-Leading Zero Protocol)
  const [selectedCountryCode, setSelectedCountryCode] = useState(() => {
    if (initialData.country_code) return initialData.country_code;
    return '+62';
  });

  const [phoneDigits, setPhoneDigits] = useState(() => {
    if (initialData.phone) {
      let d = String(initialData.phone).replace(/\D/g, '');
      if (d.startsWith('62')) d = d.substring(2);
      return d.replace(/^0+/, '');
    }
    return '';
  });

  const handleCountryCodeChange = (e) => {
    const code = e.target.value;
    setSelectedCountryCode(code);
    setFormData((prev) => ({
      ...prev,
      phone: phoneDigits ? `${code}${phoneDigits}` : '',
    }));
  };

  const handlePhoneKeyDown = (e) => {
    // Prohibit typing '0' if input is empty or cursor at index 0
    if (e.key === '0' && (e.target.value === '' || e.target.selectionStart === 0)) {
      e.preventDefault();
    }
  };

  const handlePhoneChange = (e) => {
    let raw = e.target.value;
    // Strip non-digits
    let digits = raw.replace(/\D/g, '');
    // Strictly strip any leading zeros (e.g. 0812 -> 812)
    digits = digits.replace(/^0+/, '');

    setPhoneDigits(digits);
    setFormData((prev) => ({
      ...prev,
      phone: digits ? `${selectedCountryCode}${digits}` : '',
    }));
  };

  const handlePhonePaste = (e) => {
    e.preventDefault();
    const pasted = e.clipboardData ? e.clipboardData.getData('text') : '';
    let digits = pasted.replace(/\D/g, '');
    // If pasted with country code e.g. 62812..., strip 62
    if (selectedCountryCode === '+62' && digits.startsWith('62')) {
      digits = digits.substring(2);
    }
    digits = digits.replace(/^0+/, '');

    setPhoneDigits(digits);
    setFormData((prev) => ({
      ...prev,
      phone: digits ? `${selectedCountryCode}${digits}` : '',
    }));
  };

  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState(null);
  const [successData, setSuccessData] = useState(null);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => {
      const updated = { ...prev, [name]: value };
      if (name === 'durasiHari') {
        updated.targetWaktu = `${value} ${t.daysSuffix}`;
      }
      return updated;
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setErrorMessage(null);

    const payload = {
      nama_bisnis: formData.namaBisnis,
      client_name: formData.clientName || formData.namaBisnis,
      email: formData.email,
      phone: phoneDigits ? `${selectedCountryCode}${phoneDigits}` : (formData.phone || ''),
      country_code: selectedCountryCode,
      phone_digits: phoneDigits,
      masalah_utama: formData.masalahUtama,
      tujuan_utama: formData.tujuanUtama,
      target_audiens: formData.targetAudiens,
      aktor_sistem: formData.aktorSistem,
      fitur_wajib: formData.fiturWajib,
      fitur_tambahan: formData.fiturTambahan,
      alur_kerja: formData.alurKerja,
      kebutuhan_integrasi: formData.kebutuhanIntegrasi,
      referensi_desain: formData.referensiDesain,
      kesiapan_aset: formData.kesiapanAset,
      target_waktu: `${formData.durasiHari} ${t.daysSuffix} (${formData.targetWaktu})`,
      skala_pengguna: formData.skalaPengguna,
      jangkauan_pasar: formData.jangkauanPasar,
      out_of_scope: formData.outOfScope,
      kepatuhan_keamanan: formData.kepatuhanKeamanan,
      kisaran_budget: formData.kisaranBudget,
      service_options: ['Web Architecture', 'Rapid Monolith System', 'PostgreSQL ULID', 'Midtrans DP Ready'],
    };

    try {
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
        setSuccessData(result.data);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      } else {
        const errorDetails = result.errors 
          ? Object.values(result.errors).flat().join(', ') 
          : (result.message || 'Terjadi kesalahan saat memproses data.');
        setErrorMessage(errorDetails);
      }
    } catch (err) {
      setErrorMessage('Terjadi gangguan jaringan atau koneksi. Silakan coba sesaat lagi.');
    } finally {
      setIsSubmitting(false);
    }
  };

  // Sharp theme classes
  const panelClass = "bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-none p-6 sm:p-8 transition-colors duration-200";
  const inputClass = "w-full px-4 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-0 outline-none rounded-none transition font-sans";
  const labelClass = "block text-xs font-mono uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5 font-bold";

  if (successData) {
    return (
      <div className="max-w-4xl mx-auto my-12 p-4">
        <div className="bg-white dark:bg-zinc-900 border-2 border-emerald-500 rounded-none p-8 sm:p-10 shadow-none">
          <div className="flex items-center gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-6 mb-6">
            <div className="w-12 h-12 bg-emerald-500 text-black flex items-center justify-center rounded-none font-bold">
              <CheckCircle2 className="w-8 h-8" />
            </div>
            <div>
              <span className="text-xs font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block">
                STATUS: SYNCHRONIZED
              </span>
              <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
                {t.successTitle}
              </h2>
            </div>
          </div>

          <p className="text-zinc-600 dark:text-zinc-400 text-sm leading-relaxed mb-6 font-sans">
            {t.successDesc}
          </p>

          <div className="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 mb-8 font-mono text-xs space-y-2">
            <div className="flex justify-between">
              <span className="text-zinc-500">PROJECT_NAME:</span>
              <span className="text-zinc-900 dark:text-zinc-100 font-bold">{formData.namaBisnis}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-zinc-500">REQUESTED_TIMELINE:</span>
              <span className="text-emerald-600 dark:text-emerald-400 font-bold">{formData.durasiHari} {t.daysSuffix}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-zinc-500">ERD_DATABASE:</span>
              <span className="text-zinc-900 dark:text-zinc-100 font-bold">Enterprise Distributed ULID</span>
            </div>
          </div>

          <div className="flex flex-col sm:flex-row gap-4">
            {successData.redirect_url && (
              <a
                href={successData.redirect_url}
                className="flex-1 bg-emerald-600 hover:bg-emerald-500 text-black font-black uppercase tracking-wider py-4 px-6 rounded-none text-center flex items-center justify-center gap-2 transition"
              >
                <FileText className="w-5 h-5" />
                {t.openPrdBtn}
                <ArrowRight className="w-5 h-5" />
              </a>
            )}
            <button
              type="button"
              onClick={() => { setSuccessData(null); }}
              className="px-6 py-4 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-xs uppercase tracking-wider rounded-none transition"
            >
              {t.newProjectBtn}
            </button>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto py-8 px-4 sm:px-6 font-sans">
      
      {/* TOOLBAR: THEME TOGGLE & LANGUAGE TOGGLE (SHARP BRUTALIST) */}
      <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-8">
        <div className="flex items-center gap-2">
          <span className="w-2.5 h-2.5 bg-emerald-500 rounded-none inline-block"></span>
          <span className="text-xs font-mono uppercase tracking-widest text-zinc-600 dark:text-zinc-400 font-bold">
            NERIAH PRO // ARCHITECTURE PROTOCOL v1.0
          </span>
        </div>

        <div className="flex items-center gap-2">
          {/* Language Switcher */}
          <div className="flex border border-zinc-300 dark:border-zinc-700 rounded-none overflow-hidden font-mono text-xs">
            <button
              type="button"
              onClick={() => setLang('id')}
              className={`px-3 py-1 font-bold transition ${
                lang === 'id' 
                  ? 'bg-zinc-900 text-white dark:bg-emerald-500 dark:text-black' 
                  : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'
              }`}
            >
              ID
            </button>
            <button
              type="button"
              onClick={() => setLang('en')}
              className={`px-3 py-1 font-bold transition ${
                lang === 'en' 
                  ? 'bg-zinc-900 text-white dark:bg-emerald-500 dark:text-black' 
                  : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'
              }`}
            >
              EN
            </button>
          </div>

          {/* Dark / Light Mode Switcher */}
          <button
            type="button"
            onClick={toggleTheme}
            className="p-1.5 border border-zinc-300 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-none transition"
            title="Toggle Dark / Light Mode"
          >
            {isDarkMode ? <Sun className="w-4 h-4 text-amber-400" /> : <Moon className="w-4 h-4 text-zinc-800" />}
          </button>
        </div>
      </div>

      {/* 1. EXECUTIVE TECHNICAL DISCOVERY */}
      <section className={`${panelClass} mb-8`}>
        <div className="flex items-center gap-3 mb-4">
          <div className="w-8 h-8 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black flex items-center justify-center font-mono font-bold text-xs rounded-none">
            01
          </div>
          <div>
            <span className="text-xs font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold">
              {t.badge}
            </span>
            <h2 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {t.execTitle}
            </h2>
          </div>
        </div>

        <p className="text-zinc-600 dark:text-zinc-400 text-sm leading-relaxed mb-6 font-sans">
          {t.execDesc}
        </p>

        <div className="grid md:grid-cols-2 gap-4">
          <div className="bg-zinc-50 dark:bg-zinc-950 p-5 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <div className="flex items-center gap-2 mb-2 font-mono text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
              <ShieldCheck className="w-4 h-4" />
              {t.adminTitle}
            </div>
            <p className="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed font-sans">
              {t.adminDesc}
            </p>
          </div>

          <div className="bg-zinc-50 dark:bg-zinc-950 p-5 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <div className="flex items-center gap-2 mb-2 font-mono text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
              <Zap className="w-4 h-4" />
              {t.vpsTitle}
            </div>
            <p className="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed font-sans">
              {t.vpsDesc}
            </p>
          </div>
        </div>
      </section>

      {/* 2. VISION BLUEPRINT (FORM PRD & SCOPE AGREEMENT) */}
      <section className={panelClass}>
        <div className="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
          <div className="flex items-center gap-3 mb-1">
            <div className="w-8 h-8 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black flex items-center justify-center font-mono font-bold text-xs rounded-none">
              02
            </div>
            <h2 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {t.formTitle}
            </h2>
          </div>
          <p className="text-xs text-zinc-500 dark:text-zinc-400 mt-1 font-sans">
            {t.formSubtitle}
          </p>
        </div>

        {/* AI Synthesis Info Banner */}
        {(initialData?._meta || initialData?.namaBisnis) && (
          <div className="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 text-zinc-900 dark:text-zinc-100 flex items-start gap-3 rounded-none">
            <Sparkles className="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" />
            <div>
              <h4 className="font-bold text-xs text-emerald-600 dark:text-emerald-400 font-mono uppercase tracking-wider mb-1">
                {lang === 'en' ? 'AI Architectural Synthesis Active' : 'Sintesis Arsitektur AI Siap'}
              </h4>
              <p className="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed font-sans">
                {lang === 'en'
                  ? 'Blueprint has been pre-structured from your raw vision and uploaded documents via MarkItDown. Review and adjust any technical parameters below before locking your PRD.'
                  : 'Blueprint telah diisi otomatis berdasarkan analisis visi dan berkas dokumen Anda via MarkItDown. Silakan periksa, sesuaikan, dan lengkapi rincian di bawah ini sebelum mengunci PRD & Kontrak.'}
              </p>
            </div>
          </div>
        )}

        {errorMessage && (
          <div className="mb-6 p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-start gap-3 rounded-none text-xs font-mono">
            <AlertCircle className="w-4 h-4 flex-shrink-0 mt-0.5" />
            <div>{errorMessage}</div>
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-8">
          
          {/* KONTAK PIC */}
          <div className="bg-zinc-50 dark:bg-zinc-950 p-6 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <h3 className="text-xs font-mono uppercase tracking-widest text-zinc-800 dark:text-zinc-200 mb-4 font-bold flex items-center gap-2">
              <Users className="w-4 h-4 text-emerald-500" />
              {t.contactTitle}
            </h3>
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label className={labelClass}>{t.clientNameLabel}</label>
                <input
                  type="text"
                  name="clientName"
                  value={formData.clientName}
                  onChange={handleChange}
                  placeholder={t.clientNamePh}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>{t.emailLabel}</label>
                <input
                  type="email"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                  placeholder={t.emailPh}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>
                  {t.phoneLabel} <span className="text-emerald-500 font-mono text-[10px] font-semibold">({selectedCountryCode})</span>
                </label>
                <div className="flex items-stretch border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 focus-within:border-emerald-500 transition-colors">
                  <select
                    value={selectedCountryCode}
                    onChange={handleCountryCodeChange}
                    className="w-28 sm:w-32 bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 text-xs font-mono px-2 py-2.5 border-r border-zinc-300 dark:border-zinc-700 rounded-none focus:outline-none cursor-pointer"
                    title="Pilih Kode Negara"
                  >
                    {countryList.map((c, idx) => (
                      <option key={`${c.iso || c.code}-${idx}`} value={c.code}>
                        {c.emoji || '🌐'} {c.code} ({c.name})
                      </option>
                    ))}
                  </select>
                  <input
                    type="tel"
                    name="phoneDigits"
                    value={phoneDigits}
                    onKeyDown={handlePhoneKeyDown}
                    onChange={handlePhoneChange}
                    onPaste={handlePhonePaste}
                    placeholder={t.phonePh}
                    className="flex-1 bg-transparent text-zinc-900 dark:text-zinc-100 font-mono text-xs px-3 py-2.5 rounded-none focus:outline-none placeholder-zinc-400"
                    autoComplete="tel-national"
                  />
                </div>
                <p className="text-[10px] font-mono text-zinc-500 dark:text-zinc-400 mt-1 flex items-center gap-1">
                  <span className="text-emerald-600 dark:text-emerald-400 font-bold">✓ {selectedCountryCode}</span>
                  <span>{t.phoneNote}</span>
                </p>
              </div>
            </div>
          </div>

          {/* BLOK A: KONTEKS BISNIS */}
          <div className="bg-zinc-50 dark:bg-zinc-950 p-6 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <h3 className="text-xs font-mono uppercase tracking-widest text-zinc-800 dark:text-zinc-200 mb-4 font-bold">
              {t.blockA}
            </h3>
            <div className="space-y-4">
              <div>
                <label className={labelClass}>
                  {t.namaBisnisLabel} <span className="text-rose-500">*</span>
                </label>
                <input
                  type="text"
                  name="namaBisnis"
                  required
                  value={formData.namaBisnis}
                  onChange={handleChange}
                  placeholder={t.namaBisnisPh}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>
                  {t.masalahLabel} <span className="text-rose-500">*</span>
                  <HelperTooltip content="Jelaskan kendala manual yang paling membuang waktu Anda saat ini." />
                </label>
                <textarea
                  name="masalahUtama"
                  required
                  rows={3}
                  value={formData.masalahUtama}
                  onChange={handleChange}
                  placeholder={t.masalahPh}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>
                  {t.tujuanLabel} <span className="text-rose-500">*</span>
                  <HelperTooltip content="Contoh: Otomatisasi data real-time, cetak invoice otomatis dalam PDF." />
                </label>
                <textarea
                  name="tujuanUtama"
                  required
                  rows={3}
                  value={formData.tujuanUtama}
                  onChange={handleChange}
                  placeholder={t.tujuanPh}
                  className={inputClass}
                />
              </div>
            </div>
          </div>

          {/* BLOK B: PENGGUNA & HAK AKSES */}
          <div className="bg-zinc-50 dark:bg-zinc-950 p-6 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <h3 className="text-xs font-mono uppercase tracking-widest text-zinc-800 dark:text-zinc-200 mb-4 font-bold">
              {t.blockB}
            </h3>
            <div className="space-y-4">
              <div>
                <label className={labelClass}>
                  {t.audiensLabel} <span className="text-rose-500">*</span>
                  <HelperTooltip content="Siapa yang akan menggunakan aplikasi ini? Misalnya: B2B Klien, Publik, atau Karyawan Internal." />
                </label>
                <input
                  type="text"
                  name="targetAudiens"
                  required
                  value={formData.targetAudiens}
                  onChange={handleChange}
                  placeholder={t.audiensPh}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>
                  {t.aktorLabel} <span className="text-rose-500">*</span>
                  <HelperTooltip content="Sebutkan tipe user (Role). Contoh: Super Admin, Kasir, Manager, dan Pelanggan." />
                </label>
                <textarea
                  name="aktorSistem"
                  required
                  rows={3}
                  value={formData.aktorSistem}
                  onChange={handleChange}
                  placeholder={t.aktorPh}
                  className={inputClass}
                />
              </div>
            </div>
          </div>

          {/* BLOK C: FUNGSIONALITAS & ALUR KERJA */}
          <div className="bg-zinc-50 dark:bg-zinc-950 p-6 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <h3 className="text-xs font-mono uppercase tracking-widest text-zinc-800 dark:text-zinc-200 mb-4 font-bold">
              {t.blockC}
            </h3>
            <div className="space-y-4">
              <div>
                <label className={labelClass}>
                  {t.fiturWajibLabel} <span className="text-rose-500">*</span>
                  <HelperTooltip content="Daftar fitur mutlak untuk Peluncuran Tahap 1 (MVP). Jangan masukkan fitur 'nice-to-have' di sini." />
                </label>
                <textarea
                  name="fiturWajib"
                  required
                  rows={4}
                  value={formData.fiturWajib}
                  onChange={handleChange}
                  placeholder={t.fiturWajibPh}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>
                  {t.fiturTambahanLabel}
                  <HelperTooltip content="Fitur yang bagus ada tapi bisa ditunda ke Fase 2 setelah MVP rilis." />
                </label>
                <textarea
                  name="fiturTambahan"
                  rows={3}
                  value={formData.fiturTambahan}
                  onChange={handleChange}
                  placeholder={t.fiturTambahanPh}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>
                  {t.alurKerjaLabel} <span className="text-rose-500">*</span>
                  <HelperTooltip content="Jelaskan 1-3 langkah utama user dari masuk aplikasi hingga selesai melakukan transaksi/tugas." />
                </label>
                <textarea
                  name="alurKerja"
                  required
                  rows={4}
                  value={formData.alurKerja}
                  onChange={handleChange}
                  placeholder={t.alurKerjaPh}
                  className={inputClass}
                />
              </div>
            </div>
          </div>

          {/* BLOK D: INTEGRASI, TIMELINE & KESIAPAN ASET */}
          <div className="bg-zinc-50 dark:bg-zinc-950 p-6 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <h3 className="text-xs font-mono uppercase tracking-widest text-zinc-800 dark:text-zinc-200 mb-4 font-bold">
              {t.blockD}
            </h3>
            <div className="space-y-4">
              <div>
                <label className={labelClass}>
                  {t.integrasiLabel}
                </label>
                <input
                  type="text"
                  name="kebutuhanIntegrasi"
                  value={formData.kebutuhanIntegrasi}
                  onChange={handleChange}
                  placeholder={t.integrasiPh}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>
                  {t.desainLabel}
                </label>
                <input
                  type="text"
                  name="referensiDesain"
                  value={formData.referensiDesain}
                  onChange={handleChange}
                  placeholder={t.desainPh}
                  className={inputClass}
                />
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>{t.asetLabel}</label>
                  <select
                    name="kesiapanAset"
                    value={formData.kesiapanAset}
                    onChange={handleChange}
                    className={inputClass}
                  >
                    {t.asetOptions.map((opt) => (
                      <option key={opt} value={opt}>{opt}</option>
                    ))}
                  </select>
                </div>

                {/* EXPLICIT WORKING DAYS INPUT */}
                <div>
                  <label className={labelClass}>
                    {t.timelineTitle} <span className="text-emerald-500">*</span>
                  </label>
                  <div className="flex">
                    <input
                      type="number"
                      min="7"
                      max="180"
                      name="durasiHari"
                      required
                      value={formData.durasiHari}
                      onChange={handleChange}
                      className={`${inputClass} font-mono font-bold text-base text-emerald-600 dark:text-emerald-400`}
                    />
                    <span className="inline-flex items-center px-4 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-xs uppercase tracking-wider font-bold">
                      {t.daysSuffix}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* BLOK E: SKALA, BATASAN NEGATIF & KEPATUHAN (ULTIMATE SCOPE LOCK) */}
          <div className="bg-zinc-50 dark:bg-zinc-950 p-6 border-2 border-emerald-500/40 dark:border-emerald-500/30 rounded-none">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-xs font-mono uppercase tracking-widest text-zinc-900 dark:text-zinc-100 font-bold flex items-center gap-2">
                <span className="w-2 h-2 bg-emerald-500"></span>
                {t.blockE}
              </h3>
              <span className="text-[10px] font-mono px-2 py-0.5 bg-emerald-500 text-black font-bold">
                ANTI-SCOPE CREEP
              </span>
            </div>

            <div className="space-y-4">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>
                    {t.skalaLabel} <span className="text-emerald-500">*</span>
                    <HelperTooltip content="Pilih estimasi volume pengunjung untuk menentukan apakah arsitektur Monolith cukup atau butuh kluster terpisah." />
                  </label>
                  <select
                    name="skalaPengguna"
                    value={formData.skalaPengguna}
                    onChange={handleChange}
                    className={inputClass}
                  >
                    {t.skalaOptions.map((opt) => (
                      <option key={opt} value={opt}>{opt}</option>
                    ))}
                  </select>
                </div>

                <div>
                  <label className={labelClass}>
                    {t.jangkauanLabel} <span className="text-emerald-500">*</span>
                    <HelperTooltip content="Menentukan kebutuhan multi-currency, multi-bahasa, dan lokasi server (latency proximity)." />
                  </label>
                  <select
                    name="jangkauanPasar"
                    value={formData.jangkauanPasar}
                    onChange={handleChange}
                    className={inputClass}
                  >
                    {t.jangkauanOptions.map((opt) => (
                      <option key={opt} value={opt}>{opt}</option>
                    ))}
                  </select>
                </div>
              </div>

              <div>
                <label className={labelClass}>
                  {t.outOfScopeLabel} <span className="text-rose-500">*</span>
                  <HelperTooltip content="Kunci batas ruang lingkup! Fitur yang ditulis di sini secara eksplisit TIDAK akan dikerjakan di Fase 1 untuk menjamin deadline tercapai." />
                </label>
                <textarea
                  name="outOfScope"
                  rows={3}
                  value={formData.outOfScope}
                  onChange={handleChange}
                  placeholder={t.outOfScopePh}
                  className={`${inputClass} border-rose-300 dark:border-rose-900/40 focus:border-rose-500`}
                />
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>
                    {t.kepatuhanLabel}
                    <HelperTooltip content="Standar audit kepatuhan enkripsi dan penyimpanan data (UU PDP / PCI-DSS)." />
                  </label>
                  <select
                    name="kepatuhanKeamanan"
                    value={formData.kepatuhanKeamanan}
                    onChange={handleChange}
                    className={inputClass}
                  >
                    {t.kepatuhanOptions.map((opt) => (
                      <option key={opt} value={opt}>{opt}</option>
                    ))}
                  </select>
                </div>

                <div>
                  <label className={labelClass}>
                    {t.budgetLabel} <span className="text-emerald-500">*</span>
                    <HelperTooltip content="Memandu rekomendasi arsitektur agar tidak terjadi pemborosan dana untuk server microservices yang belum diperlukan." />
                  </label>
                  <select
                    name="kisaranBudget"
                    value={formData.kisaranBudget}
                    onChange={handleChange}
                    className={inputClass}
                  >
                    {t.budgetOptions.map((opt) => (
                      <option key={opt} value={opt}>{opt}</option>
                    ))}
                  </select>
                </div>
              </div>
            </div>
          </div>

          {/* SCOPE LOCK & CONTRACT NOTICE */}
          <div className="p-4 border-l-4 border-amber-500 bg-amber-50 dark:bg-amber-950/20 text-amber-900 dark:text-amber-200 text-xs font-mono leading-relaxed rounded-none">
            <div className="flex items-center gap-2 font-bold mb-1">
              <Lock className="w-4 h-4 text-amber-600 dark:text-amber-400" />
              <span>LEGAL PROTOCOL & SCOPE FREEZE</span>
            </div>
            {t.scopeLockNotice}
          </div>

          {/* SUBMIT BUTTON */}
          <button
            type="submit"
            disabled={isSubmitting}
            className={`w-full bg-zinc-900 hover:bg-black dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white dark:text-black font-black uppercase tracking-widest py-4 px-6 rounded-none transition flex items-center justify-center gap-3 text-sm ${
              isSubmitting ? 'opacity-70 cursor-not-allowed' : ''
            }`}
          >
            {isSubmitting ? (
              <>
                <div className="w-5 h-5 border-2 border-current border-t-transparent animate-spin"></div>
                <span>{t.submittingBtn}</span>
              </>
            ) : (
              <>
                <Sparkles className="w-4 h-4" />
                <span>{t.submitBtn}</span>
                <ArrowRight className="w-4 h-4" />
              </>
            )}
          </button>
        </form>
      </section>
    </div>
  );
}
