import React, { useState, useEffect, useRef, useMemo } from 'react';
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
  RefreshCw
} from 'lucide-react';

const TRANSLATIONS = {
  id: {
    badge: "PROJECT OS // ARCHITECTURAL DISCOVERY WORKSPACE",
    heroTitle: "Sintesis Visi Sistem Anda Menjadi Blueprint Terpusat",
    heroSubtitle: "Cukup utarakan ide proyek atau alur operasional bisnis Anda di textarea bawah ini. Lampirkan dokumen spesifikasi jika ada (PDF, Word, Excel, PowerPoint, Catatan, Gambar Wireframe). Seluruh dokumen akan dikonversi ke format Markdown (.md) via Microsoft MarkItDown sehingga AI hemat token dan fokus penuh menganalisis arsitektur sistem Anda.",
    
    tabTextarea: "1. Ruang Ide & Analisis Cepat (Rekomendasi)",
    tabManual: "2. Kuesioner Manual (20 Field Rinci)",

    textareaPlaceholder: "Ceritakan visi, alur proses, dan kebutuhan fitur aplikasi Anda di sini...\n\nContoh: 'Saya ingin membangun sistem manajemen ekspedisi dan armada logistik antar pulau. Pengguna terdiri dari 3 level: Superadmin di kantor pusat, Koordinator Lapangan, dan Driver armada truk. Driver bisa scan barcode tanda terima barang dan update status pengiriman via foto. Klien bisa lacak posisi resi secara real-time. Ada integrasi Midtrans untuk pembayaran invoice tempo dan notifikasi otomatis ke WhatsApp saat armada tiba di gudang tujuan...'",
    
    charCount: "Karakter",
    sampleTemplates: "Contoh Templat Ide Cepat:",
    tplLogistics: "🚚 Platform Manajemen Armada Truk & Pelacakan Resi Logistik",
    tplClinic: "🏥 Sistem Informasi Klinik Medis & Rekam Medis Pasien Terpusat",
    tplRental: "🏢 Marketplace Rental Alat Berat B2B & Escrow Tagihan Tempo",
    tplHr: "💼 HR Portal, Presensi GPS, Evaluasi KPI & Penggajian Terpadu",

    dropzoneTitle: "Lampirkan Dokumen Spesifikasi atau Sketsa Wireframe",
    dropzoneSubtitle: "Tarik & lepaskan berkas ke sini, atau klik untuk memilih berkas",
    dropzoneFormats: "Dukungan Format: PDF, DOCX, XLSX, PPTX, CSV, TXT, MD, PNG, JPG (Maks. 5 berkas, @15MB)",
    markitdownNotice: "⚡ Microsoft MarkItDown Replica: Dokumen otomatis dikonversi menjadi file Markdown (.md) secara lokal di server. AI murni fokus menganalisis arsitektur sistem tanpa memboroskan token pada parsing biner mentah.",

    synthesizeBtn: "Sintesis Blueprint & Konversi Dokumen →",
    synthesizingBtn: "Memproses Sintesis...",

    step1: "Mengonversi berkas dokumen via MarkItDown ke Markdown (.md)...",
    step2: "Menganalisis domain bisnis, aktor RBAC & alur kerja...",
    step3: "Menyusun skema database PostgreSQL Strict ULID & rincian MVP...",

    // Proactive Guidance
    proactiveBadge: "ASISTEN PROAKTIF AI // PANDUAN KELENGKAPAN SPESIFIKASI",
    proactiveTitle: "Tinjau Blueprint & Lengkapi Apa yang Masih Kurang",
    proactiveDesc: "AI telah menstrukturkan ide Anda menjadi rancangan arsitektur di bawah ini. Anda dapat mengedit, menghapus, atau menambahkan kebutuhan baru pada setiap kartu. Gunakan rekomendasi cerdas atau ketik ide tambahan Anda.",
    completenessLabel: "Skor Kesiapan Spesifikasi:",
    suggestionsTitle: "💡 Rekomendasi Aspek Penting yang Sering Terlewatkan (Klik untuk Menambahkan):",
    coPilotPlaceholder: "Punya ide tambahan atau ingin melengkapi sesuatu? Ketik di sini (misal: 'Tambahkan alur pembatalan pesanan dan notifikasi WA driver')...",
    coPilotSubmitBtn: "✨ Lengkapi via AI",
    coPilotProcessing: "AI Memproses Penempatan...",
    
    // Result view
    resultTitle: "Hasil Sintesis Arsitektur Project OS",
    resultSubtitle: "Visi Anda telah dianalisis dan disusun menjadi rancangan arsitektur terstruktur standar Modern Monolith.",
    mdConvertedTitle: "Berkas Markdown (.md) Hasil Konversi MarkItDown",
    mdConvertedDesc: "Seluruh dokumen lampiran Anda telah diekstraksi menjadi Markdown murni. Anda dapat mengunduh atau menyalin file .md ini.",
    copyMd: "Salin Markdown",
    copiedMd: "Tersalin!",
    downloadMd: "Unduh File .md",
    
    cardProject: "1. Identitas Proyek & Domain Bisnis",
    cardProblem: "2. Masalah Utama & Tolak Ukur (KPI)",
    cardActors: "3. Pengguna & Aktor Sistem (RBAC)",
    cardFeatures: "4. Fitur Wajib MVP (Fase 1) vs Roadmap (Fase 2)",
    cardWorkflow: "5. Alur Kerja Utama (User Flow)",
    cardInfra: "6. Integrasi & Rekomendasi Arsitektur",
    cardScope: "7. Batasan Negatif (Out of Scope - Anti Scope Creep)",

    quickAddFeatureBtn: "+ Tambah Fitur Baru",
    quickAddRoleBtn: "+ Tambah Role Pengguna",
    quickAddStepBtn: "+ Tambah Langkah Alur",
    quickAddIntegrationBtn: "+ Tambah Integrasi",
    quickAddKpiBtn: "+ Tambah Target KPI",
    quickAddScopeBtn: "+ Tambah Batasan",
    inputPlaceholder: "Ketik kebutuhan baru di sini...",
    saveBtn: "Simpan",
    cancelBtn: "Batal",
    editBtn: "Edit",
    deleteBtn: "Hapus",

    contactTitle: "Pengesahan Kontak Penanggung Jawab Proyek (PIC)",
    clientNameLabel: "Nama Lengkap PIC",
    clientNamePh: "Misal: Budi Santoso",
    emailLabel: "Email Resmi PIC",
    emailPh: "budi@perusahaan.com",
    phoneLabel: "WhatsApp PIC",
    phonePh: "812-3456-7890",
    phoneNote: "Nomor WhatsApp tanpa angka 0 di awal.",

    toggleDetails: "Tinjau / Kustomisasi 20 Parameter Kuesioner (Opsional)",
    resetIdea: "Ubah Ide / Analisis Ulang",

    lockBtn: "Kunci Blueprint & Generate Ultimate PRD Resmi",
    lockingBtn: "Mengunci Blueprint & Menerbitkan PRD...",

    successTitle: "Transmisi Blueprint Berhasil!",
    successDesc: "Data spesifikasi proyek telah terekam dan disintesis menjadi dokumen PRD & Skema Database ERD standar PostgreSQL ULID.",
    openPrdBtn: "Buka Dokumen Ultimate PRD Sekarang",
    newProjectBtn: "Input Proyek Baru",
    daysSuffix: "Hari Kerja"
  },
  en: {
    badge: "PROJECT OS // ARCHITECTURAL DISCOVERY WORKSPACE",
    heroTitle: "Transform Your System Vision Into a Unified Blueprint",
    heroSubtitle: "Simply articulate your application ideas or business operations in the textarea below. Attach specification documents if available (PDF, Word, Excel, PowerPoint, Notes, Wireframe Images). All files will be automatically converted to clean Markdown (.md) via Microsoft MarkItDown so the AI consumes zero tokens on document parsing and focuses 100% on analyzing your system architecture.",
    
    tabTextarea: "1. Textarea Idea Studio (Recommended)",
    tabManual: "2. Detailed Questionnaire (20 Fields)",

    textareaPlaceholder: "Describe your system vision, user flows, and required capabilities here...\n\nExample: 'We need to build a distributed B2B logistics and fleet management platform across islands. Three user roles: Headquarters Superadmin, Field Dispatcher, and Truck Fleet Drivers. Drivers scan barcode waybills and upload delivery proof photos. Customers track shipments real-time. Integrated with Midtrans for invoiced payments and automated WhatsApp alerts upon warehouse arrival...'",
    
    charCount: "Characters",
    sampleTemplates: "Quick Idea Templates:",
    tplLogistics: "🚚 Inter-Island Fleet Logistics & Real-Time Tracking Platform",
    tplClinic: "🏥 Healthcare Clinic ERP & Electronic Medical Records (EMR)",
    tplRental: "🏢 B2B Heavy Machinery Rental Marketplace & Escrow Invoicing",
    tplHr: "💼 Enterprise HR Portal, GPS Attendance, KPI & Payroll Suite",

    dropzoneTitle: "Attach Specification Documents or Wireframe Sketches",
    dropzoneSubtitle: "Drag & drop files here, or click to browse files",
    dropzoneFormats: "Supported Formats: PDF, DOCX, XLSX, PPTX, CSV, TXT, MD, PNG, JPG (Max 5 files, @15MB)",
    markitdownNotice: "⚡ Microsoft MarkItDown Replica: Documents are automatically converted to clean Markdown (.md) on the server. AI strictly focuses on architectural synthesis without token waste on raw binary document parsing.",

    synthesizeBtn: "Synthesize Blueprint & Convert Documents →",
    synthesizingBtn: "Processing Synthesis...",

    step1: "Converting attached documents via MarkItDown to Markdown (.md)...",
    step2: "Analyzing business domain, RBAC system actors & workflows...",
    step3: "Structuring PostgreSQL Strict ULID schema & Phase 1 MVP features...",

    // Proactive Guidance
    proactiveBadge: "AI PROACTIVE ASSISTANT // SPECIFICATION GUIDE",
    proactiveTitle: "Review Blueprint & Complete Any Missing Elements",
    proactiveDesc: "AI has structured your concept into the Blueprint below. You can directly edit, delete, or append requirements inside any card. Click smart recommendations or type extra ideas into the co-pilot prompt.",
    completenessLabel: "Specification Readiness Score:",
    suggestionsTitle: "💡 Smart Suggestions for Overlooked Essentials (Click to Add):",
    coPilotPlaceholder: "Have extra ideas or want to add missing items? Type here (e.g. 'Add cashier thermal receipt printing and damaged goods refund flow')...",
    coPilotSubmitBtn: "✨ Integrate via AI",
    coPilotProcessing: "AI is Positioning...",

    // Result view
    resultTitle: "Project OS Architectural Synthesis Result",
    resultSubtitle: "Your vision has been decomposed and synthesized into a structured Modern Monolith architectural proposal.",
    mdConvertedTitle: "MarkItDown Converted Markdown Document (.md)",
    mdConvertedDesc: "All your attached files have been cleanly converted to Markdown. You can download or copy this .md file.",
    copyMd: "Copy Markdown",
    copiedMd: "Copied!",
    downloadMd: "Download .md File",
    
    cardProject: "1. Project Identity & Business Domain",
    cardProblem: "2. Core Problem & Success Metrics (KPIs)",
    cardActors: "3. Target Audience & System Actors (RBAC)",
    cardFeatures: "4. Core MVP Features (Phase 1) vs Roadmap (Phase 2)",
    cardWorkflow: "5. Primary User Flow",
    cardInfra: "6. Integrations & Architecture Specs",
    cardScope: "7. Negative Boundary (Out of Scope)",

    quickAddFeatureBtn: "+ Add New Feature",
    quickAddRoleBtn: "+ Add User Role",
    quickAddStepBtn: "+ Add Workflow Step",
    quickAddIntegrationBtn: "+ Add Integration",
    quickAddKpiBtn: "+ Add Target KPI",
    quickAddScopeBtn: "+ Add Boundary",
    inputPlaceholder: "Type new requirement here...",
    saveBtn: "Save",
    cancelBtn: "Cancel",
    editBtn: "Edit",
    deleteBtn: "Delete",

    contactTitle: "Project Manager (PIC) Contact Verification",
    clientNameLabel: "Full Name (PIC)",
    clientNamePh: "e.g. John Doe",
    emailLabel: "Official PIC Email",
    emailPh: "john@company.com",
    phoneLabel: "WhatsApp / Phone",
    phonePh: "812-3456-7890",
    phoneNote: "Phone digits without leading 0.",

    toggleDetails: "Review / Fine-Tune 20 Questionnaire Parameters (Optional)",
    resetIdea: "Modify Idea / Re-synthesize",

    lockBtn: "Lock Blueprint & Generate Ultimate PRD Document",
    lockingBtn: "Locking Blueprint & Issuing PRD...",

    successTitle: "Blueprint Transmitted Successfully!",
    successDesc: "Project specifications have been captured and synthesized into an enterprise PRD and PostgreSQL ULID ERD schema.",
    openPrdBtn: "Open Ultimate PRD Document",
    newProjectBtn: "Submit Another Project",
    daysSuffix: "Working Days"
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

// Client-side dynamic completeness score calculator
function calculateClientCompleteness(data, lang) {
  const isEn = lang === 'en';
  const checklist = [
    {
      key: 'namaBisnis',
      label: isEn ? 'Project Name & Identity' : 'Identitas & Nama Proyek',
      weight: 10,
      completed: !!data.namaBisnis && data.namaBisnis.length >= 3,
    },
    {
      key: 'masalahUtama',
      label: isEn ? 'Core Problem' : 'Masalah Bisnis & Solusi',
      weight: 10,
      completed: !!data.masalahUtama && data.masalahUtama.length >= 15,
    },
    {
      key: 'tujuanUtama',
      label: isEn ? 'Success Metrics (KPIs)' : 'Tolak Ukur Sukses (KPI)',
      weight: 10,
      completed: !!data.tujuanUtama && data.tujuanUtama.length >= 15,
    },
    {
      key: 'aktorSistem',
      label: isEn ? 'System Actors & RBAC' : 'Pengguna & Aktor Sistem (RBAC)',
      weight: 15,
      completed: !!data.aktorSistem && data.aktorSistem.length >= 15,
    },
    {
      key: 'fiturWajib',
      label: isEn ? 'Phase 1 MVP Features' : 'Fitur Wajib MVP (Fase 1)',
      weight: 25,
      completed: !!data.fiturWajib && data.fiturWajib.length >= 25,
    },
    {
      key: 'alurKerja',
      label: isEn ? 'Primary User Workflow' : 'Alur Kerja Utama (User Flow)',
      weight: 15,
      completed: !!data.alurKerja && data.alurKerja.length >= 20,
    },
    {
      key: 'kebutuhanIntegrasi',
      label: isEn ? 'Third-Party Integrations' : 'Integrasi Pihak Ketiga',
      weight: 10,
      completed: !!data.kebutuhanIntegrasi && data.kebutuhanIntegrasi.length >= 5,
    },
    {
      key: 'outOfScope',
      label: isEn ? 'Negative Boundary (Out of Scope)' : 'Batasan Negatif (Out of Scope)',
      weight: 5,
      completed: !!data.outOfScope && data.outOfScope.length >= 15,
    },
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

  // Active View Mode: 'textarea' (default/fast discovery), 'synthesized' (previewing blueprint), 'manual' (20 fields)
  const [viewMode, setViewMode] = useState(() => {
    if (initialData && (initialData._meta || initialData.namaBisnis)) {
      return 'synthesized';
    }
    return 'textarea';
  });

  // Country code selector
  const countryList = (countries && Array.isArray(countries) && countries.length > 0)
    ? countries
    : [
        { name: 'Indonesia', code: '+62', emoji: '🇮🇩', iso: 'ID' },
        { name: 'Malaysia', code: '+60', emoji: '🇲🇾', iso: 'MY' },
        { name: 'Singapore', code: '+65', emoji: '🇸🇬', iso: 'SG' },
        { name: 'United States', code: '+1', emoji: '🇺🇸', iso: 'US' },
        { name: 'Australia', code: '+61', emoji: '🇦🇺', iso: 'AU' },
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

  // Textarea Discovery State
  const [ideaText, setIdeaText] = useState(() => {
    return initialData?._meta?.raw_idea_text || '';
  });
  const [attachedFiles, setAttachedFiles] = useState([]);
  const [convertedMarkdown, setConvertedMarkdown] = useState(() => {
    return initialData?._meta?.converted_markdown || '';
  });
  const [isAnalyzing, setIsAnalyzing] = useState(false);
  const [analysisStep, setAnalysisStep] = useState('');
  const [analysisError, setAnalysisError] = useState(null);
  const [copySuccess, setCopySuccess] = useState(false);
  const [showDetailsAccordion, setShowDetailsAccordion] = useState(false);
  const [showMarkdownViewer, setShowMarkdownViewer] = useState(false);

  // Proactive Guidance & Co-Pilot States
  const [proactiveSuggestions, setProactiveSuggestions] = useState(() => {
    return initialData.proactive_suggestions || [
      {
        id: 'whatsapp_notif',
        category: 'integration',
        title: lang === 'en' ? 'Automated WhatsApp Alerts' : 'Notifikasi WhatsApp Otomatis',
        desc: lang === 'en' ? 'Real-time dispatch alerts directly to customer WhatsApp' : 'Kirim update pesanan dan resi otomatis via WhatsApp',
        target_field: 'kebutuhanIntegrasi',
        addition: 'WhatsApp Cloud API untuk notifikasi transaksi & alert real-time',
        badge: 'Integrasi',
      },
      {
        id: 'payment_midtrans',
        category: 'integration',
        title: 'Midtrans Payment Gateway (QRIS & VA)',
        desc: lang === 'en' ? 'Multi-channel payment with automated webhooks' : 'Pembayaran otomatis via Virtual Account Bank & QRIS',
        target_field: 'kebutuhanIntegrasi',
        addition: 'Midtrans Payment Gateway (Snap API, QRIS, Virtual Account BCA/Mandiri/BRI, Kartu Kredit)',
        badge: 'Pembayaran',
      },
      {
        id: 'excel_export',
        category: 'feature',
        title: lang === 'en' ? 'Export Excel (.xlsx) & PDF' : 'Ekspor Laporan Excel (.xlsx) & PDF',
        desc: lang === 'en' ? 'Instant operational reconciliation export' : 'Fitur unduh laporan operasional dan rekap data ke Excel dan PDF',
        target_field: 'fiturWajib',
        addition: 'Modul Ekspor Laporan Komprehensif: Unduh rekapitulasi operasional dan riwayat transaksi ke format Microsoft Excel (.xlsx) dan PDF resmi.',
        badge: 'Fitur MVP',
      },
      {
        id: 'approval_role',
        category: 'actor',
        title: lang === 'en' ? 'Supervisor Approval Role' : 'Tingkat Akses Supervisor / Approval',
        desc: lang === 'en' ? 'Multi-tier verification before critical execution' : 'Otorisasi persetujuan bertingkat sebelum data dieksekusi',
        target_field: 'aktorSistem',
        addition: 'Supervisor / Manajer: Otorisasi persetujuan berjenjang sebelum transaksi bernilai tinggi atau perubahan data krusial dieksekusi.',
        badge: 'Aktor & RBAC',
      },
      {
        id: 'refund_flow',
        category: 'workflow',
        title: lang === 'en' ? 'Cancellation & Refund Workflow' : 'Alur Pembatalan & Pengembalian Dana',
        desc: lang === 'en' ? 'Automated dispute handling and ledger refund' : 'Alur resmi pembatalan pesanan dan pencatatan refund otomatis',
        target_field: 'alurKerja',
        addition: 'Alur Pembatalan & Pengembalian Dana: Klien mengajukan pembatalan dengan alasan -> Staf memverifikasi -> Penyesuaian saldo dan pengiriman bukti refund otomatis.',
        badge: 'Alur Kerja',
      },
      {
        id: 'google_sso',
        category: 'feature',
        title: lang === 'en' ? '1-Click Google Sign-In' : 'Login 1-Klik Google (Google SSO)',
        desc: lang === 'en' ? 'Frictionless onboarding with Google accounts' : 'Login cepat dan aman dengan akun Google resmi',
        target_field: 'fiturWajib',
        addition: 'Autentikasi 1-Klik Google Sign-In (OAuth 2.0) untuk mempercepat pendaftaran dan kenyamanan pengguna.',
        badge: 'Fitur MVP',
      },
    ];
  });

  const [appliedSuggestionIds, setAppliedSuggestionIds] = useState(new Set());
  const [coPilotInput, setCoPilotInput] = useState('');
  const [isCoPilotLoading, setIsCoPilotLoading] = useState(false);

  // In-Card Interactive Add & Edit States
  const [activeAddCard, setActiveAddCard] = useState(null); // 'fiturWajib', 'aktorSistem', etc.
  const [newCardInputText, setNewCardInputText] = useState('');
  const [editingCardKey, setEditingCardKey] = useState(null); // 'namaBisnis', etc.

  // Toast Notification State
  const [toast, setToast] = useState(null);

  const showLocalToast = (type, message, title = '') => {
    if (window.showToast) {
      window.showToast({ type, message, title });
    }
    setToast({ type, message, title });
    setTimeout(() => {
      setToast(null);
    }, 3500);
  };

  // Anti-Spam Honeypots
  const [honeypot, setHoneypot] = useState('');
  const [honeypotWebsite, setHoneypotWebsite] = useState('');

  const fileInputRef = useRef(null);

  // Full 20 Fields Form Data
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
    return calculateClientCompleteness(formData, lang);
  }, [formData, lang]);

  useEffect(() => {
    // Theme initialization
    const savedTheme = localStorage.getItem('neriah_theme');
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      setIsDarkMode(true);
      document.documentElement.classList.add('dark');
    } else {
      setIsDarkMode(false);
      document.documentElement.classList.remove('dark');
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

  // Anti-Spam & AI Synthesis Submission
  const handleAnalyzeIdea = async (e) => {
    e?.preventDefault();
    setAnalysisError(null);

    // 1. Anti-Spam Check: Honeypot trap
    if (honeypot.trim() !== '' || honeypotWebsite.trim() !== '') {
      setViewMode('synthesized');
      return;
    }

    // 2. Minimum validation: 15 chars or attached document
    const cleanText = ideaText.trim();
    if (cleanText.length < 15 && attachedFiles.length === 0) {
      setAnalysisError(
        lang === 'en'
          ? 'Please enter at least 15 characters of your project idea, or attach a specification document.'
          : 'Mohon ceritakan ide proyek Anda minimal 15 karakter, atau lampirkan berkas dokumen spesifikasi.'
      );
      return;
    }

    // 3. Client-side anti-repetition heuristic
    if (/(.)\1{20,}/u.test(cleanText)) {
      setAnalysisError(
        lang === 'en'
          ? 'Spam pattern detected. Please enter a valid system or business description.'
          : 'Pola spam pengulangan karakter terdeteksi. Mohon masukkan deskripsi proyek yang valid.'
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

      // Populate synthesized blueprint data
      const synData = result.data || {};
      setFormData(prev => ({
        ...prev,
        ...synData,
        clientName: synData.clientName || prev.clientName,
        email: synData.email || prev.email,
        phone: synData.phone || prev.phone,
      }));

      if (synData.proactive_suggestions && Array.isArray(synData.proactive_suggestions)) {
        setProactiveSuggestions(synData.proactive_suggestions);
      }

      if (synData.phone) {
        let d = String(synData.phone).replace(/\D/g, '');
        if (d.startsWith('62')) d = d.substring(2);
        setPhoneDigits(d.replace(/^0+/, ''));
      }

      setConvertedMarkdown(result.converted_markdown || synData._meta?.converted_markdown || '');
      setViewMode('synthesized');
      window.scrollTo({ top: 350, behavior: 'smooth' });

      showLocalToast('success', lang === 'en' ? 'Blueprint successfully structured by AI!' : 'Blueprint berhasil disusun rapi oleh AI! Silakan tinjau dan lengkapi apa yang kurang.');

    } catch (err) {
      setAnalysisError(err.message || (lang === 'en' ? 'An error occurred. Please try again.' : 'Terjadi kendala. Silakan coba lagi.'));
    } finally {
      setIsAnalyzing(false);
      setAnalysisStep('');
    }
  };

  // Proactive Co-Pilot: Submit additional idea to AI
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
        throw new Error(result.message || (lang === 'en' ? 'Failed to position addition.' : 'Gagal menempatkan ide tambahan.'));
      }

      setFormData(prev => ({
        ...prev,
        ...(result.data || {})
      }));

      setCoPilotInput('');
      showLocalToast('success', result.message || (lang === 'en' ? 'Successfully added to blueprint!' : 'Kebutuhan berhasil disematkan ke blueprint!'), 'AI CO-PILOT');

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
      return {
        ...prev,
        [targetField]: updatedValue
      };
    });

    setAppliedSuggestionIds(prev => new Set([...prev, suggestion.id]));
    showLocalToast('success', `${suggestion.title} ${lang === 'en' ? 'added to blueprint!' : 'berhasil ditambahkan ke blueprint!'}`, 'SARAN PROAKTIF');
  };

  // In-Card: Add item to a numbered list
  const handleAddCardItem = (fieldKey) => {
    const text = newCardInputText.trim();
    if (!text) return;

    setFormData(prev => {
      if (fieldKey === 'kebutuhanIntegrasi') {
        const items = parseCommaList(prev[fieldKey]);
        items.push(text);
        return { ...prev, [fieldKey]: stringifyCommaList(items) };
      } else {
        const items = parseNumberedList(prev[fieldKey]);
        items.push(text);
        return { ...prev, [fieldKey]: stringifyNumberedList(items) };
      }
    });

    setNewCardInputText('');
    setActiveAddCard(null);
    showLocalToast('success', lang === 'en' ? 'New item added!' : 'Item baru berhasil ditambahkan!');
  };

  // In-Card: Delete item from list
  const handleDeleteCardItem = (fieldKey, index) => {
    setFormData(prev => {
      if (fieldKey === 'kebutuhanIntegrasi') {
        const items = parseCommaList(prev[fieldKey]);
        items.splice(index, 1);
        return { ...prev, [fieldKey]: stringifyCommaList(items) };
      } else {
        const items = parseNumberedList(prev[fieldKey]);
        items.splice(index, 1);
        return { ...prev, [fieldKey]: stringifyNumberedList(items) };
      }
    });
    showLocalToast('info', lang === 'en' ? 'Item removed' : 'Item dihapus');
  };

  // Copy MarkItDown Markdown
  const handleCopyMarkdown = () => {
    if (!convertedMarkdown) return;
    navigator.clipboard.writeText(convertedMarkdown);
    setCopySuccess(true);
    setTimeout(() => setCopySuccess(false), 2000);
  };

  // Download converted Markdown as .md file
  const handleDownloadMarkdown = () => {
    if (!convertedMarkdown) return;
    const blob = new Blob([convertedMarkdown], { type: 'text/markdown;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    const slug = (formData.namaBisnis || 'project_blueprint').toLowerCase().replace(/[^a-z0-9]+/g, '_');
    link.href = url;
    link.download = `${slug}_markitdown_spec.md`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  };

  // Quick Idea Template Click
  const applyTemplate = (text) => {
    setIdeaText(text);
    setAnalysisError(null);
  };

  // Phone input handling
  const handlePhoneDigitsChange = (e) => {
    let digits = e.target.value.replace(/\D/g, '');
    digits = digits.replace(/^0+/, '');
    setPhoneDigits(digits);
    setFormData(prev => ({
      ...prev,
      phone: digits ? `${selectedCountryCode}${digits}` : '',
    }));
  };

  // Final Submission to Lock Blueprint & Generate PRD
  const handleFinalSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setErrorMessage(null);

    // Validate PIC Contact
    if (!phoneDigits || phoneDigits.length < 7) {
      setErrorMessage(lang === 'en' ? 'Please provide a valid WhatsApp number for contract dispatch.' : 'Mohon masukkan nomor WhatsApp PIC yang valid untuk transmisi kontrak.');
      setIsSubmitting(false);
      return;
    }

    const payload = {
      nama_bisnis: formData.namaBisnis || 'Custom Digital Project',
      client_name: formData.clientName || formData.namaBisnis || 'Project PIC',
      email: formData.email,
      phone: `${selectedCountryCode}${phoneDigits}`,
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
              onClick={() => { setSuccessData(null); setViewMode('textarea'); }}
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
                : 'bg-zinc-900 border-emerald-500 text-emerald-400'
            }`}
          >
            {toast.type === 'error' ? (
              <AlertCircle className="w-4 h-4 text-red-400 shrink-0 mt-0.5" />
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
      
      {/* TOP TOOLBAR: LANGUAGE & THEME CONTROLS */}
      <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
        <div className="flex items-center gap-2">
          <span className="w-2.5 h-2.5 bg-emerald-500 rounded-none inline-block"></span>
          <span className="text-xs font-mono uppercase tracking-widest text-zinc-600 dark:text-zinc-400 font-bold">
            NERIAH PRO // PROJECT OS ARCHITECTURAL DISCOVERY
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

      {/* MODE SELECTOR TABS */}
      <div className="flex border-b border-zinc-200 dark:border-zinc-800 mb-8 font-mono text-xs uppercase font-bold tracking-wider">
        <button
          type="button"
          onClick={() => setViewMode(formData.namaBisnis ? 'synthesized' : 'textarea')}
          className={`py-3 px-5 border-b-2 transition flex items-center gap-2 ${
            viewMode === 'textarea' || viewMode === 'synthesized'
              ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-500/5'
              : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-white'
          }`}
        >
          <Sparkles className="w-4 h-4 text-emerald-500" />
          <span>{t.tabTextarea}</span>
        </button>
        <button
          type="button"
          onClick={() => setViewMode('manual')}
          className={`py-3 px-5 border-b-2 transition flex items-center gap-2 ${
            viewMode === 'manual'
              ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-500/5'
              : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-white'
          }`}
        >
          <Sliders className="w-4 h-4" />
          <span>{t.tabManual}</span>
        </button>
      </div>

      {/* =========================================================================
          VIEW MODE 1: PRIMARY TEXTAREA DISCOVERY STUDIO (RECOMMENDED & STREAMLINED)
          ========================================================================= */}
      {viewMode === 'textarea' && (
        <section className={panelClass}>
          
          <div className="border-b border-zinc-200 dark:border-zinc-800 pb-5 mb-6">
            <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block mb-1">
              {t.badge}
            </span>
            <h2 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {t.heroTitle}
            </h2>
            <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 mt-2 font-sans leading-relaxed">
              {t.heroSubtitle}
            </p>
          </div>

          {/* Quick Idea Templates */}
          <div className="mb-5">
            <span className="block text-[11px] font-mono uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-2">
              {t.sampleTemplates}
            </span>
            <div className="flex flex-wrap gap-2">
              <button
                type="button"
                onClick={() => applyTemplate(
                  lang === 'en'
                    ? "We need a nationwide freight logistics & fleet management system. 3 roles: Superadmin, Dispatcher, and Drivers. Drivers scan barcode waybills and upload photos. Real-time GPS container tracking and invoiced payment gateway."
                    : "Kami butuh platform ekspedisi dan manajemen armada truk logistik antar pulau. Terdapat 3 aktor: Superadmin, Koordinator Lapangan, dan Driver. Driver bisa scan barcode resi dan upload foto bukti kirim. Ada pelacakan posisi GPS real-time dan pembayaran invoice tempo."
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
                    ? "B2B heavy machinery equipment rental marketplace. Vendors upload machinery specs, clients request leasing contracts. Escrow down payment system and recurring monthly billing."
                    : "Marketplace rental alat berat B2B. Vendor mendaftarkan unit alat berat, penyewa mengajukan kontrak sewa. Sistem uang muka (DP) escrow dan penagihan sewa bulanan otomatis."
                )}
                className="px-2.5 py-1 text-xs font-mono bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 rounded-none transition text-left"
              >
                {t.tplRental}
              </button>
              <button
                type="button"
                onClick={() => applyTemplate(
                  lang === 'en'
                    ? "Enterprise HR Portal with GPS attendance, candidate resume screening, multi-tier approval workflows, KPI performance evaluation, and automated payroll reporting."
                    : "Portal HR perusahaan dengan presensi GPS, screening CV pelamar, alur persetujuan lembur/cuti berjenjang, evaluasi KPI berkala, dan rekap penggajian otomatis."
                )}
                className="px-2.5 py-1 text-xs font-mono bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 rounded-none transition text-left"
              >
                {t.tplHr}
              </button>
            </div>
          </div>

          <form onSubmit={handleAnalyzeIdea} className="space-y-6">
            
            {/* ANTI-SPAM HONEYPOT FIELDS (HIDDEN FROM LEGITIMATE USERS) */}
            <div aria-hidden="true" style={{ display: 'none', position: 'absolute', left: '-9999px' }}>
              <label htmlFor="_hp_check">Leave empty</label>
              <input
                id="_hp_check"
                type="text"
                name="_hp_check"
                value={honeypot}
                onChange={(e) => setHoneypot(e.target.value)}
                tabIndex="-1"
                autoComplete="off"
              />
              <label htmlFor="_website">Your Website</label>
              <input
                id="_website"
                type="text"
                name="_website"
                value={honeypotWebsite}
                onChange={(e) => setHoneypotWebsite(e.target.value)}
                tabIndex="-1"
                autoComplete="off"
              />
            </div>

            {/* PRIMARY TEXTAREA */}
            <div>
              <div className="flex items-center justify-between mb-1.5">
                <label className={labelClass}>
                  Ide / Visi Aplikasi & Operasional Bisnis Anda *
                </label>
                <span className="text-[11px] font-mono text-zinc-400">
                  {ideaText.length} {t.charCount}
                </span>
              </div>

              <textarea
                rows={8}
                value={ideaText}
                onChange={(e) => { setIdeaText(e.target.value); setAnalysisError(null); }}
                placeholder={t.textareaPlaceholder}
                className="w-full p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm focus:border-emerald-500 focus:ring-0 outline-none rounded-none transition font-sans leading-relaxed resize-y"
              />
            </div>

            {/* DOCUMENT UPLOAD & MICROSOFT MARKITDOWN CONVERSION DROPZONE */}
            <div className="border border-dashed border-zinc-300 dark:border-zinc-700 p-5 bg-zinc-50/50 dark:bg-zinc-950/50 rounded-none text-center">
              <input
                ref={fileInputRef}
                type="file"
                multiple
                accept=".pdf,.doc,.docx,.txt,.md,.rtf,.csv,.tsv,.xlsx,.pptx,.png,.jpg,.jpeg,.webp"
                onChange={handleFileChange}
                className="hidden"
                id="doc-upload-input"
              />
              <label htmlFor="doc-upload-input" className="cursor-pointer block">
                <Upload className="w-7 h-7 text-emerald-500 mx-auto mb-2" />
                <h4 className="text-xs font-mono uppercase tracking-wider font-bold text-zinc-800 dark:text-zinc-200">
                  {t.dropzoneTitle}
                </h4>
                <p className="text-xs text-zinc-500 dark:text-zinc-400 mt-1 font-sans">
                  {t.dropzoneSubtitle}
                </p>
                <span className="text-[10px] text-zinc-400 font-mono mt-1 block">
                  {t.dropzoneFormats}
                </span>
              </label>

              {/* Uploaded Files Chips */}
              {attachedFiles.length > 0 && (
                <div className="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-800 flex flex-wrap gap-2 justify-center">
                  {attachedFiles.map((file, idx) => (
                    <div
                      key={idx}
                      className="px-2.5 py-1 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 text-xs font-mono flex items-center gap-1.5 rounded-none"
                    >
                      <FileText className="w-3.5 h-3.5 text-emerald-500" />
                      <span className="truncate max-w-[180px]">{file.name}</span>
                      <span className="text-[10px] text-zinc-400">({(file.size / 1024).toFixed(0)}KB)</span>
                      <button
                        type="button"
                        onClick={() => removeFile(idx)}
                        className="text-zinc-400 hover:text-red-500 ml-1"
                      >
                        <X className="w-3.5 h-3.5" />
                      </button>
                    </div>
                  ))}
                </div>
              )}

              <div className="mt-3 text-[11px] font-mono text-emerald-600 dark:text-emerald-400/90 text-left bg-emerald-500/5 p-2.5 border border-emerald-500/20 rounded-none flex items-start gap-2">
                <Sparkles className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                <span>{t.markitdownNotice}</span>
              </div>
            </div>

            {/* Error Message */}
            {analysisError && (
              <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-xs font-mono flex items-center gap-2 rounded-none">
                <AlertCircle className="w-4 h-4 shrink-0" />
                <span>{analysisError}</span>
              </div>
            )}

            {/* Submit Button */}
            <div>
              <button
                type="submit"
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

          </form>

        </section>
      )}

      {/* =========================================================================
          VIEW MODE 2: SYNTHESIZED BLUEPRINT & PROACTIVE CO-PILOT WORKSPACE
          ========================================================================= */}
      {viewMode === 'synthesized' && (
        <div className="space-y-8">
          
          {/* Status Header Banner */}
          <div className="bg-emerald-500/10 border border-emerald-500/30 p-5 rounded-none flex items-start justify-between gap-4">
            <div className="flex items-start gap-3">
              <Sparkles className="w-6 h-6 text-emerald-500 shrink-0 mt-0.5" />
              <div>
                <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block">
                  AI SYNTHESIS COMPLETE // MARKITDOWN PROTOCOL
                </span>
                <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
                  {t.resultTitle}
                </h3>
                <p className="text-xs text-zinc-600 dark:text-zinc-400 mt-1 font-sans">
                  {t.resultSubtitle}
                </p>
              </div>
            </div>
            <button
              type="button"
              onClick={() => setViewMode('textarea')}
              className="px-3 py-1.5 border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs font-mono uppercase tracking-wider text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-none transition font-bold shrink-0 flex items-center gap-1.5"
            >
              <RotateCcw className="w-3.5 h-3.5" />
              <span>{t.resetIdea}</span>
            </button>
          </div>

          {/* =====================================================================
              PROACTIVE AI ASSISTANT: COMPLETENESS GAUGE & INTELLIGENT GUIDANCE
              ===================================================================== */}
          <section className="bg-zinc-900 text-white border-2 border-emerald-500/40 p-6 rounded-none space-y-5">
            
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-800 pb-4">
              <div>
                <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-400 font-bold block mb-1 flex items-center gap-1.5">
                  <Lightbulb className="w-4 h-4 text-emerald-400" />
                  {t.proactiveBadge}
                </span>
                <h3 className="text-base sm:text-lg font-black uppercase tracking-tight text-white">
                  {t.proactiveTitle}
                </h3>
                <p className="text-xs text-zinc-400 mt-1 font-sans leading-relaxed">
                  {t.proactiveDesc}
                </p>
              </div>

              {/* Completeness Meter */}
              <div className="bg-zinc-950 border border-zinc-800 p-3 rounded-none min-w-[200px] text-right font-mono">
                <div className="flex justify-between items-center text-xs mb-1.5">
                  <span className="text-zinc-400 text-[11px]">KESIAPAN SPESIFIKASI:</span>
                  <span className="font-bold text-emerald-400">{completeness.score}%</span>
                </div>
                <div className="w-full bg-zinc-800 h-2 rounded-none overflow-hidden mb-1">
                  <div 
                    className="bg-emerald-500 h-full transition-all duration-500"
                    style={{ width: `${completeness.score}%` }}
                  />
                </div>
                <span className="text-[10px] text-emerald-400 font-bold uppercase tracking-wider block">
                  STATUS: {completeness.status}
                </span>
              </div>
            </div>

            {/* Checklist items */}
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs font-mono">
              {completeness.checklist.map((item, idx) => (
                <div 
                  key={idx}
                  className={`p-2 border flex items-center gap-1.5 ${
                    item.completed 
                      ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300' 
                      : 'border-zinc-800 bg-zinc-950/60 text-zinc-500'
                  }`}
                >
                  {item.completed ? (
                    <Check className="w-3.5 h-3.5 text-emerald-400 shrink-0" />
                  ) : (
                    <span className="w-3.5 h-3.5 border border-zinc-600 rounded-none shrink-0 inline-block" />
                  )}
                  <span className="truncate text-[11px]">{item.label}</span>
                </div>
              ))}
            </div>

            {/* Proactive 1-Click Suggestion Chips */}
            {proactiveSuggestions.length > 0 && (
              <div className="pt-2">
                <span className="block text-xs font-mono uppercase tracking-wider text-emerald-400 font-bold mb-2">
                  {t.suggestionsTitle}
                </span>
                <div className="grid sm:grid-cols-2 gap-2">
                  {proactiveSuggestions.map((sug) => {
                    const isAdded = appliedSuggestionIds.has(sug.id);
                    return (
                      <div
                        key={sug.id}
                        className={`p-3 border rounded-none flex items-start justify-between gap-2 transition ${
                          isAdded 
                            ? 'border-emerald-500 bg-emerald-500/10' 
                            : 'border-zinc-800 bg-zinc-950 hover:border-zinc-700'
                        }`}
                      >
                        <div>
                          <div className="flex items-center gap-1.5 mb-1">
                            <span className="px-1.5 py-0.5 text-[9px] font-mono uppercase font-bold bg-zinc-800 text-zinc-300">
                              {sug.badge || 'Saran AI'}
                            </span>
                            <h5 className="text-xs font-bold text-white">
                              {sug.title}
                            </h5>
                          </div>
                          <p className="text-[11px] text-zinc-400 font-sans leading-tight">
                            {sug.desc}
                          </p>
                        </div>
                        <button
                          type="button"
                          disabled={isAdded}
                          onClick={() => handleApplySuggestion(sug)}
                          className={`px-2.5 py-1 text-xs font-mono uppercase font-bold rounded-none shrink-0 transition flex items-center gap-1 ${
                            isAdded
                              ? 'bg-emerald-500 text-black opacity-80 cursor-default'
                              : 'bg-zinc-800 hover:bg-emerald-600 hover:text-black text-zinc-200 border border-zinc-700'
                          }`}
                        >
                          {isAdded ? (
                            <>
                              <Check className="w-3 h-3" />
                              <span>Ditambahkan</span>
                            </>
                          ) : (
                            <>
                              <Plus className="w-3 h-3" />
                              <span>+ Tambah</span>
                            </>
                          )}
                        </button>
                      </div>
                    );
                  })}
                </div>
              </div>
            )}

            {/* Proactive AI Co-Pilot Input Bar */}
            <div className="pt-3 border-t border-zinc-800">
              <form onSubmit={handleCoPilotSubmit} className="flex flex-col sm:flex-row gap-2">
                <div className="relative flex-1">
                  <input
                    type="text"
                    value={coPilotInput}
                    onChange={(e) => setCoPilotInput(e.target.value)}
                    placeholder={t.coPilotPlaceholder}
                    disabled={isCoPilotLoading}
                    className="w-full px-4 py-3 bg-zinc-950 border border-zinc-700 text-white placeholder-zinc-500 text-xs font-sans focus:border-emerald-500 outline-none rounded-none"
                  />
                </div>
                <button
                  type="submit"
                  disabled={!coPilotInput.trim() || isCoPilotLoading}
                  className="px-5 py-3 bg-emerald-500 hover:bg-emerald-400 text-black font-mono text-xs font-bold uppercase tracking-wider rounded-none transition flex items-center justify-center gap-1.5 disabled:opacity-40 shrink-0"
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
              </form>
            </div>

          </section>

          {/* MARKITDOWN CONVERTED MARKDOWN (.MD) PANEL */}
          {convertedMarkdown && (
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 sm:p-6 rounded-none">
              <div className="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-3">
                <div className="flex items-center gap-2">
                  <FileCode className="w-4 h-4 text-emerald-500" />
                  <h4 className="text-xs font-mono uppercase tracking-wider font-bold text-zinc-900 dark:text-zinc-100">
                    {t.mdConvertedTitle}
                  </h4>
                </div>
                <div className="flex items-center gap-2">
                  <button
                    type="button"
                    onClick={handleCopyMarkdown}
                    className="px-2.5 py-1 text-xs font-mono border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-none transition flex items-center gap-1"
                  >
                    {copySuccess ? <Check className="w-3 h-3 text-emerald-500" /> : <Copy className="w-3 h-3" />}
                    <span>{copySuccess ? t.copiedMd : t.copyMd}</span>
                  </button>
                  <button
                    type="button"
                    onClick={handleDownloadMarkdown}
                    className="px-2.5 py-1 text-xs font-mono bg-zinc-900 text-white dark:bg-emerald-500 dark:text-black hover:opacity-90 rounded-none transition flex items-center gap-1 font-bold"
                  >
                    <Download className="w-3 h-3" />
                    <span>{t.downloadMd}</span>
                  </button>
                  <button
                    type="button"
                    onClick={() => setShowMarkdownViewer(prev => !prev)}
                    className="px-2 py-1 text-xs font-mono text-zinc-500 hover:text-zinc-900 dark:hover:text-white"
                  >
                    {showMarkdownViewer ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
                  </button>
                </div>
              </div>

              <p className="text-xs text-zinc-500 dark:text-zinc-400 mb-3 font-sans">
                {t.mdConvertedDesc}
              </p>

              {showMarkdownViewer && (
                <pre className="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-mono overflow-x-auto max-h-72 rounded-none whitespace-pre-wrap">
                  {convertedMarkdown}
                </pre>
              )}
            </div>
          )}

          {/* =====================================================================
              7 INTERACTIVE BLUEPRINT CARDS WITH INLINE ADD, EDIT & DELETE
              ===================================================================== */}
          <div className="grid md:grid-cols-2 gap-4">
            
            {/* Card 1: Project Identity & Business Problem */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none flex flex-col justify-between">
              <div>
                <div className="flex items-center justify-between mb-2">
                  <span className="text-[10px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold">
                    {t.cardProject}
                  </span>
                  <button
                    type="button"
                    onClick={() => setEditingCardKey(editingCardKey === 'project' ? null : 'project')}
                    className="text-xs font-mono text-zinc-500 hover:text-emerald-500 flex items-center gap-1"
                  >
                    <Edit2 className="w-3 h-3" />
                    <span>{editingCardKey === 'project' ? t.saveBtn : t.editBtn}</span>
                  </button>
                </div>

                {editingCardKey === 'project' ? (
                  <div className="space-y-3">
                    <input
                      type="text"
                      value={formData.namaBisnis}
                      onChange={(e) => setFormData(prev => ({ ...prev, namaBisnis: e.target.value }))}
                      placeholder="Nama Proyek / Aplikasi"
                      className={inputClass}
                    />
                    <textarea
                      rows={3}
                      value={formData.masalahUtama}
                      onChange={(e) => setFormData(prev => ({ ...prev, masalahUtama: e.target.value }))}
                      placeholder="Masalah utama yang ingin dipecahkan"
                      className={inputClass}
                    />
                  </div>
                ) : (
                  <>
                    <h4 className="text-base font-black text-zinc-900 dark:text-zinc-100 uppercase mb-2">
                      {formData.namaBisnis || 'Nama Proyek'}
                    </h4>
                    <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed">
                      {formData.masalahUtama || 'Belum diuraikan.'}
                    </p>
                  </>
                )}
              </div>
            </div>

            {/* Card 2: KPIs & Success Metrics */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none flex flex-col justify-between">
              <div>
                <div className="flex items-center justify-between mb-2">
                  <span className="text-[10px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold">
                    {t.cardProblem}
                  </span>
                  <button
                    type="button"
                    onClick={() => setActiveAddCard(activeAddCard === 'tujuanUtama' ? null : 'tujuanUtama')}
                    className="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold"
                  >
                    <Plus className="w-3 h-3" />
                    <span>{t.quickAddKpiBtn}</span>
                  </button>
                </div>

                <div className="space-y-1.5 mb-3">
                  {parseNumberedList(formData.tujuanUtama).map((kpi, idx) => (
                    <div key={idx} className="group flex items-start justify-between gap-2 p-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/60 dark:border-zinc-800/60 text-xs">
                      <span className="text-zinc-800 dark:text-zinc-200 font-sans">{idx + 1}. {kpi}</span>
                      <button
                        type="button"
                        onClick={() => handleDeleteCardItem('tujuanUtama', idx)}
                        className="text-zinc-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition"
                      >
                        <Trash2 className="w-3 h-3" />
                      </button>
                    </div>
                  ))}
                </div>

                {activeAddCard === 'tujuanUtama' && (
                  <div className="mt-2 flex gap-1.5">
                    <input
                      type="text"
                      value={newCardInputText}
                      onChange={(e) => setNewCardInputText(e.target.value)}
                      placeholder="Misal: Penurunan komplain pelanggan hingga 80%..."
                      className="flex-1 px-2.5 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                    />
                    <button
                      type="button"
                      onClick={() => handleAddCardItem('tujuanUtama')}
                      className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                    >
                      {t.saveBtn}
                    </button>
                  </div>
                )}
              </div>
            </div>

            {/* Card 3: System Actors & RBAC */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none flex flex-col justify-between">
              <div>
                <div className="flex items-center justify-between mb-2">
                  <span className="text-[10px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold">
                    {t.cardActors}
                  </span>
                  <button
                    type="button"
                    onClick={() => setActiveAddCard(activeAddCard === 'aktorSistem' ? null : 'aktorSistem')}
                    className="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold"
                  >
                    <Plus className="w-3 h-3" />
                    <span>{t.quickAddRoleBtn}</span>
                  </button>
                </div>

                <div className="space-y-1.5 mb-3">
                  {parseNumberedList(formData.aktorSistem).map((actor, idx) => (
                    <div key={idx} className="group flex items-start justify-between gap-2 p-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/60 dark:border-zinc-800/60 text-xs">
                      <span className="text-zinc-800 dark:text-zinc-200 font-sans">{actor}</span>
                      <button
                        type="button"
                        onClick={() => handleDeleteCardItem('aktorSistem', idx)}
                        className="text-zinc-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition"
                      >
                        <Trash2 className="w-3 h-3" />
                      </button>
                    </div>
                  ))}
                </div>

                {/* Quick Role Suggestions */}
                <div className="flex flex-wrap gap-1.5 mb-2">
                  {['+ Finance Staff', '+ Operator Gudang', '+ Supervisor', '+ Customer Eksternal', '+ Auditor'].map((rolePreset, idx) => (
                    <button
                      key={idx}
                      type="button"
                      onClick={() => {
                        const items = parseNumberedList(formData.aktorSistem);
                        items.push(`${rolePreset.replace('+ ', '')}: Akses khusus sesuai wewenang operasional`);
                        setFormData(prev => ({ ...prev, aktorSistem: stringifyNumberedList(items) }));
                        showLocalToast('success', `${rolePreset} ditambahkan ke Aktor!`);
                      }}
                      className="px-2 py-0.5 text-[10px] font-mono bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-400 border border-zinc-300 dark:border-zinc-700"
                    >
                      {rolePreset}
                    </button>
                  ))}
                </div>

                {activeAddCard === 'aktorSistem' && (
                  <div className="mt-2 flex gap-1.5">
                    <input
                      type="text"
                      value={newCardInputText}
                      onChange={(e) => setNewCardInputText(e.target.value)}
                      placeholder="Misal: Kasir Toko: Melayani pembayaran dan cetak kuitansi..."
                      className="flex-1 px-2.5 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                    />
                    <button
                      type="button"
                      onClick={() => handleAddCardItem('aktorSistem')}
                      className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                    >
                      {t.saveBtn}
                    </button>
                  </div>
                )}
              </div>
            </div>

            {/* Card 4: MVP Features (Phase 1) vs Roadmap (Phase 2) */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none flex flex-col justify-between">
              <div>
                <div className="flex items-center justify-between mb-2">
                  <span className="text-[10px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold">
                    {t.cardFeatures}
                  </span>
                  <button
                    type="button"
                    onClick={() => setActiveAddCard(activeAddCard === 'fiturWajib' ? null : 'fiturWajib')}
                    className="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold"
                  >
                    <Plus className="w-3 h-3" />
                    <span>{t.quickAddFeatureBtn}</span>
                  </button>
                </div>

                <div className="space-y-1.5 max-h-48 overflow-y-auto mb-3 pr-1">
                  {parseNumberedList(formData.fiturWajib).map((feat, idx) => (
                    <div key={idx} className="group flex items-start justify-between gap-2 p-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/60 dark:border-zinc-800/60 text-xs">
                      <span className="text-zinc-800 dark:text-zinc-200 font-sans leading-tight">
                        <strong className="text-emerald-600 dark:text-emerald-400">{idx + 1}.</strong> {feat}
                      </span>
                      <button
                        type="button"
                        onClick={() => handleDeleteCardItem('fiturWajib', idx)}
                        className="text-zinc-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition shrink-0"
                      >
                        <Trash2 className="w-3 h-3" />
                      </button>
                    </div>
                  ))}
                </div>

                {activeAddCard === 'fiturWajib' && (
                  <div className="mt-2 flex gap-1.5">
                    <input
                      type="text"
                      value={newCardInputText}
                      onChange={(e) => setNewCardInputText(e.target.value)}
                      placeholder="Misal: Cetak Struk Kasir Thermal Bluetooth..."
                      className="flex-1 px-2.5 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                    />
                    <button
                      type="button"
                      onClick={() => handleAddCardItem('fiturWajib')}
                      className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                    >
                      {t.saveBtn}
                    </button>
                  </div>
                )}
              </div>
            </div>

            {/* Card 5: Primary User Workflow */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none flex flex-col justify-between">
              <div>
                <div className="flex items-center justify-between mb-2">
                  <span className="text-[10px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold">
                    {t.cardWorkflow}
                  </span>
                  <button
                    type="button"
                    onClick={() => setActiveAddCard(activeAddCard === 'alurKerja' ? null : 'alurKerja')}
                    className="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold"
                  >
                    <Plus className="w-3 h-3" />
                    <span>{t.quickAddStepBtn}</span>
                  </button>
                </div>

                <div className="space-y-1.5 max-h-48 overflow-y-auto mb-3 pr-1">
                  {parseNumberedList(formData.alurKerja).map((step, idx) => (
                    <div key={idx} className="group flex items-start justify-between gap-2 p-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/60 dark:border-zinc-800/60 text-xs">
                      <span className="text-zinc-800 dark:text-zinc-200 font-sans leading-tight">
                        <strong className="text-emerald-600 dark:text-emerald-400 font-mono">Langkah {idx + 1}:</strong> {step}
                      </span>
                      <button
                        type="button"
                        onClick={() => handleDeleteCardItem('alurKerja', idx)}
                        className="text-zinc-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition shrink-0"
                      >
                        <Trash2 className="w-3 h-3" />
                      </button>
                    </div>
                  ))}
                </div>

                {activeAddCard === 'alurKerja' && (
                  <div className="mt-2 flex gap-1.5">
                    <input
                      type="text"
                      value={newCardInputText}
                      onChange={(e) => setNewCardInputText(e.target.value)}
                      placeholder="Misal: Klien melakukan verifikasi OTP via WhatsApp..."
                      className="flex-1 px-2.5 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                    />
                    <button
                      type="button"
                      onClick={() => handleAddCardItem('alurKerja')}
                      className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                    >
                      {t.saveBtn}
                    </button>
                  </div>
                )}
              </div>
            </div>

            {/* Card 6: Third-Party Integrations & Architecture */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none flex flex-col justify-between">
              <div>
                <div className="flex items-center justify-between mb-2">
                  <span className="text-[10px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold">
                    {t.cardInfra}
                  </span>
                  <button
                    type="button"
                    onClick={() => setActiveAddCard(activeAddCard === 'kebutuhanIntegrasi' ? null : 'kebutuhanIntegrasi')}
                    className="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold"
                  >
                    <Plus className="w-3 h-3" />
                    <span>{t.quickAddIntegrationBtn}</span>
                  </button>
                </div>

                {/* Integration Tags */}
                <div className="flex flex-wrap gap-1.5 mb-3">
                  {parseCommaList(formData.kebutuhanIntegrasi).map((intg, idx) => (
                    <span 
                      key={idx}
                      className="inline-flex items-center gap-1 px-2 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-mono border border-zinc-300 dark:border-zinc-700"
                    >
                      <span>{intg}</span>
                      <button
                        type="button"
                        onClick={() => handleDeleteCardItem('kebutuhanIntegrasi', idx)}
                        className="text-zinc-400 hover:text-red-500"
                      >
                        <X className="w-3 h-3" />
                      </button>
                    </span>
                  ))}
                </div>

                {/* Preset Chips */}
                <div className="flex flex-wrap gap-1.5 mb-3">
                  {['+ Midtrans Snap', '+ WhatsApp Gateway', '+ Google Maps API', '+ Cloudflare R2', '+ RajaOngkir Kurir'].map((preset, idx) => (
                    <button
                      key={idx}
                      type="button"
                      onClick={() => {
                        const items = parseCommaList(formData.kebutuhanIntegrasi);
                        const cleanPreset = preset.replace('+ ', '');
                        if (!items.includes(cleanPreset)) {
                          items.push(cleanPreset);
                          setFormData(prev => ({ ...prev, kebutuhanIntegrasi: stringifyCommaList(items) }));
                          showLocalToast('success', `${cleanPreset} ditambahkan ke Integrasi!`);
                        }
                      }}
                      className="px-2 py-0.5 text-[10px] font-mono bg-zinc-50 dark:bg-zinc-950 text-zinc-600 dark:text-zinc-400 hover:text-emerald-500 border border-zinc-200 dark:border-zinc-800"
                    >
                      {preset}
                    </button>
                  ))}
                </div>

                {activeAddCard === 'kebutuhanIntegrasi' && (
                  <div className="mt-2 flex gap-1.5 mb-3">
                    <input
                      type="text"
                      value={newCardInputText}
                      onChange={(e) => setNewCardInputText(e.target.value)}
                      placeholder="Misal: Mailgun Transactional Email API..."
                      className="flex-1 px-2.5 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                    />
                    <button
                      type="button"
                      onClick={() => handleAddCardItem('kebutuhanIntegrasi')}
                      className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                    >
                      {t.saveBtn}
                    </button>
                  </div>
                )}

                <div className="space-y-1 pt-2 border-t border-zinc-200 dark:border-zinc-800 font-mono text-[11px] text-zinc-600 dark:text-zinc-400">
                  <div className="flex justify-between">
                    <span>STACK:</span>
                    <span className="font-bold text-zinc-900 dark:text-zinc-100">Modern Monolith (Laravel 13 & Filament)</span>
                  </div>
                  <div className="flex justify-between">
                    <span>DATABASE:</span>
                    <span className="font-bold text-emerald-500">PostgreSQL Strict ULID (Keyset O(1))</span>
                  </div>
                  <div className="flex justify-between">
                    <span>ESTIMASI BUDGET:</span>
                    <span className="font-bold text-emerald-600 dark:text-emerald-400">{formData.kisaranBudget}</span>
                  </div>
                </div>

              </div>
            </div>

            {/* Card 7: Negative Boundary (Out of Scope - Anti Scope Creep) */}
            <div className="md:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none">
              <div className="flex items-center justify-between mb-2">
                <span className="text-[10px] font-mono uppercase tracking-widest text-amber-600 dark:text-amber-400 font-bold">
                  {t.cardScope}
                </span>
                <button
                  type="button"
                  onClick={() => setActiveAddCard(activeAddCard === 'outOfScope' ? null : 'outOfScope')}
                  className="text-xs font-mono text-amber-600 dark:text-amber-400 hover:underline flex items-center gap-1 font-bold"
                >
                  <Plus className="w-3 h-3" />
                  <span>{t.quickAddScopeBtn}</span>
                </button>
              </div>

              <div className="space-y-1.5 mb-2">
                {parseNumberedList(formData.outOfScope).map((scope, idx) => (
                  <div key={idx} className="group flex items-start justify-between gap-2 p-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/60 dark:border-zinc-800/60 text-xs">
                    <span className="text-zinc-700 dark:text-zinc-300 font-sans leading-tight">{scope}</span>
                    <button
                      type="button"
                      onClick={() => handleDeleteCardItem('outOfScope', idx)}
                      className="text-zinc-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition shrink-0"
                    >
                      <Trash2 className="w-3 h-3" />
                    </button>
                  </div>
                ))}
              </div>

              {activeAddCard === 'outOfScope' && (
                <div className="mt-2 flex gap-1.5">
                  <input
                    type="text"
                    value={newCardInputText}
                    onChange={(e) => setNewCardInputText(e.target.value)}
                    placeholder="Misal: Tidak mencakup pengadaan hardware printer atau mesin POS fisik..."
                    className="flex-1 px-2.5 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => handleAddCardItem('outOfScope')}
                    className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                  >
                    {t.saveBtn}
                  </button>
                </div>
              )}
            </div>

          </div>

          {/* OPTIONAL ACCORDION: REVIEW & EDIT ALL 20 DETAILED FIELDS */}
          <div className="border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 p-4 rounded-none">
            <button
              type="button"
              onClick={() => setShowDetailsAccordion(prev => !prev)}
              className="w-full flex items-center justify-between text-xs font-mono uppercase font-bold tracking-wider text-zinc-700 dark:text-zinc-300 hover:text-emerald-500 transition"
            >
              <span className="flex items-center gap-2">
                <Sliders className="w-4 h-4 text-emerald-500" />
                {t.toggleDetails}
              </span>
              {showDetailsAccordion ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
            </button>

            {showDetailsAccordion && (
              <div className="mt-5 pt-5 border-t border-zinc-200 dark:border-zinc-800 space-y-4">
                <div className="grid sm:grid-cols-2 gap-4">
                  <div>
                    <label className={labelClass}>Nama Proyek / Bisnis</label>
                    <input
                      type="text"
                      name="namaBisnis"
                      value={formData.namaBisnis}
                      onChange={(e) => setFormData(prev => ({ ...prev, namaBisnis: e.target.value }))}
                      className={inputClass}
                    />
                  </div>
                  <div>
                    <label className={labelClass}>Kebutuhan Integrasi Pihak Ketiga</label>
                    <input
                      type="text"
                      name="kebutuhanIntegrasi"
                      value={formData.kebutuhanIntegrasi}
                      onChange={(e) => setFormData(prev => ({ ...prev, kebutuhanIntegrasi: e.target.value }))}
                      className={inputClass}
                    />
                  </div>
                </div>

                <div>
                  <label className={labelClass}>Fitur Wajib MVP (Fase 1)</label>
                  <textarea
                    rows={4}
                    name="fiturWajib"
                    value={formData.fiturWajib}
                    onChange={(e) => setFormData(prev => ({ ...prev, fiturWajib: e.target.value }))}
                    className={inputClass}
                  />
                </div>

                <div>
                  <label className={labelClass}>Fitur Tambahan (Fase 2 - Roadmap)</label>
                  <textarea
                    rows={3}
                    name="fiturTambahan"
                    value={formData.fiturTambahan}
                    onChange={(e) => setFormData(prev => ({ ...prev, fiturTambahan: e.target.value }))}
                    className={inputClass}
                  />
                </div>

                <div>
                  <label className={labelClass}>Batasan Negatif (Out of Scope - Anti Scope Creep)</label>
                  <textarea
                    rows={3}
                    name="outOfScope"
                    value={formData.outOfScope}
                    onChange={(e) => setFormData(prev => ({ ...prev, outOfScope: e.target.value }))}
                    className={inputClass}
                  />
                </div>
              </div>
            )}
          </div>

          {/* PIC CONTACT VERIFICATION & CONTRACT LOCK FORM */}
          <form onSubmit={handleFinalSubmit} className={panelClass}>
            <div className="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
              <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block mb-1">
                FINAL STEP // VERIFIKASI & PENGUNCIAN LINGKUP
              </span>
              <h3 className="text-lg sm:text-xl font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                <Users className="w-5 h-5 text-emerald-500" />
                {t.contactTitle}
              </h3>
            </div>

            {errorMessage && (
              <div className="mb-6 p-4 bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-xs font-mono flex items-center gap-2 rounded-none">
                <AlertCircle className="w-4 h-4 shrink-0" />
                <span>{errorMessage}</span>
              </div>
            )}

            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
              {/* Nama PIC */}
              <div>
                <label className={labelClass}>{t.clientNameLabel} *</label>
                <input
                  type="text"
                  required
                  placeholder={t.clientNamePh}
                  value={formData.clientName}
                  onChange={(e) => setFormData(prev => ({ ...prev, clientName: e.target.value }))}
                  className={inputClass}
                />
              </div>

              {/* Email PIC */}
              <div>
                <label className={labelClass}>{t.emailLabel} *</label>
                <input
                  type="email"
                  required
                  placeholder={t.emailPh}
                  value={formData.email}
                  onChange={(e) => setFormData(prev => ({ ...prev, email: e.target.value }))}
                  className={inputClass}
                />
              </div>

              {/* WhatsApp PIC dengan Selector Kode Negara E.164 */}
              <div>
                <label className={labelClass}>{t.phoneLabel} *</label>
                <div className="flex rounded-none border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950 focus-within:border-emerald-500 transition">
                  <select
                    value={selectedCountryCode}
                    onChange={(e) => {
                      const code = e.target.value;
                      setSelectedCountryCode(code);
                      setFormData(prev => ({
                        ...prev,
                        phone: phoneDigits ? `${code}${phoneDigits}` : '',
                      }));
                    }}
                    className="bg-transparent text-xs font-mono font-bold text-zinc-700 dark:text-zinc-300 py-2.5 pl-2.5 pr-1 outline-none border-r border-zinc-300 dark:border-zinc-700 cursor-pointer"
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
                    placeholder={t.phonePh}
                    value={phoneDigits}
                    onChange={handlePhoneDigitsChange}
                    className="w-full px-3 py-2.5 bg-transparent text-zinc-900 dark:text-zinc-100 text-sm outline-none font-mono"
                  />
                </div>
                <span className="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1 block font-mono">
                  {t.phoneNote}
                </span>
              </div>
            </div>

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
          </form>

        </div>
      )}

      {/* =========================================================================
          VIEW MODE 3: DETAILED 20-FIELD MANUAL QUESTIONNAIRE (FOR POWER USERS)
          ========================================================================= */}
      {viewMode === 'manual' && (
        <section className={panelClass}>
          <div className="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6 flex items-center justify-between">
            <div>
              <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block mb-1">
                KUESIONER TEKNIS LENGKAP
              </span>
              <h3 className="text-xl font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
                20 Parameter Kuesioner PRD
              </h3>
            </div>
            <button
              type="button"
              onClick={() => setViewMode('textarea')}
              className="px-3 py-1.5 border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950 text-xs font-mono uppercase tracking-wider text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-none transition font-bold"
            >
              ← Kembali ke Textarea Studio
            </button>
          </div>

          <form onSubmit={handleFinalSubmit} className="space-y-6">
            
            {/* PIC */}
            <div className="grid sm:grid-cols-3 gap-4">
              <div>
                <label className={labelClass}>{t.clientNameLabel} *</label>
                <input
                  type="text"
                  required
                  placeholder={t.clientNamePh}
                  value={formData.clientName}
                  onChange={(e) => setFormData(prev => ({ ...prev, clientName: e.target.value }))}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>{t.emailLabel} *</label>
                <input
                  type="email"
                  required
                  placeholder={t.emailPh}
                  value={formData.email}
                  onChange={(e) => setFormData(prev => ({ ...prev, email: e.target.value }))}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>{t.phoneLabel} *</label>
                <div className="flex rounded-none border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950">
                  <select
                    value={selectedCountryCode}
                    onChange={(e) => {
                      const code = e.target.value;
                      setSelectedCountryCode(code);
                      setFormData(prev => ({ ...prev, phone: phoneDigits ? `${code}${phoneDigits}` : '' }));
                    }}
                    className="bg-transparent text-xs font-mono font-bold text-zinc-700 dark:text-zinc-300 py-2.5 pl-2 border-r border-zinc-300 dark:border-zinc-700"
                  >
                    {countryList.map((c) => (
                      <option key={c.code} value={c.code} className="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">
                        {c.emoji || '🌐'} {c.code}
                      </option>
                    ))}
                  </select>
                  <input
                    type="tel"
                    required
                    placeholder={t.phonePh}
                    value={phoneDigits}
                    onChange={handlePhoneDigitsChange}
                    className="w-full px-3 py-2.5 bg-transparent text-zinc-900 dark:text-zinc-100 text-sm outline-none font-mono"
                  />
                </div>
              </div>
            </div>

            <div>
              <label className={labelClass}>1. Nama Bisnis / Proyek *</label>
              <input
                type="text"
                required
                value={formData.namaBisnis}
                onChange={(e) => setFormData(prev => ({ ...prev, namaBisnis: e.target.value }))}
                className={inputClass}
              />
            </div>

            <div className="grid sm:grid-cols-2 gap-4">
              <div>
                <label className={labelClass}>2. Masalah Utama yang Dihadapi</label>
                <textarea
                  rows={3}
                  value={formData.masalahUtama}
                  onChange={(e) => setFormData(prev => ({ ...prev, masalahUtama: e.target.value }))}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>3. Tujuan Utama / KPI Sukses</label>
                <textarea
                  rows={3}
                  value={formData.tujuanUtama}
                  onChange={(e) => setFormData(prev => ({ ...prev, tujuanUtama: e.target.value }))}
                  className={inputClass}
                />
              </div>
            </div>

            <div className="grid sm:grid-cols-2 gap-4">
              <div>
                <label className={labelClass}>4. Target Audiens</label>
                <textarea
                  rows={2}
                  value={formData.targetAudiens}
                  onChange={(e) => setFormData(prev => ({ ...prev, targetAudiens: e.target.value }))}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>5. Aktor Sistem (Role & Akses RBAC)</label>
                <textarea
                  rows={2}
                  value={formData.aktorSistem}
                  onChange={(e) => setFormData(prev => ({ ...prev, aktorSistem: e.target.value }))}
                  className={inputClass}
                />
              </div>
            </div>

            <div>
              <label className={labelClass}>6. Fitur Wajib (Fase 1 - MVP)</label>
              <textarea
                rows={4}
                value={formData.fiturWajib}
                onChange={(e) => setFormData(prev => ({ ...prev, fiturWajib: e.target.value }))}
                className={inputClass}
              />
            </div>

            <div>
              <label className={labelClass}>7. Fitur Tambahan (Fase 2 - Roadmap)</label>
              <textarea
                rows={3}
                value={formData.fiturTambahan}
                onChange={(e) => setFormData(prev => ({ ...prev, fiturTambahan: e.target.value }))}
                className={inputClass}
              />
            </div>

            <div>
              <label className={labelClass}>8. Alur Kerja Utama (Workflow)</label>
              <textarea
                rows={3}
                value={formData.alurKerja}
                onChange={(e) => setFormData(prev => ({ ...prev, alurKerja: e.target.value }))}
                className={inputClass}
              />
            </div>

            <div className="grid sm:grid-cols-2 gap-4">
              <div>
                <label className={labelClass}>9. Integrasi Pihak Ketiga</label>
                <input
                  type="text"
                  value={formData.kebutuhanIntegrasi}
                  onChange={(e) => setFormData(prev => ({ ...prev, kebutuhanIntegrasi: e.target.value }))}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>10. Referensi Desain / UI</label>
                <input
                  type="text"
                  value={formData.referensiDesain}
                  onChange={(e) => setFormData(prev => ({ ...prev, referensiDesain: e.target.value }))}
                  className={inputClass}
                />
              </div>
            </div>

            <div className="grid sm:grid-cols-3 gap-4">
              <div>
                <label className={labelClass}>11. Durasi Target (Hari Kerja)</label>
                <input
                  type="text"
                  value={formData.durasiHari}
                  onChange={(e) => setFormData(prev => ({ ...prev, durasiHari: e.target.value }))}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>12. Skala Pengguna</label>
                <input
                  type="text"
                  value={formData.skalaPengguna}
                  onChange={(e) => setFormData(prev => ({ ...prev, skalaPengguna: e.target.value }))}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>13. Kisaran Budget</label>
                <input
                  type="text"
                  value={formData.kisaranBudget}
                  onChange={(e) => setFormData(prev => ({ ...prev, kisaranBudget: e.target.value }))}
                  className={inputClass}
                />
              </div>
            </div>

            <div>
              <label className={labelClass}>14. Batasan Negatif (Out of Scope)</label>
              <textarea
                rows={2}
                value={formData.outOfScope}
                onChange={(e) => setFormData(prev => ({ ...prev, outOfScope: e.target.value }))}
                className={inputClass}
              />
            </div>

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

          </form>
        </section>
      )}

    </div>
  );
}
