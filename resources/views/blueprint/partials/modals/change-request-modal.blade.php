    <!-- MODULE: CHANGE REQUEST MODAL -->
    <div id="modal-cr" x-show="crModalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="crModalOpen = false" x-transition.opacity></div>
        <div class="relative z-10 bg-white dark:bg-zinc-950 border-2 border-amber-500 w-full max-w-2xl rounded-none shadow-2xl" x-transition @click.stop>
            <button type="button" @click="crModalOpen = false" class="absolute top-4 right-4 text-zinc-500 hover:text-white bg-zinc-100 dark:bg-zinc-900 px-3 py-1 text-xs font-bold font-mono transition cursor-pointer">X</button>
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 bg-amber-500/10">
                <h3 class="font-black text-amber-600 dark:text-amber-500 text-lg uppercase font-mono">Request Change / Addendum Fitur Baru</h3>
                <p class="text-zinc-600 dark:text-zinc-400 text-xs mt-1 font-sans">Jelaskan fitur tambahan atau perubahan scope yang Anda inginkan. Kami akan mengestimasi ulang Story Points (SP) dan biaya tambahannya.</p>
            </div>
            <form onsubmit="event.preventDefault(); submitChangeRequest('{{ $blueprint->slug }}', this);" class="p-6 space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider mb-1">Judul / Nama Fitur Baru</label>
                    <input type="text" name="title" required class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 p-2.5 text-sm text-zinc-900 dark:text-white rounded-none focus:outline-none focus:border-amber-500 transition" placeholder="Contoh: Modul Laporan Ekspor PDF">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider mb-1">Detail Spesifikasi & Business Logic</label>
                    <textarea name="description" required rows="4" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 p-2.5 text-sm text-zinc-900 dark:text-white rounded-none focus:outline-none focus:border-amber-500 transition" placeholder="Jelaskan cara kerja fitur ini, apa saja tombolnya, dan output apa yang diharapkan..."></textarea>
                </div>
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-zinc-200 dark:border-zinc-800">
                    <button type="button" @click="crModalOpen = false" class="px-4 py-2 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold uppercase text-xs hover:bg-zinc-300 dark:hover:bg-zinc-700 transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-amber-500 hover:bg-amber-400 text-black font-black uppercase text-xs transition border border-amber-600 shadow-[2px_2px_0px_#b45309] active:translate-y-[2px] active:shadow-none cursor-pointer">Kirim Request Addendum</button>
                </div>
            </form>
        </div>
    </div>
