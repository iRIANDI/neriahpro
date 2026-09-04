import React from 'react';
import { Layers, ShieldCheck, Terminal, ArrowUpRight, MessageCircle } from 'lucide-react';

export default function FooterIsland({ settings }) {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 border-t border-zinc-200 dark:border-zinc-800 pt-16 pb-12 transition-colors font-sans">
      <div className="max-w-7xl mx-auto px-4 sm:px-6">
        
        {/* Top Branding & Mission */}
        <div className="grid grid-cols-1 md:grid-cols-12 gap-10 pb-12 border-b border-zinc-200 dark:border-zinc-800">
          
          <div className="md:col-span-5">
            <a href="/" className="inline-flex items-center gap-2 text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-4">
              <span className="w-7 h-7 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black flex items-center justify-center text-xs font-mono font-bold rounded-none">
                N
              </span>
              <span>NERIAH<span className="text-emerald-500">PRO</span> // HUB</span>
            </a>
            <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed max-w-sm mb-6">
              Pusat arsitektur dan rekayasa perangkat lunak berskala tinggi. Membantu bisnis merancang spesifikasi PRD, skema database, penguncian kontrak, dan implementasi aplikasi modern tanpa batasan.
            </p>
            <div className="inline-flex items-center gap-2 px-3 py-1.5 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 text-[11px] font-mono uppercase tracking-wider rounded-none">
              <span className="w-2 h-2 bg-emerald-500 rounded-none animate-pulse"></span>
              <span>STANDAR REKAYASA SISTEM TERUJI</span>
            </div>
          </div>

          {/* 4 Pillars Services Hub */}
          <div className="md:col-span-3">
            <h4 className="font-mono font-bold uppercase tracking-widest text-xs text-zinc-400 dark:text-zinc-500 mb-4">
              Pilar Layanan
            </h4>
            <ul className="space-y-2.5 font-mono text-xs">
              <li>
                <a href="/blueprint" className="hover:text-emerald-500 transition-colors flex items-center gap-1">
                  <span>Project OS & Blueprint</span>
                  <ArrowUpRight className="w-3 h-3 text-zinc-400" />
                </a>
              </li>
              <li>
                <a href="https://wa.me/628123456789?text=Halo%20Neriah%20Pro,%20saya%20tertarik%20dengan%20Canva%20Style%20CV%20Studio" target="_blank" rel="noopener noreferrer" className="hover:text-emerald-500 transition-colors flex items-center gap-1">
                  <span>CV & Portfolio Studio</span>
                  <ArrowUpRight className="w-3 h-3 text-zinc-400" />
                </a>
              </li>
              <li>
                <a href="https://wa.me/628123456789?text=Halo%20Neriah%20Pro,%20saya%20tertarik%20dengan%20Digital%20Contract%20dan%20Scope%20Lock" target="_blank" rel="noopener noreferrer" className="hover:text-emerald-500 transition-colors flex items-center gap-1">
                  <span>Kontrak Digital & E-Sign</span>
                  <ArrowUpRight className="w-3 h-3 text-zinc-400" />
                </a>
              </li>
              <li>
                <a href="https://wa.me/628123456789?text=Halo%20Neriah%20Pro,%20saya%20butuh%20pengembangan%20Enterprise%20Rapid%20Monolith" target="_blank" rel="noopener noreferrer" className="hover:text-emerald-500 transition-colors flex items-center gap-1">
                  <span>Enterprise Rapid Monolith</span>
                  <ArrowUpRight className="w-3 h-3 text-zinc-400" />
                </a>
              </li>
            </ul>
          </div>

          {/* Technical Architecture Standards */}
          <div className="md:col-span-2">
            <h4 className="font-mono font-bold uppercase tracking-widest text-xs text-zinc-400 dark:text-zinc-500 mb-4">
              Standar Arsitektur
            </h4>
            <ul className="space-y-2.5 font-mono text-xs text-zinc-600 dark:text-zinc-400">
              <li className="flex items-center gap-1.5">
                <span className="w-1.5 h-1.5 bg-emerald-500"></span>
                <span>Laravel 13 Monolith</span>
              </li>
              <li className="flex items-center gap-1.5">
                <span className="w-1.5 h-1.5 bg-emerald-500"></span>
                <span>PostgreSQL Strict ULID</span>
              </li>
              <li className="flex items-center gap-1.5">
                <span className="w-1.5 h-1.5 bg-emerald-500"></span>
                <span>O(1) Keystone Cursor</span>
              </li>
              <li className="flex items-center gap-1.5">
                <span className="w-1.5 h-1.5 bg-emerald-500"></span>
                <span>Dedicated Nixpacks VPS</span>
              </li>
            </ul>
          </div>

          {/* Direct WhatsApp Channel & Admin Portal */}
          <div className="md:col-span-2">
            <h4 className="font-mono font-bold uppercase tracking-widest text-xs text-zinc-400 dark:text-zinc-500 mb-4">
              Koneksi Cepat
            </h4>
            <div className="space-y-3 font-mono text-xs">
              <a
                href="https://wa.me/628123456789?text=Halo%20Neriah%20Pro,%20saya%20ingin%20konsultasi%20proyek%20teknologi"
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-black font-bold px-3 py-2 rounded-none transition w-full justify-center"
              >
                <MessageCircle className="w-3.5 h-3.5" />
                <span>Chat WhatsApp</span>
              </a>
              <a
                href="/admin/login"
                className="inline-flex items-center gap-2 border border-zinc-300 dark:border-zinc-700 hover:border-zinc-900 dark:hover:border-zinc-500 text-zinc-700 dark:text-zinc-300 px-3 py-2 rounded-none transition w-full justify-center"
              >
                <Terminal className="w-3.5 h-3.5" />
                <span>Portal Admin</span>
              </a>
            </div>
          </div>

        </div>

        {/* Bottom Legal & Copyright Bar */}
        <div className="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-mono text-zinc-500 dark:text-zinc-400">
          <p>&copy; {currentYear} NERIAH PRO. Seluruh hak cipta dilindungi undang-undang.</p>
          <div className="flex items-center gap-6">
            <span className="text-zinc-400 dark:text-zinc-500">POSTGRESQL STRICT ULID // ZERO DEPENDENCY LOCK</span>
            <span className="text-emerald-600 dark:text-emerald-400 font-bold uppercase">SYSTEM ARMED</span>
          </div>
        </div>

      </div>
    </footer>
  );
}
