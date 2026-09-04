import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { 
  Cpu, 
  ArrowRight, 
  ShieldCheck, 
  Sparkles, 
  CheckCircle2, 
  Zap,
  Terminal,
  Database
} from 'lucide-react';

export default function HeroIsland({ headline, subheadline, cta_text, cta_link }) {
  const [quickProjectName, setQuickProjectName] = useState('');
  const [quickProblem, setQuickProblem] = useState('');

  const handleQuickSubmit = (e) => {
    e.preventDefault();
    const params = new URLSearchParams();
    if (quickProjectName) params.set('name', quickProjectName);
    if (quickProblem) params.set('problem', quickProblem);
    window.location.href = `/blueprint?${params.toString()}`;
  };

  return (
    <section className="relative w-full pt-32 pb-20 px-4 sm:px-6 bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 border-b border-zinc-200 dark:border-zinc-800 transition-colors font-sans overflow-hidden">
      
      {/* Background Subtle Tech Grid */}
      <div className="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none" />

      <div className="max-w-6xl mx-auto relative z-10">
        
        {/* Status Pill */}
        <div className="inline-flex items-center gap-2 px-3 py-1 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase tracking-widest mb-6 rounded-none">
          <span className="w-2 h-2 bg-emerald-500 rounded-none animate-pulse"></span>
          <span>NERIAH PRO // DIGITAL SERVICES HUB & ARCHITECTURE PLATFORM</span>
        </div>

        {/* Main Headline */}
        <h1 className="text-3xl sm:text-5xl md:text-7xl font-black uppercase tracking-tight leading-[1.05] mb-6 text-zinc-900 dark:text-white font-sans max-w-5xl">
          PUSAT ARSITEKTUR & REKAYASA DIGITAL UNTUK PROYEK BERSKALA TINGGI.
        </h1>

        {/* Subheadline */}
        <p className="text-sm sm:text-lg md:text-xl text-zinc-600 dark:text-zinc-400 font-sans max-w-3xl leading-relaxed mb-10">
          Ubah visi bisnis Anda menjadi <strong>Product Requirements Document (PRD)</strong> lengkap, skema basis data <strong>ERD PostgreSQL Strict ULID</strong>, alur kerja bertahap, dan penguncian kontrak kerja sama dalam hitungan menit.
        </p>

        {/* INTERACTIVE DISCOVERY SIMULATOR (HIGH RETENTION WIDGET) */}
        <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 p-6 sm:p-8 mb-12 rounded-none shadow-none max-w-4xl">
          <div className="flex items-center gap-2 font-mono text-xs uppercase tracking-wider text-emerald-600 dark:text-emerald-400 font-bold mb-4">
            <Terminal className="w-4 h-4" />
            <span>INTERACTIVE DISCOVERY SIMULATOR // GENERATE PRD LIVE</span>
          </div>

          <form onSubmit={handleQuickSubmit} className="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div className="sm:col-span-5">
              <input
                type="text"
                placeholder="Nama Proyek (Misal: Sistem Logistik Express)"
                value={quickProjectName}
                onChange={(e) => setQuickProjectName(e.target.value)}
                className="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-xs font-mono rounded-none focus:border-emerald-500 outline-none"
              />
            </div>
            <div className="sm:col-span-4">
              <input
                type="text"
                placeholder="Masalah Utama (Misal: Rekap manual lambat)"
                value={quickProblem}
                onChange={(e) => setQuickProblem(e.target.value)}
                className="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-xs font-mono rounded-none focus:border-emerald-500 outline-none"
              />
            </div>
            <div className="sm:col-span-3">
              <button
                type="submit"
                className="w-full h-full bg-zinc-900 hover:bg-black dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white dark:text-black font-mono text-xs font-black uppercase tracking-wider py-3 px-4 rounded-none transition flex items-center justify-center gap-2"
              >
                <span>Sintesis PRD</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </button>
            </div>
          </form>

          <p className="text-[11px] text-zinc-500 dark:text-zinc-400 mt-3 font-mono">
            &bull; Gratis & instan: Menghasilkan skema tabel ERD, rincian MVP vs Roadmap, dan estimasi sprint.
          </p>
        </div>

        {/* Dual Actions */}
        <div className="flex flex-wrap items-center gap-4 mb-16 font-mono text-xs uppercase font-bold tracking-wider">
          <a
            href="/blueprint"
            className="bg-emerald-600 hover:bg-emerald-500 text-black font-black py-4 px-8 rounded-none transition flex items-center gap-2"
          >
            <span>Mulai Blueprint Lengkap</span>
            <ArrowRight className="w-4 h-4" />
          </a>
          <a
            href="#services"
            className="border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 py-4 px-8 rounded-none transition"
          >
            Jelajahi 4 Pilar Layanan &darr;
          </a>
        </div>

        {/* Architecture Precision Trust Metrics */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 border-t border-zinc-200 dark:border-zinc-800 pt-8 text-xs font-mono">
          <div className="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <span className="text-zinc-400 block text-[10px] uppercase">ARSITEKTUR CORE</span>
            <span className="font-bold text-zinc-900 dark:text-white text-sm">Laravel 13 Monolith</span>
          </div>
          <div className="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <span className="text-zinc-400 block text-[10px] uppercase">STANDAR DATABASE</span>
            <span className="font-bold text-emerald-600 dark:text-emerald-400 text-sm">PostgreSQL Strict ULID</span>
          </div>
          <div className="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <span className="text-zinc-400 block text-[10px] uppercase">ALGORITMA PAGINASI</span>
            <span className="font-bold text-zinc-900 dark:text-white text-sm">O(1) Keystone Cursor</span>
          </div>
          <div className="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <span className="text-zinc-400 block text-[10px] uppercase">INFRASTRUKTUR</span>
            <span className="font-bold text-zinc-900 dark:text-white text-sm">Dedicated Docker VPS</span>
          </div>
        </div>

      </div>
    </section>
  );
}
