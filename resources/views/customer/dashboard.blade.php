@php
    $isEn = app()->getLocale() === 'en';
    $whatsappNumber = \App\Models\CmsGlobalSetting::getVal('company_whatsapp', '628123456789');
    $isMidtransStrict = (bool) \App\Models\CmsGlobalSetting::getVal('midtrans_compliance_strict_mode', true);
    $cvProFlag = (bool) \App\Models\CmsGlobalSetting::getVal('feature_enable_cv_pro', false);
    $isCvProEnabled = !$isMidtransStrict && $cvProFlag;

    // Customer Initials Algorithm: first letter of each word (e.g. "Yoseph Iriandi Tambunan" => "YIT")
    $customerDisplayName = trim($user->name ?: ($user->email ?: 'Client'));
    $nameWords = preg_split('/\s+/', $customerDisplayName);
    $customerInitials = '';
    foreach ($nameWords as $w) {
        if (!empty($w)) {
            $customerInitials .= mb_substr($w, 0, 1);
        }
    }
    $customerInitials = strtoupper(mb_substr($customerInitials, 0, 4));
    if (empty($customerInitials)) {
        $customerInitials = 'U';
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Enterprise Browser Extension Noise Shield (Early Inception - Placed at Top of Head) -->
    <script>
        (function() {
            var isNoise = function(v) {
                if (!v) return false;
                var s = typeof v === 'string' ? v : (v.message || v.stack || v.description || String(v));
                return s.indexOf('Could not establish connection') !== -1 ||
                       s.indexOf('Receiving end does not exist') !== -1 ||
                       s.indexOf('message port closed') !== -1 ||
                       s.indexOf('Extension context invalidated') !== -1 ||
                       s.indexOf('A listener indicated an asynchronous response') !== -1 ||
                       s.indexOf('chrome.runtime.sendMessage') !== -1 ||
                       s.indexOf('chrome-extension://') !== -1 ||
                       s.indexOf('moz-extension://') !== -1;
            };

            // Intercept console logging
            ['error', 'warn', 'log'].forEach(function(method) {
                var _orig = console[method];
                if (typeof _orig !== 'function') return;
                console[method] = function() {
                    for (var i = 0; i < arguments.length; i++) {
                        if (isNoise(arguments[i])) return;
                    }
                    return _orig.apply(console, arguments);
                };
            });

            // Intercept unhandled promise rejections (captured before browser devtools)
            window.addEventListener('unhandledrejection', function(e) {
                if (isNoise(e ? e.reason : '')) {
                    e.preventDefault();
                    if (e.stopImmediatePropagation) e.stopImmediatePropagation();
                    if (e.stopPropagation) e.stopPropagation();
                    return false;
                }
            }, true);

            // Intercept uncaught runtime error events
            window.addEventListener('error', function(e) {
                var text = (e ? e.message : '') + ' ' + (e && e.error ? (e.error.message || e.error.stack) : '');
                if (isNoise(text)) {
                    e.preventDefault();
                    if (e.stopImmediatePropagation) e.stopImmediatePropagation();
                    if (e.stopPropagation) e.stopPropagation();
                    return false;
                }
            }, true);

            // Intercept window.onerror fallback
            var _origOnError = window.onerror;
            window.onerror = function(message, source, lineno, colno, error) {
                var msg = (message || '') + ' ' + (error ? (error.message || '' + error.stack) : '');
                if (isNoise(msg) || (source && isNoise(source))) {
                    return true;
                }
                if (typeof _origOnError === 'function') {
                    return _origOnError.apply(this, arguments);
                }
                return false;
            };
        })();
    </script>

    <title>neriahpro.com - {{ $isEn ? 'Client Portal & Project Workspace' : 'Portal Pelanggan & Workspace Proyek' }}</title>
    <meta name="description" content="{{ $isEn ? 'Unified client workspace to track software sprints, access lifetime license downloads, and view tax invoices.' : 'Workspace terpadu pelanggan untuk memantau sprint software, mengunduh lisensi seumur hidup, dan mengakses faktur pajak.' }}">

    <!-- Local Fonts (Zero External Latency) -->
    <link rel="stylesheet" href="{{ asset('fonts/instrument-sans/instrument-sans.css') }}">

    <!-- Vite Styles & Scripts -->
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Midtrans Snap JS (In-Page Popup Modal) -->
    <script src="{{ config('midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/snap.js') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <!-- Alpine.js & Tab Navigation Support (Local Vendor JS) -->
    <style>
        [x-cloak] { display: none !important; }

        /* Eradicate Google Translate Banner Bar & Body Push */
        .goog-te-banner-frame,
        .goog-te-banner-frame.skiptranslate,
        iframe.goog-te-banner-frame,
        #goog-gt-tt,
        .goog-te-balloon-frame {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
            position: absolute !important;
            top: -9999px !important;
        }

        body {
            top: 0px !important;
            position: static !important;
        }

        body > .skiptranslate {
            display: none !important;
        }

        .goog-text-highlight {
            background-color: transparent !important;
            box-shadow: none !important;
            border: none !important;
        }
    </style>
    <script>
        setInterval(function() {
            if (document.body && document.body.style && document.body.style.top && document.body.style.top !== '0px') {
                document.body.style.top = '0px';
            }
            var banner = document.querySelector('.goog-te-banner-frame');
            if (banner) banner.style.display = 'none';
        }, 100);
    </script>
    <script defer src="{{ asset('js/vendor/alpine.min.js') }}"></script>

    <script>
        window.payPelunasanSnap = async function(slug, onStart, onFinish) {
            if (onStart) onStart();
            try {
                if (window.showToast) {
                    window.showToast({
                        type: 'info',
                        title: 'MEMBUAT SESI PELUNASAN',
                        message: 'Menghubungkan ke gateway Midtrans Sandbox...'
                    });
                }
                const res = await fetch('/blueprint/' + encodeURIComponent(slug) + '/pelunasan-snap-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                    }
                });
                const data = await res.json();
                if (!data.success || !data.token) {
                    throw new Error(data.message || 'Gagal membuat sesi token pelunasan.');
                }

                if (window.snap && typeof window.snap.pay === 'function') {
                    window.snap.pay(data.token, {
                        onSuccess: function(result) {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'success',
                                    title: 'PELUNASAN BERHASIL!',
                                    message: 'Pembayaran pelunasan 50% berhasil diverifikasi. Memperbarui status proyek...',
                                    duration: 3500
                                });
                            }
                            setTimeout(function() { window.location.reload(); }, 1800);
                        },
                        onPending: function(result) {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'warning',
                                    title: 'MENUNGGU PEMBAYARAN',
                                    message: 'Instruksi pembayaran pelunasan telah dibuat. Silakan transfer sesuai rincian.',
                                    duration: 5000
                                });
                            }
                            setTimeout(function() { window.location.reload(); }, 2200);
                        },
                        onError: function(result) {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'error',
                                    title: 'PELUNASAN GAGAL',
                                    message: 'Transaksi pelunasan dibatalkan atau ditolak.'
                                });
                            }
                            if (onFinish) onFinish();
                        },
                        onClose: function() {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'info',
                                    title: 'PROMPT DITUTUP',
                                    message: 'Anda dapat menekan tombol bayar pelunasan kembali untuk melanjutkan.'
                                });
                            }
                            if (onFinish) onFinish();
                        }
                    });
                } else if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    throw new Error('Midtrans Snap tidak tersedia. Silakan periksa koneksi internet Anda.');
                }
            } catch (err) {
                if (window.showToast) {
                    window.showToast({
                        type: 'error',
                        title: 'KENDALA PELUNASAN',
                        message: err.message || 'Terjadi kesalahan sistem saat menghubungi gateway Midtrans.'
                    });
                }
                if (onFinish) onFinish();
            }
        };

        window.payRetailSnap = function(token) {
            if (!token) {
                if (window.showToast) {
                    window.showToast({
                        type: 'error',
                        title: 'TOKEN TIDAK DITEMUKAN',
                        message: 'Token Midtrans Snap tidak tersedia. Silakan hubungi tim kami atau gunakan tombol Cek Status.'
                    });
                } else {
                    alert('Token Midtrans Snap tidak ditemukan.');
                }
                return;
            }

            if (window.snap && typeof window.snap.pay === 'function') {
                window.snap.pay(token, {
                    onSuccess: function(result) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'success',
                                title: 'PEMBAYARAN LUNAS!',
                                message: 'Pembayaran lisensi berhasil diverifikasi. Memperbarui lisensi digital Anda...',
                                duration: 3500
                            });
                        }
                        setTimeout(function() { window.location.reload(); }, 1800);
                    },
                    onPending: function(result) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'warning',
                                title: 'MENUNGGU PEMBAYARAN',
                                message: 'Instruksi pembayaran telah dibuat. Silakan selesaikan pembayaran.',
                                duration: 5000
                            });
                        }
                        setTimeout(function() { window.location.reload(); }, 2200);
                    },
                    onError: function(result) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'error',
                                title: 'PEMBAYARAN DIBATALKAN',
                                message: 'Transaksi pembayaran lisensi belum diselesaikan.'
                            });
                        }
                    },
                    onClose: function() {
                        if (window.showToast) {
                            window.showToast({
                                type: 'info',
                                title: 'MODAL DITUTUP',
                                message: 'Anda dapat menekan tombol bayar kembali kapan saja.'
                            });
                        }
                    }
                });
            } else {
                if (window.showToast) {
                    window.showToast({
                        type: 'error',
                        title: 'MIDTRANS SNAP ERROR',
                        message: 'Midtrans Snap SDK gagal dimuat. Periksa koneksi internet Anda.'
                    });
                }
            }
        };

        if (localStorage.getItem('neriah_theme') === 'dark' || (!localStorage.getItem('neriah_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('neriah_theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('neriah_theme', 'dark');
            }
        }

        function changeTier1Language(locale) {
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + window.location.hostname;
            document.cookie = 'neriah_locale=' + locale + ';path=/;max-age=31536000';
            window.location.href = '/lang/' + locale;
        }

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

        function translateLanguage(langCode, langName) {
            const resolvedName = langName || langNamesMap[langCode] || langCode.toUpperCase();
            
            // Show translation loader modal
            if (window.customerDashboardAppInstance) {
                window.customerDashboardAppInstance.translatingLanguageName = resolvedName;
                window.customerDashboardAppInstance.isTranslating = true;
                window.customerDashboardAppInstance.langDropdownOpen = false;
            }

            document.cookie = 'googtrans=/id/' + langCode + '; path=/; domain=' + window.location.hostname;
            document.cookie = 'googtrans=/id/' + langCode + '; path=/;';

            const select = document.querySelector('.goog-te-combo');
            if (select) {
                select.value = langCode;
                select.dispatchEvent(new Event('change'));
                setTimeout(() => {
                    if (window.customerDashboardAppInstance) {
                        window.customerDashboardAppInstance.isTranslating = false;
                    }
                }, 1200);
            } else {
                setTimeout(() => {
                    window.location.reload();
                }, 400);
            }
        }

        function customerDashboardApp() {
            return {
                currentTab: (window.location.hash ? window.location.hash.replace('#', '') : 'overview'),
                servicesDropdownOpen: false,
                langDropdownOpen: false,
                mobileMenuOpen: false,
                userMenuModalOpen: false,
                isTranslating: false,
                translatingLanguageName: '',

                init() {
                    window.customerDashboardAppInstance = this;
                },
                
                // Profile & Billing state
                profileName: @json($profileDefaults['name'] ?? ''),
                profileCompany: @json($profileDefaults['company_name'] ?? ''),
                selectedCountryCode: @json($profileDefaults['phone_country_code'] ?? '+62'),
                profilePhone: @json($profileDefaults['phone'] ?? ''),
                profileNpwp: @json($profileDefaults['npwp'] ?? ''),
                profileAddress: @json($profileDefaults['billing_address'] ?? ''),
                profileCity: @json($profileDefaults['billing_city'] ?? ''),
                profileProvince: @json($profileDefaults['billing_province'] ?? ''),
                profilePostalCode: @json($profileDefaults['billing_postal_code'] ?? ''),
                notifyEmailSprints: {{ ($profileDefaults['notification_preferences']['email_sprints'] ?? true) ? 'true' : 'false' }},
                notifyWaBilling: {{ ($profileDefaults['notification_preferences']['wa_billing'] ?? true) ? 'true' : 'false' }},
                isSavingProfile: false,
                profileSuccessMsg: '',
                profileErrorMsg: '',
                
                // Country Zone Selector state
                countryDropdownOpen: false,
                countrySearch: '',
                countryZones: @json($countryZones ?? []),

                filteredCountryZones() {
                    if (!this.countrySearch || !this.countrySearch.trim()) {
                        return this.countryZones;
                    }
                    const q = this.countrySearch.toLowerCase().trim();
                    return this.countryZones.filter(z => 
                        (z.name && z.name.toLowerCase().includes(q)) || 
                        (z.dial_code && z.dial_code.includes(q)) || 
                        (z.code && z.code.toLowerCase().includes(q))
                    );
                },

                selectCountry(zone) {
                    this.selectedCountryCode = zone.dial_code;
                    this.countryDropdownOpen = false;
                    this.countrySearch = '';
                },

                getSelectedZone() {
                    return this.countryZones.find(z => z.dial_code === this.selectedCountryCode) || {
                        code: 'ID',
                        dial_code: '+62',
                        name: 'Indonesia',
                        flag: '🇮🇩'
                    };
                },

                async saveProfile() {
                    if (!this.profileName || !this.profileName.trim()) {
                        this.profileErrorMsg = 'Nama lengkap wajib diisi.';
                        if (window.showToast) {
                            window.showToast({ type: 'error', title: 'VALIDASI GAGAL', message: 'Nama lengkap wajib diisi.' });
                        }
                        return;
                    }

                    this.isSavingProfile = true;
                    this.profileSuccessMsg = '';
                    this.profileErrorMsg = '';

                    try {
                        const res = await fetch('/api/customer/profile', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({
                                name: this.profileName,
                                company_name: this.profileCompany,
                                phone_country_code: this.selectedCountryCode,
                                phone: this.profilePhone,
                                npwp: this.profileNpwp,
                                billing_address: this.profileAddress,
                                billing_city: this.profileCity,
                                billing_province: this.profileProvince,
                                billing_postal_code: this.profilePostalCode,
                                notify_email_sprints: this.notifyEmailSprints,
                                notify_wa_billing: this.notifyWaBilling,
                            })
                        });

                        const data = await res.json();
                        if (!res.ok || !data.success) {
                            throw new Error(data.message || 'Gagal menyimpan profil.');
                        }

                        this.profileSuccessMsg = data.message || 'Profil berhasil disimpan!';
                        if (window.showToast) {
                            window.showToast({
                                type: 'success',
                                title: 'PROFIL DIPERBARUI',
                                message: this.profileSuccessMsg
                            });
                        }
                        setTimeout(() => { this.profileSuccessMsg = ''; }, 5000);
                    } catch (err) {
                        this.profileErrorMsg = err.message || 'Terjadi kesalahan sistem.';
                        if (window.showToast) {
                            window.showToast({
                                type: 'error',
                                title: 'GAGAL MENYIMPAN',
                                message: this.profileErrorMsg
                            });
                        }
                    } finally {
                        this.isSavingProfile = false;
                    }
                },
                
                init() {
                    const allowed = ['overview', 'projects', 'licenses', 'billing', 'assets', 'account'];
                    if (!allowed.includes(this.currentTab)) {
                        this.currentTab = 'overview';
                    }
                    window.addEventListener('hashchange', () => {
                        const hash = window.location.hash.replace('#', '');
                        if (allowed.includes(hash)) {
                            this.currentTab = hash;
                        }
                    });
                },

                setTab(tab) {
                    this.currentTab = tab;
                    if (window.history && window.history.replaceState) {
                        try {
                            window.history.replaceState(null, '', '#' + tab);
                        } catch(e) {}
                    } else {
                        window.location.hash = tab;
                    }
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                async logoutCustomer() {
                    try {
                        await fetch('/api/customer/logout', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            }
                        });
                        window.location.href = '/customer/login';
                    } catch(e) {
                        window.location.href = '/';
                    }
                }
            };
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('customerDashboardApp', customerDashboardApp);
        });
    </script>
</head>
<body class="bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen transition-colors duration-200" x-data="customerDashboardApp()">

    <!-- 1. TOP UTILITY HEADER (FULL SITE NAVIGATION + DUAL TIER LANGUAGE + THEME TOGGLE) -->
    <header class="border-b border-zinc-200 dark:border-zinc-800 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            
            <!-- Left: Brand & Portal Badge -->
            <div class="flex items-center gap-3 shrink-0">
                <a href="/" class="flex items-center gap-2 group">
                    <span class="w-8 h-8 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-black text-sm flex items-center justify-center rounded-none shadow-xs group-hover:scale-105 transition-transform">
                        N
                    </span>
                    <span class="font-mono text-sm font-black tracking-tight text-zinc-900 dark:text-white uppercase">
                        Neriah<span class="text-emerald-500">Pro</span>
                    </span>
                </a>
                <span class="text-zinc-300 dark:text-zinc-700 hidden sm:inline">/</span>
                <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold uppercase tracking-wider rounded-none border border-emerald-500/20 hidden sm:inline-block">
                    {{ $isEn ? 'CLIENT WORKSPACE' : 'WORKSPACE PELANGGAN' }}
                </span>
            </div>

            <!-- Center: Desktop Standard Navigation Menus -->
            <nav class="hidden lg:flex items-center gap-6 font-mono text-xs uppercase tracking-wider font-bold">
                <a href="/" class="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
                    {{ $isEn ? 'Home' : 'Beranda' }}
                </a>

                <!-- Dropdown: Layanan HUB -->
                <div 
                    class="relative"
                    @mouseenter="servicesDropdownOpen = true"
                    @mouseleave="servicesDropdownOpen = false"
                >
                    <button 
                        type="button"
                        @click="servicesDropdownOpen = !servicesDropdownOpen"
                        class="flex items-center gap-1 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition py-2 cursor-pointer"
                    >
                        <span>{{ $isEn ? 'Services Hub' : 'Layanan HUB' }}</span>
                        <svg class="w-3 h-3 text-zinc-400 transition-transform duration-150" :class="servicesDropdownOpen ? 'rotate-180 text-emerald-500' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div 
                        x-show="servicesDropdownOpen" 
                        x-cloak 
                        class="absolute top-full left-0 w-72 bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 rounded-none shadow-2xl p-2.5 space-y-1 text-left z-50 font-sans"
                    >
                        <a href="/blueprint" class="block p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-bold text-zinc-900 dark:text-white text-xs font-mono">Project OS (PRD)</span>
                                <span class="px-1 py-0.2 bg-emerald-500 text-black text-[9px] font-mono font-bold">ACTIVE</span>
                            </div>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-sans leading-tight">
                                {{ $isEn ? 'Automated PRD & ERD Database Architecture' : 'Generator PRD & Skema ERD Otomatis' }}
                            </p>
                        </a>

                        @if($isCvProEnabled)
                        <a href="/cv-pro" class="block p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-bold text-zinc-900 dark:text-white text-xs font-mono">Studio CV Pro</span>
                                <span class="px-1 py-0.2 bg-purple-500 text-white text-[9px] font-mono font-bold">PRO STUDIO</span>
                            </div>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-sans leading-tight">
                                {{ $isEn ? 'Visual Resume & Portfolio Studio' : 'Studio CV Visual & Portofolio Klien' }}
                            </p>
                        </a>
                        @endif

                        <a href="/pricing" class="block p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none border-t border-zinc-100 dark:border-zinc-800">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-bold text-zinc-900 dark:text-white text-xs font-mono">{{ $isEn ? 'Pricing & Packages' : 'Paket & Harga' }}</span>
                                <span class="px-1 py-0.2 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 text-[9px] font-mono font-bold">PROMO</span>
                            </div>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-sans leading-tight">
                                {{ $isEn ? 'Monolith MVP, Retail Licenses & Starter Tiers' : 'Paket Monolith MVP, Retail Licenses & UMKM' }}
                            </p>
                        </a>
                    </div>
                </div>

                <a href="/pricing" class="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition flex items-center gap-1.5">
                    <span>{{ $isEn ? 'Pricing' : 'Paket & Harga' }}</span>
                    <span class="px-1 py-0.2 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 text-[9px] font-bold">PROMO</span>
                </a>

                <a href="/#architecture" class="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
                    {{ $isEn ? 'Engineering' : 'Standar Rekayasa' }}
                </a>

                <!-- Active Workspace Indicator -->
                <a href="/customer/dashboard" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 transition flex items-center gap-1.5 font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>{{ $isEn ? 'Workspace' : 'Workspace' }}</span>
                </a>

                <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener noreferrer" class="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition flex items-center gap-1">
                    <span>{{ $isEn ? 'Help / WA' : 'Bantuan WA' }}</span>
                    <svg class="w-3 h-3 text-emerald-500 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </nav>

            <!-- Right: Utilities (Language Selector + Theme Toggle + Actions) -->
            <div class="flex items-center gap-2 sm:gap-2.5">
                
                <!-- UNIFIED 2-TIER LANGUAGE SELECTOR DROPDOWN -->
                <div class="relative" @click.away="langDropdownOpen = false">
                    <button
                        type="button"
                        @click="langDropdownOpen = !langDropdownOpen"
                        class="flex items-center gap-1.5 border border-zinc-300 dark:border-zinc-700 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 py-1.5 px-2.5 text-[11px] font-mono font-bold rounded-none transition cursor-pointer"
                        title="{{ $isEn ? 'Select Language (Tier 1 & Tier 2)' : 'Pilih Bahasa (Tier 1 & Tier 2)' }}"
                    >
                        <!-- Globe SVG -->
                        <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                        <span class="uppercase tracking-wider font-bold">{{ strtoupper(app()->getLocale()) }}</span>
                        <span class="text-[10px] text-zinc-400 hidden xl:inline font-sans">({{ $isEn ? 'US' : 'ID' }})</span>
                        <svg class="w-3 h-3 text-zinc-400 transition-transform duration-150 shrink-0" :class="langDropdownOpen ? 'rotate-180 text-emerald-500' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Pane -->
                    <div
                        x-show="langDropdownOpen"
                        x-cloak
                        class="absolute top-full right-0 mt-1 w-60 bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 shadow-2xl p-2 z-50 font-sans text-xs space-y-2 rounded-none"
                    >
                        <!-- Group 1: Tier 1 Native Precise -->
                        <div>
                            <div class="px-2 py-1 text-[10px] font-mono text-zinc-500 dark:text-zinc-400 uppercase font-bold flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 mb-1">
                                <span>TIER 1 // NATIVE PRECISE</span>
                                <span class="text-[9px] px-1 py-0.2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                    {{ $isEn ? 'OFFICIAL' : 'RESMI' }}
                                </span>
                            </div>
                            <div class="space-y-0.5">
                                <button
                                    type="button"
                                    onclick="changeTier1Language('id')"
                                    class="w-full text-left px-2 py-1.5 flex items-center justify-between transition text-xs rounded-none cursor-pointer {{ app()->getLocale() === 'id' ? 'bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold' : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                                >
                                    <span class="flex items-center gap-2">
                                        <span class="text-sm">🇮🇩</span>
                                        <span>Bahasa Indonesia</span>
                                    </span>
                                    @if(app()->getLocale() === 'id')
                                        <span class="text-xs text-emerald-500">✓</span>
                                    @endif
                                </button>
                                <button
                                    type="button"
                                    onclick="changeTier1Language('en')"
                                    class="w-full text-left px-2 py-1.5 flex items-center justify-between transition text-xs rounded-none cursor-pointer {{ app()->getLocale() === 'en' ? 'bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold' : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                                >
                                    <span class="flex items-center gap-2">
                                        <span class="text-sm">🇺🇸</span>
                                        <span>English (US)</span>
                                    </span>
                                    @if(app()->getLocale() === 'en')
                                        <span class="text-xs text-emerald-500">✓</span>
                                    @endif
                                </button>
                            </div>
                        </div>

                        <!-- Group 2: Tier 2 Global Translate -->
                        @if($googleTranslateEnabled ?? true)
                        <div class="pt-1 border-t border-zinc-200 dark:border-zinc-800">
                            <div class="px-2 py-1 text-[10px] font-mono text-zinc-500 dark:text-zinc-400 uppercase font-bold flex items-center justify-between mb-1">
                                <span>TIER 2 // GLOBAL TRANSLATE</span>
                                <span class="text-[9px] px-1 py-0.2 bg-indigo-500/10 text-indigo-500 border border-indigo-500/30">
                                    AI GOOGLE
                                </span>
                            </div>
                            <div class="space-y-0.5">
                                <button type="button" onclick="translateLanguage('ja', '日本語 (Japanese)')" class="w-full text-left px-2 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-between text-zinc-700 dark:text-zinc-300 transition text-xs rounded-none cursor-pointer">
                                    <span class="flex items-center gap-2"><span>🇯🇵</span><span>日本語 (Japanese)</span></span>
                                </button>
                                <button type="button" onclick="translateLanguage('zh-CN', '中文 (Mandarin)')" class="w-full text-left px-2 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-between text-zinc-700 dark:text-zinc-300 transition text-xs rounded-none cursor-pointer">
                                    <span class="flex items-center gap-2"><span>🇨🇳</span><span>中文 (Mandarin)</span></span>
                                </button>
                                <button type="button" onclick="translateLanguage('ar', 'العربية (Arabic)')" class="w-full text-left px-2 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-between text-zinc-700 dark:text-zinc-300 transition text-xs rounded-none cursor-pointer">
                                    <span class="flex items-center gap-2"><span>🇸🇦</span><span>العربية (Arabic)</span></span>
                                </button>
                                <button type="button" onclick="translateLanguage('de', 'Deutsch (German)')" class="w-full text-left px-2 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-between text-zinc-700 dark:text-zinc-300 transition text-xs rounded-none cursor-pointer">
                                    <span class="flex items-center gap-2"><span>🇩🇪</span><span>Deutsch (German)</span></span>
                                </button>
                                <button type="button" onclick="translateLanguage('fr', 'Français (French)')" class="w-full text-left px-2 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-between text-zinc-700 dark:text-zinc-300 transition text-xs rounded-none cursor-pointer">
                                    <span class="flex items-center gap-2"><span>🇫🇷</span><span>Français (French)</span></span>
                                </button>
                                <button type="button" onclick="translateLanguage('es', 'Español (Spanish)')" class="w-full text-left px-2 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-between text-zinc-700 dark:text-zinc-300 transition text-xs rounded-none cursor-pointer">
                                    <span class="flex items-center gap-2"><span>🇪🇸</span><span>Español (Spanish)</span></span>
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- DARK / LIGHT MODE TOGGLE BUTTON -->
                <button 
                    type="button" 
                    onclick="toggleTheme()" 
                    class="p-2 border border-zinc-300 dark:border-zinc-700 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 transition rounded-none cursor-pointer flex items-center justify-center shrink-0"
                    title="{{ $isEn ? 'Toggle Dark / Light Theme' : 'Beralih Mode Gelap / Terang' }}"
                >
                    <!-- Sun SVG (Dark Mode active, click for light) -->
                    <svg class="hidden dark:block w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Moon SVG (Light Mode active, click for dark) -->
                    <svg class="block dark:hidden w-3.5 h-3.5 text-zinc-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- New Project / Blueprint CTA -->
                <a 
                    href="/blueprint" 
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-black font-mono text-xs font-bold uppercase tracking-wider transition rounded-none shadow-xs shrink-0"
                >
                    <span>+ {{ $isEn ? 'NEW PROJECT' : 'PROYEK BARU' }}</span>
                </a>

                <!-- Customer Avatar Initials (Click to open Account & Logout Modal) -->
                <button 
                    type="button" 
                    @click="userMenuModalOpen = true" 
                    class="h-8 px-2.5 flex items-center justify-center gap-1.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-900 border border-zinc-900 dark:border-zinc-200 font-mono font-black text-xs tracking-wider transition rounded-none cursor-pointer shrink-0 shadow-xs group"
                    title="{{ $customerDisplayName }} ({{ $isEn ? 'Account & Session Menu' : 'Menu Akun & Sesi' }})"
                >
                    <span class="w-1.5 h-1.5 bg-emerald-400 dark:bg-emerald-600 rounded-none shrink-0 group-hover:scale-125 transition-transform"></span>
                    <span class="font-bold tracking-tight">{{ $customerInitials }}</span>
                    <svg class="w-2.5 h-2.5 text-zinc-400 dark:text-zinc-600 shrink-0 group-hover:translate-y-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Mobile Hamburger Button -->
                <button
                    type="button"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="lg:hidden p-2 border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white transition rounded-none cursor-pointer"
                    aria-label="Toggle Navigation Menu"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu Drawer -->
        <div 
            x-show="mobileMenuOpen" 
            x-cloak 
            class="lg:hidden border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-4 py-4 space-y-3 font-mono text-xs uppercase"
        >
            <div class="space-y-1">
                <a href="/" class="block py-2 px-3 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-bold">
                    {{ $isEn ? 'Home' : 'Beranda' }}
                </a>
                <a href="/blueprint" class="block py-2 px-3 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-emerald-600 dark:text-emerald-400 font-bold">
                    Project OS (PRD)
                </a>
                @if($isCvProEnabled)
                <a href="/cv-pro" class="block py-2 px-3 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-bold">
                    Studio CV Pro
                </a>
                @endif
                <a href="/pricing" class="block py-2 px-3 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-bold">
                    {{ $isEn ? 'Pricing' : 'Paket & Harga' }}
                </a>
                <a href="/#architecture" class="block py-2 px-3 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-bold">
                    {{ $isEn ? 'Engineering' : 'Standar Rekayasa' }}
                </a>
                <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener noreferrer" class="block py-2 px-3 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-bold">
                    {{ $isEn ? 'Help / WA Support' : 'Bantuan WhatsApp' }}
                </a>
                <button type="button" @click="userMenuModalOpen = true; mobileMenuOpen = false;" class="w-full text-left py-2 px-3 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-bold cursor-pointer flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="w-5 h-5 bg-zinc-900 dark:bg-white text-white dark:text-black font-black text-[10px] flex items-center justify-center">{{ $customerInitials }}</span>
                        <span>{{ $isEn ? 'Account Profile & Logout' : 'Profil Akun & Keluar' }}</span>
                    </span>
                    <span class="text-zinc-400 text-[10px] font-mono">MENU ➔</span>
                </button>
            </div>

            <!-- Mobile Tier 1 Language Switch -->
            <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-xs">
                <span class="text-zinc-500 font-mono text-[10px] uppercase font-bold">Bahasa / Language:</span>
                <div class="flex items-center gap-1 font-mono text-xs">
                    <button type="button" onclick="changeTier1Language('id')" class="px-2 py-1 {{ app()->getLocale() === 'id' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}">ID</button>
                    <button type="button" onclick="changeTier1Language('en')" class="px-2 py-1 {{ app()->getLocale() === 'en' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}">EN</button>
                </div>
            </div>
        </div>
    </header>

    <!-- 2. MAIN MODULAR MULTI-PANE CONTAINER (ANTI-FATIGUE SCROLL ARCHITECTURE) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Solid Brutalist Session Alerts -->
        @if(session('success'))
            <div class="mb-5 p-4 border-2 border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-200 font-mono text-xs flex items-center justify-between rounded-none shadow-xs">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-sm">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-xs font-bold hover:opacity-75 cursor-pointer">✕</button>
            </div>
        @endif
        @if(session('warning'))
            <div class="mb-5 p-4 border-2 border-amber-500 bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-200 font-mono text-xs flex items-center justify-between rounded-none shadow-xs">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-sm">⚠️</span>
                    <span>{{ session('warning') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-xs font-bold hover:opacity-75 cursor-pointer">✕</button>
            </div>
        @endif
        @if(session('info'))
            <div class="mb-5 p-4 border-2 border-cyan-500 bg-cyan-50 dark:bg-cyan-950/40 text-cyan-800 dark:text-cyan-200 font-mono text-xs flex items-center justify-between rounded-none shadow-xs">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-sm">ℹ️</span>
                    <span>{{ session('info') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-xs font-bold hover:opacity-75 cursor-pointer">✕</button>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 p-4 border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-200 font-mono text-xs flex items-center justify-between rounded-none shadow-xs">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-sm">❌</span>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-xs font-bold hover:opacity-75 cursor-pointer">✕</button>
            </div>
        @endif

        <!-- MODULAR HORIZONTAL / VERTICAL NAVIGATION PILLS -->
        <nav class="flex items-center gap-1.5 sm:gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-6 overflow-x-auto font-mono text-xs no-scrollbar">
            <!-- Tab 1: Overview -->
            <button 
                type="button"
                @click="setTab('overview')"
                :class="currentTab === 'overview' 
                    ? 'bg-zinc-900 text-white dark:bg-white dark:text-black font-bold shadow-xs' 
                    : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white border border-zinc-200 dark:border-zinc-800'"
                class="px-3.5 py-2 transition rounded-none uppercase flex items-center gap-2 whitespace-nowrap cursor-pointer"
            >
                <span>📊</span>
                <span>{{ $isEn ? 'Overview' : 'Ringkasan Akun' }}</span>
            </button>

            <!-- Tab 2: Studio Projects -->
            <button 
                type="button"
                @click="setTab('projects')"
                :class="currentTab === 'projects' 
                    ? 'bg-emerald-600 text-white dark:bg-emerald-500 dark:text-black font-bold shadow-xs' 
                    : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white border border-zinc-200 dark:border-zinc-800'"
                class="px-3.5 py-2 transition rounded-none uppercase flex items-center gap-2 whitespace-nowrap cursor-pointer"
            >
                <span>🚀</span>
                <span>{{ $isEn ? 'Studio Projects' : 'Proyek Studio' }}</span>
                <span class="px-1.5 py-0.2 bg-zinc-200/50 dark:bg-zinc-800 text-[10px] font-bold">
                    {{ $metrics['total_studio'] }}
                </span>
            </button>

            <!-- Tab 3: Retail Downloads -->
            <button 
                type="button"
                @click="setTab('licenses')"
                :class="currentTab === 'licenses' 
                    ? 'bg-cyan-600 text-white dark:bg-cyan-400 dark:text-black font-bold shadow-xs' 
                    : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white border border-zinc-200 dark:border-zinc-800'"
                class="px-3.5 py-2 transition rounded-none uppercase flex items-center gap-2 whitespace-nowrap cursor-pointer"
            >
                <span>📦</span>
                <span>{{ $isEn ? 'Retail Licenses & Downloads' : 'Lisensi Retail & Unduhan' }}</span>
                <span class="px-1.5 py-0.2 bg-zinc-200/50 dark:bg-zinc-800 text-[10px] font-bold">
                    {{ $metrics['total_retail'] }}
                </span>
                @if(isset($pendingRetailOrders) && $pendingRetailOrders->isNotEmpty())
                    <span class="px-1.5 py-0.2 bg-amber-500/20 text-amber-600 dark:text-amber-400 text-[10px] font-bold animate-pulse border border-amber-500/40">
                        {{ $pendingRetailOrders->count() }} PENDING
                    </span>
                @endif
            </button>

            <!-- Tab 4: Invoices & Receipts -->
            <button 
                type="button"
                @click="setTab('billing')"
                :class="currentTab === 'billing' 
                    ? 'bg-amber-600 text-white dark:bg-amber-500 dark:text-black font-bold shadow-xs' 
                    : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white border border-zinc-200 dark:border-zinc-800'"
                class="px-3.5 py-2 transition rounded-none uppercase flex items-center gap-2 whitespace-nowrap cursor-pointer"
            >
                <span>🧾</span>
                <span>{{ $isEn ? 'Tax Invoices & Receipts' : 'Faktur Pajak & Kwitansi' }}</span>
                <span class="px-1.5 py-0.2 bg-zinc-200/50 dark:bg-zinc-800 text-[10px] font-bold">
                    {{ $transactions->count() }}
                </span>
            </button>

            <!-- Tab 5: Infrastructure Assets (Ready for expansion) -->
            <button 
                type="button"
                @click="setTab('assets')"
                :class="currentTab === 'assets' 
                    ? 'bg-indigo-600 text-white dark:bg-indigo-400 dark:text-black font-bold shadow-xs' 
                    : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white border border-zinc-200 dark:border-zinc-800'"
                class="px-3.5 py-2 transition rounded-none uppercase flex items-center gap-2 whitespace-nowrap cursor-pointer"
            >
                <span>🌐</span>
                <span>{{ $isEn ? 'Domain & VPS Hosting' : 'Domain & Server Hosting' }}</span>
                @if($hostingAssets->isNotEmpty())
                    <span class="px-1.5 py-0.2 bg-zinc-200/50 dark:bg-zinc-800 text-[10px] font-bold">
                        {{ $hostingAssets->count() }}
                    </span>
                @endif
            </button>

            <!-- Tab 6: Account & Security -->
            <button 
                type="button"
                @click="setTab('account')"
                :class="currentTab === 'account' 
                    ? 'bg-zinc-700 text-white dark:bg-zinc-300 dark:text-black font-bold shadow-xs' 
                    : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white border border-zinc-200 dark:border-zinc-800'"
                class="px-3.5 py-2 transition rounded-none uppercase flex items-center gap-2 whitespace-nowrap cursor-pointer"
            >
                <span>⚙️</span>
                <span>{{ $isEn ? 'Account' : 'Pengaturan Akun' }}</span>
            </button>
        </nav>

        <!-- =================================================================== -->
        <!-- PANE 1: OVERVIEW & EXECUTIVE LAUNCHPAD                             -->
        <!-- =================================================================== -->
        <div x-show="currentTab === 'overview'" x-cloak class="space-y-6">
            
            <!-- Welcome Header Banner -->
            <div class="p-6 bg-white dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                        <span>● {{ $isEn ? 'ACTIVE CLIENT WORKSPACE' : 'WORKSPACE KLIEN TERDAFTAR' }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                        {{ $isEn ? 'Welcome back' : 'Selamat Datang' }}, {{ $user->name ?: explode('@', $user->email)[0] }}
                    </h1>
                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-sans mt-1 max-w-2xl leading-relaxed">
                        {{ $isEn 
                            ? 'Your modular engineering portal. Monitor active sprint timelines, access lifetime downloads, and print corporate reimbursement tax receipts without page bloat.'
                            : 'Portal rekayasa perangkat lunak terpadu Anda. Pantau timeline pengerjaan sprint, akses pusat unduhan seumur hidup, dan cetak faktur pajak resmi.' }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a 
                        href="/blueprint" 
                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-mono text-xs font-black uppercase tracking-wider transition rounded-none shadow-xs"
                    >
                        + {{ $isEn ? 'NEW BLUEPRINT' : 'BUAT PROYEK BARU' }}
                    </a>
                    <a 
                        href="https://wa.me/{{ $whatsappNumber }}?text={{ rawurlencode('Halo Lead Architect Neriah Pro, saya ingin mendiskusikan progres proyek di dashboard pelanggan kami.') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="px-4 py-2 bg-zinc-900 hover:bg-black dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white font-mono text-xs font-bold uppercase tracking-wider transition rounded-none border border-zinc-700"
                    >
                        💬 {{ $isEn ? 'WHATSAPP ARCHITECT' : 'CHAT ARCHITECT' }}
                    </a>
                </div>
            </div>

            <!-- 4 KPI Metrics Row -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs cursor-pointer hover:border-cyan-500 transition" @click="setTab('licenses')">
                    <span class="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider block">
                        {{ $isEn ? 'DIGITAL LICENSES' : 'LISENSI DIGITAL RETAIL' }}
                    </span>
                    <span class="text-2xl sm:text-3xl font-black font-mono text-cyan-600 dark:text-cyan-400 mt-1 block">
                        {{ $metrics['total_retail'] }}
                    </span>
                    @if(isset($pendingRetailOrders) && $pendingRetailOrders->isNotEmpty())
                        <span class="text-[10px] text-amber-500 font-mono font-bold mt-0.5 block animate-pulse">
                            ⏳ {{ $pendingRetailOrders->count() }} {{ $isEn ? 'order awaiting payment →' : 'pesanan menunggu bayar →' }}
                        </span>
                    @else
                        <span class="text-[10px] text-zinc-500 font-sans mt-0.5 block">
                            {{ $isEn ? 'Lifetime downloads ready →' : 'Akses unduh seumur hidup →' }}
                        </span>
                    @endif
                </div>

                <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs cursor-pointer hover:border-emerald-500 transition" @click="setTab('projects')">
                    <span class="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider block">
                        {{ $isEn ? 'STUDIO PROJECTS' : 'PROYEK STUDIO AKTIF' }}
                    </span>
                    <span class="text-2xl sm:text-3xl font-black font-mono text-emerald-600 dark:text-emerald-400 mt-1 block">
                        {{ $metrics['total_studio'] }}
                    </span>
                    <span class="text-[10px] text-zinc-500 font-sans mt-0.5 block">
                        {{ $metrics['active_studio'] }} {{ $isEn ? 'in active sprint →' : 'sedang dikerjakan →' }}
                    </span>
                </div>

                <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs cursor-pointer hover:border-amber-500 transition" @click="setTab('licenses')">
                    <span class="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider block">
                        {{ $isEn ? '30-DAY REVISION GUARANTEE' : 'GARANSI REVISI 30 HARI' }}
                    </span>
                    <span class="text-2xl sm:text-3xl font-black font-mono text-amber-500 mt-1 block">
                        ACTIVE
                    </span>
                    <span class="text-[10px] text-zinc-500 font-sans mt-0.5 block">
                        {{ $isEn ? 'Parameter refinements enabled' : 'Bebas penyesuaian parameter' }}
                    </span>
                </div>

                <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs cursor-pointer hover:border-zinc-900 dark:hover:border-zinc-500 transition" @click="setTab('billing')">
                    <span class="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider block">
                        {{ $isEn ? 'TOTAL SETTLEMENT' : 'TOTAL TRANSAKSI LUNAS' }}
                    </span>
                    <span class="text-xl sm:text-2xl font-black font-mono text-zinc-900 dark:text-white mt-1 block truncate">
                        Rp {{ number_format($metrics['total_spend_idr'], 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] text-zinc-500 font-sans mt-0.5 block">
                        {{ $transactions->whereIn('status', ['settlement', 'capture', 'success'])->count() }} {{ $isEn ? 'official invoices →' : 'faktur resmi terbit →' }}
                    </span>
                </div>
            </div>

            <!-- Executive Order & Payment Status Tracker -->
            @if(isset($latestTransaction) && $latestTransaction)
                @php
                    $ltDetails = $latestTransaction->customer_details ?? [];
                    $ltPkg = $ltDetails['display_package'] ?? $ltDetails['package_tier'] ?? 'Pesanan Sistem & Lisensi Neriah Pro';
                    $ltStatus = $latestTransaction->status;
                    $isLtSettled = in_array($ltStatus, ['settlement', 'capture', 'success'], true);
                    $isLtPending = $ltStatus === 'pending';
                    $isLtExpired = $ltStatus === 'expire';
                    $isLtFailed = in_array($ltStatus, ['cancel', 'deny'], true);
                @endphp

                <div class="p-5 border-2 rounded-none shadow-xs space-y-3 transition {{ $isLtSettled ? 'bg-emerald-500/5 border-emerald-500/40 dark:bg-emerald-950/20' : '' }} {{ $isLtPending ? 'bg-amber-500/5 border-amber-500/40 dark:bg-amber-950/20' : '' }} {{ $isLtExpired ? 'bg-rose-500/5 border-rose-500/40 dark:bg-rose-950/20' : '' }} {{ $isLtFailed ? 'bg-zinc-500/5 border-zinc-500/40 dark:bg-zinc-950/20' : '' }}">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b pb-3 {{ $isLtSettled ? 'border-emerald-500/20' : '' }} {{ $isLtPending ? 'border-amber-500/20' : '' }} {{ $isLtExpired ? 'border-rose-500/20' : '' }} {{ $isLtFailed ? 'border-zinc-500/20' : '' }}">
                        <div>
                            <span class="text-[10px] font-mono font-bold uppercase tracking-wider block {{ $isLtSettled ? 'text-emerald-600 dark:text-emerald-400' : '' }} {{ $isLtPending ? 'text-amber-600 dark:text-amber-400' : '' }} {{ $isLtExpired ? 'text-rose-600 dark:text-rose-400' : '' }} {{ $isLtFailed ? 'text-zinc-500' : '' }}">
                                {{ $isEn ? 'LATEST TRANSACTION & ORDER STATUS' : 'STATUS PESANAN & PEMBAYARAN TERAKHIR' }}
                            </span>
                            <h3 class="text-base font-black text-zinc-900 dark:text-white uppercase font-sans mt-0.5">
                                {{ $ltPkg }}
                            </h3>
                            <span class="text-xs font-mono text-zinc-500 block">
                                Order ID: {{ $latestTransaction->midtrans_order_id ?: ('NPRO-' . substr($latestTransaction->id, 0, 8)) }} &bull; 
                                {{ $latestTransaction->created_at ? $latestTransaction->created_at->format('d M Y, H:i') : 'N/A' }} &bull; 
                                Rp {{ number_format((float) $latestTransaction->total_idr, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="shrink-0 flex items-center gap-2">
                            @if($isLtSettled)
                                <span class="px-3 py-1 bg-emerald-500 text-black font-mono text-xs font-black uppercase tracking-wider">
                                    ✓ {{ $isEn ? 'SETTLED / ACTIVE' : 'LUNAS & AKTIF' }}
                                </span>
                            @elseif($isLtPending)
                                <span class="px-3 py-1 bg-amber-500 text-black font-mono text-xs font-black uppercase tracking-wider animate-pulse">
                                    ⏳ {{ $isEn ? 'AWAITING PAYMENT (PENDING)' : 'MENUNGGU PEMBAYARAN' }}
                                </span>
                            @elseif($isLtExpired)
                                <span class="px-3 py-1 bg-rose-600 text-white font-mono text-xs font-black uppercase tracking-wider">
                                    ⚠️ {{ $isEn ? 'ORDER EXPIRED' : 'KADALUARSA (EXPIRED)' }}
                                </span>
                            @elseif($isLtFailed)
                                <span class="px-3 py-1 bg-zinc-700 text-white font-mono text-xs font-black uppercase tracking-wider">
                                    ❌ {{ $isEn ? 'CANCELLED / FAILED' : 'DIBATALKAN / DITOLAK' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                        <p class="text-xs font-sans text-zinc-600 dark:text-zinc-400 max-w-2xl leading-relaxed">
                            @if($isLtSettled)
                                {{ $isEn 
                                    ? 'Payment was verified settled by Midtrans Escrow. All digital specifications, PostgreSQL Strict ULID DDL, and codebase scaffolds are permanently active in your account.' 
                                    : 'Pembayaran telah sukses diverifikasi oleh Midtrans Escrow. Seluruh berkas spesifikasi PRD 26 parameter, skema database SQL PostgreSQL Strict ULID, dan scaffold codebase sudah aktif dan dapat diunduh seumur hidup.' }}
                            @elseif($isLtPending)
                                {{ $isEn 
                                    ? 'This order is awaiting payment via Midtrans Gateway. Complete payment via QRIS / Virtual Account, or click sync if you already transferred.' 
                                    : 'Pesanan ini sedang menunggu pembayaran di gateway Midtrans. Selesaikan pembayaran sesuai petunjuk Snap, atau klik "Cek Status" jika Anda sudah melakukan transfer.' }}
                            @elseif($isLtExpired)
                                {{ $isEn 
                                    ? 'Payment window for this order has expired in Midtrans (over 24h). No charges were incurred. Please create a new order to receive a fresh payment code or QRIS.' 
                                    : 'Batas waktu pembayaran untuk pesanan ini telah habis (kadaluarsa di Midtrans). Anda tidak dikenakan biaya apapun. Silakan lakukan pemesanan ulang untuk mendapatkan kode bayar atau QRIS baru.' }}
                            @elseif($isLtFailed)
                                {{ $isEn 
                                    ? 'This transaction was cancelled or denied by the payment gateway. Please create a new order to proceed.' 
                                    : 'Transaksi ini dibatalkan atau ditolak oleh payment gateway. Silakan buat pesanan baru jika ingin melanjutkan.' }}
                            @endif
                        </p>

                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            @if($isLtSettled)
                                <button type="button" @click="setTab('licenses')" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-mono text-xs font-black uppercase tracking-wider rounded-none cursor-pointer">
                                    {{ $isEn ? 'OPEN DOWNLOAD CENTER →' : 'BUKA PUSAT UNDUHAN →' }}
                                </button>
                            @elseif($isLtPending)
                                @if(!empty($ltDetails['snap_token']))
                                    <button type="button" onclick="window.payRetailSnap('{{ $ltDetails['snap_token'] }}')" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-400 text-black font-mono text-xs font-black uppercase tracking-wider rounded-none cursor-pointer">
                                        💳 {{ $isEn ? 'PAY NOW' : 'BAYAR SEKARANG' }}
                                    </button>
                                @endif
                                <form method="POST" action="{{ route('customer.transaction.sync', $latestTransaction->id) }}" class="inline-block">
                                    @csrf
                                    <button type="submit" class="px-3 py-2 bg-zinc-900 hover:bg-black dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white font-mono text-xs font-bold uppercase tracking-wider rounded-none border border-zinc-700 cursor-pointer">
                                        🔄 {{ $isEn ? 'SYNC STATUS' : 'CEK STATUS' }}
                                    </button>
                                </form>
                                @if(!config('midtrans.is_production', false))
                                    <form method="POST" action="{{ route('customer.transaction.sync', $latestTransaction->id) }}" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="simulate" value="1">
                                        <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-mono text-xs font-black uppercase tracking-wider rounded-none cursor-pointer">
                                            ⚡ {{ $isEn ? 'DEV SETTLE' : 'LUNASKAN (DEV)' }}
                                        </button>
                                    </form>
                                @endif
                            @elseif($isLtExpired || $isLtFailed)
                                <a href="/pricing" class="px-4 py-2 bg-zinc-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black font-mono text-xs font-black uppercase tracking-wider rounded-none">
                                    🛒 {{ $isEn ? 'ORDER AGAIN →' : 'PESAN ULANG SEKARANG →' }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Launch Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- Quick Card: Latest Project Timeline Snippet -->
                <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono font-bold uppercase text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            <span>🚀</span>
                            <span>{{ $isEn ? 'STUDIO SPRINT STATUS' : 'STATUS SPRINT STUDIO TERKINI' }}</span>
                        </span>
                        <button type="button" @click="setTab('projects')" class="text-[11px] font-mono font-bold text-zinc-500 hover:text-emerald-500 underline cursor-pointer">
                            {{ $isEn ? 'View All →' : 'Buka Semua →' }}
                        </button>
                    </div>

                    @if($studioProjects->isEmpty())
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-dashed border-zinc-200 dark:border-zinc-800 text-center text-xs text-zinc-500">
                            {{ $isEn ? 'No active studio custom engineering project yet.' : 'Belum ada proyek custom engineering aktif.' }}
                        </div>
                    @else
                        @php $firstStudio = $studioProjects->first(); @endphp
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                            <div class="flex items-center justify-between text-xs font-bold font-sans text-zinc-900 dark:text-white">
                                <span>{{ $firstStudio->nama_bisnis ?: $firstStudio->client_name }}</span>
                                <span class="text-[10px] font-mono px-1.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                    {{ $firstStudio->project_status ?: 'Active' }}
                                </span>
                            </div>
                            <div class="w-full bg-zinc-200 dark:bg-zinc-800 h-2 mt-2 rounded-none overflow-hidden">
                                <div class="bg-emerald-500 h-full" style="width: {{ $firstStudio->user_metadata['dev_progress_percent'] ?? 15 }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] font-mono text-zinc-500 mt-1.5">
                                <span>Progress: {{ $firstStudio->user_metadata['dev_progress_percent'] ?? 15 }}%</span>
                                <a href="/blueprint/{{ $firstStudio->slug }}" class="text-emerald-600 hover:underline">
                                    {{ $isEn ? 'Open PRD →' : 'Buka Dokumen PRD →' }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Quick Card: Latest Retail License Downloads -->
                <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono font-bold uppercase text-cyan-600 dark:text-cyan-400 flex items-center gap-1.5">
                            <span>📦</span>
                            <span>{{ $isEn ? 'LATEST DIGITAL LICENSE' : 'LISENSI DIGITAL TERBARU' }}</span>
                        </span>
                        <button type="button" @click="setTab('licenses')" class="text-[11px] font-mono font-bold text-zinc-500 hover:text-cyan-500 underline cursor-pointer">
                            {{ $isEn ? 'Download Center →' : 'Pusat Unduhan →' }}
                        </button>
                    </div>

                    @if($retailLicenses->isEmpty())
                        @if(isset($pendingRetailOrders) && $pendingRetailOrders->isNotEmpty())
                            @php $firstPending = $pendingRetailOrders->first(); @endphp
                            <div class="p-3 bg-amber-500/10 border border-amber-500/30 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1 text-[10px] font-mono text-amber-600 dark:text-amber-400 font-bold uppercase">
                                        <span>⏳</span>
                                        <span>{{ $isEn ? 'AWAITING PAYMENT' : 'MENUNGGU PEMBAYARAN' }}</span>
                                    </div>
                                    <h4 class="text-xs font-bold text-zinc-900 dark:text-white truncate">
                                        {{ $firstPending->customer_details['display_package'] ?? $firstPending->customer_details['package_tier'] ?? 'Lisensi Digital Retail' }}
                                    </h4>
                                    <span class="text-[10px] font-mono text-zinc-500 block">
                                        Rp {{ number_format((float) $firstPending->total_idr, 0, ',', '.') }}
                                    </span>
                                </div>
                                <button type="button" @click="setTab('licenses')" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-black font-mono text-[11px] font-bold uppercase shrink-0 transition cursor-pointer">
                                    {{ $isEn ? 'PAY / SYNC →' : 'BAYAR / CEK →' }}
                                </button>
                            </div>
                        @else
                            <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-dashed border-zinc-200 dark:border-zinc-800 text-center text-xs text-zinc-500">
                                {{ $isEn ? 'No retail licenses registered to this account.' : 'Belum ada lisensi retail terdaftar di akun ini.' }}
                            </div>
                        @endif
                    @else
                        @php $firstRetail = $retailLicenses->first(); @endphp
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-zinc-900 dark:text-white truncate">
                                    {{ $firstRetail->nama_bisnis ?: $firstRetail->client_name }}
                                </h4>
                                <span class="text-[10px] font-mono text-zinc-500 block">
                                    Ref: {{ $firstRetail->slug }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1 text-[11px] font-mono font-bold shrink-0">
                                <a href="/blueprint/{{ $firstRetail->slug }}/download/pdf" class="px-2 py-1 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700">PDF</a>
                                <a href="/blueprint/{{ $firstRetail->slug }}/download/md" class="px-2 py-1 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700">MD</a>
                                <a href="/blueprint/{{ $firstRetail->slug }}/export/scaffold" class="px-2 py-1 bg-emerald-500 text-black">ZIP</a>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </div>

        <!-- =================================================================== -->
        <!-- PANE 2: STUDIO ENGINEERING PROJECTS & LIVE TIMELINES               -->
        <!-- =================================================================== -->
        <div x-show="currentTab === 'projects'" x-cloak class="space-y-6">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                <div>
                    <h2 class="text-lg font-black uppercase text-zinc-900 dark:text-white font-mono flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-none"></span>
                        <span>{{ $isEn ? 'Studio Engineering Projects & Live Sprints' : 'Proyek Rekayasa Studio & Timeline Sprint Live' }}</span>
                    </h2>
                    <p class="text-xs text-zinc-500 font-sans mt-0.5">
                        {{ $isEn ? 'Sprint milestones are automatically updated in real-time as developers mark checkpoints on the PRD.' : 'Tahapan pengerjaan sprint otomatis tersinkronisasi saat developer menyelesaikan checkpoint di PRD.' }}
                    </p>
                </div>
                <span class="text-xs font-mono font-bold text-zinc-500">
                    {{ $studioProjects->count() }} {{ $isEn ? 'Projects' : 'Proyek' }}
                </span>
            </div>

            @if($studioProjects->isEmpty())
                <div class="p-10 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-center space-y-3 rounded-none">
                    <span class="text-3xl block">📐</span>
                    <h3 class="font-bold text-sm font-mono uppercase text-zinc-900 dark:text-white">
                        {{ $isEn ? 'NO ACTIVE STUDIO ENGINEERING PROJECTS YET' : 'BELUM ADA PROYEK REKAYASA STUDIO AKTIF' }}
                    </h3>
                    <p class="text-xs text-zinc-500 max-w-md mx-auto">
                        {{ $isEn 
                            ? 'You have not reserved any Full MVP or UMKM Starter custom software engineering sprint yet.' 
                            : 'Anda belum memesan sprint pengerjaan Full MVP atau UMKM Starter. Dapatkan sistem monolit modern dengan garansi DP 50% dan kontrak hukum resmi.' }}
                    </p>
                    <a href="/pricing" class="inline-block mt-2 px-4 py-2 bg-emerald-500 text-black font-mono text-xs font-bold uppercase tracking-wider rounded-none">
                        {{ $isEn ? 'EXPLORE STUDIO PACKAGES →' : 'JELAJAHI PAKET STUDIO REKAYASA →' }}
                    </a>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($studioProjects as $project)
                        @php
                            $metadata = $project->user_metadata ?? [];
                            $checkpoints = $metadata['dev_checkpoints'] ?? [];
                            $progressPercent = $metadata['dev_progress_percent'] ?? 0;
                            if (empty($progressPercent)) {
                                $totalPhases = 6;
                                $completed = count(array_filter($checkpoints, fn ($c) => !empty($c['completed'])));
                                $progressPercent = min(100, (int) round(($completed / $totalPhases) * 100));
                            }
                            $isLocked = $project->isScopeFrozen();
                            $isDpPaid = $project->isDpConfirmed();
                            $contractDoc = $project->getContractDocument();
                        @endphp

                        <div class="p-6 bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-800 rounded-none shadow-xs space-y-5">
                            
                            <!-- Project Header Info -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold uppercase">
                                            {{ $project->project_status ?: 'Active Sprint' }}
                                        </span>
                                        @if($metadata['sprint_batch'] ?? false)
                                            <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-[10px]">
                                                🗓️ {{ $metadata['sprint_batch'] }}
                                            </span>
                                        @endif
                                        @if($isLocked)
                                            <span class="px-2 py-0.5 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-mono text-[10px] font-bold">
                                                🔒 SCOPE LOCKED
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-xl font-black text-zinc-900 dark:text-white uppercase font-sans mt-1">
                                        {{ $project->nama_bisnis ?: $project->client_name }}
                                    </h3>
                                    <span class="text-xs text-zinc-500 font-mono block">
                                        Ref ID: {{ $project->slug }} // {{ $project->created_at ? $project->created_at->format('d M Y') : 'N/A' }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <a 
                                        href="/blueprint/{{ $project->slug }}" 
                                        class="px-3 py-1.5 bg-zinc-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black font-mono text-xs font-bold uppercase tracking-wider rounded-none transition"
                                    >
                                        {{ $isEn ? 'OPEN PRD SPEC →' : 'BUKA PRD LENGKAP →' }}
                                    </a>
                                    @if($contractDoc)
                                        <a 
                                            href="/document/{{ $contractDoc->id }}/preview" 
                                            class="px-3 py-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 font-mono text-xs font-bold uppercase tracking-wider rounded-none transition"
                                        >
                                            📜 {{ $isEn ? 'CONTRACT SPK' : 'KONTRAK SPK' }}
                                        </a>
                                    @endif
                                    @if($project->staging_url)
                                        <a 
                                            href="{{ $project->staging_url }}" 
                                            target="_blank" 
                                            class="px-3 py-1.5 bg-sky-500 hover:bg-sky-400 text-black font-mono text-xs font-bold uppercase tracking-wider rounded-none transition"
                                        >
                                            🚀 STAGING LIVE
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Live Sprint Progress Bar & Percent Indicator -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs font-mono">
                                    <span class="font-bold text-zinc-700 dark:text-zinc-300 uppercase">
                                        {{ $isEn ? 'LIVE SPRINT EXECUTION PROGRESS' : 'PROGRES PENGERJAAN SPRINT LIVE' }}
                                    </span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $progressPercent }}% {{ $isEn ? 'COMPLETED' : 'SELESAI' }}
                                    </span>
                                </div>
                                <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-3 border border-zinc-200 dark:border-zinc-700 rounded-none overflow-hidden">
                                    <div class="bg-gradient-to-r from-emerald-600 via-emerald-500 to-sky-400 h-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                                </div>
                            </div>

                            <!-- 6-Phase Concrete Timeline Checkpoints View -->
                            <div class="pt-2">
                                <span class="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-3">
                                    {{ $isEn ? 'SPRINT CHECKPOINT ROADMAP (AUTO-SYNCED FROM PRD):' : 'TAHAPAN PENGERJAAN SPRINT (TERSINKRONISASI DARI PRD):' }}
                                </span>
                                
                                @php
                                    $phases = [
                                        'step_foundation' => [
                                            'num' => '01',
                                            'title' => $isEn ? 'Architecture Foundation & Strict ULID SQL' : 'Fondasi Arsitektur & Skema SQL Strict ULID',
                                            'desc' => $isEn ? 'PostgreSQL 16 tables, HasUlids, Docker Stack & Base Migrations.' : 'Setup database PostgreSQL 16, model HasUlids, Docker stack, dan migrasi awal.',
                                        ],
                                        'FEAT-SPEC-0' => [
                                            'num' => '02',
                                            'title' => $isEn ? 'Core Domain Models & Relations' : 'Model Domain Inti & Integritas Relasi',
                                            'desc' => $isEn ? 'Eloquent entities, polymorphic keys, casts, and data integrity.' : 'Penyusunan relasi antar entitas bisnis, casts array/json, dan validasi foreign key.',
                                        ],
                                        'FEAT-SPEC-1' => [
                                            'num' => '03',
                                            'title' => $isEn ? 'Business Logic & O(1) Pagination' : 'Business Logic & Standar Keyset O(1)',
                                            'desc' => $isEn ? 'Services layer, cursor pagination, and enterprise stability.' : 'Implementasi logika transaksi, service layer, dan pagination O(1) tanpa offset.',
                                        ],
                                        'FEAT-SPEC-2' => [
                                            'num' => '04',
                                            'title' => $isEn ? 'API Contracts & Security Shield' : 'Kontrak REST API & Pertahanan Keamanan',
                                            'desc' => $isEn ? 'Form requests, honeypots, rate limiting, and OpenAPI 3.1.' : 'Validasi input ketat, honeypot anti-bot, rate limiter, dan kontrak endpoint.',
                                        ],
                                        'FEAT-SPEC-3' => [
                                            'num' => '05',
                                            'title' => $isEn ? 'Frontend UI & Modern Reactive Islands' : 'Frontend UI & Komponen Interaktif Modern',
                                            'desc' => $isEn ? 'Responsive layouts, subtle corners, zero native alert slop.' : 'Tampilan responsive desktop/mobile, radius tipis, toast notifikasi elegan.',
                                        ],
                                        'step_deployment' => [
                                            'num' => '06',
                                            'title' => $isEn ? 'Quality Gate, 100% Tests & Staging Live' : 'Uji Mutu Otomatis, Test Suite & Staging Live',
                                            'desc' => $isEn ? 'Automated test suite passing, staging sandbox live & UAT.' : 'Seluruh automated test passing 100%, sandbox staging aktif untuk uji coba klien.',
                                        ],
                                    ];
                                @endphp

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                    @foreach($phases as $pKey => $phase)
                                        @php
                                            $cpData = $checkpoints[$pKey] ?? null;
                                            $isDone = !empty($cpData['completed']);
                                            $completedAt = !empty($cpData['completed_at']) ? \Carbon\Carbon::parse($cpData['completed_at'])->format('d M, H:i') : null;
                                        @endphp
                                        <div class="p-3 border rounded-none transition {{ $isDone ? 'bg-emerald-500/5 border-emerald-500/40 dark:bg-emerald-950/20' : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800' }}">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-mono text-[10px] font-bold {{ $isDone ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-500' }}">
                                                    PHASE {{ $phase['num'] }}
                                                </span>
                                                @if($isDone)
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                                        ✓ {{ $isEn ? 'DONE' : 'SELESAI' }} {{ $completedAt ? "({$completedAt})" : '' }}
                                                    </span>
                                                @else
                                                    <span class="text-[10px] font-mono text-zinc-400">
                                                        ⏳ {{ $isEn ? 'IN PROGRESS' : 'DALAM PROSES' }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h4 class="text-xs font-bold text-zinc-900 dark:text-white font-mono leading-tight">
                                                {{ $phase['title'] }}
                                            </h4>
                                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-sans mt-1 leading-snug">
                                                {{ $phase['desc'] }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Milestone 2 & Final Settlement (Pelunasan 50%) Section -->
                            @if($isDpPaid)
                                @if($project->isPelunasanConfirmed())
                                    <div class="mt-4 p-4 bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-between flex-wrap gap-3">
                                        <div class="flex items-center gap-3">
                                            <span class="w-8 h-8 bg-emerald-500 text-black flex items-center justify-center font-bold text-sm">✓</span>
                                            <div>
                                                <span class="font-mono text-xs font-black uppercase text-emerald-600 dark:text-emerald-400 block">
                                                    {{ $isEn ? 'ALL PAYMENTS FULLY SETTLED (100% LUNAS)' : 'SELURUH TERMIN PEMBAYARAN TELAH LUNAS (100%)' }}
                                                </span>
                                                <span class="text-[11px] text-zinc-500 dark:text-zinc-400 block mt-0.5">
                                                    {{ $isEn ? 'Source code repository, production database, VPS infrastructure credentials, and 30-day bug warranty are fully active.' : 'Seluruh hak source code, database produksi, kredensial VPS server, dan garansi SLA 30 hari aktif penuh.' }}
                                                </span>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 bg-emerald-500 text-black font-mono text-[10px] font-black uppercase tracking-wider">
                                            {{ $isEn ? 'FULLY SETTLED' : 'LUNAS 100%' }}
                                        </span>
                                    </div>
                                @else
                                    <div class="mt-4 p-4 bg-zinc-50 dark:bg-zinc-800/80 border-2 {{ $progressPercent >= 100 ? 'border-amber-500 bg-amber-500/5' : 'border-zinc-300 dark:border-zinc-700' }} flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono text-xs font-black uppercase {{ $progressPercent >= 100 ? 'text-amber-500' : 'text-zinc-800 dark:text-zinc-200' }}">
                                                    {{ $progressPercent >= 100 
                                                        ? ($isEn ? '⚡ ALL SPRINT PHASES COMPLETED // FINAL SETTLEMENT READY' : '⚡ SEMUA TAHAPAN SPRINT SELESAI // SIAP PELUNASAN 50%') 
                                                        : ($isEn ? 'MILESTONE 2: FINAL SETTLEMENT 50%' : 'TERMIN 2: PELUNASAN SISA 50%') }}
                                                </span>
                                                <span class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-700 font-mono text-[10px] font-bold text-zinc-900 dark:text-white">
                                                    Rp {{ number_format($project->getFinalPelunasanAmount(), 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1 max-w-xl leading-relaxed">
                                                {{ $isEn 
                                                    ? 'DP 50% was verified. Settle the remaining 50% balance via Midtrans Sandbox (QRIS / Virtual Account) for official production handover, VPS deployment, and source code transfer.' 
                                                    : 'Uang Muka (DP 50%) telah terverifikasi. Selesaikan pelunasan sisa 50% via Midtrans Sandbox (QRIS / Virtual Account) untuk serah terima produksi, deployment VPS, dan transfer kepemilikan source code.' }}
                                            </p>
                                        </div>
                                        <div class="shrink-0">
                                            <button
                                                type="button"
                                                onclick="window.payPelunasanSnap('{{ $project->slug }}')"
                                                class="w-full sm:w-auto px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-black font-mono text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 transition cursor-pointer shadow-md rounded-none"
                                            >
                                                <span>💳</span>
                                                <span>{{ $isEn ? 'PAY FINAL SETTLEMENT (MIDTRANS) →' : 'BAYAR PELUNASAN 50% (MIDTRANS SNAP) →' }}</span>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="mt-4 p-4 bg-red-500/5 border border-red-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <span class="font-mono text-xs font-black uppercase text-red-600 dark:text-red-400 block">
                                            {{ $isEn ? 'AWAITING 50% DOWN PAYMENT (DP)' : 'MENUNGGU PEMBAYARAN UANG MUKA (DP 50%)' }}
                                        </span>
                                        <span class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                            {{ $isEn ? 'Sprint execution begins immediately once the 50% DP is confirmed by Midtrans.' : 'Pengerjaan sprint dimulai segera setelah DP 50% terkonfirmasi oleh Midtrans.' }}
                                        </span>
                                    </div>
                                    <a 
                                        href="/blueprint/{{ $project->slug }}" 
                                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-mono text-xs font-black uppercase tracking-wider rounded-none transition text-center shrink-0"
                                    >
                                        {{ $isEn ? 'PAY DP VIA SNAP →' : 'BAYAR DP VIA MIDTRANS SNAP →' }}
                                    </a>
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- =================================================================== -->
        <!-- PANE 3: RETAIL DIGITAL LICENSES & LIFETIME DOWNLOAD CENTER         -->
        <!-- =================================================================== -->
        <div x-show="currentTab === 'licenses'" x-cloak class="space-y-6">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                <div>
                    <h2 class="text-lg font-black uppercase text-zinc-900 dark:text-white font-mono flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-cyan-500 rounded-none"></span>
                        <span>{{ $isEn ? 'Retail Digital Licenses & Lifetime Download Center' : 'Lisensi Spesifikasi Digital Retail & Pusat Unduh Seumur Hidup' }}</span>
                    </h2>
                    <p class="text-xs text-zinc-500 font-sans mt-0.5">
                        {{ $isEn ? 'Files are stored permanently in your account for unlimited 1-click downloads.' : 'Berkas spesifikasi PRD, skema SQL DDL, dan ZIP scaffold tersimpan permanen untuk unduh tak terbatas.' }}
                    </p>
                </div>
                <span class="text-xs font-mono font-bold text-zinc-500">
                    {{ $retailLicenses->count() }} {{ $isEn ? 'Licenses' : 'Lisensi' }}
                </span>
            </div>

            @if(isset($pendingRetailOrders) && $pendingRetailOrders->isNotEmpty())
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-amber-500 rounded-none animate-pulse"></span>
                        <h3 class="text-xs font-mono font-black uppercase text-amber-600 dark:text-amber-400 tracking-wider">
                            {{ $isEn ? 'PENDING LICENSE ORDERS AWAITING SETTLEMENT' : 'PESANAN LISENSI DIGITAL MENUNGGU PEMBAYARAN' }} ({{ $pendingRetailOrders->count() }})
                        </h3>
                    </div>

                    @foreach($pendingRetailOrders as $pendingTx)
                        @php
                            $pDetails = $pendingTx->customer_details ?? [];
                            $snapToken = $pDetails['snap_token'] ?? null;
                            $pkgName = $pDetails['display_package'] ?? $pDetails['package_tier'] ?? 'Lisensi Retail';
                            $companyName = $pDetails['company'] ?? ($pDetails['name'] ?? 'Proyek Mandiri');
                        @endphp
                        <div class="p-5 bg-amber-500/5 border-2 border-amber-500/40 rounded-none shadow-xs space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-amber-500/20 pb-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-amber-500 text-black font-mono text-[10px] font-black uppercase tracking-wider">
                                            ⏳ {{ $isEn ? 'PENDING SETTLEMENT' : 'MENUNGGU PEMBAYARAN' }}
                                        </span>
                                        <span class="font-mono text-[10px] text-zinc-500">
                                            Order ID: {{ $pendingTx->midtrans_order_id }}
                                        </span>
                                    </div>
                                    <h4 class="text-base font-black text-zinc-900 dark:text-white uppercase font-sans">
                                        {{ $pkgName }}
                                    </h4>
                                    <p class="text-xs text-zinc-500 font-mono mt-0.5">
                                        Entitas: {{ $companyName }} &bull; Tanggal Order: {{ $pendingTx->created_at ? $pendingTx->created_at->format('d M Y, H:i') : 'N/A' }}
                                    </p>
                                </div>
                                <div class="text-left sm:text-right">
                                    <span class="text-[10px] font-mono text-zinc-400 uppercase block">Tagihan Nominal</span>
                                    <span class="text-xl font-black font-mono text-zinc-900 dark:text-white">
                                        Rp {{ number_format((float) $pendingTx->total_idr, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-3 bg-white/70 dark:bg-zinc-900/70 border border-zinc-200 dark:border-zinc-800 text-xs text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed">
                                <span class="font-bold text-zinc-900 dark:text-white font-mono">ℹ️ INFORMASI AKTIVASI LISENSI:</span>
                                Berkas spesifikasi PRD 26 parameter, skema SQL DDL PostgreSQL Strict ULID, dan unduhan ZIP codebase scaffold akan otomatis terbuka di bawah ini segera setelah pembayaran dikonfirmasi oleh Midtrans.
                            </div>

                            <div class="flex flex-wrap items-center gap-2 pt-1">
                                @if($snapToken)
                                    <button 
                                        type="button" 
                                        onclick="window.payRetailSnap('{{ $snapToken }}')"
                                        class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-black font-mono text-xs font-black uppercase tracking-wider transition rounded-none shadow-xs cursor-pointer flex items-center gap-1.5"
                                    >
                                        <span>💳</span>
                                        <span>{{ $isEn ? 'PAY NOW VIA MIDTRANS SNAP →' : 'BAYAR SEKARANG VIA MIDTRANS SNAP →' }}</span>
                                    </button>
                                @endif

                                <form method="POST" action="{{ route('customer.transaction.sync', $pendingTx->id) }}" class="inline-block">
                                    @csrf
                                    <button 
                                        type="submit" 
                                        class="px-3.5 py-2 bg-zinc-900 hover:bg-black dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white font-mono text-xs font-bold uppercase tracking-wider transition rounded-none border border-zinc-700 cursor-pointer flex items-center gap-1.5"
                                        title="Periksa status transaksi ke gateway Midtrans jika Anda sudah mentransfer pembayaran"
                                    >
                                        <span>🔄</span>
                                        <span>{{ $isEn ? 'CHECK / SYNC STATUS' : 'CEK & SINKRONKAN STATUS' }}</span>
                                    </button>
                                </form>

                                @if(!config('midtrans.is_production', false))
                                    <form method="POST" action="{{ route('customer.transaction.sync', $pendingTx->id) }}" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="simulate" value="1">
                                        <button 
                                            type="submit" 
                                            class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-mono text-xs font-black uppercase tracking-wider transition rounded-none shadow-xs cursor-pointer flex items-center gap-1.5"
                                            title="Simulasi pelunasan instan untuk pengujian mode Sandbox Developer"
                                        >
                                            <span>⚡</span>
                                            <span>{{ $isEn ? 'SIMULATE SETTLEMENT (DEV)' : 'SIMULASI LUNAS (SANDBOX DEV)' }}</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($retailLicenses->isEmpty())
                @if(isset($expiredRetailOrders) && $expiredRetailOrders->isNotEmpty() && (!isset($pendingRetailOrders) || $pendingRetailOrders->isEmpty()))
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-rose-500 rounded-none"></span>
                            <h3 class="text-xs font-mono font-black uppercase text-rose-600 dark:text-rose-400 tracking-wider">
                                {{ $isEn ? 'EXPIRED LICENSE ORDERS' : 'RIWAYAT PESANAN KADALUARSA (EXPIRED)' }}
                            </h3>
                        </div>

                        @foreach($expiredRetailOrders as $expTx)
                            @php
                                $expDetails = $expTx->customer_details ?? [];
                                $expPkg = $expDetails['display_package'] ?? $expDetails['package_tier'] ?? 'Lisensi Retail';
                            @endphp
                            <div class="p-4 bg-rose-500/5 border border-rose-500/30 rounded-none flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-rose-600 text-white font-mono text-[10px] font-black uppercase">
                                            ⚠️ {{ $isEn ? 'EXPIRED' : 'KADALUARSA' }}
                                        </span>
                                        <span class="font-mono text-[10px] text-zinc-500">
                                            Order ID: {{ $expTx->midtrans_order_id }} &bull; {{ $expTx->created_at ? $expTx->created_at->format('d M Y, H:i') : 'N/A' }}
                                        </span>
                                    </div>
                                    <h4 class="text-sm font-bold text-zinc-900 dark:text-white font-sans">
                                        {{ $expPkg }} (Rp {{ number_format((float) $expTx->total_idr, 0, ',', '.') }})
                                    </h4>
                                    <p class="text-xs text-zinc-500 mt-0.5">
                                        {{ $isEn 
                                            ? 'Payment window for this order passed without completion. Create a new order to access downloads.' 
                                            : 'Batas waktu pembayaran 24 jam telah lewat. Anda tidak dikenakan biaya apapun. Silakan lakukan pemesanan ulang.' }}
                                    </p>
                                </div>
                                <a href="/pricing" class="px-3.5 py-2 bg-zinc-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black font-mono text-xs font-black uppercase tracking-wider rounded-none shrink-0">
                                    🛒 {{ $isEn ? 'ORDER AGAIN →' : 'PESAN ULANG →' }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="p-10 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-center space-y-3 rounded-none">
                    <span class="text-3xl block">📦</span>
                    <h3 class="font-bold text-sm font-mono uppercase text-zinc-900 dark:text-white">
                        {{ $isEn ? 'NO ACTIVE RETAIL LICENSES REGISTERED YET' : 'BELUM ADA LISENSI DIGITAL RETAIL AKTIF' }}
                    </h3>
                    <p class="text-xs text-zinc-500 max-w-md mx-auto">
                        @if(isset($pendingRetailOrders) && $pendingRetailOrders->isNotEmpty())
                            {{ $isEn 
                                ? 'You have ' . $pendingRetailOrders->count() . ' pending license order(s) above awaiting payment. Complete payment or sync status to unlock your lifetime downloads immediately.' 
                                : 'Anda memiliki ' . $pendingRetailOrders->count() . ' pesanan lisensi di atas yang sedang menunggu pembayaran. Selesaikan pembayaran atau sinkronkan status untuk langsung mengakses pusat unduhan seumur hidup.' }}
                        @elseif(isset($expiredRetailOrders) && $expiredRetailOrders->isNotEmpty())
                            {{ $isEn 
                                ? 'Your previous license order has expired. Order again from our catalog to get instant PRD specifications and codebase scaffolds.' 
                                : 'Pesanan lisensi Anda sebelumnya telah kadaluarsa di Midtrans. Silakan lakukan pemesanan ulang untuk langsung mengaktifkan spesifikasi PRD dan scaffold codebase.' }}
                        @else
                            {{ $isEn 
                                ? 'Get instant 26-parameter PRDs, PostgreSQL Strict ULID schemas, and complete codebase scaffolds for your engineering team to build independently.' 
                                : 'Dapatkan spesifikasi PRD 26 parameter instan, skema SQL PostgreSQL Strict ULID, dan scaffold codebase lengkap untuk dibangun mandiri oleh tim developer Anda.' }}
                        @endif
                    </p>
                    @if(!isset($pendingRetailOrders) || $pendingRetailOrders->isEmpty())
                        <a href="/pricing" class="inline-block mt-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-mono text-xs font-bold uppercase tracking-wider rounded-none">
                            {{ $isEn ? 'ORDER LITE OR PRO PRD (STARTING RP 99.000) →' : 'PESAN LISENSI LITE / PRO (MULAI RP 99RB) →' }}
                        </a>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($retailLicenses as $license)
                        @php
                            $tier = $license->user_metadata['retail_tier'] ?? $license->user_metadata['package_tier'] ?? 'retail_lite';
                            $tierLabel = match($tier) {
                                'retail_spark', 'spark' => 'Spark Free Audit (Rp 0)',
                                'retail_pro', 'pro' => 'Pro Production PRD (Rp 399.000)',
                                'retail_ultimate', 'ultimate' => 'Ultimate Advisory PRD (Rp 1.490.000)',
                                default => 'Lite PRD Generator (Rp 99.000)',
                            };
                            $allowedDays = match($tier) {
                                'retail_pro', 'pro' => 180,
                                'retail_ultimate', 'ultimate' => 365,
                                default => 30,
                            };
                            $daysRemaining = max(0, $allowedDays - (int) ($license->created_at ? $license->created_at->diffInDays(now()) : 0));
                            $isRevisionActive = $daysRemaining > 0;
                        @endphp

                        <div class="p-5 bg-white dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs space-y-4 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="px-2 py-0.5 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-mono text-[10px] font-bold uppercase">
                                        {{ $tierLabel }}
                                    </span>
                                    <span class="font-mono text-[10px] text-zinc-500">
                                        {{ $license->created_at ? $license->created_at->format('d M Y') : 'N/A' }}
                                    </span>
                                </div>

                                <h3 class="text-lg font-black uppercase text-zinc-900 dark:text-white font-sans">
                                    {{ $license->nama_bisnis ?: $license->client_name }}
                                </h3>
                                <p class="text-xs text-zinc-500 font-mono mt-0.5">
                                    Slug: {{ $license->slug }}
                                </p>

                                <!-- 30-Day Revision Guarantee Badge -->
                                <div class="mt-3 p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-xs font-mono">
                                    <span class="text-zinc-600 dark:text-zinc-400">
                                        {{ $isEn ? 'Revision Window:' : 'Garansi Revisi Parameter:' }}
                                    </span>
                                    @if($isRevisionActive)
                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                                            🟢 {{ $daysRemaining }} {{ $isEn ? 'Days Left' : 'Hari Tersisa' }}
                                        </span>
                                    @else
                                        <span class="text-zinc-500 font-bold">
                                            ⚪ {{ $isEn ? 'Window Expired' : 'Masa Revisi Berakhir' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Lifetime Download Buttons -->
                            <div class="space-y-2 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                                <span class="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                                    {{ $isEn ? '1-CLICK LIFETIME DOWNLOADS:' : 'PUSAT UNDUHAN SEUMUR HIDUP:' }}
                                </span>
                                
                                <div class="grid grid-cols-2 gap-2 text-xs font-mono font-bold">
                                    <a 
                                        href="/blueprint/{{ $license->slug }}/download/pdf" 
                                        class="p-2 text-center bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 transition rounded-none"
                                        title="Unduh Dokumen Eksekutif PRD PDF"
                                    >
                                        📄 {{ $isEn ? 'PDF Doc' : 'Dokumen PDF' }}
                                    </a>
                                    <a 
                                        href="/blueprint/{{ $license->slug }}/download/md" 
                                        class="p-2 text-center bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 transition rounded-none"
                                        title="Unduh PRD Markdown untuk AI Prompt"
                                    >
                                        📝 {{ $isEn ? 'Markdown .md' : 'Markdown PRD' }}
                                    </a>
                                    <a 
                                        href="/blueprint/{{ $license->slug }}/export/scaffold" 
                                        class="p-2 text-center bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 transition rounded-none"
                                        title="Unduh Kode Scaffold, Docker Compose & SQL ZIP"
                                    >
                                        📦 {{ $isEn ? 'Scaffold ZIP' : 'Scaffold ZIP' }}
                                    </a>
                                    <a 
                                        href="/blueprint/{{ $license->slug }}" 
                                        class="p-2 text-center bg-zinc-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black transition rounded-none"
                                    >
                                        📐 {{ $isEn ? 'Open PRD →' : 'Buka PRD →' }}
                                    </a>
                                </div>

                                @if($isRevisionActive)
                                    <a 
                                        href="/blueprint?slug={{ $license->slug }}" 
                                        class="w-full mt-2 py-2 px-3 bg-amber-500 hover:bg-amber-400 text-black font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none"
                                    >
                                        <span>🔄 {{ $isEn ? 'REFINE PARAMETERS (ACTIVE REVISION)' : 'PERBARUI PARAMETER & REGENERASI PRD' }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- =================================================================== -->
        <!-- PANE 4: OFFICIAL TAX INVOICES & REIMBURSEMENT RECEIPTS             -->
        <!-- =================================================================== -->
        <div x-show="currentTab === 'billing'" x-cloak class="space-y-6">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                <div>
                    <h2 class="text-lg font-black uppercase text-zinc-900 dark:text-white font-mono flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-amber-500 rounded-none"></span>
                        <span>{{ $isEn ? 'Official Tax Invoices & Corporate Receipts' : 'Faktur Pajak Resmi & Kwitansi Reimbursement Kantor' }}</span>
                    </h2>
                    <p class="text-xs text-zinc-500 font-sans mt-0.5">
                        {{ $isEn ? 'Instantly issued for corporate finance reimbursement and tax compliance.' : 'Diterbitkan instan untuk keperluan pembukuan keuangan dan klaim reimbursement kantor.' }}
                    </p>
                </div>
                <span class="text-xs font-mono font-bold text-zinc-500">
                    {{ $transactions->count() }} {{ $isEn ? 'Records' : 'Transaksi' }}
                </span>
            </div>

            @if($transactions->isEmpty())
                <div class="p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-center rounded-none text-xs text-zinc-500 font-mono">
                    {{ $isEn ? 'No payment transaction records found for your account.' : 'Belum ada riwayat transaksi pembayaran tercatat untuk akun ini.' }}
                </div>
            @else
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none overflow-x-auto shadow-xs">
                    <table class="w-full text-left font-mono text-xs">
                        <thead class="bg-zinc-100 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 uppercase text-[10px]">
                            <tr>
                                <th class="p-3">{{ $isEn ? 'Order Reference' : 'Nomor Pesanan / Order ID' }}</th>
                                <th class="p-3">{{ $isEn ? 'Date' : 'Tanggal' }}</th>
                                <th class="p-3">{{ $isEn ? 'Total IDR' : 'Jumlah (IDR)' }}</th>
                                <th class="p-3">{{ $isEn ? 'Status' : 'Status Pembayaran' }}</th>
                                <th class="p-3 text-right">{{ $isEn ? 'Action' : 'Aksi Dokumen' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-zinc-800 dark:text-zinc-200">
                            @foreach($transactions as $tx)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-950/50 transition">
                                    <td class="p-3 font-bold text-zinc-900 dark:text-white">
                                        {{ $tx->midtrans_order_id ?: ('NPRO-' . substr($tx->id, 0, 8)) }}
                                    </td>
                                    <td class="p-3 text-zinc-500">
                                        {{ $tx->created_at ? $tx->created_at->format('d M Y, H:i') : 'N/A' }}
                                    </td>
                                    <td class="p-3 font-bold">
                                        Rp {{ number_format((float) $tx->total_idr, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3">
                                        @if(in_array($tx->status, ['settlement', 'capture', 'success']))
                                            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-500/20 text-[10px] block w-fit">
                                                ✓ LUNAS (SETTLED)
                                            </span>
                                            <span class="text-[9px] text-zinc-400 font-mono block mt-0.5">Terverifikasi Midtrans</span>
                                        @elseif($tx->status === 'pending')
                                            <span class="px-2 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-bold border border-amber-500/20 text-[10px] block w-fit">
                                                ⏳ MENUNGGU BAYAR
                                            </span>
                                            <span class="text-[9px] text-amber-500 font-mono block mt-0.5">Selesaikan transfer</span>
                                        @elseif($tx->status === 'expire')
                                            <span class="px-2 py-0.5 bg-rose-500/10 text-rose-600 dark:text-rose-400 font-bold border border-rose-500/20 text-[10px] block w-fit">
                                                ⚠️ KADALUARSA (EXPIRED)
                                            </span>
                                            <span class="text-[9px] text-zinc-400 font-mono block mt-0.5">Batas waktu habis</span>
                                        @elseif(in_array($tx->status, ['cancel', 'deny']))
                                            <span class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-[10px] block w-fit">
                                                ❌ {{ strtoupper($tx->status) }}
                                            </span>
                                            <span class="text-[9px] text-zinc-400 font-mono block mt-0.5">Transaksi dibatalkan</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-[10px] block w-fit">
                                                {{ strtoupper($tx->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if($tx->status === 'pending')
                                                @if(!empty($tx->customer_details['snap_token']))
                                                    <button 
                                                        type="button" 
                                                        onclick="window.payRetailSnap('{{ $tx->customer_details['snap_token'] }}')" 
                                                        class="px-2 py-1 bg-amber-500 hover:bg-amber-400 text-black text-[10px] font-bold font-mono transition rounded-none cursor-pointer"
                                                        title="Buka kembali popup Midtrans Snap"
                                                    >
                                                        💳 {{ $isEn ? 'PAY' : 'BAYAR' }}
                                                    </button>
                                                @endif
                                                <form method="POST" action="{{ route('customer.transaction.sync', $tx->id) }}" class="inline-block">
                                                    @csrf
                                                    <button 
                                                        type="submit" 
                                                        class="px-2 py-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 text-[10px] font-bold font-mono transition rounded-none cursor-pointer"
                                                        title="Cek status pembayaran di Midtrans"
                                                    >
                                                        🔄 {{ $isEn ? 'CHECK' : 'CEK' }}
                                                    </button>
                                                </form>
                                                @if(!config('midtrans.is_production', false))
                                                    <form method="POST" action="{{ route('customer.transaction.sync', $tx->id) }}" class="inline-block">
                                                        @csrf
                                                        <input type="hidden" name="simulate" value="1">
                                                        <button 
                                                            type="submit" 
                                                            class="px-2 py-1 bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold font-mono transition rounded-none cursor-pointer"
                                                            title="Simulasi lunas Sandbox Developer"
                                                        >
                                                            ⚡ {{ $isEn ? 'DEV' : 'LUNASKAN' }}
                                                        </button>
                                                    </form>
                                                @endif
                                            @elseif(in_array($tx->status, ['expire', 'cancel', 'deny']))
                                                <a 
                                                    href="/pricing" 
                                                    class="px-2 py-1 bg-zinc-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-[10px] font-bold font-mono transition rounded-none inline-block"
                                                    title="Pesan ulang paket ini"
                                                >
                                                    🛒 {{ $isEn ? 'RE-ORDER' : 'PESAN ULANG' }}
                                                </a>
                                            @endif
                                            <button 
                                                type="button" 
                                                onclick="window.print()" 
                                                class="px-2.5 py-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 text-[10px] font-bold transition rounded-none cursor-pointer"
                                            >
                                                🖨️ {{ $isEn ? 'PRINT' : 'CETAK' }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- =================================================================== -->
        <!-- PANE 5: DOMAIN & VPS HOSTING ASSETS (EXPANDABLE INFRASTRUCTURE)    -->
        <!-- =================================================================== -->
        <div x-show="currentTab === 'assets'" x-cloak class="space-y-6">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                <div>
                    <h2 class="text-lg font-black uppercase text-zinc-900 dark:text-white font-mono flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-indigo-500 rounded-none"></span>
                        <span>{{ $isEn ? 'Domain & Dedicated Hosting Assets' : 'Aset Domain & Server VPS Terkelola' }}</span>
                    </h2>
                    <p class="text-xs text-zinc-500 font-sans mt-0.5">
                        {{ $isEn ? 'Track production domain renewals, dedicated VPS instances, and SSL security status.' : 'Pantau masa aktif domain, server VPS terisolasi, dan status sertifikat SSL aplikasi Anda.' }}
                    </p>
                </div>
                <span class="text-xs font-mono font-bold text-zinc-500">
                    {{ $hostingAssets->count() }} {{ $isEn ? 'Assets' : 'Aset' }}
                </span>
            </div>

            @if($hostingAssets->isEmpty())
                <div class="p-10 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-center space-y-3 rounded-none">
                    <span class="text-3xl block">🌐</span>
                    <h3 class="font-bold text-sm font-mono uppercase text-zinc-900 dark:text-white">
                        {{ $isEn ? 'NO DOMAIN OR HOSTING ASSETS REGISTERED YET' : 'BELUM ADA ASET DOMAIN / SERVER TERCATAT' }}
                    </h3>
                    <p class="text-xs text-zinc-500 max-w-md mx-auto">
                        {{ $isEn 
                            ? 'Domain and VPS infrastructure assets are automatically provisioned and tracked when your Studio project reaches Phase 5-6.' 
                            : 'Aset domain dan VPS akan otomatis tercatat dan dipantau di sini ketika pengerjaan proyek Studio Anda memasuki tahap staging/live.' }}
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($hostingAssets as $asset)
                        <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.5 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-mono text-[10px] font-bold uppercase">
                                    {{ $asset->asset_type ?: 'Domain / Hosting' }}
                                </span>
                                <span class="font-mono text-[10px] text-zinc-500">
                                    Exp: {{ $asset->expires_at ? $asset->expires_at->format('d M Y') : 'N/A' }}
                                </span>
                            </div>
                            <h4 class="text-base font-bold text-zinc-900 dark:text-white font-mono">
                                {{ $asset->name }}
                            </h4>
                            <p class="text-xs text-zinc-500">
                                {{ $asset->provider ?: 'Managed VPS Cloud' }} // {{ $asset->server_ip ?: 'Dedicated IP' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- =================================================================== -->
        <!-- PANE 6: ACCOUNT PROFILE, BILLING & PREFERENCES                      -->
        <!-- =================================================================== -->
        <div x-show="currentTab === 'account'" x-cloak class="space-y-6">
            
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3 gap-2">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold uppercase tracking-wider rounded-none border border-emerald-500/20">
                            {{ $isEn ? 'ACCOUNT SETTINGS' : 'PENGATURAN AKUN' }}
                        </span>
                        <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-mono text-[10px] font-bold uppercase tracking-wider rounded-none border border-zinc-200 dark:border-zinc-700">
                            {{ $isEn ? 'TAX & BILLING' : 'FAKTUR & PENAGIHAN' }}
                        </span>
                    </div>
                    <h2 class="text-lg font-black uppercase text-zinc-900 dark:text-white font-mono flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-none"></span>
                        <span>{{ $isEn ? 'Customer Profile & Tax Invoicing Preferences' : 'Profil Akun, Identitas Bisnis & Data Penagihan' }}</span>
                    </h2>
                    <p class="text-xs text-zinc-500 font-sans mt-0.5">
                        {{ $isEn ? 'Manage contact details, registered company name for contracts & tax invoices, E.164 WhatsApp alerts, and sprint notification preferences.' : 'Sesuaikan data identitas kontak, nama resmi perusahaan untuk kontrak kerja sama & faktur pajak, WhatsApp E.164, dan preferensi notifikasi sprint.' }}
                    </p>
                </div>
            </div>

            <!-- Inline Success / Error Banner -->
            <div x-show="profileSuccessMsg" x-cloak class="p-3 border-2 border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-200 font-mono text-xs flex items-center justify-between rounded-none shadow-xs">
                <div class="flex items-center gap-2">
                    <span class="font-bold">✓</span>
                    <span x-text="profileSuccessMsg"></span>
                </div>
                <button type="button" @click="profileSuccessMsg = ''" class="font-bold hover:opacity-75 cursor-pointer">✕</button>
            </div>
            <div x-show="profileErrorMsg" x-cloak class="p-3 border-2 border-rose-500 bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-200 font-mono text-xs flex items-center justify-between rounded-none shadow-xs">
                <div class="flex items-center gap-2">
                    <span class="font-bold">❌</span>
                    <span x-text="profileErrorMsg"></span>
                </div>
                <button type="button" @click="profileErrorMsg = ''" class="font-bold hover:opacity-75 cursor-pointer">✕</button>
            </div>

            <!-- Main Profile Form Grid -->
            <form @submit.prevent="saveProfile()" class="space-y-6">
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- COLUMN 1: KONTAK & IDENTITAS PERUSAHAAN -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none space-y-4 shadow-xs">
                        <div class="border-b border-zinc-100 dark:border-zinc-800 pb-2">
                            <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-900 dark:text-white flex items-center gap-2">
                                <span class="w-2 h-2 bg-emerald-500"></span>
                                <span>{{ $isEn ? '1. Contact & Organization Identity' : '1. Identitas Kontak & Institusi' }}</span>
                            </h3>
                            <p class="text-[11px] text-zinc-500 font-sans mt-0.5">
                                {{ $isEn ? 'Primary point of contact for sprint engineering and contracts.' : 'Penanggung jawab utama proyek sprint dan dokumen legal.' }}
                            </p>
                        </div>

                        <!-- Nama Lengkap / PIC -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-mono font-bold uppercase text-zinc-700 dark:text-zinc-300">
                                {{ $isEn ? 'Full Name / Contact PIC' : 'Nama Lengkap / PIC Proyek' }}
                                <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                x-model="profileName" 
                                placeholder="{{ $isEn ? 'e.g. John Doe' : 'Contoh: Yoseph Iriandi Tambunan' }}" 
                                class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white font-mono text-xs focus:border-emerald-500 focus:outline-hidden rounded-none transition"
                                required
                            >
                        </div>

                        <!-- Email Terdaftar (Read-Only) -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-mono font-bold uppercase text-zinc-700 dark:text-zinc-300">
                                {{ $isEn ? 'Registered Customer Email (OTP Authentication)' : 'E-mail Terdaftar (Autentikasi OTP)' }}
                            </label>
                            <div class="flex items-center justify-between p-2.5 bg-zinc-100 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-600 dark:text-zinc-400">
                                <span class="font-bold">{{ $user->email }}</span>
                                <span class="text-[10px] px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-bold uppercase border border-zinc-300 dark:border-zinc-700">
                                    🔐 {{ $isEn ? 'LOCKED (OTP ID)' : 'TERKUNCI (SESI OTP)' }}
                                </span>
                            </div>
                            <p class="text-[10px] text-zinc-500 font-sans">
                                {{ $isEn ? 'Email address is linked to your passwordless 2-FA OTP sessions.' : 'Alamat email terikat pada sesi passwordless OTP 6-digit demi keamanan tingkat tinggi.' }}
                            </p>
                        </div>

                        <!-- Nama Perusahaan / Instansi -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-mono font-bold uppercase text-zinc-700 dark:text-zinc-300">
                                {{ $isEn ? 'Company / Organization / Startup Name' : 'Nama Perusahaan / Startup / Instansi' }}
                            </label>
                            <input 
                                type="text" 
                                x-model="profileCompany" 
                                placeholder="{{ $isEn ? 'e.g. PT Acme Technologies Indonesia' : 'Contoh: PT Neriah Solusi Digital' }}" 
                                class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white font-mono text-xs focus:border-emerald-500 focus:outline-hidden rounded-none transition"
                            >
                            <p class="text-[10px] text-zinc-500 font-sans">
                                {{ $isEn ? 'Attached to WBS specifications, staging environments, and official legal contracts.' : 'Dicantumkan secara resmi pada PRD, environment staging, dan surat perjanjian kerja.' }}
                            </p>
                        </div>

                        <!-- WhatsApp / Nomor Telepon (Standar E.164 + Country Zone Selector) -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-mono font-bold uppercase text-zinc-700 dark:text-zinc-300">
                                {{ $isEn ? 'WhatsApp / Mobile Number (E.164 Standard)' : 'Nomor WhatsApp / Seluler (Standar E.164)' }}
                            </label>
                            
                            <div class="flex items-center gap-1.5">
                                <!-- Country Zone Selector Dropdown -->
                                <div class="relative shrink-0" @click.away="countryDropdownOpen = false">
                                    <button 
                                        type="button" 
                                        @click="countryDropdownOpen = !countryDropdownOpen"
                                        class="flex items-center gap-1 px-2.5 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-300 dark:border-zinc-700 text-xs font-mono font-bold text-zinc-800 dark:text-zinc-200 rounded-none transition cursor-pointer"
                                        title="Pilih Kode Negara"
                                    >
                                        <span x-text="getSelectedZone().flag" class="text-sm"></span>
                                        <span x-text="selectedCountryCode"></span>
                                        <svg class="w-3 h-3 text-zinc-400 transition-transform" :class="countryDropdownOpen ? 'rotate-180 text-emerald-500' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <!-- Country Dropdown Search & List -->
                                    <div 
                                        x-show="countryDropdownOpen" 
                                        x-cloak 
                                        class="absolute top-full left-0 mt-1 w-64 bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 shadow-2xl p-2 z-50 rounded-none text-xs"
                                    >
                                        <div class="mb-2">
                                            <input 
                                                type="text" 
                                                x-model="countrySearch" 
                                                placeholder="{{ $isEn ? 'Search country or code...' : 'Cari negara / kode...' }}" 
                                                class="w-full px-2 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white font-mono text-xs focus:border-emerald-500 focus:outline-hidden rounded-none"
                                            >
                                        </div>
                                        <div class="max-h-48 overflow-y-auto space-y-0.5 no-scrollbar">
                                            <template x-for="item in filteredCountryZones()" :key="item.code">
                                                <button 
                                                    type="button" 
                                                    @click="selectCountry(item)"
                                                    class="w-full px-2 py-1.5 text-left flex items-center justify-between hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none text-zinc-800 dark:text-zinc-200 cursor-pointer"
                                                    :class="selectedCountryCode === item.dial_code ? 'bg-emerald-500/10 font-bold text-emerald-600 dark:text-emerald-400' : ''"
                                                >
                                                    <span class="flex items-center gap-2 truncate">
                                                        <span x-text="item.flag"></span>
                                                        <span x-text="item.name" class="truncate max-w-[120px]"></span>
                                                    </span>
                                                    <span x-text="item.dial_code" class="font-mono text-zinc-400 font-bold shrink-0"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Phone Number Input -->
                                <input 
                                    type="tel" 
                                    x-model="profilePhone" 
                                    placeholder="81234567890" 
                                    class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white font-mono text-xs focus:border-emerald-500 focus:outline-hidden rounded-none transition"
                                >
                            </div>
                            <p class="text-[10px] text-zinc-500 font-sans">
                                {{ $isEn ? 'Enter without leading zero (e.g. 812...). Used for sprint alerts and Midtrans payment receipts.' : 'Ketik tanpa angka 0 di depan (contoh: 812...). Digunakan untuk update sprint dan bukti transaksi.' }}
                            </p>
                        </div>
                    </div>

                    <!-- COLUMN 2: DATA FAKTUR PAJAK & ALAMAT PENAGIHAN -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none space-y-4 shadow-xs">
                        <div class="border-b border-zinc-100 dark:border-zinc-800 pb-2">
                            <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-900 dark:text-white flex items-center gap-2">
                                <span class="w-2 h-2 bg-indigo-500"></span>
                                <span>{{ $isEn ? '2. Tax Invoicing & Billing Address' : '2. Data Faktur Pajak & Penagihan' }}</span>
                            </h3>
                            <p class="text-[11px] text-zinc-500 font-sans mt-0.5">
                                {{ $isEn ? 'Used for official corporate invoices, tax declarations, and receipts.' : 'Diperlukan untuk penerbitan faktur pajak resmi, kwitansi, dan penagihan corporate.' }}
                            </p>
                        </div>

                        <!-- NPWP -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-mono font-bold uppercase text-zinc-700 dark:text-zinc-300">
                                {{ $isEn ? 'NPWP / Corporate Tax Number' : 'NPWP (Nomor Pokok Wajib Pajak)' }}
                            </label>
                            <input 
                                type="text" 
                                x-model="profileNpwp" 
                                placeholder="00.000.000.0-000.000" 
                                class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white font-mono text-xs focus:border-emerald-500 focus:outline-hidden rounded-none transition"
                            >
                            <p class="text-[10px] text-zinc-500 font-sans">
                                {{ $isEn ? 'Optional. Required if your company requires standard e-Faktur tax documents.' : 'Opsional. Lengkapi jika perusahaan Anda membutuhkan Faktur Pajak resmi (e-Faktur).' }}
                            </p>
                        </div>

                        <!-- Alamat Penagihan -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-mono font-bold uppercase text-zinc-700 dark:text-zinc-300">
                                {{ $isEn ? 'Billing / Office Address' : 'Alamat Kantor / Penagihan (Billing Address)' }}
                            </label>
                            <textarea 
                                x-model="profileAddress" 
                                rows="3" 
                                placeholder="{{ $isEn ? 'Street address, office tower, floor, unit number...' : 'Nama jalan, gedung perkantoran, lantai, nomor unit...' }}" 
                                class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white font-sans text-xs focus:border-emerald-500 focus:outline-hidden rounded-none transition"
                            ></textarea>
                        </div>

                        <!-- Kota, Provinsi, Kode Pos (3-Col Grid) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div class="space-y-1">
                                <label class="block text-[11px] font-mono font-bold uppercase text-zinc-700 dark:text-zinc-300">
                                    {{ $isEn ? 'City' : 'Kota / Kab.' }}
                                </label>
                                <input 
                                    type="text" 
                                    x-model="profileCity" 
                                    placeholder="Jakarta Selatan" 
                                    class="w-full px-2.5 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white font-mono text-xs focus:border-emerald-500 focus:outline-hidden rounded-none"
                                >
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] font-mono font-bold uppercase text-zinc-700 dark:text-zinc-300">
                                    {{ $isEn ? 'Province' : 'Provinsi' }}
                                </label>
                                <input 
                                    type="text" 
                                    x-model="profileProvince" 
                                    placeholder="DKI Jakarta" 
                                    class="w-full px-2.5 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white font-mono text-xs focus:border-emerald-500 focus:outline-hidden rounded-none"
                                >
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] font-mono font-bold uppercase text-zinc-700 dark:text-zinc-300">
                                    {{ $isEn ? 'Postal Code' : 'Kode Pos' }}
                                </label>
                                <input 
                                    type="text" 
                                    x-model="profilePostalCode" 
                                    placeholder="12190" 
                                    class="w-full px-2.5 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white font-mono text-xs focus:border-emerald-500 focus:outline-hidden rounded-none"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PREFERENSI NOTIFIKASI SPRINT & KEAMANAN -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Notifikasi Sprint -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none space-y-3 shadow-xs">
                        <div class="border-b border-zinc-100 dark:border-zinc-800 pb-2">
                            <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-900 dark:text-white flex items-center gap-2">
                                <span class="w-2 h-2 bg-amber-500"></span>
                                <span>{{ $isEn ? '3. Sprint & Payment Notifications' : '3. Preferensi Notifikasi & Komunikasi' }}</span>
                            </h3>
                            <p class="text-[11px] text-zinc-500 font-sans mt-0.5">
                                {{ $isEn ? 'Configure how you wish to receive milestone updates.' : 'Tentukan saluran penerimaan alert progres pengerjaan software factory.' }}
                            </p>
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 cursor-pointer">
                                <input type="checkbox" x-model="notifyEmailSprints" class="mt-0.5 text-emerald-500 focus:ring-0 rounded-none">
                                <div>
                                    <span class="block text-xs font-mono font-bold text-zinc-900 dark:text-white">
                                        {{ $isEn ? 'Email Sprint Milestone & QA Reports' : 'Notifikasi Milestone Sprint & Pengujian QA (Email)' }}
                                    </span>
                                    <span class="block text-[11px] text-zinc-500 font-sans mt-0.5">
                                        {{ $isEn ? 'Receive automated progress reports upon completion of WBS deliverables and staging deploy.' : 'Menerima laporan kemajuan mingguan saat fitur selesai diuji dan staging siap ditinjau.' }}
                                    </span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 cursor-pointer">
                                <input type="checkbox" x-model="notifyWaBilling" class="mt-0.5 text-emerald-500 focus:ring-0 rounded-none">
                                <div>
                                    <span class="block text-xs font-mono font-bold text-zinc-900 dark:text-white">
                                        {{ $isEn ? 'WhatsApp Billing & Payment Receipts' : 'Bukti Pembayaran & Alert Pelunasan (WhatsApp)' }}
                                    </span>
                                    <span class="block text-[11px] text-zinc-500 font-sans mt-0.5">
                                        {{ $isEn ? 'Direct alerts for Midtrans escrow verification and settlement confirmations.' : 'Menerima alert instan via WhatsApp saat pembayaran Midtrans terverifikasi dan faktur resmi terbit.' }}
                                    </span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Keamanan & Status Sesi -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none space-y-3 shadow-xs">
                        <div class="border-b border-zinc-100 dark:border-zinc-800 pb-2">
                            <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-900 dark:text-white flex items-center gap-2">
                                <span class="w-2 h-2 bg-emerald-500"></span>
                                <span>{{ $isEn ? '4. Security Credentials & Active Session' : '4. Status Keamanan & Sesi Terhubung' }}</span>
                            </h3>
                            <p class="text-[11px] text-zinc-500 font-sans mt-0.5">
                                {{ $isEn ? 'State-of-the-art enterprise authentication standards.' : 'Standar autentikasi modern anti-credential stuffing.' }}
                            </p>
                        </div>

                        <div class="space-y-2.5">
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs flex items-center justify-between">
                                <div class="flex items-center gap-2 text-zinc-700 dark:text-zinc-300">
                                    <span>🔐</span>
                                    <span>Passwordless 6-Digit Email OTP</span>
                                </div>
                                <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-500 font-mono text-[10px] font-bold border border-emerald-500/20">
                                    AKTIF & TERVERIFIKASI
                                </span>
                            </div>

                            <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs flex items-center justify-between">
                                <div class="flex items-center gap-2 text-zinc-700 dark:text-zinc-300">
                                    <span>🌐</span>
                                    <span class="font-mono">IP Sesi: {{ request()->ip() }}</span>
                                </div>
                                <span class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-mono text-[10px] font-bold">
                                    SESI AMAN
                                </span>
                            </div>
                        </div>

                        <div class="pt-2 flex items-center justify-between">
                            <span class="text-xs text-zinc-500 font-sans">
                                {{ $isEn ? 'End session on this device:' : 'Akhiri sesi login di perangkat ini:' }}
                            </span>
                            <button 
                                type="button" 
                                @click="logoutCustomer()" 
                                class="px-3 py-1.5 border border-rose-300 dark:border-rose-900 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 text-rose-700 dark:text-rose-300 font-mono text-xs font-bold uppercase transition rounded-none cursor-pointer"
                            >
                                {{ $isEn ? 'LOGOUT SESSION' : 'KELUAR DARI AKUN' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM ACTION BAR: SIMPAN PERUBAHAN -->
                <div class="p-4 bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-lg rounded-none">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-emerald-500 rounded-none"></span>
                        <span class="text-xs font-mono font-bold uppercase text-zinc-800 dark:text-zinc-200">
                            {{ $isEn ? 'All profile updates are immediately synchronized with your active contracts.' : 'Perubahan profil langsung disinkronkan ke seluruh dokumen kontrak dan faktur pajak aktif Anda.' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                        <button 
                            type="submit" 
                            :disabled="isSavingProfile"
                            class="w-full sm:w-auto px-6 py-2.5 bg-emerald-500 hover:bg-emerald-400 disabled:opacity-50 text-black font-mono text-xs font-black uppercase tracking-wider transition rounded-none shadow-xs flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <svg x-show="isSavingProfile" x-cloak class="w-4 h-4 animate-spin text-black" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSavingProfile ? '{{ $isEn ? "SAVING..." : "MENYIMPAN..." }}' : '{{ $isEn ? "SAVE PROFILE CHANGES" : "SIMPAN PERUBAHAN PROFIL" }}'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <!-- 3. FOOTER -->
    <footer class="mt-20 border-t border-zinc-200 dark:border-zinc-800 py-8 bg-white dark:bg-zinc-900 text-center font-mono text-xs text-zinc-500">
        <p>&copy; {{ date('Y') }} Neriah Pro. {{ $isEn ? 'All rights reserved. Modular Software Architecture OS.' : 'Hak cipta dilindungi. Sistem Operasi Arsitektur Perangkat Lunak Skala Enterprise.' }}</p>
    </footer>

    <script>
        window.payRetailSnap = function(snapToken) {
            if (!snapToken) return;
            if (window.snap && typeof window.snap.pay === 'function') {
                window.snap.pay(snapToken, {
                    onSuccess: function (result) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'success',
                                title: 'PEMBAYARAN BERHASIL',
                                message: 'Pembayaran lisensi berhasil diverifikasi! Memuat ulang portal...',
                                duration: 3000
                            });
                        }
                        setTimeout(function() {
                            window.location.reload();
                        }, 1800);
                    },
                    onPending: function (result) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'info',
                                title: 'MENUNGGU PEMBAYARAN',
                                message: 'Silakan selesaikan pembayaran QRIS / Virtual Account Anda.'
                            });
                        }
                    },
                    onError: function (result) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'error',
                                title: 'PEMBAYARAN DIBATALKAN',
                                message: 'Sesi pembayaran dibatalkan atau ditolak.'
                            });
                        }
                    },
                    onClose: function () {
                        if (window.showToast) {
                            window.showToast({
                                type: 'info',
                                title: 'PROMPT DITUTUP',
                                message: 'Anda dapat menekan tombol bayar kembali kapan saja.'
                            });
                        }
                    }
                });
            } else {
                if (window.showToast) {
                    window.showToast({
                        type: 'error',
                        title: 'KONEKSI MIDTRANS',
                        message: 'Sistem Snap sedang dimuat, silakan coba beberapa saat lagi.'
                    });
                }
            }
        };
    </script>

    <!-- 4. CUSTOMER ACCOUNT & LOGOUT MODAL (TRIGGERED BY AVATAR INITIALS) -->
    <div 
        x-show="userMenuModalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="userMenuModalOpen = false"
    >
        <div 
            class="relative w-full max-w-md bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 shadow-2xl rounded-none p-5 sm:p-6"
            @click.outside="userMenuModalOpen = false"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100"
        >
            <!-- Header with Close Button -->
            <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-none"></span>
                    <h3 class="font-mono font-bold text-xs uppercase tracking-wider text-zinc-900 dark:text-white">
                        {{ $isEn ? 'Customer Identity & Session' : 'Identitas Pelanggan & Sesi' }}
                    </h3>
                </div>
                <button 
                    type="button" 
                    @click="userMenuModalOpen = false"
                    class="p-1 text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white font-mono font-bold text-sm cursor-pointer transition"
                    title="{{ $isEn ? 'Close Modal' : 'Tutup Dialog' }}"
                >
                    ✕
                </button>
            </div>

            <!-- Customer Avatar & Profile Details -->
            <div class="flex items-start gap-4 p-3.5 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 mb-5">
                <div class="w-12 h-12 bg-zinc-900 text-white dark:bg-emerald-500 dark:text-black font-mono font-black text-lg flex items-center justify-center shrink-0 rounded-none shadow-xs">
                    {{ $customerInitials }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h4 class="font-bold text-sm text-zinc-900 dark:text-white truncate">
                            {{ $customerDisplayName }}
                        </h4>
                        <span class="px-1.5 py-0.2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 text-[9px] font-mono font-bold">
                            {{ $isEn ? 'VERIFIED CLIENT' : 'KLIEN TERDAFTAR' }}
                        </span>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono truncate mt-0.5">
                        {{ $user->email }}
                    </p>
                    @if(!empty($user->phone))
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">
                        {{ $user->phone_country_code ?? '+62' }} {{ $user->phone }}
                    </p>
                    @endif
                </div>
            </div>

            <!-- Quick Navigation Shortcuts -->
            <div class="space-y-1.5 mb-5 font-mono text-xs">
                <button 
                    type="button" 
                    @click="setTab('account'); userMenuModalOpen = false;"
                    class="w-full text-left px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700/80 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 flex items-center justify-between transition cursor-pointer rounded-none"
                >
                    <span class="flex items-center gap-2">
                        <span>⚙️</span>
                        <span>{{ $isEn ? 'Account Profile & Tax Data' : 'Profil Akun & Data Faktur' }}</span>
                    </span>
                    <span class="text-zinc-400 text-[10px]">➔</span>
                </button>
                <button 
                    type="button" 
                    @click="setTab('projects'); userMenuModalOpen = false;"
                    class="w-full text-left px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700/80 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 flex items-center justify-between transition cursor-pointer rounded-none"
                >
                    <span class="flex items-center gap-2">
                        <span>🚀</span>
                        <span>{{ $isEn ? 'Studio Projects & Sprints' : 'Proyek Studio & Timeline' }}</span>
                    </span>
                    <span class="text-zinc-400 text-[10px]">➔</span>
                </button>
                <button 
                    type="button" 
                    @click="setTab('billing'); userMenuModalOpen = false;"
                    class="w-full text-left px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700/80 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 flex items-center justify-between transition cursor-pointer rounded-none"
                >
                    <span class="flex items-center gap-2">
                        <span>🧾</span>
                        <span>{{ $isEn ? 'Invoices & Receipts' : 'Faktur Pajak & Kwitansi' }}</span>
                    </span>
                    <span class="text-zinc-400 text-[10px]">➔</span>
                </button>
            </div>

            <!-- Logout Section -->
            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 space-y-2">
                <button 
                    type="button" 
                    @click="logoutCustomer()" 
                    class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-700 text-white font-mono font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 border border-rose-700 transition cursor-pointer shadow-xs rounded-none"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>{{ $isEn ? 'LOGOUT OF DASHBOARD' : 'KELUAR DARI AKUN' }}</span>
                </button>
                <p class="text-[10px] text-center text-zinc-400 dark:text-zinc-500 font-mono">
                    {{ $isEn ? 'Terminates your active encrypted session on this device.' : 'Mengakhiri sesi terenkripsi aktif portal pelanggan pada perangkat ini.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- 5. GOOGLE TRANSLATE TIER 2 MODERN LOADER MODAL (STRICTLY VIEWPORT CENTERED) -->
    <div 
        x-show="isTranslating" 
        x-cloak 
        class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; display: flex !important; align-items: center !important; justify-content: center !important; z-index: 99999 !important; margin: 0 !important; padding: 1rem !important;"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="relative w-full max-w-sm bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 shadow-2xl rounded-none p-6 text-center m-auto"
            style="margin: auto !important; max-width: 24rem !important; width: 100% !important;"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100"
        >
            <!-- Animated Geometric Spinner Icon (Solid Brutalist, Sharp Non-Pill) -->
            <div class="w-10 h-10 mx-auto mb-4 border-2 border-zinc-200 dark:border-zinc-700 border-t-emerald-500 rounded-none animate-spin"></div>

            <div class="space-y-1 mb-3">
                <span class="text-[10px] font-mono uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold">
                    {{ $isEn ? 'NEURAL ENGINE ACTIVE' : 'ENGINE TERJEMAHAN AKTIF' }}
                </span>
                <h4 class="font-mono font-bold text-sm uppercase text-zinc-900 dark:text-white">
                    {{ $isEn ? 'Translating Interface...' : 'Menerjemahkan Halaman...' }}
                </h4>
            </div>

            <!-- Target Language Badge -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 mb-4 font-mono text-xs text-zinc-800 dark:text-zinc-200 font-bold rounded-none">
                <span class="w-2 h-2 bg-emerald-500 rounded-none animate-pulse"></span>
                <span x-text="translatingLanguageName"></span>
            </div>

            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-sans leading-relaxed">
                {{ $isEn ? 'Converting interface elements. This banner-free view will adjust smoothly.' : 'Mengonversi komponen antarmuka. Tampilan bebas banner Google akan tertata otomatis.' }}
            </p>

            <!-- Subtle Progress Bar -->
            <div class="mt-4 w-full h-1 bg-zinc-100 dark:bg-zinc-800 overflow-hidden rounded-none">
                <div class="h-full bg-emerald-500 animate-pulse w-full"></div>
            </div>
        </div>
    </div>

    @if($googleTranslateEnabled ?? true)
        <!-- Google Translate Container & Bridge (Tier 2) -->
        <div id="google_translate_element" class="hidden"></div>
        <script>
            function googleTranslateElementInit() {
                try {
                    new google.translate.TranslateElement({
                        pageLanguage: '{{ app()->getLocale() ?: "id" }}',
                        includedLanguages: '{{ implode(",", $allowedLangList ?? ["en","id","ja","zh-CN","ar","de","fr","es"]) }}',
                        autoDisplay: false
                    }, 'google_translate_element');
                } catch(e) {}
            }
        </script>
        <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async defer></script>
    @endif
</body>
</html>
