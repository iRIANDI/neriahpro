// Project Blueprint Constants & Presets
export const PUBLIC_UX_PRESETS_V10 = [
  {
    code: 'UX_F1_SCROLLYTELLING',
    badge: 'UX_F1 • SPLINE & STICKY SCROLL',
    hours: 14,
    name: { id: 'Spline Camera & Sticky Narrative Scrollytelling', en: 'Spline Camera & Sticky Narrative Scrollytelling' },
    summary: {
      id: 'Kanvas/visual utama terkunci mulus sementara scroll HTML menggerakkan jalur kamera/tahap secara inersia (scrub: 1.2) tersinkronisasi dua arah dengan HUD semantik ramah SEO.',
      en: 'Pinned visual stage & spline camera rig scrubbed smoothly via HTML scroll proxy (scrub: 1.2 inertia) bidirectionally synced with an SEO-safe semantic HUD.',
    },
    engine: 'GSAP ScrollTrigger (scrub: 1.2) + Lenis + Hybrid HUD CustomEvent (@vite)',
    choreography: {
      scrollRig: 'THREE.CatmullRomCurve3 / GPU translate3d Pinning (scrub: 1.2, lerp: 0.10)',
      hudBridge: 'CustomEvent("pos:camera-waypoint", { detail: { index, slug } })',
      assetPipeline: 'Forever-Cached JSON data-props + Local WebP / Draco .glb + KTX2',
    },
    waypoints: [
      { step: '01', label: { id: 'Hulu / Pondasi', en: 'Origin / Foundation' }, metric: '100% SHA-256', xyz: '[0, 1, 12]' },
      { step: '02', label: { id: 'Proses & Audit', en: 'Execution & Audit' }, metric: 'CLS 0.00 • Scrub 1.2', xyz: '[-2, -6, 2]' },
      { step: '03', label: { id: 'Dampak Nyata / BAST', en: 'Verified Handover' }, metric: 'FCP < 0.8s', xyz: '[2, -12, -8]' },
    ],
  },
  {
    code: 'UX_F2_KINETIC_ANIWALL',
    badge: 'UX_F2 • KINETIC ANIWALL',
    hours: 12,
    name: { id: 'Interactive Bento Grid & Kinetic Aniwall', en: 'Interactive Bento Grid & Kinetic Aniwall' },
    summary: {
      id: 'Grid Bento asimetris presisi dengan sorotan border mengikuti kursor dan dinding bukti sosial terverifikasi bergerak dua arah (pause on hover + prefers-reduced-motion).',
      en: 'Asymmetric Bento grid with cursor spotlight border and bidirectional hardware-accelerated proof wall (pause on hover + prefers-reduced-motion).',
    },
    engine: 'Tailwind Bento Grid + GPU @keyframes translate3d(x,0,0) + Alpine.js',
    choreography: {
      scrollRig: 'Bidirectional CSS translate3d(x,0,0) + IntersectionObserver viewport gate',
      hudBridge: 'CustomEvent("pos:aniwall-filter", { detail: { sectorCode } })',
      assetPipeline: 'Cache::rememberForever SHA-256 BAST Receipts + Local Inline SVG',
    },
    waypoints: [
      { step: 'B1', label: { id: 'Bento Telemetry Hero', en: 'Bento Telemetry Hero' }, metric: '60-120 FPS', xyz: '[0, 0, 10]' },
      { step: 'B2', label: { id: 'Aniwall Marquee 2-Arah', en: 'Bidirectional Aniwall' }, metric: 'Pause Hover', xyz: '[0, -4, 10]' },
      { step: 'B3', label: { id: 'Receipt Blueprint Modal', en: 'Blueprint Receipt Modal' }, metric: 'ULID Linked', xyz: '[0, -8, 10]' },
    ],
  },
  {
    code: 'UX_F3_SPATIAL_SHOWCASE',
    badge: 'UX_F3 • HYBRID WEBGL2 & BVH',
    hours: 18,
    name: { id: 'Hybrid Island WebGL2, BVH Raycasting & Spatial Explorer', en: 'Hybrid Island WebGL2, BVH Raycasting & Spatial Explorer' },
    summary: {
      id: 'Arsitektur Hybrid Island 3-Lapis kelas Awwwards: kanvas tunggal WebGL2 (refraksi MeshTransmission, BVH raycasting O(log N) di GPU, teks 3D SDF, Draco/KTX2 VRAM) + denah SVG & HUD Flux UI ramah SEO.',
      en: 'Awwwards-grade 3-Layer Hybrid Island: single-canvas WebGL2 (MeshTransmission refraction, O(log N) GPU BVH raycast, Troika SDF 3D text, Draco/KTX2 VRAM) + SVG floorplan & SEO-safe Flux UI HUD.',
    },
    engine: 'R3F + Drei (MeshTransmission, Bvh, SDF Text) + Draco .glb / KTX2 VRAM',
    choreography: {
      scrollRig: 'Single-Canvas R3F dpr=[1,1.5] + CatmullRomCurve3 + <Bvh firstHitOnly> O(log N)',
      hudBridge: 'CustomEvent("pos:focus-artifact", { detail: { ulid, slotCode } })',
      assetPipeline: 'Filament v5 3D Asset CMS -> @gltf-transform (Draco + KTX2 VRAM) -> Brotli',
    },
    waypoints: [
      { step: 'S1', label: { id: 'Kanvas WebGL2 / Denah BVH', en: 'WebGL2 Canvas / BVH Map' }, metric: 'O(log N) BVH', xyz: '[0, 1, 12]' },
      { step: 'S2', label: { id: 'Refraksi Shader & KTX2 VRAM', en: 'Transmission & KTX2 VRAM' }, metric: 'DPR [1, 1.5]', xyz: '[-2, -6, 4]' },
      { step: 'S3', label: { id: 'Kunci Slot Anti-Bentrok HUD', en: 'HUD Atomic Slot Hold' }, metric: 'Redis TTL', xyz: '[2, -12, -4]' },
    ],
  },
  {
    code: 'UX_F4_LOOKBOOK_COMMERCE',
    badge: 'UX_F4 • EDITORIAL COMMERCE',
    hours: 12,
    name: { id: 'Editorial Lookbook & Instant Drawer Commerce', en: 'Editorial Lookbook & Instant Drawer Commerce' },
    summary: {
      id: 'Katalog produk ritel/B2B rasio gambar terkunci (CLS 0.00) dengan transisi halaman tanpa reload, matriks spesifikasi, dan laci keranjang instan.',
      en: 'Aspect-locked retail/B2B catalog (CLS 0.00) with zero-reload View Transitions, SKU spec compare matrix, and instant slide-over cart drawer.',
    },
    engine: 'View Transitions API + Aspect-Locked 4:5 Grid + Slide-Over Cart',
    choreography: {
      scrollRig: 'Native View Transitions API + Optimistic UI Drawer State',
      hudBridge: 'CustomEvent("pos:quick-cart-open", { detail: { skuUlid } })',
      assetPipeline: 'Local AVIF/WebP Aspect-Locked Srcset + 0ms Cart Mutation',
    },
    waypoints: [
      { step: 'L1', label: { id: 'Editorial Lookbook Grid', en: 'Editorial Lookbook Grid' }, metric: 'Aspect 4:5', xyz: '[0, 0, 10]' },
      { step: 'L2', label: { id: 'Quick-View Spec Compare', en: 'Quick-View Spec Compare' }, metric: '0ms Optimistic', xyz: '[0, -5, 10]' },
      { step: 'L3', label: { id: 'Slide-Over Checkout', en: 'Slide-Over Checkout' }, metric: 'QRIS/VA', xyz: '[0, -10, 10]' },
    ],
  },
  {
    code: 'UX_F5_TRUST_SIMULATOR',
    badge: 'UX_F5 • LIVE ROI / TRANSPARENCY',
    hours: 10,
    name: { id: 'Interactive Calculator & Live Transparency Portal', en: 'Interactive Calculator & Live Transparency Portal' },
    summary: {
      id: 'Slider simulasi tarif kargo/paket medis/pajak PPh21 real-time dengan pemisah ribuan regional tabular-nums + buku besar transparansi terverifikasi.',
      en: 'Real-time cargo/medical/PPh21 mathematical sliders with regional tabular-nums thousand separators + cryptographically verified transparency ledger.',
    },
    engine: 'Reactive Alpine.js Math Sliders + Local Canvas Telemetry Chart',
    choreography: {
      scrollRig: 'Deterministic Client Formula Sliders + 60fps Canvas Telemetry Bar',
      hudBridge: 'CustomEvent("pos:simulator-lock-quote", { detail: { payload } })',
      assetPipeline: 'Forever-Cached Tariff/Rate Matrix JSON + Tabular-Nums Formatter',
    },
    waypoints: [
      { step: 'T1', label: { id: 'Slider Parameter Real-Time', en: 'Live Parameter Sliders' }, metric: 'Tabular-Nums', xyz: '[0, 0, 10]' },
      { step: 'T2', label: { id: 'Rincian Biaya Transparan', en: 'Transparent Breakdown' }, metric: '1.000 / 1,000', xyz: '[0, -5, 10]' },
      { step: 'T3', label: { id: 'Konversi Booking / Resi', en: 'Instant Booking CTA' }, metric: 'E.164 Ready', xyz: '[0, -10, 10]' },
    ],
  },
  {
    code: 'UX_F6_GAMIFIED_PATHWAY',
    badge: 'UX_F6 • SKILL-TREE PATHWAY',
    hours: 14,
    name: { id: 'Skill-Tree & Milestone Progress Showcase', en: 'Skill-Tree & Milestone Progress Showcase' },
    summary: {
      id: 'Peta perjalanan kurikulum/pelatihan sebagai pohon keahlian SVG interaktif, lencana sertifikasi bersegel SHA-256, dan progres kohort.',
      en: 'Interactive SVG skill-tree curriculum map, SHA-256 verifiable badges, and crisp cohort milestone tracker.',
    },
    engine: 'Scroll-Driven SVG Path Stroke + Interactive Curriculum Tree (@vite)',
    choreography: {
      scrollRig: 'Scroll-Driven SVG stroke-dashoffset Dagre/Tree Path + Milestone Reveal',
      hudBridge: 'CustomEvent("pos:pathway-node-inspect", { detail: { nodeUlid } })',
      assetPipeline: 'Inline Vector Skill-Tree DAG + Local SVG QR Certificate Verifier',
    },
    waypoints: [
      { step: 'G1', label: { id: 'Pohon Kurikulum SVG', en: 'Interactive SVG Skill-Tree' }, metric: 'Node Unlock', xyz: '[0, 0, 10]' },
      { step: 'G2', label: { id: 'Lencana & Sertifikat Hash', en: 'Hash-Verified Badges' }, metric: 'SHA-256', xyz: '[0, -5, 10]' },
      { step: 'G3', label: { id: 'Pendaftaran Kohort Instan', en: 'Cohort Enrollment' }, metric: 'O(1) Cursor', xyz: '[0, -10, 10]' },
    ],
  },
];

export const HYBRID_ISLAND_3_LAYERS = [
  {
    code: 'LAYER 01 • BACKEND 3D/ASSET CMS',
    badge: '0 SQL ON SCROLL',
    title: {
      id: 'Laravel 13 + Filament v5 Asset Pipeline',
      en: 'Laravel 13 + Filament v5 Asset Pipeline',
    },
    detail: {
      id: 'Tabel ULID pos_showcase_3d_assets + TranslatableTabs + job @gltf-transform/cli (Draco .glb + KTX2 VRAM) + Cache::rememberForever data-props JSON.',
      en: 'ULID table pos_showcase_3d_assets + TranslatableTabs + queued @gltf-transform/cli (Draco .glb + KTX2 VRAM) + Cache::rememberForever data-props JSON.',
    },
  },
  {
    code: 'LAYER 02 • SINGLE-CANVAS GPU RIG',
    badge: '60–120 FPS • DPR [1,1.5]',
    title: {
      id: 'R3F WebGL2 / GSAP ScrollTrigger Spline',
      en: 'R3F WebGL2 / GSAP ScrollTrigger Spline',
    },
    detail: {
      id: 'Kanvas tunggal fixed inset-0 z-0: MeshTransmissionMaterial, raycast <Bvh firstHitOnly> O(log N), teks 3D SDF Troika, & kurva kamera CatmullRomCurve3 (scrub: 1.2).',
      en: 'Single fixed inset-0 z-0 canvas: MeshTransmissionMaterial, O(log N) <Bvh firstHitOnly> raycast, Troika SDF 3D text, & CatmullRomCurve3 camera rig (scrub: 1.2).',
    },
  },
  {
    code: 'LAYER 03 • SEMANTIC HUD & SEO SHIELD',
    badge: '100% SEO + WCAG AAA',
    title: {
      id: 'Livewire 4 + Flux UI HUD Event Bridge',
      en: 'Livewire 4 + Flux UI HUD Event Bridge',
    },
    detail: {
      id: 'HUD z-10 pointer-events-none (kontrol pointer-events-auto, rounded-none tajam Terang/Gelap) + CustomEvent("pos:camera-waypoint") + Schema.org JSON-LD & /llms.txt utuh.',
      en: 'z-10 pointer-events-none HUD (pointer-events-auto controls, sharp rounded-none Light/Dark) + CustomEvent("pos:camera-waypoint") + intact Schema.org JSON-LD & /llms.txt.',
    },
  },
];

export const ADAPTIVE_GPU_GOVERNOR_TIERS = [
  {
    id: 'TIER_1_HIGH',
    badge: 'TIER 1 • HIGH GPU',
    fps: '60–120 FPS',
    label: { id: 'WebGL2 Transmission + BVH', en: 'WebGL2 Transmission + BVH' },
    desc: {
      id: 'Desktop / Apple Silicon (>=6 core): R3F penuh + MeshTransmission (samples:6) + merged EffectComposer pada dpr [1, 1.5].',
      en: 'Desktop / Apple Silicon (>=6 cores): Full R3F + MeshTransmission (samples:6) + merged EffectComposer at dpr [1, 1.5].',
    },
  },
  {
    id: 'TIER_2_MEDIUM',
    badge: 'TIER 2 • MID MOBILE',
    fps: 'LOCKED 60 FPS',
    label: { id: 'KTX2 Matcap + DPR 1.0', en: 'KTX2 Matcap + DPR 1.0' },
    desc: {
      id: 'GPU terintegrasi / HP menengah: matikan post-processing berat, ganti ke tekstur KTX2 Matcap di VRAM pada dpr=1.0.',
      en: 'Integrated / mid-tier mobile GPU: bypass multi-sample post-processing, switch to VRAM KTX2 Matcap at dpr=1.0.',
    },
  },
  {
    id: 'TIER_3_LOW',
    badge: 'TIER 3 • SAFE STATIC',
    fps: 'ZERO JANK (0 MB VRAM)',
    label: { id: 'Poster WebP + Reduced-Motion', en: 'Poster WebP + Reduced-Motion' },
    desc: {
      id: 'Aktif otomatis saat prefers-reduced-motion, Save-Data, <4 thread, atau webglcontextlost: lepas WebGL & tampilkan Poster WebP lokal tajam.',
      en: 'Auto-engaged on prefers-reduced-motion, Save-Data, <4 threads, or webglcontextlost: unmount WebGL & render crisp aspect-locked Poster WebP.',
    },
  },
];

export function resolveDefaultPublicUxForSector(sectorCode) {
  const s = String(sectorCode || '').toUpperCase();
  if (s.includes('SECTOR_01')) return 'UX_F4_LOOKBOOK_COMMERCE';
  if (s.includes('SECTOR_02')) return 'UX_F3_SPATIAL_SHOWCASE';
  if (s.includes('SECTOR_03')) return 'UX_F5_TRUST_SIMULATOR';
  if (s.includes('SECTOR_04')) return 'UX_F2_KINETIC_ANIWALL';
  if (s.includes('SECTOR_05')) return 'UX_F1_SCROLLYTELLING';
  if (s.includes('SECTOR_06')) return 'UX_F5_TRUST_SIMULATOR';
  if (s.includes('SECTOR_07')) return 'UX_F6_GAMIFIED_PATHWAY';
  if (s.includes('SECTOR_08')) return 'UX_F2_KINETIC_ANIWALL';
  if (s.includes('SECTOR_09')) return 'UX_F3_SPATIAL_SHOWCASE';
  if (s.includes('SECTOR_10')) return 'UX_F3_SPATIAL_SHOWCASE';
  if (s.includes('SECTOR_11')) return 'UX_F5_TRUST_SIMULATOR';
  if (s.includes('SECTOR_12')) return 'UX_F1_SCROLLYTELLING';
  return 'UX_F2_KINETIC_ANIWALL';
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

// Concrete Track & Tier Enums
export const TRACKS = {
  RETAIL: 'retail',
  STUDIO: 'studio',
};

export const TIERS = {
  SPARK: 'retail_spark',
  LITE: 'retail_lite',
  PRO: 'retail_pro',
  ULTIMATE: 'retail_ultimate',
  ADVISORY: 'blueprint_advisory',
  UMKM: 'umkm_starter',
  FULL_MVP: 'full_mvp',
};

export function normalizeTierKey(tier) {
  const t = String(tier || '').toLowerCase().trim();
  if (['starter', 'lite', 'retail_starter', 'retail_lite'].includes(t)) return 'lite';
  if (['pro', 'retail_pro'].includes(t)) return 'pro';
  if (['ultimate', 'retail_ultimate'].includes(t)) return 'ultimate';
  if (['advisory', 'blueprint_advisory'].includes(t)) return 'advisory';
  if (['umkm', 'starter_umkm', 'umkm_starter'].includes(t)) return 'umkm';
  if (['mvp', 'full_mvp'].includes(t)) return 'full_mvp';
  return 'spark';
}

export const TRACK_PRESETS = {
  retail: {
    spark: {
      budget: {
        id: 'Spark: Rp 0 (Akses Audit Arsitektur & Cetak Biru Gratis)',
        en: 'Spark: $0 / Free (Architectural Audit & Blueprint Access)',
      },
      termin: {
        id: 'Paket Spark (Gratis): Rp 0 / Akses Audit Arsitektur & Cetak Biru Gratis 100%',
        en: 'Spark Package (Free): $0 / Free Instant Architectural Audit & Blueprint Access',
      },
    },
    lite: {
      budget: {
        id: 'Lite: Rp 99.000 (PRD 26 Parameter, Skema DDL & AI Directives)',
        en: 'Lite: Rp 99,000 (PRD 26 Parameters, Strict ULID DDL & AI Directives)',
      },
      termin: {
        id: 'Pembayaran Penuh 100% Instan: QRIS / Virtual Account Midtrans Snap (Langsung Unduh Berkas)',
        en: 'Instant 100% Full Payment: Midtrans Snap QRIS / Virtual Account (Immediate Download)',
      },
    },
    pro: {
      budget: {
        id: 'Pro: Rp 399.000 (Bundel Lengkap DDL, 7-Fase Prompt AI, Docker & Seeder)',
        en: 'Pro: Rp 399,000 (Full Architecture Bundle: DDL, 7-Phase Prompts, Docker & Seeder)',
      },
      termin: {
        id: 'Pembayaran Penuh 100% Instan: QRIS / Virtual Account Midtrans Snap (Langsung Unduh Berkas)',
        en: 'Instant 100% Full Payment: Midtrans Snap QRIS / Virtual Account (Immediate Download)',
      },
    },
    ultimate: {
      budget: {
        id: 'Ultimate: Rp 1.490.000 (Full 10 Pilar Software Factory OS + Live MCP Kit)',
        en: 'Ultimate: Rp 1,490,000 (Full 10 Pillars Software Factory OS + Live MCP Kit)',
      },
      termin: {
        id: 'Pembayaran Penuh 100% Instan: QRIS / Virtual Account Midtrans Snap (Langsung Unduh Berkas)',
        en: 'Instant 100% Full Payment: Midtrans Snap QRIS / Virtual Account (Immediate Download)',
      },
    },
    sla: {
      id: 'Lisensi Mandiri: Penyerahan Langsung Dokumen PRD Fortune 500, Skema DDL Strict ULID & Docker Starter Kit',
      en: 'Self-Service Delivery: Instant PRD Fortune 500, Strict ULID DDL & Docker Starter Kit Handover',
    },
  },
  studio: {
    growth: {
      budget: {
        id: 'Rp 15.000.000 - Rp 35.000.000 (Growth / Custom Business Portal - Multi-Role & Gateway)',
        en: '$1,000 - $3,000 USD (Growth / Custom Business Portal - Multi-Role & Gateway)',
      },
      termin: {
        id: 'Termin Standar 50/50: 50% DP Kickoff & 50% Pelunasan setelah lolos UAT & Serah Terima Kunci (via Midtrans Snap)',
        en: 'Standard 50/50: 50% Kickoff DP & 50% Final Settlement Post-UAT Acceptance & Key Handover (via Midtrans Snap)',
      },
    },
    sla: {
      id: '30 Hari Garansi Bug Pascameluncur Bebas Biaya + Penyerahan Akses Penuh Private Repo GitHub',
      en: '30 Days Post-Launch Bug Warranty Free of Charge + Full Private GitHub Repository Handover',
    },
  },
};

export function getCanonicalTrackDefaults(track, tier = '', lang = 'id') {
  const isEn = lang === 'en';
  const cleanTrack = String(track || 'retail').toLowerCase();
  const normalizedTier = normalizeTierKey(tier);

  if (cleanTrack === 'retail') {
    const tierConfig = TRACK_PRESETS.retail[normalizedTier] || TRACK_PRESETS.retail.spark;
    return {
      kisaranBudget: isEn ? tierConfig.budget.en : tierConfig.budget.id,
      garansiSla: isEn ? TRACK_PRESETS.retail.sla.en : TRACK_PRESETS.retail.sla.id,
      terminPembayaran: isEn ? tierConfig.termin.en : tierConfig.termin.id,
    };
  }

  // Studio Defaults
  const studioPreset = TRACK_PRESETS.studio;
  return {
    kisaranBudget: isEn ? studioPreset.growth.budget.en : studioPreset.growth.budget.id,
    garansiSla: isEn ? studioPreset.sla.en : studioPreset.sla.id,
    terminPembayaran: isEn ? studioPreset.growth.termin.en : studioPreset.growth.termin.id,
  };
}

export function isStudioDefaultValue(type, value) {
  if (!value || typeof value !== 'string') return false;
  const v = value.trim();
  switch (type) {
    case 'budget':
      return v.includes('15.000.000') ||
        v.includes('35.000.000') ||
        v.includes('50.000.000') ||
        v.includes('2.500.000') ||
        v.includes('Growth') ||
        v.includes('Advisory') ||
        v.includes('UMKM') ||
        v.includes('Full MVP') ||
        v.includes('$1,000');
    case 'sla':
      return v.includes('Garansi Bug') ||
        v.includes('Pascameluncur') ||
        v.includes('Private Repo') ||
        v.includes('Bug Warranty');
    case 'termin':
      return v.includes('50/50') ||
        v.includes('Milestone') ||
        v.includes('DP Kickoff') ||
        v.includes('Kickoff DP') ||
        v.includes('Final Settlement');
    default:
      return false;
  }
}

export function isRetailDefaultValue(type, value) {
  if (!value || typeof value !== 'string') return false;
  const v = value.trim();
  switch (type) {
    case 'budget':
      return v.includes('Spark') ||
        v.includes('Lite') ||
        v.includes('Pro:') ||
        v.includes('Ultimate:') ||
        v.includes('Rp 99.000') ||
        v.includes('Rp 99,000') ||
        v.includes('Rp 399.000') ||
        v.includes('Rp 399,000') ||
        v.includes('Rp 1.490.000') ||
        v.includes('Rp 1,490,000');
    case 'sla':
      return v.includes('Lisensi Mandiri') ||
        v.includes('Self-Service Delivery') ||
        v.includes('Akses Seumur Hidup') ||
        v.includes('Dukungan Arsitektur');
    case 'termin':
      return v.includes('Pembayaran Penuh 100%') ||
        v.includes('Instant 100% Full Payment') ||
        v.includes('Paket Spark') ||
        v.includes('Spark Package') ||
        v.includes('Self-Service Delivery');
    default:
      return false;
  }
}

