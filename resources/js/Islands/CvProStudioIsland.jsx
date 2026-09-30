import React, { useState, useEffect, useRef } from 'react';
import {
  FileText,
  Sparkles,
  Mic,
  MicOff,
  Mail,
  Download,
  Eye,
  CheckCircle,
  AlertTriangle,
  Plus,
  Trash2,
  Copy,
  Save,
  Share2,
  RotateCcw,
  Briefcase,
  GraduationCap,
  Award,
  Code,
  Layout,
  Type,
  Palette,
  Check,
  ChevronRight,
  ChevronDown,
  TrendingUp,
  Volume2,
  UploadCloud,
  FileUp,
  Scan,
  X,
  DollarSign,
  FileSpreadsheet,
  ArrowUpRight,
  Wand2,
  Sliders,
  FileDown,
  Layers,
  HelpCircle,
  Clock,
  ArrowRight,
  Sparkle,
  Crown,
  Lock,
  Play,
  Square,
  Headphones,
  Globe,
  Zap,
  MessageSquare,
  ExternalLink,
  Monitor,
  Smartphone,
  Sun,
  Moon,
  FolderGit2,
  Users,
  User
} from 'lucide-react';

export default function CvProStudioIsland({ initialData, featureFlags, currentUser }) {

  // Preview Mode: 'cv' (Dokumen A4 Cetak) | 'portfolio' (Web Portfolio Live)
  const [previewMode, setPreviewMode] = useState('cv');
  const [previewDevice, setPreviewDevice] = useState('desktop');

  // Accordion State with localStorage persistence
  const [accordionState, setAccordionState] = useState(() => {
    try {
      const saved = localStorage.getItem('cv_pro_accordion_state');
      if (saved) return JSON.parse(saved);
    } catch (e) {}
    return {
      style: true,
      web_portfolio: false,
      avatar: false,
      reorder: false,
      personal: true,
      experience: true,
      education: false,
      skills: false,
      projects: false,
      certifications: false,
      references: false,
    };
  });

  const toggleAccordion = (sectionKey) => {
    setAccordionState((prev) => {
      const next = { ...prev, [sectionKey]: !prev[sectionKey] };
      try {
        localStorage.setItem('cv_pro_accordion_state', JSON.stringify(next));
      } catch (e) {}
      return next;
    });
  };

  const setAllAccordions = (open = true) => {
    setAccordionState((prev) => {
      const next = Object.keys(prev).reduce((acc, k) => {
        acc[k] = open;
        return acc;
      }, {});
      try {
        localStorage.setItem('cv_pro_accordion_state', JSON.stringify(next));
      } catch (e) {}
      return next;
    });
  };

  // Web Portfolio Customization States (10 Granular Controls)
  const [wpNavVariant, setWpNavVariant] = useState('floating_pill'); // 'minimal' | 'floating_pill' | 'brutalist' | 'glassmorphic' | 'sidebar'
  const [wpNavBg, setWpNavBg] = useState('#09090b');
  const [wpNavText, setWpNavText] = useState('#ffffff');
  const [wpNavFont, setWpNavFont] = useState('Inter');

  const [wpFooterVariant, setWpFooterVariant] = useState('social_hub'); // 'simple_clean' | 'multi_column' | 'social_hub' | 'brutalist'
  const [wpFooterBg, setWpFooterBg] = useState('#09090b');
  const [wpFooterText, setWpFooterText] = useState('#94a3b8');

  const [wpTitleFont, setWpTitleFont] = useState('Inter');
  const [wpTitleSize, setWpTitleSize] = useState('text-3xl sm:text-5xl');
  const [wpSubtitleSize, setWpSubtitleSize] = useState('text-base sm:text-lg');
  const [wpBodySize, setWpBodySize] = useState('text-xs sm:text-sm');

  const [wpBulletType, setWpBulletType] = useState('check'); // 'check' | 'disc' | 'diamond' | 'arrow' | 'square' | 'dash'
  const [wpBulletColor, setWpBulletColor] = useState('#10b981');

  const [wpCardStyle, setWpCardStyle] = useState('subtle_border'); // 'modern_flat' | 'subtle_border' | 'glassmorphic' | 'neo_brutalist' | 'gradient_glow'
  const [wpCardRadius, setWpCardRadius] = useState('rounded-xl'); // 'rounded-none' | 'rounded-lg' | 'rounded-xl' | 'rounded-2xl'
  const [wpCardBorderColor, setWpCardBorderColor] = useState('#3f3f46');

  const [wpSpyStyle, setWpSpyStyle] = useState('glowing_pill'); // 'glowing_pill' | 'underline_runner' | 'active_dot' | 'gradient_bar'
  const [wpSpyColor, setWpSpyColor] = useState('#6366f1');

  const [wpPhone, setWpPhone] = useState(content?.personal_info?.phone || '628123456789');
  const [wpCtaText, setWpCtaText] = useState('Hubungi via WhatsApp');
  const [wpMessage, setWpMessage] = useState(
    `Halo ${content?.personal_info?.name || 'Kandidat'}, saya melihat web portfolio Anda dan tertarik mendiskusikan peluang karir.`
  );

  const [wpLayout, setWpLayout] = useState('bento_grid'); // 'bento_grid' | 'split_hero' | 'developer_terminal' | 'showcase_cards' | 'editorial_narrative'
  const [wpTheme, setWpTheme] = useState('dark'); // 'dark' | 'light'

  // Smart AI Job Category Matcher State
  const [targetJobRole, setTargetJobRole] = useState('Lead Systems Architect');
  const [jobMatchReason, setJobMatchReason] = useState('');

  // Accordion Header Component Helper
  const renderAccordionHeader = (key, title, icon, badge, extra = null) => {
    const isOpen = accordionState[key];
    return (
      <div
        onClick={() => toggleAccordion(key)}
        className="p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between cursor-pointer select-none hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition group shadow-2xs"
      >
        <div className="flex items-center gap-2.5">
          <span className="p-1 rounded bg-zinc-100 dark:bg-zinc-800 text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition">
            {icon}
          </span>
          <span className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
            {title}
          </span>
          {badge && (
            <span className="px-1.5 py-0.5 text-[9px] font-mono font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 rounded">
              {badge}
            </span>
          )}
        </div>

        <div className="flex items-center gap-2" onClick={(e) => e.stopPropagation()}>
          {extra}
          <button
            type="button"
            onClick={() => toggleAccordion(key)}
            className="p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition"
            title={isOpen ? 'Tutup Panel' : 'Buka Panel'}
          >
            <ChevronDown className={`w-4 h-4 transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`} />
          </button>
        </div>
      </div>
    );
  };

  // Print PDF Helper
  const handlePrintPdf = () => {
    if (previewMode !== 'cv') {
      setPreviewMode('cv');
    }
    setTimeout(() => {
      window.print();
    }, 150);
  };

  // Smart AI Job Category Matcher Handler
  const handleSmartJobMatch = () => {
    if (!checkAiAccessOrShowUpgrade(lang === 'id' ? 'Sistem Cerdas Penentu Variasi Portfolio Loker' : 'Smart Portfolio Role Matcher')) return;

    const query = (targetJobRole || '').toLowerCase();
    let recommended = {
      layout: 'bento_grid',
      nav: 'floating_pill',
      footer: 'social_hub',
      titleFont: 'Inter',
      bullet: 'check',
      bulletColor: '#10b981',
      cardStyle: 'subtle_border',
      spyStyle: 'glowing_pill',
      spyColor: '#6366f1',
      theme: 'dark',
      reason: 'Konfigurasi seimbang direkomendasikan untuk posisi profesional modern serbaguna.',
    };

    if (query.includes('dev') || query.includes('software') || query.includes('engineer') || query.includes('backend') || query.includes('architect') || query.includes('system') || query.includes('cloud') || query.includes('tech') || query.includes('fullstack')) {
      recommended = {
        layout: 'developer_terminal',
        nav: 'brutalist',
        footer: 'brutalist',
        titleFont: 'JetBrains Mono',
        bullet: 'arrow',
        bulletColor: '#10b981',
        cardStyle: 'neo_brutalist',
        spyStyle: 'gradient_bar',
        spyColor: '#10b981',
        theme: 'dark',
        reason: 'Rekomendasi Developer & Engineering: Estetika terminal hacker, bullet panah CLI, tipografi monospace presisi tinggi, dan skema warna emerald cyberpunk.',
      };
    } else if (query.includes('design') || query.includes('ui') || query.includes('ux') || query.includes('creative') || query.includes('art') || query.includes('product') || query.includes('frontend')) {
      recommended = {
        layout: 'bento_grid',
        nav: 'glassmorphic',
        footer: 'social_hub',
        titleFont: 'Outfit',
        bullet: 'diamond',
        bulletColor: '#ec4899',
        cardStyle: 'glassmorphic',
        spyStyle: 'glowing_pill',
        spyColor: '#ec4899',
        theme: 'dark',
        reason: 'Rekomendasi Creative & UI/UX: Bento Grid interaktif, efek glassmorphism modern, font display Outfit dinamis, dan aksen pink neon.',
      };
    } else if (query.includes('exec') || query.includes('director') || query.includes('manager') || query.includes('vp') || query.includes('chief') || query.includes('lead') || query.includes('head') || query.includes('corporate') || query.includes('bank') || query.includes('finance') || query.includes('invest')) {
      recommended = {
        layout: 'split_hero',
        nav: 'minimal',
        footer: 'multi_column',
        titleFont: 'Playfair Display',
        bullet: 'check',
        bulletColor: '#d97706',
        cardStyle: 'modern_flat',
        spyStyle: 'underline_runner',
        spyColor: '#d97706',
        theme: 'light',
        reason: 'Rekomendasi Eksekutif & Korporat: Split Hero elegan dengan tipografi serif berwibawa, layout multi-kolom formal, dan palet amber emas kemewahan bisnis.',
      };
    }

    setWpLayout(recommended.layout);
    setWpNavVariant(recommended.nav);
    setWpFooterVariant(recommended.footer);
    setWpTitleFont(recommended.titleFont);
    setWpBulletType(recommended.bullet);
    setWpBulletColor(recommended.bulletColor);
    setWpCardStyle(recommended.cardStyle);
    setWpSpyStyle(recommended.spyStyle);
    setWpSpyColor(recommended.spyColor);
    setWpTheme(recommended.theme);
    setJobMatchReason(recommended.reason);
    setPreviewMode('portfolio');
  };


  // Render Portfolio Bullet Point based on wpBulletType & wpBulletColor
  const renderPortfolioBullet = () => {
    switch (wpBulletType) {
      case 'check':
        return <Check className="w-3.5 h-3.5 shrink-0 mt-0.5" style={{ color: wpBulletColor }} />;
      case 'arrow':
        return <span className="font-bold shrink-0 mt-0.5 text-sm" style={{ color: wpBulletColor }}>&rarr;</span>;
      case 'diamond':
        return <span className="shrink-0 mt-0.5 text-xs" style={{ color: wpBulletColor }}>&#9670;</span>;
      case 'square':
        return <span className="shrink-0 mt-0.5 text-xs" style={{ color: wpBulletColor }}>&#9632;</span>;
      case 'dash':
        return <span className="font-bold shrink-0 mt-0.5 text-sm" style={{ color: wpBulletColor }}>&mdash;</span>;
      case 'disc':
      default:
        return <span className="shrink-0 mt-0.5 text-sm leading-none" style={{ color: wpBulletColor }}>&bull;</span>;
    }
  };

  // Helper for Card CSS Classes
  const getCardClasses = (customPadding = 'p-5 sm:p-6') => {
    const radius = wpCardRadius;
    switch (wpCardStyle) {
      case 'modern_flat':
        return `${customPadding} ${radius} bg-zinc-100 dark:bg-zinc-900 border-0 transition-all`;
      case 'glassmorphic':
        return `${customPadding} ${radius} backdrop-blur-xl bg-white/40 dark:bg-zinc-900/40 border border-white/30 dark:border-zinc-800 shadow-lg transition-all`;
      case 'neo_brutalist':
        return `${customPadding} ${radius} bg-white dark:bg-zinc-900 border-2 border-zinc-950 dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.9)] transition-all`;
      case 'gradient_glow':
        return `${customPadding} ${radius} bg-gradient-to-br from-indigo-950/20 via-zinc-900/80 to-purple-950/20 border border-indigo-500/40 shadow-[0_0_15px_rgba(99,102,241,0.2)] transition-all`;
      case 'subtle_border':
      default:
        return `${customPadding} ${radius} bg-white dark:bg-zinc-900/90 border border-zinc-200 dark:border-zinc-800 shadow-xs transition-all`;
    }
  };

  // 1. Navigation Island Component
  const renderWebPortfolioNavbar = () => {
    const links = [
      { id: 'about', label: 'Tentang' },
      { id: 'experience', label: 'Pengalaman' },
      { id: 'projects', label: 'Proyek' },
      { id: 'skills', label: 'Keahlian' },
      { id: 'contact', label: 'Kontak' },
    ];

    const getSpyClasses = (isFirst) => {
      if (!isFirst) return 'text-zinc-400 hover:text-zinc-200';
      switch (wpSpyStyle) {
        case 'glowing_pill':
          return `px-2.5 py-1 rounded-full text-white font-bold shadow-xs`;
        case 'underline_runner':
          return `border-b-2 font-bold text-white`;
        case 'active_dot':
          return `font-bold text-white flex items-center gap-1.5`;
        default:
          return `font-bold text-white`;
      }
    };

    if (wpNavVariant === 'floating_pill') {
      return (
        <header className="sticky top-3 z-30 px-4">
          <div
            className="max-w-2xl mx-auto px-5 py-2.5 rounded-full shadow-xl border flex items-center justify-between backdrop-blur-md transition-all"
            style={{ backgroundColor: `${wpNavBg}e6`, borderColor: wpSpyColor, fontFamily: `'${wpNavFont}', sans-serif` }}
          >
            <div className="flex items-center gap-2">
              <span className="w-2.5 h-2.5 rounded-full animate-pulse" style={{ backgroundColor: wpSpyColor }} />
              <span className="font-bold text-xs tracking-wider" style={{ color: wpNavText }}>
                {content?.personal_info?.name ? content.personal_info.name.split(' ')[0] : 'PORTFOLIO'}
              </span>
            </div>
            <nav className="flex items-center gap-4 text-xs font-medium">
              {links.map((link, idx) => (
                <span
                  key={link.id}
                  className={`cursor-pointer transition ${getSpyClasses(idx === 0)}`}
                  style={idx === 0 && wpSpyStyle === 'glowing_pill' ? { backgroundColor: wpSpyColor } : idx === 0 && wpSpyStyle === 'underline_runner' ? { borderColor: wpSpyColor } : {}}
                >
                  {idx === 0 && wpSpyStyle === 'active_dot' && (
                    <span className="w-1.5 h-1.5 rounded-full inline-block" style={{ backgroundColor: wpSpyColor }} />
                  )}
                  {link.label}
                </span>
              ))}
            </nav>
            {wpPhone && (
              <a
                href={`https://wa.me/${wpPhone.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(wpMessage)}`}
                target="_blank"
                rel="noopener noreferrer"
                className="px-3 py-1 rounded-full text-[11px] font-bold text-white transition flex items-center gap-1 shadow-xs"
                style={{ backgroundColor: '#10b981' }}
              >
                <MessageSquare className="w-3 h-3" />
                <span>WA</span>
              </a>
            )}
          </div>
        </header>
      );
    }

    if (wpNavVariant === 'brutalist') {
      return (
        <header
          className="sticky top-0 z-30 px-6 py-3 border-b-2 border-black dark:border-white shadow-[4px_4px_0px_0px_#000] dark:shadow-[4px_4px_0px_0px_#fff] flex items-center justify-between transition-all"
          style={{ backgroundColor: wpNavBg, fontFamily: `'${wpNavFont}', monospace` }}
        >
          <div className="flex items-center gap-2">
            <span className="px-2 py-0.5 bg-black text-white dark:bg-white dark:text-black font-black text-xs uppercase">
              {content?.personal_info?.name || 'SYS.ARCH'}
            </span>
          </div>
          <nav className="flex items-center gap-5 text-xs font-bold uppercase">
            {links.map((link, idx) => (
              <span
                key={link.id}
                className={`cursor-pointer ${idx === 0 ? 'underline decoration-2' : 'hover:opacity-75'}`}
                style={{ color: wpNavText, textDecorationColor: wpSpyColor }}
              >
                {link.label}
              </span>
            ))}
          </nav>
          {wpPhone && (
            <a
              href={`https://wa.me/${wpPhone.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(wpMessage)}`}
              target="_blank"
              rel="noopener noreferrer"
              className="px-3 py-1 bg-emerald-500 text-black font-black text-xs uppercase border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000] flex items-center gap-1"
            >
              <MessageSquare className="w-3.5 h-3.5" />
              <span>Direct WA</span>
            </a>
          )}
        </header>
      );
    }

    // Default: Minimal & Glassmorphic
    return (
      <header
        className={`sticky top-0 z-30 px-6 py-3.5 border-b flex items-center justify-between transition-all ${
          wpNavVariant === 'glassmorphic'
            ? 'backdrop-blur-xl bg-white/20 dark:bg-zinc-950/40 border-white/20 dark:border-zinc-800'
            : 'border-zinc-200 dark:border-zinc-800'
        }`}
        style={{ backgroundColor: wpNavVariant === 'glassmorphic' ? undefined : wpNavBg, fontFamily: `'${wpNavFont}', sans-serif` }}
      >
        <div className="flex items-center gap-2.5">
          <div className="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs text-white" style={{ backgroundColor: wpSpyColor }}>
            {content?.personal_info?.name ? content.personal_info.name.charAt(0) : 'N'}
          </div>
          <span className="font-bold text-sm tracking-tight" style={{ color: wpNavText }}>
            {content?.personal_info?.name || 'My Web Portfolio'}
          </span>
        </div>
        <nav className="hidden sm:flex items-center gap-5 text-xs font-medium">
          {links.map((link, idx) => (
            <span
              key={link.id}
              className={`cursor-pointer transition ${idx === 0 ? 'font-bold' : 'opacity-70 hover:opacity-100'}`}
              style={{ color: idx === 0 ? wpSpyColor : wpNavText }}
            >
              {link.label}
            </span>
          ))}
        </nav>
        {wpPhone && (
          <a
            href={`https://wa.me/${wpPhone.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(wpMessage)}`}
            target="_blank"
            rel="noopener noreferrer"
            className="px-3 py-1.5 rounded-lg text-xs font-bold text-white transition flex items-center gap-1.5 shadow-xs"
            style={{ backgroundColor: '#10b981' }}
          >
            <MessageSquare className="w-3.5 h-3.5" />
            <span>{wpCtaText || 'Hubungi WA'}</span>
          </a>
        )}
      </header>
    );
  };

  // 2. Hero Section Component
  const renderWebPortfolioHero = () => {
    const p = content?.personal_info || {};
    const photoUrl = avatarConfig.customUrl || p.photo_url || tempImageSrc;
    const shapeClass =
      avatarConfig.shape === 'blob'
        ? 'rounded-[30%_70%_70%_30%/30%_30%_70%_70%]'
        : avatarConfig.shape === 'square'
        ? 'rounded-none'
        : avatarConfig.shape === 'rounded'
        ? 'rounded-2xl'
        : 'rounded-full';

    return (
      <section className="px-6 py-10 sm:py-16 max-w-5xl mx-auto flex flex-col md:flex-row items-center gap-8 justify-between">
        <div className="flex-1 space-y-4 text-center md:text-left">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono border" style={{ borderColor: `${wpSpyColor}60`, color: wpSpyColor }}>
            <span className="w-2 h-2 rounded-full animate-ping" style={{ backgroundColor: wpSpyColor }} />
            <span>TERSEDIA UNTUK PELUANG KARIR & KONSULTASI</span>
          </div>

          <h1 className={`font-black tracking-tight ${wpTitleSize}`} style={{ fontFamily: `'${wpTitleFont}', sans-serif` }}>
            {p.name || 'Alex Pratama, S.Kom'}
          </h1>

          <p className={`font-medium text-zinc-500 dark:text-zinc-400 ${wpSubtitleSize}`}>
            {p.title || 'Senior Full Stack & Systems Architect'}
          </p>

          <p className={`text-zinc-600 dark:text-zinc-300 leading-relaxed max-w-2xl ${wpBodySize}`}>
            {content?.summary ||
              'Arsitek perangkat lunak berpengalaman dalam merancang platform berkinerja tinggi, sistem terdistribusi, dan otomasi cerdas skala enterprise.'}
          </p>

          {/* Quick Badges & Direct WhatsApp Redirect Button */}
          <div className="pt-2 flex flex-wrap items-center gap-3 justify-center md:justify-start text-xs font-mono">
            {p.location && (
              <span className="px-2.5 py-1 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                📍 {p.location}
              </span>
            )}
            {p.email && (
              <span className="px-2.5 py-1 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                ✉️ {p.email}
              </span>
            )}
            {p.linkedin && (
              <span className="px-2.5 py-1 rounded bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                🔗 LinkedIn
              </span>
            )}
          </div>

          {/* WhatsApp Direct CTA Button (Control 7) */}
          <div className="pt-3 flex items-center gap-3 justify-center md:justify-start">
            <a
              href={wpPhone ? `https://wa.me/${wpPhone.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(wpMessage)}` : '#'}
              target="_blank"
              rel="noopener noreferrer"
              className="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm rounded-xl transition shadow-lg flex items-center gap-2 hover:scale-105 active:scale-95"
            >
              <MessageSquare className="w-4 h-4" />
              <span>{wpCtaText || 'Hubungi via WhatsApp'}</span>
              <ExternalLink className="w-3.5 h-3.5 opacity-80" />
            </a>
            <button
              type="button"
              onClick={handlePrintPdf}
              className="px-4 py-2.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 font-bold text-xs sm:text-sm rounded-xl transition flex items-center gap-2"
            >
              <Download className="w-4 h-4" />
              <span>Unduh CV A4</span>
            </button>
          </div>
        </div>

        {/* Profile Avatar with Configured Shape */}
        {avatarConfig.showPhoto && photoUrl && (
          <div className="relative shrink-0">
            <div
              className="absolute -inset-1.5 rounded-full opacity-60 blur-lg transition duration-500"
              style={{ background: `linear-gradient(45deg, ${wpSpyColor}, #ec4899)` }}
            />
            <img
              src={photoUrl}
              alt={p.name || 'Profile'}
              className={`relative w-40 h-40 sm:w-52 sm:h-52 object-cover border-4 border-white dark:border-zinc-900 shadow-2xl ${shapeClass}`}
            />
          </div>
        )}
      </section>
    );
  };

  // 3. Main Content based on wpLayout
  const renderWebPortfolioLayoutContent = () => {
    const experiences = content?.experience || [];
    const skillsList = content?.skills || [];
    const projectsList = content?.projects || [];
    const educationList = content?.education || [];

    // Layout 1: Developer Terminal Mode
    if (wpLayout === 'developer_terminal') {
      return (
        <div className="space-y-6 font-mono text-xs">
          <div className="bg-black text-emerald-400 p-5 rounded-xl border border-zinc-800 shadow-2xl space-y-4">
            <div className="flex items-center justify-between pb-3 border-b border-zinc-800 text-zinc-500">
              <span className="text-zinc-400">bash — alex@neriahpro-os:~</span>
              <span className="text-[10px] text-emerald-500 font-bold">STATUS: 200 OK</span>
            </div>
            <div>
              <p className="text-zinc-400">$ whoami</p>
              <p className="text-white font-bold text-sm pt-1">{content?.personal_info?.name} // {content?.personal_info?.title}</p>
            </div>
            <div>
              <p className="text-zinc-400">$ cat skills.json</p>
              <div className="flex flex-wrap gap-1.5 pt-2">
                {skillsList.map((sk, idx) => (
                  <span key={idx} className="px-2 py-0.5 bg-zinc-900 text-emerald-300 border border-emerald-900/60 rounded">
                    "{sk.name || sk}"
                  </span>
                ))}
              </div>
            </div>
            <div>
              <p className="text-zinc-400">$ git log --oneline --experience</p>
              <div className="space-y-2 pt-2">
                {experiences.map((exp, idx) => (
                  <div key={idx} className="pl-3 border-l-2 border-emerald-600/60">
                    <div className="text-white font-bold">{exp.role} @ {exp.company} ({exp.period})</div>
                    <ul className="text-zinc-400 space-y-1 pt-1">
                      {(exp.bullets || []).map((b, bIdx) => (
                        <li key={bIdx} className="flex items-start gap-2">
                          {renderPortfolioBullet()}
                          <span>{b}</span>
                        </li>
                      ))}
                    </ul>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      );
    }

    // Layout 2: Split Hero Mode
    if (wpLayout === 'split_hero') {
      return (
        <div className="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
          {/* Left Column (Sticky Sidebar) */}
          <div className="md:col-span-5 space-y-6 md:sticky md:top-20">
            <div className={getCardClasses('p-6')}>
              <h3 className="text-sm font-bold uppercase tracking-wider text-zinc-400 font-mono mb-3">Tentang Saya</h3>
              <p className={`text-zinc-600 dark:text-zinc-300 leading-relaxed ${wpBodySize}`}>
                {content?.summary || 'Profesional berdedikasi tinggi siap menciptakan dampak positif bagi organisasi.'}
              </p>
            </div>
            <div className={getCardClasses('p-6')}>
              <h3 className="text-sm font-bold uppercase tracking-wider text-zinc-400 font-mono mb-3">Keahlian Utama</h3>
              <div className="flex flex-wrap gap-1.5">
                {skillsList.map((sk, idx) => (
                  <span key={idx} className="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs rounded-md font-medium">
                    {sk.name || sk}
                  </span>
                ))}
              </div>
            </div>
          </div>

          {/* Right Column (Timeline) */}
          <div className="md:col-span-7 space-y-6">
            <div className={getCardClasses('p-6')}>
              <h3 className="text-base font-bold text-zinc-900 dark:text-white mb-4 flex items-center gap-2">
                <Briefcase className="w-4 h-4 text-indigo-500" />
                <span>Pengalaman Profesional</span>
              </h3>
              <div className="space-y-6">
                {experiences.map((exp, idx) => (
                  <div key={idx} className="border-l-2 pl-4 space-y-1.5" style={{ borderColor: wpSpyColor }}>
                    <div className="flex justify-between items-baseline">
                      <h4 className="font-bold text-sm text-zinc-900 dark:text-white">{exp.role}</h4>
                      <span className="text-[11px] font-mono text-zinc-400">{exp.period}</span>
                    </div>
                    <div className="text-xs font-medium text-zinc-500">{exp.company} &bull; {exp.location}</div>
                    <ul className={`text-zinc-600 dark:text-zinc-300 space-y-1 pt-1.5 ${wpBodySize}`}>
                      {(exp.bullets || []).map((b, bIdx) => (
                        <li key={bIdx} className="flex items-start gap-2">
                          {renderPortfolioBullet()}
                          <span>{b}</span>
                        </li>
                      ))}
                    </ul>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      );
    }

    // Default Layout: Bento Grid Modern
    return (
      <div className="space-y-8">
        {/* Bento Grid Top: Experience (2 cols) & Skills (1 col) */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
          <div className={`md:col-span-2 ${getCardClasses('p-6')} space-y-5`}>
            <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
              <h3 className="font-bold text-base text-zinc-900 dark:text-white flex items-center gap-2">
                <Briefcase className="w-4 h-4 text-indigo-500" />
                <span>Pengalaman Kerja Terpilih</span>
              </h3>
              <span className="text-[10px] font-mono text-zinc-400 font-bold">{experiences.length} PERUSAHAAN</span>
            </div>
            <div className="space-y-5">
              {experiences.map((exp, idx) => (
                <div key={idx} className="space-y-1.5 pb-4 border-b border-zinc-100 dark:border-zinc-800/60 last:border-0">
                  <div className="flex justify-between items-baseline">
                    <span className="font-bold text-sm text-zinc-900 dark:text-white">{exp.role}</span>
                    <span className="text-[11px] font-mono text-zinc-400">{exp.period}</span>
                  </div>
                  <div className="text-xs text-zinc-500 font-medium">{exp.company} &bull; {exp.location}</div>
                  <ul className={`text-zinc-600 dark:text-zinc-300 space-y-1 pt-1 ${wpBodySize}`}>
                    {(exp.bullets || []).map((b, bIdx) => (
                      <li key={bIdx} className="flex items-start gap-2">
                        {renderPortfolioBullet()}
                        <span>{b}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              ))}
            </div>
          </div>

          <div className={`${getCardClasses('p-6')} space-y-4 flex flex-col justify-between`}>
            <div className="space-y-3">
              <h3 className="font-bold text-base text-zinc-900 dark:text-white flex items-center gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                <Code className="w-4 h-4 text-purple-500" />
                <span>Keahlian Teknis</span>
              </h3>
              <div className="flex flex-wrap gap-1.5 pt-1">
                {skillsList.map((sk, idx) => (
                  <span
                    key={idx}
                    className="px-2.5 py-1 rounded-md text-xs font-mono font-medium transition"
                    style={{ backgroundColor: `${wpSpyColor}15`, color: wpSpyColor, borderColor: `${wpSpyColor}40`, borderWidth: 1 }}
                  >
                    {sk.name || sk}
                  </span>
                ))}
              </div>
            </div>

            <div className="pt-4 border-t border-zinc-200 dark:border-zinc-800 space-y-2">
              <span className="text-[11px] font-mono font-bold uppercase text-zinc-400 block">Pendidikan</span>
              {educationList.slice(0, 2).map((edu, idx) => (
                <div key={idx} className="text-xs">
                  <div className="font-bold text-zinc-900 dark:text-white">{edu.degree}</div>
                  <div className="text-zinc-500">{edu.school} ({edu.year})</div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Bento Grid Bottom: Projects Showcase */}
        {projectsList.length > 0 && (
          <div className={`${getCardClasses('p-6')} space-y-4`}>
            <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
              <h3 className="font-bold text-base text-zinc-900 dark:text-white flex items-center gap-2">
                <FolderGit2 className="w-4 h-4 text-emerald-500" />
                <span>Portofolio Proyek Terpilih</span>
              </h3>
              <span className="text-[10px] font-mono text-zinc-400 font-bold">{projectsList.length} PROYEK</span>
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 pt-2">
              {projectsList.map((proj, idx) => (
                <div key={idx} className="p-4 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 space-y-2 flex flex-col justify-between">
                  <div className="space-y-1">
                    <h4 className="font-bold text-xs text-zinc-900 dark:text-white">{proj.name || proj.title}</h4>
                    <p className="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">{proj.description}</p>
                  </div>
                  {proj.link && (
                    <a
                      href={proj.link}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="text-[11px] font-mono text-indigo-600 dark:text-indigo-400 font-bold hover:underline flex items-center gap-1 pt-1"
                    >
                      <span>Lihat Detail</span>
                      <ArrowUpRight className="w-3 h-3" />
                    </a>
                  )}
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
    );
  };

  // 4. Website Footer Component
  const renderWebPortfolioFooter = () => {
    return (
      <footer
        className="mt-12 py-8 px-6 border-t border-zinc-200 dark:border-zinc-800 transition-all text-xs"
        style={{ backgroundColor: wpFooterBg, color: wpFooterText }}
      >
        <div className="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
          <div className="flex items-center gap-2">
            <span className="font-bold">{content?.personal_info?.name || 'Alex Pratama'}</span>
            <span>&bull;</span>
            <span>{content?.personal_info?.title || 'Systems Architect'}</span>
          </div>

          <div className="flex items-center gap-3">
            {wpPhone && (
              <a
                href={`https://wa.me/${wpPhone.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(wpMessage)}`}
                target="_blank"
                rel="noopener noreferrer"
                className="text-emerald-500 hover:underline flex items-center gap-1 font-bold"
              >
                <MessageSquare className="w-3.5 h-3.5" />
                <span>WhatsApp ({wpPhone})</span>
              </a>
            )}
            <span>&copy; {new Date().getFullYear()} NeriahPro Island OS.</span>
          </div>
        </div>
      </footer>
    );
  };



  // Navigation Tabs: 'editor' | 'job_hub' | 'finance' | 'ats_audit' | 'mock_interview' | 'outreach'
  const [activeTab, setActiveTab] = useState('editor');

  // Tour Guide State
  const [tourModalOpen, setTourModalOpen] = useState(false);
  const [tourStep, setTourStep] = useState(0);

  // Avatar & Photo Cropper State
  const [cropModalOpen, setCropModalOpen] = useState(false);
  const [tempImageSrc, setTempImageSrc] = useState(null);
  const [photoZoom, setPhotoZoom] = useState(1);
  const [photoStyle, setPhotoStyle] = useState('circle'); // 'circle' | 'rounded' | 'blob'
  const [showPhoto, setShowPhoto] = useState(true);
  const cropCanvasRef = useRef(null);

  // Sosmed Promo Generator State
  const [sosmedTopic, setSosmedTopic] = useState('OpenToWork');
  const [sosmedCustomPrompt, setSosmedCustomPrompt] = useState('');
  const [sosmedData, setSosmedData] = useState(null);
  const [generatingSosmed, setGeneratingSosmed] = useState(false);
  const [sosmedGradient, setSosmedGradient] = useState('purple'); // 'purple' | 'emerald' | 'amber' | 'blue'
  const sosmedCanvasRef = useRef(null);

  // Transcript Paste & Analysis State
  const [transcriptModalOpen, setTranscriptModalOpen] = useState(false);
  const [pastedTranscript, setPastedTranscript] = useState('');
  const [analyzingTranscript, setAnalyzingTranscript] = useState(false);
  const [transcriptResult, setTranscriptResult] = useState(null);
  const [lang, setLang] = useState('id');
  const [humanize, setHumanize] = useState(false);

  // Resume Data State
  const [resumeId, setResumeId] = useState(null);
  const [resumeSlug, setResumeSlug] = useState('');
  const [title, setTitle] = useState('Resume Profesional');
  const [template, setTemplate] = useState('modern_minimalist');
  const [fontFamily, setFontFamily] = useState('Inter');
  const [primaryColor, setPrimaryColor] = useState('#4f46e5');
  const [content, setContent] = useState(initialData || {});

  // UI States
  const [saving, setSaving] = useState(false);
  const [saveSuccessMessage, setSaveSuccessMessage] = useState('');
  const [atsScore, setAtsScore] = useState(78);
  const [atsAudit, setAtsAudit] = useState(null);
  const [auditing, setAuditing] = useState(false);

  // Subscription, Quota & Paywall Protection States
  const [userQuota, setUserQuota] = useState(null);
  const [loadingQuota, setLoadingQuota] = useState(true);
  const [upgradeModalOpen, setUpgradeModalOpen] = useState(false);
  const [upgradeNotice, setUpgradeNotice] = useState('');
  const [activatingPlan, setActivatingPlan] = useState(false);

  // Real-Time Voice Interview Copilot States
  const [realtimeCopilotOpen, setRealtimeCopilotOpen] = useState(false);
  const [isListeningLive, setIsListeningLive] = useState(false);
  const [liveTranscript, setLiveTranscript] = useState('');
  const [liveCheatSheet, setLiveCheatSheet] = useState(null);
  const [copilotLoading, setCopilotLoading] = useState(false);
  const [manualTranscriptInput, setManualTranscriptInput] = useState('');
  const [copilotTab, setCopilotTab] = useState('voice'); // 'voice' | 'manual'
  const copilotRecognitionRef = useRef(null);

  // AI Web Portfolio Generator States
  const [webPortfolioOpen, setWebPortfolioOpen] = useState(false);
  const [generatingPortfolio, setGeneratingPortfolio] = useState(false);
  const [portfolioData, setPortfolioData] = useState(null);
  const [portfolioTheme, setPortfolioTheme] = useState('dark');

  // Quick AI Helper Loading States
  const [aiLoading, setAiLoading] = useState({});
  const [brainstormModalOpen, setBrainstormModalOpen] = useState(false);
  const [brainstormSuggestions, setBrainstormSuggestions] = useState([]);

  // Job Hub & Kanban State
  const [jobs, setJobs] = useState([
    {
      id: 'job-1',
      company: 'Neriah Pro Enterprise',
      role: 'Lead Systems Architect',
      salary: 'Rp 35.000.000 / bln',
      status: 'interviewing',
      date: '2026-09-28',
      desc: 'Mencari Lead Systems Architect untuk memimpin arsitektur cloud terdistribusi dengan Laravel 13, React 19, dan Distributed Architecture.',
      notes: 'Wawancara user teknis dijadwalkan Jumat jam 14:00 WIB.'
    },
    {
      id: 'job-2',
      company: 'Fintech Nusantara Ltd',
      role: 'Senior Backend Engineer',
      salary: 'Rp 28.000.000 / bln',
      status: 'applied',
      date: '2026-09-25',
      desc: 'Pengembangan payment gateway Midtrans dan ledger keuangan terdesentralisasi dengan O(1) query pagination.',
      notes: 'Menunggu respon dari HRD talent acquisition.'
    },
    {
      id: 'job-3',
      company: 'Unicorn Tech Asia',
      role: 'Principal Cloud Engineer',
      salary: 'Rp 45.000.000 / bln',
      status: 'wishlist',
      date: '2026-09-29',
      desc: 'Memimpin tim platform SRE, infrastruktur Docker/Kubernetes berkapasitas jutaan pengguna.',
      notes: 'Perlu menyesuaikan CV dengan kata kunci Kubernetes & AWS.'
    }
  ]);
  const [showAddJobModal, setShowAddJobModal] = useState(false);
  const [newJob, setNewJob] = useState({
    company: '',
    role: '',
    salary: '',
    status: 'wishlist',
    desc: '',
    notes: ''
  });

  // Tailored Applied CV & Diff Modal
  const [diffModalOpen, setDiffModalOpen] = useState(false);
  const [diffData, setDiffData] = useState(null);
  const [tailoring, setTailoring] = useState(false);

  // Mock Interview State
  const [targetCompany, setTargetCompany] = useState('Neriah Pro Enterprise');
  const [jobTitle, setJobTitle] = useState('Lead Systems Architect');
  const [jobDesc, setJobDesc] = useState('');
  const [interviewQuestions, setInterviewQuestions] = useState([]);
  const [selectedQuestionIndex, setSelectedQuestionIndex] = useState(0);
  const [userAnswer, setUserAnswer] = useState('');
  const [isRecording, setIsRecording] = useState(false);
  const [generatingInterview, setGeneratingInterview] = useState(false);
  const [evaluatingAnswer, setEvaluatingAnswer] = useState(false);
  const [answerEvaluation, setAnswerEvaluation] = useState(null);

  // Outreach & LinkedIn State
  const [outreachSubTab, setOutreachSubTab] = useState('letter'); // 'letter' | 'linkedin'
  const [outreachType, setOutreachType] = useState('thank_you');
  const [outreachRecipient, setOutreachRecipient] = useState('');
  const [generatedLetter, setGeneratedLetter] = useState('');
  const [generatingOutreach, setGeneratingOutreach] = useState(false);
  const [copyNotification, setCopyNotification] = useState(false);
  const [linkedInContent, setLinkedInContent] = useState(null);
  const [generatingLinkedIn, setGeneratingLinkedIn] = useState(false);

  // Finance & Pricing Ledger State
  const [financeTransactions, setFinanceTransactions] = useState([
    {
      id: 'TRX-101',
      date: '2026-09-29',
      client_name: 'Budi Hartono (PT Solusi Cemerlang)',
      package: 'Executive VIP (Career AI + Coaching)',
      price: 300000,
      payment_method: 'Midtrans QRIS',
      status: 'paid'
    },
    {
      id: 'TRX-102',
      date: '2026-09-27',
      client_name: 'Siti Aminah, S.Kom',
      package: 'Full Stack (Siap Kerja + Cover Letter)',
      price: 150000,
      payment_method: 'Bank Transfer (BCA)',
      status: 'paid'
    },
    {
      id: 'TRX-103',
      date: '2026-09-26',
      client_name: 'Rian Pratama',
      package: 'Standard (ATS Optimized)',
      price: 75000,
      payment_method: 'GoPay / E-Wallet',
      status: 'paid'
    },
    {
      id: 'TRX-104',
      date: '2026-09-24',
      client_name: 'Farhan Maulana',
      package: 'Starter / Lite (Template)',
      price: 35000,
      payment_method: 'ShopeePay',
      status: 'pending'
    }
  ]);
  const [showAddTransactionModal, setShowAddTransactionModal] = useState(false);
  const [newTransaction, setNewTransaction] = useState({
    client_name: '',
    package: 'Standard (ATS Optimized)',
    price: 75000,
    payment_method: 'Midtrans QRIS',
    status: 'paid'
  });

  // Microsoft MarkItDown Upload & Scan State
  const [uploadModalOpen, setUploadModalOpen] = useState(false);
  const [uploading, setUploading] = useState(false);
  const [uploadError, setUploadError] = useState('');
  const [uploadSuccess, setUploadSuccess] = useState('');
  const [markitdownResult, setMarkitdownResult] = useState(null);
  const fileInputRef = useRef(null);

  // Speech Recognition Ref
  const recognitionRef = useRef(null);

  // Run initial ATS linting and fetch user quota on mount
  const fetchQuota = async () => {
    try {
      const res = await fetch('/api/cv-pro/quota');
      const data = await res.json();
      if (data.success) {
        setUserQuota(data.quota);
      }
    } catch (e) {
      console.error('Gagal mengambil kuota pengguna:', e);
    } finally {
      setLoadingQuota(false);
    }
  };

  useEffect(() => {
    runAtsAudit(content);
    fetchQuota();
  }, []);

  // Access check & Paywall protection for AI features
  const checkAiAccessOrShowUpgrade = (featureName = 'Fitur AI') => {
    if (currentUser?.is_super_admin) {
      return true;
    }

    const isPaid = (userQuota && !userQuota.is_guest && userQuota.tier_code !== 'free') || ((userQuota?.ai_credits_balance ?? 0) > 0);
    if (isPaid) {
      return true;
    }

    setUpgradeNotice(lang === 'id'
      ? `Fitur '${featureName}' memerlukan paket berlangganan atau kuota AI aktif. Pengunjung gratis tetap dapat mengisi data CV secara manual dan mengunduh PDF sepuasnya tanpa biaya.`
      : `The '${featureName}' feature requires an active Pro plan or AI credits. Free visitors can edit all CV data manually and export PDF with zero fees.`);
    setUpgradeModalOpen(true);
    return false;
  };

  // Real-Time Voice Interview Copilot Handlers
  const handleOpenRealtimeCopilot = () => {
    if (!checkAiAccessOrShowUpgrade(lang === 'id' ? 'Asisten Wawancara Real-time (Voice Copilot)' : 'Voice Interview Copilot')) return;
    setRealtimeCopilotOpen(true);
  };

  const startLiveCopilotListening = () => {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
      alert(lang === 'id' ? 'Browser ini belum mendukung Web Speech Recognition langsung. Silakan gunakan tab "Tempel Teks Pertanyaan".' : 'Browser does not support direct Web Speech Recognition. Please use the "Paste Question" tab.');
      return;
    }

    try {
      const rec = new SpeechRecognition();
      rec.continuous = true;
      rec.interimResults = true;
      rec.lang = lang === 'id' ? 'id-ID' : 'en-US';

      rec.onresult = (event) => {
        let interim = '';
        for (let i = event.resultIndex; i < event.results.length; i++) {
          interim += event.results[i][0].transcript;
        }
        setLiveTranscript(interim);
        if (interim.trim().split(' ').length >= 4) {
          fetchRealtimeCheatSheet(interim);
        }
      };

      rec.onerror = (e) => {
        console.warn('Voice copilot error:', e);
        setIsListeningLive(false);
      };

      rec.onend = () => {
        setIsListeningLive(false);
      };

      rec.start();
      copilotRecognitionRef.current = rec;
      setIsListeningLive(true);
    } catch (err) {
      console.error(err);
      setIsListeningLive(false);
    }
  };

  const stopLiveCopilotListening = () => {
    if (copilotRecognitionRef.current) {
      try {
        copilotRecognitionRef.current.stop();
      } catch (e) {}
      copilotRecognitionRef.current = null;
    }
    setIsListeningLive(false);
  };

  const fetchRealtimeCheatSheet = async (textToAnalyze) => {
    const query = textToAnalyze || liveTranscript || manualTranscriptInput;
    if (!query || !query.trim()) return;

    setCopilotLoading(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/realtime-copilot', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          spoken_text: query,
          resume_content: content,
          lang,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setLiveCheatSheet(data.data);
      }
    } catch (err) {
      console.error(err);
    } finally {
      setCopilotLoading(false);
    }
  };

  // AI Web Portfolio Generator Handlers
  const handleOpenWebPortfolio = () => {
    if (!checkAiAccessOrShowUpgrade(lang === 'id' ? 'AI Web Portfolio Generator' : 'AI Web Portfolio Generator')) return;
    setWebPortfolioOpen(true);
    if (!portfolioData) {
      generatePortfolio(portfolioTheme);
    }
  };

  const generatePortfolio = async (customTheme = portfolioTheme) => {
    setGeneratingPortfolio(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/portfolio/generate', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          resume_content: content,
          theme: customTheme,
          lang,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setPortfolioData(data.data);
      }
    } catch (err) {
      console.error(err);
    } finally {
      setGeneratingPortfolio(false);
    }
  };

  const downloadPortfolioHtml = () => {
    if (!portfolioData?.html) return;
    const blob = new Blob([portfolioData.html], { type: 'text/html;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `portfolio-${(content.personal_info?.name || 'web').toLowerCase().replace(/\s+/g, '-')}.html`;
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
  };

  const openPortfolioPreviewInTab = () => {
    if (!portfolioData?.html) return;
    const blob = new Blob([portfolioData.html], { type: 'text/html;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    window.open(url, '_blank');
  };

  // Plan Activation / Simulation (for user testing)
  const handleSimulatePlanActivation = async (planCode) => {
    setActivatingPlan(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/topup', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({ plan_code: planCode }),
      });
      const data = await res.json();
      if (data.success) {
        setUserQuota(data.quota);
        alert(lang === 'id' ? `✓ Paket '${planCode}' berhasil diaktifkan!` : `✓ Plan '${planCode}' successfully activated!`);
        setUpgradeModalOpen(false);
      } else {
        if (res.status === 401) {
          window.location.href = '/pricing';
        } else {
          alert('Gagal aktivasi: ' + (data.message || 'Error'));
        }
      }
    } catch (err) {
      console.error(err);
      alert('Terjadi kesalahan koneksi.');
    } finally {
      setActivatingPlan(false);
    }
  };

  // Sync speech recognition
  useEffect(() => {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (SpeechRecognition) {
      const recognition = new SpeechRecognition();
      recognition.continuous = true;
      recognition.interimResults = true;
      recognition.lang = lang === 'id' ? 'id-ID' : 'en-US';

      recognition.onresult = (event) => {
        let transcript = '';
        for (let i = event.resultIndex; i < event.results.length; i++) {
          transcript += event.results[i][0].transcript;
        }
        setUserAnswer((prev) => prev ? prev + ' ' + transcript : transcript);
      };

      recognition.onerror = () => {
        setIsRecording(false);
      };

      recognition.onend = () => {
        setIsRecording(false);
      };

      recognitionRef.current = recognition;
    }
  }, [lang]);

  const toggleRecording = () => {
    if (!recognitionRef.current) {
      alert(lang === 'id' ? 'Fitur Speech Recognition tidak didukung di browser ini. Anda dapat mengetik jawaban langsung.' : 'Speech Recognition is not supported on this browser. You can type your answer directly.');
      return;
    }

    if (isRecording) {
      recognitionRef.current.stop();
      setIsRecording(false);
    } else {
      try {
        recognitionRef.current.start();
        setIsRecording(true);
      } catch (err) {
        setIsRecording(false);
      }
    }
  };

  // Helper to update personal info
  const handlePersonalChange = (key, val) => {
    setContent((prev) => ({
      ...prev,
      personal_info: {
        ...(prev.personal_info || {}),
        [key]: val,
      },
    }));
  };

  // Run AI ATS Audit
  const runAtsAudit = async (customContent = null) => {
    setAuditing(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/lint', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          content: customContent || content,
          lang,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setAtsScore(data.data.overall_score);
        setAtsAudit(data.data);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setAuditing(false);
    }
  };

  // Save to Cloud
  const handleSaveToCloud = async () => {
    setSaving(true);
    setSaveSuccessMessage('');
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/save', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          id: resumeId,
          title,
          target_role: content.personal_info?.title || 'Profesional',
          template,
          font_family: fontFamily,
          primary_color: primaryColor,
          content,
          lang,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setResumeId(data.data.id);
        setResumeSlug(data.data.slug);
        setAtsScore(data.data.ats_score);
        setSaveSuccessMessage(data.message);
        setTimeout(() => setSaveSuccessMessage(''), 4000);
      } else {
        alert('Gagal menyimpan: ' + JSON.stringify(data.errors || data.message));
      }
    } catch (err) {
      alert('Terjadi kesalahan jaringan: ' + err.message);
    } finally {
      setSaving(false);
    }
  };

  // Quick AI Helper API call
  const triggerAiHelper = async (action, extraParams = {}) => {
    const featureLabels = {
      summary: 'Ringkasan Cerdas AI',
      enhance_bullet: 'AI Bullet Optimizer',
      condense_bullet: 'Condense & Shorten (Save Space)',
      brainstorm: 'Brainstorm Pencapaian AI',
      skills: 'Rekomendasi Keahlian AI'
    };
    if (!checkAiAccessOrShowUpgrade(featureLabels[action] || 'Asisten AI')) return;

    const key = `${action}_${extraParams.index ?? ''}`;
    setAiLoading((prev) => ({ ...prev, [key]: true }));

    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/ai-helper', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          action,
          lang,
          humanize,
          content,
          ...extraParams,
        }),
      });
      const data = await res.json();
      if (data.success) {
        if (action === 'summary') {
          handlePersonalChange('summary', data.data.summary);
        } else if (action === 'enhance_bullet' || action === 'condense_bullet') {
          const { expIndex, bulletIndex } = extraParams;
          const exps = [...(content.experiences || [])];
          if (exps[expIndex] && exps[expIndex].bullets) {
            exps[expIndex].bullets[bulletIndex] = data.data.bullet;
            setContent((prev) => ({ ...prev, experiences: exps }));
          }
        } else if (action === 'brainstorm') {
          setBrainstormSuggestions(data.data.achievements || []);
          setBrainstormModalOpen(true);
        } else if (action === 'skills') {
          const newSkills = data.data.skills || [];
          const current = content.skills || [];
          const merged = Array.from(new Set([...current, ...newSkills]));
          setContent((prev) => ({ ...prev, skills: merged }));
        }
      }
    } catch (e) {
      console.error(e);
    } finally {
      setAiLoading((prev) => ({ ...prev, [key]: false }));
    }
  };

  // Tailor CV to target Job Posting
  const handleTailorCv = async (targetJob) => {
    if (!checkAiAccessOrShowUpgrade('Tailor CV ke Lowongan')) return;

    setTailoring(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/tailor', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          job_title: targetJob.role,
          company: targetJob.company,
          job_description: targetJob.desc || '',
          resume_content: content,
          lang,
          humanize,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setDiffData(data.data);
        setDiffModalOpen(true);
      }
    } catch (e) {
      console.error(e);
      alert('Gagal menyesuaikan CV: ' + e.message);
    } finally {
      setTailoring(false);
    }
  };

  // Apply Tailored CV to Master CV State
  const applyTailoredDiff = () => {
    if (!diffData || !diffData.applied_content) return;
    setContent(diffData.applied_content);
    setDiffModalOpen(false);
    setActiveTab('editor');
    runAtsAudit(diffData.applied_content);
    alert(lang === 'id' ? '✓ CV Anda telah disesuaikan dan diperbarui ke Master Editor!' : '✓ Your CV has been successfully updated to Master Editor!');
  };

  // Generate LinkedIn Pack
  const handleGenerateLinkedIn = async () => {
    if (!checkAiAccessOrShowUpgrade('LinkedIn Optimization Suite')) return;

    setGeneratingLinkedIn(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/linkedin', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          resume_content: content,
          lang,
          humanize,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setLinkedInContent(data.data);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setGeneratingLinkedIn(false);
    }
  };

  // Generate Mock Interview Questions
  const handleGenerateInterview = async () => {
    if (!checkAiAccessOrShowUpgrade('AI Mock Interview Questions')) return;

    setGeneratingInterview(true);
    setAnswerEvaluation(null);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/interview/generate', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          resume_id: resumeId,
          job_title: jobTitle,
          company: targetCompany,
          job_description: jobDesc,
          resume_content: content,
          lang,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setInterviewQuestions(data.data.questions || []);
        setSelectedQuestionIndex(0);
        setUserAnswer('');
      }
    } catch (e) {
      console.error(e);
    } finally {
      setGeneratingInterview(false);
    }
  };

  // Evaluate Interview Answer
  const handleEvaluateAnswer = async () => {
    if (!checkAiAccessOrShowUpgrade('Evaluasi Jawaban STAR AI')) return;

    if (!interviewQuestions[selectedQuestionIndex] || !userAnswer.trim()) {
      alert(lang === 'id' ? 'Silakan isi atau rekam jawaban Anda terlebih dahulu!' : 'Please provide your answer first!');
      return;
    }
    setEvaluatingAnswer(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/interview/evaluate', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          question: interviewQuestions[selectedQuestionIndex].question,
          answer: userAnswer,
          lang,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setAnswerEvaluation(data.data);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setEvaluatingAnswer(false);
    }
  };

  // Generate Outreach Letter
  const handleGenerateOutreach = async () => {
    if (!checkAiAccessOrShowUpgrade('Surat Lamaran & Outreach AI')) return;
    setGeneratingOutreach(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/outreach/generate', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          resume_id: resumeId,
          type: outreachType,
          company: targetCompany,
          job_title: jobTitle,
          recipient: outreachRecipient,
          resume_content: content,
          lang,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setGeneratedLetter(data.data.content);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setGeneratingOutreach(false);
    }
  };

  const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
    setCopyNotification(true);
    setTimeout(() => setCopyNotification(false), 2000);
  };

  // Export CV as JSON
  const handleExportJson = () => {
    const dataStr = 'data:text/json;charset=utf-8,' + encodeURIComponent(JSON.stringify(content, null, 2));
    const downloadAnchor = document.createElement('a');
    downloadAnchor.setAttribute('href', dataStr);
    downloadAnchor.setAttribute('download', `cv-pro-backup-${Date.now()}.json`);
    document.body.appendChild(downloadAnchor);
    downloadAnchor.click();
    downloadAnchor.remove();
  };

  // Import CV from JSON
  const handleImportJson = (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (event) => {
      try {
        const parsed = JSON.parse(event.target.result);
        setContent(parsed);
        runAtsAudit(parsed);
        alert(lang === 'id' ? 'Data CV berhasil diimpor!' : 'CV data loaded successfully!');
      } catch (err) {
        alert('File JSON tidak valid: ' + err.message);
      }
    };
    reader.readAsText(file);
  };

  // Export Financial Ledger to CSV
  const handleExportFinanceCsv = () => {
    const headers = ['ID Transaksi', 'Tanggal', 'Nama Klien', 'Paket Layanan', 'Nominal IDR', 'Metode Pembayaran', 'Status'];
    const rows = financeTransactions.map(t => [
      t.id,
      t.date,
      `"${t.client_name.replace(/"/g, '""')}"`,
      `"${t.package.replace(/"/g, '""')}"`,
      t.price,
      t.payment_method,
      t.status
    ]);
    const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `laporan-keuangan-cv-pro-${Date.now()}.csv`);
    document.body.appendChild(link);
    link.click();
    link.remove();
  };

  // Kanban Stage Helpers
  const stages = [
    { key: 'wishlist', label: 'Wishlist / Target', color: 'border-zinc-300 dark:border-zinc-700' },
    { key: 'applied', label: 'Dilamar (Applied)', color: 'border-blue-500' },
    { key: 'interviewing', label: 'Wawancara (Interview)', color: 'border-amber-500' },
    { key: 'offered', label: 'Penawaran (Offer)', color: 'border-emerald-500' },
    { key: 'rejected', label: 'Ditolak (Rejected)', color: 'border-rose-500' }
  ];

  const moveJobStage = (jobId, direction) => {
    const order = ['wishlist', 'applied', 'interviewing', 'offered', 'rejected'];
    setJobs(prev => prev.map(j => {
      if (j.id !== jobId) return j;
      const curIdx = order.indexOf(j.status);
      const nextIdx = direction === 'next' ? Math.min(order.length - 1, curIdx + 1) : Math.max(0, curIdx - 1);
      return { ...j, status: order[nextIdx] };
    }));
  };

  const deleteJob = (jobId) => {
    if (confirm(lang === 'id' ? 'Hapus lamaran ini dari pelacak?' : 'Delete this application?')) {
      setJobs(prev => prev.filter(j => j.id !== jobId));
    }
  };

  // Microsoft MarkItDown Document & Scan Processor
  const handleFileUpload = async (e) => {
    const file = e.target.files?.[0];
    if (!file) return;

    setUploading(true);
    setUploadError('');
    setUploadSuccess('');
    setMarkitdownResult(null);

    const formData = new FormData();
    formData.append('cv_file', file);
    formData.append('lang', lang);

    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/upload-cv', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf || '',
        },
        body: formData,
      });

      const data = await res.json();
      if (data.success) {
        setMarkitdownResult(data.data);
        setUploadSuccess(data.message || 'File berhasil diproses dengan Microsoft MarkItDown!');
        
        // Auto-populate CV Studio with parsed data
        if (data.data.parsed_content) {
          setContent(data.data.parsed_content);
          if (data.data.parsed_content.personal_info?.full_name) {
            setTitle(`CV - ${data.data.parsed_content.personal_info.full_name}`);
          }
        }
        if (data.data.ats_audit) {
          setAtsScore(data.data.ats_audit.overall_score || 80);
          setAtsAudit(data.data.ats_audit);
        }
      } else {
        setUploadError(data.message || 'Gagal memproses berkas CV.');
      }
    } catch (err) {
      console.error(err);
      setUploadError('Terjadi kesalahan koneksi saat mengunggah berkas.');
    } finally {
      setUploading(false);
      if (fileInputRef.current) {
        fileInputRef.current.value = '';
      }
    }
  };

  // Add work experience item
  const addExperience = () => {
    const newItem = {
      id: Date.now(),
      company: 'Nama Perusahaan',
      role: 'Posisi / Role',
      period: '2023 - Sekarang',
      location: 'Kota / Remote',
      description: 'Deskripsi tanggung jawab singkat',
      bullets: ['Berhasil menyelesaikan proyek X dengan metodologi Y', 'Mengoptimalkan alur kerja hingga meningkat 20%'],
    };
    setContent((prev) => ({
      ...prev,
      experiences: [...(prev.experiences || []), newItem],
    }));
  };

  // Remove experience item
  const removeExperience = (id) => {
    setContent((prev) => ({
      ...prev,
      experiences: (prev.experiences || []).filter((item) => item.id !== id),
    }));
  };

  // Add skill
  const addSkill = (newSkill) => {
    if (!newSkill.trim()) return;
    setContent((prev) => ({
      ...prev,
      skills: [...(prev.skills || []), newSkill.trim()],
    }));
  };

  // Remove skill
  // Section Reordering & Management
  const sectionOrder = content.section_order || [
    'experiences',
    'education',
    'skills',
    'projects',
    'certifications',
    'references'
  ];

  const moveSection = (idx, direction) => {
    const newOrder = [...sectionOrder];
    const targetIdx = idx + direction;
    if (targetIdx < 0 || targetIdx >= newOrder.length) return;
    const temp = newOrder[idx];
    newOrder[idx] = newOrder[targetIdx];
    newOrder[targetIdx] = temp;
    setContent(prev => ({ ...prev, section_order: newOrder }));
  };

  // Education Handlers
  const addEducation = () => {
    const newEdu = {
      id: Date.now(),
      institution: 'Institut Teknologi Bandung (ITB)',
      degree: 'Sarjana Komputer (S.Kom)',
      field: 'Teknik Informatika',
      year: '2018 - 2022',
      gpa: '3.85 / 4.00'
    };
    setContent(prev => ({ ...prev, education: [...(prev.education || []), newEdu] }));
  };

  const removeEducation = (id) => {
    setContent(prev => ({
      ...prev,
      education: (prev.education || []).filter(item => item.id !== id)
    }));
  };

  // Project Handlers
  const addProject = () => {
    const newProj = {
      id: Date.now(),
      name: 'Project OS & PRD Platform',
      role: 'Principal Architect',
      description: 'Platform perancangan arsitektur dan sintesis spesifikasi sistem otomatis.',
      link: 'https://neriahpro.com/blueprint'
    };
    setContent(prev => ({ ...prev, projects: [...(prev.projects || []), newProj] }));
  };

  const removeProject = (id) => {
    setContent(prev => ({
      ...prev,
      projects: (prev.projects || []).filter(item => item.id !== id)
    }));
  };

  // Certification Handlers
  const addCertification = () => {
    const newCert = {
      id: Date.now(),
      name: 'AWS Solutions Architect - Associate',
      issuer: 'Amazon Web Services',
      year: '2024',
      link: ''
    };
    setContent(prev => ({ ...prev, certifications: [...(prev.certifications || []), newCert] }));
  };

  const removeCertification = (id) => {
    setContent(prev => ({
      ...prev,
      certifications: (prev.certifications || []).filter(item => item.id !== id)
    }));
  };

  // Reference Handlers
  const addReference = () => {
    const newRef = {
      id: Date.now(),
      name: 'Dr. Ir. Hendra Gunawan, M.T.',
      title: 'Chief Technology Officer (CTO)',
      company: 'Neriah Pro Enterprise',
      email: 'hendra.gunawan@neriahpro.com',
      phone: '+62 811-9876-5432',
      note: 'Supervisi langsung selama 3 tahun dalam pengembangan sistem enterprise.'
    };
    setContent(prev => ({ ...prev, references: [...(prev.references || []), newRef] }));
  };

  const removeReference = (id) => {
    setContent(prev => ({
      ...prev,
      references: (prev.references || []).filter(item => item.id !== id)
    }));
  };

  // Photo Cropper Handlers
  const handlePhotoUpload = (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (event) => {
      setTempImageSrc(event.target.result);
      setPhotoZoom(1);
      setCropModalOpen(true);
    };
    reader.readAsDataURL(file);
  };

  const applyCropPhoto = () => {
    if (!tempImageSrc) return;
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    img.onload = () => {
      canvas.width = 300;
      canvas.height = 300;
      ctx.clearRect(0, 0, 300, 300);

      const minDim = Math.min(img.width, img.height);
      const sWidth = minDim / (photoZoom || 1);
      const sHeight = minDim / (photoZoom || 1);
      const sx = (img.width - sWidth) / 2;
      const sy = (img.height - sHeight) / 2;

      ctx.drawImage(img, sx, sy, sWidth, sHeight, 0, 0, 300, 300);
      const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.9);

      handlePersonalChange('photo_url', croppedDataUrl);
      setShowPhoto(true);
      setCropModalOpen(false);
    };
    img.src = tempImageSrc;
  };

  // Sosmed Promo Handlers
  const handleGenerateSosmed = async () => {
    if (!checkAiAccessOrShowUpgrade(lang === 'id' ? 'Generator Promo Sosmed' : 'Social Media Promo Generator')) return;
    setGeneratingSosmed(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/sosmed/generate', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          content,
          topic: sosmedTopic,
          custom_prompt: sosmedCustomPrompt,
          lang,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setSosmedData(data.data);
      }
    } catch (e) {
      console.error(e);
      alert('Gagal menghasilkan promo sosmed.');
    } finally {
      setGeneratingSosmed(false);
    }
  };

  const downloadSosmedCanvasPng = () => {
    const canvas = document.getElementById('sosmed-preview-canvas');
    if (!canvas) return;
    const url = canvas.toDataURL('image/png');
    const a = document.createElement('a');
    a.href = url;
    a.download = `Promo-${(content?.personal_info?.name || 'CV').replace(/\s+/g, '-')}.png`;
    a.click();
  };

  // Transcript Analysis Handlers
  const handleRunTranscriptAnalysis = async () => {
    if (!checkAiAccessOrShowUpgrade(lang === 'id' ? 'Analisis Transkrip Wawancara' : 'Interview Transcript Analysis')) return;
    if (!pastedTranscript.trim()) {
      alert(lang === 'id' ? 'Silakan tempel teks transkrip wawancara terlebih dahulu.' : 'Please paste transcript text first.');
      return;
    }
    setAnalyzingTranscript(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/transcript/analyze', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          transcript: pastedTranscript,
          content,
          lang,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setTranscriptResult(data.data);
      } else {
        alert(data.message || 'Gagal menganalisis transkrip.');
      }
    } catch (e) {
      console.error(e);
      alert('Terjadi kesalahan saat memproses transkrip.');
    } finally {
      setAnalyzingTranscript(false);
    }
  };

  const removeSkill = (index) => {
    setContent((prev) => ({
      ...prev,
      skills: (prev.skills || []).filter((_, i) => i !== index),
    }));
  };

  // Finance KPI calculations
  const totalOmset = financeTransactions.filter(t => t.status === 'paid').reduce((acc, t) => acc + t.price, 0);
  const totalPaidCount = financeTransactions.filter(t => t.status === 'paid').length;
  const aov = totalPaidCount > 0 ? Math.round(totalOmset / totalPaidCount) : 0;

  const p = content.personal_info || {};

  return (
    <div className="min-h-screen bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans pb-16 transition-colors">
      
      {/* 1. TOP STUDIO TOOLBAR */}
      <div className="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 sticky top-14 z-40 px-4 py-2.5 shadow-sm transition-colors">
        <div className="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3">
          
          {/* Left: Branding & Tabs */}
          <div className="flex items-center gap-2 sm:gap-4">
            <div className="flex items-center gap-2 font-mono text-xs font-black uppercase">
              <span className="w-5 h-5 bg-indigo-600 text-white flex items-center justify-center font-bold text-[11px]">CV</span>
              <span className="hidden sm:inline">CV PRO // STUDIO</span>
            </div>

            <div className="h-4 w-px bg-zinc-300 dark:bg-zinc-700 hidden sm:block"></div>

            {/* Navigation Tabs */}
            <div className="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-800 p-0.5 border border-zinc-200 dark:border-zinc-700 text-xs font-medium">
              <button
                onClick={() => setActiveTab('editor')}
                className={`px-3 py-1 flex items-center gap-1.5 transition ${activeTab === 'editor' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'}`}
              >
                <Layout className="w-3.5 h-3.5" />
                <span>Editor & Preview</span>
              </button>

              <button
                onClick={() => setActiveTab('job_hub')}
                className={`px-3 py-1 flex items-center gap-1.5 transition ${activeTab === 'job_hub' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'}`}
              >
                <Briefcase className="w-3.5 h-3.5" />
                <span>Job Hub & Pelacak</span>
                <span className="px-1.5 py-0.2 text-[10px] font-mono bg-zinc-200 dark:bg-zinc-700 rounded-full">
                  {jobs.length}
                </span>
              </button>

              <button
                onClick={() => setActiveTab('finance')}
                className={`px-3 py-1 flex items-center gap-1.5 transition ${activeTab === 'finance' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'}`}
              >
                <DollarSign className="w-3.5 h-3.5" />
                <span>Keuangan Pro</span>
              </button>

              <button
                onClick={() => { setActiveTab('ats_audit'); runAtsAudit(); }}
                className={`px-3 py-1 flex items-center gap-1.5 transition ${activeTab === 'ats_audit' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'}`}
              >
                <Sparkles className="w-3.5 h-3.5" />
                <span>AI ATS Audit</span>
                <span className={`px-1.5 py-0.2 text-[10px] font-mono font-bold ${atsScore >= 80 ? 'bg-emerald-500 text-black' : (atsScore >= 60 ? 'bg-amber-500 text-black' : 'bg-rose-500 text-white')}`}>
                  {atsScore}%
                </span>
              </button>

              <button
                onClick={() => setActiveTab('mock_interview')}
                className={`px-3 py-1 flex items-center gap-1.5 transition ${activeTab === 'mock_interview' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'}`}
              >
                <Mic className="w-3.5 h-3.5" />
                <span>Mock Interview</span>
              </button>

              <button
                onClick={() => setActiveTab('outreach')}
                className={`px-3 py-1 flex items-center gap-1.5 transition ${activeTab === 'outreach' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'}`}
              >
                <Mail className="w-3.5 h-3.5" />
                <span>Outreach Suite</span>
              </button>
            </div>
          </div>

          {/* Right: Actions */}
          <div className="flex items-center gap-2">
            {saveSuccessMessage && (
              <span className="text-xs text-emerald-600 dark:text-emerald-400 font-medium animate-pulse flex items-center gap-1">
                <Check className="w-3.5 h-3.5 inline" /> {saveSuccessMessage}
              </span>
            )}

            {/* Tour Guide Button */}
            <button
              onClick={() => { setTourStep(0); setTourModalOpen(true); }}
              id="start-tour-btn"
              className="px-2.5 py-1.5 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 text-indigo-600 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 text-xs font-bold transition flex items-center gap-1.5 shadow-sm rounded-sm"
              title="Mulai panduan interaktif seluruh fitur CV Pro"
            >
              <HelpCircle className="w-3.5 h-3.5" />
              <span className="hidden sm:inline">Panduan Tur</span>
            </button>

            {/* Transcript Paste Button */}
            <button
              onClick={() => setTranscriptModalOpen(true)}
              id="open-transcript-modal-btn"
              className="px-2.5 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 text-xs font-bold transition flex items-center gap-1.5 shadow-sm rounded-sm"
              title="Tempel transkrip wawancara untuk evaluasi AI STAR"
            >
              <FileText className="w-3.5 h-3.5 text-blue-500" />
              <span className="hidden md:inline">Evaluasi Transkrip</span>
            </button>

            {/* Plan Tier & AI Quota Pill */}
            <div className="hidden lg:flex items-center gap-1.5 px-2.5 py-1 bg-gradient-to-r from-amber-500/10 via-purple-500/10 to-indigo-500/10 border border-amber-500/30 rounded-full text-xs">
              <span className="font-mono text-[10px] font-bold text-amber-600 dark:text-amber-400">
                {currentUser?.is_super_admin ? 'SUPERADMIN' : (userQuota?.tier_code ? userQuota.tier_code.toUpperCase() : 'STARTER FREE')}
              </span>
              <span className="text-zinc-400">•</span>
              <span className="font-mono text-[10px] text-zinc-600 dark:text-zinc-300 flex items-center gap-0.5" title="Kredit AI Tersisa">
                <Zap className="w-3 h-3 text-amber-500" />
                {currentUser?.is_super_admin ? '∞' : (userQuota?.ai_credits_balance ?? 0)}
              </span>
            </div>

            {/* Upgrade & Top-up Button */}
            <button
              onClick={() => setUpgradeModalOpen(true)}
              className="px-2.5 py-1.5 bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-400 hover:to-indigo-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm rounded-sm"
              title="Beli Kuota AI atau Upgrade Paket Pro"
            >
              <Crown className="w-3.5 h-3.5" />
              <span className="hidden md:inline">Upgrade / Kuota AI</span>
              <span className="md:hidden">Upgrade</span>
            </button>

            {/* Real-Time Voice Copilot Tool Shortcut */}
            <button
              onClick={handleOpenRealtimeCopilot}
              className="px-2.5 py-1.5 bg-zinc-900 dark:bg-zinc-800 hover:bg-zinc-800 text-zinc-100 border border-purple-500/40 text-xs font-bold transition flex items-center gap-1.5 shadow-sm"
              title="Buka Asisten Wawancara Real-Time (Voice Copilot)"
            >
              <Headphones className="w-3.5 h-3.5 text-purple-400" />
              <span className="hidden xl:inline">Voice Copilot</span>
            </button>

            {/* Web Portfolio Tool Shortcut */}
            <button
              onClick={handleOpenWebPortfolio}
              className="px-2.5 py-1.5 bg-zinc-900 dark:bg-zinc-800 hover:bg-zinc-800 text-zinc-100 border border-indigo-500/40 text-xs font-bold transition flex items-center gap-1.5 shadow-sm"
              title="Buat Portofolio Web AI dari CV Anda"
            >
              <Globe className="w-3.5 h-3.5 text-indigo-400" />
              <span className="hidden xl:inline">Web Portfolio</span>
            </button>

            <button
              onClick={() => setUploadModalOpen(true)}
              className="px-3 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-zinc-100 border border-zinc-700 text-xs font-bold transition flex items-center gap-1.5 shadow-sm"
              title="Unggah berkas CV atau scan fisik via Microsoft MarkItDown"
            >
              <UploadCloud className="w-3.5 h-3.5 text-indigo-400" />
              <span className="hidden sm:inline">Scan / Upload CV</span>
              <span className="sm:hidden">Upload</span>
            </button>

            <button
              onClick={handleSaveToCloud}
              disabled={saving}
              className="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm disabled:opacity-50"
            >
              <Save className="w-3.5 h-3.5" />
              <span>{saving ? 'Menyimpan...' : 'Simpan Cloud'}</span>
            </button>

            {resumeSlug && (
              <a
                href={`/cv/${resumeSlug}`}
                target="_blank"
                rel="noreferrer"
                className="px-2.5 py-1.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-xs font-mono font-bold transition flex items-center gap-1"
                title="Buka Halaman Publik"
              >
                <Eye className="w-3.5 h-3.5" />
                <span className="hidden sm:inline">Preview Link</span>
              </a>
            )}

            <button
              onClick={() => window.print()}
              className="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm"
              title="Download PDF via print dialog"
            >
              <Download className="w-3.5 h-3.5" />
              <span>Cetak / PDF</span>
            </button>
          </div>
        </div>
      </div>

      {/* MARKITDOWN UPLOAD & SCAN MODAL */}
      {uploadModalOpen && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl flex flex-col">
            <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
              <div className="flex items-center gap-2">
                <div className="w-8 h-8 rounded bg-indigo-600/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                  <Scan className="w-4 h-4" />
                </div>
                <div>
                  <h3 className="text-sm font-bold text-zinc-900 dark:text-white">
                    Microsoft MarkItDown CV Scan & Upload Engine
                  </h3>
                  <p className="text-[11px] text-zinc-500">
                    Konversi dokumen PDF, DOCX, scan fisik (PNG/JPG), dan teks menjadi format Markdown terstruktur.
                  </p>
                </div>
              </div>
              <button 
                onClick={() => setUploadModalOpen(false)}
                className="text-zinc-400 hover:text-zinc-600 dark:hover:text-white"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <div className="p-6 space-y-4">
              <div 
                onClick={() => fileInputRef.current?.click()}
                className="border-2 border-dashed border-zinc-300 dark:border-zinc-700 hover:border-indigo-500 dark:hover:border-indigo-400 rounded-lg p-8 text-center cursor-pointer transition bg-zinc-50 dark:bg-zinc-950/50 flex flex-col items-center justify-center gap-3"
              >
                <div className="w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-inner">
                  {uploading ? (
                    <div className="w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin" />
                  ) : (
                    <FileUp className="w-6 h-6" />
                  )}
                </div>

                <div>
                  <p className="text-xs font-bold text-zinc-800 dark:text-zinc-200">
                    {uploading ? 'Sedang Memindai dengan MarkItDown...' : 'Pilih atau Tarik Berkas CV ke Sini'}
                  </p>
                  <p className="text-[11px] text-zinc-500 mt-1">
                    Mendukung PDF, Word (DOCX), Scan Fisik (PNG, JPG, WEBP), TXT, Markdown, CSV (Maks 20MB)
                  </p>
                </div>

                <span className="inline-block px-3 py-1 bg-zinc-200 dark:bg-zinc-800 text-[10px] font-mono text-zinc-600 dark:text-zinc-400 rounded">
                  Pipeline: MarkItDown → Structured AST → Auto-Fill Studio
                </span>
              </div>

              <input 
                type="file" 
                ref={fileInputRef} 
                onChange={handleFileUpload} 
                accept=".pdf,.docx,.doc,.txt,.md,.png,.jpg,.jpeg,.webp,.csv" 
                className="hidden" 
              />

              {uploadError && (
                <div className="p-3 bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 text-xs flex items-center gap-2">
                  <AlertTriangle className="w-4 h-4 shrink-0" />
                  <span>{uploadError}</span>
                </div>
              )}

              {uploadSuccess && (
                <div className="p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-xs flex items-center gap-2">
                  <CheckCircle className="w-4 h-4 shrink-0" />
                  <span>{uploadSuccess}</span>
                </div>
              )}

              {markitdownResult && (
                <div className="mt-4 border border-zinc-200 dark:border-zinc-800 rounded bg-zinc-50 dark:bg-zinc-950 p-4">
                  <div className="flex items-center justify-between pb-2 mb-2 border-b border-zinc-200 dark:border-zinc-800">
                    <span className="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5">
                      <Code className="w-3.5 h-3.5" />
                      MarkItDown Engine Output ({markitdownResult.engine})
                    </span>
                    <span className="text-[10px] font-mono px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded uppercase">
                      {markitdownResult.format}
                    </span>
                  </div>

                  <pre className="text-[11px] font-mono text-zinc-700 dark:text-zinc-300 max-h-48 overflow-y-auto whitespace-pre-wrap select-all bg-white dark:bg-zinc-900 p-2.5 rounded border border-zinc-200 dark:border-zinc-800">
                    {markitdownResult.markdown}
                  </pre>

                  <div className="mt-3 flex items-center justify-between">
                    <p className="text-[11px] text-emerald-600 dark:text-emerald-400">
                      ✓ Formulir CV Studio & Skor ATS telah otomatis diperbarui.
                    </p>
                    <button
                      onClick={() => setUploadModalOpen(false)}
                      className="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded"
                    >
                      Buka di Studio
                    </button>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      )}

      {/* DIFF MODAL (APPLIED CV vs MASTER CV) */}
      {diffModalOpen && diffData && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-3xl max-h-[90vh] overflow-y-auto shadow-2xl flex flex-col">
            <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
              <div className="flex items-center gap-2">
                <div className="w-8 h-8 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                  <Sparkles className="w-4 h-4" />
                </div>
                <div>
                  <h3 className="text-sm font-bold text-zinc-900 dark:text-white">
                    Pratinjau Penyesuaian CV: {diffData.job_title} @ {diffData.company}
                  </h3>
                  <p className="text-[11px] text-zinc-500">
                    Kesesuaian Skor: <strong className="text-emerald-600">{diffData.match_score}%</strong> • Kata Kunci Ditambahkan: {diffData.suggested_keywords?.join(', ')}
                  </p>
                </div>
              </div>
              <button 
                onClick={() => setDiffModalOpen(false)}
                className="text-zinc-400 hover:text-zinc-600 dark:hover:text-white"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <div className="p-6 space-y-4 text-xs">
              <div className="p-3 bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 text-indigo-900 dark:text-indigo-200">
                <p>
                  <strong>Rekomendasi AI:</strong> Ringkasan dan kata kunci teknis telah dioptimalkan agar lolos filter ATS rekruter perusahaan target dengan bobot relevansi tinggi.
                </p>
              </div>

              {/* Diff Box */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div className="border border-zinc-200 dark:border-zinc-800 p-3 bg-zinc-50 dark:bg-zinc-950">
                  <span className="text-[10px] font-mono font-bold uppercase text-zinc-400 block mb-1">Sebelumnya (Master CV)</span>
                  <p className="text-zinc-600 dark:text-zinc-400 leading-relaxed italic">
                    "{diffData.diff_preview?.original_summary || 'Belum ada ringkasan'}"
                  </p>
                </div>
                <div className="border border-emerald-500/40 p-3 bg-emerald-50/50 dark:bg-emerald-950/20">
                  <span className="text-[10px] font-mono font-bold uppercase text-emerald-600 block mb-1">Disesuaikan untuk Lowongan (Applied CV)</span>
                  <p className="text-zinc-800 dark:text-zinc-200 leading-relaxed font-medium">
                    "{diffData.tailored_summary}"
                  </p>
                </div>
              </div>

              {/* Added Skills Tags */}
              <div>
                <span className="text-[11px] font-bold block mb-1.5">Kata Kunci & Keahlian Tambahan:</span>
                <div className="flex flex-wrap gap-1.5">
                  {(diffData.suggested_keywords || []).map((kw, i) => (
                    <span key={i} className="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 text-[11px] font-mono font-bold rounded">
                      + {kw}
                    </span>
                  ))}
                </div>
              </div>

              <div className="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-2">
                <button
                  onClick={() => setDiffModalOpen(false)}
                  className="px-4 py-2 border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                >
                  Tutup
                </button>
                <button
                  onClick={applyTailoredDiff}
                  className="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow"
                >
                  <Check className="w-3.5 h-3.5" />
                  <span>Terapkan Perubahan ke Master CV</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* BRAINSTORM ACHIEVEMENTS MODAL */}
      {brainstormModalOpen && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl flex flex-col">
            <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
              <div className="flex items-center gap-2">
                <Sparkles className="w-4 h-4 text-indigo-500" />
                <h3 className="text-sm font-bold text-zinc-900 dark:text-white">
                  Inspirasi Pencapaian & Metrik Terukur (AI Brainstorm)
                </h3>
              </div>
              <button onClick={() => setBrainstormModalOpen(false)} className="text-zinc-400 hover:text-white">
                <X className="w-4 h-4" />
              </button>
            </div>
            <div className="p-5 space-y-3 text-xs">
              <p className="text-zinc-500">
                Pilih atau salin poin pencapaian berorientasi metrik di bawah ini untuk disisipkan ke pengalaman kerja Anda:
              </p>
              <div className="space-y-2">
                {brainstormSuggestions.map((sug, idx) => (
                  <div key={idx} className="p-3 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-start justify-between gap-3">
                    <p className="text-zinc-800 dark:text-zinc-200 leading-relaxed font-mono text-[11px]">{sug}</p>
                    <button
                      onClick={() => { copyToClipboard(sug); }}
                      className="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-bold shrink-0 transition rounded"
                    >
                      Salin
                    </button>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ADD JOB MODAL */}
      {showAddJobModal && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-md shadow-2xl flex flex-col">
            <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
              <h3 className="text-sm font-bold text-zinc-900 dark:text-white">Tambah Pelacak Lamaran Baru</h3>
              <button onClick={() => setShowAddJobModal(false)} className="text-zinc-400 hover:text-white">
                <X className="w-4 h-4" />
              </button>
            </div>
            <div className="p-5 space-y-3 text-xs">
              <div>
                <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Nama Perusahaan</label>
                <input
                  type="text"
                  value={newJob.company}
                  onChange={(e) => setNewJob({ ...newJob, company: e.target.value })}
                  placeholder="e.g. Tokopedia / GoTo"
                  className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs"
                />
              </div>
              <div>
                <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Posisi yang Dilamar</label>
                <input
                  type="text"
                  value={newJob.role}
                  onChange={(e) => setNewJob({ ...newJob, role: e.target.value })}
                  placeholder="e.g. Lead Systems Architect"
                  className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs"
                />
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Ekspektasi Gaji</label>
                  <input
                    type="text"
                    value={newJob.salary}
                    onChange={(e) => setNewJob({ ...newJob, salary: e.target.value })}
                    placeholder="e.g. Rp 30.000.000"
                    className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs"
                  />
                </div>
                <div>
                  <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Status Awal</label>
                  <select
                    value={newJob.status}
                    onChange={(e) => setNewJob({ ...newJob, status: e.target.value })}
                    className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2 py-1.5 text-xs"
                  >
                    <option value="wishlist">Wishlist / Target</option>
                    <option value="applied">Dilamar (Applied)</option>
                    <option value="interviewing">Wawancara (Interview)</option>
                    <option value="offered">Penawaran (Offer)</option>
                  </select>
                </div>
              </div>
              <div>
                <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Deskripsi Pekerjaan / Kriteria (Opsional)</label>
                <textarea
                  rows={3}
                  value={newJob.desc}
                  onChange={(e) => setNewJob({ ...newJob, desc: e.target.value })}
                  placeholder="Tempel syarat atau kriteria pekerjaan di sini..."
                  className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2 text-xs"
                />
              </div>
              <div className="pt-2 flex justify-end gap-2">
                <button
                  onClick={() => setShowAddJobModal(false)}
                  className="px-3 py-1.5 border border-zinc-300 text-xs font-bold"
                >
                  Batal
                </button>
                <button
                  onClick={() => {
                    if (!newJob.company || !newJob.role) {
                      alert('Isi nama perusahaan dan posisi!');
                      return;
                    }
                    const created = {
                      id: `job-${Date.now()}`,
                      ...newJob,
                      date: new Date().toISOString().split('T')[0]
                    };
                    setJobs(prev => [created, ...prev]);
                    setShowAddJobModal(false);
                    setNewJob({ company: '', role: '', salary: '', status: 'wishlist', desc: '', notes: '' });
                  }}
                  className="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold"
                >
                  Simpan Lamaran
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ADD TRANSACTION MODAL */}
      {showAddTransactionModal && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-md shadow-2xl flex flex-col">
            <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
              <h3 className="text-sm font-bold text-zinc-900 dark:text-white">Tambah Catatan Transaksi Layanan</h3>
              <button onClick={() => setShowAddTransactionModal(false)} className="text-zinc-400 hover:text-white">
                <X className="w-4 h-4" />
              </button>
            </div>
            <div className="p-5 space-y-3 text-xs">
              <div>
                <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Nama Klien</label>
                <input
                  type="text"
                  value={newTransaction.client_name}
                  onChange={(e) => setNewTransaction({ ...newTransaction, client_name: e.target.value })}
                  placeholder="e.g. Ahmad Fauzi, S.T."
                  className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs"
                />
              </div>
              <div>
                <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Paket Layanan</label>
                <select
                  value={newTransaction.package}
                  onChange={(e) => {
                    const pkg = e.target.value;
                    let price = 75000;
                    if (pkg.includes('Lite')) price = 35000;
                    if (pkg.includes('Full Stack')) price = 150000;
                    if (pkg.includes('VIP')) price = 300000;
                    setNewTransaction({ ...newTransaction, package: pkg, price });
                  }}
                  className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2 py-1.5 text-xs"
                >
                  <option value="Starter / Lite (Template)">1. Starter / Lite (Template) - Rp 35.000</option>
                  <option value="Standard (ATS Optimized)">2. Standard (ATS Optimized) - Rp 75.000</option>
                  <option value="Full Stack (Siap Kerja + Cover Letter)">3. Full Stack (Siap Kerja) - Rp 150.000</option>
                  <option value="Executive VIP (Career AI + Coaching)">4. Executive VIP (Career AI) - Rp 300.000</option>
                </select>
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Nominal (IDR)</label>
                  <input
                    type="number"
                    value={newTransaction.price}
                    onChange={(e) => setNewTransaction({ ...newTransaction, price: Number(e.target.value) })}
                    className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs"
                  />
                </div>
                <div>
                  <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Status</label>
                  <select
                    value={newTransaction.status}
                    onChange={(e) => setNewTransaction({ ...newTransaction, status: e.target.value })}
                    className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2 py-1.5 text-xs"
                  >
                    <option value="paid">Lunas (Paid)</option>
                    <option value="pending">Menunggu (Pending)</option>
                  </select>
                </div>
              </div>
              <div className="pt-2 flex justify-end gap-2">
                <button
                  onClick={() => setShowAddTransactionModal(false)}
                  className="px-3 py-1.5 border border-zinc-300 text-xs font-bold"
                >
                  Batal
                </button>
                <button
                  onClick={() => {
                    if (!newTransaction.client_name) {
                      alert('Isi nama klien!');
                      return;
                    }
                    const created = {
                      id: `TRX-${Date.now().toString().slice(-4)}`,
                      date: new Date().toISOString().split('T')[0],
                      ...newTransaction
                    };
                    setFinanceTransactions(prev => [created, ...prev]);
                    setShowAddTransactionModal(false);
                    setNewTransaction({ client_name: '', package: 'Standard (ATS Optimized)', price: 75000, payment_method: 'Midtrans QRIS', status: 'paid' });
                  }}
                  className="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold"
                >
                  Simpan Transaksi
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* 2. MAIN CONTAINER WITH TAB PANELS */}
      <div className="max-w-7xl mx-auto px-4 mt-6">

        {/* ========================================================================= */}
        {/* TAB 1: EDITOR & LIVE PREVIEW CANVAS                                       */}
        {/* ========================================================================= */}
                {activeTab === 'editor' && (
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {/* Left Column: Form Editor (5 cols) */}
            <div className="lg:col-span-5 space-y-6">
              
                            {/* Template & Styling Control Card */}
              <div className="border border-zinc-200 dark:border-zinc-800 shadow-2xs overflow-hidden">
                {renderAccordionHeader('style', 'Gaya & Format Visual (12 Preset Global)', <Palette className="w-4 h-4 text-indigo-500" />, 'AESTHETICS', (
                  <span className="text-[10px] font-mono text-indigo-600 dark:text-indigo-400 font-bold uppercase truncate max-w-[140px]">
                    {template.replace(/_/g, ' ')}
                  </span>
                ))}
                {accordionState.style && (
                  <div className="bg-white dark:bg-zinc-900 p-4 border-t border-zinc-200 dark:border-zinc-800 space-y-3">

                <h3 className="text-xs font-mono font-bold uppercase text-zinc-500 dark:text-zinc-400 mb-3 flex items-center justify-between">
                  <span className="flex items-center gap-2">
                    <Palette className="w-3.5 h-3.5 text-indigo-500" />
                    <span>Gaya & Format Visual (5 Preset Standar)</span>
                  </span>
                  <div className="flex items-center gap-1.5">
                    <button
                      onClick={handleOpenWebPortfolio}
                      className="px-2 py-0.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-[10px] font-mono text-white rounded font-bold flex items-center gap-1 shadow-sm"
                      title="Konversi CV ini menjadi Portofolio Web Interaktif (AI)"
                    >
                      <Globe className="w-3 h-3" />
                      <span>Web Portfolio</span>
                    </button>
                    <button
                      onClick={handleExportJson}
                      className="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-[10px] font-mono hover:bg-zinc-200 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded"
                      title="Simpan backup CV format JSON"
                    >
                      Export JSON
                    </button>
                    <label
                      className="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-[10px] font-mono hover:bg-zinc-200 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded cursor-pointer"
                      title="Pulihkan backup CV format JSON"
                    >
                      Import JSON
                      <input type="file" accept=".json" onChange={handleImportJson} className="hidden" />
                    </label>
                  </div>
                </h3>
                <div className="grid grid-cols-3 gap-3 text-xs">
                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Template</label>
                    <select
                      value={template}
                      onChange={(e) => setTemplate(e.target.value)}
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    >
                      <option value="modern_minimalist">1. Modern Minimalist</option>
                      <option value="executive_clean">2. Executive Clean</option>
                      <option value="creative_ats">3. Creative ATS (Sidebar)</option>
                      <option value="tech_dark">4. Tech Dark (Terminal)</option>
                      <option value="compact_elegant">5. Compact Elegant (1 Hal)</option>
                    </select>
                  </div>
                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Font Family</label>
                    <select
                      value={fontFamily}
                      onChange={(e) => setFontFamily(e.target.value)}
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    >
                      <option value="Inter">Inter (Modern Sans)</option>
                      <option value="Roboto">Roboto (Clean)</option>
                      <option value="Lato">Lato (Balanced)</option>
                      <option value="Merriweather">Merriweather (Classic Serif)</option>
                      <option value="Georgia">Georgia (Formal)</option>
                    </select>
                  </div>
                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Aksen Warna</label>
                    <div className="flex items-center gap-1.5 pt-1">
                      {['#4f46e5', '#059669', '#e11d48', '#d97706', '#475569', '#7c3aed'].map((c) => (
                        <button
                          key={c}
                          type="button"
                          onClick={() => setPrimaryColor(c)}
                          className={`w-5 h-5 rounded-full border-2 transition ${primaryColor === c ? 'scale-125 border-zinc-950 dark:border-white shadow-sm' : 'border-transparent'}`}
                          style={{ backgroundColor: c }}
                        />
                      ))}
                    </div>
                  </div>
                </div>
              
                  </div>
                )}
              </div>

              {/* Card 1.5: Web Portfolio Studio Customizer Card (10 Granular Controls) */}
              <div className="border border-indigo-200 dark:border-indigo-900/60 shadow-2xs overflow-hidden">
                {renderAccordionHeader('web_portfolio', 'Studio Web Portfolio (10 Kontrol Desain)', <Globe className="w-4 h-4 text-purple-500" />, 'LIVE WEB', (
                  <span className="text-[10px] font-mono text-purple-600 dark:text-purple-400 font-bold uppercase">
                    {wpLayout.replace('_', ' ')}
                  </span>
                ))}
                {accordionState.web_portfolio && (
                  <div className="p-4 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 space-y-4 text-xs">
                    
                    {/* Control 10: Smart AI Job Category Matcher */}
                    <div className="p-3 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-950/40 dark:to-purple-950/40 border border-indigo-200 dark:border-indigo-800/80 rounded space-y-2">
                      <div className="flex items-center justify-between">
                        <span className="font-bold text-indigo-900 dark:text-indigo-200 flex items-center gap-1.5 text-[11px] font-mono uppercase">
                          <Sparkles className="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" />
                          <span>10. Sistem Cerdas Penentu Variasi Loker (AI Matcher)</span>
                        </span>
                        <span className="text-[9px] font-mono px-1.5 py-0.5 bg-indigo-200 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 font-bold rounded">
                          SMART PRESET
                        </span>
                      </div>
                      <div className="flex gap-2">
                        <input
                          type="text"
                          value={targetJobRole}
                          onChange={(e) => setTargetJobRole(e.target.value)}
                          placeholder="Ketik posisi loker tujuan (e.g. Senior Backend Engineer, UI/UX Designer, CFO)..."
                          className="flex-1 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2.5 py-1.5 text-xs text-zinc-900 dark:text-zinc-100 rounded"
                        />
                        <button
                          type="button"
                          onClick={handleSmartJobMatch}
                          className="px-3 py-1.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded text-xs transition shadow-sm whitespace-nowrap"
                        >
                          Cari Preset Terbaik
                        </button>
                      </div>
                      {jobMatchReason && (
                        <div className="p-2 bg-white/80 dark:bg-zinc-900/80 border border-indigo-100 dark:border-indigo-900 rounded text-[11px] text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">
                          💡 <strong>Alasan Rekomendasi:</strong> {jobMatchReason}
                        </div>
                      )}
                    </div>

                    {/* Controls Grid */}
                    <div className="grid grid-cols-2 gap-3">
                      {/* Control 1: Navigasi Website */}
                      <div className="p-2.5 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded space-y-2">
                        <span className="font-bold text-zinc-800 dark:text-zinc-200 block text-[11px] font-mono uppercase">
                          1. Navigasi Website
                        </span>
                        <div>
                          <label className="text-[10px] text-zinc-500 block mb-0.5">Varian Navigasi</label>
                          <select
                            value={wpNavVariant}
                            onChange={(e) => setWpNavVariant(e.target.value)}
                            className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs rounded"
                          >
                            <option value="floating_pill">1. Floating Pill Island (Mengambang)</option>
                            <option value="minimal">2. Minimalist Header</option>
                            <option value="brutalist">3. Brutalist Sharp Bar</option>
                            <option value="glassmorphic">4. Glassmorphism Blur</option>
                            <option value="sidebar">5. Modern Desktop Strip</option>
                          </select>
                        </div>
                        <div className="grid grid-cols-2 gap-1.5">
                          <div>
                            <label className="text-[10px] text-zinc-500 block mb-0.5">Font Nav</label>
                            <select
                              value={wpNavFont}
                              onChange={(e) => setWpNavFont(e.target.value)}
                              className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-1.5 py-1 text-[11px] rounded"
                            >
                              <option value="Inter">Inter</option>
                              <option value="Outfit">Outfit</option>
                              <option value="JetBrains Mono">Mono</option>
                              <option value="Playfair Display">Serif</option>
                            </select>
                          </div>
                          <div>
                            <label className="text-[10px] text-zinc-500 block mb-0.5">Warna Nav</label>
                            <div className="flex items-center gap-1 pt-0.5">
                              {['#09090b', '#ffffff', '#18181b', '#4f46e5', '#059669'].map((c) => (
                                <button
                                  key={c}
                                  type="button"
                                  onClick={() => setWpNavBg(c)}
                                  className={`w-4 h-4 rounded-full border ${wpNavBg === c ? 'scale-125 border-indigo-500 shadow-xs' : 'border-zinc-400'}`}
                                  style={{ backgroundColor: c }}
                                />
                              ))}
                            </div>
                          </div>
                        </div>
                      </div>

                      {/* Control 2: Footer Website */}
                      <div className="p-2.5 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded space-y-2">
                        <span className="font-bold text-zinc-800 dark:text-zinc-200 block text-[11px] font-mono uppercase">
                          2. Footer Website
                        </span>
                        <div>
                          <label className="text-[10px] text-zinc-500 block mb-0.5">Varian Footer</label>
                          <select
                            value={wpFooterVariant}
                            onChange={(e) => setWpFooterVariant(e.target.value)}
                            className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs rounded"
                          >
                            <option value="social_hub">1. Social Hub & Direct Contact</option>
                            <option value="simple_clean">2. Simple Clean One-Liner</option>
                            <option value="multi_column">3. Multi-Column Enterprise</option>
                            <option value="brutalist">4. Brutalist Big Brand</option>
                          </select>
                        </div>
                        <div>
                          <label className="text-[10px] text-zinc-500 block mb-0.5">Warna Footer</label>
                          <div className="flex items-center gap-1.5 pt-0.5">
                            {['#09090b', '#18181b', '#ffffff', '#0f172a', '#1e1b4b'].map((c) => (
                              <button
                                key={c}
                                type="button"
                                onClick={() => setWpFooterBg(c)}
                                className={`w-4 h-4 rounded-full border ${wpFooterBg === c ? 'scale-125 border-indigo-500 shadow-xs' : 'border-zinc-400'}`}
                                style={{ backgroundColor: c }}
                              />
                            ))}
                          </div>
                        </div>
                      </div>

                      {/* Control 3: Tipografi & Skala Font */}
                      <div className="p-2.5 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded space-y-2">
                        <span className="font-bold text-zinc-800 dark:text-zinc-200 block text-[11px] font-mono uppercase">
                          3. Tipografi & Ukuran
                        </span>
                        <div>
                          <label className="text-[10px] text-zinc-500 block mb-0.5">Font Judul</label>
                          <select
                            value={wpTitleFont}
                            onChange={(e) => setWpTitleFont(e.target.value)}
                            className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs rounded"
                          >
                            <option value="Inter">Inter (Clean Modern Sans)</option>
                            <option value="Outfit">Outfit (Display Geometric)</option>
                            <option value="Playfair Display">Playfair Display (Executive Serif)</option>
                            <option value="JetBrains Mono">JetBrains Mono (Developer)</option>
                            <option value="Plus Jakarta Sans">Plus Jakarta Sans (Corporate)</option>
                          </select>
                        </div>
                        <div className="grid grid-cols-2 gap-1.5">
                          <div>
                            <label className="text-[10px] text-zinc-500 block mb-0.5">Skala Judul</label>
                            <select
                              value={wpTitleSize}
                              onChange={(e) => setWpTitleSize(e.target.value)}
                              className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-1.5 py-1 text-[11px] rounded"
                            >
                              <option value="text-3xl sm:text-5xl">Besar (5xl)</option>
                              <option value="text-4xl sm:text-6xl">Sangat Besar (6xl)</option>
                              <option value="text-2xl sm:text-4xl">Sedang (4xl)</option>
                            </select>
                          </div>
                          <div>
                            <label className="text-[10px] text-zinc-500 block mb-0.5">Skala Isi</label>
                            <select
                              value={wpBodySize}
                              onChange={(e) => setWpBodySize(e.target.value)}
                              className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-1.5 py-1 text-[11px] rounded"
                            >
                              <option value="text-xs sm:text-sm">Standar (sm)</option>
                              <option value="text-sm sm:text-base">Nyaman (base)</option>
                            </select>
                          </div>
                        </div>
                      </div>

                      {/* Control 4: Bullet Point (Jenis & Warna) */}
                      <div className="p-2.5 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded space-y-2">
                        <span className="font-bold text-zinc-800 dark:text-zinc-200 block text-[11px] font-mono uppercase">
                          4. Bullet Point
                        </span>
                        <div>
                          <label className="text-[10px] text-zinc-500 block mb-0.5">Bentuk Bullet</label>
                          <select
                            value={wpBulletType}
                            onChange={(e) => setWpBulletType(e.target.value)}
                            className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs rounded"
                          >
                            <option value="check">✓ Checkmark Modern</option>
                            <option value="arrow">➜ Panah Terminal</option>
                            <option value="diamond">◆ Berlian Geometris</option>
                            <option value="disc">• Titik Bulat (Disc)</option>
                            <option value="square">■ Kotak Sharp</option>
                            <option value="dash">— Garis Panjang (Dash)</option>
                          </select>
                        </div>
                        <div>
                          <label className="text-[10px] text-zinc-500 block mb-0.5">Warna Aksen Bullet</label>
                          <div className="flex items-center gap-1.5 pt-0.5">
                            {['#10b981', '#6366f1', '#ec4899', '#f59e0b', '#06b6d4', '#64748b'].map((c) => (
                              <button
                                key={c}
                                type="button"
                                onClick={() => setWpBulletColor(c)}
                                className={`w-4 h-4 rounded-full border ${wpBulletColor === c ? 'scale-125 border-zinc-950 dark:border-white shadow-xs' : 'border-transparent'}`}
                                style={{ backgroundColor: c }}
                              />
                            ))}
                          </div>
                        </div>
                      </div>

                      {/* Control 5: Jenis & Gaya Card */}
                      <div className="p-2.5 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded space-y-2">
                        <span className="font-bold text-zinc-800 dark:text-zinc-200 block text-[11px] font-mono uppercase">
                          5. Gaya Card
                        </span>
                        <div>
                          <label className="text-[10px] text-zinc-500 block mb-0.5">Efek Card</label>
                          <select
                            value={wpCardStyle}
                            onChange={(e) => setWpCardStyle(e.target.value)}
                            className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs rounded"
                          >
                            <option value="subtle_border">1. Subtle Border (Elegan Tipis)</option>
                            <option value="modern_flat">2. Modern Flat Background</option>
                            <option value="glassmorphic">3. Glassmorphism (Blur Translucent)</option>
                            <option value="neo_brutalist">4. Neo-Brutalist (Border 2px + Shadow)</option>
                            <option value="gradient_glow">5. Gradient Glow (Neon Border)</option>
                          </select>
                        </div>
                        <div>
                          <label className="text-[10px] text-zinc-500 block mb-0.5">Sudut Card (Radius)</label>
                          <select
                            value={wpCardRadius}
                            onChange={(e) => setWpCardRadius(e.target.value)}
                            className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs rounded"
                          >
                            <option value="rounded-none">Sharp 0px (Tajam Brutalist)</option>
                            <option value="rounded-lg">Rounded 8px (Modern)</option>
                            <option value="rounded-xl">Rounded 12px (Smooth)</option>
                            <option value="rounded-2xl">Rounded 16px (Pill High)</option>
                          </select>
                        </div>
                      </div>

                      {/* Control 6: Scroll Spy Effect */}
                      <div className="p-2.5 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded space-y-2">
                        <span className="font-bold text-zinc-800 dark:text-zinc-200 block text-[11px] font-mono uppercase">
                          6. Scroll Spy Effect
                        </span>
                        <div>
                          <label className="text-[10px] text-zinc-500 block mb-0.5">Gaya Pelacak Bagian</label>
                          <select
                            value={wpSpyStyle}
                            onChange={(e) => setWpSpyStyle(e.target.value)}
                            className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs rounded"
                          >
                            <option value="glowing_pill">1. Glowing Pill Tracker</option>
                            <option value="underline_runner">2. Underline Runner (Garis Berjalan)</option>
                            <option value="active_dot">3. Active Dot Indicator</option>
                            <option value="gradient_bar">4. Gradient Progress Bar</option>
                          </select>
                        </div>
                        <div>
                          <label className="text-[10px] text-zinc-500 block mb-0.5">Warna Spy Indicator</label>
                          <div className="flex items-center gap-1.5 pt-0.5">
                            {['#6366f1', '#10b981', '#f59e0b', '#ec4899', '#06b6d4'].map((c) => (
                              <button
                                key={c}
                                type="button"
                                onClick={() => setWpSpyColor(c)}
                                className={`w-4 h-4 rounded-full border ${wpSpyColor === c ? 'scale-125 border-zinc-950 dark:border-white shadow-xs' : 'border-transparent'}`}
                                style={{ backgroundColor: c }}
                              />
                            ))}
                          </div>
                        </div>
                      </div>
                    </div>

                    {/* Control 7: WhatsApp Redirect Configuration */}
                    <div className="p-3 bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/60 rounded space-y-2.5">
                      <div className="flex items-center justify-between">
                        <span className="font-bold text-emerald-800 dark:text-emerald-300 text-[11px] font-mono uppercase flex items-center gap-1.5">
                          <MessageSquare className="w-3.5 h-3.5 text-emerald-600" />
                          <span>7. Redirect WhatsApp Langsung</span>
                        </span>
                        <span className="text-[9px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                          DIRECT CONVERSION
                        </span>
                      </div>
                      <div className="grid grid-cols-2 gap-2">
                        <div>
                          <label className="text-[10px] text-zinc-600 dark:text-zinc-400 block mb-0.5">Nomor WhatsApp Tujuan</label>
                          <input
                            type="text"
                            value={wpPhone}
                            onChange={(e) => setWpPhone(e.target.value)}
                            placeholder="628123456789"
                            className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs rounded"
                          />
                        </div>
                        <div>
                          <label className="text-[10px] text-zinc-600 dark:text-zinc-400 block mb-0.5">Teks Tombol CTA</label>
                          <input
                            type="text"
                            value={wpCtaText}
                            onChange={(e) => setWpCtaText(e.target.value)}
                            placeholder="Hubungi via WhatsApp"
                            className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs rounded"
                          />
                        </div>
                      </div>
                      <div>
                        <label className="text-[10px] text-zinc-600 dark:text-zinc-400 block mb-0.5">Pesan Pre-filled WhatsApp</label>
                        <textarea
                          rows={2}
                          value={wpMessage}
                          onChange={(e) => setWpMessage(e.target.value)}
                          className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs rounded"
                        />
                      </div>
                    </div>

                    {/* Control 8 & 9: Layout Variasi & Theme Mode */}
                    <div className="grid grid-cols-2 gap-3">
                      <div className="p-2.5 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded space-y-1.5">
                        <span className="font-bold text-zinc-800 dark:text-zinc-200 block text-[11px] font-mono uppercase">
                          8. Opsi Layout Portofolio
                        </span>
                        <select
                          value={wpLayout}
                          onChange={(e) => setWpLayout(e.target.value)}
                          className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1.5 text-xs rounded"
                        >
                          <option value="bento_grid">1. Bento Grid Modern (Interactive Cards)</option>
                          <option value="split_hero">2. Executive Split Hero</option>
                          <option value="developer_terminal">3. Developer Terminal (CLI Monospace)</option>
                          <option value="showcase_cards">4. Creative Project Showcase</option>
                          <option value="editorial_narrative">5. Minimalist Editorial Magazine</option>
                        </select>
                      </div>

                      <div className="p-2.5 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded space-y-1.5">
                        <span className="font-bold text-zinc-800 dark:text-zinc-200 block text-[11px] font-mono uppercase">
                          9. Tema Web (Dark / Light)
                        </span>
                        <div className="grid grid-cols-2 gap-1.5 pt-0.5">
                          <button
                            type="button"
                            onClick={() => setWpTheme('dark')}
                            className={`py-1.5 px-2 rounded font-mono text-xs font-bold flex items-center justify-center gap-1.5 border transition ${
                              wpTheme === 'dark' ? 'bg-zinc-950 text-white border-indigo-500 shadow-xs' : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700'
                            }`}
                          >
                            <Moon className="w-3.5 h-3.5 text-indigo-400" />
                            <span>Dark Theme</span>
                          </button>
                          <button
                            type="button"
                            onClick={() => setWpTheme('light')}
                            className={`py-1.5 px-2 rounded font-mono text-xs font-bold flex items-center justify-center gap-1.5 border transition ${
                              wpTheme === 'light' ? 'bg-white text-zinc-950 border-amber-500 shadow-xs' : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700'
                            }`}
                          >
                            <Sun className="w-3.5 h-3.5 text-amber-500" />
                            <span>Light Theme</span>
                          </button>
                        </div>
                      </div>
                    </div>

                    {/* Action: Switch to Web Preview Now */}
                    <div className="pt-2 flex justify-between items-center border-t border-zinc-200 dark:border-zinc-800">
                      <span className="text-[11px] text-zinc-500">
                        Perubahan di atas otomatis langsung diterapkan di pratinjau sisi kanan.
                      </span>
                      <button
                        type="button"
                        onClick={() => setPreviewMode('portfolio')}
                        className="px-3.5 py-1.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold rounded text-xs transition flex items-center gap-1.5 shadow-sm"
                      >
                        <Globe className="w-3.5 h-3.5" />
                        <span>Tampilkan Web Portfolio Sisi Kanan &rarr;</span>
                      </button>
                    </div>

                  </div>
                )}
              </div>

              {/* Avatar Photo & Shape Card */}
              <div className="border border-zinc-200 dark:border-zinc-800 shadow-2xs overflow-hidden">
                {renderAccordionHeader('avatar', 'Foto Profil & Avatar Header', <Eye className="w-4 h-4 text-emerald-500" />, 'AVATAR', (
                  <span className="text-[10px] font-mono text-zinc-500 font-bold">
                    {avatarConfig.showPhoto ? 'Aktif' : 'Non-aktif'}
                  </span>
                ))}
                {accordionState.avatar && (
                  <div className="bg-white dark:bg-zinc-900 p-4 border-t border-zinc-200 dark:border-zinc-800 space-y-3">

                <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                  <h3 className="text-xs font-mono font-bold uppercase text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                    <Eye className="w-3.5 h-3.5 text-indigo-500" />
                    <span>Foto Profil & Avatar Header</span>
                  </h3>
                  <label className="flex items-center gap-1.5 text-xs text-zinc-600 dark:text-zinc-300 cursor-pointer">
                    <input
                      type="checkbox"
                      checked={showPhoto}
                      onChange={(e) => setShowPhoto(e.target.checked)}
                      className="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    <span>Tampilkan Foto</span>
                  </label>
                </div>

                <div className="flex items-center gap-4 text-xs">
                  {/* Photo Preview Thumbnail */}
                  <div className="relative group shrink-0">
                    {p.photo_url ? (
                      <img
                        src={p.photo_url}
                        alt="Avatar Preview"
                        className={`w-14 h-14 object-cover border-2 shadow-sm ${photoStyle === 'circle' ? 'rounded-full' : (photoStyle === 'blob' ? 'rounded-[35%_65%_65%_35%/40%_40%_60%_60%]' : 'rounded-lg')}`}
                        style={{ borderColor: primaryColor }}
                      />
                    ) : (
                      <div
                        className={`w-14 h-14 bg-zinc-100 dark:bg-zinc-800 border-2 border-dashed border-zinc-300 dark:border-zinc-700 flex items-center justify-center text-zinc-400 font-mono text-[10px] ${photoStyle === 'circle' ? 'rounded-full' : 'rounded-lg'}`}
                      >
                        No Foto
                      </div>
                    )}
                  </div>

                  <div className="flex-1 space-y-2">
                    <div className="flex items-center gap-2">
                      <label className="px-3 py-1 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded cursor-pointer transition shadow-sm">
                        Unggah Foto
                        <input
                          type="file"
                          accept="image/*"
                          onChange={handlePhotoUpload}
                          className="hidden"
                        />
                      </label>
                      {p.photo_url && (
                        <button
                          onClick={() => {
                            setTempImageSrc(p.photo_url);
                            setCropModalOpen(true);
                          }}
                          className="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 rounded transition"
                        >
                          Crop & Sesuaikan
                        </button>
                      )}
                    </div>

                    <div className="flex items-center gap-3 pt-1">
                      <span className="text-zinc-500 text-[11px]">Bentuk:</span>
                      <button
                        type="button"
                        onClick={() => setPhotoStyle('circle')}
                        className={`px-2 py-0.5 rounded text-[11px] font-medium border ${photoStyle === 'circle' ? 'bg-indigo-50 dark:bg-indigo-950/60 border-indigo-500 text-indigo-600 dark:text-indigo-400 font-bold' : 'border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400'}`}
                      >
                        Lingkaran
                      </button>
                      <button
                        type="button"
                        onClick={() => setPhotoStyle('rounded')}
                        className={`px-2 py-0.5 rounded text-[11px] font-medium border ${photoStyle === 'rounded' ? 'bg-indigo-50 dark:bg-indigo-950/60 border-indigo-500 text-indigo-600 dark:text-indigo-400 font-bold' : 'border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400'}`}
                      >
                        Kotak Bulat
                      </button>
                      <button
                        type="button"
                        onClick={() => setPhotoStyle('blob')}
                        className={`px-2 py-0.5 rounded text-[11px] font-medium border ${photoStyle === 'blob' ? 'bg-indigo-50 dark:bg-indigo-950/60 border-indigo-500 text-indigo-600 dark:text-indigo-400 font-bold' : 'border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400'}`}
                      >
                        Modern Blob
                      </button>
                    </div>
                  </div>
                </div>
              
                  </div>
                )}
              </div>

              {/* Urutan Bagian CV (Reorder Sections Tool) */}
              <div className="border border-zinc-200 dark:border-zinc-800 shadow-2xs overflow-hidden">
                {renderAccordionHeader('reorder', 'Urutan Hirarki Bagian CV', <Layers className="w-4 h-4 text-amber-500" />, 'HIERARCHY')}
                {accordionState.reorder && (
                  <div className="bg-white dark:bg-zinc-900 p-4 border-t border-zinc-200 dark:border-zinc-800 space-y-2">

                <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                  <h3 className="text-xs font-mono font-bold uppercase text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                    <Sliders className="w-3.5 h-3.5 text-indigo-500" />
                    <span>Urutan Hirarki Bagian Resume</span>
                  </h3>
                  <span className="text-[10px] text-zinc-500 font-mono">Geser Posisi</span>
                </div>
                <div className="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-1 text-xs font-mono">
                  {sectionOrder.map((sec, idx) => {
                    const secLabels = {
                      experiences: 'Pengalaman',
                      education: 'Pendidikan',
                      skills: 'Keahlian',
                      projects: 'Proyek',
                      certifications: 'Sertifikasi',
                      references: 'Referensi'
                    };
                    return (
                      <div
                        key={sec}
                        className="p-1.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-between rounded"
                      >
                        <span className="font-bold text-zinc-800 dark:text-zinc-200 truncate">
                          {idx + 1}. {secLabels[sec] || sec}
                        </span>
                        <div className="flex items-center gap-0.5 shrink-0">
                          <button
                            type="button"
                            onClick={() => moveSection(idx, -1)}
                            disabled={idx === 0}
                            className="p-0.5 text-zinc-500 hover:text-indigo-600 disabled:opacity-30"
                            title="Pindah ke atas"
                          >
                            ▲
                          </button>
                          <button
                            type="button"
                            onClick={() => moveSection(idx, 1)}
                            disabled={idx === sectionOrder.length - 1}
                            className="p-0.5 text-zinc-500 hover:text-indigo-600 disabled:opacity-30"
                            title="Pindah ke bawah"
                          >
                            ▼
                          </button>
                        </div>
                      </div>
                    );
                  })}
                </div>
              
                  </div>
                )}
              </div>

              {/* 1. Personal Info Section */}
              <div className="border border-zinc-200 dark:border-zinc-800 shadow-2xs overflow-hidden">
                {renderAccordionHeader('personal', '1. Identitas & Kontak', <User className="w-4 h-4 text-blue-500" />, 'REQUIRED', (
                  <span className="text-[10px] font-mono text-zinc-500 truncate max-w-[120px]">
                    {personalInfo.fullName || 'Belum Diisi'}
                  </span>
                ))}
                {accordionState.personal && (
                  <div className="bg-white dark:bg-zinc-900 p-5 border-t border-zinc-200 dark:border-zinc-800 space-y-3.5">

                <h3 className="text-xs font-mono font-bold uppercase text-zinc-900 dark:text-zinc-100 border-b border-zinc-200 dark:border-zinc-800 pb-2 flex items-center justify-between">
                  <span>1. Identitas & Kontak</span>
                  <span className="text-[10px] text-indigo-500 font-normal">Wajib Lengkap</span>
                </h3>

                <div className="grid grid-cols-2 gap-3 text-xs">
                  <div className="col-span-2">
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1">Nama Lengkap & Gelar</label>
                    <input
                      type="text"
                      value={p.name || ''}
                      onChange={(e) => handlePersonalChange('name', e.target.value)}
                      placeholder="e.g. Alex Pratama, S.Kom"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>

                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1">Role / Posisi Profesi</label>
                    <input
                      type="text"
                      value={p.title || ''}
                      onChange={(e) => handlePersonalChange('title', e.target.value)}
                      placeholder="e.g. Senior Full Stack Engineer"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>

                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1">Email Kontak</label>
                    <input
                      type="email"
                      value={p.email || ''}
                      onChange={(e) => handlePersonalChange('email', e.target.value)}
                      placeholder="alex@example.com"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>

                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1">Nomor WhatsApp</label>
                    <input
                      type="text"
                      value={p.phone || ''}
                      onChange={(e) => handlePersonalChange('phone', e.target.value)}
                      placeholder="+62 812-3456-7890"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>

                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1">Lokasi Domisili</label>
                    <input
                      type="text"
                      value={p.location || ''}
                      onChange={(e) => handlePersonalChange('location', e.target.value)}
                      placeholder="Jakarta, Indonesia"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>

                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1">LinkedIn URL</label>
                    <input
                      type="text"
                      value={p.linkedin || ''}
                      onChange={(e) => handlePersonalChange('linkedin', e.target.value)}
                      placeholder="linkedin.com/in/alex"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>

                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1">Website / Portfolio</label>
                    <input
                      type="text"
                      value={p.website || ''}
                      onChange={(e) => handlePersonalChange('website', e.target.value)}
                      placeholder="https://neriahpro.com"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>

                  <div className="col-span-2">
                    <div className="flex items-center justify-between mb-1">
                      <label className="text-zinc-600 dark:text-zinc-400 font-medium">Ringkasan Profil (Executive Summary)</label>
                      <button
                        type="button"
                        onClick={() => triggerAiHelper('summary', { role: p.title || 'Profesional' })}
                        disabled={aiLoading['summary_']}
                        className="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 text-[10.5px] font-bold flex items-center gap-1 border border-indigo-200 dark:border-indigo-800 rounded transition"
                      >
                        <Wand2 className={`w-3 h-3 ${aiLoading['summary_'] ? 'animate-spin' : ''}`} />
                        <span>{aiLoading['summary_'] ? 'AI Berpikir...' : 'AI Generate Summary'}</span>
                      </button>
                    </div>
                    <textarea
                      rows={3}
                      value={p.summary || ''}
                      onChange={(e) => handlePersonalChange('summary', e.target.value)}
                      placeholder="Tuliskan 2-3 kalimat ringkasan profesional Anda..."
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>
                </div>
              
                  </div>
                )}
              </div>

              {/* 2. Work Experience Section */}
              <div className="border border-zinc-200 dark:border-zinc-800 shadow-2xs overflow-hidden">
                {renderAccordionHeader('experience', '2. Riwayat Pengalaman Kerja', <Briefcase className="w-4 h-4 text-indigo-500" />, `${experience.length} ITEMS`)}
                {accordionState.experience && (
                  <div className="bg-white dark:bg-zinc-900 p-5 border-t border-zinc-200 dark:border-zinc-800 space-y-4">

                <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                  <h3 className="text-xs font-mono font-bold uppercase text-zinc-900 dark:text-zinc-100">
                    2. Pengalaman Kerja
                  </h3>
                  <div className="flex items-center gap-2">
                    <button
                      type="button"
                      onClick={() => triggerAiHelper('brainstorm', { role: p.title || 'Software Engineer' })}
                      className="text-xs text-amber-600 dark:text-amber-400 hover:underline flex items-center gap-1 font-bold"
                      title="Brainstorm metrik angka & pencapaian terukur"
                    >
                      <Sparkles className="w-3 h-3" />
                      <span>Brainstorm Metrik</span>
                    </button>
                    <button
                      onClick={addExperience}
                      className="text-xs text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 font-bold"
                    >
                      <Plus className="w-3.5 h-3.5" /> Tambah Posisi
                    </button>
                  </div>
                </div>

                <div className="space-y-4">
                  {(content.experiences || []).map((exp, eIdx) => (
                    <div key={exp.id || eIdx} className="p-3.5 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 space-y-2.5 rounded">
                      <div className="flex items-center justify-between">
                        <span className="font-bold text-xs text-zinc-900 dark:text-zinc-100">
                          {exp.role || 'Posisi Baru'} &bull; <span className="text-zinc-500 font-normal">{exp.company || 'Perusahaan'}</span>
                        </span>
                        <button
                          onClick={() => removeExperience(exp.id)}
                          className="text-zinc-400 hover:text-rose-500"
                          title="Hapus Pengalaman Ini"
                        >
                          <Trash2 className="w-3.5 h-3.5" />
                        </button>
                      </div>

                      <div className="grid grid-cols-2 gap-2 text-xs">
                        <input
                          type="text"
                          value={exp.role || ''}
                          onChange={(e) => {
                            const arr = [...content.experiences];
                            arr[eIdx].role = e.target.value;
                            setContent({ ...content, experiences: arr });
                          }}
                          placeholder="Jabatan (e.g. Lead Systems Architect)"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                        <input
                          type="text"
                          value={exp.company || ''}
                          onChange={(e) => {
                            const arr = [...content.experiences];
                            arr[eIdx].company = e.target.value;
                            setContent({ ...content, experiences: arr });
                          }}
                          placeholder="Perusahaan"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                        <input
                          type="text"
                          value={exp.period || ''}
                          onChange={(e) => {
                            const arr = [...content.experiences];
                            arr[eIdx].period = e.target.value;
                            setContent({ ...content, experiences: arr });
                          }}
                          placeholder="Periode (e.g. 2022 - Sekarang)"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                        <input
                          type="text"
                          value={exp.location || ''}
                          onChange={(e) => {
                            const arr = [...content.experiences];
                            arr[eIdx].location = e.target.value;
                            setContent({ ...content, experiences: arr });
                          }}
                          placeholder="Lokasi (e.g. Jakarta / Remote)"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                      </div>

                      {/* Bullet Achievements */}
                      <div className="space-y-1.5 pt-1">
                        <label className="block text-[11px] font-mono text-zinc-500 uppercase">Poin Pencapaian STAR (Metrik):</label>
                        {(exp.bullets || []).map((bullet, bIdx) => (
                          <div key={bIdx} className="flex items-center gap-1.5">
                            <input
                              type="text"
                              value={bullet}
                              onChange={(e) => {
                                const arr = [...content.experiences];
                                arr[eIdx].bullets[bIdx] = e.target.value;
                                setContent({ ...content, experiences: arr });
                              }}
                              className="flex-1 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                            />
                            <button
                              type="button"
                              onClick={() => triggerAiHelper('enhance_bullet', { bullet }, eIdx, bIdx)}
                              className="px-1.5 py-1 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-300 text-[10px] rounded hover:bg-indigo-100"
                              title="Pertajam dengan kata kerja aktif"
                            >
                              ⚡ AI
                            </button>
                            <button
                              type="button"
                              onClick={() => triggerAiHelper('condense_bullet', { bullet }, eIdx, bIdx)}
                              className="px-1.5 py-1 bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-300 text-[10px] rounded hover:bg-amber-100"
                              title="Ringkas agar muat 1 halaman"
                            >
                              ✂ Ringkas
                            </button>
                            <button
                              onClick={() => {
                                const arr = [...content.experiences];
                                arr[eIdx].bullets = arr[eIdx].bullets.filter((_, i) => i !== bIdx);
                                setContent({ ...content, experiences: arr });
                              }}
                              className="text-zinc-400 hover:text-rose-500"
                            >
                              <X className="w-3.5 h-3.5" />
                            </button>
                          </div>
                        ))}
                        <button
                          onClick={() => {
                            const arr = [...content.experiences];
                            arr[eIdx].bullets = [...(arr[eIdx].bullets || []), 'Pencapaian baru'];
                            setContent({ ...content, experiences: arr });
                          }}
                          className="text-[10px] text-indigo-500 font-bold hover:underline flex items-center gap-1 pt-1"
                        >
                          <Plus className="w-3 h-3" /> Tambah Poin
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
              
                  </div>
                )}
              </div>

              {/* 3. Education Section */}
              <div className="border border-zinc-200 dark:border-zinc-800 shadow-2xs overflow-hidden">
                {renderAccordionHeader('education', '3. Riwayat Pendidikan', <GraduationCap className="w-4 h-4 text-teal-500" />, `${education.length} ITEMS`)}
                {accordionState.education && (
                  <div className="bg-white dark:bg-zinc-900 p-5 border-t border-zinc-200 dark:border-zinc-800 space-y-3">

                <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                  <h3 className="text-xs font-mono font-bold uppercase text-zinc-900 dark:text-zinc-100">
                    3. Riwayat Pendidikan
                  </h3>
                  <button
                    onClick={addEducation}
                    className="text-xs text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 font-bold"
                  >
                    <Plus className="w-3.5 h-3.5" /> Tambah Pendidikan
                  </button>
                </div>

                <div className="space-y-3">
                  {(content.education || []).map((edu, edIdx) => (
                    <div key={edu.id || edIdx} className="p-3 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 space-y-2 rounded">
                      <div className="flex items-center justify-between">
                        <span className="font-bold text-xs text-zinc-900 dark:text-zinc-100">
                          {edu.degree || 'Gelar'} &bull; <span className="text-zinc-500 font-normal">{edu.institution || 'Kampus'}</span>
                        </span>
                        <button
                          onClick={() => removeEducation(edu.id)}
                          className="text-zinc-400 hover:text-rose-500"
                          title="Hapus Pendidikan"
                        >
                          <Trash2 className="w-3.5 h-3.5" />
                        </button>
                      </div>

                      <div className="grid grid-cols-2 gap-2 text-xs">
                        <input
                          type="text"
                          value={edu.institution || ''}
                          onChange={(e) => {
                            const arr = [...content.education];
                            arr[edIdx].institution = e.target.value;
                            setContent({ ...content, education: arr });
                          }}
                          placeholder="Nama Universitas / Lembaga"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                        <input
                          type="text"
                          value={edu.degree || ''}
                          onChange={(e) => {
                            const arr = [...content.education];
                            arr[edIdx].degree = e.target.value;
                            setContent({ ...content, education: arr });
                          }}
                          placeholder="Gelar (e.g. S.Kom / Bachelor)"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                        <input
                          type="text"
                          value={edu.field || ''}
                          onChange={(e) => {
                            const arr = [...content.education];
                            arr[edIdx].field = e.target.value;
                            setContent({ ...content, education: arr });
                          }}
                          placeholder="Jurusan / Program Studi"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                        <input
                          type="text"
                          value={edu.year || ''}
                          onChange={(e) => {
                            const arr = [...content.education];
                            arr[edIdx].year = e.target.value;
                            setContent({ ...content, education: arr });
                          }}
                          placeholder="Tahun / Periode (e.g. 2018 - 2022)"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                      </div>
                    </div>
                  ))}
                </div>
              
                  </div>
                )}
              </div>

              {/* 4. Skills Section */}
              <div className="border border-zinc-200 dark:border-zinc-800 shadow-2xs overflow-hidden">
                {renderAccordionHeader('skills', '4. Keahlian & Tech Stack', <Code className="w-4 h-4 text-violet-500" />, `${skills.length} ITEMS`)}
                {accordionState.skills && (
                  <div className="bg-white dark:bg-zinc-900 p-5 border-t border-zinc-200 dark:border-zinc-800 space-y-3">

                <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                  <h3 className="text-xs font-mono font-bold uppercase text-zinc-900 dark:text-zinc-100">
                    4. Keahlian Teknis (ATS Skills)
                  </h3>
                  <button
                    type="button"
                    onClick={() => triggerAiHelper('skills', { role: p.title || 'Engineer' })}
                    disabled={aiLoading['skills_']}
                    className="text-xs text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 font-bold"
                  >
                    <Sparkles className="w-3 h-3" />
                    <span>AI Rekomendasi Skills</span>
                  </button>
                </div>

                <div className="flex flex-wrap gap-1.5">
                  {(content.skills || []).map((skill, sIdx) => (
                    <span
                      key={sIdx}
                      className="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-mono flex items-center gap-1.5 border border-zinc-200 dark:border-zinc-700 rounded"
                    >
                      <span>{skill}</span>
                      <button
                        onClick={() => removeSkill(sIdx)}
                        className="text-zinc-400 hover:text-rose-500"
                      >
                        <X className="w-3 h-3" />
                      </button>
                    </span>
                  ))}
                </div>

                <div className="flex gap-2 pt-2">
                  <input
                    type="text"
                    id="new-skill-input"
                    placeholder="Tambah keahlian manual (e.g. Docker, Redis)..."
                    className="flex-1 bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    onKeyDown={(e) => {
                      if (e.key === 'Enter') {
                        e.preventDefault();
                        addSkill(e.target.value);
                        e.target.value = '';
                      }
                    }}
                  />
                  <button
                    onClick={() => {
                      const input = document.getElementById('new-skill-input');
                      if (input && input.value) {
                        addSkill(input.value);
                        input.value = '';
                      }
                    }}
                    className="px-3 py-1.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 text-xs font-bold transition border border-zinc-300 dark:border-zinc-700"
                  >
                    Tambah
                  </button>
                </div>
              
                  </div>
                )}
              </div>

              {/* 5. Projects Section */}
              <div className="border border-zinc-200 dark:border-zinc-800 shadow-2xs overflow-hidden">
                {renderAccordionHeader('projects', '5. Portofolio Proyek Terpilih', <FolderGit2 className="w-4 h-4 text-rose-500" />, `${projects.length} ITEMS`)}
                {accordionState.projects && (
                  <div className="bg-white dark:bg-zinc-900 p-5 border-t border-zinc-200 dark:border-zinc-800 space-y-3">

                <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                  <h3 className="text-xs font-mono font-bold uppercase text-zinc-900 dark:text-zinc-100">
                    5. Proyek & Portofolio Pilihan
                  </h3>
                  <button
                    onClick={addProject}
                    className="text-xs text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 font-bold"
                  >
                    <Plus className="w-3.5 h-3.5" /> Tambah Proyek
                  </button>
                </div>

                <div className="space-y-3">
                  {(content.projects || []).map((proj, prIdx) => (
                    <div key={proj.id || prIdx} className="p-3 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 space-y-2 rounded">
                      <div className="flex items-center justify-between">
                        <span className="font-bold text-xs text-zinc-900 dark:text-zinc-100">
                          {proj.name || 'Proyek Baru'} &bull; <span className="text-zinc-500 font-normal">{proj.role || 'Role'}</span>
                        </span>
                        <button
                          onClick={() => removeProject(proj.id)}
                          className="text-zinc-400 hover:text-rose-500"
                          title="Hapus Proyek"
                        >
                          <Trash2 className="w-3.5 h-3.5" />
                        </button>
                      </div>

                      <div className="grid grid-cols-2 gap-2 text-xs">
                        <input
                          type="text"
                          value={proj.name || ''}
                          onChange={(e) => {
                            const arr = [...content.projects];
                            arr[prIdx].name = e.target.value;
                            setContent({ ...content, projects: arr });
                          }}
                          placeholder="Nama Proyek / Aplikasi"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                        <input
                          type="text"
                          value={proj.role || ''}
                          onChange={(e) => {
                            const arr = [...content.projects];
                            arr[prIdx].role = e.target.value;
                            setContent({ ...content, projects: arr });
                          }}
                          placeholder="Peran Anda (e.g. Lead Architect)"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                        <input
                          type="text"
                          value={proj.link || ''}
                          onChange={(e) => {
                            const arr = [...content.projects];
                            arr[prIdx].link = e.target.value;
                            setContent({ ...content, projects: arr });
                          }}
                          placeholder="URL / Tautan Proyek (e.g. https://...)"
                          className="col-span-2 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                        <textarea
                          rows={2}
                          value={proj.description || ''}
                          onChange={(e) => {
                            const arr = [...content.projects];
                            arr[prIdx].description = e.target.value;
                            setContent({ ...content, projects: arr });
                          }}
                          placeholder="Deskripsi singkat arsitektur & pencapaian proyek..."
                          className="col-span-2 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 p-2 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                      </div>
                    </div>
                  ))}
                </div>
              
                  </div>
                )}
              </div>

              {/* 6. Certifications Section */}
              <div className="border border-zinc-200 dark:border-zinc-800 shadow-2xs overflow-hidden">
                {renderAccordionHeader('certifications', '6. Sertifikasi & Lisensi', <Award className="w-4 h-4 text-amber-500" />, `${certifications.length} ITEMS`)}
                {accordionState.certifications && (
                  <div className="bg-white dark:bg-zinc-900 p-5 border-t border-zinc-200 dark:border-zinc-800 space-y-3">

                <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                  <h3 className="text-xs font-mono font-bold uppercase text-zinc-900 dark:text-zinc-100">
                    6. Sertifikasi & Lisensi
                  </h3>
                  <button
                    onClick={addCertification}
                    className="text-xs text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 font-bold"
                  >
                    <Plus className="w-3.5 h-3.5" /> Tambah Sertifikat
                  </button>
                </div>

                <div className="space-y-3">
                  {(content.certifications || []).map((cert, cIdx) => (
                    <div key={cert.id || cIdx} className="p-3 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 space-y-2 rounded">
                      <div className="flex items-center justify-between">
                        <span className="font-bold text-xs text-zinc-900 dark:text-zinc-100">
                          {cert.name || 'Sertifikat'} &bull; <span className="text-zinc-500 font-normal">{cert.issuer || 'Penerbit'}</span>
                        </span>
                        <button
                          onClick={() => removeCertification(cert.id)}
                          className="text-zinc-400 hover:text-rose-500"
                          title="Hapus Sertifikat"
                        >
                          <Trash2 className="w-3.5 h-3.5" />
                        </button>
                      </div>

                      <div className="grid grid-cols-2 gap-2 text-xs">
                        <input
                          type="text"
                          value={cert.name || ''}
                          onChange={(e) => {
                            const arr = [...content.certifications];
                            arr[cIdx].name = e.target.value;
                            setContent({ ...content, certifications: arr });
                          }}
                          placeholder="Nama Sertifikasi"
                          className="col-span-2 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                        <input
                          type="text"
                          value={cert.issuer || ''}
                          onChange={(e) => {
                            const arr = [...content.certifications];
                            arr[cIdx].issuer = e.target.value;
                            setContent({ ...content, certifications: arr });
                          }}
                          placeholder="Lembaga Penerbit (e.g. AWS)"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                        <input
                          type="text"
                          value={cert.year || ''}
                          onChange={(e) => {
                            const arr = [...content.certifications];
                            arr[cIdx].year = e.target.value;
                            setContent({ ...content, certifications: arr });
                          }}
                          placeholder="Tahun Perolehan (e.g. 2024)"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                      </div>
                    </div>
                  ))}
                </div>
              
                  </div>
                )}
              </div>

              {/* 7. References Section */}
              <div className="border border-zinc-200 dark:border-zinc-800 shadow-2xs overflow-hidden">
                {renderAccordionHeader('references', '7. Kontak Referensi Profesional', <Users className="w-4 h-4 text-cyan-500" />, `${references.length} ITEMS`)}
                {accordionState.references && (
                  <div className="bg-white dark:bg-zinc-900 p-5 border-t border-zinc-200 dark:border-zinc-800 space-y-3">

                <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                  <h3 className="text-xs font-mono font-bold uppercase text-zinc-900 dark:text-zinc-100">
                    7. Referensi Profesional
                  </h3>
                  <button
                    onClick={addReference}
                    className="text-xs text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 font-bold"
                  >
                    <Plus className="w-3.5 h-3.5" /> Tambah Referensi
                  </button>
                </div>

                <div className="space-y-3">
                  {(content.references || []).map((ref, rIdx) => (
                    <div key={ref.id || rIdx} className="p-3 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 space-y-2 rounded">
                      <div className="flex items-center justify-between">
                        <span className="font-bold text-xs text-zinc-900 dark:text-zinc-100">
                          {ref.name || 'Nama Referensi'} &bull; <span className="text-zinc-500 font-normal">{ref.title || 'Jabatan'}</span>
                        </span>
                        <button
                          onClick={() => removeReference(ref.id)}
                          className="text-zinc-400 hover:text-rose-500"
                          title="Hapus Referensi"
                        >
                          <Trash2 className="w-3.5 h-3.5" />
                        </button>
                      </div>

                      <div className="grid grid-cols-2 gap-2 text-xs">
                        <input
                          type="text"
                          value={ref.name || ''}
                          onChange={(e) => {
                            const arr = [...content.references];
                            arr[rIdx].name = e.target.value;
                            setContent({ ...content, references: arr });
                          }}
                          placeholder="Nama Lengkap"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                        <input
                          type="text"
                          value={ref.title || ''}
                          onChange={(e) => {
                            const arr = [...content.references];
                            arr[rIdx].title = e.target.value;
                            setContent({ ...content, references: arr });
                          }}
                          placeholder="Jabatan (e.g. CTO / VP Eng)"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                        <input
                          type="text"
                          value={ref.company || ''}
                          onChange={(e) => {
                            const arr = [...content.references];
                            arr[rIdx].company = e.target.value;
                            setContent({ ...content, references: arr });
                          }}
                          placeholder="Perusahaan"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                        <input
                          type="email"
                          value={ref.email || ''}
                          onChange={(e) => {
                            const arr = [...content.references];
                            arr[rIdx].email = e.target.value;
                            setContent({ ...content, references: arr });
                          }}
                          placeholder="Email Kontak"
                          className="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                        <input
                          type="text"
                          value={ref.note || ''}
                          onChange={(e) => {
                            const arr = [...content.references];
                            arr[rIdx].note = e.target.value;
                            setContent({ ...content, references: arr });
                          }}
                          placeholder="Catatan relasi kerja..."
                          className="col-span-2 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-100"
                        />
                      </div>
                    </div>
                  ))}
                </div>
              
                  </div>
                )}
              </div>

            </div>

            {/* Right Column: Live Interactive Preview (7 cols) */}
            <div className="lg:col-span-7 sticky top-28">
              <div className="bg-zinc-200 dark:bg-zinc-900 p-2 sm:p-6 border border-zinc-300 dark:border-zinc-800 shadow-inner flex flex-col items-center">
                                {/* PREVIEW TOP BAR: MODE SWITCHER & ACTIONS */}
                <div className="w-full flex items-center justify-between pb-3 border-b border-zinc-300 dark:border-zinc-800 mb-3">
                  <div className="flex items-center gap-1.5 p-1 bg-zinc-300 dark:bg-zinc-800/80 rounded">
                    <button
                      type="button"
                      onClick={() => setPreviewMode('cv')}
                      className={`px-3 py-1.5 rounded text-xs font-mono font-bold flex items-center gap-1.5 transition ${
                        previewMode === 'cv'
                          ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-xs'
                          : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100'
                      }`}
                    >
                      <FileText className="w-3.5 h-3.5" />
                      <span>Dokumen CV (A4 Cetak)</span>
                    </button>
                    <button
                      type="button"
                      onClick={() => setPreviewMode('portfolio')}
                      className={`px-3 py-1.5 rounded text-xs font-mono font-bold flex items-center gap-1.5 transition ${
                        previewMode === 'portfolio'
                          ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-xs'
                          : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100'
                      }`}
                    >
                      <Globe className="w-3.5 h-3.5" />
                      <span>Web Portfolio Live</span>
                      <span className="text-[9px] px-1 py-0.2 bg-white/20 rounded font-sans uppercase">Island</span>
                    </button>
                  </div>

                  <div className="flex items-center gap-2">
                    {previewMode === 'cv' ? (
                      <>
                        <span className="font-mono text-xs text-zinc-600 dark:text-zinc-400">
                          ATS: <strong className="text-emerald-600 dark:text-emerald-400">{atsScore}%</strong>
                        </span>
                        <button
                          type="button"
                          onClick={handlePrintPdf}
                          className="px-3 py-1.5 bg-zinc-900 hover:bg-black text-white dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white rounded text-xs font-mono font-bold flex items-center gap-1.5 shadow-sm transition"
                          title="Cetak CV format A4 presisi tinggi atau simpan sebagai PDF"
                        >
                          <Download className="w-3.5 h-3.5" />
                          <span>Unduh PDF / Cetak A4</span>
                        </button>
                      </>
                    ) : (
                      <div className="flex items-center gap-2">
                        <span className="text-[11px] font-mono text-zinc-500">
                          Layout: <strong className="text-purple-600 dark:text-purple-400 uppercase">{wpLayout.replace('_', ' ')}</strong>
                        </span>
                        {wpPhone && (
                          <a
                            href={`https://wa.me/${wpPhone.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(wpMessage)}`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-mono rounded font-bold flex items-center gap-1 transition shadow-xs"
                            title="Buka link redirect WhatsApp"
                          >
                            <MessageSquare className="w-3 h-3" />
                            <span>Direct WA</span>
                          </a>
                        )}
                      </div>
                    )}
                  </div>
                </div>

                {/* CONDITIONAL PREVIEW: CV DOCUMENT vs LIVE WEB PORTFOLIO ISLAND */}
                {previewMode === 'cv' ? (
<div
                  id="cv-document-canvas"
                  className={`w-full max-w-[210mm] shadow-2xl transition-all ${template === 'tech_dark' ? 'bg-[#0f172a] text-slate-100 border border-slate-800' : 'bg-white text-zinc-900 border border-zinc-200'} ${template === 'compact_elegant' ? 'p-6 sm:p-8 text-[11px] leading-tight' : 'p-8 sm:p-12 text-xs'}`}
                  style={{ fontFamily: `'${fontFamily}', sans-serif` }}
                >
                  
                  {/* ========================================================= */}
                  {/* LAYOUT A: CREATIVE ATS (TWO-COLUMN MAGAZINE SIDEBAR)      */}
                  {/* ========================================================= */}
                  {template === 'creative_ats' ? (
                    <div className="grid grid-cols-12 gap-6">
                      {/* Left Sidebar (4 cols) */}
                      <div
                        className="col-span-4 p-5 rounded-lg text-white space-y-5"
                        style={{ backgroundColor: primaryColor }}
                      >
                        {/* Avatar */}
                        {showPhoto && p.photo_url && (
                          <div className="flex justify-center">
                            <img
                              src={p.photo_url}
                              alt={p.name}
                              className={`w-28 h-28 object-cover border-4 border-white/60 shadow-lg ${photoStyle === 'circle' ? 'rounded-full' : (photoStyle === 'blob' ? 'rounded-[35%_65%_65%_35%/40%_40%_60%_60%]' : 'rounded-xl')}`}
                            />
                          </div>
                        )}

                        <div>
                          <h1 className="text-xl font-black tracking-tight leading-tight">{p.name || 'Nama Anda'}</h1>
                          <p className="text-xs font-medium text-white/80 mt-0.5">{p.title || 'Posisi Impian'}</p>
                        </div>

                        {/* Contacts */}
                        <div className="space-y-1.5 text-[11px] text-white/90 font-mono border-t border-white/20 pt-3">
                          {p.email && <div className="truncate">✉ {p.email}</div>}
                          {p.phone && <div>📞 {p.phone}</div>}
                          {p.location && <div>📍 {p.location}</div>}
                          {p.linkedin && <div className="truncate">💼 {p.linkedin}</div>}
                          {p.website && <div className="truncate">🌐 {p.website}</div>}
                        </div>

                        {/* Skills in Sidebar */}
                        {(content.skills || []).length > 0 && (
                          <div className="border-t border-white/20 pt-3 space-y-2">
                            <h4 className="text-[10px] font-mono font-bold uppercase tracking-wider text-white/90">Keahlian Inti</h4>
                            <div className="flex flex-wrap gap-1">
                              {content.skills.map((s, idx) => (
                                <span key={idx} className="px-2 py-0.5 bg-black/20 text-white text-[10px] font-mono rounded">
                                  {s}
                                </span>
                              ))}
                            </div>
                          </div>
                        )}

                        {/* Certifications in Sidebar */}
                        {(content.certifications || []).length > 0 && (
                          <div className="border-t border-white/20 pt-3 space-y-1.5">
                            <h4 className="text-[10px] font-mono font-bold uppercase tracking-wider text-white/90">Sertifikasi</h4>
                            {content.certifications.map((c, idx) => (
                              <div key={idx} className="text-[10.5px]">
                                <div className="font-bold">{c.name}</div>
                                <div className="text-white/70 text-[10px]">{c.issuer} ({c.year})</div>
                              </div>
                            ))}
                          </div>
                        )}
                      </div>

                      {/* Right Main Body (8 cols) */}
                      <div className="col-span-8 space-y-5">
                        {p.summary && (
                          <div>
                            <h4 className="text-[11px] font-mono font-bold uppercase tracking-wider pb-1 mb-1.5 border-b" style={{ color: primaryColor, borderColor: `${primaryColor}30` }}>
                              Ringkasan Eksekutif
                            </h4>
                            <p className="text-zinc-700 leading-relaxed text-justify text-[11.5px]">{p.summary}</p>
                          </div>
                        )}

                        {/* Dynamic Section Iteration */}
                        {sectionOrder.filter(s => s !== 'skills' && s !== 'certifications').map(sec => {
                          if (sec === 'experiences' && (content.experiences || []).length > 0) {
                            return (
                              <div key="experiences">
                                <h4 className="text-[11px] font-mono font-bold uppercase tracking-wider pb-1 mb-2 border-b" style={{ color: primaryColor, borderColor: `${primaryColor}30` }}>
                                  Pengalaman Profesional
                                </h4>
                                <div className="space-y-3.5">
                                  {content.experiences.map((exp, eIdx) => (
                                    <div key={eIdx}>
                                      <div className="flex justify-between font-bold text-zinc-900 text-xs">
                                        <span>{exp.role}</span>
                                        <span className="font-mono text-zinc-500 font-normal">{exp.period}</span>
                                      </div>
                                      <div className="text-zinc-600 text-[11px] font-medium">{exp.company} &bull; {exp.location}</div>
                                      {exp.bullets && (
                                        <ul className="list-disc list-outside ml-4 mt-1 text-[11px] text-zinc-700 space-y-1 leading-relaxed">
                                          {exp.bullets.map((b, bIdx) => (
                                            <li key={bIdx}>{b}</li>
                                          ))}
                                        </ul>
                                      )}
                                    </div>
                                  ))}
                                </div>
                              </div>
                            );
                          }
                          if (sec === 'education' && (content.education || []).length > 0) {
                            return (
                              <div key="education">
                                <h4 className="text-[11px] font-mono font-bold uppercase tracking-wider pb-1 mb-2 border-b" style={{ color: primaryColor, borderColor: `${primaryColor}30` }}>
                                  Pendidikan
                                </h4>
                                <div className="space-y-2">
                                  {content.education.map((edu, edIdx) => (
                                    <div key={edIdx} className="flex justify-between items-start text-xs">
                                      <div>
                                        <div className="font-bold text-zinc-900">{edu.institution}</div>
                                        <div className="text-zinc-600 text-[11px]">{edu.degree} - {edu.field}</div>
                                      </div>
                                      <span className="font-mono text-zinc-500 text-[11px]">{edu.year}</span>
                                    </div>
                                  ))}
                                </div>
                              </div>
                            );
                          }
                          if (sec === 'projects' && (content.projects || []).length > 0) {
                            return (
                              <div key="projects">
                                <h4 className="text-[11px] font-mono font-bold uppercase tracking-wider pb-1 mb-2 border-b" style={{ color: primaryColor, borderColor: `${primaryColor}30` }}>
                                  Proyek & Karya
                                </h4>
                                <div className="space-y-2.5">
                                  {content.projects.map((proj, prIdx) => (
                                    <div key={prIdx}>
                                      <div className="flex justify-between items-center font-bold text-zinc-900 text-xs">
                                        <span>{proj.name}</span>
                                        <span className="text-[10px] text-zinc-500 font-mono">{proj.role}</span>
                                      </div>
                                      <p className="text-[11px] text-zinc-600">{proj.description}</p>
                                    </div>
                                  ))}
                                </div>
                              </div>
                            );
                          }
                          if (sec === 'references' && (content.references || []).length > 0) {
                            return (
                              <div key="references">
                                <h4 className="text-[11px] font-mono font-bold uppercase tracking-wider pb-1 mb-2 border-b" style={{ color: primaryColor, borderColor: `${primaryColor}30` }}>
                                  Referensi Profesional
                                </h4>
                                <div className="grid grid-cols-2 gap-3 text-xs">
                                  {content.references.map((rf, rIdx) => (
                                    <div key={rIdx} className="p-2 bg-zinc-50 border border-zinc-200 rounded">
                                      <div className="font-bold text-zinc-900">{rf.name}</div>
                                      <div className="text-[10.5px] text-zinc-600">{rf.title} &bull; {rf.company}</div>
                                      <div className="text-[10px] text-zinc-500 font-mono">{rf.email} &bull; {rf.phone}</div>
                                    </div>
                                  ))}
                                </div>
                              </div>
                            );
                          }
                          return null;
                        })}
                      </div>
                    </div>
                  ) : (
                    /* ========================================================= */
                    /* LAYOUTS B, C, D, E: STANDARD SINGLE/DUAL COLUMN FORMATS   */
                    /* ========================================================= */
                    <div className="space-y-5">
                      
                      {/* Header with Photo & Accent */}
                      <div className={`pb-4 border-b ${template === 'executive_clean' ? 'text-center border-double border-b-4' : 'flex items-start justify-between'} ${template === 'tech_dark' ? 'border-slate-700' : ''}`} style={{ borderColor: template === 'executive_clean' ? primaryColor : `${primaryColor}30` }}>
                        <div className={template === 'executive_clean' ? 'mx-auto' : ''}>
                          <h1 className={`font-black tracking-tight ${template === 'tech_dark' ? 'text-white font-mono text-2xl' : 'text-zinc-950 text-2xl sm:text-3xl'}`}>
                            {p.name || 'Nama Anda'}
                          </h1>
                          <p className={`font-bold mt-0.5 ${template === 'tech_dark' ? 'text-emerald-400 font-mono text-xs' : 'text-sm'}`} style={{ color: template === 'tech_dark' ? undefined : primaryColor }}>
                            {template === 'tech_dark' ? `> ${p.title || 'Systems Architect'}` : (p.title || 'Posisi Impian')}
                          </p>
                          
                          <div className={`flex flex-wrap gap-x-3 gap-y-1 text-[11px] mt-2 font-mono ${template === 'executive_clean' ? 'justify-center' : ''} ${template === 'tech_dark' ? 'text-slate-400' : 'text-zinc-600'}`}>
                            {p.email && <span>✉ {p.email}</span>}
                            {p.phone && <span>📞 {p.phone}</span>}
                            {p.location && <span>📍 {p.location}</span>}
                            {p.linkedin && <span>💼 {p.linkedin}</span>}
                            {p.website && <span>🌐 {p.website}</span>}
                          </div>
                        </div>

                        {/* Optional Header Avatar */}
                        {showPhoto && p.photo_url && template !== 'executive_clean' && (
                          <img
                            src={p.photo_url}
                            alt={p.name}
                            className={`w-20 h-20 object-cover border-2 shadow-sm shrink-0 ml-4 ${photoStyle === 'circle' ? 'rounded-full' : (photoStyle === 'blob' ? 'rounded-[35%_65%_65%_35%/40%_40%_60%_60%]' : 'rounded-lg')}`}
                            style={{ borderColor: primaryColor }}
                          />
                        )}
                      </div>

                      {/* Summary */}
                      {p.summary && (
                        <div>
                          <h4 className={`text-[11px] font-bold uppercase tracking-wider mb-1 font-mono ${template === 'tech_dark' ? 'text-emerald-400' : ''}`} style={{ color: template === 'tech_dark' ? undefined : primaryColor }}>
                            {template === 'tech_dark' ? '// 01. EXECUTIVE_SUMMARY' : 'Ringkasan Profesional'}
                          </h4>
                          <p className={`leading-relaxed text-justify ${template === 'tech_dark' ? 'text-slate-300' : 'text-zinc-700'}`}>{p.summary}</p>
                        </div>
                      )}

                      {/* Iterasi Urutan Bagian Dinamis */}
                      {sectionOrder.map((secKey) => {
                        // Experiences
                        if (secKey === 'experiences' && (content.experiences || []).length > 0) {
                          return (
                            <div key="experiences">
                              <h4 className={`text-[11px] font-bold uppercase tracking-wider mb-2 font-mono border-b pb-0.5 ${template === 'tech_dark' ? 'text-emerald-400 border-slate-700' : ''}`} style={{ color: template === 'tech_dark' ? undefined : primaryColor, borderColor: template === 'tech_dark' ? undefined : `${primaryColor}25` }}>
                                {template === 'tech_dark' ? '// 02. WORK_EXPERIENCE' : 'Pengalaman Kerja'}
                              </h4>
                              <div className="space-y-3">
                                {content.experiences.map((exp, eIdx) => (
                                  <div key={eIdx}>
                                    <div className="flex justify-between font-bold text-xs">
                                      <span className={template === 'tech_dark' ? 'text-white' : 'text-zinc-900'}>{exp.role}</span>
                                      <span className="font-mono text-zinc-500 font-normal">{exp.period}</span>
                                    </div>
                                    <div className={`text-[11px] font-medium mb-1 ${template === 'tech_dark' ? 'text-slate-400' : 'text-zinc-600'}`}>
                                      {exp.company} &bull; {exp.location}
                                    </div>
                                    {exp.bullets && (
                                      <ul className={`list-disc list-outside ml-4 text-[11px] space-y-1 leading-relaxed ${template === 'tech_dark' ? 'text-slate-300' : 'text-zinc-700'}`}>
                                        {exp.bullets.map((b, bIdx) => (
                                          <li key={bIdx}>{b}</li>
                                        ))}
                                      </ul>
                                    )}
                                  </div>
                                ))}
                              </div>
                            </div>
                          );
                        }

                        // Education
                        if (secKey === 'education' && (content.education || []).length > 0) {
                          return (
                            <div key="education">
                              <h4 className={`text-[11px] font-bold uppercase tracking-wider mb-2 font-mono border-b pb-0.5 ${template === 'tech_dark' ? 'text-emerald-400 border-slate-700' : ''}`} style={{ color: template === 'tech_dark' ? undefined : primaryColor, borderColor: template === 'tech_dark' ? undefined : `${primaryColor}25` }}>
                                {template === 'tech_dark' ? '// 03. ACADEMIC_BACKGROUND' : 'Riwayat Pendidikan'}
                              </h4>
                              <div className="space-y-2">
                                {content.education.map((edu, edIdx) => (
                                  <div key={edIdx} className="flex justify-between items-start text-xs">
                                    <div>
                                      <div className={`font-bold ${template === 'tech_dark' ? 'text-white' : 'text-zinc-900'}`}>{edu.institution}</div>
                                      <div className={`text-[11px] ${template === 'tech_dark' ? 'text-slate-400' : 'text-zinc-600'}`}>{edu.degree} - {edu.field}</div>
                                    </div>
                                    <span className="font-mono text-zinc-500 text-[11px]">{edu.year}</span>
                                  </div>
                                ))}
                              </div>
                            </div>
                          );
                        }

                        // Skills
                        if (secKey === 'skills' && (content.skills || []).length > 0) {
                          return (
                            <div key="skills">
                              <h4 className={`text-[11px] font-bold uppercase tracking-wider mb-2 font-mono border-b pb-0.5 ${template === 'tech_dark' ? 'text-emerald-400 border-slate-700' : ''}`} style={{ color: template === 'tech_dark' ? undefined : primaryColor, borderColor: template === 'tech_dark' ? undefined : `${primaryColor}25` }}>
                                {template === 'tech_dark' ? '// 04. TECHNICAL_SKILLS' : 'Keahlian Teknis & Alat'}
                              </h4>
                              <div className="flex flex-wrap gap-1.5">
                                {content.skills.map((skill, sIdx) => (
                                  <span
                                    key={sIdx}
                                    className={`px-2 py-0.5 text-[11px] font-mono rounded ${template === 'tech_dark' ? 'bg-slate-800 text-emerald-300 border border-slate-700' : 'bg-zinc-100 text-zinc-800 border border-zinc-200'}`}
                                  >
                                    {skill}
                                  </span>
                                ))}
                              </div>
                            </div>
                          );
                        }

                        // Projects
                        if (secKey === 'projects' && (content.projects || []).length > 0) {
                          return (
                            <div key="projects">
                              <h4 className={`text-[11px] font-bold uppercase tracking-wider mb-2 font-mono border-b pb-0.5 ${template === 'tech_dark' ? 'text-emerald-400 border-slate-700' : ''}`} style={{ color: template === 'tech_dark' ? undefined : primaryColor, borderColor: template === 'tech_dark' ? undefined : `${primaryColor}25` }}>
                                {template === 'tech_dark' ? '// 05. KEY_PROJECTS' : 'Proyek Portofolio Pilihan'}
                              </h4>
                              <div className="space-y-2">
                                {content.projects.map((proj, prIdx) => (
                                  <div key={prIdx}>
                                    <div className="flex justify-between items-center text-xs font-bold">
                                      <span className={template === 'tech_dark' ? 'text-white' : 'text-zinc-900'}>{proj.name}</span>
                                      <span className="text-[10px] text-zinc-500 font-mono">{proj.role}</span>
                                    </div>
                                    <p className={`text-[11px] mt-0.5 ${template === 'tech_dark' ? 'text-slate-400' : 'text-zinc-600'}`}>{proj.description}</p>
                                  </div>
                                ))}
                              </div>
                            </div>
                          );
                        }

                        // Certifications
                        if (secKey === 'certifications' && (content.certifications || []).length > 0) {
                          return (
                            <div key="certifications">
                              <h4 className={`text-[11px] font-bold uppercase tracking-wider mb-2 font-mono border-b pb-0.5 ${template === 'tech_dark' ? 'text-emerald-400 border-slate-700' : ''}`} style={{ color: template === 'tech_dark' ? undefined : primaryColor, borderColor: template === 'tech_dark' ? undefined : `${primaryColor}25` }}>
                                {template === 'tech_dark' ? '// 06. CERTIFICATIONS' : 'Sertifikasi & Lisensi'}
                              </h4>
                              <div className="grid grid-cols-2 gap-2 text-xs">
                                {content.certifications.map((c, idx) => (
                                  <div key={idx} className="flex justify-between items-center">
                                    <span className={`font-medium ${template === 'tech_dark' ? 'text-slate-200' : 'text-zinc-800'}`}>{c.name}</span>
                                    <span className="text-zinc-500 font-mono text-[10px]">{c.year}</span>
                                  </div>
                                ))}
                              </div>
                            </div>
                          );
                        }

                        // References
                        if (secKey === 'references' && (content.references || []).length > 0) {
                          return (
                            <div key="references">
                              <h4 className={`text-[11px] font-bold uppercase tracking-wider mb-2 font-mono border-b pb-0.5 ${template === 'tech_dark' ? 'text-emerald-400 border-slate-700' : ''}`} style={{ color: template === 'tech_dark' ? undefined : primaryColor, borderColor: template === 'tech_dark' ? undefined : `${primaryColor}25` }}>
                                {template === 'tech_dark' ? '// 07. REFERENCES' : 'Referensi Profesional'}
                              </h4>
                              <div className="grid grid-cols-2 gap-3 text-xs">
                                {content.references.map((rf, rIdx) => (
                                  <div key={rIdx} className={`p-2 rounded border ${template === 'tech_dark' ? 'bg-slate-900 border-slate-800' : 'bg-zinc-50 border-zinc-200'}`}>
                                    <div className={`font-bold ${template === 'tech_dark' ? 'text-white' : 'text-zinc-900'}`}>{rf.name}</div>
                                    <div className="text-[10px] text-zinc-500 font-mono">{rf.title} &bull; {rf.company}</div>
                                    <div className="text-[10px] text-zinc-400 font-mono">{rf.email}</div>
                                  </div>
                                ))}
                              </div>
                            </div>
                          );
                        }

                        return null;
                      })}

                    </div>
                  )}

                </div>
                ) : (
                  /* THE LIVE WEB PORTFOLIO ISLAND PREVIEW FRAME */
                  <div className={`w-full transition-all duration-300 ${previewDevice === 'mobile' ? 'max-w-[420px]' : previewDevice === 'tablet' ? 'max-w-[760px]' : 'max-w-full'}`}>
                    {/* Mock Browser Top Bar */}
                    <div className="bg-zinc-800 dark:bg-zinc-900 text-zinc-300 px-4 py-2.5 rounded-t-xl border border-zinc-700 flex items-center justify-between shadow-md">
                      <div className="flex items-center gap-2">
                        <div className="flex items-center gap-1.5">
                          <span className="w-3 h-3 rounded-full bg-rose-500 inline-block"></span>
                          <span className="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                          <span className="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                        </div>
                        <span className="text-[10px] font-mono text-zinc-400 pl-2">NERIAH // WEB PORTFOLIO LIVE RUNTIME</span>
                      </div>

                      {/* URL Bar */}
                      <div className="hidden sm:flex items-center gap-1.5 bg-zinc-950/70 border border-zinc-700/80 px-3 py-1 rounded-md text-[11px] font-mono text-zinc-300 w-1/2 justify-center">
                        <Lock className="w-3 h-3 text-emerald-400" />
                        <span>https://portfolio.neriahpro.com/{content?.personal_info?.name ? content.personal_info.name.toLowerCase().replace(/[^a-z0-9]/g, '-') : 'alex-pratama'}</span>
                      </div>

                      {/* Responsive Switcher & Theme Badge */}
                      <div className="flex items-center gap-1.5">
                        <div className="flex items-center bg-zinc-900 border border-zinc-700 rounded p-0.5">
                          <button
                            type="button"
                            onClick={() => setPreviewDevice('desktop')}
                            className={`p-1 rounded ${previewDevice === 'desktop' ? 'bg-zinc-700 text-white' : 'text-zinc-400 hover:text-zinc-200'}`}
                            title="Tampilan Desktop"
                          >
                            <Monitor className="w-3.5 h-3.5" />
                          </button>
                          <button
                            type="button"
                            onClick={() => setPreviewDevice('mobile')}
                            className={`p-1 rounded ${previewDevice === 'mobile' ? 'bg-zinc-700 text-white' : 'text-zinc-400 hover:text-zinc-200'}`}
                            title="Tampilan Mobile"
                          >
                            <Smartphone className="w-3.5 h-3.5" />
                          </button>
                        </div>
                        <button
                          type="button"
                          onClick={() => setWpTheme(wpTheme === 'dark' ? 'light' : 'dark')}
                          className="p-1.5 text-zinc-400 hover:text-amber-400 transition"
                          title="Toggle Dark / Light Theme"
                        >
                          {wpTheme === 'dark' ? <Sun className="w-3.5 h-3.5 text-amber-400" /> : <Moon className="w-3.5 h-3.5 text-indigo-400" />}
                        </button>
                      </div>
                    </div>

                    {/* Live Website Canvas */}
                    <div
                      className={`border-x border-b border-zinc-300 dark:border-zinc-800 rounded-b-xl overflow-hidden shadow-2xl transition-colors duration-300 ${
                        wpTheme === 'dark' ? 'bg-[#09090b] text-zinc-100' : 'bg-slate-50 text-zinc-900'
                      }`}
                      style={{ fontFamily: `'${wpTitleFont}', sans-serif` }}
                    >
                      {/* 1. Website Navigation Island */}
                      {renderWebPortfolioNavbar()}

                      {/* Scroll Spy Indicator Bar (if gradient_bar) */}
                      {wpSpyStyle === 'gradient_bar' && (
                        <div
                          className="w-full h-1 sticky top-0 z-30 transition-all"
                          style={{
                            background: `linear-gradient(90deg, ${wpSpyColor} 0%, #ec4899 50%, #f59e0b 100%)`,
                          }}
                        />
                      )}

                      {/* 2. Hero Section */}
                      {renderWebPortfolioHero()}

                      {/* 3. Main Content based on wpLayout */}
                      <div className="p-4 sm:p-8 max-w-5xl mx-auto space-y-8">
                        {renderWebPortfolioLayoutContent()}
                      </div>

                      {/* 4. Website Footer */}
                      {renderWebPortfolioFooter()}
                    </div>
                  </div>
                )}
              </div>
            </div>

          </div>
        )}

        {/* ========================================================================= */}
        {/* TAB 1.5: SOCIAL MEDIA PROMO GRAPHIC & CAPTION STUDIO                      */}
        {/* ========================================================================= */}
        {activeTab === 'sosmed' && (
          <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm space-y-6">
            <div className="flex flex-wrap items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-4">
              <div>
                <div className="flex items-center gap-2">
                  <span className="p-1.5 rounded-lg bg-pink-500/20 text-pink-600 dark:text-pink-400">
                    <Share2 className="w-5 h-5" />
                  </span>
                  <h2 className="text-lg font-black text-zinc-900 dark:text-white uppercase tracking-tight">
                    Social Media Announcement & Promo Card Generator
                  </h2>
                </div>
                <p className="text-xs text-zinc-500 mt-1">
                  Hasilkan grafik banner pengumuman LinkedIn/Instagram beresolusi tinggi dengan sorotan ATS score dan teks caption teroptimasi.
                </p>
              </div>

              <div className="flex items-center gap-2">
                <span className="text-xs font-mono text-zinc-500">Gradien Kartu:</span>
                {['purple', 'emerald', 'amber', 'blue'].map((gr) => (
                  <button
                    key={gr}
                    type="button"
                    onClick={() => setSosmedGradient(gr)}
                    className={`px-2.5 py-1 text-xs font-bold rounded capitalize border transition ${sosmedGradient === gr ? 'border-zinc-900 dark:border-white scale-105 shadow' : 'border-zinc-300 dark:border-zinc-700 opacity-70'}`}
                  >
                    {gr}
                  </button>
                ))}
              </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
              {/* Left Controls */}
              <div className="lg:col-span-5 space-y-4">
                <div className="space-y-1.5">
                  <label className="block text-xs font-bold text-zinc-700 dark:text-zinc-300">
                    Topik Kampanye Pengumuman:
                  </label>
                  <select
                    value={sosmedTopic}
                    onChange={(e) => setSosmedTopic(e.target.value)}
                    className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 rounded"
                  >
                    <option value="OpenToWork">1. #OpenToWork - Mencari Peluang Karir Baru</option>
                    <option value="Milestone">2. Karir Milestone - Berbagi Sertifikasi / Pencapaian</option>
                    <option value="ProjectShowcase">3. Show Off Proyek & Portofolio</option>
                    <option value="Other">4. Kustom (Tuliskan Arahan Khusus Anda)</option>
                  </select>
                </div>

                {sosmedTopic === 'Other' && (
                  <div className="space-y-1.5">
                    <label className="block text-xs font-bold text-zinc-700 dark:text-zinc-300">
                      Instruksi Brief Kreatif Kustom:
                    </label>
                    <textarea
                      rows={3}
                      value={sosmedCustomPrompt}
                      onChange={(e) => setSosmedCustomPrompt(e.target.value)}
                      placeholder="Contoh: 'Saya baru saja lulus ujian sertifikasi AWS dan siap membantu perusahaan start-up mendesain cloud'..."
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-zinc-100 rounded"
                    />
                  </div>
                )}

                <button
                  type="button"
                  onClick={handleGenerateSosmed}
                  disabled={generatingSosmed}
                  id="btn-gen-sosmed-image"
                  className="w-full py-3 bg-gradient-to-r from-pink-600 via-purple-600 to-indigo-600 hover:from-pink-500 hover:to-indigo-500 text-white text-xs font-black uppercase tracking-wider rounded-lg shadow-lg flex items-center justify-center gap-2 transition disabled:opacity-50"
                >
                  <Sparkles className="w-4 h-4" />
                  <span>{generatingSosmed ? 'AI Sedang Merancang Banner...' : 'Buat Gambar & Teks Promo Sosmed'}</span>
                </button>

                {sosmedData?.caption && (
                  <div className="space-y-2 pt-2">
                    <div className="flex items-center justify-between">
                      <span className="text-xs font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase">
                        Caption Siap Posting LinkedIn / IG:
                      </span>
                      <button
                        onClick={() => copyToClipboard(sosmedData.caption)}
                        className="text-xs text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 font-bold"
                      >
                        <Copy className="w-3.5 h-3.5" /> Salin Teks
                      </button>
                    </div>
                    <textarea
                      rows={7}
                      readOnly
                      value={sosmedData.caption}
                      className="w-full bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-300 dark:border-zinc-700 p-3 text-xs text-zinc-800 dark:text-zinc-200 font-mono rounded"
                    />
                  </div>
                )}
              </div>

              {/* Right: Graphic Canvas Preview */}
              <div className="lg:col-span-7 flex flex-col items-center">
                <div
                  className={`w-full max-w-[600px] aspect-[1200/630] rounded-xl shadow-2xl p-6 sm:p-8 flex flex-col justify-between text-white relative overflow-hidden transition-all ${
                    sosmedGradient === 'emerald'
                      ? 'bg-gradient-to-br from-emerald-900 via-zinc-900 to-black border border-emerald-500/30'
                      : sosmedGradient === 'amber'
                      ? 'bg-gradient-to-br from-amber-700 via-zinc-900 to-black border border-amber-500/30'
                      : sosmedGradient === 'blue'
                      ? 'bg-gradient-to-br from-blue-900 via-slate-900 to-black border border-blue-500/30'
                      : 'bg-gradient-to-br from-purple-900 via-indigo-950 to-black border border-purple-500/30'
                  }`}
                  id="promo-card-wrapper"
                >
                  {/* Background Accents */}
                  <div className="absolute -top-16 -right-16 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none" />
                  
                  {/* Top Bar */}
                  <div className="flex items-center justify-between relative z-10">
                    <div className="flex items-center gap-2">
                      <div className="w-7 h-7 rounded bg-white text-zinc-950 font-black font-mono text-xs flex items-center justify-center">
                        NP
                      </div>
                      <span className="font-mono text-[10px] tracking-wider text-white/80 font-bold uppercase">
                        Neriah Pro // Verified Candidate
                      </span>
                    </div>

                    <span className="px-2.5 py-0.5 bg-emerald-500 text-black font-mono font-black text-[10px] rounded-full shadow">
                      ATS VERIFIED 98%
                    </span>
                  </div>

                  {/* Center Content */}
                  <div className="space-y-2 relative z-10 my-4">
                    <span className="px-2 py-0.5 bg-white/15 text-white/90 font-mono text-[10px] rounded font-bold uppercase tracking-wide">
                      {sosmedTopic === 'OpenToWork' ? '🚀 READY FOR NEW ROLE' : '🌟 CAREER HIGHLIGHT'}
                    </span>
                    <h3 className="text-xl sm:text-2xl font-black tracking-tight leading-snug">
                      {sosmedData?.card_headline || `Siap Memberi Dampak Baru Sebagai ${p.title || 'Senior Engineer'}`}
                    </h3>
                    <p className="text-xs text-white/80 line-clamp-2">
                      {p.name || 'Alex Pratama'} &bull; {p.title || 'Software Engineer'} &bull; {p.location || 'Jakarta, ID'}
                    </p>
                  </div>

                  {/* Bottom Stats & Skills */}
                  <div className="border-t border-white/20 pt-3 flex items-center justify-between relative z-10 text-xs font-mono">
                    <div className="flex items-center gap-1.5 flex-wrap">
                      {(content.skills || []).slice(0, 3).map((sk, idx) => (
                        <span key={idx} className="px-2 py-0.5 bg-white/20 text-white rounded text-[10px]">
                          {sk}
                        </span>
                      ))}
                    </div>
                    <span className="text-[10px] text-white/70">neriahpro.com/cv-pro</span>
                  </div>
                </div>

                {/* Actions */}
                <div className="mt-4 flex items-center gap-3">
                  <button
                    onClick={() => {
                      alert('Grafik siap dibagikan! Silakan gunakan tombol screenshot atau salin caption untuk diposkan ke LinkedIn.');
                    }}
                    className="px-4 py-2 bg-zinc-900 hover:bg-black text-white text-xs font-bold rounded flex items-center gap-1.5 shadow"
                  >
                    <Download className="w-4 h-4" />
                    <span>Download Gambar Promo</span>
                  </button>
                  <button
                    onClick={() => copyToClipboard(sosmedData?.caption || '')}
                    className="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded flex items-center gap-1.5 shadow"
                  >
                    <Copy className="w-4 h-4" />
                    <span>Salin Teks Caption</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        )}


        {/* ========================================================================= */}
        {/* TAB 2: JOB HUB & KANBAN APPLICATION TRACKER                               */}
        {/* ========================================================================= */}
        {activeTab === 'job_hub' && (
          <div className="space-y-6">
            
            {/* Header Toolbar */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 shadow-sm flex flex-wrap items-center justify-between gap-4">
              <div>
                <h2 className="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                  <Briefcase className="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                  <span>Job Hub // Pelacak Lamaran & Penyesuaian CV Presisi</span>
                </h2>
                <p className="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                  Pantau alur rekrutmen lamaran kerja Anda dan sesuaikan CV secara otomatis untuk setiap lowongan dengan sekali klik.
                </p>
              </div>

              <div className="flex items-center gap-3">
                <button
                  onClick={() => setShowAddJobModal(true)}
                  className="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow"
                >
                  <Plus className="w-4 h-4" />
                  <span>Tambah Lamaran Kerja</span>
                </button>
              </div>
            </div>

            {/* Kanban Columns */}
            <div className="grid grid-cols-1 md:grid-cols-5 gap-4 items-start">
              {stages.map((stage) => {
                const stageJobs = jobs.filter(j => j.status === stage.key);
                return (
                  <div key={stage.key} className="bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800 rounded-lg p-3 min-h-[450px] flex flex-col">
                    <div className="flex items-center justify-between pb-2 mb-3 border-b border-zinc-200 dark:border-zinc-800">
                      <span className="text-xs font-mono font-bold uppercase text-zinc-700 dark:text-zinc-300">
                        {stage.label}
                      </span>
                      <span className="w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-800 text-[10px] font-mono font-bold flex items-center justify-center">
                        {stageJobs.length}
                      </span>
                    </div>

                    <div className="space-y-3 flex-1">
                      {stageJobs.map((job) => (
                        <div key={job.id} className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-3.5 rounded shadow-sm hover:shadow transition space-y-2">
                          <div className="flex justify-between items-start">
                            <div>
                              <h4 className="text-xs font-bold text-zinc-900 dark:text-white leading-tight">{job.role}</h4>
                              <p className="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 mt-0.5">{job.company}</p>
                            </div>
                            <button
                              onClick={() => deleteJob(job.id)}
                              className="text-zinc-400 hover:text-rose-500 p-0.5"
                              title="Hapus"
                            >
                              <X className="w-3.5 h-3.5" />
                            </button>
                          </div>

                          {job.salary && (
                            <span className="inline-block px-1.5 py-0.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[10.5px] font-mono font-semibold rounded">
                              💰 {job.salary}
                            </span>
                          )}

                          {job.desc && (
                            <p className="text-[11px] text-zinc-500 line-clamp-2 leading-relaxed">
                              {job.desc}
                            </p>
                          )}

                          {/* Action Buttons on Job Card */}
                          <div className="pt-2 border-t border-zinc-100 dark:border-zinc-800 flex flex-col gap-1.5">
                            <button
                              onClick={() => handleTailorCv(job)}
                              disabled={tailoring}
                              className="w-full py-1 px-2 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 text-indigo-700 dark:text-indigo-300 text-[10.5px] font-bold rounded flex items-center justify-center gap-1 border border-indigo-200 dark:border-indigo-800/60"
                            >
                              <Wand2 className="w-3 h-3" />
                              <span>Sesuaikan CV untuk Lowongan Ini</span>
                            </button>

                            <div className="flex items-center justify-between text-[10px] text-zinc-400 pt-1">
                              <button
                                onClick={() => moveJobStage(job.id, 'prev')}
                                className="hover:text-indigo-500 font-bold px-1"
                                title="Pindah ke tahap sebelumnya"
                              >
                                ← Mundur
                              </button>
                              <button
                                onClick={() => {
                                  setTargetCompany(job.company);
                                  setJobTitle(job.role);
                                  setJobDesc(job.desc || '');
                                  setActiveTab('mock_interview');
                                }}
                                className="text-zinc-600 dark:text-zinc-400 hover:text-indigo-500 font-medium"
                              >
                                Latihan Mock
                              </button>
                              <button
                                onClick={() => moveJobStage(job.id, 'next')}
                                className="hover:text-indigo-500 font-bold px-1"
                                title="Pindah ke tahap berikutnya"
                              >
                                Maju →
                              </button>
                            </div>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                );
              })}
            </div>

          </div>
        )}

        {/* ========================================================================= */}
        {/* TAB 3: FINANCE & PRICING CALCULATOR                                       */}
        {/* ========================================================================= */}
        {activeTab === 'finance' && (
          <div className="space-y-6">
            
            {/* Top KPI Summary Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm">
                <span className="text-xs font-mono font-bold uppercase text-zinc-500">Total Omset Layanan</span>
                <div className="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                  Rp {totalOmset.toLocaleString('id-ID')}
                </div>
                <span className="text-[11px] text-zinc-400">Dari {totalPaidCount} transaksi lunas</span>
              </div>

              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm">
                <span className="text-xs font-mono font-bold uppercase text-zinc-500">Rata-rata Order (AOV)</span>
                <div className="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">
                  Rp {aov.toLocaleString('id-ID')}
                </div>
                <span className="text-[11px] text-zinc-400">Nilai tiket per klien</span>
              </div>

              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm">
                <span className="text-xs font-mono font-bold uppercase text-zinc-500">Total Order Dicatat</span>
                <div className="text-2xl font-black text-zinc-900 dark:text-white mt-1">
                  {financeTransactions.length}
                </div>
                <span className="text-[11px] text-zinc-400">B2C & B2B Consulting</span>
              </div>

              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm">
                <span className="text-xs font-mono font-bold uppercase text-zinc-500">Tingkat Sukses Pelunasan</span>
                <div className="text-2xl font-black text-amber-500 mt-1">
                  {financeTransactions.length > 0 ? Math.round((totalPaidCount / financeTransactions.length) * 100) : 0}%
                </div>
                <span className="text-[11px] text-zinc-400">Metode QRIS & Transfer</span>
              </div>
            </div>

            {/* Rate Card & Service Packages Catalog */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm space-y-4">
              <h3 className="text-xs font-mono font-bold uppercase text-zinc-500 dark:text-zinc-400 flex items-center gap-2">
                <Sliders className="w-4 h-4 text-indigo-500" />
                <span>Katalog Paket Layanan & Kalkulator Biaya (Pricing Rate Card)</span>
              </h3>

              <div className="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                <div className="p-4 border border-zinc-200 dark:border-zinc-800 rounded bg-zinc-50 dark:bg-zinc-950/40 space-y-2">
                  <span className="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 font-mono font-bold text-[10px] rounded">1. STARTER / LITE</span>
                  <div className="text-lg font-black text-zinc-900 dark:text-white">Rp 35.000</div>
                  <ul className="text-zinc-600 dark:text-zinc-400 space-y-1 text-[11px]">
                    <li>✓ Akses template Modern</li>
                    <li>✓ Export PDF & Print View</li>
                    <li>✗ Tanpa konsultasi ahli</li>
                  </ul>
                </div>

                <div className="p-4 border-2 border-indigo-500/50 rounded bg-indigo-50/20 dark:bg-indigo-950/20 space-y-2 relative">
                  <span className="px-2 py-0.5 bg-indigo-600 text-white font-mono font-bold text-[10px] rounded">2. STANDARD (FAVORIT)</span>
                  <div className="text-lg font-black text-indigo-600 dark:text-indigo-400">Rp 75.000</div>
                  <ul className="text-zinc-700 dark:text-zinc-300 space-y-1 text-[11px]">
                    <li>✓ Audit ATS Skor 80+</li>
                    <li>✓ Poles kata kerja aksi aktif</li>
                    <li>✓ Sisipkan data metrik (%/Rp)</li>
                  </ul>
                </div>

                <div className="p-4 border border-zinc-200 dark:border-zinc-800 rounded bg-zinc-50 dark:bg-zinc-950/40 space-y-2">
                  <span className="px-2 py-0.5 bg-emerald-600 text-white font-mono font-bold text-[10px] rounded">3. FULL STACK</span>
                  <div className="text-lg font-black text-zinc-900 dark:text-white">Rp 150.000</div>
                  <ul className="text-zinc-600 dark:text-zinc-400 space-y-1 text-[11px]">
                    <li>✓ Semua fitur Standard</li>
                    <li>✓ Cover Letter tertarget</li>
                    <li>✓ Optimasi Profil LinkedIn</li>
                  </ul>
                </div>

                <div className="p-4 border border-zinc-200 dark:border-zinc-800 rounded bg-zinc-50 dark:bg-zinc-950/40 space-y-2">
                  <span className="px-2 py-0.5 bg-purple-600 text-white font-mono font-bold text-[10px] rounded">4. EXECUTIVE VIP</span>
                  <div className="text-lg font-black text-purple-600 dark:text-purple-400">Rp 300.000</div>
                  <ul className="text-zinc-600 dark:text-zinc-400 space-y-1 text-[11px]">
                    <li>✓ Semua fitur Full Stack</li>
                    <li>✓ Simulasi Mock Interview STAR</li>
                    <li>✓ Garansi lolos seleksi berkas</li>
                  </ul>
                </div>
              </div>
            </div>

            {/* Transactions Ledger Table */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm space-y-4">
              <div className="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                <h3 className="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                  <FileSpreadsheet className="w-4 h-4 text-emerald-500" />
                  <span>Buku Kas Transaksi Layanan Pembuatan CV</span>
                </h3>

                <div className="flex items-center gap-2">
                  <button
                    onClick={handleExportFinanceCsv}
                    className="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow"
                  >
                    <Download className="w-3.5 h-3.5" />
                    <span>Export Excel (.csv)</span>
                  </button>
                  <button
                    onClick={() => setShowAddTransactionModal(true)}
                    className="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow"
                  >
                    <Plus className="w-3.5 h-3.5" />
                    <span>Catat Transaksi Baru</span>
                  </button>
                </div>
              </div>

              <div className="overflow-x-auto">
                <table className="w-full text-left text-xs text-zinc-700 dark:text-zinc-300">
                  <thead className="bg-zinc-50 dark:bg-zinc-800 font-mono text-zinc-500 text-[11px] uppercase">
                    <tr>
                      <th className="p-2.5">ID TRX</th>
                      <th className="p-2.5">Tanggal</th>
                      <th className="p-2.5">Klien</th>
                      <th className="p-2.5">Paket Layanan</th>
                      <th className="p-2.5">Nominal (IDR)</th>
                      <th className="p-2.5">Metode Bayar</th>
                      <th className="p-2.5">Status</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-zinc-200 dark:divide-zinc-800">
                    {financeTransactions.map((t) => (
                      <tr key={t.id} className="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                        <td className="p-2.5 font-mono font-bold text-indigo-600 dark:text-indigo-400">{t.id}</td>
                        <td className="p-2.5 font-mono">{t.date}</td>
                        <td className="p-2.5 font-semibold text-zinc-900 dark:text-white">{t.client_name}</td>
                        <td className="p-2.5">{t.package}</td>
                        <td className="p-2.5 font-mono font-bold">Rp {t.price.toLocaleString('id-ID')}</td>
                        <td className="p-2.5 text-zinc-500">{t.payment_method}</td>
                        <td className="p-2.5">
                          <span className={`px-2 py-0.5 text-[10px] font-mono font-bold rounded ${t.status === 'paid' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400' : 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-400'}`}>
                            {t.status === 'paid' ? 'LUNAS' : 'MENUNGGU'}
                          </span>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        )}

        {/* ========================================================================= */}
        {/* TAB 4: AI ATS AUDITOR & QUALITY LINTER                                    */}
        {/* ========================================================================= */}
        {activeTab === 'ats_audit' && (
          <div className="max-w-4xl mx-auto space-y-6">
            
            {/* Score Overview Card */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-6">
              <div className="flex items-center gap-5">
                <div className={`w-24 h-24 rounded-full flex flex-col items-center justify-center font-black text-2xl shadow-inner border-4 ${atsScore >= 80 ? 'border-emerald-500 text-emerald-500 bg-emerald-50 dark:bg-emerald-950/20' : (atsScore >= 60 ? 'border-amber-500 text-amber-500 bg-amber-50 dark:bg-amber-950/20' : 'border-rose-500 text-rose-500 bg-rose-50 dark:bg-rose-950/20')}`}>
                  <span>{atsScore}</span>
                  <span className="text-[10px] font-mono uppercase font-bold text-zinc-500">Skor ATS</span>
                </div>
                <div>
                  <h2 className="text-xl font-bold text-zinc-900 dark:text-white">
                    {atsScore >= 80 ? 'Resume Sangat Kompetitif' : (atsScore >= 60 ? 'Kualitas Baik, Perlu Sedikit Poles' : 'Perlu Optimasi Mendalam')}
                  </h2>
                  <p className="text-xs text-zinc-500 dark:text-zinc-400 mt-1 max-w-md">
                    Analisis heuristik terhadap kata kerja aksi, kelengkapan kontak, data metrik terukur (% / Rp), dan kerapatan kata kunci ATS.
                  </p>
                </div>
              </div>

              <button
                onClick={() => runAtsAudit()}
                disabled={auditing}
                className="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-2 shadow"
              >
                <RotateCcw className={`w-3.5 h-3.5 ${auditing ? 'animate-spin' : ''}`} />
                <span>{auditing ? 'Memindai...' : 'Pindai Ulang AI'}</span>
              </button>
            </div>

            {/* Sub-scores breakdown */}
            {atsAudit?.sub_scores && (
              <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                {Object.entries(atsAudit.sub_scores).map(([k, v]) => (
                  <div key={k} className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 text-center">
                    <div className="text-xs text-zinc-500 uppercase font-mono">{k.replace('_', ' ')}</div>
                    <div className="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{v} / 25</div>
                  </div>
                ))}
              </div>
            )}

            {/* Actionable Improvement Items */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm space-y-4">
              <h3 className="text-sm font-bold uppercase font-mono text-zinc-900 dark:text-white flex items-center gap-2">
                <AlertTriangle className="w-4 h-4 text-amber-500" />
                <span>Rekomendasi Perbaikan Prioritas Tinggi ({atsAudit?.issues?.length || 0})</span>
              </h3>

              <div className="space-y-3">
                {(atsAudit?.issues || []).map((issue, idx) => (
                  <div key={idx} className="p-3.5 bg-amber-50 dark:bg-amber-950/20 border-l-4 border-amber-500 text-xs text-amber-950 dark:text-amber-200 flex items-start gap-3">
                    <span className="font-bold uppercase font-mono px-1.5 py-0.5 bg-amber-200 dark:bg-amber-800 text-[10px] text-amber-900 dark:text-amber-100">
                      {issue.section}
                    </span>
                    <p className="flex-1">{issue.message}</p>
                  </div>
                ))}
              </div>

              {atsAudit?.strengths && atsAudit.strengths.length > 0 && (
                <div className="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                  <h4 className="text-xs font-mono font-bold uppercase text-emerald-600 dark:text-emerald-400 mb-2 flex items-center gap-1.5">
                    <CheckCircle className="w-3.5 h-3.5" />
                    <span>Poin Kekuatan Terverifikasi</span>
                  </h4>
                  <ul className="list-disc list-inside space-y-1 text-xs text-zinc-600 dark:text-zinc-400">
                    {atsAudit.strengths.map((s, idx) => (
                      <li key={idx}>{s}</li>
                    ))}
                  </ul>
                </div>
              )}
            </div>

          </div>
        )}

        {/* ========================================================================= */}
        {/* TAB 5: VIRTUAL MOCK INTERVIEW STUDIO                                      */}
        {/* ========================================================================= */}
        {activeTab === 'mock_interview' && (
          <div className="max-w-4xl mx-auto space-y-6">
            
            {/* Live Voice Copilot Promotion Card */}
            <div className="bg-gradient-to-r from-purple-950/70 via-indigo-950/60 to-zinc-900 border border-purple-500/40 p-6 shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4 rounded-sm">
              <div className="space-y-1.5">
                <div className="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30 uppercase tracking-wider">
                  <span className="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
                  Fitur Eksklusif Pro / Executive
                </div>
                <h3 className="text-base font-bold text-white flex items-center gap-2">
                  <Headphones className="w-5 h-5 text-purple-400" />
                  <span>Asisten Wawancara Real-Time (Voice Copilot)</span>
                </h3>
                <p className="text-xs text-zinc-300 max-w-xl leading-relaxed">
                  Dengarkan pertanyaan pewawancara secara live via mikrofon saat wawancara berlangsung. AI akan langsung memunculkan contekkan STAR (Situasi, Tugas, Aksi, Hasil) dan kata kunci emas secara real-time di layar Anda!
                </p>
              </div>
              <button
                onClick={handleOpenRealtimeCopilot}
                className="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-purple-600/30 shrink-0 rounded-sm"
              >
                <Mic className="w-4 h-4" />
                <span>Buka Asisten Real-Time →</span>
              </button>
            </div>

            {/* Target Job Setup Card */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm">
              <h3 className="text-xs font-mono font-bold uppercase text-indigo-600 dark:text-indigo-400 mb-3 flex items-center gap-2">
                <Briefcase className="w-4 h-4" />
                <span>Konfigurasi Target Wawancara Kerja</span>
              </h3>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-4">
                <div>
                  <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Perusahaan Target</label>
                  <input
                    type="text"
                    value={targetCompany}
                    onChange={(e) => setTargetCompany(e.target.value)}
                    placeholder="e.g. Neriah Pro, Tokopedia, Google"
                    className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs"
                  />
                </div>

                <div>
                  <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Posisi yang Dilamar</label>
                  <input
                    type="text"
                    value={jobTitle}
                    onChange={(e) => setJobTitle(e.target.value)}
                    placeholder="e.g. Lead Systems Architect"
                    className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs"
                  />
                </div>
              </div>

              <button
                onClick={handleGenerateInterview}
                disabled={generatingInterview}
                className="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow"
              >
                <Sparkles className={`w-3.5 h-3.5 ${generatingInterview ? 'animate-spin' : ''}`} />
                <span>{generatingInterview ? 'AI Sedang Menyusun Pertanyaan...' : 'Generate 5 Pertanyaan Wawancara Strategis'}</span>
              </button>
            </div>

            {/* Questions Carousel & Voice Answer Recording */}
            {interviewQuestions.length > 0 && (
              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm space-y-6">
                
                {/* Question Header & Category */}
                <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                  <div className="flex items-center gap-2 text-xs font-mono">
                    <span className="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 font-bold">
                      SOAL {selectedQuestionIndex + 1} DARI {interviewQuestions.length}
                    </span>
                    <span className="text-zinc-400">
                      • {interviewQuestions[selectedQuestionIndex].category}
                    </span>
                  </div>

                  <div className="flex gap-1">
                    {interviewQuestions.map((_, i) => (
                      <button
                        key={i}
                        onClick={() => { setSelectedQuestionIndex(i); setAnswerEvaluation(null); setUserAnswer(''); }}
                        className={`w-6 h-6 text-xs font-mono font-bold transition ${selectedQuestionIndex === i ? 'bg-indigo-600 text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200'}`}
                      >
                        {i + 1}
                      </button>
                    ))}
                  </div>
                </div>

                {/* Question Statement */}
                <div>
                  <h4 className="text-base font-bold text-zinc-900 dark:text-white leading-snug">
                    "{interviewQuestions[selectedQuestionIndex].question}"
                  </h4>
                  <p className="text-xs text-indigo-600 dark:text-indigo-400 mt-2 font-mono flex items-start gap-1">
                    <span>💡 Kriteria Rekruter:</span>
                    <span>{interviewQuestions[selectedQuestionIndex].tip}</span>
                  </p>
                </div>

                {/* Candidate Answer Box with Voice Mic */}
                <div className="space-y-2">
                  <div className="flex items-center justify-between">
                    <label className="text-xs font-bold text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5 font-mono">
                      <span>Jawaban Anda (Ketik atau Bicara via Mic)</span>
                    </label>

                    <button
                      onClick={toggleRecording}
                      className={`px-3 py-1 text-xs font-bold transition flex items-center gap-1.5 ${isRecording ? 'bg-rose-600 text-white animate-pulse' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200'}`}
                    >
                      {isRecording ? <MicOff className="w-3.5 h-3.5" /> : <Mic className="w-3.5 h-3.5" />}
                      <span>{isRecording ? 'Berhenti Bicara' : 'Mulai Rekam Suara'}</span>
                    </button>
                  </div>

                  <textarea
                    rows={4}
                    value={userAnswer}
                    onChange={(e) => setUserAnswer(e.target.value)}
                    placeholder="Bicaralah ke mikrofon Anda atau ketik jawaban Anda di sini. Gunakan metode STAR (Situation, Task, Action, Result)..."
                    className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-3 text-xs text-zinc-900 dark:text-zinc-100"
                  />
                </div>

                {/* Evaluate Answer Button */}
                <button
                  onClick={handleEvaluateAnswer}
                  disabled={evaluatingAnswer}
                  className="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-2 shadow"
                >
                  <CheckCircle className={`w-3.5 h-3.5 ${evaluatingAnswer ? 'animate-spin' : ''}`} />
                  <span>{evaluatingAnswer ? 'AI Sedang Mengevaluasi Kerangka STAR...' : 'Evaluasi Jawaban Saya (STAR Framework)'}</span>
                </button>

                {/* Evaluation Result Box */}
                {answerEvaluation && (
                  <div className="p-4 bg-zinc-50 dark:bg-zinc-800 border-l-4 border-indigo-500 text-xs space-y-3">
                    <div className="flex items-center justify-between">
                      <span className="font-bold text-zinc-900 dark:text-white uppercase font-mono">Hasil Analisis AI:</span>
                      <span className="px-2 py-0.5 bg-indigo-600 text-white font-bold font-mono">
                        Skor STAR: {answerEvaluation.score}/100
                      </span>
                    </div>

                    <div className="grid grid-cols-4 gap-2 text-center font-mono text-[11px]">
                      {Object.entries(answerEvaluation.star_breakdown || {}).map(([stage, pass]) => (
                        <div key={stage} className={`p-1.5 border ${pass ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-zinc-300 dark:border-zinc-700 text-zinc-400'}`}>
                          {stage.toUpperCase()}: {pass ? '✓' : '✗'}
                        </div>
                      ))}
                    </div>

                    <p className="text-zinc-700 dark:text-zinc-300 leading-relaxed">
                      <strong>Ulasan:</strong> {answerEvaluation.feedback}
                    </p>

                    <p className="text-indigo-600 dark:text-indigo-400 font-mono">
                      <strong>Saran Pelatih:</strong> {answerEvaluation.coaching_tip}
                    </p>
                  </div>
                )}

              </div>
            )}

          </div>
        )}

        {/* ========================================================================= */}
        {/* TAB 6: OUTREACH & LINKEDIN PERSONAL BRANDING                              */}
        {/* ========================================================================= */}
        {activeTab === 'outreach' && (
          <div className="max-w-4xl mx-auto space-y-6">
            
            {/* Sub-tab Switcher: Letters vs LinkedIn */}
            <div className="flex border-b border-zinc-200 dark:border-zinc-800">
              <button
                onClick={() => setOutreachSubTab('letter')}
                className={`px-5 py-2.5 font-mono text-xs font-bold border-b-2 transition ${outreachSubTab === 'letter' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-900'}`}
              >
                Surat Lamaran & Email Korespondensi
              </button>
              <button
                onClick={() => { setOutreachSubTab('linkedin'); if (!linkedInContent) handleGenerateLinkedIn(); }}
                className={`px-5 py-2.5 font-mono text-xs font-bold border-b-2 transition ${outreachSubTab === 'linkedin' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-900'}`}
              >
                LinkedIn Personal Branding Suite
              </button>
            </div>

            {/* SUBTAB 1: LETTERS */}
            {outreachSubTab === 'letter' && (
              <div className="space-y-6">
                <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm space-y-4">
                  <h3 className="text-xs font-mono font-bold uppercase text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                    <Mail className="w-4 h-4" />
                    <span>Generator Surat & Email Korespondensi Karier</span>
                  </h3>

                  <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                      <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Tipe Surat</label>
                      <select
                        value={outreachType}
                        onChange={(e) => setOutreachType(e.target.value)}
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs"
                      >
                        <option value="thank_you">Thank You Email (Pasca Wawancara)</option>
                        <option value="follow_up">Follow-Up Email (Status Lamaran)</option>
                        <option value="letter_of_interest">Letter of Interest (Cold Pitch)</option>
                      </select>
                    </div>

                    <div>
                      <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Perusahaan Tujuan</label>
                      <input
                        type="text"
                        value={targetCompany}
                        onChange={(e) => setTargetCompany(e.target.value)}
                        placeholder="e.g. Neriah Pro"
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs"
                      />
                    </div>

                    <div>
                      <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Nama Penerima / HRD (Opsional)</label>
                      <input
                        type="text"
                        value={outreachRecipient}
                        onChange={(e) => setOutreachRecipient(e.target.value)}
                        placeholder="e.g. Bapak Hendra (Lead HR)"
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs"
                      />
                    </div>
                  </div>

                  <button
                    onClick={handleGenerateOutreach}
                    disabled={generatingOutreach}
                    className="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-2 shadow"
                  >
                    <Sparkles className={`w-3.5 h-3.5 ${generatingOutreach ? 'animate-spin' : ''}`} />
                    <span>{generatingOutreach ? 'Menyusun Surat...' : 'Generate Surat Profesional'}</span>
                  </button>
                </div>

                {generatedLetter && (
                  <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm space-y-4">
                    <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                      <span className="text-xs font-mono font-bold uppercase text-zinc-500">DRAF SURAT SIAP KIRIM</span>
                      <button
                        onClick={() => copyToClipboard(generatedLetter)}
                        className="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-xs font-bold transition flex items-center gap-1.5 border border-zinc-300 dark:border-zinc-700"
                      >
                        <Copy className="w-3.5 h-3.5" />
                        <span>{copyNotification ? 'Tersalin ke Clipboard!' : 'Salin Teks'}</span>
                      </button>
                    </div>

                    <textarea
                      rows={10}
                      readOnly
                      value={generatedLetter}
                      className="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 p-4 text-xs font-mono leading-relaxed text-zinc-800 dark:text-zinc-200"
                    />
                  </div>
                )}
              </div>
            )}

            {/* SUBTAB 2: LINKEDIN OPTIMIZER */}
            {outreachSubTab === 'linkedin' && (
              <div className="space-y-6">
                <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm space-y-4">
                  <div className="flex items-center justify-between">
                    <div>
                      <h3 className="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                        <Share2 className="w-4 h-4 text-blue-600" />
                        <span>LinkedIn Headline & Profile Optimization</span>
                      </h3>
                      <p className="text-xs text-zinc-500 mt-0.5">
                        Tingkatkan keterlihatan profil Anda kepada recruiter di LinkedIn dengan formula headline dan ringkasan yang menarik.
                      </p>
                    </div>

                    <button
                      onClick={handleGenerateLinkedIn}
                      disabled={generatingLinkedIn}
                      className="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow"
                    >
                      <RotateCcw className={`w-3.5 h-3.5 ${generatingLinkedIn ? 'animate-spin' : ''}`} />
                      <span>{generatingLinkedIn ? 'Menghasilkan...' : 'Generate LinkedIn Pack'}</span>
                    </button>
                  </div>

                  {linkedInContent && (
                    <div className="space-y-6 pt-2">
                      {/* Headlines */}
                      <div>
                        <h4 className="text-xs font-mono font-bold uppercase text-zinc-600 dark:text-zinc-400 mb-2">
                          1. Pilihan Headline Profil (Copy & Paste ke LinkedIn):
                        </h4>
                        <div className="space-y-2">
                          {(linkedInContent.headlines || []).map((h, idx) => (
                            <div key={idx} className="p-3 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-between gap-3 text-xs">
                              <span className="font-semibold text-zinc-800 dark:text-zinc-200">{h}</span>
                              <button
                                onClick={() => copyToClipboard(h)}
                                className="px-2.5 py-1 bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 text-[10px] font-bold rounded shrink-0"
                              >
                                Salin
                              </button>
                            </div>
                          ))}
                        </div>
                      </div>

                      {/* About section */}
                      <div>
                        <div className="flex items-center justify-between mb-1.5">
                          <h4 className="text-xs font-mono font-bold uppercase text-zinc-600 dark:text-zinc-400">
                            2. Bagian "About / Tentang Saya" Optimal:
                          </h4>
                          <button
                            onClick={() => copyToClipboard(linkedInContent.about)}
                            className="text-xs text-blue-600 hover:underline font-bold"
                          >
                            Salin Tentang Saya
                          </button>
                        </div>
                        <textarea
                          rows={6}
                          readOnly
                          value={linkedInContent.about || ''}
                          className="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 p-3 text-xs font-sans leading-relaxed text-zinc-800 dark:text-zinc-200"
                        />
                      </div>

                      {/* Thought leadership hooks */}
                      <div>
                        <h4 className="text-xs font-mono font-bold uppercase text-zinc-600 dark:text-zinc-400 mb-2">
                          3. Ide Topik Postingan LinkedIn untuk Personal Branding:
                        </h4>
                        <div className="space-y-2">
                          {(linkedInContent.thought_leadership_posts || []).map((post, idx) => (
                            <div key={idx} className="p-3 bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/40 text-xs flex items-center justify-between gap-3">
                              <span className="text-zinc-800 dark:text-zinc-200">"{post}"</span>
                              <button
                                onClick={() => copyToClipboard(post)}
                                className="px-2 py-0.5 bg-blue-600 text-white text-[10px] font-bold rounded shrink-0"
                              >
                                Salin
                              </button>
                            </div>
                          ))}
                        </div>
                      </div>
                    </div>
                  )}
                </div>
              </div>
            )}

          </div>
        )}

      {/* ========================================================================= */}
      {/* 3. MODALS: UPGRADE & PAYWALL, VOICE COPILOT, AND WEB PORTFOLIO            */}
      {/* ========================================================================= */}

      {/* UPGRADE & KUOTA AI MODAL */}
      {upgradeModalOpen && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-3xl max-h-[92vh] overflow-y-auto shadow-2xl flex flex-col">
            <div className="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-amber-500/10">
              <div className="flex items-center gap-2.5">
                <div className="w-9 h-9 rounded-lg bg-amber-500/20 text-amber-500 flex items-center justify-center font-bold">
                  <Crown className="w-5 h-5" />
                </div>
                <div>
                  <h3 className="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                    <span>Buka Akses Penuh AI & Fitur Eksklusif CV Pro</span>
                    <span className="text-[10px] font-mono px-2 py-0.5 bg-amber-500/20 text-amber-600 dark:text-amber-400 rounded-full font-bold">PRO & TOP-UP</span>
                  </h3>
                  <p className="text-xs text-zinc-500">
                    Pengunjung gratis bebas mengetik data manual dan unduh PDF sepuasnya tanpa biaya.
                  </p>
                </div>
              </div>
              <button 
                onClick={() => setUpgradeModalOpen(false)}
                className="text-zinc-400 hover:text-zinc-600 dark:hover:text-white"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <div className="p-6 space-y-6">
              {upgradeNotice && (
                <div className="p-3.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-300 dark:border-amber-800/60 text-xs text-amber-800 dark:text-amber-200 rounded flex items-start gap-2.5">
                  <Lock className="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                  <div>
                    <span className="font-bold">Akses Dibatasi: </span>
                    <span>{upgradeNotice}</span>
                  </div>
                </div>
              )}

              {/* Pricing Tier Grid */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {/* Pro Career */}
                <div className="p-4 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 rounded-lg flex flex-col justify-between">
                  <div>
                    <span className="text-[10px] font-mono font-bold uppercase text-indigo-500">Paling Populer</span>
                    <h4 className="text-sm font-bold text-zinc-900 dark:text-white mt-1">Pro Career</h4>
                    <div className="text-lg font-black text-indigo-600 dark:text-indigo-400 mt-2">
                      Rp 99.000 <span className="text-xs font-normal text-zinc-400">/bln</span>
                    </div>
                    <ul className="text-[11px] text-zinc-600 dark:text-zinc-300 space-y-1.5 mt-3">
                      <li className="flex items-center gap-1.5"><Check className="w-3.5 h-3.5 text-emerald-500" /> Unlimited ATS Audits</li>
                      <li className="flex items-center gap-1.5"><Check className="w-3.5 h-3.5 text-emerald-500" /> 10x Tailor CV ke Lowongan</li>
                      <li className="flex items-center gap-1.5"><Check className="w-3.5 h-3.5 text-emerald-500" /> 5x Mock Interview AI</li>
                      <li className="flex items-center gap-1.5"><Check className="w-3.5 h-3.5 text-emerald-500" /> 50 AI Credits Bulanan</li>
                    </ul>
                  </div>
                  <div className="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-700 space-y-2">
                    <a
                      href="/pricing"
                      className="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold text-center block rounded transition"
                    >
                      Pilih Paket Ini →
                    </a>
                    <button
                      onClick={() => handleSimulatePlanActivation('pro_career')}
                      disabled={activatingPlan}
                      className="w-full py-1 text-[10px] font-mono text-indigo-600 dark:text-indigo-400 hover:underline"
                    >
                      [Tes / Simulasi Pro Career]
                    </button>
                  </div>
                </div>

                {/* Ultimate Executive */}
                <div className="p-4 bg-gradient-to-b from-purple-900/10 to-zinc-900/40 border-2 border-purple-500 rounded-lg flex flex-col justify-between relative shadow-lg">
                  <span className="absolute -top-2.5 right-3 px-2 py-0.5 bg-purple-600 text-white text-[9px] font-mono font-bold rounded-full">
                    REKOMENDASI
                  </span>
                  <div>
                    <span className="text-[10px] font-mono font-bold uppercase text-purple-400">All-Inclusive</span>
                    <h4 className="text-sm font-bold text-zinc-900 dark:text-white mt-1">Ultimate Executive</h4>
                    <div className="text-lg font-black text-purple-500 mt-2">
                      Rp 199.000 <span className="text-xs font-normal text-zinc-400">/bln</span>
                    </div>
                    <ul className="text-[11px] text-zinc-600 dark:text-zinc-300 space-y-1.5 mt-3">
                      <li className="flex items-center gap-1.5 font-bold text-purple-400"><Check className="w-3.5 h-3.5 text-purple-400" /> Voice Interview Copilot Real-Time</li>
                      <li className="flex items-center gap-1.5 font-bold text-purple-400"><Check className="w-3.5 h-3.5 text-purple-400" /> AI Web Portfolio Generator</li>
                      <li className="flex items-center gap-1.5"><Check className="w-3.5 h-3.5 text-emerald-500" /> Unlimited Tailor CV & ATS</li>
                      <li className="flex items-center gap-1.5"><Check className="w-3.5 h-3.5 text-emerald-500" /> 150 AI Credits Bulanan</li>
                    </ul>
                  </div>
                  <div className="mt-4 pt-3 border-t border-purple-500/30 space-y-2">
                    <a
                      href="/pricing"
                      className="w-full py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-xs font-bold text-center block rounded transition shadow"
                    >
                      Langganan Sekarang →
                    </a>
                    <button
                      onClick={() => handleSimulatePlanActivation('ultimate_executive')}
                      disabled={activatingPlan}
                      className="w-full py-1 text-[10px] font-mono text-purple-400 hover:underline"
                    >
                      [Tes / Simulasi Executive]
                    </button>
                  </div>
                </div>

                {/* Top Up A La Carte */}
                <div className="p-4 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 rounded-lg flex flex-col justify-between">
                  <div>
                    <span className="text-[10px] font-mono font-bold uppercase text-amber-500">A La Carte / Top Up</span>
                    <h4 className="text-sm font-bold text-zinc-900 dark:text-white mt-1">Isi Ulang Fleksibel</h4>
                    <div className="text-lg font-black text-amber-500 mt-2">
                      Mulai Rp 15.000
                    </div>
                    <ul className="text-[11px] text-zinc-600 dark:text-zinc-300 space-y-1.5 mt-3">
                      <li className="flex items-center gap-1.5"><Zap className="w-3.5 h-3.5 text-amber-500" /> 25 Kuota AI: Rp 25.000</li>
                      <li className="flex items-center gap-1.5"><Zap className="w-3.5 h-3.5 text-amber-500" /> 1x Tailor CV Kilat: Rp 15.000</li>
                      <li className="flex items-center gap-1.5"><Zap className="w-3.5 h-3.5 text-amber-500" /> 1x Mock Interview AI: Rp 20.000</li>
                      <li className="flex items-center gap-1.5"><Check className="w-3.5 h-3.5 text-emerald-500" /> Kuota tidak pernah hangus</li>
                    </ul>
                  </div>
                  <div className="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-700 space-y-2">
                    <a
                      href="/pricing"
                      className="w-full py-2 bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold text-center block rounded transition"
                    >
                      Beli Kuota Eceran →
                    </a>
                    <button
                      onClick={() => handleSimulatePlanActivation('topup_ai_credits_25')}
                      disabled={activatingPlan}
                      className="w-full py-1 text-[10px] font-mono text-amber-500 hover:underline"
                    >
                      [Tes Top Up 25 Kredit]
                    </button>
                  </div>
                </div>
              </div>

              {/* Free Plan Clarification Box */}
              <div className="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-300 dark:border-emerald-800/40 rounded-lg flex items-start gap-3">
                <CheckCircle className="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
                <div className="text-xs text-emerald-800 dark:text-emerald-200 leading-relaxed">
                  <span className="font-bold">Jaminan Bebas Biaya untuk Pengunjung: </span>
                  Anda dapat mengisi form resume, mengganti template modern, mengatur warna dan font, mengekspor backup JSON, dan mengunduh format PDF sepuasnya tanpa dipungut biaya apa pun. Pembelian hanya diperlukan jika Anda menginginkan otomatisasi AI secepat kilat!
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* REAL-TIME VOICE INTERVIEW COPILOT MODAL */}
      {realtimeCopilotOpen && (
        <div className="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-4">
          <div className="bg-zinc-900 border border-purple-500/40 w-full max-w-4xl max-h-[92vh] overflow-y-auto shadow-2xl flex flex-col text-zinc-100">
            
            {/* Header */}
            <div className="p-4 border-b border-zinc-800 flex items-center justify-between bg-zinc-950/60">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl bg-purple-600/20 text-purple-400 flex items-center justify-center border border-purple-500/30">
                  <Headphones className="w-5 h-5" />
                </div>
                <div>
                  <div className="flex items-center gap-2">
                    <h3 className="text-sm font-bold text-white">
                      Asisten Wawancara Real-Time (Live Voice Copilot)
                    </h3>
                    <span className="px-2 py-0.5 bg-purple-500/20 text-purple-300 border border-purple-500/30 text-[10px] font-mono rounded-full font-bold">
                      PRO COPILOT
                    </span>
                  </div>
                  <p className="text-xs text-zinc-400">
                    Mendengarkan suara pewawancara secara live & menghasilkan contekkan STAR langsung di layar Anda.
                  </p>
                </div>
              </div>

              <button
                onClick={() => { stopLiveCopilotListening(); setRealtimeCopilotOpen(false); }}
                className="text-zinc-400 hover:text-white p-1"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <div className="p-6 space-y-6">
              
              {/* Controls: Mode Switcher */}
              <div className="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-800 pb-4">
                <div className="flex items-center gap-2">
                  <button
                    onClick={() => setCopilotTab('voice')}
                    className={`px-3 py-1.5 text-xs font-bold transition flex items-center gap-1.5 rounded ${copilotTab === 'voice' ? 'bg-purple-600 text-white' : 'bg-zinc-800 text-zinc-400 hover:text-white'}`}
                  >
                    <Mic className="w-3.5 h-3.5" />
                    <span>Mikrofon Suara Live</span>
                  </button>
                  <button
                    onClick={() => setCopilotTab('manual')}
                    className={`px-3 py-1.5 text-xs font-bold transition flex items-center gap-1.5 rounded ${copilotTab === 'manual' ? 'bg-purple-600 text-white' : 'bg-zinc-800 text-zinc-400 hover:text-white'}`}
                  >
                    <Type className="w-3.5 h-3.5" />
                    <span>Tempel Teks Pertanyaan</span>
                  </button>
                </div>

                {copilotTab === 'voice' && (
                  <div>
                    {isListeningLive ? (
                      <button
                        onClick={stopLiveCopilotListening}
                        className="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold transition flex items-center gap-2 shadow animate-pulse rounded"
                      >
                        <Square className="w-3.5 h-3.5 fill-current" />
                        <span>Hentikan Mendengarkan</span>
                      </button>
                    ) : (
                      <button
                        onClick={startLiveCopilotListening}
                        className="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-2 shadow rounded"
                      >
                        <Play className="w-3.5 h-3.5 fill-current" />
                        <span>Mulai Mendengarkan Live</span>
                      </button>
                    )}
                  </div>
                )}
              </div>

              {/* Voice Listening Box */}
              {copilotTab === 'voice' && (
                <div className="p-4 bg-zinc-950 border border-zinc-800 rounded-lg space-y-3">
                  <div className="flex items-center justify-between text-xs font-mono">
                    <span className="text-zinc-400 flex items-center gap-2">
                      <span className={`w-2.5 h-2.5 rounded-full ${isListeningLive ? 'bg-emerald-400 animate-ping' : 'bg-zinc-600'}`}></span>
                      {isListeningLive ? 'STATUS: Mendengarkan Suara Pewawancara...' : 'STATUS: Standby (Klik tombol Mulai Mendengarkan)'}
                    </span>
                    {liveTranscript && (
                      <button
                        onClick={() => fetchRealtimeCheatSheet(liveTranscript)}
                        className="text-purple-400 hover:underline font-bold"
                      >
                        Analisis Ulang Transkrip Ini →
                      </button>
                    )}
                  </div>

                  <div className="min-h-[60px] p-3 bg-zinc-900 border border-zinc-800 text-sm font-sans text-zinc-200 rounded">
                    {liveTranscript || <span className="text-zinc-500 italic">Transkrip ucapan pewawancara akan muncul otomatis di sini secara real-time saat Anda berbicara atau saat pewawancara bertanya...</span>}
                  </div>
                </div>
              )}

              {/* Manual Paste Box */}
              {copilotTab === 'manual' && (
                <div className="p-4 bg-zinc-950 border border-zinc-800 rounded-lg space-y-3">
                  <label className="block text-xs font-bold text-zinc-300">
                    Tempel Pertanyaan Pewawancara / Topik Wawancara:
                  </label>
                  <textarea
                    rows={3}
                    value={manualTranscriptInput}
                    onChange={(e) => setManualTranscriptInput(e.target.value)}
                    placeholder="Contoh: 'Ceritakan pengalaman tersulit saat memimpin tim engineer menghadapi deadline mendadak?'"
                    className="w-full bg-zinc-900 border border-zinc-700 p-3 text-xs text-white rounded"
                  />
                  <button
                    onClick={() => fetchRealtimeCheatSheet(manualTranscriptInput)}
                    disabled={copilotLoading || !manualTranscriptInput.trim()}
                    className="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold transition flex items-center gap-1.5 rounded shadow disabled:opacity-50"
                  >
                    <Sparkles className="w-3.5 h-3.5" />
                    <span>{copilotLoading ? 'Menyusun STAR Cheat Sheet...' : 'Hasilkan Contekkan STAR Instan'}</span>
                  </button>
                </div>
              )}

              {/* Real-Time Live STAR Cheat-Sheet Display */}
              {liveCheatSheet && (
                <div className="space-y-4 pt-2">
                  <div className="flex items-center justify-between border-b border-zinc-800 pb-2">
                    <div className="flex items-center gap-2">
                      <span className="w-2 h-2 rounded-full bg-emerald-400"></span>
                      <span className="text-xs font-mono font-bold text-emerald-400 uppercase">
                        {liveCheatSheet.category}
                      </span>
                    </div>
                    <span className="text-[11px] text-zinc-400 italic">
                      {liveCheatSheet.pacing_tip}
                    </span>
                  </div>

                  {/* STAR Grid */}
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {/* Situation */}
                    <div className="p-3.5 bg-zinc-950/80 border border-zinc-800 rounded">
                      <div className="text-[11px] font-mono font-bold text-indigo-400 uppercase mb-1">
                        1. S - Situasi (Konteks Masalah)
                      </div>
                      <p className="text-xs text-zinc-200 leading-relaxed">
                        {liveCheatSheet.cheat_sheet?.situation}
                      </p>
                    </div>

                    {/* Task */}
                    <div className="p-3.5 bg-zinc-950/80 border border-zinc-800 rounded">
                      <div className="text-[11px] font-mono font-bold text-blue-400 uppercase mb-1">
                        2. T - Tugas / Tanggung Jawab
                      </div>
                      <p className="text-xs text-zinc-200 leading-relaxed">
                        {liveCheatSheet.cheat_sheet?.task}
                      </p>
                    </div>

                    {/* Action */}
                    <div className="p-3.5 bg-zinc-950/80 border border-zinc-800 rounded">
                      <div className="text-[11px] font-mono font-bold text-amber-400 uppercase mb-1">
                        3. A - Aksi Solutif & Tech Stack
                      </div>
                      <p className="text-xs text-zinc-200 leading-relaxed">
                        {liveCheatSheet.cheat_sheet?.action}
                      </p>
                    </div>

                    {/* Result */}
                    <div className="p-3.5 bg-zinc-950/80 border border-emerald-500/30 rounded bg-emerald-950/10">
                      <div className="text-[11px] font-mono font-bold text-emerald-400 uppercase mb-1">
                        4. R - Hasil Terukur (Angka Metrik)
                      </div>
                      <p className="text-xs text-emerald-200 font-semibold leading-relaxed">
                        {liveCheatSheet.cheat_sheet?.result}
                      </p>
                    </div>
                  </div>

                  {/* Power Keywords */}
                  <div className="p-3 bg-zinc-950 border border-zinc-800 rounded flex flex-wrap items-center gap-2">
                    <span className="text-[11px] font-mono text-zinc-400 uppercase">
                      Kata Kunci Emas untuk Diucapkan:
                    </span>
                    {(liveCheatSheet.power_keywords || []).map((kw, i) => (
                      <span key={i} className="px-2.5 py-1 bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-mono font-bold rounded">
                        {kw}
                      </span>
                    ))}
                  </div>
                </div>
              )}

            </div>
          </div>
        </div>
      )}

      {/* AI WEB PORTFOLIO GENERATOR MODAL */}
      {webPortfolioOpen && (
        <div className="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-4">
          <div className="bg-zinc-900 border border-indigo-500/40 w-full max-w-5xl max-h-[92vh] overflow-y-auto shadow-2xl flex flex-col text-zinc-100">
            
            {/* Header */}
            <div className="p-4 border-b border-zinc-800 flex items-center justify-between bg-zinc-950/60">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl bg-indigo-600/20 text-indigo-400 flex items-center justify-center border border-indigo-500/30">
                  <Globe className="w-5 h-5" />
                </div>
                <div>
                  <div className="flex items-center gap-2">
                    <h3 className="text-sm font-bold text-white">
                      AI Web Portfolio Generator
                    </h3>
                    <span className="px-2 py-0.5 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-[10px] font-mono rounded-full font-bold">
                      PRO FEATURE
                    </span>
                  </div>
                  <p className="text-xs text-zinc-400">
                    Konversi instan CV Anda menjadi website portofolio interaktif responsif siap publikasi.
                  </p>
                </div>
              </div>

              <button
                onClick={() => setWebPortfolioOpen(false)}
                className="text-zinc-400 hover:text-white p-1"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <div className="p-6 space-y-5">
              
              {/* Actions & Theme Bar */}
              <div className="flex flex-wrap items-center justify-between gap-3 bg-zinc-950 p-3 rounded-lg border border-zinc-800">
                <div className="flex items-center gap-2">
                  <span className="text-xs text-zinc-400 font-mono">Pilih Tema:</span>
                  <button
                    onClick={() => { setPortfolioTheme('dark'); generatePortfolio('dark'); }}
                    className={`px-3 py-1 text-xs font-bold rounded transition ${portfolioTheme === 'dark' ? 'bg-indigo-600 text-white' : 'bg-zinc-800 text-zinc-400 hover:text-white'}`}
                  >
                    Dark Slate Modern
                  </button>
                  <button
                    onClick={() => { setPortfolioTheme('light'); generatePortfolio('light'); }}
                    className={`px-3 py-1 text-xs font-bold rounded transition ${portfolioTheme === 'light' ? 'bg-indigo-600 text-white' : 'bg-zinc-800 text-zinc-400 hover:text-white'}`}
                  >
                    Clean White Minimalist
                  </button>
                </div>

                <div className="flex items-center gap-2">
                  <button
                    onClick={downloadPortfolioHtml}
                    disabled={!portfolioData?.html}
                    className="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5 rounded shadow disabled:opacity-50"
                  >
                    <Download className="w-3.5 h-3.5" />
                    <span>Unduh index.html</span>
                  </button>
                  <button
                    onClick={() => { copyToClipboard(portfolioData?.html || ''); }}
                    disabled={!portfolioData?.html}
                    className="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold transition flex items-center gap-1.5 rounded disabled:opacity-50"
                  >
                    <Copy className="w-3.5 h-3.5" />
                    <span>Salin HTML</span>
                  </button>
                  <button
                    onClick={openPortfolioPreviewInTab}
                    disabled={!portfolioData?.html}
                    className="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1.5 rounded shadow disabled:opacity-50"
                  >
                    <Eye className="w-3.5 h-3.5" />
                    <span>Buka Layar Penuh ↗</span>
                  </button>
                </div>
              </div>

              {/* Live Preview Iframe */}
              <div className="border border-zinc-800 rounded-lg overflow-hidden bg-zinc-950 min-h-[450px]">
                {generatingPortfolio ? (
                  <div className="min-h-[450px] flex flex-col items-center justify-center gap-3 text-zinc-400 text-xs">
                    <div className="w-8 h-8 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin" />
                    <span>AI Sedang Merakit Website Portofolio Responsif...</span>
                  </div>
                ) : (
                  <iframe
                    title="Portfolio Preview"
                    srcDoc={portfolioData?.html || ''}
                    className="w-full h-[500px] border-0"
                    sandbox="allow-scripts"
                  />
                )}
              </div>

            </div>
          </div>
        </div>
      )}


      {/* ========================================================================= */}
      {/* MODAL 1: INTERACTIVE TOUR GUIDE WALKTHROUGH                              */}
      {/* ========================================================================= */}
      {tourModalOpen && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-xl shadow-2xl flex flex-col rounded-xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            {/* Header */}
            <div className="p-4 bg-gradient-to-r from-indigo-600 via-purple-600 to-amber-600 text-white flex items-center justify-between">
              <div className="flex items-center gap-2 font-bold text-sm">
                <HelpCircle className="w-5 h-5 text-amber-300" />
                <span>Panduan Interaktif Fitur CV Pro Studio</span>
              </div>
              <span className="font-mono text-xs px-2 py-0.5 bg-black/30 rounded-full">
                Langkah {tourStep + 1} dari 8
              </span>
            </div>

            {/* Tour Steps Content */}
            <div className="p-6 space-y-4 text-xs">
              {tourStep === 0 && (
                <div className="space-y-3">
                  <div className="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xl">
                    👋
                  </div>
                  <h3 className="text-base font-bold text-zinc-900 dark:text-white">
                    Selamat Datang di Neriah Pro CV Pro Studio!
                  </h3>
                  <p className="text-zinc-600 dark:text-zinc-300 leading-relaxed">
                    CV Pro Studio dirancang untuk mengakselerasi karir Anda ke level tertinggi. Mulai dari pembuat resume berstandar internasional, scanner ATS AI, asisten wawancara suara langsung (Voice Copilot), hingga generator grafis promo sosmed siap pakai.
                  </p>
                  <div className="p-3 bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded text-indigo-700 dark:text-indigo-300">
                    💡 <strong>Tips:</strong> Pengunjung gratis dapat mengetik seluruh isi resume dan mengunduh format PDF sepuasnya tanpa biaya!
                  </div>
                </div>
              )}

              {tourStep === 1 && (
                <div className="space-y-3">
                  <div className="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold">
                    <Palette className="w-5 h-5" />
                  </div>
                  <h3 className="text-sm font-bold text-zinc-900 dark:text-white">
                    1. Gaya & Format Visual (5 Preset Standar Industri)
                  </h3>
                  <p className="text-zinc-600 dark:text-zinc-300 leading-relaxed">
                    Pilih salah satu dari 5 template resume modern: <strong>Modern Minimalist</strong>, <strong>Executive Clean</strong>, <strong>Creative ATS (Sidebar)</strong>, <strong>Tech Dark (Terminal)</strong>, atau <strong>Compact Elegant (1 Halaman)</strong>. Sesuaikan font dan aksen warna favorit Anda.
                  </p>
                </div>
              )}

              {tourStep === 2 && (
                <div className="space-y-3">
                  <div className="w-10 h-10 rounded-lg bg-pink-100 dark:bg-pink-950/60 text-pink-600 dark:text-pink-400 flex items-center justify-center font-bold">
                    <Eye className="w-5 h-5" />
                  </div>
                  <h3 className="text-sm font-bold text-zinc-900 dark:text-white">
                    2. Foto Profil & Avatar Header Cropper
                  </h3>
                  <p className="text-zinc-600 dark:text-zinc-300 leading-relaxed">
                    Unggah foto profesional Anda langsung dari laptop atau HP. Manfaatkan fitur <strong>Crop Interaktif</strong> untuk memperbesar dan memusatkan wajah, serta pilih bentuk bingkai: Lingkaran, Kotak Bulat, atau Modern Blob.
                  </p>
                </div>
              )}

              {tourStep === 3 && (
                <div className="space-y-3">
                  <div className="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                    <Sliders className="w-5 h-5" />
                  </div>
                  <h3 className="text-sm font-bold text-zinc-900 dark:text-white">
                    3. Urutan Hirarki Bagian Resume (Reorder Sections)
                  </h3>
                  <p className="text-zinc-600 dark:text-zinc-300 leading-relaxed">
                    Anda memiliki kendali penuh atas susunan resume. Gunakan tombol panah <strong>▲ Pindah ke atas</strong> dan <strong>▼ Pindah ke bawah</strong> untuk mengatur apakah bagian Keahlian, Proyek, atau Pendidikan yang ingin ditampilkan lebih dahulu.
                  </p>
                </div>
              )}

              {tourStep === 4 && (
                <div className="space-y-3">
                  <div className="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                    <Sparkles className="w-5 h-5" />
                  </div>
                  <h3 className="text-sm font-bold text-zinc-900 dark:text-white">
                    4. AI ATS Audit & Penajaman Kata Kerja (Formula STAR)
                  </h3>
                  <p className="text-zinc-600 dark:text-zinc-300 leading-relaxed">
                    Audit skor resume secara real-time. Tombol <strong>⚡ AI Pertajam</strong> membantu mengubah kalimat pasif menjadi kata kerja aktif terukur, dan <strong>✂ Ringkas</strong> memastikan teks padat muat dalam 1 halaman.
                  </p>
                </div>
              )}

              {tourStep === 5 && (
                <div className="space-y-3">
                  <div className="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold">
                    <Headphones className="w-5 h-5" />
                  </div>
                  <h3 className="text-sm font-bold text-zinc-900 dark:text-white">
                    5. Asisten Wawancara Real-Time (Live Voice Copilot)
                  </h3>
                  <p className="text-zinc-600 dark:text-zinc-300 leading-relaxed">
                    Saat wawancara via Zoom/Google Meet berlangsung, aktifkan mikrofon. AI mendengarkan pertanyaan pewawancara dan seketika menampilkan contekkan <strong>STAR (Situation, Task, Action, Result)</strong> di layar Anda!
                  </p>
                </div>
              )}

              {tourStep === 6 && (
                <div className="space-y-3">
                  <div className="w-10 h-10 rounded-lg bg-pink-100 dark:bg-pink-950/60 text-pink-600 dark:text-pink-400 flex items-center justify-center font-bold">
                    <Share2 className="w-5 h-5" />
                  </div>
                  <h3 className="text-sm font-bold text-zinc-900 dark:text-white">
                    6. Social Media Promo Graphic & Caption Generator
                  </h3>
                  <p className="text-zinc-600 dark:text-zinc-300 leading-relaxed">
                    Umumkan ketersediaan Anda di LinkedIn dengan banner grafis 1200x630 yang elegan. Lengkap dengan lencana <em>ATS VERIFIED 98%</em> dan teks postingan siap salin!
                  </p>
                </div>
              )}

              {tourStep === 7 && (
                <div className="space-y-3">
                  <div className="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                    <FileDown className="w-5 h-5" />
                  </div>
                  <h3 className="text-sm font-bold text-zinc-900 dark:text-white">
                    7. Unduh PDF, Cetak & Portofolio Web Interaktif
                  </h3>
                  <p className="text-zinc-600 dark:text-zinc-300 leading-relaxed">
                    Selesai menyusun resume? Klik tombol <strong>Cetak / PDF</strong> untuk mencetak dokumen format A4 beresolusi tinggi, atau klik <strong>Web Portfolio</strong> untuk mengubah CV Anda menjadi website online interaktif!
                  </p>
                </div>
              )}
            </div>

            {/* Footer Navigation */}
            <div className="p-4 bg-zinc-50 dark:bg-zinc-800/80 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
              <button
                type="button"
                onClick={() => setTourModalOpen(false)}
                className="text-zinc-500 hover:text-zinc-800 dark:hover:text-white text-xs font-mono"
              >
                Lewati Tur
              </button>

              <div className="flex items-center gap-2">
                {tourStep > 0 && (
                  <button
                    type="button"
                    onClick={() => setTourStep(prev => prev - 1)}
                    className="px-3 py-1.5 bg-white dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-600 rounded text-xs font-bold"
                  >
                    &larr; Sebelumnya
                  </button>
                )}

                {tourStep < 7 ? (
                  <button
                    type="button"
                    onClick={() => setTourStep(prev => prev + 1)}
                    className="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded text-xs font-bold shadow"
                  >
                    Selanjutnya &rarr;
                  </button>
                ) : (
                  <button
                    type="button"
                    onClick={() => setTourModalOpen(false)}
                    className="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded text-xs font-bold shadow"
                  >
                    Selesai & Mulai Buat CV
                  </button>
                )}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ========================================================================= */}
      {/* MODAL 2: INTERACTIVE AVATAR PHOTO CROPPER MODAL                           */}
      {/* ========================================================================= */}
      {cropModalOpen && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-md shadow-2xl flex flex-col rounded-xl overflow-hidden">
            <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
              <h3 className="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                <Eye className="w-4 h-4 text-indigo-500" />
                <span>Crop & Sesuaikan Foto Anda</span>
              </h3>
              <button onClick={() => setCropModalOpen(false)} className="text-zinc-400 hover:text-zinc-600">
                <X className="w-4 h-4" />
              </button>
            </div>

            <div className="p-6 space-y-4 flex flex-col items-center">
              {/* Image Canvas Box */}
              <div className="w-64 h-64 bg-zinc-100 dark:bg-zinc-800 border-2 border-dashed border-zinc-300 dark:border-zinc-700 flex items-center justify-center overflow-hidden rounded-xl relative shadow-inner">
                {tempImageSrc ? (
                  <img
                    src={tempImageSrc}
                    alt="Source to Crop"
                    className="object-cover max-w-none transition-transform"
                    style={{
                      transform: `scale(${photoZoom})`,
                      width: '100%',
                      height: '100%'
                    }}
                  />
                ) : (
                  <span className="text-xs text-zinc-400">Tidak ada gambar</span>
                )}
                {/* Crop Overlay Grid Guide */}
                <div
                  className={`absolute inset-4 pointer-events-none border-2 border-indigo-500/80 shadow-[0_0_0_9999px_rgba(0,0,0,0.4)] ${photoStyle === 'circle' ? 'rounded-full' : (photoStyle === 'blob' ? 'rounded-[35%_65%_65%_35%/40%_40%_60%_60%]' : 'rounded-lg')}`}
                />
              </div>

              {/* Zoom Control Slider */}
              <div className="w-full space-y-1">
                <div className="flex justify-between text-xs text-zinc-600 dark:text-zinc-400 font-mono">
                  <span>Zoom / Pembesaran:</span>
                  <span>{photoZoom.toFixed(1)}x</span>
                </div>
                <input
                  type="range"
                  min="1"
                  max="3"
                  step="0.1"
                  value={photoZoom}
                  onChange={(e) => setPhotoZoom(parseFloat(e.target.value))}
                  className="w-full accent-indigo-600"
                />
              </div>

              {/* Shape Selection */}
              <div className="flex items-center gap-2 w-full pt-1">
                <span className="text-xs text-zinc-500 font-mono">Bentuk:</span>
                <button
                  type="button"
                  onClick={() => setPhotoStyle('circle')}
                  className={`flex-1 py-1 text-xs font-bold rounded border ${photoStyle === 'circle' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700'}`}
                >
                  Lingkaran
                </button>
                <button
                  type="button"
                  onClick={() => setPhotoStyle('rounded')}
                  className={`flex-1 py-1 text-xs font-bold rounded border ${photoStyle === 'rounded' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700'}`}
                >
                  Kotak Bulat
                </button>
                <button
                  type="button"
                  onClick={() => setPhotoStyle('blob')}
                  className={`flex-1 py-1 text-xs font-bold rounded border ${photoStyle === 'blob' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700'}`}
                >
                  Modern Blob
                </button>
              </div>
            </div>

            <div className="p-4 bg-zinc-50 dark:bg-zinc-800/80 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-end gap-2">
              <button
                type="button"
                onClick={() => setCropModalOpen(false)}
                id="cancel-crop-btn"
                className="px-3 py-1.5 text-xs text-zinc-600 dark:text-zinc-400 hover:underline"
              >
                Batal
              </button>
              <button
                type="button"
                onClick={applyCropPhoto}
                id="crop-btn"
                className="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded shadow"
              >
                Crop & Gunakan Foto
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ========================================================================= */}
      {/* MODAL 3: INTERVIEW TRANSCRIPT PASTE & STAR ANALYSIS MODAL                 */}
      {/* ========================================================================= */}
      {transcriptModalOpen && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl flex flex-col rounded-xl">
            <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
              <div className="flex items-center gap-2">
                <FileText className="w-5 h-5 text-indigo-500" />
                <h3 className="text-sm font-bold text-zinc-900 dark:text-white">
                  Analisis Transkrip Wawancara (Recruiter Call / Mock Interview)
                </h3>
              </div>
              <button onClick={() => setTranscriptModalOpen(false)} className="text-zinc-400 hover:text-zinc-600">
                <X className="w-4 h-4" />
              </button>
            </div>

            <div className="p-6 space-y-4 text-xs">
              <p className="text-zinc-600 dark:text-zinc-300 leading-relaxed">
                Tempelkan percakapan atau transkrip wawancara Anda dari Zoom, Google Meet, rekaman audio, atau panggilan telepon. AI akan mengevaluasi jawaban Anda dan menyusun respon STAR yang ideal.
              </p>

              <div>
                <label className="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">
                  Transkrip Percakapan Wawancara:
                </label>
                <textarea
                  rows={5}
                  value={pastedTranscript}
                  onChange={(e) => setPastedTranscript(e.target.value)}
                  placeholder="Pewawancara: Ceritakan pengalaman Anda saat memimpin migrasi basis data?\nSaya: Kami memigrasikan database ke cloud dan latency berkurang 50%..."
                  className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-3 text-xs text-zinc-900 dark:text-zinc-100 font-mono rounded"
                />
              </div>

              <button
                type="button"
                onClick={handleRunTranscriptAnalysis}
                disabled={analyzingTranscript || !pastedTranscript.trim()}
                id="run-transcript-analysis-btn"
                className="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded shadow flex items-center justify-center gap-2 disabled:opacity-50"
              >
                <Sparkles className="w-4 h-4" />
                <span>{analyzingTranscript ? 'AI Sedang Menganalisis Transkrip...' : 'Jalankan Analisis Transkrip STAR'}</span>
              </button>

              {/* Analysis Result */}
              {transcriptResult && (
                <div className="mt-4 p-4 bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 rounded-lg space-y-3">
                  <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-700 pb-2">
                    <span className="font-bold text-zinc-900 dark:text-white uppercase font-mono">
                      Rating Jawaban: <span className="text-indigo-600 dark:text-indigo-400">{transcriptResult.overall_rating}</span>
                    </span>
                    <span className="text-[10px] text-zinc-500 font-mono">
                      {transcriptResult.transcript_length} Kata Terdeteksi
                    </span>
                  </div>

                  <div className="space-y-2">
                    <div className="font-bold text-emerald-600 dark:text-emerald-400">✓ Kekuatan Jawaban:</div>
                    <ul className="list-disc list-outside ml-4 text-zinc-700 dark:text-zinc-300 space-y-1">
                      {(transcriptResult.strengths || []).map((st, i) => (
                        <li key={i}>{st}</li>
                      ))}
                    </ul>
                  </div>

                  <div className="space-y-2">
                    <div className="font-bold text-amber-600 dark:text-amber-400">⚡ Area Peningkatan:</div>
                    <ul className="list-disc list-outside ml-4 text-zinc-700 dark:text-zinc-300 space-y-1">
                      {(transcriptResult.improvements || []).map((imp, i) => (
                        <li key={i}>{imp}</li>
                      ))}
                    </ul>
                  </div>

                  {transcriptResult.recommended_star_response && (
                    <div className="p-3 bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded space-y-1.5">
                      <div className="font-bold text-indigo-700 dark:text-indigo-300 font-mono text-[11px] uppercase">
                        Rekomendasi Jawaban STAR Ideal:
                      </div>
                      <div className="text-zinc-700 dark:text-zinc-300 space-y-1">
                        <div><strong>Situation:</strong> {transcriptResult.recommended_star_response.situation}</div>
                        <div><strong>Task:</strong> {transcriptResult.recommended_star_response.task}</div>
                        <div><strong>Action:</strong> {transcriptResult.recommended_star_response.action}</div>
                        <div><strong>Result:</strong> {transcriptResult.recommended_star_response.result}</div>
                      </div>
                    </div>
                  )}
                </div>
              )}
            </div>
          </div>
        </div>
      )}

      </div>
    </div>
  );
}
