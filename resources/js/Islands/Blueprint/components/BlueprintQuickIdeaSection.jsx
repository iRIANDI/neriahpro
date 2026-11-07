import React from 'react';
import {
  Sparkles,
  ChevronUp,
  ChevronDown,
  Cpu,
  Layers,
  Zap,
  Upload,
  FileCode,
  FileSpreadsheet,
  FileText,
  File,
  X,
  Copy,
  Download,
  Check,
  RotateCcw,
  Sliders,
  Loader2,
  Lightbulb,
  Send,
  AlertCircle,
  CheckCircle2,
  ShieldCheck,
  Plus,
} from 'lucide-react';
import { QUICK_IDEA_CATALOG } from '../constants/blueprintConstants';

export default function BlueprintQuickIdeaSection({
  isStudioOpen,
  setIsStudioOpen,
  lang,
  t,
  detectedIdeaCategory,
  activeIdeaCategory,
  selectedIdeaCategory,
  setSelectedIdeaCategory,
  applyTemplate,
  aiModels,
  selectedAiModel,
  setSelectedAiModel,
  aiTelemetry,
  ideaText,
  setIdeaText,
  textareaRef,
  isAnalyzing,
  analysisStep,
  analysisStepIndex,
  analysisError,
  honeypot,
  setHoneypot,
  honeypotWebsite,
  setHoneypotWebsite,
  attachedFiles,
  handleAnalyzeIdea,
  handleQuickDetectSectorFromIdea = () => {},
  handleFileChange,
  fileInputRef,
  removeFile,
  proactiveSuggestions,
  appliedSuggestionIds,
  handleApplySuggestion,
  selectedPaymentRailIds = new Set(['qris_national']),
  formData = {},
  coPilotInput,
  setCoPilotInput,
  isCoPilotLoading,
  handleCoPilotSubmit,
  labelClass = "block text-xs font-mono uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5 font-bold",
  inputClass = "w-full px-4 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-0 outline-none rounded-none transition font-sans",
}) {
  const configuredAiList = React.useMemo(() => {
    return Object.values(aiModels || {}).filter(m => !!m.has_key);
  }, [aiModels]);

  return (
    <>
      <section id="section-ide-studio" className="scroll-mt-24 mb-8 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 rounded-none transition-all">

        {/* Studio Accordion Header */}
        <button
          type="button"
          onClick={() => setIsStudioOpen(prev => !prev)}
          className="w-full p-4 sm:p-5 flex items-center justify-between text-left hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition border-b border-zinc-200 dark:border-zinc-800"
        >
          <div className="flex items-center gap-3">
            <div className="w-8 h-8 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
              <Sparkles className="w-4 h-4" />
            </div>
            <div>
              <span className="text-[10px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block">
                {t.studioBadge}
              </span>
              <h3 className="text-sm sm:text-base font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
                {t.studioTitle}
              </h3>
            </div>
          </div>

          <span className="text-xs font-mono font-bold text-zinc-500 flex items-center gap-1.5 shrink-0">
            {isStudioOpen ? t.studioToggleOpen : t.studioToggleClosed}
            {isStudioOpen ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
          </span>
        </button>

        {isStudioOpen && (
          <div className="p-6 sm:p-8 space-y-6">
            <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed">
              {t.studioDesc}
            </p>

            {/* Quick Templates: Dynamic Domain-Adaptive System */}
            <div className="space-y-2">
              <div className="flex flex-wrap items-center justify-between gap-2">
                <span className="block text-[11px] font-mono uppercase tracking-wider text-zinc-600 dark:text-zinc-400 font-bold">
                  {lang === 'en' ? 'Quick Idea Templates (Auto-Adapts to Client Vision):' : 'Contoh Templat Ide Cepat (Otomatis Beradaptasi dengan Ide Klien):'}
                </span>
                <span className="text-[10px] font-mono px-2 py-0.5 bg-emerald-100 text-emerald-950 dark:bg-emerald-950 dark:text-emerald-200 border border-emerald-400 dark:border-emerald-700 font-bold">
                  ⚡ {lang === 'en' ? 'Domain Adaptive' : 'Adaptif Multi-Sektor'}
                </span>
              </div>

              {/* Category Switcher Pills */}
              <div className="flex flex-wrap gap-1.5 font-mono text-[10px]">
                {detectedIdeaCategory && (
                  <button
                    type="button"
                    onClick={() => setSelectedIdeaCategory(null)}
                    className={`px-2 py-0.5 border rounded-none cursor-pointer transition ${!selectedIdeaCategory
                      ? 'bg-emerald-600 text-white border-emerald-600 font-bold shadow-xs'
                      : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'
                    }`}
                  >
                    ⚡ {lang === 'en' ? 'Auto-Detected' : 'Deteksi Otomatis'} ({QUICK_IDEA_CATALOG[detectedIdeaCategory]?.label[lang] || detectedIdeaCategory})
                  </button>
                )}
                {Object.entries(QUICK_IDEA_CATALOG).map(([catKey, catData]) => (
                  <button
                    key={catKey}
                    type="button"
                    onClick={() => setSelectedIdeaCategory(catKey)}
                    className={`px-2 py-0.5 border rounded-none cursor-pointer transition ${(selectedIdeaCategory === catKey || (!detectedIdeaCategory && activeIdeaCategory === catKey))
                      ? 'bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 border-zinc-950 dark:border-zinc-100 font-black'
                      : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'
                    }`}
                  >
                    {catData.label[lang] || catData.label.id}
                  </button>
                ))}
              </div>

              {/* Dynamic Template Cards for the Active Category */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                {(QUICK_IDEA_CATALOG[activeIdeaCategory]?.templates || []).map((tpl) => (
                  <button
                    key={tpl.id}
                    type="button"
                    onClick={() => applyTemplate(tpl.prompt[lang] || tpl.prompt.id)}
                    className="p-3 text-left border border-zinc-200 dark:border-zinc-800 hover:border-emerald-500 dark:hover:border-emerald-500 bg-white dark:bg-zinc-900 transition flex flex-col justify-between group rounded-none cursor-pointer shadow-xs"
                  >
                    <div>
                      <div className="flex items-center justify-between gap-1 mb-1">
                        <span className="text-[9px] font-mono px-1.5 py-0.2 bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border border-emerald-500/30 font-bold uppercase">
                          {tpl.badge}
                        </span>
                        <span className="text-[10px] font-mono text-zinc-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition font-bold">
                          + {lang === 'en' ? 'Use Template' : 'Gunakan'} →
                        </span>
                      </div>
                      <span className="text-xs font-bold font-sans text-zinc-950 dark:text-zinc-100 group-hover:text-emerald-700 dark:group-hover:text-emerald-300 block line-clamp-2">
                        {tpl.title[lang] || tpl.title.id}
                      </span>
                    </div>
                  </button>
                ))}
              </div>
            </div>

            {/* Multi-AI Model Selection & Token Limit Shield */}
            <div className="border border-zinc-200 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-950/80 p-4 rounded-none">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                <div className="flex items-center gap-2">
                  <div className="w-6 h-6 bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs">
                    <Cpu className="w-3.5 h-3.5" />
                  </div>
                  <div>
                    <span className="text-[10px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block">
                      {t.aiSelectorTitle}
                    </span>
                    <span className="text-xs text-zinc-600 dark:text-zinc-400 font-sans">
                      {t.aiSelectorSubtitle}
                    </span>
                  </div>
                </div>
                <div className="flex items-center gap-1.5 self-start sm:self-auto">
                  <span className="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 text-[10px] font-mono font-bold uppercase tracking-wider">
                    CIRCUIT BREAKER: AKTIF
                  </span>
                </div>
              </div>

              {/* Model Select Grid */}
              <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 mb-2.5">
                {/* Option: Auto-Failover */}
                <button
                  type="button"
                  onClick={() => setSelectedAiModel('auto')}
                  className={`p-2.5 text-left border rounded-none transition flex flex-col justify-between cursor-pointer ${selectedAiModel === 'auto'
                    ? 'bg-emerald-500/15 border-emerald-500 text-zinc-900 dark:text-zinc-100 font-bold shadow-xs'
                    : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 hover:border-zinc-400 dark:hover:border-zinc-600'
                    }`}
                >
                  <div>
                    <div className="flex items-center justify-between gap-1 mb-1">
                      <span className="text-xs font-mono font-bold flex items-center gap-1">
                        <Zap className="w-3 h-3 text-emerald-500" />
                        Auto-Failover
                      </span>
                      <span className="text-[9px] font-mono px-1.5 py-0.2 bg-emerald-500 text-black font-black uppercase">
                        REKOMENDASI
                      </span>
                    </div>
                    <p className="text-[10px] text-zinc-500 dark:text-zinc-400 leading-tight">
                      {configuredAiList.length > 0
                        ? (lang === 'en'
                          ? `Cascades automatically through ${configuredAiList.map(m => m.name).join(' -> ')} if token runs out.`
                          : `Mengalir otomatis ke ${configuredAiList.map(m => m.name).join(' -> ')} jika salah satu limit habis.`)
                        : (lang === 'en'
                          ? 'Deterministic Architecture Engine active (Configure AI keys in Admin Panel).'
                          : 'Deterministic Architecture Engine aktif (Input API Key di Admin Panel untuk mengaktifkan AI).')
                      }
                    </p>
                  </div>
                </button>

                {/* Dynamic models from getCatalog() strictly based on Backend Admin Keys */}
                {Object.entries(aiModels).map(([key, info]) => {
                  const isSelected = selectedAiModel === key;
                  const hasKey = !!info.has_key;
                  const isExhausted = info.status === 'exhausted';
                  const isWarning = info.status === 'warning';
                  const isHealthy = info.status === 'healthy';
                  // ONLY show GRATIS if key is actually configured!
                  const isFree = info.is_free && hasKey;
                  const isUltimatePrd = key === 'deepseek' || key === 'anthropic';

                  return (
                    <button
                      key={key}
                      type="button"
                      disabled={!hasKey}
                      onClick={() => {
                        if (hasKey) setSelectedAiModel(key);
                      }}
                      className={`p-2.5 text-left border rounded-none transition flex flex-col justify-between ${!hasKey
                        ? 'opacity-40 bg-zinc-100 dark:bg-zinc-900/40 border-zinc-200 dark:border-zinc-800 text-zinc-400 dark:text-zinc-600 cursor-not-allowed select-none'
                        : isSelected
                          ? 'bg-emerald-500/15 border-emerald-500 text-zinc-900 dark:text-zinc-100 font-bold shadow-xs cursor-pointer'
                          : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 hover:border-zinc-400 dark:hover:border-zinc-600 cursor-pointer'
                        }`}
                    >
                      <div>
                        <div className="flex items-center justify-between gap-1 mb-1">
                          <span className="text-xs font-mono font-bold truncate">
                            {info.name}
                          </span>
                          <div className="flex items-center gap-1 shrink-0">
                            {isFree && (
                              <span className="text-[9px] font-mono font-bold px-1 py-0.2 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/30">
                                GRATIS
                              </span>
                            )}
                            <span
                              className={`text-[9px] font-mono font-bold px-1.5 py-0.2 border ${!hasKey
                                ? 'bg-zinc-200 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 border-zinc-300 dark:border-zinc-700'
                                : isHealthy
                                  ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30'
                                  : isWarning
                                    ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/30'
                                    : isExhausted
                                      ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/30'
                                      : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500 border-zinc-300 dark:border-zinc-700'
                                }`}
                            >
                              {!hasKey
                                ? (lang === 'en' ? '⚪ NO API KEY' : '⚪ BELUM ADA KEY')
                                : isHealthy
                                  ? '🟢 SEHAT'
                                  : isWarning
                                    ? '🟡 MENIPIS'
                                    : isExhausted
                                      ? '🔴 HABIS'
                                      : '⚪ READY'}
                            </span>
                          </div>
                        </div>
                        {hasKey && isUltimatePrd && (
                          <span className="text-[9px] font-mono text-amber-600 dark:text-amber-400 font-bold block mb-1">
                            {t.aiBestForPrdBadge}
                          </span>
                        )}
                        <p className="text-[10px] text-zinc-500 dark:text-zinc-400 line-clamp-2 leading-tight">
                          {hasKey
                            ? info.recommended_for
                            : (lang === 'en' ? 'API Key not configured in Admin Panel.' : 'API Key belum diisi di Admin Panel.')}
                        </p>
                      </div>
                      {isExhausted && info.cooldown_sec > 0 && (
                        <span className="text-[9px] font-mono text-rose-500 mt-1 block">
                          Cooldown: {Math.ceil(info.cooldown_sec / 60)}m (Auto-Failover Aktif)
                        </span>
                      )}
                    </button>
                  );
                })}
              </div>

              {/* Active Failover / Telemetry Alert if available */}
              {aiTelemetry && aiTelemetry.notification && (
                <div className="mt-2.5 p-2.5 bg-amber-500/10 border border-amber-500/30 text-amber-700 dark:text-amber-300 text-xs font-mono flex items-start gap-2">
                  <AlertCircle className="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                  <div>
                    <span className="font-bold uppercase tracking-wider block mb-0.5">INFO FAILOVER OTOMATIS:</span>
                    <span>{aiTelemetry.notification}</span>
                  </div>
                </div>
              )}

              {aiTelemetry && aiTelemetry.strategic_insight && (
                <div className="mt-2.5 p-2.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs font-mono flex items-start gap-2">
                  <Sparkles className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                  <div>
                    <span className="font-bold uppercase tracking-wider block mb-0.5">
                      AI ARCHITECTURAL VERDICT ({aiTelemetry.provider_name} - {aiTelemetry.model}):
                    </span>
                    <span className="italic font-sans">"{aiTelemetry.strategic_insight}"</span>
                  </div>
                </div>
              )}

              <p className="text-[11px] font-mono text-zinc-500 dark:text-zinc-400 mt-2 flex items-center gap-1.5">
                <ShieldCheck className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                <span>{t.aiAutoFailoverHelper}</span>
              </p>
            </div>

            {/* Brain Dump Textarea + Tahap A Smart Idea Auto-Mapping Button */}
            <div id="field-ideaText" className="space-y-2.5">
              <div className="flex items-center justify-between mb-1.5">
                <label className={labelClass}>
                  {lang === 'en' ? 'Project Vision & Business Process Description' : 'Uraian Ide & Alur Proses Bisnis'}
                </label>
                <span className="text-[11px] font-mono text-zinc-400">
                  {ideaText.length} {t.charCount}
                </span>
              </div>
              <textarea
                ref={textareaRef}
                rows={5}
                value={ideaText}
                onChange={(e) => setIdeaText(e.target.value)}
                placeholder={t.textareaPlaceholder}
                className={inputClass}
              />

              {/* Tahap A (Progressive Disclosure Flow v12.0): 1-Click Auto-Detect Sector & Modules */}
              <div className="p-3 bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-400 dark:border-emerald-700 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 rounded-none">
                <div className="text-[11px] font-sans text-zinc-800 dark:text-zinc-200 leading-snug">
                  <span className="font-mono font-black uppercase text-emerald-950 dark:text-emerald-300 block">
                    {lang === 'en'
                      ? 'TAHAP A // ZERO-JARGON SMART IDEA AUTO-MAPPING'
                      : 'TAHAP A // PEMETAAN IDE OTOMATIS TANPA ISTILAH TEKNIS'}
                  </span>
                  <span>
                    {lang === 'en'
                      ? 'Not sure which of the 12 Sectors, Transaction Primitives, or Zone A+B UI fits your business? Click the button to auto-detect instantly:'
                      : 'Bingung memilih Sektor #01–#12, Modul Transaksi, atau Tampilan Layar Zona A+B? Klik tombol di samping untuk mendeteksi otomatis dari ide Anda:'}
                  </span>
                </div>
                <button
                  type="button"
                  id="btn-auto-detect-sector-idea"
                  onClick={handleQuickDetectSectorFromIdea}
                  className="px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 text-emerald-400 dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:text-zinc-950 font-mono text-[11px] font-black uppercase tracking-wider shrink-0 rounded-none cursor-pointer transition"
                >
                  {lang === 'en'
                    ? '✨ Auto-Detect Sector & Modules from My Idea'
                    : '✨ Deteksi Sektor & Modul Otomatis dari Ide Saya'}
                </button>
              </div>
            </div>

            {/* MarkItDown File Dropzone */}
            <div id="field-attachedFiles">
              <label className={labelClass}>
                {t.dropzoneTitle}
              </label>
              <div
                onClick={() => fileInputRef.current?.click()}
                onDragOver={(e) => e.preventDefault()}
                onDrop={(e) => {
                  e.preventDefault();
                  if (e.dataTransfer.files) {
                    handleFileChange({ target: { files: e.dataTransfer.files } });
                  }
                }}
                className="border-2 border-dashed border-zinc-300 dark:border-zinc-700 hover:border-emerald-500 dark:hover:border-emerald-500 bg-zinc-50 dark:bg-zinc-950 p-6 text-center cursor-pointer transition rounded-none"
              >
                <input
                  ref={fileInputRef}
                  type="file"
                  multiple
                  accept=".pdf,.doc,.docx,.txt,.md,.rtf,.csv,.tsv,.xlsx,.pptx,.png,.jpg,.jpeg,.webp"
                  onChange={handleFileChange}
                  className="hidden"
                />
                <Upload className="w-8 h-8 text-zinc-400 dark:text-zinc-500 mx-auto mb-2" />
                <p className="text-xs sm:text-sm font-bold text-zinc-800 dark:text-zinc-200">
                  {t.dropzoneSubtitle}
                </p>
                <p className="text-[11px] font-mono text-zinc-500 dark:text-zinc-400 mt-1">
                  {t.dropzoneFormats}
                </p>
              </div>

              {/* Token Reality & MarkItDown Notice */}
              <div className="mt-2.5 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-[11px] font-mono text-zinc-700 dark:text-zinc-300 flex items-start gap-2">
                <CheckCircle2 className="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                <span>{t.markitdownNotice}</span>
              </div>

              {/* v9.0 NeriahPro AgentShield 5-Layer Anti-Poisoning & Indirect Prompt Injection Firewall */}
              <div className="mt-2 p-3 bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-400 dark:border-emerald-800 text-[11px] font-mono text-zinc-900 dark:text-zinc-100 space-y-1.5 rounded-none">
                <div className="flex flex-wrap items-center justify-between gap-2">
                  <span className="font-black uppercase text-emerald-950 dark:text-emerald-300 flex items-center gap-1.5">
                    <ShieldCheck className="w-3.5 h-3.5 shrink-0" />
                    {lang === 'en'
                      ? 'NERIAHPRO AGENTSHIELD v9.0 — 5-LAYER AI MALWARE & PROMPT-INJECTION FIREWALL'
                      : 'NERIAHPRO AGENTSHIELD v9.0 — PERTAHANAN 5 LAPIS ANTI-AI MALWARE & PROMPT INJECTION'}
                  </span>
                  <span className="px-1.5 py-0.5 bg-zinc-950 text-emerald-400 dark:bg-emerald-400 dark:text-zinc-950 font-black text-[9px] uppercase rounded-none">
                    {(formData.agentshieldReport?.status || formData._meta?.agentshield_report?.status) === 'THREATS_NEUTRALIZED'
                      ? `${Number(formData.agentshieldReport?.threats_neutralized_count || formData._meta?.agentshield_report?.threats_neutralized_count || 1).toLocaleString()} THREATS STRIPPED`
                      : 'ACTIVE SANDBOX GUARD'}
                  </span>
                </div>
                <p className="text-[10px] text-zinc-800 dark:text-zinc-300 leading-relaxed font-sans">
                  {lang === 'en'
                    ? 'Every uploaded PDF/DOCX/MD & idea text is sanitized before LLM ingestion: strips Zero-Width Unicode (U+200B..U+E007F), BiDi overrides, hidden 0.1px CSS, PDF /JS /OpenAction payloads, EXIF GPS/payloads, isolates untrusted text inside <UNTRUSTED_CLIENT_DOCUMENT_DATA>, and enforces APPROVED_DEPENDENCY_MANIFEST + .claude/settings.json jail.'
                    : 'Setiap dokumen PDF/DOCX/MD & ide disanitasi otomatis sebelum masuk ke AI: melucuti karakter tak terlihat Zero-Width Unicode (U+200B..U+E007F), BiDi override, CSS 0.1px putih-di-atas-putih, payload PDF /JS /OpenAction, metadata EXIF gambar, mengisolasi teks di dalam <UNTRUSTED_CLIENT_DOCUMENT_DATA>, serta mengunci APPROVED_DEPENDENCY_MANIFEST & sandbox .claude/settings.json.'}
                </p>
              </div>

              {/* Uploaded File Badges */}
              {attachedFiles.length > 0 && (
                <div className="mt-3 space-y-2">
                  <span className="text-[11px] font-mono uppercase text-zinc-500 font-bold block">
                    {lang === 'en' ? `Files Ready for Conversion (${attachedFiles.length}/5):` : `Berkas Siap Dikonversi (${attachedFiles.length}/5):`}
                  </span>
                  <div className="flex flex-wrap gap-2">
                    {attachedFiles.map((file, idx) => (
                      <div
                        key={idx}
                        className="flex items-center gap-2 px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-xs font-mono"
                      >
                        <FileCode className="w-3.5 h-3.5 text-emerald-500" />
                        <span className="font-bold text-zinc-800 dark:text-zinc-200 truncate max-w-[200px]">{file.name}</span>
                        <span className="text-[10px] text-zinc-400">({(file.size / 1024).toFixed(0)} KB)</span>
                        <button
                          type="button"
                          onClick={(e) => { e.stopPropagation(); removeFile(idx); }}
                          className="text-zinc-400 hover:text-red-500 ml-1"
                        >
                          <X className="w-3.5 h-3.5" />
                        </button>
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>

            {/* Error Message */}
            {analysisError && (
              <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-xs font-mono flex items-center gap-2">
                <AlertCircle className="w-4 h-4 shrink-0" />
                <span>{analysisError}</span>
              </div>
            )}

            {/* Honeypots */}
            <input type="text" name="_hp_check" value={honeypot} onChange={(e) => setHoneypot(e.target.value)} className="hidden" tabIndex={-1} autoComplete="off" />
            <input type="text" name="_website" value={honeypotWebsite} onChange={(e) => setHoneypotWebsite(e.target.value)} className="hidden" tabIndex={-1} autoComplete="off" />

            {/* Synthesize Button */}
            <button
              type="button"
              onClick={handleAnalyzeIdea}
              disabled={isAnalyzing}
              className="w-full bg-emerald-600 hover:bg-emerald-500 text-black font-black uppercase tracking-wider py-4 px-6 rounded-none text-center flex items-center justify-center gap-2 transition disabled:opacity-50 text-sm font-mono shadow-md"
            >
              {isAnalyzing ? (
                <>
                  <Loader2 className="w-4 h-4 animate-spin text-black" />
                  <span className="font-black text-black">{analysisStep || t.synthesizingBtn}</span>
                </>
              ) : (
                <>
                  <Sparkles className="w-4 h-4 text-black" />
                  <span>{t.synthesizeBtn}</span>
                </>
              )}
            </button>

            {/* LIVE AI SYNTHESIS STATUS & HELPER CARD */}
            {isAnalyzing ? (
              <div className="mt-4 p-5 bg-white dark:bg-zinc-900 border-2 border-emerald-600 dark:border-emerald-500 text-zinc-950 dark:text-zinc-50 rounded-none shadow-xl font-mono transition-colors">
                <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4">
                  <div className="flex items-center gap-2">
                    <span className="relative flex h-3 w-3">
                      <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                      <span className="relative inline-flex rounded-full h-3 w-3 bg-emerald-600 dark:bg-emerald-500"></span>
                    </span>
                    <span className="text-xs font-black uppercase tracking-wider text-emerald-800 dark:text-emerald-400">
                      {lang === 'en' ? 'AI ARCHITECTURAL SYNTHESIS IN PROGRESS' : 'AI SEDANG MENSINTESIS ARSITEKTUR SISTEM'}
                    </span>
                  </div>
                  <span className="text-[11px] px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-700 font-black rounded-none">
                    {analysisStepIndex}/3
                  </span>
                </div>

                <div className="mb-4">
                  <div className="text-sm font-black text-zinc-950 dark:text-zinc-50 flex items-center gap-2">
                    <Loader2 className="w-4 h-4 animate-spin text-emerald-600 dark:text-emerald-400" />
                    <span>{analysisStep || (lang === 'en' ? 'Analyzing your project requirements...' : 'Menganalisis kebutuhan proyek Anda...')}</span>
                  </div>
                  <p className="text-xs text-zinc-700 dark:text-zinc-300 mt-1 font-sans leading-relaxed">
                    {lang === 'en'
                      ? 'The AI engine is synthesizing your vision into 26 enterprise architectural parameters with zero loss. Please wait a moment.'
                      : 'Mesin AI sedang memetakan visi Anda menjadi 26 parameter arsitektur enterprise tanpa kehilangan detail. Mohon tunggu beberapa detik.'}
                  </p>
                </div>

                {/* 3 Progressive Milestones */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-2.5 pt-2 border-t border-zinc-200 dark:border-zinc-800 text-xs">
                  {/* Step 1 */}
                  <div className={`p-2.5 border rounded-none transition ${
                    analysisStepIndex >= 1
                      ? 'bg-emerald-50/90 dark:bg-zinc-800/80 border-emerald-500 text-zinc-950 dark:text-zinc-100'
                      : 'bg-zinc-50 dark:bg-zinc-950/60 border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400'
                  }`}>
                    <div className="flex items-center gap-1.5 font-bold mb-1">
                      {analysisStepIndex > 1 ? (
                        <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                      ) : (
                        <span className="w-3.5 h-3.5 rounded-none bg-emerald-600 dark:bg-emerald-500 text-white dark:text-black flex items-center justify-center text-[10px] font-black shrink-0">1</span>
                      )}
                      <span className={analysisStepIndex >= 1 ? 'text-emerald-950 dark:text-emerald-300 font-black' : 'text-zinc-600 dark:text-zinc-400'}>
                        {lang === 'en' ? 'Zero-Loss Intake' : 'Ingesti & Ekstraksi'}
                      </span>
                    </div>
                    <p className={`text-[11px] font-sans leading-snug ${analysisStepIndex >= 1 ? 'text-zinc-800 dark:text-zinc-300' : 'text-zinc-500 dark:text-zinc-500'}`}>
                      {lang === 'en'
                        ? 'Capturing client features, operational points, and attachments.'
                        : 'Mengekstrak poin-poin ide bisnis, kebutuhan armada/fitur, dan dokumen.'}
                    </p>
                  </div>

                  {/* Step 2 */}
                  <div className={`p-2.5 border rounded-none transition ${
                    analysisStepIndex >= 2
                      ? 'bg-emerald-50/90 dark:bg-zinc-800/80 border-emerald-500 text-zinc-950 dark:text-zinc-100'
                      : 'bg-zinc-50 dark:bg-zinc-950/60 border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400'
                  }`}>
                    <div className="flex items-center gap-1.5 font-bold mb-1">
                      {analysisStepIndex > 2 ? (
                        <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                      ) : (
                        <span className={`w-3.5 h-3.5 rounded-none flex items-center justify-center text-[10px] font-black shrink-0 ${analysisStepIndex === 2 ? 'bg-emerald-600 dark:bg-emerald-500 text-white dark:text-black' : 'bg-zinc-300 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-300'}`}>2</span>
                      )}
                      <span className={analysisStepIndex >= 2 ? 'text-emerald-950 dark:text-emerald-300 font-black' : 'text-zinc-600 dark:text-zinc-400'}>
                        {lang === 'en' ? 'Domain & RBAC' : 'Domain & Aktor RBAC'}
                      </span>
                    </div>
                    <p className={`text-[11px] font-sans leading-snug ${analysisStepIndex >= 2 ? 'text-zinc-800 dark:text-zinc-300' : 'text-zinc-500 dark:text-zinc-500'}`}>
                      {lang === 'en'
                        ? 'Determining industry domain, actors, and tech stack topology.'
                        : 'Menentukan sektor industri, hak akses peran (Superadmin, Operator, Driver, Klien).'}
                    </p>
                  </div>

                  {/* Step 3 */}
                  <div className={`p-2.5 border rounded-none transition ${
                    analysisStepIndex >= 3
                      ? 'bg-emerald-50/90 dark:bg-zinc-800/80 border-emerald-500 text-zinc-950 dark:text-zinc-100'
                      : 'bg-zinc-50 dark:bg-zinc-950/60 border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400'
                  }`}>
                    <div className="flex items-center gap-1.5 font-bold mb-1">
                      <span className={`w-3.5 h-3.5 rounded-none flex items-center justify-center text-[10px] font-black shrink-0 ${analysisStepIndex === 3 ? 'bg-emerald-600 dark:bg-emerald-500 text-white dark:text-black' : 'bg-zinc-300 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-300'}`}>3</span>
                      <span className={analysisStepIndex >= 3 ? 'text-emerald-950 dark:text-emerald-300 font-black' : 'text-zinc-600 dark:text-zinc-400'}>
                        {lang === 'en' ? 'Auto-Fill 26 Fields' : 'Isi 26 Parameter'}
                      </span>
                    </div>
                    <p className={`text-[11px] font-sans leading-snug ${analysisStepIndex >= 3 ? 'text-zinc-800 dark:text-zinc-300' : 'text-zinc-500 dark:text-zinc-500'}`}>
                      {lang === 'en'
                        ? 'Populating Blocks A-F: KPIs, MVP features, workflows, and scope.'
                        : 'Mengisi Blok A-F: Masalah, KPI, Fitur MVP, Alur Kerja, Integrasi, dan Scope.'}
                    </p>
                  </div>
                </div>

                {/* Footer notification */}
                <div className="mt-3 pt-2.5 border-t border-zinc-200 dark:border-zinc-800 flex items-center gap-2 text-[11px] text-zinc-800 dark:text-zinc-300 font-sans">
                  <Lightbulb className="w-3.5 h-3.5 text-amber-500 shrink-0" />
                  <span>
                    {lang === 'en'
                      ? 'Once complete, the page will automatically scroll down to Block A so you can inspect and adjust the results.'
                      : 'Setelah proses selesai, halaman akan otomatis bergulir ke Blok A di bawah untuk Anda tinjau dan sesuaikan.'}
                  </span>
                </div>
              </div>
            ) : (
              <div className="mt-2.5 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-sans flex items-start gap-2.5">
                <Lightbulb className="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                <div className="leading-relaxed">
                  <strong className="font-semibold text-zinc-900 dark:text-zinc-100">
                    {lang === 'en' ? 'How Auto-Fill Works: ' : 'Cara Kerja Tombol Sintesis: '}
                  </strong>
                  <span>
                    {lang === 'en'
                      ? 'Clicking this button triggers AI to analyze your idea and auto-populate all 26 parameters below (Blocks A through F). You retain 100% control to review, edit, or customize any field afterwards.'
                      : 'Menekan tombol di atas akan mengaktifkan AI untuk menganalisis narasi/dokumen Anda dan otomatis mengisi seluruh 26 kolom formulir di bawah (Blok A s/d Blok F). Anda tetap memiliki kendali penuh untuk meninjau, mengubah, atau menyempurnakan setiap kolom hasil sintesis.'}
                  </span>
                </div>
              </div>
            )}
          </div>
        )}
      </section>

      {/* =========================================================================
          SECTION 2: PROACTIVE AI ASSISTANT (ANTI-BONCOS FLASH CO-PILOT)
          ========================================================================= */}
      <section id="section-ai-assistant" className="scroll-mt-24 mb-8 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 sm:p-8 rounded-none">
        <div className="flex items-center gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-5">
          <div className="w-8 h-8 bg-emerald-500 text-black flex items-center justify-center font-bold">
            <Lightbulb className="w-4 h-4" />
          </div>
          <div>
            <span className="text-[10px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold block">
              {t.proactiveBadge}
            </span>
            <h3 className="text-base sm:text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-zinc-100">
              {t.proactiveTitle}
            </h3>
          </div>
        </div>

        <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-4">
          {t.proactiveDesc}
        </p>

        {/* 1-Click Suggestion Chips */}
        <div id="field-proactiveSuggestions" className="mb-6">
          <span className="block text-[11px] font-mono uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-2.5 font-bold">
            {t.suggestionsTitle}
          </span>
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
            {proactiveSuggestions.map((sug) => {
              const isPaymentSelector = sug.is_payment_selector || sug.id === 'payment_multi_rail' || sug.id.startsWith('payment_');
              const isApplied = appliedSuggestionIds.has(sug.id);
              const isDisabled = isApplied && !isPaymentSelector;

              return (
                <button
                  key={sug.id}
                  type="button"
                  onClick={() => handleApplySuggestion(sug)}
                  disabled={isDisabled}
                  className={`p-3 text-left border rounded-none transition flex flex-col justify-between ${
                    isApplied
                      ? isPaymentSelector
                        ? 'bg-emerald-500/10 border-emerald-500/50 text-emerald-950 dark:text-emerald-200 hover:border-emerald-600 cursor-pointer shadow-xs'
                        : 'bg-emerald-500/10 border-emerald-500/40 text-emerald-800 dark:text-emerald-300 opacity-90 cursor-default'
                      : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-300 dark:border-zinc-700 hover:border-emerald-500 text-zinc-800 dark:text-zinc-200 cursor-pointer'
                  }`}
                >
                  <div>
                    <div className="flex items-center justify-between gap-1 mb-1">
                      <span className="text-[10px] font-mono uppercase tracking-wider px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold">
                        {sug.badge}
                      </span>
                      {isApplied && (
                        <span className="text-[10px] font-mono text-emerald-800 dark:text-emerald-300 font-black flex items-center gap-0.5">
                          <Check className="w-3 h-3 stroke-[2.5]" />
                          {isPaymentSelector
                            ? (lang === 'en' ? `✓ Added (${selectedPaymentRailIds.size} Rails)` : `✓ Terpasang (${selectedPaymentRailIds.size} Rel)`)
                            : (lang === 'en' ? 'Added' : 'Ditambahkan')}
                        </span>
                      )}
                    </div>
                    <span className="text-xs font-bold block mb-1 font-sans text-zinc-950 dark:text-zinc-100">{sug.title}</span>
                    <span className="text-[11px] text-zinc-600 dark:text-zinc-400 block line-clamp-2 font-sans">{sug.desc}</span>
                  </div>
                  {!isApplied && (
                    <span className="text-[10px] font-mono text-emerald-800 dark:text-emerald-300 font-black mt-2 flex items-center gap-1">
                      <Plus className="w-3 h-3 stroke-[2.5]" /> {lang === 'en' ? 'Apply to Form' : 'Pasang ke Form'}
                    </span>
                  )}
                  {isApplied && isPaymentSelector && (
                    <span className="text-[10px] font-mono text-emerald-700 dark:text-emerald-400 font-bold mt-2 flex items-center gap-1 hover:underline">
                      ✎ {lang === 'en' ? 'Click to Reconfigure Rails' : 'Klik untuk Ubah / Tambah Rel'}
                    </span>
                  )}
                </button>
              );
            })}
          </div>
        </div>

        {/* Lightweight Co-Pilot Input Bar */}
        <form onSubmit={handleCoPilotSubmit} className="pt-4 border-t border-zinc-200 dark:border-zinc-800">
          <label className={labelClass}>
            {lang === 'en' ? 'Consult New Requirements with AI Assistant (Gemini Flash)' : 'Konsultasikan Kebutuhan Baru ke Asisten AI (Gemini Flash)'}
          </label>
          <div className="flex gap-2">
            <input
              type="text"
              value={coPilotInput}
              onChange={(e) => setCoPilotInput(e.target.value)}
              placeholder={t.coPilotPlaceholder}
              className="flex-1 px-4 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-sm outline-none focus:border-emerald-500 font-sans"
            />
            <button
              type="submit"
              disabled={isCoPilotLoading || !coPilotInput.trim()}
              className="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-black font-mono text-xs font-black uppercase tracking-wider rounded-none flex items-center gap-2 transition disabled:opacity-50 shrink-0"
            >
              {isCoPilotLoading ? (
                <>
                  <Loader2 className="w-3.5 h-3.5 animate-spin" />
                  <span>{t.coPilotProcessing}</span>
                </>
              ) : (
                <>
                  <Sparkles className="w-3.5 h-3.5" />
                  <span>{t.coPilotSubmitBtn}</span>
                </>
              )}
            </button>
          </div>
        </form>
      </section>
    </>
  );
}
