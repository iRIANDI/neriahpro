<x-layouts.app 
    title="neriahpro.com - Login Pelanggan // Verifikasi OTP"
    metaDescription="Portal login aman pelanggan Neriah Pro dengan kode OTP email 6-digit tanpa password."
>
<div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-zinc-950 text-zinc-100">
    <div class="max-w-md w-full space-y-8 bg-zinc-900/90 border border-zinc-800 p-8 rounded-sm shadow-2xl relative"
         x-data="customerLoginApp()">
        
        <!-- Header -->
        <div class="text-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xs text-xs font-semibold tracking-wider uppercase bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                <span class="w-1.5 h-1.5 rounded-xs bg-indigo-400 animate-pulse"></span>
                Autentikasi Aman // Zero-Password
            </span>
            <h2 class="mt-4 text-2xl font-bold tracking-tight text-white">
                Masuk ke Portal Klien
            </h2>
            <p class="mt-2 text-xs text-zinc-400 leading-relaxed">
                Kelola cetak biru arsitektur, unduh PDF PRD, dan akses arsip proyek Anda tanpa repot mengingat kata sandi.
            </p>
        </div>

        <!-- Alert Notification Box -->
        <div x-show="alertMessage" 
             x-cloak 
             class="p-3 text-xs rounded-xs border transition-all duration-300"
             :class="alertType === 'error' ? 'bg-rose-500/10 text-rose-300 border-rose-500/30' : 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30'">
            <div class="flex items-center gap-2">
                <span x-text="alertType === 'error' ? '⚠️' : '✅'"></span>
                <span x-text="alertMessage" class="flex-1 font-medium"></span>
            </div>
        </div>

        <!-- Step 1: Input Email -->
        <form x-show="step === 'email'" @submit.prevent="requestOtp" class="mt-6 space-y-5">
            <div>
                <label for="customer-email" class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-2">
                    Alamat E-mail Pelanggan
                </label>
                <div class="relative">
                    <input 
                        id="customer-email" 
                        type="email" 
                        x-model="email" 
                        required 
                        placeholder="contoh@perusahaan.com"
                        class="w-full bg-zinc-950 border border-zinc-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-zinc-100 text-sm px-4 py-3 rounded-xs outline-none transition-colors"
                        :disabled="isLoading"
                    />
                </div>
                <p class="mt-1.5 text-[11px] text-zinc-500">
                    Kami akan mengirimkan 6-digit kode OTP ke email ini.
                </p>
            </div>

            <button 
                type="submit" 
                :disabled="isLoading || !email"
                class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-indigo-500/50 text-xs font-bold uppercase tracking-wider rounded-xs text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed shadow-lg transition-all"
            >
                <span x-show="!isLoading">Kirim Kode OTP Masuk &rarr;</span>
                <span x-show="isLoading" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses Pengiriman...
                </span>
            </button>
        </form>

        <!-- Step 2: Input OTP -->
        <form x-show="step === 'otp'" x-cloak @submit.prevent="verifyOtp" class="mt-6 space-y-5">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="otp-code" class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider">
                        Kode OTP 6-Digit
                    </label>
                    <button 
                        type="button" 
                        @click="changeEmail" 
                        class="text-[11px] text-indigo-400 hover:text-indigo-300 underline"
                    >
                        Ganti Email
                    </button>
                </div>

                <div class="text-xs text-zinc-400 mb-3 bg-zinc-950 p-2.5 rounded-xs border border-zinc-800">
                    Kode dikirim ke: <strong class="text-zinc-200" x-text="email"></strong>
                </div>

                <input 
                    id="otp-code" 
                    type="text" 
                    x-model="otp" 
                    maxlength="6"
                    pattern="[0-9]{6}"
                    required 
                    placeholder="123456"
                    class="w-full text-center tracking-[0.4em] font-mono text-2xl font-black bg-zinc-950 border border-zinc-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-cyan-400 px-4 py-3 rounded-xs outline-none transition-colors"
                    :disabled="isLoading"
                    autocomplete="one-time-code"
                />
                
                <div class="mt-2 flex items-center justify-between text-[11px] text-zinc-500">
                    <span>Berlaku 10 menit</span>
                    <button 
                        type="button" 
                        @click="requestOtp" 
                        :disabled="isLoading || resendCountdown > 0"
                        class="text-indigo-400 hover:text-indigo-300 disabled:opacity-40 disabled:hover:text-zinc-500"
                    >
                        <span x-show="resendCountdown === 0">Kirim Ulang OTP</span>
                        <span x-show="resendCountdown > 0" x-text="'Tunggu ' + resendCountdown + 's'"></span>
                    </button>
                </div>
            </div>

            <button 
                type="submit" 
                :disabled="isLoading || otp.length !== 6"
                class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-cyan-500/50 text-xs font-bold uppercase tracking-wider rounded-xs text-white bg-cyan-600 hover:bg-cyan-500 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed shadow-lg transition-all"
            >
                <span x-show="!isLoading">Verifikasi & Masuk Portal &rarr;</span>
                <span x-show="isLoading" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memverifikasi...
                </span>
            </button>
        </form>

        <!-- Footer Info -->
        <div class="pt-4 border-t border-zinc-800 text-center text-[11px] text-zinc-500 space-y-2">
            <div>
                Belum punya blueprint? 
                <a href="/blueprint" class="text-indigo-400 hover:underline">Buat Proyek Gratis (Spark)</a> atau 
                <a href="/pricing" class="text-indigo-400 hover:underline">Lihat Paket</a>
            </div>
            <div>
                Akses Administrator Tim? 
                <a href="/admin/login" class="text-zinc-400 hover:text-zinc-200 underline">Login Control Center &rarr;</a>
            </div>
        </div>

    </div>
</div>

<script>
function customerLoginApp() {
    return {
        step: 'email',
        email: '',
        otp: '',
        isLoading: false,
        alertMessage: '',
        alertType: 'info',
        resendCountdown: 0,
        countdownTimer: null,

        showAlert(message, type = 'info') {
            this.alertMessage = message;
            this.alertType = type;
            if (window.showToast) {
                window.showToast({
                    type: type === 'error' ? 'error' : 'success',
                    title: type === 'error' ? 'Autentikasi Gagal' : 'Informasi',
                    message: message
                });
            }
        },

        startCountdown(seconds = 60) {
            this.resendCountdown = seconds;
            if (this.countdownTimer) clearInterval(this.countdownTimer);
            this.countdownTimer = setInterval(() => {
                if (this.resendCountdown > 0) {
                    this.resendCountdown--;
                } else {
                    clearInterval(this.countdownTimer);
                }
            }, 1000);
        },

        changeEmail() {
            this.step = 'email';
            this.otp = '';
            this.alertMessage = '';
        },

        async requestOtp() {
            if (!this.email || this.isLoading) return;
            this.isLoading = true;
            this.alertMessage = '';

            try {
                const response = await fetch('/api/customer/otp/request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email: this.email })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.step = 'otp';
                    this.showAlert(data.message, 'success');
                    this.startCountdown(60);
                } else {
                    this.showAlert(data.message || 'Gagal mengirimkan kode OTP. Silakan coba kembali.', 'error');
                }
            } catch (err) {
                this.showAlert('Terjadi gangguan jaringan saat menghubungi server: ' + err.message, 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async verifyOtp() {
            if (this.otp.length !== 6 || this.isLoading) return;
            this.isLoading = true;
            this.alertMessage = '';

            try {
                const response = await fetch('/api/customer/otp/verify', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email: this.email, otp: this.otp })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showAlert(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect_url || '/blueprint';
                    }, 800);
                } else {
                    this.showAlert(data.message || 'Kode OTP salah atau telah kedaluwarsa.', 'error');
                }
            } catch (err) {
                this.showAlert('Terjadi kesalahan sistem saat verifikasi: ' + err.message, 'error');
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
</x-layouts.app>
