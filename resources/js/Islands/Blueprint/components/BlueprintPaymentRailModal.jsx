import React from 'react';
import { X, Check, CreditCard, Shield, Activity, Truck } from 'lucide-react';
import { PAYMENT_RAIL_OPTIONS } from '../constants/blueprintTranslations';

export default function BlueprintPaymentRailModal({
  isOpen,
  onClose,
  selectedPaymentRailIds,
  setSelectedPaymentRailIds,
  onApply,
  setPendingPaymentSuggestion = () => {},
  lang = 'id',
}) {
  if (!isOpen) return null;

  const handleTogglePaymentRail = (railId) => {
    setSelectedPaymentRailIds(prev => {
      const next = new Set(prev);
      if (next.has(railId)) {
        next.delete(railId);
      } else {
        next.add(railId);
      }
      return next;
    });
  };

  return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-xs">
          <div className="bg-white dark:bg-zinc-900 border-2 border-emerald-600 dark:border-emerald-500 w-full max-w-2xl max-h-[90vh] flex flex-col shadow-2xl rounded-none">
            {/* Modal Header */}
            <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-start justify-between bg-zinc-50 dark:bg-zinc-950">
              <div>
                <div className="flex items-center gap-2 mb-1">
                  <span className="text-[10px] font-mono px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 font-black uppercase">
                    ARUS KEUANGAN &amp; PEMBAYARAN (MULTI-SELECT)
                  </span>
                  <span className="text-[11px] font-mono font-bold text-emerald-800 dark:text-emerald-300">
                    {selectedPaymentRailIds.size} Rel Dipilih
                  </span>
                </div>
                <h3 className="text-sm font-black text-zinc-950 dark:text-zinc-100 font-mono">
                  {lang === 'en' ? 'Select Payment Rails (Multi-Select Supported)' : 'Pilih Rel Pembayaran Proyek Anda (Bisa Lebih Dari Satu)'}
                </h3>
                <p className="text-xs text-zinc-600 dark:text-zinc-400 mt-1 leading-relaxed">
                  {lang === 'en'
                    ? 'Click each payment rail you want to support. You can combine Direct Bank API (BRI/BNI), QRIS Dinamis, and Global Gateways (Stripe/Wise/PayPal).'
                    : 'Klik untuk memilih atau membatalkan pilihan rel pembayaran. Anda dapat menggabungkan Direct Bank API (BRI/BNI), QRIS Dinamis Nasional, dan Gateway Global (Stripe/Wise/PayPal) sekaligus.'}
                </p>
              </div>
              <button
                type="button"
                onClick={() => {
                  onClose();
                  setPendingPaymentSuggestion(null);
                }}
                className="p-1.5 text-zinc-500 hover:text-zinc-950 dark:hover:text-zinc-100 hover:bg-zinc-200 dark:hover:bg-zinc-800 cursor-pointer"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Quick Selection Helpers */}
            <div className="px-4 py-2 bg-zinc-100 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-800 flex flex-wrap items-center justify-between gap-2 text-[11px] font-mono">
              <span className="text-zinc-600 dark:text-zinc-400 font-bold">
                Pilihan Cepat:
              </span>
              <div className="flex flex-wrap gap-1.5">
                <button
                  type="button"
                  onClick={() => setSelectedPaymentRailIds(new Set(['bri_api', 'bni_api', 'qris_national']))}
                  className="px-2 py-0.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 hover:border-emerald-500 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold cursor-pointer"
                >
                  Domestik (BRI + BNI + QRIS)
                </button>
                <button
                  type="button"
                  onClick={() => setSelectedPaymentRailIds(new Set(['qris_national', 'stripe', 'wise']))}
                  className="px-2 py-0.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 hover:border-emerald-500 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold cursor-pointer"
                >
                  Hybrid Tour &amp; Travel (QRIS + Stripe + Wise)
                </button>
                <button
                  type="button"
                  onClick={() => setSelectedPaymentRailIds(new Set(PAYMENT_RAIL_OPTIONS.filter(o => o.id !== 'no_gateway').map(o => o.id)))}
                  className="px-2 py-0.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 hover:border-emerald-500 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold cursor-pointer"
                >
                  Pilih Semua
                </button>
                <button
                  type="button"
                  onClick={() => setSelectedPaymentRailIds(new Set())}
                  className="px-2 py-0.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 hover:border-red-500 text-red-600 dark:text-red-400 text-[10px] font-bold cursor-pointer"
                >
                  Reset
                </button>
              </div>
            </div>

            {/* Modal Content / Options List */}
            <div className="p-4 overflow-y-auto space-y-2 flex-1 studio-scroll">
              {PAYMENT_RAIL_OPTIONS.map((opt) => {
                const isSelected = selectedPaymentRailIds.has(opt.id);
                return (
                  <div
                    key={opt.id}
                    onClick={() => handleTogglePaymentRail(opt.id)}
                    className={`w-full p-3 text-left border rounded-none transition flex items-start justify-between gap-3 cursor-pointer ${
                      isSelected
                        ? 'bg-emerald-500/10 border-emerald-500 ring-1 ring-emerald-500 text-zinc-950 dark:text-zinc-50'
                        : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 text-zinc-800 dark:text-zinc-200'
                    }`}
                  >
                    <div className="flex items-start gap-2.5 min-w-0">
                      {/* Custom Checkbox */}
                      <div
                        className={`mt-0.5 w-4 h-4 shrink-0 flex items-center justify-center border transition ${
                          isSelected
                            ? 'bg-emerald-600 border-emerald-600 text-white'
                            : 'border-zinc-400 dark:border-zinc-600 bg-white dark:bg-zinc-900'
                        }`}
                      >
                        {isSelected && <Check className="w-3 h-3 stroke-[3]" />}
                      </div>

                      <div className="min-w-0">
                        <div className="flex items-center gap-2 mb-1 flex-wrap">
                          <span className="text-xs font-black font-mono text-zinc-950 dark:text-zinc-100">
                            {opt.name}
                          </span>
                          <span className={`text-[9px] font-mono px-1.5 py-0.5 border ${opt.badgeClass} font-bold uppercase`}>
                            {opt.badge}
                          </span>
                        </div>
                        <p className="text-[11px] text-zinc-600 dark:text-zinc-400 leading-tight font-sans">
                          {opt.desc}
                        </p>
                        <span className="text-[10px] font-mono text-zinc-500 dark:text-zinc-400 mt-1 block truncate">
                          → {opt.text}
                        </span>
                      </div>
                    </div>

                    <div className="shrink-0 flex items-center">
                      <span
                        className={`text-[10px] font-mono px-2 py-1 border font-bold uppercase transition ${
                          isSelected
                            ? 'bg-emerald-600 text-white border-emerald-600'
                            : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700'
                        }`}
                      >
                        {isSelected ? '✓ Terpilih' : '+ Pilih'}
                      </span>
                    </div>
                  </div>
                );
              })}
            </div>

            {/* Modal Footer */}
            <div className="p-3 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 flex flex-col sm:flex-row items-center justify-between gap-2">
              <span className="text-[11px] text-zinc-600 dark:text-zinc-400 font-mono">
                <strong>{selectedPaymentRailIds.size} rel</strong> akan disematkan ke field <strong>Kebutuhan Integrasi</strong>.
              </span>
              <div className="flex items-center gap-2 w-full sm:w-auto justify-end">
                <button
                  type="button"
                  onClick={() => {
                    onClose();
                    setPendingPaymentSuggestion(null);
                  }}
                  className="px-3 py-1.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-xs font-mono font-bold text-zinc-800 dark:text-zinc-200 rounded-none cursor-pointer"
                >
                  Batal
                </button>
                <button
                  type="button"
                  disabled={selectedPaymentRailIds.size === 0}
                  onClick={onApply}
                  className={`px-4 py-1.5 font-mono text-xs font-black rounded-none cursor-pointer uppercase transition ${
                    selectedPaymentRailIds.size > 0
                      ? 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs'
                      : 'bg-zinc-300 dark:bg-zinc-800 text-zinc-500 cursor-not-allowed'
                  }`}
                >
                  {selectedPaymentRailIds.size > 0
                    ? `Terapkan ${selectedPaymentRailIds.size} Rel Pembayaran →`
                    : 'Pilih Minimal 1 Rel'}
                </button>
              </div>
            </div>
          </div>
        </div>
  );
}
