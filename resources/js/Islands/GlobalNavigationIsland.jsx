import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
  Cpu, 
  FileText, 
  Layers, 
  ShieldCheck, 
  Sparkles, 
  ArrowRight, 
  Sun, 
  Moon, 
  ChevronDown, 
  Menu, 
  X,
  Lock,
  Zap,
  Globe,
  ShoppingCart,
  Clock,
  AlertCircle,
  Check
} from 'lucide-react';

export default function GlobalNavigationIsland({ settings, featureFlags, cartData }) {
  const isMidtransStrict = Boolean(featureFlags?.midtrans_mode);
  const isCvProEnabled = !isMidtransStrict && (featureFlags?.enable_cv_pro !== false);
  const isBlueprintEnabled = featureFlags?.enable_vision_blueprint !== false;
  const isContractEnabled = featureFlags?.enable_digital_contract !== false;
  const isClientOnboardingEnabled = featureFlags?.enable_client_onboarding !== false;
  const isPricingEnabled = !isMidtransStrict && (featureFlags?.enable_pricing !== false) && isCvProEnabled;
  const hasAnyService = isBlueprintEnabled || isCvProEnabled || isContractEnabled || isClientOnboardingEnabled;

  const [scrolled, setScrolled] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [servicesDropdownOpen, setServicesDropdownOpen] = useState(false);
  const [cartDropdownOpen, setCartDropdownOpen] = useState(false);
  const [globalLangOpen, setGlobalLangOpen] = useState(false);
  const [mobileLangOpen, setMobileLangOpen] = useState(false);
  const [isDarkMode, setIsDarkMode] = useState(false);
  
  // Cart state & Real-time Anti-Ghost Hold countdown
  const [cartState, setCartState] = useState({
    count: 0,
    items: [],
    min_remaining_seconds: 86400
  });
  const [remainingSeconds, setRemainingSeconds] = useState(86400);

  useEffect(() => {
    // Fetch live cart state
    fetch('/api/cart')
      .then(res => res.json())
      .then(data => {
        if (data) {
          setCartState(data);
          if (data.min_remaining_seconds !== null && data.min_remaining_seconds !== undefined) {
            setRemainingSeconds(data.min_remaining_seconds);
          }
        }
      })
      .catch(() => {});
  }, []);

  // 1-second interval countdown for anti-ghost hold
  useEffect(() => {
    if (cartState.count === 0 || remainingSeconds <= 0) return;
    const timer = setInterval(() => {
      setRemainingSeconds(prev => {
        if (prev <= 1) {
          clearInterval(timer);
          return 0;
        }
        return prev - 1;
      });
    }, 1000);
    return () => clearInterval(timer);
  }, [cartState.count, remainingSeconds]);

  const formatCountdown = (secs) => {
    if (!secs || secs <= 0) return '00:00:00';
    const h = Math.floor(secs / 3600);
    const m = Math.floor((secs % 3600) / 60);
    const s = Math.floor(secs % 60);
    return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
  };

  const [lang, setLang] = useState(() => {
    if (typeof window !== 'undefined') {
      const match = document.cookie.match(new RegExp('(^| )neriah_locale=([^;]+)'));
      if (match) return match[2];
      return document.documentElement.lang?.startsWith('en') ? 'en' : 'id';
    }
    return 'id';
  });

  const [activeTier2, setActiveTier2] = useState(() => {
    if (typeof window !== 'undefined') {
      const match = document.cookie.match(new RegExp('(^| )googtrans=([^;]+)'));
      if (match) {
        const parts = decodeURIComponent(match[2]).split('/');
        const target = parts[parts.length - 1];
        if (target && target !== 'id' && target !== '') {
          return target;
        }
      }
    }
    return null;
  });

  const tier1Languages = [
    { code: 'id', name: 'Bahasa Indonesia', flag: '🇮🇩' },
    { code: 'en', name: 'English (US)', flag: '🇺🇸' },
  ];

  const tier2Languages = [
    { code: 'ja', name: '日本語', label: 'Japanese', flag: '🇯🇵' },
    { code: 'zh-CN', name: '中文', label: 'Mandarin', flag: '🇨🇳' },
    { code: 'ar', name: 'العربية', label: 'Arabic', flag: '🇸🇦' },
    { code: 'de', name: 'Deutsch', label: 'German', flag: '🇩🇪' },
    { code: 'fr', name: 'Français', label: 'French', flag: '🇫🇷' },
    { code: 'es', name: 'Español', label: 'Spanish', flag: '🇪🇸' },
  ];

  const changeLanguage = (newLang) => {
    // Clear Google Translate cookie so Tier 1 native takes full precedence
    document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    document.cookie = `googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=${window.location.hostname}`;
    document.cookie = `neriah_locale=${newLang};path=/;max-age=31536000`;
    setLang(newLang);
    setActiveTier2(null);
    setGlobalLangOpen(false);
    window.location.href = `/lang/${newLang}`;
  };

  const selectTier2Language = (code) => {
    setActiveTier2(code);
    setGlobalLangOpen(false);
    if (window.translateLanguage) {
      window.translateLanguage(code);
    } else {
      document.cookie = `googtrans=/id/${code}; path=/; domain=${window.location.hostname}`;
      document.cookie = `googtrans=/id/${code}; path=/;`;
      window.location.reload();
    }
  };

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 20);
    };
    window.addEventListener('scroll', handleScroll);

    // Sync theme
    const savedTheme = localStorage.getItem('neriah_theme');
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      setIsDarkMode(true);
      document.documentElement.classList.add('dark');
    } else {
      setIsDarkMode(false);
      document.documentElement.classList.remove('dark');
    }

    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const toggleTheme = () => {
    if (isDarkMode) {
      document.documentElement.classList.remove('dark');
      localStorage.setItem('neriah_theme', 'light');
      setIsDarkMode(false);
    } else {
      document.documentElement.classList.add('dark');
      localStorage.setItem('neriah_theme', 'dark');
      setIsDarkMode(true);
    }
  };

  const navLinks = [
    { label: lang === 'id' ? 'Beranda' : 'Home', href: '/' },
    ...(isBlueprintEnabled ? [{ label: 'Project OS (PRD)', href: '/blueprint', highlight: true }] : []),
    ...(isCvProEnabled ? [{ label: lang === 'id' ? 'Studio CV Pro' : 'CV Pro Studio', href: '/cv-pro' }] : []),
    ...(isPricingEnabled ? [{ label: lang === 'id' ? 'Paket & Harga' : 'Pricing', href: '/pricing' }] : []),
    ...(hasAnyService ? [{ label: lang === 'id' ? 'Layanan HUB' : 'Service Hub', href: '/#services' }] : []),
  ];

  return (
    <header className="fixed top-0 w-full z-50 transition-colors font-sans">
      
      {/* 1. TOP ANNOUNCEMENT BAR (HIGH RETENTION & SHAREABLE ALERT) */}
      <div className="bg-zinc-900 border-b border-zinc-800 text-zinc-300 py-1.5 px-4 text-xs font-mono flex items-center justify-between">
        <div className="max-w-7xl mx-auto w-full flex items-center justify-between">
          <div className="flex items-center gap-2">
            <span className="px-1.5 py-0.2 bg-emerald-500 text-black font-black text-[10px] uppercase rounded-none">
              NEW RELEASE
            </span>
            <span className="hidden sm:inline text-zinc-400">
              {lang === 'id' 
                ? 'Rancang Arsitektur & Generate PRD Proyek Anda secara Otomatis dalam 60 Detik.' 
                : 'Synthesize Project Specs, Database ERD & PRD in under 60 seconds.'}
            </span>
            {isBlueprintEnabled && (
              <a href="/blueprint" className="text-emerald-400 hover:text-emerald-300 font-bold underline ml-1 flex items-center gap-0.5">
                <span>{lang === 'id' ? 'Coba Project OS' : 'Launch Project OS'}</span>
                <ArrowRight className="w-3 h-3 inline" />
              </a>
            )}
          </div>

          <div className="flex items-center gap-3">
            {/* Dark / Light Mode Switch */}
            <button 
              onClick={toggleTheme} 
              className="text-zinc-400 hover:text-white transition p-1 flex items-center gap-1 cursor-pointer" 
              title={isDarkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'}
            >
              {isDarkMode ? <Sun className="w-3.5 h-3.5 text-amber-400" /> : <Moon className="w-3.5 h-3.5" />}
            </button>
          </div>
        </div>
      </div>

      {/* 2. MAIN NAVIGATION (SHARP BRUTALIST PRECISION) */}
      <nav 
        className={`w-full transition-all duration-200 border-b ${
          scrolled 
            ? 'bg-white/95 dark:bg-zinc-950/95 backdrop-blur-md border-zinc-200 dark:border-zinc-800 py-2.5 shadow-xs' 
            : 'bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 py-3 sm:py-3.5'
        }`}
      >
        <div className="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between">
          
          {/* Brand Logo */}
          <a href="/" className="flex items-center gap-2 group shrink-0 mr-4 sm:mr-6 lg:mr-8">
            <span className="w-7 h-7 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black flex items-center justify-center font-mono font-black text-xs rounded-none transition-transform group-hover:scale-105">
              N
            </span>
            <span className="font-black text-lg sm:text-xl uppercase tracking-tighter text-zinc-900 dark:text-white font-sans">
              NERIAH<span className="text-emerald-500">PRO</span>
            </span>
            <span className="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 ml-2 hidden xl:inline font-bold whitespace-nowrap rounded-none">
              {lang === 'id' ? 'APLIKASI SOLUSI HIDUP' : 'LIFE SOLUTION APPS'}
            </span>
          </a>

          {/* Desktop Navigation Links */}
          <div className="hidden md:flex items-center gap-8 font-mono text-xs uppercase tracking-wider font-bold">
            <a href="/" className="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
              {lang === 'id' ? 'Beranda' : 'Home'}
            </a>

            {/* Dropdown: Layanan HUB (Only visible if at least one service is enabled) */}
            {hasAnyService && (
              <div 
                className="relative"
                onMouseEnter={() => setServicesDropdownOpen(true)}
                onMouseLeave={() => setServicesDropdownOpen(false)}
              >
                <button className="flex items-center gap-1 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition py-2">
                  <span>{lang === 'id' ? 'Layanan HUB' : 'Services Hub'}</span>
                  <ChevronDown className="w-3.5 h-3.5" />
                </button>

                <AnimatePresence>
                  {servicesDropdownOpen && (
                    <motion.div 
                      initial={{ opacity: 0, y: 5 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0, y: 5 }}
                      transition={{ duration: 0.15 }}
                      className="absolute top-full left-0 w-72 bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 rounded-none shadow-xl p-3 space-y-1 text-left"
                    >
                      {isBlueprintEnabled && (
                        <a href="/blueprint" className="block p-2.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none">
                          <div className="flex items-center justify-between mb-0.5">
                            <span className="font-bold text-zinc-900 dark:text-white text-xs">Project OS (PRD)</span>
                            <span className="px-1 py-0.2 bg-emerald-500 text-black text-[9px] font-bold">ACTIVE</span>
                          </div>
                          <p className="text-[11px] text-zinc-500 dark:text-zinc-400 font-sans leading-tight">
                            {lang === 'id' ? 'Generator PRD & Skema ERD Otomatis' : 'Automated PRD & ERD Schema'}
                          </p>
                        </a>
                      )}

                      {isCvProEnabled && (
                        <a href="/cv-pro" className="block p-2.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none">
                          <div className="flex items-center justify-between mb-0.5">
                            <span className="font-bold text-zinc-900 dark:text-white text-xs">CV Generator</span>
                            <span className="px-1 py-0.2 bg-purple-500 text-white text-[9px] font-bold">PRO STUDIO</span>
                          </div>
                          <p className="text-[11px] text-zinc-500 dark:text-zinc-400 font-sans leading-tight">
                            {lang === 'id' ? 'Studio CV Visual & Portofolio Klien' : 'Visual Resume & Portfolio Studio'}
                          </p>
                        </a>
                      )}
                    </motion.div>
                  )}
                </AnimatePresence>
              </div>
            )}

            <a href="/pricing" className="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition flex items-center gap-1.5">
              <span>{lang === 'id' ? 'Paket & Harga' : 'Pricing'}</span>
              <span className="px-1 py-0.2 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 text-[9px] font-bold">PROMO</span>
            </a>

            <a href="#architecture" className="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
              {lang === 'id' ? 'Standar Rekayasa' : 'Engineering'}
            </a>

            <a href="/customer/dashboard" className="text-zinc-600 dark:text-zinc-400 hover:text-emerald-500 dark:hover:text-emerald-400 transition flex items-center gap-1.5">
              <span className="w-1.5 h-1.5 rounded-none bg-emerald-500"></span>
              <span>{lang === 'id' ? 'Portal Klien' : 'Client Portal'}</span>
            </a>

            <a href="/admin/login" className="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
              Portal Admin
            </a>
          </div>

          {/* Action CTAs */}
          <div className="hidden sm:flex items-center gap-3">
            {/* Unified Language Selector Dropdown (Tier 1 Native & Tier 2 Global) */}
            <div 
              className="relative"
              onMouseEnter={() => setGlobalLangOpen(true)}
              onMouseLeave={() => setGlobalLangOpen(false)}
            >
              <button
                type="button"
                onClick={() => setGlobalLangOpen(!globalLangOpen)}
                className={`flex items-center gap-1.5 border py-1.5 px-2.5 text-[11px] font-mono font-bold rounded-xs transition cursor-pointer ${
                  globalLangOpen 
                    ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400' 
                    : 'border-zinc-300 dark:border-zinc-700 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300'
                }`}
                title="Pilih Bahasa (Tier 1 & Tier 2)"
              >
                <Globe className="w-3.5 h-3.5 text-emerald-500" />
                <span className="uppercase tracking-wider font-bold">
                  {activeTier2 ? activeTier2.toUpperCase() : lang.toUpperCase()}
                </span>
                <span className="text-[10px] text-zinc-400 hidden lg:inline font-sans">
                  {activeTier2 ? '(Global)' : (lang === 'id' ? '(ID)' : '(EN)')}
                </span>
                <ChevronDown className={`w-3 h-3 text-zinc-400 transition-transform duration-150 ${globalLangOpen ? 'rotate-180 text-emerald-500' : ''}`} />
              </button>

              <AnimatePresence>
                {globalLangOpen && (
                  <motion.div
                    initial={{ opacity: 0, y: 5 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, y: 5 }}
                    transition={{ duration: 0.15 }}
                    className="absolute top-full right-0 mt-1 w-64 bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 shadow-2xl p-2 z-50 font-sans text-xs space-y-2 rounded-xs"
                  >
                    {/* LIST GROUP 1: TIER 1 NATIVE PRECISE */}
                    <div>
                      <div className="px-2 py-1 text-[10px] font-mono text-zinc-500 dark:text-zinc-400 uppercase font-bold flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 mb-1">
                        <span>TIER 1 // NATIVE PRECISE</span>
                        <span className="text-[9px] px-1 py-0.2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                          {lang === 'id' ? 'RESMI' : 'OFFICIAL'}
                        </span>
                      </div>
                      <div className="space-y-0.5">
                        {tier1Languages.map((item) => {
                          const isActive = !activeTier2 && lang === item.code;
                          return (
                            <button
                              key={item.code}
                              type="button"
                              onClick={() => changeLanguage(item.code)}
                              className={`w-full text-left px-2 py-1.5 flex items-center justify-between transition text-xs rounded-none cursor-pointer ${
                                isActive 
                                  ? 'bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold' 
                                  : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800'
                              }`}
                            >
                              <span className="flex items-center gap-2">
                                <span className="text-sm">{item.flag}</span>
                                <span>{item.name}</span>
                              </span>
                              <div className="flex items-center gap-1.5 font-mono text-[10px]">
                                <span className="uppercase text-zinc-400 font-bold">{item.code}</span>
                                {isActive && <Check className="w-3.5 h-3.5 text-emerald-500 stroke-[2.5]" />}
                              </div>
                            </button>
                          );
                        })}
                      </div>
                    </div>

                    {/* LIST GROUP 2: TIER 2 GLOBAL TRANSLATE */}
                    <div className="pt-1 border-t border-zinc-200 dark:border-zinc-800">
                      <div className="px-2 py-1 text-[10px] font-mono text-zinc-500 dark:text-zinc-400 uppercase font-bold flex items-center justify-between mb-1">
                        <span>TIER 2 // GLOBAL TRANSLATE</span>
                        <span className="text-[9px] text-zinc-400 font-mono">GOOGLE AI</span>
                      </div>
                      <div className="space-y-0.5 max-h-48 overflow-y-auto pr-0.5">
                        {tier2Languages.map((item) => {
                          const isActive = activeTier2 === item.code;
                          return (
                            <button
                              key={item.code}
                              type="button"
                              onClick={() => selectTier2Language(item.code)}
                              className={`w-full text-left px-2 py-1.5 flex items-center justify-between transition text-xs rounded-none cursor-pointer ${
                                isActive 
                                  ? 'bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold' 
                                  : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800'
                              }`}
                            >
                              <span className="flex items-center gap-2">
                                <span className="text-sm">{item.flag}</span>
                                <span>{item.name} <span className="text-zinc-400 text-[11px]">({item.label})</span></span>
                              </span>
                              <div className="flex items-center gap-1.5 font-mono text-[10px]">
                                <span className="uppercase text-zinc-400 font-bold">{item.code}</span>
                                {isActive && <Check className="w-3.5 h-3.5 text-emerald-500 stroke-[2.5]" />}
                              </div>
                            </button>
                          );
                        })}
                      </div>
                    </div>
                  </motion.div>
                )}
              </AnimatePresence>
            </div>

            {/* Cart Button with Anti-Ghost Hold Countdown */}
            <div 
              className="relative"
              onMouseEnter={() => setCartDropdownOpen(true)}
              onMouseLeave={() => setCartDropdownOpen(false)}
            >
              <a
                href="/cart"
                className="bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 py-2.5 px-3 rounded-none font-mono text-xs font-bold flex items-center gap-2 transition"
              >
                <div className="relative">
                  <ShoppingCart className="w-4 h-4 text-emerald-500" />
                  {cartState.count > 0 && (
                    <span className="absolute -top-2.5 -right-2.5 bg-emerald-500 text-black text-[9px] font-black w-4 h-4 flex items-center justify-center rounded-none animate-pulse">
                      {cartState.count}
                    </span>
                  )}
                </div>
                <span>CART</span>
                {cartState.count > 0 && remainingSeconds > 0 && (
                  <span className="text-[10px] text-amber-600 dark:text-amber-400 font-mono font-bold flex items-center gap-0.5 bg-amber-500/10 px-1 py-0.5 border border-amber-500/30">
                    <Clock className="w-2.5 h-2.5" />
                    <span>{formatCountdown(remainingSeconds)}</span>
                  </span>
                )}
              </a>

              {/* Cart Dropdown Preview Board */}
              <AnimatePresence>
                {cartDropdownOpen && (
                  <motion.div
                    initial={{ opacity: 0, y: 5 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, y: 5 }}
                    transition={{ duration: 0.15 }}
                    className="absolute top-full right-0 mt-1 w-80 bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-emerald-500 shadow-2xl p-4 z-50 text-left font-mono"
                  >
                    <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2 mb-2">
                      <span className="text-[10px] text-zinc-400 uppercase font-bold tracking-wider">
                        ANTI-GHOST HOLD CART
                      </span>
                      {cartState.count > 0 && (
                        <span className="text-[10px] font-bold text-amber-500 flex items-center gap-1">
                          <Clock className="w-3 h-3" />
                          <span>{formatCountdown(remainingSeconds)}</span>
                        </span>
                      )}
                    </div>

                    {cartState.count === 0 ? (
                      <div className="py-4 text-center text-xs text-zinc-500 font-sans">
                        <p>Cart saat ini masih kosong.</p>
                        <a href="/blueprint" className="text-emerald-500 font-bold font-mono underline mt-1.5 inline-block">
                          Rancang Blueprint Proyek &rarr;
                        </a>
                      </div>
                    ) : (
                      <div className="space-y-3">
                        <div className="p-2 bg-amber-50 dark:bg-amber-950/30 border border-amber-300 dark:border-amber-800 text-[10px] text-amber-800 dark:text-amber-300 leading-tight">
                          <div className="font-bold flex items-center gap-1 mb-0.5">
                            <AlertCircle className="w-3 h-3 text-amber-500" />
                            <span>SLOT RESERVED (ANTI-GHOST HOLD)</span>
                          </div>
                          Slot pengerjaan & alokasi AI Ultra di-hold selama timer berjalan.
                        </div>

                        <div className="space-y-2 max-h-48 overflow-y-auto">
                          {cartState.items.map((item, idx) => (
                            <div key={idx} className="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs">
                              <div className="font-bold text-zinc-900 dark:text-white truncate">{item.title}</div>
                              <div className="flex items-center justify-between text-[10px] text-zinc-500 mt-1">
                                <span className="text-emerald-500 font-bold">{item.tier_name}</span>
                                <span>DP: Rp {new Intl.NumberFormat('id-ID').format(item.dp_amount)}</span>
                              </div>
                            </div>
                          ))}
                        </div>

                        <a
                          href="/cart"
                          className="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase tracking-wider py-2.5 text-center block transition"
                        >
                          Buka Cart & Bayar DP &rarr;
                        </a>
                      </div>
                    )}
                  </motion.div>
                )}
              </AnimatePresence>
            </div>

            {isBlueprintEnabled ? (
              <a
                href="/blueprint"
                className="bg-zinc-900 hover:bg-black dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white dark:text-black font-mono text-xs uppercase tracking-widest font-black py-2.5 px-5 rounded-none transition flex items-center gap-1.5 shadow-none"
              >
                <span>{lang === 'id' ? 'Mulai Blueprint' : 'Launch Blueprint'}</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </a>
            ) : (
              <a
                href="#services"
                className="bg-zinc-900 hover:bg-black dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white dark:text-black font-mono text-xs uppercase tracking-widest font-black py-2.5 px-5 rounded-none transition flex items-center gap-1.5 shadow-none"
              >
                <span>{lang === 'id' ? 'Eksplorasi Layanan' : 'Explore Services'}</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </a>
            )}
          </div>

          {/* Mobile Menu Button */}
          <button 
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            className="md:hidden p-2 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 rounded-none flex items-center gap-1.5"
          >
            {cartState.count > 0 && (
              <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            )}
            {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
          </button>

        </div>

        {/* Mobile Dropdown Menu */}
        <AnimatePresence>
          {mobileMenuOpen && (
            <motion.div 
              initial={{ height: 0, opacity: 0 }}
              animate={{ height: 'auto', opacity: 1 }}
              exit={{ height: 0, opacity: 0 }}
              className="md:hidden border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 p-4 space-y-3 font-mono text-xs uppercase"
            >
              {/* Mobile Cart Link with Countdown */}
              <a href="/cart" className="flex items-center justify-between py-2 text-zinc-900 dark:text-zinc-100 font-bold border-b border-zinc-100 dark:border-zinc-900 bg-zinc-50 dark:bg-zinc-900 p-2">
                <div className="flex items-center gap-2">
                  <ShoppingCart className="w-4 h-4 text-emerald-500" />
                  <span>Cart Belanja ({cartState.count})</span>
                </div>
                {cartState.count > 0 && (
                  <span className="text-[10px] text-amber-500 font-bold flex items-center gap-1">
                    <Clock className="w-3 h-3" />
                    <span>{formatCountdown(remainingSeconds)}</span>
                  </span>
                )}
              </a>
              {/* Mobile Unified Tier 1 & Tier 2 Language Selector */}
              <div className="border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 overflow-hidden rounded-xs">
                <button
                  type="button"
                  onClick={() => setMobileLangOpen(!mobileLangOpen)}
                  className="w-full py-2.5 px-3 flex items-center justify-between text-left font-mono text-xs font-bold cursor-pointer"
                >
                  <div className="flex items-center gap-2">
                    <Globe className="w-3.5 h-3.5 text-emerald-500" />
                    <span className="text-zinc-500 text-[10px] uppercase">BAHASA / LANG:</span>
                    <span className="text-zinc-900 dark:text-zinc-100">
                      {activeTier2 ? `${activeTier2.toUpperCase()} (Tier 2)` : `${lang.toUpperCase()} (Tier 1)`}
                    </span>
                  </div>
                  <ChevronDown className={`w-3.5 h-3.5 text-zinc-400 transition-transform duration-150 ${mobileLangOpen ? 'rotate-180 text-emerald-500' : ''}`} />
                </button>

                {mobileLangOpen && (
                  <div className="p-2 border-t border-zinc-200 dark:border-zinc-800 space-y-2 bg-white dark:bg-zinc-950 font-sans text-xs">
                    {/* Tier 1 Group */}
                    <div>
                      <div className="px-1.5 py-0.5 text-[10px] font-mono text-zinc-500 dark:text-zinc-400 uppercase font-bold flex items-center justify-between">
                        <span>TIER 1 // NATIVE PRECISE</span>
                        <span className="text-[9px] px-1 py-0.2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">RESMI</span>
                      </div>
                      <div className="grid grid-cols-2 gap-1.5 mt-1">
                        {tier1Languages.map((item) => {
                          const isActive = !activeTier2 && lang === item.code;
                          return (
                            <button
                              key={item.code}
                              type="button"
                              onClick={() => { changeLanguage(item.code); setMobileLangOpen(false); }}
                              className={`px-2.5 py-1.5 text-xs text-left border flex items-center justify-between transition cursor-pointer rounded-none ${
                                isActive
                                  ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-bold'
                                  : 'border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300'
                              }`}
                            >
                              <span>{item.flag} {item.code.toUpperCase()}</span>
                              {isActive && <Check className="w-3 h-3 text-emerald-500" />}
                            </button>
                          );
                        })}
                      </div>
                    </div>

                    {/* Tier 2 Group */}
                    <div className="pt-2 border-t border-zinc-100 dark:border-zinc-800">
                      <div className="px-1.5 py-0.5 text-[10px] font-mono text-zinc-500 dark:text-zinc-400 uppercase font-bold flex items-center justify-between">
                        <span>TIER 2 // GLOBAL TRANSLATE</span>
                        <span className="text-[9px] text-zinc-400 font-mono">GOOGLE AI</span>
                      </div>
                      <div className="grid grid-cols-2 gap-1.5 mt-1">
                        {tier2Languages.map((item) => {
                          const isActive = activeTier2 === item.code;
                          return (
                            <button
                              key={item.code}
                              type="button"
                              onClick={() => { selectTier2Language(item.code); setMobileLangOpen(false); }}
                              className={`px-2 py-1.5 text-xs text-left border flex items-center justify-between transition cursor-pointer rounded-none ${
                                isActive
                                  ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-bold'
                                  : 'border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300'
                              }`}
                            >
                              <span className="truncate">{item.flag} {item.name}</span>
                              {isActive && <Check className="w-3 h-3 text-emerald-500 shrink-0 ml-1" />}
                            </button>
                          );
                        })}
                      </div>
                    </div>
                  </div>
                )}
              </div>

              <a href="/" className="block py-2 text-zinc-800 dark:text-zinc-200 font-bold border-b border-zinc-100 dark:border-zinc-900">
                Beranda
              </a>
              <a href="/pricing" className="block py-2 text-emerald-600 dark:text-emerald-400 font-bold border-b border-zinc-100 dark:border-zinc-900 flex items-center justify-between">
                <span>{lang === 'id' ? 'Paket & Biaya Layanan' : 'Pricing & Packages'}</span>
                <span className="px-1.5 py-0.5 bg-emerald-500 text-black text-[9px] font-black">PROMO</span>
              </a>
              {isBlueprintEnabled && (
                <a href="/blueprint" className="block py-2 text-emerald-600 dark:text-emerald-400 font-bold border-b border-zinc-100 dark:border-zinc-900">
                  Project OS (PRD Generator) &rarr;
                </a>
              )}
              {isCvProEnabled && (
                <a href="/cv-pro" className="block py-2 text-purple-600 dark:text-purple-400 font-bold border-b border-zinc-100 dark:border-zinc-900">
                  CV Pro Studio &rarr;
                </a>
              )}
              {hasAnyService && (
                <a href="#services" className="block py-2 text-zinc-800 dark:text-zinc-200 font-bold border-b border-zinc-100 dark:border-zinc-900">
                  Aplikasi Solusi Kebutuhan Hidup
                </a>
              )}
              <a href="#architecture" className="block py-2 text-zinc-800 dark:text-zinc-200 font-bold border-b border-zinc-100 dark:border-zinc-900">
                Standar Arsitektur Enterprise
              </a>
              <a href="/customer/dashboard" className="block py-2 text-emerald-600 dark:text-emerald-400 font-bold border-b border-zinc-100 dark:border-zinc-900 flex items-center justify-between">
                <span>{lang === 'id' ? 'Portal Klien (Dashboard)' : 'Client Portal (Dashboard)'}</span>
                <span className="px-1.5 py-0.5 bg-emerald-500/10 text-emerald-500 border border-emerald-500/30 text-[9px] font-bold">AKUN</span>
              </a>
              <a href="/admin/login" className="block py-2 text-zinc-800 dark:text-zinc-200 font-bold">
                Login Administrator
              </a>
              {isBlueprintEnabled ? (
                <a 
                  href="/blueprint" 
                  className="w-full bg-emerald-500 text-black font-black py-3 text-center block rounded-none uppercase"
                >
                  Buat Blueprint Proyek Sekarang &rarr;
                </a>
              ) : (
                <a 
                  href="#services" 
                  className="w-full bg-emerald-500 text-black font-black py-3 text-center block rounded-none uppercase"
                >
                  Jelajahi Layanan &rarr;
                </a>
              )}
            </motion.div>
          )}
        </AnimatePresence>

      </nav>
    </header>
  );
}
