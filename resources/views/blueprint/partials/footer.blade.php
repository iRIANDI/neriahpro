
    <!-- Global Footer -->
    <footer class="bg-white dark:bg-zinc-900 text-zinc-500 dark:text-zinc-400 py-6 px-4 text-center text-xs border-t border-zinc-200 dark:border-zinc-800 font-mono no-print">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>&copy; {{ date('Y') }} NERIAH PRO HUB &bull; ULTIMATE PRD & ARCHITECTURE BLUEPRINT</p>
            <div class="flex flex-wrap items-center justify-center gap-4 text-[11px]">
                <a href="/terms" target="_blank" class="hover:text-emerald-500 underline underline-offset-2">
                    <span x-show="locale === 'en'">Terms of Service</span>
                    <span x-show="locale !== 'en'">Syarat &amp; Ketentuan</span>
                </a>
                <span class="text-zinc-300 dark:text-zinc-700">&bull;</span>
                <a href="/privacy-policy" target="_blank" class="hover:text-emerald-500 underline underline-offset-2">
                    <span x-show="locale === 'en'">Privacy Policy</span>
                    <span x-show="locale !== 'en'">Kebijakan Privasi</span>
                </a>
                <span class="text-zinc-300 dark:text-zinc-700">&bull;</span>
                <a href="/refund-policy" target="_blank" class="hover:text-emerald-500 underline underline-offset-2">
                    <span x-show="locale === 'en'">Refund &amp; Warranty Policy</span>
                    <span x-show="locale !== 'en'">Garansi &amp; Kebijakan Refund</span>
                </a>
            </div>
        </div>
    </footer>

