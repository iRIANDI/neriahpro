import React, { useState, useRef } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
  ArrowRight, 
  Sparkles, 
  Terminal,
  Paperclip,
  FileText,
  Image as ImageIcon,
  X,
  Loader2,
  ShieldCheck,
  CheckCircle2,
  AlertCircle
} from 'lucide-react';

export default function HeroIsland({ headline, subheadline, cta_text, cta_link, featureFlags, currentLocale }) {
  const isMidtransStrict = Boolean(featureFlags?.midtrans_mode);
  const isCvProEnabled = !isMidtransStrict && (featureFlags?.enable_cv_pro !== false);
  const isBlueprintEnabled = featureFlags?.enable_vision_blueprint !== false;
  const isContractEnabled = featureFlags?.enable_digital_contract !== false;
  const isClientOnboardingEnabled = featureFlags?.enable_client_onboarding !== false;
  const hasAnyPillar = isBlueprintEnabled || isCvProEnabled || isContractEnabled || isClientOnboardingEnabled;

  const isEn = currentLocale === 'en' || (typeof window !== 'undefined' && (document.documentElement.lang?.startsWith('en') || document.cookie.includes('neriah_locale=en')));

  // State for the Unified Idea & MarkItDown Workspace
  const [ideaText, setIdeaText] = useState('');
  const [attachedFiles, setAttachedFiles] = useState([]);
  const [honeypot, setHoneypot] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [loadingStep, setLoadingStep] = useState('');
  const [errorMessage, setErrorMessage] = useState(null);

  const fileInputRef = useRef(null);

  const handleFileChange = (e) => {
    if (!e.target.files) return;
    const newFiles = Array.from(e.target.files);
    
    // Check total limit (max 5 files)
    if (attachedFiles.length + newFiles.length > 5) {
      setErrorMessage(isEn ? 'Maximum 5 files can be attached.' : 'Maksimal 5 berkas dapat dilampirkan.');
      return;
    }

    // Filter file sizes (max 15MB each)
    const validFiles = [];
    for (const f of newFiles) {
      if (f.size > 15 * 1024 * 1024) {
        setErrorMessage(isEn ? `File ${f.name} exceeds 15MB limit.` : `Berkas ${f.name} melebihi batas 15MB.`);
        return;
      }
      validFiles.push(f);
    }

    setAttachedFiles(prev => [...prev, ...validFiles]);
    setErrorMessage(null);
    e.target.value = '';
  };

  const removeFile = (index) => {
    setAttachedFiles(prev => prev.filter((_, i) => i !== index));
  };

  const handleSubmitIdea = async (e) => {
    e.preventDefault();
    setErrorMessage(null);

    // Anti-Spam Check: Honeypot
    if (honeypot.trim() !== '') {
      // Silently redirect to avoid alerting bots
      window.location.href = '/blueprint';
      return;
    }

    // Minimum check: at least 15 chars or attached file
    const cleanText = ideaText.trim();
    if (cleanText.length < 15 && attachedFiles.length === 0) {
      setErrorMessage(isEn 
        ? 'Please write at least 15 characters of your project idea, or attach a specification document.' 
        : 'Mohon ceritakan ide proyek Anda minimal 15 karakter, atau lampirkan berkas dokumen pendukung.');
      return;
    }

    setIsSubmitting(true);
    setLoadingStep(attachedFiles.length > 0 
      ? (isEn ? 'Converting documents via MarkItDown...' : 'Mengonversi dokumen via MarkItDown...')
      : (isEn ? 'Analyzing project architecture...' : 'Menganalisis arsitektur proyek...'));

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const formData = new FormData();
      formData.append('idea_text', cleanText);
      formData.append('_hp_check', honeypot);
      formData.append('locale', isEn ? 'en' : 'id');

      attachedFiles.forEach(file => {
        formData.append('files[]', file);
      });

      // Advance loading stage after 1.2s for pleasant feedback
      const timer = setTimeout(() => {
        setLoadingStep(isEn ? 'Synthesizing RBAC, ERD & Milestones...' : 'Menyusun RBAC, Skema ERD & Milestone...');
      }, 1200);

      const response = await fetch('/api/blueprint/analyze-idea', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: formData
      });

      clearTimeout(timer);

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw new Error(result.message || (isEn ? 'Failed to process idea.' : 'Gagal memproses ide.'));
      }

      setLoadingStep(isEn ? 'Redirecting to Blueprint Workspace...' : 'Mengarahkan ke Ruang Penyesuaian Blueprint...');

      // Redirect user to the blueprint page where all inputs are populated
      window.location.href = result.redirect_url || '/blueprint';

    } catch (err) {
      setIsSubmitting(false);
      setLoadingStep('');
      setErrorMessage(err.message || (isEn ? 'An error occurred. Please try again.' : 'Terjadi kendala. Silakan coba lagi.'));
    }
  };

  return (
    <section className="relative w-full pt-28 pb-16 px-4 sm:px-6 bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 border-b border-zinc-200 dark:border-zinc-800 transition-colors font-sans overflow-hidden">
      
      {/* Background Subtle Tech Grid */}
      <div className="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none" />

      <div className="max-w-5xl mx-auto relative z-10">
        
        {/* Status Pill */}
        <div className="inline-flex items-center gap-2 px-3 py-1 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase tracking-widest mb-5 rounded-none">
          <span className="w-2 h-2 bg-emerald-500 rounded-none animate-pulse"></span>
          <span>NERIAH PRO // DIGITAL SERVICES HUB & ARCHITECTURE PLATFORM</span>
        </div>

        {/* Refined Headline: Balanced, authoritative, non-oversized */}
        <h1 className="text-2xl sm:text-4xl md:text-5xl font-extrabold uppercase tracking-tight leading-[1.12] mb-4 text-zinc-900 dark:text-zinc-50 font-sans max-w-4xl">
          {headline || (isEn 
            ? 'DIGITAL ARCHITECTURE & ENTERPRISE SOFTWARE HUB FOR HIGH-SCALE PROJECTS.'
            : 'PUSAT ARSITEKTUR & REKAYASA DIGITAL UNTUK PROYEK BERSKALA TINGGI.')}
        </h1>

        {/* Subheadline: Clear and balanced */}
        <p className="text-sm sm:text-base md:text-lg text-zinc-600 dark:text-zinc-400 font-sans max-w-3xl leading-relaxed mb-8">
          {subheadline || (isEn
            ? <>Transform your business vision into comprehensive <strong>Product Requirements Documents (PRDs)</strong>, distributed <strong>PostgreSQL Strict ULID schemas</strong>, sprint milestones, and locked contracts in minutes.</>
            : <>Ubah visi bisnis Anda menjadi <strong>Product Requirements Document (PRD)</strong> lengkap, skema basis data <strong>ERD Arsitektur Terdistribusi</strong>, alur kerja bertahap, dan penguncian kontrak kerja sama dalam hitungan menit.</>)}
        </p>

        {/* UNIFIED DISCOVERY WORKSPACE: Single Textarea + Document & Image Upload */}
        {isBlueprintEnabled && (
          <div className="bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 p-5 sm:p-7 mb-8 rounded-none shadow-none max-w-4xl">
            <div className="flex flex-wrap items-center justify-between gap-2 mb-3">
              <div className="flex items-center gap-2 font-mono text-xs uppercase tracking-wider text-emerald-600 dark:text-emerald-400 font-bold">
                <Sparkles className="w-4 h-4 text-emerald-500" />
                <span>{isEn ? 'AI ARCHITECTURAL DISCOVERY // SINTESIS BLUEPRINT LIVE' : 'AI ARCHITECTURAL DISCOVERY // SINTESIS BLUEPRINT LIVE'}</span>
              </div>
              <span className="text-[11px] font-mono text-zinc-500 dark:text-zinc-400">
                {isEn ? 'Powered by MarkItDown + AI Synthesis' : 'Didukung MarkItDown + Sintesis AI'}
              </span>
            </div>

            <p className="text-xs text-zinc-600 dark:text-zinc-400 font-sans mb-3.5 leading-relaxed">
              {isEn 
                ? 'Pour your application ideas, business operations, or attach specification documents/wireframe images. MarkItDown converts files into clean Markdown, allowing AI to comprehensively structure your Blueprint for review.'
                : 'Curahkan ide aplikasi, proses operasional, atau lampirkan dokumen spesifikasi/sketsa Anda. MarkItDown akan mengonversi berkas menjadi Markdown, lalu AI menyusun Blueprint Arsitektur terstruktur untuk Anda tinjau.'}
            </p>

            <form onSubmit={handleSubmitIdea} className="space-y-3">
              
              {/* Anti-Spam Honeypot Field (Hidden from real users) */}
              <input
                type="text"
                name="_hp_check"
                value={honeypot}
                onChange={(e) => setHoneypot(e.target.value)}
                tabIndex="-1"
                autoComplete="off"
                className="hidden"
                aria-hidden="true"
              />

              {/* Single Comprehensive Idea Textarea */}
              <div className="relative">
                <textarea
                  rows={4}
                  value={ideaText}
                  onChange={(e) => setIdeaText(e.target.value)}
                  disabled={isSubmitting}
                  placeholder={isEn 
                    ? 'Describe your application vision, business workflow, or operational pain points in your own words... (e.g. "We want an inter-island equipment rental platform. Users rent by day, vendors verify fleet availability, with milestone payments, container GPS tracking, and automated WhatsApp invoices...")'
                    : 'Ceritakan ide aplikasi, alur kerja bisnis, atau masalah operasional Anda secara bebas di sini... (Misal: "Saya ingin membuat aplikasi sewa alat berat antar pulau. Pengguna bisa sewa per hari, vendor verifikasi armada, ada pembayaran bertahap, pelacakan GPS kontainer, dan invoice otomatis via WhatsApp...")'}
                  className="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-xs sm:text-sm font-sans rounded-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition resize-y min-h-[110px]"
                />
                
                {/* Character counter & anti-spam indicator */}
                <div className="absolute bottom-2.5 right-3 text-[10px] font-mono text-zinc-400 select-none pointer-events-none">
                  {ideaText.length} {isEn ? 'chars' : 'karakter'}
                </div>
              </div>

              {/* Attached Files List */}
              {attachedFiles.length > 0 && (
                <div className="flex flex-wrap gap-2 pt-1 pb-1">
                  {attachedFiles.map((file, idx) => {
                    const isImg = file.type.startsWith('image/');
                    return (
                      <div 
                        key={idx} 
                        className="inline-flex items-center gap-2 px-2.5 py-1.5 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-xs font-mono text-zinc-800 dark:text-zinc-200 rounded-none"
                      >
                        {isImg ? (
                          <ImageIcon className="w-3.5 h-3.5 text-blue-500" />
                        ) : (
                          <FileText className="w-3.5 h-3.5 text-emerald-500" />
                        )}
                        <span className="max-w-[160px] sm:max-w-[240px] truncate">{file.name}</span>
                        <span className="text-[10px] text-zinc-400">({Math.round(file.size / 1024)} KB)</span>
                        <button
                          type="button"
                          onClick={() => removeFile(idx)}
                          disabled={isSubmitting}
                          className="hover:text-red-500 transition p-0.5 text-zinc-400"
                          title="Hapus berkas"
                        >
                          <X className="w-3.5 h-3.5" />
                        </button>
                      </div>
                    );
                  })}
                </div>
              )}

              {/* Action Toolbar: File Attach Button + Submit Button */}
              <div className="flex flex-wrap items-center justify-between gap-3 pt-1">
                
                {/* File Attachment Trigger */}
                <div className="flex items-center gap-2">
                  <input
                    type="file"
                    ref={fileInputRef}
                    onChange={handleFileChange}
                    multiple
                    accept=".pdf,.doc,.docx,.txt,.md,.rtf,.csv,.tsv,.xlsx,.pptx,image/*"
                    className="hidden"
                  />
                  <button
                    type="button"
                    onClick={() => fileInputRef.current?.click()}
                    disabled={isSubmitting}
                    className="inline-flex items-center gap-1.5 px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono font-bold border border-zinc-300 dark:border-zinc-700 rounded-none transition"
                  >
                    <Paperclip className="w-3.5 h-3.5 text-zinc-500" />
                    <span>{isEn ? 'Attach Docs / Images' : 'Lampirkan Dokumen / Gambar'}</span>
                  </button>

                  <span className="hidden sm:inline-block text-[11px] text-zinc-500 dark:text-zinc-400 font-mono">
                    (PDF, DOCX, XLSX, TXT, Wireframe)
                  </span>
                </div>

                {/* Primary Submit Button */}
                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="w-full sm:w-auto bg-zinc-900 hover:bg-black dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white dark:text-black font-mono text-xs font-black uppercase tracking-wider py-2.5 px-5 rounded-none transition flex items-center justify-center gap-2 disabled:opacity-50"
                >
                  {isSubmitting ? (
                    <>
                      <Loader2 className="w-3.5 h-3.5 animate-spin" />
                      <span>{loadingStep || (isEn ? 'Processing...' : 'Memproses...')}</span>
                    </>
                  ) : (
                    <>
                      <Sparkles className="w-3.5 h-3.5" />
                      <span>{isEn ? 'Synthesize & Adjust Blueprint →' : 'Sintesis & Tinjau Blueprint →'}</span>
                    </>
                  )}
                </button>
              </div>

              {/* Error Message Display */}
              {errorMessage && (
                <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-xs font-mono flex items-center gap-2 rounded-none">
                  <AlertCircle className="w-4 h-4 shrink-0" />
                  <span>{errorMessage}</span>
                </div>
              )}
            </form>

            <div className="flex flex-wrap items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400 mt-3 pt-2 border-t border-zinc-100 dark:border-zinc-800/60 font-mono">
              <span>
                &bull; {isEn 
                  ? 'MarkItDown converts all documents to clean Markdown. AI focuses strictly on architectural synthesis.'
                  : 'MarkItDown mengonversi berkas dokumen ke Markdown. AI murni fokus pada sintesis arsitektur.'}
              </span>
              <a 
                href="/blueprint" 
                className="hover:text-emerald-500 underline transition inline-flex items-center gap-1 mt-1 sm:mt-0"
              >
                <span>{isEn ? 'Or fill blueprint questionnaire directly →' : 'Atau isi kuesioner blueprint manual →'}</span>
              </a>
            </div>
          </div>
        )}

        {/* Dual Actions */}
        <div className="flex flex-wrap items-center gap-4 mb-14 font-mono text-xs uppercase font-bold tracking-wider">
          {isBlueprintEnabled ? (
            <a
              href="/blueprint"
              className="bg-emerald-600 hover:bg-emerald-500 text-black font-black py-3.5 px-7 rounded-none transition flex items-center gap-2"
            >
              <span>{cta_text || (isEn ? 'Open Blueprint Workspace' : 'Buka Ruang Blueprint')}</span>
              <ArrowRight className="w-4 h-4" />
            </a>
          ) : (
            <a
              href={hasAnyPillar ? "#services" : "#architecture"}
              className="bg-emerald-600 hover:bg-emerald-500 text-black font-black py-3.5 px-7 rounded-none transition flex items-center gap-2"
            >
              <span>{isEn ? 'Architecture Consultation' : 'Konsultasi Arsitektur'}</span>
              <ArrowRight className="w-4 h-4" />
            </a>
          )}
          {hasAnyPillar ? (
            <a
              href="#services"
              className="border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 py-3.5 px-7 rounded-none transition"
            >
              {isEn ? 'Explore Service Pillars ↓' : 'Jelajahi Pilar Layanan ↓'}
            </a>
          ) : (
            <a
              href="#architecture"
              className="border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 py-3.5 px-7 rounded-none transition"
            >
              {isEn ? 'Engineering Standards ↓' : 'Standar Rekayasa ↓'}
            </a>
          )}
        </div>

        {/* Architecture Precision Trust Metrics */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-3.5 border-t border-zinc-200 dark:border-zinc-800 pt-7 text-xs font-mono">
          <div className="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <span className="text-zinc-400 block text-[10px] uppercase">{isEn ? 'CORE ARCHITECTURE' : 'ARSITEKTUR CORE'}</span>
            <span className="font-bold text-zinc-900 dark:text-white text-xs sm:text-sm">High-Availability Monolith</span>
          </div>
          <div className="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <span className="text-zinc-400 block text-[10px] uppercase">{isEn ? 'DATABASE STANDARD' : 'STANDAR BASIS DATA'}</span>
            <span className="font-bold text-emerald-600 dark:text-emerald-400 text-xs sm:text-sm">Distributed ULID Engine</span>
          </div>
          <div className="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <span className="text-zinc-400 block text-[10px] uppercase">{isEn ? 'DOC CONVERSION' : 'KONVERSI DOKUMEN'}</span>
            <span className="font-bold text-zinc-900 dark:text-white text-xs sm:text-sm">Microsoft MarkItDown</span>
          </div>
          <div className="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
            <span className="text-zinc-400 block text-[10px] uppercase">{isEn ? 'INFRASTRUCTURE' : 'INFRASTRUKTUR'}</span>
            <span className="font-bold text-zinc-900 dark:text-white text-xs sm:text-sm">Dedicated Cloud VPS</span>
          </div>
        </div>

      </div>
    </section>
  );
}
