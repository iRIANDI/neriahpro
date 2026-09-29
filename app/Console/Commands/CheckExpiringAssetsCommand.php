<?php

namespace App\Console\Commands;

use App\Filament\Resources\DomainHostingAssets\DomainHostingAssetResource;
use App\Models\DomainHostingAsset;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;

class CheckExpiringAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:check-expirations {--days=30 : Ambang batas hari sebelum jatuh tempo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memeriksa domain dan hosting yang mendekati masa tenggat dan mengirimkan notifikasi pengingat.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $this->info("Memindai aset domain & hosting dengan sisa tenggat <= {$days} hari...");

        $expiringAssets = DomainHostingAsset::active()
            ->whereBetween('expires_at', [
                Carbon::now()->startOfDay()->toDateString(),
                Carbon::now()->addDays($days)->endOfDay()->toDateString(),
            ])
            ->where(function ($query) {
                // Kirim pengingat jika belum pernah dikirim atau sudah lebih dari 3 hari sejak pengingat terakhir
                $query->whereNull('last_reminder_sent_at')
                      ->orWhere('last_reminder_sent_at', '<=', Carbon::now()->subDays(3));
            })
            ->get();

        if ($expiringAssets->isEmpty()) {
            $this->info('Tidak ada aset yang mendekati masa tenggat atau semua sudah diingatkan baru-baru ini.');
            return Command::SUCCESS;
        }

        $admins = User::all();
        if ($admins->isEmpty()) {
            $this->warn('Tidak ditemukan admin untuk menerima notifikasi.');
            return Command::SUCCESS;
        }

        $notifiedCount = 0;

        foreach ($expiringAssets as $asset) {
            $label = $asset->domain_name ?: $asset->name;
            $urgency = $asset->days_remaining <= 7 ? 'KRITIS' : 'PERINGATAN';

            foreach ($admins as $admin) {
                try {
                    Notification::make()
                        ->title("⚠️ [{$urgency}] Jatuh Tempo: {$label}")
                        ->body("Layanan {$asset->asset_type} ({$asset->provider}) akan berakhir pada {$asset->expires_at->format('d M Y')} ({$asset->expiration_label}). Segera periksa dan lakukan perpanjangan sewa.")
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color($asset->days_remaining <= 7 ? 'danger' : 'warning')
                        ->actions([
                            Action::make('view')
                                ->label('Kelola Aset')
                                ->url(DomainHostingAssetResource::getUrl('view', ['record' => $asset])),
                        ])
                        ->sendToDatabase($admin);
                } catch (\Throwable $e) {
                    $this->error("Gagal mengirim notifikasi ke {$admin->email}: " . $e->getMessage());
                }
            }

            $asset->update([
                'last_reminder_sent_at' => Carbon::now(),
                'status' => 'expiring_soon',
            ]);

            $this->line("• Pengingat terkirim: {$label} ({$asset->expiration_label})");
            $notifiedCount++;
        }

        $this->info("Selesai! {$notifiedCount} aset berhasil diproses dan dikirimkan pengingatnya.");
        return Command::SUCCESS;
    }
}
