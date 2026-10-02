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
  CheckCheck
} from 'lucide-react';

const TRANSLATIONS = {
  id: {
    topBadge: "PROJECT OS // ARCHITECTURAL DISCOVERY WORKSPACE",
    headerTitle: "Blueprint Arsitektur & Kuesioner Spesifikasi Sistem",
    headerSubtitle: "Lengkapi 20 parameter arsitektur di bawah ini untuk menghasilkan dokumen Ultimate PRD dan skema database PostgreSQL Strict ULID siap bangun. Anda dapat mengetik langsung, atau gunakan Ruang Ide & MarkItDown di bawah untuk mengisi otomatis.",
    
    // Auto-save & Status
    autoSaveSaved: "Tersimpan Otomatis",
    autoSaveSaving: "Menyimpan ke Cloud...",
    autoSaveLocal: "Tersimpan di Perangkat",
    readinessLabel: "Skor Kesiapan Spesifikasi:",
    aiTierLabel: "AI Tier: Gemini Flash (Efisien & Zero-Boncos)",

    // Quick Idea & MarkItDown Studio
    studioToggleOpen: "Tutup Ruang Ide & Dokumen MarkItDown",
    studioToggleClosed: "Buka Ruang Ide Cepat & Unggah Dokumen (Isi Otomatis 20 Field)",
    studioBadge: "AI ACCELERATOR // MICROSOFT MARKITDOWN REPLICA",
    studioTitle: "Ceritakan Visi Proyek atau Lampirkan Dokumen Spesifikasi",
    studioDesc: "Ketik ringkasan bisnis atau lampirkan dokumen (PDF, Word, Excel, PowerPoint, Wireframe). Dokumen otomatis dikonversi ke format Markdown (.md) murni oleh MarkItDown secara lokal di server sebelum dibaca AI, memangkas >80% token ekstraksi biner dan otomatis mengisi 20 field di bawah ini.",
    
    textareaPlaceholder: "Ceritakan ide, proses bisnis, atau kebutuhan aplikasi Anda di sini...\n\nContoh: 'Saya ingin membangun sistem ekspedisi dan armada logistik antar pulau. Ada 3 level pengguna: Superadmin di kantor pusat, Koordinator Lapangan, dan Driver truk. Driver bisa scan barcode resi dan update status kirim via foto. Klien bisa lacak posisi resi real-time. Ada integrasi Midtrans untuk pembayaran invoice tempo dan notifikasi otomatis ke WhatsApp...'",
    
    charCount: "Karakter",
    sampleTemplates: "Contoh Templat Ide Cepat:",
    tplLogistics: "🚚 Manajemen Armada Truk & Pelacakan Resi Logistik",
    tplClinic: "🏥 Sistem Informasi Klinik & Rekam Medis Pasien Terpadu",
    tplRental: "🏢 Marketplace Rental Alat Berat B2B & Escrow Tagihan Tempo",
    tplHr: "💼 HR Portal, Presensi GPS, Evaluasi KPI & Penggajian Terpadu",

    dropzoneTitle: "Lampirkan Dokumen Spesifikasi atau Sketsa Wireframe",
    dropzoneSubtitle: "Tarik & lepaskan berkas ke sini, atau klik untuk memilih berkas",
    dropzoneFormats: "Dukungan Format: PDF, DOCX, XLSX, PPTX, CSV, TXT, MD, PNG, JPG (Maks. 5 berkas, @15MB)",
    markitdownNotice: "⚡ Fakta Token: Dokumen biner (PDF/Office) diekstraksi ke Markdown (.md) secara lokal di server. AI murni membaca teks bersih tanpa overhead XML/biner, menghemat >80% token AI.",

    synthesizeBtn: "✨ Sintesis Ide & Isi 20 Parameter Form ↓",
    synthesizingBtn: "Menganalisis & Mengisi Form...",

    step1: "Mengonversi berkas dokumen via MarkItDown ke Markdown (.md)...",
    step2: "Menganalisis domain bisnis, aktor RBAC & alur kerja...",
    step3: "Menyusun skema arsitektur & mengisi 20 parameter blueprint...",

    // Proactive Guidance
    proactiveBadge: "ASISTEN PROAKTIF AI // PANDUAN KELENGKAPAN SPESIFIKASI",
    proactiveTitle: "Rekomendasi Cerdas untuk Menyempurnakan Arsitektur",
    proactiveDesc: "Berikut adalah aspek penting yang sering terlewatkan dalam spesifikasi perangkat lunak. Klik salah satu rekomendasi untuk langsung menyematkannya ke dalam form di bawah ini.",
    suggestionsTitle: "💡 Rekomendasi Fitur & Aspek Kritis (Klik untuk Menambahkan):",
    coPilotPlaceholder: "Punya ide tambahan atau ingin melengkapi sesuatu? Ketik di sini (misal: 'Tambahkan alur pembatalan pesanan dan notifikasi WA driver')...",
    coPilotSubmitBtn: "✨ Lengkapi via AI Flash",
    coPilotProcessing: "AI Memproses Penempatan...",

    // 5 Architectural Blocks
    blockA: "BLOK A: IDENTITAS PROYEK & TUJUAN BISNIS",
    blockADesc: "Fondasi domain bisnis, masalah utama yang dihadapi, dan tolak ukur keberhasilan.",
    
    blockB: "BLOK B: TARGET PENGGUNA & RBAC (ROLE-BASED ACCESS CONTROL)",
    blockBDesc: "Pemetaan profil pengguna akhir dan matriks wewenang operasional sistem.",

    blockC: "BLOK C: FITUR INTI MVP & ALUR KERJA (USER FLOW)",
    blockCDesc: "Daftar fitur prioritas Fase 1, roadmap masa depan, serta alur operasional langkah demi langkah.",

    blockD: "BLOK D: INTEGRASI, ESTETIKA & TIMELINE EKSEKUSI",
    blockDDesc: "Koneksi gateway eksternal, referensi antarmuka UI/UX, dan target durasi pengerjaan.",

    blockE: "BLOK E: SKALA INFRASTRUKTUR, KEAMANAN & BATASAN RUANG LINGKUP",
    blockEDesc: "Kapasitas trafik, standar keamanan OWASP, kepatuhan hukum, dan proteksi dari scope creep.",

    blockF: "BLOK F: PENGESAHAN KONTAK PENANGGUNG JAWAB (PIC)",
    blockFDesc: "Identitas pemegang wewenang proyek untuk pengesahan digital dan kontrak resmi.",

    // Action Buttons
    saveBtn: "Simpan",
    cancelBtn: "Batal",
    deleteBtn: "Hapus",
    lockBtn: "Kunci Blueprint & Terbitkan Dokumen Ultimate PRD",
    lockingBtn: "Mengunci Blueprint & Menerbitkan Dokumen PRD...",

    successTitle: "Blueprint Berhasil Disinkronkan!",
    successDesc: "Spesifikasi arsitektur proyek telah dikunci dan dokumen Ultimate PRD dengan skema PostgreSQL Strict ULID siap diunduh.",
    openPrdBtn: "Buka Dokumen Ultimate PRD",
    newProjectBtn: "Kirim Proyek Lainnya",
    daysSuffix: "Hari Kerja",
  },
  en: {
    topBadge: "PROJECT OS // ARCHITECTURAL DISCOVERY WORKSPACE",
    headerTitle: "Architecture Blueprint & System Specification Form",
    headerSubtitle: "Complete the 20 architectural parameters below to generate the Ultimate PRD document and PostgreSQL Strict ULID schema. You can type directly, or use the Quick Idea Studio & MarkItDown uploader below to auto-fill.",
    
    // Auto-save & Status
    autoSaveSaved: "Auto-Saved",
    autoSaveSaving: "Saving to Cloud...",
    autoSaveLocal: "Saved on Device",
    readinessLabel: "Specification Readiness Score:",
    aiTierLabel: "AI Tier: Gemini Flash (Token-Efficient & Zero-Waste)",

    // Quick Idea & MarkItDown Studio
    studioToggleOpen: "Collapse Idea Studio & Documents",
    studioToggleClosed: "Open Quick Idea Studio & Document Uploader (Auto-Fill 20 Fields)",
    studioBadge: "AI ACCELERATOR // MICROSOFT MARKITDOWN REPLICA",
    studioTitle: "Describe Project Vision or Attach Specification Documents",
    studioDesc: "Type your vision or attach documents (PDF, Word, Excel, PowerPoint, Wireframes). Documents are automatically converted into pure Markdown (.md) by MarkItDown locally on the server before AI ingestion, saving >80% tokens and auto-populating all 20 fields below.",
    
    textareaPlaceholder: "Describe your project vision, workflow, or business requirements here...\n\nExample: 'We need an inter-island freight logistics & fleet management system. 3 user roles: Superadmin at HQ, Field Dispatcher, and Truck Drivers. Drivers scan barcode waybills and update delivery status via photo. Clients track consignments in real-time. Integrated Midtrans for invoice payments and automated WhatsApp alerts...'",
    
    charCount: "Characters",
    sampleTemplates: "Quick Idea Templates:",
    tplLogistics: "🚚 Freight Logistics & Waybill Tracking Platform",
    tplClinic: "🏥 Integrated Medical Clinic & Electronic Health Records",
    tplRental: "🏢 B2B Heavy Machinery Rental & Invoiced Escrow",
    tplHr: "💼 HR Portal, GPS Attendance, KPI Review & Payroll",

    dropzoneTitle: "Attach Specification Documents or Wireframe Sketches",
    dropzoneSubtitle: "Drag & drop files here, or click to browse",
    dropzoneFormats: "Supported: PDF, DOCX, XLSX, PPTX, CSV, TXT, MD, PNG, JPG (Max 5 files, @15MB)",
    markitdownNotice: "⚡ Token Reality: Binary files (PDF/Office) are converted to clean Markdown (.md) locally. AI reads clean text without XML/binary bloat, slashing >80% tokens.",

    synthesizeBtn: "✨ Synthesize Idea & Auto-Fill 20 Fields ↓",
    synthesizingBtn: "Analyzing & Populating...",

    step1: "Converting documents to Markdown (.md) via MarkItDown...",
    step2: "Analyzing business domain, RBAC actors & user flows...",
    step3: "Structuring architecture & populating 20 blueprint parameters...",

    // Proactive Guidance
    proactiveBadge: "PROACTIVE AI ASSISTANT // SPECIFICATION COMPLETENESS",
    proactiveTitle: "Smart Architectural Recommendations",
    proactiveDesc: "Here are critical requirements commonly overlooked in enterprise specifications. Click any suggestion to instantly append it into the form below.",
    suggestionsTitle: "💡 Critical Features & Recommended Aspects (Click to Add):",
    coPilotPlaceholder: "Have additional requirements? Type here (e.g. 'Add cancellation flow and driver WhatsApp notification')...",
    coPilotSubmitBtn: "✨ Append via AI Flash",
    coPilotProcessing: "AI Positioning Addition...",

    // 5 Architectural Blocks
    blockA: "BLOCK A: PROJECT IDENTITY & BUSINESS GOALS",
    blockADesc: "Business domain foundation, core problem statement, and primary KPIs.",
    
    blockB: "BLOCK B: TARGET USERS & RBAC (ROLE-BASED ACCESS CONTROL)",
    blockBDesc: "End-user demographics and operational access authority matrix.",

    blockC: "BLOCK C: CORE MVP FEATURES & WORKFLOW (USER FLOW)",
    blockCDesc: "Phase 1 priority features, roadmap expansion, and step-by-step operational flow.",

    blockD: "BLOCK D: INTEGRATIONS, AESTHETICS & TIMELINE",
    blockDDesc: "External gateways, UI/UX benchmark references, and target delivery duration.",

    blockE: "BLOCK E: INFRASTRUCTURE SCALE, SECURITY & SCOPE BOUNDARIES",
    blockEDesc: "Traffic concurrency, OWASP security standards, legal compliance, and anti scope-creep boundaries.",

    blockF: "BLOCK F: PROJECT MANAGER (PIC) CONTACT VERIFICATION",
    blockFDesc: "Authorized PIC identity for digital sign-off and official project agreement.",

    // Action Buttons
    saveBtn: "Save",
    cancelBtn: "Cancel",
    deleteBtn: "Delete",
    lockBtn: "Lock Blueprint & Generate Ultimate PRD Document",
    lockingBtn: "Locking Blueprint & Issuing PRD Document...",

    successTitle: "Blueprint Synchronized Successfully!",
    successDesc: "Project specifications have been locked into an enterprise Ultimate PRD and PostgreSQL Strict ULID schema.",
    openPrdBtn: "Open Ultimate PRD Document",
    newProjectBtn: "Submit Another Project",
    daysSuffix: "Working Days",
  }
};

// Helper: Parse string list into array of clean strings
function parseNumberedList(text) {
  if (!text) return [];
  return text
    .split(/\n+/)
    .map(line => line.replace(/^\d+[\.\)]\s*/, '').trim())
    .filter(Boolean);
}

// Helper: Convert array of strings into numbered string
function stringifyNumberedList(items) {
  if (!items || !items.length) return '';
  return items.map((it, idx) => `${idx + 1}. ${it}`).join('\n');
}

// Helper: Parse comma list into array
function parseCommaList(text) {
  if (!text) return [];
  return text
    .split(/,\s*/)
    .map(s => s.trim())
    .filter(Boolean);
}

// Helper: Convert array to comma list
function stringifyCommaList(items) {
  if (!items || !items.length) return '';
  return items.join(', ');
}

// Dynamic completeness score calculator
function calculateCompleteness(data, lang) {
  const isEn = lang === 'en';
  const checklist = [
    { key: 'namaBisnis', label: isEn ? 'Project Name' : 'Nama Proyek', weight: 8, completed: !!data.namaBisnis && data.namaBisnis.length >= 3 },
    { key: 'masalahUtama', label: isEn ? 'Core Problem' : 'Masalah Bisnis', weight: 10, completed: !!data.masalahUtama && data.masalahUtama.length >= 15 },
    { key: 'tujuanUtama', label: isEn ? 'Success Metrics (KPIs)' : 'Tolak Ukur Sukses (KPI)', weight: 8, completed: !!data.tujuanUtama && data.tujuanUtama.length >= 10 },
    { key: 'targetAudiens', label: isEn ? 'Target Audience' : 'Target Audiens', weight: 6, completed: !!data.targetAudiens && data.targetAudiens.length >= 5 },
    { key: 'aktorSistem', label: isEn ? 'System Actors & RBAC' : 'Pengguna & Aktor RBAC', weight: 12, completed: !!data.aktorSistem && data.aktorSistem.length >= 10 },
    { key: 'fiturWajib', label: isEn ? 'MVP Features (Phase 1)' : 'Fitur Wajib MVP (Fase 1)', weight: 20, completed: !!data.fiturWajib && data.fiturWajib.length >= 20 },
    { key: 'fiturTambahan', label: isEn ? 'Roadmap (Phase 2)' : 'Roadmap Fitur (Fase 2)', weight: 6, completed: !!data.fiturTambahan && data.fiturTambahan.length >= 10 },
    { key: 'alurKerja', label: isEn ? 'User Workflow' : 'Alur Kerja Utama (User Flow)', weight: 12, completed: !!data.alurKerja && data.alurKerja.length >= 15 },
    { key: 'kebutuhanIntegrasi', label: isEn ? 'Third-Party Integrations' : 'Integrasi Pihak Ketiga', weight: 8, completed: !!data.kebutuhanIntegrasi && data.kebutuhanIntegrasi.length >= 4 },
    { key: 'outOfScope', label: isEn ? 'Negative Scope (Anti Creep)' : 'Batasan Negatif (Out of Scope)', weight: 6, completed: !!data.outOfScope && data.outOfScope.length >= 10 },
    { key: 'clientName', label: isEn ? 'PIC Name' : 'Nama Lengkap PIC', weight: 4, completed: !!data.clientName && data.clientName.length >= 3 },
  ];

  let score = 0;
  checklist.forEach(item => {
    if (item.completed) score += item.weight;
  });

  const status = isEn
    ? (score >= 90 ? 'Ready to Lock' : (score >= 70 ? 'Substantially Complete' : 'Needs More Detail'))
    : (score >= 90 ? 'Sangat Siap Dikunci' : (score >= 70 ? 'Hampir Sempurna' : 'Perlu Dilengkapi'));

  return { score: Math.min(100, score), status, checklist };
}

export default function ProjectBlueprintIsland({ csrfToken, submitUrl, initialData = {}, countries = [] }) {
  const [lang, setLang] = useState('id');
  const [isDarkMode, setIsDarkMode] = useState(false);
  const t = TRANSLATIONS[lang];

  // Country code selector
  const countryList = (countries && Array.isArray(countries) && countries.length > 0)
    ? countries
    : [
        { name: 'Indonesia', code: '+62', emoji: '🇮🇩', iso: 'ID' },
        { name: 'Malaysia', code: '+60', emoji: '🇲🇾', iso: 'MY' },
        { name: 'Singapore', code: '+65', emoji: '🇸🇬', iso: 'SG' },
        { name: 'United States', code: '+1', emoji: '🇺🇸', iso: 'US' },
        { name: 'Australia', code: '+61', emoji: '🇦🇺', iso: 'AU' },
        { name: 'United Kingdom', code: '+44', emoji: '🇬🇧', iso: 'GB' },
        { name: 'Japan', code: '+81', emoji: '🇯🇵', iso: 'JP' },
      ];

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

  // Top Idea Studio Open/Closed State
  const [isStudioOpen, setIsStudioOpen] = useState(true);

  // Textarea & Dropzone State
  const [ideaText, setIdeaText] = useState(() => initialData?._meta?.raw_idea_text || '');
  const [attachedFiles, setAttachedFiles] = useState([]);
  const [convertedMarkdown, setConvertedMarkdown] = useState(() => initialData?._meta?.converted_markdown || '');
  const [isAnalyzing, setIsAnalyzing] = useState(false);
  const [analysisStep, setAnalysisStep] = useState('');
  const [analysisError, setAnalysisError] = useState(null);
  const [copySuccess, setCopySuccess] = useState(false);

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
        id: 'payment_midtrans',
        category: 'integration',
        title: 'Midtrans Payment Gateway (QRIS & VA)',
        desc: lang === 'en' ? 'Multi-channel payment with automated webhooks' : 'Pembayaran otomatis via Virtual Account & QRIS',
        target_field: 'kebutuhanIntegrasi',
        addition: 'Midtrans Payment Gateway (Snap API, QRIS, Virtual Account BCA/Mandiri/BRI)',
        badge: 'Pembayaran',
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
    ];
  });

  const [appliedSuggestionIds, setAppliedSuggestionIds] = useState(new Set());
  const [coPilotInput, setCoPilotInput] = useState('');
  const [isCoPilotLoading, setIsCoPilotLoading] = useState(false);

  // In-Card Interactive Add Helpers
  const [activeAddKey, setActiveAddKey] = useState(null);
  const [quickInputText, setQuickInputText] = useState('');

  // Toast Notification State
  const [toast, setToast] = useState(null);

  const showLocalToast = useCallback((type, message, title = '') => {
    if (window.showToast) {
      window.showToast({ type, message, title });
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

  // Auto-Save Telemetry
  const [autoSaveStatus, setAutoSaveStatus] = useState('idle'); // 'idle' | 'saving' | 'saved' | 'error'
  const [lastSavedTime, setLastSavedTime] = useState(() => {
    const tz = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    return `${tz} WIB`;
  });
  const autoSaveDebounceRef = useRef(null);
  const draftIdRef = useRef(initialData._draft_id || null);

  // Full 20 Fields Form Data (Single Source of Truth)
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
    referensiDesain: initialData.referensiDesain || 'Clean Modern Monolith (Linear.app & Stripe inspired), sharp rectangular borders, dark/light mode fidelity.',
    kesiapanAset: initialData.kesiapanAset || 'Sedang Disiapkan Tim Internal',
    durasiHari: initialData.durasiHari || '30',
    targetWaktu: initialData.targetWaktu || '30 Hari Kerja',
    skalaPengguna: initialData.skalaPengguna || '0 - 100.000 Pengguna / Bulan (Dedicated VPS Monolith)',
    jangkauanPasar: initialData.jangkauanPasar || 'Domestik Indonesia (IDR, Zona WIB/WITA/WIT)',
    outOfScope: initialData.outOfScope || '',
    kepatuhanKeamanan: initialData.kepatuhanKeamanan || 'Standar Web Application & OWASP Top 10 (CSRF, XSS, HTTPS)',
    kisaranBudget: initialData.kisaranBudget || 'Rp 15.000.000 - Rp 35.000.000 (Growth / Custom Business Portal - Multi-Role & Gateway)',
  });

  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState(null);
  const [successData, setSuccessData] = useState(null);

  // Dynamic Completeness
  const completeness = useMemo(() => {
    return calculateCompleteness(formData, lang);
  }, [formData, lang]);

  // Initial local storage hydration
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

    // LocalStorage Draft Restoration if initialData is empty
    if (!initialData.namaBisnis) {
      try {
        const localSaved = localStorage.getItem('neriah_blueprint_autosave');
        if (localSaved) {
          const parsed = JSON.parse(localSaved);
          if (parsed && typeof parsed === 'object' && parsed.namaBisnis) {
            setFormData(prev => ({ ...prev, ...parsed }));
            if (parsed.phone) {
              let d = String(parsed.phone).replace(/\D/g, '');
              if (d.startsWith('62')) d = d.substring(2);
              setPhoneDigits(d.replace(/^0+/, ''));
            }
            showLocalToast('info', 'Draft spesifikasi sebelumnya berhasil dimuat otomatis.', 'PULIHKAN DRAFT');
          }
        }
      } catch (e) {
        console.warn('Failed to parse localStorage blueprint draft', e);
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

  // Dual-Tier Auto-Save: LocalStorage (Immediate) + Server Debounce (1500ms)
  const triggerAutoSave = useCallback((updatedData) => {
    // 1. Immediate LocalStorage save
    try {
      localStorage.setItem('neriah_blueprint_autosave', JSON.stringify(updatedData));
    } catch (e) {}

    // 2. Debounced server save
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
          }),
        });

        if (response.ok) {
          const result = await response.json();
          if (result.draft_id) draftIdRef.current = result.draft_id;
          if (result.saved_at) setLastSavedTime(result.saved_at);
          setAutoSaveStatus('saved');
        } else {
          setAutoSaveStatus('saved'); // Still saved locally
        }
      } catch (err) {
        setAutoSaveStatus('saved'); // LocalStorage is primary safety net
      }
    }, 1500);
  }, [csrfToken]);

  // Form Field Updater
  const updateField = (field, value) => {
    setFormData(prev => {
      const next = { ...prev, [field]: value };
      triggerAutoSave(next);
      return next;
    });
  };

  // Phone input sanitizer
  const handlePhoneDigitsChange = (e) => {
    let clean = e.target.value.replace(/\D/g, '');
    clean = clean.replace(/^0+/, '');
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

  // Synthesize Idea & Fill All 20 Fields
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

    setIsAnalyzing(true);
    setAnalysisStep(attachedFiles.length > 0 ? t.step1 : t.step2);

    try {
      const token = csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const formDataUpload = new FormData();
      formDataUpload.append('idea_text', cleanText);
      formDataUpload.append('_hp_check', honeypot);
      formDataUpload.append('_website', honeypotWebsite);
      formDataUpload.append('locale', lang);

      attachedFiles.forEach(file => {
        formDataUpload.append('files[]', file);
      });

      const timer = setTimeout(() => {
        setAnalysisStep(t.step3);
      }, 1400);

      const response = await fetch('/api/blueprint/analyze-idea', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
        },
        body: formDataUpload
      });

      clearTimeout(timer);

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw new Error(result.message || (lang === 'en' ? 'Failed to process idea.' : 'Gagal memproses ide.'));
      }

      // Populate synthesized blueprint data into all 20 fields
      const synData = result.data || {};
      const merged = {
        ...formData,
        ...synData,
        clientName: synData.clientName || formData.clientName,
        email: synData.email || formData.email,
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
      
      // Smoothly scroll down to Block A so user can see all 20 fields populated
      const blockAEl = document.getElementById('section-block-a');
      if (blockAEl) {
        blockAEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }

      showLocalToast(
        'success',
        lang === 'en' 
          ? 'All 20 Blueprint parameters populated by AI! Review and fine-tune below.' 
          : '20 Parameter Blueprint berhasil diisi otomatis oleh AI! Silakan tinjau dan sesuaikan di bawah.',
        'SINTESIS SUKSES'
      );

    } catch (err) {
      setAnalysisError(err.message || (lang === 'en' ? 'An error occurred. Please try again.' : 'Terjadi kendala. Silakan coba lagi.'));
    } finally {
      setIsAnalyzing(false);
      setAnalysisStep('');
    }
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
    if (appliedSuggestionIds.has(suggestion.id)) return;

    const targetField = suggestion.target_field || 'fiturWajib';
    const addition = suggestion.addition;

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

  // Final Blueprint Submission (Generate Contract & PRD)
  const handleFinalSubmit = async (e) => {
    e?.preventDefault();
    setErrorMessage(null);

    // Form validation
    if (!formData.clientName || !formData.email || !formData.namaBisnis) {
      setErrorMessage(
        lang === 'en'
          ? 'Please complete the Project Name, PIC Name, and PIC Email.'
          : 'Mohon lengkapi Nama Proyek, Nama PIC, dan Email PIC.'
      );
      window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
      return;
    }

    if (!formData.phone) {
      setErrorMessage(
        lang === 'en'
          ? 'Please enter a valid WhatsApp / Phone number for verification.'
          : 'Mohon isi nomor WhatsApp aktif untuk verifikasi.'
      );
      return;
    }

    setIsSubmitting(true);

    try {
      const payload = {
        ...formData,
        phone: formData.phone,
        country_code: selectedCountryCode,
        _hp_check: honeypot,
        _website: honeypotWebsite,
        locale: lang,
      };

      const response = await fetch(submitUrl || '/blueprint/store', {
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
        // Clear local storage after successful lock
        try { localStorage.removeItem('neriah_blueprint_autosave'); } catch (e) {}
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

  const panelClass = "bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-none p-6 sm:p-8 transition-colors duration-200";
  const inputClass = "w-full px-4 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-0 outline-none rounded-none transition font-sans";
  const labelClass = "block text-xs font-mono uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5 font-bold";

  // SUCCESS VIEW
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
                STATUS: SYNCHRONIZED & LOCKED
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
              <span className="text-zinc-500">PIC_NAME:</span>
              <span className="text-zinc-900 dark:text-zinc-100 font-bold">{formData.clientName}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-zinc-500">REQUESTED_TIMELINE:</span>
              <span className="text-emerald-600 dark:text-emerald-400 font-bold">{formData.durasiHari} {t.daysSuffix}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-zinc-500">ERD_DATABASE:</span>
              <span className="text-zinc-900 dark:text-zinc-100 font-bold">PostgreSQL Strict ULID (O(1) Keyset Cursor)</span>
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
    <div className="max-w-4xl mx-auto py-8 px-4 sm:px-6 font-sans relative">

      {/* FLOATING TOAST NOTIFICATION */}
      <AnimatePresence>
        {toast && (
          <motion.div
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
            className={`fixed top-4 right-4 z-50 p-4 border max-w-sm rounded-none font-mono text-xs shadow-lg flex items-start gap-3 ${
              toast.type === 'error'
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
      
      {/* TOP TOOLBAR: BRANDING, THEME, LANGUAGE & LIVE AUTO-SAVE TELEMETRY */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
        <div>
          <div className="flex items-center gap-2 mb-1">
            <span className="w-2.5 h-2.5 bg-emerald-500 rounded-none inline-block"></span>
            <span className="text-[11px] font-mono uppercase tracking-widest text-zinc-600 dark:text-zinc-400 font-bold">
              {t.topBadge}
            </span>
          </div>
          <h1 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
            {t.headerTitle}
          </h1>
        </div>

        <div className="flex flex-wrap items-center gap-2">
          {/* Live Auto-Save Indicator */}
          <div className="flex items-center gap-1.5 px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 font-mono text-[11px]">
            {autoSaveStatus === 'saving' ? (
              <>
                <Loader2 className="w-3 h-3 text-amber-500 animate-spin" />
                <span className="text-amber-600 dark:text-amber-400 font-bold">{t.autoSaveSaving}</span>
              </>
            ) : (
              <>
                <span className="w-2 h-2 rounded-none bg-emerald-500 animate-pulse"></span>
                <span className="text-zinc-700 dark:text-zinc-300 font-bold">{t.autoSaveSaved}</span>
                <span className="text-zinc-400 dark:text-zinc-500 hidden sm:inline">({lastSavedTime})</span>
              </>
            )}
          </div>

          {/* Language Switcher */}
          <div className="flex border border-zinc-300 dark:border-zinc-700 rounded-none overflow-hidden font-mono text-xs">
            <button
              type="button"
              onClick={() => setLang('id')}
              className={`px-2.5 py-1 font-bold transition ${
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
              className={`px-2.5 py-1 font-bold transition ${
                lang === 'en' 
                  ? 'bg-zinc-900 text-white dark:bg-emerald-500 dark:text-black' 
                  : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'
              }`}
            >
              EN
            </button>
          </div>

          {/* Theme Switcher */}
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

      {/* READINESS & AI TIER HEADER BANNER */}
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6 font-mono text-xs">
        {/* Specification Readiness Bar */}
        <div className="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex flex-col justify-between">
          <div className="flex items-center justify-between mb-1.5">
            <span className="text-zinc-500 dark:text-zinc-400 font-bold">{t.readinessLabel}</span>
            <span className="font-black text-emerald-600 dark:text-emerald-400">{completeness.score}% ({completeness.status})</span>
          </div>
          <div className="w-full bg-zinc-200 dark:bg-zinc-800 h-2 rounded-none overflow-hidden">
            <div 
              className="bg-emerald-500 h-full transition-all duration-300"
              style={{ width: `${completeness.score}%` }}
            ></div>
          </div>
        </div>

        {/* Adaptive AI Engine Status (Anti-Boncos Tiering) */}
        <div className="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
          <div>
            <span className="text-emerald-600 dark:text-emerald-400 font-bold block mb-0.5 flex items-center gap-1.5">
              <Zap className="w-3.5 h-3.5" />
              {t.aiTierLabel}
            </span>
            <span className="text-[10px] text-zinc-500 dark:text-zinc-400 font-sans">
              Tahap Blueprint: Flash Tier (Hemat Token). Tahap PRD Final: Pro Tier.
            </span>
          </div>
          <span className="px-2 py-0.5 text-[10px] bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 font-bold shrink-0">
            FREE PLAN OK
          </span>
        </div>
      </div>

      {/* =========================================================================
          SECTION 1: QUICK IDEA STUDIO & MARKITDOWN DROPZONE (TOP ACCELERATOR)
          ========================================================================= */}
      <section className="mb-8 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 rounded-none transition-all">
        
        {/* Studio Accordion Header */}
        <button
          type="button"
          onClick={() => setIsStudioOpen(prev => !prev)}
          className="w-full p-4 sm:p-5 flex items-center justify-between text-left hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition border-b border-zinc-200 dark:border-zinc-800"
        >
          <div className="flex items-center gap-3">
            <div className="w-8 h-8 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
              <Sparkles className="w-4 h-4" />
            </div>
            <div>
              <span className="text-[10px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block">
                {t.studioBadge}
              </span>
              <h3 className="text-sm sm:text-base font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
                {t.studioTitle}
              </h3>
            </div>
          </div>
          
          <span className="text-xs font-mono font-bold text-zinc-500 flex items-center gap-1.5 shrink-0">
            {isStudioOpen ? t.studioToggleOpen : t.studioToggleClosed}
            {isStudioOpen ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
          </span>
        </button>

        {isStudioOpen && (
          <div className="p-6 sm:p-8 space-y-6">
            <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed">
              {t.studioDesc}
            </p>

            {/* Quick Templates */}
            <div>
              <span className="block text-[11px] font-mono uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-2 font-bold">
                {t.sampleTemplates}
              </span>
              <div className="flex flex-wrap gap-2">
                <button
                  type="button"
                  onClick={() => applyTemplate(
                    lang === 'en'
                      ? "We need an inter-island freight logistics & fleet management system. 3 roles: Superadmin, Dispatcher, and Drivers. Drivers scan barcode waybills and upload delivery photos. Real-time container tracking and Midtrans invoice payments."
                      : "Kami butuh platform ekspedisi dan armada truk logistik antar pulau. Terdapat 3 level: Superadmin di kantor pusat, Koordinator Lapangan, dan Driver truk. Driver bisa scan barcode resi dan upload foto bukti kirim. Ada pelacakan GPS real-time dan pembayaran invoice tempo via Midtrans."
                  )}
                  className="px-2.5 py-1 text-xs font-mono bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 rounded-none transition text-left"
                >
                  {t.tplLogistics}
                </button>
                <button
                  type="button"
                  onClick={() => applyTemplate(
                    lang === 'en'
                      ? "Medical clinic platform with Electronic Health Records (EHR). Doctors input patient diagnoses and prescribe medications. Online patient queue ticketing and integrated pharmacy stock management."
                      : "Sistem klinik medis terpadu dan Rekam Medis Elektronik (RME). Dokter input diagnosis pasien dan resep obat. Pasien bisa ambil nomor antrean online dan inventaris apotek terhubung otomatis."
                  )}
                  className="px-2.5 py-1 text-xs font-mono bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 rounded-none transition text-left"
                >
                  {t.tplClinic}
                </button>
                <button
                  type="button"
                  onClick={() => applyTemplate(
                    lang === 'en'
                      ? "B2B heavy machinery rental marketplace. Equipment owners list excavators and bulldozers with hourly pricing. Construction contractors rent with automated escrow and milestone payment schedule."
                      : "Marketplace rental alat berat B2B (excavator, crane, bulldozer). Pemilik alat listing spesifikasi & tarif sewa. Kontraktor menyewa dengan sistem escrow deposit dan jadwal penagihan tempo."
                  )}
                  className="px-2.5 py-1 text-xs font-mono bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 rounded-none transition text-left"
                >
                  {t.tplRental}
                </button>
              </div>
            </div>

            {/* Brain Dump Textarea */}
            <div>
              <div className="flex items-center justify-between mb-1.5">
                <label className={labelClass}>
                  Uraian Ide & Alur Proses Bisnis
                </label>
                <span className="text-[11px] font-mono text-zinc-400">
                  {ideaText.length} {t.charCount}
                </span>
              </div>
              <textarea
                rows={5}
                value={ideaText}
                onChange={(e) => setIdeaText(e.target.value)}
                placeholder={t.textareaPlaceholder}
                className={inputClass}
              />
            </div>

            {/* MarkItDown File Dropzone */}
            <div>
              <label className={labelClass}>
                {t.dropzoneTitle}
              </label>
              <div
                onClick={() => fileInputRef.current?.click()}
                onDragOver={(e) => e.preventDefault()}
                onDrop={(e) => {
                  e.preventDefault();
                  if (e.dataTransfer.files) {
                    handleFileChange({ target: { files: e.dataTransfer.files } });
                  }
                }}
                className="border-2 border-dashed border-zinc-300 dark:border-zinc-700 hover:border-emerald-500 dark:hover:border-emerald-500 bg-zinc-50 dark:bg-zinc-950 p-6 text-center cursor-pointer transition rounded-none"
              >
                <input
                  ref={fileInputRef}
                  type="file"
                  multiple
                  accept=".pdf,.doc,.docx,.txt,.md,.rtf,.csv,.tsv,.xlsx,.pptx,.png,.jpg,.jpeg,.webp"
                  onChange={handleFileChange}
                  className="hidden"
                />
                <Upload className="w-8 h-8 text-zinc-400 dark:text-zinc-500 mx-auto mb-2" />
                <p className="text-xs sm:text-sm font-bold text-zinc-800 dark:text-zinc-200">
                  {t.dropzoneSubtitle}
                </p>
                <p className="text-[11px] font-mono text-zinc-500 dark:text-zinc-400 mt-1">
                  {t.dropzoneFormats}
                </p>
              </div>

              {/* Token Reality & MarkItDown Notice */}
              <div className="mt-2.5 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-[11px] font-mono text-zinc-600 dark:text-zinc-400 flex items-start gap-2">
                <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                <span>{t.markitdownNotice}</span>
              </div>

              {/* Uploaded File Badges */}
              {attachedFiles.length > 0 && (
                <div className="mt-3 space-y-2">
                  <span className="text-[11px] font-mono uppercase text-zinc-500 font-bold block">
                    Berkas Siap Dikonversi ({attachedFiles.length}/5):
                  </span>
                  <div className="flex flex-wrap gap-2">
                    {attachedFiles.map((file, idx) => (
                      <div
                        key={idx}
                        className="flex items-center gap-2 px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-xs font-mono"
                      >
                        <FileCode className="w-3.5 h-3.5 text-emerald-500" />
                        <span className="font-bold text-zinc-800 dark:text-zinc-200 truncate max-w-[200px]">{file.name}</span>
                        <span className="text-[10px] text-zinc-400">({(file.size / 1024).toFixed(0)} KB)</span>
                        <button
                          type="button"
                          onClick={(e) => { e.stopPropagation(); removeFile(idx); }}
                          className="text-zinc-400 hover:text-red-500 ml-1"
                        >
                          <X className="w-3.5 h-3.5" />
                        </button>
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>

            {/* Error Message */}
            {analysisError && (
              <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-xs font-mono flex items-center gap-2">
                <AlertCircle className="w-4 h-4 shrink-0" />
                <span>{analysisError}</span>
              </div>
            )}

            {/* Honeypots */}
            <input type="text" name="_hp_check" value={honeypot} onChange={(e) => setHoneypot(e.target.value)} className="hidden" tabIndex={-1} autoComplete="off" />
            <input type="text" name="_website" value={honeypotWebsite} onChange={(e) => setHoneypotWebsite(e.target.value)} className="hidden" tabIndex={-1} autoComplete="off" />

            {/* Synthesize Button */}
            <button
              type="button"
              onClick={handleAnalyzeIdea}
              disabled={isAnalyzing}
              className="w-full bg-emerald-600 hover:bg-emerald-500 text-black font-black uppercase tracking-wider py-4 px-6 rounded-none text-center flex items-center justify-center gap-2 transition disabled:opacity-50 text-sm font-mono"
            >
              {isAnalyzing ? (
                <>
                  <Loader2 className="w-4 h-4 animate-spin" />
                  <span>{analysisStep || t.synthesizingBtn}</span>
                </>
              ) : (
                <>
                  <Sparkles className="w-4 h-4" />
                  <span>{t.synthesizeBtn}</span>
                </>
              )}
            </button>
          </div>
        )}
      </section>

      {/* =========================================================================
          SECTION 2: PROACTIVE AI ASSISTANT (ANTI-BONCOS FLASH CO-PILOT)
          ========================================================================= */}
      <section className="mb-8 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 sm:p-8 rounded-none">
        <div className="flex items-center gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-5">
          <div className="w-8 h-8 bg-emerald-500 text-black flex items-center justify-center font-bold">
            <Lightbulb className="w-4 h-4" />
          </div>
          <div>
            <span className="text-[10px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block">
              {t.proactiveBadge}
            </span>
            <h3 className="text-base sm:text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {t.proactiveTitle}
            </h3>
          </div>
        </div>

        <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
          {t.proactiveDesc}
        </p>

        {/* 1-Click Suggestion Chips */}
        <div className="mb-6">
          <span className="block text-[11px] font-mono uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-2.5 font-bold">
            {t.suggestionsTitle}
          </span>
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
            {proactiveSuggestions.map((sug) => {
              const isApplied = appliedSuggestionIds.has(sug.id);
              return (
                <button
                  key={sug.id}
                  type="button"
                  onClick={() => handleApplySuggestion(sug)}
                  disabled={isApplied}
                  className={`p-3 text-left border rounded-none transition flex flex-col justify-between ${
                    isApplied
                      ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-600 dark:text-emerald-400 opacity-80 cursor-default'
                      : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-300 dark:border-zinc-700 hover:border-emerald-500 text-zinc-800 dark:text-zinc-200'
                  }`}
                >
                  <div>
                    <div className="flex items-center justify-between gap-1 mb-1">
                      <span className="text-[10px] font-mono uppercase tracking-wider px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-bold">
                        {sug.badge}
                      </span>
                      {isApplied && (
                        <span className="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-0.5">
                          <Check className="w-3 h-3" /> Ditambahkan
                        </span>
                      )}
                    </div>
                    <span className="text-xs font-bold block mb-1 font-sans">{sug.title}</span>
                    <span className="text-[11px] text-zinc-500 dark:text-zinc-400 block line-clamp-2 font-sans">{sug.desc}</span>
                  </div>
                  {!isApplied && (
                    <span className="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold mt-2 flex items-center gap-1">
                      <Plus className="w-3 h-3" /> Pasang ke Form
                    </span>
                  )}
                </button>
              );
            })}
          </div>
        </div>

        {/* Lightweight Co-Pilot Input Bar */}
        <form onSubmit={handleCoPilotSubmit} className="pt-4 border-t border-zinc-200 dark:border-zinc-800">
          <label className={labelClass}>
            Konsultasikan Kebutuhan Baru ke Asisten AI (Gemini Flash)
          </label>
          <div className="flex gap-2">
            <input
              type="text"
              value={coPilotInput}
              onChange={(e) => setCoPilotInput(e.target.value)}
              placeholder={t.coPilotPlaceholder}
              className="flex-1 px-4 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-sm outline-none focus:border-emerald-500 font-sans"
            />
            <button
              type="submit"
              disabled={isCoPilotLoading || !coPilotInput.trim()}
              className="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-black font-mono text-xs font-black uppercase tracking-wider rounded-none flex items-center gap-2 transition disabled:opacity-50 shrink-0"
            >
              {isCoPilotLoading ? (
                <>
                  <Loader2 className="w-3.5 h-3.5 animate-spin" />
                  <span>{t.coPilotProcessing}</span>
                </>
              ) : (
                <>
                  <Sparkles className="w-3.5 h-3.5" />
                  <span>{t.coPilotSubmitBtn}</span>
                </>
              )}
            </button>
          </div>
        </form>
      </section>

      {/* =========================================================================
          SECTION 3: THE COMPLETE 20-FIELD BLUEPRINT FORM (5 ARCHITECTURAL BLOCKS)
          ========================================================================= */}
      <form onSubmit={handleFinalSubmit} className="space-y-8">

        {/* ---------------------------------------------------------------------
            BLOK A: IDENTITAS PROYEK & TUJUAN BISNIS (FIELDS 1 - 3)
            --------------------------------------------------------------------- */}
        <section id="section-block-a" className={panelClass}>
          <div className="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
            <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block mb-1">
              ARSITEKTUR BISNIS // 01
            </span>
            <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {t.blockA}
            </h3>
            <p className="text-xs text-zinc-500 mt-1 font-sans">{t.blockADesc}</p>
          </div>

          <div className="space-y-5">
            {/* Field 1: namaBisnis */}
            <div>
              <label className={labelClass}>
                1. Nama Aplikasi / Domain Sistem *
              </label>
              <input
                type="text"
                required
                value={formData.namaBisnis}
                onChange={(e) => updateField('namaBisnis', e.target.value)}
                placeholder="Misal: TransLogistix Pro / Klinik Sehat Sentosa"
                className={inputClass}
              />
            </div>

            {/* Field 2: masalahUtama */}
            <div>
              <label className={labelClass}>
                2. Masalah Utama & Pain Points yang Dihadapi Bisnis
              </label>
              <textarea
                rows={3}
                value={formData.masalahUtama}
                onChange={(e) => updateField('masalahUtama', e.target.value)}
                placeholder="Uraikan kendala operasional, inefisiensi manual, atau titik rawan kebocoran yang ingin diselesaikan dengan sistem ini..."
                className={inputClass}
              />
            </div>

            {/* Field 3: tujuanUtama (KPIs) */}
            <div>
              <div className="flex items-center justify-between mb-1.5">
                <label className={labelClass}>
                  3. Tolak Ukur Keberhasilan (Target KPI Bisnis)
                </label>
                <button
                  type="button"
                  onClick={() => setActiveAddKey(activeAddKey === 'tujuanUtama' ? null : 'tujuanUtama')}
                  className="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold"
                >
                  <Plus className="w-3.5 h-3.5" /> Tambah KPI
                </button>
              </div>

              <div className="space-y-1.5 mb-2">
                {parseNumberedList(formData.tujuanUtama).map((kpi, idx) => (
                  <div key={idx} className="group flex items-start justify-between gap-2 p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs">
                    <span className="text-zinc-800 dark:text-zinc-200 font-sans">
                      <strong className="text-emerald-600 dark:text-emerald-400 font-mono">{idx + 1}.</strong> {kpi}
                    </span>
                    <button
                      type="button"
                      onClick={() => handleDeleteListItem('tujuanUtama', idx)}
                      className="text-zinc-400 hover:text-red-500 opacity-60 group-hover:opacity-100 transition shrink-0"
                    >
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                  </div>
                ))}
              </div>

              {activeAddKey === 'tujuanUtama' && (
                <div className="flex gap-2 mt-2">
                  <input
                    type="text"
                    value={quickInputText}
                    onChange={(e) => setQuickInputText(e.target.value)}
                    placeholder="Misal: Rekonsiliasi keuangan memangkas waktu kerja dari 3 hari menjadi 15 menit..."
                    className="flex-1 px-3 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => handleAddListItem('tujuanUtama')}
                    className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                  >
                    Simpan
                  </button>
                </div>
              )}
            </div>
          </div>
        </section>

        {/* ---------------------------------------------------------------------
            BLOK B: TARGET PENGGUNA & RBAC (FIELDS 4 - 5)
            --------------------------------------------------------------------- */}
        <section className={panelClass}>
          <div className="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
            <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block mb-1">
              AKTOR & HAK AKSES // 02
            </span>
            <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {t.blockB}
            </h3>
            <p className="text-xs text-zinc-500 mt-1 font-sans">{t.blockBDesc}</p>
          </div>

          <div className="space-y-5">
            {/* Field 4: targetAudiens */}
            <div>
              <label className={labelClass}>
                4. Profil Target Audiens / Pengguna Akhir
              </label>
              <textarea
                rows={2}
                value={formData.targetAudiens}
                onChange={(e) => updateField('targetAudiens', e.target.value)}
                placeholder="Misal: Pemilik armada truk, manajer logistik perusahaan FMCG, dan staf gudang lapangan..."
                className={inputClass}
              />
            </div>

            {/* Field 5: aktorSistem (RBAC) */}
            <div>
              <div className="flex items-center justify-between mb-1.5">
                <label className={labelClass}>
                  5. Aktor Sistem & Matriks Wewenang (RBAC)
                </label>
                <button
                  type="button"
                  onClick={() => setActiveAddKey(activeAddKey === 'aktorSistem' ? null : 'aktorSistem')}
                  className="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold"
                >
                  <Plus className="w-3.5 h-3.5" /> Tambah Role
                </button>
              </div>

              {/* Quick Role Preset Badges */}
              <div className="flex flex-wrap gap-1.5 mb-2.5">
                {['+ Superadmin', '+ Supervisor / Approval', '+ Finance Staff', '+ Operator Lapangan', '+ Driver / Kurir', '+ Customer Eksternal'].map((rolePreset, idx) => (
                  <button
                    key={idx}
                    type="button"
                    onClick={() => {
                      const items = parseNumberedList(formData.aktorSistem);
                      const cleanRole = rolePreset.replace('+ ', '');
                      items.push(`${cleanRole}: Akses operasional sesuai wewenang tugas`);
                      updateField('aktorSistem', stringifyNumberedList(items));
                      showLocalToast('success', `${cleanRole} ditambahkan ke Role!`);
                    }}
                    className="px-2 py-0.5 text-[10px] font-mono bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-400 border border-zinc-300 dark:border-zinc-700"
                  >
                    {rolePreset}
                  </button>
                ))}
              </div>

              <div className="space-y-1.5 mb-2">
                {parseNumberedList(formData.aktorSistem).map((actor, idx) => (
                  <div key={idx} className="group flex items-start justify-between gap-2 p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs">
                    <span className="text-zinc-800 dark:text-zinc-200 font-sans">
                      <strong className="text-emerald-600 dark:text-emerald-400 font-mono">{idx + 1}.</strong> {actor}
                    </span>
                    <button
                      type="button"
                      onClick={() => handleDeleteListItem('aktorSistem', idx)}
                      className="text-zinc-400 hover:text-red-500 opacity-60 group-hover:opacity-100 transition shrink-0"
                    >
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                  </div>
                ))}
              </div>

              {activeAddKey === 'aktorSistem' && (
                <div className="flex gap-2 mt-2">
                  <input
                    type="text"
                    value={quickInputText}
                    onChange={(e) => setQuickInputText(e.target.value)}
                    placeholder="Misal: Auditor Internal: Hak akses Read-Only pada seluruh buku transaksi..."
                    className="flex-1 px-3 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => handleAddListItem('aktorSistem')}
                    className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                  >
                    Simpan
                  </button>
                </div>
              )}
            </div>
          </div>
        </section>

        {/* ---------------------------------------------------------------------
            BLOK C: FITUR MVP & ALUR KERJA (FIELDS 6 - 8)
            --------------------------------------------------------------------- */}
        <section className={panelClass}>
          <div className="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
            <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block mb-1">
              FITUR & WORKFLOW // 03
            </span>
            <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {t.blockC}
            </h3>
            <p className="text-xs text-zinc-500 mt-1 font-sans">{t.blockCDesc}</p>
          </div>

          <div className="space-y-6">
            {/* Field 6: fiturWajib (MVP) */}
            <div>
              <div className="flex items-center justify-between mb-1.5">
                <label className={labelClass}>
                  6. Fitur Wajib MVP (Fase 1 - Prioritas Mutlak)
                </label>
                <button
                  type="button"
                  onClick={() => setActiveAddKey(activeAddKey === 'fiturWajib' ? null : 'fiturWajib')}
                  className="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold"
                >
                  <Plus className="w-3.5 h-3.5" /> Tambah Fitur
                </button>
              </div>

              <div className="space-y-1.5 mb-2 max-h-60 overflow-y-auto pr-1">
                {parseNumberedList(formData.fiturWajib).map((feat, idx) => (
                  <div key={idx} className="group flex items-start justify-between gap-2 p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs">
                    <span className="text-zinc-800 dark:text-zinc-200 font-sans leading-tight">
                      <strong className="text-emerald-600 dark:text-emerald-400 font-mono">{idx + 1}.</strong> {feat}
                    </span>
                    <button
                      type="button"
                      onClick={() => handleDeleteListItem('fiturWajib', idx)}
                      className="text-zinc-400 hover:text-red-500 opacity-60 group-hover:opacity-100 transition shrink-0"
                    >
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                  </div>
                ))}
              </div>

              {activeAddKey === 'fiturWajib' && (
                <div className="flex gap-2 mt-2">
                  <input
                    type="text"
                    value={quickInputText}
                    onChange={(e) => setQuickInputText(e.target.value)}
                    placeholder="Misal: Scan Barcode Surat Jalan via Kamera Ponsel..."
                    className="flex-1 px-3 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => handleAddListItem('fiturWajib')}
                    className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                  >
                    Simpan
                  </button>
                </div>
              )}
            </div>

            {/* Field 7: fiturTambahan (Roadmap) */}
            <div>
              <label className={labelClass}>
                7. Fitur Tambahan (Fase 2 - Roadmap Masa Depan)
              </label>
              <textarea
                rows={3}
                value={formData.fiturTambahan}
                onChange={(e) => updateField('fiturTambahan', e.target.value)}
                placeholder="Fitur sekunder yang dapat ditunda setelah rilis MVP (misal: Aplikasi Mobile Native, Integrasi AI Prediktif rute armada, dll.)..."
                className={inputClass}
              />
            </div>

            {/* Field 8: alurKerja (User Flow) */}
            <div>
              <div className="flex items-center justify-between mb-1.5">
                <label className={labelClass}>
                  8. Alur Kerja Utama (User Flow Langkah demi Langkah)
                </label>
                <button
                  type="button"
                  onClick={() => setActiveAddKey(activeAddKey === 'alurKerja' ? null : 'alurKerja')}
                  className="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold"
                >
                  <Plus className="w-3.5 h-3.5" /> Tambah Langkah
                </button>
              </div>

              <div className="space-y-1.5 mb-2 max-h-60 overflow-y-auto pr-1">
                {parseNumberedList(formData.alurKerja).map((step, idx) => (
                  <div key={idx} className="group flex items-start justify-between gap-2 p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs">
                    <span className="text-zinc-800 dark:text-zinc-200 font-sans leading-tight">
                      <strong className="text-emerald-600 dark:text-emerald-400 font-mono">Langkah {idx + 1}:</strong> {step}
                    </span>
                    <button
                      type="button"
                      onClick={() => handleDeleteListItem('alurKerja', idx)}
                      className="text-zinc-400 hover:text-red-500 opacity-60 group-hover:opacity-100 transition shrink-0"
                    >
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                  </div>
                ))}
              </div>

              {activeAddKey === 'alurKerja' && (
                <div className="flex gap-2 mt-2">
                  <input
                    type="text"
                    value={quickInputText}
                    onChange={(e) => setQuickInputText(e.target.value)}
                    placeholder="Misal: Driver mengunggah foto kuitansi -> Sistem otomatis mencocokkan nominal invoice..."
                    className="flex-1 px-3 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => handleAddListItem('alurKerja')}
                    className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                  >
                    Simpan
                  </button>
                </div>
              )}
            </div>
          </div>
        </section>

        {/* ---------------------------------------------------------------------
            BLOK D: INTEGRASI, TIMELINE & DESAIN (FIELDS 9 - 13)
            --------------------------------------------------------------------- */}
        <section className={panelClass}>
          <div className="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
            <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block mb-1">
              INTEGRASI & TIMELINE // 04
            </span>
            <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {t.blockD}
            </h3>
            <p className="text-xs text-zinc-500 mt-1 font-sans">{t.blockDDesc}</p>
          </div>

          <div className="space-y-5">
            {/* Field 9: kebutuhanIntegrasi */}
            <div>
              <div className="flex items-center justify-between mb-1.5">
                <label className={labelClass}>
                  9. Kebutuhan Integrasi Pihak Ketiga (API & Gateway)
                </label>
                <button
                  type="button"
                  onClick={() => setActiveAddKey(activeAddKey === 'kebutuhanIntegrasi' ? null : 'kebutuhanIntegrasi')}
                  className="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold"
                >
                  <Plus className="w-3.5 h-3.5" /> Tambah Integrasi
                </button>
              </div>

              {/* Preset Chips */}
              <div className="flex flex-wrap gap-1.5 mb-2.5">
                {['+ Midtrans Snap', '+ WhatsApp Gateway', '+ Google Maps API', '+ Cloudflare R2', '+ RajaOngkir Kurir', '+ Mailgun Transactional'].map((preset, idx) => (
                  <button
                    key={idx}
                    type="button"
                    onClick={() => {
                      const items = parseCommaList(formData.kebutuhanIntegrasi);
                      const cleanPreset = preset.replace('+ ', '');
                      if (!items.includes(cleanPreset)) {
                        items.push(cleanPreset);
                        updateField('kebutuhanIntegrasi', stringifyCommaList(items));
                        showLocalToast('success', `${cleanPreset} ditambahkan ke Integrasi!`);
                      }
                    }}
                    className="px-2 py-0.5 text-[10px] font-mono bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-400 border border-zinc-300 dark:border-zinc-700"
                  >
                    {preset}
                  </button>
                ))}
              </div>

              {/* Tag Badges */}
              <div className="flex flex-wrap gap-1.5 mb-2">
                {parseCommaList(formData.kebutuhanIntegrasi).map((intg, idx) => (
                  <span
                    key={idx}
                    className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-mono border border-zinc-300 dark:border-zinc-700"
                  >
                    <span>{intg}</span>
                    <button
                      type="button"
                      onClick={() => handleDeleteListItem('kebutuhanIntegrasi', idx)}
                      className="text-zinc-400 hover:text-red-500"
                    >
                      <X className="w-3 h-3" />
                    </button>
                  </span>
                ))}
              </div>

              {activeAddKey === 'kebutuhanIntegrasi' && (
                <div className="flex gap-2 mt-2">
                  <input
                    type="text"
                    value={quickInputText}
                    onChange={(e) => setQuickInputText(e.target.value)}
                    placeholder="Misal: Xendit Payment Gateway..."
                    className="flex-1 px-3 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => handleAddListItem('kebutuhanIntegrasi')}
                    className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                  >
                    Simpan
                  </button>
                </div>
              )}
            </div>

            {/* Field 10 & 11: referensiDesain & kesiapanAset */}
            <div className="grid sm:grid-cols-2 gap-4">
              <div>
                <label className={labelClass}>
                  10. Referensi Desain / Benchmark UI/UX
                </label>
                <input
                  type="text"
                  value={formData.referensiDesain}
                  onChange={(e) => updateField('referensiDesain', e.target.value)}
                  placeholder="Misal: Linear.app, Stripe Dashboard, Shopify Admin"
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>
                  11. Kesiapan Aset Digital (Logo, Konten)
                </label>
                <input
                  type="text"
                  value={formData.kesiapanAset}
                  onChange={(e) => updateField('kesiapanAset', e.target.value)}
                  placeholder="Misal: Sudah Siap / Sedang Dibuat Tim Desain"
                  className={inputClass}
                />
              </div>
            </div>

            {/* Field 12 & 13: durasiHari & targetWaktu */}
            <div className="grid sm:grid-cols-2 gap-4">
              <div>
                <label className={labelClass}>
                  12. Target Durasi Pengerjaan (Hari Kerja)
                </label>
                <div className="flex gap-2 mb-2">
                  {['14', '30', '45', '60'].map((d) => (
                    <button
                      key={d}
                      type="button"
                      onClick={() => {
                        updateField('durasiHari', d);
                        updateField('targetWaktu', `${d} Hari Kerja (Fase 1 MVP)`);
                      }}
                      className={`flex-1 py-1.5 text-xs font-mono font-bold border transition ${
                        formData.durasiHari === d
                          ? 'bg-emerald-500 text-black border-emerald-500'
                          : 'bg-zinc-50 dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 hover:border-emerald-500'
                      }`}
                    >
                      {d} Hari
                    </button>
                  ))}
                </div>
                <input
                  type="text"
                  value={formData.durasiHari}
                  onChange={(e) => updateField('durasiHari', e.target.value)}
                  className={inputClass}
                />
              </div>

              <div>
                <label className={labelClass}>
                  13. Target Waktu Peluncuran (Target Rilis)
                </label>
                <input
                  type="text"
                  value={formData.targetWaktu}
                  onChange={(e) => updateField('targetWaktu', e.target.value)}
                  placeholder="Misal: 30 Hari Kerja (Fase 1 MVP)"
                  className={inputClass}
                />
              </div>
            </div>
          </div>
        </section>

        {/* ---------------------------------------------------------------------
            BLOK E: SKALA, KEAMANAN & BATASAN RUANG LINGKUP (FIELDS 14 - 18)
            --------------------------------------------------------------------- */}
        <section className={panelClass}>
          <div className="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
            <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block mb-1">
              SKALA & BOUNDARY // 05
            </span>
            <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {t.blockE}
            </h3>
            <p className="text-xs text-zinc-500 mt-1 font-sans">{t.blockEDesc}</p>
          </div>

          <div className="space-y-5">
            {/* Field 14 & 15: skalaPengguna & jangkauanPasar */}
            <div className="grid sm:grid-cols-2 gap-4">
              <div>
                <label className={labelClass}>
                  14. Estimasi Skala Trafik Pengguna
                </label>
                <input
                  type="text"
                  value={formData.skalaPengguna}
                  onChange={(e) => updateField('skalaPengguna', e.target.value)}
                  placeholder="0 - 100.000 Pengguna / Bulan (Dedicated VPS Monolith)"
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>
                  15. Jangkauan Pasar & Zona Waktu
                </label>
                <input
                  type="text"
                  value={formData.jangkauanPasar}
                  onChange={(e) => updateField('jangkauanPasar', e.target.value)}
                  placeholder="Domestik Indonesia (IDR, Zona WIB/WITA/WIT)"
                  className={inputClass}
                />
              </div>
            </div>

            {/* Field 16: outOfScope (Anti Scope Creep) */}
            <div>
              <div className="flex items-center justify-between mb-1.5">
                <label className={labelClass}>
                  16. Batasan Negatif (Out of Scope - Anti Scope Creep)
                </label>
                <button
                  type="button"
                  onClick={() => setActiveAddKey(activeAddKey === 'outOfScope' ? null : 'outOfScope')}
                  className="text-xs font-mono text-amber-600 dark:text-amber-400 hover:underline flex items-center gap-1 font-bold"
                >
                  <Plus className="w-3.5 h-3.5" /> Tambah Batasan
                </button>
              </div>

              <div className="space-y-1.5 mb-2">
                {parseNumberedList(formData.outOfScope).map((scope, idx) => (
                  <div key={idx} className="group flex items-start justify-between gap-2 p-2 bg-amber-500/5 border border-amber-500/20 text-xs">
                    <span className="text-zinc-800 dark:text-zinc-200 font-sans">
                      <strong className="text-amber-600 dark:text-amber-400 font-mono">{idx + 1}.</strong> {scope}
                    </span>
                    <button
                      type="button"
                      onClick={() => handleDeleteListItem('outOfScope', idx)}
                      className="text-zinc-400 hover:text-red-500 opacity-60 group-hover:opacity-100 transition shrink-0"
                    >
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                  </div>
                ))}
              </div>

              {activeAddKey === 'outOfScope' && (
                <div className="flex gap-2 mt-2">
                  <input
                    type="text"
                    value={quickInputText}
                    onChange={(e) => setQuickInputText(e.target.value)}
                    placeholder="Misal: Tidak mencakup pengadaan hardware fisik atau biaya langganan WhatsApp API..."
                    className="flex-1 px-3 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => handleAddListItem('outOfScope')}
                    className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                  >
                    Simpan
                  </button>
                </div>
              )}
            </div>

            {/* Field 17 & 18: kepatuhanKeamanan & kisaranBudget */}
            <div className="grid sm:grid-cols-2 gap-4">
              <div>
                <label className={labelClass}>
                  17. Standar Keamanan & Enkripsi
                </label>
                <input
                  type="text"
                  value={formData.kepatuhanKeamanan}
                  onChange={(e) => updateField('kepatuhanKeamanan', e.target.value)}
                  placeholder="Standar Web Application & OWASP Top 10 (CSRF, XSS, HTTPS)"
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>
                  18. Alokasi Kisaran Budget Investasi
                </label>
                <input
                  type="text"
                  value={formData.kisaranBudget}
                  onChange={(e) => updateField('kisaranBudget', e.target.value)}
                  placeholder="Rp 15.000.000 - Rp 35.000.000 (Growth Monolith)"
                  className={inputClass}
                />
              </div>
            </div>
          </div>
        </section>

        {/* ---------------------------------------------------------------------
            BLOK F: PENGESAHAN KONTAK PIC (FIELDS 19 - 20) & DIGITAL LOCK
            --------------------------------------------------------------------- */}
        <section className={panelClass}>
          <div className="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
            <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block mb-1">
              PENGESAHAN PIC // 06
            </span>
            <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
              <Users className="w-5 h-5 text-emerald-500" />
              {t.blockF}
            </h3>
            <p className="text-xs text-zinc-500 mt-1 font-sans">{t.blockFDesc}</p>
          </div>

          {errorMessage && (
            <div className="mb-6 p-4 bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-xs font-mono flex items-center gap-2">
              <AlertCircle className="w-4 h-4 shrink-0" />
              <span>{errorMessage}</span>
            </div>
          )}

          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            {/* Field 19: clientName */}
            <div>
              <label className={labelClass}>
                19. Nama Lengkap PIC *
              </label>
              <input
                type="text"
                required
                placeholder="Misal: Budi Santoso"
                value={formData.clientName}
                onChange={(e) => updateField('clientName', e.target.value)}
                className={inputClass}
              />
            </div>

            {/* Field 20a: email */}
            <div>
              <label className={labelClass}>
                20a. Email Resmi PIC *
              </label>
              <input
                type="email"
                required
                placeholder="budi@perusahaan.com"
                value={formData.email}
                onChange={(e) => updateField('email', e.target.value)}
                className={inputClass}
              />
            </div>

            {/* Field 20b: phone with Country Zone Code */}
            <div>
              <label className={labelClass}>
                20b. WhatsApp / Telepon PIC *
              </label>
              <div className="flex rounded-none border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950 focus-within:border-emerald-500 transition">
                <select
                  value={selectedCountryCode}
                  onChange={(e) => {
                    const code = e.target.value;
                    setSelectedCountryCode(code);
                    updateField('phone', phoneDigits ? `${code}${phoneDigits}` : '');
                  }}
                  className="bg-transparent text-xs font-mono font-bold text-zinc-700 dark:text-zinc-300 py-2.5 pl-2 pr-1 outline-none border-r border-zinc-300 dark:border-zinc-700 cursor-pointer"
                >
                  {countryList.map((c) => (
                    <option key={c.code} value={c.code} className="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">
                      {c.emoji || '🌐'} {c.code} ({c.name})
                    </option>
                  ))}
                </select>
                <input
                  type="tel"
                  required
                  placeholder="81234567890"
                  value={phoneDigits}
                  onChange={handlePhoneDigitsChange}
                  className="w-full px-3 py-2.5 bg-transparent text-zinc-900 dark:text-zinc-100 text-sm outline-none font-mono"
                />
              </div>
              <span className="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1 block font-mono">
                Ketik digit tanpa angka 0 di depan (E.164 compliant).
              </span>
            </div>
          </div>

          {/* Submit Button */}
          <button
            type="submit"
            disabled={isSubmitting}
            className="w-full bg-emerald-600 hover:bg-emerald-500 text-black font-black uppercase tracking-wider py-4 px-6 rounded-none text-center flex items-center justify-center gap-2 transition disabled:opacity-50 text-sm font-mono"
          >
            {isSubmitting ? (
              <>
                <Loader2 className="w-4 h-4 animate-spin" />
                <span>{t.lockingBtn}</span>
              </>
            ) : (
              <>
                <Lock className="w-4 h-4" />
                <span>{t.lockBtn}</span>
                <ArrowRight className="w-4 h-4" />
              </>
            )}
          </button>
        </section>

      </form>

    </div>
  );
}
