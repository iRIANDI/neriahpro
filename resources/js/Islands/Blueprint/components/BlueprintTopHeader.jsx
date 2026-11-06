import React from 'react';
import { Loader2, Sun, Moon, RotateCcw } from 'lucide-react';

export default function BlueprintTopHeader({
  currentTrack,
  lang,
  setLang,
  isDarkMode,
  toggleTheme,
  autoSaveStatus,
  lastSavedTime,
  completeness,
  formData,
  setFormData,
  handleResetForm,
  t,
}) {
  return (
    <>
      {/* SATU PINTU 5-STEP END-TO-END GUIDED CLIENT WIZARD BAR (RETAIL & MITRA V6.0) */}
      <div className="mb-5 border-2 border-zinc-900 dark:border-zinc-700 bg-white dark:bg-zinc-900 rounded-none shadow-xs">
        <div className="px-3.5 py-2 bg-zinc-950 text-white flex flex-wrap items-center justify-between gap-2 font-mono text-[11px]">
          <div className="flex flex-wrap items-center gap-2">
            <span className="px-2 py-0.5 bg-emerald-500 text-zinc-950 font-black uppercase">SATU PINTU V6.0</span>
            <span className="font-bold uppercase">
              {currentTrack === 'studio'
                ? (lang === 'en' ? 'ACTIVE TRACK: MITRA TURNKEY PROJECT (RAB, SPK SHA-256, UAT & BAST)' : 'JALUR AKTIF: MITRA PROYEK TURNKEY (RAB, SPK SHA-256, UAT & BAST)')
                : (lang === 'en' ? 'ACTIVE TRACK: RETAIL SELF-DEV (PRD, ULID SQL, DAG SUB-PARTS & MCP)' : 'JALUR AKTIF: RETAIL MANDIRI (PRD, SQL ULID, DAG SUB-PART & MCP)')}
            </span>
          </div>
          <div className="flex flex-wrap items-center gap-1.5">
            <button
              type="button"
              onClick={handleResetForm}
              className="px-2 py-0.5 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black uppercase text-[10px] rounded-none cursor-pointer"
              title={lang === 'en' ? 'Start a 100% clean isolated new project' : 'Mulai ruang kerja proyek baru yang 100% bersih tanpa sisa proyek lama'}
            >
              + {lang === 'en' ? 'Clean New Project' : 'Proyek Baru Bersih'}
            </button>
            <a
              href="/pricing"
              className="px-2 py-0.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 font-bold uppercase text-[10px] rounded-none"
            >
              &larr; {lang === 'en' ? 'Pricing Wizard' : 'Harga & Simulasi RAB'}
            </a>
            <a
              href={`/blueprint/studio?mode=${currentTrack === 'studio' ? 'mitra' : 'retail'}${formData.primarySector ? `&sector=${formData.primarySector}` : ''}${formData.stackPreset ? `&stack=${formData.stackPreset}` : ''}`}
              className="px-2.5 py-0.5 bg-amber-400 hover:bg-amber-300 text-zinc-950 font-black uppercase text-[10px] rounded-none"
            >
              🖥️ {lang === 'en' ? 'Open 3-Pane Studio →' : 'Buka 3-Pane Studio →'}
            </a>
          </div>
        </div>
        <div className="grid grid-cols-2 sm:grid-cols-5 divide-y sm:divide-y-0 sm:divide-x divide-zinc-200 dark:divide-zinc-800 bg-zinc-50 dark:bg-zinc-950 font-mono text-[10px]">
          {[
            { num: '01', label: lang === 'en' ? 'Path & 12 Sectors' : 'Pilih Jalur & 12 Sektor', done: true, active: false },
            { num: '02', label: lang === 'en' ? '26-Param Discovery' : 'Elaborasi 26 Parameter', done: completeness.score >= 90, active: true },
            { num: '03', label: currentTrack === 'studio' ? (lang === 'en' ? 'RAB & SPK SHA-256' : 'RAB & Kunci SPK SHA-256') : (lang === 'en' ? 'Instant License' : 'Lisensi & Unduh Kit'), done: false, active: false },
            { num: '04', label: currentTrack === 'studio' ? (lang === 'en' ? '7-Milestone & Staging' : '7-Milestone & Live Staging') : (lang === 'en' ? '7-Phase IDE & MCP' : '7 Fase AI & Live MCP'), done: false, active: false },
            { num: '05', label: currentTrack === 'studio' ? (lang === 'en' ? 'UAT & Digital BAST' : 'Checklist UAT & BAST') : (lang === 'en' ? 'Verify.sh & RedTeam' : 'Verify.sh & Red-Team'), done: false, active: false },
          ].map((st) => (
            <div
              key={st.num}
              className={`px-3 py-2 flex items-center gap-2 ${st.active
                ? 'bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-950 dark:text-emerald-200 font-black'
                : st.done
                  ? 'text-emerald-900 dark:text-emerald-300 font-bold'
                  : 'text-zinc-600 dark:text-zinc-400 font-bold'
                }`}
            >
              <span
                className={`px-1.5 py-0.5 border ${st.active
                  ? 'bg-zinc-950 text-emerald-400 border-zinc-950 dark:bg-emerald-400 dark:text-zinc-950 dark:border-emerald-400'
                  : st.done
                    ? 'bg-emerald-600 text-white border-emerald-600'
                    : 'bg-white dark:bg-zinc-900 border-zinc-300 dark:border-zinc-700'
                  }`}
              >
                {st.done && !st.active ? '✓' : st.num}
              </span>
              <span className="leading-tight">{st.label}</span>
            </div>
          ))}
        </div>
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
              onClick={() => {
                setLang('id');
                setFormData(prev => ({ ...prev, outputLocale: 'id', output_locale: 'id' }));
              }}
              className={`px-2.5 py-1 font-bold transition ${lang === 'id'
                ? 'bg-zinc-900 text-white dark:bg-emerald-500 dark:text-black'
                : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'
                }`}
            >
              ID
            </button>
            <button
              type="button"
              onClick={() => {
                setLang('en');
                setFormData(prev => ({ ...prev, outputLocale: 'en', output_locale: 'en' }));
              }}
              className={`px-2.5 py-1 font-bold transition ${lang === 'en'
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

          {/* Bersihkan Form / Reset Button */}
          <button
            type="button"
            onClick={handleResetForm}
            className="px-2.5 py-1 flex items-center gap-1.5 border border-red-300 dark:border-red-900/60 bg-red-50/70 dark:bg-red-950/40 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/60 font-mono text-xs font-bold transition rounded-none cursor-pointer"
            title={lang === 'en' ? 'Reset all fields to blank' : 'Bersihkan seluruh isi formulir menjadi kosong'}
          >
            <RotateCcw className="w-3.5 h-3.5" />
            <span>{t.resetFormBtn || (lang === 'en' ? 'Reset' : 'Bersihkan Form')}</span>
          </button>
        </div>
      </div>
    </>
  );
}
