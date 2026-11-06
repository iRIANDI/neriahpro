    <!-- 5. GOOGLE TRANSLATE TIER 2 MODERN LOADER MODAL (STRICTLY VIEWPORT DEAD-CENTERED) -->
    <div 
        id="translate_loader_modal" 
        style="display: none; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; align-items: center !important; justify-content: center !important; z-index: 999999 !important; background: rgba(0,0,0,0.85) !important; backdrop-filter: blur(8px) !important; margin: 0 !important; padding: 1rem !important;"
    >
        <div 
            class="relative w-full max-w-sm bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 shadow-2xl rounded-none p-6 text-center m-auto"
            style="margin: auto !important; max-width: 24rem !important; width: 100% !important;"
        >
            <!-- Animated Geometric Spinner Icon (Solid Brutalist, Sharp Non-Pill) -->
            <div class="w-10 h-10 mx-auto mb-4 border-2 border-zinc-200 dark:border-zinc-700 border-t-emerald-500 rounded-none animate-spin"></div>

            <div class="space-y-1 mb-3">
                <span class="text-[10px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold">
                    {{ app()->getLocale() === 'en' ? 'NEURAL ENGINE ACTIVE' : 'ENGINE TERJEMAHAN AKTIF' }}
                </span>
                <h4 class="font-mono font-bold text-sm uppercase text-zinc-900 dark:text-white">
                    {{ app()->getLocale() === 'en' ? 'Translating Interface...' : 'Menerjemahkan Halaman...' }}
                </h4>
            </div>

            <!-- Target Language Badge -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 mb-4 font-mono text-xs text-zinc-800 dark:text-zinc-200 font-bold rounded-none">
                <span class="w-2 h-2 bg-emerald-500 rounded-none animate-pulse"></span>
                <span id="translate_loader_lang_badge">Translating...</span>
            </div>

            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-sans leading-relaxed">
                {{ app()->getLocale() === 'en' ? 'Converting interface elements. This banner-free view will adjust smoothly.' : 'Mengonversi komponen antarmuka. Tampilan bebas banner Google akan tertata otomatis.' }}
            </p>

            <!-- Subtle Progress Bar -->
            <div class="mt-4 w-full h-1 bg-zinc-100 dark:bg-zinc-800 overflow-hidden rounded-none">
                <div class="h-full bg-emerald-500 animate-pulse w-full"></div>
            </div>
        </div>
    </div>

    <!-- Google Translate Container & Bridge (Tier 2) -->
    <div id="google_translate_element" class="hidden"></div>
    <script>
        const langNamesMap = {
            'ja': '日本語 (Japanese)',
            'zh-CN': '中文 (Mandarin)',
            'ar': 'العربية (Arabic)',
            'de': 'Deutsch (German)',
            'fr': 'Français (French)',
            'es': 'Español (Spanish)',
            'en': 'English (US)',
            'id': 'Bahasa Indonesia'
        };

        function googleTranslateElementInit() {
            try {
                new google.translate.TranslateElement({
                    pageLanguage: 'id',
                    includedLanguages: '{{ implode(",", $allowedLangList) }}',
                    autoDisplay: false
                }, 'google_translate_element');
            } catch(e) {}
        }

        window.translateLanguage = function(langCode, langName) {
            const resolvedName = langName || langNamesMap[langCode] || langCode.toUpperCase();
            const modal = document.getElementById('translate_loader_modal');
            const badge = document.getElementById('translate_loader_lang_badge');

            if (modal) {
                if (badge) badge.textContent = resolvedName;
                modal.style.display = 'flex';
            }

            document.cookie = 'googtrans=/id/' + langCode + '; path=/; domain=' + window.location.hostname;
            document.cookie = 'googtrans=/id/' + langCode + '; path=/;';

            const select = document.querySelector('.goog-te-combo');
            if (select) {
                select.value = langCode;
                select.dispatchEvent(new Event('change'));
                setTimeout(() => {
                    if (modal) modal.style.display = 'none';
                }, 1200);
            } else {
                setTimeout(() => {
                    location.reload();
                }, 400);
            }
        };
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async defer></script>
    @endif
