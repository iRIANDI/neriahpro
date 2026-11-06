import React from 'react';
import { X, Sparkles, CheckSquare, Layers } from 'lucide-react';

export default function BlueprintKpiCascadeModal({
  isOpen,
  onClose,
  kpiCascadeResult,
  activeKpiCascadeTab,
  setActiveKpiCascadeTab,
  selectedCascadeFeatures,
  setSelectedCascadeFeatures,
  selectedCascadeActors,
  setSelectedCascadeActors,
  includeCascadeGherkin,
  setIncludeCascadeGherkin,
  onApply,
  lang = 'id',
}) {
  if (!isOpen || !kpiCascadeResult) return null;

  return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 bg-black/75 backdrop-blur-xs overflow-y-auto">
          <div className="relative w-full max-w-4xl bg-white dark:bg-zinc-950 border-2 border-zinc-900 dark:border-zinc-700 shadow-2xl flex flex-col max-h-[90vh] my-auto rounded-none">
            {/* Modal Header */}
            <div className="p-4 bg-zinc-950 text-white border-b border-zinc-800 flex items-center justify-between">
              <div className="flex items-center gap-2.5">
                <div className="w-8 h-8 rounded-none bg-emerald-500/20 border border-emerald-400 flex items-center justify-center shrink-0">
                  <Sparkles className="w-4 h-4 text-emerald-400" />
                </div>
                <div>
                  <h3 className="font-mono text-xs sm:text-sm font-black uppercase tracking-tight text-white flex flex-wrap items-center gap-2">
                    <span>{lang === 'en' ? 'AI KPI Deep-Dive & Architectural Cascade Matrix' : 'Elaborasi KPI & Matriks Dampak Arsitektur Sistem'}</span>
                    <span className="px-1.5 py-0.5 bg-emerald-500 text-zinc-950 text-[9px] font-black uppercase">
                      {kpiCascadeResult.mode === 'ai_synthesis' ? 'MULTI-AI REASONING' : 'DETERMINISTIC HEURISTIC'}
                    </span>
                  </h3>
                  <p className="text-[11px] text-zinc-400 mt-0.5">
                    {lang === 'en'
                      ? 'Target KPIs are enriched with concrete baselines, targets & verifiers, directly mutating linked modules and RBAC actors.'
                      : 'Target KPI diperdalam dengan baseline, target numerik, dan verifier, serta otomatis berdampak pada modul fitur dan aktor sistem.'}
                  </p>
                </div>
              </div>
              <button
                type="button"
                onClick={() => onClose()}
                className="p-1.5 text-zinc-400 hover:text-white transition-colors cursor-pointer"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Sub-Tabs */}
            <div className="flex border-b border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-900 px-4 pt-2 gap-2 font-mono text-xs font-bold">
              <button
                type="button"
                onClick={() => setActiveKpiCascadeTab('kpis')}
                className={`px-3 py-2 border-b-2 transition-colors cursor-pointer ${
                  activeKpiCascadeTab === 'kpis'
                    ? 'border-emerald-600 dark:border-emerald-400 text-zinc-950 dark:text-white font-black bg-white dark:bg-zinc-950'
                    : 'border-transparent text-zinc-600 dark:text-zinc-400 hover:text-zinc-950'
                }`}
              >
                1. {lang === 'en' ? `Smart Quantitative KPIs (${(kpiCascadeResult.kpis || []).length})` : `Target KPI SMART (${(kpiCascadeResult.kpis || []).length})`}
              </button>
              <button
                type="button"
                onClick={() => setActiveKpiCascadeTab('cascades')}
                className={`px-3 py-2 border-b-2 transition-colors cursor-pointer ${
                  activeKpiCascadeTab === 'cascades'
                    ? 'border-emerald-600 dark:border-emerald-400 text-zinc-950 dark:text-white font-black bg-white dark:bg-zinc-950'
                    : 'border-transparent text-zinc-600 dark:text-zinc-400 hover:text-zinc-950'
                }`}
              >
                2. {lang === 'en' ? 'Cascading System Impacts (Modules, RBAC & UAT)' : 'Dampak ke Area Terkait (Modul, RBAC & UAT)'}
              </button>
            </div>

            {/* Modal Body */}
            <div className="p-4 overflow-y-auto space-y-4 flex-1 text-xs">
              {activeKpiCascadeTab === 'kpis' ? (
                <div className="space-y-3">
                  {(kpiCascadeResult.kpis || []).map((kpi, idx) => (
                    <div key={idx} className="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 space-y-2">
                      <div className="flex items-center justify-between gap-2">
                        <span className="font-mono text-xs font-black text-emerald-800 dark:text-emerald-400 uppercase">
                          KPI #{idx + 1}: {kpi.title}
                        </span>
                        <span className="px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-mono text-[9px] font-bold">
                          SMART METRIC
                        </span>
                      </div>
                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] font-mono">
                        <div className="p-2 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                          <span className="text-zinc-500 block text-[9px] uppercase font-bold">Baseline:</span>
                          <span className="text-zinc-800 dark:text-zinc-200 font-semibold">{kpi.baseline}</span>
                        </div>
                        <div className="p-2 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800">
                          <span className="text-emerald-900 dark:text-emerald-300 block text-[9px] uppercase font-bold">Target &amp; SLA:</span>
                          <span className="text-emerald-950 dark:text-emerald-100 font-bold">{kpi.target}</span>
                        </div>
                      </div>
                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] font-mono">
                        <div className="p-2 bg-zinc-100 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                          <span className="text-zinc-500 block text-[9px] uppercase font-bold">Indikator Terukur:</span>
                          <span className="text-zinc-800 dark:text-zinc-200">{kpi.metric}</span>
                        </div>
                        <div className="p-2 bg-zinc-100 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                          <span className="text-zinc-500 block text-[9px] uppercase font-bold">Instrumen Verifikasi:</span>
                          <span className="text-zinc-800 dark:text-zinc-200">{kpi.verification}</span>
                        </div>
                      </div>
                      <div className="p-2 bg-zinc-100 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-[11px]">
                        <span className="text-zinc-500 font-mono text-[9px] uppercase font-bold block mb-0.5">Teks Narasi Blueprint:</span>
                        <p className="font-sans text-zinc-900 dark:text-zinc-100 leading-relaxed font-semibold">{kpi.synthesized_text || kpi.title}</p>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="space-y-4">
                  {/* Features Cascade */}
                  <div>
                    <div className="font-mono text-xs font-black uppercase text-zinc-950 dark:text-zinc-100 mb-2 flex flex-wrap items-center justify-between gap-1">
                      <span>📦 Modul Fitur Arsitektur yang Diciptakan / Diperkaya</span>
                      <span className="text-[10px] text-zinc-500">Pilih modul yang akan dimasukkan ke Field 7 (Fitur Utama)</span>
                    </div>
                    <div className="space-y-2">
                      {(kpiCascadeResult.cascaded_impacts?.features || []).map((feat, idx) => {
                        const featKey = feat.code || idx;
                        const isChecked = !!selectedCascadeFeatures[featKey];
                        return (
                          <label
                            key={idx}
                            className={`flex items-start gap-2.5 p-3 border transition cursor-pointer ${
                              isChecked
                                ? 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-500 text-zinc-950 dark:text-zinc-100'
                                : 'bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 opacity-60 text-zinc-600 dark:text-zinc-400'
                            }`}
                          >
                            <input
                              type="checkbox"
                              checked={isChecked}
                              onChange={(e) => {
                                setSelectedCascadeFeatures((prev) => ({ ...prev, [featKey]: e.target.checked }));
                              }}
                              className="mt-0.5 rounded-none"
                            />
                            <div className="flex-1 min-w-0">
                              <div className="flex items-center gap-2">
                                <span className="font-mono text-[10px] font-black uppercase px-1.5 py-0.5 bg-zinc-900 text-white dark:bg-emerald-400 dark:text-zinc-950">
                                  {feat.code || `FEAT-0${idx + 1}`}
                                </span>
                                <span className="font-bold text-xs">{feat.title}</span>
                                <span className="text-[10px] text-zinc-500 font-mono">({feat.category || 'Core'})</span>
                              </div>
                              <p className="text-[11px] mt-1 text-zinc-700 dark:text-zinc-300">{feat.spec}</p>
                              <p className="text-[10px] text-emerald-800 dark:text-emerald-400 mt-1 font-mono italic">
                                ↳ Dampak: {feat.rationale}
                              </p>
                            </div>
                          </label>
                        );
                      })}
                    </div>
                  </div>

                  {/* Actors Cascade */}
                  <div>
                    <div className="font-mono text-xs font-black uppercase text-zinc-950 dark:text-zinc-100 mb-2">
                      👥 Matriks Wewenang &amp; Aktor Terkait (RBAC)
                    </div>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                      {(kpiCascadeResult.cascaded_impacts?.actors || []).map((actor, idx) => {
                        const actorKey = actor.role || idx;
                        const isChecked = !!selectedCascadeActors[actorKey];
                        return (
                          <label
                            key={idx}
                            className={`flex items-start gap-2 p-2.5 border transition cursor-pointer ${
                              isChecked
                                ? 'bg-zinc-50 dark:bg-zinc-900 border-zinc-900 dark:border-emerald-400'
                                : 'bg-zinc-100 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 opacity-60'
                            }`}
                          >
                            <input
                              type="checkbox"
                              checked={isChecked}
                              onChange={(e) => {
                                setSelectedCascadeActors((prev) => ({ ...prev, [actorKey]: e.target.checked }));
                              }}
                              className="mt-0.5 rounded-none"
                            />
                            <div>
                              <div className="font-black text-xs text-zinc-950 dark:text-zinc-100">{actor.role}</div>
                              <div className="text-[10px] text-zinc-600 dark:text-zinc-400 mt-0.5">{actor.access}</div>
                            </div>
                          </label>
                        );
                      })}
                    </div>
                  </div>

                  {/* UAT Scenario */}
                  {kpiCascadeResult.cascaded_impacts?.acceptance_criteria && (
                    <div>
                      <div className="font-mono text-xs font-black uppercase text-zinc-950 dark:text-zinc-100 mb-1 flex items-center justify-between">
                        <span>🧪 Skenario Kriteria Penerimaan (Gherkin UAT)</span>
                        <label className="flex items-center gap-1.5 cursor-pointer text-[10px] font-mono">
                          <input
                            type="checkbox"
                            checked={includeCascadeGherkin}
                            onChange={(e) => setIncludeCascadeGherkin(e.target.checked)}
                            className="rounded-none"
                          />
                          <span>Sertakan ke Skenario Gherkin</span>
                        </label>
                      </div>
                      <pre className="p-3 bg-zinc-900 text-emerald-400 font-mono text-[10px] leading-relaxed overflow-x-auto border border-zinc-800 whitespace-pre-wrap">
                        {kpiCascadeResult.cascaded_impacts.acceptance_criteria}
                      </pre>
                    </div>
                  )}

                  {/* ROI Summary */}
                  {kpiCascadeResult.cascaded_impacts?.roi_summary && (
                    <div className="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-400 dark:border-amber-700 text-amber-950 dark:text-amber-100 font-mono text-[11px]">
                      <strong className="block text-[10px] font-black uppercase mb-0.5">💰 Proyeksi Nilai Bisnis &amp; Efisiensi Finansial:</strong>
                      {kpiCascadeResult.cascaded_impacts.roi_summary}
                    </div>
                  )}
                </div>
              )}
            </div>

            {/* Modal Footer */}
            <div className="p-3 bg-zinc-100 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 flex flex-wrap items-center justify-between gap-2">
              <button
                type="button"
                onClick={() => onClose()}
                className="px-3 py-1.5 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 font-mono text-xs font-bold uppercase transition-colors cursor-pointer"
              >
                {lang === 'en' ? 'Cancel' : 'Tutup'}
              </button>

              <button
                type="button"
                onClick={onApply}
                className="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-mono text-xs font-black uppercase transition-colors cursor-pointer shadow-sm flex items-center gap-1.5"
              >
                <Sparkles className="w-3.5 h-3.5" />
                <span>{lang === 'en' ? 'Apply KPIs & Synchronize Architecture' : 'Terapkan KPI & Sinkronkan Arsitektur'}</span>
              </button>
            </div>
          </div>
        </div>
  );
}
