/**
 * Neriah Pro Enterprise Toast Notification System
 * Prohibits native browser alerts ("modal kampungan") in favor of sharp, high-precision dark/light toasts.
 */

(function () {
    if (typeof window === 'undefined') return;

    const ICONS = {
        success: `<svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`,
        error: `<svg class="w-4 h-4 text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
        warning: `<svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`,
        info: `<svg class="w-4 h-4 text-cyan-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
    };

    const BORDERS = {
        success: 'border-emerald-500 shadow-emerald-500/10',
        error: 'border-rose-500 shadow-rose-500/10',
        warning: 'border-amber-500 shadow-amber-500/10',
        info: 'border-cyan-500 shadow-cyan-500/10',
    };

    const BADGES = {
        success: 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40',
        error: 'bg-rose-500/20 text-rose-400 border border-rose-500/40',
        warning: 'bg-amber-500/20 text-amber-400 border border-amber-500/40',
        info: 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/40',
    };

    function getCurrentLocale() {
        if (typeof document === 'undefined') return 'id';
        if (typeof window !== 'undefined' && window.__locale) {
            return String(window.__locale).toLowerCase().startsWith('en') ? 'en' : 'id';
        }
        const cookieMatch = document.cookie.match(/(?:^|;\s*)neriah_locale=([^;]+)/);
        if (cookieMatch && cookieMatch[1]) {
            return decodeURIComponent(cookieMatch[1]).toLowerCase().startsWith('en') ? 'en' : 'id';
        }
        if (document.documentElement && document.documentElement.lang && document.documentElement.lang.toLowerCase().startsWith('en')) {
            return 'en';
        }
        try {
            const stored = localStorage.getItem('locale') || localStorage.getItem('neriah_contract_locale');
            if (stored && stored.toLowerCase().startsWith('en')) {
                return 'en';
            }
        } catch (_) {}
        return 'id';
    }

    const DICTIONARY_EN = {
        'TRANSAKSI / BERHASIL': 'TRANSACTION / SUCCESS',
        'PERINGATAN SISTEM': 'SYSTEM ALERT',
        'PERHATIAN / PENDING': 'ATTENTION / PENDING',
        'INFORMASI SISTEM': 'SYSTEM INFORMATION',
        'PEMBERITAHUAN SISTEM': 'SYSTEM NOTIFICATION',
        'INSTRUKSI PENAJAMAN BERHASIL DISUSUN': 'DIRECTIVE ELABORATION COMPILED',
        'GAGAL MENYUSUN INSTRUKSI': 'DIRECTIVE ELABORATION FAILED',
        'Instruksi penajaman berhasil dirumuskan berbasis Dokumen Acuan & Standar Industri Dunia.': 'Refinement directive successfully compiled from Project Documents & Global Standards.',
        'Instruksi penajaman berhasil dielaborasi dari Dokumen Acuan & Standar Industri.': 'Refinement directive successfully elaborated from Project Documents & Industry Standards.',
        'Gagal menyusun instruksi penajaman.': 'Failed to compile refinement directive.',
        'Gagal mengelaborasi instruksi:': 'Failed to elaborate directive:',
        'AKSES DITOLAK': 'ACCESS DENIED',
        'Dokumen berada dalam status Scope Lock (Kontrak Studio).': 'Document is currently in Scope Lock status (Studio Contract).',
        'TAUTAN DISALIN': 'LINK COPIED',
        'Tautan portofolio telah disalin ke clipboard.': 'Portfolio link copied to clipboard.',
        'KODE DISALIN': 'CODE COPIED',
        'Kode berhasil disalin ke clipboard!': 'Code copied to clipboard!',
        'PROMPT DISALIN': 'PROMPT COPIED',
        'Prompt berhasil disalin ke clipboard!': 'Prompt copied to clipboard!',
        'BERHASIL DISALIN': 'COPIED TO CLIPBOARD',
        'Konten berhasil disalin ke clipboard.': 'Content successfully copied to clipboard.',
        'SHA-256 DISALIN': 'SHA-256 COPIED',
        'Cryptographic hash berhasil disalin ke clipboard.': 'Cryptographic hash successfully copied to clipboard.',
        'DELTA DISALIN': 'DELTA COPIED',
        'Surgical Delta Prompt disalin ke clipboard.': 'Surgical Delta Prompt copied to clipboard.',
        'PERUBAHAN DISIMPAN': 'CHANGES SAVED',
        'BERKAS DISIMPAN': 'FILE SAVED',
        'Berkas berhasil disimpan.': 'File successfully saved.',
        'MEMUAT ARSITEKTUR...': 'LOADING ARCHITECTURE...',
        'MENYIAPKAN PREVIEW...': 'PREPARING PREVIEW...',
        'SISTEM SIAP': 'SYSTEM READY',
        'Pilih Proyek': 'Select Project',
        'Silakan pilih proyek blueprint terlebih dahulu!': 'Please select a blueprint project first!',
        'VALIDASI GAGAL': 'VALIDATION FAILED',
        'Nama lengkap wajib diisi.': 'Full name is required.',
        'AUDIT GAGAL': 'AUDIT FAILED',
        'DOCTOR GAGAL': 'DOCTOR FAILED',
        'Tidak dapat menghubungi endpoint Slop Scanner.': 'Could not connect to Slop Scanner endpoint.',
        'Tidak dapat menghubungi endpoint Blueprint Doctor.': 'Could not connect to Blueprint Doctor endpoint.',
        'Request Addendum Dikirim': 'Addendum Request Submitted',
        'Tim kami akan mereview request ini dan mengirimkan estimasi SP.': 'Our team will review this request and provide story point estimates.',
        'Gagal mengirim tiket addendum.': 'Failed to submit addendum ticket.',
        'Tiket QA berhasil dikirim.': 'QA ticket successfully submitted.',
        'Gagal mengirim tiket.': 'Failed to submit ticket.',
        'Komentar ditambahkan.': 'Comment added successfully.',
        'Gagal menambahkan komentar.': 'Failed to add comment.',
        'Tutup': 'Close',
    };

    function resolveText(val, currentLocale) {
        if (!val) return '';
        if (typeof val === 'object') {
            return val[currentLocale] || (currentLocale === 'en' ? (val.en || val.id) : (val.id || val.en)) || Object.values(val)[0] || '';
        }
        if (typeof val === 'string' && currentLocale === 'en') {
            if (DICTIONARY_EN[val]) return DICTIONARY_EN[val];
            for (const [k, v] of Object.entries(DICTIONARY_EN)) {
                if (val.startsWith(k)) {
                    return val.replace(k, v);
                }
            }
        }
        return String(val);
    }

    function getContainer() {
        let container = document.getElementById('neriah-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'neriah-toast-container';
            container.setAttribute('aria-live', 'polite');
            container.className = 'fixed top-5 right-5 z-[999999] flex flex-col gap-2.5 max-w-sm sm:max-w-md w-full px-3 sm:px-0 pointer-events-none';
            document.body.appendChild(container);
        }
        return container;
    }

    window.showToast = function (opts, typeFallback = 'info') {
        let options = {
            type: 'info',
            title: '',
            message: '',
            duration: 5000,
        };

        if (typeof opts === 'string') {
            options.message = opts;
            options.type = typeFallback;
        } else if (typeof opts === 'object' && opts !== null) {
            options = { ...options, ...opts };
        }

        const currentLocale = getCurrentLocale();
        const type = ['success', 'error', 'warning', 'info'].includes(options.type) ? options.type : 'info';
        const container = getContainer();

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto transform translate-x-12 opacity-0 transition-all duration-300 ease-out bg-zinc-950/95 text-white border-2 ${BORDERS[type]} p-4 shadow-2xl backdrop-blur-md rounded-none flex items-start gap-3 relative`;

        const defaultTitles = {
            en: {
                success: 'TRANSACTION / SUCCESS',
                error: 'SYSTEM ALERT',
                warning: 'ATTENTION / PENDING',
                info: 'SYSTEM INFORMATION',
            },
            id: {
                success: 'TRANSAKSI / BERHASIL',
                error: 'PERINGATAN SISTEM',
                warning: 'PERHATIAN / PENDING',
                info: 'INFORMASI SISTEM',
            }
        };

        const resolvedTitle = resolveText(options.title, currentLocale) || defaultTitles[currentLocale][type];
        const resolvedMessage = resolveText(options.message, currentLocale);
        const closeLabel = currentLocale === 'en' ? 'Close' : 'Tutup';

        toast.innerHTML = `
            <div class="flex-shrink-0 mt-0.5">${ICONS[type]}</div>
            <div class="flex-1 min-w-0 pr-4">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-mono font-black uppercase tracking-wider px-1.5 py-0.5 rounded-none ${BADGES[type]}">
                        ${resolvedTitle}
                    </span>
                </div>
                <div class="text-xs font-sans text-zinc-300 leading-relaxed break-words">
                    ${resolvedMessage}
                </div>
            </div>
            <button type="button" aria-label="${closeLabel}" class="text-zinc-500 hover:text-white transition text-xs font-mono p-1 absolute top-2 right-2 cursor-pointer">
                ✕
            </button>
        `;

        const closeBtn = toast.querySelector('button');
        const dismiss = () => {
            toast.classList.add('translate-x-12', 'opacity-0');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        };

        closeBtn.addEventListener('click', dismiss);

        container.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-12', 'opacity-0');
        });

        // Auto dismiss timer
        if (options.duration > 0) {
            setTimeout(dismiss, options.duration);
        }

        return toast;
    };

    window.toast = window.showToast;
    window.toast.success = (msg, title) => window.showToast({ type: 'success', message: msg, title });
    window.toast.error = (msg, title) => window.showToast({ type: 'error', message: msg, title });
    window.toast.warning = (msg, title) => window.showToast({ type: 'warning', message: msg, title });
    window.toast.info = (msg, title) => window.showToast({ type: 'info', message: msg, title });

    // Enforce strict ban on primitive browser dialogs ("modal kampungan") by redirecting native alert()
    try {
        window.alert = function (message) {
            window.showToast({
                type: 'info',
                title: 'PEMBERITAHUAN SISTEM',
                message: typeof message === 'object' ? JSON.stringify(message) : String(message),
            });
        };
    } catch (e) {
        console.warn('Could not override window.alert:', e);
    }
})();
