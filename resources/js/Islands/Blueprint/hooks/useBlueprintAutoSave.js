import { useState, useRef, useCallback } from 'react';

export function useBlueprintAutoSave({ csrfToken, initialData = {} }) {
  const [autoSaveStatus, setAutoSaveStatus] = useState('idle');
  const [lastSavedTime, setLastSavedTime] = useState(() => {
    const tz = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    return `${tz} WIB`;
  });

  const autoSaveDebounceRef = useRef(null);
  const isExplicitSlugEdit = Boolean(initialData?._meta?.is_editing_slug);
  const isFreshWorkspace = Boolean(initialData?._meta?.is_fresh_workspace) || (!isExplicitSlugEdit && !initialData?._draft_id && !initialData?.namaBisnis);

  const draftIdRef = useRef(
    initialData?._meta?.draft_token
    || initialData?._draft_id
    || ('draft_' + Date.now() + '_' + Math.random().toString(36).slice(2, 10))
  );

  const getScopedStorageKey = useCallback(() => {
    if (isExplicitSlugEdit && initialData?._meta?.is_editing_slug) {
      return `neriah_bp_slug_${initialData._meta.is_editing_slug}`;
    }
    return `neriah_bp_${draftIdRef.current || 'fresh'}`;
  }, [isExplicitSlugEdit, initialData]);

  const triggerAutoSave = useCallback((updatedData) => {
    // 1. Immediate Scoped LocalStorage save
    try {
      localStorage.setItem(getScopedStorageKey(), JSON.stringify(updatedData));
    } catch (e) { }

    // 2. Debounced server save
    setAutoSaveStatus('saving');
    if (autoSaveDebounceRef.current) {
      clearTimeout(autoSaveDebounceRef.current);
    }

    autoSaveDebounceRef.current = setTimeout(async () => {
      try {
        const token = csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch('/api/blueprint/autosave', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            blueprint: updatedData,
            draft_id: draftIdRef.current,
            is_explicit_slug_edit: isExplicitSlugEdit,
            slug: isExplicitSlugEdit ? (initialData?._meta?.is_editing_slug || null) : null,
          }),
        });

        if (response.ok) {
          const result = await response.json();
          if (result.draft_id) draftIdRef.current = result.draft_id;
          if (result.saved_at) setLastSavedTime(result.saved_at);
          setAutoSaveStatus('saved');
        } else {
          setAutoSaveStatus('saved');
        }
      } catch (err) {
        setAutoSaveStatus('saved');
      }
    }, 1500);
  }, [csrfToken, getScopedStorageKey, isExplicitSlugEdit, initialData]);

  return {
    autoSaveStatus,
    lastSavedTime,
    setLastSavedTime,
    draftIdRef,
    isExplicitSlugEdit,
    isFreshWorkspace,
    getScopedStorageKey,
    triggerAutoSave,
  };
}

