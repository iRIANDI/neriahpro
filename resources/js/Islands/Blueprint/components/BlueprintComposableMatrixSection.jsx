import React from 'react';
import {
  Layers,
  Sparkles,
  Check,
  Loader2,
  AlertTriangle,
  ArrowRight,
  Shield,
  Activity,
  Server,
  Database,
  Smartphone,
  Cpu,
  Zap,
  Globe,
  FileCode,
  Lightbulb,
} from 'lucide-react';
import { PUBLIC_UX_PRESETS_V10, resolveDefaultPublicUxForSector } from '../constants/blueprintConstants';

export default function BlueprintComposableMatrixSection({
  matrixCatalog,
  setMatrixCatalog,
  formData,
  setFormData,
  triggerAutoSave,
  activeMatrixSectorObj,
  matrixMode,
  setMatrixMode,
  activeWorkbenchTab,
  setActiveWorkbenchTab,
  activeTopologyFilter,
  setActiveTopologyFilter,
  showStackMatrixTable,
  setShowStackMatrixTable,
  v7RuntimePreview,
  setV7RuntimePreview,
  selectMatrixSector,
  selectStackPreset,
  handleQuickDetectSectorFromIdea,
  doctorLoading,
  doctorReport,
  runPreFlightDoctorAudit,
  applyDoctorAutoFix,
  livePreFlightHints,
  showBrownfieldLab,
  setShowBrownfieldLab,
  brownfieldSqlText,
  setBrownfieldSqlText,
  brownfieldLoading,
  brownfieldReport,
  runBrownfieldAudit,
  applyBrownfieldRecommendation,
  panelClass,
  lang,
  t,
  initialData,
}) {
  return (
    <>
        {matrixCatalog.sectors.length > 0 && (() => {
          const fallbackSector = matrixCatalog.sectors[0] || null;
          const displaySectorObj = activeMatrixSectorObj || fallbackSector;
          const displaySectorName = displaySectorObj
            ? (displaySectorObj.name?.[lang] || displaySectorObj.name?.id || displaySectorObj.code)
            : (lang === 'en' ? 'Auto-Detected by AI' : 'Deteksi Otomatis AI');
          const displayUiArchetype = displaySectorObj?.ui_ux_archetypes?.primary_archetype || 'POS_SPLIT_TERMINAL';
          const displayDayTwo = displaySectorObj?.day_two_ops_contract || {};
          const thermalMode = displayDayTwo?.printable_and_thermal?.thermal_printer_spec?.mode || 'THERMAL_80MM_AND_58MM';
          const officialDocsCount = (displayDayTwo?.printable_and_thermal?.official_a4_documents || []).length || 3;
          const hasDoctorIssues = doctorReport
            ? doctorReport.status !== 'OPTIMAL_READY'
            : livePreFlightHints.hints.length > 0;
          const canOneClickFix = !!(doctorReport?.one_click_auto_fix?.has_fixes || doctorReport?.one_click_auto_fix?.available || livePreFlightHints.addPrims.length > 0 || livePreFlightHints.recommendedStack);
          const doctorSummaryText = doctorReport
            ? (typeof doctorReport.summary_banner === 'object'
              ? (doctorReport.summary_banner?.[lang] || doctorReport.summary_banner?.id || '')
              : doctorReport.summary_banner)
            : '';

          return (
            <section id="section-composable-matrix" className={panelClass}>
              {/* Header: Command Cockpit Layout with Full-Breathing Typography & Balanced Controls */}
              <div className="border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4 space-y-2.5">
                {/* Top Deck: Specification Pill & Studio IDE Quick Launch */}
                <div className="flex flex-wrap items-center justify-between gap-2">
                  <div className="flex items-center gap-2 flex-wrap">
                    <span className="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black uppercase tracking-wider">
                      PROJECT OS V12.0 ULTIMATE
                    </span>
                    <span className="text-[10px] sm:text-[11px] font-mono text-zinc-600 dark:text-zinc-400 font-bold">
                      12 SEKTOR • 25 MENU L1–L4 • 5 ENTERPRISE GLUE • DAG SUB-PARTS • SYMBOL LOCK
                    </span>
                  </div>

                  <a
                    href={initialData?.slug ? `/blueprint/studio/${initialData.slug}` : '/blueprint/studio'}
                    className="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-mono text-[10px] sm:text-[11px] font-black uppercase transition-all border border-emerald-500 shadow-xs cursor-pointer"
                    title={lang === 'en' ? 'Open Full-Screen 3-Pane Studio IDE' : 'Buka IDE Layar Penuh 3-Pane Studio'}
                  >
                    <span>🖥️</span>
                    <span>{lang === 'en' ? '3-Pane Studio IDE ↗' : 'Buka 3-Pane Studio ↗'}</span>
                  </a>
                </div>

                {/* Main Deck: Wide Title + Subtitle & In-Page Mode Segmented Switcher */}
                <div className="flex flex-col lg:flex-row lg:items-end justify-between gap-3 pt-1">
                  <div className="flex-1 min-w-0">
                    <h3 className="text-base sm:text-lg font-black uppercase tracking-tight text-zinc-950 dark:text-zinc-100 leading-snug">
                      {lang === 'en'
                        ? 'Zero-Assumption Architecture, L1–L4 Enterprise Menus & 5 Guarantee Engines'
                        : 'Kokpit Arsitektur Tanpa Asumsi, 25 Menu L1–L4 & 5 Mesin Penjamin Enterprise'}
                    </h3>
                    <p className="text-xs text-zinc-700 dark:text-zinc-300 mt-1 font-sans leading-relaxed max-w-4xl">
                      {lang === 'en'
                        ? 'Even a 5-word idea automatically blooms 25 L1–L4 Enterprise Sub-Menus + 5 Corporate Glue modules, compiled via Kahn\'s DAG Sub-Parts and SYMBOL_MANIFEST.json.'
                        : 'Bahkan ide singkat 5 kata otomatis dimekarkan menjadi 25 Sub-Menu Operasional L1–L4 + 5 Modul Enterprise Glue setara SAP/Odoo, dikompilasi dengan Kahn DAG Sub-Part & SYMBOL_MANIFEST.json.'}
                    </p>
                  </div>

                  {/* Mode Switcher Buttons */}
                  <div className="shrink-0 flex items-center">
                    <div className="inline-flex items-center p-0.5 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-none shadow-xs">
                      <button
                        type="button"
                        onClick={() => setMatrixMode('express')}
                        className={`px-3 py-1.5 font-mono text-[10px] font-black uppercase transition-all rounded-none cursor-pointer ${matrixMode === 'express'
                          ? 'bg-zinc-950 text-white dark:bg-emerald-400 dark:text-zinc-950 shadow-xs'
                          : 'text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white'
                          }`}
                      >
                        {lang === 'en' ? '⚡ Guided Auto Mode' : '⚡ Mode Ringkas AI'}
                      </button>
                      <button
                        type="button"
                        onClick={() => setMatrixMode('workbench')}
                        className={`px-3 py-1.5 font-mono text-[10px] font-black uppercase transition-all rounded-none cursor-pointer ${matrixMode === 'workbench'
                          ? 'bg-zinc-950 text-white dark:bg-emerald-400 dark:text-zinc-950 shadow-xs'
                          : 'text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white'
                          }`}
                      >
                        {lang === 'en' ? '🛠️ Architect Workbench (7 Tabs)' : '🛠️ Workbench Arsitek (7 Tab)'}
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              {/* =================================================================
                  ALWAYS-VISIBLE COMPACT SUMMARY TILES + LIVE PRE-FLIGHT DOCTOR BAR
                  ================================================================= */}
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5 mb-3.5">
                <button
                  type="button"
                  onClick={() => { setMatrixMode('workbench'); setActiveWorkbenchTab('sectors'); }}
                  className="text-left p-3.5 rounded-none bg-zinc-50 hover:bg-zinc-100/90 dark:bg-zinc-950 dark:hover:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 hover:border-zinc-500 dark:hover:border-zinc-600 transition-all cursor-pointer flex flex-col justify-between min-h-[110px]"
                >
                  <div>
                    <div className="flex items-center justify-between gap-1 font-mono text-[9px] font-black uppercase text-zinc-700 dark:text-zinc-400">
                      <span>01. {lang === 'en' ? 'SECTOR & INDUSTRIAL UI' : 'SEKTOR & UI INDUSTRI'}</span>
                      <span className="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700">
                        {formData.primarySector ? 'MANUAL' : 'AUTO AI'}
                      </span>
                    </div>
                    <div className="text-xs font-black text-zinc-950 dark:text-zinc-100 mt-1.5 truncate">{displaySectorName}</div>
                  </div>
                  <div className="font-mono text-[10px] font-bold text-emerald-800 dark:text-emerald-300 mt-2 truncate pt-1.5 border-t border-zinc-200 dark:border-zinc-800">
                    UI: {displayUiArchetype}
                  </div>
                </button>

                <button
                  type="button"
                  onClick={() => { setMatrixMode('workbench'); setActiveWorkbenchTab('primitives'); }}
                  className="text-left p-3.5 rounded-none bg-zinc-50 hover:bg-zinc-100/90 dark:bg-zinc-950 dark:hover:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 hover:border-zinc-500 dark:hover:border-zinc-600 transition-all cursor-pointer flex flex-col justify-between min-h-[110px]"
                >
                  <div>
                    <div className="flex items-center justify-between gap-1 font-mono text-[9px] font-black uppercase text-zinc-700 dark:text-zinc-400">
                      <span>02. {lang === 'en' ? 'CORE PRIMITIVES' : 'MODUL PRIMITIF'}</span>
                      <span className="tabular-nums font-black text-zinc-950 dark:text-zinc-100">{effectivePrimitiveCodes.length}/12</span>
                    </div>
                    <div className="text-xs font-black text-zinc-950 dark:text-zinc-100 mt-1.5 tabular-nums">
                      {effectivePrimitiveCodes.length} {lang === 'en' ? 'Modules Locked' : 'Modul Terkunci'}
                    </div>
                  </div>
                  <div className="font-mono text-[10px] text-zinc-700 dark:text-zinc-300 mt-2 truncate pt-1.5 border-t border-zinc-200 dark:border-zinc-800">
                    ULID • State Machine • FOR UPDATE
                  </div>
                </button>

                <button
                  type="button"
                  onClick={() => { setMatrixMode('workbench'); setActiveWorkbenchTab('stacks'); }}
                  className="text-left p-3.5 rounded-none bg-zinc-50 hover:bg-zinc-100/90 dark:bg-zinc-950 dark:hover:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 hover:border-zinc-500 dark:hover:border-zinc-600 transition-all cursor-pointer flex flex-col justify-between min-h-[110px]"
                >
                  <div>
                    <div className="flex items-center justify-between gap-1 font-mono text-[9px] font-black uppercase text-zinc-700 dark:text-zinc-400">
                      <span>03. {lang === 'en' ? 'TOPOLOGY & STACK' : 'TOPOLOGI & STACK'}</span>
                      <span className="px-1.5 py-0.5 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 font-bold">
                        {activeStackPresetObj?.short_code || 'M1'}
                      </span>
                    </div>
                    <div className="text-xs font-black text-zinc-950 dark:text-zinc-100 mt-1.5 truncate">
                      {activeStackPresetObj?.name?.[lang] || activeStackPresetObj?.name?.id || 'Laravel 13 + Filament v5 Monolith'}
                    </div>
                  </div>
                  <div className="font-mono text-[10px] text-zinc-700 dark:text-zinc-300 mt-2 truncate pt-1.5 border-t border-zinc-200 dark:border-zinc-800">
                    {activeStackPresetObj?.topology || 'MONOLITH'} • Type-Safe
                  </div>
                </button>

                <button
                  type="button"
                  onClick={() => { setMatrixMode('workbench'); setActiveWorkbenchTab('doctor'); }}
                  className="text-left p-3.5 rounded-none bg-zinc-50 hover:bg-zinc-100/90 dark:bg-zinc-950 dark:hover:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 hover:border-zinc-500 dark:hover:border-zinc-600 transition-all cursor-pointer flex flex-col justify-between min-h-[110px]"
                >
                  <div>
                    <div className="flex items-center justify-between gap-1 font-mono text-[9px] font-black uppercase text-zinc-700 dark:text-zinc-400">
                      <span>04. {lang === 'en' ? 'DAY-2 OPS & PRINT' : 'DAY-2 OPS & CETAK'}</span>
                      <span className="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700">
                        {lang === 'en' ? '8 LAYERS' : '8 LAYER'}
                      </span>
                    </div>
                    <div className="text-xs font-black text-zinc-950 dark:text-zinc-100 mt-1.5 truncate">
                      {thermalMode === 'A4_OFFICIAL_ONLY' ? 'Official A4 + SVG QR' : 'Thermal 58/80mm + A4 QR'}
                    </div>
                  </div>
                  <div className="font-mono text-[10px] text-zinc-700 dark:text-zinc-300 mt-2 truncate pt-1.5 border-t border-zinc-200 dark:border-zinc-800 tabular-nums">
                    {officialDocsCount} {lang === 'en' ? 'Official Docs • *_snapshot' : 'Dokumen Resmi • *_snapshot'}
                  </div>
                </button>

                <button
                  type="button"
                  onClick={() => { setMatrixMode('workbench'); setActiveWorkbenchTab('menus_glue'); }}
                  className="text-left p-3.5 rounded-none bg-emerald-50/70 hover:bg-emerald-100/70 dark:bg-emerald-950/35 dark:hover:bg-emerald-950/60 border border-emerald-400 dark:border-emerald-700 hover:border-emerald-600 transition-all cursor-pointer flex flex-col justify-between min-h-[110px]"
                >
                  <div>
                    <div className="flex items-center justify-between gap-1 font-mono text-[9px] font-black uppercase text-emerald-950 dark:text-emerald-200">
                      <span>05. {lang === 'en' ? 'L1–L4 MENUS & GLUE' : 'MENU L1–L4 & GLUE'}</span>
                      <span className="px-1.5 py-0.5 bg-zinc-950 text-white dark:bg-emerald-400 dark:text-zinc-950 tabular-nums font-black">
                        {effectiveSelectedMenus.length}/25
                      </span>
                    </div>
                    <div className="text-xs font-black text-zinc-950 dark:text-zinc-100 mt-1.5 truncate tabular-nums">
                      {effectiveSelectedMenus.length} {lang === 'en' ? 'v1.0 Sub-Menus + 5 Glue' : 'Sub-Menu v1.0 + 5 Glue'}
                    </div>
                  </div>
                  <div className="font-mono text-[10px] font-bold text-emerald-900 dark:text-emerald-300 mt-2 truncate pt-1.5 border-t border-emerald-300 dark:border-emerald-800">
                    SYMBOL LOCK • Kahn DAG
                  </div>
                </button>
              </div>

              {/* Pre-Flight Blueprint Doctor Instant Readiness Strip */}
              <div className={`p-3 rounded-xs border flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 ${hasDoctorIssues
                ? 'bg-amber-50 border-amber-400 text-amber-950 dark:bg-amber-950/50 dark:border-amber-700 dark:text-amber-100'
                : 'bg-emerald-50/90 border-emerald-400 text-emerald-950 dark:bg-emerald-950/40 dark:border-emerald-700 dark:text-emerald-100'
                }`}>
                <div className="min-w-0">
                  <div className="flex flex-wrap items-center gap-2 font-mono text-[10px] font-black uppercase">
                    <span className={`px-1.5 py-0.5 rounded-xs ${hasDoctorIssues
                      ? 'bg-amber-900 text-white dark:bg-amber-400 dark:text-zinc-950'
                      : 'bg-emerald-700 text-white dark:bg-emerald-400 dark:text-zinc-950'
                      }`}>
                      {hasDoctorIssues ? '🩺 PERLU PENYELARASAN MODUL' : '🩺 BLUEPRINT DOCTOR: READY'}
                    </span>
                    <span className="tabular-nums">
                      {doctorReport
                        ? `SKOR PRE-FLIGHT: ${doctorReport.readiness_score}/100`
                        : (hasDoctorIssues ? `${livePreFlightHints.hints.length} CELAH LINTAS-MODUL TERDETEKSI` : 'ZERO CROSS-MODULE CONFLICT')}
                    </span>
                  </div>
                  <div className="text-xs font-bold mt-1">
                    {doctorReport
                      ? doctorSummaryText
                      : (livePreFlightHints.hints[0]?.title || (lang === 'en'
                        ? 'Thermal/A4 print contracts, immutable *_snapshot columns, staged CSV importer & 4-lane Redis queues are automatically compiled.'
                        : 'Kontrak cetak Thermal/A4, kolom *_snapshot anti-rusak historis, Staged CSV Importer & 4 jalur Queue otomatis dikompilasi.'))}
                  </div>
                </div>

                <div className="shrink-0 flex flex-wrap items-center gap-1.5 font-mono text-[10px]">
                  {canOneClickFix && (
                    <button
                      type="button"
                      onClick={applyDoctorAutoFix}
                      className="px-3 py-1.5 rounded-xs bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-emerald-400 dark:hover:bg-emerald-300 dark:text-zinc-950 font-black uppercase cursor-pointer"
                    >
                      {lang === 'en' ? '✨ 1-Click Auto-Fix' : '✨ Perbaiki Otomatis (1-Klik)'}
                    </button>
                  )}
                  <button
                    type="button"
                    disabled={doctorLoading}
                    onClick={() => {
                      runPreFlightDoctor();
                      setMatrixMode('workbench');
                      setActiveWorkbenchTab('doctor');
                    }}
                    className="px-2.5 py-1.5 rounded-xs bg-white hover:bg-zinc-100 text-zinc-950 border border-zinc-400 dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 font-black uppercase cursor-pointer"
                  >
                    {doctorLoading
                      ? (lang === 'en' ? 'Diagnosing...' : 'Mendiagnosa...')
                      : (lang === 'en' ? '🩺 Run Deep Doctor' : '🩺 Audit Blueprint Doctor')}
                  </button>
                  {matrixMode === 'express' && (
                    <button
                      type="button"
                      onClick={() => { setMatrixMode('workbench'); setActiveWorkbenchTab('brownfield'); setShowBrownfieldLab(true); }}
                      className="px-2.5 py-1.5 rounded-xs bg-white hover:bg-zinc-100 text-zinc-950 border border-zinc-400 dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 font-bold uppercase cursor-pointer"
                    >
                      {lang === 'en' ? '📥 Import Legacy SQL' : '📥 Impor SQL Lama'}
                    </button>
                  )}
                </div>
              </div>

              {/* v8.0 Cross-Stage Live Causality Ribbon (100% Architectural Freeze — Every Stage Mutates Downstream Stages) */}
              <div className="mt-2.5 p-3 rounded-xs bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 flex flex-col xl:flex-row xl:items-center justify-between gap-2.5">
                <div className="flex flex-wrap items-center gap-1.5 font-mono text-[10px]">
                  <span className="px-2 py-0.5 bg-zinc-950 text-white dark:bg-emerald-400 dark:text-zinc-950 font-black uppercase">
                    v8.0 • 15 PILAR FREEZE + 36 VISUAL UI
                  </span>
                  <span className="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-black tabular-nums">
                    {lang === 'en'
                      ? `UI: ${v7RuntimePreview?.visual_frontend_ui_contract?.selected_variant_code || formData.selectedUiVariant || 'AUTO'} • Locale: ${String(formData.outputLocale || 'id').toUpperCase()}`
                      : `UI: ${v7RuntimePreview?.visual_frontend_ui_contract?.selected_variant_code || formData.selectedUiVariant || 'AUTO'} • Regulasi: ${String(formData.outputLocale || 'id').toUpperCase()}`}
                  </span>
                  <span className="px-2 py-0.5 bg-white text-zinc-950 border border-zinc-300 dark:bg-zinc-900 dark:text-zinc-200 dark:border-zinc-700 font-bold tabular-nums">
                    {lang === 'en'
                      ? `0% N+1 + PG16 Partition • DAG: ${v7RuntimePreview?.summary?.total_sub_parts ?? 12} Parts • Role SOP: ${v7RuntimePreview?.client_sop_and_onboarding?.role_count ?? 4}`
                      : `0% N+1 + Partisi PG16 • DAG: ${v7RuntimePreview?.summary?.total_sub_parts ?? 12} Part • SOP Role: ${v7RuntimePreview?.client_sop_and_onboarding?.role_count ?? 4}`}
                  </span>
                  {v7RuntimePreview?.live_commercial_sync && (
                    <span className="px-2 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700 font-black tabular-nums">
                      {lang === 'en' ? 'Mitra Turnkey Est:' : 'Est. Mitra Turnkey:'} {v7RuntimePreview.live_commercial_sync.formatted_quotation_idr} • SHA256:{v7RuntimePreview.live_commercial_sync.scope_hash_short}
                    </span>
                  )}
                </div>
                <button
                  type="button"
                  onClick={() => { setMatrixMode('workbench'); setActiveWorkbenchTab('runtime_v7'); }}
                  className="shrink-0 px-2.5 py-1.5 rounded-xs bg-emerald-600 hover:bg-emerald-500 text-zinc-950 border border-emerald-700 font-mono text-[10px] font-black uppercase cursor-pointer"
                >
                  {lang === 'en' ? '⚡ Inspect 5 Runtime & 15 Pillars' : '⚡ Lihat 5 Runtime & 15 Pilar'}
                </button>
              </div>

              {/* v8.0 DETERMINISTIC VISUAL FRONTEND UI/UX SELECTOR (12 SECTORS x 3 VARIANTS = 36 BLUEPRINTS) + TIER-1 JURISDICTION */}
              {(() => {
                const resolvedSectorKey = activeMatrixSectorObj?.code
                  || formData.primarySector
                  || Object.keys(matrixCatalog.sector_visual_ui_catalog || {})[0]
                  || 'SECTOR_01_COMMERCE_OMNICHANNEL';
                const visualVariants = matrixCatalog.sector_visual_ui_catalog?.[resolvedSectorKey]
                  || v7RuntimePreview?.visual_frontend_ui_contract?.available_variants
                  || [];
                const activeVariant = visualVariants.find((v) => v.code === formData.selectedUiVariant)
                  || visualVariants[0]
                  || null;
                const shells = Object.values(matrixCatalog.navigation_shell_options || {});
                const jurisdictionOptions = [
                  { code: 'id', label: '🇮🇩 ID — Indonesia (UU PDP • PSAK 109 / ISAK 35 • PPh 21 TER)' },
                  { code: 'en', label: '🇺🇸 EN — International (SOC2 • FASB ASC 958 • HIPAA)' },
                  { code: 'ar', label: '🇸🇦 AR — العربية MENA [dir="rtl"] (ZATCA Fatoora • AAOIFI)' },
                  { code: 'ja', label: '🇯🇵 JA — 日本語 Japan (APPI • Qualified Invoice JCT)' },
                  { code: 'zh-CN', label: '🇨🇳 ZH — 简体中文 APAC (PIPL • Golden Tax Fapiao)' },
                  { code: 'de', label: '🇩🇪 DE — Deutsch EU (GDPR • GoBD • XRechnung)' },
                  { code: 'es', label: '🇪🇸 ES — Español LATAM/EU (GDPR • CFDI / FacturaE)' },
                ];

                if (visualVariants.length === 0) return null;

                return (
                  <div className="mt-2.5 p-3.5 rounded-xs bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-800 space-y-3">
                    <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-2 pb-2.5 border-b border-zinc-200 dark:border-zinc-800">
                      <div>
                        <span className="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300">
                          {lang === 'en'
                            ? '🎨 VISUAL FRONTEND UI/UX BLUEPRINT PICKER (LOCKED INTO PHASE 5 & SHA-256 SCOPE)'
                            : '🎨 PEMILIH VISUAL FRONTEND UI/UX PROYEK (DIKUNCI KE PROMPT FASE 5 & SCOPE SHA-256)'}
                        </span>
                        <p className="text-[11px] text-zinc-700 dark:text-zinc-300 mt-0.5">
                          {lang === 'en'
                            ? 'Pick your exact multi-pane operational frontend layout so AI coders and Studio engineers build the exact screen topology you want.'
                            : 'Pilih tata letak visual layar depan proyek Anda secara pasti (Opsi A / B / C) agar hasil koding AI maupun tim Studio 100% sesuai ekspektasi tanpa tebak-tebakan.'}
                        </p>
                      </div>
                      <div className="flex flex-wrap items-center gap-2">
                        <select
                          value={formData.outputLocale || 'id'}
                          onChange={(e) => {
                            const nextLoc = e.target.value;
                            setFormData((prev) => {
                              const next = { ...prev, outputLocale: nextLoc };
                              triggerAutoSave(next);
                              return next;
                            });
                          }}
                          className="px-2.5 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 font-mono text-[10px] font-bold rounded-none"
                        >
                          {jurisdictionOptions.map((jo) => (
                            <option key={jo.code} value={jo.code}>{jo.label}</option>
                          ))}
                        </select>
                        {shells.length > 0 && (
                          <select
                            value={formData.navigationShell || activeVariant?.default_shell || 'SIDEBAR_ERP_DENSE'}
                            onChange={(e) => {
                              const nextShell = e.target.value;
                              setFormData((prev) => {
                                const next = { ...prev, navigationShell: nextShell };
                                triggerAutoSave(next);
                                return next;
                              });
                            }}
                            className="px-2.5 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 font-mono text-[10px] font-bold rounded-none"
                          >
                            {shells.map((sh) => (
                              <option key={sh.code} value={sh.code}>
                                {lang === 'en' ? sh.name?.en : sh.name?.id}
                              </option>
                            ))}
                          </select>
                        )}
                      </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-2.5">
                      {visualVariants.map((vr) => {
                        const isPicked = activeVariant?.code === vr.code;
                        const vrTitle = lang === 'en' ? (vr.name?.en || vr.code) : (vr.name?.id || vr.code);
                        const vrDesc = lang === 'en' ? (vr.summary?.en || '') : (vr.summary?.id || '');
                        return (
                          <button
                            key={vr.code}
                            type="button"
                            onClick={() => {
                              setFormData((prev) => {
                                const designNote = `[UI TERKUNCI: ${vr.code} | ${vr.default_shell}] ${vrTitle}`;
                                const next = {
                                  ...prev,
                                  selectedUiVariant: vr.code,
                                  navigationShell: vr.default_shell || prev.navigationShell,
                                  referensiDesain: prev.referensiDesain && !prev.referensiDesain.startsWith('[UI TERKUNCI:')
                                    ? prev.referensiDesain
                                    : designNote,
                                };
                                triggerAutoSave(next);
                                return next;
                              });
                            }}
                            className={`p-3 text-left border transition rounded-none cursor-pointer flex flex-col justify-between ${isPicked
                              ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-950/90 dark:text-white dark:border-emerald-400'
                              : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-900 border-zinc-300 dark:bg-zinc-950 dark:hover:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-800'
                              }`}
                          >
                            <div>
                              <div className="flex items-center justify-between gap-1.5 font-mono text-[9px] font-black uppercase">
                                <span className={isPicked ? 'text-emerald-400' : 'text-emerald-900 dark:text-emerald-300'}>
                                  {vr.badge}
                                </span>
                                <span>{isPicked ? '✓ LOCKED' : vr.default_shell}</span>
                              </div>
                              <div className="text-xs font-black mt-1 leading-snug">{vrTitle}</div>
                              <p className={`text-[11px] mt-1 leading-relaxed ${isPicked ? 'text-zinc-300' : 'text-zinc-600 dark:text-zinc-400'}`}>
                                {vrDesc}
                              </p>
                            </div>
                          </button>
                        );
                      })}
                    </div>

                    {activeVariant?.panes && activeVariant.panes.length > 0 && (() => {
                      const effectiveShell = formData.navigationShell || activeVariant?.default_shell || 'SIDEBAR_ERP_DENSE';
                      const isRtlFrame = (formData.outputLocale || 'id') === 'ar';
                      const taskMatrix = v7RuntimePreview?.world_class_task_matrix || formData.worldClassTaskMatrix || null;
                      const sectorAuditPreset = v7RuntimePreview?.cryptographic_audit_explorer_contract?.active_sector_preset
                        || matrixCatalog.sector_audit_presets?.[resolvedSectorKey]
                        || null;
                      const spanToColClass = (spanNum) => {
                        const s = Number(spanNum) || 6;
                        if (s <= 2) return 'md:col-span-2';
                        if (s === 3) return 'md:col-span-3';
                        if (s === 4) return 'md:col-span-4';
                        if (s === 5) return 'md:col-span-5';
                        if (s === 7) return 'md:col-span-7';
                        if (s === 8) return 'md:col-span-8';
                        if (s >= 9) return 'md:col-span-9';
                        return 'md:col-span-6';
                      };

                      return (
                        <div className="space-y-3">
                          {/* VISUAL MINIATURE APPLICATION SCREEN MOCKUP (12 SECTORS x 3 VARIANTS x 5 NAVIGATION SHELLS) */}
                          <div
                            dir={isRtlFrame ? 'rtl' : 'ltr'}
                            className="border-2 border-zinc-900 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-950 rounded-none overflow-hidden"
                          >
                            {/* Miniature Browser / OS Window Bar */}
                            <div className="px-3 py-1.5 bg-zinc-950 text-white border-b border-zinc-800 flex flex-wrap items-center justify-between gap-2 font-mono text-[10px]">
                              <div className="flex items-center gap-2">
                                <span className="inline-flex items-center gap-1">
                                  <span className="w-2 h-2 bg-rose-500 inline-block"></span>
                                  <span className="w-2 h-2 bg-amber-400 inline-block"></span>
                                  <span className="w-2 h-2 bg-emerald-400 inline-block"></span>
                                </span>
                                <span className="px-1.5 py-0.5 bg-emerald-500 text-zinc-950 font-black uppercase">
                                  {lang === 'en' ? 'LIVE VISUAL UI WIREFRAME' : 'PRATINJAU VISUAL ANATOMI LAYAR'}
                                </span>
                                <span className="font-bold text-zinc-200 truncate">
                                  {activeVariant.code} • {effectiveShell} • {isRtlFrame ? 'RTL' : 'LTR'}
                                </span>
                              </div>
                              <a
                                href={`/blueprint/studio?mode=${currentTrack === 'studio' ? 'mitra' : 'retail'}&sector=${resolvedSectorKey}&stack=${formData.stackPreset || 'STACK_M1_LARAVEL_MONOLITH'}&ui_variant=${activeVariant.code}&shell=${effectiveShell}&tab=codesign_room`}
                                className="px-2 py-0.5 bg-amber-400 hover:bg-amber-300 text-zinc-950 font-black uppercase text-[9px] rounded-none"
                              >
                                🤝 {lang === 'en' ? 'Co-Design & Sign-Off Room →' : 'Ruang Co-Design & Sign-Off →'}
                              </a>
                            </div>

                            {/* Shell-Specific Top Header Bar (Kiosk / Topbar Executive Deck) */}
                            {(effectiveShell === 'FULLSCREEN_OPERATIONAL_KIOSK' || effectiveShell === 'TOPBAR_EXECUTIVE_DECK') && (
                              <div className="px-3 py-1.5 bg-white dark:bg-zinc-900 border-b border-zinc-300 dark:border-zinc-800 flex flex-wrap items-center justify-between gap-2 font-mono text-[9px]">
                                <div className="flex flex-wrap items-center gap-1.5">
                                  <span className="px-1.5 py-0.5 bg-zinc-950 text-white dark:bg-emerald-500 dark:text-zinc-950 font-black">
                                    {effectiveShell === 'FULLSCREEN_OPERATIONAL_KIOSK' ? '[KIOSK ZERO-DISTRACTION BAR]' : '[EXECUTIVE KPI DECK]'}
                                  </span>
                                  {(effectiveSelectedMenus || []).slice(0, 4).map((mCode) => (
                                    <span key={mCode} className="px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 font-bold">
                                      {mCode}
                                    </span>
                                  ))}
                                </div>
                                <span className="text-emerald-900 dark:text-emerald-300 font-black tabular-nums">
                                  WS ZERO-POLL • SUB-10MS O(1)
                                </span>
                              </div>
                            )}

                            {/* Main Shell Frame Body */}
                            <div className="grid grid-cols-12">
                              {/* Left Collapsible Sidebar Shell */}
                              {effectiveShell === 'SIDEBAR_ERP_DENSE' && (
                                <div className="col-span-12 md:col-span-2 bg-zinc-900 text-zinc-100 border-b md:border-b-0 md:border-r border-zinc-800 p-2 font-mono text-[9px] space-y-1">
                                  <div className="text-emerald-400 font-black uppercase pb-1 border-b border-zinc-800">
                                    L1–L4 NAV ({effectiveSelectedMenus.length}/25)
                                  </div>
                                  {['L1. Master & Config', 'L2. Core Transaksi', 'L3. Approval & Ops', 'L4. Audit & Report'].map((navLbl, nIdx) => (
                                    <div
                                      key={nIdx}
                                      className={`px-1.5 py-1 border ${nIdx === 1 ? 'bg-emerald-500 text-zinc-950 border-emerald-400 font-black' : 'bg-zinc-950/80 text-zinc-300 border-zinc-800 font-bold'}`}
                                    >
                                      {navLbl}
                                    </div>
                                  ))}
                                </div>
                              )}

                              {/* Proportional 12-Column Operational Canvas */}
                              <div className={`col-span-12 ${effectiveShell === 'SIDEBAR_ERP_DENSE' ? 'md:col-span-10' : effectiveShell === 'SLIDEOVER_WORKBENCH' ? 'md:col-span-9' : 'md:col-span-12'} p-2.5 grid grid-cols-12 gap-2`}>
                                {activeVariant.panes.map((pn, pIdx) => (
                                  <div
                                    key={pIdx}
                                    className={`col-span-12 ${spanToColClass(pn.span)} p-2.5 bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-800 flex flex-col justify-between font-mono text-[10px]`}
                                  >
                                    <div>
                                      <div className="flex items-center justify-between gap-1 pb-1.5 mb-2 border-b border-zinc-200 dark:border-zinc-800">
                                        <span className="font-black text-emerald-950 dark:text-emerald-300 uppercase truncate">
                                          [{pn.label}]
                                        </span>
                                        <span className="px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 text-[9px] font-black tabular-nums shrink-0">
                                          {pn.span}/12 COL
                                        </span>
                                      </div>
                                      {/* Visual Wireframe Skeleton Header Bars */}
                                      <div className="grid grid-cols-3 gap-1 mb-2">
                                        <div className="h-4 bg-emerald-100 dark:bg-emerald-950/70 border border-emerald-400 dark:border-emerald-800 px-1 flex items-center text-[8px] font-black text-emerald-950 dark:text-emerald-200">
                                          KPI #{pIdx + 1}
                                        </div>
                                        <div className="h-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-1 flex items-center text-[8px] font-bold text-zinc-800 dark:text-zinc-300">
                                          ULID O(1)
                                        </div>
                                        <div className="h-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-1 flex items-center text-[8px] font-bold text-zinc-800 dark:text-zinc-300">
                                          SEARCH [X]
                                        </div>
                                      </div>
                                      <div className="flex flex-wrap gap-1">
                                        {(pn.widgets || []).map((wg, wIdx) => (
                                          <span
                                            key={wIdx}
                                            className="px-1.5 py-0.5 bg-zinc-50 dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 font-bold text-[9px]"
                                          >
                                            ▣ {wg}
                                          </span>
                                        ))}
                                      </div>
                                    </div>
                                    <div className="mt-2 pt-1.5 border-t border-dashed border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-[9px] text-zinc-600 dark:text-zinc-400">
                                      <span>Sticky Header • Freeze L/R</span>
                                      <span className="font-bold text-zinc-900 dark:text-zinc-200">Zero-Capsule</span>
                                    </div>
                                  </div>
                                ))}
                              </div>

                              {/* Slide-Over Workbench Inspector Drawer */}
                              {effectiveShell === 'SLIDEOVER_WORKBENCH' && (
                                <div className="col-span-12 md:col-span-3 bg-zinc-50 dark:bg-zinc-900 border-t md:border-t-0 md:border-l-2 border-emerald-500 p-2.5 font-mono text-[9px] space-y-1.5">
                                  <div className="font-black uppercase text-emerald-950 dark:text-emerald-300">
                                    [SLIDE-OVER INSPECTOR DRAWER]
                                  </div>
                                  <div className="p-1.5 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 font-bold">
                                    ✓ Konteks Detail Tanpa Pindah Halaman
                                  </div>
                                  <div className="p-1.5 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 font-bold">
                                    ✓ Sign-Off &amp; SHA-256 Audit Log
                                  </div>
                                </div>
                              )}
                            </div>

                            {/* Mobile / PWA Bottom Thumb Navigation Dock */}
                            {effectiveShell === 'MOBILE_BOTTOM_THUMB_BAR' && (
                              <div className="px-3 py-1.5 bg-zinc-950 text-white border-t border-zinc-800 grid grid-cols-4 gap-1.5 font-mono text-[9px] text-center">
                                {['01. HOME', '02. SCAN QR', '03. TRANSAKSI', '04. AKUN RBAC'].map((tb, tIdx) => (
                                  <div
                                    key={tIdx}
                                    className={`py-1 border ${tIdx === 0 ? 'bg-emerald-500 text-zinc-950 border-emerald-400 font-black' : 'bg-zinc-900 text-zinc-200 border-zinc-700 font-bold'}`}
                                  >
                                    {tb}
                                  </div>
                                ))}
                              </div>
                            )}
                          </div>

                          {/* v10.0 ZONE B — PUBLIC FRONT-STAGE UX VISUAL SELECTOR (UX_F1..UX_F6) + 5 IRON ANTI-SLOP ANIMATION LAWS */}
                          {(() => {
                            const activePubUxCode = formData.publicUxPreset || resolveDefaultPublicUxForSector(resolvedSectorKey);
                            return (
                              <div className="p-3.5 bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 rounded-none space-y-2.5">
                                <div className="flex flex-wrap items-center justify-between gap-2">
                                  <div>
                                    <div className="font-mono text-[10px] font-black uppercase text-emerald-950 dark:text-emerald-300">
                                      ✨ {lang === 'en'
                                        ? 'ZONE B — PUBLIC FRONT-STAGE UX PRESET (6 ADVANCED ARCHETYPES • ZERO-JANK 60FPS)'
                                        : 'ZONE B — PILIHAN UX HALAMAN PUBLIK / DEPAN (6 PRESET INTERAKTIF • ZERO-JANK 60FPS)'}
                                    </div>
                                    <p className="text-[11px] text-zinc-700 dark:text-zinc-300 mt-0.5">
                                      {lang === 'en'
                                        ? 'Dual-Zone Architecture strictly separates Zone A Back-Stage (zero heavy animation) from Zone B Public Front-Stage (GPU translate3d, CLS = 0.00, 100% local @vite).'
                                        : 'Arsitektur Dual-Zone memisahkan tegas Zone A Operasional Staf (tanpa animasi berat) dari Zone B Halaman Publik Klien (GPU translate3d, CLS = 0.00, 100% lokal @vite).'}
                                    </p>
                                  </div>
                                  <span className="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/90 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[9px] font-black uppercase rounded-none">
                                    LOCKED: {activePubUxCode}
                                  </span>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-3 gap-2">
                                  {PUBLIC_UX_PRESETS_V10.map((ux) => {
                                    const isPubPicked = activePubUxCode === ux.code;
                                    const uxTitle = lang === 'en' ? ux.name.en : ux.name.id;
                                    const uxDesc = lang === 'en' ? ux.summary.en : ux.summary.id;
                                    return (
                                      <button
                                        key={ux.code}
                                        type="button"
                                        onClick={() => {
                                          setFormData((prev) => {
                                            const next = { ...prev, publicUxPreset: ux.code };
                                            triggerAutoSave(next);
                                            return next;
                                          });
                                        }}
                                        className={`p-2.5 text-left border rounded-none transition cursor-pointer flex flex-col justify-between ${isPubPicked
                                          ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-950/90 dark:text-white dark:border-emerald-400'
                                          : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-900 border-zinc-300 dark:bg-zinc-950 dark:hover:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-800'
                                          }`}
                                      >
                                        <div>
                                          <div className="flex items-center justify-between gap-1 font-mono text-[9px] font-black uppercase">
                                            <span className={isPubPicked ? 'text-emerald-400' : 'text-emerald-900 dark:text-emerald-300'}>
                                              {ux.badge}
                                            </span>
                                            <span>{isPubPicked ? '✓ ACTIVE' : `+${ux.hours}h`}</span>
                                          </div>
                                          <div className="text-xs font-black mt-1 leading-snug">{uxTitle}</div>
                                          <p className={`text-[10px] mt-1 leading-relaxed ${isPubPicked ? 'text-zinc-300' : 'text-zinc-600 dark:text-zinc-400'}`}>
                                            {uxDesc}
                                          </p>
                                        </div>
                                        <div className={`mt-2 pt-1.5 border-t font-mono text-[9px] ${isPubPicked ? 'border-zinc-800 text-emerald-300' : 'border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-400'}`}>
                                          ⚡ {ux.engine}
                                        </div>
                                      </button>
                                    );
                                  })}
                                </div>

                                <div className="flex flex-wrap gap-1.5 pt-1 font-mono text-[9px]">
                                  {[
                                    'LAW_01: 100% Local @vite (0% CDN)',
                                    'LAW_02: GPU translate3d/opacity Only',
                                    'LAW_03: CLS = 0.00 Aspect Lock',
                                    'LAW_04: prefers-reduced-motion Guard',
                                    'LAW_05: IntersectionObserver FCP <0.8s',
                                  ].map((law) => (
                                    <span key={law} className="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-800 font-bold rounded-none">
                                      ✓ {law}
                                    </span>
                                  ))}
                                </div>
                              </div>
                            );
                          })()}

                          {/* v9.0 WORLD-CLASS 5-SPRINT ENGINEERING TASK BACKLOG & CRYPTO AUDIT PRESET */}
                          {taskMatrix?.sprints && taskMatrix.sprints.length > 0 && (() => {
                            const activePresetObj = taskMatrix.sector_audit_preset || sectorAuditPreset || null;
                            const presetLabel = activePresetObj
                              ? (activePresetObj.title?.[lang] || activePresetObj.localized_title || activePresetObj.badge || resolvedSectorKey)
                              : null;
                            const totalTasksCount = Number(taskMatrix.total_world_class_tasks || taskMatrix.total_tasks_count || 13);

                            return (
                              <div className="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 rounded-none space-y-2">
                                <div className="flex flex-wrap items-center justify-between gap-2">
                                  <div className="font-mono text-[10px] font-black uppercase text-zinc-950 dark:text-zinc-100">
                                    🚀 {lang === 'en'
                                      ? `WORLD-CLASS ${taskMatrix.total_sprints || 5}-SPRINT EXECUTION BACKLOG (${totalTasksCount} DETERMINISTIC TASKS • LOCKED TO ${activeVariant.code})`
                                      : `MATRIKS ${taskMatrix.total_sprints || 5}-SPRINT EKSEKUSI KELAS DUNIA (${totalTasksCount} TASK DETERMINISTIK • TERKUNCI KE ${activeVariant.code})`}
                                  </div>
                                  {presetLabel && (
                                    <span className="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/90 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[9px] font-black truncate max-w-md" title={presetLabel}>
                                      AUDIT PRESET: {activePresetObj?.badge || presetLabel}
                                    </span>
                                  )}
                                </div>
                                <div className="grid grid-cols-1 md:grid-cols-5 gap-1.5 font-mono text-[9px]">
                                  {taskMatrix.sprints.map((sp) => {
                                    const spTitle = typeof sp.title === 'object' ? (sp.title?.[lang] || sp.title?.id || sp.code) : (sp.title || sp.code);
                                    return (
                                      <div key={sp.code || sp.sprint_code} className="p-2 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 flex flex-col justify-between">
                                        <div>
                                          <div className="font-black text-emerald-950 dark:text-emerald-300 leading-snug">
                                            {spTitle}
                                          </div>
                                          <div className="mt-1 space-y-0.5 text-zinc-800 dark:text-zinc-300">
                                            {(sp.tasks || []).map((tk) => {
                                              const tCode = tk.task_code || tk.task_id;
                                              const tTitle = typeof tk.title === 'object' ? (tk.title?.[lang] || tk.title?.id || tCode) : (tk.title || tCode);
                                              return (
                                                <div key={tCode} className="truncate" title={`${tCode} (${tk.role || 'Engineer'}): ${tTitle}`}>
                                                  • <strong>{tCode}</strong>: {tTitle}
                                                </div>
                                              );
                                            })}
                                          </div>
                                        </div>
                                        <div className="mt-1.5 pt-1 border-t border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-400 font-bold tabular-nums flex items-center justify-between">
                                          <span>{sp.phase_range || `Sprint ${sp.sprint || ''}`}</span>
                                          <span>{(sp.tasks || []).length} Tasks</span>
                                        </div>
                                      </div>
                                    );
                                  })}
                                </div>
                              </div>
                            );
                          })()}
                        </div>
                      );
                    })()}
                  </div>
                );
              })()}

              {/* =================================================================
                  7-TAB SENIOR ARCHITECT WORKBENCH (VISIBLE WHEN matrixMode === 'workbench')
                  ================================================================= */}
              {matrixMode === 'workbench' && (
                <div className="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                  {/* Horizontal 7-Tab Navigation Bar */}
                  <div className="flex flex-wrap items-center gap-1.5 mb-4 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    {[
                      { id: 'sectors', label: lang === 'en' ? '1. Sectors & Industrial UI (12)' : '1. Sektor & UI Industri (12)' },
                      { id: 'primitives', label: lang === 'en' ? `2. Core Primitives (${effectivePrimitiveCodes.length}/12)` : `2. Modul Primitif (${effectivePrimitiveCodes.length}/12)` },
                      { id: 'stacks', label: lang === 'en' ? '3. Stack & Topology (9)' : '3. Topologi & Stack (9)' },
                      { id: 'brownfield', label: lang === 'en' ? '4. Legacy SQL Importer' : '4. Impor SQL Lama' },
                      { id: 'doctor', label: lang === 'en' ? '5. 🩺 Doctor & 8 Day-2 Layers' : '5. 🩺 Doctor & 8 Layer Day-2' },
                      { id: 'menus_glue', label: lang === 'en' ? `6. 🏢 25 L1–L4 Menus & 5 Glue (${effectiveSelectedMenus.length}/25)` : `6. 🏢 25 Menu L1–L4 & 5 Glue (${effectiveSelectedMenus.length}/25)` },
                      { id: 'runtime_v7', label: lang === 'en' ? '7. ⚡ 5 Real-World & 15 Pillars (100%)' : '7. ⚡ 5 Elemen Nyata & 15 Pilar (100%)' },
                    ].map((wbTab) => {
                      const isTabActive = activeWorkbenchTab === wbTab.id;
                      return (
                        <button
                          key={wbTab.id}
                          type="button"
                          onClick={() => {
                            setActiveWorkbenchTab(wbTab.id);
                            if (wbTab.id === 'brownfield') setShowBrownfieldLab(true);
                          }}
                          className={`px-3 py-2 rounded-xs border font-mono text-[11px] font-black transition-colors cursor-pointer ${isTabActive
                            ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-500 dark:text-zinc-950 dark:border-emerald-400'
                            : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-900 border-zinc-300 dark:bg-zinc-950 dark:hover:bg-zinc-900 dark:text-zinc-200 dark:border-zinc-800'
                            }`}
                        >
                          {wbTab.label}
                        </button>
                      );
                    })}
                  </div>

                  {/* TAB 1: 12 Functional Sectors & Industrial UI/UX Cockpit */}
                  {activeWorkbenchTab === 'sectors' && (
                    <div>
                      <div className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-2.5">
                        {lang === 'en' ? '1. Primary Functional Sector (12 Domain-Driven Taxonomies)' : '1. Pilih Sektor Fungsional Utama (12 Domain-Driven Taxonomies)'}
                      </div>
                      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                        {matrixCatalog.sectors.map((sec) => {
                          const isSelected = formData.primarySector === sec.code;
                          const title = (sec.name && (sec.name[lang] || sec.name.id || sec.name.en)) || sec.code;
                          const desc = (sec.description && (sec.description[lang] || sec.description.id || sec.description.en)) || '';
                          return (
                            <button
                              key={sec.code}
                              type="button"
                              onClick={() => selectMatrixSector(sec.code)}
                              className={`text-left p-3 rounded-sm border transition-colors cursor-pointer ${isSelected
                                ? 'bg-zinc-900 text-white border-zinc-900 dark:bg-emerald-950/70 dark:text-zinc-50 dark:border-emerald-500'
                                : 'bg-zinc-50 hover:bg-zinc-100/90 text-zinc-900 border-zinc-200 hover:border-zinc-400 dark:bg-zinc-950 dark:hover:bg-zinc-900 dark:text-zinc-100 dark:border-zinc-800 dark:hover:border-zinc-600'
                                }`}
                            >
                              <div className="flex items-start justify-between gap-2">
                                <span className="text-xs font-bold leading-snug">{title}</span>
                                {isSelected && (
                                  <span className="px-1.5 py-0.5 rounded-xs bg-emerald-500 text-zinc-950 font-mono text-[9px] font-black shrink-0">
                                    AKTIF
                                  </span>
                                )}
                              </div>
                              <p className={`mt-1 text-[11px] leading-relaxed line-clamp-2 ${isSelected ? 'text-zinc-300 dark:text-zinc-300' : 'text-zinc-600 dark:text-zinc-400'}`}>
                                {desc}
                              </p>
                            </button>
                          );
                        })}
                      </div>

                      {displaySectorObj?.ui_ux_archetypes && (() => {
                        const uiArc = displaySectorObj.ui_ux_archetypes;
                        const seederContract = displaySectorObj.realistic_seeder_contract || {};
                        const panes = uiArc?.wireframe_blueprint?.panes || [];
                        const shortcuts = uiArc?.keyboard_shortcuts || [];
                        const forbidden = (uiArc?.forbidden_ux_patterns || [])[0] || '';
                        const headline = uiArc?.headline?.[lang] || uiArc?.headline?.id || uiArc?.primary_archetype || '';

                        return (
                          <div className="mt-3 p-3.5 rounded-sm bg-zinc-50 dark:bg-zinc-950 border border-emerald-500/50 dark:border-emerald-700/70 space-y-3">
                            <div className="flex flex-wrap items-center justify-between gap-2">
                              <div>
                                <span className="text-[10px] font-mono font-black uppercase text-emerald-800 dark:text-emerald-300 block">
                                  {lang === 'en'
                                    ? 'LOCKED INDUSTRIAL UI/UX COCKPIT & INDONESIAN DOMAIN SEEDER'
                                    : 'KONTRAK UI/UX SPESIFIK INDUSTRI & SEEDER KONTEKSTUAL INDONESIA'}
                                </span>
                                <div className="text-xs font-black text-zinc-950 dark:text-zinc-100 mt-0.5">
                                  {headline}
                                </div>
                              </div>
                              <div className="flex flex-wrap items-center gap-1.5">
                                <span className="px-2 py-0.5 rounded-xs bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black">
                                  1° {uiArc.primary_archetype}
                                </span>
                                {uiArc.secondary_archetype && (
                                  <span className="px-2 py-0.5 rounded-xs bg-zinc-200 text-zinc-950 border border-zinc-400 dark:bg-zinc-900 dark:text-zinc-200 dark:border-zinc-700 font-mono text-[10px] font-black">
                                    2° {uiArc.secondary_archetype}
                                  </span>
                                )}
                                {seederContract.seeder_class && (
                                  <span className="px-2 py-0.5 rounded-xs bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 font-mono text-[10px] font-black">
                                    {seederContract.seeder_class}.php
                                  </span>
                                )}
                              </div>
                            </div>

                            {panes.length > 0 && (
                              <div className={`grid grid-cols-1 ${panes.length >= 3 ? 'md:grid-cols-3' : 'md:grid-cols-2'} gap-2`}>
                                {panes.map((p, idx) => (
                                  <div
                                    key={idx}
                                    className="p-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs"
                                  >
                                    <div className="flex items-center justify-between gap-1 mb-1">
                                      <span className="px-1.5 py-0.5 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 font-mono text-[9px] font-black">
                                        {p.span} COL
                                      </span>
                                      <span className="font-mono text-[9px] font-bold text-emerald-800 dark:text-emerald-400">
                                        PANE #{idx + 1}
                                      </span>
                                    </div>
                                    <div className="text-[11px] font-bold text-zinc-950 dark:text-zinc-100">
                                      {p.title}
                                    </div>
                                  </div>
                                ))}
                              </div>
                            )}

                            <div className="flex flex-wrap items-center justify-between gap-2 pt-1 text-[11px]">
                              <div className="flex flex-wrap items-center gap-1.5">
                                {shortcuts.slice(0, 4).map((sc, sIdx) => (
                                  <span
                                    key={sIdx}
                                    className="px-2 py-0.5 bg-white dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 font-mono text-[10px] font-bold rounded-xs"
                                  >
                                    [{sc.key}] {sc.action}
                                  </span>
                                ))}
                              </div>
                              {forbidden && (
                                <span className="font-mono text-[10px] font-bold text-amber-950 dark:text-amber-300">
                                  🚫 {forbidden}
                                </span>
                              )}
                            </div>
                          </div>
                        );
                      })()}
                    </div>
                  )}

                  {/* TAB 2: Composable Cross-Sector Core Primitives */}
                  {activeWorkbenchTab === 'primitives' && (
                    <div>
                      <div className="flex items-center justify-between gap-2 mb-2.5">
                        <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                          {lang === 'en'
                            ? '2. Injected Cross-Sector Core Primitives (Combine freely for Composite Business)'
                            : '2. Rakit Modul Primitif Lintas Sektor (Gabungkan Bebas untuk Bisnis Komposit)'}
                        </span>
                        <span className="text-[10px] font-mono font-black text-zinc-900 dark:text-zinc-200 tabular-nums">
                          {effectivePrimitiveCodes.length} Modul Aktif
                        </span>
                      </div>
                      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        {matrixCatalog.primitives.map((prim) => {
                          const mandatoryList = displaySectorObj?.mandatory_primitives || [];
                          const isMandatory = mandatoryList.includes(prim.code);
                          const isChecked = isMandatory || (formData.selectedPrimitives || []).includes(prim.code);
                          const pTitle = (prim.name && (prim.name[lang] || prim.name.id || prim.name.en)) || prim.code;
                          const tables = (prim.table_names || []).join(', ');
                          return (
                            <button
                              key={prim.code}
                              type="button"
                              onClick={() => toggleMatrixPrimitive(prim.code)}
                              className={`text-left p-2.5 rounded-sm border transition-colors flex items-start gap-2.5 cursor-pointer ${isChecked
                                ? 'bg-emerald-50/90 border-emerald-500 text-zinc-950 dark:bg-emerald-950/40 dark:border-emerald-600 dark:text-zinc-100'
                                : 'bg-white hover:bg-zinc-50 border-zinc-200 hover:border-zinc-400 text-zinc-800 dark:bg-zinc-950 dark:hover:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-300'
                                }`}
                            >
                              <span className={`mt-0.5 w-4 h-4 rounded-xs border flex items-center justify-center shrink-0 font-mono text-[10px] font-black ${isChecked
                                ? 'bg-emerald-600 border-emerald-600 text-white dark:bg-emerald-500 dark:border-emerald-500 dark:text-zinc-950'
                                : 'bg-white dark:bg-zinc-900 border-zinc-400 dark:border-zinc-600 text-transparent'
                                }`}>
                                ✓
                              </span>
                              <div className="min-w-0 flex-1">
                                <div className="flex items-center justify-between gap-1.5">
                                  <span className="text-[11px] font-bold leading-tight truncate">{pTitle}</span>
                                  {isMandatory && (
                                    <span className="px-1 py-0.5 rounded-xs bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 font-mono text-[8px] font-black shrink-0">
                                      WAJIB
                                    </span>
                                  )}
                                </div>
                                {tables && (
                                  <div className="mt-1 text-[10px] font-mono text-zinc-600 dark:text-zinc-400 truncate">
                                    DDL: {tables}
                                  </div>
                                )}
                              </div>
                            </button>
                          );
                        })}
                      </div>
                    </div>
                  )}

                  {/* TAB 3: 9 Curated Tech Stack & Topology Presets */}
                  {activeWorkbenchTab === 'stacks' && (matrixCatalog.stack_presets || []).length > 0 && (
                    <div>
                      <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2.5 mb-3">
                        <div>
                          <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100 block">
                            {lang === 'en'
                              ? '3. Topology & Curated Tech Stack Preset (9 Type-Safe Adapters — Zero Free-Text Stack Soup)'
                              : '3. Pilih Topologi & Preset Tech Stack Terkurasi (9 Adapter Type-Safe — Domain Bisnis Terpisah 100% dari Stack)'}
                          </span>
                          <p className="text-[11px] text-zinc-600 dark:text-zinc-400 mt-0.5">
                            {lang === 'en'
                              ? 'Switch freely between Monolith (M1–M4), Decoupled Headless (D1–D3), and Microservices (S1–S2) while keeping the exact same 12-Sector & 8-Layer Day-2 domain blueprint.'
                              : 'Blueprint bisnis yang sama dapat dikompilasi ke Laravel 13 Monolith (M1), Go + sqlc + Next.js (D2), atau Polyglot Microservices + Temporal.io (S1) hanya dengan mengganti adapter di bawah.'}
                          </p>
                        </div>
                        <div className="flex flex-wrap items-center gap-1.5 shrink-0">
                          {[
                            { id: 'ALL', label: lang === 'en' ? 'ALL (9)' : 'SEMUA (9)' },
                            { id: 'MONOLITH', label: 'MONOLITH (M1–M4)' },
                            { id: 'DECOUPLED_HEADLESS', label: 'HEADLESS (D1–D3)' },
                            { id: 'MICROSERVICES', label: 'MICROSERVICES (S1–S2)' },
                          ].map((tab) => {
                            const active = activeTopologyFilter === tab.id;
                            return (
                              <button
                                key={tab.id}
                                type="button"
                                onClick={() => setActiveTopologyFilter(tab.id)}
                                className={`px-2.5 py-1 rounded-xs border font-mono text-[10px] font-black transition-colors cursor-pointer ${active
                                  ? 'bg-zinc-900 text-white border-zinc-900 dark:bg-zinc-100 dark:text-zinc-950 dark:border-zinc-100'
                                  : 'bg-zinc-100 hover:bg-zinc-200/80 text-zinc-900 border-zinc-300 dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700'
                                  }`}
                              >
                                {tab.label}
                              </button>
                            );
                          })}
                          {(matrixCatalog.stack_comparison_matrix || []).length > 0 && (
                            <button
                              type="button"
                              onClick={() => setShowStackMatrixTable((v) => !v)}
                              className="px-2.5 py-1 rounded-xs border border-emerald-500 bg-emerald-100 hover:bg-emerald-200/80 text-emerald-950 dark:bg-emerald-950/80 dark:hover:bg-emerald-900 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black transition-colors cursor-pointer"
                            >
                              {showStackMatrixTable
                                ? (lang === 'en' ? 'Hide Decision Matrix' : 'Tutup Matriks Panduan')
                                : (lang === 'en' ? 'Compare 9 Stacks' : 'Panduan Pilih Stack')}
                            </button>
                          )}
                        </div>
                      </div>

                      {showStackMatrixTable && (matrixCatalog.stack_comparison_matrix || []).length > 0 && (
                        <div className="mb-3.5 overflow-x-auto border border-zinc-300 dark:border-zinc-800 rounded-sm bg-white dark:bg-zinc-950">
                          <table className="w-full text-left border-collapse text-[11px]">
                            <thead className="bg-zinc-100 dark:bg-zinc-900 border-b border-zinc-300 dark:border-zinc-800 font-mono text-[10px] uppercase text-zinc-900 dark:text-zinc-200">
                              <tr>
                                <th className="py-2 px-3 font-black">{lang === 'en' ? 'Target Scale & Use Case' : 'Kebutuhan Bisnis & Skala Sistem'}</th>
                                <th className="py-2 px-3 font-black">{lang === 'en' ? 'Recommended Preset' : 'Rekomendasi Preset'}</th>
                                <th className="py-2 px-3 font-black">{lang === 'en' ? 'Architectural Rationale' : 'Alasan Arsitektural Utama'}</th>
                                <th className="py-2 px-3 font-black text-right">{lang === 'en' ? 'Action' : 'Aksi'}</th>
                              </tr>
                            </thead>
                            <tbody className="divide-y divide-zinc-200 dark:divide-zinc-800">
                              {matrixCatalog.stack_comparison_matrix.map((row, idx) => {
                                const presetCode = row.code || row.preset_code;
                                const isRowSelected = formData.stackPreset === presetCode;
                                return (
                                  <tr
                                    key={presetCode || idx}
                                    className="odd:bg-white dark:odd:bg-zinc-950 even:bg-zinc-50/70 dark:even:bg-zinc-900/40 hover:bg-zinc-100/90 dark:hover:bg-zinc-800/80 transition-colors"
                                  >
                                    <td className="py-2 px-3 font-bold text-zinc-900 dark:text-zinc-100">
                                      {row.best_use_case?.[lang] || row.best_use_case?.id || row.use_case?.[lang] || ''}
                                    </td>
                                    <td className="py-2 px-3 font-mono font-black text-emerald-900 dark:text-emerald-300 whitespace-nowrap">
                                      [{row.short_code}] {row.architecture_label || row.topology}
                                    </td>
                                    <td className="py-2 px-3 text-zinc-700 dark:text-zinc-300">
                                      {row.inter_layer_contract || row.key_reason?.[lang] || ''}
                                    </td>
                                    <td className="py-2 px-3 text-right whitespace-nowrap">
                                      <button
                                        type="button"
                                        onClick={() => selectStackPreset(presetCode)}
                                        className={`px-2 py-1 rounded-xs font-mono text-[10px] font-black border cursor-pointer ${isRowSelected
                                          ? 'bg-emerald-600 text-white border-emerald-700 dark:bg-emerald-500 dark:text-zinc-950 dark:border-emerald-400'
                                          : 'bg-zinc-900 text-white border-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-950 dark:border-zinc-100'
                                          }`}
                                      >
                                        {isRowSelected ? 'AKTIF' : (lang === 'en' ? 'Select' : 'Gunakan')}
                                      </button>
                                    </td>
                                  </tr>
                                );
                              })}
                            </tbody>
                          </table>
                        </div>
                      )}

                      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                        {filteredStackPresets.map((preset) => {
                          const isSelected = (formData.stackPreset || 'STACK_M1_LARAVEL_MONOLITH') === preset.code;
                          const pName = (preset.name && (preset.name[lang] || preset.name.id || preset.name.en)) || preset.code;
                          const pBestObj = preset.best_use_cases || preset.best_for || {};
                          const pBest = pBestObj[lang] || pBestObj.id || pBestObj.en || '';
                          const contractMech = preset.inter_layer_type_contract?.mechanism || '';
                          return (
                            <button
                              key={preset.code}
                              type="button"
                              onClick={() => selectStackPreset(preset.code)}
                              className={`text-left p-3 rounded-sm border transition-colors flex flex-col justify-between cursor-pointer ${isSelected
                                ? 'bg-zinc-900 text-white border-zinc-900 ring-1 ring-emerald-500 dark:bg-emerald-950/75 dark:text-zinc-50 dark:border-emerald-500'
                                : 'bg-white hover:bg-zinc-50 text-zinc-900 border-zinc-200 hover:border-zinc-400 dark:bg-zinc-950 dark:hover:bg-zinc-900 dark:text-zinc-100 dark:border-zinc-800 dark:hover:border-zinc-600'
                                }`}
                            >
                              <div>
                                <div className="flex items-center justify-between gap-1.5 mb-1.5">
                                  <div className="flex items-center gap-1.5">
                                    <span className={`px-1.5 py-0.5 rounded-xs font-mono text-[10px] font-black ${isSelected
                                      ? 'bg-emerald-500 text-zinc-950'
                                      : 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950'
                                      }`}>
                                      {preset.short_code}
                                    </span>
                                    <span className={`px-1.5 py-0.5 rounded-xs font-mono text-[9px] font-black border ${isSelected
                                      ? 'bg-black text-white border-zinc-700'
                                      : 'bg-zinc-100 text-zinc-900 border-zinc-300 dark:bg-zinc-900 dark:text-zinc-200 dark:border-zinc-700'
                                      }`}>
                                      {preset.topology}
                                    </span>
                                  </div>
                                  {preset.is_default && (
                                    <span className={`px-1.5 py-0.5 rounded-xs font-mono text-[8px] font-black uppercase ${isSelected
                                      ? 'bg-white text-zinc-950'
                                      : 'bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700'
                                      }`}>
                                      DEFAULT
                                    </span>
                                  )}
                                </div>
                                <div className="text-xs font-black leading-snug">{pName}</div>
                                <p className={`mt-1 text-[11px] leading-relaxed line-clamp-2 ${isSelected ? 'text-zinc-300' : 'text-zinc-600 dark:text-zinc-400'
                                  }`}>
                                  {pBest}
                                </p>
                              </div>
                              {contractMech && (
                                <div className={`mt-2.5 pt-2 border-t font-mono text-[10px] truncate ${isSelected
                                  ? 'border-zinc-700 text-emerald-300'
                                  : 'border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300'
                                  }`}>
                                  Contract: {contractMech}
                                </div>
                              )}
                            </button>
                          );
                        })}
                      </div>
                    </div>
                  )}

                  {/* TAB 4: Brownfield Legacy SQL / Migration / Prisma Importer */}
                  {activeWorkbenchTab === 'brownfield' && (
                    <div className="p-4 rounded-sm bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 space-y-3">
                      <div className="flex flex-wrap items-center justify-between gap-2">
                        <div>
                          <span className="font-mono text-[11px] font-black uppercase text-zinc-950 dark:text-zinc-100 block">
                            {lang === 'en'
                              ? '4. Brownfield Legacy SQL / Laravel Migration / Prisma Importer (Shadow ULID)'
                              : '4. Audit & Impor Skema SQL / Migrasi Laravel / Prisma (Shadow ULID Tanpa Kehilangan Data)'}
                          </span>
                          <span className="text-[11px] text-zinc-600 dark:text-zinc-400">
                            {lang === 'en'
                              ? 'Detects AUTO_INCREMENT / FLOAT / ON DELETE CASCADE debt and maps Sector + Primitives automatically.'
                              : 'Mendeteksi utang teknis AUTO_INCREMENT / FLOAT / CASCADE serta memetakan Sektor + Primitives otomatis.'}
                          </span>
                        </div>
                        <button
                          type="button"
                          onClick={() => setBrownfieldSqlText(
                            "CREATE TABLE orders (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  patient_id INT NOT NULL,\n  total_amount FLOAT DEFAULT 0,\n  FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE\n);\nCREATE TABLE medical_records (\n  id BIGINT AUTO_INCREMENT PRIMARY KEY,\n  diagnosis TEXT\n);"
                          )}
                          className="px-2.5 py-1 rounded-xs bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-200 font-mono text-[10px] font-bold hover:border-emerald-500 cursor-pointer"
                        >
                          {lang === 'en' ? 'Load Legacy Sample SQL' : 'Isi Contoh SQL Legacy'}
                        </button>
                      </div>

                      <textarea
                        rows={5}
                        value={brownfieldSqlText}
                        onChange={(e) => setBrownfieldSqlText(e.target.value)}
                        placeholder="CREATE TABLE invoices ( id INT AUTO_INCREMENT PRIMARY KEY, amount FLOAT ... );"
                        className="w-full p-3 rounded-xs bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 font-mono text-xs text-zinc-950 dark:text-zinc-100 focus:outline-none focus:border-emerald-500"
                      />

                      <div className="flex flex-wrap items-center gap-2">
                        <button
                          type="button"
                          disabled={brownfieldLoading}
                          onClick={runBrownfieldAudit}
                          className="px-3.5 py-2 rounded-xs bg-emerald-600 hover:bg-emerald-500 disabled:opacity-60 text-white dark:text-zinc-950 font-mono text-xs font-black uppercase cursor-pointer"
                        >
                          {brownfieldLoading
                            ? (lang === 'en' ? 'Scanning Legacy Debt...' : 'Memindai Utang Teknis...')
                            : (lang === 'en' ? 'Audit Legacy Schema & Map Sector' : 'Audit Skema & Petakan Sektor')}
                        </button>

                        {brownfieldReport && (
                          <>
                            <button
                              type="button"
                              onClick={applyBrownfieldRecommendation}
                              className="px-3.5 py-2 rounded-xs bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:text-zinc-950 font-mono text-xs font-black uppercase cursor-pointer"
                            >
                              {lang === 'en' ? 'Apply Sector + Shadow ULID Plan' : 'Terapkan Sektor + Rencana Shadow ULID'}
                            </button>
                            <button
                              type="button"
                              onClick={() => {
                                if (navigator?.clipboard?.writeText && brownfieldReport.ai_refactor_prompt) {
                                  navigator.clipboard.writeText(brownfieldReport.ai_refactor_prompt);
                                }
                                if (window.showToast) {
                                  window.showToast({
                                    type: 'success',
                                    title: lang === 'en' ? 'Refactor Prompt Copied' : 'Prompt Refactor Disalin',
                                    message: lang === 'en'
                                      ? 'Paste into Claude Code / Cursor / Antigravity to execute the 4-stage migration.'
                                      : 'Tempelkan ke Claude Code / Cursor / Antigravity untuk mengeksekusi migrasi 4 tahap.',
                                  });
                                }
                              }}
                              className="px-3 py-2 rounded-xs bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 font-mono text-xs font-bold uppercase cursor-pointer"
                            >
                              {lang === 'en' ? 'Copy AI Refactor Prompt' : 'Salin AI Refactor Prompt'}
                            </button>
                          </>
                        )}
                      </div>

                      {brownfieldReport && (
                        <div className="mt-3 p-3.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-3">
                          <div className="flex flex-wrap items-center justify-between gap-2">
                            <div className="font-mono text-xs font-black text-zinc-950 dark:text-zinc-100">
                              {lang === 'en' ? 'Recommended Sector:' : 'Rekomendasi Sektor:'}{' '}
                              <span className="text-emerald-800 dark:text-emerald-300">
                                {brownfieldReport.recommended_sector?.name} ({brownfieldReport.recommended_sector?.primary_ui_archetype})
                              </span>
                            </div>
                            <span className={`px-2 py-0.5 rounded-xs font-mono text-[10px] font-black border tabular-nums ${brownfieldReport.readiness_score >= 85
                              ? 'bg-emerald-100 text-emerald-950 border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700'
                              : 'bg-amber-100 text-amber-950 border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700'
                              }`}>
                              SCORE: {brownfieldReport.readiness_score}/100 &bull; {brownfieldReport.grade}
                            </span>
                          </div>

                          {(brownfieldReport.violations || []).length > 0 && (
                            <div className="space-y-1.5">
                              {brownfieldReport.violations.map((v, vIdx) => (
                                <div
                                  key={vIdx}
                                  className="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xs text-[11px]"
                                >
                                  <div className="font-mono font-black text-amber-950 dark:text-amber-300">
                                    [{v.code} • {v.severity}] {v.title}
                                  </div>
                                  <div className="text-zinc-700 dark:text-zinc-300 mt-0.5">{v.remediation}</div>
                                </div>
                              ))}
                            </div>
                          )}
                        </div>
                      )}
                    </div>
                  )}

                  {/* TAB 5: Layer 8 Blueprint Doctor & 8 Day-2 Production Layers */}
                  {activeWorkbenchTab === 'doctor' && (
                    <div className="space-y-4">
                      <div className="p-4 rounded-sm bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 space-y-3">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                          <div>
                            <span className="font-mono text-[10px] font-black uppercase text-emerald-800 dark:text-emerald-300 block">
                              LAYER 8 • PRE-FLIGHT CONFLICT RESOLVER &amp; READINESS VALIDATOR
                            </span>
                            <h4 className="text-sm font-black text-zinc-950 dark:text-zinc-100 mt-0.5">
                              {lang === 'en'
                                ? 'Interactive Blueprint Doctor (Cross-Module & Topology Validator)'
                                : 'Diagnosa Pre-Flight Blueprint Doctor (Validasi Lintas Modul & Topologi)'}
                            </h4>
                          </div>
                          <div className="flex flex-wrap items-center gap-2 font-mono text-[10px]">
                            <button
                              type="button"
                              disabled={doctorLoading}
                              onClick={runPreFlightDoctor}
                              className="px-3 py-1.5 rounded-xs bg-emerald-600 hover:bg-emerald-500 text-white dark:text-zinc-950 font-black uppercase cursor-pointer"
                            >
                              {doctorLoading
                                ? (lang === 'en' ? 'Scanning...' : 'Mendiagnosa...')
                                : (lang === 'en' ? '🩺 Run Pre-Flight Audit' : '🩺 Jalankan Diagnosa Penuh')}
                            </button>
                            {canOneClickFix && (
                              <button
                                type="button"
                                onClick={applyDoctorAutoFix}
                                className="px-3 py-1.5 rounded-xs bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:text-zinc-950 font-black uppercase cursor-pointer"
                              >
                                {lang === 'en' ? '✨ Apply 1-Click Auto-Fix' : '✨ Terapkan 1-Klik Auto-Fix'}
                              </button>
                            )}
                          </div>
                        </div>

                        {doctorReport ? (
                          <div className="space-y-2">
                            <div className="flex flex-wrap items-center justify-between gap-2 p-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs font-mono text-[11px]">
                              <span className="font-black text-zinc-950 dark:text-zinc-100 tabular-nums">
                                STATUS: {doctorReport.status} &bull; SCORE: {doctorReport.readiness_score}/100
                              </span>
                              <span className="text-zinc-700 dark:text-zinc-300 tabular-nums">
                                Warnings: {doctorReport.warning_count ?? 0} &bull; Suggestions: {doctorReport.recommendation_count ?? 0} &bull; SHA-256 Logged
                              </span>
                            </div>
                            {(doctorReport.findings || []).map((f, fIdx) => {
                              const fTitle = typeof f.title === 'object' ? (f.title?.[lang] || f.title?.id || f.code) : (f.title || f.code);
                              const fMsg = typeof f.message === 'object' ? (f.message?.[lang] || f.message?.id || '') : (f.message || f.explanation || '');
                              const fFixLabel = typeof f.auto_fix?.label === 'object' ? (f.auto_fix.label?.[lang] || f.auto_fix.label?.id || '') : (f.recommendation || '');
                              return (
                                <div
                                  key={fIdx}
                                  className="p-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs text-[11px]"
                                >
                                  <div className="flex items-center justify-between gap-2 font-mono font-black">
                                    <span className={f.severity === 'WARNING' ? 'text-amber-950 dark:text-amber-300' : 'text-emerald-900 dark:text-emerald-300'}>
                                      [{f.code} • {f.severity}] {fTitle}
                                    </span>
                                  </div>
                                  <div className="text-zinc-700 dark:text-zinc-300 mt-0.5">{fMsg}</div>
                                  {fFixLabel && (
                                    <div className="font-mono text-[10px] text-emerald-800 dark:text-emerald-300 mt-1">
                                      Fix: {fFixLabel}
                                    </div>
                                  )}
                                </div>
                              );
                            })}
                          </div>
                        ) : (
                          <p className="text-xs text-zinc-700 dark:text-zinc-300">
                            {lang === 'en'
                              ? 'Click "Run Pre-Flight Audit" to verify Pharmacy/Stock ledgers, Double-Entry settlement journals, Maker-Checker approvals, and Stack vs Timeline realism before locking your PRD.'
                              : 'Klik "Jalankan Diagnosa Penuh" untuk memvalidasi kecocokan Buku Besar Stok/Resep, Jurnal Double-Entry Settlement, Otorisasi Berjenjang, dan kecocokan Topologi Stack terhadap target waktu Go-Live Anda.'}
                          </p>
                        )}
                      </div>

                      {/* 8 Day-2 Production Layers Grid */}
                      {(matrixCatalog.final_eight_layers || []).length > 0 && (
                        <div>
                          <div className="text-[11px] font-mono font-black uppercase tracking-wider text-zinc-900 dark:text-zinc-100 mb-2">
                            {lang === 'en'
                              ? '8 Day-2 Production Layers Automatically Compiled into Your Blueprint'
                              : '8 Lapisan Operasional Day-2 yang Otomatis Dikompilasi ke Blueprint Anda'}
                          </div>
                          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
                            {matrixCatalog.final_eight_layers.map((layer) => {
                              const layerNum = layer.number || layer.layer;
                              const layerDesc = layer.desc?.[lang] || layer.desc?.id || layer.summary?.[lang] || layer.summary?.id || '';
                              return (
                                <div
                                  key={layerNum || layer.code}
                                  className="p-2.5 rounded-xs bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800"
                                >
                                  <div className="flex items-center justify-between gap-1 font-mono text-[9px] font-black mb-1">
                                    <span className="px-1.5 py-0.5 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950">
                                      LAYER 0{layerNum}
                                    </span>
                                    <span className="text-emerald-800 dark:text-emerald-300">{layer.badge || layer.code}</span>
                                  </div>
                                  <div className="text-[11px] font-black text-zinc-950 dark:text-zinc-100 leading-snug">
                                    {layer.title?.[lang] || layer.title?.id || layer.code}
                                  </div>
                                  <p className="text-[10px] text-zinc-700 dark:text-zinc-300 mt-1 leading-relaxed line-clamp-3">
                                    {layerDesc}
                                  </p>
                                </div>
                              );
                            })}
                          </div>
                        </div>
                      )}
                    </div>
                  )}

                  {/* TAB 6: Project OS v6.0 — 25 L1–L4 Enterprise Sub-Menus + 5 Corporate Glue + 5 Guarantee Engines */}
                  {activeWorkbenchTab === 'menus_glue' && (
                    <div className="space-y-4">
                      <div className="p-3.5 rounded-xs bg-zinc-50 dark:bg-zinc-950 border border-emerald-500/70 dark:border-emerald-700 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                        <div>
                          <span className="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300 block">
                            JAMINAN 1 &amp; 2 • L1–L4 ENTERPRISE MENU BLOOM (25 SUB-MENU) + 5 CORPORATE GLUE PRIMITIVES
                          </span>
                          <div className="text-xs font-black text-zinc-950 dark:text-zinc-100 mt-0.5">
                            {displaySectorName} — {effectiveSelectedMenus.length}/25 Sub-Menu Aktif di v1.0 ({Math.max(0, 25 - effectiveSelectedMenus.length)} masuk v1.1 Backlog)
                          </div>
                          <p className="text-[11px] text-zinc-700 dark:text-zinc-300 mt-0.5">
                            {lang === 'en'
                              ? 'Uncheck any sub-menu to defer it cleanly to Phase v1.1 Backlog without losing its ULID schema compatibility.'
                              : 'Hilangkan centang pada sub-menu yang ingin ditunda ke Backlog v1.1 tanpa merusak kompatibilitas skema ULID utama.'}
                          </p>
                        </div>
                        <div className="shrink-0 flex flex-wrap items-center gap-1.5 font-mono text-[10px]">
                          <button
                            type="button"
                            onClick={injectEnterpriseMenusIntoMvp}
                            className="px-3 py-1.5 rounded-xs bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-black uppercase cursor-pointer"
                          >
                            {lang === 'en' ? '✨ Embed v1.0 Menus into MVP Field' : '✨ Sematkan Menu v1.0 ke Fitur Wajib MVP'}
                          </button>
                        </div>
                      </div>

                      {/* 5 Operational Divisions × 5 Concrete L3 Sub-Menus */}
                      {Array.isArray(activeEnterpriseMenuMatrix?.divisions) && (
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-3">
                          {activeEnterpriseMenuMatrix.divisions.map((div) => {
                            const divTitle = div.division_name?.[lang] || div.division_name?.id || div.division_code;
                            return (
                              <div
                                key={div.division_code}
                                className="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-2"
                              >
                                <div className="flex items-center justify-between gap-2 pb-1.5 border-b border-zinc-200 dark:border-zinc-800">
                                  <span className="font-mono text-[10px] font-black uppercase text-zinc-950 dark:text-zinc-100">
                                    {divTitle}
                                  </span>
                                  <span className="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[9px] font-black">
                                    5 SUB-MENU
                                  </span>
                                </div>
                                <div className="space-y-1.5">
                                  {(div.menus || []).map((menu) => {
                                    const isMenuChecked = effectiveSelectedMenus.includes(menu.code);
                                    return (
                                      <button
                                        key={menu.code}
                                        type="button"
                                        onClick={() => toggleEnterpriseMenuCode(menu.code)}
                                        className={`w-full text-left p-2 rounded-xs border flex items-start gap-2 transition-colors cursor-pointer ${isMenuChecked
                                          ? 'bg-emerald-50/70 border-emerald-400 text-zinc-950 dark:bg-emerald-950/35 dark:border-emerald-700 dark:text-zinc-100'
                                          : 'bg-zinc-50 border-zinc-200 text-zinc-600 dark:bg-zinc-950 dark:border-zinc-800 dark:text-zinc-400'
                                          }`}
                                      >
                                        <span className={`mt-0.5 w-3.5 h-3.5 rounded-none border flex items-center justify-center shrink-0 font-mono text-[9px] font-black ${isMenuChecked
                                          ? 'bg-emerald-600 border-emerald-700 text-white dark:bg-emerald-400 dark:border-emerald-400 dark:text-zinc-950'
                                          : 'bg-white dark:bg-zinc-900 border-zinc-400 dark:border-zinc-700 text-transparent'
                                          }`}>
                                          ✓
                                        </span>
                                        <div className="min-w-0 flex-1">
                                          <div className="flex items-center justify-between gap-1.5">
                                            <span className="font-mono text-[9px] font-black text-emerald-900 dark:text-emerald-300">
                                              {menu.code}
                                            </span>
                                            <span className={`px-1 py-0.2 font-mono text-[8px] font-black border ${isMenuChecked
                                              ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-400 dark:text-zinc-950 dark:border-emerald-400'
                                              : 'bg-amber-100 text-amber-950 border-amber-400 dark:bg-amber-950/70 dark:text-amber-200 dark:border-amber-700'
                                              }`}>
                                              {isMenuChecked ? 'v1.0 MVP' : 'v1.1 BACKLOG'}
                                            </span>
                                          </div>
                                          <div className="text-[11px] font-bold leading-snug mt-0.5">{menu.title}</div>
                                        </div>
                                      </button>
                                    );
                                  })}
                                </div>
                              </div>
                            );
                          })}
                        </div>
                      )}

                      {/* 5 Corporate Glue Primitives + 5 Deterministic Guarantee Engines */}
                      {(matrixCatalog.five_enterprise_glue_primitives || []).length > 0 && (
                        <div>
                          <div className="text-[11px] font-mono font-black uppercase tracking-wider text-zinc-950 dark:text-zinc-100 mb-2">
                            {lang === 'en'
                              ? '5 Enterprise Glue Primitives Auto-Injected into PostgreSQL 16 ULID Schema'
                              : '5 Modul Perekat Korporat (Enterprise Glue) Otomatis Disuntikkan ke Skema PostgreSQL 16 ULID'}
                          </div>
                          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-2">
                            {matrixCatalog.five_enterprise_glue_primitives.map((glue) => (
                              <div
                                key={glue.code}
                                className="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 rounded-xs"
                              >
                                <span className="px-1.5 py-0.5 bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 font-mono text-[9px] font-black inline-block mb-1">
                                  {glue.table_name}
                                </span>
                                <div className="text-[11px] font-black text-zinc-950 dark:text-zinc-100 leading-tight">
                                  {glue.name?.[lang] || glue.name?.id || glue.code}
                                </div>
                                <p className="text-[10px] text-zinc-700 dark:text-zinc-300 mt-1 leading-relaxed line-clamp-3">
                                  {glue.invariant}
                                </p>
                              </div>
                            ))}
                          </div>
                        </div>
                      )}
                    </div>
                  )}

                  {/* TAB 7: Project OS v7.0 — 5 Real-World Field Runtime Contracts + Master 15 Architecture Pillars (100% Freeze) */}
                  {activeWorkbenchTab === 'runtime_v7' && (
                    <div className="space-y-4">
                      <div className="p-3.5 rounded-xs bg-zinc-50 dark:bg-zinc-950 border border-emerald-500/70 dark:border-emerald-700 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                        <div>
                          <span className="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300 block">
                            PROJECT OS v7.0 • 100% ARCHITECTURAL FREEZE (5 REAL-WORLD ELEMENTS + 15 MASTER PILLARS)
                          </span>
                          <div className="text-xs font-black text-zinc-950 dark:text-zinc-100 mt-0.5">
                            {displaySectorName} • {activeStackPresetObj?.code || formData.stackPreset} • {effectiveSelectedMenus.length}/25 Menu v1.0
                          </div>
                          <p className="text-[11px] text-zinc-700 dark:text-zinc-300 mt-0.5">
                            {lang === 'en'
                              ? 'Every change in Sector, Core Primitives, L1–L4 Menus, or Stack Preset deterministically updates Hardware Bridges, Zero-Poll WebSocket Channels, Composite PG16 Schema, SYNTAX_LOCK_2026.md, Role SOPs, and Mitra RAB/SHA-256 Scope Hash.'
                              : 'Setiap perubahan Sektor, Modul Primitif, Menu L1–L4, atau Stack secara deterministik memperbarui Jembatan Hardware, Kanal WebSocket Tanpa Polling, Skema Partisi PG16, SYNTAX_LOCK_2026.md, Buku SOP Role, hingga RAB & SHA-256 Scope Hash.'}
                          </p>
                        </div>
                        {v7RuntimePreview?.live_commercial_sync && (
                          <div className="shrink-0 p-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 font-mono text-[10px]">
                            <div className="font-black text-emerald-900 dark:text-emerald-300">
                              {lang === 'en' ? 'LIVE CAUSAL SCOPE SYNC' : 'SINKRONISASI KAUSAL REAL-TIME'}
                            </div>
                            <div className="font-black text-zinc-950 dark:text-zinc-100 mt-0.5 tabular-nums">
                              {v7RuntimePreview.live_commercial_sync.formatted_quotation_idr} ({v7RuntimePreview.live_commercial_sync.estimated_working_days} {lang === 'en' ? 'Days' : 'Hari'})
                            </div>
                            <div className="text-zinc-600 dark:text-zinc-400 tabular-nums">
                              SHA-256: {v7RuntimePreview.live_commercial_sync.scope_hash_short}
                            </div>
                          </div>
                        )}
                      </div>

                      {/* Live 5 Real-World Contracts Breakdown for Selected Sector & Stack */}
                      <div className="grid grid-cols-1 lg:grid-cols-3 gap-3">
                        <div className="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-1.5">
                          <div className="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300">
                            {lang === 'en' ? '1. HARDWARE & DUAL-SCREEN BRIDGE' : '1. HARDWARE FISIK & LAYAR KEDUA'}
                          </div>
                          <div className="text-xs font-black text-zinc-950 dark:text-zinc-100">
                            {v7RuntimePreview?.hardware_iot_peripheral_contract?.barcode_rfid_wedge?.enabled
                              ? (lang === 'en' ? '<30ms Global Barcode/RFID Wedge: ACTIVE' : 'Scanner Barcode/RFID <30ms: AKTIF')
                              : (lang === 'en' ? 'On-Demand Peripheral Ready' : 'Siap Periferal Opsional')}
                          </div>
                          <p className="text-[11px] text-zinc-700 dark:text-zinc-300">
                            {v7RuntimePreview?.hardware_iot_peripheral_contract?.customer_dual_screen?.enabled
                              ? (lang === 'en' ? 'BroadcastChannel Customer Screen + 100% Local Dynamic QRIS active.' : 'Layar kedua pelanggan BroadcastChannel + QRIS Dinamis 100% lokal aktif.')
                              : (lang === 'en' ? 'Zero external QR CDN latency guaranteed.' : 'Jaminan nol latensi CDN eksternal untuk seluruh kode QR/dokumen.')}
                          </p>
                          {v7RuntimePreview?.hardware_iot_peripheral_contract?.biometric_and_turnstile_iot?.enabled && (
                            <div className="font-mono text-[10px] font-bold text-amber-900 dark:text-amber-300">
                              ZKTeco ADMS Idempotent Webhook + Turnstile Relay &lt;150ms
                            </div>
                          )}
                        </div>

                        <div className="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-1.5">
                          <div className="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300">
                            {lang === 'en' ? '2. ZERO-POLLING WEBSOCKET & 0% N+1' : '2. WEBSOCKET TANPA POLLING & 0% N+1'}
                          </div>
                          <div className="text-xs font-black text-zinc-950 dark:text-zinc-100">
                            {v7RuntimePreview?.realtime_websocket_contract?.engine || 'Laravel Reverb / Soketi'}
                          </div>
                          <div className="space-y-1 font-mono text-[10px] text-zinc-800 dark:text-zinc-200">
                            {(v7RuntimePreview?.realtime_websocket_contract?.channels || ['organization.{ulid}.operations']).slice(0, 3).map((ch, idx) => (
                              <div key={idx} className="px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 truncate">
                                {typeof ch === 'string' ? ch : (ch.channel || ch.event)}
                              </div>
                            ))}
                          </div>
                          <p className="text-[10px] font-bold text-rose-900 dark:text-rose-300">
                            {lang === 'en' ? 'STRICT BAN: wire:poll.<30s • Strict N+1 Guard ON • PG16 Quarterly Partitioning' : 'DILARANG: wire:poll.<30s • Anti N+1 Aktif • Partisi Kuartalan PG16'}
                          </p>
                        </div>

                        <div className="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-1.5">
                          <div className="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300">
                            {lang === 'en' ? '3. SYNTAX LOCK 2026 & ROLE CLIENT SOP' : '3. SYNTAX LOCK 2026 & BUKU SOP KLIEN'}
                          </div>
                          <div className="text-xs font-black text-zinc-950 dark:text-zinc-100">
                            .agents/SYNTAX_LOCK_2026.md + SmartGuide.php
                          </div>
                          <p className="text-[11px] text-zinc-700 dark:text-zinc-300">
                            {lang === 'en'
                              ? `${v7RuntimePreview?.client_sop_and_onboarding?.role_count ?? 4} RBAC Role Onboarding Tours & Printable A4 BAST Training Manual reactively compiled from ${effectiveSelectedMenus.length} active v1.0 menus.`
                              : `${v7RuntimePreview?.client_sop_and_onboarding?.role_count ?? 4} Panduan Interaktif Peran Staf & Buku SOP Cetak A4 BAST otomatis disusun dari ${effectiveSelectedMenus.length} menu v1.0 aktif.`}
                          </p>
                        </div>
                      </div>

                      {/* Master 15 Architecture Pillars Grid */}
                      {(matrixCatalog.fifteen_architecture_pillars || []).length > 0 && (
                        <div>
                          <div className="text-[11px] font-mono font-black uppercase tracking-wider text-zinc-950 dark:text-zinc-100 mb-2">
                            {lang === 'en'
                              ? 'Master 15 Canonical Architecture Pillars — 100% Lock & Freeze (Database Cached Forever)'
                              : '15 Pilar Arsitektur Kanonikal Project OS — 100% Lock & Freeze (Tersimpan di Database)'}
                          </div>
                          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-2">
                            {matrixCatalog.fifteen_architecture_pillars.map((pillar) => (
                              <div
                                key={pillar.code}
                                className="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 rounded-xs flex flex-col justify-between"
                              >
                                <div>
                                  <div className="flex items-center justify-between gap-1 mb-1">
                                    <span className="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[9px] font-black">
                                      #{String(pillar.pillar_number).padStart(2, '0')}
                                    </span>
                                    <span className="font-mono text-[9px] font-black text-emerald-900 dark:text-emerald-300">
                                      {pillar.maturity_percent || 100}%
                                    </span>
                                  </div>
                                  <div className="text-[11px] font-black text-zinc-950 dark:text-zinc-100 leading-snug">
                                    {pillar.name?.[lang] || pillar.name?.id || pillar.code}
                                  </div>
                                  <p className="text-[10px] text-zinc-700 dark:text-zinc-300 mt-1 line-clamp-2">
                                    {pillar.guaranteed_outcome?.[lang] || pillar.guaranteed_outcome?.id || ''}
                                  </p>
                                </div>
                                <div className="mt-1.5 pt-1 border-t border-zinc-200 dark:border-zinc-800 font-mono text-[9px] font-bold text-zinc-600 dark:text-zinc-400 truncate">
                                  {pillar.artifact_contract}
                                </div>
                              </div>
                            ))}
                          </div>
                        </div>
                      )}
                    </div>
                  )}
                </div>
              )}
            </section>
          );
        })()}
    </>
  );
}
