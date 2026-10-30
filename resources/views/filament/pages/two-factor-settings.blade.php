<x-filament-panels::page>
    <div class="space-y-6 max-w-4xl mx-auto">
        <!-- Kartu Utama Status 2FA -->
        <div class="bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none p-6 sm:p-8 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-zinc-200 dark:border-zinc-800">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-none flex items-center justify-center {{ $isEnabled ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-950 dark:text-emerald-200 border border-emerald-400 dark:border-emerald-700' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700' }}">
                        @if($isEnabled)
                            <x-heroicon-o-shield-check class="w-7 h-7" />
                        @else
                            <x-heroicon-o-shield-exclamation class="w-7 h-7" />
                        @endif
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-black uppercase text-zinc-900 dark:text-white">
                                Autentikasi Dua Faktor (2FA TOTP)
                            </h2>
                            @if($isEnabled)
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-bold bg-emerald-100 text-emerald-950 dark:bg-emerald-950/80 dark:text-emerald-200 border border-emerald-400 dark:border-emerald-700 rounded-none">
                                    Aktif & Terlindungi
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-bold bg-zinc-100 text-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 rounded-none">
                                    Belum Diaktifkan (Opsional)
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-2 leading-relaxed">
                            @if($isEnabled)
                                Akun Anda kini terlindungi dengan standar keamanan tinggi (RFC 6238 TOTP). Setiap kode verifikasi 6 digit diverifikasi secara mandiri melalui aplikasi authenticator ponsel Anda.
                            @else
                                Fitur ini berstatus opsional. Mengaktifkan fitur ini memberikan proteksi tingkat tinggi anti-pembajakan menggunakan kode 6 digit dari aplikasi <strong>Google Authenticator</strong> atau <strong>Microsoft Authenticator</strong> saat login.
                            @endif
                        </p>
                    </div>
                </div>

                @if(!$isEnabled && !$isSettingUp)
                    <div class="flex-shrink-0">
                        <x-filament::button 
                            wire:click="startSetup"
                            size="lg" 
                            color="primary" 
                            icon="heroicon-m-shield-check"
                            class="rounded-none font-bold">
                            Aktifkan 2FA Sekarang
                        </x-filament::button>
                    </div>
                @endif
            </div>

            <!-- Langkah Aktivasi (Saat Setup Dimulai) -->
            @if($isSettingUp)
                <div class="mt-6 pt-2 space-y-6">
                    <div class="bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 p-4 text-xs text-zinc-800 dark:text-zinc-200 rounded-none">
                        <strong class="font-bold text-zinc-900 dark:text-white uppercase font-mono">Panduan Aktivasi:</strong>
                        <ol class="list-decimal list-inside mt-2 space-y-1 font-mono">
                            <li>Buka aplikasi <strong>Google Authenticator</strong> atau <strong>Microsoft Authenticator</strong> di smartphone Anda.</li>
                            <li>Tekan tombol <strong>+</strong> lalu pilih <strong>Pindai kode QR</strong> (atau masukkan Kunci Rahasia secara manual).</li>
                            <li>Masukkan 6 digit angka yang muncul pada aplikasi ke kolom konfirmasi di bawah ini, lalu klik Verifikasi.</li>
                        </ol>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center pt-2">
                        <div class="flex flex-col items-center justify-center p-6 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
                            <div class="bg-white p-2.5 rounded-none border border-zinc-300 flex items-center justify-center min-w-[210px] min-h-[210px]">
                                @if($qrCodeUrl)
                                    <img src="{{ $qrCodeUrl }}" alt="QR Code 2FA" class="w-52 h-52 object-contain" />
                                @endif
                            </div>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-3 font-mono">Pindai dengan Authenticator App</span>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-mono font-bold text-zinc-600 dark:text-zinc-400 uppercase mb-1.5">
                                    Kunci Rahasia Manual
                                </label>
                                <input type="text" readonly value="{{ $secretKey }}" class="w-full font-mono text-sm px-3.5 py-2.5 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-none text-zinc-900 dark:text-white select-all font-bold tracking-wider" />
                                <p class="text-[11px] text-zinc-500 mt-1">Gunakan kunci ini jika kamera Anda tidak dapat memindai QR code.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-mono font-bold text-zinc-600 dark:text-zinc-400 uppercase mb-1.5">
                                    Masukkan 6 Digit Kode Konfirmasi
                                </label>
                                <input 
                                    type="text" 
                                    maxlength="6" 
                                    wire:model="confirmationCode" 
                                    placeholder="000000" 
                                    class="w-full font-mono text-2xl tracking-widest text-center px-4 py-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-none text-zinc-900 dark:text-white focus:ring-1 focus:ring-emerald-500" 
                                />
                            </div>

                            <div class="flex items-center gap-3 pt-3">
                                <x-filament::button 
                                    wire:click="confirmSetup" 
                                    color="success" 
                                    size="md" 
                                    icon="heroicon-m-check" 
                                    class="rounded-none font-bold">
                                    Verifikasi & Aktifkan 2FA
                                </x-filament::button>

                                <x-filament::button 
                                    wire:click="cancelSetup" 
                                    color="gray" 
                                    size="md"
                                    class="rounded-none">
                                    Batal
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Kode Pemulihan Darurat (Saat 2FA Aktif) -->
            @if($isEnabled)
                <div class="mt-8 pt-6 border-t border-zinc-200 dark:border-zinc-800 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2 uppercase font-mono">
                                <x-heroicon-o-key class="w-5 h-5 text-emerald-500" />
                                Kode Pemulihan Darurat (Recovery Codes)
                            </h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                Simpan kode ini di tempat yang aman. Setiap kode dapat digunakan 1 kali untuk login jika ponsel Anda hilang.
                            </p>
                        </div>

                        <x-filament::button 
                            wire:click="regenerateRecoveryCodes" 
                            color="gray" 
                            size="sm" 
                            icon="heroicon-m-arrow-path"
                            class="rounded-none font-mono">
                            Perbarui Kode Pemulihan
                        </x-filament::button>
                    </div>

                    @if(!empty($recoveryCodes))
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 p-4 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none">
                            @foreach($recoveryCodes as $code)
                                <div class="font-mono text-xs font-bold text-center py-2 px-3 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 select-all tracking-wider rounded-none">
                                    {{ $code }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Nonaktifkan 2FA -->
                    <div class="pt-6 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold font-mono uppercase text-zinc-900 dark:text-zinc-100">Nonaktifkan Autentikasi Dua Faktor</span>
                            <p class="text-[11px] text-zinc-500">Kembalikan metode login ke standar kata sandi biasa.</p>
                        </div>

                        @if(!$isDisabling)
                            <x-filament::button 
                                wire:click="startDisabling" 
                                color="danger" 
                                size="sm" 
                                icon="heroicon-m-power"
                                class="rounded-none font-mono">
                                Matikan 2FA
                            </x-filament::button>
                        @endif
                    </div>

                    @if($isDisabling)
                        <div class="p-4 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 space-y-3 rounded-none">
                            <label class="block text-xs font-mono font-bold text-zinc-800 dark:text-zinc-200 uppercase">
                                Masukkan Kata Sandi Akun Anda untuk Konfirmasi:
                            </label>
                            <input 
                                type="password" 
                                wire:model="currentPassword" 
                                placeholder="Kata Sandi Akun Anda" 
                                class="w-full sm:w-80 px-3.5 py-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-none text-zinc-900 dark:text-white" 
                            />
                            <div class="flex items-center gap-2 pt-2">
                                <x-filament::button 
                                    wire:click="disable2fa" 
                                    color="danger" 
                                    size="sm"
                                    class="rounded-none font-bold">
                                    Konfirmasi Matikan 2FA
                                </x-filament::button>
                                <x-filament::button 
                                    wire:click="cancelDisabling" 
                                    color="gray" 
                                    size="sm"
                                    class="rounded-none">
                                    Batal
                                </x-filament::button>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>

