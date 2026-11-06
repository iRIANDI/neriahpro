import React from 'react';
import {
  Check,
  Plus,
  Trash2,
  Sparkles,
  Loader2,
  AlertTriangle,
  ArrowRight,
  RotateCcw,
  ArrowUpRight,
  Lightbulb,
  Rocket,
  ShoppingBag,
  ChevronDown,
} from 'lucide-react';
import { FIELD_LABELS } from '../constants/blueprintConstants';

export default function BlueprintDiscoveryParamsSection({
  formData,
  updateField,
  panelClass,
  labelClass,
  inputClass,
  selectClass,
  invalidFields,
  errorMessage,
  setErrorMessage,
  isSubmitting,
  strokeActive,
  lang,
  t,
  jumpToField,
  handleFinalSubmit,
  countryList,
  selectedCountryCode,
  setSelectedCountryCode,
  currentCountry,
  phoneDigits,
  handlePhoneDigitsChange,
  currentTrack,
  isElaboratingKpi,
  elaborateKpiCascade,
  setPaymentModalOpen,
  setPendingPaymentSuggestion,
  activeAddKey,
  setActiveAddKey,
  newFeatureInput,
  setNewFeatureInput,
}) {
  return (
    <>
        <section id="section-block-a" className={panelClass}>
          <div className="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
            <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block mb-1">
              {lang === 'en' ? 'BUSINESS ARCHITECTURE // 01' : 'ARSITEKTUR BISNIS // 01'}
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
              <div className="flex flex-wrap items-center justify-between gap-2 mb-2">
                <label className={labelClass}>
                  {lang === 'en' ? '3. Key Success Metrics (Business KPIs)' : '3. Tolak Ukur Keberhasilan (Target KPI Bisnis)'}
                </label>
                <div className="flex items-center gap-2">
                  <button
                    type="button"
                    onClick={handleTriggerKpiElaboration}
                    disabled={isElaboratingKpi}
                    className="px-2.5 py-1 rounded-xs bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-mono text-[10px] font-black uppercase flex items-center gap-1.5 transition-colors cursor-pointer disabled:opacity-50 border border-emerald-500 shadow-xs"
                    title={lang === 'en' ? 'Deep-dive KPIs and cascade impact to features & actors' : 'Elaborasi KPI & sinkronkan dampak ke modul dan aktor'}
                  >
                    {isElaboratingKpi ? (
                      <>
                        <Loader2 className="w-3.5 h-3.5 animate-spin" />
                        <span>{lang === 'en' ? 'Calculating Cascade...' : 'Menganalisis Cascade...'}</span>
                      </>
                    ) : (
                      <>
                        <Sparkles className="w-3.5 h-3.5" />
                        <span>{lang === 'en' ? '✨ AI KPI Deep-Dive (Cascade Impact)' : '✨ Elaborasi KPI dengan AI (Cascade Impact)'}</span>
                      </>
                    )}
                  </button>

                  <button
                    type="button"
                    onClick={() => setActiveAddKey(activeAddKey === 'tujuanUtama' ? null : 'tujuanUtama')}
                    className="px-2.5 py-1 rounded-xs bg-zinc-100 dark:bg-zinc-900 hover:bg-zinc-200 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 text-[10px] font-mono font-bold flex items-center gap-1 transition-colors cursor-pointer"
                  >
                    <Plus className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" /> {lang === 'en' ? 'Add KPI' : 'Tambah KPI'}
                  </button>
                </div>
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
              {lang === 'en' ? 'ACTORS & PLATFORMS // 02' : 'AKTOR & PLATFORM // 02'}
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
                    ? 'Decoupled Headless: Next.js 15 / Nuxt 3 Frontend + Headless Laravel 13 / NestJS REST & GraphQL API'
                    : 'Decoupled Headless: Next.js 15 / Nuxt 3 Frontend + Headless Laravel 13 / NestJS REST & GraphQL API',
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
                    className={`px-3 py-1.5 text-xs font-mono border transition ${formData.targetPlatform === plat
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
              {lang === 'en' ? 'FEATURES, WORKFLOW & DATA // 03' : 'FITUR, ALUR & DATA // 03'}
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
                    className={`px-3 py-1.5 text-xs font-mono border transition ${formData.migrasiData === mig
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
              {lang === 'en' ? 'INTEGRATIONS & HOSTING // 04' : 'INTEGRASI & HOSTING // 04'}
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
                    ? 'Decoupled Edge & Cloud: Cloudflare Pages (Frontend) + Dedicated VPS Coolify (API Backend) + Cloudflare R2'
                    : 'Topologi Decoupled: Cloudflare Pages (Frontend Edge) + Dedicated VPS Coolify (API Backend) + Cloudflare R2 ($0 Egress)',
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
                    className={`px-3 py-1.5 text-xs font-mono border transition ${formData.preferensiHosting === host
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
              {lang === 'en' ? 'TIMELINE & SCOPE BOUNDARIES // 05' : 'TIMELINE & BATASAN // 05'}
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
                      className={`flex-1 py-1.5 text-xs font-mono font-bold border transition cursor-pointer ${formData.durasiHari === d
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
              {lang === 'en' ? 'PIC SIGN-OFF & CONTRACT // 06' : 'PENGESAHAN PIC & KONTRAK // 06'}
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
              <div className="flex items-center justify-between gap-2 mb-1.5 flex-wrap">
                <label className={labelClass}>
                  {currentTrack === 'retail'
                    ? (lang === 'en' ? '21. Software Factory OS License Allocation *' : '21. Alokasi Lisensi Digital Retail (Software Factory OS) *')
                    : (lang === 'en' ? '21. Dedicated Studio Investment Budget *' : '21. Alokasi Kisaran Budget Investasi Turnkey Studio *')}
                </label>
                <span className={`text-[10px] font-mono font-bold px-1.5 py-0.5 border ${currentTrack === 'retail'
                  ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-300 dark:border-emerald-800'
                  : 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border-blue-300 dark:border-blue-800'
                  }`}>
                  {currentTrack === 'retail'
                    ? (lang === 'en' ? 'Self-Service (In-House Team)' : 'Jalur Mandiri (Programmer Sendiri)')
                    : (lang === 'en' ? 'Dedicated Studio (Turnkey)' : 'Jalur Turnkey (Dedicated Studio)')}
                </span>
              </div>
              <p className="text-[11px] text-zinc-500 dark:text-zinc-400 mb-2 font-sans">
                {currentTrack === 'retail'
                  ? (lang === 'en' ? 'Choose your Software Factory OS architectural blueprint bundle for internal coding (zero development agency markup).' : 'Pilih paket lisensi bundel cetak biru arsitektur Fortune 500 untuk dikoding tim internal Anda.')
                  : (lang === 'en' ? 'Turnkey end-to-end development by Neriah Pro Senior Architect with bug warranty & managed sprint.' : 'Estimasi alokasi investasi pengerjaan penuh turnkey oleh tim Neriah Pro bergaransi SLA.')}
              </p>

              {/* Presets */}
              <div className="flex flex-wrap gap-2 mb-2">
                {(currentTrack === 'retail' ? [
                  lang === 'en' ? 'Spark: $0 / Free (Architectural Audit & Blueprint Access)' : 'Spark: Rp 0 (Akses Audit Arsitektur & Cetak Biru Gratis)',
                  lang === 'en' ? 'Lite: Rp 99,000 (PRD 26 Parameters, Strict ULID DDL & AI Directives)' : 'Lite: Rp 99.000 (PRD 26 Parameter, Schema DDL & AI Directives)',
                  lang === 'en' ? 'Pro: Rp 399,000 (Full Architecture Bundle: DDL, 7-Phase Prompts, Docker & Seeder)' : 'Pro: Rp 399.000 (Bundel Lengkap DDL, 7-Fase Prompt AI, Docker & Seeder)',
                  lang === 'en' ? 'Ultimate: Rp 1,490,000 (Full 10 Pillars Software Factory OS + Live MCP Kit)' : 'Ultimate: Rp 1.490.000 (Full 10 Pilar Software Factory OS + Live MCP Kit)',
                ] : [
                  lang === 'en' ? 'Advisory Tier: Rp 2,500,000 (PRD 26 Parameters, Strict DDL & Gantt WBS — 100% DP Credit)' : 'Advisory Tier: Rp 2.500.000 (PRD 26 Parameter, Strict DDL & Gantt WBS — Kredit DP 100%)',
                  lang === 'en' ? 'UMKM Digital Starter: Rp 3,750,000 (50% Subsidy — Web Engine & Admin Dashboard)' : 'UMKM Digital Starter: Rp 3.750.000 (Subsidi 50% — Engine Transaksi Web & Admin Dashboard)',
                  lang === 'en' ? 'Mitra Turnkey Project: Algorithmic Fair RAB (30% DP / 50% Staging / 20% Digital BAST)' : 'Mitra Proyek Turnkey: RAB Algoritmik Transparan (Termin 30% DP / 50% Staging / 20% BAST Digital)',
                  lang === 'en' ? 'Enterprise Monolith / Decoupled: Custom Quotation (Dedicated Senior Architect Squad + SLA)' : 'Enterprise Monolith / Decoupled: Custom Quotation (Dedicated Senior Architect Squad + Garansi SLA)',
                ]).map((tierOpt, idx) => (
                  <button
                    key={idx}
                    type="button"
                    onClick={() => updateField('kisaranBudget', tierOpt)}
                    className={`px-3 py-1.5 text-xs font-mono border transition ${formData.kisaranBudget === tierOpt
                      ? (currentTrack === 'retail' ? 'bg-emerald-500 text-black border-emerald-500 font-bold' : 'bg-blue-600 text-white border-blue-600 font-bold')
                      : 'bg-zinc-50 dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'
                      }`}
                  >
                    {tierOpt}
                  </button>
                ))}
              </div>
              <input
                type="text"
                value={formData.kisaranBudget}
                onChange={(e) => updateField('kisaranBudget', e.target.value)}
                placeholder={currentTrack === 'retail' ? (lang === 'en' ? 'Spark: $0 / Free (Architectural Audit & Blueprint Access)' : 'Spark: Rp 0 (Akses Audit Arsitektur & Cetak Biru Gratis)') : (lang === 'en' ? '$1,000 - $2,500 (Growth Monolith)' : 'Rp 15.000.000 - Rp 35.000.000 (Growth Monolith)')}
                className={inputClass}
              />
            </div>

            {/* Field 22: garansiSla */}
            <div id="field-garansiSla">
              <div className="flex items-center justify-between gap-2 mb-1.5 flex-wrap">
                <label className={labelClass}>
                  {currentTrack === 'retail'
                    ? (lang === 'en' ? '22. Deliverable Model & Technical Support *' : '22. Model Serah Terima Berkas & Dukungan Teknis Lisensi Retail *')
                    : (lang === 'en' ? '22. Post-Launch Bug Warranty, SLA & Git Repo Handover *' : '22. Skema Garansi Pascameluncur, SLA & Serah Terima Repo Git *')}
                </label>
                <span className={`text-[10px] font-mono font-bold px-1.5 py-0.5 border ${currentTrack === 'retail'
                  ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-300 dark:border-emerald-800'
                  : 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border-blue-300 dark:border-blue-800'
                  }`}>
                  {currentTrack === 'retail'
                    ? (lang === 'en' ? 'Self-Service Assets Handover' : 'Serah Terima Artefak Siap Pakai')
                    : (lang === 'en' ? 'Studio Bug Warranty & SLA' : 'Garansi Pengerjaan Kode Studio')}
                </span>
              </div>
              <p className="text-[11px] text-zinc-500 dark:text-zinc-400 mb-2 font-sans">
                {currentTrack === 'retail'
                  ? (lang === 'en' ? 'Because your internal team develops the code, Neriah Pro delivers verified ready-to-run architecture assets & lifetime portal access.' : 'Karena koding dikerjakan mandiri oleh programmer Anda, Neriah Pro menyerahkan seluruh artefak arsitektur terverifikasi & akses unduhan seumur hidup.')
                  : (lang === 'en' ? 'Managed quality assurance and bug fixing warranty directly by Neriah Pro engineering squad.' : 'Jaminan penanganan bug pascameluncur dan pemeliharaan kode langsung oleh Neriah Pro.')}
              </p>
              <div className="flex flex-wrap gap-2 mb-2">
                {(currentTrack === 'retail' ? [
                  lang === 'en' ? 'Self-Service Delivery: Instant PRD Fortune 500, Strict ULID DDL & Docker Starter Kit Handover' : 'Lisensi Mandiri: Penyerahan Langsung Dokumen PRD Fortune 500, Skema DDL Strict ULID & Docker Starter Kit',
                  lang === 'en' ? 'Permanent Access: Lifetime Downloads & Artifact Archives in Neriah Pro Client Portal' : 'Akses Seumur Hidup: Unduhan Permanen Berkas & Arsip Dokumen di Portal Klien Neriah Pro',
                  lang === 'en' ? 'Architectural Validation: Verification Support via AI Co-Pilot & Client Portal Gateway' : 'Dukungan Arsitektur: Konsultasi Verifikasi Spesifikasi via AI Co-Pilot & Portal Klien',
                ] : [
                  lang === 'en' ? '30 Days Post-Launch Bug Warranty Free of Charge + Full Private GitHub Repository Handover' : '30 Hari Garansi Bug Pascameluncur Bebas Biaya + Penyerahan Akses Penuh Private Repo GitHub',
                  lang === 'en' ? '60 Days Bug Warranty + GitHub Handover + Operational Staff Training Session' : '60 Hari Garansi Bug + Handover Repo Git + Sesi Training Staf Operasional',
                  lang === 'en' ? '30-Day Warranty + Ongoing Monthly Managed Maintenance SLA (Automated Backups & Security Patches)' : 'Garansi 30 Hari + Kontrak Managed Maintenance Bulanan (SLA Backup & Security Patch)',
                ]).map((sla, idx) => (
                  <button
                    key={idx}
                    type="button"
                    onClick={() => updateField('garansiSla', sla)}
                    className={`px-3 py-1.5 text-xs font-mono border transition ${formData.garansiSla === sla
                      ? (currentTrack === 'retail' ? 'bg-emerald-500 text-black border-emerald-500 font-bold' : 'bg-blue-600 text-white border-blue-600 font-bold')
                      : 'bg-zinc-50 dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'
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

            {/* Field 23: terminPembayaran */}
            <div id="field-terminPembayaran">
              <div className="flex items-center justify-between gap-2 mb-1.5 flex-wrap">
                <label className={labelClass}>
                  {currentTrack === 'retail'
                    ? (lang === 'en' ? '23. Retail License Transaction & Access Scheme *' : '23. Skema Transaksi & Akses Lisensi Retail *')
                    : (lang === 'en' ? '23. Payment Milestone Terms & Schedule *' : '23. Integrasi Skema Termin Pembayaran & Jadwal Milestone *')}
                </label>
                <span className={`text-[10px] font-mono font-bold px-1.5 py-0.5 border ${currentTrack === 'retail'
                  ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-300 dark:border-emerald-800'
                  : 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border-blue-300 dark:border-blue-800'
                  }`}>
                  {currentTrack === 'retail'
                    ? (lang === 'en' ? 'Instant Access (No 50/50 DP)' : 'Akses Instan (Tanpa Termin DP 50/50)')
                    : (lang === 'en' ? 'Milestone DP 50% Contract' : 'Kontrak Termin Bertahap DP 50%')}
                </span>
              </div>
              <p className="text-[11px] text-zinc-500 dark:text-zinc-400 mb-2 font-sans">
                {currentTrack === 'retail'
                  ? (lang === 'en' ? 'Self-service licenses are instant deliverables. No milestone lock, no agency contractual terms.' : 'Lisensi retail adalah produk digital instan mandiri. Bukan kontrak pengerjaan agensi ber-termin 50/50.')
                  : (lang === 'en' ? 'Turnkey studio development with milestones protected by Milestone DP 50% & UAT sign-off.' : 'Pengerjaan koding studio dengan termin bertahap yang dilindungi garansi serah terima UAT.')}
              </p>
              <div className="flex flex-wrap gap-2 mb-2">
                {(currentTrack === 'retail' ? [
                  lang === 'en' ? 'Spark Package (Free): $0 / Free Instant Architectural Audit & Blueprint Access' : 'Paket Spark (Gratis): Rp 0 / Akses Audit Arsitektur & Cetak Biru Gratis 100%',
                  lang === 'en' ? 'Instant 100% Full Payment: Midtrans Snap QRIS / Virtual Account (Immediate Download)' : 'Pembayaran Penuh 100% Instan: QRIS / Virtual Account Midtrans Snap (Langsung Unduh Berkas)',
                  lang === 'en' ? 'Direct Self-Service: Automated Real-Time Webhook Verification & License Key Issuance' : 'Self-Service Delivery: Verifikasi Otomatis Webhook Midtrans Real-Time & Lisensi Instan',
                ] : [
                  lang === 'en' ? 'Standard 50/50: 50% Kickoff DP & 50% Final Settlement Post-UAT Acceptance & Key Handover (via Midtrans Snap)' : 'Termin Standar 50/50: 50% DP Kickoff & 50% Pelunasan setelah lolos UAT & Serah Terima Kunci (via Midtrans Snap)',
                  lang === 'en' ? 'Milestone 30/40/30: 30% Kickoff DP, 40% MVP Demo Completed, 30% Final Go-Live Settlement' : 'Termin Milestone 30/40/30: 30% DP Kickoff, 40% Demo MVP Selesai, 30% Pelunasan Go-Live',
                  lang === 'en' ? '100% Full Upfront Payment (Priority Dedicated Squad Allocation)' : 'Termin Pelunasan Penuh 100% Upfront (Prioritas Utama Dedicated Squad)',
                ]).map((termin, idx) => (
                  <button
                    key={idx}
                    type="button"
                    onClick={() => updateField('terminPembayaran', termin)}
                    className={`px-3 py-1.5 text-xs font-mono border transition ${formData.terminPembayaran === termin
                      ? (currentTrack === 'retail' ? 'bg-emerald-500 text-black border-emerald-500 font-bold' : 'bg-blue-600 text-white border-blue-600 font-bold')
                      : 'bg-zinc-50 dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'
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
              <div
                id="blueprint-validation-banner"
                className={`p-5 rounded-none shadow-sm transition-all duration-300 border-2 ${
                  invalidFields.length > 0
                    ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-500 text-zinc-900 dark:text-zinc-100'
                    : 'bg-amber-50/90 dark:bg-zinc-900 border-amber-500 text-zinc-900 dark:text-zinc-100'
                }`}
              >
                <div className="flex items-start gap-3">
                  <div className={`p-1.5 text-white rounded-none shrink-0 mt-0.5 ${
                    invalidFields.length > 0 ? 'bg-rose-600' : 'bg-amber-600'
                  }`}>
                    {invalidFields.length > 0 ? (
                      <AlertCircle className="w-5 h-5" />
                    ) : (
                      <AlertTriangle className="w-5 h-5" />
                    )}
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center justify-between gap-2 flex-wrap mb-1.5">
                      <h4 className={`text-xs font-mono font-black uppercase tracking-wider ${
                        invalidFields.length > 0 ? 'text-rose-700 dark:text-rose-400' : 'text-amber-800 dark:text-amber-400'
                      }`}>
                        {invalidFields.length > 0
                          ? (lang === 'en'
                            ? `ACTION REQUIRED: ${invalidFields.length} MANDATORY PARAMETER(S) INCOMPLETE`
                            : `PERHATIAN: ${invalidFields.length} PARAMETER WAJIB BELUM DIISI`)
                          : (lang === 'en'
                            ? 'SYSTEM / SERVER COMMUNICATION NOTICE'
                            : 'KENDALA KONEKSI SISTEM / DATABASE SERVER')}
                      </h4>

                      {invalidFields.length > 0 && (
                        <button
                          type="button"
                          onClick={() => jumpToField(invalidFields[0])}
                          className="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-mono font-black bg-rose-600 hover:bg-rose-700 text-white border border-rose-600 rounded-none transition cursor-pointer"
                        >
                          <span>{lang === 'en' ? 'Scroll to First Field' : 'Gulir ke Field Pertama'}</span>
                          <ArrowUpRight className="w-3.5 h-3.5" />
                        </button>
                      )}
                    </div>

                    {invalidFields.length > 0 ? (
                      <>
                        <p className="text-xs text-rose-800 dark:text-rose-300 mb-3 leading-relaxed font-sans">
                          {lang === 'en'
                            ? 'The screen has auto-scrolled and incomplete fields have been marked with a red stroke highlight. In the left navigation index, incomplete sections and fields are marked in RED with [BELUM LENGKAP] and [WAJIB ISI]. Click any parameter button below to jump directly to it:'
                            : 'Layar telah digulir otomatis dan parameter yang belum diisi diberi efek garis tepi (stroke) merah berkedip. Pada Index Navigasi sebelah kiri, bagian yang belum lengkap ditandai warna MERAH dengan label [BELUM LENGKAP] dan [WAJIB ISI]. Klik salah satu tombol di bawah untuk melompat langsung ke field terkait:'}
                        </p>

                        <div className="flex flex-wrap gap-2 pt-2.5 border-t border-rose-200 dark:border-rose-900/60">
                          {invalidFields.map(key => {
                            const labelObj = FIELD_LABELS[key];
                            const labelText = labelObj ? (labelObj[lang] || labelObj.id) : key;
                            return (
                              <button
                                key={key}
                                type="button"
                                onClick={() => jumpToField(key)}
                                className="group inline-flex items-center gap-1.5 px-2.5 py-1 bg-white dark:bg-zinc-900 border border-rose-300 dark:border-rose-700 hover:border-rose-500 hover:bg-rose-600 hover:text-white dark:hover:bg-rose-600 text-rose-700 dark:text-rose-300 text-xs font-mono font-bold rounded-none transition shadow-xs cursor-pointer"
                              >
                                <span>{labelText}</span>
                                <ArrowUpRight className="w-3 h-3 opacity-60 group-hover:opacity-100 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
                              </button>
                            );
                          })}
                        </div>
                      </>
                    ) : (
                      <>
                        <p className="text-xs text-zinc-800 dark:text-zinc-200 mb-2 leading-relaxed font-sans">
                          {lang === 'en'
                            ? 'An unexpected error or timeout occurred while saving data to the server. All your filled blueprint data is safely preserved in memory and autosaved.'
                            : 'Terjadi kendala saat sistem memproses dan menyimpan data blueprint ke server. Seluruh data yang telah Anda isi tetap tersimpan aman di layar dan tidak hilang.'}
                        </p>

                        <div className="p-2.5 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-[11px] font-mono text-zinc-800 dark:text-zinc-200 mb-3">
                          <span className="font-bold text-rose-600 dark:text-rose-400 block mb-0.5">
                            {lang === 'en' ? 'Server Diagnostic Message:' : 'Pesan Diagnostik Server:'}
                          </span>
                          <span>{errorMessage}</span>
                        </div>

                        {/* Local host helper if it looks like connection issue */}
                        <div className="p-2.5 bg-amber-500/10 border border-amber-500/30 text-[11px] text-amber-900 dark:text-amber-200 font-sans mb-3 flex items-start gap-2">
                          <Lightbulb className="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                          <span>
                            {lang === 'en'
                              ? 'Tip: If running on a local development server (XAMPP/Laragon), ensure that the MySQL/MariaDB database service is running.'
                              : 'Petunjuk: Jika Anda sedang menguji di komputer lokal (XAMPP/Laragon), pastikan service database MySQL/MariaDB sedang aktif (Running). Database MariaDB saat ini telah diaktifkan kembali.'}
                          </span>
                        </div>

                        {/* Action buttons for retry */}
                        <div className="flex flex-wrap items-center gap-2 pt-2 border-t border-amber-200 dark:border-zinc-800">
                          <button
                            type="button"
                            onClick={handleFinalSubmit}
                            disabled={isSubmitting}
                            className="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-black font-mono font-black text-xs uppercase tracking-wider rounded-none transition cursor-pointer shadow-md disabled:opacity-50"
                          >
                            {isSubmitting ? (
                              <>
                                <Loader2 className="w-3.5 h-3.5 animate-spin" />
                                <span>{lang === 'en' ? 'Retrying...' : 'Mencoba Kirim Ulang...'}</span>
                              </>
                            ) : (
                              <>
                                <RotateCcw className="w-3.5 h-3.5" />
                                <span>{lang === 'en' ? '↻ Retry Submission Now' : '↻ Coba Kirim Ulang Sekarang'}</span>
                              </>
                            )}
                          </button>
                          <button
                            type="button"
                            onClick={() => setErrorMessage(null)}
                            className="inline-flex items-center gap-1 px-3 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-mono text-xs rounded-none transition cursor-pointer"
                          >
                            <span>{lang === 'en' ? 'Dismiss Notice' : 'Tutup Notifikasi'}</span>
                          </button>
                        </div>
                      </>
                    )}
                  </div>
                </div>
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

          {/* Submit Button & Track Finalization */}
          <div className="space-y-2">
            <button
              id="section-submit"
              type="submit"
              disabled={isSubmitting}
              className={`w-full font-black uppercase tracking-wider py-4 px-6 rounded-none text-center flex items-center justify-center gap-2 transition disabled:opacity-50 text-sm font-mono cursor-pointer shadow-lg ${currentTrack === 'studio'
                ? 'bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black border-2 border-emerald-500'
                : 'bg-emerald-600 hover:bg-emerald-500 text-black'
                }`}
            >
              {isSubmitting ? (
                <>
                  <Loader2 className="w-4 h-4 animate-spin" />
                  <span>{t.lockingBtn}</span>
                </>
              ) : currentTrack === 'studio' ? (
                <>
                  <Rocket className="w-4 h-4 text-emerald-500" />
                  <span>{lang === 'en' ? 'LOCK BLUEPRINT & BOOK STUDIO SPRINT SCHEDULE (50% DP MILESTONE) →' : 'KUNCI BLUEPRINT & KUNCI JADWAL SPRINT STUDIO (KONTRAK SLA & DP 50%) →'}</span>
                  <ArrowRight className="w-4 h-4" />
                </>
              ) : (
                <>
                  <ShoppingBag className="w-4 h-4 text-black" />
                  <span>{lang === 'en' ? 'LOCK BLUEPRINT & SELECT RETAIL LICENSE TIER →' : 'KUNCI BLUEPRINT & PILIH PAKET LISENSI RETAIL →'}</span>
                  <ArrowRight className="w-4 h-4" />
                </>
              )}
            </button>
            <div className="text-center font-mono text-[11px] text-zinc-500 dark:text-zinc-400">
              {currentTrack === 'studio' ? (
                <span>🛡️ {lang === 'en' ? 'Includes dedicated senior engineer slot allocation, Pest ApiContractTest, live cloud VPS deploy & SLA guarantee.' : 'Termasuk alokasi slot engineer terisolasi, pengujian Pest ApiContractTest, deploy live cloud VPS & garansi bug SLA.'}</span>
              ) : (
                <span>📦 {lang === 'en' ? 'Instant download: Ultimate PRD 26 params, PostgreSQL Strict ULID DDL, Docker config & AI directives (.cursorrules).' : 'Unduh instan: PRD 26 parameter Fortune 500, skema DDL PostgreSQL Strict ULID, Docker container & panduan AI agent.'}</span>
              )}
            </div>
          </div>
        </section>

    </>
  );
}
