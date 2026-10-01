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

        const type = ['success', 'error', 'warning', 'info'].includes(options.type) ? options.type : 'info';
        const container = getContainer();

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto transform translate-x-12 opacity-0 transition-all duration-300 ease-out bg-zinc-950/95 text-white border-2 ${BORDERS[type]} p-4 shadow-2xl backdrop-blur-md rounded-none flex items-start gap-3 relative`;

        const defaultTitles = {
            success: 'TRANSAKSI / BERHASIL',
            error: 'PERINGATAN SISTEM',
            warning: 'PERHATIAN / PENDING',
            info: 'INFORMASI SISTEM',
        };

        const titleText = options.title || defaultTitles[type];

        toast.innerHTML = `
            <div class="flex-shrink-0 mt-0.5">${ICONS[type]}</div>
            <div class="flex-1 min-w-0 pr-4">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-mono font-black uppercase tracking-wider px-1.5 py-0.5 rounded-none ${BADGES[type]}">
                        ${titleText}
                    </span>
                </div>
                <div class="text-xs font-sans text-zinc-300 leading-relaxed break-words">
                    ${options.message}
                </div>
            </div>
            <button type="button" aria-label="Tutup" class="text-zinc-500 hover:text-white transition text-xs font-mono p-1 absolute top-2 right-2 cursor-pointer">
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
