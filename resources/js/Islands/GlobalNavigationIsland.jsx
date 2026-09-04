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
  Globe
} from 'lucide-react';

export default function GlobalNavigationIsland({ settings }) {
  const [scrolled, setScrolled] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [servicesDropdownOpen, setServicesDropdownOpen] = useState(false);
  const [isDarkMode, setIsDarkMode] = useState(false);
  const [lang, setLang] = useState('id');

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
    { label: 'Project OS (PRD)', href: '/blueprint', highlight: true },
    { label: lang === 'id' ? 'Layanan HUB' : 'Service Hub', href: '#services' },
    { label: lang === 'id' ? 'Arsitektur' : 'Architecture', href: '#architecture' },
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
            <a href="/blueprint" className="text-emerald-400 hover:text-emerald-300 font-bold underline ml-1 flex items-center gap-0.5">
              <span>{lang === 'id' ? 'Coba Project OS' : 'Launch Project OS'}</span>
              <ArrowRight className="w-3 h-3 inline" />
            </a>
          </div>

          <div className="flex items-center gap-3">
            {/* Language Switch */}
            <div className="flex items-center gap-1 text-[11px] font-bold">
              <button 
                onClick={() => setLang('id')} 
                className={`px-1.5 py-0.5 rounded-none transition ${lang === 'id' ? 'bg-emerald-500 text-black font-black' : 'text-zinc-400 hover:text-white'}`}
              >
                ID
              </button>
              <span className="text-zinc-600">/</span>
              <button 
                onClick={() => setLang('en')} 
                className={`px-1.5 py-0.5 rounded-none transition ${lang === 'en' ? 'bg-emerald-500 text-black font-black' : 'text-zinc-400 hover:text-white'}`}
              >
                EN
              </button>
            </div>

            {/* Dark / Light Mode Switch */}
            <button 
              onClick={toggleTheme} 
              className="text-zinc-400 hover:text-white transition p-0.5" 
              title="Toggle Theme"
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
            ? 'bg-white/95 dark:bg-zinc-950/95 backdrop-blur-md border-zinc-200 dark:border-zinc-800 py-3 shadow-xs' 
            : 'bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 py-4'
        }`}
      >
        <div className="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between">
          
          {/* Brand Logo */}
          <a href="/" className="flex items-center gap-2 group">
            <span className="w-7 h-7 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black flex items-center justify-center font-mono font-black text-xs rounded-none transition-transform group-hover:scale-105">
              N
            </span>
            <span className="font-black text-lg sm:text-xl uppercase tracking-tighter text-zinc-900 dark:text-white font-sans">
              NERIAH<span className="text-emerald-500">PRO</span>
            </span>
            <span className="text-[10px] font-mono text-zinc-600 dark:text-zinc-400 border-l border-zinc-300 dark:border-zinc-700 pl-2 hidden md:inline">
              DIGITAL HUB
            </span>
          </a>

          {/* Desktop Navigation Links */}
          <div className="hidden md:flex items-center gap-8 font-mono text-xs uppercase tracking-wider font-bold">
            <a href="/" className="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
              {lang === 'id' ? 'Beranda' : 'Home'}
            </a>

            {/* Dropdown: Layanan HUB */}
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
                    <a href="/blueprint" className="block p-2.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none">
                      <div className="flex items-center justify-between mb-0.5">
                        <span className="font-bold text-zinc-900 dark:text-white text-xs">Project OS (PRD)</span>
                        <span className="px-1 py-0.2 bg-emerald-500 text-black text-[9px] font-bold">ACTIVE</span>
                      </div>
                      <p className="text-[11px] text-zinc-500 dark:text-zinc-400 font-sans leading-tight">
                        {lang === 'id' ? 'Generator PRD & Skema ERD Otomatis' : 'Automated PRD & ERD Schema'}
                      </p>
                    </a>

                    <a href="#services" className="block p-2.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none">
                      <div className="flex items-center justify-between mb-0.5">
                        <span className="font-bold text-zinc-900 dark:text-white text-xs">CV Generator</span>
                        <span className="px-1 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-[9px] font-bold">CANVA STYLE</span>
                      </div>
                      <p className="text-[11px] text-zinc-500 dark:text-zinc-400 font-sans leading-tight">
                        {lang === 'id' ? 'Studio CV Visual & Portofolio Klien' : 'Visual Resume & Portfolio Studio'}
                      </p>
                    </a>

                    <a href="#services" className="block p-2.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none">
                      <div className="flex items-center justify-between mb-0.5">
                        <span className="font-bold text-zinc-900 dark:text-white text-xs">Digital Contract</span>
                        <span className="px-1 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-[9px] font-bold">E-SIGN</span>
                      </div>
                      <p className="text-[11px] text-zinc-500 dark:text-zinc-400 font-sans leading-tight">
                        {lang === 'id' ? 'Tanda Tangan Elektronik & Penguncian Scope' : 'Legal E-Signature & Scope Freeze'}
                      </p>
                    </a>
                  </motion.div>
                )}
              </AnimatePresence>
            </div>

            <a href="#architecture" className="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
              {lang === 'id' ? 'Standar Rekayasa' : 'Engineering'}
            </a>

            <a href="/admin/login" className="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
              Portal Admin
            </a>
          </div>

          {/* Action CTAs */}
          <div className="hidden sm:flex items-center gap-3">
            <a
              href="/blueprint"
              className="bg-zinc-900 hover:bg-black dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white dark:text-black font-mono text-xs uppercase tracking-widest font-black py-2.5 px-5 rounded-none transition flex items-center gap-1.5 shadow-none"
            >
              <span>{lang === 'id' ? 'Mulai Blueprint' : 'Launch Blueprint'}</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </a>
          </div>

          {/* Mobile Menu Button */}
          <button 
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            className="md:hidden p-2 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 rounded-none"
          >
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
              <a href="/" className="block py-2 text-zinc-800 dark:text-zinc-200 font-bold border-b border-zinc-100 dark:border-zinc-900">
                Beranda
              </a>
              <a href="/blueprint" className="block py-2 text-emerald-600 dark:text-emerald-400 font-bold border-b border-zinc-100 dark:border-zinc-900">
                Project OS (PRD Generator) &rarr;
              </a>
              <a href="#services" className="block py-2 text-zinc-800 dark:text-zinc-200 font-bold border-b border-zinc-100 dark:border-zinc-900">
                Layanan Digital HUB
              </a>
              <a href="#architecture" className="block py-2 text-zinc-800 dark:text-zinc-200 font-bold border-b border-zinc-100 dark:border-zinc-900">
                Standar Arsitektur (PostgreSQL ULID)
              </a>
              <a href="/admin/login" className="block py-2 text-zinc-800 dark:text-zinc-200 font-bold">
                Login Administrator
              </a>
              <a 
                href="/blueprint" 
                className="w-full bg-emerald-500 text-black font-black py-3 text-center block rounded-none uppercase"
              >
                Buat Blueprint Proyek Sekarang &rarr;
              </a>
            </motion.div>
          )}
        </AnimatePresence>

      </nav>
    </header>
  );
}
