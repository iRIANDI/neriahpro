import React from 'react';
import { CheckCircle2, Rocket, ShoppingBag, ArrowRight } from 'lucide-react';

export default function BlueprintSuccessView({
  successData,
  formData,
  currentTrack,
  lang,
  t,
  onResetForm,
}) {
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

        <div className="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 mb-6 font-mono text-xs space-y-2">
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
          <div className="flex justify-between border-t border-zinc-200 dark:border-zinc-800 pt-2">
            <span className="text-zinc-500">EXECUTION_TRACK:</span>
            <span className={`font-bold font-mono uppercase ${currentTrack === 'studio' ? 'text-blue-500' : 'text-emerald-500'}`}>
              {currentTrack === 'studio' ? (lang === 'en' ? 'Turnkey Studio (SLA & Managed Sprint)' : 'Jalur Turnkey (Dedicated Studio & SLA)') : (lang === 'en' ? 'Self-Service Retail (Software Factory OS)' : 'Jalur Mandiri (Lisensi Digital Retail)')}
            </span>
          </div>
        </div>

        {/* 3 WAYS TO START ROADMAP NOTIFICATION */}
        <div className="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-400 dark:border-emerald-700 mb-8 font-mono text-xs space-y-2">
          <div className="flex items-center gap-2 text-emerald-950 dark:text-emerald-300 font-black uppercase">
            <span>🚀</span>
            <span>{lang === 'en' ? '3 WAYS TO START ARE READY INSIDE YOUR BLUEPRINT:' : '3 PILIHAN CARA MULAI MENUNGGU DI DOKUMEN CETAK BIRU:'}</span>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[11px] font-sans text-zinc-700 dark:text-zinc-300 pt-1">
            <div className="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
              <strong className="block text-zinc-950 dark:text-zinc-100 font-bold">🚀 {lang === 'en' ? 'Option A: Antigravity Chat' : 'Pilihan A: Sesi Chat Antigravity'}</strong>
              <span>{lang === 'en' ? 'Instant execution in this session with 1-click prompts.' : 'Langsung koding di sesi ini dengan prompt 1-klik.'}</span>
            </div>
            <div className="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
              <strong className="block text-zinc-950 dark:text-zinc-100 font-bold">📦 {lang === 'en' ? 'Option B: Standalone App' : 'Pilihan B: Standalone App'}</strong>
              <span>{lang === 'en' ? 'Download 22-file ZIP with Docker Compose stack.' : 'Unduh paket ZIP 22 file dengan Docker Compose.'}</span>
            </div>
            <div className="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
              <strong className="block text-zinc-950 dark:text-zinc-100 font-bold">🎯 {lang === 'en' ? 'Option C: AI Cockpit' : 'Pilihan C: AI Cockpit'}</strong>
              <span>{lang === 'en' ? 'Step-by-step phased vertical slice prompts.' : 'Eksekusi bertahap anti context-rot per sprint.'}</span>
            </div>
          </div>
        </div>

        <div className="flex flex-col sm:flex-row gap-4">
          {successData.redirect_url && (
            <a
              href={successData.redirect_url}
              className="flex-1 bg-emerald-600 hover:bg-emerald-500 text-black font-black uppercase tracking-wider py-4 px-6 rounded-none text-center flex items-center justify-center gap-2 transition"
            >
              {currentTrack === 'studio' ? (
                <>
                  <Rocket className="w-5 h-5 text-black" />
                  <span>{lang === 'en' ? 'Review PRD & Book Studio Sprint Slot →' : 'Tinjau PRD & Kunci Jadwal Sprint Studio →'}</span>
                </>
              ) : (
                <>
                  <ShoppingBag className="w-5 h-5 text-black" />
                  <span>{lang === 'en' ? 'Open PRD & Choose Retail License →' : 'Buka Dokumen PRD & Pilih Paket Lisensi →'}</span>
                </>
              )}
              <ArrowRight className="w-5 h-5" />
            </a>
          )}
          <button
            type="button"
            onClick={onResetForm}
            className="px-6 py-4 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-200 font-mono text-xs font-black uppercase tracking-wider rounded-none transition cursor-pointer"
          >
            + {t.newProjectBtn}
          </button>
        </div>
      </div>
    </div>
  );
}

