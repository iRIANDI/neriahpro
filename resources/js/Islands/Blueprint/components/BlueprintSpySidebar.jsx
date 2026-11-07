import React from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
  Compass,
  Search,
  X,
  ChevronDown,
  ChevronRight,
  ChevronLeft,
  Check,
  RotateCcw,
  AlertCircle,
  ArrowUp,
} from 'lucide-react';

export default function BlueprintSpySidebar({
  isIndexExpanded,
  setIsIndexExpanded,
  mobileDrawerOpen,
  setMobileDrawerOpen,
  indexTopOffset,
  indexSections,
  openAccordionGroups,
  toggleAccordionGroup,
  expandedSubBlocks,
  toggleSubBlock,
  isSubBlockOpen = () => false,
  expandAllGroups,
  collapseAllGroups,
  activeSection,
  activeField,
  invalidFields = [],
  indexSearchQuery,
  setIndexSearchQuery,
  jumpToSection,
  jumpToField,
  desktopScrollRef,
  completeness,
  lang = 'id',
  t,
  autoSyncAccordion = true,
  setAutoSyncAccordion = () => {},
  toggleAllSubBlocks = () => {},
  isAllSubExpanded = false,
  handleResetForm = () => {},
  accordionGroups = [],
  filteredIndexSections = [],
  getActiveSectionTitle = () => '',
}) {
  return (
    <>
      {/* 1. DESKTOP FLOATING LEFT SIDEBAR / RAIL (xl:flex) */}
      <aside
        aria-label="Blueprint Index Navigation"
        style={{
          top: `${indexTopOffset}px`,
          height: `calc(100vh - ${indexTopOffset + 16}px)`,
          maxHeight: `calc(100vh - ${indexTopOffset + 16}px)`
        }}
        className="fixed left-3 2xl:left-6 z-40 hidden xl:flex flex-col font-mono select-none transition-[top] duration-75 min-h-0"
      >
        {isIndexExpanded ? (
          /* EXPANDED DIRECTORY PANEL */
          <div
            onWheel={(e) => {
              if (desktopScrollRef.current) {
                const el = desktopScrollRef.current;
                const canScrollUp = el.scrollTop > 0;
                const canScrollDown = el.scrollTop < (el.scrollHeight - el.clientHeight - 1);
                if ((e.deltaY > 0 && canScrollDown) || (e.deltaY < 0 && canScrollUp)) {
                  el.scrollTop += e.deltaY;
                }
              }
            }}
            className="w-72 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col h-full max-h-full min-h-0 rounded-none overflow-hidden transition-all duration-200"
          >
            {/* Header */}
            <div className="p-3 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/80 dark:bg-zinc-950/80 shrink-0">
              <div className="flex items-center gap-2">
                <Compass className="w-4 h-4 text-emerald-500" />
                <div>
                  <div className="flex items-center gap-1.5">
                    <span className="text-[10px] font-mono font-black uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
                      {t.indexTitle}
                    </span>
                    <button
                      type="button"
                      onClick={() => setAutoSyncAccordion(!autoSyncAccordion)}
                      className={`text-[9px] px-1.5 py-0.2 font-mono border font-bold transition rounded-none ${autoSyncAccordion
                        ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30'
                        : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 border-zinc-300 dark:border-zinc-700'
                        }`}
                      title={autoSyncAccordion ? 'Auto-Collapse SPY Active (Click to Pause)' : 'Auto-Collapse Paused (Click to Enable)'}
                    >
                      {autoSyncAccordion ? t.indexSpyActive : t.indexSpyManual}
                    </button>
                  </div>
                </div>
              </div>
              <div className="flex items-center gap-1">
                <button
                  type="button"
                  onClick={() => setIsIndexExpanded(false)}
                  className="p-1 text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition rounded-none"
                  title={t.indexHidePanel}
                >
                  <ChevronLeft className="w-4 h-4" />
                </button>
              </div>
            </div>

            {/* Sub-header / Search & Quick Controls */}
            <div className="p-2 border-b border-zinc-200 dark:border-zinc-800 space-y-1.5 bg-zinc-50/40 dark:bg-zinc-950/40 shrink-0">
              {/* Search Bar */}
              <div className="relative flex items-center">
                <Search className="w-3.5 h-3.5 absolute left-2 text-zinc-400 pointer-events-none" />
                <input
                  type="text"
                  value={indexSearchQuery}
                  onChange={(e) => setIndexSearchQuery(e.target.value)}
                  placeholder={t.indexSearchPlaceholder}
                  className="w-full pl-7 pr-6 py-1 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-[11px] text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-emerald-500 font-sans"
                />
                {indexSearchQuery && (
                  <button
                    type="button"
                    onClick={() => setIndexSearchQuery('')}
                    className="absolute right-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
                  >
                    <X className="w-3 h-3" />
                  </button>
                )}
              </div>

              {/* Group Toggle & Top Jumper Toolbar */}
              <div className="flex items-center justify-between text-[10px] text-zinc-500 dark:text-zinc-400 px-0.5 pt-0.5">
                <div className="flex items-center gap-1.5 flex-wrap">
                  <button
                    type="button"
                    onClick={expandAllGroups}
                    className="hover:text-emerald-600 dark:hover:text-emerald-400 transition underline underline-offset-2"
                  >
                    {t.indexExpandAll}
                  </button>
                  <span>·</span>
                  <button
                    type="button"
                    onClick={collapseAllGroups}
                    className="hover:text-emerald-600 dark:hover:text-emerald-400 transition underline underline-offset-2"
                  >
                    {t.indexCollapseAll}
                  </button>
                  <span>·</span>
                  <button
                    type="button"
                    onClick={toggleAllSubBlocks}
                    className="hover:text-emerald-600 dark:hover:text-emerald-400 transition font-bold text-emerald-600 dark:text-emerald-400"
                    title={t.indexToggleDetails}
                  >
                    {isAllSubExpanded ? t.indexCollapseDetails : t.indexExpandDetails}
                  </button>
                  <span>·</span>
                  <button
                    type="button"
                    onClick={handleResetForm}
                    className="hover:text-red-600 dark:hover:text-red-400 text-red-500 font-bold transition flex items-center gap-0.5 cursor-pointer"
                    title={lang === 'en' ? 'Reset form to blank' : 'Bersihkan seluruh isi formulir'}
                  >
                    <RotateCcw className="w-2.5 h-2.5" />
                    <span>Reset</span>
                  </button>
                </div>
                <button
                  type="button"
                  onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}
                  className="flex items-center gap-0.5 hover:text-emerald-600 dark:hover:text-emerald-400 transition shrink-0"
                  title="Scroll to top"
                >
                  <ArrowUp className="w-3 h-3" />
                  <span>{t.indexBackToTop}</span>
                </button>
              </div>
            </div>

            {/* Accordion List Body with Custom Scrollbar & Contain Behavior */}
            <div
              ref={desktopScrollRef}
              className="flex-1 min-h-0 overflow-y-auto px-2 py-2 space-y-2 text-xs divide-y divide-zinc-100 dark:divide-zinc-800/60 custom-prd-scrollbar select-none focus:outline-none"
              style={{ overscrollBehavior: 'contain' }}
            >
              {accordionGroups.map((grp) => {
                const groupSections = filteredIndexSections.filter(sec => sec.group === grp.key);
                if (groupSections.length === 0) return null;
                const isOpen = openAccordionGroups[grp.key] ?? true;
                const isGroupActive = groupSections.some(sec => sec.id === activeSection);
                const grpHasInvalid = groupSections.some(sec =>
                  sec.subItems?.some(sub => {
                    const cleanSub = sub.id.replace(/^field-/, '');
                    return invalidFields.some(inv => inv.replace(/^field-/, '') === cleanSub || inv === sub.id);
                  })
                );

                return (
                  <div key={grp.key} className="pt-2 first:pt-0">
                    {/* Accordion Group Header */}
                    <button
                      type="button"
                      onClick={() => toggleAccordionGroup(grp.key)}
                      className={`w-full flex items-center justify-between py-1 text-left hover:text-zinc-800 dark:hover:text-zinc-200 group font-bold tracking-wider text-[10px] uppercase transition ${grpHasInvalid
                        ? 'text-rose-600 dark:text-rose-400 font-black'
                        : isGroupActive
                          ? 'text-emerald-600 dark:text-emerald-400 font-black'
                          : 'text-zinc-500 dark:text-zinc-400'
                        }`}
                    >
                      <span className="flex items-center gap-1.5 truncate">
                        {isOpen ? <ChevronDown className="w-3 h-3 shrink-0" /> : <ChevronRight className="w-3 h-3 shrink-0" />}
                        <span className="truncate">{grp.title}</span>
                        {grpHasInvalid ? (
                          <span className="w-1.5 h-1.5 bg-rose-600 rounded-none shrink-0 animate-ping" title="Needs Attention"></span>
                        ) : isGroupActive ? (
                          <span className="w-1.5 h-1.5 bg-emerald-500 rounded-none shrink-0" title="Active Group"></span>
                        ) : null}
                      </span>
                      {grpHasInvalid ? (
                        <span className="text-[9px] px-1.5 py-0.2 border shrink-0 bg-rose-600 text-white font-black border-rose-700 animate-pulse">
                          {lang === 'en' ? 'INCOMPLETE' : 'WAJIB ISI'}
                        </span>
                      ) : (
                        <span className={`text-[9px] px-1 py-0.2 border shrink-0 font-normal ${isGroupActive
                          ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-bold'
                          : 'bg-zinc-100 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400'
                          }`}>
                          {groupSections.length}
                        </span>
                      )}
                    </button>

                    {/* Group Items */}
                    {isOpen && (
                      <div className="mt-1 space-y-1 pl-1">
                        {groupSections.map((sec) => {
                          const isActive = activeSection === sec.id;
                          const isSubOpen = isSubBlockOpen(sec.id);
                          const hasSubItems = sec.subItems && sec.subItems.length > 0;
                          const secHasInvalid = hasSubItems && sec.subItems.some(sub => {
                            const cleanSub = sub.id.replace(/^field-/, '');
                            return invalidFields.some(inv => inv.replace(/^field-/, '') === cleanSub || inv === sub.id);
                          });

                          return (
                            <div key={sec.id} className="space-y-0.5">
                              <div
                                data-nav-sec={sec.id}
                                onClick={() => jumpToSection(sec.id)}
                                className={`w-full text-left p-1.5 flex items-center justify-between gap-1.5 border transition-all cursor-pointer ${secHasInvalid
                                  ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-500 text-rose-800 dark:text-rose-200 font-bold shadow-xs'
                                  : isActive
                                    ? 'bg-emerald-500/10 border-emerald-500/50 text-emerald-700 dark:text-emerald-300 font-bold shadow-xs'
                                    : 'bg-transparent border-transparent hover:bg-zinc-100 dark:hover:bg-zinc-800/60 text-zinc-700 dark:text-zinc-300'
                                  }`}
                              >
                                <div className="flex items-center gap-2 truncate">
                                  <span className={`w-5 h-5 flex items-center justify-center text-[10px] font-mono shrink-0 border ${secHasInvalid
                                    ? 'bg-rose-600 text-white border-rose-600 font-bold'
                                    : isActive
                                      ? 'bg-emerald-500 text-black border-emerald-500 font-bold'
                                      : sec.isComplete
                                        ? 'bg-emerald-500 text-black border-emerald-500 font-bold'
                                        : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 border-zinc-200 dark:border-zinc-700'
                                    }`}>
                                    {secHasInvalid ? (
                                      <AlertCircle className="w-3 h-3 text-white stroke-[2.5]" />
                                    ) : sec.isComplete ? (
                                      <Check className="w-3 h-3 stroke-[3]" />
                                    ) : (
                                      sec.badge
                                    )}
                                  </span>
                                  <div className="truncate">
                                    <div className="text-[11px] truncate leading-tight font-sans">
                                      {sec.title}
                                    </div>
                                  </div>
                                </div>

                                <div className="flex items-center gap-1 shrink-0">
                                  {secHasInvalid ? (
                                    <span className="text-[9px] font-mono px-1 py-0.2 border border-rose-500/50 text-rose-700 dark:text-rose-300 bg-rose-500/15 font-black uppercase">
                                      {lang === 'en' ? 'INCOMPLETE' : 'BELUM LENGKAP'}
                                    </span>
                                  ) : sec.countLabel && (
                                    <span className={`text-[9px] font-mono px-1 py-0.2 border ${sec.isComplete
                                      ? 'border-emerald-500/40 text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 font-bold'
                                      : 'border-zinc-200 dark:border-zinc-700 text-zinc-500 bg-zinc-50 dark:bg-zinc-800'
                                      }`}>
                                      {sec.countLabel}
                                    </span>
                                  )}

                                  {hasSubItems && (
                                    <button
                                      type="button"
                                      onClick={(e) => toggleSubBlock(sec.id, e)}
                                      className="p-0.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition rounded-none"
                                      title={isSubOpen ? t.indexCollapseDetails : t.indexExpandDetails}
                                    >
                                      {isSubOpen ? (
                                        <ChevronDown className="w-3 h-3" />
                                      ) : (
                                        <ChevronRight className="w-3 h-3" />
                                      )}
                                    </button>
                                  )}

                                  {isActive && !secHasInvalid && (
                                    <span className="relative flex h-1.5 w-1.5 ml-0.5">
                                      <span className="animate-ping absolute inline-flex h-full w-full rounded-none bg-emerald-400 opacity-75"></span>
                                      <span className="relative inline-flex rounded-none h-1.5 w-1.5 bg-emerald-500"></span>
                                    </span>
                                  )}
                                </div>
                              </div>

                              {/* Sub-Items List when Expanded */}
                              {isSubOpen && hasSubItems && (
                                <div className="ml-3 pl-2 border-l border-zinc-200 dark:border-zinc-800/80 space-y-0.5 py-0.5">
                                  {sec.subItems.map((sub) => {
                                    const cleanSub = sub.id.replace(/^field-/, '');
                                    const isInvalid = invalidFields.some(inv => inv.replace(/^field-/, '') === cleanSub || inv === sub.id);
                                    const isFieldActive = activeField === sub.id || activeField === `field-${cleanSub}`;

                                    return (
                                      <button
                                        key={sub.id}
                                        data-nav-field={sub.id}
                                        type="button"
                                        onClick={(e) => {
                                          e.stopPropagation();
                                          jumpToField(sub.id, sec.id);
                                        }}
                                        className={`w-full text-left py-1 px-1.5 flex items-center justify-between gap-1 text-[10px] transition ${isInvalid
                                          ? 'bg-rose-500/15 text-rose-800 dark:text-rose-200 font-bold border-l-2 border-rose-600 animate-pulse'
                                          : isFieldActive
                                            ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-bold border-l-2 border-emerald-500'
                                            : sub.isFilled
                                              ? 'text-zinc-800 dark:text-zinc-200 hover:bg-emerald-500/10 hover:text-emerald-700 dark:hover:text-emerald-300'
                                              : 'text-zinc-400 dark:text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800/60 hover:text-zinc-700 dark:hover:text-zinc-300'
                                          }`}
                                      >
                                        <div className="flex items-center gap-1.5 truncate">
                                          {isInvalid ? (
                                            <span className="w-3.5 h-3.5 shrink-0 flex items-center justify-center bg-rose-600 text-white font-bold">
                                              <AlertCircle className="w-2.5 h-2.5 stroke-[2.5]" />
                                            </span>
                                          ) : sub.isFilled ? (
                                            <span className="w-3.5 h-3.5 shrink-0 flex items-center justify-center bg-emerald-500 text-black">
                                              <Check className="w-2.5 h-2.5 stroke-[3]" />
                                            </span>
                                          ) : (
                                            <span className="w-3.5 h-3.5 shrink-0 flex items-center justify-center border border-zinc-300 dark:border-zinc-700 text-zinc-400 text-[8px] font-mono">
                                              ○
                                            </span>
                                          )}
                                          <span className={`font-mono text-[9px] shrink-0 ${isInvalid ? 'text-rose-700 dark:text-rose-300 font-bold' : 'text-zinc-400'}`}>{sub.code}</span>
                                          <span className={`truncate font-sans ${isInvalid ? 'font-black text-rose-900 dark:text-rose-100' : sub.isFilled ? 'font-medium' : ''}`}>
                                            {sub.label}
                                          </span>
                                        </div>

                                        <span className={`text-[8px] font-mono shrink-0 px-1 py-0.2 border uppercase ${isInvalid
                                          ? 'bg-rose-600 text-white font-black border-rose-700'
                                          : sub.isFilled
                                            ? 'bg-emerald-500/15 border-emerald-500/40 text-emerald-600 dark:text-emerald-400 font-bold'
                                            : 'bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-400'
                                          }`}>
                                          {isInvalid
                                            ? (lang === 'en' ? '! REQUIRED' : '! WAJIB ISI')
                                            : sub.isFilled
                                              ? (lang === 'en' ? '✓ Filled' : '✓ Terisi')
                                              : (lang === 'en' ? 'Empty' : 'Kosong')}
                                        </span>
                                      </button>
                                    );
                                  })}
                                </div>
                              )}
                            </div>
                          );
                        })}
                      </div>
                    )}
                  </div>
                );
              })}
            </div>

            {/* Footer Telemetry - Anchored cleanly at bottom */}
            <div className="p-2.5 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-950/80 shrink-0">
              <div className="flex items-center justify-between text-[10px] mb-1">
                <span className="text-zinc-500 uppercase tracking-wider">{lang === 'en' ? 'Readiness' : 'Kesiapan'}:</span>
                <span className="font-bold text-emerald-600 dark:text-emerald-400">{completeness.score}%</span>
              </div>
              <div className="w-full bg-zinc-200 dark:bg-zinc-800 h-1.5 rounded-none overflow-hidden">
                <div
                  className="bg-emerald-500 h-full transition-all duration-300"
                  style={{ width: `${completeness.score}%` }}
                />
              </div>
              <div className="mt-1.5 text-[9px] text-zinc-400 dark:text-zinc-500 flex items-center justify-between font-mono">
                <span className="truncate max-w-[130px]">{getActiveSectionTitle()}</span>
                <span className="text-emerald-500 font-bold uppercase">{t.indexActiveStatus}</span>
              </div>
            </div>
          </div>
        ) : (
          /* COLLAPSED RAIL MODE */
          <div className="w-12 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 shadow-xl flex flex-col items-center py-2.5 rounded-none transition-all duration-200 min-h-0 h-full">
            {/* Expand Trigger */}
            <button
              type="button"
              onClick={() => setIsIndexExpanded(true)}
              className="p-1.5 text-zinc-500 hover:text-emerald-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition mb-2 rounded-none"
              title={t.indexShowPanel}
            >
              <ChevronRight className="w-4 h-4 text-emerald-500" />
            </button>

            <div className="w-6 h-[1px] bg-zinc-200 dark:bg-zinc-800 mb-2 shrink-0" />

            {/* Vertical Icons List with scrollbar */}
            <div className="flex flex-col gap-1 w-full px-1.5 flex-1 min-h-0 overflow-y-auto custom-prd-scrollbar">
              {indexSections.map((sec) => {
                const isActive = activeSection === sec.id;
                return (
                  <button
                    key={sec.id}
                    type="button"
                    onClick={() => jumpToSection(sec.id)}
                    title={`${sec.badge} - ${sec.title}`}
                    className={`w-full aspect-square flex items-center justify-center text-[11px] font-mono transition border ${isActive
                      ? 'bg-emerald-500 text-black border-emerald-500 font-bold shadow-xs scale-105'
                      : sec.isComplete
                        ? 'bg-emerald-500 text-black border-emerald-500 font-bold hover:bg-emerald-400'
                        : 'bg-zinc-50 dark:bg-zinc-950 text-zinc-500 border-zinc-200 dark:border-zinc-800 hover:border-emerald-500/50 hover:text-zinc-900 dark:hover:text-zinc-100'
                      }`}
                  >
                    {sec.isComplete ? <Check className="w-3.5 h-3.5 stroke-[3]" /> : sec.badge}
                  </button>
                );
              })}
            </div>

            <div className="w-6 h-[1px] bg-zinc-200 dark:bg-zinc-800 my-2 shrink-0" />

            {/* Mini Progress Percentage Pill */}
            <div className="text-[9px] font-mono font-bold text-emerald-600 dark:text-emerald-400 px-1 py-0.5 bg-emerald-500/10 border border-emerald-500/30 shrink-0">
              {completeness.score}%
            </div>
          </div>
        )}
      </aside>

      {/* 2. MOBILE / TABLET FLOATING BUTTON & SLIDE-OVER DRAWER (xl:hidden) */}
      <div className="xl:hidden">
        {/* Floating Trigger Badge on Bottom Left */}
        <button
          type="button"
          onClick={() => setMobileDrawerOpen(true)}
          className="fixed left-3 bottom-5 z-40 bg-zinc-950/90 text-white border border-zinc-700/80 shadow-2xl px-3 py-2 flex items-center gap-2 font-mono text-xs font-bold rounded-none hover:border-emerald-500 transition-all backdrop-blur-md active:scale-95 group"
          aria-label="Open Blueprint Section Directory"
        >
          <Compass className="w-4 h-4 text-emerald-400 group-hover:rotate-45 transition-transform" />
          <span className="uppercase tracking-wider">INDEX</span>
          <span className="px-1.5 py-0.2 bg-emerald-500 text-black text-[10px] font-mono font-bold">
            {completeness.score}%
          </span>
          <span className="text-[10px] text-zinc-400 hidden sm:inline truncate max-w-[120px]">
            {getActiveSectionTitle()}
          </span>
        </button>

        {/* Mobile Slide-over Drawer Modal */}
        <AnimatePresence>
          {mobileDrawerOpen && (
            <>
              {/* Backdrop */}
              <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                onClick={() => setMobileDrawerOpen(false)}
                className="fixed inset-0 bg-black/60 backdrop-blur-xs z-50"
              />

              {/* Drawer Sheet */}
              <motion.div
                initial={{ x: '-100%' }}
                animate={{ x: 0 }}
                exit={{ x: '-100%' }}
                transition={{ type: 'tween', duration: 0.25 }}
                className="fixed inset-y-0 left-0 w-80 max-w-[85vw] bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800 z-50 flex flex-col font-mono text-xs shadow-2xl"
              >
                {/* Drawer Header */}
                <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-950 shrink-0">
                  <div className="flex items-center gap-2">
                    <Compass className="w-4 h-4 text-emerald-500" />
                    <span className="font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
                      {t.indexTitle}
                    </span>
                    <button
                      type="button"
                      onClick={() => setAutoSyncAccordion(!autoSyncAccordion)}
                      className={`text-[9px] px-1.5 py-0.2 font-mono border font-bold transition rounded-none ${autoSyncAccordion
                        ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30'
                        : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 border-zinc-300 dark:border-zinc-700'
                        }`}
                      title={autoSyncAccordion ? 'Auto-Collapse SPY Active' : 'Auto-Collapse Paused'}
                    >
                      {autoSyncAccordion ? t.indexSpyActive : t.indexSpyManual}
                    </button>
                  </div>
                  <button
                    type="button"
                    onClick={() => setMobileDrawerOpen(false)}
                    className="p-1.5 text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 rounded-none"
                  >
                    <X className="w-4 h-4" />
                  </button>
                </div>

                {/* Search Bar in Mobile Drawer */}
                <div className="p-3 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/50 space-y-2 shrink-0">
                  <div className="relative flex items-center">
                    <Search className="w-3.5 h-3.5 absolute left-2.5 text-zinc-400 pointer-events-none" />
                    <input
                      type="text"
                      value={indexSearchQuery}
                      onChange={(e) => setIndexSearchQuery(e.target.value)}
                      placeholder={t.indexSearchPlaceholder}
                      className="w-full pl-8 pr-6 py-1.5 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-emerald-500 font-sans"
                    />
                    {indexSearchQuery && (
                      <button
                        type="button"
                        onClick={() => setIndexSearchQuery('')}
                        className="absolute right-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
                      >
                        <X className="w-3.5 h-3.5" />
                      </button>
                    )}
                  </div>
                  {/* Mobile Details Toggle */}
                  <div className="flex items-center justify-between text-[10px] text-zinc-500">
                    <button
                      type="button"
                      onClick={toggleAllSubBlocks}
                      className="hover:text-emerald-500 transition font-bold text-emerald-600 dark:text-emerald-400"
                    >
                      {isAllSubExpanded ? t.indexCollapseDetails : t.indexExpandDetails}
                    </button>
                    <button
                      type="button"
                      onClick={() => {
                        setMobileDrawerOpen(false);
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                      }}
                      className="hover:text-emerald-500 transition flex items-center gap-1"
                    >
                      <ArrowUp className="w-3 h-3" />
                      <span>{t.indexBackToTop}</span>
                    </button>
                  </div>
                </div>

                {/* Drawer Scrollable Body with Custom Scrollbar */}
                <div className="flex-1 min-h-0 overflow-y-auto p-3 space-y-3 custom-prd-scrollbar">
                  {accordionGroups.map((grp) => {
                    const groupSections = filteredIndexSections.filter(sec => sec.group === grp.key);
                    if (groupSections.length === 0) return null;
                    const isOpen = openAccordionGroups[grp.key] ?? true;
                    const isGroupActive = groupSections.some(sec => sec.id === activeSection);
                    const grpHasInvalid = groupSections.some(sec =>
                      sec.subItems?.some(sub => {
                        const cleanSub = sub.id.replace(/^field-/, '');
                        return invalidFields.some(inv => inv.replace(/^field-/, '') === cleanSub || inv === sub.id);
                      })
                    );

                    return (
                      <div key={grp.key} className="space-y-1">
                        <button
                          type="button"
                          onClick={() => toggleAccordionGroup(grp.key)}
                          className={`w-full flex items-center justify-between py-1 text-left font-bold uppercase text-[10px] tracking-wider transition ${grpHasInvalid
                            ? 'text-rose-600 dark:text-rose-400 font-black'
                            : isGroupActive
                              ? 'text-emerald-600 dark:text-emerald-400 font-black'
                              : 'text-zinc-500 dark:text-zinc-400'
                            }`}
                        >
                          <span className="flex items-center gap-1.5 truncate">
                            {isOpen ? <ChevronDown className="w-3 h-3 shrink-0" /> : <ChevronRight className="w-3 h-3 shrink-0" />}
                            <span className="truncate">{grp.title}</span>
                            {grpHasInvalid ? (
                              <span className="w-1.5 h-1.5 bg-rose-600 rounded-none shrink-0 animate-ping" title="Needs Attention"></span>
                            ) : isGroupActive ? (
                              <span className="w-1.5 h-1.5 bg-emerald-500 rounded-none shrink-0" title="Active Group"></span>
                            ) : null}
                          </span>
                          {grpHasInvalid ? (
                            <span className="text-[9px] px-1.5 py-0.2 border shrink-0 bg-rose-600 text-white font-black border-rose-700 animate-pulse">
                              {lang === 'en' ? 'INCOMPLETE' : 'WAJIB ISI'}
                            </span>
                          ) : (
                            <span className={`text-[9px] px-1 border shrink-0 ${isGroupActive
                              ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-bold'
                              : 'bg-zinc-100 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400'
                              }`}>
                              {groupSections.length}
                            </span>
                          )}
                        </button>

                        {isOpen && (
                          <div className="space-y-1 pl-1">
                            {groupSections.map((sec) => {
                              const isActive = activeSection === sec.id;
                              const isSubOpen = isSubBlockOpen(sec.id);
                              const hasSubItems = sec.subItems && sec.subItems.length > 0;
                              const secHasInvalid = hasSubItems && sec.subItems.some(sub => {
                                const cleanSub = sub.id.replace(/^field-/, '');
                                return invalidFields.some(inv => inv.replace(/^field-/, '') === cleanSub || inv === sub.id);
                              });

                              return (
                                <div key={sec.id} className="space-y-0.5">
                                  <div
                                    data-nav-sec={sec.id}
                                    onClick={() => jumpToSection(sec.id)}
                                    className={`w-full text-left p-2 flex items-center justify-between border transition cursor-pointer ${secHasInvalid
                                      ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-500 text-rose-800 dark:text-rose-200 font-bold shadow-xs'
                                      : isActive
                                        ? 'bg-emerald-500/10 border-emerald-500/60 text-emerald-600 dark:text-emerald-400 font-bold'
                                        : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200'
                                      }`}
                                  >
                                    <div className="flex items-center gap-2 truncate">
                                      <span className={`w-5 h-5 flex items-center justify-center text-[10px] font-mono shrink-0 border ${secHasInvalid
                                        ? 'bg-rose-600 text-white border-rose-600 font-bold'
                                        : isActive
                                          ? 'bg-emerald-500 text-black border-emerald-500 font-bold'
                                          : sec.isComplete
                                            ? 'bg-emerald-500 text-black border-emerald-500 font-bold'
                                            : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700'
                                        }`}>
                                        {secHasInvalid ? (
                                          <AlertCircle className="w-3 h-3 text-white stroke-[2.5]" />
                                        ) : sec.isComplete ? (
                                          <Check className="w-3 h-3 stroke-[3]" />
                                        ) : (
                                          sec.badge
                                        )}
                                      </span>
                                      <span className="truncate text-xs font-sans">{sec.title}</span>
                                    </div>
                                    <div className="flex items-center gap-1 shrink-0">
                                      {secHasInvalid ? (
                                        <span className="text-[9px] font-mono px-1 py-0.2 border border-rose-500/50 text-rose-700 dark:text-rose-300 bg-rose-500/15 font-black uppercase">
                                          {lang === 'en' ? 'INCOMPLETE' : 'BELUM LENGKAP'}
                                        </span>
                                      ) : sec.countLabel && (
                                        <span className={`text-[10px] font-mono px-1 py-0.2 border ${sec.isComplete
                                          ? 'border-emerald-500/40 text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 font-bold'
                                          : 'border-zinc-300 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'
                                          }`}>
                                          {sec.countLabel}
                                        </span>
                                      )}
                                      {hasSubItems && (
                                        <button
                                          type="button"
                                          onClick={(e) => toggleSubBlock(sec.id, e)}
                                          className="p-0.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-none"
                                        >
                                          {isSubOpen ? <ChevronDown className="w-3 h-3" /> : <ChevronRight className="w-3 h-3" />}
                                        </button>
                                      )}
                                    </div>
                                  </div>

                                  {/* Sub-items in Mobile Drawer */}
                                  {isSubOpen && hasSubItems && (
                                    <div className="ml-3 pl-2 border-l border-zinc-200 dark:border-zinc-800 space-y-1 py-1">
                                      {sec.subItems.map((sub) => {
                                        const cleanSub = sub.id.replace(/^field-/, '');
                                        const isInvalid = invalidFields.some(inv => inv.replace(/^field-/, '') === cleanSub || inv === sub.id);
                                        const isFieldActive = activeField === sub.id || activeField === `field-${cleanSub}`;

                                        return (
                                          <button
                                            key={sub.id}
                                            data-nav-field={sub.id}
                                            type="button"
                                            onClick={(e) => {
                                              e.stopPropagation();
                                              jumpToField(sub.id, sec.id);
                                            }}
                                            className={`w-full text-left py-1 px-1.5 flex items-center justify-between gap-1 text-[11px] transition ${isInvalid
                                              ? 'bg-rose-500/15 text-rose-800 dark:text-rose-200 font-bold border-l-2 border-rose-600 animate-pulse'
                                              : isFieldActive
                                                ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold border-l-2 border-emerald-500'
                                                : sub.isFilled
                                                  ? 'text-zinc-800 dark:text-zinc-200 hover:text-emerald-500 font-medium'
                                                  : 'text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'
                                              }`}
                                          >
                                            <div className="flex items-center gap-1.5 truncate">
                                              {isInvalid ? (
                                                <span className="w-3.5 h-3.5 shrink-0 flex items-center justify-center bg-rose-600 text-white font-bold">
                                                  <AlertCircle className="w-2.5 h-2.5 stroke-[2.5]" />
                                                </span>
                                              ) : sub.isFilled ? (
                                                <span className="w-3.5 h-3.5 shrink-0 flex items-center justify-center bg-emerald-500 text-black">
                                                  <Check className="w-2.5 h-2.5 stroke-[3]" />
                                                </span>
                                              ) : (
                                                <span className="w-3.5 h-3.5 shrink-0 flex items-center justify-center border border-zinc-400 text-zinc-400 text-[8px] font-mono">
                                                  ○
                                                </span>
                                              )}
                                              <span className={`font-mono text-[10px] shrink-0 ${isInvalid ? 'text-rose-700 dark:text-rose-300 font-bold' : 'text-zinc-400'}`}>{sub.code}</span>
                                              <span className={`truncate font-sans ${isInvalid ? 'font-black text-rose-900 dark:text-rose-100' : ''}`}>{sub.label}</span>
                                            </div>
                                            <span className={`text-[8px] font-mono shrink-0 px-1 py-0.2 border uppercase ${isInvalid
                                              ? 'bg-rose-600 text-white font-black border-rose-700'
                                              : sub.isFilled
                                                ? 'bg-emerald-500/15 border-emerald-500/40 text-emerald-600 dark:text-emerald-400 font-bold'
                                                : 'bg-zinc-100 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-400'
                                              }`}>
                                              {isInvalid
                                                ? (lang === 'en' ? '! REQUIRED' : '! WAJIB ISI')
                                                : sub.isFilled
                                                  ? (lang === 'en' ? '✓' : '✓')
                                                  : '—'}
                                            </span>
                                          </button>
                                        );
                                      })}
                                    </div>
                                  )}
                                </div>
                              );
                            })}
                          </div>
                        )}
                      </div>
                    );
                  })}
                </div>

                {/* Drawer Footer */}
                <div className="p-3 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950">
                  <div className="flex items-center justify-between text-xs mb-1">
                    <span className="text-zinc-500">{lang === 'en' ? 'Readiness Score:' : 'Skor Kesiapan:'}</span>
                    <span className="font-bold text-emerald-600 dark:text-emerald-400">{completeness.score}%</span>
                  </div>
                  <div className="w-full bg-zinc-200 dark:bg-zinc-800 h-1.5 rounded-none overflow-hidden">
                    <div
                      className="bg-emerald-500 h-full transition-all duration-300"
                      style={{ width: `${completeness.score}%` }}
                    />
                  </div>
                </div>
              </motion.div>
            </>
          )}
        </AnimatePresence>
      </div>
    </>
  );
}
