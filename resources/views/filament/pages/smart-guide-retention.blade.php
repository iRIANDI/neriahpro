<x-filament-panels::page>
    <div class="space-y-6" x-data="unitEconomicsCalculator()">

        <!-- Security & Confidentiality Banner -->
        <div class="relative overflow-hidden bg-gradient-to-r from-amber-950/40 via-zinc-900 to-zinc-900 border border-amber-500/30 p-5 rounded-sm shadow-xl">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xs bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 font-mono text-lg font-black">
                        🔒
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-xs text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                Strictly Confidential // Founder Only
                            </span>
                            <span class="text-xs text-zinc-400 font-mono">
                                Auth: yoseph.iriandi.tambunan@gmail.com
                            </span>
                        </div>
                        <h2 class="text-base font-bold text-white mt-1">
                            Sistem Perlindungan Margin & Panduan Retensi Bisnis Neriah Pro
                        </h2>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xs text-xs font-mono font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                        <span class="w-2 h-2 rounded-xs bg-emerald-400 animate-pulse"></span>
                        Akses Terkunci: Reviewer Midtrans Dilarang
                    </span>
                </div>
            </div>
            <p class="text-xs text-zinc-400 mt-3 leading-relaxed">
                Halaman ini berisi rahasia unit economics, arsitektur margin laba 98%+, playbook konversi upsell kontrak Studio Rp 50.000.000, serta penegakan batasan teknis per tier. Akun audit/reviewer Midtrans (<code class="text-amber-300">reviewer.midtrans@neriahpro.com</code>) dan akun non-superadmin diblokir 100% dari URL ini dengan HTTP 403 Forbidden.
            </p>
        </div>

        <!-- Jawaban Inti: Mengapa Lifetime Tidak Merugikan Neriah Pro? -->
        <div class="bg-zinc-900/90 border border-zinc-800 p-6 rounded-sm shadow-lg">
            <div class="flex items-center justify-between border-b border-zinc-800 pb-4 mb-4">
                <div>
                    <span class="text-[11px] font-mono uppercase tracking-wider text-indigo-400 font-semibold">
                        Pertanyaan Strategis Founder
                    </span>
                    <h3 class="text-lg font-bold text-white mt-0.5">
                        "Kalau Lifetime Itu Apa Gak Rugi Saya? Bagaimana Marginnya? Apa Mereka Tidak Bayar Lagi?"
                    </h3>
                </div>
                <span class="text-xs font-mono px-3 py-1 bg-indigo-500/10 text-indigo-300 border border-indigo-500/30 rounded-xs">
                    Gross Margin: 98.6%
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Pilar 1 -->
                <div class="bg-zinc-950 p-4 rounded-xs border border-zinc-800 space-y-2">
                    <div class="text-2xl font-black text-indigo-400 font-mono">01</div>
                    <h4 class="text-sm font-bold text-zinc-100">Prinsip Pay-Per-Project</h4>
                    <p class="text-xs text-zinc-400 leading-relaxed">
                        <strong>Bukan Langganan Bikin Proyek Unlimited!</strong> 1 transaksi (misal: Rp 399.000) strictly hanya berlaku untuk <strong>1 Entitas Proyek</strong>. Jika bulan depan klien ingin membangun ide sistem kedua atau ketiga, mereka <strong>wajib membayar kembali</strong> untuk lisensi proyek berikutnya.
                    </p>
                </div>

                <!-- Pilar 2 -->
                <div class="bg-zinc-950 p-4 rounded-xs border border-zinc-800 space-y-2">
                    <div class="text-2xl font-black text-cyan-400 font-mono">02</div>
                    <h4 class="text-sm font-bold text-zinc-100">Zero Marginal Cost ($O(1)$)</h4>
                    <p class="text-xs text-zinc-400 leading-relaxed">
                        "Hak Unduh Selamanya" hanya berarti klien dapat mengunduh arsip PDF, Markdown, atau Scaffold dari <strong>proyek yang sudah di-generate</strong>. Mengunduh data yang sudah tersimpan di database PostgreSQL menghabiskan biaya AI <strong>Rp 0</strong>. Beban server untuk 50KB teks mendekati Rp 0.
                    </p>
                </div>

                <!-- Pilar 3 -->
                <div class="bg-zinc-950 p-4 rounded-xs border border-zinc-800 space-y-2">
                    <div class="text-2xl font-black text-emerald-400 font-mono">03</div>
                    <h4 class="text-sm font-bold text-zinc-100">Trojan Horse Upsell Rp 50 Juta</h4>
                    <p class="text-xs text-zinc-400 leading-relaxed">
                        Blueprint adalah mesin filter klien berdana (Zero-CAC Lead Magnet). Dari setiap 20 pembeli blueprint mandiri, 1-2 founder akan mengajukan kontrak <strong>Studio Turnkey Monolith MVP seharga Rp 50.000.000</strong> karena mereka tidak memiliki in-house developer.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabel Perbandingan Unit Economics & Biaya Riil (COGS) -->
        <div class="bg-zinc-900/90 border border-zinc-800 p-6 rounded-sm shadow-lg">
            <h3 class="text-base font-bold text-white mb-2">
                Rincian Biaya Riil (COGS) vs Harga Jual per Transaksi
            </h3>
            <p class="text-xs text-zinc-400 mb-4">
                Biaya komputasi AI menggunakan Google Gemini 1.5 Pro via Multi-AI Model Manager, Midtrans fee QRIS (0.7%) / Virtual Account (Rp 2.000 - Rp 4.000), dan penyimpanan PostgreSQL 16.
            </p>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-950 text-zinc-300 font-mono uppercase tracking-wider border-b border-zinc-800">
                        <tr>
                            <th class="p-3">Paket Retail</th>
                            <th class="p-3">Harga Jual</th>
                            <th class="p-3">Biaya AI (Tokens)</th>
                            <th class="p-3">Biaya Payment</th>
                            <th class="p-3">Laba Bersih per Unit</th>
                            <th class="p-3">Gross Margin</th>
                            <th class="p-3">Siklus Repeat Order</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800 text-zinc-300">
                        <tr class="hover:bg-zinc-800/40">
                            <td class="p-3 font-semibold text-zinc-100">Spark (Lead Magnet)</td>
                            <td class="p-3 font-mono font-bold text-zinc-400">{{ $pricingSettings['spark_price'] }}</td>
                            <td class="p-3 font-mono text-zinc-400">~Rp 500 (10k tok)</td>
                            <td class="p-3 font-mono text-zinc-400">Rp 0</td>
                            <td class="p-3 font-mono text-amber-400">-Rp 500 (CAC)</td>
                            <td class="p-3 font-mono text-zinc-500">N/A (2x/bln limit)</td>
                            <td class="p-3 text-zinc-400">Auto-reset tgl 1 tiap bulan</td>
                        </tr>
                        <tr class="hover:bg-zinc-800/40">
                            <td class="p-3 font-semibold text-zinc-100">Lite Blueprint</td>
                            <td class="p-3 font-mono font-bold text-cyan-400">{{ $pricingSettings['lite_price'] }}</td>
                            <td class="p-3 font-mono text-zinc-400">~Rp 1.000 (20k tok)</td>
                            <td class="p-3 font-mono text-zinc-400">~Rp 2.000 (Midtrans)</td>
                            <td class="p-3 font-mono font-bold text-emerald-400">Rp 96.000</td>
                            <td class="p-3 font-mono font-bold text-emerald-400">97.0%</td>
                            <td class="p-3 text-zinc-400">Beli lagi per ide proyek baru</td>
                        </tr>
                        <tr class="hover:bg-zinc-800/40 bg-indigo-950/20 border-l-2 border-indigo-500">
                            <td class="p-3 font-bold text-indigo-300">Pro Blueprint (Rekomendasi)</td>
                            <td class="p-3 font-mono font-bold text-indigo-400">{{ $pricingSettings['pro_price'] }}</td>
                            <td class="p-3 font-mono text-zinc-400">~Rp 1.500 (30k tok)</td>
                            <td class="p-3 font-mono text-zinc-400">~Rp 4.000 (Midtrans)</td>
                            <td class="p-3 font-mono font-bold text-emerald-400">Rp 393.500</td>
                            <td class="p-3 font-mono font-bold text-emerald-400">98.6%</td>
                            <td class="p-3 text-zinc-400">Beli lagi per ide proyek baru</td>
                        </tr>
                        <tr class="hover:bg-zinc-800/40">
                            <td class="p-3 font-semibold text-purple-300">Ultimate Blueprint</td>
                            <td class="p-3 font-mono font-bold text-purple-400">{{ $pricingSettings['ultimate_price'] }}</td>
                            <td class="p-3 font-mono text-zinc-400">~Rp 3.000 (60k tok)</td>
                            <td class="p-3 font-mono text-zinc-400">~Rp 6.000 (Midtrans)</td>
                            <td class="p-3 font-mono font-bold text-emerald-400">Rp 1.481.000</td>
                            <td class="p-3 font-mono font-bold text-emerald-400">99.4%</td>
                            <td class="p-3 text-zinc-400">+ Upsell Studio Turnkey</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Interactive Margin & Upsell Simulator (Alpine.js) -->
        <div class="bg-zinc-900/90 border border-zinc-800 p-6 rounded-sm shadow-lg space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
                <div>
                    <h3 class="text-base font-bold text-white">
                        Kalkulator Proyeksi Omzet & Laba Bersih Neriah Pro
                    </h3>
                    <p class="text-xs text-zinc-400">
                        Geser slider untuk mensimulasikan volume penjualan paket Pro (Rp 399.000) dan potensi pipeline Studio MVP.
                    </p>
                </div>
                <span class="text-xs font-mono text-zinc-400">Model: Pay-Per-Project</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-xs font-semibold text-zinc-300 mb-1">
                            <span>Volume Penjualan Paket Pro / Bulan:</span>
                            <span class="font-mono text-indigo-400 text-sm font-bold" x-text="salesVolume + ' Proyek'"></span>
                        </div>
                        <input 
                            type="range" 
                            min="5" 
                            max="300" 
                            step="5" 
                            x-model="salesVolume" 
                            class="w-full accent-indigo-500 bg-zinc-950 rounded-xs cursor-pointer h-2"
                        />
                        <div class="flex justify-between text-[10px] text-zinc-500 mt-1 font-mono">
                            <span>5 unit</span>
                            <span>150 unit</span>
                            <span>300 unit</span>
                        </div>
                    </div>

                    <div class="bg-zinc-950 p-4 rounded-xs border border-zinc-800 space-y-2">
                        <div class="flex justify-between text-xs">
                            <span class="text-zinc-400">Omzet Kotor Penjualan:</span>
                            <span class="font-mono font-bold text-zinc-100" x-text="formatCurrency(salesVolume * 399000)"></span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-zinc-400">Total Biaya AI (Gemini 1.5 Pro):</span>
                            <span class="font-mono text-rose-400" x-text="formatCurrency(salesVolume * 1500)"></span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-zinc-400">Total Biaya Midtrans (Payment):</span>
                            <span class="font-mono text-rose-400" x-text="formatCurrency(salesVolume * 4000)"></span>
                        </div>
                        <div class="pt-2 border-t border-zinc-800 flex justify-between text-sm font-bold">
                            <span class="text-emerald-400">Laba Bersih Tunai (Cash):</span>
                            <span class="font-mono text-emerald-400" x-text="formatCurrency(salesVolume * 393500)"></span>
                        </div>
                    </div>
                </div>

                <!-- Pipeline Upsell Studio -->
                <div class="bg-gradient-to-br from-indigo-950/30 to-zinc-950 p-5 rounded-xs border border-indigo-500/30 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-xs text-[10px] font-mono font-bold uppercase tracking-wider bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                            The Real Goldmine // Studio MVP
                        </span>
                    </div>
                    <h4 class="text-sm font-bold text-white">
                        Proyeksi Konversi Klien Turnkey (Rp 50 Juta / Proyek)
                    </h4>
                    <p class="text-xs text-zinc-400 leading-relaxed">
                        Jika hanya <strong>5% (1 dari 20 pembeli)</strong> yang tidak mau coding sendiri dan menyewa Neriah Pro Engineering Studio:
                    </p>
                    <div class="p-3 bg-zinc-900/80 rounded-xs border border-zinc-800 space-y-1">
                        <div class="flex justify-between text-xs">
                            <span class="text-zinc-400">Estimasi Klien Studio MVP:</span>
                            <span class="font-mono font-bold text-indigo-300" x-text="Math.floor(salesVolume * 0.05) + ' Klien'"></span>
                        </div>
                        <div class="flex justify-between text-xs font-bold pt-1">
                            <span class="text-zinc-200">Pipeline Nilai Kontrak Studio:</span>
                            <span class="font-mono text-indigo-400 text-sm" x-text="formatCurrency(Math.floor(salesVolume * 0.05) * 50000000)"></span>
                        </div>
                    </div>
                    <div class="text-[11px] text-zinc-500 leading-tight">
                        *Inilah sebabnya paket Blueprint retail dijual terjangkau: untuk menarik ratusan founder serius, menyaring siapa yang punya modal, lalu meng-upsell mereka ke kontrak puluhan juta rupiah.
                    </div>
                </div>
            </div>
        </div>

        <!-- Matriks Batasan Teknis & Siklus Reset Real-time -->
        <div class="bg-zinc-900/90 border border-zinc-800 p-6 rounded-sm shadow-lg space-y-4">
            <h3 class="text-base font-bold text-white">
                Matriks Batasan Teknis, Kuota & Siklus Reset yang Ditegakkan Sistem
            </h3>
            <p class="text-xs text-zinc-400">
                Semua batasan di bawah ini telah dikunci pada level backend PHP dan middleware API untuk mencegah eksploitasi:
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <!-- Spark Card -->
                <div class="bg-zinc-950 p-4 rounded-xs border border-zinc-800 space-y-2">
                    <div class="font-mono font-bold text-zinc-300">SPARK (RP 0)</div>
                    <div class="text-[11px] text-zinc-400 space-y-1">
                        <div><strong>Batas Kuota:</strong> 2x analisis / bulan per IP/guest.</div>
                        <div><strong>Siklus Reset:</strong> Tanggal 1 tiap awal bulan (auto-reset).</div>
                        <div><strong>Sesi Tamu:</strong> 7 hari draft tersimpan di cache.</div>
                        <div><strong>Login:</strong> Bebas / Guest Mode (Tanpa Login).</div>
                    </div>
                </div>

                <!-- Lite Card -->
                <div class="bg-zinc-950 p-4 rounded-xs border border-zinc-800 space-y-2">
                    <div class="font-mono font-bold text-cyan-400">LITE (RP 99.000)</div>
                    <div class="text-[11px] text-zinc-400 space-y-1">
                        <div><strong>Batas Entitas:</strong> 1 Proyek PRD + DDL ULID.</div>
                        <div><strong>Jendela Revisi:</strong> 30 hari form draft editing.</div>
                        <div><strong>Hak Unduh:</strong> Selamanya (file statis).</div>
                        <div><strong>Login:</strong> Wajib Login Akun (OTP Email).</div>
                    </div>
                </div>

                <!-- Pro Card -->
                <div class="bg-zinc-950 p-4 rounded-xs border border-indigo-500/40 space-y-2">
                    <div class="font-mono font-bold text-indigo-400">PRO (RP 399.000)</div>
                    <div class="text-[11px] text-zinc-400 space-y-1">
                        <div><strong>Batas Entitas:</strong> 1 Proyek Ultimate PRD + 6 Diagram + WBS.</div>
                        <div><strong>Jendela AI:</strong> 6 bulan unlimited re-prompt copilot.</div>
                        <div><strong>Hak Unduh:</strong> Selamanya (PDF, MD, Scaffold Zip).</div>
                        <div><strong>Login:</strong> Wajib Login Akun (OTP Email).</div>
                    </div>
                </div>

                <!-- Ultimate Card -->
                <div class="bg-zinc-950 p-4 rounded-xs border border-purple-500/40 space-y-2">
                    <div class="font-mono font-bold text-purple-400">ULTIMATE (RP 1.490.000)</div>
                    <div class="text-[11px] text-zinc-400 space-y-1">
                        <div><strong>Batas Entitas:</strong> 1 Proyek Enterprise + AI Failover Shield.</div>
                        <div><strong>Jendela Update:</strong> 1 tahun prioritas update dokumen.</div>
                        <div><strong>Booking Advisory:</strong> 60 hari booking sesi Meet 60 menit.</div>
                        <div><strong>Login:</strong> Wajib Akun Terverifikasi.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales & Customer Service Playbook (Anti-Bingung) -->
        <div class="bg-zinc-900/90 border border-zinc-800 p-6 rounded-sm shadow-lg space-y-4">
            <h3 class="text-base font-bold text-white">
                Playbook CS & Sales: Script Tanggapan Keberatan Klien
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div class="bg-zinc-950 p-4 rounded-xs border border-zinc-800 space-y-2">
                    <div class="font-bold text-amber-300">
                        Q: "Kenapa proyek kedua saya disuruh bayar lagi? Katanya lifetime?"
                    </div>
                    <p class="text-zinc-400 italic bg-zinc-900/80 p-2.5 rounded-xs border border-zinc-800">
                        "Halo Kak, betul sekali! Lisensi yang Kakak beli memberikan hak unduh dan akses arsip SELAMANYA untuk proyek [Nama Proyek Pertama] tanpa biaya bulanan. Untuk membangun arsitektur sistem baru dengan spesifikasi, DDL, dan scope yang berbeda, sistem kami membutuhkan komputasi AI baru sehingga memerlukan 1 lisensi terpisah per entitas proyek."
                    </p>
                </div>

                <div class="bg-zinc-950 p-4 rounded-xs border border-zinc-800 space-y-2">
                    <div class="font-bold text-emerald-300">
                        Q: "Saya sudah punya blueprint Pro, tapi tim saya bingung cara kodingnya. Bisa tolong kodingin?"
                    </div>
                    <p class="text-zinc-400 italic bg-zinc-900/80 p-2.5 rounded-xs border border-zinc-800">
                        "Tentu bisa sekali Kak! Paket Blueprint adalah paket Self-Service untuk tim internal Kakak. Namun jika Kakak ingin sistem ini dibangun 100% turnkey dan siap pakai oleh Software Architect & Senior Engineer Neriah Pro, Kakak dapat meng-upgrade ke Kontrak Monolith MVP Studio (mulai Rp 50 Juta). Dokumen Blueprint Kakak yang sudah ada akan langsung kami gunakan sebagai acuan sprint produksi!"
                    </p>
                </div>
            </div>
        </div>

    </div>

    <script>
    function unitEconomicsCalculator() {
        return {
            salesVolume: 30,

            formatCurrency(val) {
                return 'Rp ' + Number(val).toLocaleString('id-ID');
            }
        };
    }
    </script>
</x-filament-panels::page>
