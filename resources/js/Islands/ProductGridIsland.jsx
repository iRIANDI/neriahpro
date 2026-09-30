import React from 'react';
import { motion } from 'framer-motion';
import { 
  Cpu, 
  FileText, 
  ShieldCheck, 
  Layers, 
  ArrowRight, 
  Sparkles, 
  CheckCircle2, 
  Lock, 
  Zap,
  Palette,
  Database
} from 'lucide-react';

export default function ProductGridIsland({ title, featureFlags }) {
  const isMidtransStrict = Boolean(featureFlags?.midtrans_mode);
  const isCvProEnabled = !isMidtransStrict && (featureFlags?.enable_cv_pro !== false);
  const isBlueprintEnabled = featureFlags?.enable_vision_blueprint !== false;
  const isContractEnabled = featureFlags?.enable_digital_contract !== false;
  const isClientOnboardingEnabled = featureFlags?.enable_client_onboarding !== false;

  const pillars = [
    {
      id: "project-os",
      title: "Project OS & PRD Generator",
      tag: "ACTIVE & LIVE",
      badgeColor: "bg-emerald-500 text-black",
      desc: "Platform otomatisasi sintesis ide bisnis menjadi Product Requirements Document (PRD), skema ERD PostgreSQL, alur kerja bertahap, dan penguncian kontrak kerja.",
      features: [
        "Formulir Kuesioner Discovery 4 Blok",
        "Skema ERD Otomatis Standar ULID",
        "Pemisahan Scope MVP vs Roadmap Fase 2",
        "Estimasi Sprint & Aligned Timeline",
        "Kunci Scope Kontrak & Pembayaran DP Midtrans"
      ],
      ctaText: "Mulai Rancang Blueprint",
      ctaLink: "/blueprint",
      isPrimary: true
    },
    {
      id: "cv-studio",
      title: "Canva-Style CV & Portfolio Studio",
      tag: "AI & ATS READY",
      badgeColor: "bg-zinc-800 text-zinc-200",
      desc: "Studio pembuat resume dan portofolio profesional interaktif bergaya Canva dengan drag-and-drop layer dan format ATS-ready internasional.",
      features: [
        "Kanvas Visual Drag & Drop",
        "Desain Minimalis Presisi & Sharp",
        "Ekspor PDF Otomatis High-Res",
        "Simpan Profil & Sinkronisasi Cloud",
        "Integrasi Link Portofolio Publik"
      ],
      ctaText: "Buka Studio CV Pro",
      ctaLink: "/cv-pro",
      isPrimary: false
    },
    {
      id: "legal-contracts",
      title: "Digital Contract & Legal E-Signature",
      tag: "CRYPTOGRAPHIC SHA-256",
      badgeColor: "bg-zinc-800 text-zinc-200",
      desc: "Modul pembuatan surat perjanjian kerja sama resmi dan penandatanganan digital sah untuk mengunci ruang lingkup pekerjaan sebelum proyek dimulai.",
      features: [
        "Perekaman Tanda Tangan Touchscreen & Mouse",
        "Enkripsi Hash SHA-256 & UTC Timestamp",
        "Protokol Scope Freeze / Penguncian Fitur",
        "Mekanisme Change Request (CR) Terpisah",
        "Rekam Jejak Audit IP Address Legal"
      ],
      ctaText: "Pelajari Kontrak Digital",
      ctaLink: "/blueprint#contract",
      isPrimary: false
    },
    {
      id: "rapid-monolith",
      title: "Enterprise Rapid Monolith Development",
      tag: "ENTERPRISE SCALABILITY",
      badgeColor: "bg-zinc-800 text-zinc-200",
      desc: "Jasa rekayasa perangkat lunak berskala tinggi menggunakan stack Modern Monolith (Laravel 13, Filament v5, PostgreSQL ULID, dan Redis) di atas Dedicated VPS.",
      features: [
        "Backend Monolith Tangguh & Cepat Rilis",
        "Pusat Kendali Dasbor Filament PHP v5",
        "Algoritma Paginasi Keyset O(1) Tanpa Lemot",
        "Frontend Island Architecture (React + Livewire)",
        "Dedicated Docker / Nixpacks VPS Environment"
      ],
      ctaText: "Konsultasi & Onboarding",
      ctaLink: "#onboarding",
      isPrimary: false
    }
  ];

  const activePillars = pillars.filter(pillar => {
    if (pillar.id === 'project-os') return isBlueprintEnabled;
    if (pillar.id === 'cv-studio') return isCvProEnabled;
    if (pillar.id === 'legal-contracts') return isContractEnabled;
    if (pillar.id === 'rapid-monolith') return isClientOnboardingEnabled;
    return true;
  });

  if (activePillars.length === 0) {
    return null;
  }

  const dynamicTitle = title 
    ? title.replace(/\b4\b/g, activePillars.length) 
    : `${activePillars.length} Pilar Layanan Digital Hub.`;

  return (
    <section id="services" className="py-24 px-4 sm:px-6 max-w-7xl mx-auto bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 transition-colors font-sans">
      
      {/* Section Header */}
      <div className="border-b border-zinc-200 dark:border-zinc-800 pb-8 mb-16">
        <div className="flex items-center gap-2 font-mono text-xs uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold mb-2">
          <Layers className="w-4 h-4" />
          <span>NERIAH PRO // {activePillars.length === 1 ? 'SERVICES ARSENAL' : `${activePillars.length} PILLARS ARSENAL`}</span>
        </div>
        <h2 className="text-3xl sm:text-5xl font-black uppercase tracking-tight text-zinc-900 dark:text-white">
          {dynamicTitle}
        </h2>
        <p className="text-zinc-600 dark:text-zinc-400 text-sm sm:text-base mt-2 max-w-2xl font-sans">
          Ekosistem terintegrasi untuk mewujudkan proyek teknologi Anda dari ide mentah, perancangan arsitektur, hingga peluncuran sistem enterprise.
        </p>
      </div>

      {/* Grid: Active Pillars */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {activePillars.map((pillar) => (
          <div 
            key={pillar.id}
            className={`border p-8 rounded-none transition-all duration-200 flex flex-col justify-between ${
              pillar.isPrimary 
                ? 'border-2 border-emerald-500 bg-white dark:bg-zinc-900 shadow-none' 
                : 'border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/60 hover:border-zinc-400 dark:hover:border-zinc-600'
            }`}
          >
            <div>
              {/* Badge & Tag */}
              <div className="flex items-center justify-between gap-2 mb-4 font-mono">
                <span className={`px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider rounded-none ${pillar.badgeColor}`}>
                  {pillar.tag}
                </span>
                {pillar.isPrimary && (
                  <span className="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">
                    &bull; UNGGULAN
                  </span>
                )}
              </div>

              {/* Title & Desc */}
              <h3 className="text-xl sm:text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-3">
                {pillar.title}
              </h3>
              <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed mb-6 font-sans">
                {pillar.desc}
              </p>

              {/* Checklist */}
              <div className="space-y-2 border-t border-zinc-100 dark:border-zinc-800 pt-4 mb-8 font-sans">
                {pillar.features.map((feat, idx) => (
                  <div key={idx} className="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                    <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 flex-shrink-0 mt-0.5" />
                    <span>{feat}</span>
                  </div>
                ))}
              </div>
            </div>

            {/* Action CTA */}
            <div>
              <a
                href={pillar.ctaLink}
                className={`w-full py-3.5 px-6 font-mono text-xs uppercase tracking-widest font-black rounded-none flex items-center justify-center gap-2 transition ${
                  pillar.isPrimary
                    ? 'bg-emerald-600 hover:bg-emerald-500 text-black'
                    : 'bg-zinc-900 hover:bg-black dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white'
                }`}
              >
                <span>{pillar.ctaText}</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </a>
            </div>
          </div>
        ))}
      </div>

    </section>
  );
}
