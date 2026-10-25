import React, { useState, useEffect } from 'react';
import { Layers, ShieldCheck, ArrowUpRight, MessageCircle, X, ExternalLink, FileText, Lock, RefreshCcw, MapPin, Mail, Phone, Clock } from 'lucide-react';

export default function FooterIsland({ 
  settings, 
  featureFlags, 
  whatsappNumber = '6281380448253', 
  currentLocale, 
  supportEmail = 'support@neriahpro.com',
  companyAddress = 'Gianyar, Bali, Indonesia',
  ownerName = 'Yoseph Iriandi',
  ownerTitle = 'Independent Full-Stack Developer & Software Architect'
}) {
  const currentYear = new Date().getFullYear();
  const effectiveEmail = supportEmail || 'support@neriahpro.com';
  let rawWa = (whatsappNumber || '6281380448253').toString().replace(/\D/g, '');
  if (rawWa.startsWith('938') && rawWa.length >= 11) {
    rawWa = '62' + rawWa.substring(2);
  } else if (rawWa.startsWith('08')) {
    rawWa = '62' + rawWa.substring(1);
  } else if (rawWa.startsWith('8') && rawWa.length >= 9 && rawWa.length <= 13) {
    rawWa = '62' + rawWa;
  }
  const cleanWaNumber = rawWa.startsWith('62') ? rawWa : '6281380448253';
  const formattedPhone = cleanWaNumber.startsWith('62')
    ? `+62 ${cleanWaNumber.substring(2, 5)} ${cleanWaNumber.substring(5, 9)} ${cleanWaNumber.substring(9)}`
    : `+${cleanWaNumber}`;
  const isMidtransStrict = Boolean(featureFlags?.midtrans_mode);
  const isCvProEnabled = !isMidtransStrict && (featureFlags?.enable_cv_pro !== false);
  const isBlueprintEnabled = featureFlags?.enable_vision_blueprint !== false;
  const isContractEnabled = featureFlags?.enable_digital_contract !== false;
  const isClientOnboardingEnabled = featureFlags?.enable_client_onboarding !== false;
  const hasAnyPillar = isBlueprintEnabled || isCvProEnabled || isContractEnabled || isClientOnboardingEnabled;

  const isEn = currentLocale === 'en' || (typeof window !== 'undefined' && (document.documentElement.lang?.startsWith('en') || document.cookie.includes('neriah_locale=en')));

  // State for in-page Legal Policies Accordion above footer
  const [openPolicyTab, setOpenPolicyTab] = useState(null); // 'terms' | 'privacy' | 'refund' | null

  const handleTogglePolicy = (tabKey) => {
    if (openPolicyTab === tabKey) {
      setOpenPolicyTab(null);
    } else {
      setOpenPolicyTab(tabKey);
      setTimeout(() => {
        const el = document.getElementById('footer-legal-accordion');
        if (el) {
          el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }, 60);
    }
  };

  // Sync with URL Hash and custom events across the page
  useEffect(() => {
    if (typeof window !== 'undefined') {
      const hash = (window.location.hash || '').toLowerCase();
      if (hash === '#terms' || hash === '#syarat-ketentuan') {
        setOpenPolicyTab('terms');
      } else if (hash === '#privacy' || hash === '#kebijakan-privasi') {
        setOpenPolicyTab('privacy');
      } else if (hash === '#refund' || hash === '#kebijakan-pengembalian') {
        setOpenPolicyTab('refund');
      }

      const handleCustomEvent = (e) => {
        if (e.detail?.tab) {
          handleTogglePolicy(e.detail.tab);
        }
      };
      window.addEventListener('open-legal-accordion', handleCustomEvent);
      return () => window.removeEventListener('open-legal-accordion', handleCustomEvent);
    }
  }, []);

  // Structured Policy Data (ID & EN)
  const policies = {
    terms: {
      title: isEn ? 'Terms & Conditions of Service' : 'Syarat & Ketentuan Layanan',
      icon: FileText,
      fullUrl: isEn ? '/terms?lang=en' : '/terms?lang=id',
      clauses: isEn ? [
        {
          num: '1',
          head: 'Scope of Digital Services',
          body: 'Neriah Pro provides enterprise-grade software architecture blueprints (PRD), downloadable schema engines, and full custom studio monolith development (5 sprints). All deliverables strictly conform to scope-locked specifications.'
        },
        {
          num: '2',
          head: 'Payment Terms via Midtrans Snap',
          body: 'Retail digital licenses are settled 100% upfront via Midtrans Snap (QRIS, Virtual Account, Credit Card). Studio turnkey projects require a 50% Down Payment (DP) to lock engineering schedules, with the final 50% due upon production delivery.'
        },
        {
          num: '3',
          head: 'Scope Freeze & Formal Addendum',
          body: 'Signed blueprints are legally binding and scope-locked. Additional feature requests beyond the PRD baseline are processed via formal Change Requests (CR) / Addendums with separate sprint timelines.'
        },
        {
          num: '4',
          head: 'Intellectual Property (IP) Rights',
          body: 'Retail licenses grant a perpetual, royalty-free commercial license to build derivative software. Studio contracts transfer 100% source code ownership and private GitHub repository to the client upon final settlement.'
        },
        {
          num: '5',
          head: 'Governing Law',
          body: 'These terms are governed by and construed in accordance with the laws of the Republic of Indonesia. Both parties submit to the jurisdiction of Indonesian courts.'
        }
      ] : [
        {
          num: '1',
          head: 'Ruang Lingkup Layanan Digital',
          body: 'Neriah Pro menyediakan produk cetak biru arsitektur perangkat lunak (PRD), mesin skema basis data siap pakai, serta jasa pengerjaan rekayasa software turnkey 5 sprint. Seluruh pengerjaan mengacu ketat pada spesifikasi yang telah dikunci (Scope Locked).'
        },
        {
          num: '2',
          head: 'Tata Kelola Pembayaran Midtrans Snap',
          body: 'Lisensi retail dibayarkan 100% di muka melalui Midtrans Snap (QRIS, Virtual Account, Kartu Kredit). Proyek kemitraan studio dibagi menjadi 2 termin: Uang Muka (DP 50%) untuk mengunci slot engineer, dan Pelunasan (50%) saat serah terima sistem.'
        },
        {
          num: '3',
          head: 'Prinsip Scope Freeze & Addendum Resmi',
          body: 'Dokumen PRD yang telah disetujui bersifat mengikat secara hukum. Permintaan penambahan fitur di luar PRD akan diproses secara transparan melalui Change Request (CR) / Addendum resmi dengan tambahan biaya dan hari kerja tersendiri.'
        },
        {
          num: '4',
          head: 'Hak Kekayaan Intelektual (IP)',
          body: 'Lisensi retail memberikan hak komersial seumur hidup bebas royalti untuk membangun software turunan (dilarang menjual kembali file mentah template). Proyek studio menyerahkan 100% hak cipta kode repositori GitHub ke Klien pascapelunasan.'
        },
        {
          num: '5',
          head: 'Hukum yang Berlaku',
          body: 'Syarat dan ketentuan ini diatur dan ditafsirkan sesuai hukum Republik Indonesia. Segala sengketa akan diselesaikan secara musyawarah mufakat terlebih dahulu.'
        }
      ]
    },
    privacy: {
      title: isEn ? 'Privacy & Data Protection Policy' : 'Kebijakan Privasi & Perlindungan Data',
      icon: Lock,
      fullUrl: isEn ? '/privacy-policy?lang=en' : '/privacy-policy?lang=id',
      clauses: isEn ? [
        {
          num: '1',
          head: 'Zero-Disclosure Confidentiality',
          body: 'We guarantee 100% confidentiality regarding your business ideas, database schemas, and architectural blueprints. Neriah Pro never sells, leases, or shares client data with any third parties.'
        },
        {
          num: '2',
          head: 'Information Collected',
          body: 'We collect contact metadata (name, email, E.164 phone number) solely for account verification, sprint notifications, and electronic commercial invoice generation.'
        },
        {
          num: '3',
          head: 'PostgreSQL Distributed ULID Isolation',
          body: 'All client records are secured within PostgreSQL 16 using 26-character distributed ULID primary keys, preventing enumeration attacks and ensuring multi-tenant isolation.'
        },
        {
          num: '4',
          head: 'Payment Vault Security (PCI-DSS)',
          body: 'Payment card data is processed directly inside Midtrans PCI-DSS Level 1 tokenized vaults. Neriah Pro never stores card numbers, CVVs, or bank credentials on our local servers.'
        },
        {
          num: '5',
          head: 'Data Rights & Removal',
          body: `Clients maintain the right to inspect, export, or permanently delete their account data by contacting our official compliance team at ${effectiveEmail}.`
        }
      ] : [
        {
          num: '1',
          head: 'Komitmen Kerahasiaan Ide & Data Klien',
          body: 'Kami menjamin 100% kerahasiaan ide bisnis, skema basis data, dan berkas PRD Anda. Neriah Pro tidak akan menjual, menyewakan, atau membagikan data klien kepada pihak ketiga mana pun untuk tujuan komersial.'
        },
        {
          num: '2',
          head: 'Informasi yang Dikumpulkan',
          body: 'Kami mengumpulkan data kontak (nama, email, nomor WhatsApp E.164) semata-mata untuk verifikasi akun, pengiriman laporan sprint mingguan, dan penerbitan bukti pembayaran / invoice resmi.'
        },
        {
          num: '3',
          head: 'Isolasi Basis Data PostgreSQL Strict ULID',
          body: 'Seluruh identitas data disimpan aman pada PostgreSQL 16 dengan Distributed ULID 26 karakter untuk menjamin integritas data terisolasi dan mengeliminasi risiko serangan enumerasi ID.'
        },
        {
          num: '4',
          head: 'Keamanan Transaksi Berstandar PCI-DSS',
          body: 'Neriah Pro tidak menyimpan data nomor kartu kredit atau CVV. Seluruh proses pembayaran kartu dan tokenisasi ditangani langsung oleh infrastruktur bersertifikasi PCI-DSS Level 1 Midtrans.'
        },
        {
          num: '5',
          head: 'Hak Pemilik Data',
          body: `Klien berhak meminta salinan data akun atau mengajukan permohonan penghapusan data permanen melalui email resmi: ${effectiveEmail}.`
        }
      ]
    },
    refund: {
      title: isEn ? 'Refund, Cancellation & Warranty Policy' : 'Garansi & Kebijakan Pengembalian Dana',
      icon: RefreshCcw,
      fullUrl: isEn ? '/refund-policy?lang=en' : '/refund-policy?lang=id',
      clauses: isEn ? [
        {
          num: '1',
          head: 'Studio 50% Down Payment Policy',
          body: 'The 50% Down Payment (DP) is non-refundable once architecture sprint work has commenced for the booked batch schedule, reflecting committed engineering resources.'
        },
        {
          num: '2',
          head: '30-Calendar-Day Bug Fix Warranty (SLA)',
          body: 'Upon production deployment, Neriah Pro provides a 30-calendar-day warranty for defect and bug remediation within the agreed PRD scope at zero additional cost, backed by a < 4 hour emergency response SLA.'
        },
        {
          num: '3',
          head: 'Digital Retail License Guarantee',
          body: 'For downloadable digital blueprints (PRD PDF, OpenAPI, DDL SQL), refund requests may be submitted within 24 hours if files are proven to be corrupted, incomplete, or technically non-functional.'
        },
        {
          num: '4',
          head: 'Duplicate Payment Protection (Anti-Collision)',
          body: 'In the event of duplicate transactions caused by banking network latency on the same Order ID, our webhook reconciliation automatically issues a full refund or converts the amount into installment credit upon client request.'
        },
        {
          num: '5',
          head: 'Claim Resolution Procedure',
          body: `Submit refund claims or warranty tickets to ${effectiveEmail} citing your Midtrans ULID Order ID and technical details. Our audit team responds within 24 business hours.`
        }
      ] : [
        {
          num: '1',
          head: 'Kebijakan Uang Muka (DP 50%) Proyek Studio',
          body: 'Uang Muka (DP 50%) yang dibayarkan via Midtrans bersifat non-refundable apabila sprint pengerjaan arsitektur telah resmi dimulai sesuai slot jadwal batch yang telah dikunci.'
        },
        {
          num: '2',
          head: 'Garansi 30 Hari Kalender Perbaikan Bug Gratis',
          body: 'Setelah serah terima sistem di server produksi, Klien memperoleh garansi pemeliharaan dan perbaikan bug gratis selama 30 hari kalender dengan SLA respons darurat < 4 jam pada jam kerja.'
        },
        {
          num: '3',
          head: 'Kebijakan Lisensi Retail Digital',
          body: 'Untuk produk digital unduhan instan (PRD PDF, OpenAPI, DDL SQL), pengembalian dana 100% dapat diajukan dalam 24 jam apabila berkas terbukti rusak (corrupted) atau tidak sesuai spesifikasi teknis.'
        },
        {
          num: '4',
          head: 'Proteksi Transaksi Ganda (Anti-Collision)',
          body: 'Apabila terjadi transaksi ganda pada Order ID yang sama akibat latensi perbankan, sistem otomatis memproses refund penuh (auto-refund) atau mengonversikannya menjadi kredit termin atas persetujuan Klien.'
        },
        {
          num: '5',
          head: 'Alur Pengajuan Klaim Refund & Garansi',
          body: `Ajukan klaim refund atau tiket garansi melalui email resmi ke ${effectiveEmail} dengan menyertakan Nomor Transaksi Midtrans / ULID Order ID. Tim kami memberikan keputusan dalam 1x24 jam kerja.`
        }
      ]
    }
  };

  const activePolicy = openPolicyTab ? policies[openPolicyTab] : null;

  return (
    <footer className="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 border-t border-zinc-200 dark:border-zinc-800 pt-16 pb-12 transition-colors font-sans">
      <div className="max-w-7xl mx-auto px-4 sm:px-6">

        {/* =================================================================== */}
        {/* INTERACTIVE LEGAL POLICIES ACCORDION (EXPANDS DIRECTLY ABOVE FOOTER) */}
        {/* =================================================================== */}
        {openPolicyTab && activePolicy && (
          <div
            id="footer-legal-accordion"
            className="mb-14 border-2 border-emerald-500 bg-zinc-50 dark:bg-zinc-900/90 rounded-none shadow-lg overflow-hidden transition-all duration-300"
          >
            {/* Accordion Top Control Bar */}
            <div className="bg-zinc-900 text-white p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-800">
              <div className="flex items-center gap-3">
                <span className="w-2.5 h-2.5 bg-emerald-500 rounded-none animate-pulse"></span>
                <div>
                  <span className="text-[10px] font-mono uppercase tracking-widest text-emerald-400 font-bold block">
                    {isEn ? 'LEGAL POLICIES & USER GOVERNANCE' : 'DOKUMEN HUKUM & TATA KELOLA KLIEN'}
                  </span>
                  <h3 className="text-base sm:text-lg font-black uppercase tracking-tight text-white flex items-center gap-2">
                    {activePolicy.title}
                  </h3>
                </div>
              </div>

              {/* Accordion Tab Switcher Buttons */}
              <div className="flex flex-wrap items-center gap-2 font-mono text-xs">
                <button
                  type="button"
                  onClick={() => setOpenPolicyTab('terms')}
                  className={`px-3 py-1.5 font-bold uppercase transition rounded-none cursor-pointer ${
                    openPolicyTab === 'terms'
                      ? 'bg-emerald-500 text-black shadow-xs'
                      : 'bg-zinc-800 text-zinc-300 hover:bg-zinc-700 hover:text-white'
                  }`}
                >
                  {isEn ? 'Terms' : 'Syarat & Ketentuan'}
                </button>

                <button
                  type="button"
                  onClick={() => setOpenPolicyTab('privacy')}
                  className={`px-3 py-1.5 font-bold uppercase transition rounded-none cursor-pointer ${
                    openPolicyTab === 'privacy'
                      ? 'bg-emerald-500 text-black shadow-xs'
                      : 'bg-zinc-800 text-zinc-300 hover:bg-zinc-700 hover:text-white'
                  }`}
                >
                  {isEn ? 'Privacy' : 'Kebijakan Privasi'}
                </button>

                <button
                  type="button"
                  onClick={() => setOpenPolicyTab('refund')}
                  className={`px-3 py-1.5 font-bold uppercase transition rounded-none cursor-pointer ${
                    openPolicyTab === 'refund'
                      ? 'bg-emerald-500 text-black shadow-xs'
                      : 'bg-zinc-800 text-zinc-300 hover:bg-zinc-700 hover:text-white'
                  }`}
                >
                  {isEn ? 'Refund Policy' : 'Garansi & Refund'}
                </button>

                <a
                  href={activePolicy.fullUrl}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="px-2.5 py-1.5 text-zinc-400 hover:text-emerald-400 flex items-center gap-1 transition"
                  title={isEn ? 'Open standalone page' : 'Buka halaman penuh'}
                >
                  <ExternalLink className="w-3.5 h-3.5" />
                  <span className="hidden sm:inline">{isEn ? 'Full Page ↗' : 'Buka Penuh ↗'}</span>
                </a>

                <button
                  type="button"
                  onClick={() => setOpenPolicyTab(null)}
                  className="px-3 py-1.5 bg-rose-950/80 hover:bg-rose-900 text-rose-300 border border-rose-800 font-bold uppercase transition rounded-none cursor-pointer flex items-center gap-1"
                  title={isEn ? 'Close Legal Panel' : 'Tutup Panel Hukum'}
                >
                  <X className="w-3.5 h-3.5" />
                  <span>{isEn ? 'Close' : 'Tutup'}</span>
                </button>
              </div>
            </div>

            {/* Accordion Body Clauses */}
            <div className="p-6 sm:p-8 space-y-6 text-sm text-zinc-800 dark:text-zinc-200">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6 font-mono text-xs">
                {activePolicy.clauses.map((clause, idx) => (
                  <div
                    key={idx}
                    className="p-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs space-y-2"
                  >
                    <div className="flex items-center gap-2 border-b border-zinc-100 dark:border-zinc-800 pb-2">
                      <span className="w-5 h-5 bg-emerald-500 text-black font-black flex items-center justify-center text-[10px]">
                        {clause.num}
                      </span>
                      <h4 className="font-black uppercase text-zinc-900 dark:text-white">
                        {clause.head}
                      </h4>
                    </div>
                    <p className="text-zinc-600 dark:text-zinc-400 leading-relaxed font-sans text-xs">
                      {clause.body}
                    </p>
                  </div>
                ))}
              </div>

              {/* Bottom Quick Help Box */}
              <div className="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-300 dark:border-emerald-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs font-mono">
                <div className="flex items-center gap-2 text-emerald-950 dark:text-emerald-200">
                  <ShieldCheck className="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
                  <span>
                    {isEn 
                      ? 'Have questions regarding compliance or commercial agreements? Contact legal team directly:' 
                      : 'Memerlukan klarifikasi khusus terkait perjanjian kerja sama atau transaksi? Hubungi tim legal:'}
                  </span>
                </div>
                <a
                  href={`mailto:${effectiveEmail}`}
                  className="font-bold text-emerald-700 dark:text-emerald-400 hover:underline uppercase"
                >
                  {effectiveEmail}
                </a>
              </div>
            </div>
          </div>
        )}
        
        {/* Top Branding & Mission */}
        <div className="grid grid-cols-1 md:grid-cols-12 gap-10 pb-12 border-b border-zinc-200 dark:border-zinc-800">
          
          <div className="md:col-span-3">
            <a href="/" className="inline-flex items-center gap-2 text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-4">
              <span className="w-7 h-7 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black flex items-center justify-center text-xs font-mono font-bold rounded-none">
                N
              </span>
              <span>NERIAH<span className="text-emerald-500">PRO</span> // HUB</span>
            </a>
            <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed max-w-sm mb-6">
              {isEn 
                ? 'Platform providing innovative applications and digital systems that empower everyday life needs. From Project OS architectural blueprinting to automated productivity studios.'
                : 'Situs dan platform penyedia aplikasi-aplikasi inovatif yang membantu berbagai kebutuhan hidup dan produktivitas Anda. Dari perancangan cetak biru Project OS hingga studio karier dan sistem digital modern.'}
            </p>
            <div className="inline-flex items-center gap-2 px-3 py-1.5 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 text-[11px] font-mono uppercase tracking-wider rounded-none">
              <span className="w-2 h-2 bg-emerald-500 rounded-none animate-pulse"></span>
              <span>{isEn ? 'LIFE SOLUTIONS & SOFTWARE ENGINEERING' : 'APLIKASI SOLUSI HIDUP & REKAYASA SISTEM'}</span>
            </div>
          </div>

          {/* Solutions Hub */}
          {hasAnyPillar && (
            <div className="md:col-span-2">
              <h4 className="font-mono font-bold uppercase tracking-widest text-xs text-zinc-400 dark:text-zinc-500 mb-4">
                {isEn ? 'Solutions' : 'Aplikasi'}
              </h4>
              <ul className="space-y-2.5 font-mono text-xs text-zinc-600 dark:text-zinc-400">
                {isBlueprintEnabled && (
                  <li>
                    <a href="/blueprint" className="hover:text-emerald-500 transition-colors flex items-center gap-1">
                      <span>Project OS (PRD)</span>
                      <ArrowUpRight className="w-3 h-3 text-zinc-400" />
                    </a>
                  </li>
                )}
                {isCvProEnabled && (
                  <li>
                    <a href="/cv-pro" className="hover:text-emerald-500 transition-colors flex items-center gap-1">
                      <span>CV Pro Studio</span>
                      <ArrowUpRight className="w-3 h-3 text-zinc-400" />
                    </a>
                  </li>
                )}
              </ul>
            </div>
          )}

          {/* Legal & Compliance Links (Toggles Accordion Above) */}
          <div className="md:col-span-2">
            <h4 className="font-mono font-bold uppercase tracking-widest text-xs text-zinc-400 dark:text-zinc-500 mb-4">
              {isEn ? 'Legal & Policy' : 'Legal & Regulasi'}
            </h4>
            <ul className="space-y-2.5 font-mono text-xs text-zinc-600 dark:text-zinc-400">
              <li>
                <button
                  type="button"
                  onClick={() => handleTogglePolicy('terms')}
                  className="hover:text-emerald-500 transition-colors flex items-center gap-1 text-left cursor-pointer"
                >
                  <span className={openPolicyTab === 'terms' ? 'text-emerald-500 font-bold underline' : ''}>
                    {isEn ? 'Terms of Service' : 'Syarat & Ketentuan'}
                  </span>
                </button>
              </li>
              <li>
                <button
                  type="button"
                  onClick={() => handleTogglePolicy('privacy')}
                  className="hover:text-emerald-500 transition-colors flex items-center gap-1 text-left cursor-pointer"
                >
                  <span className={openPolicyTab === 'privacy' ? 'text-emerald-500 font-bold underline' : ''}>
                    {isEn ? 'Privacy Policy' : 'Kebijakan Privasi'}
                  </span>
                </button>
              </li>
              <li>
                <button
                  type="button"
                  onClick={() => handleTogglePolicy('refund')}
                  className="hover:text-emerald-500 transition-colors flex items-center gap-1 text-left cursor-pointer"
                >
                  <span className={openPolicyTab === 'refund' ? 'text-emerald-500 font-bold underline' : ''}>
                    {isEn ? 'Refund & Warranty' : 'Kebijakan Refund'}
                  </span>
                </button>
              </li>
              <li>
                <a href="/smart-guide" className="hover:text-emerald-500 transition-colors flex items-center gap-1 font-bold text-emerald-600 dark:text-emerald-400">
                  <span>{isEn ? 'Smart Client Guide' : 'Panduan & Hak Klien'}</span>
                </a>
              </li>
            </ul>
          </div>

          {/* Technical Architecture Standards */}
          <div className="md:col-span-2">
            <h4 className="font-mono font-bold uppercase tracking-widest text-xs text-zinc-400 dark:text-zinc-500 mb-4">
              {isEn ? 'Developer Skills' : 'Keahlian Rekayasa'}
            </h4>
            <ul className="space-y-2.5 font-mono text-xs text-zinc-600 dark:text-zinc-400">
              <li className="flex items-center gap-1.5">
                <span className="w-1.5 h-1.5 bg-emerald-500"></span>
                <span>Full-Stack Web Systems</span>
              </li>
              <li className="flex items-center gap-1.5">
                <span className="w-1.5 h-1.5 bg-emerald-500"></span>
                <span>PostgreSQL &amp; Strict ULID</span>
              </li>
              <li className="flex items-center gap-1.5">
                <span className="w-1.5 h-1.5 bg-emerald-500"></span>
                <span>Midtrans API &amp; Webhooks</span>
              </li>
              <li className="flex items-center gap-1.5">
                <span className="w-1.5 h-1.5 bg-emerald-500"></span>
                <span>Clean Code &amp; Linux VPS</span>
              </li>
            </ul>
          </div>

          {/* Direct Office Address & Fast Connect */}
          <div className="md:col-span-3">
            <h4 className="font-mono font-bold uppercase tracking-widest text-xs text-zinc-400 dark:text-zinc-500 mb-4">
              {isEn ? 'Developer & Contact' : 'Identitas & Kontak Resmi'}
            </h4>
            <div className="space-y-3 font-mono text-xs">
              {/* Owner / Developer Profile */}
              <div className="pb-2 border-b border-zinc-200 dark:border-zinc-800">
                <div className="font-bold text-zinc-900 dark:text-zinc-100 text-xs">
                  {ownerName}
                </div>
                <div className="text-[11px] text-emerald-600 dark:text-emerald-400 font-mono">
                  {ownerTitle}
                </div>
                <div className="text-[10px] text-zinc-500 font-sans mt-0.5">
                  {isEn ? 'Individual Merchant / Developer Profile' : 'Profil Usaha Perorangan (Jalur KTP)'}
                </div>
              </div>

              {/* Physical Office Address */}
              <div className="text-zinc-600 dark:text-zinc-400 flex items-start gap-2 leading-relaxed">
                <MapPin className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                <span className="font-sans text-[11px] text-zinc-700 dark:text-zinc-300">
                  {companyAddress}
                </span>
              </div>

              {/* Official Email */}
              <div className="text-zinc-600 dark:text-zinc-400 flex items-center gap-2">
                <Mail className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                <a href={`mailto:${effectiveEmail}`} className="hover:text-emerald-500 transition-colors font-mono text-[11px] text-zinc-700 dark:text-zinc-300">
                  {effectiveEmail}
                </a>
              </div>

              {/* Explicit Phone / WhatsApp Number */}
              <div className="text-zinc-600 dark:text-zinc-400 flex items-center gap-2">
                <Phone className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                <a href={`tel:+${cleanWaNumber}`} className="font-mono font-bold text-zinc-900 dark:text-zinc-100 hover:text-emerald-500 transition-colors text-xs">
                  {formattedPhone}
                </a>
              </div>

              {/* Service Hours */}
              <div className="text-zinc-500 dark:text-zinc-400 text-[10px] flex items-center gap-2 pt-1 border-t border-zinc-200 dark:border-zinc-800">
                <Clock className="w-3 h-3 text-zinc-400 shrink-0" />
                <span>{isEn ? 'Mon - Fri, 09:00 - 18:00 WITA' : 'Senin - Jumat, 09:00 - 18:00 WITA'}</span>
              </div>

              {/* Action Buttons */}
              <div className="space-y-2 pt-1">
                <a
                  href={`https://wa.me/${cleanWaNumber}?text=Halo%20Yoseph%20Iriandi,%20saya%20ingin%20konsultasi%20layanan%20pengembangan%20software`}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white font-bold px-3 py-2 rounded-none transition w-full justify-center"
                >
                  <MessageCircle className="w-3.5 h-3.5" />
                  <span>{isEn ? 'WhatsApp Chat' : 'Chat WhatsApp'}</span>
                </a>
                <a
                  href="/customer/dashboard"
                  className="inline-flex items-center gap-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 px-3 py-2 rounded-none transition w-full justify-center"
                >
                  <ShieldCheck className="w-3.5 h-3.5 text-emerald-500" />
                  <span>{isEn ? 'Client Dashboard' : 'Portal Klien'}</span>
                </a>
              </div>
            </div>
          </div>

        </div>

        {/* Bottom Legal & Copyright Bar (Toggles Accordion Above) */}
        <div className="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-mono text-zinc-500 dark:text-zinc-400">
          <div>
            <p>&copy; {currentYear} NERIAH PRO by {ownerName}. {isEn ? 'Professional Independent Developer Services.' : 'Layanan Rekayasa Software Mandiri.'}</p>
            <div className="flex flex-wrap items-center gap-4 mt-2 text-[11px]">
              <button
                type="button"
                onClick={() => handleTogglePolicy('terms')}
                className="hover:text-emerald-500 transition underline underline-offset-2 cursor-pointer"
              >
                <span className={openPolicyTab === 'terms' ? 'text-emerald-500 font-bold' : ''}>
                  {isEn ? 'Terms of Service' : 'Syarat & Ketentuan'}
                </span>
              </button>
              <span className="text-zinc-300 dark:text-zinc-700">&bull;</span>
              <button
                type="button"
                onClick={() => handleTogglePolicy('privacy')}
                className="hover:text-emerald-500 transition underline underline-offset-2 cursor-pointer"
              >
                <span className={openPolicyTab === 'privacy' ? 'text-emerald-500 font-bold' : ''}>
                  {isEn ? 'Privacy Policy' : 'Kebijakan Privasi'}
                </span>
              </button>
              <span className="text-zinc-300 dark:text-zinc-700">&bull;</span>
              <button
                type="button"
                onClick={() => handleTogglePolicy('refund')}
                className="hover:text-emerald-500 transition underline underline-offset-2 cursor-pointer"
              >
                <span className={openPolicyTab === 'refund' ? 'text-emerald-500 font-bold' : ''}>
                  {isEn ? 'Refund & Warranty Policy' : 'Garansi & Kebijakan Pengembalian Dana'}
                </span>
              </button>
              <span className="text-zinc-300 dark:text-zinc-700">&bull;</span>
              <a href="/smart-guide" className="hover:text-emerald-500 transition underline underline-offset-2 font-bold text-emerald-600 dark:text-emerald-400">
                {isEn ? 'Smart Client Guide' : 'Panduan Pintar & Hak'}
              </a>
            </div>
          </div>
          <div className="flex items-center gap-4">
            <span className="text-zinc-400 dark:text-zinc-500 text-[11px]">SECURE CLOUD PLATFORM // ENTERPRISE DATA INTEGRITY</span>
            <span className="text-emerald-600 dark:text-emerald-400 font-bold uppercase text-[11px] px-2 py-0.5 bg-emerald-500/10 border border-emerald-500/20">
              MIDTRANS READY
            </span>
          </div>
        </div>

      </div>
    </footer>
  );
}
