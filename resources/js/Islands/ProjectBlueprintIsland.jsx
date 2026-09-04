import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
  Building2, 
  Target, 
  Users, 
  ShieldCheck, 
  Zap, 
  Layers, 
  GitBranch, 
  Cpu, 
  Palette, 
  Clock, 
  Sparkles, 
  ArrowRight, 
  CheckCircle2, 
  AlertCircle,
  FileText
} from 'lucide-react';

export default function ProjectBlueprintIsland({ csrfToken, submitUrl, initialData = {} }) {
  const [formData, setFormData] = useState({
    namaBisnis: initialData.namaBisnis || '',
    clientName: initialData.clientName || '',
    email: initialData.email || '',
    phone: initialData.phone || '',
    masalahUtama: initialData.masalahUtama || '',
    tujuanUtama: initialData.tujuanUtama || '',
    targetAudiens: initialData.targetAudiens || '',
    aktorSistem: initialData.aktorSistem || '',
    fiturWajib: initialData.fiturWajib || '',
    fiturTambahan: initialData.fiturTambahan || '',
    alurKerja: initialData.alurKerja || '',
    kebutuhanIntegrasi: initialData.kebutuhanIntegrasi || '',
    referensiDesain: initialData.referensiDesain || '',
    kesiapanAset: initialData.kesiapanAset || 'Sedang Disiapkan',
    targetWaktu: initialData.targetWaktu || '3-4 Pekan',
  });

  const [activeTab, setActiveTab] = useState('all'); // 'all' or step-by-step
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState(null);
  const [successData, setSuccessData] = useState(null);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setErrorMessage(null);

    const payload = {
      nama_bisnis: formData.namaBisnis,
      client_name: formData.clientName || formData.namaBisnis,
      email: formData.email,
      phone: formData.phone,
      masalah_utama: formData.masalahUtama,
      tujuan_utama: formData.tujuanUtama,
      target_audiens: formData.targetAudiens,
      aktor_sistem: formData.aktorSistem,
      fitur_wajib: formData.fiturWajib,
      fitur_tambahan: formData.fiturTambahan,
      alur_kerja: formData.alurKerja,
      kebutuhan_integrasi: formData.kebutuhanIntegrasi,
      referensi_desain: formData.referensiDesain,
      kesiapan_aset: formData.kesiapanAset,
      target_waktu: formData.targetWaktu,
      service_options: ['Web Architecture', 'Rapid Monolith System', 'PostgreSQL ULID'],
    };

    try {
      const response = await fetch(submitUrl || '/api/vision-blueprint', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify(payload),
      });

      const result = await response.json();

      if (response.ok && result.success) {
        setSuccessData(result.data);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      } else {
        const errorDetails = result.errors 
          ? Object.values(result.errors).flat().join(', ') 
          : (result.message || 'Terjadi kesalahan saat memproses data.');
        setErrorMessage(errorDetails);
      }
    } catch (err) {
      setErrorMessage('Terjadi gangguan jaringan atau koneksi. Silakan coba sesaat lagi.');
    } finally {
      setIsSubmitting(false);
    }
  };

  if (successData) {
    return (
      <motion.div 
        initial={{ opacity: 0, scale: 0.95 }}
        animate={{ opacity: 1, scale: 1 }}
        transition={{ duration: 0.5 }}
        className="max-w-3xl mx-auto my-12 bg-white rounded-2xl shadow-xl border border-emerald-100 overflow-hidden"
      >
        <div className="bg-gradient-to-r from-emerald-600 to-teal-700 p-8 text-white text-center">
          <div className="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-4">
            <CheckCircle2 className="w-10 h-10 text-white" />
          </div>
          <h2 className="text-3xl font-extrabold tracking-tight">Transmisi Berhasil!</h2>
          <p className="text-emerald-100 mt-2 max-w-lg mx-auto">
            Blueprint Proyek & Skema PRD Ultimate Anda telah berhasil disintesis dan terkunci di pusat data arsitektur Neriah Pro.
          </p>
        </div>

        <div className="p-8 space-y-6">
          <div className="bg-slate-50 border border-slate-200 rounded-xl p-6">
            <div className="flex items-center justify-between border-b border-slate-200 pb-4 mb-4">
              <span className="text-sm font-semibold text-slate-500 uppercase tracking-wider">Nama Bisnis / Proyek</span>
              <span className="font-bold text-slate-800 text-lg">{formData.namaBisnis}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-sm font-semibold text-slate-500 uppercase tracking-wider">Status Dokumen</span>
              <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                <Sparkles className="w-3 h-3 mr-1" />
                PRD & ERD Schema Generated
              </span>
            </div>
          </div>

          <div className="flex flex-col sm:flex-row gap-4">
            {successData.redirect_url && (
              <a
                href={successData.redirect_url}
                className="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-center flex items-center justify-center gap-2"
              >
                <FileText className="w-5 h-5" />
                Buka Dokumen Ultimate PRD Sekarang
                <ArrowRight className="w-5 h-5" />
              </a>
            )}
            <button
              type="button"
              onClick={() => { setSuccessData(null); }}
              className="px-6 py-4 border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold rounded-xl transition"
            >
              Input Proyek Baru
            </button>
          </div>
        </div>
      </motion.div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto py-8 px-4 sm:px-6">
      
      {/* 1. EXECUTIVE TECHNICAL DISCOVERY */}
      <section className="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-10 relative overflow-hidden">
        <div className="absolute top-0 left-0 w-2 h-full bg-indigo-600"></div>
        <div className="flex items-center gap-3 mb-4">
          <div className="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
            <Cpu className="w-5 h-5" />
          </div>
          <div>
            <span className="text-xs font-bold uppercase tracking-wider text-indigo-600">Arsitektur Terpadu</span>
            <h2 className="text-2xl font-bold text-slate-800">1. Executive Technical Discovery</h2>
          </div>
        </div>
        
        <p className="text-slate-600 leading-relaxed mb-6">
          Untuk mencapai efisiensi biaya server dan kecepatan pengerjaan tanpa mengorbankan kualitas keamanan, kami merekomendasikan infrastruktur <strong>Modern Monolith (Laravel 13 & Filament PHP)</strong>. Sistem ini memungkinkan pengembangan fitur manajemen data secara kilat (<em>Rapid Prototyping</em>) dengan skala enterprise.
        </p>

        <div className="grid md:grid-cols-2 gap-6">
          <div className="bg-slate-50 p-5 rounded-xl border border-slate-100 hover:border-indigo-200 transition">
            <div className="flex items-center gap-2 mb-2 text-indigo-700 font-bold">
              <ShieldCheck className="w-5 h-5" />
              Pusat Kendali (Admin Panel)
            </div>
            <p className="text-sm text-slate-600 leading-relaxed">
              Menggunakan <em>Filament PHP</em> untuk merender dasbor kelas <em>enterprise</em> dengan filter data canggih, metrik analitik, dan tabel dinamis seketika.
            </p>
          </div>
          <div className="bg-slate-50 p-5 rounded-xl border border-slate-100 hover:border-indigo-200 transition">
            <div className="flex items-center gap-2 mb-2 text-indigo-700 font-bold">
              <Zap className="w-5 h-5" />
              Keamanan & Performa (VPS Dedicated)
            </div>
            <p className="text-sm text-slate-600 leading-relaxed">
              Sistem di-host di <em>Virtual Private Server</em> terdedikasi berbasis Nixpacks & Docker untuk menghindari <em>resource sharing</em>, menjamin kelancaran <em>Role-Based Access Control (RBAC)</em>.
            </p>
          </div>
        </div>
      </section>

      {/* 2. VISION BLUEPRINT (PRD SETUP FORM) */}
      <section className="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div className="border-b-2 border-emerald-500 pb-3 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
          <div>
            <h2 className="text-2xl font-bold text-slate-800">2. Vision Blueprint (PRD Setup)</h2>
            <p className="text-slate-500 text-sm mt-1">
              Mohon isi kuesioner ini selengkap mungkin. Data ini akan menjadi <em>Product Requirements Document (PRD)</em> dan acuan ruang lingkup pekerjaan.
            </p>
          </div>
          <span className="self-start sm:self-auto px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200">
            Auto-ERD Generator Active
          </span>
        </div>

        {errorMessage && (
          <div className="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 flex items-start gap-3">
            <AlertCircle className="w-5 h-5 flex-shrink-0 mt-0.5" />
            <div className="text-sm font-medium">{errorMessage}</div>
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-8">
          
          {/* INFORMASI KONTAK & PIC */}
          <div className="bg-slate-50 p-6 rounded-xl border border-slate-200">
            <h3 className="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
              <Users className="w-5 h-5 text-indigo-600" />
              Informasi Kontak Penanggung Jawab
            </h3>
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label className="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">
                  Nama Anda / PIC
                </label>
                <input
                  type="text"
                  name="clientName"
                  value={formData.clientName}
                  onChange={handleChange}
                  placeholder="Misal: Budi Santoso"
                  className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                />
              </div>
              <div>
                <label className="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">
                  Email Kontak
                </label>
                <input
                  type="email"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                  placeholder="budi@perusahaan.com"
                  className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                />
              </div>
              <div>
                <label className="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">
                  WhatsApp / No HP
                </label>
                <input
                  type="tel"
                  name="phone"
                  value={formData.phone}
                  onChange={handleChange}
                  placeholder="+62 812-3456-7890"
                  className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                />
              </div>
            </div>
          </div>

          {/* BLOK A: KONTEKS BISNIS */}
          <div className="bg-slate-50 p-6 rounded-xl border border-slate-200">
            <h3 className="text-lg font-bold text-slate-800 mb-4 flex items-center">
              <span className="bg-emerald-100 text-emerald-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 font-extrabold text-sm">
                A
              </span>
              Konteks Bisnis & Tujuan Utama
            </h3>
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-1">
                  Nama Proyek / Bisnis <span className="text-rose-500">*</span>
                </label>
                <input
                  type="text"
                  name="namaBisnis"
                  required
                  value={formData.namaBisnis}
                  onChange={handleChange}
                  placeholder="Misal: Neriah Project Hub, Logistik Express, Klinik Sehat..."
                  className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition font-medium"
                />
              </div>
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-1">
                  Masalah Utama yang Ingin Diselesaikan? <span className="text-rose-500">*</span>
                </label>
                <textarea
                  name="masalahUtama"
                  required
                  rows={2}
                  value={formData.masalahUtama}
                  onChange={handleChange}
                  placeholder="Misal: Saat ini pendaftaran masih manual pakai kertas, jadi sering hilang dan rekapnya susah..."
                  className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                />
              </div>
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-1">
                  Tolak Ukur Kesuksesan (KPI) <span className="text-rose-500">*</span>
                </label>
                <textarea
                  name="tujuanUtama"
                  required
                  rows={2}
                  value={formData.tujuanUtama}
                  onChange={handleChange}
                  placeholder="Misal: Proses rekap data menjadi otomatis, dan ada laporan harian yang bisa didownload PDF."
                  className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                />
              </div>
            </div>
          </div>

          {/* BLOK B: PENGGUNA & HAK AKSES */}
          <div className="bg-slate-50 p-6 rounded-xl border border-slate-200">
            <h3 className="text-lg font-bold text-slate-800 mb-4 flex items-center">
              <span className="bg-emerald-100 text-emerald-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 font-extrabold text-sm">
                B
              </span>
              Pengguna & Hak Akses
            </h3>
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-1">
                  Siapa Target Audiens / Pengunjung Utama? <span className="text-rose-500">*</span>
                </label>
                <input
                  type="text"
                  name="targetAudiens"
                  required
                  value={formData.targetAudiens}
                  onChange={handleChange}
                  placeholder="Misal: Calon pelamar kerja, klien B2B, masyarakat umum..."
                  className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                />
              </div>
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-1">
                  Siapa Saja yang Akan Login ke Dalam Sistem (Aktor)? <span className="text-rose-500">*</span>
                </label>
                <textarea
                  name="aktorSistem"
                  required
                  rows={2}
                  value={formData.aktorSistem}
                  onChange={handleChange}
                  placeholder="Misal: 1. Superadmin (bisa semua), 2. Staff HR (hanya bisa lihat data), 3. Calon Pelamar (hanya bisa isi form)."
                  className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                />
              </div>
            </div>
          </div>

          {/* BLOK C: FUNGSIONALITAS SISTEM */}
          <div className="bg-slate-50 p-6 rounded-xl border border-slate-200">
            <h3 className="text-lg font-bold text-slate-800 mb-4 flex items-center">
              <span className="bg-emerald-100 text-emerald-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 font-extrabold text-sm">
                C
              </span>
              Fungsionalitas Sistem & Alur Kerja
            </h3>
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-1">
                  Fitur Wajib (Fase 1 - MVP) <span className="text-rose-500">*</span>
                </label>
                <textarea
                  name="fiturWajib"
                  required
                  rows={3}
                  value={formData.fiturWajib}
                  onChange={handleChange}
                  placeholder="Sebutkan poin per poin. Misal: Form registrasi pelamar, Tabel database pelamar, Fitur Export Excel..."
                  className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                />
              </div>
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-1">
                  Fitur Tambahan (Boleh Disusul di Fase 2)
                </label>
                <textarea
                  name="fiturTambahan"
                  rows={2}
                  value={formData.fiturTambahan}
                  onChange={handleChange}
                  placeholder="Misal: Fitur notifikasi ke email, Integrasi absensi, Dark Mode..."
                  className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                />
              </div>
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-1">
                  Alur Kerja Utama (User Flow) <span className="text-rose-500">*</span>
                </label>
                <textarea
                  name="alurKerja"
                  required
                  rows={3}
                  value={formData.alurKerja}
                  onChange={handleChange}
                  placeholder="Ceritakan urutannya: User buka web -> Isi Form -> Muncul popup sukses -> Admin dapat notif -> Admin approve data."
                  className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                />
              </div>
            </div>
          </div>

          {/* BLOK D: KEBUTUHAN EKSTRA & KESIAPAN */}
          <div className="bg-slate-50 p-6 rounded-xl border border-slate-200">
            <h3 className="text-lg font-bold text-slate-800 mb-4 flex items-center">
              <span className="bg-emerald-100 text-emerald-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 font-extrabold text-sm">
                D
              </span>
              Kebutuhan Ekstra & Kesiapan Aset
            </h3>
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-1">
                  Apakah Butuh Integrasi ke Aplikasi Lain?
                </label>
                <input
                  type="text"
                  name="kebutuhanIntegrasi"
                  value={formData.kebutuhanIntegrasi}
                  onChange={handleChange}
                  placeholder="Misal: Payment Gateway (Midtrans), WhatsApp Notif, API Pihak Ketiga..."
                  className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                />
              </div>
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-1">
                  Referensi Website / Aplikasi (Inspirasi Desain)
                </label>
                <input
                  type="text"
                  name="referensiDesain"
                  value={formData.referensiDesain}
                  onChange={handleChange}
                  placeholder="Sebutkan URL website atau gaya desain yang Anda sukai..."
                  className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                />
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-semibold text-slate-700 mb-1">
                    Kesiapan Aset (Logo, Teks, Foto)
                  </label>
                  <select
                    name="kesiapanAset"
                    value={formData.kesiapanAset}
                    onChange={handleChange}
                    className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                  >
                    <option value="Belum Siap Sama Sekali">Belum Siap Sama Sekali</option>
                    <option value="Sedang Disiapkan">Sedang Disiapkan Tim Internal</option>
                    <option value="Sudah Siap Lengkap">Sudah Siap Lengkap</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-semibold text-slate-700 mb-1">
                    Target Rilis (Deadline)
                  </label>
                  <input
                    type="text"
                    name="targetWaktu"
                    value={formData.targetWaktu}
                    onChange={handleChange}
                    placeholder="Misal: Akhir Bulan Ini, 4 Pekan, ASAP..."
                    className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                  />
                </div>
              </div>
            </div>
          </div>

          {/* SUBMIT BUTTON */}
          <button
            type="submit"
            disabled={isSubmitting}
            className={`w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex justify-center items-center gap-3 text-lg ${
              isSubmitting ? 'opacity-70 cursor-not-allowed' : ''
            }`}
          >
            {isSubmitting ? (
              <>
                <div className="w-6 h-6 border-3 border-white/30 border-t-white rounded-full animate-spin"></div>
                <span>Menyusun & Men-generate Ultimate PRD...</span>
              </>
            ) : (
              <>
                <Sparkles className="w-5 h-5 text-emerald-400" />
                <span>Kirim Ultimate Blueprint & Generate PRD</span>
                <ArrowRight className="w-5 h-5" />
              </>
            )}
          </button>
        </form>
      </section>
    </div>
  );
}
