import React from 'react';
import { ShoppingBag, Rocket, ShieldCheck, CheckCircle2, Zap } from 'lucide-react';

export default function BlueprintTrackSwitcher({
  currentTrack,
  handleTrackChange,
  completeness,
  lang,
  t,
}) {
  return (
    <>
      {/* EXECUTION TRACK SWITCHER (JALUR MANDIRI RETAIL vs JALUR TURNKEY STUDIO) */}
      <div className="mb-6 border-2 border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 sm:p-5 rounded-none shadow-xs">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4">
          <div>
            <div className="text-[10px] font-mono uppercase tracking-wider font-bold text-zinc-500">
              {lang === 'en' ? 'SYSTEM EXECUTION TRACK' : 'PILIHAN JALUR EKSEKUSI SISTEM'}
            </div>
            <div className="text-sm sm:text-base font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
              <span>{lang === 'en' ? 'Select How This Blueprint Will Be Built:' : 'Tentukan Siapa yang Mengeksekusi Blueprint Ini:'}</span>
            </div>
          </div>
          <div className="text-[11px] font-mono text-zinc-500">
            {lang === 'en' ? 'Switch tracks anytime' : 'Bebas beralih jalur kapan saja'}
          </div>
        </div>

        {/* 2 Track Toggle Buttons */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
          {/* TRACK 1: RETAIL */}
          <button
            type="button"
            onClick={() => handleTrackChange('retail')}
            className={`p-3.5 text-left border-2 transition rounded-none cursor-pointer flex flex-col justify-between ${currentTrack === 'retail'
              ? 'border-emerald-500 bg-emerald-500/10 dark:bg-emerald-500/10'
              : 'border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 bg-zinc-50 dark:bg-zinc-950'
              }`}
          >
            <div className="flex items-start justify-between gap-2 mb-2">
              <div className="flex items-center gap-2">
                <ShoppingBag className={`w-4 h-4 ${currentTrack === 'retail' ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-400'}`} />
                <span className="font-mono text-xs font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
                  {lang === 'en' ? '01 // Self-Service Track' : '01 // Jalur Mandiri (Retail)'}
                </span>
              </div>
              <span className={`text-[9px] font-mono px-1.5 py-0.5 border font-bold uppercase ${currentTrack === 'retail'
                ? 'bg-emerald-500 text-black border-emerald-500'
                : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700'
                }`}>
                {currentTrack === 'retail' ? (lang === 'en' ? 'ACTIVE' : 'AKTIF') : (lang === 'en' ? 'SELECT' : 'PILIH')}
              </span>
            </div>
            <p className="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-3">
              {lang === 'en'
                ? 'For in-house coding teams. Instant download: PRD 26 params, Strict ULID DDL, Docker & AI Rules.'
                : 'Punya programmer sendiri. Unduh instan cetak biru PRD 26 param, DDL Strict ULID, Docker & Aturan AI.'}
            </p>
            <div className="font-mono text-[10px] text-emerald-600 dark:text-emerald-400 font-bold border-t border-zinc-200 dark:border-zinc-800/80 pt-2 flex items-center justify-between">
              <span>Rp 0 (Gratis) s/d Rp 1.490.000</span>
              <span className="text-zinc-500 font-normal">Akses Permanen</span>
            </div>
          </button>

          {/* TRACK 2: STUDIO */}
          <button
            type="button"
            onClick={() => handleTrackChange('studio')}
            className={`p-3.5 text-left border-2 transition rounded-none cursor-pointer flex flex-col justify-between ${currentTrack === 'studio'
              ? 'border-emerald-500 bg-emerald-500/10 dark:bg-emerald-500/10'
              : 'border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 bg-zinc-50 dark:bg-zinc-950'
              }`}
          >
            <div className="flex items-start justify-between gap-2 mb-2">
              <div className="flex items-center gap-2">
                <Rocket className={`w-4 h-4 ${currentTrack === 'studio' ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-400'}`} />
                <span className="font-mono text-xs font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
                  {lang === 'en' ? '02 // Dedicated Studio Track' : '02 // Jalur Turnkey (Studio)'}
                </span>
              </div>
              <span className={`text-[9px] font-mono px-1.5 py-0.5 border font-bold uppercase ${currentTrack === 'studio'
                ? 'bg-emerald-500 text-black border-emerald-500'
                : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700'
                }`}>
                {currentTrack === 'studio' ? (lang === 'en' ? 'ACTIVE' : 'AKTIF') : (lang === 'en' ? 'SELECT' : 'PILIH')}
              </span>
            </div>
            <p className="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-3">
              {lang === 'en'
                ? 'End-to-end coding by Neriah Pro Senior Architect. Managed sprint capacity & SLA bug warranty.'
                : 'Dikerjakan 100% turnkey oleh Senior Architect Neriah Pro. Managed sprint batch & garansi bug SLA.'}
            </p>
            <div className="font-mono text-[10px] text-blue-600 dark:text-blue-400 font-bold border-t border-zinc-200 dark:border-zinc-800/80 pt-2 flex items-center justify-between">
              <span>Rp 3.750.000 (UMKM) / Custom Quotation</span>
              <span className="text-zinc-500 font-normal">Termin DP 50%</span>
            </div>
          </button>
        </div>

        {/* Active Track Status Banner */}
        <div className={`p-3 border text-xs font-mono rounded-none ${currentTrack === 'studio'
          ? 'bg-blue-500/5 dark:bg-blue-500/[0.05] border-blue-500/30 text-zinc-800 dark:text-zinc-200'
          : 'bg-emerald-500/5 dark:bg-emerald-500/[0.05] border-emerald-500/30 text-zinc-800 dark:text-zinc-200'
          }`}>
          <div className="flex flex-wrap items-center justify-between gap-2 mb-1.5">
            <span className={`font-bold uppercase tracking-wider text-[11px] flex items-center gap-1.5 ${currentTrack === 'studio' ? 'text-blue-600 dark:text-blue-400' : 'text-emerald-600 dark:text-emerald-400'
              }`}>
              {currentTrack === 'studio' ? (
                <>
                  <ShieldCheck className="w-4 h-4 text-blue-500" />
                  <span>{lang === 'en' ? 'ACTIVE MODE: DEDICATED TURNKEY STUDIO (MANAGED SPRINT & SLA)' : 'JALUR AKTIF: DEDICATED STUDIO TURNKEY (MANAGED SPRINT & KONTRAK SLA)'}</span>
                </>
              ) : (
                <>
                  <CheckCircle2 className="w-4 h-4 text-emerald-500" />
                  <span>{lang === 'en' ? 'ACTIVE MODE: DIGITAL RETAIL LICENSE (SELF-SERVICE)' : 'JALUR AKTIF: LISENSI DIGITAL RETAIL (SOFTWARE FACTORY OS)'}</span>
                </>
              )}
            </span>
            <span className="text-[10px] font-bold text-zinc-500">
              {currentTrack === 'studio'
                ? (lang === 'en' ? 'Tiers: Advisory, UMKM Starter, Full MVP' : 'Pilihan Paket: Advisory, UMKM Starter, Full MVP')
                : (lang === 'en' ? 'Tiers: Spark, Starter, Pro, Ultimate 10 Pillars' : 'Pilihan Paket: Spark, Starter, Pro, Ultimate 10 Pilar')}
            </span>
          </div>

          <p className="font-sans text-[11px] leading-relaxed text-zinc-600 dark:text-zinc-400">
            {currentTrack === 'studio'
              ? (lang === 'en'
                ? 'All 26 parameters will form your official Turnkey Master Blueprint. Our Senior Architect will code, run Pest ApiContractTest, configure Docker, deploy to VPS, and transfer 100% intellectual property.'
                : 'Seluruh 26 parameter ini akan menjadi acuan resmi Master WBS Sprint. Senior Architect Neriah Pro yang akan mengoding, menguji Pest ApiContractTest, menyusun Docker, deploy live ke VPS, dan serah terima hak cipta 100%.')
              : (lang === 'en'
                ? 'All 26 parameters will generate an instant Fortune 500 architecture bundle: PRD document, PostgreSQL Strict ULID schema, Docker configs, and AI rules for your in-house engineering team.'
                : 'Seluruh 26 parameter ini akan menghasilkan bundel arsitektur siap unduh: Dokumen PRD Fortune 500, skema DDL PostgreSQL Strict ULID, konfigurasi Docker, dan aturan AI coding (.cursorrules & AGENTS.md) untuk tim koding Anda.')}
          </p>

          {currentTrack === 'studio' && (
            <div className="flex flex-wrap gap-2 mt-2 pt-2 border-t border-blue-500/20 text-[10px]">
              <span className="px-2 py-0.5 bg-blue-500/10 text-blue-600 dark:text-blue-400 font-bold border border-blue-500/20">
                🛡️ KONTRAK SLA &amp; HAK CIPTA 100%
              </span>
              <span className="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-500/20">
                ⏱️ MANAGED SPRINT BATCH (ANTI-COLLISION)
              </span>
              <span className="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold border border-zinc-300 dark:border-zinc-700">
                💳 MIDTRANS SNAP PAYMENT (DP 50%)
              </span>
            </div>
          )}
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
              {lang === 'en' ? 'Blueprint Stage: Flash Tier (Token-Saver). Final PRD Stage: Pro Tier.' : 'Tahap Blueprint: Flash Tier (Hemat Token). Tahap PRD Final: Pro Tier.'}
            </span>
          </div>
          <span className="px-2 py-0.5 text-[10px] bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 font-bold shrink-0">
            FREE PLAN OK
          </span>
        </div>
      </div>

    </>
  );
}
