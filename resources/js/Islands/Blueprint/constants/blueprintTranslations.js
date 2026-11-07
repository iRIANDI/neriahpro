export const TRANSLATIONS = {
  id: {
    topBadge: "PROJECT OS // ARCHITECTURAL DISCOVERY WORKSPACE",
    headerTitle: "Blueprint Arsitektur & Kuesioner Spesifikasi Sistem",
    headerSubtitle: "Lengkapi 26 parameter arsitektur terstruktur di bawah ini untuk menghasilkan dokumen Ultimate PRD dan skema basis data PostgreSQL Strict ULID siap bangun. Anda dapat mengisi form secara langsung atau gunakan Ruang Ide Cepat & MarkItDown di bawah untuk mengisi otomatis.",

    // Auto-save & Status
    autoSaveSaved: "Tersimpan Otomatis",
    autoSaveSaving: "Menyimpan ke Cloud...",
    autoSaveLocal: "Tersimpan di Perangkat",
    readinessLabel: "Skor Kesiapan Spesifikasi:",
    aiTierLabel: "AI Multi-Model: Auto-Failover Shield Aktif",
    aiSelectorTitle: "MULTI-AI ORCHESTRATOR // AUTO-FAILOVER TOKEN SHIELD",
    aiSelectorSubtitle: "Pilih model AI spesifik atau gunakan Auto-Failover untuk proteksi otomatis saat limit habis.",
    aiBadgeFree: "GRATIS",
    aiBadgeHealthy: "SEHAT",
    aiBadgeWarning: "MENDEKATI LIMIT",
    aiBadgeExhausted: "LIMIT HABIS (COOLDOWN)",
    aiBadgeUnconfigured: "BELUM ADA KEY",
    aiBestForPrdBadge: "👑 Terbaik untuk PRD Ultimate",
    aiAutoFailoverOption: "⚡ Auto-Failover (Direkomendasikan: Mengalir Otomatis jika Token Habis)",
    aiAutoFailoverHelper: "Jika model pilihan kehabisan token (Rate Limit 429), sistem otomatis memunculkan helper dan beralih ke model sehat berikutnya tanpa gagal.",

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
    tplRental: "🏢 Marketplace Rental Alat Berat B2B & Tagihan Bertahap",
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
    resetFormBtn: "Bersihkan Form",
    resetFormSuccess: "Seluruh isi formulir blueprint berhasil dibersihkan.",
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
    indexSpyActive: "● SPY",
    indexSpyManual: "○ OFF",
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
    aiTierLabel: "Multi-AI Orchestrator: Auto-Failover Shield Active",
    aiSelectorTitle: "MULTI-AI ORCHESTRATOR // AUTO-FAILOVER TOKEN SHIELD",
    aiSelectorSubtitle: "Select specific AI model or keep Auto-Failover to prevent token exhaustion downtime.",
    aiBadgeFree: "FREE TIER",
    aiBadgeHealthy: "HEALTHY",
    aiBadgeWarning: "LOW LIMIT",
    aiBadgeExhausted: "TOKEN EXHAUSTED",
    aiBadgeUnconfigured: "NO API KEY",
    aiBestForPrdBadge: "👑 Ultimate for PRD",
    aiAutoFailoverOption: "⚡ Auto-Failover (Recommended: Cascades automatically if token runs out)",
    aiAutoFailoverHelper: "If the chosen model hits quota exhaustion (429 Rate Limit), the system issues a helper notice and auto-routes to the next healthy model.",

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
    tplRental: "🏢 B2B Heavy Machinery Rental & Milestone Invoicing",
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
    resetFormBtn: "Reset Form",
    resetFormSuccess: "All blueprint fields have been reset to blank.",
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
    indexSpyActive: "● SPY",
    indexSpyManual: "○ OFF",
  }
};

// Helper: Parse string list into array of clean strings
export function parseNumberedList(text) {
  if (!text) return [];
  return text
    .split(/\n+/)
    .map(line => line.replace(/^\d+[\.\)]\s*/, '').trim())
    .filter(Boolean);
}

// Helper: Convert array of strings into numbered string
export function stringifyNumberedList(items) {
  if (!items || !items.length) return '';
  return items.map((it, idx) => `${idx + 1}. ${it}`).join('\n');
}

// Helper: Parse comma list into array
export function parseCommaList(text) {
  if (!text) return [];
  return text
    .split(/,\s*/)
    .map(s => s.trim())
    .filter(Boolean);
}

// Helper: Convert array to comma list
export function stringifyCommaList(items) {
  if (!items || !items.length) return '';
  return items.join(', ');
}

// Dynamic completeness score calculator (25 Parameters mapped to 100% score)
export function calculateCompleteness(data, lang) {
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

export const PAYMENT_RAIL_OPTIONS = [
  {
    id: 'bri_api',
    name: 'Bank BRI Open API & BRIVA',
    category: 'BUMN Direct Banking',
    desc: 'Integrasi direct Host-to-Host (H2H) Virtual Account BRIVA & Corporate API resmi Bank BRI',
    text: 'Bank BRI Open API (BRIVA Host-to-Host Virtual Account & Direct Debit API)',
    badge: 'BUMN Direct',
    badgeClass: 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/30',
  },
  {
    id: 'bni_api',
    name: 'Bank BNI Open API & Corporate VA',
    category: 'BUMN Direct Banking',
    desc: 'Integrasi direct API Bank BNI untuk Virtual Account corporate dan rekonsiliasi mutasi otomatis',
    text: 'Bank BNI Open API & Corporate Virtual Account H2H',
    badge: 'BUMN Direct',
    badgeClass: 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-500/30',
  },
  {
    id: 'mandiri_api',
    name: 'Bank Mandiri Direct API (MCM)',
    category: 'Enterprise Banking',
    desc: 'Integrasi direct Host-to-Host Virtual Account & Bill Payment Bank Mandiri Cash Management',
    text: 'Bank Mandiri Direct API (Mandiri Cash Management & Corporate VA)',
    badge: 'Enterprise',
    badgeClass: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/30',
  },
  {
    id: 'bca_api',
    name: 'BCA Open API (OneKlik & BCA VA)',
    category: 'Private Banking',
    desc: 'Integrasi direct Host-to-Host BCA Virtual Account & OneKlik untuk penyelesaian instan',
    text: 'BCA Open API (BCA Virtual Account H2H & OneKlik)',
    badge: 'Private Bank',
    badgeClass: 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/30',
  },
  {
    id: 'stripe',
    name: 'Stripe Global Payments',
    category: 'Global Cards & Multi-Currency',
    desc: 'Penerimaan pembayaran internasional (Visa, MasterCard, Amex, Apple Pay, Google Pay) dalam USD/EUR/SGD',
    text: 'Stripe Global Payments API (Multi-Currency Checkout, Visa/Mastercard/Amex, Apple Pay)',
    badge: 'Global Valas',
    badgeClass: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/30',
  },
  {
    id: 'wise',
    name: 'Wise Multi-Currency & Payouts',
    category: 'Cross-Border Real Mid-Market FX',
    desc: 'Transfer uang lintas negara dengan kurs riil pasar menengah tanpa markup biaya valas tersembunyi',
    text: 'Wise Business Multi-Currency API & International Payouts',
    badge: 'Global FX',
    badgeClass: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30',
  },
  {
    id: 'paypal',
    name: 'PayPal Commerce Platform',
    category: 'Global Digital Wallet',
    desc: 'Opsi pembayaran dompet digital global paling terpercaya untuk wisatawan & pembeli mancanegara',
    text: 'PayPal Commerce Platform API',
    badge: 'Global Wallet',
    badgeClass: 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/30',
  },
  {
    id: 'qris_national',
    name: 'QRIS Dinamis & Multi-VA Nasional (Midtrans / Xendit)',
    category: 'Domestic Aggregator',
    desc: 'Infrastruktur pembayaran digital multi-bank instan via QRIS Dinamis & Virtual Account domestik',
    text: 'Payment Gateway Domestik (QRIS Dinamis & Virtual Account Multi-Bank via Midtrans/Xendit)',
    badge: 'Nasional',
    badgeClass: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/30',
  },
  {
    id: 'no_gateway',
    name: 'Tanpa Payment Gateway (Manual Transfer / WhatsApp)',
    category: 'Direct Settlement / Inquiry',
    desc: 'Tidak memerlukan payment gateway online. Transaksi melalui invoice transfer manual atau survey WhatsApp',
    text: 'Verifikasi Manual Bukti Transfer Bank (Upload Bukti Bayar & Konfirmasi WhatsApp Staf)',
    badge: 'Tanpa Gateway',
    badgeClass: 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-500/30',
  },
];;
