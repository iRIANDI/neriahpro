import { useState, useEffect, useRef, useMemo, useCallback } from 'react';
import { FIELD_MAP, FIELD_SECTION_MAP } from '../constants/blueprintConstants';

export function useBlueprintSpyNavigation({ formData, onOpenStudio }) {
  const [activeSection, setActiveSection] = useState('section-ide-studio');
  const [activeField, setActiveField] = useState(null);
  const [autoSyncAccordion, setAutoSyncAccordion] = useState(true);
  const activeSectionRef = useRef('section-ide-studio');
  const autoSyncSpyRef = useRef(true);
  const desktopScrollRef = useRef(null);

  useEffect(() => {
    autoSyncSpyRef.current = autoSyncAccordion;
  }, [autoSyncAccordion]);

  const [isIndexExpanded, setIsIndexExpanded] = useState(() => {
    if (typeof window !== 'undefined') {
      return window.innerWidth >= 1280;
    }
    return true;
  });
  const [mobileDrawerOpen, setMobileDrawerOpen] = useState(false);
  const [indexSearchQuery, setIndexSearchQuery] = useState('');
  const [openAccordionGroups, setOpenAccordionGroups] = useState({
    studio: true,
    spec1: false,
    spec2: false,
    final: false,
  });

  const sectionGroupMap = useMemo(() => ({
    'section-ide-studio': 'studio',
    'section-ai-assistant': 'studio',
    'section-composable-matrix': 'studio',
    'section-block-a': 'spec1',
    'section-block-b': 'spec1',
    'section-block-c': 'spec1',
    'section-block-d': 'spec2',
    'section-block-e': 'spec2',
    'section-block-f': 'spec2',
    'section-submit': 'final',
  }), []);

  // Sub-block detail toggle state
  const [expandedSubBlocks, setExpandedSubBlocks] = useState({
    'section-ide-studio': true,
  });

  const syncAccordionToSection = useCallback((secId) => {
    const targetGroup = sectionGroupMap[secId];
    if (!targetGroup) return;

    setOpenAccordionGroups({
      studio: targetGroup === 'studio',
      spec1: targetGroup === 'spec1',
      spec2: targetGroup === 'spec2',
      final: targetGroup === 'final',
    });

    setExpandedSubBlocks({
      [secId]: true,
    });

    setTimeout(() => {
      const container = desktopScrollRef.current;
      if (!container) return;
      const activeEl = container.querySelector(`[data-nav-sec="${secId}"]`);
      if (!activeEl) return;

      const containerRect = container.getBoundingClientRect();
      const elRect = activeEl.getBoundingClientRect();

      if (elRect.top < containerRect.top + 20 || elRect.bottom > containerRect.bottom - 20) {
        const targetScrollTop = container.scrollTop + (elRect.top - containerRect.top) - (container.clientHeight / 2) + (elRect.height / 2);
        container.scrollTo({ top: Math.max(0, targetScrollTop), behavior: 'smooth' });
      }
    }, 60);
  }, [sectionGroupMap]);

  const toggleAccordionGroup = useCallback((grp) => {
    setOpenAccordionGroups(prev => ({ ...prev, [grp]: !prev[grp] }));
  }, []);

  const expandAllGroups = useCallback(() => {
    setAutoSyncAccordion(false);
    setOpenAccordionGroups({
      studio: true,
      spec1: true,
      spec2: true,
      final: true,
    });
  }, []);

  const collapseAllGroups = useCallback(() => {
    setAutoSyncAccordion(false);
    setOpenAccordionGroups({
      studio: false,
      spec1: false,
      spec2: false,
      final: false,
    });
    setExpandedSubBlocks({});
  }, []);

  const toggleSubBlock = useCallback((secId, e) => {
    e?.stopPropagation();
    setExpandedSubBlocks(prev => ({
      ...prev,
      [secId]: !prev[secId],
    }));
  }, []);

  const [indexTopOffset, setIndexTopOffset] = useState(() => {
    if (typeof window !== 'undefined') {
      const heroEl = document.getElementById('blueprint-hero');
      if (heroEl) {
        return Math.max(heroEl.getBoundingClientRect().bottom + 16, 68);
      }
    }
    return 360;
  });

  const jumpToSection = useCallback((id) => {
    if (id === 'section-ide-studio' && onOpenStudio) {
      onOpenStudio();
    }
    const el = document.getElementById(id);
    if (!el) return;
    const yOffset = -90;
    const y = el.getBoundingClientRect().top + window.pageYOffset + yOffset;
    window.scrollTo({ top: y, behavior: 'smooth' });
    setActiveSection(id);
    activeSectionRef.current = id;
    syncAccordionToSection(id);
    setMobileDrawerOpen(false);

    el.classList.add('outline-2', 'outline-emerald-500', 'transition-all');
    setTimeout(() => {
      el.classList.remove('outline-2', 'outline-emerald-500');
    }, 2000);
  }, [onOpenStudio, syncAccordionToSection]);

  const jumpToField = useCallback((fieldIdOrKey, explicitSectionId) => {
    const rawKey = String(fieldIdOrKey || '').replace(/^field-/, '');
    const canonicalKey = FIELD_MAP[rawKey] || rawKey;
    const finalDomId = String(fieldIdOrKey || '').startsWith('field-') ? fieldIdOrKey : `field-${canonicalKey}`;
    const sectionId = explicitSectionId || FIELD_SECTION_MAP[canonicalKey];

    if (sectionId === 'section-ide-studio' && onOpenStudio) {
      onOpenStudio();
    }
    const el = document.getElementById(finalDomId);
    if (el) {
      const yOffset = -130;
      const y = el.getBoundingClientRect().top + window.pageYOffset + yOffset;
      window.scrollTo({ top: Math.max(0, y), behavior: 'smooth' });
      if (sectionId) {
        setActiveSection(sectionId);
        activeSectionRef.current = sectionId;
        syncAccordionToSection(sectionId);
      }
      setActiveField(finalDomId);
      setMobileDrawerOpen(false);

      el.classList.add('field-stroke-alert', 'field-stroke-pulsing');
      setTimeout(() => {
        el.classList.remove('field-stroke-pulsing');
      }, 4000);

      const inputEl = el.querySelector('input, textarea, select') || (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' ? el : null);
      if (inputEl) {
        try {
          inputEl.focus({ preventScroll: true });
        } catch (_) { }
      }
    } else if (sectionId) {
      jumpToSection(sectionId);
    }
  }, [jumpToSection, onOpenStudio, syncAccordionToSection]);

  const sectionIds = useMemo(() => [
    'section-ide-studio',
    'section-ai-assistant',
    'section-composable-matrix',
    'section-block-a',
    'section-block-b',
    'section-block-c',
    'section-block-d',
    'section-block-e',
    'section-block-f',
    'section-submit'
  ], []);

  const allFieldIds = useMemo(() => [
    'field-ideaText', 'field-attachedFiles', 'field-proactiveSuggestions',
    'field-namaBisnis', 'field-masalahUtama', 'field-tujuanUtama',
    'field-targetAudiens', 'field-aktorSistem', 'field-targetPlatform',
    'field-fiturWajib', 'field-fiturTambahan', 'field-alurKerja', 'field-migrasiData',
    'field-kebutuhanIntegrasi', 'field-referensiDesain', 'field-kesiapanAset', 'field-preferensiHosting',
    'field-targetWaktu', 'field-skalaPengguna', 'field-outOfScope', 'field-kepatuhanKeamanan',
    'field-kisaranBudget', 'field-garansiSla', 'field-terminPembayaran', 'field-clientName', 'field-email', 'field-phone',
    'field-readinessScore', 'field-lockBtn'
  ], []);

  useEffect(() => {
    let ticking = false;
    const handleScroll = () => {
      if (!ticking) {
        window.requestAnimationFrame(() => {
          const heroEl = document.getElementById('blueprint-hero');
          const minTop = 68;
          if (heroEl) {
            const rect = heroEl.getBoundingClientRect();
            const calculatedTop = Math.max(rect.bottom + 16, minTop);
            setIndexTopOffset(calculatedTop);
          } else {
            setIndexTopOffset(minTop);
          }

          const triggerOffset = 220;
          const isBottom = (window.innerHeight + window.scrollY) >= (document.documentElement.scrollHeight - 80);

          let current = sectionIds[0];
          if (isBottom) {
            current = 'section-submit';
          } else {
            for (let i = sectionIds.length - 1; i >= 0; i--) {
              const el = document.getElementById(sectionIds[i]);
              if (el) {
                const rect = el.getBoundingClientRect();
                if (rect.top <= triggerOffset) {
                  current = sectionIds[i];
                  break;
                }
              }
            }
          }

          let currentField = null;
          for (let i = allFieldIds.length - 1; i >= 0; i--) {
            const el = document.getElementById(allFieldIds[i]);
            if (el) {
              const rect = el.getBoundingClientRect();
              if (rect.top <= triggerOffset + 40) {
                currentField = allFieldIds[i];
                break;
              }
            }
          }
          if (currentField) {
            setActiveField(currentField);
          }

          if (current !== activeSectionRef.current) {
            activeSectionRef.current = current;
            setActiveSection(current);

            if (autoSyncSpyRef.current) {
              syncAccordionToSection(current);
            }
          }

          ticking = false;
        });
        ticking = true;
      }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('resize', handleScroll, { passive: true });
    handleScroll();
    return () => {
      window.removeEventListener('scroll', handleScroll);
      window.removeEventListener('resize', handleScroll);
    };
  }, [sectionIds, allFieldIds, syncAccordionToSection]);

  const blockCompletion = useMemo(() => {
    const check = (fields) => {
      let filled = 0;
      fields.forEach(f => {
        const val = formData[f];
        if (Array.isArray(val) ? val.length > 0 : !!val && String(val).trim().length > 0) {
          filled++;
        }
      });
      return { filled, total: fields.length, isComplete: filled === fields.length };
    };

    return {
      blockA: check(['namaBisnis', 'masalahUtama', 'tujuanUtama']),
      blockB: check(['targetAudiens', 'aktorSistem', 'targetPlatform']),
      blockC: check(['fiturWajib', 'fiturTambahan', 'alurKerja', 'migrasiData']),
      blockD: check(['kebutuhanIntegrasi', 'referensiDesain', 'kesiapanAset', 'preferensiHosting']),
      blockE: check(['targetWaktu', 'skalaPengguna', 'outOfScope', 'kepatuhanKeamanan']),
      blockF: check(['kisaranBudget', 'garansiSla', 'terminPembayaran', 'clientName', 'email', 'phone']),
    };
  }, [formData]);

  return {
    activeSection,
    activeField,
    autoSyncAccordion,
    setAutoSyncAccordion,
    isIndexExpanded,
    setIsIndexExpanded,
    mobileDrawerOpen,
    setMobileDrawerOpen,
    indexSearchQuery,
    setIndexSearchQuery,
    openAccordionGroups,
    toggleAccordionGroup,
    expandedSubBlocks,
    toggleSubBlock,
    expandAllGroups,
    collapseAllGroups,
    indexTopOffset,
    desktopScrollRef,
    jumpToSection,
    jumpToField,
    blockCompletion,
  };
}

