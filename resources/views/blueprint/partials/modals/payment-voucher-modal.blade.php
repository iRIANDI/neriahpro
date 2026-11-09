    <!-- Interactive Midtrans Payment & Voucher Modal -->
    <div 
        x-show="paymentModalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 font-mono text-xs"
        @keydown.escape.window="paymentModalOpen = false"
    >
        <div 
            class="bg-white dark:bg-zinc-900 border-2 border-emerald-500 max-w-lg w-full p-6 shadow-2xl relative rounded-none"
            @click.outside="paymentModalOpen = false"
        >
            <!-- Close Button -->
            <button 
                @click="paymentModalOpen = false" 
                class="absolute top-4 right-4 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-lg font-bold"
            >
                &times;
            </button>

            <!-- Modal Header -->
            <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-4">
                <span class="px-2 py-0.5 bg-emerald-500 text-black text-[10px] font-bold uppercase tracking-wider rounded-none">
                    MIDTRANS SECURE PAYMENT &amp; VOUCHER PORTAL
                </span>
                <h3 class="text-xl font-black uppercase text-zinc-900 dark:text-zinc-100 mt-2">
                    <span x-show="locale === 'en'">DP Payment &amp; Claim Instructions</span>
                    <span x-show="locale !== 'en'">Instruksi Pembayaran DP &amp; Klaim</span>
                </h3>
                <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-1 font-sans">
                    <span x-show="locale === 'en'">Project:</span><span x-show="locale !== 'en'">Proyek:</span> <strong>{{ $blueprint->nama_bisnis ?: $blueprint->client_name }}</strong>
                </p>
            </div>

            <!-- Invoice Summary Card -->
            <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 mb-4 space-y-2 rounded-none">
                @php
                    $contractDocModal = $contractDocument ?? $blueprint->getContractDocument();
                    $hasSignedContractModal = $contractDocModal && $contractDocModal->status === 'signed' && (float)$contractDocModal->contract_amount > 0;
                @endphp
                @if($hasSignedContractModal)
                    <div class="p-2.5 bg-cyan-500/10 border border-cyan-500/30 text-cyan-800 dark:text-cyan-300 text-xs mb-2">
                        <strong class="font-bold flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span x-show="locale === 'en'">OFFICIAL BINDING LEGAL CONTRACT</span>
                            <span x-show="locale !== 'en'">KONTRAK KERJA SAMA RESMI SAH &amp; TERIKAT</span>
                        </strong>
                        <span class="text-[11px] block mt-0.5">
                            <span x-show="locale === 'en'">Down payment invoice (50% DP) is automatically adjusted to the agreed and digitally signed contract amount.</span>
                            <span x-show="locale !== 'en'">Tagihan uang muka (DP 50%) disesuaikan secara otomatis dengan nilai kontrak yang telah disepakati dan ditandatangani.</span>
                        </span>
                    </div>
                    <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                        <span><span x-show="locale === 'en'">Signed Contract Tier</span><span x-show="locale !== 'en'">Paket Kontrak Sah</span></span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100" x-text="(locale === 'en' ? tierAmounts[selectedTier]?.name_en : tierAmounts[selectedTier]?.name_id) || tierAmounts[selectedTier]?.name || '{{ $contractDocModal->title }}'"></span>
                    </div>
                    <div class="flex justify-between items-baseline text-zinc-600 dark:text-zinc-400">
                        <span><span x-show="locale === 'en'">Total Contract Value</span><span x-show="locale !== 'en'">Nilai Total Kontrak</span></span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100">Rp {{ number_format($contractDocModal->contract_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                        <span><span x-show="locale === 'en'">Down Payment Term</span><span x-show="locale !== 'en'">Termin DP (Uang Muka)</span></span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400"><span x-show="locale === 'en'">50% Upfront</span><span x-show="locale !== 'en'">50% di Muka</span></span>
                    </div>
                    <div class="border-t border-zinc-200 dark:border-zinc-800 pt-2 flex justify-between items-baseline">
                        <span class="font-bold uppercase text-zinc-900 dark:text-zinc-100"><span x-show="locale === 'en'">Total DP Invoice</span><span x-show="locale !== 'en'">Total Tagihan DP</span></span>
                        <div class="text-right">
                            <span class="text-lg font-black text-emerald-600 dark:text-emerald-400">Rp {{ number_format($contractDocModal->dp_amount ?: ($contractDocModal->contract_amount * 0.50), 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="text-[10px] text-zinc-400 pt-1 flex justify-between">
                        <span>ORDER ID: {{ $contractDocModal->midtrans_order_id ?: ('NPRO-DP-' . strtoupper(substr($blueprint->id, 0, 8))) }}</span>
                        <span>STATUS: <span x-show="locale === 'en'">SIGNED CONTRACT</span><span x-show="locale !== 'en'">KONTRAK SAH</span></span>
                    </div>
                @else
                    <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                        <span><span x-show="locale === 'en'">Selected Velocity Option</span><span x-show="locale !== 'en'">Opsi Velocity Terpilih</span></span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100" x-text="(locale === 'en' ? tierAmounts[selectedTier]?.name_en : tierAmounts[selectedTier]?.name_id) || tierAmounts[selectedTier]?.name || 'Standard Velocity'"></span>
                    </div>
                    <div class="flex justify-between items-baseline text-zinc-600 dark:text-zinc-400">
                        <span><span x-show="locale === 'en'">Total Contract Value</span><span x-show="locale !== 'en'">Nilai Total Kontrak</span></span>
                        <div>
                            <span x-show="appliedVoucher" class="line-through text-zinc-400 text-xs mr-1.5" x-text="'Rp ' + (tierAmounts[selectedTier]?.contract || 0).toLocaleString('id-ID')"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100" :class="appliedVoucher ? 'text-emerald-600 dark:text-emerald-400 font-black' : ''" x-text="'Rp ' + getDiscountedContract(selectedTier).toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                    <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                        <span><span x-show="locale === 'en'">Down Payment Term</span><span x-show="locale !== 'en'">Termin DP (Uang Muka)</span></span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400"><span x-show="locale === 'en'">50% Upfront</span><span x-show="locale !== 'en'">50% di Muka</span></span>
                    </div>

                    <!-- Voucher Status Row (If Applied) -->
                    <template x-if="appliedVoucher">
                        <div class="flex justify-between items-center text-emerald-600 dark:text-emerald-400 font-bold border-t border-dashed border-zinc-200 dark:border-zinc-800 pt-2 text-xs">
                            <span x-text="'Subsidi Voucher (' + appliedVoucher.code + ')'"></span>
                            <span x-text="appliedVoucher.is_free_bypass ? '-100% (FREE BYPASS)' : (appliedVoucher.discount_type === 'percent' ? '-' + appliedVoucher.discount_value + '% (-Rp ' + getDiscountAmount(selectedTier).toLocaleString('id-ID') + ')' : '-Rp ' + getDiscountAmount(selectedTier).toLocaleString('id-ID'))"></span>
                        </div>
                    </template>

                    <div class="border-t border-zinc-200 dark:border-zinc-800 pt-2 flex justify-between items-baseline">
                        <span class="font-bold uppercase text-zinc-900 dark:text-zinc-100"><span x-show="locale === 'en'">Total DP Invoice</span><span x-show="locale !== 'en'">Total Tagihan DP</span></span>
                        <template x-if="appliedVoucher && appliedVoucher.is_free_bypass">
                            <span class="text-lg font-black text-emerald-500"><span x-show="locale === 'en'">RP 0 (FREE)</span><span x-show="locale !== 'en'">RP 0 (GRATIS)</span></span>
                        </template>
                        <template x-if="!appliedVoucher || !appliedVoucher.is_free_bypass">
                            <div class="text-right">
                                <span x-show="appliedVoucher" class="line-through text-zinc-400 text-xs block" x-text="'Rp ' + (tierAmounts[selectedTier]?.dp || 0).toLocaleString('id-ID')"></span>
                                <span class="text-lg font-black text-emerald-600 dark:text-emerald-400" x-text="'Rp ' + getDiscountedDp(selectedTier).toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                    </div>
                    <div class="text-[10px] text-zinc-400 pt-1 flex justify-between">
                        <span>ORDER ID: NPRO-DP-{{ strtoupper(substr($blueprint->id, 0, 8)) }}</span>
                        <span>SPEC ID: {{ strtoupper(substr($blueprint->id, 0, 8)) }}</span>
                    </div>
                @endif
            </div>

            <!-- Voucher Promo / Pelayanan Input Block -->
            <div class="border border-dashed border-zinc-300 dark:border-zinc-700 p-3 mb-4 bg-zinc-50 dark:bg-zinc-950/60 rounded-none">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold text-[11px] uppercase tracking-wider text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        <span><span x-show="locale === 'en'">Have a Promo Code / Service Voucher?</span><span x-show="locale !== 'en'">Punya Kode Promo / Voucher Pelayanan?</span></span>
                    </span>
                    <span x-show="appliedVoucher" class="text-[10px] px-1.5 py-0.5 bg-emerald-500/20 text-emerald-400 font-bold border border-emerald-500/40 rounded-none">
                        <span x-show="locale === 'en'">VOUCHER ACTIVE</span><span x-show="locale !== 'en'">VOUCHER AKTIF</span>
                    </span>
                </div>
                <div class="flex gap-2">
                    <input 
                        type="text" 
                        x-model="voucherCode" 
                        :disabled="appliedVoucher !== null || isValidatingVoucher"
                        @keydown.enter.prevent="applyVoucher()"
                        :placeholder="locale === 'en' ? 'Example: MINISTRY-GRANT' : 'Contoh: PELAYANAN-KASIH'" placeholder="Contoh: PELAYANAN-KASIH"
                        class="flex-1 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2.5 py-2 text-xs font-mono uppercase focus:outline-none focus:border-emerald-500 rounded-none"
                    />
                    <button 
                        type="button" 
                        x-show="!appliedVoucher"
                        @click="applyVoucher()"
                        :disabled="isValidatingVoucher || !voucherCode.trim()"
                        class="bg-zinc-800 hover:bg-zinc-700 text-white font-mono font-bold text-xs uppercase px-3 py-2 disabled:opacity-50 transition border border-zinc-600 rounded-none"
                    >
                        <span x-show="!isValidatingVoucher"><span x-show="locale === 'en'">Apply</span><span x-show="locale !== 'en'">Terapkan</span></span>
                        <span x-show="isValidatingVoucher"><span x-show="locale === 'en'">Checking...</span><span x-show="locale !== 'en'">Cek...</span></span>
                    </button>
                    <button 
                        type="button" 
                        x-show="appliedVoucher" 
                        @click="resetVoucher()"
                        class="bg-rose-900/40 hover:bg-rose-800/60 text-rose-300 font-mono font-bold text-xs uppercase px-2.5 py-2 border border-rose-700/50 rounded-none"
                    >
                        Batal
                    </button>
                </div>
                <template x-if="appliedVoucher">
                    <div class="mt-2.5 p-2 bg-emerald-950/60 border border-emerald-600/60 text-emerald-300 text-[11px] font-mono leading-relaxed rounded-none">
                        <div class="font-bold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span x-text="locale === 'en' ? (appliedVoucher.code + ' SUCCESSFULLY APPLIED') : (appliedVoucher.code + ' BERHASIL DIGUNAKAN')"></span>
                        </div>
                        <div class="text-[10px] text-emerald-400 mt-0.5" x-text="appliedVoucher.description"></div>
                        <div x-show="appliedVoucher.is_free_bypass" class="mt-1 font-bold text-emerald-200">
                            <span x-show="locale === 'en'">&bull; Service Grant: Invoice Becomes Rp 0 &amp; Auto-Provision Staging Sandbox.</span><span x-show="locale !== 'en'">&bull; Subsidi Pelayanan: Tagihan Menjadi Rp 0 &amp; Auto-Provision Staging Sandbox.</span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Mandatory Legal Sign-Off & Midtrans Compliance Checkbox -->
            <label class="flex items-start gap-2.5 p-3 bg-zinc-100 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 cursor-pointer mb-4 select-none rounded-none hover:border-emerald-500 transition">
                <input 
                    type="checkbox" 
                    x-model="agreeSignOff" 
                    class="mt-0.5 rounded-none border-zinc-400 text-emerald-600 focus:ring-emerald-500 cursor-pointer"
                />
                <span class="text-[11px] leading-snug text-zinc-700 dark:text-zinc-300 font-sans">
                    <strong class="font-mono text-emerald-600 dark:text-emerald-400 block uppercase font-bold text-[10px] mb-0.5">
                        <span x-show="locale === 'en'">Agreement to Terms of Service, 30-Day SLA &amp; Midtrans Refund Policy</span><span x-show="locale !== 'en'">Persetujuan Syarat &amp; Ketentuan Layanan, Garansi SLA &amp; Refund Midtrans</span>
                    </strong>
                    <span x-show="locale === 'id'">
                        Saya menyetujui <button type="button" @click.stop.prevent="openLegalTermsModal('id')" class="text-emerald-600 dark:text-emerald-400 underline font-bold hover:text-emerald-500 cursor-pointer pointer-events-auto">Syarat &amp; Ketentuan Layanan</button>, 
                        <button type="button" @click.stop.prevent="openLegalTermsModal('id')" class="text-emerald-600 dark:text-emerald-400 underline font-bold hover:text-emerald-500 cursor-pointer pointer-events-auto">Kebijakan Garansi 30 Hari &amp; Refund</button>, 
                        <button type="button" @click.stop.prevent="openLegalTermsModal('id')" class="text-emerald-600 dark:text-emerald-400 underline font-bold hover:text-emerald-500 cursor-pointer pointer-events-auto">Penanganan Pembayaran Ganda</button>, serta 
                        penguncian spesifikasi scope dokumen <span class="font-mono font-bold text-zinc-900 dark:text-zinc-100">{{ strtoupper(substr($blueprint->id, 0, 10)) }}</span> (Scope Freeze).
                    </span>
                    <span x-show="locale === 'en'">
                        I agree to the <button type="button" @click.stop.prevent="openLegalTermsModal('en')" class="text-emerald-600 dark:text-emerald-400 underline font-bold hover:text-emerald-500 cursor-pointer pointer-events-auto">Terms of Service</button>, 
                        <button type="button" @click.stop.prevent="openLegalTermsModal('en')" class="text-emerald-600 dark:text-emerald-400 underline font-bold hover:text-emerald-500 cursor-pointer pointer-events-auto">30-Day SLA &amp; Refund Policy</button>, 
                        <button type="button" @click.stop.prevent="openLegalTermsModal('en')" class="text-emerald-600 dark:text-emerald-400 underline font-bold hover:text-emerald-500 cursor-pointer pointer-events-auto">Double-Payment Protection</button>, and 
                        locking scope specifications of document <span class="font-mono font-bold text-zinc-900 dark:text-zinc-100">{{ strtoupper(substr($blueprint->id, 0, 10)) }}</span> (Scope Freeze).
                    </span>
                </span>
            </label>

            <!-- Payment Methods Info (Only show if not free bypass) -->
            <div x-show="!appliedVoucher || !appliedVoucher.is_free_bypass" class="space-y-2 mb-4">
                <div class="text-[11px] font-bold uppercase text-zinc-700 dark:text-zinc-300">
                    <span x-show="locale === 'en'">Automated Payment Channels:</span><span x-show="locale !== 'en'">Kanal Pembayaran Otomatis:</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-[10px]">
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold rounded-none">QRIS (GoPay/OVO)</div>
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold rounded-none">BCA VA</div>
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold rounded-none">Mandiri Bill</div>
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold rounded-none">Kartu Kredit</div>
                </div>
            </div>

            <!-- Actions -->
            @if($blueprint->isDpConfirmed())
                @if($blueprint->isPelunasanConfirmed())
                    <div class="p-4 bg-emerald-950/40 border border-emerald-500/50 text-emerald-400 text-center space-y-2 rounded-none">
                        <div class="font-bold uppercase text-xs tracking-wider flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>PROYEK LUNAS 100% (SETTLEMENT COMPLETED)</span>
                        </div>
                        <p class="text-xs text-zinc-300 font-sans">
                            Seluruh termin pembayaran (DP 50% &amp; Pelunasan 50%) telah lunas terverifikasi. Seluruh hak source code, repositori, dan server produksi aktif penuh.
                        </p>
                        <button 
                            type="button" 
                            @click="paymentModalOpen = false" 
                            class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-bold py-3 px-4 text-xs uppercase cursor-pointer rounded-none"
                        ><span x-show="locale === 'en'">Close Modal</span><span x-show="locale !== 'en'">Tutup Modal</span></button>
                    </div>
                @else
                    <div class="p-4 bg-zinc-900 border border-amber-500/50 text-center space-y-3 rounded-none">
                        <div class="font-bold uppercase text-xs tracking-wider flex items-center justify-center gap-1.5 text-emerald-400">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>PEMBAYARAN DP SUDAH TERKONFIRMASI</span>
                        </div>
                        <p class="text-xs text-zinc-300 font-sans">
                            Uang muka (DP 50%) telah terverifikasi. Apabila tahapan pengerjaan sprint telah selesai atau siap serah terima final, Anda dapat menyelesaikan Pelunasan sisa 50% di bawah ini.
                        </p>
                        <div class="p-2.5 bg-black/50 border border-zinc-800 font-mono text-xs flex items-center justify-between">
                            <span class="text-zinc-400">Nilai Pelunasan 50%:</span>
                            <span class="font-bold text-amber-400 text-sm">Rp {{ number_format($blueprint->getFinalPelunasanAmount(), 0, ',', '.') }}</span>
                        </div>
                        <button 
                            type="button" 
                            @click="window.payPelunasanSnap('{{ $blueprint->slug }}')" 
                            class="w-full bg-amber-500 hover:bg-amber-400 text-black font-black py-3.5 px-4 text-xs uppercase tracking-wider cursor-pointer rounded-none transition shadow-lg flex items-center justify-center gap-2"
                        >
                            <span>💳</span>
                            <span>Bayar Pelunasan 50% via Midtrans Snap &rarr;</span>
                        </button>
                        <button 
                            type="button" 
                            @click="paymentModalOpen = false" 
                            class="w-full bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold py-2.5 px-4 text-xs uppercase cursor-pointer rounded-none"
                        ><span x-show="locale === 'en'">Close Modal</span><span x-show="locale !== 'en'">Tutup Modal</span></button>
                    </div>
                @endif
            @else
                <div class="space-y-2">
                    <!-- Free Voucher Bypass Claim Button -->
                    <template x-if="appliedVoucher && appliedVoucher.is_free_bypass">
                        <button 
                            type="button" 
                            @click="submitClaimVoucher()"
                            :disabled="isClaimingVoucher || !agreeSignOff"
                            class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase tracking-wider py-4 px-4 text-center block transition shadow-lg cursor-pointer disabled:opacity-50 rounded-none"
                        >
                            <span x-show="!isClaimingVoucher">KLAIM PELAYANAN GRATIS SEKARANG (Rp 0) &rarr;</span>
                            <span x-show="isClaimingVoucher" class="inline-block animate-pulse">Mengaktifkan Sandbox &amp; Kontrak...</span>
                        </button>
                    </template>

                    <!-- Standard Midtrans Snap Payment Button -->
                    <template x-if="!appliedVoucher || !appliedVoucher.is_free_bypass">
                        <button 
                            type="button" 
                            @click="isPayingSnap = true; window.payBlueprintSnap(selectedTier, agreeSignOff, voucherCode, () => { isPayingSnap = true }, () => { isPayingSnap = false })"
                            :disabled="isPayingSnap || !agreeSignOff"
                            class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase tracking-wider py-3.5 px-4 text-center block transition shadow-lg cursor-pointer disabled:opacity-50 rounded-none"
                        >
                            <span x-show="!isPayingSnap" x-text="appliedVoucher ? 'Bayar DP Sekarang (Rp ' + getDiscountedDp(selectedTier).toLocaleString('id-ID') + ') &rarr;' : 'Bayar Sekarang via Midtrans Snap &rarr;'"></span>
                            <span x-show="isPayingSnap" class="inline-block animate-pulse">Membuat Sesi Snap...</span>
                        </button>
                    </template>
                    
                    <form method="POST" action="{{ route('cart.add', $blueprint->slug) }}" class="m-0">
                        @csrf
                        <input type="hidden" name="tier" :value="selectedTier">
                        <input type="hidden" name="voucher" :value="voucherCode">
                        <button 
                            type="submit" 
                            class="w-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold text-xs uppercase tracking-wider py-2.5 px-4 text-center block transition border border-zinc-300 dark:border-zinc-700 rounded-none cursor-pointer"
                        >
                            <span x-show="locale === 'en'">Save to Shopping Cart</span><span x-show="locale !== 'en'">Simpan ke Cart Belanja</span>
                        </button>
                    </form>

                    @if(auth()->check() && auth()->user()->isSuperAdmin())
                        <div class="pt-3 border-t-2 border-dashed border-amber-500/40 bg-amber-500/5 dark:bg-amber-500/10 p-3 text-left">
                            <div class="flex items-center gap-1.5 mb-1.5">
                                <span class="px-1.5 py-0.5 bg-amber-500 text-black font-mono font-black text-[10px] uppercase">👑 VIP FOUNDER ACCESS</span>
                                <span class="text-[11px] font-mono text-amber-600 dark:text-amber-400 font-bold">1-Click Instant Activation</span>
                            </div>
                            <p class="text-[11px] font-mono text-zinc-600 dark:text-zinc-300 mb-2 leading-tight">
                                Aktifkan blueprint ini langsung ke status <strong>Active Sprint</strong> &amp; buka 15 Pilar Project OS secara instan tanpa gateway pembayaran.
                            </p>
                            <form method="POST" action="{{ route('blueprint.vip-bypass', $blueprint->slug) }}" class="m-0">
                                @csrf
                                <button 
                                    type="submit" 
                                    class="w-full bg-zinc-900 hover:bg-black dark:bg-amber-400 dark:hover:bg-amber-300 text-white dark:text-black font-mono font-black text-xs uppercase tracking-wider py-2.5 px-3 text-center flex items-center justify-center gap-2 transition cursor-pointer border border-amber-500/60 rounded-none shadow-sm"
                                >
                                    <span>👑 BYPASS PEMBAYARAN (AKTIFKAN VIP OWNER) &rarr;</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

