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
  Award
} from 'lucide-react';

const TRANSLATIONS = {
  id: {
    topBadge: "PROJECT OS // ARCHITECTURAL DISCOVERY WORKSPACE",
    headerTitle: "Blueprint Arsitektur & Kuesioner Spesifikasi Sistem",
    headerSubtitle: "Lengkapi 26 parameter arsitektur terstruktur di bawah ini untuk menghasilkan dokumen Ultimate PRD dan skema basis data PostgreSQL Strict ULID siap bangun. Anda dapat mengisi form secara langsung atau gunakan Ruang Ide Cepat & MarkItDown di bawah untuk mengisi otomatis.",
    
    // Auto-save & Status
    autoSaveSaved: "Tersimpan Otomatis",
    autoSaveSaving: "Menyimpan ke Cloud...",
    autoSaveLocal: "Tersimpan di Perangkat",
    readinessLabel: "Skor Kesiapan Spesifikasi:",
    aiTierLabel: "AI Tier: Gemini Flash (Efisien & Zero-Boncos)",

    // Quick Idea & MarkItDown Studio
    studioToggleOpen: "Tutup Ruang Ide & Dokumen MarkItDown",
    studioToggleClosed: "Buka Ruang Ide Cepat & Unggah Dokumen (Isi Otomatis 26 Field)",
    studioBadge: "AI ACCELERATOR // MICROSOFT MARKITDOWN REPLICA",
    studioTitle: "Ceritakan Visi Proyek atau Lampirkan Dokumen Spesifikasi",
    studioDesc: "Ketik ringkasan ide bisnis atau lampirkan dokumen (PDF, Word, Excel, PowerPoint, Catatan, Wireframe). Dokumen otomatis dikonversi ke format Markdown (.md) murni oleh MarkItDown secara lokal di server sebelum dibaca AI, memangkas >80% token ekstraksi biner dan otomatis mengisi 26 parameter di bawah ini.",
    
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

    synthesizeBtn: "✨ Sintesis Ide & Isi 26 Parameter Form ↓",
    synthesizingBtn: "Menganalisis & Mengisi Form...",

    step1: "Mengonversi berkas dokumen via MarkItDown ke Markdown (.md)...",
    step2: "Menganalisis domain bisnis, aktor RBAC & alur kerja...",
    step3: "Menyusun skema arsitektur & mengisi 26 parameter blueprint...",

    // Proactive Guidance
    proactiveBadge: "ASISTEN PROAKTIF AI // PANDUAN KELENGKAPAN SPESIFIKASI",
    proactiveTitle: "Rekomendasi Cerdas untuk Menyempurnakan Arsitektur",
    proactiveDesc: "Berikut adalah aspek krusial yang sering terlewatkan dalam spesifikasi perangkat lunak. Klik salah satu rekomendasi untuk langsung menyematkannya ke dalam form di bawah ini.",
    suggestionsTitle: "💡 Rekomendasi Fitur & Aspek Kritis (Klik untuk Menambahkan):",
    coPilotPlaceholder: "Punya ide tambahan atau ingin melengkapi sesuatu? Ketik di sini (misal: 'Tambahkan alur pembatalan pesanan dan notifikasi WA driver')...",
    coPilotSubmitBtn: "✨ Lengkapi via AI Flash",
    coPilotProcessing: "AI Memproses Penempatan...",

    // 6 Architectural Blocks
    blockA: "BLOK A: IDENTITAS PROYEK & TUJUAN BISNIS",
    blockADesc: "Fondasi domain bisnis, masalah utama yang dihadapi, dan tolak ukur keberhasilan.",
    
    blockB: "BLOK B: TARGET PENGGUNA, RBAC & PLATFORM PERANGKAT",
    blockBDesc: "Pemetaan profil pengguna, hak akses RBAC, dan sasaran perangkat aksesibilitas.",

    blockC: "BLOK C: FITUR INTI MVP, ALUR KERJA & MIGRASI DATA",
    blockCDesc: "Daftar fitur MVP prioritas, alur operasional, dan kepastian migrasi data lama.",

    blockD: "BLOK D: INTEGRASI, ESTETIKA & INFRASTRUKTUR HOSTING",
    blockDDesc: "Koneksi gateway eksternal, referensi antarmuka UI/UX, dan kepemilikan server.",

    blockE: "BLOK E: TIMELINE, SKALA, KEAMANAN & BATASAN RUANG LINGKUP",
    blockEDesc: "Kapasitas trafik, standar keamanan OWASP, kepatuhan hukum, dan proteksi dari scope creep.",

    blockF: "BLOK F: ANGGARAN, GARANSI SLA, TERMIN & PENGESAHAN PIC",
    blockFDesc: "Alokasi investasi, jaminan garansi bug, skema termin pembayaran, dan identitas PIC resmi.",

    // Action Buttons
    saveBtn: "Simpan",
    cancelBtn: "Batal",
    deleteBtn: "Hapus",
    lockBtn: "Kunci Blueprint & Terbitkan Dokumen Ultimate PRD",
    lockingBtn: "Mengunci Blueprint & Menerbitkan Dokumen PRD...",

    successTitle: "Blueprint Berhasil Disinkronkan!",
    successDesc: "Seluruh 26 parameter arsitektur proyek telah dikunci dan dokumen Ultimate PRD dengan skema PostgreSQL Strict ULID siap diunduh.",
    openPrdBtn: "Buka Dokumen Ultimate PRD",
    newProjectBtn: "Kirim Proyek Lainnya",
    daysSuffix: "Hari Kerja",

    // Floating Left Index
    indexTitle: "Index Navigasi",
    indexSubtitle: "Pindah cepat tanpa scrolling",
    indexSearchPlaceholder: "Cari blok / parameter...",
    indexExpandAll: "Buka Semua",
    indexCollapseAll: "Tutup Semua",
    indexHidePanel: "Sembunyikan Panel",
    indexShowPanel: "Buka Index Navigasi",
    indexActiveStatus: "FOKUS AKTIF",
    indexJumpAction: "Fokuskan Blok",
    indexBackToTop: "Ke Atas",
    indexSubmitBlock: "Kunci & Terbitkan PRD",
    indexSubmitDesc: "Finalisasi 26 parameter arsitektur",
    indexGroupStudio: "Ruang Kerja AI & Ide",
    indexGroupStudioDesc: "Sintesis cepat & panduan proaktif",
    indexGroupSpec1: "Spesifikasi Inti (Blok A-C)",
    indexGroupSpec1Desc: "Identitas bisnis, RBAC & fitur MVP",
    indexGroupSpec2: "Arsitektur & Legal (Blok D-F)",
    indexGroupSpec2Desc: "Hosting, skala, budget & PIC",
    indexGroupFinal: "Finalisasi Spesifikasi",
    indexToggleDetails: "Rincian Parameter",
    indexExpandDetails: "+ Rincian",
    indexCollapseDetails: "- Rincian",
    indexSubFilled: "Terisi",
    indexSubEmpty: "Kosong",
  },
  en: {
    topBadge: "PROJECT OS // ARCHITECTURAL DISCOVERY WORKSPACE",
    headerTitle: "Architecture Blueprint & System Specification Form",
    headerSubtitle: "Complete the 26 structured architectural parameters below to generate the Ultimate PRD document and PostgreSQL Strict ULID schema. Fill directly or use the Quick Idea Studio & MarkItDown uploader below to auto-populate.",
    
    // Auto-save & Status
    autoSaveSaved: "Auto-Saved",
    autoSaveSaving: "Saving to Cloud...",
    autoSaveLocal: "Saved on Device",
    readinessLabel: "Specification Readiness Score:",
    aiTierLabel: "AI Tier: Gemini Flash (Token-Efficient & Zero-Waste)",

    // Quick Idea & MarkItDown Studio
    studioToggleOpen: "Collapse Idea Studio & Documents",
    studioToggleClosed: "Open Quick Idea Studio & Document Uploader (Auto-Fill 26 Fields)",
    studioBadge: "AI ACCELERATOR // MICROSOFT MARKITDOWN REPLICA",
    studioTitle: "Describe Project Vision or Attach Specification Documents",
    studioDesc: "Type your vision or attach documents (PDF, Word, Excel, PowerPoint, Wireframes). Documents are automatically converted into pure Markdown (.md) by MarkItDown locally on the server before AI ingestion, saving >80% tokens and auto-populating all 26 fields below.",
    
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

    synthesizeBtn: "✨ Synthesize Idea & Auto-Fill 26 Fields ↓",
    synthesizingBtn: "Analyzing & Populating...",

    step1: "Converting documents to Markdown (.md) via MarkItDown...",
    step2: "Analyzing business domain, RBAC actors & user flows...",
    step3: "Structuring architecture & populating 26 blueprint parameters...",

    // Proactive Guidance
    proactiveBadge: "PROACTIVE AI ASSISTANT // SPECIFICATION COMPLETENESS",
    proactiveTitle: "Smart Architectural Recommendations",
    proactiveDesc: "Here are critical requirements commonly overlooked in enterprise specifications. Click any suggestion to instantly append it into the form below.",
    suggestionsTitle: "💡 Critical Features & Recommended Aspects (Click to Add):",
    coPilotPlaceholder: "Have additional requirements? Type here (e.g. 'Add cancellation flow and driver WhatsApp notification')...",
    coPilotSubmitBtn: "✨ Append via AI Flash",
    coPilotProcessing: "AI Positioning Addition...",

    // 6 Architectural Blocks
    blockA: "BLOCK A: PROJECT IDENTITY & BUSINESS GOALS",
    blockADesc: "Business domain foundation, core problem statement, and primary KPIs.",
    
    blockB: "BLOCK B: TARGET USERS, RBAC & DEVICE PLATFORMS",
    blockBDesc: "End-user demographics, access control matrix, and target client form factors.",

    blockC: "BLOCK C: CORE MVP FEATURES, WORKFLOW & DATA MIGRATION",
    blockCDesc: "Phase 1 priority features, operational flows, and legacy data migration scope.",

    blockD: "BLOCK D: INTEGRATIONS, AESTHETICS & HOSTING INFRASTRUCTURE",
    blockDDesc: "External gateways, UI/UX benchmark references, and server infrastructure.",

    blockE: "BLOCK E: TIMELINE, SCALE, SECURITY & SCOPE BOUNDARIES",
    blockEDesc: "Traffic concurrency, OWASP security standards, legal compliance, and anti scope-creep boundaries.",

    blockF: "BLOCK F: BUDGET, WARRANTY SLA, MILESTONES & PIC VERIFICATION",
    blockFDesc: "Investment allocation, post-launch bug warranty, payment milestone terms, and authorized PIC.",

    // Action Buttons
    saveBtn: "Save",
    cancelBtn: "Cancel",
    deleteBtn: "Delete",
    lockBtn: "Lock Blueprint & Generate Ultimate PRD Document",
    lockingBtn: "Locking Blueprint & Issuing PRD Document...",

    successTitle: "Blueprint Synchronized Successfully!",
    successDesc: "All 26 project architecture parameters have been locked into an enterprise Ultimate PRD and PostgreSQL Strict ULID schema.",
    openPrdBtn: "Open Ultimate PRD Document",
    newProjectBtn: "Submit Another Project",
    daysSuffix: "Working Days",

    // Floating Left Index
    indexTitle: "Block Directory",
    indexSubtitle: "Jump directly to blocks without scrolling fatigue",
    indexSearchPlaceholder: "Filter blocks / params...",
    indexExpandAll: "Expand All",
    indexCollapseAll: "Collapse All",
    indexHidePanel: "Collapse Rail",
    indexShowPanel: "Open Directory",
    indexActiveStatus: "IN FOCUS",
    indexJumpAction: "Focus Block",
    indexBackToTop: "Top",
    indexSubmitBlock: "Lock & Generate PRD",
    indexSubmitDesc: "Finalize all 26 parameters",
    indexGroupStudio: "AI Workspace & Ideas",
    indexGroupStudioDesc: "Quick synthesis & proactive co-pilot",
    indexGroupSpec1: "Core Specification (Blocks A-C)",
    indexGroupSpec1Desc: "Identity, RBAC & MVP features",
    indexGroupSpec2: "Architecture & Legal (Blocks D-F)",
    indexGroupSpec2Desc: "Hosting, scale, budget & PIC",
    indexGroupFinal: "Specification Finalization",
    indexToggleDetails: "Parameter Details",
    indexExpandDetails: "+ Details",
    indexCollapseDetails: "- Details",
    indexSubFilled: "Filled",
    indexSubEmpty: "Empty",
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

// Dynamic completeness score calculator (25 Parameters mapped to 100% score)
function calculateCompleteness(data, lang) {
  const isEn = lang === 'en';
  const checklist = [
    { key: 'namaBisnis', label: isEn ? 'Project Name' : 'Nama Proyek', weight: 7, completed: !!data.namaBisnis && data.namaBisnis.length >= 3 },
    { key: 'masalahUtama', label: isEn ? 'Core Problem' : 'Masalah Bisnis', weight: 8, completed: !!data.masalahUtama && data.masalahUtama.length >= 15 },
    { key: 'tujuanUtama', label: isEn ? 'Success Metrics (KPIs)' : 'Tolak Ukur Sukses (KPI)', weight: 6, completed: !!data.tujuanUtama && data.tujuanUtama.length >= 10 },
    { key: 'targetAudiens', label: isEn ? 'Target Audience' : 'Target Audiens', weight: 5, completed: !!data.targetAudiens && data.targetAudiens.length >= 5 },
    { key: 'aktorSistem', label: isEn ? 'System Actors & RBAC' : 'Pengguna & Aktor RBAC', weight: 8, completed: !!data.aktorSistem && data.aktorSistem.length >= 10 },
    { key: 'targetPlatform', label: isEn ? 'Target Device / Platform' : 'Platform & Perangkat', weight: 5, completed: !!data.targetPlatform && data.targetPlatform.length >= 5 },
    { key: 'fiturWajib', label: isEn ? 'MVP Features (Phase 1)' : 'Fitur Wajib MVP (Fase 1)', weight: 15, completed: !!data.fiturWajib && data.fiturWajib.length >= 20 },
    { key: 'fiturTambahan', label: isEn ? 'Roadmap (Phase 2)' : 'Roadmap Fitur (Fase 2)', weight: 5, completed: !!data.fiturTambahan && data.fiturTambahan.length >= 10 },
    { key: 'alurKerja', label: isEn ? 'User Workflow' : 'Alur Kerja Utama (User Flow)', weight: 10, completed: !!data.alurKerja && data.alurKerja.length >= 15 },
    { key: 'migrasiData', label: isEn ? 'Data Migration Scope' : 'Migrasi Data Warisan', weight: 4, completed: !!data.migrasiData && data.migrasiData.length >= 5 },
    { key: 'kebutuhanIntegrasi', label: isEn ? 'Third-Party Integrations' : 'Integrasi Pihak Ketiga', weight: 6, completed: !!data.kebutuhanIntegrasi && data.kebutuhanIntegrasi.length >= 4 },
    { key: 'referensiDesain', label: isEn ? 'UI/UX Design Style' : 'Referensi Desain UI/UX', weight: 3, completed: !!data.referensiDesain && data.referensiDesain.length >= 5 },
    { key: 'kesiapanAset', label: isEn ? 'Digital Asset Readiness' : 'Kesiapan Aset Digital', weight: 3, completed: !!data.kesiapanAset && data.kesiapanAset.length >= 4 },
    { key: 'preferensiHosting', label: isEn ? 'Hosting & Server' : 'Infrastruktur Hosting', weight: 4, completed: !!data.preferensiHosting && data.preferensiHosting.length >= 5 },
    { key: 'durasiHari', label: isEn ? 'Timeline Days' : 'Target Durasi Hari', weight: 2, completed: !!data.durasiHari },
    { key: 'targetWaktu', label: isEn ? 'Target Release' : 'Target Waktu Rilis', weight: 2, completed: !!data.targetWaktu },
    { key: 'skalaPengguna', label: isEn ? 'User Scale' : 'Skala Trafik Pengguna', weight: 2, completed: !!data.skalaPengguna },
    { key: 'jangkauanPasar', label: isEn ? 'Market Reach' : 'Jangkauan Pasar', weight: 2, completed: !!data.jangkauanPasar },
    { key: 'outOfScope', label: isEn ? 'Negative Scope (Anti Creep)' : 'Batasan Negatif (Out of Scope)', weight: 4, completed: !!data.outOfScope && data.outOfScope.length >= 10 },
    { key: 'kepatuhanKeamanan', label: isEn ? 'Security Standards' : 'Standar Keamanan', weight: 2, completed: !!data.kepatuhanKeamanan },
    { key: 'kisaranBudget', label: isEn ? 'Budget Range' : 'Kisaran Budget', weight: 2, completed: !!data.kisaranBudget },
    { key: 'garansiSla', label: isEn ? 'Warranty & Git Handover' : 'Garansi Bug & Handover Git', weight: 2, completed: !!data.garansiSla && data.garansiSla.length >= 5 },
    { key: 'terminPembayaran', label: isEn ? 'Payment Milestones' : 'Termin Pembayaran', weight: 2, completed: !!data.terminPembayaran && data.terminPembayaran.length >= 5 },
    { key: 'clientName', label: isEn ? 'PIC Name' : 'Nama Lengkap PIC', weight: 3, completed: !!data.clientName && data.clientName.length >= 3 },
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
  const [autoSaveStatus, setAutoSaveStatus] = useState('idle');
  const [lastSavedTime, setLastSavedTime] = useState(() => {
    const tz = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    return `${tz} WIB`;
  });
  const autoSaveDebounceRef = useRef(null);
  const draftIdRef = useRef(initialData._draft_id || null);

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
    kisaranBudget: initialData.kisaranBudget || 'Rp 15.000.000 - Rp 35.000.000 (Growth / Custom Business Portal - Multi-Role & Gateway)',
    garansiSla: initialData.garansiSla || '30 Hari Garansi Bug Pascameluncur Bebas Biaya + Penyerahan Akses Penuh Private Repo GitHub',
    terminPembayaran: initialData.terminPembayaran || 'Termin Standar 50/50: 50% DP Kickoff & 50% Pelunasan setelah lolos UAT & Serah Terima Kunci (via Midtrans Snap)',
    clientName: initialData.clientName || '',
    email: initialData.email || '',
    phone: initialData.phone || '',
  });

  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState(null);
  const [successData, setSuccessData] = useState(null);
  const isLocked = !!successData;

  // Dynamic Completeness Score
  const completeness = useMemo(() => {
    return calculateCompleteness(formData, lang);
  }, [formData, lang]);

  // Floating Left Index & Scroll-Spy States
  const [activeSection, setActiveSection] = useState('section-ide-studio');
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
    spec1: true,
    spec2: true,
    final: true,
  });

  const toggleAccordionGroup = useCallback((grp) => {
    setOpenAccordionGroups(prev => ({ ...prev, [grp]: !prev[grp] }));
  }, []);

  const expandAllGroups = useCallback(() => {
    setOpenAccordionGroups({
      studio: true,
      spec1: true,
      spec2: true,
      final: true,
    });
  }, []);

  const collapseAllGroups = useCallback(() => {
    setOpenAccordionGroups({
      studio: false,
      spec1: false,
      spec2: false,
      final: false,
    });
  }, []);

  // Sub-block detail toggle state
  const [expandedSubBlocks, setExpandedSubBlocks] = useState({
    'section-block-a': true,
  });

  const toggleSubBlock = useCallback((secId, e) => {
    e?.stopPropagation();
    setExpandedSubBlocks(prev => ({
      ...prev,
      [secId]: !prev[secId],
    }));
  }, []);

  const [indexTopOffset, setIndexTopOffset] = useState(260);

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
    setMobileDrawerOpen(false);

    // Visual pulse outline
    el.classList.add('outline-2', 'outline-emerald-500', 'transition-all');
    setTimeout(() => {
      el.classList.remove('outline-2', 'outline-emerald-500');
    }, 2000);
  }, []);

  const jumpToField = useCallback((fieldId, sectionId) => {
    if (sectionId === 'section-ide-studio') {
      setIsStudioOpen(true);
    }
    const el = document.getElementById(fieldId);
    if (el) {
      const yOffset = -90;
      const y = el.getBoundingClientRect().top + window.pageYOffset + yOffset;
      window.scrollTo({ top: y, behavior: 'smooth' });
      if (sectionId) {
        setActiveSection(sectionId);
      }
      setMobileDrawerOpen(false);

      // Flash highlight
      el.classList.add('ring-2', 'ring-emerald-500', 'ring-offset-2', 'transition-all');
      setTimeout(() => {
        el.classList.remove('ring-2', 'ring-emerald-500', 'ring-offset-2');
      }, 2000);

      // Focus if input or textarea
      const inputEl = el.querySelector('input, textarea, select') || (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' ? el : null);
      if (inputEl) {
        try {
          inputEl.focus({ preventScroll: true });
        } catch (_) {}
      }
    } else if (sectionId) {
      jumpToSection(sectionId);
    }
  }, [jumpToSection]);

  const sectionIds = useMemo(() => [
    'section-ide-studio',
    'section-ai-assistant',
    'section-block-a',
    'section-block-b',
    'section-block-c',
    'section-block-d',
    'section-block-e',
    'section-block-f',
    'section-submit'
  ], []);

  useEffect(() => {
    let ticking = false;
    const handleScroll = () => {
      if (!ticking) {
        window.requestAnimationFrame(() => {
          // Dynamic calculation: index stops strictly beneath the hero area when hero is visible
          const heroEl = document.getElementById('blueprint-hero');
          const minTop = 68; // sticky header is ~56px + 12px gap
          if (heroEl) {
            const rect = heroEl.getBoundingClientRect();
            // rect.bottom is the viewport pixel distance to bottom edge of hero
            const calculatedTop = Math.max(rect.bottom + 12, minTop);
            setIndexTopOffset(calculatedTop);
          } else {
            setIndexTopOffset(minTop);
          }

          // Active Section Spy
          const scrollPosition = window.scrollY + 180;
          let current = sectionIds[0];
          for (let i = sectionIds.length - 1; i >= 0; i--) {
            const el = document.getElementById(sectionIds[i]);
            if (el && el.offsetTop <= scrollPosition) {
              current = sectionIds[i];
              break;
            }
          }
          setActiveSection(current);
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
  }, [sectionIds]);

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
  ], [lang, ideaText, attachedFiles, proactiveSuggestions, blockCompletion, completeness, formData, isLocked]);

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
      itemCount: 2,
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
            slug: initialData?._meta?.is_editing_slug || initialData?.slug || null,
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

    setIsAnalyzing(true);
    setAnalysisStep(attachedFiles.length > 0 ? t.step1 : t.step2);

    try {
      const token = csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const formDataUpload = new FormData();
      formDataUpload.append('idea_text', cleanText);
      formDataUpload.append('_hp_check', honeypot);
      formDataUpload.append('_website', honeypotWebsite);
      formDataUpload.append('locale', lang);
      if (formData.namaBisnis && formData.namaBisnis.trim()) {
        formDataUpload.append('nama_bisnis', formData.namaBisnis.trim());
      }

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

      // Populate synthesized blueprint data into all 25 fields
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

  const panelClass = "bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-none p-6 sm:p-8 transition-colors duration-200 scroll-mt-24";
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
              <span className="text-zinc-500">TARGET_PLATFORM:</span>
              <span className="text-zinc-900 dark:text-zinc-100 font-bold">{formData.targetPlatform}</span>
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
      
      {/* =========================================================================
          FLOATING LEFT HUD INDEX: TABLE OF CONTENTS (DESKTOP RAIL & MOBILE DRAWER)
          ========================================================================= */}

      {/* 1. DESKTOP FLOATING LEFT SIDEBAR / RAIL (xl:flex) */}
      <aside 
        aria-label="Blueprint Index Navigation"
        style={{
          top: `${indexTopOffset}px`,
          maxHeight: `calc(100vh - ${indexTopOffset + 16}px)`
        }}
        className="fixed left-3 2xl:left-6 z-40 hidden xl:flex flex-col font-mono select-none transition-[top] duration-75"
      >
        {isIndexExpanded ? (
          /* EXPANDED DIRECTORY PANEL */
          <div className="w-72 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col h-full max-h-full rounded-none transition-all duration-200">
            {/* Header */}
            <div className="p-3 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/80 dark:bg-zinc-950/80">
              <div className="flex items-center gap-2">
                <Compass className="w-4 h-4 text-emerald-500" />
                <div>
                  <div className="flex items-center gap-1.5">
                    <span className="text-[10px] font-mono font-black uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
                      {t.indexTitle}
                    </span>
                    <span className="text-[9px] px-1 py-0.2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 font-bold">
                      HUD
                    </span>
                  </div>
                </div>
              </div>
              <div className="flex items-center gap-1">
                <button
                  type="button"
                  onClick={() => setIsIndexExpanded(false)}
                  className="p-1 text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition"
                  title={t.indexHidePanel}
                >
                  <ChevronLeft className="w-4 h-4" />
                </button>
              </div>
            </div>

            {/* Sub-header / Search & Quick Controls */}
            <div className="p-2 border-b border-zinc-200 dark:border-zinc-800 space-y-1.5 bg-zinc-50/40 dark:bg-zinc-950/40">
              {/* Search Bar */}
              <div className="relative flex items-center">
                <Search className="w-3.5 h-3.5 absolute left-2 text-zinc-400 pointer-events-none" />
                <input
                  type="text"
                  value={indexSearchQuery}
                  onChange={(e) => setIndexSearchQuery(e.target.value)}
                  placeholder={t.indexSearchPlaceholder}
                  className="w-full pl-7 pr-6 py-1 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-[11px] text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-emerald-500 font-sans"
                />
                {indexSearchQuery && (
                  <button
                    type="button"
                    onClick={() => setIndexSearchQuery('')}
                    className="absolute right-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
                  >
                    <X className="w-3 h-3" />
                  </button>
                )}
              </div>

              {/* Group Toggle & Top Jumper Toolbar */}
              <div className="flex items-center justify-between text-[10px] text-zinc-500 dark:text-zinc-400 px-0.5 pt-0.5">
                <div className="flex items-center gap-1.5 flex-wrap">
                  <button
                    type="button"
                    onClick={expandAllGroups}
                    className="hover:text-emerald-600 dark:hover:text-emerald-400 transition underline underline-offset-2"
                  >
                    {t.indexExpandAll}
                  </button>
                  <span>·</span>
                  <button
                    type="button"
                    onClick={collapseAllGroups}
                    className="hover:text-emerald-600 dark:hover:text-emerald-400 transition underline underline-offset-2"
                  >
                    {t.indexCollapseAll}
                  </button>
                  <span>·</span>
                  <button
                    type="button"
                    onClick={toggleAllSubBlocks}
                    className="hover:text-emerald-600 dark:hover:text-emerald-400 transition font-bold text-emerald-600 dark:text-emerald-400"
                    title={t.indexToggleDetails}
                  >
                    {isAllSubExpanded ? t.indexCollapseDetails : t.indexExpandDetails}
                  </button>
                </div>
                <button
                  type="button"
                  onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}
                  className="flex items-center gap-0.5 hover:text-emerald-600 dark:hover:text-emerald-400 transition shrink-0"
                  title="Scroll to top"
                >
                  <ArrowUp className="w-3 h-3" />
                  <span>{t.indexBackToTop}</span>
                </button>
              </div>
            </div>

            {/* Accordion List Body */}
            <div className="flex-1 overflow-y-auto px-2 py-2 space-y-2 text-xs divide-y divide-zinc-100 dark:divide-zinc-800/60">
              {accordionGroups.map((grp) => {
                const groupSections = filteredIndexSections.filter(sec => sec.group === grp.key);
                if (groupSections.length === 0) return null;
                const isOpen = openAccordionGroups[grp.key] ?? true;

                return (
                  <div key={grp.key} className="pt-2 first:pt-0">
                    {/* Accordion Group Header */}
                    <button
                      type="button"
                      onClick={() => toggleAccordionGroup(grp.key)}
                      className="w-full flex items-center justify-between py-1 text-left text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 group font-bold tracking-wider text-[10px] uppercase"
                    >
                      <span className="flex items-center gap-1.5 truncate">
                        {isOpen ? <ChevronDown className="w-3 h-3 shrink-0" /> : <ChevronRight className="w-3 h-3 shrink-0" />}
                        <span className="truncate">{grp.title}</span>
                      </span>
                      <span className="text-[9px] px-1 py-0.2 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 shrink-0 font-normal">
                        {groupSections.length}
                      </span>
                    </button>

                    {/* Group Items */}
                    {isOpen && (
                      <div className="mt-1 space-y-1 pl-1">
                        {groupSections.map((sec) => {
                          const isActive = activeSection === sec.id;
                          const isSubOpen = isSubBlockOpen(sec.id);
                          const hasSubItems = sec.subItems && sec.subItems.length > 0;
                          return (
                            <div key={sec.id} className="space-y-0.5">
                              <div
                                onClick={() => jumpToSection(sec.id)}
                                className={`w-full text-left p-1.5 flex items-center justify-between gap-1.5 border transition-all cursor-pointer ${
                                  isActive
                                    ? 'bg-emerald-500/10 border-emerald-500/50 text-emerald-700 dark:text-emerald-300 font-bold shadow-xs'
                                    : 'bg-transparent border-transparent hover:bg-zinc-100 dark:hover:bg-zinc-800/60 text-zinc-700 dark:text-zinc-300'
                                }`}
                              >
                                <div className="flex items-center gap-2 truncate">
                                  <span className={`w-5 h-5 flex items-center justify-center text-[10px] font-mono shrink-0 border ${
                                    isActive
                                      ? 'bg-emerald-500 text-black border-emerald-500 font-bold'
                                      : sec.isComplete
                                      ? 'bg-emerald-500 text-black border-emerald-500 font-bold'
                                      : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 border-zinc-200 dark:border-zinc-700'
                                  }`}>
                                    {sec.isComplete ? (
                                      <Check className="w-3 h-3 stroke-[3]" />
                                    ) : (
                                      sec.badge
                                    )}
                                  </span>
                                  <div className="truncate">
                                    <div className="text-[11px] truncate leading-tight font-sans">
                                      {sec.title}
                                    </div>
                                  </div>
                                </div>

                                <div className="flex items-center gap-1 shrink-0">
                                  {sec.countLabel && (
                                    <span className={`text-[9px] font-mono px-1 py-0.2 border ${
                                      sec.isComplete
                                        ? 'border-emerald-500/40 text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 font-bold'
                                        : 'border-zinc-200 dark:border-zinc-700 text-zinc-500 bg-zinc-50 dark:bg-zinc-800'
                                    }`}>
                                      {sec.countLabel}
                                    </span>
                                  )}

                                  {hasSubItems && (
                                    <button
                                      type="button"
                                      onClick={(e) => toggleSubBlock(sec.id, e)}
                                      className="p-0.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition rounded-none"
                                      title={isSubOpen ? t.indexCollapseDetails : t.indexExpandDetails}
                                    >
                                      {isSubOpen ? (
                                        <ChevronDown className="w-3 h-3" />
                                      ) : (
                                        <ChevronRight className="w-3 h-3" />
                                      )}
                                    </button>
                                  )}

                                  {isActive && (
                                    <span className="relative flex h-1.5 w-1.5 ml-0.5">
                                      <span className="animate-ping absolute inline-flex h-full w-full rounded-none bg-emerald-400 opacity-75"></span>
                                      <span className="relative inline-flex rounded-none h-1.5 w-1.5 bg-emerald-500"></span>
                                    </span>
                                  )}
                                </div>
                              </div>

                              {/* Sub-Items List when Expanded */}
                              {isSubOpen && hasSubItems && (
                                <div className="ml-3 pl-2 border-l border-zinc-200 dark:border-zinc-800/80 space-y-0.5 py-0.5">
                                  {sec.subItems.map((sub) => (
                                    <button
                                      key={sub.id}
                                      type="button"
                                      onClick={(e) => {
                                        e.stopPropagation();
                                        jumpToField(sub.id, sec.id);
                                      }}
                                      className={`w-full text-left py-1 px-1.5 flex items-center justify-between gap-1 text-[10px] transition ${
                                        sub.isFilled
                                          ? 'text-zinc-800 dark:text-zinc-200 hover:bg-emerald-500/10 hover:text-emerald-700 dark:hover:text-emerald-300'
                                          : 'text-zinc-400 dark:text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800/60 hover:text-zinc-700 dark:hover:text-zinc-300'
                                      }`}
                                    >
                                      <div className="flex items-center gap-1.5 truncate">
                                        {sub.isFilled ? (
                                          <span className="w-3.5 h-3.5 shrink-0 flex items-center justify-center bg-emerald-500 text-black">
                                            <Check className="w-2.5 h-2.5 stroke-[3]" />
                                          </span>
                                        ) : (
                                          <span className="w-3.5 h-3.5 shrink-0 flex items-center justify-center border border-zinc-300 dark:border-zinc-700 text-zinc-400 text-[8px] font-mono">
                                            ○
                                          </span>
                                        )}
                                        <span className="font-mono text-[9px] text-zinc-400 shrink-0">{sub.code}</span>
                                        <span className={`truncate font-sans ${sub.isFilled ? 'font-medium' : ''}`}>
                                          {sub.label}
                                        </span>
                                      </div>

                                      <span className={`text-[8px] font-mono shrink-0 px-1 py-0.2 border uppercase ${
                                        sub.isFilled
                                          ? 'bg-emerald-500/15 border-emerald-500/40 text-emerald-600 dark:text-emerald-400 font-bold'
                                          : 'bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-400'
                                      }`}>
                                        {sub.isFilled ? (lang === 'en' ? '✓ Filled' : '✓ Terisi') : (lang === 'en' ? 'Empty' : 'Kosong')}
                                      </span>
                                    </button>
                                  ))}
                                </div>
                              )}
                            </div>
                          );
                        })}
                      </div>
                    )}
                  </div>
                );
              })}
            </div>

            {/* Footer Telemetry */}
            <div className="p-2.5 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-950/80">
              <div className="flex items-center justify-between text-[10px] mb-1">
                <span className="text-zinc-500 uppercase tracking-wider">{lang === 'en' ? 'Readiness' : 'Kesiapan'}:</span>
                <span className="font-bold text-emerald-600 dark:text-emerald-400">{completeness.score}%</span>
              </div>
              <div className="w-full bg-zinc-200 dark:bg-zinc-800 h-1.5 rounded-none overflow-hidden">
                <div
                  className="bg-emerald-500 h-full transition-all duration-300"
                  style={{ width: `${completeness.score}%` }}
                />
              </div>
              <div className="mt-1.5 text-[9px] text-zinc-400 dark:text-zinc-500 flex items-center justify-between font-mono">
                <span className="truncate max-w-[130px]">{getActiveSectionTitle()}</span>
                <span className="text-emerald-500 font-bold uppercase">{t.indexActiveStatus}</span>
              </div>
            </div>
          </div>
        ) : (
          /* COLLAPSED RAIL MODE */
          <div className="w-12 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 shadow-xl flex flex-col items-center py-2.5 rounded-none transition-all duration-200">
            {/* Expand Trigger */}
            <button
              type="button"
              onClick={() => setIsIndexExpanded(true)}
              className="p-1.5 text-zinc-500 hover:text-emerald-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition mb-2"
              title={t.indexShowPanel}
            >
              <ChevronRight className="w-4 h-4 text-emerald-500" />
            </button>

            <div className="w-6 h-[1px] bg-zinc-200 dark:bg-zinc-800 mb-2" />

            {/* Vertical Icons List */}
            <div className="flex flex-col gap-1 w-full px-1.5">
              {indexSections.map((sec) => {
                const isActive = activeSection === sec.id;
                return (
                  <button
                    key={sec.id}
                    type="button"
                    onClick={() => jumpToSection(sec.id)}
                    title={`${sec.badge} - ${sec.title}`}
                    className={`w-full aspect-square flex items-center justify-center text-[11px] font-mono transition border ${
                      isActive
                        ? 'bg-emerald-500 text-black border-emerald-500 font-bold shadow-xs scale-105'
                        : sec.isComplete
                        ? 'bg-emerald-500 text-black border-emerald-500 font-bold hover:bg-emerald-400'
                        : 'bg-zinc-50 dark:bg-zinc-950 text-zinc-500 border-zinc-200 dark:border-zinc-800 hover:border-emerald-500/50 hover:text-zinc-900 dark:hover:text-zinc-100'
                    }`}
                  >
                    {sec.isComplete ? <Check className="w-3.5 h-3.5 stroke-[3]" /> : sec.badge}
                  </button>
                );
              })}
            </div>

            <div className="w-6 h-[1px] bg-zinc-200 dark:bg-zinc-800 my-2" />

            {/* Mini Progress Percentage Pill */}
            <div className="text-[9px] font-mono font-bold text-emerald-600 dark:text-emerald-400 px-1 py-0.5 bg-emerald-500/10 border border-emerald-500/30">
              {completeness.score}%
            </div>
          </div>
        )}
      </aside>

      {/* 2. MOBILE / TABLET FLOATING BUTTON & SLIDE-OVER DRAWER (xl:hidden) */}
      <div className="xl:hidden">
        {/* Floating Trigger Badge on Bottom Left */}
        <button
          type="button"
          onClick={() => setMobileDrawerOpen(true)}
          className="fixed left-3 bottom-5 z-40 bg-zinc-950/90 text-white border border-zinc-700/80 shadow-2xl px-3 py-2 flex items-center gap-2 font-mono text-xs font-bold rounded-none hover:border-emerald-500 transition-all backdrop-blur-md active:scale-95 group"
          aria-label="Open Blueprint Section Directory"
        >
          <Compass className="w-4 h-4 text-emerald-400 group-hover:rotate-45 transition-transform" />
          <span className="uppercase tracking-wider">INDEX</span>
          <span className="px-1.5 py-0.2 bg-emerald-500 text-black text-[10px] font-mono font-bold">
            {completeness.score}%
          </span>
          <span className="text-[10px] text-zinc-400 hidden sm:inline truncate max-w-[120px]">
            {getActiveSectionTitle()}
          </span>
        </button>

        {/* Mobile Slide-over Drawer Modal */}
        <AnimatePresence>
          {mobileDrawerOpen && (
            <>
              {/* Backdrop */}
              <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                onClick={() => setMobileDrawerOpen(false)}
                className="fixed inset-0 bg-black/60 backdrop-blur-xs z-50"
              />

              {/* Drawer Sheet */}
              <motion.div
                initial={{ x: '-100%' }}
                animate={{ x: 0 }}
                exit={{ x: '-100%' }}
                transition={{ type: 'tween', duration: 0.25 }}
                className="fixed inset-y-0 left-0 w-80 max-w-[85vw] bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800 z-50 flex flex-col font-mono text-xs shadow-2xl"
              >
                {/* Drawer Header */}
                <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-950">
                  <div className="flex items-center gap-2">
                    <Compass className="w-4 h-4 text-emerald-500" />
                    <span className="font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
                      {t.indexTitle}
                    </span>
                  </div>
                  <button
                    type="button"
                    onClick={() => setMobileDrawerOpen(false)}
                    className="p-1.5 text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200"
                  >
                    <X className="w-4 h-4" />
                  </button>
                </div>

                {/* Search Bar in Mobile Drawer */}
                <div className="p-3 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/50 space-y-2">
                  <div className="relative flex items-center">
                    <Search className="w-3.5 h-3.5 absolute left-2.5 text-zinc-400 pointer-events-none" />
                    <input
                      type="text"
                      value={indexSearchQuery}
                      onChange={(e) => setIndexSearchQuery(e.target.value)}
                      placeholder={t.indexSearchPlaceholder}
                      className="w-full pl-8 pr-6 py-1.5 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-emerald-500 font-sans"
                    />
                    {indexSearchQuery && (
                      <button
                        type="button"
                        onClick={() => setIndexSearchQuery('')}
                        className="absolute right-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
                      >
                        <X className="w-3.5 h-3.5" />
                      </button>
                    )}
                  </div>
                  {/* Mobile Details Toggle */}
                  <div className="flex items-center justify-between text-[10px] text-zinc-500">
                    <button
                      type="button"
                      onClick={toggleAllSubBlocks}
                      className="hover:text-emerald-500 transition font-bold text-emerald-600 dark:text-emerald-400"
                    >
                      {isAllSubExpanded ? t.indexCollapseDetails : t.indexExpandDetails}
                    </button>
                    <button
                      type="button"
                      onClick={() => {
                        setMobileDrawerOpen(false);
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                      }}
                      className="hover:text-emerald-500 transition flex items-center gap-1"
                    >
                      <ArrowUp className="w-3 h-3" />
                      <span>{t.indexBackToTop}</span>
                    </button>
                  </div>
                </div>

                {/* Drawer Scrollable Body */}
                <div className="flex-1 overflow-y-auto p-3 space-y-3">
                  {accordionGroups.map((grp) => {
                    const groupSections = filteredIndexSections.filter(sec => sec.group === grp.key);
                    if (groupSections.length === 0) return null;
                    const isOpen = openAccordionGroups[grp.key] ?? true;

                    return (
                      <div key={grp.key} className="space-y-1">
                        <button
                          type="button"
                          onClick={() => toggleAccordionGroup(grp.key)}
                          className="w-full flex items-center justify-between py-1 text-left text-zinc-500 dark:text-zinc-400 font-bold uppercase text-[10px] tracking-wider"
                        >
                          <span className="flex items-center gap-1.5">
                            {isOpen ? <ChevronDown className="w-3 h-3" /> : <ChevronRight className="w-3 h-3" />}
                            <span>{grp.title}</span>
                          </span>
                          <span className="text-[9px] px-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                            {groupSections.length}
                          </span>
                        </button>

                        {isOpen && (
                          <div className="space-y-1 pl-1">
                            {groupSections.map((sec) => {
                              const isActive = activeSection === sec.id;
                              const isSubOpen = isSubBlockOpen(sec.id);
                              const hasSubItems = sec.subItems && sec.subItems.length > 0;
                              return (
                                <div key={sec.id} className="space-y-0.5">
                                  <div
                                    onClick={() => jumpToSection(sec.id)}
                                    className={`w-full text-left p-2 flex items-center justify-between border transition cursor-pointer ${
                                      isActive
                                        ? 'bg-emerald-500/10 border-emerald-500/60 text-emerald-600 dark:text-emerald-400 font-bold'
                                        : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200'
                                    }`}
                                  >
                                    <div className="flex items-center gap-2 truncate">
                                      <span className={`w-5 h-5 flex items-center justify-center text-[10px] font-mono shrink-0 border ${
                                        isActive
                                          ? 'bg-emerald-500 text-black border-emerald-500 font-bold'
                                          : sec.isComplete
                                          ? 'bg-emerald-500 text-black border-emerald-500 font-bold'
                                          : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700'
                                      }`}>
                                        {sec.isComplete ? <Check className="w-3 h-3 stroke-[3]" /> : sec.badge}
                                      </span>
                                      <span className="truncate text-xs font-sans">{sec.title}</span>
                                    </div>
                                    <div className="flex items-center gap-1 shrink-0">
                                      {sec.countLabel && (
                                        <span className={`text-[10px] font-mono px-1 py-0.2 border ${
                                          sec.isComplete
                                            ? 'border-emerald-500/40 text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 font-bold'
                                            : 'border-zinc-300 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'
                                        }`}>
                                          {sec.countLabel}
                                        </span>
                                      )}
                                      {hasSubItems && (
                                        <button
                                          type="button"
                                          onClick={(e) => toggleSubBlock(sec.id, e)}
                                          className="p-0.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200"
                                        >
                                          {isSubOpen ? <ChevronDown className="w-3 h-3" /> : <ChevronRight className="w-3 h-3" />}
                                        </button>
                                      )}
                                    </div>
                                  </div>

                                  {/* Sub-items in Mobile Drawer */}
                                  {isSubOpen && hasSubItems && (
                                    <div className="ml-3 pl-2 border-l border-zinc-200 dark:border-zinc-800 space-y-1 py-1">
                                      {sec.subItems.map((sub) => (
                                        <button
                                          key={sub.id}
                                          type="button"
                                          onClick={(e) => {
                                            e.stopPropagation();
                                            jumpToField(sub.id, sec.id);
                                          }}
                                          className={`w-full text-left py-1 px-1.5 flex items-center justify-between gap-1 text-[11px] transition ${
                                            sub.isFilled
                                              ? 'text-zinc-800 dark:text-zinc-200 hover:text-emerald-500 font-medium'
                                              : 'text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'
                                          }`}
                                        >
                                          <div className="flex items-center gap-1.5 truncate">
                                            {sub.isFilled ? (
                                              <span className="w-3.5 h-3.5 shrink-0 flex items-center justify-center bg-emerald-500 text-black">
                                                <Check className="w-2.5 h-2.5 stroke-[3]" />
                                              </span>
                                            ) : (
                                              <span className="w-3.5 h-3.5 shrink-0 flex items-center justify-center border border-zinc-400 text-zinc-400 text-[8px] font-mono">
                                                ○
                                              </span>
                                            )}
                                            <span className="font-mono text-[10px] text-zinc-400 shrink-0">{sub.code}</span>
                                            <span className="truncate font-sans">{sub.label}</span>
                                          </div>
                                          <span className={`text-[8px] font-mono shrink-0 px-1 py-0.2 border uppercase ${
                                            sub.isFilled
                                              ? 'bg-emerald-500/15 border-emerald-500/40 text-emerald-600 dark:text-emerald-400 font-bold'
                                              : 'bg-zinc-100 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-400'
                                          }`}>
                                            {sub.isFilled ? (lang === 'en' ? '✓' : '✓') : '—'}
                                          </span>
                                        </button>
                                      ))}
                                    </div>
                                  )}
                                </div>
                              );
                            })}
                          </div>
                        )}
                      </div>
                    );
                  })}
                </div>

                {/* Drawer Footer */}
                <div className="p-3 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950">
                  <div className="flex items-center justify-between text-xs mb-1">
                    <span className="text-zinc-500">{lang === 'en' ? 'Readiness Score:' : 'Skor Kesiapan:'}</span>
                    <span className="font-bold text-emerald-600 dark:text-emerald-400">{completeness.score}%</span>
                  </div>
                  <div className="w-full bg-zinc-200 dark:bg-zinc-800 h-1.5 rounded-none overflow-hidden">
                    <div
                      className="bg-emerald-500 h-full transition-all duration-300"
                      style={{ width: `${completeness.score}%` }}
                    />
                  </div>
                </div>
              </motion.div>
            </>
          )}
        </AnimatePresence>
      </div>
      
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
      <section id="section-ide-studio" className="scroll-mt-24 mb-8 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 rounded-none transition-all">
        
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
            <div id="field-ideaText">
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
            <div id="field-attachedFiles">
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
      <section id="section-ai-assistant" className="scroll-mt-24 mb-8 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 sm:p-8 rounded-none">
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
        <div id="field-proactiveSuggestions" className="mb-6">
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
          SECTION 3: THE COMPLETE 25-FIELD BLUEPRINT FORM (6 ARCHITECTURAL BLOCKS)
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
            <div id="field-namaBisnis">
              <label className={labelClass}>
                {lang === 'en' ? '1. Application Name / System Domain *' : '1. Nama Aplikasi / Domain Sistem *'}
              </label>
              <input
                type="text"
                required
                value={formData.namaBisnis}
                onChange={(e) => updateField('namaBisnis', e.target.value)}
                placeholder={lang === 'en' ? "e.g. TransLogistix Pro / Apex Health Clinic" : "Misal: TransLogistix Pro / Klinik Sehat Sentosa"}
                className={inputClass}
              />
            </div>

            {/* Field 2: masalahUtama */}
            <div id="field-masalahUtama">
              <label className={labelClass}>
                {lang === 'en' ? '2. Core Problem & Business Pain Points' : '2. Masalah Utama & Pain Points yang Dihadapi Bisnis'}
              </label>
              <textarea
                rows={3}
                value={formData.masalahUtama}
                onChange={(e) => updateField('masalahUtama', e.target.value)}
                placeholder={lang === 'en' ? "Describe operational bottlenecks, manual inefficiencies, or revenue leaks to be solved..." : "Uraikan kendala operasional, inefisiensi manual, atau titik rawan kebocoran yang ingin diselesaikan dengan sistem ini..."}
                className={inputClass}
              />
            </div>

            {/* Field 3: tujuanUtama (KPIs) */}
            <div id="field-tujuanUtama">
              <div className="flex items-center justify-between mb-1.5">
                <label className={labelClass}>
                  {lang === 'en' ? '3. Key Success Metrics (Business KPIs)' : '3. Tolak Ukur Keberhasilan (Target KPI Bisnis)'}
                </label>
                <button
                  type="button"
                  onClick={() => setActiveAddKey(activeAddKey === 'tujuanUtama' ? null : 'tujuanUtama')}
                  className="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold"
                >
                  <Plus className="w-3.5 h-3.5" /> {lang === 'en' ? 'Add KPI' : 'Tambah KPI'}
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
                    placeholder={lang === 'en' ? "e.g. Cut reconciliation time from 3 days to 15 minutes..." : "Misal: Rekonsiliasi keuangan memangkas waktu kerja dari 3 hari menjadi 15 menit..."}
                    className="flex-1 px-3 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => handleAddListItem('tujuanUtama')}
                    className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                  >
                    {lang === 'en' ? 'Save' : 'Simpan'}
                  </button>
                </div>
              )}
            </div>
          </div>
        </section>

        {/* ---------------------------------------------------------------------
            BLOK B: TARGET PENGGUNA, RBAC & PLATFORM PERANGKAT (FIELDS 4 - 6)
            --------------------------------------------------------------------- */}
        <section id="section-block-b" className={panelClass}>
          <div className="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
            <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block mb-1">
              AKTOR & PLATFORM // 02
            </span>
            <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {t.blockB}
            </h3>
            <p className="text-xs text-zinc-500 mt-1 font-sans">{t.blockBDesc}</p>
          </div>

          <div className="space-y-5">
            {/* Field 4: targetAudiens */}
            <div id="field-targetAudiens">
              <label className={labelClass}>
                {lang === 'en' ? '4. Target Audience / End-User Profile' : '4. Profil Target Audiens / Pengguna Akhir'}
              </label>
              <textarea
                rows={2}
                value={formData.targetAudiens}
                onChange={(e) => updateField('targetAudiens', e.target.value)}
                placeholder={lang === 'en' ? "e.g. Fleet owners, logistics coordinators, and warehouse operators..." : "Misal: Pemilik armada truk, manajer logistik perusahaan FMCG, dan staf gudang lapangan..."}
                className={inputClass}
              />
            </div>

            {/* Field 5: aktorSistem (RBAC) */}
            <div id="field-aktorSistem">
              <div className="flex items-center justify-between mb-1.5">
                <label className={labelClass}>
                  {lang === 'en' ? '5. System Actors & Role Permissions (RBAC Matrix)' : '5. Aktor Sistem & Matriks Wewenang (RBAC)'}
                </label>
                <button
                  type="button"
                  onClick={() => setActiveAddKey(activeAddKey === 'aktorSistem' ? null : 'aktorSistem')}
                  className="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold"
                >
                  <Plus className="w-3.5 h-3.5" /> {lang === 'en' ? 'Add Role' : 'Tambah Role'}
                </button>
              </div>

              {/* Quick Role Preset Badges */}
              <div className="flex flex-wrap gap-1.5 mb-2.5">
                {(lang === 'en'
                  ? ['+ Superadmin', '+ Supervisor / Approval', '+ Finance Staff', '+ Field Operator', '+ Courier / Driver', '+ External Client']
                  : ['+ Superadmin', '+ Supervisor / Approval', '+ Finance Staff', '+ Operator Lapangan', '+ Driver / Kurir', '+ Customer Eksternal']
                ).map((rolePreset, idx) => (
                  <button
                    key={idx}
                    type="button"
                    onClick={() => {
                      const items = parseNumberedList(formData.aktorSistem);
                      const cleanRole = rolePreset.replace('+ ', '');
                      items.push(`${cleanRole}: ${lang === 'en' ? 'Operational access per assigned duties' : 'Akses operasional sesuai wewenang tugas'}`);
                      updateField('aktorSistem', stringifyNumberedList(items));
                      showLocalToast('success', `${cleanRole} ${lang === 'en' ? 'added to Roles!' : 'ditambahkan ke Role!'}`);
                    }}
                    className="px-2 py-0.5 text-[10px] font-mono bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-400 border border-zinc-300 dark:border-zinc-700 cursor-pointer"
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
                    placeholder={lang === 'en' ? "e.g. Internal Auditor: Read-Only access to transaction ledgers..." : "Misal: Auditor Internal: Hak akses Read-Only pada seluruh buku transaksi..."}
                    className="flex-1 px-3 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => handleAddListItem('aktorSistem')}
                    className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                  >
                    {lang === 'en' ? 'Save' : 'Simpan'}
                  </button>
                </div>
              )}
            </div>

            {/* Field 6: targetPlatform (NEW & CRITICAL) */}
            <div id="field-targetPlatform">
              <label className={labelClass}>
                {lang === 'en'
                  ? '6. Target Platform & Device Accessibility *'
                  : '6. Platform Target & Aksesibilitas Perangkat *'}
              </label>
              <div className="flex flex-wrap gap-2 mb-2">
                {[
                  lang === 'en'
                    ? 'Hybrid: Web App + Mobile Apps (Flutter / React Native - iOS & Android) + SQLite Local DB'
                    : 'Hybrid: Web App + Mobile Apps (Flutter / React Native - iOS & Android) + SQLite Local DB',
                  lang === 'en'
                    ? 'Mobile-First Native App (Flutter / Swift / Kotlin) + Offline Local DB + Cloud PostgreSQL Sync'
                    : 'Mobile-First Native App (Flutter / Swift / Kotlin) + Offline Local DB + Cloud PostgreSQL Sync',
                  lang === 'en'
                    ? 'Modern Web Application Responsive & PWA (Desktop, Tablet & Mobile)'
                    : 'Modern Web Application Responsive & PWA (Desktop, Tablet & Mobile)',
                  lang === 'en'
                    ? 'Web Desktop Backoffice & High-Density Operational Portal'
                    : 'Web Desktop Backoffice (Khusus Monitor & Komputer Kantor)',
                  lang === 'en'
                    ? 'Dedicated Tablet POS & Kasir (Touchscreen Optimized)'
                    : 'Dedicated Tablet POS & Kasir (Touchscreen Optimized)'
                ].map((plat, idx) => (
                  <button
                    key={idx}
                    type="button"
                    onClick={() => updateField('targetPlatform', plat)}
                    className={`px-3 py-1.5 text-xs font-mono border transition ${
                      formData.targetPlatform === plat
                        ? 'bg-emerald-500 text-black border-emerald-500 font-bold'
                        : 'bg-zinc-50 dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 hover:border-emerald-500'
                    }`}
                  >
                    {plat}
                  </button>
                ))}
              </div>
              <input
                type="text"
                value={formData.targetPlatform}
                onChange={(e) => updateField('targetPlatform', e.target.value)}
                className={inputClass}
              />
            </div>
          </div>
        </section>

        {/* ---------------------------------------------------------------------
            BLOK C: FITUR MVP, WORKFLOW & MIGRASI DATA (FIELDS 7 - 10)
            --------------------------------------------------------------------- */}
        <section id="section-block-c" className={panelClass}>
          <div className="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
            <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block mb-1">
              FITUR, ALUR & DATA // 03
            </span>
            <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {t.blockC}
            </h3>
            <p className="text-xs text-zinc-500 mt-1 font-sans">{t.blockCDesc}</p>
          </div>

          <div className="space-y-6">
            {/* Field 7: fiturWajib (MVP) */}
            <div id="field-fiturWajib">
              <div className="flex items-center justify-between mb-1.5">
                <label className={labelClass}>
                  {lang === 'en' ? '7. Essential MVP Features (Phase 1 - Absolute Priority)' : '7. Fitur Wajib MVP (Fase 1 - Prioritas Mutlak)'}
                </label>
                <button
                  type="button"
                  onClick={() => setActiveAddKey(activeAddKey === 'fiturWajib' ? null : 'fiturWajib')}
                  className="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold"
                >
                  <Plus className="w-3.5 h-3.5" /> {lang === 'en' ? 'Add Feature' : 'Tambah Fitur'}
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
                    placeholder={lang === 'en' ? "e.g. Scan Waybill QR / Barcode via Phone Camera..." : "Misal: Scan Barcode Surat Jalan via Kamera Ponsel..."}
                    className="flex-1 px-3 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => handleAddListItem('fiturWajib')}
                    className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                  >
                    {lang === 'en' ? 'Save' : 'Simpan'}
                  </button>
                </div>
              )}
            </div>

            {/* Field 8: fiturTambahan (Roadmap) */}
            <div id="field-fiturTambahan">
              <label className={labelClass}>
                {lang === 'en' ? '8. Secondary Features (Phase 2 - Future Roadmap)' : '8. Fitur Tambahan (Fase 2 - Roadmap Masa Depan)'}
              </label>
              <textarea
                rows={3}
                value={formData.fiturTambahan}
                onChange={(e) => updateField('fiturTambahan', e.target.value)}
                placeholder={lang === 'en' ? "Secondary features that can be deferred post-MVP (e.g. Native Mobile App, AI route prediction, etc.)..." : "Fitur sekunder yang dapat ditunda setelah rilis MVP (misal: Aplikasi Mobile Native, Integrasi AI Prediktif rute armada, dll.)..."}
                className={inputClass}
              />
            </div>

            {/* Field 9: alurKerja (User Flow) */}
            <div id="field-alurKerja">
              <div className="flex items-center justify-between mb-1.5">
                <label className={labelClass}>
                  {lang === 'en' ? '9. Core Operational Workflow (Step-by-Step User Flow)' : '9. Alur Kerja Utama (User Flow Langkah demi Langkah)'}
                </label>
                <button
                  type="button"
                  onClick={() => setActiveAddKey(activeAddKey === 'alurKerja' ? null : 'alurKerja')}
                  className="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold"
                >
                  <Plus className="w-3.5 h-3.5" /> {lang === 'en' ? 'Add Step' : 'Tambah Langkah'}
                </button>
              </div>

              <div className="space-y-1.5 mb-2 max-h-60 overflow-y-auto pr-1">
                {parseNumberedList(formData.alurKerja).map((step, idx) => (
                  <div key={idx} className="group flex items-start justify-between gap-2 p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs">
                    <span className="text-zinc-800 dark:text-zinc-200 font-sans leading-tight">
                      <strong className="text-emerald-600 dark:text-emerald-400 font-mono">{lang === 'en' ? 'Step' : 'Langkah'} {idx + 1}:</strong> {step}
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
                    placeholder={lang === 'en' ? "e.g. Driver uploads delivery photo receipt -> System auto-matches invoice..." : "Misal: Driver mengunggah foto kuitansi -> Sistem otomatis mencocokkan nominal invoice..."}
                    className="flex-1 px-3 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => handleAddListItem('alurKerja')}
                    className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                  >
                    {lang === 'en' ? 'Save' : 'Simpan'}
                  </button>
                </div>
              )}
            </div>

            {/* Field 10: migrasiData (NEW & CRITICAL) */}
            <div id="field-migrasiData">
              <label className={labelClass}>
                {lang === 'en'
                  ? '10. Legacy Data Migration Scope & Strategy *'
                  : '10. Status Migrasi Data Warisan (Legacy Data Migration) *'}
              </label>
              <div className="flex flex-wrap gap-2 mb-2">
                {[
                  lang === 'en'
                    ? 'Clean Database Start (Manual Intake & CSV Template Support)'
                    : 'Database Baru Bersih (Input Mandiri & Dukungan Template CSV)',
                  lang === 'en'
                    ? 'Data Cleansing & Batch Importing from Excel / Google Sheets'
                    : 'Perlu Impor & Pembersihan Data dari Spreadsheet Excel / Google Sheets',
                  lang === 'en'
                    ? 'Full Schema ETL Migration from Legacy SQL (MySQL / PostgreSQL / MS SQL)'
                    : 'Migrasi Skema Penuh dari Basis Data SQL Lama (MySQL / PostgreSQL)'
                ].map((mig, idx) => (
                  <button
                    key={idx}
                    type="button"
                    onClick={() => updateField('migrasiData', mig)}
                    className={`px-3 py-1.5 text-xs font-mono border transition ${
                      formData.migrasiData === mig
                        ? 'bg-emerald-500 text-black border-emerald-500 font-bold'
                        : 'bg-zinc-50 dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 hover:border-emerald-500'
                    }`}
                  >
                    {mig}
                  </button>
                ))}
              </div>
              <input
                type="text"
                value={formData.migrasiData}
                onChange={(e) => updateField('migrasiData', e.target.value)}
                className={inputClass}
              />
            </div>
          </div>
        </section>

        {/* ---------------------------------------------------------------------
            BLOK D: INTEGRASI, ESTETIKA & HOSTING (FIELDS 11 - 14)
            --------------------------------------------------------------------- */}
        <section id="section-block-d" className={panelClass}>
          <div className="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
            <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block mb-1">
              INTEGRASI & HOSTING // 04
            </span>
            <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {t.blockD}
            </h3>
            <p className="text-xs text-zinc-500 mt-1 font-sans">{t.blockDDesc}</p>
          </div>

          <div className="space-y-5">
            {/* Field 11: kebutuhanIntegrasi */}
            <div id="field-kebutuhanIntegrasi">
              <div className="flex items-center justify-between mb-1.5">
                <label className={labelClass}>
                  {lang === 'en' ? '11. Third-Party Integrations (APIs & Gateways)' : '11. Kebutuhan Integrasi Pihak Ketiga (API & Gateway)'}
                </label>
                <button
                  type="button"
                  onClick={() => setActiveAddKey(activeAddKey === 'kebutuhanIntegrasi' ? null : 'kebutuhanIntegrasi')}
                  className="text-xs font-mono text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold"
                >
                  <Plus className="w-3.5 h-3.5" /> {lang === 'en' ? 'Add Integration' : 'Tambah Integrasi'}
                </button>
              </div>

              {/* Preset Chips */}
              <div className="flex flex-wrap gap-1.5 mb-2.5">
                {['+ Midtrans Snap', '+ WhatsApp Gateway', '+ Offline Sync (SQLite/Mobile)', '+ Firebase FCM Push', '+ Google Maps API', '+ Cloudflare R2', '+ RajaOngkir Kurir'].map((preset, idx) => (
                  <button
                    key={idx}
                    type="button"
                    onClick={() => {
                      const items = parseCommaList(formData.kebutuhanIntegrasi);
                      const cleanPreset = preset.replace('+ ', '');
                      if (!items.includes(cleanPreset)) {
                        items.push(cleanPreset);
                        updateField('kebutuhanIntegrasi', stringifyCommaList(items));
                        showLocalToast('success', `${cleanPreset} ${lang === 'en' ? 'added to Integrations!' : 'ditambahkan ke Integrasi!'}`);
                      }
                    }}
                    className="px-2 py-0.5 text-[10px] font-mono bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-400 border border-zinc-300 dark:border-zinc-700 cursor-pointer"
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
                      <X className="w-3.5 h-3.5" />
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
                    placeholder={lang === 'en' ? "e.g. Stripe Payment Gateway..." : "Misal: Xendit Payment Gateway..."}
                    className="flex-1 px-3 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => handleAddListItem('kebutuhanIntegrasi')}
                    className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                  >
                    {lang === 'en' ? 'Save' : 'Simpan'}
                  </button>
                </div>
              )}
            </div>

            {/* Field 12 & 13: referensiDesain & kesiapanAset */}
            <div className="grid sm:grid-cols-2 gap-4">
              <div id="field-referensiDesain">
                <label className={labelClass}>
                  {lang === 'en' ? '12. UI/UX Design References & Brand Benchmarks' : '12. Referensi Desain / Benchmark UI/UX'}
                </label>
                <input
                  type="text"
                  value={formData.referensiDesain}
                  onChange={(e) => updateField('referensiDesain', e.target.value)}
                  placeholder={lang === 'en' ? "e.g. Linear.app, Stripe Dashboard, Shopify Admin" : "Misal: Linear.app, Stripe Dashboard, Shopify Admin"}
                  className={inputClass}
                />
              </div>
              <div id="field-kesiapanAset">
                <label className={labelClass}>
                  {lang === 'en' ? '13. Digital Assets Readiness (Logo, Copywriting)' : '13. Kesiapan Aset Digital (Logo, Konten)'}
                </label>
                <input
                  type="text"
                  value={formData.kesiapanAset}
                  onChange={(e) => updateField('kesiapanAset', e.target.value)}
                  placeholder={lang === 'en' ? "e.g. Ready / In design production" : "Misal: Sudah Siap / Sedang Dibuat Tim Desain"}
                  className={inputClass}
                />
              </div>
            </div>

            {/* Field 14: preferensiHosting (NEW & CRITICAL) */}
            <div id="field-preferensiHosting">
              <label className={labelClass}>
                {lang === 'en'
                  ? '14. Hosting Infrastructure & Server Ownership Preference *'
                  : '14. Preferensi Infrastruktur Hosting & Kepemilikan Server *'}
              </label>
              <div className="flex flex-wrap gap-2 mb-2">
                {[
                  lang === 'en'
                    ? 'Hybrid Topology: Central Cloud VPS (PostgreSQL 16+, Redis) + Mobile Client Local DB (SQLite Offline Sync)'
                    : 'Topologi Hybrid: Cloud Server PostgreSQL 16+ & Redis + Database Lokal Mobile SQLite (Offline-First Sync)',
                  lang === 'en'
                    ? 'Managed Dedicated Cloud VPS Neriah Pro (PostgreSQL 16, Redis, Automated Backups)'
                    : 'Managed Dedicated Cloud VPS Neriah Pro (PostgreSQL 16, Redis, Backup Otomatis)',
                  lang === 'en'
                    ? 'Client Dedicated Cloud VPS (AWS EC2 / DigitalOcean / Google Cloud Platform)'
                    : 'Private Cloud Server Akun Klien (AWS EC2 / DigitalOcean / Google Cloud)',
                  lang === 'en'
                    ? 'On-Premise Private Company Local Server (Intranet / Physical Office)'
                    : 'On-Premise Server Lokal Milik Perusahaan (Intranet / Kantor Fisik)'
                ].map((host, idx) => (
                  <button
                    key={idx}
                    type="button"
                    onClick={() => updateField('preferensiHosting', host)}
                    className={`px-3 py-1.5 text-xs font-mono border transition ${
                      formData.preferensiHosting === host
                        ? 'bg-emerald-500 text-black border-emerald-500 font-bold'
                        : 'bg-zinc-50 dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 hover:border-emerald-500'
                    }`}
                  >
                    {host}
                  </button>
                ))}
              </div>
              <input
                type="text"
                value={formData.preferensiHosting}
                onChange={(e) => updateField('preferensiHosting', e.target.value)}
                className={inputClass}
              />
            </div>
          </div>
        </section>

        {/* ---------------------------------------------------------------------
            BLOK E: TIMELINE, SKALA, KEAMANAN & BATASAN RUANG LINGKUP (FIELDS 15 - 20)
            --------------------------------------------------------------------- */}
        <section id="section-block-e" className={panelClass}>
          <div className="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
            <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block mb-1">
              TIMELINE & BATASAN // 05
            </span>
            <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {t.blockE}
            </h3>
            <p className="text-xs text-zinc-500 mt-1 font-sans">{t.blockEDesc}</p>
          </div>

          <div className="space-y-5">
            {/* Field 15 & 16: durasiHari & targetWaktu */}
            <div className="grid sm:grid-cols-2 gap-4">
              <div>
                <label className={labelClass}>
                  {lang === 'en' ? '15. Target Sprint Duration (Working Days)' : '15. Target Durasi Pengerjaan (Hari Kerja)'}
                </label>
                <div className="flex gap-2 mb-2">
                  {['14', '30', '45', '60'].map((d) => (
                    <button
                      key={d}
                      type="button"
                      onClick={() => {
                        updateField('durasiHari', d);
                        updateField('targetWaktu', `${d} ${lang === 'en' ? 'Working Days (Phase 1 MVP)' : 'Hari Kerja (Fase 1 MVP)'}`);
                      }}
                      className={`flex-1 py-1.5 text-xs font-mono font-bold border transition cursor-pointer ${
                        formData.durasiHari === d
                          ? 'bg-emerald-500 text-black border-emerald-500'
                          : 'bg-zinc-50 dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 hover:border-emerald-500'
                      }`}
                    >
                      {d} {lang === 'en' ? 'Days' : 'Hari'}
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

              <div id="field-targetWaktu">
                <label className={labelClass}>
                  {lang === 'en' ? '16. Target Launch Window (Target Release)' : '16. Target Waktu Peluncuran (Target Rilis)'}
                </label>
                <input
                  type="text"
                  value={formData.targetWaktu}
                  onChange={(e) => updateField('targetWaktu', e.target.value)}
                  placeholder={lang === 'en' ? "e.g. 30 Working Days (Phase 1 MVP)" : "Misal: 30 Hari Kerja (Fase 1 MVP)"}
                  className={inputClass}
                />
              </div>
            </div>

            {/* Field 17 & 18: skalaPengguna & jangkauanPasar */}
            <div className="grid sm:grid-cols-2 gap-4">
              <div id="field-skalaPengguna">
                <label className={labelClass}>
                  {lang === 'en' ? '17. Estimated User Traffic Scale (Concurrent Users)' : '17. Estimasi Skala Trafik Pengguna'}
                </label>
                <input
                  type="text"
                  value={formData.skalaPengguna}
                  onChange={(e) => updateField('skalaPengguna', e.target.value)}
                  placeholder={lang === 'en' ? "0 - 100,000 Users / Month (Dedicated VPS Monolith)" : "0 - 100.000 Pengguna / Bulan (Dedicated VPS Monolith)"}
                  className={inputClass}
                />
              </div>
              <div>
                <label className={labelClass}>
                  {lang === 'en' ? '18. Market Coverage & Timezone Requirements' : '18. Jangkauan Pasar & Zona Waktu'}
                </label>
                <input
                  type="text"
                  value={formData.jangkauanPasar}
                  onChange={(e) => updateField('jangkauanPasar', e.target.value)}
                  placeholder={lang === 'en' ? "Domestic Indonesia (IDR, WIB/WITA/WIT Timezones)" : "Domestik Indonesia (IDR, Zona WIB/WITA/WIT)"}
                  className={inputClass}
                />
              </div>
            </div>

            {/* Field 19: outOfScope (Anti Scope Creep) */}
            <div id="field-outOfScope">
              <div className="flex items-center justify-between mb-1.5">
                <label className={labelClass}>
                  {lang === 'en' ? '19. Negative Scope Limits (Out of Scope - Anti Scope Creep)' : '19. Batasan Negatif (Out of Scope - Anti Scope Creep)'}
                </label>
                <button
                  type="button"
                  onClick={() => setActiveAddKey(activeAddKey === 'outOfScope' ? null : 'outOfScope')}
                  className="text-xs font-mono text-amber-600 dark:text-amber-400 hover:underline flex items-center gap-1 font-bold"
                >
                  <Plus className="w-3.5 h-3.5" /> {lang === 'en' ? 'Add Boundary' : 'Tambah Batasan'}
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
                    placeholder={lang === 'en' ? "e.g. Does not cover physical hardware procurement or WhatsApp API billing fees..." : "Misal: Tidak mencakup pengadaan hardware fisik atau biaya langganan WhatsApp API..."}
                    className="flex-1 px-3 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs outline-none"
                  />
                  <button
                    type="button"
                    onClick={() => handleAddListItem('outOfScope')}
                    className="px-3 py-1.5 bg-emerald-600 text-black text-xs font-mono font-bold"
                  >
                    {lang === 'en' ? 'Save' : 'Simpan'}
                  </button>
                </div>
              )}
            </div>

            {/* Field 20: kepatuhanKeamanan */}
            <div id="field-kepatuhanKeamanan">
              <label className={labelClass}>
                {lang === 'en' ? '20. Security Standards & Encryption Compliance' : '20. Standar Keamanan & Kepatuhan Enkripsi'}
              </label>
              <input
                type="text"
                value={formData.kepatuhanKeamanan}
                onChange={(e) => updateField('kepatuhanKeamanan', e.target.value)}
                placeholder={lang === 'en' ? "Modern Web Application & OWASP Top 10 Standards (CSRF, XSS, HTTPS)" : "Standar Web Application & OWASP Top 10 (CSRF, XSS, HTTPS)"}
                className={inputClass}
              />
            </div>
          </div>
        </section>

        {/* ---------------------------------------------------------------------
            BLOK F: ANGGARAN, GARANSI SLA & PENGESAHAN PIC (FIELDS 21 - 25)
            --------------------------------------------------------------------- */}
        <section id="section-block-f" className={panelClass}>
          <div className="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
            <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block mb-1">
              PENGESAHAN PIC & KONTRAK // 06
            </span>
            <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
              <Users className="w-5 h-5 text-emerald-500" />
              {t.blockF}
            </h3>
            <p className="text-xs text-zinc-500 mt-1 font-sans">{t.blockFDesc}</p>
          </div>

          <div className="space-y-6 mb-8">
            {/* Field 21: kisaranBudget */}
            <div id="field-kisaranBudget">
              <label className={labelClass}>
                {lang === 'en' ? '21. Investment Budget Allocation *' : '21. Alokasi Kisaran Budget Investasi *'}
              </label>
              <input
                type="text"
                value={formData.kisaranBudget}
                onChange={(e) => updateField('kisaranBudget', e.target.value)}
                placeholder={lang === 'en' ? "e.g. $1,000 - $3,000 USD (Growth Monolith)" : "Rp 15.000.000 - Rp 35.000.000 (Growth Monolith)"}
                className={inputClass}
              />
            </div>

            {/* Field 22: garansiSla (NEW & CRITICAL) */}
            <div id="field-garansiSla">
              <label className={labelClass}>
                {lang === 'en'
                  ? '22. Post-Launch Bug Warranty, SLA & Git Repo Handover *'
                  : '22. Skema Garansi Pascameluncur, SLA & Serah Terima Repo Git *'}
              </label>
              <div className="flex flex-wrap gap-2 mb-2">
                {[
                  lang === 'en'
                    ? '30 Days Post-Launch Bug Warranty Free of Charge + Full Private GitHub Repository Handover'
                    : '30 Hari Garansi Bug Pascameluncur Bebas Biaya + Penyerahan Akses Penuh Private Repo GitHub',
                  lang === 'en'
                    ? '60 Days Bug Warranty + GitHub Handover + Operational Staff Training Session'
                    : '60 Hari Garansi Bug + Handover Repo Git + Sesi Training Staf Operasional',
                  lang === 'en'
                    ? '30-Day Warranty + Ongoing Monthly Managed Maintenance SLA (Automated Backups & Security Patches)'
                    : 'Garansi 30 Hari + Kontrak Managed Maintenance Bulanan (SLA Backup & Security Patch)'
                ].map((sla, idx) => (
                  <button
                    key={idx}
                    type="button"
                    onClick={() => updateField('garansiSla', sla)}
                    className={`px-3 py-1.5 text-xs font-mono border transition ${
                      formData.garansiSla === sla
                        ? 'bg-emerald-500 text-black border-emerald-500 font-bold'
                        : 'bg-zinc-50 dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 hover:border-emerald-500'
                    }`}
                  >
                    {sla}
                  </button>
                ))}
              </div>
              <input
                type="text"
                value={formData.garansiSla}
                onChange={(e) => updateField('garansiSla', e.target.value)}
                className={inputClass}
              />
            </div>

            {/* Field 23: terminPembayaran (NEW & CRITICAL) */}
            <div id="field-terminPembayaran">
              <label className={labelClass}>
                {lang === 'en'
                  ? '23. Payment Milestone Terms & Schedule *'
                  : '23. Integrasi Skema Termin Pembayaran & Jadwal Milestone *'}
              </label>
              <div className="flex flex-wrap gap-2 mb-2">
                {[
                  lang === 'en'
                    ? 'Standard 50/50: 50% Kickoff DP & 50% Final Settlement Post-UAT Acceptance & Key Handover (via Midtrans Snap)'
                    : 'Termin Standar 50/50: 50% DP Kickoff & 50% Pelunasan setelah lolos UAT & Serah Terima Kunci (via Midtrans Snap)',
                  lang === 'en'
                    ? 'Milestone 30/40/30: 30% Kickoff DP, 40% MVP Demo Completed, 30% Final Go-Live Settlement'
                    : 'Termin Milestone 30/40/30: 30% DP Kickoff, 40% Demo MVP Selesai, 30% Pelunasan Go-Live',
                  lang === 'en'
                    ? '100% Full Upfront Payment (Priority Dedicated Squad Allocation)'
                    : 'Termin Pelunasan Penuh 100% Upfront (Prioritas Utama Dedicated Squad)'
                ].map((termin, idx) => (
                  <button
                    key={idx}
                    type="button"
                    onClick={() => updateField('terminPembayaran', termin)}
                    className={`px-3 py-1.5 text-xs font-mono border transition ${
                      formData.terminPembayaran === termin
                        ? 'bg-emerald-500 text-black border-emerald-500 font-bold'
                        : 'bg-zinc-50 dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 hover:border-emerald-500'
                    }`}
                  >
                    {termin}
                  </button>
                ))}
              </div>
              <input
                type="text"
                value={formData.terminPembayaran}
                onChange={(e) => updateField('terminPembayaran', e.target.value)}
                className={inputClass}
              />
            </div>

            {errorMessage && (
              <div className="p-4 bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-xs font-mono flex items-center gap-2">
                <AlertCircle className="w-4 h-4 shrink-0" />
                <span>{errorMessage}</span>
              </div>
            )}

            {/* Fields 24, 25, 26: PIC Contact Info */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
              {/* Field 24: clientName */}
              <div id="field-clientName" className="min-w-0">
                <label className={labelClass}>
                  {lang === 'en' ? '24. Authorized PIC Full Name *' : '24. Nama Lengkap PIC *'}
                </label>
                <input
                  type="text"
                  required
                  placeholder={lang === 'en' ? 'E.g. John Doe' : 'Misal: Budi Santoso'}
                  value={formData.clientName}
                  onChange={(e) => updateField('clientName', e.target.value)}
                  className={inputClass}
                />
              </div>

              {/* Field 25: email */}
              <div id="field-email" className="min-w-0">
                <label className={labelClass}>
                  {lang === 'en' ? '25. Official PIC Email *' : '25. Email Resmi PIC *'}
                </label>
                <input
                  type="email"
                  required
                  placeholder={lang === 'en' ? 'john@company.com' : 'budi@perusahaan.com'}
                  value={formData.email}
                  onChange={(e) => updateField('email', e.target.value)}
                  className={inputClass}
                />
              </div>

              {/* Field 26: phone with Country Zone Code */}
              <div id="field-phone" className="min-w-0">
                <label className={labelClass}>
                  {lang === 'en' ? '26. Authorized PIC WhatsApp / Phone *' : '26. WhatsApp / Telepon PIC *'}
                </label>
                <div className="flex w-full min-w-0 rounded-none border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950 focus-within:border-emerald-500 transition">
                  {/* Compact Country Selector with Overlay Native Select */}
                  <div className="relative shrink-0 w-24 bg-zinc-100 dark:bg-zinc-900 border-r border-zinc-300 dark:border-zinc-700 flex items-center justify-between px-2 cursor-pointer group">
                    <span className="text-xs font-mono font-bold text-zinc-800 dark:text-zinc-200 truncate pointer-events-none flex items-center gap-1">
                      <span className="text-sm">{currentCountry?.emoji || '🌐'}</span>
                      <span>{selectedCountryCode}</span>
                    </span>
                    <ChevronDown className="w-3 h-3 text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-200 shrink-0 pointer-events-none" />
                    <select
                      value={selectedCountryCode}
                      onChange={(e) => {
                        const code = e.target.value;
                        setSelectedCountryCode(code);
                        updateField('phone', phoneDigits ? `${code}${phoneDigits}` : '');
                      }}
                      aria-label="Country Dialing Code"
                      className="absolute inset-0 w-full h-full opacity-0 cursor-pointer text-xs"
                    >
                      {countryList.map((c, idx) => (
                        <option key={`${c.code}-${c.iso || idx}`} value={c.code} className="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">
                          {c.emoji || '🌐'} {c.code} ({c.name})
                        </option>
                      ))}
                    </select>
                  </div>
                  <input
                    type="tel"
                    required
                    placeholder="81234567890"
                    value={phoneDigits}
                    onChange={handlePhoneDigitsChange}
                    className="flex-1 min-w-0 px-3 py-2.5 bg-transparent text-zinc-900 dark:text-zinc-100 text-sm outline-none font-mono"
                  />
                </div>
                <span className="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1 block font-mono">
                  {lang === 'en' 
                    ? 'Type digits without leading 0 (E.164 international standard).' 
                    : 'Ketik digit tanpa angka 0 di depan (E.164 compliant).'}
                </span>
              </div>
            </div>
          </div>

          {/* Submit Button */}
          <button
            id="section-submit"
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
