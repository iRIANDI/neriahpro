<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cart Pembayaran Proyek // Neriah Pro</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

        @if(empty($items))
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-zinc-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <h3 class="text-base font-bold uppercase font-mono text-zinc-900 dark:text-zinc-100 mb-1">Cart Masih Kosong</h3>
                <p class="text-zinc-500 text-xs font-sans mb-6">Belum ada spesifikasi Blueprint proyek yang ditambahkan ke cart belanja.</p>
                <a href="/blueprint" class="inline-block px-5 py-2.5 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs uppercase tracking-wider transition">
                    Mulai Buat PRD Baru
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Cart Items List (Col Span 2) -->
                <div class="lg:col-span-2 space-y-4">
                    @foreach($items as $item)
                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-3 mb-3">
                                <div>
                                    <span class="text-[10px] font-mono uppercase tracking-wider px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                        ID: {{ strtoupper(substr($item['blueprint']->id, 0, 8)) }}
                                    </span>
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

                            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 text-xs font-mono">
                                <a href="{{ route('blueprint.show', $item['slug']) }}" class="text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-bold">
                                    <span>&larr; Lihat Dokumen PRD</span>
                                </a>

                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('blueprint.generate-contract', $item['slug']) }}" class="m-0">
                                        @csrf
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
                        <a 
                            href="https://app.sandbox.midtrans.com/snap/v2/vtweb/demo-neriahpro-dp"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-widest py-3.5 px-4 text-center block transition"
                        >
                            Bayar DP Sekarang (Midtrans) &rarr;
                        </a>
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

</body>
</html>
