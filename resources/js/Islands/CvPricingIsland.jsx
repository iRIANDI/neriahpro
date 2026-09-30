import React, { useState } from 'react';
import { 
  Check, 
  Sparkles, 
  Zap, 
  Crown, 
  ShieldCheck, 
  HelpCircle, 
  ArrowRight, 
  Layers, 
  Mic, 
  Briefcase, 
  FileText,
  Lock,
  Globe,
  Radio
} from 'lucide-react';

export default function CvPricingIsland({ 
  headline = 'INVESTASI KARIR IMPIAN // PILIHAN KELAS & KUOTA CV PRO',
  subheadline = 'Pilih paket yang sesuai dengan akselerasi karir Anda. Pengunjung gratis tetap dapat mengisi form secara manual dan mengunduh PDF secara cuma-cuma.',
  whatsappNumber = '628123456789'
}) {
  const [billingCycle, setBillingCycle] = useState('monthly'); // 'monthly' | 'alacarte'

  const plans = [
    {
      id: 'starter_free',
      name: 'Starter (Free)',
      price: 'Rp 0',
      period: 'Gratis Selamanya',
      tag: 'FREE FOREVER',
      tagColor: 'bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300',
      description: 'Ideal untuk mahasiswa dan pencari kerja yang ingin menyusun CV rapi secara manual tanpa biaya.',
      features: [
        { text: 'Akses Penuh Manual CV Editor', included: true },
        { text: 'Unduh & Cetak PDF Kualitas Tinggi', included: true },
        { text: 'Pilihan 4+ Template Desain ATS', included: true },
        { text: 'Export & Import Backup JSON', included: true },
        { text: '1x Audit ATS Kata Kunci Dasar', included: true },
        { text: 'AI Otomatis Tailor ke Loker', included: false, locked: true },
        { text: 'AI Mock Interview Simulator', included: false, locked: true },
        { text: 'Asisten Wawancara Real-Time', included: false, locked: true },
        { text: 'AI Web Portfolio Generator', included: false, locked: true },
      ],
      ctaText: 'Mulai Manual & Unduh Gratis',
      ctaLink: '/cv-pro',
      isPopular: false,
    },
    {
      id: 'pro_career',
      name: 'Pro Career (Tier A)',
      price: 'Rp 49.000',
      period: '/ bulan',
      tag: 'POPULER UNTUK JOB SEEKER',
      tagColor: 'bg-indigo-500 text-white',
      description: 'Optimalkan setiap lamaran kerja dengan penyesuaian otomatis AI terhadap deskripsi loker.',
      features: [
        { text: 'Semua Fitur Manual & Unduh PDF', included: true },
        { text: '5 Resume CV Berbeda Sekaligus', included: true },
        { text: '10x Penyesuaian CV AI per Loker (Diff)', included: true },
        { text: '5x Sesi Simulasi Wawancara AI (STAR)', included: true },
        { text: '5x Audit Kualitas ATS & Verbs', included: true },
        { text: 'LinkedIn Personal Branding Suite', included: true },
        { text: 'AI Humanizer & Bullet Condenser', included: true },
        { text: '50 AI Booster Credits', included: true },
        { text: 'Asisten Wawancara Real-Time', included: false, locked: true },
      ],
      ctaText: 'Pilih Pro Career',
      ctaLink: '/cv-pro?plan=pro_career',
      isPopular: false,
    },
    {
      id: 'ultimate_exec',
      name: 'Ultimate Executive (Tier B)',
      price: 'Rp 99.000',
      period: '/ bulan',
      tag: 'BEST VALUE // REKOMENDASI',
      tagColor: 'bg-emerald-500 text-black font-black',
      description: 'Paket lengkap profesional karir: AI Copilot wawancara live, tailoring tanpa batas, dan web portfolio.',
      features: [
        { text: 'Semua Fitur Pro Career', included: true },
        { text: 'Unlimited CV Resume Creation', included: true },
        { text: '30x Penyesuaian CV AI per Loker', included: true },
        { text: '15x Simulasi Wawancara Voice & Audio', included: true },
        { text: 'Asisten Wawancara Real-Time (Live Mic)', included: true, highlight: true },
        { text: 'AI Web Portfolio Generator 1-Klik', included: true, highlight: true },
        { text: 'Cover Letter & Outreach Suite', included: true },
        { text: '150 AI Booster Credits', included: true },
        { text: 'Prioritas Antrean AI Processing', included: true },
      ],
      ctaText: 'Pilih Ultimate Executive',
      ctaLink: '/cv-pro?plan=ultimate_exec',
      isPopular: true,
    },
    {
      id: 'vip_sprint',
      name: 'VIP Sprint Pass (Tier C)',
      price: 'Rp 199.000',
      period: '/ 3 bulan (Akses Penuh)',
      tag: 'ULTRA INTENSIF',
      tagColor: 'bg-amber-500 text-black font-bold',
      description: 'Sprint 90 hari intensif untuk mengamankan pekerjaan impian dengan kuota AI raksasa.',
      features: [
        { text: 'Semua Fitur Ultimate Executive', included: true },
        { text: '100x Penyesuaian CV AI per Loker', included: true },
        { text: '50x Simulasi Wawancara AI Lengkap', included: true },
        { text: 'Asisten Wawancara Real-Time Tanpa Batas', included: true },
        { text: '500 AI Booster Credits', included: true },
        { text: 'Konsultasi Teknis & Format via WhatsApp', included: true },
        { text: 'Masa Aktif 90 Hari Penuh', included: true },
      ],
      ctaText: 'Pilih VIP Sprint Pass',
      ctaLink: '/cv-pro?plan=vip_sprint',
      isPopular: false,
    }
  ];

  const topups = [
    {
      name: 'Top-up 5 Mock Interview',
      price: 'Rp 29.000',
      desc: '+5 Sesi latihan simulasi wawancara suara dan evaluasi STAR method.',
      badge: 'PERMANEN // TANPA EXPIRED',
      icon: Mic,
      ctaLink: '/cv-pro?topup=mock_interviews'
    },
    {
      name: 'Top-up 10 Tailored CV',
      price: 'Rp 19.000',
      desc: '+10 Kuota tailoring penyesuaian CV terhadap kata kunci loker impian.',
      badge: 'PERMANEN // TANPA EXPIRED',
      icon: Briefcase,
      ctaLink: '/cv-pro?topup=tailor_cv'
    },
    {
      name: 'Top-up 100 AI Booster',
      price: 'Rp 15.000',
      desc: '+100 Kredit kecerdasan buatan serbaguna (Summary, Bullet Enhancer, Condenser).',
      badge: 'PERMANEN // TANPA EXPIRED',
      icon: Sparkles,
      ctaLink: '/cv-pro?topup=ai_credits'
    }
  ];

  return (
    <section className="py-20 px-4 sm:px-6 max-w-7xl mx-auto font-sans text-zinc-900 dark:text-zinc-100 transition-colors">
      
      {/* Section Header */}
      <div className="text-center max-w-3xl mx-auto mb-16">
        <div className="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/10 border border-indigo-500/30 text-indigo-600 dark:text-indigo-400 font-mono text-xs uppercase tracking-wider mb-4 font-bold">
          <Crown className="w-3.5 h-3.5" />
          <span>TRANSPARAN // TANPA BIAYA TERSEMBUNYI</span>
        </div>
        <h2 className="text-3xl sm:text-5xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-4">
          {headline}
        </h2>
        <p className="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 leading-relaxed">
          {subheadline}
        </p>

        {/* Free Tier Notice Banner */}
        <div className="mt-6 p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs sm:text-sm font-medium flex items-center justify-center gap-2 max-w-2xl mx-auto">
          <Check className="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
          <span>
            <strong>Jaminan Akses Gratis:</strong> Siapa pun dapat mengisi seluruh form CV secara manual dan mengunduh format PDF secara 100% gratis tanpa batasan.
          </span>
        </div>
      </div>

      {/* Pricing Cards Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-20">
        {plans.map((plan) => (
          <div 
            key={plan.id}
            className={`flex flex-col justify-between border transition relative p-6 bg-white dark:bg-zinc-900 ${
              plan.isPopular 
                ? 'border-emerald-500 shadow-xl dark:shadow-emerald-950/20 ring-1 ring-emerald-500' 
                : 'border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-700'
            }`}
          >
            {/* Top Badge */}
            {plan.tag && (
              <div className="mb-4">
                <span className={`inline-block text-[10px] font-mono px-2.5 py-1 uppercase tracking-wider ${plan.tagColor}`}>
                  {plan.tag}
                </span>
              </div>
            )}

            <div>
              <h3 className="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-1">
                {plan.name}
              </h3>
              <p className="text-xs text-zinc-500 dark:text-zinc-400 min-h-[40px] mb-4 leading-relaxed">
                {plan.description}
              </p>

              {/* Price Display */}
              <div className="pb-6 mb-6 border-b border-zinc-200 dark:border-zinc-800">
                <div className="flex items-baseline gap-1">
                  <span className="text-3xl sm:text-4xl font-black font-mono tracking-tight text-zinc-900 dark:text-white">
                    {plan.price}
                  </span>
                  <span className="text-xs text-zinc-500 font-mono">
                    {plan.period}
                  </span>
                </div>
              </div>

              {/* Features List */}
              <ul className="space-y-3 mb-8 text-xs font-sans">
                {plan.features.map((feat, idx) => (
                  <li key={idx} className="flex items-start gap-2">
                    {feat.included ? (
                      <Check className={`w-4 h-4 shrink-0 mt-0.5 ${feat.highlight ? 'text-emerald-500 font-bold' : 'text-indigo-500'}`} />
                    ) : (
                      <Lock className="w-3.5 h-3.5 text-zinc-400 dark:text-zinc-600 shrink-0 mt-0.5" />
                    )}
                    <span className={`leading-relaxed ${
                      !feat.included 
                        ? 'text-zinc-400 dark:text-zinc-600 line-through' 
                        : (feat.highlight ? 'font-bold text-zinc-900 dark:text-white' : 'text-zinc-700 dark:text-zinc-300')
                    }`}>
                      {feat.text}
                    </span>
                  </li>
                ))}
              </ul>
            </div>

            {/* CTA Button */}
            <a
              href={plan.ctaLink}
              className={`w-full py-3 px-4 text-xs font-mono font-bold uppercase tracking-wider flex items-center justify-center gap-2 transition rounded-none ${
                plan.isPopular
                  ? 'bg-emerald-500 hover:bg-emerald-400 text-black'
                  : (plan.id === 'starter_free' 
                      ? 'border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-900 dark:text-white'
                      : 'bg-indigo-600 hover:bg-indigo-500 text-white')
              }`}
            >
              <span>{plan.ctaText}</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </a>
          </div>
        ))}
      </div>

      {/* A LA CARTE TOP-UP SECTION */}
      <div className="border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 p-8 mb-20">
        <div className="max-w-2xl mb-8">
          <div className="inline-flex items-center gap-2 font-mono text-xs uppercase tracking-wider text-amber-600 dark:text-amber-400 font-bold mb-2">
            <Zap className="w-4 h-4" />
            <span>PAKET TOP-UP KUOTA A LA CARTE</span>
          </div>
          <h3 className="text-2xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2">
            Beli Kuota Tambahan Sesuai Kebutuhan.
          </h3>
          <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">
            Kredit dan kuota top-up berlaku permanen dan tidak akan pernah kedaluwarsa. Gunakan kapan saja saat Anda membutuhkannya.
          </p>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
          {topups.map((top, idx) => {
            const Icon = top.icon;
            return (
              <div key={idx} className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 flex flex-col justify-between">
                <div>
                  <div className="flex items-center justify-between mb-3">
                    <div className="w-8 h-8 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                      <Icon className="w-4 h-4" />
                    </div>
                    <span className="text-[10px] font-mono px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold">
                      {top.badge}
                    </span>
                  </div>
                  <h4 className="text-sm font-bold text-zinc-900 dark:text-white mb-1">
                    {top.name}
                  </h4>
                  <div className="text-xl font-black font-mono text-indigo-600 dark:text-indigo-400 mb-3">
                    {top.price}
                  </div>
                  <p className="text-xs text-zinc-500 leading-relaxed mb-6">
                    {top.desc}
                  </p>
                </div>
                <a
                  href={top.ctaLink}
                  className="w-full py-2.5 px-3 bg-zinc-900 dark:bg-zinc-800 hover:bg-zinc-800 dark:hover:bg-zinc-700 text-white text-xs font-mono font-bold uppercase tracking-wider text-center transition"
                >
                  Beli Top-Up Ini
                </a>
              </div>
            );
          })}
        </div>
      </div>

      {/* FREQUENTLY ASKED QUESTIONS */}
      <div className="max-w-3xl mx-auto border-t border-zinc-200 dark:border-zinc-800 pt-16">
        <h3 className="text-2xl font-black uppercase tracking-tight text-center text-zinc-900 dark:text-white mb-8">
          Pertanyaan Seputar Akses & Fitur AI
        </h3>
        
        <div className="space-y-4 text-xs sm:text-sm">
          <div className="border border-zinc-200 dark:border-zinc-800 p-5 bg-white dark:bg-zinc-900">
            <h4 className="font-bold text-zinc-900 dark:text-white mb-2 flex items-center gap-2">
              <HelpCircle className="w-4 h-4 text-indigo-500 shrink-0" />
              <span>Apakah pengunjung yang belum membeli tetap bisa membuat dan mengunduh CV?</span>
            </h4>
            <p className="text-zinc-600 dark:text-zinc-400 leading-relaxed pl-6">
              <strong>YA, BISA 100% GRATIS!</strong> Anda dapat mengisi seluruh riwayat pengalaman, pendidikan, profil, keahlian, memilih berbagai template desain ATS, dan mengunduh file PDF secara cuma-cuma tanpa ada batasan waktu.
            </p>
          </div>

          <div className="border border-zinc-200 dark:border-zinc-800 p-5 bg-white dark:bg-zinc-900">
            <h4 className="font-bold text-zinc-900 dark:text-white mb-2 flex items-center gap-2">
              <HelpCircle className="w-4 h-4 text-indigo-500 shrink-0" />
              <span>Apa perbedaan utama paket berbayar dibandingkan tier gratis?</span>
            </h4>
            <p className="text-zinc-600 dark:text-zinc-400 leading-relaxed pl-6">
              Paket berbayar membuka seluruh otomatisasi kecerdasan buatan (AI): penyesuaian kata kunci CV otomatis terhadap deskripsi lowongan kerja (Tailored CV with side-by-side Diff), simulator wawancara suara (Mock Interview STAR method), Copilot wawancara langsung (Real-Time Voice Assistant), generator LinkedIn branding, dan audit ATS mendalam.
            </p>
          </div>

          <div className="border border-zinc-200 dark:border-zinc-800 p-5 bg-white dark:bg-zinc-900">
            <h4 className="font-bold text-zinc-900 dark:text-white mb-2 flex items-center gap-2">
              <HelpCircle className="w-4 h-4 text-indigo-500 shrink-0" />
              <span>Bagaimana cara kerja fitur Asisten Wawancara Real-Time (Live Copilot)?</span>
            </h4>
            <p className="text-zinc-600 dark:text-zinc-400 leading-relaxed pl-6">
              Fitur eksklusif ini menggunakan mikrofon laptop/perangkat Anda untuk mendengarkan pertanyaan yang diucapkan pewawancara secara live. AI secara instan memunculkan transkrip dan menyusun contekan poin jawaban terstruktur dengan metode STAR (Situation, Task, Action, Result) di layar Anda secara real-time.
            </p>
          </div>
        </div>
      </div>

    </section>
  );
}
