<x-filament-panels::page>
    <div class="space-y-6">

        {{-- 1. TOP KPI SUMMARY CARDS --}}
        @php
            $kpis = $this->kpis;
            $records = $this->getRecords();
            $rateCards = $this->rateCards;
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Total Omset --}}
            <div class="bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-5 rounded-xs shadow-xs transition hover:border-zinc-400 dark:hover:border-zinc-600">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Omset Layanan</span>
                <div class="text-2xl font-black text-emerald-950 dark:text-emerald-300 mt-1">
                    Rp {{ number_format($kpis['total_omset'], 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Dari {{ $kpis['total_paid_count'] }} transaksi lunas terverifikasi
                </div>
            </div>

            {{-- Card 2: Rata-rata Order (AOV) --}}
            <div class="bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-5 rounded-xs shadow-xs transition hover:border-zinc-400 dark:hover:border-zinc-600">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Rata-Rata Order (AOV)</span>
                <div class="text-2xl font-black text-indigo-950 dark:text-indigo-300 mt-1">
                    Rp {{ number_format($kpis['aov'], 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Nilai tiket per klien aktif
                </div>
            </div>

            {{-- Card 3: Total Order Dicatat --}}
            <div class="bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-5 rounded-xs shadow-xs transition hover:border-zinc-400 dark:hover:border-zinc-600">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Order Dicatat</span>
                <div class="text-2xl font-black text-zinc-950 dark:text-zinc-100 mt-1">
                    {{ $kpis['total_count'] }}
                </div>
                <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Konsultasi B2C & B2B Karir
                </div>
            </div>

            {{-- Card 4: Tingkat Sukses Pelunasan --}}
            <div class="bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-5 rounded-xs shadow-xs transition hover:border-zinc-400 dark:hover:border-zinc-600">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Tingkat Sukses Pelunasan</span>
                <div class="text-2xl font-black text-amber-950 dark:text-amber-300 mt-1">
                    {{ $kpis['success_rate'] }}%
                </div>
                <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Metode QRIS & Transfer Bank
                </div>
            </div>
        </div>

        {{-- 2. PRICING RATE CARD & SERVICE PACKAGES CATALOG --}}
        <div class="bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-6 rounded-xs shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-mono font-bold uppercase text-zinc-900 dark:text-zinc-200 flex items-center gap-2">
                    <span class="w-2 h-2 bg-indigo-600 dark:bg-indigo-400 inline-block"></span>
                    <span>Katalog Paket Layanan & Kalkulator Biaya (Pricing Rate Card)</span>
                </h3>
                <span class="text-[11px] font-mono text-zinc-500 dark:text-zinc-400">Standardized Official Rate</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                @foreach ($rateCards as $card)
                    <div class="p-4 border rounded-xs transition {{ ($card['badge'] ?? '') === 'FAVORIT' ? 'border-indigo-500 bg-indigo-50/20 dark:bg-indigo-950/20' : 'border-zinc-200 dark:border-zinc-800 bg-zinc-50/60 dark:bg-zinc-900/40' }} space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 font-mono font-bold text-[10px] rounded-none {{ ($card['badge'] ?? '') === 'FAVORIT' ? 'bg-indigo-600 text-white' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200' }}">
                                {{ $card['number'] }}
                            </span>
                            @if (!empty($card['badge']))
                                <span class="px-1.5 py-0.2 text-[9px] font-mono font-black border border-indigo-500 text-indigo-700 dark:text-indigo-300">
                                    {{ $card['badge'] }}
                                </span>
                            @endif
                        </div>

                        <div class="text-lg font-black text-zinc-950 dark:text-white">
                            Rp {{ number_format($card['price'], 0, ',', '.') }}
                        </div>

                        <ul class="text-zinc-700 dark:text-zinc-300 space-y-1.5 text-[11px]">
                            @foreach ($card['features'] as $feat)
                                <li class="flex items-start gap-1.5">
                                    <span class="{{ $feat['available'] ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-rose-500 font-bold' }}">
                                        {{ $feat['available'] ? '✓' : '✗' }}
                                    </span>
                                    <span>{{ $feat['text'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 3. TRANSACTIONS LEDGER TABLE (BUKU KAS) --}}
        <div class="bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-6 rounded-xs shadow-xs space-y-4">
            
            {{-- Toolbar: Title, Search, Filter & Actions --}}
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-4">
                <div>
                    <h3 class="text-base font-bold text-zinc-950 dark:text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-emerald-600 dark:bg-emerald-400 inline-block"></span>
                        <span>Buku Kas Transaksi Layanan Pembuatan CV</span>
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                        Audit trail transaksi realtime, pencatatan kas manual, dan ekspor pelaporan keuangan.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    {{-- Status Filter --}}
                    <select
                        wire:model.live="statusFilter"
                        class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-xs px-2.5 py-1.5 text-zinc-800 dark:text-zinc-200 rounded-none focus:ring-1 focus:ring-zinc-500"
                    >
                        <option value="all">Semua Status</option>
                        <option value="paid">Hanya Lunas (Paid)</option>
                        <option value="pending">Hanya Menunggu (Pending)</option>
                    </select>

                    {{-- Search with Instant Clear (X) --}}
                    <div class="relative flex items-center">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="misal: cari nama klien atau kode transaksi..."
                            class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-xs px-3 py-1.5 pr-7 text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 dark:placeholder-zinc-500 rounded-none w-56 sm:w-64 focus:ring-1 focus:ring-zinc-500"
                        />
                        @if (!empty($search))
                            <button
                                type="button"
                                wire:click="clearSearch"
                                class="absolute right-2 text-zinc-400 hover:text-zinc-900 dark:hover:text-white text-xs font-bold"
                                title="Hapus pencarian"
                            >
                                ✕
                            </button>
                        @endif
                    </div>

                    {{-- Action: Export CSV --}}
                    <button
                        type="button"
                        wire:click="exportCsv"
                        class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-none transition flex items-center gap-1.5 cursor-pointer shadow-xs"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Export Excel (.csv)</span>
                    </button>

                    {{-- Action: Catat Transaksi Baru --}}
                    <button
                        type="button"
                        wire:click="openCreateModal"
                        class="px-3.5 py-1.5 bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-950 text-xs font-bold rounded-none transition flex items-center gap-1.5 cursor-pointer shadow-xs"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Catat Transaksi Baru</span>
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-800">
                <table class="w-full text-left text-xs text-zinc-800 dark:text-zinc-200">
                    <thead class="sticky top-0 z-20 bg-zinc-100 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 font-mono text-[11px] uppercase tracking-wider text-zinc-600 dark:text-zinc-400">
                        <tr>
                            <th class="p-3">ID TRX</th>
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">Klien</th>
                            <th class="p-3">Paket Layanan</th>
                            <th class="p-3">Nominal (IDR)</th>
                            <th class="p-3">Metode Bayar</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($records as $row)
                            <tr class="odd:bg-white dark:odd:bg-zinc-950 even:bg-zinc-50/60 dark:even:bg-zinc-900/40 hover:bg-zinc-100/90 dark:hover:bg-zinc-800/80 transition">
                                <td class="p-3 font-mono font-bold text-indigo-700 dark:text-indigo-400">
                                    {{ $row->trx_code }}
                                </td>
                                <td class="p-3 font-mono text-zinc-600 dark:text-zinc-400">
                                    {{ $row->transaction_date ? $row->transaction_date->format('Y-m-d') : '-' }}
                                </td>
                                <td class="p-3">
                                    <div class="font-bold text-zinc-950 dark:text-white">{{ $row->client_name }}</div>
                                    @if ($row->client_email)
                                        <div class="text-[10px] font-mono text-zinc-500">{{ $row->client_email }}</div>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <span class="font-medium">{{ $row->package_name }}</span>
                                </td>
                                <td class="p-3 font-mono font-bold text-zinc-950 dark:text-white">
                                    Rp {{ number_format((float) $row->amount_idr, 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-zinc-600 dark:text-zinc-400 font-mono text-[11px]">
                                    {{ $row->payment_method }}
                                </td>
                                <td class="p-3">
                                    @if ($row->status === 'paid')
                                        <span class="inline-block px-2 py-0.5 text-[10px] font-mono font-black bg-emerald-100 dark:bg-emerald-950/80 text-emerald-950 dark:text-emerald-200 border border-emerald-400 dark:border-emerald-700 rounded-none">
                                            LUNAS
                                        </span>
                                    @elseif ($row->status === 'pending')
                                        <span class="inline-block px-2 py-0.5 text-[10px] font-mono font-black bg-amber-100 dark:bg-amber-950/80 text-amber-950 dark:text-amber-200 border border-amber-400 dark:border-amber-700 rounded-none">
                                            MENUNGGU
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 text-[10px] font-mono font-black bg-zinc-200 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-300 border border-zinc-400 rounded-none">
                                            {{ strtoupper($row->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Tidak ada catatan transaksi yang sesuai dengan filter atau kata kunci.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Cursor Pagination O(1) --}}
            @if (method_exists($records, 'hasPages') && $records->hasPages())
                <div class="pt-3">
                    {{ $records->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- MODAL: CATAT TRANSAKSI BARU --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 bg-black/80 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 w-full max-w-lg shadow-2xl p-6 rounded-xs space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <h3 class="text-sm font-bold text-zinc-950 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 bg-indigo-600 inline-block"></span>
                        <span>Catat Transaksi Layanan CV Baru</span>
                    </h3>
                    <button
                        type="button"
                        wire:click="closeCreateModal"
                        class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white text-base font-bold cursor-pointer"
                    >
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="saveTransaction" class="space-y-3.5 text-xs">
                    {{-- Nama Klien --}}
                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                            Nama Klien *
                        </label>
                        <input
                            type="text"
                            wire:model="newClientName"
                            placeholder="misal: Ahmad Fauzi, S.T. / PT Maju Gemilang"
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 rounded-none focus:ring-1 focus:ring-zinc-500"
                        />
                        @error('newClientName') <span class="text-rose-600 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email Klien --}}
                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                            Email Klien (Opsional)
                        </label>
                        <input
                            type="email"
                            wire:model="newClientEmail"
                            placeholder="misal: client@perusahaan.com"
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 rounded-none focus:ring-1 focus:ring-zinc-500"
                        />
                        @error('newClientEmail') <span class="text-rose-600 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    {{-- Paket Layanan --}}
                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                            Paket Layanan *
                        </label>
                        <select
                            wire:model.live="newPackageName"
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 rounded-none focus:ring-1 focus:ring-zinc-500"
                        >
                            <option value="Starter / Lite (Template)">1. Starter / Lite (Template) - Rp 35.000</option>
                            <option value="Standard (ATS Optimized)">2. Standard (ATS Optimized) - Rp 75.000</option>
                            <option value="Full Stack (Siap Kerja)">3. Full Stack (Siap Kerja) - Rp 150.000</option>
                            <option value="Executive VIP (Career AI + Coaching)">4. Executive VIP (Career AI + Coaching) - Rp 300.000</option>
                        </select>
                        @error('newPackageName') <span class="text-rose-600 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        {{-- Nominal IDR --}}
                        <div>
                            <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                Nominal (IDR) *
                            </label>
                            <input
                                type="number"
                                wire:model="newAmountIdr"
                                placeholder="misal: 75000"
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 rounded-none focus:ring-1 focus:ring-zinc-500"
                            />
                            @error('newAmountIdr') <span class="text-rose-600 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                Status Transaksi *
                            </label>
                            <select
                                wire:model="newStatus"
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 rounded-none focus:ring-1 focus:ring-zinc-500"
                            >
                                <option value="paid">Lunas (Paid)</option>
                                <option value="pending">Menunggu (Pending)</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                            @error('newStatus') <span class="text-rose-600 text-[11px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Metode Bayar --}}
                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                            Metode Pembayaran *
                        </label>
                        <select
                            wire:model="newPaymentMethod"
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 rounded-none focus:ring-1 focus:ring-zinc-500"
                        >
                            <option value="Midtrans QRIS">Midtrans QRIS</option>
                            <option value="BCA Transfer">BCA Transfer</option>
                            <option value="Mandiri Transfer">Mandiri Transfer</option>
                            <option value="BRI Transfer">BRI Transfer</option>
                            <option value="GoPay / E-Wallet">GoPay / E-Wallet</option>
                            <option value="Manual Cash / Tunai">Manual Cash / Tunai</option>
                        </select>
                        @error('newPaymentMethod') <span class="text-rose-600 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    {{-- Catatan Opsional --}}
                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                            Catatan Konsultasi / Scope
                        </label>
                        <textarea
                            wire:model="newNotes"
                            rows="2"
                            placeholder="misal: Catatan khusus optimasi kata kerja aktif atau cover letter..."
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 rounded-none focus:ring-1 focus:ring-zinc-500"
                        ></textarea>
                    </div>

                    {{-- Modal Buttons --}}
                    <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-2">
                        <button
                            type="button"
                            wire:click="closeCreateModal"
                            class="px-3.5 py-1.5 border border-zinc-300 dark:border-zinc-700 text-xs font-bold text-zinc-700 dark:text-zinc-300 rounded-none hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-1.5 bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-950 text-xs font-bold rounded-none transition cursor-pointer shadow-xs"
                        >
                            Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-filament-panels::page>

