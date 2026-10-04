<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cart Pembayaran Proyek // Neriah Pro</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

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

        @if(session('warning'))
            <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs font-mono flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>{{ session('warning') }}</span>
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
                    <span id="cart-master-countdown" class="text-sm">--:--:--</span>
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
                            $tierLabel = 'Standard (30 Hari)';
                            $tierBadge = 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300';
                            if ($tierKey === 'fast_track') {
                                $tierLabel = '⚡ Fast-Track (14 Hari) &bull; Gemini Ultra AI Accelerator';
                                $tierBadge = 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/40';
                            } elseif ($tierKey === 'hyper_sprint') {
                                $tierLabel = '🔥 Hyper-Sprint (7 Hari) &bull; 24/7 War Room Squad';
                                $tierBadge = 'bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/40';
                            }
                        @endphp
                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 item-row" data-expires="{{ $item['expires_at'] }}" x-data="{ showBreakdown: false }">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-3 mb-3">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                        <span class="text-[10px] font-mono uppercase tracking-wider px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                            ID: {{ strtoupper(substr($item['blueprint']->id, 0, 8)) }}
                                        </span>
                                        <span class="text-[10px] font-mono uppercase tracking-wider px-2 py-0.5 font-bold {{ $tierBadge }}">
                                            {!! $tierLabel !!}
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
                                </div>

                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('blueprint.generate-contract', $item['slug']) }}" class="m-0">
                                        @csrf
                                        <input type="hidden" name="tier" value="{{ $tierKey }}">
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

                    <div class="space-y-3 text-xs font-mono mb-6">
                        <div class="flex justify-between text-zinc-500 dark:text-zinc-400">
                            <span>Total Nilai Kontrak</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Rp {{ number_format($totalContract, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-zinc-500 dark:text-zinc-400">
                            <span>Ketentuan Termin DP</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">50% di Muka</span>
                        </div>
                        <div class="border-t border-zinc-200 dark:border-zinc-800 pt-3 flex justify-between items-baseline">
                            <span class="font-bold uppercase text-zinc-900 dark:text-zinc-100">Total Tagihan DP</span>
                            <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalDp, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="bg-zinc-50 dark:bg-zinc-950 p-3 border border-zinc-200 dark:border-zinc-800 text-[11px] font-mono text-zinc-500 dark:text-zinc-400 mb-6 leading-relaxed">
                        <strong class="text-zinc-900 dark:text-zinc-100 block mb-0.5">METODE PEMBAYARAN MIDTRANS:</strong>
                        Mendukung QRIS, BCA Virtual Account, Mandiri Bill, BNI, BRI, Permata, dan Kartu Kredit dengan enkripsi 3D Secure.
                    </div>

                    <div class="space-y-3">
                        <button 
                            type="button"
                            id="btn-pay-snap"
                            onclick="payWithSnap()"
                            class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-widest py-3.5 px-4 text-center block transition cursor-pointer disabled:opacity-50 shadow-md"
                        >
                            Bayar DP Sekarang (Midtrans Snap) &rarr;
                        </button>
                        <p class="text-[10px] text-zinc-400 text-center font-mono">
                            Escrow diamankan & diverifikasi otomatis oleh Midtrans webhook.
                        </p>
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
                if (seconds <= 0) return 'EXPIRED (00:00:00)';
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
                    const exp = parseInt(row.getAttribute('data-expires'), 10);
                    if (!isNaN(exp)) {
                        const rem = exp - now;
                        const labelEl = row.querySelector('.item-countdown');
                        if (labelEl) {
                            labelEl.textContent = formatTime(rem);
                            if (rem <= 0) {
                                labelEl.classList.add('text-rose-500');
                            }
                        }
                        if (rem > 0 && (minRemaining === null || rem < minRemaining)) {
                            minRemaining = rem;
                        }
                    }
                });

                const masterEl = document.getElementById('cart-master-countdown');
                if (masterEl) {
                    if (minRemaining !== null) {
                        masterEl.textContent = formatTime(minRemaining);
                    } else if (rows.length > 0) {
                        masterEl.textContent = 'EXPIRED (RELEASED)';
                        masterEl.classList.add('text-rose-400');
                        // Auto-refresh once after expiry to allow server-side cleanup
                        setTimeout(function() {
                            window.location.reload();
                        }, 2500);
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
                                    title: 'PROMPT DITUTUP',
                                    message: 'Modal pembayaran ditutup. Klik tombol kembali jika Anda ingin melanjutkan transaksi.'
                                });
                            }
                            btn.disabled = false;
                            btn.innerHTML = originalText;
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
    </script>
</body>
</html>
