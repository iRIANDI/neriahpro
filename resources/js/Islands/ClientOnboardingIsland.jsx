import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { ArrowRight, ArrowLeft, CheckCircle2, ShieldCheck, Terminal, Layers } from 'lucide-react';

const steps = [
  { id: 'intro', title: 'Identitas Proyek', desc: 'Siapa Anda dan apa nama entitas Anda?' },
  { id: 'vision', title: 'Visi & Target Masalah', desc: 'Apa masalah mendesak yang diselesaikan?' },
  { id: 'budget', title: 'Alokasi & Timeline', desc: 'Berapa estimasi investasi & target rilis?' },
  { id: 'consent', title: 'Protokol Keamanan Data', desc: 'Finalisasi dan persetujuan pengolahan ide' }
];

export default function ClientOnboardingIsland({ csrfToken, submitUrl }) {
  const [currentStep, setCurrentStep] = useState(0);
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    company_name: '',
    project_needs: {},
    budget_range: '',
    privacy_consent_agreed: false
  });

  const [visionText, setVisionText] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isSuccess, setIsSuccess] = useState(false);

  const nextStep = () => setCurrentStep((prev) => Math.min(prev + 1, steps.length - 1));
  const prevStep = () => setCurrentStep((prev) => Math.max(prev - 1, 0));

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    if (name === 'vision') {
      setVisionText(value);
      setFormData(prev => ({ ...prev, project_needs: { ...prev.project_needs, vision: value } }));
    } else {
      setFormData(prev => ({
        ...prev,
        [name]: type === 'checkbox' ? checked : value
      }));
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!formData.privacy_consent_agreed) {
      alert("Harap setujui komitmen kerahasiaan dan privasi data.");
      return;
    }
    
    setIsSubmitting(true);
    
    try {
      const response = await fetch(submitUrl || '/api/onboarding', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify(formData)
      });
      
      if (response.ok) {
        setIsSuccess(true);
      } else {
        console.error("Submission failed.");
        alert("Gagal mengirim data. Silakan coba lagi atau hubungi via WhatsApp.");
      }
    } catch (err) {
      console.error(err);
      alert("Terjadi kendala jaringan.");
    } finally {
      setIsSubmitting(false);
    }
  };

  if (isSuccess) {
    return (
      <section className="py-20 px-4 sm:px-6 max-w-4xl mx-auto font-sans">
        <div className="p-8 sm:p-12 border-2 border-emerald-500 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white rounded-none shadow-none text-center">
          <div className="w-12 h-12 bg-emerald-500 text-black flex items-center justify-center mx-auto mb-6 rounded-none font-bold">
            <CheckCircle2 className="w-7 h-7" />
          </div>
          <span className="text-xs font-mono font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 block mb-2">
            INITIALIZATION COMPLETE // DATA ENCRYPTED
          </span>
          <h2 className="text-2xl sm:text-4xl font-black uppercase tracking-tight mb-4">
            Ide & Kebutuhan Berhasil Diterima.
          </h2>
          <p className="text-sm text-zinc-600 dark:text-zinc-400 max-w-lg mx-auto leading-relaxed mb-8 font-sans">
            Data Anda telah diamankan dengan standar PostgreSQL Strict ULID. Tim teknis Neriah Pro akan menyusun draf proposal arsitektur awal dalam 1x24 jam kerja.
          </p>
          <div className="flex flex-wrap justify-center gap-4 font-mono text-xs uppercase font-bold">
            <a
              href="/blueprint"
              className="bg-emerald-600 hover:bg-emerald-500 text-black px-6 py-3.5 rounded-none transition flex items-center gap-2"
            >
              <span>Buka PRD Blueprint Generator &rarr;</span>
            </a>
            <a
              href="/"
              className="border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 px-6 py-3.5 rounded-none transition"
            >
              Kembali ke Beranda
            </a>
          </div>
        </div>
      </section>
    );
  }

  return (
    <section className="py-20 px-4 sm:px-6 max-w-4xl mx-auto font-sans">
      <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 p-6 sm:p-10 rounded-none shadow-none text-zinc-900 dark:text-zinc-100 transition-colors">
        
        {/* Header Bar */}
        <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-6 mb-8">
          <div>
            <div className="flex items-center gap-2 font-mono text-xs uppercase tracking-widest text-emerald-600 dark:text-emerald-400 font-bold mb-1">
              <Terminal className="w-4 h-4" />
              <span>ONBOARDING ENGINE // TAHAP {currentStep + 1} DARI {steps.length}</span>
            </div>
            <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight">
              {steps[currentStep].title}
            </h2>
            <p className="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 font-mono mt-1">
              {steps[currentStep].desc}
            </p>
          </div>

          <div className="hidden sm:block text-right font-mono text-xs text-zinc-400">
            <span>MODERN MONOLITH PROTOCOL</span>
          </div>
        </div>

        {/* Sharp Step Indicators */}
        <div className="grid grid-cols-4 gap-2 mb-10">
          {steps.map((step, idx) => (
            <div 
              key={step.id} 
              className={`h-2 rounded-none transition-all duration-300 ${
                idx <= currentStep 
                  ? 'bg-emerald-500' 
                  : 'bg-zinc-200 dark:bg-zinc-800'
              }`} 
            />
          ))}
        </div>

        {/* Step Body */}
        <AnimatePresence mode="wait">
          <motion.div
            key={currentStep}
            initial={{ opacity: 0, y: 10 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -10 }}
            transition={{ duration: 0.2 }}
            className="min-h-[260px]"
          >
            {currentStep === 0 && (
              <div className="space-y-6">
                <div>
                  <label className="block text-xs font-mono uppercase font-bold tracking-wider text-zinc-600 dark:text-zinc-400 mb-2">
                    Nama Lengkap PIC *
                  </label>
                  <input 
                    type="text" 
                    name="name" 
                    value={formData.name} 
                    onChange={handleChange} 
                    className="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm font-sans rounded-none focus:border-emerald-500 outline-none" 
                    placeholder="Contoh: Alexander Wijaya" 
                  />
                </div>
                <div>
                  <label className="block text-xs font-mono uppercase font-bold tracking-wider text-zinc-600 dark:text-zinc-400 mb-2">
                    Alamat Email Kerja *
                  </label>
                  <input 
                    type="email" 
                    name="email" 
                    value={formData.email} 
                    onChange={handleChange} 
                    className="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm font-sans rounded-none focus:border-emerald-500 outline-none" 
                    placeholder="alexander@perusahaan.co.id" 
                  />
                </div>
                <div>
                  <label className="block text-xs font-mono uppercase font-bold tracking-wider text-zinc-600 dark:text-zinc-400 mb-2">
                    Nama Badan Usaha / Brand (Opsional)
                  </label>
                  <input 
                    type="text" 
                    name="company_name" 
                    value={formData.company_name} 
                    onChange={handleChange} 
                    className="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm font-sans rounded-none focus:border-emerald-500 outline-none" 
                    placeholder="PT. Inovasi Solusi Bersama" 
                  />
                </div>
              </div>
            )}

            {currentStep === 1 && (
              <div className="space-y-6">
                <div>
                  <label className="block text-xs font-mono uppercase font-bold tracking-wider text-zinc-600 dark:text-zinc-400 mb-2">
                    Uraikan Masalah atau Ide Aplikasi yang Ingin Dibuat *
                  </label>
                  <textarea 
                    name="vision" 
                    value={visionText} 
                    onChange={handleChange} 
                    rows="6" 
                    className="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm font-sans rounded-none focus:border-emerald-500 outline-none resize-none leading-relaxed" 
                    placeholder="Jelaskan kebutuhan Anda: Misal kami membutuhkan platform logistik multi-cabang dengan tracking armada real-time, invoice otomatis, dan hak akses bertingkat..." 
                  />
                  <p className="text-xs font-mono text-zinc-500 mt-2">
                    &bull; Catatan: Jangan sertakan kata sandi atau data kredensial rahasia.
                  </p>
                </div>
              </div>
            )}

            {currentStep === 2 && (
              <div className="space-y-6">
                <label className="block text-xs font-mono uppercase font-bold tracking-wider text-zinc-600 dark:text-zinc-400 mb-3">
                  Pilih Rentang Estimasi Investasi Proyek *
                </label>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  {[
                    { id: '<Rp 15 Juta', desc: 'MVP Ringan / Prototyping Cepat' },
                    { id: 'Rp 15 - 50 Juta', desc: 'Sistem Bisnis Operasional Standar' },
                    { id: 'Rp 50 - 150 Juta', desc: 'Enterprise Monolith Skala Penuh' },
                    { id: '> Rp 150 Juta', desc: 'Custom Distributed / High Throughput' }
                  ].map(range => (
                    <button 
                      key={range.id} 
                      type="button"
                      onClick={() => setFormData(prev => ({ ...prev, budget_range: range.id }))}
                      className={`p-4 border text-left rounded-none font-mono transition-all ${
                        formData.budget_range === range.id 
                          ? 'border-2 border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30 text-zinc-900 dark:text-white' 
                          : 'border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 bg-zinc-50 dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300'
                      }`}
                    >
                      <div className="text-sm font-bold">{range.id}</div>
                      <div className="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{range.desc}</div>
                    </button>
                  ))}
                </div>
              </div>
            )}

            {currentStep === 3 && (
              <div className="space-y-6">
                <div className="bg-zinc-50 dark:bg-zinc-950 p-6 border border-zinc-200 dark:border-zinc-800 rounded-none">
                  <div className="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-mono text-xs font-bold uppercase mb-2">
                    <ShieldCheck className="w-4 h-4" />
                    <span>KOMITMEN KERAHASIAAN & DATA PRIVACY</span>
                  </div>
                  <p className="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed mb-6 font-sans">
                    Seluruh ide, deskripsi proses bisnis, dan rincian arsitektur yang Anda bagikan dilindungi dengan standar kerahasiaan ketat. Neriah Pro tidak akan pernah membagikan atau menjual data Anda kepada pihak ketiga.
                  </p>
                  
                  <label className="flex items-start gap-3 cursor-pointer select-none">
                    <input 
                      type="checkbox" 
                      name="privacy_consent_agreed" 
                      checked={formData.privacy_consent_agreed} 
                      onChange={handleChange} 
                      className="w-4 h-4 mt-0.5 accent-emerald-500 rounded-none cursor-pointer" 
                    />
                    <span className="text-xs font-sans text-zinc-700 dark:text-zinc-300">
                      Saya menyetujui pemrosesan data ide dan spesifikasi teknis ini untuk keperluan penyusunan blueprint Neriah Pro.
                    </span>
                  </label>
                </div>
              </div>
            )}
          </motion.div>
        </AnimatePresence>

        {/* Footer Navigation */}
        <div className="mt-8 pt-6 border-t border-zinc-200 dark:border-zinc-800 flex justify-between items-center font-mono text-xs uppercase font-bold">
          <button 
            type="button"
            onClick={prevStep} 
            disabled={currentStep === 0}
            className={`px-4 py-2.5 rounded-none border border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 flex items-center gap-2 transition ${
              currentStep === 0 
                ? 'opacity-20 cursor-not-allowed' 
                : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'
            }`}
          >
            <ArrowLeft className="w-3.5 h-3.5" />
            <span>Kembali</span>
          </button>
          
          {currentStep < steps.length - 1 ? (
            <button 
              type="button"
              onClick={nextStep}
              className="bg-zinc-900 hover:bg-black dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white dark:text-black px-6 py-2.5 rounded-none transition flex items-center gap-2"
            >
              <span>Lanjut</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </button>
          ) : (
            <button 
              type="button"
              onClick={handleSubmit}
              disabled={isSubmitting || !formData.privacy_consent_agreed}
              className={`px-6 py-2.5 rounded-none transition flex items-center gap-2 ${
                isSubmitting || !formData.privacy_consent_agreed
                  ? 'bg-zinc-300 dark:bg-zinc-800 text-zinc-500 cursor-not-allowed'
                  : 'bg-emerald-600 hover:bg-emerald-500 text-black font-black'
              }`}
            >
              <span>{isSubmitting ? 'Mengamankan...' : 'Kirim Kebutuhan'}</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </button>
          )}
        </div>

      </div>
    </section>
  );
}
