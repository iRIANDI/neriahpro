@php
    $locale = app()->getLocale() ?: session('locale', 'id');
    $isEn = ($locale === 'en');
    $googleTranslateEnabled = true;
    $allowedLangList = ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es'];
    try {
        $googleTranslateEnabled = (bool) \App\Models\CmsGlobalSetting::getVal('google_translate_enabled', true);
        $rawAllowed = \App\Models\CmsGlobalSetting::getVal('google_translate_allowed_languages', $allowedLangList);
        $allowedLangList = is_array($rawAllowed) ? $rawAllowed : (is_string($rawAllowed) ? json_decode($rawAllowed, true) : $allowedLangList);
        if (empty($allowedLangList)) $allowedLangList = ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es'];
    } catch (\Throwable $e) {
        $googleTranslateEnabled = true;
        $allowedLangList = ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es'];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>neriahpro.com - {{ $isEn ? 'Client Portal Login // Password, Captcha & 2FA OTP' : 'Login Portal Klien // Password, Captcha & 2FA OTP' }}</title>
    <meta name="description" content="{{ $isEn ? 'Secure client authentication portal with password, interactive visual sequence pin captcha, and 6-digit email OTP two-factor verification.' : 'Portal login aman pelanggan Neriah Pro dengan otentikasi kata sandi, visual sequence pin captcha, dan verifikasi OTP email 6-digit.' }}">

    <!-- Local Fonts -->
    <link rel="stylesheet" href="{{ asset('fonts/instrument-sans/instrument-sans.css') }}">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js Vendor & Anti-FOUC Style -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="{{ asset('js/vendor/alpine.min.js') }}"></script>

    <script>
        // Init theme before render to eliminate flash
        if (localStorage.getItem('neriah_theme') === 'dark' || (!localStorage.getItem('neriah_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Alpine State Definition
        function customerLoginApp() {
            return {
                step: 'credentials', // 'credentials' | 'otp'
                mode: 'login', // 'login' | 'register' | 'forgot_password'
                lang: '{{ $locale }}',
                name: '',
                email: '',
                password: '',
                newPassword: '',
                otp: '',
                showPassword: false,
                isLoading: false,
                alertMessage: '',
                alertType: 'info',
                resendCountdown: 0,
                countdownTimer: null,
                tier2Open: false,

                // Visual Scattered Object PIN Captcha Data
                isCaptchaModalOpen: false,
                captchaCatalog: [
                    { id: 'shield', icon: '🛡️', label_id: 'Perisai', label_en: 'Shield' },
                    { id: 'bolt', icon: '⚡', label_id: 'Petir', label_en: 'Lightning' },
                    { id: 'key', icon: '🔑', label_id: 'Kunci', label_en: 'Key' },
                    { id: 'rocket', icon: '🚀', label_id: 'Roket', label_en: 'Rocket' },
                    { id: 'diamond', icon: '💎', label_id: 'Permata', label_en: 'Diamond' },
                    { id: 'target', icon: '🎯', label_id: 'Target', label_en: 'Target' },
                    { id: 'lock', icon: '🔒', label_id: 'Gembok', label_en: 'Padlock' },
                    { id: 'compass', icon: '📐', label_id: 'Penggaris', label_en: 'Ruler' },
                ],
                targetCaptchaSequence: [],
                scatteredSceneObjects: [],
                pinnedCaptchaSequence: [],
                isCaptchaVerified: false,
                captchaError: '',

                init() {
                    this.generateCaptcha();
                },

                openCaptchaModal() {
                    this.isCaptchaModalOpen = true;
                    if (!this.isCaptchaVerified && this.targetCaptchaSequence.length === 0) {
                        this.generateCaptcha();
                    }
                },

                closeCaptchaModal() {
                    this.isCaptchaModalOpen = false;
                },

                generateCaptcha() {
                    this.isCaptchaVerified = false;
                    this.pinnedCaptchaSequence = [];
                    this.captchaError = '';
                    
                    // Shuffle catalog and pick 3 distinct target items for the clue
                    const shuffledCatalog = [...this.captchaCatalog].sort(() => 0.5 - Math.random());
                    this.targetCaptchaSequence = shuffledCatalog.slice(0, 3);

                    // 8 spatial anchors distributed across the image canvas (percentages)
                    const anchors = [
                        { x: 15, y: 22 },
                        { x: 42, y: 18 },
                        { x: 68, y: 24 },
                        { x: 88, y: 20 },
                        { x: 22, y: 55 },
                        { x: 78, y: 53 },
                        { x: 38, y: 82 },
                        { x: 82, y: 80 },
                    ];

                    const shuffledAnchors = [...anchors].sort(() => 0.5 - Math.random());

                    // Scatter objects across canvas with subtle jitter and rotation
                    this.scatteredSceneObjects = shuffledCatalog.map((item, index) => {
                        const anchor = shuffledAnchors[index];
                        const jitterX = Math.floor(Math.random() * 8) - 4; // -4% to +4%
                        const jitterY = Math.floor(Math.random() * 8) - 4; // -4% to +4%
                        const rotation = Math.floor(Math.random() * 20) - 10; // -10deg to +10deg
                        return {
                            ...item,
                            x: Math.max(10, Math.min(88, anchor.x + jitterX)),
                            y: Math.max(15, Math.min(84, anchor.y + jitterY)),
                            rotation: rotation
                        };
                    });
                },

                resetPins() {
                    if (this.isCaptchaVerified) return;
                    this.pinnedCaptchaSequence = [];
                    this.captchaError = '';
                },

                pinCaptchaItem(item) {
                    if (this.isCaptchaVerified) return;

                    // If already pinned, unpin it
                    const existingIndex = this.pinnedCaptchaSequence.findIndex(p => p.id === item.id);
                    if (existingIndex !== -1) {
                        this.pinnedCaptchaSequence.splice(existingIndex, 1);
                        this.captchaError = '';
                        return;
                    }

                    // Max 3 pins
                    if (this.pinnedCaptchaSequence.length >= this.targetCaptchaSequence.length) {
                        return;
                    }

                    this.pinnedCaptchaSequence.push(item);
                    this.captchaError = '';

                    // When 3 pins placed, verify against target sequence!
                    if (this.pinnedCaptchaSequence.length === this.targetCaptchaSequence.length) {
                        const isMatch = this.pinnedCaptchaSequence.every((pinned, idx) => pinned.id === this.targetCaptchaSequence[idx].id);
                        if (isMatch) {
                            this.isCaptchaVerified = true;
                            this.captchaError = '';
                            if (window.showToast) {
                                window.showToast({
                                    type: 'success',
                                    title: this.lang === 'en' ? 'Captcha Verified' : 'Captcha Berhasil',
                                    message: this.lang === 'en' ? 'Visual scene objects verified! Login unlocked.' : 'Objek gambar terverifikasi! Tombol login kini aktif.'
                                });
                            }
                            setTimeout(() => {
                                this.closeCaptchaModal();
                            }, 650);
                        } else {
                            this.isCaptchaVerified = false;
                            this.captchaError = this.lang === 'en' ? 'Incorrect pin sequence. Resetting...' : 'Urutan pin objek salah. Mengulang...';
                            setTimeout(() => {
                                this.pinnedCaptchaSequence = [];
                                this.captchaError = '';
                            }, 850);
                        }
                    }
                },

                getPinIndex(item) {
                    const idx = this.pinnedCaptchaSequence.findIndex(p => p.id === item.id);
                    return idx !== -1 ? (idx + 1) : null;
                },

                allowedLanguages: [
                    { code: 'en', name: 'English (US)', flag: '🇺🇸' },
                    { code: 'id', name: 'Bahasa Indonesia', flag: '🇮🇩' },
                    { code: 'ja', name: '日本語 (Japanese)', flag: '🇯🇵' },
                    { code: 'zh-CN', name: '中文 (Chinese)', flag: '🇨🇳' },
                    { code: 'ar', name: 'العربية (Arabic)', flag: '🇸🇦' },
                    { code: 'de', name: 'Deutsch (German)', flag: '🇩🇪' },
                    { code: 'fr', name: 'Français (French)', flag: '🇫🇷' },
                    { code: 'es', name: 'Español (Spanish)', flag: '🇪🇸' }
                ],

                setLocale(newLocale) {
                    this.lang = newLocale;
                    fetch('/lang/' + newLocale).catch(() => {});
                },

                runTranslate(langCode) {
                    this.tier2Open = false;
                    const select = document.querySelector('.goog-te-combo');
                    if (select) {
                        select.value = langCode;
                        select.dispatchEvent(new Event('change'));
                    } else {
                        document.cookie = 'googtrans=/id/' + langCode + '; path=/; domain=' + window.location.hostname;
                        document.cookie = 'googtrans=/id/' + langCode + '; path=/;';
                        location.reload();
                    }
                },

                toggleTheme() {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('neriah_theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('neriah_theme', 'dark');
                    }
                },

                switchMode(newMode) {
                    this.mode = newMode;
                    this.step = 'credentials';
                    this.otp = '';
                    this.alertMessage = '';
                    this.generateCaptcha();
                },

                showAlert(message, type = 'info') {
                    this.alertMessage = message;
                    this.alertType = type;
                    if (window.showToast) {
                        window.showToast({
                            type: type === 'error' ? 'error' : 'success',
                            title: type === 'error' ? (this.lang === 'en' ? 'Authentication Notice' : 'Pemberitahuan Autentikasi') : (this.lang === 'en' ? 'Success' : 'Berhasil'),
                            message: message
                        });
                    }
                },

                startCountdown(seconds = 60) {
                    this.resendCountdown = seconds;
                    if (this.countdownTimer) clearInterval(this.countdownTimer);
                    this.countdownTimer = setInterval(() => {
                        if (this.resendCountdown > 0) {
                            this.resendCountdown--;
                        } else {
                            clearInterval(this.countdownTimer);
                        }
                    }, 1000);
                },

                async requestOtp() {
                    if (!this.email || this.isLoading) return;
                    if (this.mode !== 'forgot_password' && !this.password) return;
                    
                    // Trigger Snap Modal if Captcha is not yet completed
                    if (this.mode !== 'forgot_password' && !this.isCaptchaVerified) {
                        this.openCaptchaModal();
                        return;
                    }

                    this.isLoading = true;
                    this.alertMessage = '';

                    try {
                        const response = await fetch('/api/customer/otp/request', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                email: this.email,
                                password: this.password,
                                name: this.name,
                                mode: this.mode,
                                captcha_verified: this.isCaptchaVerified
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.step = 'otp';
                            this.showAlert(data.message, 'success');
                            this.startCountdown(60);
                        } else {
                            this.showAlert(data.message || (this.lang === 'en' ? 'Failed to dispatch OTP code. Please check your credentials.' : 'Gagal mengirimkan kode OTP. Silakan periksa kembali email & kata sandi Anda.'), 'error');
                            this.generateCaptcha();
                        }
                    } catch (err) {
                        this.showAlert((this.lang === 'en' ? 'Network error connecting to server: ' : 'Terjadi gangguan jaringan: ') + err.message, 'error');
                    } finally {
                        this.isLoading = false;
                    }
                },

                async verifyOtp() {
                    if (this.otp.length !== 6 || this.isLoading) return;
                    this.isLoading = true;
                    this.alertMessage = '';

                    try {
                        const response = await fetch('/api/customer/otp/verify', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                email: this.email,
                                otp: this.otp,
                                new_password: this.newPassword
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.showAlert(data.message, 'success');
                            setTimeout(() => {
                                window.location.href = data.redirect_url || '/customer/dashboard';
                            }, 800);
                        } else {
                            this.showAlert(data.message || (this.lang === 'en' ? 'Invalid or expired OTP code.' : 'Kode OTP salah atau telah kedaluwarsa.'), 'error');
                        }
                    } catch (err) {
                        this.showAlert((this.lang === 'en' ? 'Verification error: ' : 'Terjadi kesalahan sistem: ') + err.message, 'error');
                    } finally {
                        this.isLoading = false;
                    }
                }
            };
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('customerLoginApp', customerLoginApp);
        });
    </script>
</head>
<body class="bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col justify-between transition-colors duration-200"
      x-data="customerLoginApp()">

    <!-- TOP NAVIGATION BAR: BRAND, TIER 1 LOCALE, TIER 2 TRANSLATE, THEME TOGGLE -->
    <header class="border-b border-zinc-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <!-- Left: Brand -->
            <a href="/" class="flex items-center gap-2.5 group">
                <span class="w-8 h-8 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-black text-sm flex items-center justify-center rounded-none shadow-xs group-hover:scale-105 transition-transform">
                    N
                </span>
                <span class="font-mono text-sm font-black tracking-tight text-zinc-900 dark:text-white uppercase">
                    Neriah<span class="text-emerald-500">Pro</span>
                </span>
                <span class="text-zinc-300 dark:text-zinc-700 hidden sm:inline">/</span>
                <span class="hidden sm:inline-block px-2 py-0.5 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-mono text-[10px] font-bold uppercase tracking-wider border border-indigo-500/20">
                    <span x-text="lang === 'en' ? '2-FA SECURE PORTAL' : 'PORTAL AMAN 2-FA'">{{ $isEn ? '2-FA SECURE PORTAL' : 'PORTAL AMAN 2-FA' }}</span>
                </span>
            </a>

            <!-- Right: Utilities (Tier 1 Locale + Tier 2 Translate + Theme Toggle) -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Tier 1: Native Language Toggle (ID / EN) -->
                <div class="flex items-center border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 font-mono text-xs rounded-none overflow-hidden">
                    <button 
                        type="button" 
                        @click="setLocale('id')"
                        :class="lang === 'id' ? 'bg-emerald-500 text-black font-black' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'"
                        class="px-2.5 py-1.5 transition cursor-pointer"
                        title="Bahasa Indonesia (Tier 1)"
                    >
                        ID
                    </button>
                    <button 
                        type="button" 
                        @click="setLocale('en')"
                        :class="lang === 'en' ? 'bg-emerald-500 text-black font-black' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'"
                        class="px-2.5 py-1.5 transition cursor-pointer"
                        title="English (Tier 1)"
                    >
                        EN
                    </button>
                </div>

                @if($googleTranslateEnabled)
                <!-- Tier 2: Global Whitelist Language Dropdown (Google Translate) -->
                <div class="relative" @click.away="tier2Open = false">
                    <button 
                        type="button" 
                        @click="tier2Open = !tier2Open"
                        class="flex items-center gap-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 py-1.5 px-2.5 text-[11px] font-mono font-bold rounded-none transition cursor-pointer"
                        title="Global Language Translation (Tier 2)"
                    >
                        <span>🌐</span>
                        <span class="hidden md:inline uppercase">Global</span>
                        <span class="text-[9px]">▼</span>
                    </button>

                    <div 
                        x-show="tier2Open" 
                        x-cloak 
                        class="absolute top-full right-0 mt-1 w-48 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 shadow-xl p-2 z-50 font-sans text-xs space-y-1 rounded-none"
                    >
                        <div class="px-2 py-1 text-[10px] font-mono text-zinc-400 uppercase font-bold border-b border-zinc-200 dark:border-zinc-800 mb-1">
                            Google Translate (Tier 2)
                        </div>
                        <template x-for="item in allowedLanguages" :key="item.code">
                            <button 
                                type="button" 
                                @click="runTranslate(item.code)"
                                class="w-full text-left px-2 py-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-between text-zinc-700 dark:text-zinc-200 transition text-xs rounded-none cursor-pointer"
                            >
                                <span class="flex items-center gap-2">
                                    <span x-text="item.flag"></span>
                                    <span x-text="item.name"></span>
                                </span>
                            </button>
                        </template>
                    </div>
                </div>
                @endif

                <!-- Dark / Light Mode Toggle -->
                <button 
                    type="button" 
                    @click="toggleTheme()" 
                    class="p-2 border border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition rounded-none bg-zinc-50 dark:bg-zinc-800 cursor-pointer"
                    title="Toggle Theme"
                >
                    <span class="dark:hidden">🌙</span>
                    <span class="hidden dark:inline">☀️</span>
                </button>
            </div>
        </div>
    </header>

    <!-- MAIN LOGIN CARD -->
    <main class="flex-1 flex items-center justify-center py-8 px-4 sm:px-6">
        <div class="max-w-md w-full bg-white dark:bg-zinc-900/95 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 rounded-none shadow-xl dark:shadow-2xl relative space-y-5">
            
            <!-- Header Badges & Titles -->
            <div class="text-center space-y-2">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-none text-[11px] font-mono font-bold tracking-wider uppercase bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-none bg-emerald-500 animate-pulse"></span>
                    <span x-text="step === 'otp' ? (lang === 'en' ? 'STEP 2 // EMAIL OTP VERIFICATION' : 'TAHAP 2 // VERIFIKASI KODE OTP') : (lang === 'en' ? '2-FA // PASSWORD + CAPTCHA + OTP' : 'AUTENTIKASI 2-FA // PASSWORD + CAPTCHA + OTP')">{{ $isEn ? '2-FA // PASSWORD + CAPTCHA + OTP' : 'AUTENTIKASI 2-FA // PASSWORD + CAPTCHA + OTP' }}</span>
                </div>

                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white uppercase font-sans"
                    x-text="mode === 'register' ? (lang === 'en' ? 'Create Client Account' : 'Daftar Akun Portal Klien') : (mode === 'forgot_password' ? (lang === 'en' ? 'Reset Account Password' : 'Atur Ulang Kata Sandi') : (lang === 'en' ? 'Customer Portal Login' : 'Masuk ke Portal Klien'))">{{ $isEn ? 'Customer Portal Login' : 'Masuk ke Portal Klien' }}</h1>

                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed font-sans"
                   x-text="step === 'otp' 
                       ? (lang === 'en' ? 'Enter the 6-digit OTP code sent to your registered email to complete sign in.' : 'Masukkan 6-digit kode OTP yang telah dikirim ke email Anda untuk menyelesaikan login.')
                       : (mode === 'register'
                           ? (lang === 'en' ? 'Fill in your details, solve the visual captcha pin, then verify your email with OTP.' : 'Lengkapi data, selesaikan pin captcha visual, lalu verifikasi email dengan OTP.')
                           : (mode === 'forgot_password'
                               ? (lang === 'en' ? 'Enter your registered email to receive a password reset OTP code.' : 'Masukkan email terdaftar untuk menerima kode OTP pemulihan kata sandi.')
                               : (lang === 'en' ? 'Enter email & password, complete snap captcha in modal, then input OTP code.' : 'Masukkan email & kata sandi, selesaikan snap captcha, lalu masukkan kode OTP.')))">{{ $isEn ? 'Enter email & password, complete snap captcha in modal, then input OTP code.' : 'Masukkan email & kata sandi, selesaikan snap captcha, lalu masukkan kode OTP.' }}</p>
            </div>

            <!-- Mode Switcher Tabs (Only visible on Step 1) -->
            <div x-show="step !== 'otp'" class="grid grid-cols-2 gap-1 border border-zinc-200 dark:border-zinc-800 p-1 bg-zinc-50 dark:bg-zinc-950 font-mono text-xs">
                <button 
                    type="button" 
                    @click="switchMode('login')"
                    :class="mode === 'login' ? 'bg-zinc-900 text-white dark:bg-emerald-500 dark:text-black font-bold shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'"
                    class="py-2 text-center transition cursor-pointer uppercase rounded-none"
                >
                    <span x-text="lang === 'en' ? 'Sign In (Login)' : 'Masuk Akun'">{{ $isEn ? 'Sign In (Login)' : 'Masuk Akun' }}</span>
                </button>
                <button 
                    type="button" 
                    @click="switchMode('register')"
                    :class="mode === 'register' ? 'bg-zinc-900 text-white dark:bg-emerald-500 dark:text-black font-bold shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'"
                    class="py-2 text-center transition cursor-pointer uppercase rounded-none"
                >
                    <span x-text="lang === 'en' ? 'New Account' : 'Daftar Baru'">{{ $isEn ? 'New Account' : 'Daftar Baru' }}</span>
                </button>
            </div>

            <!-- Toast / Alert Notification Box -->
            <div x-show="alertMessage" 
                 x-cloak 
                 class="p-3 text-xs rounded-none border transition-all duration-300 font-sans"
                 :class="alertType === 'error' ? 'bg-rose-500/10 text-rose-600 dark:text-rose-300 border-rose-500/30' : 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/30'">
                <div class="flex items-start gap-2">
                    <span x-text="alertType === 'error' ? '⚠️' : '✅'" class="text-sm"></span>
                    <span x-text="alertMessage" class="flex-1 font-medium leading-relaxed"></span>
                </div>
            </div>

            <!-- ============================================================= -->
            <!-- STEP 1: INPUT CREDENTIALS + VISUAL SEQUENCE PIN CAPTCHA       -->
            <!-- ============================================================= -->
            <form x-show="step === 'credentials'" @submit.prevent="requestOtp" class="space-y-4">
                
                <!-- Name field (Only in Register mode) -->
                <div x-show="mode === 'register'" x-cloak>
                    <label for="customer-name" class="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                        <span x-text="lang === 'en' ? 'Full Name *' : 'Nama Lengkap *'">{{ $isEn ? 'Full Name *' : 'Nama Lengkap *' }}</span>
                    </label>
                    <input 
                        id="customer-name" 
                        type="text" 
                        x-model="name" 
                        :required="mode === 'register'" 
                        :placeholder="lang === 'en' ? 'e.g. John Doe' : 'Contoh: Budi Santoso'"
                        class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-zinc-900 dark:text-zinc-100 text-sm px-3.5 py-2.5 rounded-none outline-none transition-colors"
                        :disabled="isLoading"
                    />
                </div>

                <!-- Email field -->
                <div>
                    <label for="customer-email" class="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                        <span x-text="lang === 'en' ? 'Customer Email Address *' : 'Alamat E-mail Pelanggan *'">{{ $isEn ? 'Customer Email Address *' : 'Alamat E-mail Pelanggan *' }}</span>
                    </label>
                    <input 
                        id="customer-email" 
                        type="email" 
                        x-model="email" 
                        required 
                        placeholder="contoh@perusahaan.com"
                        class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-zinc-900 dark:text-zinc-100 text-sm px-3.5 py-2.5 rounded-none outline-none transition-colors"
                        :disabled="isLoading"
                    />
                </div>

                <!-- Password field -->
                <div x-show="mode !== 'forgot_password'">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="customer-password" class="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">
                            <span x-text="lang === 'en' ? 'Account Password *' : 'Kata Sandi Akun *'">{{ $isEn ? 'Account Password *' : 'Kata Sandi Akun *' }}</span>
                        </label>
                        <button 
                            x-show="mode === 'login'" 
                            type="button" 
                            @click="switchMode('forgot_password')"
                            class="text-[11px] font-mono text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer"
                        >
                            <span x-text="lang === 'en' ? 'Forgot Password?' : 'Lupa Kata Sandi?'">{{ $isEn ? 'Forgot Password?' : 'Lupa Kata Sandi?' }}</span>
                        </button>
                    </div>

                    <div class="relative">
                        <input 
                            id="customer-password" 
                            :type="showPassword ? 'text' : 'password'" 
                            x-model="password" 
                            :required="mode !== 'forgot_password'"
                            :placeholder="lang === 'en' ? 'Enter your secure password' : 'Masukkan kata sandi akun'"
                            class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-zinc-900 dark:text-zinc-100 text-sm px-3.5 py-2.5 pr-10 rounded-none outline-none transition-colors"
                            :disabled="isLoading"
                        />
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-xs font-mono cursor-pointer"
                            tabindex="-1"
                        >
                            <span x-text="showPassword ? '🙈' : '👁️'">👁️</span>
                        </button>
                    </div>
                </div>

                <!-- VISUAL SEQUENCE PIN CAPTCHA (SNAP MODAL TRIGGER BAR) -->
                <div x-show="mode !== 'forgot_password'" class="pt-2.5 pb-0.5 border-t border-zinc-200 dark:border-zinc-800 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-none" :class="isCaptchaVerified ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                            <span x-text="lang === 'en' ? 'Security Challenge (Captcha) *' : 'Tantangan Keamanan (Captcha) *'">{{ $isEn ? 'Security Challenge (Captcha) *' : 'Tantangan Keamanan (Captcha) *' }}</span>
                        </span>
                        <span x-show="isCaptchaVerified" x-cloak class="text-[10px] font-mono font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                            <span>✅</span>
                            <span x-text="lang === 'en' ? 'VERIFIED' : 'TERVERIFIKASI'">{{ $isEn ? 'VERIFIED' : 'TERVERIFIKASI' }}</span>
                        </span>
                    </div>

                    <!-- Trigger Button for Snap Modal -->
                    <button 
                        type="button" 
                        @click="openCaptchaModal()"
                        :class="isCaptchaVerified ? 'border-emerald-500 bg-emerald-500/10 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 ring-1 ring-emerald-500/30' : 'border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950 hover:border-emerald-500 hover:bg-emerald-500/5 text-zinc-700 dark:text-zinc-300'"
                        class="w-full flex items-center justify-between p-3 border rounded-none transition group cursor-pointer text-left"
                    >
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 flex items-center justify-center border font-mono text-sm shadow-xs rounded-none transition"
                                  :class="isCaptchaVerified ? 'border-emerald-500 bg-emerald-500 text-black' : 'border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 group-hover:border-emerald-500'">
                                <span x-show="!isCaptchaVerified">🛡️</span>
                                <span x-show="isCaptchaVerified" x-cloak>✓</span>
                            </span>
                            <div class="font-mono">
                                <div class="text-xs font-bold" x-text="isCaptchaVerified ? (lang === 'en' ? 'Image Objects Verified' : 'Objek Gambar Terverifikasi') : (lang === 'en' ? 'Pin Image Objects (Visual Captcha)' : 'Pin Objek Gambar (Visual Captcha)')">
                                    {{ $isEn ? 'Pin Image Objects (Visual Captcha)' : 'Pin Objek Gambar (Visual Captcha)' }}
                                </div>
                                <div class="text-[10px] text-zinc-500 dark:text-zinc-400" x-text="isCaptchaVerified ? (lang === 'en' ? 'Challenge passed! Click to view or reset' : 'Tantangan lolos! Klik untuk melihat atau mereset') : (lang === 'en' ? 'Follow clue and pin 3 scattered objects on image' : 'Ikuti petunjuk urutan dan pin 3 objek pada gambar')">
                                    {{ $isEn ? 'Follow clue and pin 3 scattered objects on image' : 'Ikuti petunjuk urutan dan pin 3 objek pada gambar' }}
                                </div>
                            </div>
                        </div>
                        <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                            <span x-text="isCaptchaVerified ? (lang === 'en' ? 'VERIFIED ✓' : 'TERVERIFIKASI ✓') : (lang === 'en' ? 'OPEN MODAL ➔' : 'BUKA MODAL ➔')">
                                {{ $isEn ? 'OPEN MODAL ➔' : 'BUKA MODAL ➔' }}
                            </span>
                        </span>
                    </button>
                </div>

                <!-- Forgot Password Back Link -->
                <div x-show="mode === 'forgot_password'" x-cloak class="text-right">
                    <button 
                        type="button" 
                        @click="switchMode('login')"
                        class="text-[11px] font-mono text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline cursor-pointer"
                    >
                        <span x-text="lang === 'en' ? '← Back to Login' : '← Kembali ke Login'">{{ $isEn ? '← Back to Login' : '← Kembali ke Login' }}</span>
                    </button>
                </div>

                <!-- Submit Button Step 1 (Enabled / Unlocked after Captcha is verified, or opens modal if clicked when ready) -->
                <button 
                    type="submit" 
                    :disabled="isLoading || !email || (mode !== 'forgot_password' && !password)"
                    :class="{
                        'bg-emerald-500 hover:bg-emerald-400 text-black border-emerald-600 shadow-md cursor-pointer animate-pulse': isCaptchaVerified || mode === 'forgot_password',
                        'bg-zinc-800 hover:bg-zinc-700 text-emerald-400 border-zinc-700 shadow-sm cursor-pointer': !isCaptchaVerified && mode !== 'forgot_password' && email && password,
                        'bg-zinc-200 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 border-zinc-300 dark:border-zinc-700 cursor-not-allowed': !isCaptchaVerified && mode !== 'forgot_password' && (!email || !password)
                    }"
                    class="w-full flex justify-center items-center gap-2 py-3 px-4 border text-xs font-mono font-black uppercase tracking-wider rounded-none transition-all"
                >
                    <span x-show="!isLoading && !isCaptchaVerified && mode !== 'forgot_password'" class="flex items-center gap-1.5">
                        <span>🛡️</span>
                        <span x-text="lang === 'en' ? 'SOLVE SNAP CAPTCHA TO CONTINUE →' : 'SELESAIKAN SNAP CAPTCHA UNTUK LANJUT →'">{{ $isEn ? 'SOLVE SNAP CAPTCHA TO CONTINUE →' : 'SELESAIKAN SNAP CAPTCHA UNTUK LANJUT →' }}</span>
                    </span>
                    <span x-show="!isLoading && (isCaptchaVerified || mode === 'forgot_password')" x-cloak>
                        <span x-text="mode === 'register' ? (lang === 'en' ? 'REGISTER & SEND 2FA OTP →' : 'DAFTAR & KIRIM KODE OTP →') : (mode === 'forgot_password' ? (lang === 'en' ? 'SEND RESET OTP CODE →' : 'KIRIM KODE OTP RESET →') : (lang === 'en' ? 'VERIFY PASSWORD & GET OTP →' : 'VERIFIKASI KATA SANDI & KIRIM OTP →'))">{{ $isEn ? 'VERIFY PASSWORD & GET OTP →' : 'VERIFIKASI KATA SANDI & KIRIM OTP →' }}</span>
                    </span>
                    <span x-show="isLoading" x-cloak class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="lang === 'en' ? 'VERIFYING CREDENTIALS...' : 'MEMVERIFIKASI KREDENSIAL...'">{{ $isEn ? 'VERIFYING CREDENTIALS...' : 'MEMVERIFIKASI KREDENSIAL...' }}</span>
                    </span>
                </button>
            </form>

            <!-- ============================================================= -->
            <!-- STEP 2: INPUT OTP (6-DIGIT EMAIL CODE)                        -->
            <!-- ============================================================= -->
            <form x-show="step === 'otp'" x-cloak @submit.prevent="verifyOtp" class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="otp-code" class="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">
                            <span x-text="lang === 'en' ? '6-Digit OTP Security Code' : 'Kode OTP Keamanan 6-Digit'">{{ $isEn ? '6-Digit OTP Security Code' : 'Kode OTP Keamanan 6-Digit' }}</span>
                        </label>
                        <button 
                            type="button" 
                            @click="step = 'credentials'" 
                            class="text-[11px] font-mono text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer"
                        >
                            <span x-text="lang === 'en' ? '← Change Password / Email' : '← Ubah Kata Sandi / Email'">{{ $isEn ? '← Change Password / Email' : '← Ubah Kata Sandi / Email' }}</span>
                        </button>
                    </div>

                    <div class="text-xs text-zinc-600 dark:text-zinc-400 mb-3 bg-zinc-50 dark:bg-zinc-950 p-2.5 rounded-none border border-zinc-200 dark:border-zinc-800 font-mono">
                        <span x-text="lang === 'en' ? 'Code dispatched to: ' : 'Kode dikirimkan ke: '">{{ $isEn ? 'Code dispatched to: ' : 'Kode dikirimkan ke: ' }}</span>
                        <strong class="text-zinc-900 dark:text-zinc-200" x-text="email"></strong>
                    </div>

                    <!-- 6-digit OTP Input -->
                    <input 
                        id="otp-code" 
                        type="text" 
                        x-model="otp" 
                        maxlength="6" 
                        pattern="[0-9]{6}" 
                        required 
                        placeholder="123456" 
                        class="w-full text-center tracking-[0.4em] font-mono text-2xl font-black bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-none outline-none transition-colors"
                        :disabled="isLoading"
                        autocomplete="one-time-code"
                        autofocus
                    />

                    <!-- New Password Input (Only when resetting password) -->
                    <div x-show="mode === 'forgot_password'" x-cloak class="mt-3">
                        <label class="block text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase mb-1">
                            <span x-text="lang === 'en' ? 'New Account Password (Min 8 Chars) *' : 'Kata Sandi Baru (Min 8 Karakter) *'">{{ $isEn ? 'New Account Password (Min 8 Chars) *' : 'Kata Sandi Baru (Min 8 Karakter) *' }}</span>
                        </label>
                        <input 
                            type="password" 
                            x-model="newPassword" 
                            :required="mode === 'forgot_password'"
                            class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 p-2 text-xs text-zinc-900 dark:text-white rounded-none outline-none"
                            placeholder="********"
                        />
                    </div>
                    
                    <div class="mt-2.5 flex items-center justify-between text-[11px] font-mono text-zinc-500">
                        <span x-text="lang === 'en' ? 'Valid for 10 minutes' : 'Berlaku selama 10 menit'">{{ $isEn ? 'Valid for 10 minutes' : 'Berlaku selama 10 menit' }}</span>
                        <button 
                            type="button" 
                            @click="requestOtp" 
                            :disabled="isLoading || resendCountdown > 0"
                            class="text-emerald-600 dark:text-emerald-400 hover:underline disabled:opacity-40 disabled:hover:no-underline cursor-pointer"
                        >
                            <span x-show="resendCountdown === 0" x-text="lang === 'en' ? 'Resend OTP' : 'Kirim Ulang OTP'">{{ $isEn ? 'Resend OTP' : 'Kirim Ulang OTP' }}</span>
                            <span x-show="resendCountdown > 0" x-cloak x-text="(lang === 'en' ? 'Wait ' : 'Tunggu ') + resendCountdown + 's'"></span>
                        </button>
                    </div>
                </div>

                <!-- Submit Button Step 2 -->
                <button 
                    type="submit" 
                    :disabled="isLoading || otp.length !== 6"
                    class="w-full flex justify-center items-center gap-2 py-3 px-4 border text-xs font-mono font-black uppercase tracking-wider rounded-none transition-all bg-emerald-500 hover:bg-emerald-400 text-black border-emerald-600 shadow-md disabled:bg-zinc-200 dark:disabled:bg-zinc-800 disabled:text-zinc-400 disabled:cursor-not-allowed cursor-pointer"
                >
                    <span x-show="!isLoading" x-text="mode === 'forgot_password' ? (lang === 'en' ? 'RESET PASSWORD & SIGN IN →' : 'RESET SANDI & MASUK →') : (lang === 'en' ? 'AUTHENTICATE & ENTER PORTAL →' : 'VERIFIKASI & MASUK PORTAL →')">{{ $isEn ? 'VERIFY & ENTER PORTAL →' : 'VERIFIKASI & MASUK PORTAL →' }}</span>
                    <span x-show="isLoading" x-cloak class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-black" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="lang === 'en' ? 'AUTHENTICATING OTP...' : 'MEMVALIDASI OTP...'">{{ $isEn ? 'AUTHENTICATING OTP...' : 'MEMVALIDASI OTP...' }}</span>
                    </span>
                </button>
            </form>

            <!-- Bottom Navigation & Help -->
            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 text-center font-mono text-[11px] text-zinc-500 space-y-1.5">
                <div>
                    <span x-text="lang === 'en' ? 'Need an architectural blueprint?' : 'Belum memiliki cetak biru proyek?'">{{ $isEn ? 'Need an architectural blueprint?' : 'Belum memiliki cetak biru proyek?' }}</span>
                    <a href="/blueprint" class="text-emerald-600 dark:text-emerald-400 hover:underline">Spark Free Audit</a> /
                    <a href="/pricing" class="text-emerald-600 dark:text-emerald-400 hover:underline" x-text="lang === 'en' ? 'View Pricing' : 'Lihat Paket'">{{ $isEn ? 'View Pricing' : 'Lihat Paket' }}</a>
                </div>
                <div>
                    <span x-text="lang === 'en' ? 'Internal Team Superadmin?' : 'Akses Administrator Tim?'">{{ $isEn ? 'Internal Team Superadmin?' : 'Akses Administrator Tim?' }}</span>
                    <a href="/admin/login" class="text-zinc-600 dark:text-zinc-300 hover:underline font-bold" x-text="lang === 'en' ? 'Admin Control Center →' : 'Login Control Center →'">{{ $isEn ? 'Admin Control Center →' : 'Login Control Center →' }}</a>
                </div>
            </div>

        </div>
    </main>

    <!-- FOOTER -->
    <footer class="border-t border-zinc-200 dark:border-zinc-800 py-4 bg-white/50 dark:bg-zinc-900/50 text-center font-mono text-[11px] text-zinc-500">
        <p>&copy; {{ date('Y') }} Neriah Pro. <span x-text="lang === 'en' ? 'All rights reserved. Secure Dual-Factor Client Workspace.' : 'Hak cipta dilindungi. Sistem Autentikasi Ganda 2-FA Portal Klien.'">{{ $isEn ? 'All rights reserved. Secure Dual-Factor Client Workspace.' : 'Hak cipta dilindungi. Sistem Autentikasi Ganda 2-FA Portal Klien.' }}</span></p>
    </footer>

    <!-- ===================================================================== -->
    <!-- VISUAL SEQUENCE PIN CAPTCHA SNAP MODAL DIALOG                         -->
    <!-- ===================================================================== -->
    <div 
        x-show="isCaptchaModalOpen" 
        x-cloak 
        @keydown.escape.window="closeCaptchaModal()" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-md transition-all duration-200"
        role="dialog"
        aria-modal="true"
    >
        <!-- Modal Backdrop Click Dismiss -->
        <div class="absolute inset-0" @click="closeCaptchaModal()"></div>

        <!-- Modal Dialog Box -->
        <div 
            class="relative w-full max-w-lg bg-zinc-900 border-2 border-emerald-500/70 shadow-2xl p-4 sm:p-5 space-y-3.5 font-sans z-10 rounded-none animate-in fade-in zoom-in-95 duration-200 text-zinc-100"
            @click.stop
        >
            <!-- Modal Header -->
            <div class="flex items-start justify-between border-b border-zinc-800 pb-2.5">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-500/10 text-emerald-400 font-mono text-[10px] font-bold uppercase tracking-wider border border-emerald-500/20 mb-1">
                        <span class="w-1.5 h-1.5 rounded-none bg-emerald-500 animate-pulse"></span>
                        <span x-text="lang === 'en' ? 'SECURITY CHALLENGE // OBJECT PINPOINT CANVAS' : 'TANTANGAN KEAMANAN // PINPOINT OBJEK GAMBAR'">TANTANGAN KEAMANAN // PINPOINT OBJEK GAMBAR</span>
                    </div>
                    <h3 class="text-base font-black uppercase text-white font-sans tracking-tight"
                        x-text="lang === 'en' ? 'Pin 3 Objects in Sequence' : 'Pin 3 Objek Gambar Berurutan'">
                        {{ $isEn ? 'Pin 3 Objects in Sequence' : 'Pin 3 Objek Gambar Berurutan' }}
                    </h3>
                    <p class="text-xs text-zinc-400 font-sans mt-0.5"
                       x-text="lang === 'en' ? 'Follow the sequence clue below and click to pin 3 objects directly on the image.' : 'Ikuti petunjuk urutan di bawah, lalu klik dan pasang pin pada 3 objek di dalam gambar.'">
                        {{ $isEn ? 'Follow the sequence clue below and click to pin 3 objects directly on the image.' : 'Ikuti petunjuk urutan di bawah, lalu klik dan pasang pin pada 3 objek di dalam gambar.' }}
                    </p>
                </div>
                <button 
                    type="button" 
                    @click="closeCaptchaModal()" 
                    class="text-zinc-400 hover:text-zinc-200 text-lg font-mono p-1 rounded-none cursor-pointer"
                    title="Tutup / Close"
                >
                    ✕
                </button>
            </div>

            <!-- Target Clue Sequence Box -->
            <div class="p-2.5 bg-zinc-950 border border-zinc-800 rounded-none space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono text-zinc-400 uppercase font-bold flex items-center gap-1">
                        <span>🎯</span>
                        <span x-text="lang === 'en' ? 'Target Sequence Clue (Follow in Order):' : 'Petunjuk Urutan Pin (Ikuti Berurutan):'">
                            {{ $isEn ? 'Target Sequence Clue (Follow in Order):' : 'Petunjuk Urutan Pin (Ikuti Berurutan):' }}
                        </span>
                    </span>
                    <button 
                        type="button" 
                        @click="generateCaptcha()" 
                        class="text-[10px] font-mono text-emerald-400 hover:underline flex items-center gap-1 cursor-pointer font-bold"
                        title="Acak Ulang Gambar & Petunjuk"
                    >
                        <span>🔄</span>
                        <span x-text="lang === 'en' ? 'Reshuffle Scene' : 'Acak Ulang'">{{ $isEn ? 'Reshuffle Scene' : 'Acak Ulang' }}</span>
                    </button>
                </div>

                <!-- Reference Example Icons Sequence Clue -->
                <div class="flex items-center justify-center gap-1.5 sm:gap-2 py-0.5 min-h-[44px]">
                    <template x-for="(item, idx) in targetCaptchaSequence" :key="item.id">
                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <div 
                                class="flex items-center gap-1.5 px-2.5 py-1 border font-mono transition-all duration-150"
                                :class="pinnedCaptchaSequence.length > idx && pinnedCaptchaSequence[idx].id === item.id 
                                    ? 'bg-emerald-500/20 border-emerald-400 text-emerald-300 ring-1 ring-emerald-400/50' 
                                    : 'bg-zinc-900 border-zinc-700 text-zinc-200'"
                            >
                                <span 
                                    class="text-[11px] font-black px-1 rounded-none"
                                    :class="pinnedCaptchaSequence.length > idx && pinnedCaptchaSequence[idx].id === item.id ? 'bg-emerald-500 text-black' : 'bg-zinc-800 text-emerald-400'"
                                    x-text="(idx + 1)"
                                ></span>
                                <span class="text-lg" x-text="item.icon"></span>
                                <span class="text-[10px] font-bold hidden sm:inline" x-text="lang === 'en' ? item.label_en : item.label_id"></span>
                                <span x-show="pinnedCaptchaSequence.length > idx && pinnedCaptchaSequence[idx].id === item.id" x-cloak class="text-emerald-400 text-xs font-black">✓</span>
                            </div>
                            <span x-show="idx < targetCaptchaSequence.length - 1" class="text-zinc-600 text-xs font-bold">➔</span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Visual Scattered Objects Image Canvas -->
            <div class="relative w-full h-72 sm:h-80 bg-zinc-950 border border-zinc-700 select-none overflow-hidden rounded-none shadow-inner">
                <!-- Blueprint Grid Architectural Pattern -->
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#10b98115_1px,transparent_1px),linear-gradient(to_bottom,#10b98115_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none"></div>
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,#10b98112_0%,transparent_75%)] pointer-events-none"></div>

                <!-- Subtle moving scanline -->
                <div class="absolute inset-x-0 h-14 bg-gradient-to-b from-transparent via-emerald-500/10 to-transparent pointer-events-none animate-pulse"></div>

                <!-- HUD Metadata Overlays -->
                <div class="absolute top-2 left-2.5 flex items-center gap-1.5 text-[9px] font-mono text-zinc-500 tracking-wider pointer-events-none">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-none animate-ping"></span>
                    <span>SCENE-GRID // OBJECT-MATRIX [LIVE]</span>
                </div>
                <div class="absolute top-2 right-2.5 text-[9px] font-mono text-zinc-500 tracking-wider pointer-events-none">
                    <span>X: 104.22° // Y: 72.84°</span>
                </div>
                <div class="absolute bottom-2 left-2.5 text-[9px] font-mono text-zinc-600 pointer-events-none">
                    <span>NERIAH-SEC // PINPOINT LAYER</span>
                </div>

                <!-- Corner Crosshair Brackets -->
                <div class="absolute top-1 left-1 w-2.5 h-2.5 border-t-2 border-l-2 border-emerald-500/40 pointer-events-none"></div>
                <div class="absolute top-1 right-1 w-2.5 h-2.5 border-t-2 border-r-2 border-emerald-500/40 pointer-events-none"></div>
                <div class="absolute bottom-1 left-1 w-2.5 h-2.5 border-b-2 border-l-2 border-emerald-500/40 pointer-events-none"></div>
                <div class="absolute bottom-1 right-1 w-2.5 h-2.5 border-b-2 border-r-2 border-emerald-500/40 pointer-events-none"></div>

                <!-- Scattered Interactive Objects on Image Canvas -->
                <template x-for="item in scatteredSceneObjects" :key="item.id">
                    <button
                        type="button"
                        @click="pinCaptchaItem(item)"
                        :disabled="isCaptchaVerified"
                        :style="`left: ${item.x}%; top: ${item.y}%; transform: translate(-50%, -50%) rotate(${item.rotation}deg);`"
                        class="absolute p-1 flex flex-col items-center justify-center transition-all duration-150 cursor-pointer group focus:outline-none"
                        :class="{
                            'scale-110 z-20': getPinIndex(item) !== null,
                            'hover:scale-115 hover:z-10 z-10': getPinIndex(item) === null,
                            'opacity-50 cursor-default': isCaptchaVerified
                        }"
                    >
                        <!-- Object Box / Pin Target Container -->
                        <div 
                            class="relative w-11 h-11 sm:w-12 sm:h-12 flex items-center justify-center border transition-all duration-150 shadow-md"
                            :class="getPinIndex(item) !== null 
                                ? 'border-emerald-400 bg-emerald-500/25 ring-2 ring-emerald-400/60 shadow-[0_0_15px_rgba(16,185,129,0.5)]' 
                                : 'border-zinc-700 bg-zinc-900/90 hover:border-emerald-500/80 hover:bg-zinc-800/95'"
                        >
                            <!-- Pinned Sequence Flag Badge -->
                            <span 
                                x-show="getPinIndex(item) !== null"
                                x-cloak
                                class="absolute -top-3 -right-3 px-1.5 py-0.5 bg-emerald-500 text-black font-mono font-black text-[9px] tracking-tight flex items-center gap-0.5 border border-black shadow-lg rounded-none animate-bounce"
                            >
                                <span class="text-[8px]">📌</span>
                                <span x-text="'PIN #' + getPinIndex(item)"></span>
                            </span>

                            <!-- Target Crosshair Lines when Pinned -->
                            <span x-show="getPinIndex(item) !== null" x-cloak class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-40">
                                <span class="w-full h-px bg-emerald-400"></span>
                                <span class="h-full w-px bg-emerald-400 absolute"></span>
                            </span>

                            <!-- Object Icon -->
                            <span class="text-2xl filter drop-shadow-md select-none" x-text="item.icon"></span>
                        </div>

                        <!-- Object Name Tooltip -->
                        <span 
                            class="mt-1 px-1.5 py-0.5 text-[9px] font-mono font-bold tracking-tight rounded-none shadow-xs border transition-colors select-none"
                            :class="getPinIndex(item) !== null 
                                ? 'bg-emerald-500 text-black border-emerald-400' 
                                : 'bg-zinc-900/90 text-zinc-300 border-zinc-700 group-hover:border-zinc-500'"
                            x-text="lang === 'en' ? item.label_en : item.label_id"
                        ></span>
                    </button>
                </template>

                <!-- Success HUD Scan Overlay -->
                <div 
                    x-show="isCaptchaVerified" 
                    x-cloak 
                    class="absolute inset-0 bg-emerald-950/90 backdrop-blur-xs flex flex-col items-center justify-center text-center p-4 z-30 transition-all"
                >
                    <div class="w-10 h-10 bg-emerald-500 text-black flex items-center justify-center font-mono font-black text-xl mb-2 shadow-lg rounded-none">✓</div>
                    <h4 class="text-sm font-mono font-black text-emerald-300 uppercase tracking-widest" x-text="lang === 'en' ? 'CHALLENGE PASSED // VERIFIED' : 'TANTANGAN BERHASIL // TERVERIFIKASI'"></h4>
                    <p class="text-[11px] font-mono text-emerald-400/90 mt-1" x-text="lang === 'en' ? 'Object pins verified. Unlocking login...' : 'Pin objek sesuai petunjuk. Membuka login...'"></p>
                </div>

                <!-- Error HUD Flash Bar -->
                <div 
                    x-show="captchaError" 
                    x-cloak 
                    class="absolute inset-x-0 bottom-0 bg-rose-950/95 border-t border-rose-500 p-2.5 flex items-center justify-between text-[11px] font-mono text-rose-300 z-30"
                >
                    <span class="flex items-center gap-1.5">
                        <span class="font-bold">⚠️</span>
                        <span x-text="captchaError"></span>
                    </span>
                    <span class="text-[10px] text-rose-400 uppercase font-bold animate-pulse" x-text="lang === 'en' ? 'Auto-resetting pins...' : 'Mereset pin otomatis...'"></span>
                </div>
            </div>

            <!-- Pin Progress Bar Below Canvas -->
            <div class="flex items-center justify-between p-2 bg-zinc-950 border border-zinc-800 text-[11px] font-mono">
                <div class="flex items-center gap-2">
                    <span class="text-zinc-500 uppercase font-bold" x-text="lang === 'en' ? 'Pinned:' : 'Terpin:'">Terpin:</span>
                    <div class="flex items-center gap-1.5">
                        <template x-for="idx in [0, 1, 2]" :key="idx">
                            <span 
                                class="px-2 py-0.5 border text-[10px] flex items-center gap-1"
                                :class="pinnedCaptchaSequence.length > idx 
                                    ? 'border-emerald-500 bg-emerald-500/15 text-emerald-400' 
                                    : 'border-zinc-800 bg-zinc-900 text-zinc-600'"
                            >
                                <span class="font-bold" x-text="'#' + (idx + 1)"></span>
                                <span x-text="pinnedCaptchaSequence.length > idx ? (pinnedCaptchaSequence[idx].icon + ' ' + (lang === 'en' ? pinnedCaptchaSequence[idx].label_en : pinnedCaptchaSequence[idx].label_id)) : '—'"></span>
                            </span>
                        </template>
                    </div>
                </div>
                <span class="text-emerald-400 font-bold" x-text="pinnedCaptchaSequence.length + ' / 3'"></span>
            </div>

            <!-- Modal Footer Actions -->
            <div class="flex items-center justify-between pt-2 border-t border-zinc-800 font-mono text-xs">
                <button 
                    type="button" 
                    @click="closeCaptchaModal()" 
                    class="px-3.5 py-1.5 border border-zinc-700 text-zinc-400 hover:text-white rounded-none cursor-pointer"
                >
                    <span x-text="lang === 'en' ? 'Cancel' : 'Batal'">{{ $isEn ? 'Cancel' : 'Batal' }}</span>
                </button>
                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        @click="resetPins()" 
                        :disabled="pinnedCaptchaSequence.length === 0 || isCaptchaVerified"
                        class="px-3 py-1.5 border border-zinc-700 bg-zinc-800 text-zinc-300 hover:bg-zinc-700 disabled:opacity-40 disabled:cursor-not-allowed rounded-none cursor-pointer flex items-center gap-1"
                    >
                        <span>↺</span>
                        <span x-text="lang === 'en' ? 'Reset Pins' : 'Reset Pin'">{{ $isEn ? 'Reset Pins' : 'Reset Pin' }}</span>
                    </button>
                    <button 
                        type="button" 
                        @click="generateCaptcha()" 
                        class="px-3 py-1.5 border border-emerald-600 bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 rounded-none cursor-pointer flex items-center gap-1 font-bold"
                    >
                        <span>🔄</span>
                        <span x-text="lang === 'en' ? 'Reshuffle Scene' : 'Acak Gambar'">{{ $isEn ? 'Reshuffle Scene' : 'Acak Gambar' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($googleTranslateEnabled)
    <!-- Google Translate Container & Bridge (Tier 2) -->
    <div id="google_translate_element" class="hidden"></div>
    <script>
        function googleTranslateElementInit() {
            try {
                new google.translate.TranslateElement({
                    pageLanguage: '{{ $locale }}',
                    includedLanguages: '{{ implode(",", $allowedLangList) }}',
                    autoDisplay: false
                }, 'google_translate_element');
            } catch(e) {}
        }
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async defer></script>
    @endif
</body>
</html>
