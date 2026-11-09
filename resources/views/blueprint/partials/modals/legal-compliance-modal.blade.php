    <!-- MIDTRANS COMPLIANCE & LEGAL TERMS MODAL (TIER 1 BILINGUAL ID & EN) -->
    <div 
        x-show="showLegalTermsModal" 
        x-cloak 
        @click.self="closeLegalTermsModal()"
        @keydown.escape.window="closeLegalTermsModal()"
        class="fixed inset-0 z-[160] overflow-y-auto bg-black/85 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6 no-print font-sans"
        style="display: none;"
    >
        <!-- Dialog Box -->
        <div 
            class="relative z-20 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 max-w-3xl w-full my-auto max-h-[88vh] flex flex-col p-6 rounded-none text-zinc-900 dark:text-zinc-100 shadow-2xl pointer-events-auto"
        >
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4 shrink-0">
                <div>
                    <h3 class="text-base sm:text-lg font-black uppercase text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
                        <span x-show="legalTermsTab === 'en'">SERVICE COMPLIANCE &amp; MIDTRANS SNAP PAYMENT</span>
                        <span x-show="legalTermsTab !== 'en'">KEPATUHAN LAYANAN &amp; MIDTRANS SNAP PAYMENT</span>
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">
                        <span x-show="legalTermsTab === 'en'">Consumer Transparency, 30-Day SLA Warranty, &amp; Transaction Idempotency Handling</span>
                        <span x-show="legalTermsTab !== 'en'">Transparansi Konsumen, Garansi SLA 30 Hari, &amp; Penanganan Idempotency Transaksi</span>
                    </p>
                </div>
                <!-- Lang Switcher inside modal -->
                <div class="flex items-center gap-1.5 font-mono text-xs">
                    <button 
                        type="button" 
                        @click="legalTermsTab = 'id'" 
                        :class="legalTermsTab === 'id' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                        class="px-2.5 py-1 rounded-none border border-zinc-300 dark:border-zinc-700 cursor-pointer pointer-events-auto transition"
                    >
                        ID
                    </button>
                    <button 
                        type="button" 
                        @click="legalTermsTab = 'en'" 
                        :class="legalTermsTab === 'en' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                        class="px-2.5 py-1 rounded-none border border-zinc-300 dark:border-zinc-700 cursor-pointer pointer-events-auto transition"
                    >
                        EN
                    </button>
                    <button 
                        type="button" 
                        @click="closeLegalTermsModal()" 
                        class="ml-2 text-zinc-400 hover:text-zinc-700 dark:hover:text-white text-lg font-bold px-2 cursor-pointer pointer-events-auto"
                        title="Tutup (ESC)"
                    >
                        &times;
                    </button>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div class="overflow-y-auto space-y-4 pr-1 text-xs leading-relaxed text-zinc-700 dark:text-zinc-300">
                <!-- Tab ID -->
                <div x-show="legalTermsTab === 'id'" class="space-y-3">
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                            <span class="text-emerald-500 font-mono">01.</span> Syarat &amp; Ketentuan Layanan (Terms of Service)
                        </h4>
                        <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400 font-sans">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_terms_content_id', "1. Lisensi & Hak Cipta: Setiap blueprint dan kode sumber yang telah dilunasi menjadi hak milik penuh klien.\n2. Batasan Revisi: Revisi spesifikasi gratis dibatasi sesuai tier yang dipilih (Spark 2x/bln, Lite 30 hari, Pro 6 bulan, Ultimate 1 tahun).\n3. Penguncian Scope: Setelah uang muka (DP) 50% atau pelunasan terkonfirmasi, ruang lingkup proyek dikunci (scope frozen) untuk menjaga ketepatan waktu sprint engineering.") }}
                        </div>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                            <span class="text-emerald-500 font-mono">02.</span> Jaminan Garansi &amp; Kebijakan Refund (30 Hari SLA)
                        </h4>
                        <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400 font-sans">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_refund_policy_id', "1. Garansi SLA: Paket Turnkey Studio MVP dilindungi 30 Hari Garansi Bug pasca peluncuran resmi.\n2. Jaminan Refund 100%: Pengembalian dana penuh 100% berlaku jika terjadi kegagalan teknis fatal dari pihak Neriah Pro sebelum dimulainya sprint pengembangan.\n3. Non-Refundable Post-Delivery: Karena produk digital dan blueprint arsitektur bersifat kekayaan intelektual langsung pakai, pembayaran yang telah diselesaikan setelah serah terima berkas tidak dapat dikembalikan sepihak.") }}
                        </div>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                            <span class="text-emerald-500 font-mono">03.</span> Penanganan Pembayaran Ganda (Double-Payment Anti-Collision)
                        </h4>
                        <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400 font-sans">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_double_payment_policy_id', "1. Deteksi Idempotency: Sistem Neriah Pro mendeteksi setiap transaksi menggunakan ID pesanan unik untuk mencegah duplikasi.\n2. Reversal Otomatis: Jika pelanggan tidak sengaja melakukan transfer ganda melalui gateway bank, sistem otomatis mencatat di Dead Letter Queue (DLQ).\n3. Pengembalian Dana: Kelebihan pembayaran akan diverifikasi dan dikembalikan ke rekening asal dalam 3 - 5 hari kerja tanpa potongan biaya sistem.") }}
                        </div>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                            <span class="text-emerald-500 font-mono">04.</span> Kebijakan Pembatalan Proyek (Cancellation Terms)
                        </h4>
                        <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400 font-sans">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_cancellation_policy_id', "1. Sebelum Kickoff / DP: Pembatalan pesanan dapat dilakukan kapan saja tanpa penalti biaya.\n2. Pasca Pembayaran DP 50%: Jika klien membatalkan proyek secara sepihak saat sprint pengembangan telah berlangsung, DP yang telah dibayarkan dialokasikan untuk kompensasi jam kerja arsitek (non-refundable), namun seluruh berkas blueprint dan kode yang telah dikerjakan tetap diserahkan kepada klien.") }}
                        </div>
                    </div>
                </div>

                <!-- Tab EN -->
                <div x-show="legalTermsTab === 'en'" class="space-y-3">
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                            <span class="text-emerald-500 font-mono">01.</span> Terms of Service
                        </h4>
                        <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400 font-sans">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_terms_content_en', "1. License & Intellectual Property: Blueprints and source code settled in full are 100% owned by the client.\n2. Revision Window: Free AI revisions are limited by tier (Spark 2x/mo, Lite 30 days, Pro 6 months, Ultimate 1 year).\n3. Scope Locking: Once 50% DP or full settlement is confirmed, project scope is frozen to ensure engineering milestone delivery.") }}
                        </div>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                            <span class="text-emerald-500 font-mono">02.</span> Warranty &amp; Refund Policy (30-Day Bug SLA)
                        </h4>
                        <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400 font-sans">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_refund_policy_en', "1. SLA Warranty: Studio MVP Turnkey packages include a 30-Day Bug Warranty after official deployment.\n2. 100% Refund Guarantee: Full 100% refund applies if critical technical failure occurs on Neriah Pro's side prior to sprint commencement.\n3. Non-Refundable Post-Delivery: Due to the intellectual nature of digital blueprints, fees paid after document delivery are non-refundable for unilateral client cancellations.") }}
                        </div>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                            <span class="text-emerald-500 font-mono">03.</span> Double-Payment Anti-Collision Policy
                        </h4>
                        <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400 font-sans">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_double_payment_policy_en', "1. Idempotency Detection: Neriah Pro utilizes unique order IDs to prevent duplicate transaction charges.\n2. Automated Reversal: If duplicate transfers occur due to bank network retries, the event is trapped in the Dead Letter Queue (DLQ).\n3. Refund Timeline: Excess payments are verified and reimbursed to the source account within 3 - 5 business days with zero deduction.") }}
                        </div>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                            <span class="text-emerald-500 font-mono">04.</span> Project Cancellation Policy
                        </h4>
                        <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400 font-sans">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_cancellation_policy_en', "1. Prior to Kickoff / DP: Orders can be cancelled anytime with zero penalty.\n2. Post-DP 50% Kickoff: If the client cancels unilaterally while development sprints are active, the DP is allocated toward incurred engineering hours (non-refundable), while all produced blueprints and source code remain delivered to the client.") }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-zinc-200 dark:border-zinc-800 pt-3 mt-4 flex items-center justify-between shrink-0">
                <span class="text-[10px] text-zinc-500 font-mono">NERIAH PRO &bull; MIDTRANS PAYMENT COMPLIANCE</span>
                <button 
                    type="button" 
                    @click="agreeSignOff = true; closeLegalTermsModal()" 
                    class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-bold font-mono text-xs uppercase rounded-none transition cursor-pointer pointer-events-auto"
                >
                    Saya Mengerti &amp; Setuju
                </button>
            </div>
        </div>
    </div>

