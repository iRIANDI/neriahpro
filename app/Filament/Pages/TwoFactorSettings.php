<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use App\Services\TwoFactorService;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use BackedEnum;
use UnitEnum;

class TwoFactorSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static string|UnitEnum|null $navigationGroup = 'System & Security';
    protected static ?string $navigationLabel = 'Keamanan 2FA (Two-Factor)';
    protected static ?string $title = 'Autentikasi Dua Faktor (2FA TOTP)';
    protected static ?int $navigationSort = 16;

    protected string $view = 'filament.pages.two-factor-settings';

    public bool $isEnabled = false;
    public bool $isSettingUp = false;
    public bool $isEnforced = false;
    public ?string $secretKey = null;
    public ?string $qrCodeUrl = null;
    public ?string $otpAuthUri = null;
    public string $confirmationCode = '';
    public array $recoveryCodes = [];
    public bool $showRecoveryCodes = false;

    // Untuk penonaktifan
    public string $currentPassword = '';
    public bool $isDisabling = false;

    public function mount(): void
    {
        $user = Filament::auth()->user() ?? auth()->user();
        if (!$user) {
            return;
        }

        $this->isEnabled = $user->hasEnabledTwoFactor();
        $this->isEnforced = (bool) ($user->two_factor_required ?? false);

        if ($this->isEnabled) {
            try {
                $this->recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes ?? '[]'), true) ?: [];
            } catch (\Throwable) {
                $this->recoveryCodes = [];
            }
        }
    }

    public function startSetup(): void
    {
        $user = Filament::auth()->user() ?? auth()->user();
        if (!$user) {
            Notification::make()
                ->title('Sesi Tidak Ditemukan')
                ->body('Silakan muat ulang halaman untuk memperbarui sesi Anda.')
                ->danger()
                ->send();
            return;
        }

        $service = app(TwoFactorService::class);
        $this->secretKey = $service->generateSecretKey();
        $this->otpAuthUri = $service->getOtpAuthUri('Neriah Pro Hub', $user->email ?? 'admin@neriahpro.com', $this->secretKey);
        $this->qrCodeUrl = $service->getQrCodeUrl($this->otpAuthUri);
        $this->isSettingUp = true;
        $this->confirmationCode = '';

        Notification::make()
            ->title('Siap untuk Aktivasi 2FA')
            ->body('Silakan pindai QR Code dengan aplikasi Google Authenticator atau masukkan kunci rahasia.')
            ->info()
            ->send();
    }

    public function cancelSetup(): void
    {
        $this->isSettingUp = false;
        $this->secretKey = null;
        $this->qrCodeUrl = null;
        $this->otpAuthUri = null;
        $this->confirmationCode = '';
    }

    public function confirmSetup(): void
    {
        $user = Filament::auth()->user() ?? auth()->user();
        if (!$user || !$this->secretKey) {
            return;
        }

        $service = app(TwoFactorService::class);
        if (!$service->verifyCode($this->secretKey, $this->confirmationCode)) {
            Notification::make()
                ->title('Kode Verifikasi Tidak Cocok')
                ->body('Kode 6 digit yang Anda masukkan salah atau kadaluarsa. Silakan periksa jam di ponsel Anda dan coba lagi.')
                ->danger()
                ->send();
            return;
        }

        $this->recoveryCodes = $service->generateRecoveryCodes();
        $user->enableTwoFactor($this->secretKey, $this->recoveryCodes);

        $this->isEnabled = true;
        $this->isSettingUp = false;
        $this->showRecoveryCodes = true;
        $this->secretKey = null;
        $this->qrCodeUrl = null;

        AuditLog::record(
            action: '2fa_enabled',
            model: $user,
            newValues: ['two_factor' => 'enabled']
        );

        Notification::make()
            ->title('2FA Berhasil Diaktifkan!')
            ->body('Akun Anda kini terlindungi dengan verifikasi dua faktor (TOTP). Simpan kode pemulihan darurat Anda.')
            ->success()
            ->send();
    }

    public function startDisabling(): void
    {
        $this->isDisabling = true;
        $this->currentPassword = '';
    }

    public function cancelDisabling(): void
    {
        $this->isDisabling = false;
        $this->currentPassword = '';
    }

    public function disable2fa(): void
    {
        $user = Filament::auth()->user() ?? auth()->user();
        if (!$user) {
            return;
        }

        if (empty($this->currentPassword) || !Hash::check($this->currentPassword, $user->password)) {
            Notification::make()
                ->title('Kata Sandi Salah')
                ->body('Kata sandi saat ini yang Anda masukkan tidak sesuai.')
                ->danger()
                ->send();
            return;
        }

        $user->disableTwoFactor();

        $this->isEnabled = false;
        $this->isDisabling = false;
        $this->showRecoveryCodes = false;
        $this->recoveryCodes = [];

        AuditLog::record(
            action: '2fa_disabled',
            model: $user,
            newValues: ['two_factor' => 'disabled']
        );

        Notification::make()
            ->title('2FA Telah Dinonaktifkan')
            ->body('Autentikasi dua faktor untuk akun Anda telah berhasil dimatikan.')
            ->warning()
            ->send();
    }

    public function regenerateRecoveryCodes(): void
    {
        $user = Filament::auth()->user() ?? auth()->user();
        if (!$user || !$this->isEnabled) {
            return;
        }

        $service = app(TwoFactorService::class);
        $this->recoveryCodes = $service->generateRecoveryCodes();
        $user->update([
            'two_factor_recovery_codes' => encrypt(json_encode($this->recoveryCodes)),
        ]);
        $this->showRecoveryCodes = true;

        AuditLog::record(
            action: '2fa_recovery_codes_regenerated',
            model: $user
        );

        Notification::make()
            ->title('Kode Pemulihan Diperbarui')
            ->body('Kode pemulihan baru telah dibuat. Kode lama sudah tidak berlaku lagi.')
            ->success()
            ->send();
    }
}

