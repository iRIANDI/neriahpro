<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cart Pembayaran Proyek // Neriah Pro</title>
    
    <!-- Local Fonts (Zero External Latency) -->
    <link rel="stylesheet" href="{{ asset('fonts/instrument-sans/instrument-sans.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Midtrans Snap JS (In-Page Popup Modal) -->
    <script src="{{ config('midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/snap.js') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script>
        if (localStorage.getItem('neriah_theme') === 'dark' || (!localStorage.getItem('neriah_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('neriah_theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('neriah_theme', 'dark');
            }
        }
    </script>
</head>
<body class="bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col transition-colors duration-200">

    <!-- Header Navigation Bar -->
    <header class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 py-3 px-6 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <a href="/" class="text-sm font-black uppercase tracking-tight flex items-center gap-2 text-zinc-900 dark:text-white">
                <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black flex items-center justify-center text-xs font-mono font-bold rounded-none">N</span>
                <span>NERIAH<span class="text-emerald-500">PRO</span> // CHECKOUT</span>
            </a>

            <div class="flex items-center gap-3">
                <button onclick="toggleTheme()" class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono rounded-none border border-zinc-300 dark:border-zinc-700 transition">
                    THEME
                </button>
                <a href="/blueprint" class="px-3 py-1 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black text-xs font-mono uppercase font-bold rounded-none transition">
                    + NEW PROPOSAL
                </a>
            </div>
        </div>
    </header>

    <main class="flex-1 py-10 px-4 sm:px-6 max-w-5xl mx-auto w-full">
        <!-- Title & Status -->
        <div class="mb-8 border-b border-zinc-200 dark:border-zinc-800 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-wider bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black rounded-none">
                    ORDER CART & ESCROW
                </span>
                <h1 class="text-2xl sm:text-3xl font-black uppercase text-zinc-900 dark:text-zinc-100 mt-2">
                    Cart Pembayaran & Kunci Kontrak
                </h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-xs font-mono mt-1">
                    Daftar spesifikasi proyek yang siap dikunci scope dan dibayarkan termin DP (50%) via Midtrans Escrow.
                </p>
            </div>
            @if(count($items) > 0)
                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-xs font-mono text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-rose-300 dark:border-rose-900 transition">
                        Kosongkan Cart
                    </button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-mono flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-950/40 border border-blue-300 dark:border-blue-800 text-blue-800 dark:text-blue-300 text-xs font-mono flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if(!empty($pendingOrder))
            <!-- Active Pending Order Alert & Quick Resume Card -->
            <div class="mb-6 p-5 bg-white dark:bg-zinc-900 border-2 border-emerald-500 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-mono font-bold bg-amber-500 text-black uppercase tracking-wider">
                            Status: Menunggu Pembayaran
                        </span>
                        <span class="text-xs font-mono text-zinc-500 dark:text-zinc-400">
                            Order ID: <strong class="text-zinc-900 dark:text-zinc-100 font-mono">{{ $pendingOrder['order_id'] }}</strong>
                        </span>
                    </div>
                    <p class="text-xs text-zinc-700 dark:text-zinc-300 font-sans leading-relaxed">
                        Tagihan sebesar <strong class="text-emerald-600 dark:text-emerald-400 font-mono text-sm">Rp {{ number_format($pendingOrder['gross_amount'] ?? ($pendingOrder['total_idr'] ?? 0), 0, ',', '.') }}</strong> telah dicatat di backend. Anda dapat langsung melanjutkan pembayaran tanpa membuat tagihan baru, atau membatalkan sesi untuk mengganti metode pembayaran.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <button type="button" 
                            id="btn-resume-snap"
                            onclick="resumePendingSnap('{{ $pendingOrder['snap_token'] ?? '' }}')"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-mono font-bold text-xs uppercase tracking-wider flex items-center gap-2 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        Lanjutkan Bayar
                    </button>
                    <form action="{{ route('cart.reset-pending') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-3 py-2 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-xs uppercase tracking-wider transition cursor-pointer">
                            Ganti Metode / Batal
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if(count($items) > 0)
            <!-- Anti-Ghost Hold Realtime Countdown Banner -->
            <div class="mb-6 p-4 bg-amber-500/10 dark:bg-amber-500/5 border-2 border-amber-500 text-amber-900 dark:text-amber-300 font-mono text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3" id="ghost-hold-banner">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-amber-500 animate-ping"></span>
                    <div>
                        <strong class="uppercase font-bold tracking-wide block sm:inline">ANTI-GHOST HOLD PROTOCOL:</strong>
                        <span class="text-zinc-700 dark:text-zinc-300">Reservasi slot engineering dikunci maksimal 24 jam untuk mencegah penahanan kuota tanpa kepastian.</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 font-mono font-bold bg-amber-500 text-black px-3 py-1.5 whitespace-nowrap self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>SISA RESERVASI:</span>
                    <span id="cart-master-countdown" class="text-sm" data-rem="{{ $minRemainingSeconds ?? 86400 }}">{{ gmdate('H:i:s', max(0, (int)($minRemainingSeconds ?? 86400))) }}</span>
                </div>
            </div>
        @endif

        @if(empty($items))
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-zinc-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <h3 class="text-base font-bold uppercase font-mono text-zinc-900 dark:text-zinc-100 mb-1">Cart Masih Kosong</h3>
                <p class="text-zinc-500 text-xs font-sans mb-6">Belum ada spesifikasi Blueprint proyek yang ditambahkan ke cart belanja atau masa reservasi 24 jam telah kedaluwarsa.</p>
                <a href="/blueprint" class="inline-block px-5 py-2.5 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs uppercase tracking-wider transition">
                    Mulai Buat PRD Baru
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Cart Items List (Col Span 2) -->
                <div class="lg:col-span-2 space-y-4">
                    @foreach($items as $item)
                        @php
                            $tierKey = $item['tier'] ?? 'standard';
                            $tierLabel = $item['tier_name'] ?? 'Standard Velocity (30 Hari)';
                            $isHighSpeed = str_contains(strtolower($tierKey), 'hyper') || str_contains(strtolower($tierKey), 'emergency');
                            $isMiddleFast = str_contains(strtolower($tierKey), 'fast') || str_contains(strtolower($tierKey), 'swarm') || str_contains(strtolower($tierKey), 'ultra');

                            $tierBadge = 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300';
                            if ($isHighSpeed) {
                                $tierBadge = 'bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/40';
                            } elseif ($isMiddleFast) {
                                $tierBadge = 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/40';
                            }
                        @endphp
                        @php
                            $itemUnixExpiry = \Illuminate\Support\Carbon::parse($item['expires_at'])->timestamp;
                        @endphp
                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 item-row" data-expires="{{ $itemUnixExpiry }}" data-rem="{{ (int)$item['remaining_seconds'] }}" x-data="{ showBreakdown: false }">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-3 mb-3">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                        <span class="text-[10px] font-mono uppercase tracking-wider px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                            ID: {{ strtoupper(substr($item['blueprint']->id, 0, 8)) }}
                                        </span>
                                        <span class="text-[10px] font-mono uppercase tracking-wider px-2 py-0.5 font-bold {{ $tierBadge }}">
                                            {!! e($tierLabel) !!}
                                        </span>
                                        <span class="text-[10px] font-mono px-2 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30 flex items-center gap-1 font-bold">
                                            <span>⏱️</span>
                                            <span class="item-countdown">{{ gmdate('H:i:s', max(0, (int)$item['remaining_seconds'])) }}</span>
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-black uppercase text-zinc-900 dark:text-zinc-100 mt-1">
                                        {{ $item['title'] }}
                                    </h3>
                                    <p class="text-zinc-500 text-xs font-mono">PIC: {{ $item['client_name'] }} &bull; {{ $item['email'] }}</p>
                                </div>
                                <div class="text-left sm:text-right font-mono">
                                    <span class="text-[10px] text-zinc-400 block uppercase">TERMIN DP (50%)</span>
                                    <span class="text-lg font-black text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($item['dp_amount'], 0, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] text-zinc-400 block">Total: Rp {{ number_format($item['contract_amount'], 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Itemized Scope Breakdown Toggle Button -->
                            @if(!empty($item['itemized_items']))
                                <div class="mb-3">
                                    <button 
                                        type="button" 
                                        @click="showBreakdown = !showBreakdown"
                                        class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 flex items-center gap-1.5 py-1 px-2.5 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-300 dark:border-emerald-800/60 transition"
                                    >
                                        <span x-text="showBreakdown ? '▲ Sembunyikan Rincian Spesifikasi' : '▼ Lihat Rincian Biaya Input Anda (' + {{ count($item['itemized_items']) }} + ' Komponen Teranalisis)'"></span>
                                    </button>

                                    <!-- Collapsible Itemized Scope Table -->
                                    <div x-show="showBreakdown" x-cloak class="mt-2.5 p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 font-mono text-[11px] space-y-2">
                                        <div class="flex items-center justify-between text-[10px] font-bold uppercase text-zinc-400 border-b border-zinc-200 dark:border-zinc-800 pb-1 mb-2">
                                            <span>Komponen / Fitur Yang Dianalisis</span>
                                            <span>Bobot Nilai</span>
                                        </div>
                                        <div class="space-y-1.5 max-h-64 overflow-y-auto pr-1">
                                            @foreach($item['itemized_items'] as $it)
                                                <div class="flex items-start justify-between gap-2 py-1 border-b border-zinc-100 dark:border-zinc-900 last:border-0">
                                                    <div>
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="text-[9px] px-1 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold">{{ $it['code'] ?? 'FEAT' }}</span>
                                                            <strong class="text-zinc-800 dark:text-zinc-200">{{ $it['title'] }}</strong>
                                                        </div>
                                                        <p class="text-[10px] text-zinc-500 mt-0.5 line-clamp-1">{{ $it['desc'] }}</p>
                                                    </div>
                                                    <span class="font-bold whitespace-nowrap {{ ($it['amount'] ?? 0) < 0 ? 'text-amber-500' : 'text-zinc-900 dark:text-zinc-100' }}">
                                                        {{ ($it['amount'] ?? 0) < 0 ? '-Rp ' : 'Rp ' }}{{ number_format(abs($it['amount'] ?? 0), 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="border-t border-zinc-200 dark:border-zinc-800 pt-2 flex items-center justify-between text-xs font-bold">
                                            <span class="text-zinc-500">Tier Terpilih: {{ $item['tier_name'] }}</span>
                                            <span class="text-emerald-600 dark:text-emerald-400">Total: Rp {{ number_format($item['contract_amount'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 text-xs font-mono">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('blueprint.show', $item['slug']) }}" class="text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold">
                                        <span>&larr; Lihat Dokumen PRD</span>
                                    </a>
                                    <span class="text-[10px] text-zinc-400 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Exp: <span class="item-countdown font-bold text-zinc-700 dark:text-zinc-300">--:--:--</span></span>
                                    </span>
                                                               <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('blueprint.generate-contract', $item['slug']) }}" class="m-0">
                                        @csrf
                                        <input type="hidden" name="tier" value="{{ $tierKey }}">
                                        @if($voucher)
                                            <input type="hidden" name="voucher" value="{{ $voucher['code'] }}">
                                        @endif
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-black font-bold uppercase text-[11px] transition">
                                            Tanda Tangan Kontrak
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('cart.remove', $item['slug']) }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 text-zinc-400 hover:text-rose-500 text-[11px] transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Summary & Checkout (Col Span 1) -->
                <div class="bg-white dark:bg-zinc-900 border-2 border-emerald-500 p-6 h-fit">
                    <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block mb-1">
                        RINGKASAN PEMBAYARAN
                    </span>
                    <h3 class="text-xl font-black uppercase text-zinc-900 dark:text-zinc-100 border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4">
                        Midtrans Escrow
                    </h3>

                    <div class="space-y-3 text-xs font-mono mb-4">
                        <div class="flex justify-between items-baseline text-zinc-500 dark:text-zinc-400">
                            <span>Total Nilai Kontrak</span>
                            <div>
                                @if($voucher)
                                    <span class="line-through text-zinc-400 text-[11px] mr-1">Rp {{ number_format($totalContract, 0, ',', '.') }}</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($finalTotalContract, 0, ',', '.') }}</span>
                                @else
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100">Rp {{ number_format($totalContract, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>

                        @if($voucher)
                            <div class="flex justify-between items-center text-emerald-600 dark:text-emerald-400 font-bold border-t border-dashed border-zinc-200 dark:border-zinc-800 pt-2 text-[11px]">
                                <span>Subsidi Voucher ({{ $voucher['code'] }})</span>
                                <span>-Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between text-zinc-500 dark:text-zinc-400">
                            <span>Ketentuan Termin DP</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">50% di Muka</span>
                        </div>
                        <div class="border-t border-zinc-200 dark:border-zinc-800 pt-3 flex justify-between items-baseline">
                            <span class="font-bold uppercase text-zinc-900 dark:text-zinc-100">Total Tagihan DP</span>
                            <div class="text-right">
                                @if($voucher)
                                    <span class="line-through text-zinc-400 text-xs block">Rp {{ number_format($totalDp, 0, ',', '.') }}</span>
                                    <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">Rp {{ number_format($finalTotalDp, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalDp, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Voucher Promo / Pelayanan Input Block in Cart -->
                    <div class="border border-dashed border-zinc-300 dark:border-zinc-700 p-3 mb-6 bg-zinc-50 dark:bg-zinc-950/60 rounded-none font-mono">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-[11px] uppercase tracking-wider text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                <span>Punya Kode Voucher / Subsidi?</span>
                            </span>
                            @if($voucher)
                                <span class="text-[10px] px-1.5 py-0.5 bg-emerald-500/20 text-emerald-400 font-bold border border-emerald-500/40">AKTIF</span>
                            @endif
                        </div>

                        @if(!$voucher)
                            <form method="POST" action="{{ route('cart.voucher.apply') }}" class="flex gap-2 m-0">
                                @csrf
                                <input 
                                    type="text" 
                                    name="voucher_code" 
                                    placeholder="Contoh: PELAYANAN-KASIH"
                                    class="flex-1 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2.5 py-1.5 text-xs font-mono uppercase focus:outline-none focus:border-emerald-500 rounded-none"
                                    required
                                />
                                <button 
                                    type="submit" 
                                    class="bg-zinc-800 hover:bg-zinc-700 text-white font-mono font-bold text-xs uppercase px-3 py-1.5 transition border border-zinc-600 rounded-none"
                                >
                                    Terapkan
                                </button>
                            </form>
                        @else
                            <div class="p-2 bg-emerald-950/40 border border-emerald-600/40 text-emerald-300 text-[11px] space-y-1">
                                <div class="flex items-center justify-between">
                                    <strong class="font-bold text-emerald-400 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        {{ $voucher['code'] }}
                                    </strong>
                                    <form method="POST" action="{{ route('cart.voucher.remove') }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="text-rose-400 hover:text-rose-300 text-[10px] uppercase font-bold underline cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                                <div class="text-[10px] text-zinc-400 leading-snug">
                                    {{ $voucher['description'] ?: 'Potongan subsidi berhasil diterapkan ke seluruh tagihan cart.' }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-zinc-50 dark:bg-zinc-950 p-3 border border-zinc-200 dark:border-zinc-800 text-[11px] font-mono text-zinc-500 dark:text-zinc-400 mb-4 leading-relaxed">
                        <strong class="text-zinc-900 dark:text-zinc-100 block mb-0.5">METODE PEMBAYARAN MIDTRANS:</strong>
                        Mendukung QRIS, BCA Virtual Account, Mandiri Bill, BNI, BRI, Permata, dan Kartu Kredit dengan enkripsi 3D Secure.
                    </div>

                    <!-- Mandatory Midtrans Compliance Agreement Checkbox -->
                    <div x-data="{ showLegalModal: false, legalTab: 'id' }">
                        <label class="flex items-start gap-2.5 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 cursor-pointer mb-4 select-none rounded-none hover:border-emerald-500 transition">
                            <input 
                                type="checkbox" 
                                id="cart-terms-agree" 
                                class="mt-0.5 rounded-none border-zinc-400 text-emerald-600 focus:ring-emerald-500 cursor-pointer"
                                required
                            />
                            <span class="text-[11px] leading-snug text-zinc-700 dark:text-zinc-300 font-sans">
                                <strong class="font-mono text-emerald-600 dark:text-emerald-400 block uppercase font-bold text-[10px] mb-0.5">
                                    Persetujuan Syarat &amp; Ketentuan Layanan, Garansi SLA &amp; Refund Midtrans
                                </strong>
                                Saya telah membaca dan menyetujui 
                                <button type="button" @click.stop.prevent="showLegalModal = true" class="text-emerald-600 dark:text-emerald-400 underline font-bold hover:text-emerald-500 cursor-pointer">Syarat &amp; Ketentuan Layanan</button>, 
                                <button type="button" @click.stop.prevent="showLegalModal = true" class="text-emerald-600 dark:text-emerald-400 underline font-bold hover:text-emerald-500 cursor-pointer">Kebijakan Garansi 30 Hari &amp; Refund</button>, 
                                <button type="button" @click.stop.prevent="showLegalModal = true" class="text-emerald-600 dark:text-emerald-400 underline font-bold hover:text-emerald-500 cursor-pointer">Penanganan Pembayaran Ganda</button>, serta 
                                Ketentuan Pembatalan Neriah Pro.
                            </span>
                        </label>

                        <!-- Legal Terms Modal -->
                        <div 
                            x-show="showLegalModal" 
                            x-cloak 
                            @keydown.escape.window="showLegalModal = false"
                            class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6 no-print font-sans"
                        >
                            <div 
                                @click.outside="showLegalModal = false" 
                                class="bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 max-w-3xl w-full my-auto max-h-[88vh] flex flex-col p-6 rounded-none text-zinc-900 dark:text-zinc-100 shadow-2xl relative"
                            >
                                <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4 shrink-0">
                                    <div>
                                        <h3 class="text-base sm:text-lg font-black uppercase text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
                                            <span>KEPATUHAN LAYANAN &amp; MIDTRANS ESCROW</span>
                                        </h3>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">
                                            Transparansi Konsumen, Garansi SLA 30 Hari, &amp; Penanganan Idempotency Transaksi
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1.5 font-mono text-xs">
                                        <button 
                                            type="button" 
                                            @click="legalTab = 'id'" 
                                            :class="legalTab === 'id' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'"
                                            class="px-2.5 py-1 rounded-none border border-zinc-300 dark:border-zinc-700 cursor-pointer"
                                        >
                                            ID
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="legalTab = 'en'" 
                                            :class="legalTab === 'en' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'"
                                            class="px-2.5 py-1 rounded-none border border-zinc-300 dark:border-zinc-700 cursor-pointer"
                                        >
                                            EN
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="showLegalModal = false" 
                                            class="ml-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-white text-lg font-bold px-2 cursor-pointer"
                                        >
                                            &times;
                                        </button>
                                    </div>
                                </div>

                                <div class="overflow-y-auto space-y-3 pr-1 text-xs leading-relaxed text-zinc-700 dark:text-zinc-300">
                                    <div x-show="legalTab === 'id'" class="space-y-3">
                                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                            <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                                                <span class="text-emerald-500 font-mono">01.</span> Syarat &amp; Ketentuan Layanan (Terms of Service)
                                            </h4>
                                            <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_terms_content_id', "1. Lisensi & Hak Cipta: Setiap blueprint dan kode sumber yang telah dilunasi menjadi hak milik penuh klien.\n2. Batasan Revisi: Revisi spesifikasi gratis dibatasi sesuai tier yang dipilih (Spark 2x/bln, Lite 30 hari, Pro 6 bulan, Ultimate 1 tahun).\n3. Penguncian Scope: Setelah uang muka (DP) 50% atau pelunasan terkonfirmasi, ruang lingkup proyek dikunci (scope frozen) untuk menjaga ketepatan waktu sprint engineering.") }}
                                            </div>
                                        </div>

                                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                            <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                                                <span class="text-emerald-500 font-mono">02.</span> Jaminan Garansi &amp; Kebijakan Refund (30 Hari SLA)
                                            </h4>
                                            <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_refund_policy_id', "1. Garansi SLA: Paket Turnkey Studio MVP dilindungi 30 Hari Garansi Bug pasca peluncuran resmi.\n2. Jaminan Refund 100%: Pengembalian dana penuh 100% berlaku jika terjadi kegagalan teknis fatal dari pihak Neriah Pro sebelum dimulainya sprint pengembangan.\n3. Non-Refundable Post-Delivery: Karena produk digital dan blueprint arsitektur bersifat kekayaan intelektual langsung pakai, pembayaran yang telah diselesaikan setelah serah terima berkas tidak dapat dikembalikan sepihak.") }}
                                            </div>
                                        </div>

                                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                            <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                                                <span class="text-emerald-500 font-mono">03.</span> Penanganan Pembayaran Ganda (Double-Payment Anti-Collision)
                                            </h4>
                                            <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_double_payment_policy_id', "1. Deteksi Idempotency: Sistem Neriah Pro mendeteksi setiap transaksi menggunakan ID pesanan unik untuk mencegah duplikasi.\n2. Reversal Otomatis: Jika pelanggan tidak sengaja melakukan transfer ganda melalui gateway bank, sistem otomatis mencatat di Dead Letter Queue (DLQ).\n3. Pengembalian Dana: Kelebihan pembayaran akan diverifikasi dan dikembalikan ke rekening asal dalam 3 - 5 hari kerja tanpa potongan biaya sistem.") }}
                                            </div>
                                        </div>

                                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                            <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                                                <span class="text-emerald-500 font-mono">04.</span> Kebijakan Pembatalan Proyek (Cancellation Terms)
                                            </h4>
                                            <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_cancellation_policy_id', "1. Sebelum Kickoff / DP: Pembatalan pesanan dapat dilakukan kapan saja tanpa penalti biaya.\n2. Pasca Pembayaran DP 50%: Jika klien membatalkan proyek secara sepihak saat sprint pengembangan telah berlangsung, DP yang telah dibayarkan dialokasikan untuk kompensasi jam kerja arsitek (non-refundable), namun seluruh berkas blueprint dan kode yang telah dikerjakan tetap diserahkan kepada klien.") }}
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="legalTab === 'en'" class="space-y-3">
                                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                            <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                                                <span class="text-emerald-500 font-mono">01.</span> Terms of Service
                                            </h4>
                                            <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_terms_content_en', "1. License & Intellectual Property: Blueprints and source code settled in full are 100% owned by the client.\n2. Revision Window: Free AI revisions are limited by tier (Spark 2x/mo, Lite 30 days, Pro 6 months, Ultimate 1 year).\n3. Scope Locking: Once 50% DP or full settlement is confirmed, project scope is frozen to ensure engineering milestone delivery.") }}
                                            </div>
                                        </div>

                                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                            <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                                                <span class="text-emerald-500 font-mono">02.</span> Warranty &amp; Refund Policy (30-Day Bug SLA)
                                            </h4>
                                            <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_refund_policy_en', "1. SLA Warranty: Studio MVP Turnkey packages include a 30-Day Bug Warranty after official deployment.\n2. 100% Refund Guarantee: Full 100% refund applies if critical technical failure occurs on Neriah Pro's side prior to sprint commencement.\n3. Non-Refundable Post-Delivery: Due to the intellectual nature of digital blueprints, fees paid after document delivery are non-refundable for unilateral client cancellations.") }}
                                            </div>
                                        </div>

                                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                            <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                                                <span class="text-emerald-500 font-mono">03.</span> Double-Payment Anti-Collision Policy
                                            </h4>
                                            <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_double_payment_policy_en', "1. Idempotency Detection: Neriah Pro utilizes unique order IDs to prevent duplicate transaction charges.\n2. Automated Reversal: If duplicate transfers occur due to bank network retries, the event is trapped in the Dead Letter Queue (DLQ).\n3. Refund Timeline: Excess payments are verified and reimbursed to the source account within 3 - 5 business days with zero deduction.") }}
                                            </div>
                                        </div>

                                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                            <h4 class="font-bold text-zinc-900 dark:text-white uppercase mb-1 flex items-center gap-1.5 text-[11px]">
                                                <span class="text-emerald-500 font-mono">04.</span> Project Cancellation Policy
                                            </h4>
                                            <div class="whitespace-pre-line text-zinc-600 dark:text-zinc-400">
{{ \App\Models\CmsGlobalSetting::getVal('midtrans_cancellation_policy_en', "1. Prior to Kickoff / DP: Orders can be cancelled anytime with zero penalty.\n2. Post-DP 50% Kickoff: If the client cancels unilaterally while development sprints are active, the DP is allocated toward incurred engineering hours (non-refundable), while all produced blueprints and source code remain delivered to the client.") }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-zinc-200 dark:border-zinc-800 pt-3 mt-4 flex items-center justify-between shrink-0">
                                    <span class="text-[10px] text-zinc-500 font-mono">NERIAH PRO &bull; MIDTRANS ESCROW</span>
                                    <button 
                                        type="button" 
                                        @click="showLegalModal = false; const c = document.getElementById('cart-terms-agree'); if(c) c.checked = true;" 
                                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-bold font-mono text-xs uppercase rounded-none transition cursor-pointer"
                                    >
                                        Saya Mengerti &amp; Setuju
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @if($finalTotalDp <= 0)
                            <form method="POST" action="{{ route('cart.claim-free') }}" class="m-0">
                                @csrf
                                <button 
                                    type="submit" 
                                    class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-widest py-3.5 px-4 text-center block transition cursor-pointer shadow-md rounded-none"
                                >
                                    KLAIM VOUCHER PELAYANAN (RP 0 FREE BYPASS) &amp; KUNCI KONTRAK &rarr;
                                </button>
                            </form>
                            <p class="text-[10px] text-zinc-400 text-center font-mono">
                                Provisi sandbox staging otomatis &amp; penguncian scope tanpa biaya.
                            </p>
                        @else
                            <button 
                                type="button" 
                                id="btn-pay-snap"
                                onclick="payWithSnap()"
                                class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-widest py-3.5 px-4 text-center block transition cursor-pointer disabled:opacity-50 shadow-md rounded-none"
                            >
                                {{ $voucher ? 'Bayar DP Sekarang (Rp ' . number_format($finalTotalDp, 0, ',', '.') . ') &rarr;' : 'Bayar DP Sekarang (Midtrans Snap) &rarr;' }}
                            </button>
                            <p class="text-[10px] text-zinc-400 text-center font-mono">
                                Escrow diamankan &amp; diverifikasi otomatis oleh Midtrans webhook.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </main>

    <!-- Global Footer -->
    <footer class="bg-white dark:bg-zinc-900 text-zinc-500 dark:text-zinc-400 py-6 px-4 text-center text-xs border-t border-zinc-200 dark:border-zinc-800 font-mono mt-12">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} NERIAH PRO HUB &bull; ULTIMATE PRD & ARCHITECTURE BLUEPRINT</p>
            <p class="text-zinc-400 dark:text-zinc-500">MIDTRANS STRICT ESCROW PROTOCOL</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function formatTime(seconds) {
                if (seconds <= 0) return '00:00:00';
                const h = Math.floor(seconds / 3600);
                const m = Math.floor((seconds % 3600) / 60);
                const s = Math.floor(seconds % 60);
                return [
                    h.toString().padStart(2, '0'),
                    m.toString().padStart(2, '0'),
                    s.toString().padStart(2, '0')
                ].join(':');
            }

            function updateCountdowns() {
                const now = Math.floor(Date.now() / 1000);
                let minRemaining = null;
                const rows = document.querySelectorAll('.item-row');

                rows.forEach(function(row) {
                    const rawExp = row.getAttribute('data-expires');
                    let exp = null;
                    if (rawExp) {
                        if (/^\d+$/.test(rawExp)) {
                            exp = parseInt(rawExp, 10);
                        } else {
                            exp = Math.floor(new Date(rawExp).getTime() / 1000);
                        }
                    }

                    let rem = 0;
                    if (exp && !isNaN(exp)) {
                        rem = exp - now;
                    } else {
                        const fallbackRem = parseInt(row.getAttribute('data-rem'), 10);
                        if (!isNaN(fallbackRem)) {
                            rem = fallbackRem;
                        }
                    }

                    const labelEl = row.querySelector('.item-countdown');
                    if (labelEl) {
                        labelEl.textContent = formatTime(rem);
                        if (rem <= 0) {
                            labelEl.classList.add('text-rose-500');
                        } else {
                            labelEl.classList.remove('text-rose-500');
                        }
                    }

                    if (rem > 0 && (minRemaining === null || rem < minRemaining)) {
                        minRemaining = rem;
                    }
                });

                const masterEl = document.getElementById('cart-master-countdown');
                if (masterEl) {
                    if (minRemaining !== null && minRemaining > 0) {
                        masterEl.textContent = formatTime(minRemaining);
                    } else if (rows.length > 0) {
                        const rawMasterRem = parseInt(masterEl.getAttribute('data-rem'), 10);
                        if (!isNaN(rawMasterRem) && rawMasterRem > 0 && minRemaining === null) {
                            masterEl.textContent = formatTime(rawMasterRem);
                        } else {
                            masterEl.textContent = '00:00:00 (EXPIRED)';
                            masterEl.classList.add('text-rose-400');
                        }
                    }
                }
            }

            updateCountdowns();
            setInterval(updateCountdowns, 1000);
        });

        async function payWithSnap() {
            const btn = document.getElementById('btn-pay-snap');
            if (!btn) return;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="inline-block animate-pulse">MEMBUAT SESI SNAP...</span>';

            try {
                const response = await fetch('{{ route('cart.snap-token') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (!data.success || !data.token) {
                    throw new Error(data.message || 'Gagal memperoleh Snap Token dari Midtrans.');
                }

                if (window.snap && window.snap.pay) {
                    window.snap.pay(data.token, {
                        onSuccess: function(result) {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'success',
                                    title: 'PEMBAYARAN DP DIKONFIRMASI',
                                    message: 'Transaksi berhasil diverifikasi oleh Midtrans Escrow! Memperbarui status...',
                                    duration: 3500
                                });
                            }
                            setTimeout(function() { window.location.reload(); }, 1800);
                        },
                        onPending: function(result) {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'warning',
                                    title: 'MENUNGGU PEMBAYARAN',
                                    message: 'Tagihan berhasil dibuat. Silakan selesaikan pembayaran sesuai instruksi Midtrans.',
                                    duration: 5000
                                });
                            }
                            setTimeout(function() { window.location.reload(); }, 2200);
                        },
                        onError: function(result) {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'error',
                                    title: 'PEMBAYARAN DIBATALKAN',
                                    message: 'Transaksi tidak dapat diselesaikan atau dibatalkan oleh pengguna.'
                                });
                            }
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        },
                        onClose: function() {
                            if (window.showToast) {
                                window.showToast({
                                    type: 'info',
                                    title: 'TAGIHAN TERCATAT PENDING',
                                    message: 'Modal ditutup. Tagihan pembayaran Anda tetap aman tersimpan di sistem dan dapat dilanjutkan sewaktu-waktu.'
                                });
                            }
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                            setTimeout(function() { window.location.reload(); }, 1200);
                        }
                    });
                } else {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        if (window.showToast) {
                            window.showToast({
                                type: 'error',
                                title: 'KONEKSI SNAP MIDTRANS',
                                message: 'Script Snap gagal dimuat. Silakan periksa koneksi internet Anda.'
                            });
                        }
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                }
            } catch (err) {
                if (window.showToast) {
                    window.showToast({
                        type: 'error',
                        title: 'KENDALA TRANSAKSI',
                        message: err.message || 'Terjadi kesalahan saat memproses sesi pembayaran.'
                    });
                }
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        function resumePendingSnap(token) {
            if (!token) {
                if (window.showToast) {
                    window.showToast({
                        type: 'error',
                        title: 'SESI KEDALUWARSA',
                        message: 'Token pembayaran tidak ditemukan. Silakan batalkan sesi tagihan dan buat sesi baru.'
                    });
                }
                return;
            }

            if (window.snap && window.snap.pay) {
                window.snap.pay(token, {
                    onSuccess: function(result) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'success',
                                title: 'PEMBAYARAN DP DIKONFIRMASI',
                                message: 'Transaksi berhasil diverifikasi oleh Midtrans Escrow! Memperbarui status...',
                                duration: 3500
                            });
                        }
                        setTimeout(function() { window.location.reload(); }, 1800);
                    },
                    onPending: function(result) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'warning',
                                title: 'MENUNGGU PEMBAYARAN',
                                message: 'Tagihan menunggu pembayaran. Anda dapat menyelesaikannya sesuai panduan Midtrans.',
                                duration: 5000
                            });
                        }
                        setTimeout(function() { window.location.reload(); }, 2000);
                    },
                    onError: function(result) {
                        if (window.showToast) {
                            window.showToast({
                                type: 'error',
                                title: 'PEMBAYARAN DIBATALKAN',
                                message: 'Transaksi tidak dapat diselesaikan atau dibatalkan.'
                            });
                        }
                    },
                    onClose: function() {
                        if (window.showToast) {
                            window.showToast({
                                type: 'info',
                                title: 'TAGIHAN TETAP TERSIMPAN',
                                message: 'Tagihan pembayaran tetap tersimpan. Anda dapat melanjutkannya melalui kartu status tagihan di atas.'
                            });
                        }
                    }
                });
            } else {
                if (window.showToast) {
                    window.showToast({
                        type: 'error',
                        title: 'KONEKSI SNAP MIDTRANS',
                        message: 'Script Snap gateway gagal dimuat. Silakan periksa koneksi internet Anda.'
                    });
                }
            }
        }
    </script>
</body>
</html>
