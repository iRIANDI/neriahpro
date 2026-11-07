// Project Blueprint Constants & Presets
export const PUBLIC_UX_PRESETS_V10 = [
  {
    code: 'UX_F1_SCROLLYTELLING',
    badge: 'UX_F1 • NARRATIVE SCROLL',
    hours: 8,
    name: { id: 'Narrative Scrollytelling & Sticky Reveal', en: 'Narrative Scrollytelling & Sticky Reveal' },
    summary: {
      id: 'Layar kanan sticky berubah mulus 60fps mengikuti narasi scroll kiri (gaya Stripe/Apple). 100% GPU translate3d & CLS = 0.00.',
      en: 'Sticky right viewport morphs smoothly at 60fps with left narrative steps (Stripe/Apple style). 100% GPU translate3d & CLS = 0.00.',
    },
    engine: 'Local CSS Sticky + IntersectionObserver + GPU translate3d',
  },
  {
    code: 'UX_F2_KINETIC_ANIWALL',
    badge: 'UX_F2 • KINETIC ANIWALL',
    hours: 6,
    name: { id: 'Infinite Kinetic Aniwall & Bento Grid', en: 'Infinite Kinetic Aniwall & Bento Grid' },
    summary: {
      id: 'Dinding bukti sosial & portofolio bergerak halus dua arah (pause on hover + prefers-reduced-motion) dengan filter instan.',
      en: 'Bidirectional smooth kinetic proof wall & bento grid (pause on hover + prefers-reduced-motion) with instant filter.',
    },
    engine: 'Pure CSS @keyframes translate3d(x,0,0) + Bento Grid',
  },
  {
    code: 'UX_F3_SPATIAL_SHOWCASE',
    badge: 'UX_F3 • SPATIAL FLOORPLAN',
    hours: 12,
    name: { id: 'Interactive Spatial Map, Unit & Seat Picker', en: 'Interactive Spatial Map, Unit & Seat Picker' },
    summary: {
      id: 'Eksplorasi denah lantai/kavling/meja/kamar interaktif berbasis SVG lokal dengan status ketersediaan real-time.',
      en: 'Interactive local SVG floorplan/unit/table/room explorer with real-time availability status.',
    },
    engine: 'Local Interactive SVG Pan/Zoom + WebSocket Stock Badge',
  },
  {
    code: 'UX_F4_LOOKBOOK_COMMERCE',
    badge: 'UX_F4 • EDITORIAL COMMERCE',
    hours: 8,
    name: { id: 'Editorial Lookbook & Slide-Over Quick-Cart', en: 'Editorial Lookbook & Slide-Over Quick-Cart' },
    summary: {
      id: 'Katalog produk ritel/F&B rasio gambar terkunci (CLS 0.00) dengan laci keranjang instan tanpa reload halaman.',
      en: 'Aspect-locked retail/F&B product catalog (CLS 0.00) with instant slide-over cart drawer and zero page reload.',
    },
    engine: 'Aspect-Locked Grid + Slide-Over Cart Drawer',
  },
  {
    code: 'UX_F5_TRUST_SIMULATOR',
    badge: 'UX_F5 • LIVE ROI / TRANSPARENCY',
    hours: 9,
    name: { id: 'Interactive Calculator & Live Transparency Ledger', en: 'Interactive Calculator & Live Transparency Ledger' },
    summary: {
      id: 'Kalkulator cicilan/premi/pajak/donasi interaktif tabular-nums real-time + buku besar transparansi dana terverifikasi.',
      en: 'Real-time tabular-nums installment/tax/donation simulator + cryptographically verified public transparency ledger.',
    },
    engine: 'Deterministic Client Math + Tabular-Nums Counter',
  },
  {
    code: 'UX_F6_GAMIFIED_PATHWAY',
    badge: 'UX_F6 • MILESTONE PATHWAY',
    hours: 8,
    name: { id: 'Guided Step-by-Step Pathway & Milestone Tracker', en: 'Guided Step-by-Step Pathway & Milestone Tracker' },
    summary: {
      id: 'Peta kurikulum/pelatihan/pendaftaran bertahap dengan indikator progres tajam dan verifikasi sertifikat QR.',
      en: 'Step-by-step curriculum/admissions/onboarding milestone pathway with crisp progress tracker and QR verification.',
    },
    engine: 'Progressive Stepper + Instant Milestone State',
  },
];

export function resolveDefaultPublicUxForSector(sectorCode) {
  const s = String(sectorCode || '').toUpperCase();
  if (s.includes('SECTOR_01')) return 'UX_F4_LOOKBOOK_COMMERCE';
  if (s.includes('SECTOR_02')) return 'UX_F6_GAMIFIED_PATHWAY';
  if (s.includes('SECTOR_03')) return 'UX_F5_TRUST_SIMULATOR';
  if (s.includes('SECTOR_04')) return 'UX_F1_SCROLLYTELLING';
  if (s.includes('SECTOR_05')) return 'UX_F3_SPATIAL_SHOWCASE';
  if (s.includes('SECTOR_06')) return 'UX_F6_GAMIFIED_PATHWAY';
  if (s.includes('SECTOR_07')) return 'UX_F1_SCROLLYTELLING';
  if (s.includes('SECTOR_08')) return 'UX_F5_TRUST_SIMULATOR';
  if (s.includes('SECTOR_09')) return 'UX_F1_SCROLLYTELLING';
  if (s.includes('SECTOR_10')) return 'UX_F2_KINETIC_ANIWALL';
  if (s.includes('SECTOR_11')) return 'UX_F5_TRUST_SIMULATOR';
  if (s.includes('SECTOR_12')) return 'UX_F3_SPATIAL_SHOWCASE';
  return 'UX_F1_SCROLLYTELLING';
}

export const QUICK_IDEA_CATALOG = {
  streaming_translation: {
    categoryKey: 'streaming_translation',
    label: { id: '🎙️ Audio Streaming & WebRTC Interpreter', en: '🎙️ Audio Streaming & WebRTC Interpreter' },
    keywords: ['translator', 'translate', 'interpreter', 'webrtc', 'livekit', 'mediamtx', 'streaming', 'audio', 'mic', 'mikrofon', 'penerjemah', 'listener', 'jemaat', 'sfu', 'church', 'gereja', 'ibadah', 'hang tuah', 'multilingual'],
    templates: [
      {
        id: 'church_audio_interpreter',
        badge: 'Real-time Audio Interpreter',
        title: {
          id: '🎙️ Aplikasi Penerjemah Suara Real-time (WebRTC LAN & PWA)',
          en: '🎙️ Real-time WebRTC Audio Interpreter & Multilingual PWA',
        },
        prompt: {
          id: "Sistem penerjemah (interpreter) audio real-time berbasis jaringan Wi-Fi lokal (LAN) dan browser smartphone untuk gereja / konferensi:\n1. Server WebRTC lokal dengan SFU (LiveKit / MediaMTX) latensi ultra-rendah (<150ms) tanpa membebani kuota internet.\n2. Akses HTTPS lokal otomatis via Local DNS dan SSL Certbot/Caddy (DNS-01 Challenge).\n3. Portal Listener (Jemaat) responsif mobile Dark Mode tanpa perlu login (cukup scan QR code dan pilih channel bahasa).\n4. Portal Translator untuk kontrol mikrofon, status stream live, dan monitor jumlah pendengar.\n5. Dashboard Admin berbasis Laravel 13 + Filament untuk manajemen channel bahasa, akun translator, dan moderasi chat.",
          en: "Real-time multilingual audio interpreter system operating over local Wi-Fi (LAN) and smartphone browsers for churches/conferences:\n1. Low-latency local WebRTC SFU (LiveKit / MediaMTX) with <150ms audio latency without consuming public internet bandwidth.\n2. Local HTTPS/SSL automated via Local DNS and Caddy/Certbot DNS-01 challenge.\n3. Mobile-first Dark Mode Listener portal requiring zero login (scan QR code, select language channel, instant listening).\n4. Dedicated Translator portal with microphone controls, live audio visualizer, and listener count monitor.\n5. Centralized backoffice powered by Laravel 13 + Filament for language channel CRUD, translator credentials, and chat moderation.",
        },
      },
    ],
  },
  cross_platform_apps: {
    categoryKey: 'cross_platform_apps',
    label: { id: '📱 Multi-Platform Desktop & Mobile', en: '📱 Multi-Platform Desktop & Mobile' },
    keywords: ['android', 'ios', 'windows', 'macos', 'mac', 'linux', 'flutter', 'tauri', 'electron', 'cross-platform', 'desktop', 'mobile app', 'sqlite', 'offline-first'],
    templates: [
      {
        id: 'flutter_tauri_omnichannel',
        badge: 'Mobile & Desktop Native',
        title: {
          id: '📱 Aplikasi Desktop (Windows/Mac/Linux) & Mobile (Android/iOS) Offline-First',
          en: '📱 Cross-Platform Desktop (Windows/Mac/Linux) & Mobile (Android/iOS) Offline-First',
        },
        prompt: {
          id: "Aplikasi cross-platform untuk perangkat mobile (Android & iOS) dan desktop (Windows, macOS, Linux):\n1. Engine client menggunakan Flutter / Tauri dengan basis data lokal SQLite WAL Mode dan enkripsi SQLCipher AES-256.\n2. Sinkronisasi dua arah (Bi-Directional Delta Sync) dengan backend cloud PostgreSQL 16 Strict ULID dan resolusi konflik mikrosekon.\n3. Dukungan penuh 100% Offline-First sehingga operasi pencatatan tetap berjalan tanpa koneksi internet.\n4. Integrasi WebSockets / WebRTC untuk notifikasi push dan komunikasi real-time lintas platform.",
          en: "Cross-platform application targeting mobile (Android & iOS) and desktop (Windows, macOS, Linux):\n1. Client engine powered by Flutter / Tauri with local SQLite WAL Mode and SQLCipher AES-256 encryption.\n2. Bi-directional delta synchronization with cloud PostgreSQL 16 Strict ULID backend and microsecond conflict resolution.\n3. 100% Offline-First architecture ensuring uninterrupted local operations without internet connectivity.\n4. Real-time push notifications and cross-platform communication powered by WebSockets / WebRTC.",
        },
      },
    ],
  },
  tourism: {
    categoryKey: 'tourism',
    label: { id: '🌴 Tour & Travel (Bali / Nusantara)', en: '🌴 Tour & Travel (Bali / Destinations)' },
    keywords: ['tour', 'travel', 'wisata bali', 'tour bali', 'wisata', 'liburan', 'hotel', 'itinerary', 'trip', 'bandara ngurah rai', 'pantai', 'bromo', 'ijen', 'overland', 'driver', 'destination', 'tourist', 'gayabalical', 'paket wisata'],
    templates: [
      {
        id: 'bali_tour_transfer',
        badge: 'Bali Tour & Transfer',
        title: {
          id: '🌴 Tour & Travel Bali: Transfer Bandara, Paket Wisata & AI Itinerary',
          en: '🌴 Bali Tour & Travel: Airport Transfer, Day Tour Packages & AI Itinerary',
        },
        prompt: {
          id: "Saya mau buat website berupa 'Tour and Travel' (PT. Gayabalical) untuk pelayanan jasa wisata di Bali:\n1. Jasa transportasi transfer in dan out hotel - Bandara Internasional I Gusti Ngurah Rai Denpasar.\n2. Paket wisata/day tour di Bali dengan pilihan jenis kendaraan (Avanza/Innova/HiAce), jumlah orang, rute objek wisata, dan harga transparan.\n3. Fitur custom wish list dari klien yang akan dianalisis oleh AI tanpa asumsi dan berdasarkan data di database aplikasi untuk membuat daftar itinerary beserta estimasi harga otomatis.\n4. Layanan overland tour ke wilayah Jawa Timur seperti Gunung Bromo dan Kawah Ijen (wisata ke luar Bali).\nMulti-bahasa ID & EN, integrasi WhatsApp reservasi instan, serta opsi pembayaran multi-rail (QRIS Dinamis, Direct Bank API, dan Kartu Kredit Global).",
          en: "We need an enterprise Tour and Travel web platform (PT. Gayabalical) for Bali and regional tourism:\n1. Airport transfer in/out service between Ngurah Rai Denpasar Airport and hotels.\n2. Curated Bali day tour packages with fleet selection (Avanza, Innova, HiAce), passenger capacity, custom routes, and transparent pricing.\n3. Dynamic client wish list analyzer powered by AI that parses client preferences against database attractions to synthesize optimized daily itineraries with price breakdown.\n4. Overland cross-island tours to East Java (Mount Bromo & Ijen Crater).\nBilingual (ID & EN), instant WhatsApp booking dispatch, and multi-rail payment (Dynamic QRIS, Direct Bank API, and Global Cards).",
        },
      },
      {
        id: 'nusa_islands_cruise',
        badge: 'Fastboat & Island Cruise',
        title: {
          id: '🛥️ Reservasi Kapal Cepat, Snorkeling & Wisata Nusa Penida',
          en: '🛥️ Fastboat Ticketing, Snorkeling & Nusa Penida Island Tours',
        },
        prompt: {
          id: "Platform reservasi tiket kapal cepat (fastboat) dan paket tour pulau Nusa Penida, Nusa Lembongan, dan Gili Trawangan. Wisatawan dapat memilih jadwal keberangkatan live, booking sewa peralatan snorkeling, dan bayar online dengan QRIS atau Stripe/PayPal untuk turis mancanegara. Tiket digital otomatis dikirim via WhatsApp dengan kode QR check-in pelabuhan Sanur.",
          en: "Fastboat ticketing and island excursion reservation platform connecting Sanur Harbor to Nusa Penida, Lembongan, and Gili Islands. Live vessel schedule selector, snorkeling equipment rentals, multi-currency checkout via Stripe/PayPal/QRIS, and automated WhatsApp boarding pass dispatch with QR verification.",
        },
      },
    ],
  },
  property: {
    categoryKey: 'property',
    label: { id: '🏡 Villa & Real Estate', en: '🏡 Villa & Real Estate' },
    keywords: ['villa', 'properti', 'property', 'real estate', 'kost', 'apartemen', 'unit', 'sewa', 'tenant', 'landlord', 'occupancy'],
    templates: [
      {
        id: 'luxury_villa_rental',
        badge: 'Villa Showcase & Booking',
        title: {
          id: '🏡 Katalog Sewa Villa Mewah & Booking Kalender Okupansi Live',
          en: '🏡 Luxury Villa Showcase, Live Calendar & Direct Booking',
        },
        prompt: {
          id: "Platform digital showcase dan penyewaan villa mewah (Luxury Villa Catalog). Dilengkapi galeri foto HD 360°, filter lokasi dan jumlah kamar, kalender okupansi real-time, integrasi chat WhatsApp langsung dengan villa host, sistem pembayaran deposit 50% via Midtrans/Stripe, serta dashboard pemilik villa untuk mengelola unit dan harga sewa.",
          en: "Luxury villa rental showcase platform with 360 HD photo galleries, dynamic bedroom and location filters, live occupancy calendar, direct WhatsApp inquiry with villa hosts, 50% down-payment checkout via Midtrans/Stripe, and a host control panel to manage unit pricing and occupancy.",
        },
      },
      {
        id: 'real_estate_asset_mgmt',
        badge: 'Asset & Tenant Management',
        title: {
          id: '🏢 Manajemen Aset Properti & Penagihan Sewa Tenant Otomatis',
          en: '🏢 Real Estate Asset Management & Automated Tenant Invoicing',
        },
        prompt: {
          id: "Sistem manajemen gedung dan aset sewa properti komersial. Mengelola inventaris unit sewa, kontrak legal tenant, penagihan invoice jatuh tempo otomatis via WhatsApp & Email, pencatatan mutasi biaya utilitas (air/listrik), serta laporan rekonsiliasi keuangan akuntansi berpasangan (double-entry ledger).",
          en: "Commercial real estate asset management platform. Manages rentable property units, tenant lease contracts, automated monthly invoice reminders via WhatsApp & Email, utility usage tracking, and multi-currency financial accounting ledger with reconciliation reports.",
        },
      },
    ],
  },
  commerce: {
    categoryKey: 'commerce',
    label: { id: '🛒 Ritel & E-Commerce', en: '🛒 Retail & E-Commerce' },
    keywords: ['toko', 'produk', 'jual', 'beli', 'retail', 'pos', 'kasir', 'belanja', 'ecommerce', 'e-commerce', 'grosir', 'distribusi', 'store'],
    templates: [
      {
        id: 'omnichannel_pos_store',
        badge: 'Omnichannel POS & Web',
        title: {
          id: '🛒 Webstore & Kasir POS Multi-Outlet (Stok Terpusat & QRIS)',
          en: '🛒 Multi-Outlet POS & Webstore (Synced Stock & QRIS)',
        },
        prompt: {
          id: "Platform e-commerce dan aplikasi kasir POS omnichannel untuk brand ritel multi-cabang. Sinkronisasi stok inventaris real-time antar outlet, cetak struk belanja thermal 58mm/80mm, QRIS Dinamis instan, program loyalitas poin pelanggan, dan laporan laba kotor konsolidasi.",
          en: "Omnichannel e-commerce and POS cloud application for multi-outlet retail brands. Real-time inventory sync across branches, 58mm/80mm thermal receipt printing, dynamic QRIS checkout, customer loyalty point engine, and unified gross profit analytics.",
        },
      },
      {
        id: 'b2b_wholesale_portal',
        badge: 'B2B Wholesale',
        title: {
          id: '📦 Portal Grosir B2B & Faktur Pajak Invoice Tempo (TOP 30)',
          en: '📦 B2B Wholesale Portal & Milestone Credit Invoicing (TOP 30)',
        },
        prompt: {
          id: "Portal pemesanan grosir B2B untuk distributor dan agen reseller. Pengelompokan tier harga khusus (Silver, Gold, Platinum), plafon kredit hutang dagang, persetujuan PO berjenjang oleh manajer, penerbitan invoice tempo TOP 30/60 hari, dan integrasi pengiriman kargo.",
          en: "B2B wholesale distribution ordering portal for resellers and partners. Tiered pricing tiers (Silver, Gold, Platinum), credit limit enforcement, multi-level manager PO approval workflow, 30/60-day invoice terms, and cargo dispatch tracking.",
        },
      },
    ],
  },
  healthcare: {
    categoryKey: 'healthcare',
    label: { id: '🏥 Medis & Klinik', en: '🏥 Healthcare & Clinic' },
    keywords: ['klinik', 'medis', 'dokter', 'pasien', 'obat', 'apotek', 'rme', 'rekam medis', 'kesehatan', 'rumahsakit'],
    templates: [
      {
        id: 'clinic_ehr_queue',
        badge: 'Klinik & RME',
        title: {
          id: '🏥 Sistem Klinik Terpadu, RME Standar Kemenkes & Antrean Online',
          en: '🏥 Integrated Medical Clinic, Electronic Health Records & Queues',
        },
        prompt: {
          id: "Sistem informasi manajemen klinik medis terpadu dan Rekam Medis Elektronik (RME) standar Kemenkes/SATUSEHAT. Registrasi dan reservasi nomor antrean online pasien via WhatsApp, modul anamnesis & diagnosis dokter (ICD-10), resep obat digital terhubung apotek, dan audit log kepatuhan privasi medis (UU PDP).",
          en: "Medical clinic management system and Electronic Health Records (EHR) compliant with healthcare regulatory standards. Online patient appointment queue ticketing, doctor consultation & diagnosis workbench (ICD-10), e-prescriptions, integrated pharmacy POS, and data privacy audit logging.",
        },
      },
    ],
  },
  logistics: {
    categoryKey: 'logistics',
    label: { id: '🚚 Logistik & Armada', en: '🚚 Logistics & Fleet' },
    keywords: ['logistik', 'ekspedisi', 'kargo', 'truk', 'armada', 'resi', 'waybill', 'pengiriman', 'kurir', 'fleet', 'freight'],
    templates: [
      {
        id: 'freight_waybill_tracking',
        badge: 'Ekspedisi Kargo',
        title: {
          id: '🚚 Ekspedisi Logistik Antar Pulau, Resi Barcode & Driver Mobile',
          en: '🚚 Freight Logistics, Barcode Waybill Tracking & Driver Mobile',
        },
        prompt: {
          id: "Platform ekspedisi logistik dan armada truk logistik antar pulau. Terdapat 3 level: Superadmin di kantor pusat, Koordinator Lapangan, dan Driver truk. Driver bisa scan barcode resi (waybill) dan upload foto bukti kirim (e-POD). Pelacakan GPS posisi armada real-time dan pembayaran invoice tempo via Midtrans.",
          en: "Inter-island freight forwarding and fleet dispatch platform. 3 user roles: Central Superadmin, Field Dispatcher, and Truck Drivers. Mobile driver portal for barcode waybill scanning and photo Proof of Delivery (e-POD). Live GPS shipment tracking, fuel/toll expense reconciliation, and Midtrans invoice checkout.",
        },
      },
    ],
  },
  fnb: {
    categoryKey: 'fnb',
    label: { id: '☕ F&B & Resto', en: '☕ F&B, Cafe & Resto' },
    keywords: ['cafe', 'resto', 'restoran', 'makanan', 'minuman', 'kuliner', 'kitchen', 'meja', 'dine in', 'fnb', 'coffee'],
    templates: [
      {
        id: 'smart_cafe_qr_dinein',
        badge: 'Cafe & Kitchen POS',
        title: {
          id: '☕ Smart Cafe & Resto: QR Order Meja, Kitchen Display & Resep HPP',
          en: '☕ Smart Cafe & Restaurant: QR Table Ordering, KDS & Recipe COGS',
        },
        prompt: {
          id: "Aplikasi kasir cafe & restoran modern dengan pemesanan mandiri via scan QR di meja (QR Dine-in). Pesanan otomatis masuk ke Kitchen Display System (KDS) dapur dan kasir, inventaris bahan baku otomatis terpotong berdasarkan resep HPP (Bill of Materials), serta pembayaran instan QRIS meja.",
          en: "Modern restaurant and cafe management platform with contactless QR code table ordering. Instant routing to Kitchen Display System (KDS) and bar counter, real-time raw ingredient deduction based on recipe Bill of Materials (BOM), split-bill checkout, and instant table QRIS payment.",
        },
      },
    ],
  },
};

// Mapping between backend snake_case validation keys and DOM field IDs
export const FIELD_MAP = {
  nama_bisnis: 'namaBisnis',
  namaBisnis: 'namaBisnis',
  masalah_utama: 'masalahUtama',
  masalahUtama: 'masalahUtama',
  tujuan_utama: 'tujuanUtama',
  tujuanUtama: 'tujuanUtama',
  target_audiens: 'targetAudiens',
  targetAudiens: 'targetAudiens',
  aktor_sistem: 'aktorSistem',
  aktorSistem: 'aktorSistem',
  target_platform: 'targetPlatform',
  targetPlatform: 'targetPlatform',
  fitur_wajib: 'fiturWajib',
  fiturWajib: 'fiturWajib',
  fitur_tambahan: 'fiturTambahan',
  fiturTambahan: 'fiturTambahan',
  alur_kerja: 'alurKerja',
  alurKerja: 'alurKerja',
  client_name: 'clientName',
  clientName: 'clientName',
  email: 'email',
  phone: 'phone',
  migrasi_data: 'migrasiData',
  migrasiData: 'migrasiData',
  kebutuhan_integrasi: 'kebutuhanIntegrasi',
  kebutuhanIntegrasi: 'kebutuhanIntegrasi',
  referensi_desain: 'referensiDesain',
  referensiDesain: 'referensiDesain',
  kesiapan_aset: 'kesiapanAset',
  kesiapanAset: 'kesiapanAset',
  preferensi_hosting: 'preferensiHosting',
  preferensiHosting: 'preferensiHosting',
  target_waktu: 'targetWaktu',
  targetWaktu: 'targetWaktu',
  durasi_hari: 'durasiHari',
  durasiHari: 'durasiHari',
  skala_pengguna: 'skalaPengguna',
  skalaPengguna: 'skalaPengguna',
  jangkauan_pasar: 'jangkauanPasar',
  jangkauanPasar: 'jangkauanPasar',
  out_of_scope: 'outOfScope',
  outOfScope: 'outOfScope',
  kepatuhan_keamanan: 'kepatuhanKeamanan',
  kepatuhanKeamanan: 'kepatuhanKeamanan',
  kisaran_budget: 'kisaranBudget',
  kisaranBudget: 'kisaranBudget',
  garansi_sla: 'garansiSla',
  garansiSla: 'garansiSla',
  termin_pembayaran: 'terminPembayaran',
  terminPembayaran: 'terminPembayaran',
};

export const CANONICAL_FIELD_MAP = FIELD_MAP;

export const FIELD_SECTION_MAP = {
  namaBisnis: 'section-block-a',
  masalahUtama: 'section-block-a',
  tujuanUtama: 'section-block-a',
  targetAudiens: 'section-block-b',
  aktorSistem: 'section-block-b',
  targetPlatform: 'section-block-b',
  fiturWajib: 'section-block-c',
  fiturTambahan: 'section-block-c',
  alurKerja: 'section-block-c',
  migrasiData: 'section-block-c',
  kebutuhanIntegrasi: 'section-block-d',
  referensiDesain: 'section-block-d',
  kesiapanAset: 'section-block-d',
  preferensiHosting: 'section-block-d',
  targetWaktu: 'section-block-e',
  durasiHari: 'section-block-e',
  skalaPengguna: 'section-block-e',
  jangkauanPasar: 'section-block-e',
  outOfScope: 'section-block-e',
  kepatuhanKeamanan: 'section-block-e',
  kisaranBudget: 'section-block-f',
  garansiSla: 'section-block-f',
  terminPembayaran: 'section-block-f',
  clientName: 'section-block-f',
  email: 'section-block-f',
  phone: 'section-block-f',
};

export const FIELD_LABELS = {
  namaBisnis: { num: '1', id: '1. Nama Aplikasi / Bisnis', en: '1. Application / Business Name' },
  masalahUtama: { num: '2', id: '2. Masalah Utama Bisnis', en: '2. Core Business Problem' },
  tujuanUtama: { num: '3', id: '3. Tolak Ukur Sukses (KPI)', en: '3. Success Metrics (KPIs)' },
  targetAudiens: { num: '4', id: '4. Target Audiens / Pengguna', en: '4. Target Audience Profile' },
  aktorSistem: { num: '5', id: '5. Aktor Sistem & RBAC Matrix', en: '5. System Actors & RBAC' },
  targetPlatform: { num: '6', id: '6. Platform Target & Perangkat', en: '6. Target Platform' },
  fiturWajib: { num: '7', id: '7. Fitur Wajib MVP (Fase 1)', en: '7. Core MVP Features' },
  fiturTambahan: { num: '8', id: '8. Roadmap Fitur (Fase 2)', en: '8. Phase 2 Features' },
  alurKerja: { num: '9', id: '9. Alur Kerja Utama (Workflow)', en: '9. User Workflow' },
  migrasiData: { num: '10', id: '10. Migrasi Data Warisan', en: '10. Data Migration' },
  kebutuhanIntegrasi: { num: '11', id: '11. Kebutuhan Integrasi', en: '11. Integrations' },
  referensiDesain: { num: '12', id: '12. Referensi Desain UI/UX', en: '12. Design References' },
  kesiapanAset: { num: '13', id: '13. Kesiapan Aset Konten', en: '13. Asset Readiness' },
  preferensiHosting: { num: '14', id: '14. Infrastruktur Hosting', en: '14. Hosting Preference' },
  targetWaktu: { num: '15', id: '15. Target Waktu Rilis', en: '15. Target Timeline' },
  durasiHari: { num: '15', id: '15. Target Durasi Hari', en: '15. Duration Days' },
  skalaPengguna: { num: '16', id: '16. Skala Trafik Pengguna', en: '16. User Scale' },
  jangkauanPasar: { num: '17', id: '17. Jangkauan Pasar & Regulasi', en: '17. Market Reach' },
  outOfScope: { num: '18', id: '18. Batasan Negatif (Out of Scope)', en: '18. Negative Scope' },
  kepatuhanKeamanan: { num: '19', id: '19. Standar Keamanan Siber', en: '19. Security Standards' },
  kisaranBudget: { num: '20', id: '20. Alokasi Budget Investasi', en: '20. Budget Range' },
  garansiSla: { num: '21', id: '21. Garansi SLA & Akses Git', en: '21. Warranty SLA' },
  terminPembayaran: { num: '22', id: '22. Termin Pembayaran', en: '22. Payment Terms' },
  clientName: { num: '24', id: '24. Nama Lengkap PIC', en: '24. PIC Full Name' },
  email: { num: '25', id: '25. Email Resmi PIC', en: '25. PIC Official Email' },
  phone: { num: '26', id: '26. WhatsApp / Telepon PIC', en: '26. PIC WhatsApp / Phone' },
};
