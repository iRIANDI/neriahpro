import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { AlertCircle, CheckCircle, Copy, Shield, Gift, Zap, X, Loader2 } from 'lucide-react';

export default function BlueprintIncidentReportModal({
  isOpen,
  onClose,
  errorMessage,
  formData,
  blueprintId,
  lang = 'id',
  csrfToken,
  onSuccessClaim,
}) {
  const [clientNotes, setClientNotes] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [result, setResult] = useState(null);
  const [copied, setCopied] = useState(false);
  const [errorNotice, setErrorNotice] = useState(null);

  if (!isOpen) return null;

  const handleSubmitReport = async (e) => {
    e?.preventDefault();
    setIsSubmitting(true);
    setErrorNotice(null);

    const email = formData?.email?.trim();
    if (!email) {
      setErrorNotice(lang === 'en' ? 'Please fill your official email in Field 25 first so we can issue your compensation voucher.' : 'Mohon lengkapi email resmi di Parameter 25 terlebih dahulu agar voucher kompensasi dapat diterbitkan.');
      setIsSubmitting(false);
      return;
    }

    try {
      const token = csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const payload = {
        error_message: errorMessage || 'Server communication timeout or error during blueprint update.',
        client_email: email,
        client_name: formData?.clientName || 'Valued Client',
        client_phone: formData?.phone || null,
        error_type: errorMessage?.toLowerCase().includes('timeout') ? 'AI_SYNTHESIS_TIMEOUT' : 'SERVER_HTTP_ERROR',
        vision_blueprint_id: blueprintId || null,
        client_notes: clientNotes.trim() || null,
        technical_diagnostics: {
          browser: navigator.userAgent,
          screen: `${window.innerWidth}x${window.innerHeight}`,
          url: window.location.href,
        },
      };

      const response = await fetch('/api/blueprint/report-incident', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify(payload),
      });

      const resData = await response.json();

      if (response.ok && resData.success) {
        setResult(resData.data);
        if (onSuccessClaim) {
          onSuccessClaim(resData.data);
        }
      } else {
        setErrorNotice(resData.message || (lang === 'en' ? 'Failed to record incident report.' : 'Gagal mengirim laporan gangguan.'));
      }
    } catch (err) {
      setErrorNotice(lang === 'en' ? 'Network error occurred while submitting report.' : 'Terjadi gangguan jaringan saat mengirim laporan.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleCopyVoucher = () => {
    if (!result?.voucher_code) return;
    navigator.clipboard.writeText(result.voucher_code);
    setCopied(true);
    setTimeout(() => setCopied(false), 2500);
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm">
      <motion.div
        initial={{ opacity: 0, scale: 0.96 }}
        animate={{ opacity: 1, scale: 1 }}
        exit={{ opacity: 0, scale: 0.96 }}
        className="w-full max-w-xl bg-white dark:bg-zinc-900 border-2 border-rose-500 rounded-none shadow-2xl p-6 relative max-h-[92vh] overflow-y-auto font-sans"
      >
        <button
          type="button"
          onClick={onClose}
          className="absolute top-4 right-4 p-1.5 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 transition cursor-pointer"
        >
          <X className="w-5 h-5" />
        </button>

        {!result ? (
          <div>
            <div className="flex items-center gap-2 text-rose-600 dark:text-rose-400 font-mono text-xs font-bold tracking-wider uppercase mb-1">
              <AlertCircle className="w-4 h-4 shrink-0" />
              <span>World-Class SLA Guarantee Protocol</span>
            </div>
            <h3 className="text-xl font-black text-zinc-950 dark:text-zinc-50 mb-2">
              {lang === 'en' ? 'Report Incident & Claim SLA Compensation' : 'Lapor Gangguan & Klaim Kompensasi SLA'}
            </h3>
            <p className="text-xs text-zinc-600 dark:text-zinc-400 mb-4 leading-relaxed">
              {lang === 'en'
                ? 'At Neriah Pro, we adhere to strict enterprise SLA standards. If our systems experience latency or failure while processing your vision, you are immediately entitled to service credits and extended warranty.'
                : 'Di Neriah Pro, kami menegakkan standar SLA kelas dunia. Jika sistem mengalami kendala atau keterlambatan saat memproses arsitektur Anda, Anda berhak menerima kompensasi kredit dan perpanjangan garansi seketika.'}
            </p>

            {/* SLA Compensation Guarantees Card */}
            <div className="p-4 bg-emerald-500/10 border border-emerald-500/40 rounded-none mb-4 space-y-2">
              <div className="text-xs font-mono font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5">
                <Gift className="w-4 h-4" />
                <span>{lang === 'en' ? 'AUTOMATIC COMPENSATION PACKAGE:' : 'PAKET KOMPENSASI OTOMATIS (100% KLAIM LANGSUNG):'}</span>
              </div>
              <ul className="text-xs text-zinc-800 dark:text-zinc-200 space-y-1.5 list-none pl-1">
                <li className="flex items-start gap-2">
                  <CheckCircle className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span><strong>Voucher Kredit Rp 150.000</strong> {lang === 'en' ? 'valid for 30 days towards any license upgrade.' : 'berlaku 30 hari untuk potongan lisensi / add-on.'}</span>
                </li>
                <li className="flex items-start gap-2">
                  <Shield className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span><strong>+30 Hari Tambahan Garansi Bug SLA</strong> {lang === 'en' ? 'added to your official project SLA scope.' : 'ditambahkan ke kontrak garansi pemeliharaan proyek.'}</span>
                </li>
                <li className="flex items-start gap-2">
                  <Zap className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span><strong>VIP Priority Fast-Track Queue</strong> {lang === 'en' ? 'Senior Architect reviews your blueprint in priority queue.' : 'Senior Architect memprioritaskan review blueprint Anda di antrean utama.'}</span>
                </li>
              </ul>
            </div>

            {/* Error Context Diagnostic */}
            <div className="p-3 bg-zinc-100 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-[11px] font-mono text-zinc-700 dark:text-zinc-300 mb-4 rounded-none">
              <span className="text-rose-600 dark:text-rose-400 font-bold block mb-1">
                {lang === 'en' ? 'CAPTURED SYSTEM DIAGNOSTIC:' : 'DIAGNOSTIK GANGGUAN TERDETEKSI:'}
              </span>
              <p className="break-words">{errorMessage || 'System communication latency / timeout'}</p>
              <div className="mt-2 pt-2 border-t border-zinc-300 dark:border-zinc-800 text-[10px] text-zinc-500">
                <span>PIC: {formData?.email || 'Belum diisi di Form'}</span> • <span>Project: {formData?.namaBisnis || 'Active Blueprint'}</span>
              </div>
            </div>

            {errorNotice && (
              <div className="p-3 bg-rose-50 dark:bg-rose-950/40 border border-rose-500 text-rose-800 dark:text-rose-200 text-xs mb-4 rounded-none font-bold">
                {errorNotice}
              </div>
            )}

            <form onSubmit={handleSubmitReport} className="space-y-4">
              <div>
                <label className="block text-xs font-mono uppercase font-bold text-zinc-700 dark:text-zinc-300 mb-1">
                  {lang === 'en' ? 'What were you doing when this occurred? (Optional Notes)' : 'Apa yang sedang Anda lakukan saat kendala terjadi? (Catatan Opsional)'}
                </label>
                <textarea
                  value={clientNotes}
                  onChange={(e) => setClientNotes(e.target.value)}
                  placeholder={lang === 'en' ? 'e.g., I was synthesizing a large document or regenerating the PRD...' : 'Contoh: Saya sedang mengunggah dokumen spesifikasi panjang atau memperbarui PRD...'}
                  className="w-full px-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 rounded-none focus:border-rose-500 outline-none"
                  rows={3}
                />
              </div>

              <div className="flex items-center justify-end gap-2 pt-2">
                <button
                  type="button"
                  onClick={onClose}
                  className="px-4 py-2 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-xs font-mono rounded-none transition cursor-pointer"
                >
                  {lang === 'en' ? 'Cancel' : 'Batal'}
                </button>
                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="px-5 py-2 bg-rose-600 hover:bg-rose-700 disabled:opacity-50 text-white text-xs font-mono font-bold rounded-none transition inline-flex items-center gap-1.5 cursor-pointer shadow-md"
                >
                  {isSubmitting ? (
                    <>
                      <Loader2 className="w-3.5 h-3.5 animate-spin" />
                      <span>{lang === 'en' ? 'Claiming SLA Compensation...' : 'Menerbitkan Kompensasi...'}</span>
                    </>
                  ) : (
                    <>
                      <Gift className="w-3.5 h-3.5" />
                      <span>{lang === 'en' ? 'SUBMIT INCIDENT & CLAIM COMPENSATION →' : 'KIRIM LAPORAN & KLAIM KOMPENSASI SEKARANG →'}</span>
                    </>
                  )}
                </button>
              </div>
            </form>
          </div>
        ) : (
          <div className="text-center py-4">
            <div className="w-12 h-12 bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 mx-auto flex items-center justify-center rounded-none mb-3">
              <CheckCircle className="w-7 h-7" />
            </div>
            <h3 className="text-xl font-black text-zinc-950 dark:text-zinc-50 mb-1">
              {lang === 'en' ? 'SLA Compensation Successfully Granted!' : 'Kompensasi SLA Berhasil Diterbitkan!'}
            </h3>
            <p className="text-xs text-zinc-600 dark:text-zinc-400 max-w-md mx-auto mb-6">
              {lang === 'en'
                ? `Incident ${result.incident_code} has been logged in our immutable audit chain. Your service credit voucher has been activated.`
                : `Insiden ${result.incident_code} telah tercatat di rantai audit Neriah Pro. Voucher kompensasi Anda telah aktif seketika.`}
            </p>

            {/* Voucher Card */}
            <div className="p-5 bg-zinc-50 dark:bg-zinc-950 border-2 border-dashed border-emerald-500 max-w-md mx-auto mb-6 text-left rounded-none">
              <div className="text-[10px] font-mono uppercase tracking-wider text-zinc-500 mb-1">
                KODE VOUCHER KOMPENSASI RESMI (POTONGAN RP 150.000):
              </div>
              <div className="flex items-center justify-between gap-3">
                <span className="font-mono text-xl font-black text-emerald-600 dark:text-emerald-400 tracking-wider">
                  {result.voucher_code}
                </span>
                <button
                  type="button"
                  onClick={handleCopyVoucher}
                  className="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-mono text-xs font-bold inline-flex items-center gap-1 rounded-none transition cursor-pointer"
                >
                  <Copy className="w-3.5 h-3.5" />
                  <span>{copied ? 'Tersalin!' : 'Salin Kode'}</span>
                </button>
              </div>
              <div className="mt-3 pt-3 border-t border-zinc-200 dark:border-zinc-800 text-[11px] text-zinc-600 dark:text-zinc-400 space-y-1">
                <div>🛡️ <strong>+30 Hari Garansi Bug Ekstra:</strong> Telah ditambahkan otomatis ke blueprint Anda.</div>
                <div>⚡ <strong>Antrean VIP:</strong> Senior Architect telah diprioritaskan untuk mengawal blueprint Anda.</div>
              </div>
            </div>

            <button
              type="button"
              onClick={onClose}
              className="px-6 py-2.5 bg-zinc-900 dark:bg-zinc-100 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 font-mono text-xs font-bold rounded-none transition cursor-pointer"
            >
              {lang === 'en' ? 'Close & Continue Editing Blueprint' : 'Tutup & Lanjutkan Blueprint'}
            </button>
          </div>
        )}
      </motion.div>
    </div>
  );
}

